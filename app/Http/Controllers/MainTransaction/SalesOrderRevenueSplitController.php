<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Services\SalesOrderRevenueSplitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class SalesOrderRevenueSplitController extends Controller
{
    private $revenueSplitService;

    public function __construct(SalesOrderRevenueSplitService $revenueSplitService)
    {
        $this->revenueSplitService = $revenueSplitService;
    }

    /**
     * Get revenue splits for a sales order
     */
    public function getRevenueSplits($salesOrderId)
    {
        try {
            $splits = $this->revenueSplitService->getRevenueSplitsForSalesOrder($salesOrderId);
            $summary = $this->revenueSplitService->getRevenueSplitSummary($salesOrderId);

            return response()->json([
                'success' => true,
                'data' => [
                    'splits' => $splits,
                    'summary' => $summary
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving revenue splits: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update revenue split for a specific product
     */
    public function updateRevenueSplit(Request $request, $salesOrderId, $productId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'porsi_perusahaan' => 'required|numeric|min:0',
                'porsi_owner' => 'required|numeric|min:0',
                'porsi_pabrik' => 'required|numeric|min:0',
                'remaining_allocation' => 'required|in:perusahaan,owner,pabrik',
                'product_total_amount' => 'required|numeric|min:0',
                'split_notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $splitData = $request->only([
                'porsi_perusahaan',
                'porsi_owner', 
                'porsi_pabrik',
                'remaining_allocation',
                'product_total_amount',
                'split_notes'
            ]);

            $this->revenueSplitService->updateRevenueSplit($salesOrderId, $productId, $splitData);

            return response()->json([
                'success' => true,
                'message' => 'Revenue split updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating revenue split: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue split breakdown view for sales order
     */
    public function showRevenueSplitBreakdown($salesOrderId)
    {
        try {
            $splits = $this->revenueSplitService->getRevenueSplitsForSalesOrder($salesOrderId);
            $summary = $this->revenueSplitService->getRevenueSplitSummary($salesOrderId);

            $html = view('pages.main_transaction.sales_order.revenue_split_breakdown', [
                'splits' => $splits,
                'summary' => $summary,
                'salesOrderId' => $salesOrderId
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => [
                    'splits' => $splits,
                    'summary' => $summary
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading revenue split breakdown: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset revenue split to use default configuration
     */
    public function resetRevenueSplit($salesOrderId, $productId)
    {
        try {
            // Get current product info from sales order
            $companyCode = session('user_company_code');
            
            $soProduct = \DB::table('t_sales_order_products as sop')
                ->where('sop.f_sales_order_id', $salesOrderId)
                ->where('sop.f_product_id', $productId)
                ->first();

            if (!$soProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in sales order'
                ], 404);
            }

            // Delete existing split
            \DB::table('t_sales_order_revenue_splits')
                ->where('company_code', $companyCode)
                ->where('f_sales_order_id', $salesOrderId)
                ->where('f_product_id', $productId)
                ->delete();

            // Recreate with default configuration
            $productData = [
                'f_product_id' => $soProduct->f_product_id,
                'unit_price' => $soProduct->unit_price,
                'quantity' => $soProduct->quantity
            ];

            $this->revenueSplitService->createRevenueSplitsForSalesOrder($salesOrderId, [$productData]);

            return response()->json([
                'success' => true,
                'message' => 'Revenue split reset to default configuration'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting revenue split: ' . $e->getMessage()
            ], 500);
        }
    }
}