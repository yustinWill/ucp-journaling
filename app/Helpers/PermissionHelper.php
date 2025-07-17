<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Gate;

class PermissionHelper
{
    /**
     * Check if current user has specific permission
     */
    public static function hasPermission(string $module, string $permission): bool
    {
        return Gate::allows("{$module}.{$permission}");
    }

    /**
     * Check if current user has read permission for module
     */
    public static function canRead(string $module): bool
    {
        return self::hasPermission($module, 'read');
    }

    /**
     * Check if current user has write permission for module
     */
    public static function canWrite(string $module): bool
    {
        return self::hasPermission($module, 'write');
    }

    /**
     * Check if current user has delete permission for module
     */
    public static function canDelete(string $module): bool
    {
        return self::hasPermission($module, 'delete');
    }

    /**
     * Check if current user has approval permission for module
     */
    public static function canApprove(string $module): bool
    {
        return self::hasPermission($module, 'approval');
    }

    /**
     * Get all permissions for a module
     */
    public static function getModulePermissions(string $module): array
    {
        return [
            'read' => self::canRead($module),
            'write' => self::canWrite($module),
            'delete' => self::canDelete($module),
            'approval' => self::canApprove($module),
        ];
    }

    /**
     * Check if user has any permission for a module
     */
    public static function hasAnyPermission(string $module): bool
    {
        return self::canRead($module) || 
               self::canWrite($module) || 
               self::canDelete($module) || 
               self::canApprove($module);
    }

    /**
     * Get menu items that user has access to
     */
    public static function getAccessibleMenuItems(): array
    {
        $modules = [
            'user' => 'User Management',
            'role' => 'Role Management',
            'contact' => 'Contact Management',
            'product' => 'Product Management',
            'sales' => 'Sales Management',
            'purchase' => 'Purchase Management',
            'journal' => 'Journal Management',
            'expense' => 'Expense Management',
            'coa' => 'COA Management',
        ];

        $accessibleModules = [];
        
        foreach ($modules as $module => $title) {
            if (self::hasAnyPermission($module)) {
                $accessibleModules[$module] = [
                    'title' => $title,
                    'permissions' => self::getModulePermissions($module)
                ];
            }
        }

        return $accessibleModules;
    }
}