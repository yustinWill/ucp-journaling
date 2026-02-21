<?php

namespace App\Helpers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionHelper
{
    /**
     * Check if user has permission
     *
     * @param User $user
     * @param string $permission
     * @return bool
     */
    public static function hasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has role
     *
     * @param User $user
     * @param string $role
     * @return bool
     */
    public static function hasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Check if user has any of the given roles
     *
     * @param User $user
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole(User $user, array $roles): bool
    {
        return $user->hasAnyRole($roles);
    }

    /**
     * Check if user has all of the given roles
     *
     * @param User $user
     * @param array $roles
     * @return bool
     */
    public static function hasAllRoles(User $user, array $roles): bool
    {
        return $user->hasAllRoles($roles);
    }

    /**
     * Assign role to user
     *
     * @param User $user
     * @param string $role
     * @return mixed
     */
    public static function assignRole(User $user, string $role)
    {
        return $user->assignRole($role);
    }

    /**
     * Assign multiple roles to user
     *
     * @param User $user
     * @param array $roles
     * @return mixed
     */
    public static function assignRoles(User $user, array $roles)
    {
        return $user->assignRole($roles);
    }

    /**
     * Remove role from user
     *
     * @param User $user
     * @param string $role
     * @return mixed
     */
    public static function removeRole(User $user, string $role)
    {
        return $user->removeRole($role);
    }

    /**
     * Sync roles for user
     *
     * @param User $user
     * @param array $roles
     * @return mixed
     */
    public static function syncRoles(User $user, array $roles)
    {
        return $user->syncRoles($roles);
    }

    /**
     * Give permission to user
     *
     * @param User $user
     * @param string $permission
     * @return mixed
     */
    public static function givePermission(User $user, string $permission)
    {
        return $user->givePermissionTo($permission);
    }

    /**
     * Get all permissions for user
     *
     * @param User $user
     * @return mixed
     */
    public static function getUserPermissions(User $user)
    {
        return $user->getPermissionsViaRoles()->merge($user->getDirectPermissions());
    }

    /**
     * Get all roles for user
     *
     * @param User $user
     * @return mixed
     */
    public static function getUserRoles(User $user)
    {
        return $user->getRoleNames();
    }

    /**
     * Get all permissions
     *
     * @return mixed
     */
    public static function getAllPermissions()
    {
        return Permission::all();
    }

    /**
     * Get all roles
     *
     * @return mixed
     */
    public static function getAllRoles()
    {
        return Role::all();
    }

    /**
     * Create new role
     *
     * @param string $name
     * @return Role
     */
    public static function createRole(string $name, string $guardName = 'web'): Role
    {
        return Role::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Create new permission
     *
     * @param string $name
     * @return Permission
     */
    public static function createPermission(string $name, string $guardName = 'web'): Permission
    {
        return Permission::create([
            'name' => $name,
            'guard_name' => $guardName,
        ]);
    }

    /**
     * Assign permission to role
     *
     * @param Role $role
     * @param string $permission
     * @return mixed
     */
    public static function assignPermissionToRole(Role $role, string $permission)
    {
        return $role->givePermissionTo($permission);
    }

    /**
     * Get permissions for role
     *
     * @param Role $role
     * @return mixed
     */
    public static function getRolePermissions(Role $role)
    {
        return $role->permissions;
    }

    /**
     * Get view permissions for a module
     * Returns an array of permission flags for Blade templates
     *
     * @param string $module Module name (e.g., 'main_transaction_reimbursement')
     * @param User|null $user Optional user, defaults to auth()->user()
     * @return array
     */
    public static function getViewPermissions(string $module, User $user = null): array
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return self::getDefaultPermissions($module);
        }

        // Convert module name to PascalCase for variable names
        // e.g., 'main_transaction_reimbursement' -> 'Reimbursement'
        $permissionKey = self::getPermissionKey($module);

        return [
            "canView{$permissionKey}" => $user->hasPermissionTo("{$module}.view") || $user->hasPermissionTo("{$module}.read"),
            "canCreate{$permissionKey}" => $user->hasPermissionTo("{$module}.create") || $user->hasPermissionTo("{$module}.write"),
            "canEdit{$permissionKey}" => $user->hasPermissionTo("{$module}.edit") || $user->hasPermissionTo("{$module}.write"),
            "canDelete{$permissionKey}" => $user->hasPermissionTo("{$module}.delete"),
            "canApprove{$permissionKey}" => $user->hasPermissionTo("{$module}.approve"),
        ];
    }

    /**
     * Get default permissions (all false) for a module
     *
     * @param string $module
     * @return array
     */
    private static function getDefaultPermissions(string $module): array
    {
        $permissionKey = self::getPermissionKey($module);

        return [
            "canView{$permissionKey}" => false,
            "canCreate{$permissionKey}" => false,
            "canEdit{$permissionKey}" => false,
            "canDelete{$permissionKey}" => false,
            "canApprove{$permissionKey}" => false,
        ];
    }

    /**
     * Convert module name to PascalCase permission key
     * e.g., 'main_transaction_reimbursement' -> 'Reimbursement'
     * e.g., 'main_transaction_reimbursement_approval' -> 'ReimbursementApproval'
     *
     * @param string $module
     * @return string
     */
    private static function getPermissionKey(string $module): string
    {
        // Remove common prefixes
        $module = preg_replace('/^(main_transaction|master_data|operasional)_/', '', $module);

        // Convert to PascalCase (split by underscore and capitalize each part)
        $parts = explode('_', $module);
        return implode('', array_map('ucfirst', $parts));
    }
}
