<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PrestashopStore;
use App\Models\PrestashopStoreOptimizationSetting;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceStoreOptimizationSettingsController extends ApiController
{
    public function show(Request $request, Workspace $workspace, PrestashopStore $store): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceStore($workspace, $store);

        return $this->success($this->settingsPayload($store), 'Optimization settings loaded.');
    }

    public function update(Request $request, Workspace $workspace, PrestashopStore $store): JsonResponse
    {
        $this->assertWorkspaceMember($request, $workspace);
        $this->assertWorkspaceStore($workspace, $store);

        $data = $request->validate([
            'css_optimization_enabled' => ['sometimes', 'boolean'],
            'generate_critical_css' => ['sometimes', 'boolean'],
            'defer_safe_stylesheets' => ['sometimes', 'boolean'],
            'minify_css' => ['sometimes', 'boolean'],
            'optimize_web_fonts' => ['sometimes', 'boolean'],
            'optimize_javascript' => ['sometimes', 'boolean'],
            'delay_ads_analytics_scripts' => ['sometimes', 'boolean'],
            'prioritize_speed_over_slider_loading' => ['sometimes', 'boolean'],
            'compress_inline_js' => ['sometimes', 'boolean'],
            'lazy_load_iframes_youtube' => ['sometimes', 'boolean'],
            'lazy_load_vimeo_videos' => ['sometimes', 'boolean'],
            'compress_final_html' => ['sometimes', 'boolean'],
            'cache_ttl' => ['sometimes', 'string', 'max:50'],
            'skip_lazy_load_css_patterns' => ['sometimes', 'array'],
            'skip_lazy_load_css_patterns.*' => ['string', 'max:255'],
            'skip_lazy_load_js_patterns' => ['sometimes', 'array'],
            'skip_lazy_load_js_patterns.*' => ['string', 'max:255'],
        ]);

        $setting = $store->optimizationSetting()->first();

        if (! $setting) {
            $setting = new PrestashopStoreOptimizationSetting([
                'id' => (string) Str::uuid(),
                'prestashop_store_id' => $store->id,
                ...PrestashopStoreOptimizationSetting::defaults(),
            ]);
        }

        $setting->fill($data);
        $setting->save();

        $store->unsetRelation('optimizationSetting');

        return $this->success($this->settingsPayload($store->fresh('optimizationSetting')), 'Optimization settings updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function settingsPayload(PrestashopStore $store): array
    {
        $setting = $store->relationLoaded('optimizationSetting')
            ? $store->optimizationSetting
            : $store->optimizationSetting()->first();

        if (! $setting) {
            return PrestashopStoreOptimizationSetting::defaults();
        }

        return array_merge(
            PrestashopStoreOptimizationSetting::defaults(),
            $setting->only([
                'generate_critical_css',
                'css_optimization_enabled',
                'defer_safe_stylesheets',
                'minify_css',
                'optimize_web_fonts',
                'optimize_javascript',
                'delay_ads_analytics_scripts',
                'prioritize_speed_over_slider_loading',
                'compress_inline_js',
                'lazy_load_iframes_youtube',
                'lazy_load_vimeo_videos',
                'compress_final_html',
                'cache_ttl',
                'skip_lazy_load_css_patterns',
                'skip_lazy_load_js_patterns',
            ])
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

    private function assertWorkspaceStore(Workspace $workspace, PrestashopStore $store): void
    {
        if ($store->workspace_id !== $workspace->id) {
            throw new AuthorizationException('This store does not belong to the selected workspace.');
        }
    }
}
