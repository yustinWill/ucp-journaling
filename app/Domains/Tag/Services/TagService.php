<?php

namespace App\Domains\Tag\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\GeneralStatuses;

class TagService
{
    private $table_name = 'm_tags';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'name' => $data['name'],
            'code' => $data['code'],
            'status' => $data['status'],
            'f_tag_color_id' => $data['f_tag_color_id'],
            'description' => $data['description'] ?? null,
            'memo' => $data['memo'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'f_tag_color_id' => $data['f_tag_color_id'],
                'description' => $data['description'] ?? null,
                'memo' => $data['memo'] ?? null,
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.status',
                't.usage_count',
                't.description',
                't.memo',
                't.created_at',
                't.updated_at',
                DB::raw("CONCAT(tc.preview, ' | ', tc.name) as tag_color"),
                't.created_by',
                't.updated_by',
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id');

        // Search support
        if ($search = request()->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('t.name', 'like', "%{$search}%")
                    ->orWhere('t.code', 'like', "%{$search}%")
                    ->orWhere('t.status', 'like', "%{$search}%")
                    ->orWhere('tc.name', 'like', "%{$search}%");
            });
        }
        $query->where('t.company_code', session('user_company_code'));
        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        if ($code = request()->input('code')) {
            $query->where('t.code', $code);
        }
        if ($name = request()->input('name')) {
            $query->where('t.name', 'like', "%{$name}%");
        }

        // Filter by tag color
        if ($colorId = request()->input('f_tag_color_id')) {
            $query->where('t.f_tag_color_id', $colorId);
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $recordsFiltered = $filteredQuery->count();

        $query->skip($start)->take($length);
        $query->orderBy('name', 'ASC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findAll()
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                'tc.code AS tag_color_code'
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.status', GeneralStatuses::ACTIVE)
            ->orderBy('code', 'ASC')
            ->get();
    }

    public function findOne($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.status',
                'tc.id as tag_id',
                'tc.name as tag_name',
                'tc.code as tag_code',
                'tc.preview as tag_preview',
                't.description',
                't.status',
                't.memo',
                't.usage_count'
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.id', $id)
            ->first();
    }
}
