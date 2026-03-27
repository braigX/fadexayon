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
- [x] Store optimization settings per store

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

- [x] Conservative final HTML compression
- [x] Inline CSS minification
- [x] Inline JS minification
- [x] Safe script attribute adjustments only where known-safe
- [ ] No unused CSS removal yet
- [ ] No aggressive JS delay yet
- [x] Save raw and optimized artifacts clearly
- [x] Add HTML optimization step logs
- [x] Validation before publish
- [x] CSS / critical CSS / deferral settings wiring
- [x] Skip CSS steps cleanly when CSS optimization is disabled

## V2

- [x] Critical CSS
- [x] CSS delivery optimization
- [ ] Defer-safe JS handling
- [x] Visual compare between raw and optimized render
- [ ] Console error comparison
- [x] Publish only when validation passes
- [x] CSS analysis reports and delivery classification

## V3

- [ ] Used CSS artifacts
- [ ] Selective JS delay
- [ ] Per-group optimization strategies
- [ ] Grouped optimization as default
- [ ] Per-page override setting

## Run Steps

- [x] `validate_target`
- [x] `cache_prepare`
- [x] `render_page`
- [x] `analyze_css`
- [x] `build_css`
- [x] `build_html`
- [x] `validate_artifact`
- [x] `publish_cache`
- [ ] `extract_assets`
- [ ] `build_used_css`
- [ ] `build_js`

## API

- [x] `ModuleCacheService`
- [x] `BrowserRenderService`
- [x] Optimization runs + progress
- [x] PageSpeed score scan endpoint
- [x] `HtmlOptimizationService`
- [x] CSS analysis + delivery classification
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
- [x] Optimization step details in blue alert
- [x] Optimization settings page
- [ ] Validation result labels
- [ ] Published cache state label
- [ ] Rollback action

## Used CSS Phases

- [ ] Phase 1: Generate `used.css` artifact only
- [ ] Phase 1: Store `used.css` path / bytes / checksum
- [ ] Phase 1: Show `used.css` metrics in CSS page
- [ ] Phase 2: Validate `used.css` before any delivery change
- [ ] Phase 2: Block publish if `used.css` validation fails
- [ ] Phase 3: Controlled `used.css` delivery with original CSS fallback
- [ ] Phase 3: Remove selected original CSS only after repeated validation success

## Later

- [x] Separate CSS management page
- [ ] Separate JS management page
- [ ] Cache publish history page
- [ ] Re-optimize changed variants automatically
- [ ] Show per-variant optimization details from JSON step history
