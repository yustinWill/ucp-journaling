<?php

namespace App\Domains\AccountingJournal\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingJournalService
{
    private $table_name = 't_accounting_journals';

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.date',
                't.reference_no',
                't.description',
                DB::raw('SUM(t.amount)/2 as amount'),
                't.created_at',
                'users.name as created_by',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->join('users', 't.created_by', '=', 'users.id')
            ->where('t.status', '=', GeneralStatuses::ACTIVE);

        // Filter by status
        $query->where('t.company_code', session('user_company_code'));
        if ($ref_no = request()->input('reference_no')) {
            $query->where('t.reference_no', 'like', "%{$ref_no}%");
        }

        if ($description = request()->input('note')) {
            $query->where('t.description', 'like', "%{$description}%");
        }

        if ($created_by = request()->input('created_by')) {
            $query->where('t.created_by', 'like', "%{$created_by}%");
        }

        if ($date = request()->input('date')) {
            [$from, $to] = array_map('trim', explode(' - ', $date));
            $query->whereBetween('t.date', [$from, $to]);
        }

        $query->groupBy('t.reference_no', 't.date', 't.description', 't.created_at', 't.created_by');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('created_at', 'ASC');
        $data = $query->get();

        foreach ($data as &$row) {
            if (! empty($row->tags)) {
                $tags = json_decode($row->tags, true);
                $uniqueTags = [];
                foreach ($tags as $tag) {
                    $key = $tag['code'].'_'.$tag['preview'];
                    $uniqueTags[$key] = $tag;
                }
                $row->tags = json_encode(array_values($uniqueTags));
            }
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findAllPerCoaDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.date',
                't.reference_no',
                'm_accounting_features.name as type',
                't.description',
                't.amount',
                't.debit_credit',
                't.created_at',
                'users.name as created_by',
            ])
            ->join('users', 't.created_by', '=', 'users.id')
            ->join('m_accounting_features', 't.f_accounting_feature_code', '=', 'm_accounting_features.code')
            ->where('t.status', '=', GeneralStatuses::ACTIVE)
            ->where('t.f_coa_code', '=', request()->input('coa_code'))
            ->where('t.company_code', '=', session('user_company_code'));

        // Filter by status
        $query->where('t.company_code', session('user_company_code'));
        if ($description_journal = request()->input('description_journal')) {
            $query->where('t.description', 'like', "%{$description_journal}%");
        }

        if ($reference_no = request()->input('reference_no')) {
            $query->where('t.reference_no', 'like', "%{$reference_no}%");
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('company_code', session('user_company_code'))
            ->where('f_coa_code', '=', request()->input('coa_code'))
            ->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOneByReferenceId($referenceId)
    {
        return DB::table($this->table_name)
            ->where('reference_no', $referenceId)
            ->first();
    }

    public function generateReferenceNo($date)
    {
        do {
            $prefix = date('ymd', strtotime($date));
            $random = strtoupper(substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
            $referenceNo = $prefix.'.'.session('user_company_code').'.JLL.'.$random;
            $data = $this->findOneByReferenceId($referenceNo);
        } while ($data);

        return $referenceNo;
    }

    public function create($data)
    {
        $now = now();
        $inserted_ids = [];
        foreach ($data as $row) {
            $inserted_ids[] = DB::table($this->table_name)->insertGetId([
                'f_transaction_code' => $row['transaction_code'],
                'reference_no' => $row['reference_no'],
                'company_code' => session('user_company_code'),
                'date' => $row['date'],
                'f_accounting_feature_code' => $row['feature_code'],
                'f_coa_code' => $row['coa_code'],
                'description' => $row['description'],
                'internal_memo' => $row['internal_memo'],
                'coa_description' => $row['coa_description'],
                'amount' => $row['amount'],
                'debit_credit' => $row['debit_credit'],
                'f_tag_codes' => json_encode($row['f_tag_codes']),
                'status' => $row['status'],
                'is_locked' => false,
                'created_at' => $now,
                'created_by' => Auth::id(),
            ]);
        }

        return $inserted_ids;
    }

    public function findOne($reference_no)
    {
        return DB::table($this->table_name.' as t')
            ->select('t.*', 'm_coas.name as coa_name', DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"))
            ->join('m_coas', 'm_coas.code', '=', 't.f_coa_code')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('reference_no', $reference_no)
            ->where('t.status', GeneralStatuses::ACTIVE)
            ->orderBy('id', 'ASC')
            ->groupBy('t.id', 't.reference_no', 't.date', 't.description', 't.amount', 't.created_at', 't.created_by', 'm_coas.name')
            ->get();
    }

    public function updateSetInactive($reference_no)
    {
        return DB::table($this->table_name)
            ->where('reference_no', $reference_no)
            ->update([
                'status' => GeneralStatuses::INACTIVE,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }
}
