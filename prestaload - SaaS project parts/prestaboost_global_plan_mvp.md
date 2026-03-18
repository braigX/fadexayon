# PrestaBoost — Global SaaS Plan (MVP → Optimisation) Checklist

> Target: a low-friction Prestashop optimisation SaaS where the merchant installs the module, your platform scans performance, and **images are optimised via Cloudflare + optimizer service** with safe version-based invalidation.

---

## Phase 1 — Foundations (already in place / verify)

### 1) Prestashop module (client side)
- [x] Store connect/disconnect implemented (signed requests)
- [x] Canonical URL discovery implemented (home/category/product/CMS + sensitive routes)
- [ ] Module can fetch config from SaaS (`GET /config`)
- [ ] Module can send actions to SaaS (purge/version bump, heartbeat)

### 2) Control plane (Laravel API + React dashboard)
- [ ] Stores + keys + settings/exclusions management in dashboard
- [x] Laravel API endpoints ready for module + dashboard actions
- [x] URL inventory stored in DB (`store_urls`)

### 2.1) Optimisation settings tabs (work one by one)
- [ ] `Image` tab
  - [x] Image optimisation toggles persisted in API
  - [x] Image tab UI implemented in dashboard
  - [ ] Module/runtime fully enforces all image settings
- [ ] `CSS` tab
  - [x] CSS optimisation toggles persisted in API
  - [x] CSS tab UI implemented in dashboard
  - [ ] Module/runtime fully enforces all CSS settings
- [ ] `JS` tab
  - [x] JS optimisation toggles persisted in API
  - [x] JS tab UI implemented in dashboard
  - [ ] Module/runtime fully enforces all JS settings
- [ ] `Others` tab
  - [x] Other optimisation toggles persisted in API
  - [x] Others tab UI implemented in dashboard
  - [ ] Module/runtime fully enforces all Other settings

### 3) Scanner stack (local scanner service for now)
- [x] Scanner service runs locally from `prestaloader/scanner`
- [x] Scanner runs Playwright + Lighthouse
- [x] Scanner returns JSON summary for pages (mobile/desktop)

---

## Phase 2 — Make Laravel the “Brain” (jobs + results + actions)

### 1) Job lifecycle & persistence (MySQL)
- [ ] Add scan run grouping (`scan_runs`)
- [x] Add per-URL job tracking (`page_scan_jobs`) with statuses (queued/running/done/failed/timeout/excluded)
- [x] Store results for both devices (`page_scan_device_results`: mobile + desktop)
- [x] Store raw JSON payload in DB (MVP) + extract key metrics columns for fast UI

### 2) Worker ↔ Laravel contract
- [x] Laravel dispatches scan jobs and scanner returns results to Laravel over HTTP
- [x] Dashboard reads only from Laravel DB (no scanner report directory dependency)

### 3) Reporting baseline
- [x] Dashboard shows baseline scores (mobile + desktop)
- [x] Dashboard shows payload sizes (CSS/JS/IMG + requests)
- [ ] Dashboard shows top failing audits/checklist items per store

---

## Phase 3 — Optimisation MVP (Images first, low risk, high ROI)

## Best MVP path (confirmed)
- [ ] **Optimizer service**: imgproxy **or** Image Optimize (Docker)
- [ ] **Cloudflare** in front as the actual CDN cache (your domain: Mode A)
- [ ] **Prestashop module rewrites image URLs** to your CDN domain
- [ ] **Laravel controls**: allowlists, URL signing, versions (purge by version bump)

### 1) Deploy optimizer service
- [ ] Choose optimizer: [ ] imgproxy  [ ] Image Optimize
- [ ] Deploy on VPS (Docker)
- [ ] Add health endpoint checks
- [ ] Enforce strict origin pull:
  - [ ] Only allow fetching from the connected store domain(s)
  - [ ] Timeouts + max file size limits

### 2) Configure Cloudflare (Mode A — no merchant DNS changes)
- [ ] Add your zone to Cloudflare (e.g. `yourdomain.com`)
- [ ] Create `cdn.yourdomain.com` DNS record to optimizer origin (proxy ON)
- [ ] SSL/TLS configured (Full/Strict recommended)
- [ ] Create cache rules for image endpoints (long TTL / cache everything for image routes)

### 3) Implement signing + allowlists in Laravel
- [ ] Store-level allowed origins/domains (per store)
- [ ] Store-level signing secret (rotate support)
- [ ] Implement URL signing rules (prevent open-proxy abuse)
- [ ] Implement request validation on optimizer inputs (storeId, version, width, format)

### 4) Add versioning for purge (preferred invalidation)
- [ ] Add `store_versions` table:
  - [ ] `img_version` integer
  - [ ] (later) `asset_version` integer
  - [ ] `config_version` integer
- [ ] Add endpoint/action to bump `img_version`:
  - [ ] triggered by module hooks (product image updates)
  - [ ] triggered by dashboard “Clear image cache”
- [ ] Store audit log for version bumps

### 5) Module image rewrite integration
- [ ] Rewrite `<img src>`/`srcset`/background images to `https://cdn.yourdomain.com/...`
- [ ] Include required parameters (storeId + img_version + width/format)
- [ ] Include signature token
- [ ] Safety rules:
  - [ ] Do not rewrite sensitive pages unless explicitly allowed (cart/checkout/account)
  - [ ] Do not lazy-load the LCP/hero image
  - [ ] Keep fail-open behavior (if CDN fails, fall back to origin)

### 6) Validate optimisation impact (scan loop)
- [x] Baseline scan stored (before optimisation)
- [ ] Enable image CDN rewrite for store
- [ ] Rescan sample URLs (mobile + desktop)
- [ ] Dashboard shows before/after deltas for:
  - [ ] LCP
  - [ ] total transfer bytes
  - [ ] image bytes
  - [ ] performance score

---

## Phase 4 — Safe HTML assists (optional after images are stable)

- [ ] Add width/height where missing (reduce CLS)
- [ ] Add lazyload below-the-fold images/iframes
- [ ] Add `preconnect` / `dns-prefetch` for `cdn.yourdomain.com`
- [ ] Add exclusion controls per URL pattern

---

## Phase 5 — CSS/JS optimisation (later, careful)

- [ ] Start with minify-only (no bundling) + long caching
- [ ] Exclusions for theme/payment/checkout scripts
- [ ] Generating critical CSS (do later, after the image pipeline and rollback safety are stable)
- [ ] Rollback / safe-mode toggle if errors spike
- [ ] Rescan + compare results

---

## Phase 6 — Advanced (only after rollback/exclusions are mature)

- [ ] Critical CSS per page type (home/category/product/CMS)
- [ ] Delay non-critical 3rd-party scripts (allowlist model)
- [ ] Automated regression detection (scan diffs + error spikes)

---

## Done criteria (MVP)
- [x] Merchant connects store → URLs discovered and stored
- [x] Baseline scan (mobile+desktop) available in dashboard
- [ ] Image optimisation enabled:
  - [ ] images served via `cdn.yourdomain.com` with caching
  - [ ] safe version bump invalidation working
- [ ] Rescan shows measurable improvements and history is stored
