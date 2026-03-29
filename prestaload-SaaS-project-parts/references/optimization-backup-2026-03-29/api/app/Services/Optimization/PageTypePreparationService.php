<?php

namespace App\Services\Optimization;

use App\Jobs\Optimization\RunPageTypePreparationJob;
use App\Models\PrestashopShop;
use App\Models\PrestashopStore;
use App\Models\PrestashopShopPageTypeProfile;
use App\Models\PrestashopShopUrl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class PageTypePreparationService
{
    public function __construct(
        private readonly PageTypeProfileService $pageTypeProfileService,
        private readonly PageTypeSampleUrlCheckerService $pageTypeSampleUrlCheckerService
    ) {
    }

    /**
     * @return array{
     *   shops:int,
     *   queued_count:int,
     *   skipped_count:int,
     *   queued:list<array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}>,
     *   skipped:list<array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}>
     * }
     */
    public function queueMissingPreparationsForStore(PrestashopStore $store, string $triggerSource): array
    {
        $startedAt = microtime(true);
        Log::info('prestaload.page_type_preparation.queue_for_store.started', [
            'store_id' => $store->id,
            'trigger_source' => $triggerSource,
        ]);

        $queued = [];
        $skipped = [];

        $shops = $store->shops()
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->orderBy('prestashop_shop_id')
            ->get();

        $shopsById = $shops->keyBy('id');

        foreach ($this->sampleUrlsForStore($store) as $selection) {
            if (! ($selection['reachable'] ?? false)) {
                $skipped[] = $selection['item'];
                continue;
            }

            /** @var PrestashopShopUrl $shopUrl */
            $shopUrl = $selection['shop_url'];
            /** @var PrestashopShop|null $shop */
            $shop = $shopsById->get($shopUrl->prestashop_shop_id);
            if (! $shop instanceof PrestashopShop) {
                $skipped[] = $this->buildResultItem(null, $shopUrl, 'missing_shop');
                continue;
            }

            $result = $this->queuePreparationForUrl($shop, $shopUrl, $triggerSource);
            if ($result['queued']) {
                $queued[] = $result['item'];
            } else {
                $skipped[] = $result['item'];
            }
        }

        $result = [
            'shops' => $shops->count(),
            'queued_count' => count($queued),
            'skipped_count' => count($skipped),
            'queued' => $queued,
            'skipped' => $skipped,
        ];

        Log::info('prestaload.page_type_preparation.queue_for_store.completed', [
            'store_id' => $store->id,
            'trigger_source' => $triggerSource,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'queued_count' => $result['queued_count'],
            'skipped_count' => $result['skipped_count'],
        ]);

        return $result;
    }

    /**
     * @return array{
     *   shops:int,
     *   queued_count:int,
     *   skipped_count:int,
     *   queued:list<array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}>,
     *   skipped:list<array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}>
     * }
     */
    public function queueMissingPreparationsForShop(PrestashopShop $shop, string $triggerSource): array
    {
        $queued = [];
        $skipped = [];

        foreach ($this->sampleUrlsForShop($shop) as $selection) {
            if (! ($selection['reachable'] ?? false)) {
                $skipped[] = $selection['item'];
                continue;
            }

            /** @var PrestashopShopUrl $shopUrl */
            $shopUrl = $selection['shop_url'];
            $result = $this->queuePreparationForUrl($shop, $shopUrl, $triggerSource);
            if ($result['queued']) {
                $queued[] = $result['item'];
            } else {
                $skipped[] = $result['item'];
            }
        }

        return [
            'shops' => 1,
            'queued_count' => count($queued),
            'skipped_count' => count($skipped),
            'queued' => $queued,
            'skipped' => $skipped,
        ];
    }

    /**
     * @return array{queued: bool, item: array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}}
     */
    public function queuePreparationForUrl(PrestashopShop $shop, PrestashopShopUrl $shopUrl, string $triggerSource): array
    {
        $startedAt = microtime(true);
        $storeProfile = $this->pageTypeProfileService->findProfileForUrl($shopUrl);
        if ($storeProfile instanceof PrestashopShopPageTypeProfile) {
            if ($this->pageTypeProfileService->isPreparedProfile($storeProfile)) {
                Log::info('prestaload.page_type_preparation.reused_ready', [
                    'store_id' => $shop->prestashop_store_id,
                    'shop_id' => $shop->id,
                    'shop_url_id' => $shopUrl->id,
                    'url' => $shopUrl->url,
                    'page_type' => $shopUrl->page_type,
                    'profile_id' => $storeProfile->id,
                    'trigger_source' => $triggerSource,
                    'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                ]);
                return [
                    'queued' => false,
                    'item' => $this->buildResultItem($storeProfile, $shopUrl, 'ready'),
                ];
            }

            if (in_array((string) $storeProfile->status, ['queued', 'preparing'], true)) {
                Log::info('prestaload.page_type_preparation.reused_in_progress', [
                    'store_id' => $shop->prestashop_store_id,
                    'shop_id' => $shop->id,
                    'shop_url_id' => $shopUrl->id,
                    'url' => $shopUrl->url,
                    'page_type' => $shopUrl->page_type,
                    'profile_id' => $storeProfile->id,
                    'profile_status' => $storeProfile->status,
                    'trigger_source' => $triggerSource,
                    'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                ]);
                return [
                    'queued' => false,
                    'item' => $this->buildResultItem($storeProfile, $shopUrl, (string) $storeProfile->status),
                ];
            }
        }

        $profile = $this->pageTypeProfileService->ensureProfileForUrl($shop, $shopUrl);

        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return [
                'queued' => false,
                'item' => [
                    'profile_id' => '',
                    'shop_url_id' => $shopUrl->id,
                    'url' => $shopUrl->url,
                    'page_type' => $shopUrl->page_type,
                    'status' => 'missing_page_type',
                ],
            ];
        }

        $this->pageTypeProfileService->markProfileQueued($profile, $shopUrl->id);

        try {
            RunPageTypePreparationJob::dispatch(
                $profile->id,
                $shop->id,
                $shopUrl->id,
                $triggerSource
            )->onQueue('default');
        } catch (Throwable $throwable) {
            $this->pageTypeProfileService->markProfileFailedForUrl($shopUrl);

            Log::error('prestaload.page_type_preparation.dispatch_failed', [
                'profile_id' => $profile->id,
                'shop_id' => $shop->id,
                'shop_url_id' => $shopUrl->id,
                'trigger_source' => $triggerSource,
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        Log::info('prestaload.page_type_preparation.queued', [
            'store_id' => $shop->prestashop_store_id,
            'shop_id' => $shop->id,
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type' => $shopUrl->page_type,
            'profile_id' => $profile->id,
            'trigger_source' => $triggerSource,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        return [
            'queued' => true,
            'item' => $this->buildResultItem($profile, $shopUrl, 'queued'),
        ];
    }

    public function beginInlinePreparationIfNeeded(PrestashopShop $shop, PrestashopShopUrl $shopUrl): ?PrestashopShopPageTypeProfile
    {
        $profile = $this->pageTypeProfileService->findProfileForUrl($shopUrl);
        if ($profile instanceof PrestashopShopPageTypeProfile
            && ($this->pageTypeProfileService->isPreparedProfile($profile)
                || in_array((string) $profile->status, ['queued', 'preparing'], true))) {
            return $profile;
        }

        $profile = $this->pageTypeProfileService->ensureProfileForUrl($shop, $shopUrl);
        if (! $profile instanceof PrestashopShopPageTypeProfile) {
            return null;
        }

        if ($this->pageTypeProfileService->isPreparedProfile($profile)) {
            return $profile;
        }

        if (! in_array((string) $profile->status, ['queued', 'preparing'], true)) {
            $this->pageTypeProfileService->markProfilePreparing($profile, $shopUrl->id);
        }

        return $profile->fresh();
    }

    /**
     * @return Collection<int, array{reachable: bool, shop_url: PrestashopShopUrl, item: array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}}>
     */
    private function sampleUrlsForStore(PrestashopStore $store): Collection
    {
        $urls = PrestashopShopUrl::query()
            ->whereHas('prestashopShop', static function ($query) use ($store): void {
                $query->where('prestashop_store_id', $store->id)
                    ->where('is_active', true);
            })
            ->whereNotNull('page_type_id')
            ->orderByRaw("
                case page_type
                    when 'home' then 0
                    when 'category' then 1
                    when 'product' then 2
                    when 'cms' then 3
                    else 9
                end
            ")
            ->orderBy('created_at')
            ->get();

        return $urls
            ->groupBy('page_type_id')
            ->map(function (Collection $group) use ($store): array {
                foreach ($group as $shopUrl) {
                    $shop = $shopUrl->prestashopShop;
                    if (! $shop instanceof PrestashopShop) {
                        $shop = PrestashopShop::query()->find($shopUrl->prestashop_shop_id);
                    }

                    if (! $shop instanceof PrestashopShop || (string) $shop->prestashop_store_id !== (string) $store->id) {
                        continue;
                    }

                    $check = $this->pageTypeSampleUrlCheckerService->check($shop, $shopUrl);
                    if ($check['reachable']) {
                        return [
                            'reachable' => true,
                            'shop_url' => $shopUrl,
                            'item' => $this->buildResultItem(null, $shopUrl, 'selected'),
                        ];
                    }
                }

                /** @var PrestashopShopUrl $fallback */
                $fallback = $group->first();

                return [
                    'reachable' => false,
                    'shop_url' => $fallback,
                    'item' => $this->buildResultItem(null, $fallback, 'no_reachable_sample'),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, array{reachable: bool, shop_url: PrestashopShopUrl, item: array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}}>
     */
    private function sampleUrlsForShop(PrestashopShop $shop): Collection
    {
        $urls = PrestashopShopUrl::query()
            ->where('prestashop_shop_id', $shop->id)
            ->whereNotNull('page_type_id')
            ->orderByRaw("
                case page_type
                    when 'home' then 0
                    when 'category' then 1
                    when 'product' then 2
                    when 'cms' then 3
                    else 9
                end
            ")
            ->orderBy('created_at')
            ->get();

        return $urls
            ->groupBy('page_type_id')
            ->map(function (Collection $group) use ($shop): array {
                foreach ($group as $shopUrl) {
                    $check = $this->pageTypeSampleUrlCheckerService->check($shop, $shopUrl);
                    if ($check['reachable']) {
                        return [
                            'reachable' => true,
                            'shop_url' => $shopUrl,
                            'item' => $this->buildResultItem(null, $shopUrl, 'selected'),
                        ];
                    }
                }

                /** @var PrestashopShopUrl $fallback */
                $fallback = $group->first();

                return [
                    'reachable' => false,
                    'shop_url' => $fallback,
                    'item' => $this->buildResultItem(null, $fallback, 'no_reachable_sample'),
                ];
            })
            ->values();
    }

    /**
     * @return array{profile_id:string,shop_url_id:string,url:string,page_type:string|null,status:string}
     */
    private function buildResultItem(
        ?PrestashopShopPageTypeProfile $profile,
        PrestashopShopUrl $shopUrl,
        string $status
    ): array {
        return [
            'profile_id' => $profile?->id ?? '',
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type' => $shopUrl->page_type,
            'status' => $status,
        ];
    }
}
