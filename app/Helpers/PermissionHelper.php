<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class PermissionHelper
{
    private static function generatePermissions()
    {
        $permissions = Session::get('permissions');
        $formattedPermissions = [];

        foreach ($permissions as $permission) {
            $moduleName = $permission->module_name;
            $permissionType = $permission->permission_type;
            $permissionStatus = $permission->status;

            $formattedPermissions[$moduleName][$permissionType] = $permissionStatus;
        }
        return $formattedPermissions;
    }
    /**
     * Mock permissions for development - replace with actual Gate logic in production
     * All permissions set to true for development phase
     */
    private static function getMockPermissions(): array
    {
        return self::generatePermissions();
    }

    /**
     * Check if current user has specific permission
     */
    public static function hasPermission(string $module, string $permission): bool
    {
        // For development, use mock permissions
        $mockPermissions = self::getMockPermissions();
        if (isset($mockPermissions[$module][$permission])) {
            return $mockPermissions[$module][$permission];
        }

        // Fallback to Gate for production
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
     * Get permission-aware view data for controllers
     */
    public static function getViewPermissions(string $module): array
    {
        $permissions = self::getModulePermissions($module);

        return [
            'permissions' => $permissions,
            'canRead' => $permissions['read'],
            'canWrite' => $permissions['write'],
            'canDelete' => $permissions['delete'],
            'canApprove' => $permissions['approval'],
            'canShowAddButton' => $permissions['write'],
            'canShowEditButton' => $permissions['write'],
            'canShowDeleteButton' => $permissions['delete'],
            'canShowImportButton' => $permissions['write'],
            'canShowExportButton' => $permissions['read']
        ];
    }

    /**
     * Check if user can access a specific route
     */
    public static function canAccessRoute(string $route): bool
    {
        // Map routes to permissions
        $routePermissions = [
            // Sales Person
            'master_data.sales_person.index' => ['master_data_sales_person', 'read'],
            'master_data.sales_person.detail' => ['master_data_sales_person', 'read'],
            'master_data.sales_person.export' => ['master_data_sales_person', 'read'],
            'master_data.sales_person.add' => ['master_data_sales_person', 'write'],
            'master_data.sales_person.save' => ['master_data_sales_person', 'write'],
            'master_data.sales_person.edit' => ['master_data_sales_person', 'write'],
            'master_data.sales_person.update' => ['master_data_sales_person', 'write'],
            'master_data.sales_person.import' => ['master_data_sales_person', 'write'],
            'master_data.sales_person.delete' => ['master_data_sales_person', 'delete'],

            // Tax
            'master_data.tax.index' => ['master_data_tax', 'read'],
            'master_data.tax.detail' => ['master_data_tax', 'read'],
            'master_data.tax.export' => ['master_data_tax', 'read'],
            'master_data.tax.add' => ['master_data_tax', 'write'],
            'master_data.tax.save' => ['master_data_tax', 'write'],
            'master_data.tax.edit' => ['master_data_tax', 'write'],
            'master_data.tax.update' => ['master_data_tax', 'write'],
            'master_data.tax.import' => ['master_data_tax', 'write'],
            'master_data.tax.delete' => ['master_data_tax', 'delete'],

            // Unit
            'master_data.unit.index' => ['master_data_unit', 'read'],
            'master_data.unit.detail' => ['master_data_unit', 'read'],
            'master_data.unit.export' => ['master_data_unit', 'read'],
            'master_data.unit.add' => ['master_data_unit', 'write'],
            'master_data.unit.save' => ['master_data_unit', 'write'],
            'master_data.unit.edit' => ['master_data_unit', 'write'],
            'master_data.unit.update' => ['master_data_unit', 'write'],
            'master_data.unit.import' => ['master_data_unit', 'write'],
            'master_data.unit.delete' => ['master_data_unit', 'delete'],

            // Category
            'master_data.category.index' => ['master_data_category', 'read'],
            'master_data.category.detail' => ['master_data_category', 'read'],
            'master_data.category.export' => ['master_data_category', 'read'],
            'master_data.category.add' => ['master_data_category', 'write'],
            'master_data.category.save' => ['master_data_category', 'write'],
            'master_data.category.edit' => ['master_data_category', 'write'],
            'master_data.category.update' => ['master_data_category', 'write'],
            'master_data.category.import' => ['master_data_category', 'write'],
            'master_data.category.delete' => ['master_data_category', 'delete'],
        ];

        if (!isset($routePermissions[$route])) {
            return false;
        }

        [$module, $permission] = $routePermissions[$route];
        return self::hasPermission($module, $permission);
    }

    /**
     * Get menu items that user has access to
     */
    public static function getAccessibleMenuItems(): array
    {
        $modules = [
            'master_data_sales_person' => 'Sales Person',
            'master_data_tax' => 'Pajak',
            'master_data_unit' => 'Unit',
            'master_data_category' => 'Kategori',
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
