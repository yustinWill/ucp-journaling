<?php

namespace App\Domains\PurchaseDelivery\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseDeliveryAuditLogService
{
    private $table_name = 't_purchase_delivery_audit_logs';

    public function create(array $data, int $purchaseDeliveryId)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_purchase_delivery_id' => $purchaseDeliveryId,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findByPurchaseDeliveryId(int $purchaseDeliveryId)
    {
        return DB::table($this->table_name . ' as pal')
            ->select([
                'pal.*',
                'u.name as user_name',
            ])
            ->leftJoin('users as u', 'pal.created_by', '=', 'u.id')
            ->where('pal.f_purchase_delivery_id', $purchaseDeliveryId)
            ->orderBy('pal.created_at', 'DESC')
            ->get();
    }

    public function findAllDatatableByPurchaseDeliveryId(int $purchaseDeliveryId)
    {
        $query = DB::table($this->table_name . ' as pal')
            ->select([
                'pal.created_at',
                'pal.user_id',
                'pal.action_type',
                'pal.description',
                'u.name as user_name',
            ])
            ->leftJoin('m_users as u', 'pal.user_id', '=', 'u.id')
            ->where('pal.purchase_delivery_id', $purchaseDeliveryId);

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('purchase_delivery_id', $purchaseDeliveryId)->count();
        $recordsFiltered = $recordsTotal;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $query->orderBy('pal.created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
