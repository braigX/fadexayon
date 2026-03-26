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

- [ ] Publish only validated optimized cache
- [ ] Keep previous published cache as fallback
- [ ] Add rollback to previous published version
- [ ] Skip unsafe pages:
  - [ ] logged-in
  - [ ] cart
  - [ ] checkout
  - [ ] account
- [ ] Skip unsafe JS patterns by default
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

## V2

- [ ] Critical CSS
- [ ] CSS delivery optimization
- [ ] Defer-safe JS handling
- [ ] Validation step before publish
- [ ] Visual compare between raw and optimized render
- [ ] Console error comparison
- [ ] Publish only when validation passes

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
- [x] `publish_cache`
- [ ] `extract_assets`
- [ ] `build_css`
- [ ] `build_js`
- [ ] `validate_artifact`

## API

- [x] `ModuleCacheService`
- [x] `BrowserRenderService`
- [x] Optimization runs + progress
- [x] PageSpeed score scan endpoint
- [ ] `HtmlOptimizationService`
- [ ] `CssOptimizationService`
- [ ] `JsOptimizationService`
- [ ] `ArtifactValidationService`
- [ ] Publish history / rollback service

## Module

- [x] `cachevariants`
- [x] `cacheprepare`
- [x] `cachepublish`
- [x] `cachepurge`
- [x] Runtime cache serving
- [ ] Runtime fallback to previous published version
- [ ] Cache metadata for validation/publish history

## Dashboard

- [x] Optimize URL action
- [x] Purge cache action
- [x] Live optimization progress alert
- [x] PageSpeed score labels
- [ ] Validation result labels
- [ ] Published cache state label
- [ ] Rollback action

## Later

- [ ] Separate CSS management page
- [ ] Separate JS management page
- [ ] Cache publish history page
- [ ] Re-optimize changed variants automatically
