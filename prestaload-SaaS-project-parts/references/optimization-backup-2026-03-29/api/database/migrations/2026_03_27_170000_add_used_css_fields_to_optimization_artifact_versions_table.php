<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->string('used_css_path', 1024)->nullable()->after('critical_css_path');
            $table->unsignedInteger('used_css_bytes')->nullable()->after('critical_css_bytes');
            $table->string('used_css_sha256', 64)->nullable()->after('critical_css_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->dropColumn([
                'used_css_path',
                'used_css_bytes',
                'used_css_sha256',
            ]);
        });
    }
};
