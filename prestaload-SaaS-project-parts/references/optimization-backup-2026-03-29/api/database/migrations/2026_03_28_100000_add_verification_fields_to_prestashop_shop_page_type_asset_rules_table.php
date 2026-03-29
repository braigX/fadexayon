<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->string('device_class', 30)->default('desktop')->after('profile_id');
            $table->json('reasons_json')->nullable()->after('confidence');
            $table->json('evidence_json')->nullable()->after('reasons_json');
            $table->timestamp('last_verified_at')->nullable()->after('evidence_json');

            $table->unique(
                ['profile_id', 'device_class', 'asset_type', 'asset_url'],
                'shop_page_type_asset_rules_profile_device_type_url_unique'
            );
            $table->index(
                ['profile_id', 'device_class', 'effective_action'],
                'shop_page_type_asset_rules_profile_device_action_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('prestashop_shop_page_type_asset_rules', function (Blueprint $table): void {
            $table->dropUnique('shop_page_type_asset_rules_profile_device_type_url_unique');
            $table->dropIndex('shop_page_type_asset_rules_profile_device_action_index');
            $table->dropColumn(['device_class', 'reasons_json', 'evidence_json', 'last_verified_at']);
        });
    }
};
