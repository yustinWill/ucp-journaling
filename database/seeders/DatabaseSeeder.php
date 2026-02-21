<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default company first
        $companyCode = 'RIYO001';
        $company = \Illuminate\Support\Facades\DB::table('m_companies')
            ->where('code', $companyCode)
            ->first();
        
        if (!$company) {
            \Illuminate\Support\Facades\DB::table('m_companies')->insert([
                'code' => $companyCode,
                'name' => 'RIYO Company',
                'description' => 'Default Company',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create default super admin user with Spatie role mapping
        if (!User::where('email', 'joes1@joes.com')->exists()) {
            $user = User::create([
                'name' => 'Joes',
                'email' => 'joes1@joes.com',
                'phone' => '6281000000001',
                'password' => Hash::make('Password1*'),
            ]);

            // Assign super_admin role via Spatie
            $superAdminRole = Role::where('name', 'super_admin')->first();
            if ($superAdminRole) {
                $user->assignRole($superAdminRole->name);
                $this->command->info('✓ Default super admin user created with Spatie role');
            }
        }

        // Seed other data
        $this->call([
            UserTestDataSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📝 Default Login Credentials:');
        $this->command->info('   Email: joes1@joes.com');
        $this->command->info('   Password: Password1*');
        $this->command->info('   Role: Super Admin');
        $this->command->info('');
        $this->command->info('👥 Test Users Created:');
        $this->command->info('   - joes.admin@riyo.test (Super Admin)');
        $this->command->info('   - budi.manager@riyo.test (Admin)');
        $this->command->info('   - siti.staff@riyo.test (Manager)');
        $this->command->info('   - ahmad.warehouse@riyo.test (Staff)');
        $this->command->info('   - diana.sales@riyo.test (Viewer)');
        $this->command->info('   - roni.viewer@riyo.test (User)');
        $this->command->info('   All test users password: Password1*');
    }
}
