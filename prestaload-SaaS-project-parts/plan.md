# PrestaLoad SaaS Optimization Plan

## Goal

Build a production-safe optimization pipeline for one page variant at a time.

Each optimized page must:

- reduce delivered CSS safely
- preserve fallback behavior
- be validated before publish
- be rollback-safe

This system must work for sensitive storefront pages, so publish safety matters more than aggressive optimization.

---

## Core Flow

For one page variant:

1. Laravel creates an optimization run
2. `render-worker` loads the page in a real browser
3. `render-worker` outputs:
   - raw HTML
   - DOM snapshot
   - CSS coverage
   - network log
   - screenshot
4. `css-builder` reads:
   - original CSS sources
   - coverage
   - DOM snapshot
5. `css-builder` produces:
   - `critical.css`
   - `used.css`
   - rule manifest
6. `html-assembler` rewrites final HTML:
   - inline critical CSS
   - swap full CSS links for generated `used.css`
   - preserve fallback markers
7. `validator` compares:
   - original screenshot vs optimized
   - required selectors/blocks
   - console errors
8. Laravel publishes only if validation passes

---

## Optimization Scope

The user can choose how optimizations are organized.

### Grouped

One optimization strategy applies to a group of pages.

Initial supported groups:

- `home`
- `category`
- `product`
- `cms`

Benefits:

- fewer runs
- better cache reuse
- cheaper processing

### Per Page

Each page variant gets its own optimization run and artifacts.

Benefits:

- more accurate
- better for difficult themes
- better for premium users

Important:

- serving is always per page variant
- only optimization strategy and artifacts may be grouped

---

## Services

### Laravel API

Responsible for:

- store and shop management
- URL inventory
- optimization run creation
- queue dispatch
- step logging
- artifact metadata
- publish and rollback state
- dashboard reporting

### render-worker

Responsible for:

- opening the page in Playwright/Chromium
- waiting for stable render
- simulating safe interactions if needed
- capturing:
  - raw HTML
  - DOM snapshot
  - CSS coverage
  - network log
  - screenshot
  - console errors

### css-builder

Responsible for:

- reading original CSS sources
- mapping coverage to CSS rules
- preserving required dependencies:
  - CSS variables
  - `@media`
  - `@supports`
  - `@keyframes`
  - needed `@font-face`
- generating:
  - `critical.css`
  - `used.css`
  - rule manifest

### html-assembler

Responsible for:

- rewriting HTML to:
  - inline `critical.css`
  - load generated `used.css`
  - preserve fallback markers for original CSS
- emitting final optimized HTML

### validator

Responsible for:

- comparing original vs optimized screenshot
- checking required selectors and blocks
- checking console errors
- returning:
  - `pass`
  - `warning`
  - `reject`

---

## Database Structure

Laravel should store optimization orchestration in SQL, not only in logs.

### 1. optimization_groups

Represents a reusable optimization scope chosen by the user.

Fields:

- `id`
- `workspace_id`
- `prestashop_store_id`
- `prestashop_shop_id` nullable
- `name`
- `scope_type`
  - `grouped`
  - `per_page`
- `page_type`
  - `home`
  - `category`
  - `product`
  - `cms`
  - nullable for custom scope
- `is_premium`
- `is_active`
- `config_version`
- `created_by`
- `created_at`
- `updated_at`

### 2. optimization_targets

Represents what the user started optimizing.

This is the queueable target record for one page variant or one grouped scope.

Fields:

- `id`
- `optimization_group_id` nullable
- `prestashop_store_id`
- `prestashop_shop_id`
- `prestashop_shop_url_id` nullable
- `target_type`
  - `page_variant`
  - `page_group`
- `page_type`
- `normalized_url`
- `language_iso`
- `currency_iso` nullable
- `device_class`
  - `mobile`
  - `desktop`
  - `tablet`
- `variant_key`
- `status`
  - `pending`
  - `running`
  - `validated`
  - `published`
  - `failed`
  - `rejected`
  - `rolled_back`
- `priority`
- `current_optimization_run_id` nullable
- `published_artifact_version_id` nullable
- `last_error` nullable
- `created_at`
- `updated_at`

### 3. optimization_runs

Represents one execution attempt for a target.

Fields:

- `id`
- `optimization_target_id`
- `run_number`
- `trigger_type`
  - `manual`
  - `scheduled`
  - `retry`
  - `republish`
- `status`
  - `queued`
  - `rendering`
  - `building`
  - `assembling`
  - `validating`
  - `publishing`
  - `published`
  - `failed`
  - `rejected`
- `started_at` nullable
- `finished_at` nullable
- `duration_ms` nullable
- `failure_reason` nullable
- `created_at`
- `updated_at`

### 4. optimization_run_steps

Represents each worker step inside a run.

This is the main per-worker detail table.

Fields:

- `id`
- `optimization_run_id`
- `worker_type`
  - `render_worker`
  - `css_builder`
  - `html_assembler`
  - `validator`
  - `publisher`
