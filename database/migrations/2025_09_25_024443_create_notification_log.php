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
        Schema::create('t_notification', function (Blueprint $table) {
            $table->id();
            $table->enum('broadcast_type', ['PUBLIC', 'PRIVATE']);
            $table->string('notification_type');
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->string('icon_text');
            $table->string('title');
            $table->text('message');
            $table->json('metadata');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('target_user_id')->references('id')->on('users');
        });

        Schema::create('l_read_notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_notification_id');
            $table->unsignedBigInteger('f_read_by_user_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('f_notification_id')->references('id')->on('t_notification');
            $table->foreign('f_read_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_log');
    }
};
