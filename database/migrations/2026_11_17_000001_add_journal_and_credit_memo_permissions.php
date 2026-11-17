<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private array $journal = ['journal.view', 'journal.create', 'journal.edit'];

    private array $creditMemo = ['credit_memo.view', 'credit_memo.create', 'credit_memo.edit', 'credit_memo.delete'];

    public function up(): void
    {
        foreach ([...$this->journal, ...$this->creditMemo] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Role 1 is the full-access role on every server (Owner on production, super_admin on staging).
        Role::find(1)?->givePermissionTo([...$this->journal, ...$this->creditMemo]);

        app()['cache']->forget('spatie.permission.cache');
    }

    public function down(): void
    {
        // credit_memo.* predates this migration on staging, so only journal.* is removed.
        Permission::whereIn('name', $this->journal)->where('guard_name', 'web')->get()
            ->each(function (Permission $permission) {
                $permission->roles()->detach();
                $permission->users()->detach();
                $permission->delete();
            });

        app()['cache']->forget('spatie.permission.cache');
    }
};
