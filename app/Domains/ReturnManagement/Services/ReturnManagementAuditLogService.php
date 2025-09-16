<?php

namespace App\Domains\ReturnManagement\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnManagementAuditLogService
{
    private $table_name = 't_return_management_audit_logs';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_return_management_id' => $data['f_return_management_id'],
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => isset($data['metadata']) ? json_encode($data['metadata']) : null,
            'user_id' => $data['user_id'] ?? Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findByReturnId(int $returnId)
    {
        return DB::table($this->table_name . ' as ral')
            ->leftJoin('users as u', 'ral.user_id', '=', 'u.id')
            ->where('ral.f_return_management_id', $returnId)
            ->where('ral.company_code', session('user_company_code'))
            ->orderBy('ral.created_at', 'desc')
            ->select([
                'ral.*',
                'u.name as user_name'
            ])
            ->get();
    }

    public function getForDataTable(int $returnId)
    {
        return DB::table($this->table_name . ' as ral')
            ->leftJoin('users as u', 'ral.user_id', '=', 'u.id')
            ->where('ral.f_return_management_id', $returnId)
            ->where('ral.company_code', session('user_company_code'))
            ->select([
                'ral.created_at',
                'u.name as user_name',
                'ral.action_type',
                'ral.description',
                'ral.user_id'
            ])
            ->orderBy('ral.created_at', 'desc');
    }

    public function logCreate(int $returnId, string $transactionCode, array $metadata = []): int
    {
        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'CREATE',
            'description' => "Return {$transactionCode} dibuat",
            'metadata' => $metadata
        ]);
    }

    public function logUpdate(int $returnId, string $transactionCode, array $changes = [], array $metadata = []): int
    {
        $changeDescription = '';
        if (!empty($changes)) {
            $changeItems = [];
            foreach ($changes as $field => $change) {
                $changeItems[] = "{$field}: {$change['old']} → {$change['new']}";
            }
            $changeDescription = ' (' . implode(', ', $changeItems) . ')';
        }

        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'UPDATE',
            'description' => "Return {$transactionCode} diperbarui{$changeDescription}",
            'metadata' => array_merge($metadata, ['changes' => $changes])
        ]);
    }

    public function logApprove(int $returnId, string $transactionCode, string $approvalNotes = '', array $metadata = []): int
    {
        $description = "Return {$transactionCode} disetujui";
        if ($approvalNotes) {
            $description .= " dengan catatan: {$approvalNotes}";
        }

        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'APPROVE',
            'description' => $description,
            'metadata' => array_merge($metadata, ['approval_notes' => $approvalNotes])
        ]);
    }

    public function logReject(int $returnId, string $transactionCode, string $reason = '', array $metadata = []): int
    {
        $description = "Return {$transactionCode} ditolak";
        if ($reason) {
            $description .= " dengan alasan: {$reason}";
        }

        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'REJECT',
            'description' => $description,
            'metadata' => array_merge($metadata, ['rejection_reason' => $reason])
        ]);
    }

    public function logCancel(int $returnId, string $transactionCode, string $reason = '', array $metadata = []): int
    {
        $description = "Return {$transactionCode} dibatalkan";
        if ($reason) {
            $description .= " dengan alasan: {$reason}";
        }

        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'CANCEL',
            'description' => $description,
            'metadata' => array_merge($metadata, ['cancellation_reason' => $reason])
        ]);
    }

    public function logProcess(int $returnId, string $transactionCode, string $processingStatus = 'PARTIAL', array $metadata = []): int
    {
        $statusText = $processingStatus === 'COMPLETED' ? 'selesai diproses' : 'mulai diproses';
        
        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'PROCESS',
            'description' => "Return {$transactionCode} {$statusText}",
            'metadata' => array_merge($metadata, ['processing_status' => $processingStatus])
        ]);
    }

    public function logComplete(int $returnId, string $transactionCode, array $metadata = []): int
    {
        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'COMPLETE',
            'description' => "Return {$transactionCode} diselesaikan",
            'metadata' => $metadata
        ]);
    }

    public function logDelete(int $returnId, string $transactionCode, array $metadata = []): int
    {
        return $this->create([
            'f_return_management_id' => $returnId,
            'action_type' => 'DELETE',
            'description' => "Return {$transactionCode} dihapus",
            'metadata' => $metadata
        ]);
    }
}