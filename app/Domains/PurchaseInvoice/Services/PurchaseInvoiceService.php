<?php

namespace App\Domains\PurchaseInvoice\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseInvoiceService
{
    private $table_name = 't_purchase_invoices';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['invoice_number'],
            'f_purchase_order_id' => $data['purchase_order_id'],
            'invoice_date' => $data['invoice_date'],
            'transaction_date' => $data['transaction_date'],
            'due_date' => $data['due_date'],
            'payment_terms' => $data['payment_terms'],
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['internal_memo'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'total_tax' => $data['tax_amount'] ?? 0,
            'total_discount' => $data['discount_amount'] ?? 0,
            'grand_total' => $data['total'] ?? 0,
            'status' => $data['status'] ?? 'UNPAID',
            'created_at' => now(),
            'created_by' => $data['created_by'] ?? Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $update_data = [
            'invoice_date' => $data['invoice_date'],
            'transaction_date' => $data['transaction_date'],
            'due_date' => $data['due_date'],
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['internal_memo'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'total_tax' => $data['tax_amount'] ?? 0,
            'total_discount' => $data['discount_amount'] ?? 0,
            'grand_total' => $data['total'] ?? 0,
            'status' => $data['status'] ?? 'UNPAID',
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
        $query = DB::table($this->table_name.' as pi')
            ->select(
                'pi.*',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                'mc.name AS supplier_name',
                'mc.id AS supplier_id',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('t_purchase_orders as po', 'pi.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as mc', 'po.f_supplier_id', '=', 'mc.id')
            ->leftJoin('users as u', 'pi.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(pi.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('pi.company_code', session('user_company_code'));

        // Apply filters
        if ($dateRange = request()->input('date_range_filter')) {
            if (strpos($dateRange, ' - ') !== false) {
                [$dateFrom, $dateTo] = array_map('trim', explode(' - ', $dateRange));
                $dateFrom .= ' 00:00:00';
                $dateTo .= ' 23:59:59';
                $query->whereBetween('pi.created_at', [$dateFrom, $dateTo]);
            }
        }

        if ($invoiceDate = request()->input('invoice_date_filter')) {
            $query->where('pi.invoice_date', $invoiceDate);
        }

        if ($dueDate = request()->input('due_date_filter')) {
            $query->where('pi.due_date', $dueDate);
        }

        if ($supplier = request()->input('supplier_filter')) {
            $query->where('po.f_supplier_id', $supplier);
        }

        if ($status = request()->input('status_filter')) {
            $query->where('pi.status', $status);
        }

        if ($invoiceSearch = request()->input('invoice_search_filter')) {
            $query->where(function ($q) use ($invoiceSearch) {
                $q->where('pi.transaction_code', 'like', "%{$invoiceSearch}%")
                    ->orWhere('po.transaction_code', 'like', "%{$invoiceSearch}%");
            });
        }

        $query->groupBy('pi.id');
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy('pi.created_at', 'DESC');
        $data = $query->get();

        // Format data for DataTable
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'transaction_date' => $item->created_at,
                'invoice_number' => $item->transaction_code,
                'invoice_date' => $item->invoice_date,
                'due_date' => $item->due_date,
                'po_reference' => $item->po_reference,
                'po_id' => $item->po_id,
                'supplier_name' => $item->supplier_name,
                'supplier_id' => $item->supplier_id,
                'grand_total' => $item->grand_total,
                'status' => $item->status,
                'payment_terms' => $item->payment_terms,
                'created_by_name' => $item->created_by_name,
                'created_by' => $item->created_by,
                'tags' => $item->tags,
            ];
        });

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
        ];
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as pi')
            ->select([
                'pi.*',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                'po.transaction_date as po_date',
                'po.status as po_status',
                'po.grand_price as po_grand_total',
                'po.supplier_email',
                'po.supplier_address',
                'po.f_warehouse_id',
                'mc.name as supplier_name',
                'mc.id as supplier_id',
                'pt.code AS payment_term_code',
                'pt.name AS payment_term_name',
                'pt.type AS payment_term_type',
                'mw.code AS warehouse_code',
                'mw.name AS warehouse_name',
                'u_created.name as created_by_name',
                'u_updated.name as updated_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('t_purchase_orders as po', 'pi.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as mc', 'po.f_supplier_id', '=', 'mc.id')
            ->leftJoin('m_payment_terms as pt', 'po.f_payment_term_id', '=', 'pt.id')
            ->leftJoin('m_warehouses as mw', 'po.f_warehouse_id', '=', 'mw.id')
            ->leftJoin('users as u_created', 'pi.created_by', '=', 'u_created.id')
            ->leftJoin('users as u_updated', 'pi.updated_by', '=', 'u_updated.id')
            ->leftJoin(DB::raw("JSON_TABLE(pi.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('pi.id', $id)
            ->where('pi.company_code', session('user_company_code'))
            ->groupBy('pi.id')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags);
        }
        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes);
        }
        if ($data && $data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }

        return $data;
    }

