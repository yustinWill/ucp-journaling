<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed role management first (roles, permissions, modules)
        $this->call([
            RoleManagementSeeder::class,
        ]);

        // Create default super admin user
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();

        User::create([
            'name' => 'Joes',
            'email' => 'joes1@joes.com',
            'password' => Hash::make('Password1*'),
            'role_id' => $superAdminRole ? $superAdminRole->id : 1,
        ]);

        // Create default company
        $companyCode = 'RIYO001';
        DB::table('m_companies')->insert([
            'code' => $companyCode,
            'name' => 'RIYO Company',
            'description' => 'Default Company',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign user to company
        $user = User::where('email', 'joes1@joes.com')->first();
        if ($user) {
            DB::table('m_user_companies')->insert([
                'f_user_id' => $user->id,
                'f_company_code' => $companyCode,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed other data
        $this->call([
            CategorySeeder::class,
            PaymentTermSeeder::class,
            TransactionNumberSeeder::class,
            RevenueSplitSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: joes1@joes.com');
        $this->command->info('Password: Password1*');
    }
}
