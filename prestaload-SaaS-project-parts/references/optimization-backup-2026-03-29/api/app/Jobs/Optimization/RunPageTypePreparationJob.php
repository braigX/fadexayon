<?php

namespace App\Jobs\Optimization;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\PrestashopShopUrl;
use App\Services\Optimization\OptimizationRunService;
use App\Services\Optimization\PageTypeProfileService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunPageTypePreparationJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    public int $uniqueFor = 3600;

    public function __construct(
        public readonly string $profileId,
        public readonly string $shopId,
        public readonly string $shopUrlId,
        public readonly string $triggerSource,
    ) {
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return 'page-type-preparation:' . $this->profileId;
    }

    public function handle(
        PageTypeProfileService $pageTypeProfileService,
        OptimizationRunService $runService
    ): void {
        $startedAt = microtime(true);
        $profile = PrestashopShopPageTypeProfile::query()->find($this->profileId);
        $shop = PrestashopShop::query()->with('prestashopStore')->find($this->shopId);
        $shopUrl = PrestashopShopUrl::query()->find($this->shopUrlId);

        if (! $profile instanceof PrestashopShopPageTypeProfile || ! $shop instanceof PrestashopShop || ! $shopUrl instanceof PrestashopShopUrl) {
            return;
        }

        Log::info('prestaload.page_type_preparation.job.started', [
            'profile_id' => $profile->id,
            'store_id' => $shop->prestashop_store_id,
            'shop_id' => $shop->id,
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type' => $shopUrl->page_type,
            'trigger_source' => $this->triggerSource,
        ]);

        if ($pageTypeProfileService->isPreparedProfile($profile)) {
            Log::info('prestaload.page_type_preparation.job.skipped_ready', [
                'profile_id' => $profile->id,
                'store_id' => $shop->prestashop_store_id,
                'shop_id' => $shop->id,
                'shop_url_id' => $shopUrl->id,
                'url' => $shopUrl->url,
                'page_type' => $shopUrl->page_type,
                'trigger_source' => $this->triggerSource,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
            return;
        }

        $pageTypeProfileService->markProfilePreparing($profile, $shopUrl->id);

        try {
            $result = $runService->queueShopUrlOptimization($shop, $shopUrl, 'page_type_prepare');

            RunOptimizationJob::dispatch($result['run']->id)->onQueue('optimization-render');
            $runService->markRunQueued($result['target'], $result['run']);

            Log::info('prestaload.page_type_preparation.job.dispatched', [
                'profile_id' => $profile->id,
                'store_id' => $shop->prestashop_store_id,
                'shop_id' => $shop->id,
                'shop_url_id' => $shopUrl->id,
                'url' => $shopUrl->url,
                'page_type' => $shopUrl->page_type,
                'run_id' => $result['run']->id,
                'target_id' => $result['target']->id,
                'trigger_source' => $this->triggerSource,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $throwable) {
            $pageTypeProfileService->markProfileFailedForUrl($shopUrl);

            Log::error('prestaload.page_type_preparation.failed', [
                'profile_id' => $profile->id,
                'shop_id' => $shop->id,
                'shop_url_id' => $shopUrl->id,
                'trigger_source' => $this->triggerSource,
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }
}
