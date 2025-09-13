<?php

namespace App\Domains\SalesInvoice\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesInvoiceAuditLogService
{
    private $table_name = 't_sales_invoice_audit_logs';

    public function create(int $salesInvoiceId, string $actionType, string $description, array $metadata = [])
    {
        return DB::table($this->table_name)->insertGetId([
            'f_sales_invoice_id' => $salesInvoiceId,
            'created_by' => Auth::id(),
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function findAllBySalesInvoiceId(int $salesInvoiceId)
    {
        return DB::table($this->table_name.' as sal')
            ->select([
                'sal.*',
                'u.name as user_name',
            ])
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.f_sales_invoice_id', $salesInvoiceId)
            ->orderBy('sal.created_at', 'DESC')
            ->get();
    }

    public function findAllDatatableBySalesInvoiceId(int $salesInvoiceId)
    {
        $query = DB::table($this->table_name.' as sal')
            ->select([
                'sal.*',
                'u.name as user_name',
            ])
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.f_sales_invoice_id', $salesInvoiceId);

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = $query->count();
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy('sal.created_at', 'DESC');
        $data = $query->get();

        // Format data for DataTable
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'created_at' => $item->created_at,
                'user_name' => $item->user_name,
                'action_type' => $item->action_type,
                'description' => $item->description,
                'metadata' => $item->metadata ? json_decode($item->metadata, true) : null,
            ];
        });

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
        ];
    }
}
