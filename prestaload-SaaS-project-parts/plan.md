# Full Optimized HTML Cache Plan for PrestaShop SaaS

## Objective

Build a production-safe optimization pipeline that turns synced store URLs into fully optimized HTML artifacts with:

- optimized HTML
- inlined critical CSS
- reduced non-critical CSS
- fallback CSS
- deferred/lazy JS strategy
- rewritten asset references
- validation and rollback support

The Laravel application acts as the orchestrator and dashboard. Workers execute specialized processing stages.

---

# 1. Target Architecture

## Core roles

### Laravel Orchestrator
Responsible for:

- shop registration and authentication
- synced URL management
- variant key generation
- job dispatching
- artifact metadata storage
- retry and failure classification
- publish/rollback state
- dashboard and reporting

### Worker Pool A — Fetch and Classify
Responsible for:

- requesting the storefront page
- storing raw HTML and headers
- resolving redirects/canonical URL
- detecting page family

### Worker Pool B — Render and Trace
Responsible for:

- loading the page in a real browser
- waiting for stable rendering
- simulating interactions
- collecting CSS coverage
- capturing DOM snapshot and screenshot
- collecting diagnostics

### Worker Pool C — Build and Assemble
Responsible for:

- CSS reduction
- critical CSS generation
- used CSS generation
- fallback CSS generation
- JS loading strategy generation
- final optimized HTML assembly

### Worker Pool D — Validate and Publish
Responsible for:

- comparing original and optimized outputs
- screenshot/DOM sanity checks
- page-specific required element checks
- publish decision
- version promotion and rollback preservation

---

# 2. Optimization Pipeline

## Stage 1 — Preparation
Laravel creates an optimization run and resolves the exact page variant.

### Inputs
- shop ID
- URL
- language
- currency
- device class
- login state
- customer group if relevant
- theme hash
- optimization config version

### Output
- optimization run record
- page variant key
- queued jobs

---

## Stage 2 — Fetch and Classify
Worker Pool A performs:

- fetch page HTML
- capture response headers
- follow redirects
- detect final normalized URL
- detect page family:
  - home
  - product
  - category
  - cart
  - CMS
  - listing/search

### Output artifacts
- raw HTML
- headers metadata
- normalized final URL
- page family

---

## Stage 3 — Render and Trace
Worker Pool B performs:

- open page in Chromium/Playwright/Puppeteer
- wait for network idle and render stability
- simulate important interactions:
  - mobile menu
  - accordion open
  - tabs switch
  - image gallery click
  - quick-view if supported
  - add-to-cart modal if safely testable
- capture CSS coverage
- capture final DOM snapshot
- capture screenshot
- collect console errors and diagnostics

### Output artifacts
- DOM snapshot
- CSS coverage report
- JS inventory
- screenshot
- console log
- render diagnostics

---

## Stage 4 — CSS Optimization
Worker Pool C performs:

- parse source CSS files
- map coverage to CSS rules
- preserve required dependencies:
  - @media
  - @supports
  - @keyframes
  - @font-face
  - CSS variables/custom properties
- apply theme/module safelists
- generate:
  - critical CSS
  - used non-critical CSS
  - fallback CSS

### Output artifacts
- critical CSS
- used CSS
- fallback CSS
- CSS optimization report

---

## Stage 5 — JS Strategy
Worker Pool C performs:

- inventory script tags and assets
- classify scripts:
  - render-critical
  - interactive after paint
  - third-party
  - lazy/delay candidates
- generate a safe loading strategy:
  - keep essential scripts
  - defer safe scripts
  - delay non-critical third-party scripts
  - lazy-init below-the-fold features where applicable

### Output artifacts
- JS manifest
- delayed/deferred strategy rules
- script classification report

---

## Stage 6 — HTML Assembly
Worker Pool C performs:

- inline critical CSS into HTML head
- link reduced CSS
- attach fallback CSS strategy
- rewrite script loading according to JS manifest
- add preloads if useful
- normalize final HTML output
- create final artifact manifest

### Output artifacts
- final optimized HTML
- artifact manifest JSON
- asset reference map

---

