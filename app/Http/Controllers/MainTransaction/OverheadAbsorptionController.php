<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\OverheadAbsorption\Services\OverheadAbsorptionService;
use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OverheadAbsorptionController extends Controller
{
    private $service;

    public function __construct()
    {
        $this->service = app(OverheadAbsorptionService::class);
    }

    public function index()
    {
        if (!PermissionHelper::hasPermission('main_transaction_overhead_absorption', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data absorpsi overhead.');
        }

        return view('pages.main_transaction.overhead_absorption.index');
    }

    /**
     * Get preview data for overhead absorption calculation
     */
    public function getPreview(Request $request)
    {
        try {
            $request->validate([
                'period' => 'required|date_format:Y-m',
            ]);

            $period = $request->input('period');
            $preview = $this->service->calculatePreview($period);

            return response()->json([
                'success' => true,
                'data' => $preview,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error getting overhead absorption preview: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get preview',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Settle overhead absorption
     */
    public function settle(Request $request)
    {
        if (!PermissionHelper::hasPermission('main_transaction_overhead_absorption', 'write')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses untuk melakukan settlement overhead.',
            ], 403);
        }

        try {
            $request->validate([
                'period' => 'required|date_format:Y-m',
                'notes' => 'nullable|string|max:1000',
            ]);

            $result = $this->service->settleOverhead(
                $request->input('period'),
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Overhead berhasil disettle',
                'data' => $result,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error settling overhead absorption: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get settlement history
     */
    public function getHistory(Request $request)
    {
        try {
            $settlements = $this->service->getSettlementHistory();

            return response()->json([
                'success' => true,
                'data' => $settlements,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting settlement history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get settlement history',
            ], 500);
        }
    }

    /**
     * Get settlement details
     */
    public function getDetails(Request $request, $id)
    {
        try {
            $details = $this->service->getSettlementDetails($id);

            return response()->json([
                'success' => true,
                'data' => $details,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting settlement details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
