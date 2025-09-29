<?php

namespace App\Domains\RevenueSplit\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevenueSplitService
{
    private $table_name = 'm_revenue_splits';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'cycle_name' => $data['cycle_name'],
            'description' => $data['description'] ?? '',
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'target_pabrik' => $data['target_pabrik'],
            'target_owner' => $data['target_owner'],
            'target_perusahaan' => $data['target_perusahaan'],
            'total_target' => $data['target_pabrik'] + $data['target_owner'] + $data['target_perusahaan'],
            'notes' => $data['notes'] ?? '',
            'status' => $data['status'] ?? 'DRAFT',
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'cycle_name' => $data['cycle_name'],
                'description' => $data['description'] ?? '',
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'target_pabrik' => $data['target_pabrik'],
                'target_owner' => $data['target_owner'],
                'target_perusahaan' => $data['target_perusahaan'],
                'total_target' => $data['target_pabrik'] + $data['target_owner'] + $data['target_perusahaan'],
                'notes' => $data['notes'] ?? '',
                'status' => $data['status'] ?? 'DRAFT',
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.cycle_name',
                't.start_date',
                't.end_date',
                't.target_pabrik',
                't.target_owner',
                't.target_perusahaan',
                't.total_target',
                't.status',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by',
            ]);

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('t.status', $status);
        }

        // Filter by cycle name
        if ($cycleName = request()->input('cycle_name')) {
            $query->where('t.cycle_name', 'like', "%{$cycleName}%");
        }

        // Filter by periode (date range)
        if ($periode = request()->input('periode')) {
            // Format expected: "2024-01-01 - 2024-12-31"
            $dates = explode(' - ', $periode);
            if (count($dates) == 2) {
                $query->where('t.start_date', '>=', $dates[0])
                      ->where('t.end_date', '<=', $dates[1]);
            }
        }

        // Filter by total minimal
        if ($totalMinimal = request()->input('total_minimal')) {
            $query->where('t.total_target', '>=', $totalMinimal);
        }

        $query->where('t.company_code', session('user_company_code'));
        
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('t.created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOne($id)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('id', $id)
            ->first();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'cycle_name', 'description', 'start_date', 'end_date', 'target_pabrik', 'target_owner', 'target_perusahaan', 'total_target', 'status', 'notes')
            ->where('company_code', session('user_company_code'))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSummaryCards()
    {
        $totalCycles = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->count();

        $activeCycles = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('status', 'ACTIVE')
            ->count();

        $draftCycles = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('status', 'DRAFT')
            ->count();

        $completedCycles = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('status', 'SELESAI')
            ->count();

        $totalKeuntungan = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->sum('total_target');

        return [
            'total_cycles' => $totalCycles,
            'active_cycles' => $activeCycles,
            'draft_cycles' => $draftCycles,
            'completed_cycles' => $completedCycles,
            'total_keuntungan' => $totalKeuntungan,
        ];
    }
}