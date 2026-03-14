# PrestaLoad Performance Plan

Source audit: `modules/prestaload/plexi.local.test-20260314T180057.json`  
Fetched at: `2026-03-14T18:00:57.632Z`  
Target URL: `https://plexi.local.test/`

## Audit Reliability

- [ ] Re-run Lighthouse in a clean incognito session because the current report is invalidated by `runtimeError.code = NO_FCP`.
- [ ] Verify why the homepage failed to paint any content during the audit before trusting FCP, LCP, TBT, CLS, Speed Index, or TTI.
- [ ] Confirm whether the failure is caused by a PHP fatal error, a stalled backend response, a redirect loop, blocking JavaScript, or browser-only local environment behavior.
- [ ] Capture the next run with browser console errors, PHP logs, and network waterfall alongside Lighthouse so future fixes are evidence-based.

## Server And Document Delivery

- [ ] Check for homepage redirect chains because Lighthouse flagged `redirects`, but the current run could not measure the chain due to `NO_FCP`.
- [ ] Reduce main document latency and backend processing time for the homepage request (`document-latency-insight`, `server-response-time`, `network-server-latency`).
- [ ] Review cache headers and TTL strategy for HTML, CSS, JS, fonts, and images (`cache-insight`).
- [ ] Confirm HTTP delivery path is modern and efficient for the local stack and production stack (`modern-http-insight`).

## CSS, JavaScript, And Main Thread

- [ ] Remove render-blocking CSS and JavaScript from the initial homepage render (`render-blocking-insight`).
- [ ] Reduce unused CSS shipped to the homepage (`unused-css-rules`).
- [ ] Reduce unused JavaScript and delay non-critical bundles (`unused-javascript`).
- [ ] Minify CSS assets that are still served unminified (`unminified-css`).
- [ ] Minify JavaScript assets that are still served unminified (`unminified-javascript`).
- [ ] Cut JavaScript boot time and execution cost on the main thread (`bootup-time`, `mainthread-work-breakdown`, `long-tasks`).
- [ ] Remove duplicated JavaScript payloads and overlapping libraries (`duplicated-javascript-insight`).
- [ ] Eliminate legacy JavaScript sent to modern browsers (`legacy-javascript-insight`).
- [ ] Investigate forced synchronous layouts and reflows triggered during page startup (`forced-reflow-insight`).

## Images, Fonts, And Layout Stability

- [ ] Improve image delivery strategy for the homepage hero, category, and product imagery (`image-delivery-insight`).
- [ ] Add explicit `width` and `height` attributes to rendered images to reduce CLS (`unsized-images`).
- [ ] Identify and fix the largest layout shift culprits (`cls-culprits-insight`, `layout-shifts`).
- [ ] Ensure the LCP element is discoverable early and not lazy-loaded incorrectly (`lcp-breakdown-insight`, `lcp-discovery-insight`).
- [ ] Use `font-display` and audit webfont loading behavior (`font-display-insight`).
- [ ] Remove non-composited animations that can increase jank and layout instability (`non-composited-animations`).

## Page Structure And Third Parties

- [ ] Reduce DOM size on the homepage if builders, sliders, or mega menus are inflating the initial render tree (`dom-size-insight`).
- [ ] Audit third-party scripts, widgets, trackers, and embeds for startup cost (`third-parties-insight`).
- [ ] Improve back/forward cache compatibility to speed up repeat navigations (`bf-cache`).
- [ ] Validate mobile viewport configuration and front theme behavior on small screens (`viewport-insight`).

## Instrumentation And Verification

- [ ] Add repeatable before/after benchmarks for homepage TTFB, HTML size, total transferred bytes, JS execution time, and CLS once the page paints again.
- [ ] Add User Timing markers around critical Prestashop render phases if deeper profiling is needed (`user-timings`).
- [ ] After each optimization batch, re-run Lighthouse and keep the raw JSON beside this plan for traceability.

## Module Execution Track

- [x] Create an installable Prestashop module skeleton in `modules/prestaload`.
- [ ] Add asset-governance logic inside the module to disable or defer non-critical front-office assets.
- [ ] Add HTML post-processing only if targeted fixes cannot be achieved at the theme or module source level.
- [ ] Add safe configuration toggles so each optimization can be enabled progressively and rolled back independently.
- [ ] Document every implemented optimization in this file as items move from unchecked to checked.
