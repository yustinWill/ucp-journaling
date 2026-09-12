<?php

namespace App\Domains\SalesOrder\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderRevertLogService
{
    private string $tableName = 't_sales_order_revert_logs';

    public function create(object $salesOrder, array $summary): int
    {
        $actor = Auth::user();
        $customerName = DB::table('m_contacts')
            ->where('id', $salesOrder->f_customer_id)
            ->value('name');

        $orderSnapshot = [
            'id' => (int) $salesOrder->id,
            'transaction_code' => $salesOrder->transaction_code,
            'transaction_date' => $salesOrder->transaction_date,
            'customer_id' => (int) $salesOrder->f_customer_id,
            'customer_name' => $customerName,
            'grand_price' => (float) $salesOrder->grand_price,
            'status' => $salesOrder->status,
        ];

        return DB::table($this->tableName)->insertGetId([
            'company_code' => $salesOrder->company_code,
            'sales_order_id' => $salesOrder->id,
            'transaction_code' => $salesOrder->transaction_code,
            'customer_name' => $customerName,
            'grand_price' => $salesOrder->grand_price,
            'created_by' => Auth::id(),
            'actor_name' => $actor?->name,
            'actor_email' => $actor?->email,
            'order_snapshot' => json_encode($orderSnapshot),
            'summary' => json_encode($summary),
            'created_at' => now(),
        ]);
    }

    public function findAllDatatable(): array
    {
        $companyCode = session('user_company_code');
        $draw = (int) request()->input('draw', 1);

        if (! $companyCode) {
            return [
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ];
        }

        $baseQuery = DB::table($this->tableName)
            ->where('company_code', $companyCode);

        $recordsTotal = (clone $baseQuery)->count();
        $query = clone $baseQuery;

        $search = trim((string) request()->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('transaction_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('actor_name', 'like', "%{$search}%")
                    ->orWhere('actor_email', 'like', "%{$search}%");
            });
        }

        if ($dateFrom = request()->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = request()->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $recordsFiltered = (clone $query)->count();
        $start = max(0, (int) request()->input('start', 0));
        $length = min(100, max(1, (int) request()->input('length', 10)));

        $data = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->skip($start)
            ->take($length)
            ->get()
            ->map(function ($log) {
                $log->order_snapshot = json_decode($log->order_snapshot, true) ?? [];
                $log->summary = json_decode($log->summary, true) ?? [];

                return $log;
            });

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
