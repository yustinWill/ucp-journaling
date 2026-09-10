<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permission = Permission::firstOrCreate([
            'name' => 'sales_order_revert.view',
            'guard_name' => 'web',
        ]);

        $attributes = [];
        if (Schema::hasColumn('permissions', 'display_name')) {
            $attributes['display_name'] = 'View Sales Order Revert';
        }
        if (Schema::hasColumn('permissions', 'group')) {
            $attributes['group'] = 'sales';
        }
        if ($attributes !== []) {
            DB::table('permissions')->where('id', $permission->id)->update($attributes);
        }

        Role::where('name', 'super_admin')
            ->where('guard_name', 'web')
            ->first()?->givePermissionTo($permission);

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'sales_order_revert.view')
            ->where('guard_name', 'web')
            ->first();

        if ($permission) {
            $permission->roles()->detach();
            $permission->users()->detach();
            $permission->delete();
        }

        app()['cache']->forget('spatie.permission.cache');
    }
};
