<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RevenueSplitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, check if the module already exists, if not create it
        $existingModule = DB::table('modules')->where('slug', 'master-data-revenue-split')->first();
        if ($existingModule) {
            $moduleId = $existingModule->id;
        } else {
            $moduleId = DB::table('modules')->insertGetId([
                'name' => 'master_data_revenue_split',
                'slug' => 'master-data-revenue-split',
                'description' => 'Master Data Revenue Split',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create permissions for the Revenue Split module
        $permissions = ['read', 'write', 'delete', 'approval'];
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            $existingPermission = DB::table('permissions')
                ->where('module_id', $moduleId)
                ->where('permission_type', $permission)
                ->first();
                
            if ($existingPermission) {
                $permissionIds[] = $existingPermission->id;
            } else {
                $permissionId = DB::table('permissions')->insertGetId([
                    'module_id' => $moduleId,
                    'permission_type' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $permissionIds[] = $permissionId;
            }
        }

        // Assign all Revenue Split permissions to Super Admin role
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            foreach ($permissionIds as $permissionId) {
                $existingRolePermission = DB::table('role_permissions')
                    ->where('role_id', $superAdminRole->id)
                    ->where('permission_id', $permissionId)
                    ->first();

                if (!$existingRolePermission) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $superAdminRole->id,
                        'permission_id' => $permissionId,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Create sample Revenue Split data
        $sampleRevenueSplits = [
            [
                'cycle_name' => 'Cycle 1 - Q1 2024',
                'description' => 'Kuartal Pertama 2024',
                'start_date' => '2024-01-01',
                'end_date' => '2024-03-31',
                'target_pabrik' => 850000000,
                'target_owner' => 600000000,
                'target_perusahaan' => 750000000,
                'total_target' => 2200000000,
                'status' => 'SELESAI',
                'notes' => 'Cycle pertama 2024 dengan target pencapaian tinggi',
                'company_code' => '001',
                'created_at' => '2024-06-30 23:59:59',
                'updated_at' => '2024-06-30 23:59:59',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'cycle_name' => 'Cycle 2 - Q2 2024',
                'description' => 'Kuartal Kedua 2024',
                'start_date' => '2024-04-01',
                'end_date' => '2024-06-30',
                'target_pabrik' => 600000000,
                'target_owner' => 420000000,
                'target_perusahaan' => 180000000,
                'total_target' => 1200000000,
                'status' => 'SELESAI',
                'notes' => 'Cycle kedua dengan fokus optimasi operasional',
                'company_code' => '001',
                'created_at' => '2024-06-30 23:59:59',
                'updated_at' => '2024-06-30 23:59:59',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'cycle_name' => 'Cycle 3 - Q3 2024',
                'description' => 'Kuartal Ketiga 2024',
                'start_date' => '2024-07-01',
                'end_date' => '2024-09-30',
                'target_pabrik' => 950000000,
                'target_owner' => 665000000,
                'target_perusahaan' => 285000000,
                'total_target' => 1900000000,
                'status' => 'SELESAI',
                'notes' => 'Cycle ketiga dengan target pertumbuhan berkelanjutan',
                'company_code' => '001',
                'created_at' => '2024-06-30 23:59:59',
                'updated_at' => '2024-06-30 23:59:59',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'cycle_name' => 'Cycle 4 - Q4 2024',
                'description' => 'Kuartal Keempat 2024',
                'start_date' => '2024-10-01',
                'end_date' => '2024-12-31',
                'target_pabrik' => 1400000000,
                'target_owner' => 980000000,
                'target_perusahaan' => 420000000,
                'total_target' => 2800000000,
                'status' => 'SELESAI',
                'notes' => 'Cycle keempat target akhir tahun maksimal',
                'company_code' => '001',
                'created_at' => '2024-06-30 23:59:59',
                'updated_at' => '2024-06-30 23:59:59',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'cycle_name' => 'Cycle 5 - Q1 2025',
                'description' => 'Kuartal Pertama Tahun Baru',
                'start_date' => '2025-01-01',
                'end_date' => '2025-03-31',
                'target_pabrik' => 1550000000,
                'target_owner' => 1085000000,
                'target_perusahaan' => 465000000,
                'total_target' => 3100000000,
                'status' => 'ACTIVE',
                'notes' => 'Cycle pertama 2025 dengan ekspektasi tinggi',
                'company_code' => '001',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        // Insert sample data only if no data exists
        $existingData = DB::table('m_revenue_splits')->where('company_code', '001')->count();
        if ($existingData == 0) {
            foreach ($sampleRevenueSplits as $data) {
                $revenueSplitId = DB::table('m_revenue_splits')->insertGetId($data);
                
                // Create audit log for each created record
                DB::table('m_revenue_split_audit_logs')->insert([
                    'f_revenue_split_id' => $revenueSplitId,
                    'action_type' => 'CREATE',
                    'description' => 'Pembuatan Cycle Keuntungan Penjualan',
                    'metadata' => json_encode([
                        'before' => null,
                        'after' => $data,
                    ]),
                    'created_at' => $data['created_at'],
                    'created_by' => $data['created_by'],
                ]);
            }
        }
    }
}