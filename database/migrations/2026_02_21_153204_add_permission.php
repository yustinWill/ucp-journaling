<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up()
    {
        $permissions = [
            'reimbursement.view' => 'Can view reimbursements',
            'reimbursement.create' => 'Can create reimbursements',
            'reimbursement_approval.view' => 'Can view reimbursements',
            'reimbursement_approval.create' => 'Can create reimbursements',
        ];

        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web', 'description' => $description]
            );
        }
    }

    public function down()
    {
        // Optional: clean up
    }
};