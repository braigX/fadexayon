# PrestaLoad SaaS Plan

## Product Direction

- [ ] Keep the Prestashop module thin: connection, toggles, cache serving, diagnostics, fallback.
- [ ] Move optimization intelligence to the SaaS server: analysis, decisions, generation, rewriting, orchestration.
- [ ] Optimize pages progressively, not all at once.
- [ ] Make every optimization reversible.
- [ ] Support multi-store, multi-language, multi-currency, and device variants from day one in the data model.
- [ ] Treat compatibility and rollback as core product features, not later fixes.

## Core Workflow

- [ ] Merchant installs `PrestaLoad` module.
- [ ] Merchant connects the module to the SaaS workspace.
- [ ] Module sends shop identity, version, domain, store list, language list, currency list, and feature capabilities.
- [ ] SaaS creates a site record and scans the website.
- [ ] SaaS records initial baseline scores and raw page diagnostics.
- [ ] SaaS discovers representative pages:
  - [ ] Homepage
  - [ ] Category pages
  - [ ] Product pages
  - [ ] CMS pages
  - [ ] Search page if relevant
  - [ ] Cart and checkout only for diagnostics, not caching
- [ ] SaaS groups URLs by page type and variant dimensions.
- [ ] Optimization starts page by page.
- [ ] For each page type, SaaS applies features one by one and records the effect.
- [ ] SaaS promotes only the changes that improve the page safely.
- [ ] Module pulls or receives optimized assets/rules/cache payloads.
- [ ] Module serves optimized HTML locally through page cache.

## Page Optimization Strategy

- [ ] Do not start with automatic full optimization on every page.
- [ ] Start with baseline scanning first.
- [ ] Keep a report per page type and per variant.
- [ ] Store "before" and "after" metrics for every optimization step.
- [ ] Apply features in sequence, not all together.
- [ ] Keep the sequence configurable per page type.

### Optimization Sequence Per Page

- [ ] Step 1: baseline scan
  - [ ] Lighthouse / PSI-like metrics
  - [ ] HTML snapshot
  - [ ] asset inventory
  - [ ] font usage
  - [ ] image inventory
  - [ ] JS execution inventory
  - [ ] CSS waste inventory
- [ ] Step 2: image optimization
  - [ ] image proxy
  - [ ] width / height fixes
  - [ ] loading / fetchpriority tuning
  - [ ] background image lazy loading
- [ ] Step 3: font optimization
  - [ ] dedupe icon fonts
  - [ ] reduce Google Fonts payload
  - [ ] `display=swap`
  - [ ] `preconnect`
- [ ] Step 4: critical CSS generation
  - [ ] mobile
  - [ ] tablet
  - [ ] desktop
  - [ ] reject bad outputs
  - [ ] visual safety checks later
- [ ] Step 5: CSS delivery optimization
  - [ ] defer selected non-critical CSS
  - [ ] keep large core CSS external
  - [ ] avoid editing original Prestashop core CSS files
- [ ] Step 6: JS delivery optimization
  - [ ] defer
  - [ ] after load
  - [ ] after interaction
  - [ ] disable page-inappropriate assets
  - [ ] handle inline injectors
- [ ] Step 7: HTML cleanup
  - [ ] safe compression
  - [ ] inline noise reduction
  - [ ] move non-critical inline config later when safe
- [ ] Step 8: page cache generation
  - [ ] generate cache variants after the page is optimized
  - [ ] do not depend on automatic live traffic warming

## What Stays In The Prestashop Module

- [ ] Site connection and authentication with the SaaS
- [ ] Merchant-facing dashboard tabs
- [ ] Feature toggles
- [ ] Local cache storage and serving
- [ ] Edge bootstrap serving through `index.php`
- [ ] Local critical CSS storage and injection
- [ ] Local minified asset storage if still needed
- [ ] Local page cache writer
- [ ] Local rollback / disable switches
- [ ] Local feature logs and diagnostics
- [ ] Safe anonymous cache eligibility rules
- [ ] Warming and beta generation triggers
- [ ] Pulling SaaS decisions and applying them
- [ ] Shop-aware and variant-aware cache key generation
- [ ] Fallback when SaaS is unavailable

## What Moves To The SaaS Server

- [ ] Website scan orchestration
- [ ] Baseline score recording
- [ ] Representative page discovery and grouping
- [ ] Critical CSS generation
- [ ] Font usage analysis
- [ ] CSS/JS/image analysis
- [ ] Page optimization decision engine
- [ ] HTML rewrite planning
- [ ] Compatibility knowledge base
- [ ] Per-theme / per-module heuristics
- [ ] Feature experiment tracking per page type
- [ ] Safety thresholds and rollback rules
- [ ] Page-by-page optimization history
- [ ] Reports and score deltas
- [ ] Team permissions and workspace management
- [ ] Subscription / quotas / usage accounting

## Server Endpoints

- [ ] `POST /sites/connect`
  - [ ] connect module to workspace
- [ ] `POST /sites/sync`
  - [ ] sync shops, languages, currencies, capabilities
- [ ] `POST /scan/site`
  - [ ] discover URLs and baseline score candidates
- [ ] `POST /scan/page`
  - [ ] run deep diagnostics on one page variant
- [ ] `POST /font-usage`
  - [ ] return font usage report
- [ ] `POST /critical-css`
  - [ ] return critical CSS variants
- [ ] `POST /page/optimize`
  - [ ] return optimized HTML + metadata