- `step_name`
  - `render_page`
  - `build_css`
  - `assemble_html`
  - `validate_artifact`
  - `publish_artifact`
- `status`
  - `queued`
  - `running`
  - `completed`
  - `failed`
  - `skipped`
- `attempt_number`
- `queue_name` nullable
- `worker_host` nullable
- `worker_container` nullable
- `started_at` nullable
- `finished_at` nullable
- `duration_ms` nullable
- `input_summary_json` nullable
- `output_summary_json` nullable
- `error_summary` nullable
- `created_at`
- `updated_at`

### 5. optimization_artifact_versions

Represents a produced artifact bundle for one run.

Fields:

- `id`
- `optimization_run_id`
- `optimization_target_id`
- `version_number`
- `status`
  - `draft`
  - `validated`
  - `published`
  - `rejected`
  - `rolled_back`
- `storage_prefix`
- `raw_html_path` nullable
- `dom_snapshot_path` nullable
- `coverage_json_path` nullable
- `network_log_path` nullable
- `screenshot_original_path` nullable
- `critical_css_path` nullable
- `used_css_path` nullable
- `rule_manifest_path` nullable
- `optimized_html_path` nullable
- `validation_report_path` nullable
- `created_at`
- `updated_at`

### 6. optimization_publications

Represents live publish history and rollback chain.

Fields:

- `id`
- `optimization_target_id`
- `published_artifact_version_id`
- `previous_artifact_version_id` nullable
- `published_by`
- `publish_status`
  - `published`
  - `rolled_back`
- `published_at`
- `rollback_at` nullable
- `rollback_reason` nullable
- `created_at`
- `updated_at`

### 7. optimization_run_logs

Optional structured log table for important messages.

Fields:

- `id`
- `optimization_run_id`
- `optimization_run_step_id` nullable
- `level`
  - `info`
  - `warning`
  - `error`
- `message`
- `context_json` nullable
- `created_at`

---

## Artifact Model

Each variant should produce:

- raw HTML
- DOM snapshot
- CSS coverage JSON
- network log JSON
- original screenshot
- `critical.css`
- `used.css`
- rule manifest JSON
- optimized HTML
- validation report JSON

These should be referenced from `optimization_artifact_versions`, not stored inline in SQL.

---

## Variant Key

Never cache only by URL.

Minimum variant fields:

- store ID
- shop ID
- normalized URL
- language
- currency
- device class
- login state
- theme hash
- optimization config version

Optional later:

- customer group
- region
- experiment bucket

---

## Publish Rules

Laravel should only publish when:

- render completed successfully
- CSS artifacts were generated
- optimized HTML was assembled
- validator returned `pass` or acceptable `warning`

If validation fails:

- do not publish
- preserve the previous live version
- keep the failed build as draft/debug artifact

---

## Fallback Model

Every published page must preserve:

- current live version
- previous live version
- original CSS fallback markers

If runtime problems are detected:

- revert to previous live version
- mark the new version as rejected

---

## Laravel Logging

Laravel should log every step with:

- run ID
- variant key
- step name
- status
- started at
- finished at
- duration ms
- input artifact refs
- output artifact refs
- output sizes
- failure reason if any

Target steps:

1. `create_run`
2. `render_page`
3. `build_css`
4. `assemble_html`
5. `validate_artifact`
6. `publish_artifact`

Most of this should live in:

- `optimization_runs`
- `optimization_run_steps`
- `optimization_run_logs`

---

## Container Layout

Current containers:

- `scanner`
- `imgproxy`

Planned containers:

- `render-worker`
- `css-builder`
- `artifact-origin`

Suggested responsibilities:

- `scanner`: reports and audits
- `imgproxy`: image optimization
- `render-worker`: page rendering and tracing
- `css-builder`: CSS artifact generation
- `artifact-origin`: serve generated HTML/CSS artifacts

---

## Build Order

### Phase 1

- create tables:
  - `optimization_groups`
  - `optimization_targets`
  - `optimization_runs`
  - `optimization_run_steps`
  - `optimization_artifact_versions`
  - `optimization_publications`
- define variant key
- define artifact naming

### Phase 2

- implement Laravel optimization run creation
- implement step logging
- implement grouped vs per-page target creation

### Phase 3

- implement `render-worker`
- save:
  - raw HTML
  - DOM snapshot
  - CSS coverage
  - screenshot
  - console log

### Phase 4

- implement `css-builder`
- generate:
  - `critical.css`
  - `used.css`
  - rule manifest

### Phase 5

- implement `html-assembler`
- produce optimized HTML with fallback markers

### Phase 6

- implement `validator`
- compare screenshots
- verify required selectors/blocks
- check console errors

### Phase 7

- implement publish and rollback flow
- publish only validated artifacts

---

## First Success Criteria

The first complete version is successful when:

- one selected page variant can be processed end-to-end
- optimized HTML is generated with `critical.css` and `used.css`
- validation passes
- Laravel publishes the artifact
- previous version remains available for rollback
