# PrestaLoad Module Plan

## Direction

The app is being rebuilt around a small core plus feature modules.

Core stays responsible for:
- auth
- users
- workspaces
- permissions
- integrations
- stores
- shops
- discovered URLs
- notifications
- billing later

Feature logic moves into modules.

## Module Layers

Each module should follow the same structure:
- `Application`
- `Domain`
- `Infrastructure`
- `Http`
- `Database`
- `Console`
- `Providers`

Suggested root:
- `api/app/Modules`

## Global Module

The `Global` module should be infrastructure only.

It should contain:
- module registry
- module enable/disable state
- shared settings framework
- shared migrations loader
- shared commands base classes
- shared queue helpers
- shared storage helpers
- shared artifact publishing helpers
- shared logging/diagnostics helpers
- shared rule override support

It should not own business features.

## Core App

The active app outside modules should keep:
- `Auth`
- `Workspace`
- `Integrations`
- `Cloudflare`
- `PrestaBoost`
- `Store / Shop / URL discovery`

These remain the foundation for all later modules.

## Required Feature Modules

### 1. PageOptimization

Purpose:
- orchestrate one optimization run for one URL
- coordinate enabled modules
- manage run lifecycle
- publish final cacheable output

Responsibilities:
- run creation
- step execution
- queue jobs
- progress tracking
- final publish trigger
- failure handling

### 2. HtmlOptimization

Purpose:
- optimize final HTML output safely

Responsibilities:
- HTML minify/compress
- DOM cleanup
- preload/preconnect injection
- lazy-loading attributes
- link/script rewriting hooks
- final cache-ready HTML generation

### 3. CssOptimization

Purpose:
- fix CSS delivery and render-blocking issues

Responsibilities:
- CSS collection
- coverage ingestion
- critical CSS
- used CSS
- CSS rules
- inline CSS rules
- minify/reduce/preload/remove logic
- bundle generation
- generated CSS asset publishing

### 4. JavascriptOptimization

Purpose:
- reduce JavaScript cost and delay non-critical execution

Responsibilities:
- JS audit ingestion
- JS rules
- inline JS handling
- minify/defer/delay/load-on-interaction/remove logic
- third-party JS control
- generated JS asset publishing

### 5. FontOptimization

Purpose:
- solve common font-loading issues safely

Responsibilities:
- font usage detection
- Google Fonts dedupe
- self-hosting
- font-display fixes
- critical font preload
- duplicate icon font cleanup
- rewritten font CSS publishing

### 6. ImageOptimization

Purpose:
- solve image-related PageSpeed issues

Responsibilities:
- image CDN / imgproxy integration
- next-gen formats
- responsive variants
- width/height fixes
- lazy-loading rules
- above-the-fold prioritization
- background-image handling

### 7. CacheOptimization

Purpose:
- manage cache strategy and invalidation

Responsibilities:
- cache TTL rules
- purge by URL / shop / store
- variation strategy
- cache publish rules
- stale-while-revalidate later

### 8. PerformanceScanning

Purpose:
- collect evidence from scanners and external audits

Responsibilities:
- local scanner integration
- PageSpeed integration
- normalized audit output
- per-page evidence
- later per-page-type evidence

### 9. Validation

Purpose:
- block broken publishes

Responsibilities:
- visual diff
- HTML correctness checks
- asset-load verification
- broken-page detection
- rollback or block publish on failure

### 10. RulesEngine

Purpose:
- centralize decision logic

Responsibilities:
- evidence-to-action decisions
- default rules
- user override resolution
- effective action resolution
- shared decision framework for CSS / JS / fonts / images

### 11. Artifacts

Purpose:
- manage generated files consistently

Responsibilities:
- generated asset storage
- public publishing
- asset URL generation
- bundle manifests
- cleanup
- versioning

### 12. Reporting

Purpose:
- expose optimization results clearly

Responsibilities:
- summary dashboards
- before/after stats
- issue summaries
- module-specific report pages
- recommendations

## Recommended Build Order

### Phase 1

- `Global`
- `PageOptimization`
- `HtmlOptimization`

Goal:
- one clean optimization pipeline
- one clean final HTML output
- no asset-specific intelligence yet

### Phase 2

- `PerformanceScanning`
- `Validation`
- `Artifacts`

Goal:
- safe evidence collection
- safe publish rules
- clean generated file handling

### Phase 3

- `CssOptimization`
- `JavascriptOptimization`
- `FontOptimization`

Goal:
- fix most major PageSpeed asset issues

### Phase 4

- `ImageOptimization`
- `CacheOptimization`
- `Reporting`

Goal:
- cover the biggest remaining storefront performance issues
- expose useful reporting in dashboard

### Phase 5

- `RulesEngine`

Goal:
- centralize all evidence-based decisions
- support admin overrides cleanly

## Database Ownership

### Core Database

Core should keep owning:
- users
- workspaces
- workspace access
- integrations
- stores
- shops
- discovered URLs
- notifications

### Global Database

Global should own only shared infrastructure tables, for example:
- module registry
- module settings
- module enablement
- shared artifact registry if needed globally

### Feature Database

Each feature module should own its own tables.

Examples:

`PageOptimization`
- optimization runs
- optimization run steps or `steps_json`
- publish results

`CssOptimization`
- CSS evidence
- CSS rules
- CSS generated assets

`JavascriptOptimization`
- JS evidence
- JS rules
- JS generated assets

`FontOptimization`
- font evidence
- font rules
- rewritten font assets

`ImageOptimization`
- image rules
- image versions
- image delivery metadata

`CacheOptimization`
- cache publish versions
- purge logs
- TTL policies

## Frontend Module Direction

The frontend should also move to feature modules.

Suggested root:
- `web/src/modules`

Each frontend module should own:
- pages
- views
- components
- hooks
- api client
- utils

The app shell should keep only:
- routing
- nav
- auth shell
- layout shell
- workspace/store selection context

## Minimum Serious Product Scope

To make the app general and solve most important PageSpeed issues, the minimum serious module set is:
- `Global`
- `PageOptimization`
- `HtmlOptimization`
- `CssOptimization`
- `JavascriptOptimization`
- `FontOptimization`
- `ImageOptimization`
- `PerformanceScanning`
- `Validation`
- `CacheOptimization`

## Constraints

- `Global` must stay small
- feature modules must own their own logic
- optimization should run through one orchestrator
- each module must be independently enabled/disabled
- shared core data should not be duplicated into modules unless necessary
- scanner evidence should guide decisions, not replace validation

## Next Step

Build the first new module set in this order:
1. `Global`
2. `PageOptimization`
3. `HtmlOptimization`

After that:
4. `PerformanceScanning`
5. `Validation`
6. `Artifacts`
7. `CssOptimization`
8. `JavascriptOptimization`
9. `FontOptimization`
10. `ImageOptimization`
11. `CacheOptimization`
12. `Reporting`
