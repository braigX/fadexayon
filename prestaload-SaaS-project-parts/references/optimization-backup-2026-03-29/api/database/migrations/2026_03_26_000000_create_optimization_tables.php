<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('optimization_targets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('prestashop_store_id')->constrained('prestashop_stores')->cascadeOnDelete();
            $table->foreignUuid('prestashop_shop_id')->constrained('prestashop_shops')->cascadeOnDelete();
            $table->foreignUuid('prestashop_shop_url_id')->constrained('prestashop_shop_urls')->cascadeOnDelete();
            $table->string('page_type', 50)->nullable();
            $table->string('normalized_url', 2048);
            $table->string('device_class', 20)->default('desktop');
            $table->string('status', 30)->default('pending');
            $table->uuid('current_optimization_run_id')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->unique(['prestashop_shop_url_id', 'device_class'], 'optimization_targets_url_device_unique');
            $table->index(['prestashop_shop_id', 'status']);
        });

        Schema::create('optimization_runs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('optimization_target_id')->constrained('optimization_targets')->cascadeOnDelete();
            $table->unsignedInteger('run_number')->default(1);
            $table->string('trigger_type', 30)->default('manual');
            $table->string('status', 30)->default('queued');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->unique(['optimization_target_id', 'run_number'], 'optimization_runs_target_run_unique');
            $table->index(['status', 'created_at']);
        });

        Schema::table('optimization_targets', function (Blueprint $table): void {
            $table->foreign('current_optimization_run_id', 'optimization_targets_current_run_foreign')
                ->references('id')
                ->on('optimization_runs')
                ->nullOnDelete();
        });

        Schema::create('optimization_run_steps', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('optimization_run_id')->constrained('optimization_runs')->cascadeOnDelete();
            $table->string('step_name', 50);
            $table->string('worker_type', 50);
            $table->string('status', 30)->default('queued');
            $table->string('queue_name', 100)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('input_summary_json')->nullable();
            $table->json('output_summary_json')->nullable();
            $table->text('error_summary')->nullable();
            $table->timestamps();

            $table->index(['optimization_run_id', 'step_name']);
            $table->index(['status', 'queue_name']);
        });

        Schema::create('optimization_artifact_versions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('optimization_target_id')->constrained('optimization_targets')->cascadeOnDelete();
            $table->foreignUuid('optimization_run_id')->constrained('optimization_runs')->cascadeOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->string('status', 30)->default('draft');
            $table->string('storage_prefix', 1024)->nullable();
            $table->string('raw_html_path', 1024)->nullable();
            $table->string('optimized_html_path', 1024)->nullable();
            $table->json('meta_json')->nullable();
            $table->timestamps();

            $table->unique(['optimization_target_id', 'version_number'], 'optimization_artifacts_target_version_unique');
            $table->index(['optimization_run_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('optimization_artifact_versions');
        Schema::dropIfExists('optimization_run_steps');
        Schema::dropIfExists('optimization_runs');
        Schema::dropIfExists('optimization_targets');
    }
};