- [ ] `POST /page/plan`
  - [ ] compute recommended feature sequence before applying
- [ ] `POST /cache/generate`
  - [ ] trigger optimized cache generation plan
- [ ] `POST /rules/publish`
  - [ ] publish final asset/font/image rules to module
- [ ] `GET /reports/page`
  - [ ] page optimization results
- [ ] `GET /reports/site`
  - [ ] site-level score summary

## `/page/optimize` Responsibilities

- [ ] Accept:
  - [ ] URL
  - [ ] page type
  - [ ] device
  - [ ] language
  - [ ] currency
  - [ ] country
  - [ ] raw HTML
  - [ ] response headers
- [ ] Parse final HTML
- [ ] identify assets
- [ ] load saved decisions for that page type / variant
- [ ] generate or load critical CSS
- [ ] build CSS delivery plan
- [ ] build JS delivery plan
- [ ] apply image optimization rules
- [ ] apply font optimization rules
- [ ] rewrite the HTML
- [ ] return optimized HTML + metadata
- [ ] never run on live storefront request path

## No Auto Caching Strategy

- [ ] Disable reliance on random first visitor traffic for cache generation.
- [ ] Generate optimized cache explicitly from dashboard actions or server queue.
- [ ] Separate "page optimization" from "page serving".
- [ ] Keep automatic live caching optional, not required for correctness.
- [ ] Use proactive generation for:
  - [ ] homepage
  - [ ] top categories
  - [ ] top products
  - [ ] selected CMS pages

## Multi-Store Support

- [ ] Model one workspace with multiple Prestashop shops.
- [ ] Store separate shop identities:
  - [ ] `id_shop`
  - [ ] base URL
  - [ ] theme
  - [ ] language set
  - [ ] currency set
  - [ ] country behavior
- [ ] Keep reports scoped per shop.
- [ ] Keep rules scoped per shop.
- [ ] Keep cache generation scoped per shop.
- [ ] Keep connection tokens scoped safely.
- [ ] Detect whether host/path mapping changes page identity.

## Multi-Language / Variant Handling

- [ ] Track cache/page variants by:
  - [ ] shop
  - [ ] language
  - [ ] currency
  - [ ] country
  - [ ] device
- [ ] Do not assume one homepage variant.
- [ ] Generate critical CSS per device.
- [ ] Generate cache variants per language/currency/country/device where needed.
- [ ] Avoid warming impossible variant combinations.
- [ ] Reuse page-type grouping while storing per-variant results.

## Dashboard Permissions

- [ ] Workspace owner
- [ ] Admin
- [ ] Developer
- [ ] Analyst / read-only
- [ ] Merchant user
- [ ] Per-site access restrictions
- [ ] Per-shop access restrictions
- [ ] Action permissions:
  - [ ] connect/disconnect
  - [ ] publish rules
  - [ ] generate cache
  - [ ] purge cache
  - [ ] change billing
  - [ ] edit exclusions

## SaaS Application Concerns

- [ ] Authentication
- [ ] workspaces
- [ ] projects / sites
- [ ] shop records
- [ ] environment records:
  - [ ] production
  - [ ] staging
  - [ ] development
- [ ] audit logs
- [ ] billing and plan limits
- [ ] domain verification
- [ ] connection tokens rotation
- [ ] job queues
- [ ] retry handling
- [ ] rate limiting
- [ ] report storage
- [ ] artifact storage:
  - [ ] HTML snapshots
  - [ ] critical CSS
  - [ ] screenshots
  - [ ] scan JSON
- [ ] release channels:
  - [ ] stable
  - [ ] beta
  - [ ] experimental

## Safety / Rollback

- [ ] Every feature must be independently reversible.
- [ ] Store previous working optimization state.
- [ ] Allow rollback by:
  - [ ] page
  - [ ] page type
  - [ ] shop
  - [ ] whole site
- [ ] Reject critical CSS by min/max thresholds.
- [ ] Add visual diff validation later.
- [ ] Disable caching for logged-in/cart/checkout flows by default.
- [ ] Keep a "safe mode" profile for problematic sites.

## Reporting

- [ ] Baseline score before any optimization
- [ ] score after each feature step
- [ ] page-level improvement history
- [ ] CSS bytes before/after
- [ ] JS bytes before/after
- [ ] image bytes before/after
- [ ] cache hit rate
- [ ] critical CSS acceptance/rejection reasons
- [ ] feature impact timeline

## Beta Roadmap

- [ ] Phase 1: existing module features work reliably locally
- [ ] Phase 2: beta cache generating tab works per URL and per variant
- [ ] Phase 3: SaaS scan endpoints own diagnostics
- [ ] Phase 4: SaaS critical CSS + font usage are primary
- [ ] Phase 5: SaaS `/page/optimize` becomes the source of truth
- [ ] Phase 6: module becomes mainly a connector/cache-serving layer
- [ ] Phase 7: production-safe rollout and permissions

## Immediate Next Tasks

- [ ] Finalize critical CSS generation quality
- [ ] Reduce Google Fonts payload
- [ ] Deduplicate icon-font stylesheets
- [ ] Remove or gate Charla on homepage/mobile
- [ ] Make CSS defer strategy safe for large Prestashop CSS
- [ ] Test beta cache generation page by page
- [ ] Define `/page/optimize` request/response contract
- [ ] Add site/workspace connection model on the SaaS side
