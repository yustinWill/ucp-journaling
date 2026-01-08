<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_inventory_assets', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('code')->unique(); // Asset code: A-SRV-001, INV-RAW-001
            $table->string('name'); // Asset/Inventory name
            $table->text('description')->nullable();
            $table->enum('type', ['FIXED_ASSET', 'INVENTORY']); // Type of item
            $table->string('category'); // IT, Kendaraan, Kantor, Raw Material, Finished Goods
            $table->string('location')->nullable(); // Physical location
            $table->unsignedBigInteger('f_warehouse_id')->nullable(); // For inventory items

            // Acquisition details
            $table->date('acquisition_date')->nullable();
            $table->enum('acquisition_method', ['PURCHASE', 'LEASE', 'GRANT', 'TRANSFER'])->nullable();
            $table->decimal('acquisition_value', 15, 2)->default(0); // Purchase price
            $table->string('vendor_name')->nullable();
            $table->unsignedBigInteger('f_vendor_contact_id')->nullable();

            // Depreciation (for fixed assets)
            $table->enum('depreciation_method', ['STRAIGHT_LINE', 'REDUCING_BALANCE', 'UNITS_OF_PRODUCTION', 'NONE'])->default('NONE');
            $table->decimal('depreciation_rate', 5, 2)->nullable(); // Percentage
            $table->integer('useful_life_years')->nullable();
            $table->decimal('salvage_value', 15, 2)->default(0);
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->decimal('net_book_value', 15, 2)->default(0);

            // Stock/Quantity (for inventory)
            $table->decimal('quantity', 10, 4)->default(0);
            $table->unsignedBigInteger('f_unit_id')->nullable();
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->decimal('reorder_point', 10, 4)->nullable();

            // Status
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'DISPOSED', 'MAINTENANCE'])->default('ACTIVE');
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_value', 15, 2)->nullable();
            $table->text('disposal_notes')->nullable();

            // Accounting
            $table->string('f_asset_coa_code')->nullable(); // Chart of Account
            $table->string('f_depreciation_expense_coa_code')->nullable();
            $table->string('f_accumulated_depreciation_coa_code')->nullable();

            // Additional info
            $table->json('f_tag_codes')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
            $table->foreign('f_vendor_contact_id')->references('id')->on('m_contacts');
            $table->foreign('f_unit_id')->references('id')->on('m_units');
            $table->foreign('f_asset_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_depreciation_expense_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_accumulated_depreciation_coa_code')->references('code')->on('m_coas');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('company_code')->references('code')->on('m_companies');

            // Indexes
            $table->index('company_code');
            $table->index('type');
            $table->index('category');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_inventory_assets');
    }
};
