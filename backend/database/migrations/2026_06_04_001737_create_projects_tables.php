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
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('group_id')->constrained()->onDelete('cascade');

            $table->string('name', 64);
            $table->text('description')->nullable();

            $table->text('image_url');

            $table->text('git_url');
            $table->string('default_branch')->default('main');

            $table->timestampTz('last_pulled_at')->nullable();
            $table->timestampsTz();
            $table->softDeletes();

            $table->unique(['group_id', 'name']);
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->jsonb('permissions');
            $table->timestampsTz();

            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('project_secrets', function (Blueprint $table) {
            $table->foreignUuid('project_id')->primary()->constrained();
            $table->string('auth_type')->default('none');
            $table->text('access_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('project_secrets');
    }
};
