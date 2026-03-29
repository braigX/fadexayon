<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->unsignedBigInteger('raw_html_bytes')->nullable()->after('optimized_html_path');
            $table->unsignedBigInteger('optimized_html_bytes')->nullable()->after('raw_html_bytes');
            $table->string('raw_html_sha256', 64)->nullable()->after('optimized_html_bytes');
            $table->string('optimized_html_sha256', 64)->nullable()->after('raw_html_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->dropColumn([
                'raw_html_bytes',
                'optimized_html_bytes',
                'raw_html_sha256',
                'optimized_html_sha256',
            ]);
        });
    }
};
