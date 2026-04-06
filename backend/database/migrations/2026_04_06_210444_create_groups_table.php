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
            $table->foreignUuid('parent_id')->nullable()->constrained('groups')->onDelete('cascade');
            
            // --- Type of the group e.g. INDIVIDUAL, TEAM, ORGANIZATION ---
            $table->string('type');

            // --- Billing email for possible invoices ---
            $table->string('billing_email')->nullable();

            // --- Miscellaneous ---
            $table->text('icon_path');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // Group Roles table
        Schema::create('group_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->string('name', 64);
            $table->jsonb('permissions');
            $table->timestampsTz();
        });

        // Join table for group members
        Schema::create('group_members', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // If either the group or user is deleted, cascade deletion
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained('group_roles');
            $table->timestampsTz();

            $table->unique(['user_id', 'group_id']);
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
