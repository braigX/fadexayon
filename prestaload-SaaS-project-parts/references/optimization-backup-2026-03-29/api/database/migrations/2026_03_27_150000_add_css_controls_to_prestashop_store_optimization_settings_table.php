<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_store_optimization_settings', function (Blueprint $table): void {
            $table->boolean('css_optimization_enabled')->default(true)->after('prestashop_store_id');
            $table->boolean('defer_safe_stylesheets')->default(true)->after('generate_critical_css');
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_store_optimization_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'css_optimization_enabled',
                'defer_safe_stylesheets',
            ]);
        });
    }
};
