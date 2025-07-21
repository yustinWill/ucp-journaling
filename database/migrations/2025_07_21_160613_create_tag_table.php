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
        Schema::create('m_tag_colors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('preview');
            $table->timestamps();
        });

        Schema::create('m_tags', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedBigInteger('f_tag_color_id');
            $table->foreign('f_tag_color_id')->references('id')->on('m_tag_colors')->onDelete('cascade');
            $table->string('status');
            $table->integer('counter')->default(0);
            $table->string('description')->nullable();
            $table->string('memo')->nullable();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('m_tag_colors');
        Schema::dropIfExists('m_tags');
    }
};
