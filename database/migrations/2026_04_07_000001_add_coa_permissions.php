<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up()
    {
        $permissions = [
            'coa.view',
            'coa.create',
            'coa.edit',
            'coa.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
        }

        // Clear permission cache
        app()['cache']->forget('spatie.permission.cache');
    }

    public function down()
    {
        app()['cache']->forget('spatie.permission.cache');
    }
};
