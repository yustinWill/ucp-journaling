<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Budged\Services\BudgedService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\ReimbursementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReimbursementApprovalController extends Controller
{
    private $reimbursementService;

    public function __construct(ReimbursementService $reimbursementService)
    {
        $this->reimbursementService = $reimbursementService;
    }

    /**
     * Display reimbursement approval list (admin view - all reimbursements)
     */
    public function index()
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement_approval', 'read')) {
        //     abort(403, 'Anda tidak memiliki akses untuk melihat data approval reimbursement.');
        // }

        $stats = $this->reimbursementService->getSummaryStatistics();
        $activeBudgets = app(BudgedService::class)->getActiveBudgets();
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_reimbursement_approval');

        return view('pages.main_transaction.reimbursement_approval.index', array_merge([
            'stats' => $stats,
            'activeBudgets' => $activeBudgets,
        ], $permissionData));
    }

    /**
     * DataTable endpoint for all reimbursements (admin view)
     */
    public function index_data_table()
    {
        $data = $this->reimbursementService->findAllDatatableForAdmin();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    /**
     * Show reimbursement approval detail
     */
    public function detail($id)
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement_approval', 'read')) {
        //     abort(403, 'Anda tidak memiliki akses untuk melihat detail approval reimbursement.');
        // }

        $reimbursement = $this->reimbursementService->findOne($id);

        if (!$reimbursement) {
            abort(404, 'Reimbursement tidak ditemukan.');
        }

        // Get active budgets for budget category selection
        $activeBudgets = app(BudgedService::class)->getActiveBudgets();

        $permissionData = PermissionHelper::getViewPermissions('main_transaction_reimbursement_approval');

        return view('pages.main_transaction.reimbursement_approval.detail', array_merge([
            'reimbursement' => $reimbursement,
            'activeBudgets' => $activeBudgets,
        ], $permissionData));
    }

    /**
     * Get reimbursement detail as JSON (for modal)
     */
    public function detailJson($id)
    {
        $reimbursement = $this->reimbursementService->findOne($id);

        if (!$reimbursement) {
            return response()->json(['message' => 'Reimbursement tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reimbursement->id,
                'transaction_code' => $reimbursement->transaction_code,
                'reimbursement_name' => $reimbursement->reimbursement_name,
                'amount' => $reimbursement->amount,
                'description' => $reimbursement->description,
                'attachment' => $reimbursement->attachment,
                'status' => $reimbursement->status,
                'rejection_reason' => $reimbursement->rejection_reason,
                'created_at' => $reimbursement->created_at->format('d/m/Y H:i'),
                'creator_name' => $reimbursement->creator->name ?? '-',
                'reviewer_name' => $reimbursement->reviewer->name ?? null,
                'reviewed_at' => $reimbursement->reviewed_at ? $reimbursement->reviewed_at->format('d/m/Y H:i') : null,
                'budget_category_name' => $reimbursement->budget_category->budged_name ?? null,
            ],
        ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    /**
     * Approve reimbursement
     */
    public function approve(Request $request, $id)
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement_approval', 'write')) {
        //     return response()->json([
        //         'message' => 'Anda tidak memiliki akses untuk approve reimbursement.',
        //         'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
        //     ], 403);
        // }

        try {
            $validated = $request->validate([
                'budget_category_id' => 'required|exists:m_budgeds,id',
            ], [], [
                'budget_category_id' => 'Anggaran',
            ]);

            $reimbursement = $this->reimbursementService->approve(
                $id,
                $validated['budget_category_id'],
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Reimbursement berhasil disetujui.',
                'data' => $reimbursement,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    /**
     * Reject reimbursement
     */
    public function reject(Request $request, $id)
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement_approval', 'write')) {
        //     return response()->json([
        //         'message' => 'Anda tidak memiliki akses untuk reject reimbursement.',
        //         'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
        //     ], 403);
        // }

        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ], [], [
                'rejection_reason' => 'Alasan Penolakan',
            ]);

            $reimbursement = $this->reimbursementService->reject(
                $id,
                $validated['rejection_reason'],
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Reimbursement berhasil ditolak.',
                'data' => $reimbursement,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }
}
