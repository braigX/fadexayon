# PrestaLoad Performance Plan

Source audit: `modules/prestaload/plexi.local.test-20260314T182958.json`  
Fetched at: `2026-03-14T18:29:58.333Z`  
Target URL: `https://plexi.local.test/`  
Lighthouse performance score: `55`

## Plexi Errors

- [ ] Reduce initial server response time for the homepage. Lighthouse measured the root document at `9.7 s` TTFB (`server-response-time`, `document-latency-insight`).
- [ ] Improve the homepage rendering path. Current metrics are `FCP 1.8 s`, `LCP 2.2 s`, `TTI 4.3 s`, `TBT 410 ms`, `Max Potential FID 230 ms`, and `Speed Index 8.2 s`.
- [ ] Reduce main-thread work on local assets and page code. Lighthouse reports `3.8 s` main-thread work, with heavy script evaluation and layout (`mainthread-work-breakdown`).
- [ ] Reduce JavaScript execution cost from Plexi-owned files, especially `themes/core.js`, `themes/modez/assets/js/theme.js`, and `js/jquery/ui/jquery-ui.min.js` (`bootup-time`, `unused-javascript`, `forced-reflow-insight`).
- [ ] Remove or defer unused JavaScript from Plexi assets. Largest Plexi offenders include `themes/modez/assets/js/theme.js` and `js/jquery/ui/jquery-ui.min.js` (`unused-javascript`).
- [ ] Remove or split unused CSS from Plexi assets. Largest offenders include:
  - `themes/modez/assets/css/theme.css`
  - `themes/modez/assets/css/custom.css`
  - `modules/crazyelements/assets/css/frontend.min.css`
  - `modules/crazyelements/assets/css/editor-preview.min.css`
  - `modules/crazyelements/assets/lib/font-awesome/css/fontawesome.min.css`
- [ ] Minify Plexi CSS that is still shipped unminified, especially:
  - `themes/modez/assets/css/custom.css`
  - `modules/roy_customizer/css/rt_customizer_1.css`
  - `themes/modez/modules/ets_megamenu/views/css/megamenu.css`
- [ ] Review JavaScript minification coverage. Lighthouse still reports `121 KiB` potential JavaScript savings (`unminified-javascript`).
- [ ] Remove render-blocking CSS from the homepage critical path, especially:
  - `modules/crazyelements/assets/css/frontend/css/post-page-25-1-1.css`
  - `modules/crazyelements/assets/css/frontend/css/post-page-2-1-1.css`
  - `modules/idxrcustomproduct/views/css/17/front_accordion.css`
  - `modules/idxrcustomproduct/views/css/17/front.css`
- [ ] Fix forced reflows coming from `themes/core.js`, `themes/modez/assets/js/theme.js`, `themes/modez/assets/js/custom.js`, and `modules/crazyelements/assets/js/frontend.min.js` (`forced-reflow-insight`).
- [ ] Improve image delivery for Plexi-owned media and same-brand media currently used on the page. Top flagged images include:
  - `www.plexi-cindar.com/img/cms/plexiglass-sur-mesure.webp`
  - `www.plexi-cindar.com/img/cms/plexiglas sur mesure avec machine laser.webp`
  - `www.plexi-cindar.com/img/cms/plexiglass.webp`
  - `www.plexi-cindar.com/img/cms/polyester.webp`
- [ ] Add explicit `width` and `height` attributes to flagged images. Lighthouse found `11` unsized images, including SVG feature icons, product-category visuals, and the local header icon `themes/modez/assets/img/user.svg` where `with=\"32\"` is misspelled instead of `width=\"32\"` (`unsized-images`).
- [ ] Reduce total page weight. The page currently transfers `2,724 KiB`, with notable Plexi-owned payloads including:
  - `img/cms/commandez-plaque-de-plexiglas.mp4`
  - `themes/modez/assets/js/theme.js`
  - large CMS images served from `www.plexi-cindar.com`
- [ ] Add efficient cache lifetimes for Plexi-owned assets. Lighthouse reports `1,559 KiB` potential savings and shows zero cache lifetime on assets such as:
  - `img/cms/commandez-plaque-de-plexiglas.mp4`
  - `themes/modez/assets/js/theme.js`
  - `js/jquery/ui/jquery-ui.min.js`
  - `modules/roy_customizer/upload/logo-plexi.svg`
  - local Font Awesome webfonts from `modules/crazyelements`
- [ ] Review homepage response headers for back/forward cache compatibility. The page is blocked by `Cache-Control: no-store` on the main resource and JS-network-related responses (`bf-cache`).
- [ ] Move local delivery away from `http/1.1` where possible. Lighthouse flagged the document and multiple local CSS assets as non-modern delivery (`modern-http-insight`).
- [ ] Investigate the LCP path. Lighthouse identifies the homepage `<h1>` as the LCP element and attributes most delay to `TTFB 9710 ms` plus `element render delay 2568 ms` (`lcp-breakdown-insight`).

## Outside Plexi Errors

- [ ] Remove audit noise from browser extensions before using results as a baseline. Lighthouse captured `chrome-extension://bnjjngeaknajbdcgpfkgnonkmififhfo/src/content-script.js` in the report.
- [ ] Re-run with a fully clean browser profile if possible. The report still contains the warning: `There may be stored data affecting loading performance in this location: IndexedDB.`
- [ ] Reduce or delay the Charla chat widget. It is one of the heaviest third-party offenders:
  - `https://app.getcharla.com/widget/widget.js`
  - also opens WebSocket-related behavior that hurts `bf-cache`
- [ ] Reduce Google Tag Manager / Google Ads script cost. Lighthouse flags:
  - `https://www.googletagmanager.com/gtag/js?id=G-QLZ7D75TBJ`
  - `https://www.googletagmanager.com/gtag/js?id=AW-996312213`
  - `https://www.googletagmanager.com/gtag/js?id=AW-996312213&cx=c&gtm=4e63b1`
- [ ] Reduce Google Sign-In / Google API payload on the homepage:
  - `https://accounts.google.com/gsi/client`
- [ ] Reduce Google Fonts render-blocking and font-display impact from:
  - `fonts.googleapis.com`
  - `fonts.gstatic.com`
- [ ] Review whether externally hosted same-brand assets on `www.plexi-cindar.com` should be considered third-party for this environment, because they add cross-origin requests and are flagged separately from `plexi.local.test`.

