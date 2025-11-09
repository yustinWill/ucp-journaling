<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\RevenueSplit\Actions\CreateRevenueSplitAction;
use App\Domains\RevenueSplit\Actions\UpdateRevenueSplitAction;
use App\Domains\RevenueSplit\Services\RevenueSplitAuditLogService;
use App\Domains\RevenueSplit\Services\RevenueSplitService;
use App\Enums\AuditLogTypes;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RevenueSplitController extends Controller
{
    private $statuses = [
        ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'SELESAI', 'label' => 'SELESAI - Selesai'],
    ];

    public function index()
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        $statuses = $this->statuses;
        $summaryCards = app(RevenueSplitService::class)->getSummaryCards();
        $permissionData = PermissionHelper::getViewPermissions('master_data_revenue_split');

        return view('pages.master_data.revenue_split.index', array_merge(
            compact('statuses', 'summaryCards'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(RevenueSplitService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_revenue_split');

        return view('pages.master_data.revenue_split.add', array_merge([
            'statuses' => $statuses,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyimpan data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'cycle_name' => 'required|string|max:255|unique:m_revenue_splits,cycle_name',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'target_pabrik' => 'required|numeric|min:0',
                'target_owner' => 'required|numeric|min:0',
                'target_perusahaan' => 'required|numeric|min:0',
                'notes' => 'nullable|string',
            ], [], [
                'cycle_name' => 'Nama Cycle',
                'description' => 'Deskripsi',
                'start_date' => 'Tanggal Mulai',
                'end_date' => 'Tanggal Selesai',
                'target_pabrik' => 'Target Keuntungan Pabrik',
                'target_owner' => 'Target Keuntungan Owner',
                'target_perusahaan' => 'Target Keuntungan Perusahaan',
                'notes' => 'Catatan',
            ]);

            // Set default status
            $validated['status'] = 'DRAFT';

            app(CreateRevenueSplitAction::class)->execute($validated);

            return response()->json([
                'message' => 'Cycle keuntungan penjualan berhasil ditambahkan.',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $revenueSplt = app(RevenueSplitService::class)->findOne($id);

        if ($revenueSplt == null) {
            abort(404);
        }

        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_revenue_split');

        return view('pages.master_data.revenue_split.edit', array_merge([
            'statuses' => $statuses,
            'revenueSplt' => $revenueSplt,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_revenue_splits,id',
                'cycle_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'target_pabrik' => 'required|numeric|min:0',
                'target_owner' => 'required|numeric|min:0',
                'target_perusahaan' => 'required|numeric|min:0',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'notes' => 'nullable|string',
            ], [], [
                'id' => 'ID',
                'cycle_name' => 'Nama Cycle',
                'description' => 'Deskripsi',
                'start_date' => 'Tanggal Mulai',
                'end_date' => 'Tanggal Selesai',
                'target_pabrik' => 'Target Keuntungan Pabrik',
                'target_owner' => 'Target Keuntungan Owner',
                'target_perusahaan' => 'Target Keuntungan Perusahaan',
                'status' => 'Status',
                'notes' => 'Catatan',
            ]);

            app(UpdateRevenueSplitAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Cycle keuntungan penjualan berhasil diperbarui.',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function detail($id)
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail data.');
        }

        $revenueSplt = app(RevenueSplitService::class)->findOne($id);

        if ($revenueSplt == null) {
            abort(404);
        }

        // Get sales orders and related data for the cycle period
        $salesOrdersData = $this->getSalesOrderDataForCycle($revenueSplt);
        
        // dd($salesOrdersData);
        $auditLogs = app(RevenueSplitAuditLogService::class)->findAllBasedOnRevenueSplitId($id);
        $permissionData = PermissionHelper::getViewPermissions('master_data_revenue_split');

        return view('pages.master_data.revenue_split.detail', array_merge(
            compact('revenueSplt', 'auditLogs'),
            $salesOrdersData,
            $permissionData,
            ['auditLogType' => AuditLogTypes::MASTER_DATA_REVENUE_SPLIT->code()]
        ));
    }

    private function getSalesOrderDataForCycle($cycle)
    {
        // Get sales orders with product details within the cycle period
        $salesOrdersRaw = DB::table('t_sales_orders as so')
            ->leftJoin('t_sales_order_products as sop', 'sop.f_sales_order_id', '=', 'so.id')
            ->leftJoin('m_products as p', 'p.id', '=', 'sop.f_product_id')
            ->leftJoin('m_categories as c', 'c.id', '=', 'p.f_category_id')
            ->leftJoin('m_contacts as cust', 'cust.id', '=', 'so.f_customer_id')
            ->leftJoin('t_sales_invoices as si', 'si.f_sales_order_id', '=', 'so.id')
            ->leftJoin('t_sales_order_revenue_splits as sors', function($join) use ($cycle) {
                $join->on('sors.f_sales_order_id', '=', 'so.id');
                $join->on('sors.f_sales_order_product_id', '=', 'sop.id');
            })
            ->select([
                'so.id as order_id',
                'so.transaction_code',
                'so.transaction_date',
                'so.grand_price as order_total',
                'so.status as order_status',
                'p.id as product_id',
                'p.name as product_name',
                'p.code as product_code',
                'c.name as category_name',
                'sop.quantity',
                'sop.unit_price',
                'sop.line_total',
                'cust.name as customer_name',
                'cust.type as customer_type',
                'si.grand_total as invoice_total',
                'si.status as invoice_status',
                'sors.porsi_perusahaan AS porsi_perusahaan',
                'sors.porsi_owner AS porsi_owner',
                'sors.porsi_pabrik AS porsi_pabrik',
            ])
            ->where('so.company_code', session('user_company_code'))
            ->whereBetween('so.transaction_date', [$cycle->start_date, $cycle->end_date])
            ->orderBy('so.transaction_date', 'desc')
            ->get();


        $expected_keuntungan_parbrik = 0;
        $expected_keuntungan_owner = 0;
        $expected_keuntungan_perusahaan = 0;
        $expected_total_achievement = 0;

        $actual_keuntungan_parbrik = 0;
        $actual_keuntungan_owner = 0;
        $actual_keuntungan_perusahaan = 0;
        $actual_total_achievement = 0;
        
        $total_so = 0;
        $total_so_paid = 0;

        $so_counter_arr = [];
        foreach ($salesOrdersRaw as $row) {
            $so_counter_arr[] = [
                'order_id' => $row->order_id,
                'invoice_status' => $row->invoice_status,
            ];

            if ($row->invoice_status === 'PAID') {
                $actual_keuntungan_parbrik += $row->porsi_pabrik;
                $actual_keuntungan_owner += $row->porsi_owner;
                $actual_keuntungan_perusahaan += $row->porsi_perusahaan;
            }
            else{
                $expected_keuntungan_parbrik += $row->porsi_pabrik;
                $expected_keuntungan_owner += $row->porsi_owner;
                $expected_keuntungan_perusahaan += $row->porsi_perusahaan;
            }
        }

        $expected_total_achievement = $expected_keuntungan_parbrik + $expected_keuntungan_owner + $expected_keuntungan_perusahaan;
        $actual_total_achievement = $actual_keuntungan_parbrik + $actual_keuntungan_owner + $actual_keuntungan_perusahaan;

        $distinctSoCounter = array_values(array_unique($so_counter_arr, SORT_REGULAR));
        $total_so = count($distinctSoCounter);
        foreach ($distinctSoCounter as $so) {
            if ($so['invoice_status'] === 'PAID') {
                $total_so_paid += 1;
            }
        }



         $salesOrdersGrouped = [];
        foreach ($salesOrdersRaw as $item) {
            $orderId = $item->order_id;
            if (!isset($salesOrdersGrouped[$orderId])) {
                $salesOrdersGrouped[$orderId] = [
                    'so_number' => $item->transaction_code,
                    'reference_no' => $item->transaction_code, // Use transaction_code as reference
                    'transaction_date' => $item->transaction_date,
                    'customer_name' => $item->customer_name,
                    'customer_type' => $item->customer_type,
                    'status' => $item->order_status,
                    'total_amount' => $item->order_total,
                    'profit_amount' => 0,
                    'profit_margin' => 0,
                    'total_quantity' => 0,
                    'product_count' => 0,
                    'invoice_status' => $item->invoice_status, // Include invoice status
                    'products' => []
                ];
            }
            
            if ($item->product_id) {
                $salesOrdersGrouped[$orderId]['total_quantity'] += $item->quantity;
                $salesOrdersGrouped[$orderId]['product_count']++;
                $salesOrdersGrouped[$orderId]['products'][] = [
                    'name' => $item->product_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total
                ];
            }
        }

        // Calculate profit for each sales order and track revenue status
        $expectedRevenue = 0;
        $actualRevenue = 0;
        $totalSalesOrders = 0;
        $paidSalesOrders = 0;
        $unpaidSalesOrders = 0;
        $noInvoiceSalesOrders = 0;

        foreach ($salesOrdersGrouped as &$so) {
            $so['profit_amount'] = $so['total_amount'] * 1; // Assume 22.5% profit margin
            $so['profit_margin'] = 100;
            
            $totalSalesOrders++;
            $expectedRevenue += $so['total_amount'];
            // Determine revenue status based on invoice
            if (isset($so['invoice_status'])) {
                if ($so['invoice_status'] === 'PAID') {
                    $actualRevenue += $so['total_amount'];
                    $paidSalesOrders++;
                } else {
                    $unpaidSalesOrders++;
                }
            } else {
                $noInvoiceSalesOrders++;
            }
        }

        $salesOrders = array_values($salesOrdersGrouped);

        // Group products by category and calculate totals using raw data
        $productsByCategory = [];
        $totalSalesValue = 0;
        $totalQuantity = 0;
        $categoryTotals = [];

        foreach ($salesOrdersRaw as $order) {
            if ($order->product_id) {
                $category = $order->category_name ?: 'Uncategorized';
                $productKey = $order->product_id;

                if (!isset($productsByCategory[$category])) {
                    $productsByCategory[$category] = [];
                }

                if (!isset($productsByCategory[$category][$productKey])) {
                    $productsByCategory[$category][$productKey] = [
                        'product_name' => $order->product_name,
                        'product_code' => $order->product_code,
                        'category' => $category,
                        'total_sales' => 0,
                        'total_quantity' => 0,
                        'profit_margin' => 100, // Default margin
                        'orders_count' => 0
                    ];
                }

                $productsByCategory[$category][$productKey]['total_sales'] += $order->line_total;
                $productsByCategory[$category][$productKey]['total_quantity'] += $order->quantity;
                $productsByCategory[$category][$productKey]['orders_count']++;

                $totalSalesValue += $order->line_total;
                $totalQuantity += $order->quantity;

                // Calculate category totals
                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = 0;
                }
                $categoryTotals[$category] += $order->line_total;
            }
        }

        // Flatten products array and calculate contributions
        $products = [];
        foreach ($productsByCategory as $category => $categoryProducts) {
            foreach ($categoryProducts as $product) {
                $profit = $product['total_sales'] * ($product['profit_margin'] / 100);
                $contribution = $totalSalesValue > 0 ? ($profit / ($totalSalesValue * 0.225)) * 100 : 0;
                
                $product['profit_amount'] = $profit;
                $product['contribution_percentage'] = $contribution;
                $products[] = $product;
            }
        }

        // Sort by contribution percentage
        usort($products, function($a, $b) {
            return $b['contribution_percentage'] <=> $a['contribution_percentage'];
        });

        // Calculate summary metrics
        // $totalProfit = $totalSalesValue * 0.225; // 100% margin
        // $variantCount = count($products);
        
        // // Calculate category-specific revenue distribution
        // // Distribution percentages based on targets
        // $pabrikPercentage = $cycle->target_pabrik / $cycle->total_target;
        // $ownerPercentage = $cycle->target_owner / $cycle->total_target;
        // $perusahaanPercentage = $cycle->target_perusahaan / $cycle->total_target;
        
        // // Distribute actual revenue across categories
        // $actualPabrikRevenue = $actualRevenue * $pabrikPercentage;
        // $actualOwnerRevenue = $actualRevenue * $ownerPercentage;
        // $actualPerusahaanRevenue = $actualRevenue * $perusahaanPercentage;
        
        // // Calculate achievement percentages (can exceed 100% but capped for display)
        // $pabrikAchievement = $cycle->target_pabrik > 0 ? ($actualPabrikRevenue / $cycle->target_pabrik) * 100 : 0;
        // $ownerAchievement = $cycle->target_owner > 0 ? ($actualOwnerRevenue / $cycle->target_owner) * 100 : 0;
        // $perusahaanAchievement = $cycle->target_perusahaan > 0 ? ($actualPerusahaanRevenue / $cycle->target_perusahaan) * 100 : 0;
        
        // // Total achievement is not just average - it's actual total vs target total
        // $totalAchievement = $cycle->total_target > 0 ? ($actualRevenue / $cycle->total_target) * 100 : 0;








        

        // // Group by sales order to get unique sales orders for the tab
        // $salesOrdersGrouped = [];
        // foreach ($salesOrdersRaw as $item) {
        //     $orderId = $item->order_id;
        //     if (!isset($salesOrdersGrouped[$orderId])) {
        //         $salesOrdersGrouped[$orderId] = [
        //             'so_number' => $item->transaction_code,
        //             'reference_no' => $item->transaction_code, // Use transaction_code as reference
        //             'transaction_date' => $item->transaction_date,
        //             'customer_name' => $item->customer_name,
        //             'customer_type' => $item->customer_type,
        //             'status' => $item->order_status,
        //             'total_amount' => $item->order_total,
        //             'profit_amount' => 0,
        //             'profit_margin' => 0,
        //             'total_quantity' => 0,
        //             'product_count' => 0,
        //             'invoice_status' => $item->invoice_status, // Include invoice status
        //             'products' => []
        //         ];
        //     }
            
        //     if ($item->product_id) {
        //         $salesOrdersGrouped[$orderId]['total_quantity'] += $item->quantity;
        //         $salesOrdersGrouped[$orderId]['product_count']++;
        //         $salesOrdersGrouped[$orderId]['products'][] = [
        //             'name' => $item->product_name,
        //             'quantity' => $item->quantity,
        //             'unit_price' => $item->unit_price,
        //             'line_total' => $item->line_total
        //         ];
        //     }
        // }

        // // Calculate profit for each sales order and track revenue status
        // $expectedRevenue = 0;
        // $actualRevenue = 0;
        // $totalSalesOrders = 0;
        // $paidSalesOrders = 0;
        // $unpaidSalesOrders = 0;
        // $noInvoiceSalesOrders = 0;

        // foreach ($salesOrdersGrouped as &$so) {
        //     $so['profit_amount'] = $so['total_amount'] * 0.225; // Assume 22.5% profit margin
        //     $so['profit_margin'] = 100;
            
        //     $totalSalesOrders++;
        //     $expectedRevenue += $so['total_amount'];
        //     // dd($expectedRevenue);
        //     // Determine revenue status based on invoice
        //     if (isset($so['invoice_status'])) {
        //         if ($so['invoice_status'] === 'PAID') {
        //             $actualRevenue += $so['total_amount'];
        //             $paidSalesOrders++;
        //         } else {
        //             $unpaidSalesOrders++;
        //         }
        //     } else {
        //         $noInvoiceSalesOrders++;
        //     }
        // }

        // $salesOrders = array_values($salesOrdersGrouped);

        // // Group products by category and calculate totals using raw data
        // $productsByCategory = [];
        // $totalSalesValue = 0;
        // $totalQuantity = 0;
        // $categoryTotals = [];

        // foreach ($salesOrdersRaw as $order) {
        //     if ($order->product_id) {
        //         $category = $order->category_name ?: 'Uncategorized';
        //         $productKey = $order->product_id;

        //         if (!isset($productsByCategory[$category])) {
        //             $productsByCategory[$category] = [];
        //         }

        //         if (!isset($productsByCategory[$category][$productKey])) {
        //             $productsByCategory[$category][$productKey] = [
        //                 'product_name' => $order->product_name,
        //                 'product_code' => $order->product_code,
        //                 'category' => $category,
        //                 'total_sales' => 0,
        //                 'total_quantity' => 0,
        //                 'profit_margin' => 100, // Default margin
        //                 'orders_count' => 0
        //             ];
        //         }

        //         $productsByCategory[$category][$productKey]['total_sales'] += $order->line_total;
        //         $productsByCategory[$category][$productKey]['total_quantity'] += $order->quantity;
        //         $productsByCategory[$category][$productKey]['orders_count']++;

        //         $totalSalesValue += $order->line_total;
        //         $totalQuantity += $order->quantity;

        //         // Calculate category totals
        //         if (!isset($categoryTotals[$category])) {
        //             $categoryTotals[$category] = 0;
        //         }
        //         $categoryTotals[$category] += $order->line_total;
        //     }
        // }

        // // Flatten products array and calculate contributions
        // $products = [];
        // foreach ($productsByCategory as $category => $categoryProducts) {
        //     foreach ($categoryProducts as $product) {
        //         $profit = $product['total_sales'] * ($product['profit_margin'] / 100);
        //         $contribution = $totalSalesValue > 0 ? ($profit / ($totalSalesValue * 0.225)) * 100 : 0;
                
        //         $product['profit_amount'] = $profit;
        //         $product['contribution_percentage'] = $contribution;
        //         $products[] = $product;
        //     }
        // }

        // // Sort by contribution percentage
        // usort($products, function($a, $b) {
        //     return $b['contribution_percentage'] <=> $a['contribution_percentage'];
        // });

        // // Calculate summary metrics
        // $totalProfit = $totalSalesValue * 0.225; // 100% margin
        // $variantCount = count($products);
        
        // // Calculate category-specific revenue distribution
        // // Distribution percentages based on targets
        // $pabrikPercentage = $cycle->target_pabrik / $cycle->total_target;
        // $ownerPercentage = $cycle->target_owner / $cycle->total_target;
        // $perusahaanPercentage = $cycle->target_perusahaan / $cycle->total_target;
        
        // // Distribute actual revenue across categories
        // $actualPabrikRevenue = $actualRevenue * $pabrikPercentage;
        // $actualOwnerRevenue = $actualRevenue * $ownerPercentage;
        // $actualPerusahaanRevenue = $actualRevenue * $perusahaanPercentage;
        
        // // Calculate achievement percentages (can exceed 100% but capped for display)
        // $pabrikAchievement = $cycle->target_pabrik > 0 ? ($actualPabrikRevenue / $cycle->target_pabrik) * 100 : 0;
        // $ownerAchievement = $cycle->target_owner > 0 ? ($actualOwnerRevenue / $cycle->target_owner) * 100 : 0;
        // $perusahaanAchievement = $cycle->target_perusahaan > 0 ? ($actualPerusahaanRevenue / $cycle->target_perusahaan) * 100 : 0;
        
        // // Total achievement is not just average - it's actual total vs target total
        // $totalAchievement = $cycle->total_target > 0 ? ($actualRevenue / $cycle->total_target) * 100 : 0;

        // return [
            // 'products' => $products,
            // 'salesOrders' => $salesOrders,
            // 'totalSalesValue' => $totalSalesValue,
            // 'totalQuantity' => $totalQuantity,
            // 'totalProfit' => $totalProfit,
            // 'variantCount' => $variantCount,
            // 'marginPercentage' => 22.5,
            // 'categoryTotals' => $categoryTotals,
            // // Revenue tracking data
            // 'expectedRevenue' => $expectedRevenue,
            // 'actualRevenue' => $actualRevenue,
            // 'totalSalesOrders' => $totalSalesOrders,
            // 'paidSalesOrders' => $paidSalesOrders,
            // 'unpaidSalesOrders' => $unpaidSalesOrders,
            // 'noInvoiceSalesOrders' => $noInvoiceSalesOrders,
            // // Category-specific progress data
            // 'actualPabrikRevenue' => $actualPabrikRevenue,
            // 'actualOwnerRevenue' => $actualOwnerRevenue,
            // 'actualPerusahaanRevenue' => $actualPerusahaanRevenue,
            // 'pabrikAchievement' => $pabrikAchievement,
            // 'ownerAchievement' => $ownerAchievement,
            // 'perusahaanAchievement' => $perusahaanAchievement,
            // 'totalAchievement' => $totalAchievement
        // ];

        return [
            'expected_keuntungan_parbrik' => $expected_keuntungan_parbrik,
            'expected_keuntungan_owner' => $expected_keuntungan_owner,
            'expected_keuntungan_perusahaan' => $expected_keuntungan_perusahaan,
            'expected_total_achievement' => $expected_total_achievement,
            'actual_keuntungan_parbrik' => $actual_keuntungan_parbrik,
            'actual_keuntungan_owner' => $actual_keuntungan_owner,
            'actual_keuntungan_perusahaan' => $actual_keuntungan_perusahaan,
            'actual_total_achievement' => $actual_total_achievement,
            'total_so' => $total_so,
            'total_so_paid' => $total_so_paid,
            'salesOrdersTable' => $salesOrders
        ];
    }

    public function preview($id)
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'read')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cycle = app(RevenueSplitService::class)->findOne($id);
        if (!$cycle) {
            return response()->json(['success' => false, 'message' => 'Cycle not found'], 404);
        }

        // Get sales orders within the cycle period
        $salesOrders = DB::table('t_sales_orders as so')
            ->leftJoin('m_contacts as c', 'c.id', '=', 'so.f_customer_id')
            ->leftJoin('t_sales_invoices as si', 'si.f_sales_order_id', '=', 'so.id')
            ->select([
                'so.id',
                'so.transaction_code',
                'so.transaction_date',
                'so.grand_price',
                'so.status as so_status',
                'c.name as customer_name',
                'si.id as invoice_id',
                'si.transaction_code as invoice_code',
                'si.grand_total as invoice_total',
                'si.status as invoice_status'
            ])
            ->where('so.company_code', session('user_company_code'))
            ->whereBetween('so.transaction_date', [$cycle->start_date, $cycle->end_date])
            ->orderBy('so.transaction_date', 'desc')
            ->get();

        // Group and calculate totals
        $totalSalesOrders = $salesOrders->count();
        $totalSalesValue = $salesOrders->sum('grand_price');
        $invoicedOrders = $salesOrders->whereNotNull('invoice_id');
        $totalInvoiced = $invoicedOrders->sum('invoice_total');
        $paidInvoices = $invoicedOrders->where('invoice_status', 'PAID');
        $totalPaid = $paidInvoices->sum('invoice_total');

        // Generate preview HTML
        $previewHtml = view('pages.master_data.revenue_split.preview', [
            'cycle' => $cycle,
            'salesOrders' => $salesOrders,
            'totalSalesOrders' => $totalSalesOrders,
            'totalSalesValue' => $totalSalesValue,
            'totalInvoiced' => $totalInvoiced,
            'totalPaid' => $totalPaid,
            'invoicedCount' => $invoicedOrders->count(),
            'paidCount' => $paidInvoices->count()
        ])->render();

        return response()->json([
            'success' => true,
            'cycle' => $cycle,
            'html' => $previewHtml
        ]);
    }

    public function audit_log_data_table($id)
    {
        $data = app(RevenueSplitAuditLogService::class)->findAllDatatableBasedOnRevenueSplitId($id);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function export()
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        $data = [
            ['no' => 1, 'value' => 'Nama Cycle'],
            ['no' => 2, 'value' => 'Deskripsi'],
            ['no' => 3, 'value' => 'Tanggal Mulai'],
            ['no' => 4, 'value' => 'Tanggal Selesai'],
            ['no' => 5, 'value' => 'Target Keuntungan Pabrik'],
            ['no' => 6, 'value' => 'Target Keuntungan Owner'],
            ['no' => 7, 'value' => 'Target Keuntungan Perusahaan'],
            ['no' => 8, 'value' => 'Total Target Keuntungan'],
            ['no' => 9, 'value' => 'Status'],
            ['no' => 10, 'value' => 'Catatan'],
        ];

        return view('pages.master_data.revenue_split.export', ['data' => $data]);
    }

    public function import()
    {
        if (! PermissionHelper::hasPermission('master_data_revenue_split', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.master_data.revenue_split.import', []);
    }
}