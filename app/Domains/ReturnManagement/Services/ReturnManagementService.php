<?php

namespace App\Domains\ReturnManagement\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnManagementService
{
    private $table_name = 't_return_managements';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('return_date', $year)
            ->whereMonth('return_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['transaction_code'],
            'return_type' => $data['return_type'],
            'f_source_invoice_id' => $data['source_invoice_id'],
            'source_invoice_code' => $data['source_invoice_code'],
            'return_date' => $data['return_date'],
            'transaction_date' => $data['transaction_date'],
            'return_reason' => $data['return_reason'] ?? 'OTHER',
            'return_reason_description' => $data['return_reason_description'] ?? null,
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['internal_memo'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'total_tax' => $data['total_tax'] ?? 0,
            'total_discount' => $data['total_discount'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'status' => $data['status'] ?? 'DRAFT',
            'processing_status' => 'NOT_PROCESSED',
            'approval_notes' => $data['approval_notes'] ?? null,
            'approved_by' => $data['approved_by'] ?? null,
            'approved_at' => $data['approved_at'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->table_name.' as rm')
            ->select(
                'rm.*',
                'creator.name as created_by_name',
                'updater.name as updated_by_name',
                'approver.name as approved_by_name'
            )
            ->leftJoin('users as creator', 'rm.created_by', '=', 'creator.id')
            ->leftJoin('users as updater', 'rm.updated_by', '=', 'updater.id')
            ->leftJoin('users as approver', 'rm.approved_by', '=', 'approver.id')
            ->where('rm.id', $id)
            ->where('rm.company_code', session('user_company_code'))
            ->first();
    }

    public function findBySourceInvoiceId(int $invoiceId, string $returnType)
    {
        return DB::table($this->table_name.' as rm')
            ->select(
                'rm.*',
                'u.name as created_by_name',
                DB::raw('(SELECT COUNT(*) FROM t_return_management_products WHERE f_return_management_id = rm.id) as product_count')
            )
            ->leftJoin('users as u', 'rm.created_by', '=', 'u.id')
            ->where('rm.company_code', session('user_company_code'))
            ->where('rm.f_source_invoice_id', $invoiceId)
            ->where('rm.return_type', $returnType)
            ->first();
    }

    public function findByPurchaseOrderId(int $purchaseOrderId)
    {
        return DB::table($this->table_name.' as rm')
            ->select(
                'rm.*',
                'u.name as created_by_name',
                'pi.transaction_code as source_invoice_code'
            )
            ->leftJoin('users as u', 'rm.created_by', '=', 'u.id')
            ->leftJoin('t_purchase_invoices as pi', 'rm.f_source_invoice_id', '=', 'pi.id')
            ->where('rm.company_code', session('user_company_code'))
            ->where('pi.f_purchase_order_id', $purchaseOrderId)
            ->where('rm.return_type', 'PURCHASE_RETURN')
            ->orderBy('rm.created_at', 'desc')
            ->get();
    }

    public function findBySalesOrderId(int $salesOrderId)
    {
        return DB::table($this->table_name.' as rm')
            ->select(
                'rm.*',
                'u.name as created_by_name',
                'si.transaction_code as source_invoice_code'
            )
            ->leftJoin('users as u', 'rm.created_by', '=', 'u.id')
            ->leftJoin('t_sales_invoices as si', 'rm.f_source_invoice_id', '=', 'si.id')
            ->where('rm.company_code', session('user_company_code'))
            ->where('si.f_sales_order_id', $salesOrderId)
            ->where('rm.return_type', 'SALES_RETURN')
            ->orderBy('rm.created_at', 'desc')
            ->get();
    }

    public function update(int $id, array $data): int
    {
        $updateData = [];
        
        // Only include fields that exist in the data array
        $allowedFields = [
            'return_date', 'transaction_date', 'return_reason', 'return_reason_description',
            'description', 'internal_memo', 'attachment', 'subtotal', 'total_tax',
            'total_discount', 'grand_total', 'status', 'processing_status',
            'approval_notes', 'approved_by', 'approved_at'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Handle tags separately
        if (isset($data['tags'])) {
            $updateData['f_tag_codes'] = json_encode($data['tags']);
        }

        $updateData['updated_at'] = now();
        $updateData['updated_by'] = Auth::id();

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($updateData);
    }

    public function delete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->delete();
    }

    public function getAllForDataTable(array $filters = [])
    {
        $query = DB::table($this->table_name . ' as rm')
            ->leftJoin('t_purchase_invoices as pi', function ($join) {
                $join->on('rm.f_source_invoice_id', '=', 'pi.id')
                     ->where('rm.return_type', '=', 'PURCHASE_RETURN');
            })
            ->leftJoin('t_sales_invoices as si', function ($join) {
                $join->on('rm.f_source_invoice_id', '=', 'si.id')
                     ->where('rm.return_type', '=', 'SALES_RETURN');
            })
            ->leftJoin('users as creator', 'rm.created_by', '=', 'creator.id')
            ->leftJoin('users as approver', 'rm.approved_by', '=', 'approver.id')
            ->where('rm.company_code', session('user_company_code'))
            ->select([
                'rm.*',
                'creator.name as created_by_name',
                'approver.name as approved_by_name'
            ]);

        // Apply filters if provided
        if (!empty($filters['status'])) {
            $query->where('rm.status', $filters['status']);
        }

        if (!empty($filters['return_type'])) {
            $query->where('rm.return_type', $filters['return_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('rm.return_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('rm.return_date', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as rm')
            ->select(
                'rm.*',
                'u.name AS created_by_name',
                DB::raw('COALESCE(rm.f_tag_codes, "[]") as tags')
            )
            ->leftJoin('users as u', 'rm.created_by', '=', 'u.id')
            ->where('rm.company_code', session('user_company_code'));

        // Apply filters
        if ($status = request()->input('status_filter')) {
            $query->where('rm.status', $status);
        }

        if ($returnType = request()->input('return_type_filter')) {
            $query->where('rm.return_type', $returnType);
        }

        if ($dateFrom = request()->input('date_from')) {
            // Convert DD/MM/YYYY to YYYY-MM-DD for database comparison
            $dateFromFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $dateFrom)));
            $query->whereDate('rm.return_date', '>=', $dateFromFormatted);
        }

        if ($dateTo = request()->input('date_to')) {
            // Convert DD/MM/YYYY to YYYY-MM-DD for database comparison
            $dateToFormatted = date('Y-m-d', strtotime(str_replace('/', '-', $dateTo)));
            $query->whereDate('rm.return_date', '<=', $dateToFormatted);
        }

        if ($transactionCode = request()->input('transaction_code_filter')) {
            $query->where('rm.transaction_code', 'like', "%{$transactionCode}%");
        }
        
        // Filter by Source Sales Order for return history
        if ($sourceSoId = request()->input('source_so_id')) {
            $query->join('t_sales_invoices as si', 'rm.f_source_invoice_id', '=', 'si.id')
                  ->where('si.f_sales_order_id', $sourceSoId);
        }
        
        // Filter by Source Purchase Order for return history
        if ($sourcePoId = request()->input('source_po_id')) {
            $query->join('t_purchase_invoices as pi', 'rm.f_source_invoice_id', '=', 'pi.id')
                  ->where('pi.f_purchase_order_id', $sourcePoId);
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('rm.id', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function updateStatus(int $id, string $status, array $additionalData = []): int
    {
        $updateData = [
            'status' => $status,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];

        // Add additional data based on status
        if ($status === 'APPROVED' && isset($additionalData['approval_notes'])) {
            $updateData['approval_notes'] = $additionalData['approval_notes'];
            $updateData['approved_by'] = Auth::id();
            $updateData['approved_at'] = now();
        }

        if (isset($additionalData['processing_status'])) {
            $updateData['processing_status'] = $additionalData['processing_status'];
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($updateData);
    }
}