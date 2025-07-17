<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleSlug, string $permissionType): Response
    {
        // Skip permission check if no user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Skip permission check if user doesn't have role_id (backward compatibility)
        if (!isset($user->role_id)) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$this->hasPermission($user->role_id, $moduleSlug, $permissionType)) {
            abort(403, 'Insufficient permissions to access this resource.');
        }

        return $next($request);
    }

    /**
     * Check if user role has specific permission
     */
    private function hasPermission(int $roleId, string $moduleSlug, string $permissionType): bool
    {
        $permission = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->join('modules', 'permissions.module_id', '=', 'modules.id')
            ->where('role_permissions.role_id', $roleId)
            ->where('modules.slug', $moduleSlug)
            ->where('permissions.permission_type', $permissionType)
            ->exists();

        return $permission;
    }
}