## Stage 7 — Validation and QA
Worker Pool D performs:

- compare original and optimized versions
- validate HTML/DOM sanity
- compare screenshots
- verify required elements exist
- evaluate console errors
- assign QA status:
  - pass
  - pass with warning
  - reject

### Minimum checks
- page title present
- H1 preserved if original had one
- product price block exists on product page
- add-to-cart exists on product page
- main image exists on product page
- category grid exists on category page
- no fatal console error threshold exceeded
- screenshot mismatch below threshold

### Output artifacts
- QA report
- validation score
- publish decision

---

## Stage 8 — Publish
Worker Pool D performs:

- store approved artifact in artifact storage
- activate new version
- preserve previous version for rollback
- update Laravel state
- mark page variant as publish-ready

### Output artifacts
- published artifact version
- rollback version pointer
- publish log

---

# 3. Worker Deployment Plan

## MVP deployment model

### Server 1 — Control Plane
Host:
- Laravel
- dashboard
- database
- Redis / queue broker

### Server 2 — Browser Workers
Host:
- Node.js
- Chromium/Playwright/Puppeteer
- render/trace jobs

### Server 3 — Build + QA Workers and Artifact Origin
Host:
- CSS/JS build jobs
- validation/publish jobs
- object storage gateway or artifact serving origin

## Scaling model later
Split into:

- control plane instances
- browser worker pool
- build worker pool
- QA/publish worker pool
- object storage
- CDN / edge delivery layer

---

# 4. Variant Key Rules

A page must never be cached only by URL.

## Minimum variant key fields
- shop ID
- normalized URL or route
- language
- currency
- device class
- login state
- customer group if relevant
- theme hash
- optimization config version

## Optional variant fields
- geo/tax region if output differs
- AB test bucket if merchant uses experiments
- specific theme/module runtime flags

---

# 5. Artifact Storage Structure

## Recommended artifact components
- raw HTML snapshot
- DOM snapshot
- screenshot
- CSS coverage JSON
- critical CSS file
- used CSS file
- fallback CSS file
- JS manifest JSON
- final optimized HTML
- final artifact manifest JSON
- QA report JSON

## Storage requirements
- versioned
- content-hash aware
- rollback-safe
- independent from worker local disk

---

# 6. Laravel Job Chain

## Proposed job flow

1. `CreateOptimizationRunJob`
2. `FetchPageJob`
3. `ClassifyPageJob`
4. `RenderAndTraceJob`
5. `BuildCssArtifactsJob`
6. `BuildJsStrategyJob`
7. `AssembleHtmlArtifactJob`
8. `ValidateArtifactJob`
9. `PublishArtifactJob`

## Run states
- queued
- fetching
- rendering
- building
- validating
- publishing
- published
- failed
- rejected
- rolled_back

---

# 7. Page Profiles

Optimization should vary by page family.

## Homepage profile
Focus on:
- hero area
- navigation
- homepage blocks
- sliders
- banners

## Product page profile
Focus on:
- product gallery
- price block
- add-to-cart
- combinations/variants
- tabs
- reviews/widgets

## Category page profile
Focus on:
- filters
- product grid
- sorting
- pagination
- lazy-loaded cards

## CMS page profile
Focus on:
- content body
- article media
- banners
- embeds/widgets

---

# 8. Error Strategy

## Retry categories

### Transient
Retry allowed:
- network timeout
- temporary 5xx
- browser crash
- storage unavailability

### Deterministic
Usually no retry:
- CSS parse failure from invalid source
- unsupported DOM pattern
- required selector not found in deterministic way
- incompatible theme script behavior

### Merchant-side issues
Flag for dashboard:
- blocked bot/browser access
- anti-bot wall
- login/protection issue
- malformed page output
- unstable theme/plugin behavior

---

# 9. Rollout Strategy

## Phase 1
Support only:
- homepage
- product pages
- category pages

## Phase 2
Add:
- CMS pages
- search/listing pages
- cart page if safe

## Phase 3
Add:
- theme adapters
- advanced JS delay rules
- stronger validation
- rollback automation
- edge delivery enhancements