    public function createInvoiceProducts(int $invoiceId, array $products)
    {
        $insertData = [];
        foreach ($products as $product) {
            $insertData[] = [
                'f_purchase_invoice_id' => $invoiceId,
                'f_product_order_product_id' => $product['product_order_product_id'],
                'f_product_id' => $product['product_id'],
                'invoiced_qty_before' => $product['invoiced_qty_before'],
                'remaining_qty_before' => $product['remaining_qty_before'],
                'qty' => $product['invoice_qty'],
                'unit_price' => $product['unit_price'],
                'total_price' => $product['total_price'],
                'invoiced_qty_after' => $product['invoiced_qty_after'] ?? ($product['invoiced_qty_before'] + $product['invoice_qty']),
                'remaining_qty_after' => $product['remaining_qty_after'] ?? ($product['remaining_qty_before'] - $product['invoice_qty']),
                'note' => $product['note'] ?? null,
                'status' => $product['status'] ?? 'ACTIVE',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ];
        }

        if (! empty($insertData)) {
            DB::table('t_purchase_invoice_products')->insert($insertData);
        }
    }

    public function findAllProducts($id)
    {
        return DB::table('t_purchase_invoice_products'.' as pip')
            ->select([
                'pip.*',
                'pip.note AS product_invoice_note',
                'pop.qty AS ordered_qty',
                'pop.price_per_qty AS unit_price',
                'mp.name AS product_name',
                'mu.code AS unit_code',
                'mu.name AS unit_name',
            ])
            ->leftJoin('m_products as mp', 'pip.f_product_id', '=', 'mp.id')
            ->leftJoin('t_purchase_order_products as pop', 'pip.f_product_order_product_id', '=', 'pop.id')
            ->leftJoin('m_units as mu', 'mu.id', '=', 'pop.f_unit_id')
            ->where('pip.f_purchase_invoice_id', $id)
            ->orderBy('pip.id', 'ASC')
            ->get();
    }

    public function findAvailableForInvoicing()
    {
        return DB::table('t_purchase_orders as po')
            ->select([
                'po.id as value',
                DB::raw("CONCAT(po.transaction_code, ' - ', mc.name) as label"),
                'mc.name as supplier',
                'mc.id as supplier_id',
                'po.supplier_email as email',
                'po.supplier_address as address',
                'po.f_warehouse_id as warehouse_id',
                'po.transaction_code as reference_no',
                'po.transaction_date as po_date',
                'po.status as po_status',
                'po.grand_price as po_grand_total',
                'pt.code as payment_term_code',
                'pt.name as payment_term_name',
                'pt.type as payment_term_type',
                'pt.days as payment_days',
            ])
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->leftJoin('m_payment_terms as pt', 'po.f_payment_term_id', '=', 'pt.id')
            ->where('po.company_code', session('user_company_code'))
            ->whereIn('po.status', ['APPROVED', 'PARTIAL_INVOICED'])
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
                    'po_date' => $item->po_date,
                    'po_status' => $item->po_status,
                    'po_grand_total' => $item->po_grand_total,
                    'payment_term_code' => $item->payment_term_code,
                    'payment_term_name' => $item->payment_term_name,
                    'payment_term_type' => $item->payment_term_type,
                    'payment_days' => $item->payment_days,
                ];
            })
            ->toArray();
    }

    public function findAllHeaderBasedOnPoId($poId)
    {
        return DB::table($this->table_name.' as pi')
            ->select([
                'pi.*',
                'u.name as created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin(DB::raw("JSON_TABLE(pi.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->leftJoin('users as u', 'pi.created_by', '=', 'u.id')
            ->where('pi.f_purchase_order_id', $poId)
            ->where('pi.company_code', session('user_company_code'))
            ->groupBy('pi.id')
            ->orderBy('pi.invoice_date', 'ASC')
            ->get();
    }

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('invoice_date', $year)
            ->whereMonth('invoice_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }
}
