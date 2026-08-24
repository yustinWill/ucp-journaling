<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Services\RevenueSplitCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProductRevenueSplitController extends Controller
{
    private $tableName = 'm_product_revenue_split_configs';

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Get all product revenue split configurations
     */
    public function getAllConfigurations()
    {
        try {
            $companyCode = session('user_company_code');
            
            // Get all products with their revenue split configurations
            $products = DB::table('m_products as p')
                ->leftJoin($this->tableName . ' as rsc', function($join) {
                    $join->on('p.id', '=', 'rsc.f_product_id')
                         ->where('rsc.status', '=', 'ACTIVE');
                })
                ->leftJoin('m_categories as c', 'p.f_category_id', '=', 'c.id')
                ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                ->where('p.company_code', $companyCode)
                ->where('p.status', 'ACTIVE')
                ->select([
                    'p.id as product_id',
                    'p.code as product_code', 
                    'p.name as product_name',
                    'p.unit_purchase_price',
                    'c.name as category_name',
                    'u.name as unit_name',
                    'rsc.id as config_id',
                    'rsc.porsi_perusahaan',
                    'rsc.porsi_owner',
                    'rsc.porsi_pabrik',
                    'rsc.remaining_allocation',
                    'rsc.base_price',
                    'rsc.notes',
                    'rsc.created_at as config_created_at'
                ])
                ->orderBy('p.name')
                ->get();

            $configurations = [];
            foreach ($products as $product) {
                $isConfigured = !is_null($product->config_id);
                
                $configurations[] = [
                    'product_id' => $product->product_id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'category_name' => $product->category_name,
                    'unit_name' => $product->unit_name,
                    'unit_purchase_price' => $product->unit_purchase_price,
                    'base_price' => $isConfigured ? $product->base_price : $product->unit_purchase_price,
                    'porsi_perusahaan' => $isConfigured ? $product->porsi_perusahaan : 0,
                    'porsi_owner' => $isConfigured ? $product->porsi_owner : 0,
                    'porsi_pabrik' => $isConfigured ? $product->porsi_pabrik : 0,
                    'remaining_allocation' => $isConfigured ? $product->remaining_allocation : 'perusahaan',
                    'notes' => $isConfigured ? $product->notes : null,
                    'is_configured' => $isConfigured,
                    'config_created_at' => $product->config_created_at
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $configurations
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving configurations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue split configuration for a product
     */
    public function getConfiguration($productId)
    {
        try {
            $companyCode = session('user_company_code');
            
            $config = DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_product_id', $productId)
                ->where('status', 'ACTIVE')
                ->first();

            // Get product details
            $product = DB::table('m_products')
                ->where('id', $productId)
                ->where('company_code', $companyCode)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'config' => $config,
                    'has_config' => !is_null($config)
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save or update revenue split configuration
     */
    public function saveConfiguration(Request $request, $productId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'porsi_perusahaan' => 'required|numeric|min:0',
                'porsi_owner' => 'required|numeric|min:0',
                'porsi_pabrik' => 'required|numeric|min:0',
                'remaining_allocation' => 'required|in:perusahaan,owner,pabrik',
                'base_price' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyCode = session('user_company_code');
            $userId = Auth::id();

            // Validate that the sum of fixed portions doesn't exceed base price
            $totalFixed = $request->porsi_perusahaan + $request->porsi_owner + $request->porsi_pabrik;
            if ($totalFixed > $request->base_price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total fixed portions cannot exceed base price'
                ], 422);
            }

            // Verify product exists
            $product = DB::table('m_products')
                ->where('id', $productId)
                ->where('company_code', $companyCode)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            DB::beginTransaction();

            // Deactivate existing configuration
            DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_product_id', $productId)
                ->where('status', 'ACTIVE')
                ->update([
                    'status' => 'INACTIVE',
                    'updated_by' => $userId,
                    'updated_at' => now()
                ]);

            // Create new configuration
            $configId = DB::table($this->tableName)->insertGetId([
                'company_code' => $companyCode,
                'f_product_id' => $productId,
                'porsi_perusahaan' => $request->porsi_perusahaan,
                'porsi_owner' => $request->porsi_owner,
                'porsi_pabrik' => $request->porsi_pabrik,
                'remaining_allocation' => $request->remaining_allocation,
                'base_price' => $request->base_price,
                'notes' => $request->notes,
                'status' => 'ACTIVE',
                'created_by' => $userId,
                'updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revenue split configuration saved successfully',
                'data' => [
                    'config_id' => $configId
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate revenue split for a given price
     */
    public function calculateSplit(Request $request, $productId, RevenueSplitCalculator $calculator)
    {
        try {
            $validator = Validator::make($request->all(), [
                'selling_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $companyCode = session('selected_company_code');
            
            $config = DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_product_id', $productId)
                ->where('status', 'ACTIVE')
                ->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'No revenue split configuration found for this product'
                ], 404);
            }

            $quantity = $calculator->wholeQuantity($request->quantity, 'Qty');
            $totalAmount = $calculator->grossTotal($request->selling_price, $quantity);
            $calculation = $calculator->calculate([
                'qty_total' => $quantity,
                'qty_anulir' => 0,
                'qty_bonus' => 0,
                'rate_pabrik' => $config->porsi_pabrik,
                'rate_owner' => $config->porsi_owner,
                'rate_perusahaan' => $config->porsi_perusahaan,
                'total_produk' => $totalAmount,
                'sisa_ke' => $config->remaining_allocation,
            ]);
            $calculator->assertSavable($calculation);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_amount' => $totalAmount,
                    'porsi_perusahaan' => $calculation['porsi_perusahaan'],
                    'porsi_owner' => $calculation['porsi_owner'],
                    'porsi_pabrik' => $calculation['porsi_pabrik'],
                    'remaining_allocation' => $calculation['remaining_allocation'],
                    'remaining_amount' => $calculation['remaining_amount'],
                    'config' => $config
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating split: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete revenue split configuration
     */
    public function deleteConfiguration($productId)
    {
        try {
            $companyCode = session('selected_company_code');
            $userId = Auth::id();

            $config = DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_product_id', $productId)
                ->where('status', 'ACTIVE')
                ->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active configuration found'
                ], 404);
            }

            DB::table($this->tableName)
                ->where('id', $config->id)
                ->update([
                    'status' => 'INACTIVE',
                    'updated_by' => $userId,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Revenue split configuration deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting configuration: ' . $e->getMessage()
            ], 500);
        }
    }
}
