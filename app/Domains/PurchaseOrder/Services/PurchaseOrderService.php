<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderService
{
    private $table_name = 't_purchase_orders';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['reference_no'],
            'transaction_date' => $data['date'],
            'f_supplier_id' => $data['supplier_id'],
            'supplier_email' => $data['email'],
            'supplier_address' => $data['address'],
            'supplier_reference_no' => $data['supplier_reference'],
            'f_customer_id' => $data['customer_id'],
            'f_payment_term_id' => $data['payment_terms'],
            'payment_term_day_count' => $data['payment_term_day_count'] ?? null,
            'payment_term_due_date' => $data['payment_term_due_date'] ?? null,
            'f_warehouse_id' => $data['warehouse_id'],
            'description' => $data['description'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            // 'is_price_include_tax' => $data['include_tax'] ?? false,
            // 'subtotal' => $data['subtotal'] ?? 0,
            // 'total_tax' => $data['tax_amount'] ?? 0,
            // 'total_price_before_discount' => $data['total_before_discount'] ?? 0,
            // 'discount' => $data['discount_amount'] ?? 0,
            'grand_price' => $data['total'] ?? 0,
            'attachment' => $data['attachment'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update($id, array $data)
    {
        $update_data = [
            'transaction_date' => $data['date'],
            'f_supplier_id' => $data['supplier_id'],
            'supplier_email' => $data['email'],
            'supplier_address' => $data['address'],
            'supplier_reference_no' => $data['supplier_reference'],
            'f_customer_id' => $data['customer_id'],
            'f_payment_term_id' => $data['payment_terms'],
            'payment_term_day_count' => $data['payment_term_day_count'] ?? null,
            'payment_term_due_date' => $data['payment_term_due_date'] ?? null,
            'f_warehouse_id' => $data['warehouse_id'],
            'description' => $data['description'],
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            // 'is_price_include_tax' => $data['include_tax'] ?? false,
            // 'subtotal' => $data['subtotal'] ?? 0,
            // 'total_tax' => $data['tax_amount'] ?? 0,
            // 'total_price_before_discount' => $data['total_before_discount'] ?? 0,
            // 'discount' => $data['discount_amount'] ?? 0,
            'grand_price' => $data['total'] ?? 0,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];

        if (isset($data['attachment'])) {
            $update_data['attachment'] = $data['attachment'];
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($update_data);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as po')
            ->select(
                'po.*',
                'mc.name AS supplier_name',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id) as pi_count"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id) as pd_count"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status IN ('PAID', 'SETTLED')) as pi_settled_count"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status = 'PARTIAL_PAID') as pi_partial_count"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id AND pd.delivery_status = 'COMPLETED') as pd_delivered_count"),
                DB::raw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id AND pd.delivery_status = 'PARTIAL') as pd_in_transit_count"),
                DB::raw("CASE
                    WHEN (SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id) > 0
                         AND (SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status IN ('PAID', 'SETTLED')) > 0
                    THEN 5
                    WHEN (SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status IN ('PAID', 'SETTLED')) > 0
                    THEN 4
                    WHEN (SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id) > 0
                    THEN 3
                    WHEN (SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id) > 0
                    THEN 2
                    ELSE 1
                END as status_priority")
            )
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->leftJoin('users as u', 'po.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(po.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('po.company_code', session('user_company_code'));

        if ($date = request()->input('date_range_filter')) {
            // Check if date is a range (contains ' - ')
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('po.transaction_date', [$dateFrom, $dateTo]);
            }
        }

        if ($on_due_date_filter = request()->input('on_due_date_filter')) {
            $query->where('po.payment_term_due_date', $on_due_date_filter);
        }

        if ($suppllier_filter = request()->input('suppllier_filter')) {
            $query->where('po.f_supplier_id', $suppllier_filter);
        }

        if ($purchase_order_no_filter = request()->input('purchase_order_no_filter')) {
            $query->where('po.transaction_code', 'like', "%{$purchase_order_no_filter}%");
        }

        // PD Status filter
        if ($pdStatusFilter = request()->input('pd_status_filter')) {
            switch ($pdStatusFilter) {
                case 'Belum Terima':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id) = 0");
                    break;
                case 'Sebagian Diterima':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id AND pd.delivery_status = 'PARTIAL') > 0");
                    break;
                case 'Diterima':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_deliveries pd WHERE pd.f_purchase_order_id = po.id AND pd.delivery_status = 'COMPLETED') > 0");
                    break;
            }
        }

        // PI Status filter
        if ($piStatusFilter = request()->input('pi_status_filter')) {
            switch ($piStatusFilter) {
                case 'Belum Ditagih':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id) = 0");
                    break;
                case 'Ditagih':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status NOT IN ('PAID', 'PARTIAL_PAID')) > 0");
                    break;
                case 'Dibayar Sebagian':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status = 'PARTIAL_PAID') > 0");
                    break;
                case 'Lunas':
                    $query->havingRaw("(SELECT COUNT(*) FROM t_purchase_invoices pi WHERE pi.f_purchase_order_id = po.id AND pi.status = 'PAID') > 0");
                    break;
            }
        }

        $query->groupBy('po.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('status_priority', 'ASC')
              ->orderBy('po.transaction_date', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as po')
            ->select(
                'po.*',
                'mc.name AS supplier_name',
                'pt.code AS payment_term_code',
                'pt.name AS payment_term_name',
                'pt.type AS payment_term_type',
                'mw.code AS warehouse_code',
                'mw.name AS warehouse_name',
                'c.name AS customer_name',
                // 'c.address AS customer_address',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->leftJoin('m_contacts as c', 'c.id', '=', 'po.f_customer_id')
            ->leftJoin('users as u', 'po.created_by', '=', 'u.id')
            ->leftJoin('m_payment_terms as pt', 'pt.id', '=', 'po.f_payment_term_id')
            ->leftJoin('m_warehouses as mw', 'mw.id', '=', 'po.f_warehouse_id')
            ->leftJoin(DB::raw("JSON_TABLE(po.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('po.id', $id)
            ->where('po.company_code', session('user_company_code'))
            ->groupBy('po.id')
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
        }
        if ($data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }

        return $data;
    }

    public function findAvailableForDelivery()
    {
        return DB::table($this->table_name.' as po')
            ->select([
                'po.id as value',
                DB::raw("CONCAT(po.transaction_code, ' - ', mc.name) as label"),
                'mc.name as supplier',
                'mc.id as supplier_id',
                'po.supplier_email as email',
                'po.supplier_address as address',
                'po.f_warehouse_id as warehouse_id',
                'po.transaction_code as reference_no',
            ])
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->where('po.company_code', session('user_company_code'))
            ->whereNotIn('po.status', ['CANCELLED', 'DRAFT'])
            ->orderBy('po.created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => $item->label,
                    'supplier' => $item->supplier,
                    'supplier_id' => $item->supplier_id,
                    'email' => $item->email,
                    'address' => $item->address,
                    'warehouse_id' => $item->warehouse_id,
                    'reference_no' => $item->reference_no,
                ];
            })
            ->toArray();
    }
}
