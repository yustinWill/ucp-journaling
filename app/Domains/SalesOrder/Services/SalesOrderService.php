<?php

namespace App\Domains\SalesOrder\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    private $table_name = 't_sales_orders';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }

    public function create(array $data): int
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['reference_no'],
            'transaction_date' => $data['date'],
            'f_customer_id' => $data['customer_id'],
            'customer_email' => $data['email'] ?? null,
            'customer_address' => $data['address'] ?? null,
            'customer_reference_no' => $data['customer_reference'] ?? null,
            'f_payment_term_id' => $data['payment_terms'] ?? null,
            'payment_term_day_count' => $data['payment_term_day_count'] ?? null,
            'payment_term_due_date' => $data['payment_term_due_date'] ?? null,
            'f_warehouse_id' => $data['warehouse_id'],
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            // 'is_price_include_tax' => $data['include_tax'] ?? false,
            // 'subtotal' => $data['subtotal'] ?? 0,
            // 'total_tax' => $data['tax_amount'] ?? 0,
            // 'total_price_before_discount' => $data['total_before_discount'] ?? 0,
            // 'discount' => $data['discount_amount'] ?? 0,
            'grand_price' => $data['total'] ?? 0,
            'attachment' => $data['attachment'] ?? null,
            'status' => 'PENDING',
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->first();
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'transaction_date' => $data['date'],
                'f_customer_id' => $data['customer_id'],
                'customer_email' => $data['email'] ?? null,
                'customer_address' => $data['address'] ?? null,
                'customer_reference_no' => $data['customer_reference'] ?? null,
                'f_payment_term_id' => $data['payment_terms'] ?? null,
                'payment_term_day_count' => $data['payment_term_day_count'] ?? null,
                'payment_term_due_date' => $data['payment_term_due_date'] ?? null,
                'f_warehouse_id' => $data['warehouse_id'],
                'description' => $data['description'] ?? null,
                'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
                'internal_memo' => $data['memo'] ?? null,
                // 'is_price_include_tax' => $data['include_tax'] ?? false,
                // 'subtotal' => $data['subtotal'] ?? 0,
                // 'total_tax' => $data['tax_amount'] ?? 0,
                // 'total_price_before_discount' => $data['total_before_discount'] ?? 0,
                // 'discount' => $data['discount_amount'] ?? 0,
                'grand_price' => $data['grand_price'] ?? 0,
                'attachment' => $data['attachment'] ?? null,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as so')
            ->select(
                'so.*',
                'mc.name AS customer_name',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'so.f_customer_id')
            ->leftJoin('users as u', 'so.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(so.f_tag_codes, '\$[*]' COLUMNS(id VARCHAR(255) PATH '\$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('so.company_code', session('user_company_code'));

        // Apply filters from request
        if ($date = request()->input('date_range_filter')) {
            // Check if date is a range (contains ' - ')
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('so.transaction_date', [$dateFrom, $dateTo]);
            } else {
                // Single date filter
                $query->where('so.transaction_date', $date);
            }
        }

        if ($onDueDateFilter = request()->input('on_due_date_filter')) {
            $query->where('so.payment_term_due_date', $onDueDateFilter);
        }

        if ($customerFilter = request()->input('customer_filter')) {
            $query->where('so.f_customer_id', $customerFilter);
        }

        if ($salesOrderNoFilter = request()->input('sales_order_no_filter')) {
            $query->where('so.transaction_code', 'like', "%{$salesOrderNoFilter}%");
        }

        $query->groupBy('so.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('so.id', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findWithDetails(int $id)
    {
        $salesOrder = DB::table($this->table_name . ' as so')
            ->leftJoin('m_contacts as c', 'so.f_customer_id', '=', 'c.id')
            ->leftJoin('m_warehouses as w', 'so.f_warehouse_id', '=', 'w.id')
            ->leftJoin('m_payment_terms as pt', 'so.f_payment_term_id', '=', 'pt.id')
            ->leftJoin('users as u', 'c.f_sales_pic_id', '=', 'u.id')
            ->leftJoin('m_contact_addresses as ca', function ($join) {
                $join->on('ca.f_contact_id', '=', 'c.id')
                    ->where('ca.type', '=', 'BILLING');
            })
            ->where('so.id', $id)
            ->where('so.company_code', session('user_company_code'))
            ->select([
                'so.*',
                'c.f_sales_pic_id AS customer_sales_person_id',
                'c.name as customer',
                'c.email as customer_default_email',
                'ca.address as customer_default_address',
                'u.name as sales_person_name',
                'w.name as warehouse',
                'pt.name as payment_terms',
                'pt.type as payment_terms_type',
            ])
            ->first();

        if (! $salesOrder) {
            return null;
        }

        // Convert to array for consistency
        $salesOrder = (array) $salesOrder;

        // Process tags
        if (! empty($salesOrder['f_tag_codes'])) {
            $tagIds = json_decode($salesOrder['f_tag_codes'], true);
            if (is_array($tagIds) && ! empty($tagIds)) {
                $tags = DB::table('m_tags')
                    ->whereIn('id', $tagIds)
                    ->pluck('name')
                    ->toArray();
                $salesOrder['tags'] = $tags;
                $salesOrder['selected_tags'] = $tagIds;
            } else {
                $salesOrder['tags'] = [];
                $salesOrder['selected_tags'] = [];
            }
        } else {
            $salesOrder['tags'] = [];
            $salesOrder['selected_tags'] = [];
        }

        // Get product lines
        $productLines = $this->findProductLines($id);
        $salesOrder['product_lines'] = $productLines;

        return $salesOrder;
    }

    public function findAllPending()
    {
        $salesOrder = DB::table($this->table_name . ' as so')
            ->select([
                'so.*',
                'c.name as customer',
            ])
            ->leftJoin('m_contacts as c', 'so.f_customer_id', '=', 'c.id')
            ->where('so.status', 'PENDING')
            ->where('so.company_code', session('user_company_code'))
            ->get();

        return $salesOrder;
    }

    public function findProductLines(int $salesOrderId, bool $returnAsJson = false)
    {
        $productLines = DB::table('t_sales_order_products as sop')
            ->leftJoin('m_products as p', 'sop.f_product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->leftJoin('t_sales_invoice_products as sip', 'sip.f_sales_order_product_id', '=', 'sop.id')
            // ->leftJoin('m_taxes as t', 'sop.f_tax_id', '=', 't.id')
            ->where('sop.f_sales_order_id', $salesOrderId)
            ->select([
                'sop.*',
                'p.name as product_name',
                'p.code as product_code',
                'u.name as unit_name',
                // 't.name as tax_name',
                // 't.rate as tax_rate',
                DB::raw('COALESCE(SUM(sip.qty), 0) AS invoiced_qty'),
                DB::raw('(sop.quantity - COALESCE(SUM(sip.qty), 0)) AS remaining_qty_for_invoice')
            ])
            ->groupBy('sop.id', 'p.code', 'p.name', 'u.code', 'u.name')
            ->orderBy('sop.id', 'asc')
            ->get();

        if ($returnAsJson === false) {
            return $productLines->map(function ($item) {
                return (array) $item;
            })->toArray();
        }
    
        return $productLines;
        // Convert stdClass objects to arrays for consistency

    }

    public function delete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->delete();
    }

    public function updateDeliveredQuantities($so_id, $product_order_product_id, $qty)
    {
        return DB::table('t_sales_order_products')
            ->where('f_sales_order_id', $so_id)
            ->where('id', $product_order_product_id)
            ->update([
                'delivered_qty' => DB::raw('delivered_qty + '.$qty),
                'remaining_qty' => DB::raw('remaining_qty - '.$qty),
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

}
