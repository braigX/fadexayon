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

- [x] Used CSS artifacts
- [ ] Selective JS delay
- [x] Per-page-type optimization strategies
- [ ] Strategy scope abstraction for future per-URL strategies
- [x] Grouped optimization as default
- [ ] Per-page override setting

## Run Steps

- [x] `validate_target`
- [x] `cache_prepare`
- [x] `render_page`
- [x] `analyze_css`
- [x] `build_css`
- [x] `build_used_css`
- [x] `scan_performance`
- [x] `build_html`
- [x] `validate_artifact`
- [x] `publish_cache`
- [ ] `extract_assets`
- [ ] `build_js`

## API

- [x] `ModuleCacheService`
- [x] `BrowserRenderService`
- [x] Optimization runs + progress
- [x] PageSpeed score scan endpoint
- [x] Local scanner endpoint wiring for local/private URLs
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
- [x] Separate page-type preparation alert
- [x] Optimization settings page
- [ ] Validation result labels
- [ ] Published cache state label
- [ ] Rollback action

## Used CSS Phases

- [x] Phase 1: Generate `used.css` artifact
- [x] Phase 1: Store `used.css` path / bytes / checksum
- [x] Phase 1: Show `used.css` metrics in CSS page
- [x] Phase 2: Validate `used.css` before publish
- [x] Phase 2: Block publish if `used.css` validation fails
- [x] Phase 3: Controlled `used.css` delivery with original CSS fallback
- [ ] Phase 3: Remove selected original CSS only after repeated validation success

## Page-Type Profiles

- [ ] Detect page type reliably per URL:
  - [ ] `home`
  - [ ] `category`
  - [ ] `product`
  - [ ] `cms`
  - [ ] `search`
- [ ] Create one optimization profile per `shop + page_type`
- [ ] Aggregate coverage, CSS analysis, and `used.css` by page type instead of by single URL
- [ ] Rebuild one page-type `used.css` per device:
  - [ ] desktop
  - [ ] mobile
- [ ] Reuse page-type strategy for new URLs of the same type
- [ ] Keep URL-level cache purge lightweight:
  - [ ] `Purge` removes only cached page artifacts for that URL
  - [ ] keeps page-type analysis and page-type `used.css`
- [ ] Make `Purge all` a full shop reset:
  - [ ] cached pages
  - [ ] page-type coverage
  - [ ] page-type CSS analysis
  - [ ] page-type `used.css`
  - [ ] page-type asset rules
- [ ] Add `Purge per type` later
- [ ] Keep room for future strategy scope selector:
  - [ ] `page_type`
  - [ ] `url`
  - [ ] do not enable URL mode in this version

## CSS Asset Rules

- [x] Persist page-type CSS asset rules in DB
- [x] Store:
  - recommended action
  - effective action
  - action source
  - reasons
  - evidence
- [x] Support CSS actions:
  - `keep`
  - `preload`
  - `minify`
  - `reduce`
  - `reduce + minify`
  - `remove`
- [x] Generate reduced CSS assets per page type and device
- [x] Generate minified CSS assets per page type and device
- [x] Group reduced/minified CSS into page-type bundles
- [x] Apply CSS rules into optimized HTML from persisted `effective_action`
- [ ] Add admin editing for CSS asset rules
- [ ] Add safe rollback from overridden CSS rules

## JS Asset Rules

- [x] Store raw JS audit evidence in scan reports
- [x] Add separate JS Optimization page
- [x] Persist page-type JS asset rules in DB using `asset_type = js`
- [x] Support JS actions:
  - `keep`
  - `load_on_interaction`
  - `minify`
  - `reduce`
  - `reduce + minify`
- [x] Apply JS `load_on_interaction` from persisted rules in optimized HTML
- [ ] Apply JS `minify` / `reduce` / `reduce + minify` with generated JS assets
- [ ] Add admin editing for JS asset rules
- [ ] Add explicit JS defer / delay rule families for same-origin scripts

