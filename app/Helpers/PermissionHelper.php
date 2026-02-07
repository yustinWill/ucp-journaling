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
}
