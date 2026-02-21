<?php

namespace App\Http\Controllers\MainTransaction;

use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\ReimbursementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReimbursementController extends Controller
{
    private $reimbursementService;

    public function __construct(ReimbursementService $reimbursementService)
    {
        $this->reimbursementService = $reimbursementService;
    }

    /**
     * Display reimbursement list (staff view - only their own)
     */
    public function index()
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement', 'read')) {
        //     abort(403, 'Anda tidak memiliki akses untuk melihat data reimbursement.');
        // }

        $stats = $this->reimbursementService->getSummaryStatistics(auth()->id());
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_reimbursement');

        return view('pages.main_transaction.reimbursement.index', array_merge([
            'stats' => $stats,
        ], $permissionData));
    }

    /**
     * DataTable endpoint for staff reimbursements
     */
    public function index_data_table()
    {
        $data = $this->reimbursementService->findAllDatatableForStaff(auth()->id());

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    /**
     * Show add reimbursement form
     */
    public function add()
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement', 'write')) {
        //     abort(403, 'Anda tidak memiliki akses untuk menambah data reimbursement.');
        // }

        $permissionData = PermissionHelper::getViewPermissions('main_transaction_reimbursement');

        return view('pages.main_transaction.reimbursement.add', $permissionData);
    }

    /**
     * Store new reimbursement
     */
    public function store(Request $request)
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement', 'write')) {
        //     return response()->json([
        //         'message' => 'Anda tidak memiliki akses untuk menyimpan data reimbursement.',
        //         'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
        //     ], 403);
        // }

        try {
            $validated = $request->validate([
                'reimbursement_name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
            ], [], [
                'reimbursement_name' => 'Nama Reimbursement',
                'amount' => 'Jumlah',
                'description' => 'Deskripsi',
            ]);

            $reimbursement = $this->reimbursementService->create($validated, auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Reimbursement berhasil diajukan.',
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
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    /**
     * Show reimbursement detail
     */
    public function detail($id)
    {
        // Temporarily open for all
        // if (!PermissionHelper::hasPermission('main_transaction_reimbursement', 'read')) {
        //     abort(403, 'Anda tidak memiliki akses untuk melihat detail reimbursement.');
        // }

        $reimbursement = $this->reimbursementService->findOne($id);

        if (!$reimbursement) {
            abort(404, 'Reimbursement tidak ditemukan.');
        }

        // Check if user owns this reimbursement
        if ($reimbursement->created_by !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat reimbursement ini.');
        }

        $permissionData = PermissionHelper::getViewPermissions('main_transaction_reimbursement');

        return view('pages.main_transaction.reimbursement.detail', array_merge([
            'reimbursement' => $reimbursement,
        ], $permissionData));
    }
}
