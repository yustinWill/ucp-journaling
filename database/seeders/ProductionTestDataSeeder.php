<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $companyCode = 'RIYO';
        $userId = 1; // Admin user

        // Get first active warehouse
        $warehouse = DB::table('m_warehouses')->where('status', 'ACTIVE')->first();
        if (!$warehouse) {
            $this->command->error('No active warehouse found!');
            return;
        }

        // Get first bundle product with compositions
        $bundleProduct = DB::table('m_products as p')
            ->join('m_product_compositions as pc', 'p.id', '=', 'pc.f_product_id')
            ->where('p.status', 'ACTIVE')
            ->select('p.*')
            ->groupBy('p.id')
            ->first();

        if (!$bundleProduct) {
            $this->command->error('No bundle product with compositions found!');
            return;
        }

        // Get compositions for the bundle
        $compositions = DB::table('m_product_compositions as pc')
            ->join('m_products as p', 'pc.f_product_composition_product_id', '=', 'p.id')
            ->where('pc.f_product_id', $bundleProduct->id)
            ->select(
                'p.id as product_id',
                'p.name',
                'pc.qty',
                'pc.f_unit_id'
            )
            ->get();

        $this->command->info("Using bundle: {$bundleProduct->name}");
        $this->command->info("Components count: " . $compositions->count());

        // TEST 1: Quantity Planned Mode
        $this->command->info("\n=== Creating Quantity Planned Mode Production ===");

        $productionId1 = DB::table('t_productions')->insertGetId([
            'company_code' => $companyCode,
            'production_number' => 'PROD-TEST-QP-001',
            'production_date' => now()->format('Y-m-d'),
            'bundle_product_id' => $bundleProduct->id,
            'warehouse_id' => $warehouse->id,
            'pic_id' => $userId,
            'quantity_planned' => 10,
            'quantity_produced' => 10,
            'status' => 'COMPLETED',
            'production_mode' => 'QUANTITY_PLANNED',
            'leftover_materials' => null,
            'description' => 'Test production - Quantity Planned Mode',
            'notes' => 'Created by seeder for testing',
            'total_raw_material_cost' => 0,
            'total_production_expense' => 0,
            'total_production_cost' => 0,
            'total_items_consumed' => $compositions->count(),
            'created_by' => $userId,
            'created_at' => now(),
        ]);

        // Create production items for QP mode
        foreach ($compositions as $comp) {
            $unit = DB::table('m_units')->where('id', $comp->f_unit_id)->first();
            $quantityRequired = $comp->qty * 10; // 10 bundles

            DB::table('t_production_items')->insert([
                'production_id' => $productionId1,
                'raw_material_product_id' => $comp->product_id,
                'quantity_required' => $quantityRequired,
                'quantity_consumed' => $quantityRequired,
                'unit_code' => $unit->code ?? 'PCS',
                'unit_cost' => 1000,
                'total_cost' => $quantityRequired * 1000,
                'material_input' => null,
                'leftover_amount' => null,
                'batch_consumption' => json_encode([]),
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ Created production {$productionId1}: PROD-TEST-QP-001");

        // TEST 2: Material-Based Mode
        $this->command->info("\n=== Creating Material-Based Mode Production ===");

        $materialInputsData = [];
        $productionItems2 = [];

        // Simulate material inputs with leftovers
        $maxProduction = PHP_INT_MAX;
        foreach ($compositions as $index => $comp) {
            $unit = DB::table('m_units')->where('id', $comp->f_unit_id)->first();

            // Simulate different material inputs to create a bottleneck
            $materialInput = match($index) {
                0 => 20.5,      // Will produce 5 bundles if qty=4
                1 => 10.7,      // Will produce 10 bundles if qty=1
                default => 100  // Will produce more
            };

            $possibleProduction = floor($materialInput / $comp->qty);
            if ($possibleProduction < $maxProduction) {
                $maxProduction = $possibleProduction;
            }

            $materialInputsData[] = [
                'product_id' => $comp->product_id,
                'product_name' => $comp->name,
                'qty_per_bundle' => $comp->qty,
                'material_input' => $materialInput,
                'unit' => $unit->code ?? 'PCS'
            ];
        }

        // Calculate actual leftovers based on bottleneck
        foreach ($materialInputsData as &$data) {
            $usedMaterial = $maxProduction * $data['qty_per_bundle'];
            $data['leftover'] = round($data['material_input'] - $usedMaterial, 4);
        }

        $productionId2 = DB::table('t_productions')->insertGetId([
            'company_code' => $companyCode,
            'production_number' => 'PROD-TEST-MB-001',
            'production_date' => now()->format('Y-m-d'),
            'bundle_product_id' => $bundleProduct->id,
            'warehouse_id' => $warehouse->id,
            'pic_id' => $userId,
            'quantity_planned' => $maxProduction,
            'quantity_produced' => $maxProduction,
            'status' => 'COMPLETED',
            'production_mode' => 'MATERIAL_BASED',
            'leftover_materials' => json_encode($materialInputsData),
            'description' => 'Test production - Material-Based Mode',
            'notes' => 'Created by seeder - has leftover materials',
            'total_raw_material_cost' => 0,
            'total_production_expense' => 0,
            'total_production_cost' => 0,
            'total_items_consumed' => count($materialInputsData),
            'created_by' => $userId,
            'created_at' => now(),
        ]);

        // Create production items for MB mode
        foreach ($materialInputsData as $data) {
            $quantityConsumed = $maxProduction * $data['qty_per_bundle'];

            DB::table('t_production_items')->insert([
                'production_id' => $productionId2,
                'raw_material_product_id' => $data['product_id'],
                'quantity_required' => $quantityConsumed,
                'quantity_consumed' => $quantityConsumed,
                'unit_code' => $data['unit'],
                'unit_cost' => 1000,
                'total_cost' => $quantityConsumed * 1000,
                'material_input' => $data['material_input'],
                'leftover_amount' => $data['leftover'],
                'batch_consumption' => json_encode([]),
                'notes' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ Created production {$productionId2}: PROD-TEST-MB-001");
        $this->command->info("  Max production: {$maxProduction} units");
        $this->command->info("  Leftover materials tracked with 4 decimals\n");

        $this->command->info("=== Test Data Created Successfully ===");
        $this->command->info("View at: /main_transaction/production/index");
    }
}
