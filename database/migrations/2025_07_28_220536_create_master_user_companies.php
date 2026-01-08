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
        Schema::create('m_user_companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_user_id');
            $table->string('f_company_code');
            $table->string('status');
            $table->boolean('default_company')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_user_id')->references('id')->on('users');
            $table->foreign('f_company_code')->references('code')->on('m_companies');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['f_user_id']);
            $table->index(['f_company_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_user_companies');
    }
};
