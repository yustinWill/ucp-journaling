<?php

namespace App\Domains\InventoryAsset\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAssetService
{
    private $table_name = 'm_inventory_assets';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSummaryData($tab = 'pending_assets')
    {
        $query = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'));

        // Apply tab-specific filters
        switch($tab) {
            case 'pending_assets':
                // Aset Tunda - Assets with MAINTENANCE status (temporary approach)
                // TODO: Add sisa_tagihan field for proper installment tracking
                $query->where('status', 'MAINTENANCE');
                break;

            case 'active_assets':
                // Aset Aktif - Active operational assets
                $query->where('status', 'ACTIVE');
                break;

            case 'disposed':
                // Dijual/Dilepas - Disposed assets
                $query->where('status', 'DISPOSED');
                break;

            case 'depreciation':
                // Penyusutan - Fixed assets with depreciation enabled
                $query->where('type', 'FIXED_ASSET')
                      ->where('depreciation_method', '!=', 'NONE')
                      ->where('status', 'ACTIVE');
                break;
        }

        $totalAset = $query->count();
        $nilaiPerolehan = $query->sum('acquisition_value') ?: 0;
        $akumulasiPenyusutan = $query->sum('accumulated_depreciation') ?: 0;
        $nilaiBukuSaatIni = $query->sum('net_book_value') ?: 0;

        // Pelunasan 30 hari terakhir - for now, return 0
        // TODO: Implement payment tracking system
        $pelunasan30Hari = 0;

        return [
            'total_aset' => $totalAset,
            'nilai_perolehan' => $nilaiPerolehan,
            'akumulasi_penyusutan' => $akumulasiPenyusutan,
            'nilai_buku_saat_ini' => $nilaiBukuSaatIni,
            'pelunasan_30_hari' => $pelunasan30Hari,
        ];
    }

