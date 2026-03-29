<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\OptimizationCleanupService;
use App\Services\Optimization\PageTypePreparationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkspaceShopUrlCacheController extends ApiController
{
    public function __construct(
        private readonly OptimizationCleanupService $cleanupService,
        private readonly PageTypePreparationService $pageTypePreparationService
    )
    {
    }

    public function purge(Request $request, Workspace $workspace, PrestashopShop $shop, PrestashopShopUrl $shopUrl): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);
        $this->assertShopUrlBelongsToShop($shop, $shopUrl);

        $result = $this->cleanupService->purgeUrl($workspace, $shop, $shopUrl);

        return $this->success([
            'shop_url_id' => $shopUrl->id,
            'variants_count' => (int) ($result['module']['variants_count'] ?? 0),
            'purged_count' => (int) ($result['module']['purged_count'] ?? 0),
            'results' => $result['module']['results'] ?? [],
            'api' => $result['api'] ?? [],
        ], 'Cache purged.');
    }

    public function purgeAll(Request $request, Workspace $workspace, PrestashopShop $shop): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);

        $startedAt = microtime(true);
        Log::info('prestaload.cache.purge_all.started', [
            'workspace_id' => $workspace->id,
            'shop_id' => $shop->id,
            'store_id' => $shop->prestashop_store_id,
        ]);

        $result = $this->cleanupService->purgeShop($workspace, $shop);
        $pageTypePreparation = null;
        $pageTypePreparationError = null;
        try {
            $pageTypePreparation = $this->pageTypePreparationService->queueMissingPreparationsForStore($shop->prestashopStore, 'purge_all');
        } catch (\Throwable $exception) {
            $pageTypePreparationError = $exception->getMessage();
        }

        Log::info('prestaload.cache.purge_all.completed', [
            'workspace_id' => $workspace->id,
            'shop_id' => $shop->id,
            'store_id' => $shop->prestashop_store_id,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'page_type_preparation' => $pageTypePreparation,
            'page_type_preparation_error' => $pageTypePreparationError,
        ]);

        return $this->success([
            'shop_id' => $shop->id,
            'related_shop_ids' => $result['related_shop_ids'] ?? [],
            'module' => $result['module'] ?? [],
            'api' => $result['api'] ?? [],
            'page_type_preparation' => $pageTypePreparation,
            'page_type_preparation_error' => $pageTypePreparationError,
        ], 'Shop cache and optimization artifacts purged.');
    }

    private function assertWorkspaceMember(Request $request, Workspace $workspace): void
    {
        $exists = WorkspaceMember::query()
            ->where('workspace_id', $workspace->id)
            ->where('user_id', $request->user()?->id)
            ->where('status', 'active')
            ->exists();

        if (! $exists) {
            throw new AuthorizationException('You do not have access to this workspace.');
        }
    }

    private function assertWorkspaceShop(Workspace $workspace, PrestashopShop $shop): void
    {
        if ($shop->prestashopStore?->workspace_id !== $workspace->id) {
            throw new AuthorizationException('This shop does not belong to the selected workspace.');
        }
    }

    private function assertShopUrlBelongsToShop(PrestashopShop $shop, PrestashopShopUrl $shopUrl): void
    {
        if ($shopUrl->prestashop_shop_id !== $shop->id) {
            throw new AuthorizationException('This URL does not belong to the selected shop.');
        }
    }
}
