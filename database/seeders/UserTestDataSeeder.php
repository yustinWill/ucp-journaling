<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test user data with different roles
        $testUsers = [
            [
                'name' => 'Joes Admin',
                'email' => 'joes.admin@riyo.test',
                'phone' => '081234567801',
                'password' => 'Password1*',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Budi Manager',
                'email' => 'budi.manager@riyo.test',
                'phone' => '081234567802',
                'password' => 'Password1*',
                'role' => 'admin',
            ],
            [
                'name' => 'Siti Staff',
                'email' => 'siti.staff@riyo.test',
                'phone' => '081234567803',
                'password' => 'Password1*',
                'role' => 'manager',
            ],
            [
                'name' => 'Ahmad Warehouse',
                'email' => 'ahmad.warehouse@riyo.test',
                'phone' => '081234567804',
                'password' => 'Password1*',
                'role' => 'staff',
            ],
            [
                'name' => 'Diana Sales',
                'email' => 'diana.sales@riyo.test',
                'phone' => '081234567805',
                'password' => 'Password1*',
                'role' => 'viewer',
            ],
            [
                'name' => 'Roni Viewer',
                'email' => 'roni.viewer@riyo.test',
                'phone' => '081234567806',
                'password' => 'Password1*',
                'role' => 'user',
            ],
        ];

        // Create users and assign Spatie roles
        foreach ($testUsers as $userData) {
            // Check if user already exists
            if (User::where('email', $userData['email'])->exists()) {
                $this->command->info("  ⊘ User {$userData['email']} already exists");
                continue;
            }

            try {
                // Get role from Spatie
                $role = Role::where('name', $userData['role'])->first();
                if (!$role) {
                    $this->command->warn("  ✗ Role '{$userData['role']}' not found, skipping {$userData['email']}");
                    continue;
                }

                // Create user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'phone' => $userData['phone'],
                    'password' => Hash::make($userData['password']),
                ]);

                // Assign Spatie role
                $user->assignRole($role->name);

                $this->command->info("  ✓ {$userData['name']} ({$userData['role']})");
            } catch (\Exception $e) {
                $this->command->error("  ✗ Failed to create {$userData['email']}: {$e->getMessage()}");
            }
        }

        $this->command->info("\n✅ Test users seeders completed!");
    }
}
