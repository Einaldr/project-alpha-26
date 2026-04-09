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

        // Main group table
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');

            // --- ID of a parent id ---
            $table->foreignUuid('parent_id')->nullable();
            
            // --- Type of the group e.g. INDIVIDUAL, TEAM, ORGANIZATION ---
            $table->string('type');

            $table->foreignUuid('owner_id')->constrained('users')->onDelete('cascade');
            // --- Billing email for possible invoices ---
            $table->string('billing_email')->nullable();

            // --- Miscellaneous ---
            $table->text('icon_path');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Create a foreign key for parent_id field
        Schema::table('groups', function (Blueprint $table) {
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('groups')
                  ->onDelete('cascade');
        });

        // DB Statement for partial unique indexing
        DB::statement("CREATE UNIQUE INDEX unique_individual_owner ON groups (owner_id) WHERE (type = 'individual')");

        // Group Roles table
        Schema::create('group_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->string('name', 64);
            $table->jsonb('permissions');
            $table->timestampsTz();

            $table->unique(['name', 'group_id']);
        });

        // Join table for group members
        Schema::create('group_members', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // If either the group or user is deleted, cascade deletion
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->timestampsTz();

            $table->unique(['user_id', 'group_id']);
        });

        // Pivot table for group members to have roles
        Schema::create('pivot_group_roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('group_member_id')->constrained('group_members')->onDelete('cascade');
            $table->foreignUuid('role_id')->constrained('group_roles')->onDelete('cascade');
            $table->timestampsTz();

            $table->unique(['group_member_id', 'role_id']);
        });

        // Audit logs table
        Schema::create('group_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // In case the group is deleted, cascade
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained();

            // --- Content ---
            $table->string('action');
            $table->nullableUuidMorphs('target'); // E.g. USER, GROUP
            $table->jsonb('payload')->nullable();

            // --- Timestamps ---
            $table->timestampsTZ();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('group_roles');
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('group_audit_logs');
    }
};
