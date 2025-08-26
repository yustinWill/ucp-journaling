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
        Schema::create('t_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('transaction_code');
            $table->date('transaction_date');
            $table->unsignedBigInteger('f_supplier_id');
            $table->string('supplier_email')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_reference_no')->nullable();
            $table->unsignedBigInteger('f_payment_term_id')->nullable();
            $table->unsignedBigInteger('f_warehouse_id')->nullable();
            $table->string('description')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->string('internal_memo')->nullable();
            $table->boolean('is_price_include_tax')->default(false);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_price_before_discount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grand_price', 15, 2)->default(0);

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('company_code')->references('code')->on('m_companies');
            $table->foreign('f_supplier_id')->references('id')->on('m_contacts');
            $table->foreign('f_payment_term_id')->references('id')->on('m_payment_terms');
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
        });

        Schema::create('t_purchase_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_purchase_order_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('qty', 15, 2)->default(0);
            $table->unsignedBigInteger('f_unit_id')->nullable();
            $table->decimal('price_per_qty', 15, 2)->default(0);
            $table->unsignedBigInteger('f_tax_id')->nullable();
            $table->decimal('total', 15, 2)->default(0);

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_purchase_order_id')->references('id')->on('t_purchase_orders');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('f_unit_id')->references('id')->on('m_units');
            $table->foreign('f_tax_id')->references('id')->on('m_taxes');
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
