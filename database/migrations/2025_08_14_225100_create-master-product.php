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
        Schema::create('m_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('f_unit_id');
            $table->unsignedBigInteger('f_category_id')->nullable();
            $table->string('main_image')->nullable();
            $table->string('type');

            $table->boolean('is_item_purchased');
            $table->decimal('unit_purchase_price', 15, 2)->nullable();
            $table->string('f_purchase_coa_code')->nullable();
            $table->unsignedBigInteger('f_purchase_tax_id')->nullable();

            $table->boolean('is_item_sold');
            $table->decimal('unit_sell_price', 15, 2)->nullable();
            $table->string('f_sell_coa_code')->nullable();
            $table->unsignedBigInteger('f_sell_tax_id')->nullable();

            $table->boolean('monitor_stock');
            $table->decimal('minimum_stock', 15, 2)->nullable();
            $table->string('f_inventory_coa_code')->nullable();

            $table->boolean('track_qty_only');
            $table->boolean('track_batch');

            $table->json('f_tag_codes')->nullable();
            $table->string('internal_memo')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('modified_by')->references('id')->on('users');

            $table->foreign('f_unit_id')->references('id')->on('m_units');
            $table->foreign('f_category_id')->references('id')->on('m_categories');
            $table->foreign('f_purchase_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_sell_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_inventory_coa_code')->references('code')->on('m_coas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
