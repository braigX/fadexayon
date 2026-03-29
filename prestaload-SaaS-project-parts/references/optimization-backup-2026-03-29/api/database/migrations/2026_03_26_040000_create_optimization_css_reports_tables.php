<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('optimization_css_reports', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('optimization_target_id')->constrained('optimization_targets')->cascadeOnDelete();
            $table->foreignUuid('optimization_run_id')->constrained('optimization_runs')->cascadeOnDelete();
            $table->foreignUuid('optimization_artifact_version_id')->nullable()->constrained('optimization_artifact_versions')->nullOnDelete();
            $table->string('variant_key', 255)->nullable();
            $table->string('variant_label', 255)->nullable();
            $table->string('device_class', 20)->default('desktop');
            $table->string('final_url', 2048)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('stylesheet_count')->default(0);
            $table->unsignedBigInteger('total_css_bytes')->default(0);
            $table->unsignedBigInteger('total_used_css_bytes')->default(0);
            $table->decimal('used_ratio', 8, 4)->default(0);
            $table->decimal('unused_ratio', 8, 4)->default(0);
            $table->unsignedInteger('scroll_height')->nullable();
            $table->unsignedInteger('viewport_height')->nullable();
            $table->unsignedInteger('console_message_count')->default(0);
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();

            $table->index(['optimization_run_id', 'device_class']);
            $table->index(['optimization_target_id', 'created_at']);
        });

        Schema::create('optimization_css_report_stylesheets', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('optimization_css_report_id')->constrained('optimization_css_reports')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->string('style_sheet_key', 255)->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('origin', 50)->nullable();
            $table->boolean('is_inline')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->unsignedBigInteger('bytes')->default(0);
            $table->unsignedBigInteger('used_bytes')->default(0);
            $table->decimal('used_ratio', 8, 4)->default(0);
            $table->unsignedInteger('rule_count')->nullable();
            $table->unsignedBigInteger('minified_bytes')->nullable();
            $table->timestamps();

            $table->index(['optimization_css_report_id', 'position'], 'optimization_css_stylesheets_report_position_index');
            $table->index(['source_url'], 'optimization_css_stylesheets_source_url_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('optimization_css_report_stylesheets');
        Schema::dropIfExists('optimization_css_reports');
    }
};