---

# 10. Production Safety Principles

- Never cache only by URL
- Never publish without validation
- Always keep native fallback path
- Always preserve previous artifact version
- Never rely only on static CSS purging
- Keep workers stateless where possible
- Store artifacts outside worker local disk
- Separate browser rendering from Laravel API infrastructure

---

# 11. Build Order Recommendation

## Step 1
Implement:
- optimization run records
- variant key logic
- job orchestration in Laravel

## Step 2
Implement Worker Pool A:
- fetch page
- classify page family

## Step 3
Implement Worker Pool B:
- render page
- collect coverage
- simulate interactions
- capture DOM and screenshot

## Step 4
Implement Worker Pool C:
- build critical/used/fallback CSS
- build JS strategy
- assemble optimized HTML

## Step 5
Implement Worker Pool D:
- validate artifact
- publish artifact
- preserve rollback version

## Step 6
Add dashboard observability:
- run status
- failure reason
- publish history
- rollback history
- cache hit/miss metrics
- optimization duration

---

# 12. Implementation Checklist

## Foundation
- [ ] Define optimization run model in Laravel
- [ ] Define page variant key logic
- [ ] Define artifact versioning strategy
- [ ] Define storage paths and naming conventions
- [ ] Define run states and failure categories
- [ ] Add queue orchestration in Laravel

## Worker Pool A — Fetch and Classify
- [ ] Implement page fetch worker
- [ ] Capture raw HTML and headers
- [ ] Normalize final URL
- [ ] Detect page family
- [ ] Save fetch metadata
- [ ] Add retry logic for transient failures

## Worker Pool B — Render and Trace
- [ ] Set up headless browser worker
- [ ] Load page with stable wait rules
- [ ] Capture DOM snapshot
- [ ] Capture screenshot
- [ ] Collect CSS coverage
- [ ] Collect JS/script inventory
- [ ] Collect console errors
- [ ] Simulate core interactions
- [ ] Save render diagnostics

## Worker Pool C — Build and Assemble
- [ ] Parse source CSS assets
- [ ] Build critical CSS
- [ ] Build used CSS
- [ ] Build fallback CSS
- [ ] Add theme/module safelists
- [ ] Inventory scripts
- [ ] Define defer/delay strategy
- [ ] Rewrite script loading
- [ ] Assemble final optimized HTML
- [ ] Create artifact manifest

## Worker Pool D — Validate and Publish
- [ ] Compare original and optimized output
- [ ] Add screenshot similarity checks
- [ ] Add required-element checks per page family
- [ ] Add console error threshold checks
- [ ] Produce QA report
- [ ] Block publish on reject
- [ ] Publish approved artifact
- [ ] Preserve previous version for rollback

## Storage and Delivery
- [ ] Choose artifact storage backend
- [ ] Store all generated artifacts by version/hash
- [ ] Make artifacts accessible to delivery layer
- [ ] Add immutable caching for versioned assets
- [ ] Add rollback pointer support

## Dashboard and Observability
- [ ] Show run status timeline
- [ ] Show worker failure reasons
- [ ] Show validation score and QA result
- [ ] Show published artifact history
- [ ] Show rollback history
- [ ] Show optimization duration by stage
- [ ] Show cache hit/miss metrics later

## Rollout
- [ ] Launch homepage optimization
- [ ] Launch product page optimization
- [ ] Launch category page optimization
- [ ] Add CMS pages later
- [ ] Add cart/search pages after stability checks

---

# 13. MVP Scope Recommendation

For the first production-capable release, use these deployable pools:

- Pool A: Fetch and classify
- Pool B: Render and trace
- Pool C: Build CSS/JS and assemble HTML
- Pool D: Validate and publish

This is the best balance between professional architecture and manageable complexity.

---

# 14. Final Decision

The system should be built as:

- Laravel orchestrator as the control plane
- specialized worker pools by workload type
- versioned artifact storage independent from worker local disk
- validation gate before publish
- rollback-safe publishing model
- strict page variant awareness

This is production-feasible for many PrestaShop merchants and is the right foundation for a full optimized HTML cache SaaS.
