<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Example controller demonstrating Spatie Permission usage
 *
 * This is a reference implementation showing how to:
 * - Check user permissions
 * - Check user roles
 * - Assign/manage roles
 * - Use helper methods
 */
class PermissionExampleController extends Controller
{
    /**
     * Show all users with their roles
     */
    public function index()
    {
        // Check if current user has permission
        if (!auth()->user()->hasPermissionTo('view_users')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Check user permission example
     */
    public function checkPermission(User $user)
    {
        // Method 1: Using trait method directly
        $hasPermission = $user->hasPermissionTo('view_users');

        // Method 2: Using can() method
        $hasPermission = auth()->user()->can('view_users');

        // Method 3: Using gate
        $hasPermission = Gate::allows('view_users');

        return response()->json([
            'has_permission' => $hasPermission
        ]);
    }

    /**
     * Assign role to user example
     */
    public function assignRole(Request $request, User $user)
    {
        // Check if current user can manage roles
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        // Sync roles (replaces all existing roles)
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Assign multiple roles to user example
     */
    public function assignMultipleRoles(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        // Sync multiple roles
        $user->syncRoles($validated['roles']);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Give direct permission to user example
     */
    public function givePermission(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        // Give permission directly to user (bypassing roles)
        $user->givePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permission granted successfully',
            'user' => $user->load('permissions')
        ]);
    }

    /**
     * Get user roles and permissions
     */
    public function getRolesAndPermissions(User $user)
    {
        // Get all roles
        $roles = $user->getRoleNames(); // Collection of role names

        // Get all permissions (from roles + direct)
        $permissions = $user->getAllPermissions();

        // Get direct permissions only
        $directPermissions = $user->getDirectPermissions();

        // Get permissions via roles
        $rolePermissions = $user->getPermissionsViaRoles();

        return response()->json([
            'roles' => $roles,
            'all_permissions' => $permissions->pluck('name'),
            'direct_permissions' => $directPermissions->pluck('name'),
            'role_permissions' => $rolePermissions->pluck('name'),
        ]);
    }

    /**
     * Check multiple permissions example
     */
    public function checkMultiplePermissions(User $user)
    {
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users'
        ];

        $results = [];

        foreach ($permissions as $permission) {
            $results[$permission] = $user->hasPermissionTo($permission);
        }

        return response()->json($results);
    }

    /**
     * Get all roles and permissions available in system
     */
    public function getAllRolesAndPermissions()
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Create new role example
     */
    public function createRole(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        // Assign permissions if provided
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->load('permissions'),
        ], 201);
    }

    /**
     * Create new permission example
     */
    public function createPermission(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission,
        ], 201);
    }

    /**
     * Remove role from user example
     */
    public function removeRole(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('manage_roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'role' => 'required|string',
        ]);

        $user->removeRole($validated['role']);

        return response()->json([
            'message' => 'Role removed successfully',
            'user' => $user->load('roles')
        ]);
    }

    /**
     * Revoke permission from user example
     */
    public function revokePermission(Request $request, User $user)
    {
        if (!auth()->user()->hasPermissionTo('manage_permissions')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'permission' => 'required|string',
        ]);

        $user->revokePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permission revoked successfully',
            'user' => $user->load('permissions')
        ]);
    }
}
