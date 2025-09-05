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
        Schema::create('t_purchase_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('transaction_code');
            $table->unsignedBigInteger('f_purchase_order_id')->nullable();
            $table->date('delivery_date');
            $table->unsignedBigInteger('f_supplier_id');
            $table->string('supplier_email')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_reference_no')->nullable();
            // $table->unsignedBigInteger('f_payment_term_id')->nullable();
            $table->unsignedBigInteger('f_warehouse_id')->nullable();
            $table->date('expected_received_date')->nullable();
            $table->string('description')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->string('internal_memo')->nullable();
            $table->boolean('allow_over_delivery')->default(false);
            $table->unsignedInteger('total_item')->default(0);

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('company_code')->references('code')->on('m_companies');
            $table->foreign('f_supplier_id')->references('id')->on('m_contacts');
            //$table->foreign('f_payment_term_id')->references('id')->on('m_payment_terms');
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
            $table->foreign('f_purchase_order_id')->references('id')->on('t_purchase_orders');
        });

        Schema::create('t_purchase_delivery_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_purchase_delivery_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('qty', 15, 2)->default(0);
            $table->unsignedBigInteger('f_unit_id')->nullable();
            // $table->decimal('price_per_qty', 15, 2)->default(0);
            // $table->unsignedBigInteger('f_tax_id')->nullable();
            $table->string('note')->nullable();
            $table->string('status')->nullable();

            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_purchase_delivery_id')->references('id')->on('t_purchase_deliveries');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('f_unit_id')->references('id')->on('m_units');
            // $table->foreign('f_tax_id')->references('id')->on('m_taxes');
        });

         Schema::create('t_purchase_delivery_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_purchase_delivery_id');
            $table->string('company_code');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('roles');
            $table->foreign('updated_by')->references('id')->on('roles');
            $table->foreign('f_purchase_delivery_id')->references('id')->on('t_purchase_deliveries');
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
