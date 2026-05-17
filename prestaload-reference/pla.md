# PrestaLoad SaaS Protection Plan

## Goal

Protect the commercial value of `PrestaLoad` without increasing page render time on merchant sites.

The core rule is:

- never put a blocking API call in the front-office render path
- keep the local module thin
- keep the valuable logic and paid services on your servers

## What Cannot Be Secured

If you ship all valuable logic inside a downloadable Prestashop module, it cannot be made fully secure.

The merchant controls:

- PHP files
- runtime
- network calls
- local license checks

So these are not enough on their own:

- activation keys
- obfuscation
- encoded PHP
- periodic blocking license checks in page rendering

They can slow abuse down, but they do not protect the business.

## Correct Product Model

Use a hybrid architecture:

1. Local Prestashop module
2. PrestaLoad API
3. PrestaLoad managed services

The module should be a connector and execution layer.
The paid value should live in your SaaS.

## What Stays Local

Keep these inside the Prestashop module:

- admin UI
- local page cache
- early `index.php` static cache bootstrap
- local HTML rewrites that use already-known rules
- asset scan result display
- purge hooks for products, categories, CMS, settings
- local queue / state files
- cached copy of subscription entitlements

The local module must always be able to render or serve cache without waiting for your API.

## What Moves to SaaS

Keep these on your servers:

- account management
- subscription and billing
- domain binding
- remote scanner
- image CDN / imgproxy service
- edge cache service
- rule generation / recommendations
- optimization intelligence
- usage limits and quotas
- signed entitlements
- dashboard analytics

This is the actual product.

## Frontend Performance Rule

Do not call your API before rendering a page.

Never do:

- license validation during front-office request
- remote rule fetch during front-office request
- remote optimization request during front-office request
- remote scan/report fetch during front-office request

Those calls increase TTFB and create an outage dependency on your SaaS.

## Safe Runtime Model

Use a local snapshot model.

The module should work from local cached state:

- `entitlements.json`
- `rules.json`
- `runtime-config.json`

These files are updated in the background by:

- admin save actions
- cron
- post-response background work
- scheduled heartbeats

Front-office requests only read local files.

## Licensing Model

Bind the subscription to:

- account id
- shop domain
- optional shop group id

The server should issue a signed entitlement payload containing:

- account id
- domain
- plan
- enabled features
- quotas
- issued_at
- expires_at
- signature

The module stores that payload locally and verifies the signature locally with a public key.

That means:

- no secret key in the module
- no blocking validation call during page render
- tampering with the payload is detectable

## Recommended Crypto Model

Use asymmetric signing:

- server signs with a private key
- module verifies with a bundled public key

Do not use a shared secret embedded in the module for trust decisions.

Suggested flow:

1. Merchant connects the shop in admin.
2. Server verifies subscription and domain.
3. Server returns signed entitlement JSON.
4. Module stores it locally.
5. Front-office and admin features read the local entitlement.
6. Background sync refreshes it periodically.

## Grace Period Model

To avoid breaking shops if your API is down:

- keep entitlements valid locally for a grace period
- recommended grace: 3 to 7 days

Behavior:

- active subscription + fresh entitlement: all paid features enabled
- active subscription + stale but inside grace period: keep paid features enabled
- expired beyond grace period: degrade safely

Safe degradation:

- keep already-built page cache serving
- stop new premium scans
- stop new SaaS-only optimization generation
- keep core non-premium module features working

Do not hard-fail storefront rendering.

## Feature Gating

Split features into 2 groups.

### Local features

These may continue without API access:

- full-page cache
- early static cache
- local minification
- local defer / disable / load-after-load rules
- local cache lifetimes

### SaaS-gated features

These require a valid entitlement:

- remote scanner
- remote recommendations
- managed image CDN
- edge HTML cache
- central dashboard and analytics
- multi-site cloud rule sync

This gives you a viable product even when offline, while reserving premium value for paying users.

## API Design Rules

All API communication should be:

- async where possible
- retried with backoff
- short timeout
- non-blocking to front-office

Recommended timeouts:

- admin interactive requests: 3 to 8 seconds
- background sync: 10 to 30 seconds
- front-office render path: 0 seconds, because no remote call should occur

## Background Sync Strategy

Use these triggers:

- module connect / disconnect
- admin settings save
- scheduled cron
- purge events
- report requests from admin

Background jobs can:

- refresh entitlements
- upload change events
- send purge requests to edge
- fetch recommended rules
- upload anonymous diagnostics if allowed

## Preventing Resale

You do not prevent resale by hiding the module.
You prevent resale by making the copied module incomplete without your service.

Practical controls:

- one or more verified domains per subscription
- server-issued signed entitlements
- server-side quotas
- dashboard access only through your SaaS
- scanner/imgproxy/edge requiring valid token
- audit logs for domain and token usage

If a pirate copies the module:

- local basic features may still run
- premium SaaS features will not

That is acceptable and commercially strong.

## Anti-Abuse Controls

Implement on the server:

- token rotation
- domain verification
- rate limiting
- per-plan quotas
- anomaly detection for token reuse across unrelated domains
- signed short-lived service tokens for scanner/imgproxy/edge

For image CDN and scanner access:

- issue derived short-lived tokens from your main account token
- validate domain ownership and plan before issuing them

## Domain Verification

Use at least one of:

- module callback challenge
- DNS TXT challenge for advanced users
- known-file challenge under the shop

Minimum viable flow:

1. Module requests connect token.
2. Server returns challenge.
3. Module stores challenge locally.
4. Server validates by calling the shop.
5. Server binds the domain.

## Edge/Scanner/Image Services

These should all require SaaS validation, but not during page render.

### Scanner

- admin-triggered
- remote service
- authenticated with signed short-lived token

### ImgProxy / image CDN

- signed URLs
- customer/domain aware
- optional per-plan limits

### Edge HTML cache

- purge driven by module events
- domain routed
- bypass rules controlled by signed config snapshots

## Failure Model

Design every premium integration for failure without storefront breakage.

If SaaS is unavailable:

- storefront still serves local or edge-cached pages
- local HTML optimization rules still apply
- scans and premium dashboards may temporarily fail
- admin sees a warning, not a fatal state

## Data to Store Locally

Recommended local state files:

- `cache/runtime-config.php`
- `cache/entitlements.json`
- `cache/service-tokens.json`
- `cache/asset-rules.json`
- `cache/feature-flags.json`
- `cache/last-sync.json`

These should be writable by the web process and easy to invalidate.

## Recommended Rollout for PrestaLoad

### Phase 1

- keep current module local features
- add signed entitlement file
- add background sync job
- gate scanner and imgproxy behind SaaS tokens

### Phase 2

- add SaaS dashboard
- add remote rule recommendations
- add per-domain subscription binding

### Phase 3

- add managed edge HTML cache
- make edge integration the premium core offering

## Immediate Implementation Rules

For the current codebase, follow these rules:

- never call PrestaLoad API from `hookActionDispatcher` or any render hook
- read only local config/rules from front-office
- refresh entitlements only from admin actions or background jobs
- treat stale entitlements with grace period
- keep cache serving independent from SaaS availability

## Final Recommendation

The secure commercial model is:

- local module for execution
- SaaS for intelligence, reports, tokens, and premium delivery
- signed local entitlements
- no blocking API calls in page rendering

That gives you:

- good storefront performance
- practical license protection
- graceful failure handling
- a product that is hard to clone by copying PHP files alone
