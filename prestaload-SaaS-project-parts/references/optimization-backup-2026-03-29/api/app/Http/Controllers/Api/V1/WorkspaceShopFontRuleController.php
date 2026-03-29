<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PrestashopShop;
use App\Models\PrestashopShopPageTypeAssetRule;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Services\Optimization\PageTypeFontAssetService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkspaceShopFontRuleController extends ApiController
{
    public function __construct(
        private readonly PageTypeFontAssetService $pageTypeFontAssetService
    ) {
    }

    public function update(
        Request $request,
        Workspace $workspace,
        PrestashopShop $shop,
        PrestashopShopPageTypeAssetRule $rule
    ): JsonResponse {
        $this->assertWorkspaceMember($request, $workspace);

        if ($shop->prestashopStore?->workspace_id !== $workspace->id) {
            throw new AuthorizationException('This shop does not belong to the selected workspace.');
        }

        $rule->loadMissing('profile.prestashopShop');
        if (
            ! $rule->profile
            || (string) $rule->profile->prestashop_shop_id !== (string) $shop->id
            || (string) $rule->asset_type !== 'font'
        ) {
            throw new AuthorizationException('This font rule does not belong to the selected shop.');
        }

        $allowedActions = $this->allowedActionsForRule($rule);
        $validated = $request->validate([
            'effective_action' => ['required', 'string', Rule::in($allowedActions)],
        ]);

        $rule->effective_action = (string) $validated['effective_action'];
        $rule->action_source = 'user';
        $rule->save();

        if (in_array($rule->effective_action, ['self_host', 'self_host_preload', 'set_font_display_swap'], true)) {
            $this->pageTypeFontAssetService->syncSelfHostedAssets($rule->profile, (string) $rule->device_class);
        } else {
            $this->pageTypeFontAssetService->clearSelfHostedAsset($rule);
        }

        $rule->refresh();

        return $this->success([
            'id' => $rule->id,
            'effective_action' => $rule->effective_action,
            'action_source' => $rule->action_source,
            'font_asset_status' => $rule->font_asset_status,
            'font_css_public_url' => $rule->font_css_public_url,
        ], 'Font rule updated.');
    }

    /**
     * @return list<string>
     */
    private function allowedActionsForRule(PrestashopShopPageTypeAssetRule $rule): array
    {
        $type = (string) ($rule->evidence_json['type'] ?? '');

        if ($type === 'icon_stylesheet') {
            return ['keep', 'dedupe_icon_font'];
        }

        if ($type === 'local_font_stylesheet') {
            return ['keep', 'set_font_display_swap'];
        }

        return ['keep', 'self_host', 'self_host_preload', 'remove_unused'];
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
}
