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
        Schema::create('m_payment_terms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Unique payment term code (e.g., NET_30)');
            $table->string('name', 100)->comment('Payment term name (e.g., Net 30)');
            $table->text('description')->nullable()->comment('Detailed description of payment terms');
            $table->integer('days')->default(0)->comment('Number of days for payment (0 for immediate)');
            $table->enum('type', ['net', 'immediate', 'cod', 'custom'])->default('net')->comment('Type of payment term');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Status of payment term');
            $table->boolean('is_default')->default(false)->comment('Is this the default payment term');
            $table->timestamps();

            // Indexes
            $table->index(['status', 'code']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_payment_terms');
    }
};
