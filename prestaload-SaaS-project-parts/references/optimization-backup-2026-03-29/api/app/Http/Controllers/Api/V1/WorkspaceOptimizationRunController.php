<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\OptimizationRunResource;
use App\Models\OptimizationRun;
use App\Models\PrestashopShop;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\OptimizationQueueControlService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceOptimizationRunController extends ApiController
{
    public function __construct(
        private readonly OptimizationQueueControlService $queueControlService
    )
    {
    }

    public function show(Request $request, Workspace $workspace, OptimizationRun $run): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);

        $run->loadMissing('optimizationTarget.prestashopStore', 'cssReports.stylesheets', 'cssReports.optimizationTarget.prestashopShopUrl', 'steps');

        if ($run->optimizationTarget?->prestashopStore?->workspace_id !== $workspace->id) {
            throw new AuthorizationException('This optimization run does not belong to the selected workspace.');
        }

        return $this->success(
            new OptimizationRunResource($run),
            'Optimization run fetched.'
        );
    }

    public function destroy(Request $request, Workspace $workspace, PrestashopShop $shop, OptimizationRun $run): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);

        $this->queueControlService->cancelRun($workspace, $shop, $run);

        return $this->success(
            null,
            'Optimization run cancelled.'
        );
    }

    public function clearQueue(Request $request, Workspace $workspace, PrestashopShop $shop): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceShop($workspace, $shop);

        $cleared = $this->queueControlService->clearQueuedRunsForShop($workspace, $shop);

        return $this->success(
            [
                'cleared_count' => $cleared,
            ],
            'Optimization queue cleared.'
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
}
