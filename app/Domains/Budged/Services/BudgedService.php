<?php

namespace App\Domains\Budged\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgedService
{
    private $table_name = 'm_budgeds';
    private $period_table = 'm_budget_periods';

    // ========================================================================
    // PERIOD METHODS
    // ========================================================================

    public function createPeriod(array $data)
    {
        return DB::table($this->period_table)->insertGetId([
            'period_start_date' => $data['period_start_date'],
            'period_end_date' => $data['period_end_date'],
            'type' => $data['type'] ?? 'kingdom',
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findPeriodById($periodId)
    {
        $period = DB::table($this->period_table)
            ->where('id', $periodId)
            ->first();

        if ($period == null) {
            abort(404);
        }

        return $period;
    }

    public function findAllDatatable()
    {
        // Get periods with their budget counts and totals
        $query = DB::table($this->period_table . ' as bp')
            ->leftJoin($this->table_name . ' as b', 'bp.id', '=', 'b.f_budget_period_id')
            ->select([
                'bp.id as period_id',
                'bp.period_start_date',
                'bp.period_end_date',
                DB::raw('COUNT(b.id) as budget_count'),
                DB::raw('COALESCE(SUM(b.budged_limit_value), 0) as total_limit'),
                DB::raw('COALESCE(SUM(b.budged_remaining_value), 0) as total_remaining'),
            ])
            ->groupBy('bp.id', 'bp.period_start_date', 'bp.period_end_date');

        // Search filter
        if ($search = request()->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('bp.period_start_date', 'like', "%{$search}%")
                  ->orWhere('bp.period_end_date', 'like', "%{$search}%")
                  ->orWhere('b.budged_name', 'like', "%{$search}%");
            });
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->period_table)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('bp.period_start_date', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    // ========================================================================
    // BUDGET METHODS
    // ========================================================================

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'f_budget_period_id' => $data['f_budget_period_id'],
            'f_budget_category_id' => $data['f_budget_category_id'] ?? null,
            'budged_name' => $data['budged_name'],
            'budged_limit_value' => $data['budged_limit_value'],
            'budged_remaining_value' => $data['budged_remaining_value'] ?? $data['budged_limit_value'],
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'budged_limit_value' => $data['budged_limit_value'],
                'budged_remaining_value' => $data['budged_remaining_value'],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
    }

    public function findByPeriodId($periodId)
    {
        return DB::table($this->table_name)
            ->where('f_budget_period_id', $periodId)
            ->orderBy('budged_name', 'ASC')
            ->get();
    }

    public function findById($id)
    {
        $data = DB::table($this->table_name)
            ->where('id', $id)
            ->first();

        if ($data == null) {
            abort(404);
        }

        return $data;
    }

    public function findOne($id)
    {
        return $this->findById($id);
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'budged_name', 'f_budget_period_id')
            ->orderBy('budged_name', 'ASC')
            ->get();
    }

    public function deleteBudget($id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }

    // ========================================================================
    // ACTIVE BUDGETS (for expense tracking)
    // ========================================================================

    public function getActiveBudgets($entityType = null)
    {
        $currentDate = now()->toDateString();

        $query = DB::table($this->table_name . ' as b')
            ->join($this->period_table . ' as bp', 'b.f_budget_period_id', '=', 'bp.id')
            ->where('bp.period_start_date', '<=', $currentDate)
            ->where('bp.period_end_date', '>=', $currentDate);

        // Filter by entity type if provided
        if ($entityType) {
            $query->where('bp.type', $entityType);
        }

        return $query->select([
                'b.id as budget_id',
                'b.budged_name',
                'b.budged_limit_value',
                'b.budged_remaining_value',
                'bp.id as period_id',
                'bp.period_start_date',
                'bp.period_end_date',
                'bp.type as entity_type',
            ])
            ->orderBy('bp.type')
            ->orderBy('b.budged_name')
            ->get();
    }

    // ========================================================================
    // EXPENSE TRACKING
    // ========================================================================

    public function updateBudgetRemaining($budgetId, $amount)
    {
        return DB::table($this->table_name)
            ->where('id', $budgetId)
            ->decrement('budged_remaining_value', $amount, [
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
    }

    public function getBudgetWithExpenses($budgetId)
    {
        $budget = $this->findOne($budgetId);

        // Get expenses for this budget
        $budget->expenses = DB::table('t_budged_expenses as be')
            ->join('t_expenses as e', 'be.expense_id', '=', 'e.id')
            ->leftJoin('m_contacts as c', 'e.f_receiver_customer_id', '=', 'c.id')
            ->where('be.budged_history_id', $budgetId)
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

        return $budget;
    }

    public function getPeriodWithBudgetsAndExpenses($periodId)
    {
        $period = $this->findPeriodById($periodId);

        // Get all budgets for this period
        $budgets = $this->findByPeriodId($periodId);

        // For each budget, get associated expenses
        foreach ($budgets as $budget) {
            $budget->expenses = DB::table('t_budged_expenses as be')
                ->join('t_expenses as e', 'be.expense_id', '=', 'e.id')
                ->leftJoin('m_contacts as c', 'e.f_receiver_customer_id', '=', 'c.id')
                ->where('be.budged_history_id', $budget->id)
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

        $period->budgets = $budgets;
        return $period;
    }

    // ========================================================================
    // BUDGET HISTORY/PERIOD MANAGEMENT METHODS
    // ========================================================================

    public function validatePeriodOverlap($budgedId, $periodStartDate, $periodEndDate, $excludeHistoryId = null)
    {
        $query = DB::table('m_budgeds_history')
            ->where('budged_id', $budgedId)
            ->where(function ($q) use ($periodStartDate, $periodEndDate) {
                $q->whereBetween('period_start_date', [$periodStartDate, $periodEndDate])
                  ->orWhereBetween('period_end_date', [$periodStartDate, $periodEndDate])
                  ->orWhere(function ($q2) use ($periodStartDate, $periodEndDate) {
                      $q2->where('period_start_date', '<=', $periodStartDate)
                         ->where('period_end_date', '>=', $periodEndDate);
                  });
            });

        if ($excludeHistoryId) {
            $query->where('id', '!=', $excludeHistoryId);
        }

        $overlapping = $query->first();

        if ($overlapping) {
            return [
                'valid' => false,
                'message' => 'Periode anggaran tumpang tindih dengan periode yang sudah ada: ' .
                            date('d/m/Y', strtotime($overlapping->period_start_date)) . ' - ' .
                            date('d/m/Y', strtotime($overlapping->period_end_date))
            ];
        }

        return ['valid' => true];
    }

    public function createHistory($budgedId, array $data)
    {
        return DB::table('m_budgeds_history')->insertGetId([
            'budged_id' => $budgedId,
            'period_start_date' => $data['period_start_date'],
            'period_end_date' => $data['period_end_date'],
            'budged_limit_value' => $data['budged_limit_value'],
            'budged_remaining_value' => $data['budged_limit_value'],
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findHistoryOne($historyId)
    {
        $history = DB::table('m_budgeds_history')
            ->where('id', $historyId)
            ->first();

        if ($history == null) {
            abort(404);
        }

        return $history;
    }

    public function updateHistory($historyId, array $data)
    {
        return DB::table('m_budgeds_history')
            ->where('id', $historyId)
            ->update([
                'period_start_date' => $data['period_start_date'],
                'period_end_date' => $data['period_end_date'],
                'budged_limit_value' => $data['budged_limit_value'],
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ]);
    }

    public function deleteHistory($historyId)
    {
        return DB::table('m_budgeds_history')
            ->where('id', $historyId)
            ->delete();
    }

    public function findHistoryByBudgedId($budgedId)
    {
        return DB::table('m_budgeds_history')
            ->where('budged_id', $budgedId)
            ->orderBy('period_start_date', 'DESC')
            ->get();
    }
}
