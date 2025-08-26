<?php

namespace App\Domains\Production\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionAuditLogService
{
    private $table_name = 't_production_audit_logs';

    public function create(int $productionId, string $actionType, string $description, array $metadata = []): int
    {
        return DB::table($this->table_name)->insertGetId([
            'production_id' => $productionId,
            'user_id' => Auth::id(),
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_at' => now()
        ]);
    }

    public function findByProductionId(int $productionId): array
    {
        return DB::table($this->table_name . ' as pal')
            ->select(
                'pal.*',
                'u.name as user_name'
            )
            ->leftJoin('users as u', 'pal.user_id', '=', 'u.id')
            ->where('pal.production_id', $productionId)
            ->orderBy('pal.created_at', 'DESC')
            ->get()
            ->map(function ($log) {
                if ($log->metadata) {
                    $log->metadata = json_decode($log->metadata, true);
                }
                return $log;
            })
            ->toArray();
    }

    public function logCreation(int $productionId, array $productionData, array $items = [], array $expenses = []): int
    {
        $metadata = [
            'production_data' => $productionData,
            'items_count' => count($items),
            'expenses_count' => count($expenses),
            'total_raw_material_cost' => array_sum(array_column($items, 'total_cost')),
            'total_production_expense' => array_sum(array_column($expenses, 'actual_amount')),
            'items_summary' => $this->summarizeItems($items),
            'expenses_summary' => $this->summarizeExpenses($expenses)
        ];

        return $this->create(
            $productionId,
            'CREATE',
            "Production order {$productionData['production_number']} created",
            $metadata
        );
    }

    public function logUpdate(int $productionId, array $beforeData, array $afterData, array $changes = []): int
    {
        $metadata = [
            'before' => $beforeData,
            'after' => $afterData,
            'changes' => $changes,
            'changed_fields' => array_keys($changes)
        ];

        $changedFieldsText = implode(', ', array_keys($changes));
        
        return $this->create(
            $productionId,
            'UPDATE',
            "Production updated. Changed fields: {$changedFieldsText}",
            $metadata
        );
    }

    public function logStatusChange(int $productionId, string $fromStatus, string $toStatus, string $reason = null): int
    {
        $metadata = [
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'reason' => $reason,
            'status_change_time' => now()->toISOString()
        ];

        return $this->create(
            $productionId,
            'STATUS_CHANGE',
            "Status changed from {$fromStatus} to {$toStatus}",
            $metadata
        );
    }

    public function logCompletion(int $productionId, array $completionData): int
    {
        $metadata = [
            'completion_data' => $completionData,
            'quantity_produced' => $completionData['quantity_produced'] ?? 0,
            'completion_time' => now()->toISOString(),
            'stock_movements' => $completionData['stock_movements'] ?? [],
            'batch_created' => $completionData['batch_created'] ?? null
        ];

        return $this->create(
            $productionId,
            'COMPLETE',
            "Production completed. Produced: {$completionData['quantity_produced']} units",
            $metadata
        );
    }

    public function logReversal(int $productionId, array $reversalData): int
    {
        $metadata = [
            'reversal_data' => $reversalData,
            'reversed_quantity' => $reversalData['quantity_reversed'] ?? 0,
            'reversal_time' => now()->toISOString(),
            'stock_movements_reversed' => $reversalData['stock_movements'] ?? [],
            'batches_affected' => $reversalData['batches_affected'] ?? [],
            'reversal_reason' => $reversalData['reason'] ?? null
        ];

        return $this->create(
            $productionId,
            'REVERSAL',
            "Production reversed. Quantity: {$reversalData['quantity_reversed']} units",
            $metadata
        );
    }

    public function logDeletion(int $productionId, array $productionData): int
    {
        $metadata = [
            'deleted_production' => $productionData,
            'deletion_time' => now()->toISOString(),
            'had_stock_impact' => $productionData['status'] === 'COMPLETED'
        ];

        return $this->create(
            $productionId,
            'DELETE',
            "Production order {$productionData['production_number']} deleted",
            $metadata
        );
    }

    public function getActionsSummary(int $productionId): array
    {
        $logs = DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->select('action_type', DB::raw('COUNT(*) as count'))
            ->groupBy('action_type')
            ->get()
            ->pluck('count', 'action_type')
            ->toArray();

        return [
            'total_actions' => array_sum($logs),
            'actions_breakdown' => $logs,
            'last_action' => DB::table($this->table_name)
                ->where('production_id', $productionId)
                ->orderBy('created_at', 'DESC')
                ->value('action_type')
        ];
    }

    public function getUserActivity(int $userId, int $days = 30): array
    {
        return DB::table($this->table_name . ' as pal')
            ->select(
                'pal.action_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('DATE(pal.created_at) as action_date')
            )
            ->where('pal.user_id', $userId)
            ->where('pal.created_at', '>=', now()->subDays($days))
            ->groupBy('pal.action_type', 'action_date')
            ->orderBy('action_date', 'DESC')
            ->get()
            ->toArray();
    }

    private function summarizeItems(array $items): array
    {
        return array_map(function ($item) {
            return [
                'product_id' => $item['raw_material_product_id'] ?? null,
                'quantity' => $item['quantity_consumed'] ?? $item['quantity_required'] ?? 0,
                'cost' => $item['total_cost'] ?? 0
            ];
        }, $items);
    }

    private function summarizeExpenses(array $expenses): array
    {
        return array_map(function ($expense) {
            return [
                'type' => $expense['expense_type'] ?? null,
                'name' => $expense['expense_name'] ?? null,
                'amount' => $expense['actual_amount'] ?? 0
            ];
        }, $expenses);
    }
}