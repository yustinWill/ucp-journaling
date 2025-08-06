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
        Schema::create('m_contact_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_contact_id');
            $table->string('type')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('subdistrict')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_contact_id')->references('id')->on('m_contacts');
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
