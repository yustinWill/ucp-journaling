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
        // Update sales order table to match standard pattern used in purchase orders
        Schema::table('t_sales_orders', function (Blueprint $table) {
            // Add missing columns following purchase order pattern
            $table->string('company_code')->after('id');
            $table->string('transaction_code')->unique()->after('company_code');
            $table->date('transaction_date')->after('transaction_code');
            $table->unsignedBigInteger('f_customer_id')->after('transaction_date');
            $table->unsignedBigInteger('f_payment_term_id')->nullable()->after('f_customer_id');
            $table->unsignedBigInteger('f_warehouse_id')->nullable()->after('f_payment_term_id');
            $table->string('customer_email')->nullable()->after('f_warehouse_id');
            $table->text('customer_address')->nullable()->after('customer_email');
            $table->string('customer_reference_no')->nullable()->after('customer_address');
            $table->text('description')->nullable()->after('customer_reference_no');
            $table->date('payment_term_due_date')->nullable()->after('description');
            $table->integer('payment_term_day_count')->nullable()->after('payment_term_due_date');
            $table->decimal('grand_price', 15, 2)->default(0)->after('total_amount');
            $table->decimal('total_price_before_discount', 15, 2)->default(0)->after('grand_price');
            $table->decimal('total_tax', 15, 2)->default(0)->after('total_price_before_discount');
            $table->decimal('discount', 15, 2)->default(0)->after('total_tax');
            $table->text('internal_memo')->nullable()->after('memo');
            $table->json('f_tag_codes')->nullable()->after('internal_memo');
            $table->string('attachment')->nullable()->after('f_tag_codes');
            $table->string('status')->default('DRAFT')->change();
            
            // Add foreign keys
            $table->foreign('f_customer_id')->references('id')->on('m_contacts');
            $table->foreign('f_payment_term_id')->references('id')->on('m_payment_terms');
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
        });
        
        // Update sales order products table to match standard pattern
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->string('company_code')->after('id');
            $table->renameColumn('sales_order_id', 'f_sales_order_id');
            $table->renameColumn('product_id', 'f_product_id');
            $table->renameColumn('quantity_ordered', 'quantity');
            $table->renameColumn('tax_id', 'f_tax_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert sales order products table
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->dropColumn('company_code');
            $table->renameColumn('f_sales_order_id', 'sales_order_id');
            $table->renameColumn('f_product_id', 'product_id');
            $table->renameColumn('quantity', 'quantity_ordered');
            $table->renameColumn('f_tax_id', 'tax_id');
        });
        
        // Revert sales order table
        Schema::table('t_sales_orders', function (Blueprint $table) {
            $table->dropForeign(['f_customer_id']);
            $table->dropForeign(['f_payment_term_id']);
            $table->dropForeign(['f_warehouse_id']);
            
            $table->dropColumn([
                'company_code',
                'transaction_code',
                'transaction_date',
                'f_customer_id',
                'f_payment_term_id',
                'f_warehouse_id',
                'customer_email',
                'customer_address',
                'customer_reference_no',
                'description',
                'payment_term_due_date',
                'payment_term_day_count',
                'grand_price',
                'total_price_before_discount',
                'total_tax',
                'discount',
                'internal_memo',
                'f_tag_codes',
                'attachment'
            ]);
            
            $table->string('status')->default('PENDING')->change();
        });
    }
};
