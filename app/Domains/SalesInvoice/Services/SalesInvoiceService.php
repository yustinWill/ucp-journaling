<?php

namespace App\Domains\SalesInvoice\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalesInvoiceService
{
    private $table_name = 't_sales_invoices';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }

    public function create(array $data)
    {
        // Get sales order data for due_date fallback if not provided
        $dueDate = $data['due_date'] ?? null;
        $paymentTerms = $data['payment_terms'] ?? null;

        if (empty($dueDate) && !empty($data['sales_order_id'])) {
            $salesOrder = DB::table('t_sales_orders')
                ->where('id', $data['sales_order_id'])
                ->first(['payment_term_due_date', 'f_payment_term_id']);

            if ($salesOrder) {
                $dueDate = $salesOrder->payment_term_due_date;

                // If still no due date, get from payment term
                if (empty($dueDate) && $salesOrder->f_payment_term_id) {
                    $paymentTerm = DB::table('m_payment_terms')
                        ->where('id', $salesOrder->f_payment_term_id)
                        ->first(['name', 'days']);

                    if ($paymentTerm) {
                        $paymentTerms = $paymentTerm->name;
                        $dayCount = $paymentTerm->days ?? 30;
                        $dueDate = now()->addDays($dayCount)->format('Y-m-d');
                    }
                }
            }
        }

        // Final fallback: 30 days from invoice date
        if (empty($dueDate)) {
            $dueDate = now()->addDays(30)->format('Y-m-d');
        }

        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['invoice_number'],
            'f_sales_order_id' => $data['sales_order_id'],
            'invoice_date' => $data['invoice_date'],
            'transaction_date' => $data['transaction_date'],
            'due_date' => $dueDate,
            'payment_terms' => $paymentTerms,
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'total_tax' => $data['total_tax'] ?? 0,
            'total_discount' => $data['total_discount'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'status' => $data['status'] ?? 'UNPAID',
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update($id, array $data)
    {
        $update_data = [
            'invoice_date' => $data['invoice_date'],
            'transaction_date' => $data['transaction_date'],
            'due_date' => $data['due_date'],
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
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
        $query = DB::table($this->table_name.' as si')
            ->select(
                'si.id',
                'si.invoice_date',
                'si.transaction_code',
                'si.due_date',
                'si.grand_total',
                'si.status',
                'si.created_by',
                'si.f_sales_order_id',
                'so.transaction_code as so_reference',
                'c.name AS customer_name',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('t_sales_orders as so', 'so.id', '=', 'si.f_sales_order_id')
            ->leftJoin('m_contacts as c', 'c.id', '=', 'so.f_customer_id')
            ->leftJoin('users as u', 'si.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(si.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('si.company_code', session('user_company_code'));

        // Apply filters
        if ($date = request()->input('date_range_filter')) {
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('si.invoice_date', [$dateFrom, $dateTo]);
            }
        }

        if ($dueDate = request()->input('due_date_filter')) {
            $query->where('si.due_date', $dueDate);
        }

        if ($customer = request()->input('customer_filter')) {
            $query->where('so.f_customer_id', $customer);
        }

        if ($status = request()->input('status_filter')) {
            $query->where('si.status', $status);
        }

        if ($invoiceSearch = request()->input('invoice_search_filter')) {
            $query->where(function ($q) use ($invoiceSearch) {
                $q->where('si.transaction_code', 'like', "%{$invoiceSearch}%")
                    ->orWhere('so.transaction_code', 'like', "%{$invoiceSearch}%");
            });
        }

        $query->groupBy('si.id');
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        // Order by status priority (OPEN, PARTIAL first, then PAID/SETTLED), then by invoice_date DESC
        $query->orderByRaw("CASE WHEN si.status IN ('PAID', 'SETTLED') THEN 1 ELSE 0 END ASC")
              ->orderBy('si.invoice_date', 'DESC');
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
        $data = DB::table($this->table_name.' as si')
            ->select(
                'si.*',
                'so.f_warehouse_id AS warehouse_id',
                'w.name AS warehouse_name',

                'so.transaction_code as so_reference',
                'so.id as so_id',
                'so.f_customer_id as customer_id',
                'c.name AS customer_name',
                'so.customer_email as customer_email',
                'so.customer_address as customer_address',
                'so.transaction_date AS so_transaction_date',
                'so.grand_price AS so_grand_price',
                'pt.code AS payment_term_code',
                'pt.name AS payment_term_name',
                'pt.type AS payment_term_type',
                'w.code AS warehouse_code',
                'w.name AS warehouse_name',
                'u_created.name as created_by_name',
                'u_updated.name as updated_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('t_sales_orders as so', 'so.id', '=', 'si.f_sales_order_id')
            ->leftJoin('m_contacts as c', 'c.id', '=', 'so.f_customer_id')
            ->leftJoin('users as u_created', 'si.created_by', '=', 'u_created.id')
            ->leftJoin('users as u_updated', 'si.updated_by', '=', 'u_updated.id')
            ->leftJoin('m_payment_terms as pt', 'pt.id', '=', 'so.f_payment_term_id')
            ->leftJoin('m_warehouses as w', 'w.id', '=', 'so.f_warehouse_id')
            ->leftJoin(DB::raw("JSON_TABLE(si.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('si.id', $id)
            ->where('si.company_code', session('user_company_code'))
            ->groupBy('si.id')
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes);
        }
        if ($data && $data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }

        return $data;
    }

    public function findAvailableForInvoicing()
    {
        return DB::table('t_sales_orders as so')
            ->select([
                'so.id as value',
                DB::raw("CONCAT(so.transaction_code, ' - ', c.name) as label"),
                'c.name as customer',
                'c.id as customer_id',
                'so.customer_email as email',
                'so.customer_address as address',
                'so.f_warehouse_id as warehouse_id',
                'so.transaction_code as reference_no',
                'so.grand_price as total',
                'pt.name as payment_terms',
                'pt.type as payment_term_type',
                // 'pt.day_count as payment_term_days',
            ])
            ->leftJoin('m_contacts as c', 'c.id', '=', 'so.f_customer_id')
            ->leftJoin('m_payment_terms as pt', 'pt.id', '=', 'so.f_payment_term_id')
            ->where('so.company_code', session('user_company_code'))
            ->whereNotIn('so.status', ['CANCELLED', 'DRAFT'])
            ->orderBy('so.created_at', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->value,
                    'label' => $item->label,
                    'customer' => $item->customer,
                    'customer_id' => $item->customer_id,
                    'email' => $item->email,
                    'address' => $item->address,
                    'warehouse_id' => $item->warehouse_id,
                    'reference_no' => $item->reference_no,
                    'total' => $item->total,
                    'payment_terms' => $item->payment_terms,
                    'payment_term_type' => $item->payment_term_type,
                    // 'payment_term_days' => $item->payment_term_days,
                ];
            })
            ->toArray();
    }

    public function createInvoiceProducts(int $invoiceId, array $products)
    {
        $insertData = [];
        foreach ($products as $product) {
            $insertData[] = [
                'f_sales_invoice_id' => $invoiceId,
                'f_sales_order_product_id' => $product['sales_order_product_id'],
                'f_product_id' => $product['product_id'],
                'invoiced_qty_before' => $product['invoiced_qty_before'] ?? 0,
                'remaining_qty_before' => $product['remaining_qty_before'] ?? 0,
                'qty' => $product['invoice_qty'],
                'invoiced_qty_after' => ($product['invoiced_qty_before'] ?? 0) + $product['invoice_qty'],
                'remaining_qty_after' => ($product['remaining_qty_before'] ?? 0) - $product['invoice_qty'],
                'unit_price' => $product['unit_price'],
                'line_total' => $product['invoice_qty'] * $product['unit_price'],
                'note' => $product['note'] ?? null,
                'status' => $product['status'] ?? 'INVOICED',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ];
        }

        if (! empty($insertData)) {
            DB::table('t_sales_invoice_products')->insert($insertData);
        }
    }

    public function findAllProducts($id)
    {
        return DB::table('t_sales_invoice_products as sip')
            ->select([
                'sip.*',
                'sip.note AS product_invoice_note',
                'sop.quantity AS ordered_qty',
                'sop.unit_price AS price_per_qty',
                'mp.name AS product_name',
                'mp.code AS product_code',
                'mu.code AS unit_code',
                'mu.name AS unit_name',
            ])
            ->leftJoin('m_products as mp', 'sip.f_product_id', '=', 'mp.id')
            ->leftJoin('t_sales_order_products as sop', 'sip.f_sales_order_product_id', '=', 'sop.id')
            ->leftJoin('m_units as mu', 'mu.id', '=', 'mp.f_unit_id')
            ->where('sip.f_sales_invoice_id', $id)
            ->orderBy('sip.id', 'ASC')
            ->get();
    }

    public function findAllHeaderBasedOnSoId($soId)
    {
        return DB::table($this->table_name.' as si')
            ->select([
                'si.*',
                'u.name as created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin(DB::raw("JSON_TABLE(si.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->leftJoin('users as u', 'si.created_by', '=', 'u.id')
            ->where('si.f_sales_order_id', $soId)
            ->groupBy('si.id')
            ->orderBy('si.invoice_date', 'ASC')
            ->get();
    }

    public function updateStatusIfFullyPaid($invoiceId)
    {
        // Get invoice grand total
        $invoice = DB::table($this->table_name)
            ->where('id', $invoiceId)
            ->where('company_code', session('user_company_code'))
            ->first(['grand_total', 'status']);

        if (!$invoice) {
            return;
        }

        // Get total payments made
        $totalPayments = DB::table('t_sales_invoice_payment')
            ->where('sales_invoice_id', $invoiceId)
            ->sum(DB::raw('payment_value - COALESCE(deduction_value, 0)'));

        // Check if fully paid
        if ($totalPayments >= $invoice->grand_total && $invoice->status !== 'PAID') {
            DB::table($this->table_name)
                ->where('id', $invoiceId)
                ->update([
                    'status' => 'PAID',
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);
        }
    }

}