## Font Optimization

- [ ] Add a dedicated font optimization track in the module
- [ ] Detect loaded font files and font CSS per page type
- [ ] Classify:
  - text fonts
  - icon fonts
  - same-origin fonts
  - third-party fonts
  - used weights/styles
- [ ] Add font actions:
  - `keep`
  - `preload`
  - `self_host`
  - `subset`
  - `remove_unused_weights`
  - later `replace_icon_font`
- [ ] Self-host fonts when allowed and useful
- [ ] Prefer `woff2` delivery by default
- [ ] Add long immutable cache policy for published font assets
- [ ] Generate per-language or per-page-type subsets where safe
- [ ] Add `font-display` controls:
  - `swap`
  - `optional`
- [ ] Preload only critical above-the-fold fonts
- [ ] Deduplicate duplicated Google Fonts / external font requests
- [ ] Add fallback metric tuning later:
  - `size-adjust`
  - `ascent-override`
  - `descent-override`
  - `line-gap-override`
- [ ] Reduce icon-font dependence by moving toward SVG icons where practical
- [ ] Expose font rules in UI with per-page-type editing

## Strategy Scope Model

- [ ] Introduce generic optimization strategy scope
- [ ] Allow one strategy to target either:
  - [ ] a `page_type`
  - [ ] a single `url`
- [ ] Keep current product behavior on `page_type` only
- [ ] Add UI room for a future scope selector without enabling it yet
- [ ] Make strategy resolution order explicit for future rollout:
  - [ ] URL strategy override
  - [ ] page-type strategy fallback
  - [ ] default shop optimization fallback

## Strategy Tables

- [ ] `optimization_strategies`
  - `id`
  - `prestashop_store_id`
  - `prestashop_shop_id`
  - `scope_type`
  - `scope_key`
  - `page_type`
  - `normalized_url`
  - `name`
  - `status`
  - `last_aggregated_at`
  - `published_version_id`
- [ ] `optimization_strategy_sample_urls`
  - `id`
  - `strategy_id`
  - `optimization_target_id`
  - `url`
  - `page_type`
  - `sample_weight`
  - `last_analyzed_at`
- [ ] `optimization_strategy_assets`
  - `id`
  - `strategy_id`
  - `asset_type`
  - `asset_url`
  - `asset_pattern`
  - `recommended_action`
  - `effective_action`
  - `action_source`
  - `confidence`
  - `notes`
- [ ] `optimization_strategy_asset_stats`
  - `id`
  - `strategy_asset_id`
  - `device_class`
  - `sample_count`
  - `total_bytes`
  - `avg_used_bytes`
  - `avg_used_ratio`
  - `last_seen_at`
- [ ] `optimization_strategy_css_artifacts`
  - `id`
  - `strategy_id`
  - `device_class`
  - `css_type`
  - `storage_path`
  - `bytes`
  - `sha256`
  - `status`
  - `generated_from_sample_count`
  - `published_at`

## Strategy Relationships

- [ ] `prestashop_store` has many `optimization_strategies`
- [ ] `optimization_strategy` has many sample URLs
- [ ] `optimization_strategy` has many asset rules
- [ ] `optimization_strategy_asset` has many aggregated stats by device
- [ ] `optimization_strategy` has many CSS artifacts
- [ ] `optimization_target` can be associated with one effective strategy
- [ ] URL optimization runs feed strategy aggregation, but URL cache publish stays separate from strategy storage
- [ ] current effective strategy generation is page-type based
- [ ] future effective strategy can be URL-specific
- [ ] page-type `used.css` becomes reusable optimization knowledge
- [ ] URL cached HTML consumes the current published effective strategy with fallback to original CSS files

## Later

- [x] Separate CSS management page
- [x] Separate JS management page
- [ ] Cache publish history page
- [ ] Re-optimize changed variants automatically
- [ ] Show per-variant optimization details from JSON step history
