<?php

namespace App\Http\Controllers\MainTransaction;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopUpController extends Controller
{
    /**
     * Display Top Up index page
     */
    public function index()
    {
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_topup');
        return view('pages.main_transaction.topup.index', $permissionData);
    }

    /**
     * DataTable server-side processing for Top Up list
     */
    public function index_data_table(Request $request)
    {
        $companyCode = session('user_company_code');

        // Base query
        $query = DB::table('t_topup as tu')
            ->select([
                'tu.id',
                'tu.topup_number',
                'tu.topup_date',
                'tu.entity_type',
                'tu.topup_amount',
                'tu.allocated_amount',
                'tu.remaining_balance',
                'tu.status',
                'tu.created_at',
            ])
            ->where('tu.company_code', $companyCode);

        // Apply filters
        if ($request->filled('entity_type')) {
            $query->where('tu.entity_type', $request->entity_type);
        }

        if ($request->filled('start_date')) {
            $query->where('tu.topup_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('tu.topup_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('tu.status', $request->status);
        }

        // Search functionality (if needed)
        if ($request->filled('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('tu.topup_number', 'LIKE', "%{$search}%")
                    ->orWhere('tu.entity_type', 'LIKE', "%{$search}%");
            });
        }

        // Get total records before pagination
        $recordsTotal = DB::table('t_topup')
            ->where('company_code', $companyCode)
            ->count();

        // Get filtered records count
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Order by
        $query->orderBy('tu.topup_date', 'DESC')
            ->orderBy('tu.id', 'DESC');

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        // Get data
        $data = $query->get();

        // Format data for DataTable
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'topup_number' => $item->topup_number,
                'topup_date' => $item->topup_date,
                'entity_type' => $item->entity_type,
                'topup_amount' => $item->topup_amount,
                'allocated_amount' => $item->allocated_amount,
                'remaining_balance' => $item->remaining_balance,
                'status' => $item->status,
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
        ]);
    }

    /**
     * Show form to create new Top Up
     */
    public function add()
    {

        return view('pages.main_transaction.topup.add');
    }

    /**
     * Store new Top Up with FIFO allocation
     */
    public function store(Request $request)
    {

        // Validation
        $validated = $request->validate([
            'topup_date' => 'required|date',
            // 'entity_type' => 'required|in:KINGDOM,OWNER',
            'topup_amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [], [
            'topup_date' => 'Tanggal Top Up',
            // 'entity_type' => 'Entity',
            'topup_amount' => 'Jumlah Top Up',
            'notes' => 'Catatan',
            'attachment' => 'Lampiran',
        ]);

        try {
            DB::beginTransaction();

            $companyCode = session('user_company_code');
            $entityType = "OWNER";
            $topupAmount = $request->topup_amount;

            // Generate Top Up Number
            $topupNumber = app(\App\Services\TransactionNumberService::class)
                ->generateNumber('top_up');

            // Determine which field to update based on entity
            $isPaidField = $entityType === 'KINGDOM' ? 'is_paid_perusahaan' : 'is_paid_owner';
            $porsiField = $entityType === 'KINGDOM' ? 'porsi_perusahaan' : 'porsi_pabrik';

            // Get outstanding Sales Orders (same logic as getOutstandingSalesOrders)
            $outstandingSO = DB::table('t_sales_orders as so')
                ->join('t_sales_order_revenue_splits as rs', 'so.id', '=', 'rs.f_sales_order_id')
                ->select([
                    'so.id',
                    'so.transaction_code',
                    'so.transaction_date',
                    DB::raw("SUM(rs.{$porsiField}) as total_amount"),
                ])
                ->where('so.company_code', $companyCode)
                ->where('rs.' . $isPaidField, false)
                ->groupBy('so.id', 'so.transaction_code', 'so.transaction_date')
                ->orderBy('so.transaction_date', 'ASC')
                ->orderBy('so.id', 'ASC')
                ->get();

            // Calculate FIFO allocation
            $remainingTopup = $topupAmount;
            $allocations = [];
            $sequence = 1;
            $totalAllocated = 0;

            foreach ($outstandingSO as $so) {
                if ($remainingTopup <= 0) {
                    break;
                }

                // Get already allocated deposits
                $allocatedDeposit = DB::table('t_topup_sales_order')
                    ->where('sales_order_id', $so->id)
                    ->sum('allocated_amount');

                $soRemaining = $so->total_amount - $allocatedDeposit;

                // Skip if already fully paid
                if ($soRemaining <= 0) {
                    continue;
                }

                $allocationAmount = min($remainingTopup, $soRemaining);

                $allocations[] = [
                    'so_id' => $so->id,
                    'so_total' => $so->total_amount,
                    'so_remaining_before' => $soRemaining,
                    'allocated_amount' => $allocationAmount,
                    'so_remaining_after' => $soRemaining - $allocationAmount,
                    'sequence' => $sequence,
                ];

                $remainingTopup -= $allocationAmount;
                $totalAllocated += $allocationAmount;
                $sequence++;
            }

            // Determine Top Up status
            $topupStatus = 'OPEN';
            if ($totalAllocated > 0 && $remainingTopup > 0) {
                $topupStatus = 'PARTIAL';
            } elseif ($remainingTopup == 0) {
                $topupStatus = 'PAID';
            }

            // Create Top Up record
            $topupId = DB::table('t_topup')->insertGetId([
                'company_code' => $companyCode,
                'topup_number' => $topupNumber,
                'topup_date' => $request->topup_date,
                'entity_type' => $entityType,
                'topup_amount' => $topupAmount,
                'allocated_amount' => $totalAllocated,
                'remaining_balance' => $remainingTopup,
                'notes' => $request->notes,
                'status' => $topupStatus,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create allocation records and update revenue splits
            foreach ($allocations as $allocation) {
                // Create t_topup_sales_order record
                DB::table('t_topup_sales_order')->insert([
                    'company_code' => $companyCode,
                    'topup_id' => $topupId,
                    'sales_order_id' => $allocation['so_id'],
                    'allocated_amount' => $allocation['allocated_amount'],
                    'so_total_amount' => $allocation['so_total'],
                    'so_remaining_before' => $allocation['so_remaining_before'],
                    'so_remaining_after' => $allocation['so_remaining_after'],
                    'allocation_sequence' => $allocation['sequence'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update revenue split status if SO is fully paid
                if ($allocation['so_remaining_after'] <= 0) {
                    DB::table('t_sales_order_revenue_splits')
                        ->where('f_sales_order_id', $allocation['so_id'])
                        ->update([
                            $isPaidField => true,
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top Up berhasil disimpan dengan nomor: ' . $topupNumber,
                'topup_id' => $topupId,
                'topup_number' => $topupNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan Top Up: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show Top Up detail
     */
    public function detail($id)
    {

        $companyCode = session('user_company_code');

        // Get Top Up data
        $topup = DB::table('t_topup')
            ->join('users as cu', 't_topup.created_by', '=', 'cu.id')
            ->where('t_topup.id', $id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$topup) {
            abort(404, 'Top Up tidak ditemukan');
        }

        // Get allocation details
        $allocations = DB::table('t_topup_sales_order as tso')
            ->join('t_sales_orders as so', 'tso.sales_order_id', '=', 'so.id')
            ->select([
                'tso.*',
                'so.transaction_code as so_number',
                'so.transaction_date as so_date',
            ])
            ->where('tso.topup_id', $id)
            ->orderBy('tso.allocation_sequence', 'ASC')
            ->get();

        return view('pages.main_transaction.topup.detail', [
            'topup' => $topup,
            'allocations' => $allocations,
            'canEdit' => true, // Permission disabled - always allow
            'canDelete' => true, // Permission disabled - always allow
        ]);
    }

    /**
     * Show edit form (only for OPEN status)
     * DISABLED - Edit functionality not needed
     */
    // public function edit($id)
    // {
    //     abort(404, 'Edit functionality is disabled');
    // }

    /**
     * Update Top Up
     * DISABLED - Update functionality not needed
     */
    // public function update(Request $request)
    // {
    //     abort(404, 'Update functionality is disabled');
    // }

    /**
     * Delete Top Up (only for OPEN status)
     */
    public function destroy($id)
    {

        try {
            $companyCode = session('user_company_code');

            $topup = DB::table('t_topup')
                ->where('id', $id)
                ->where('company_code', $companyCode)
                ->first();

            if (!$topup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Top Up tidak ditemukan',
                ], 404);
            }

            // Only allow deleting OPEN status
            if ($topup->status !== 'OPEN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya Top Up dengan status OPEN yang dapat dihapus',
                ], 403);
            }

            DB::beginTransaction();

            // Delete allocations (cascade will handle this, but being explicit)
            DB::table('t_topup_sales_order')
                ->where('topup_id', $id)
                ->delete();

            // Delete Top Up
            DB::table('t_topup')
                ->where('id', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Top Up berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Top Up: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Generate transaction number
     */
    public function generateTransactionNumber()
    {
        try {
            // Use existing TransactionNumberService
            $transactionNumber = app(\App\Services\TransactionNumberService::class)
                ->generateNumber('topup');

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate transaction number: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Get outstanding Sales Orders for entity (FIFO order)
     */
    public function getOutstandingSalesOrders(Request $request)
    {
        $entityType = $request->input('entity_type');
        $companyCode = session('user_company_code');

        if (!$entityType) {
            return response()->json([
                'success' => false,
                'message' => 'Entity type is required',
            ], 400);
        }

        // Determine which field to check based on entity
        $isPaidField = $entityType === 'KINGDOM' ? 'is_paid_perusahaan' : 'is_paid_owner';
        $porsiField = $entityType === 'KINGDOM' ? 'porsi_perusahaan' : 'porsi_pabrik';

        // Get outstanding Sales Orders ordered by date (FIFO)
        $outstandingSO = DB::table('t_sales_orders as so')
            ->join('t_sales_order_revenue_splits as rs', 'so.id', '=', 'rs.f_sales_order_id')
            ->leftJoin('m_contacts as c', 'so.f_customer_id', '=', 'c.id')
            ->select([
                'so.id',
                'so.transaction_code',
                'so.transaction_date',
                'c.name as customer_name',
                DB::raw("SUM(rs.{$porsiField}) as total_amount"),
            ])
            ->where('so.company_code', $companyCode)
            ->where('rs.' . $isPaidField, false) // Not paid yet
            ->groupBy('so.id', 'so.transaction_code', 'so.transaction_date', 'c.name')
            ->orderBy('so.transaction_date', 'ASC') // FIFO: oldest first
            ->orderBy('so.id', 'ASC')
            ->get();

        // Calculate remaining amount for each SO (total - already allocated deposits)
        $formattedData = $outstandingSO->map(function ($item) {
            // Get total already allocated from deposits (t_topup_sales_order)
            $allocatedDeposit = DB::table('t_topup_sales_order as tso')
                ->where('tso.sales_order_id', $item->id)
                ->sum('tso.allocated_amount');

            $remainingAmount = $item->total_amount - $allocatedDeposit;

            return [
                'id' => $item->id,
                'so_number' => $item->transaction_code,
                'so_date' => $item->transaction_date,
                'customer_name' => $item->customer_name,
                'total_amount' => $item->total_amount,
                'allocated_deposit' => $allocatedDeposit,
                'remaining_amount' => $remainingAmount,
            ];
        });

        // Filter out SOs that are fully paid (remaining_amount <= 0)
        $filteredData = $formattedData->filter(function ($item) {
            return $item['remaining_amount'] > 0;
        })->values();

        return response()->json([
            'success' => true,
            'data' => $filteredData,
        ]);
    }

    /**
     * AJAX: Calculate FIFO allocation preview
     */
    public function calculateFifoAllocation(Request $request)
    {
        $entityType = $request->input('entity_type');
        $topupAmount = (float) $request->input('topup_amount');
        $companyCode = session('user_company_code');

        if (!$entityType || $topupAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
            ], 400);
        }

        // Determine which field to check based on entity
        $isPaidField = $entityType === 'KINGDOM' ? 'is_paid_perusahaan' : 'is_paid_owner';
        $porsiField = $entityType === 'KINGDOM' ? 'porsi_perusahaan' : 'porsi_pabrik';

        // Get outstanding Sales Orders ordered by date (FIFO)
        $outstandingSO = DB::table('t_sales_orders as so')
            ->join('t_sales_order_revenue_splits as rs', 'so.id', '=', 'rs.f_sales_order_id')
            ->select([
                'so.id',
                'so.transaction_code',
                'so.transaction_date',
                DB::raw("SUM(rs.{$porsiField}) as total_amount"),
            ])
            ->where('so.company_code', $companyCode)
            ->where('rs.' . $isPaidField, false)
            ->groupBy('so.id', 'so.transaction_code', 'so.transaction_date')
            ->orderBy('so.transaction_date', 'ASC')
            ->orderBy('so.id', 'ASC')
            ->get();

        // Calculate FIFO allocation with deposit consideration
        $remainingTopup = $topupAmount;
        $allocations = [];
        $sequence = 1;
        $totalAllocated = 0;

        foreach ($outstandingSO as $so) {
            if ($remainingTopup <= 0) {
                break;
            }

            // Get total already allocated from deposits (t_topup_sales_order)
            $allocatedDeposit = DB::table('t_topup_sales_order')
                ->where('sales_order_id', $so->id)
                ->sum('allocated_amount');

            $soRemaining = $so->total_amount - $allocatedDeposit;

            // Skip if already fully paid
            if ($soRemaining <= 0) {
                continue;
            }

            $allocationAmount = min($remainingTopup, $soRemaining);

            $allocations[] = [
                'sequence' => $sequence,
                'so_id' => $so->id,
                'so_number' => $so->transaction_code,
                'so_date' => $so->transaction_date,
                'so_total' => $so->total_amount,
                'so_remaining_before' => $soRemaining,
                'allocated_amount' => $allocationAmount,
                'so_remaining_after' => $soRemaining - $allocationAmount,
            ];

            $remainingTopup -= $allocationAmount;
            $totalAllocated += $allocationAmount;
            $sequence++;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'topup_amount' => $topupAmount,
                'total_allocated' => $totalAllocated,
                'remaining_balance' => $remainingTopup,
                'allocations' => $allocations,
            ],
        ]);
    }
}
