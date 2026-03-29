<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->string('critical_css_path', 1024)->nullable()->after('optimized_html_path');
            $table->unsignedBigInteger('critical_css_bytes')->nullable()->after('optimized_html_bytes');
            $table->string('critical_css_sha256', 64)->nullable()->after('optimized_html_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->dropColumn([
                'critical_css_path',
                'critical_css_bytes',
                'critical_css_sha256',
            ]);
        });
    }
};