    public function findAllDatatable($tab = 'active_assets', $categoryFilter = null, $statusFilter = null, $search = null,$month = null, $year = null)
    {
        $query = DB::table($this->table_name.' as ia')
            ->select([
                'ia.*',
                'units.name as unit_name',
                'users.name as created_by_name'
            ])
            ->leftJoin('m_units as units', 'ia.f_unit_id', '=', 'units.id')
            ->leftJoin('users', 'ia.created_by', '=', 'users.id')
            ->where('ia.company_code', session('user_company_code'));

        // Apply category filter
        if ($categoryFilter) {
            $query->where('ia.category', $categoryFilter);
        }

        // Apply status filter
        if ($statusFilter) {
            $query->where('ia.status', $statusFilter);
        }
        if ($tab === 'depreciation') {
            $query->where('ia.type', 'FIXED_ASSET')
                ->where('ia.depreciation_method', '!=', 'NONE')
                ->where('ia.status', 'ACTIVE');

            // Filter by month and year if provided
            if ($month && $year) {
                $query->whereMonth('ia.acquisition_date', $month)
                    ->whereYear('ia.acquisition_date', $year);
            }
        }
        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('ia.name', 'like', '%' . $search . '%')
                ->orWhere('ia.code', 'like', '%' . $search . '%')
                ->orWhere('ia.category', 'like', '%' . $search . '%');
            });
        }

        $data = $query->orderBy('ia.created_at', 'desc')->get();

        // Add badge classes for status
        foreach ($data as $row) {
            $row->status_badge_class = match($row->status) {
                'ACTIVE' => 'bg-label-success',
                'INACTIVE' => 'bg-label-secondary',
                'DISPOSED' => 'bg-label-danger',
                'MAINTENANCE' => 'bg-label-warning',
                default => 'bg-label-secondary'
            };

            // Format tags if exists
            if ($row->f_tag_codes) {
                $tagCodes = json_decode($row->f_tag_codes, true);
                $tags = DB::table('m_tags')
                    ->select('m_tags.code', 'm_tags.name', DB::raw("CONCAT('bg-label-', m_tag_colors.code) as preview"))
                    ->join('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
                    ->whereIn('m_tags.id', $tagCodes)
                    ->get();
                $row->tags = $tags;
            } else {
                $row->tags = [];
            }
        }

        return response()->json(['data' => $data], 200);
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as ia')
            ->select([
                'ia.*',
                'units.name as unit_name',
                'warehouses.name as warehouse_name',
                'created_user.name as created_by_name',
                'updated_user.name as updated_by_name'
            ])
            ->leftJoin('m_units as units', 'ia.f_unit_id', '=', 'units.id')
            ->leftJoin('m_warehouses as warehouses', 'ia.f_warehouse_id', '=', 'warehouses.id')
            ->leftJoin('users as created_user', 'ia.created_by', '=', 'created_user.id')
            ->leftJoin('users as updated_user', 'ia.updated_by', '=', 'updated_user.id')
            ->where('ia.id', $id)
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes, true);
        }

        return $data;
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'category' => $data['category'],
            'location' => $data['location'] ?? null,
            'f_warehouse_id' => $data['warehouse_id'] ?? null,
            'acquisition_date' => $data['acquisition_date'] ?? null,
            'acquisition_method' => $data['acquisition_method'] ?? null,
            'acquisition_value' => $data['acquisition_value'] ?? 0,
            'vendor_name' => $data['vendor_name'] ?? null,
            'payment_terms' => $data['payment_terms'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'f_asset_coa_code' => $data['f_asset_coa_code'] ?? null,
            'f_purchase_coa_code' => $data['f_purchase_coa_code'] ?? null,
            'depreciation_method' => $data['depreciation_method'] ?? 'NONE',
            'depreciation_rate' => $data['depreciation_rate'] ?? null,
            'depreciation_period_months' => $data['depreciation_period_months'] ?? null,
            'useful_life_years' => $data['useful_life_years'] ?? null,
            'salvage_value' => $data['salvage_value'] ?? 0,
            'total_depreciation' => $data['total_depreciation'] ?? 0,
            'accumulated_depreciation' => $data['accumulated_depreciation'] ?? 0,
            'net_book_value' => $data['net_book_value'] ?? 0,
            'f_depreciation_expense_coa_code' => $data['f_depreciation_expense_coa_code'] ?? null,
            'f_accumulated_depreciation_coa_code' => $data['f_accumulated_depreciation_coa_code'] ?? null,
            'f_coa_depreciation_debit' => $data['f_coa_depreciation_debit'] ?? null,
            'f_coa_depreciation_credit' => $data['f_coa_depreciation_credit'] ?? null,
            'quantity' => $data['quantity'] ?? 0,
            'f_unit_id' => $data['unit_id'] ?? null,
            'unit_cost' => $data['unit_cost'] ?? 0,
            'reorder_point' => $data['reorder_point'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE',
            'f_tag_codes' => isset($data['tag_codes']) ? json_encode($data['tag_codes']) : null,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function update($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'],
            'location' => $data['location'] ?? null,
            'f_warehouse_id' => $data['warehouse_id'] ?? null,
            'acquisition_value' => $data['acquisition_value'] ?? 0,
            'vendor_name' => $data['vendor_name'] ?? null,
            'depreciation_method' => $data['depreciation_method'] ?? 'NONE',
            'depreciation_rate' => $data['depreciation_rate'] ?? null,
            'useful_life_years' => $data['useful_life_years'] ?? null,
            'salvage_value' => $data['salvage_value'] ?? 0,
            'quantity' => $data['quantity'] ?? 0,
            'f_unit_id' => $data['unit_id'] ?? null,
            'unit_cost' => $data['unit_cost'] ?? 0,
            'reorder_point' => $data['reorder_point'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE',
            'f_tag_codes' => isset($data['tag_codes']) ? json_encode($data['tag_codes']) : null,
            'notes' => $data['notes'] ?? null,
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    public function updateDispose($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'f_sales_coa_code' => $data['f_sales_coa_code'],
            'f_difference_coa_code' => $data['f_difference_coa_code'],
            'disposal_code' => $data['disposal_code'],
            'disposal_date' => $data['disposal_date'],
            'disposal_value' => $data['disposal_value'],
            'status' => $data['status'] ?? 'DISPOSED',
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    public function delete($id)
    {
        return DB::table($this->table_name)->where('id', $id)->delete();
    }

    public function createSchedule($id, array $data)
    {
        $depreciationPeriodMonths = $data['depreciation_period_months'] ?? 0;

        // Ensure depreciation_period_months is valid
        if ($depreciationPeriodMonths <= 0) {
            throw new \InvalidArgumentException('Depreciation period must be greater than 0.');
        }

        $startDate = isset($data['acquisition_date']) ? new \DateTime($data['acquisition_date']) : new \DateTime();
        $schedules = [];
        $valuePerMonth = ($data['acquisition_value'] ?? 0) / $depreciationPeriodMonths;

        for ($i = 0; $i < $depreciationPeriodMonths; $i++) {
            // Clone the start date and add $i months
            $scheduleDate = (clone $startDate)->modify("+{$i} months");

            // Get the last day of the month
            $endOfMonth = $scheduleDate->format('Y-m-t');

            $schedules[] = [
                'asset_id' => $id,
                'asset_depreciation_date' => $endOfMonth,
                'asset_depreciation_value' => $valuePerMonth,
            ];
        }

        // Insert all schedules into the database
        DB::table("m_inventory_assets_depreciation_schedule")->insert($schedules);

        return count($schedules); // Return the number of schedules created
    }

    public function findAllDepreciation($month = null, $year = null)
    {
        $query = DB::table($this->table_name . ' as ia')
            ->select([
                'ia.*',
                'ias.depreciation_id',
                'ias.depreciation_value',
                'ias.depreciation_month_year',
                'units.name as unit_name',
                'users.name as created_by_name',
                'ias.total_depreciation_value',
            ])
            ->JoinSub(
                DB::table('m_inventory_assets_depreciation_schedule as ias')
                    ->select([
                        DB::raw('MAX(ias.id) as depreciation_id'), // Aggregate non-grouped column
                        'ias.asset_id',
                        DB::raw('MAX(ias.asset_depreciation_value) as depreciation_value'), // Aggregate non-grouped column
                        DB::raw('DATE_FORMAT(MAX(ias.asset_depreciation_date), "%M %Y") as depreciation_month_year'), // Aggregate date
                        DB::raw('MAX(ias.asset_is_depreciation) as asset_is_depreciation'), // Aggregate asset_is_depreciation
                        DB::raw('SUM(CASE WHEN ias.asset_is_depreciation IS NOT NULL THEN ias.asset_depreciation_value ELSE 0 END) AS total_depreciation_value'),
                    ])
                    ->whereNotNull('ias.asset_is_depreciation') // Add condition for asset_is_depreciation
                    ->whereMonth('ias.asset_depreciation_date', $month)
                    ->whereYear('ias.asset_depreciation_date', $year)
                    ->groupBy('ias.asset_id') // Group by asset_id
                    ->orderBy('ias.asset_id', 'asc'), // Order by asset_id
                'ias',
                'ias.asset_id',
                '=',
                'ia.id'
            )
            ->leftJoin('m_units as units', 'ia.f_unit_id', '=', 'units.id')
            ->leftJoin('users', 'ia.created_by', '=', 'users.id')
            ->where('ia.company_code', session('user_company_code'))
            ->where('ia.type', 'FIXED_ASSET')
            ->where('ia.depreciation_method', '!=', 'NONE')
            ->where('ia.status', 'ACTIVE');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        $recordsFiltered = $filteredQuery->get()->count();
        $data = $query->orderBy('ia.created_at', 'desc')->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
