<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\InventoryAsset\Actions\CreateInventoryAssetAction;
use App\Domains\InventoryAsset\Actions\UpdateInventoryAssetAction;
use App\Domains\InventoryAsset\Services\InventoryAssetAuditLogService;
use App\Domains\InventoryAsset\Services\InventoryAssetService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\AuditLogTypes;
use App\Enums\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryAssetController extends Controller
{
    public function index()
    {
        // Get summary data for default tab (pending_assets)
        $summaryData = app(InventoryAssetService::class)->getSummaryData('pending_assets');

        return view('pages.master_data.inventory.index', [
            'summaryData' => $summaryData
        ]);
    }

    public function index_data_table(Request $request)
    {
        $categoryFilter = $request->get('category_filter');
        $statusFilter = $request->get('status_filter');
        $search = $request->get('search');
        $tab = $request->get('tab', 'active_assets');

        // Get summary data for the current tab
        $summaryData = app(InventoryAssetService::class)->getSummaryData($tab);

        $data = app(InventoryAssetService::class)->findAllDatatable($tab, $categoryFilter, $statusFilter, $search);

        return response()->json([
            'data' => $data->original['data'],
            'summary' => $summaryData
        ]);
    }

    public function add()
    {
        // Get tags with color preview
        $tags = DB::table('m_tags as t')
            ->select([
                't.id',
                't.name',
                't.code',
                DB::raw("CONCAT('bg-label-', tc.code) as preview")
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.status', 'ACTIVE')
            ->orderBy('t.code', 'ASC')
            ->get();

        // Get COA accounts for asset and purchase
        $coaAccounts = DB::table('m_coas')
            ->where('company_code', session('user_company_code'))
            ->where('status', 'ACTIVE')
            ->select('code', 'name')
            ->orderBy('code')
            ->get();

        // Payment terms options
        $paymentTerms = [
            ['value' => 'CASH', 'label' => 'CASH - Tunai'],
            ['value' => 'NET_7', 'label' => 'NET_7 - Jatuh Tempo 7 Hari'],
            ['value' => 'NET_15', 'label' => 'NET_15 - Jatuh Tempo 15 Hari'],
            ['value' => 'NET_30', 'label' => 'NET_30 - Jatuh Tempo 30 Hari'],
            ['value' => 'NET_45', 'label' => 'NET_45 - Jatuh Tempo 45 Hari'],
            ['value' => 'NET_60', 'label' => 'NET_60 - Jatuh Tempo 60 Hari']
        ];

        return view('pages.master_data.inventory.add', [
            'availableTags' => $tags,
            'coaAccounts' => $coaAccounts,
            'paymentTerms' => $paymentTerms
        ]);
    }

    public function generateTransactionNumber()
    {
        try {
            $transactionNumber = app(TransactionNumberService::class)->generateNumber('asset_purchase');

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'transaction_number' => 'required|string|unique:m_inventory_assets,code',
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'required|string',
            'transaction_date' => 'required|date',
            'payment_terms' => 'required|string',
            'price' => 'required|string',
        ]);

        // Prepare data for creation
        $data = [
            'code' => $request->transaction_number,
            'name' => $request->asset_name,
            'description' => $request->description,
            'type' => 'FIXED_ASSET',
            'category' => 'ASSET', // Default category
            'acquisition_date' => $request->transaction_date,
            'acquisition_method' => 'PURCHASE',
            'acquisition_value' => floatval(str_replace(',', '', $request->price)),
            'payment_terms' => $request->payment_terms,
            'due_date' => $request->due_date,
            'f_asset_coa_code' => $request->fixed_asset_account,
            'f_purchase_coa_code' => $request->purchase_account,
            'status' => 'ACTIVE',
            'tag_codes' => $request->input('tag_codes', []),
            'notes' => $request->memo,
        ];

        // Handle depreciation fields if checkbox is checked
        if ($request->has('is_depreciated')) {
            $data['depreciation_method'] = 'STRAIGHT_LINE';
            $data['depreciation_rate'] = $request->depreciation_rate_per_year;
            $data['depreciation_period_months'] = $request->depreciation_period_months;
            $data['useful_life_years'] = round($request->depreciation_period_months / 12, 2);
            $data['f_depreciation_expense_coa_code'] = $request->depreciation_account;
            $data['f_accumulated_depreciation_coa_code'] = $request->accumulated_depreciation_account;

            // Calculate depreciation values
            $acquisitionValue = floatval(str_replace(',', '', $request->price));
            $rate = floatval($request->depreciation_rate_per_year);
            $totalDepreciation = $acquisitionValue * ($rate / 100);

            $data['total_depreciation'] = $totalDepreciation;
            $data['accumulated_depreciation'] = 0; // Initial
            $data['net_book_value'] = $acquisitionValue - 0; // Initial
        } else {
            $data['depreciation_method'] = 'NONE';
            $data['net_book_value'] = $data['acquisition_value'];
        }

        app(CreateInventoryAssetAction::class)->execute($data);

        return response()->json([
            'message' => 'Asset berhasil dibuat',
            'success' => true
        ]);
    }

    public function detail($id)
    {
        $asset = app(InventoryAssetService::class)->findOne($id);
        $auditLogs = app(InventoryAssetAuditLogService::class)->findAllBasedOnId($id);

        // Get tags if exist
        $tags = [];
        if ($asset && $asset->f_tag_codes && is_array($asset->f_tag_codes)) {
            $tags = DB::table('m_tags as t')
                ->select([
                    't.code',
                    't.name',
                    DB::raw("CONCAT('bg-label-', tc.code) as preview")
                ])
                ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
                ->whereIn('t.id', $asset->f_tag_codes)
                ->get();
        }

        // Get COA account names
        $assetAccount = null;
        $purchaseAccount = null;
        $depreciationAccount = null;
        $accumulatedDepreciationAccount = null;

        if ($asset) {
            if ($asset->f_asset_coa_code) {
                $assetAccount = DB::table('m_coas')
                    ->where('code', $asset->f_asset_coa_code)
                    ->first();
            }
            if ($asset->f_purchase_coa_code) {
                $purchaseAccount = DB::table('m_coas')
                    ->where('code', $asset->f_purchase_coa_code)
                    ->first();
            }
            if ($asset->f_depreciation_expense_coa_code) {
                $depreciationAccount = DB::table('m_coas')
                    ->where('code', $asset->f_depreciation_expense_coa_code)
                    ->first();
            }
            if ($asset->f_accumulated_depreciation_coa_code) {
                $accumulatedDepreciationAccount = DB::table('m_coas')
                    ->where('code', $asset->f_accumulated_depreciation_coa_code)
                    ->first();
            }
        }

        return view('pages.master_data.inventory.detail', [
            'asset' => $asset,
            'tags' => $tags,
            'assetAccount' => $assetAccount,
            'purchaseAccount' => $purchaseAccount,
            'depreciationAccount' => $depreciationAccount,
            'accumulatedDepreciationAccount' => $accumulatedDepreciationAccount,
            'auditLogs' => $auditLogs,
            'auditLogType' => AuditLogTypes::MASTER_DATA_INVENTORY_ASSET->code()
        ]);
    }

    public function edit($id)
    {
        $asset = app(InventoryAssetService::class)->findOne($id);
        $tags = app(TagService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $units = DB::table('m_units')->where('status', 'ACTIVE')->get();

        $statusOptions = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Dalam Perawatan'],
            ['value' => 'DISPOSED', 'label' => 'DISPOSED - Sudah Dilepas']
        ];

        $typeOptions = [
            ['value' => 'FIXED_ASSET', 'label' => 'FIXED_ASSET - Aset Tetap'],
            ['value' => 'INVENTORY', 'label' => 'INVENTORY - Inventaris']
        ];

        $depreciationMethods = [
            ['value' => 'NONE', 'label' => 'NONE - Tidak Ada'],
            ['value' => 'STRAIGHT_LINE', 'label' => 'STRAIGHT_LINE - Garis Lurus'],
            ['value' => 'REDUCING_BALANCE', 'label' => 'REDUCING_BALANCE - Saldo Menurun']
        ];

        return view('pages.master_data.inventory.edit', [
            'asset' => $asset,
            'availableTags' => $tags,
            'warehouses' => $warehouses,
            'units' => $units,
            'statusOptions' => $statusOptions,
            'typeOptions' => $typeOptions,
            'depreciationMethods' => $depreciationMethods
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:m_inventory_assets,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'status' => 'required|in:ACTIVE,INACTIVE,MAINTENANCE,DISPOSED'
        ]);

        $data = $request->all();
        $data['tag_codes'] = $request->input('tag_codes', []);

        app(UpdateInventoryAssetAction::class)->execute($request->id, $data);

        return response()->json([
            'message' => 'Asset/Inventory updated successfully',
            'success' => true
        ]);
    }

    public function dispose()
    {
        // Get assets
        $assets = DB::table('m_inventory_assets')
            ->select([
                'id',
                'code',
                'name',
            ])
            ->where('status', 'ACTIVE')
            ->orderBy('created_at', 'DESC')
            ->get();

        // Get COA accounts for asset and purchase
        $coaAccounts = DB::table('m_coas')
            ->where('company_code', session('user_company_code'))
            ->where('status', 'ACTIVE')
            ->select('code', 'name')
            ->orderBy('code')
            ->get();

        return view('pages.master_data.inventory.dispose', [
            'assets' => $assets,
            'coaAccounts' => $coaAccounts
        ]);
    }

    public function get_asset_data($id)
    {
        $asset = app(InventoryAssetService::class)->findOne($id);
        // $auditLogs = app(InventoryAssetAuditLogService::class)->findAllBasedOnId($id);

        // // Get tags if exist
        // $tags = [];
        // if ($asset && $asset->f_tag_codes && is_array($asset->f_tag_codes)) {
        //     $tags = DB::table('m_tags as t')
        //         ->select([
        //             't.code',
        //             't.name',
        //             DB::raw("CONCAT('bg-label-', tc.code) as preview")
        //         ])
        //         ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
        //         ->whereIn('t.id', $asset->f_tag_codes)
        //         ->get();
        // }

        // // Get COA account names
        // $assetAccount = null;
        // $purchaseAccount = null;
        // $depreciationAccount = null;
        // $accumulatedDepreciationAccount = null;

        // if ($asset) {
        //     if ($asset->f_asset_coa_code) {
        //         $assetAccount = DB::table('m_coas')
        //             ->where('code', $asset->f_asset_coa_code)
        //             ->first();
        //     }
        //     if ($asset->f_purchase_coa_code) {
        //         $purchaseAccount = DB::table('m_coas')
        //             ->where('code', $asset->f_purchase_coa_code)
        //             ->first();
        //     }
        //     if ($asset->f_depreciation_expense_coa_code) {
        //         $depreciationAccount = DB::table('m_coas')
        //             ->where('code', $asset->f_depreciation_expense_coa_code)
        //             ->first();
        //     }
        //     if ($asset->f_accumulated_depreciation_coa_code) {
        //         $accumulatedDepreciationAccount = DB::table('m_coas')
        //             ->where('code', $asset->f_accumulated_depreciation_coa_code)
        //             ->first();
        //     }
        // }
        return response()->json([
            'success' => true,
            'data' => $asset,
        ]);
    }
}
