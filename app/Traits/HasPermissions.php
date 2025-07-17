<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

trait HasPermissions
{
    /**
     * Check if current user has specific permission
     */
    protected function hasPermission(string $module, string $permission): bool
    {
        return Gate::allows("{$module}.{$permission}");
    }

    /**
     * Check if current user has read permission for module
     */
    protected function canRead(string $module): bool
    {
        return $this->hasPermission($module, 'read');
    }

    /**
     * Check if current user has write permission for module
     */
    protected function canWrite(string $module): bool
    {
        return $this->hasPermission($module, 'write');
    }

    /**
     * Check if current user has delete permission for module
     */
    protected function canDelete(string $module): bool
    {
        return $this->hasPermission($module, 'delete');
    }

    /**
     * Check if current user has approval permission for module
     */
    protected function canApprove(string $module): bool
    {
        return $this->hasPermission($module, 'approval');
    }

    /**
     * Abort if user doesn't have permission
     */
    protected function requirePermission(string $module, string $permission): void
    {
        if (!$this->hasPermission($module, $permission)) {
            abort(403, 'Insufficient permissions to access this resource.');
        }
    }

    /**
     * Get user permissions for view
     */
    protected function getUserPermissions(string $module): array
    {
        return [
            'can_read' => $this->canRead($module),
            'can_write' => $this->canWrite($module),
            'can_delete' => $this->canDelete($module),
            'can_approve' => $this->canApprove($module),
        ];
    }

    /**
     * Filter view data based on permissions
     */
    protected function getViewDataWithPermissions(string $module, array $data = []): array
    {
        return array_merge($data, [
            'permissions' => $this->getUserPermissions($module)
        ]);
    }
}