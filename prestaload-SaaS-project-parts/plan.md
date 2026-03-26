# PrestaLoad Plan

## Current Base

- [x] Connect store and sync shops
- [x] Discover page URLs per shop
- [x] List URLs per selected shop in dashboard
- [x] Browser worker renders full HTML
- [x] Generate optimized HTML version
- [x] Build cache variants from module
- [x] Publish cached HTML to module
- [x] Store cache files with hash-based fanout
- [x] Serve cached HTML from module runtime
- [x] Purge cached HTML variants
- [x] Track optimization runs and progress
- [x] Scan mobile and desktop PageSpeed scores

## Safety Rules

- [x] Publish only validated optimized cache
- [x] On validation failure, leave PrestaShop render normally
- [ ] Keep previous published cache as fallback
- [ ] Add rollback to previous published version
- [x] Skip unsafe pages:
  - [x] logged-in
  - [x] cart
  - [ ] checkout
  - [ ] account
- [x] Skip unsafe JS patterns by default
- [ ] Preserve original CSS/JS fallback references until validated

## V1

- [x] Final HTML minification
- [x] Inline CSS minification
- [x] Inline JS minification
- [x] Safe script attribute adjustments only where known-safe
- [ ] No unused CSS removal yet
- [ ] No aggressive JS delay yet
- [x] Save raw and optimized artifacts clearly
- [x] Add HTML optimization step logs
- [x] Validation before publish

## V2

- [ ] Critical CSS
- [ ] CSS delivery optimization
- [ ] Defer-safe JS handling
- [ ] Visual compare between raw and optimized render
- [ ] Console error comparison
- [x] Publish only when validation passes

## V3

- [ ] Used CSS artifacts
- [ ] Selective JS delay
- [ ] Per-group optimization strategies
- [ ] Grouped optimization as default
- [ ] Per-page override setting

## Run Steps

- [x] `cache_prepare`
- [x] `render_source`
- [x] `build_html`
- [x] `validate_artifact`
- [x] `publish_cache`
- [ ] `extract_assets`
- [ ] `build_css`
- [ ] `build_js`

## API

- [x] `ModuleCacheService`
- [x] `BrowserRenderService`
- [x] Optimization runs + progress
- [x] PageSpeed score scan endpoint
- [x] `HtmlOptimizationService`
- [ ] `CssOptimizationService`
- [ ] `JsOptimizationService`
- [x] `ArtifactValidationService`
- [ ] Publish history / rollback service

## Module

- [x] `cachevariants`
- [x] `cacheprepare`
- [x] `cachepublish`
- [x] `cachepurge`
- [x] Runtime cache serving
- [x] One-day cache for fetched variants
- [ ] Runtime fallback to previous published version
- [ ] Cache metadata for validation/publish history

## Dashboard

- [x] Optimize URL action
- [x] Purge cache action
- [x] Live optimization progress alert
- [x] Queued requests alert
- [x] PageSpeed score labels
- [ ] Validation result labels
- [ ] Published cache state label
- [ ] Rollback action

## Later

- [ ] Separate CSS management page
- [ ] Separate JS management page
- [ ] Cache publish history page
- [ ] Re-optimize changed variants automatically
- [ ] Show per-variant optimization details from JSON step history
