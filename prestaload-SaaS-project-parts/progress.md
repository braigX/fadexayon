# Progress

## Laravel API App

- Rebuilt optimization flow around:
  - `cachevariants`
  - `cacheprepare`
  - browser render
  - CSS analysis
  - critical CSS generation
  - CSS delivery settings
  - validation
  - `cachepublish`
- Added optimization tables:
  - targets
  - runs
  - steps
  - artifact versions
- Added CSS report tables:
  - optimization CSS reports
  - optimization CSS report stylesheets
- Added compact JSON step history in DB for optimization runs
- Added multi-variant optimization workflow with progress
- Added validation before publish
- Added visual validation before publish:
  - viewport screenshot comparison
  - visual diff threshold check
- Added store optimization settings persistence
- Added live settings wiring for:
  - CSS optimization
  - critical CSS
  - stylesheet deferral
  - HTML compression
  - inline CSS minification
  - inline JS minification
- Added safe failure behavior:
  - failed variants do not publish
  - failed variants purge existing cached variant
  - storefront falls back to normal PrestaShop render
- Added Google PageSpeed scan service and endpoint
- Saved per-URL:
  - mobile score
  - desktop score
  - last scanned at
- Fixed duplicate store creation on reconnect

## PrestaShop Module

- Rebuilt signed communication flow with:
  - `cachevariants`
  - `cacheprepare`
  - `cachepublish`
  - `cachepurge`
- Added organized cache services and runtime serving
- Added module-side HTML cache storage with metadata
- Switched cache storage to hash-based fanout
- Added runtime cache serving hook
- Added cache purge flow
- Added one-day cache for fetched variant lists
- Cleaned module JSON logging and enriched it with store/shop context
- Stopped runtime skip logging for admin and ajax requests

## Browser Worker

- Added Playwright browser worker container
- Added full HTML render for cache variants
- Added optimized HTML generation
- Added:
  - inline CSS minification
  - inline JS minification
- Moved final HTML compression decision out of the worker path
- Added realistic mobile rendering profile:
  - mobile viewport
  - mobile user agent
  - touch emulation
- Added rendered HTML validation endpoint:
  - render source URL
  - render optimized HTML
  - capture viewport screenshots
  - compare screenshots visually

## Optimizer Worker

- Added dedicated optimizer worker container
- Added CSS coverage analysis with Playwright + Chromium
- Added CSS byte and used-byte collection
- Added per-stylesheet coverage reporting
- Added first-paint-aware rule usage tracking
- Added critical CSS generation from parsed CSS structure
- Added critical CSS controls:
  - disabled mode
  - capped / skipped modes
- Added critical CSS trimming:
  - exclude `@font-face`
  - keep referenced keyframes only
  - keep referenced custom properties only
- Fixed CSS accounting:
  - include fully unused stylesheets
  - merge overlapping used ranges
  - tighten always-include selector rules

## React App

- Added shop-scoped URL listing in overview
- Added optimize, purge, and scan actions in table
- Added live optimization alerts:
  - yellow queued requests alert
  - blue active optimization progress alert
- Added better alert dismissal behavior
- Added URL display inside optimization progress text
- Added colored PageSpeed score labels
- Added clearer action buttons with text and icons
- Removed critical CSS columns from overview for now
- Added `CSS Optimization` page in sidebar
- Added CSS reports page with:
  - averages
  - per-URL CSS stats
  - per-stylesheet details
  - visual diff validation status
- Added optimization settings page updates:
  - centered tabs with icons
  - page optimization tab
  - CSS master switch
  - critical CSS switch
  - non-critical stylesheet deferral switch
  - inline CSS / inline JS minification switches
  - step details in the blue alert

## Current Result

- Connect
- Discover
- Optimize
- Analyze CSS
- Generate critical CSS
- Control CSS/HTML optimization from settings
- Validate
- Publish
- Serve
- Purge
- Scan

## Next Focus

- Generate `used.css` artifact only
- Validate `used.css` safely before any delivery change
- Show `used.css` size / reduction in CSS page
- Later:
  - controlled `used.css` delivery
  - broader JS defer strategy
