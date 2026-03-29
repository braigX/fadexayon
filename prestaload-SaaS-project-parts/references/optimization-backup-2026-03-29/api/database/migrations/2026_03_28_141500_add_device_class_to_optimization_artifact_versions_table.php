<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->string('device_class', 20)->nullable()->after('optimization_run_id');
            $table->index(['optimization_run_id', 'device_class'], 'optimization_artifacts_run_device_index');
        });

        DB::table('optimization_artifact_versions')
            ->select(['id', 'optimization_target_id', 'meta_json'])
            ->orderBy('created_at')
            ->chunkById(100, function ($artifacts): void {
                foreach ($artifacts as $artifact) {
                    $meta = json_decode((string) ($artifact->meta_json ?? '{}'), true);
                    $deviceClass = null;

                    if (is_array($meta)) {
                        $deviceClass = trim((string) ($meta['variant']['device_class'] ?? ''));
                    }

                    if ($deviceClass === '') {
                        $target = DB::table('optimization_targets')
                            ->select('device_class')
                            ->where('id', $artifact->optimization_target_id)
                            ->first();
                        $deviceClass = trim((string) ($target->device_class ?? ''));
                    }

                    if ($deviceClass === '') {
                        $deviceClass = 'desktop';
                    }

                    DB::table('optimization_artifact_versions')
                        ->where('id', $artifact->id)
                        ->update([
                            'device_class' => strtolower($deviceClass) === 'mobile' ? 'mobile' : 'desktop',
                        ]);
                }
            }, 'id');
    }

    public function down(): void
    {
        Schema::table('optimization_artifact_versions', function (Blueprint $table): void {
            $table->dropIndex('optimization_artifacts_run_device_index');
            $table->dropColumn('device_class');
        });
    }
};
