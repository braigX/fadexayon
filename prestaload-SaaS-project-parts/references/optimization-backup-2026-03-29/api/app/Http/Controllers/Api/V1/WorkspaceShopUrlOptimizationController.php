<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptimizationRunResource;
use App\Jobs\Optimization\RunOptimizationJob;
use App\Models\PrestashopShop;
use App\Models\PrestashopShopUrl;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\OptimizationCleanupService;
use App\Services\Optimization\OptimizationRunService;
use App\Services\Optimization\PageTypePreparationService;
use App\Services\Optimization\UrlVerificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WorkspaceShopUrlOptimizationController extends ApiController
{
    public function __construct(
        private readonly OptimizationRunService $runService,
        private readonly OptimizationCleanupService $cleanupService,
        private readonly PageTypePreparationService $pageTypePreparationService,
        private readonly UrlVerificationService $urlVerificationService
    )
    {
    }

    public function store(Request $request, Workspace $workspace, PrestashopShop $shop, PrestashopShopUrl $shopUrl): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);
        $this->assertShopUrlBelongsToShop($shop, $shopUrl);

        $startedAt = microtime(true);
        Log::info('prestaload.optimization.request.started', [
            'workspace_id' => $workspace->id,
            'shop_id' => $shop->id,
            'store_id' => $shop->prestashop_store_id,
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type' => $shopUrl->page_type,
        ]);

        $this->pageTypePreparationService->beginInlinePreparationIfNeeded($shop, $shopUrl);

        $this->cleanupService->purgeUrl($workspace, $shop, $shopUrl);

        $result = $this->runService->queueUrlOptimization($workspace, $shop, $shopUrl);

        try {
            RunOptimizationJob::dispatch($result['run']->id)->onQueue('optimization-render');
            $this->runService->markRunQueued($result['target'], $result['run']);
        } catch (Throwable $throwable) {
            $this->runService->markDispatchFailed($result['target'], $result['run'], $throwable->getMessage());

            Log::error('prestaload.optimization.job.dispatch_failed', [
                'run_id' => $result['run']->id,
                'target_id' => $result['target']->id,
                'message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        Log::info('prestaload.optimization.request.queued', [
            'workspace_id' => $workspace->id,
            'shop_id' => $shop->id,
            'store_id' => $shop->prestashop_store_id,
            'shop_url_id' => $shopUrl->id,
            'url' => $shopUrl->url,
            'page_type' => $shopUrl->page_type,
            'run_id' => $result['run']->id,
            'trigger_type' => $result['run']->trigger_type,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ]);

        return $this->success(
            new OptimizationRunResource($result['run']),
            'Optimization queued.',
            202
        );
    }

    public function verify(Request $request, Workspace $workspace, PrestashopShop $shop, PrestashopShopUrl $shopUrl): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);
        $this->assertShopUrlBelongsToShop($shop, $shopUrl);

        $result = $this->urlVerificationService->verifyLatestPublished($shop, $shopUrl);

        return $this->success(
            $result,
            $result['overall_valid'] ? 'Verification passed.' : 'Verification found issues.'
        );
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
