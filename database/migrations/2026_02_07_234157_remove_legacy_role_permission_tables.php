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
        // Drop foreign key and role_id column from users table
        if (Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            });
        }

        // Drop legacy role audit logs table first (depends on legacy roles)
        Schema::dropIfExists('m_role_audit_logs');

        // Drop role_permissions table (depends on roles and permissions)
        Schema::dropIfExists('role_permissions');

        // Drop legacy permissions table
        Schema::dropIfExists('permissions');

        // Drop modules table
        Schema::dropIfExists('modules');

        // Drop legacy roles table
        Schema::dropIfExists('roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate legacy roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate modules table
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate legacy permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->enum('permission_type', ['read', 'write', 'delete', 'approval']);
            $table->timestamps();

            $table->unique(['module_id', 'permission_type']);
        });

        // Recreate role_permissions table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        // Recreate role audit logs table
        Schema::create('m_role_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_role_id');
            $table->string('company_code');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_role_id')->references('id')->on('roles');
        });

        // Re-add role_id to users table
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
            });
        }
    }
};
