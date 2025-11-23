<?php

namespace App\Domains\Budged\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgedService
{
    private $table_name = 'm_budgeds';
    private $history_table = 'm_budgeds_history';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'budged_name' => $data['budged_name'],
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'budged_name' => $data['budged_name'],
                'update_by' => Auth::id(),
                'update_at' => now(),
            ]);
    }

    public function findAllDatatable()
    {
        $currentDate = now()->toDateString();

        // Subquery to get current period for each budget
        $currentPeriodSubquery = DB::table($this->history_table)
            ->select([
                'budged_id',
                'period_start_date',
                'period_end_date',
                'budged_limit_value',
                'budged_remaining_value'
            ])
            ->where('period_start_date', '<=', $currentDate)
            ->where('period_end_date', '>=', $currentDate);

        $query = DB::table($this->table_name . ' as b')
            ->select([
                'b.id',
                'b.budged_name',
                'cp.period_start_date',
                'cp.period_end_date',
                'cp.budged_limit_value as current_limit',
                'cp.budged_remaining_value as current_remaining',
            ])
            ->leftJoinSub($currentPeriodSubquery, 'cp', function($join) {
                $join->on('b.id', '=', 'cp.budged_id');
            });

        // Search filter
        if ($search = request()->input('search')) {
            $query->where('b.budged_name', 'like', "%{$search}%");
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('b.id', 'DESC');
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
        $data = DB::table($this->table_name . ' as b')
            ->select([
                'b.id',
                'b.budged_name',
                'b.created_at',
                'b.created_by',
                'b.update_at',
                'b.update_by',
            ])
            ->where('b.id', $id)
            ->first();

        if ($data == null) {
            abort(404);
        }

        return $data;
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'budged_name')
            ->orderBy('budged_name', 'ASC')
            ->get();
    }

    // Budget History Methods
    public function createHistory(int $budged_id, array $data)
    {
        return DB::table($this->history_table)->insertGetId([
            'budged_id' => $budged_id,
            'budged_limit_value' => $data['budged_limit_value'],
            'budged_remaining_value' => $data['budged_limit_value'], // Initially same as limit
            'period_start_date' => $data['period_start_date'] ?? null,
            'period_end_date' => $data['period_end_date'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function validatePeriodOverlap(int $budged_id, string $start_date, string $end_date, ?int $exclude_history_id = null): array
    {
        $query = DB::table($this->history_table)
            ->where('budged_id', $budged_id)
            ->where(function($q) use ($start_date, $end_date) {
                // Check if new period overlaps with existing periods
                $q->where(function($subQ) use ($start_date, $end_date) {
                    // New period starts within existing period
                    $subQ->where('period_start_date', '<=', $start_date)
                         ->where('period_end_date', '>=', $start_date);
                })
                ->orWhere(function($subQ) use ($start_date, $end_date) {
                    // New period ends within existing period
                    $subQ->where('period_start_date', '<=', $end_date)
                         ->where('period_end_date', '>=', $end_date);
                })
                ->orWhere(function($subQ) use ($start_date, $end_date) {
                    // New period completely contains existing period
                    $subQ->where('period_start_date', '>=', $start_date)
                         ->where('period_end_date', '<=', $end_date);
                });
            });

        if ($exclude_history_id) {
            $query->where('id', '!=', $exclude_history_id);
        }

        $overlapping = $query->first();

        if ($overlapping) {
            return [
                'valid' => false,
                'message' => sprintf(
                    'Periode yang dipilih (%s s/d %s) bertumpang tindih dengan periode yang sudah ada (%s s/d %s)',
                    date('d/m/Y', strtotime($start_date)),
                    date('d/m/Y', strtotime($end_date)),
                    date('d/m/Y', strtotime($overlapping->period_start_date)),
                    date('d/m/Y', strtotime($overlapping->period_end_date))
                )
            ];
        }

        return ['valid' => true];
    }

    public function updateHistory(int $history_id, array $data): int
    {
        return DB::table($this->history_table)
            ->where('id', $history_id)
            ->update([
                'budged_limit_value' => $data['budged_limit_value'],
                'budged_remaining_value' => $data['budged_remaining_value'],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
    }

    public function findHistoryByBudgedId($budged_id)
    {
        return DB::table($this->history_table)
            ->where('budged_id', $budged_id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function findHistoryWithExpenses($budged_id)
    {
        // Get all periods for this budget
        $periods = DB::table($this->history_table)
            ->where('budged_id', $budged_id)
            ->orderBy('period_start_date', 'DESC')
            ->get();
        // For each period, get associated expenses
        foreach ($periods as $period) {
            $period->expenses = DB::table('t_budged_expenses as be')
                ->join('t_expenses as e', 'be.expense_id', '=', 'e.id')
                ->leftJoin('m_contacts as c', 'e.f_receiver_customer_id', '=', 'c.id')
                ->where('be.budged_history_id', $period->id)
                ->select([
                    'e.id',
                    'e.type',
                    'e.transaction_no',
                    'e.transaction_date',
                    'e.description',
                    'e.total_amount',
                    'e.to_be_paid_later',
                    'e.total_paid_amount',
                    'e.total_remaining_amount',
                    'c.name as receiver_name',
                ])
                ->orderBy('e.transaction_date', 'DESC')
                ->get();
        }

        return $periods;
    }

    public function findHistoryOne($history_id)
    {
        return DB::table($this->history_table)
            ->where('id', $history_id)
            ->first();
    }

    public function deleteHistory($history_id): int
    {
        return DB::table($this->history_table)
            ->where('id', $history_id)
            ->delete();
    }

    public function getActiveBudgets()
    {
        $currentDate = now()->toDateString();

        return DB::table($this->table_name . ' as b')
            ->join($this->history_table . ' as bh', 'b.id', '=', 'bh.budged_id')
            ->where('bh.period_start_date', '<=', $currentDate)
            ->where('bh.period_end_date', '>=', $currentDate)
            ->select([
                'bh.id as history_id',
                'b.id as budged_id',
                'b.budged_name',
                'bh.period_start_date',
                'bh.period_end_date',
                'bh.budged_limit_value',
                'bh.budged_remaining_value'
            ])
            ->orderBy('b.budged_name')
            ->get();
    }
}
