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
        // Clean up old columns that are no longer needed
        Schema::table('t_sales_orders', function (Blueprint $table) {
            // Drop old foreign keys first
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['sales_person_id']);
            
            // Drop old columns
            $table->dropColumn([
                'order_number',
                'customer_id', 
                'sales_person_id',
                'order_date',
                'payment_terms',
                'subtotal',
                'tax_total',
                'discount_amount',
                'total_amount',
                'tags',
                'attachment_path'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns
        Schema::table('t_sales_orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->after('id');
            $table->unsignedBigInteger('customer_id')->after('order_number');
            $table->unsignedBigInteger('sales_person_id')->nullable()->after('customer_id');
            $table->date('order_date')->after('sales_person_id');
            $table->string('payment_terms')->nullable()->after('order_date');
            $table->decimal('subtotal', 15, 2)->default(0)->after('payment_terms');
            $table->decimal('tax_total', 15, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('tax_total');
            $table->decimal('total_amount', 15, 2)->default(0)->after('discount_amount');
            $table->text('tags')->nullable()->after('total_amount');
            $table->string('attachment_path')->nullable()->after('tags');
            
            // Restore foreign keys
            $table->foreign('customer_id')->references('id')->on('m_contacts');
            $table->foreign('sales_person_id')->references('id')->on('m_sales_persons');
        });
    }
};
