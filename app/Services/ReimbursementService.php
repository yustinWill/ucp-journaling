<?php

namespace App\Services;

use App\Domains\Budged\Services\BudgedService;
use App\Enums\ReimbursementStatuses;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\DB;

class ReimbursementService
{
    /**
     * Generate reimbursement transaction code
     */
    public function generateTransactionCode(): string
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "REIMB-{$year}{$month}";

        // Get the latest reimbursement for this month
        $latest = Reimbursement::where('transaction_code', 'LIKE', "{$prefix}-%")
            ->orderBy('transaction_code', 'desc')
            ->first();

        if ($latest) {
            // Extract the sequence number and increment
            $parts = explode('-', $latest->transaction_code);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s-%03d', $prefix, $sequence);
    }

    /**
     * Build base datatable query
     */
    private function buildDatatableQuery($userId = null)
    {
        $query = DB::table('t_reimbursements as t')
            ->select([
                't.id',
                't.transaction_code',
                't.reimbursement_name',
                't.amount',
                't.description',
                't.status',
                't.rejection_reason',
                't.created_at',
                't.reviewed_at',
                'creator.name as creator_name',
                'reviewer.name as reviewer_name',
                'bc.budged_name as budget_category_name',
            ])
            ->leftJoin('users as creator', 'creator.id', '=', 't.created_by')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 't.reviewed_by')
            ->leftJoin('m_budgeds as bc', 'bc.id', '=', 't.f_budget_category_id')
            ->where('t.company_code', session('user_company_code'))
            ->whereNull('t.deleted_at');

        if ($userId) {
            $query->where('t.created_by', $userId);
        }

        if ($statusFilter = request()->input('status_filter')) {
            $query->where('t.status', $statusFilter);
        }

        return $query;
    }

    /**
     * Find all reimbursements for DataTable (staff view - only their own)
     */
    public function findAllDatatableForStaff($userId)
    {
        $query = $this->buildDatatableQuery($userId);

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table('t_reimbursements')
            ->where('company_code', session('user_company_code'))
            ->where('created_by', $userId)
            ->whereNull('deleted_at')
            ->count();

        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $data = $query->orderBy('t.id', 'DESC')->skip($start)->take($length)->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * Find all reimbursements for DataTable (admin view - all reimbursements)
     */
    public function findAllDatatableForAdmin()
    {
        $query = $this->buildDatatableQuery();

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table('t_reimbursements')
            ->where('company_code', session('user_company_code'))
            ->whereNull('deleted_at')
            ->count();

        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $data = $query->orderBy('t.id', 'DESC')->skip($start)->take($length)->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    /**
     * Find one reimbursement by ID
     */
    public function findOne($id)
    {
        $reimbursement = Reimbursement::with(['creator', 'reviewer'])
            ->where('company_code', session('user_company_code'))
            ->find($id);

        if ($reimbursement && $reimbursement->f_budget_category_id) {
            $reimbursement->budget_category = DB::table('m_budgeds')
                ->where('id', $reimbursement->f_budget_category_id)
                ->first();
        }

        return $reimbursement;
    }

    /**
     * Create a new reimbursement
     */
    public function create(array $data, $userId)
    {
        DB::beginTransaction();
        try {
            $data['transaction_code'] = $this->generateTransactionCode();
            $data['created_by'] = $userId;
            $data['company_code'] = session('user_company_code');
            $data['status'] = ReimbursementStatuses::PENDING;

            $reimbursement = Reimbursement::create($data);

            DB::commit();
            return $reimbursement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve reimbursement and assign budget category
     */
    public function approve($id, $budgetCategoryId, $reviewerId)
    {
        DB::beginTransaction();
        try {
            $reimbursement = $this->findOne($id);

            if (!$reimbursement) {
                throw new \Exception('Reimbursement not found');
            }

            if ($reimbursement->status !== ReimbursementStatuses::PENDING) {
                throw new \Exception('Only pending reimbursements can be approved');
            }

            $reimbursement->update([
                'status' => ReimbursementStatuses::APPROVED,
                'f_budget_category_id' => $budgetCategoryId,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            // Record budget usage (same pattern as expenses)
            DB::table('t_budged_reimbursements')->insert([
                'budged_history_id' => $budgetCategoryId,
                'reimbursement_id' => $reimbursement->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            app(BudgedService::class)->updateBudgetRemaining(
                $budgetCategoryId,
                $reimbursement->amount
            );

            DB::commit();
            return $reimbursement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reject reimbursement
     */
    public function reject($id, $rejectionReason, $reviewerId)
    {
        DB::beginTransaction();
        try {
            $reimbursement = $this->findOne($id);

            if (!$reimbursement) {
                throw new \Exception('Reimbursement not found');
            }

            if ($reimbursement->status !== ReimbursementStatuses::PENDING) {
                throw new \Exception('Only pending reimbursements can be rejected');
            }

            $reimbursement->update([
                'status' => ReimbursementStatuses::REJECTED,
                'rejection_reason' => $rejectionReason,
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now(),
            ]);

            DB::commit();
            return $reimbursement;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getSummaryStatistics($userId = null)
    {
        $query = Reimbursement::where('company_code', session('user_company_code'));

        if ($userId) {
            $query->where('created_by', $userId);
        }

        return [
            'total_pending' => $query->clone()->where('status', ReimbursementStatuses::PENDING)->count(),
            'total_approved' => $query->clone()->where('status', ReimbursementStatuses::APPROVED)->count(),
            'total_rejected' => $query->clone()->where('status', ReimbursementStatuses::REJECTED)->count(),
            'total_amount_approved' => $query->clone()->where('status', ReimbursementStatuses::APPROVED)->sum('amount'),
        ];
    }
}
