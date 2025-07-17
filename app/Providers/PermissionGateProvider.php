<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class PermissionGateProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPermissionGates();
    }

    /**
     * Register dynamic permission gates
     */
    private function registerPermissionGates(): void
    {
        // Define permission gates dynamically
        Gate::define('user.read', function ($user) {
            return $this->hasPermission($user, 'user', 'read');
        });

        Gate::define('user.write', function ($user) {
            return $this->hasPermission($user, 'user', 'write');
        });

        Gate::define('user.delete', function ($user) {
            return $this->hasPermission($user, 'user', 'delete');
        });

        Gate::define('user.approval', function ($user) {
            return $this->hasPermission($user, 'user', 'approval');
        });

        Gate::define('role.read', function ($user) {
            return $this->hasPermission($user, 'role', 'read');
        });

        Gate::define('role.write', function ($user) {
            return $this->hasPermission($user, 'role', 'write');
        });

        Gate::define('role.delete', function ($user) {
            return $this->hasPermission($user, 'role', 'delete');
        });

        Gate::define('role.approval', function ($user) {
            return $this->hasPermission($user, 'role', 'approval');
        });

        Gate::define('contact.read', function ($user) {
            return $this->hasPermission($user, 'contact', 'read');
        });

        Gate::define('contact.write', function ($user) {
            return $this->hasPermission($user, 'contact', 'write');
        });

        Gate::define('contact.delete', function ($user) {
            return $this->hasPermission($user, 'contact', 'delete');
        });

        Gate::define('contact.approval', function ($user) {
            return $this->hasPermission($user, 'contact', 'approval');
        });

        Gate::define('product.read', function ($user) {
            return $this->hasPermission($user, 'product', 'read');
        });

        Gate::define('product.write', function ($user) {
            return $this->hasPermission($user, 'product', 'write');
        });

        Gate::define('product.delete', function ($user) {
            return $this->hasPermission($user, 'product', 'delete');
        });

        Gate::define('product.approval', function ($user) {
            return $this->hasPermission($user, 'product', 'approval');
        });

        Gate::define('sales.read', function ($user) {
            return $this->hasPermission($user, 'sales', 'read');
        });

        Gate::define('sales.write', function ($user) {
            return $this->hasPermission($user, 'sales', 'write');
        });

        Gate::define('sales.delete', function ($user) {
            return $this->hasPermission($user, 'sales', 'delete');
        });

        Gate::define('sales.approval', function ($user) {
            return $this->hasPermission($user, 'sales', 'approval');
        });

        Gate::define('purchase.read', function ($user) {
            return $this->hasPermission($user, 'purchase', 'read');
        });

        Gate::define('purchase.write', function ($user) {
            return $this->hasPermission($user, 'purchase', 'write');
        });

        Gate::define('purchase.delete', function ($user) {
            return $this->hasPermission($user, 'purchase', 'delete');
        });

        Gate::define('purchase.approval', function ($user) {
            return $this->hasPermission($user, 'purchase', 'approval');
        });

        Gate::define('journal.read', function ($user) {
            return $this->hasPermission($user, 'journal', 'read');
        });

        Gate::define('journal.write', function ($user) {
            return $this->hasPermission($user, 'journal', 'write');
        });

        Gate::define('journal.delete', function ($user) {
            return $this->hasPermission($user, 'journal', 'delete');
        });

        Gate::define('journal.approval', function ($user) {
            return $this->hasPermission($user, 'journal', 'approval');
        });

        Gate::define('expense.read', function ($user) {
            return $this->hasPermission($user, 'expense', 'read');
        });

        Gate::define('expense.write', function ($user) {
            return $this->hasPermission($user, 'expense', 'write');
        });

        Gate::define('expense.delete', function ($user) {
            return $this->hasPermission($user, 'expense', 'delete');
        });

        Gate::define('expense.approval', function ($user) {
            return $this->hasPermission($user, 'expense', 'approval');
        });

        Gate::define('coa.read', function ($user) {
            return $this->hasPermission($user, 'coa', 'read');
        });

        Gate::define('coa.write', function ($user) {
            return $this->hasPermission($user, 'coa', 'write');
        });

        Gate::define('coa.delete', function ($user) {
            return $this->hasPermission($user, 'coa', 'delete');
        });

        Gate::define('coa.approval', function ($user) {
            return $this->hasPermission($user, 'coa', 'approval');
        });
    }

    /**
     * Check if user has specific permission
     */
    private function hasPermission($user, string $moduleSlug, string $permissionType): bool
    {
        // Skip permission check if user doesn't have role_id
        if (!isset($user->role_id)) {
            return true; // Backward compatibility
        }

        $permission = DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->join('modules', 'permissions.module_id', '=', 'modules.id')
            ->where('role_permissions.role_id', $user->role_id)
            ->where('modules.slug', $moduleSlug)
            ->where('permissions.permission_type', $permissionType)
            ->exists();

        return $permission;
    }
}