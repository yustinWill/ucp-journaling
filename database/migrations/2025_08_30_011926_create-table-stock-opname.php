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

        Schema::create('t_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('stock_opname_no')->unique();
            $table->date('transaction_date');
            $table->unsignedBigInteger('f_warehouse_id');
            $table->unsignedBigInteger('f_pic_id');
            $table->string('status');
            $table->string('description')->nullable();
            $table->string('memo')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('company_code')->references('code')->on('m_companies');
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
            $table->foreign('f_pic_id')->references('id')->on('users');
        });

        Schema::create('t_stock_opname_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_stock_opname_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('system_stock', 15, 2);
            $table->decimal('physical_qty', 15, 2);
            $table->decimal('variance', 15, 2);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_stock_opname_id')->references('id')->on('t_stock_opnames');
            $table->foreign('f_product_id')->references('id')->on('m_products');
        });

        Schema::create('t_stock_opname_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_stock_opname_id');
            $table->string('company_code');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_stock_opname_id')->references('id')->on('t_stock_opnames');
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
