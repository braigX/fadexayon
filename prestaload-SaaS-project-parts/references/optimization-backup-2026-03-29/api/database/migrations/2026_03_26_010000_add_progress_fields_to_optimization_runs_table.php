<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimization_runs', function (Blueprint $table): void {
            $table->unsignedInteger('total_variants')->default(0)->after('status');
            $table->unsignedInteger('completed_variants')->default(0)->after('total_variants');
            $table->unsignedInteger('failed_variants')->default(0)->after('completed_variants');
            $table->unsignedInteger('progress_percent')->default(0)->after('failed_variants');
            $table->string('current_variant_label', 255)->nullable()->after('progress_percent');
            $table->json('variants_json')->nullable()->after('current_variant_label');
        });
    }

    public function down(): void
    {
        Schema::table('optimization_runs', function (Blueprint $table): void {
            $table->dropColumn([
                'total_variants',
                'completed_variants',
                'failed_variants',
                'progress_percent',
                'current_variant_label',
                'variants_json',
            ]);
        });
    }
};
