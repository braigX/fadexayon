# PrestaLoad Database Structure

## Scope

The database is split into:
- core tables
- Global module tables

Core keeps identity, workspace, integration, store, shop, and discovered URL data.

Global owns:
- module activation
- module settings
- page-type production line
- reports
- rules
- artifacts
- optimization runs

Page-type production data is scoped to `shop`, not `store`.

## Core Tables

These stay as the base app schema:
- `users`
- `workspaces`
- `workspace_members`
- `workspace_permissions`
- `prestashop_stores`
- `prestashop_shops`
- `prestashop_shop_urls`

Core relationships:
- `workspace` has many `prestashop_stores`
- `prestashop_store` has many `prestashop_shops`
- `prestashop_shop` has many `prestashop_shop_urls`

Important:
- `prestashop_shop_urls.page_type` remains the grouping key for production-line optimization

## Global Tables

### 1. `global_modules`

Purpose:
- registry of available modules

Columns:
- `id`: primary key of the module row.
- `key`: stable internal module identifier used in code and settings.
- `name`: human-readable module name shown in admin UI.
- `is_active`: global availability flag for the module.
- `created_at`: module creation timestamp.
- `updated_at`: last module update timestamp.

Examples of `key`:
- `page_optimization`
- `html_optimization`
- `css_optimization`
- `javascript_optimization`
- `font_optimization`
- `image_optimization`
- `cache_optimization`
- `performance_scanning`
- `validation`
- `reporting`

### 2. `global_workspace_modules`

Purpose:
- enable or disable modules per workspace

Columns:
- `id`: primary key of the workspace-module mapping.
- `workspace_id`: target workspace that owns the module activation.
- `module_id`: linked module from `global_modules`.
- `enabled`: whether the module is enabled for this workspace.
- `created_at`: row creation timestamp.
- `updated_at`: last change timestamp.

Constraints:
- unique `workspace_id + module_id`

Relationships:
- belongs to `workspace`
- belongs to `global_modules`

### 3. `global_shop_module_settings`

Purpose:
- module settings per shop

Columns:
- `id`: primary key of the shop settings row.
- `prestashop_shop_id`: target shop receiving these module settings.
- `module_id`: linked module from `global_modules`.
- `settings_json`: flexible JSON payload for module-specific shop settings.
- `created_at`: row creation timestamp.
- `updated_at`: last settings update timestamp.

Constraints:
- unique `prestashop_shop_id + module_id`

Relationships:
- belongs to `prestashop_shop`
- belongs to `global_modules`

## Shop-Scoped Production Line

These tables drive optimization by page type.

### 4. `global_page_type_profiles`

Purpose:
- one production profile per `shop + page_type`

Columns:
- `id`: primary key of the page-type production profile.
- `prestashop_shop_id`: target shop that owns this page-type profile.
- `page_type`: normalized page-type key such as `home`, `category`, or `product`.
- `sample_shop_url_id`: representative URL used to build reports for this page type.
- `status`: current pipeline state for the page-type profile.
- `pipeline_state_json`: internal pipeline metadata, counters, and progress state.
- `current_scan_report_id`: current active performance scan report reference.
- `current_css_report_id`: current active CSS report reference.
- `current_js_report_id`: current active JavaScript report reference.
- `current_font_report_id`: current active font report reference.
- `current_image_report_id`: current active image report reference.
- `current_validation_report_id`: current active validation report reference.
- `last_prepared_at`: last successful preparation timestamp for this profile.
- `created_at`: row creation timestamp.
- `updated_at`: last profile update timestamp.

Constraints:
- unique `prestashop_shop_id + page_type`

Statuses:
- `new`
- `queued`
- `preparing`
- `ready`
- `failed`

Relationships:
- belongs to `prestashop_shop`
- belongs to `prestashop_shop_urls` through `sample_shop_url_id`
- has many `global_page_type_reports`
- has many `global_page_type_rules`
- has many `global_artifacts`
- has many `global_page_optimization_runs`

### 5. `global_page_type_reports`

Purpose:
- all reports for a page type live here, regardless of module

Columns:
- `id`: primary key of the report row.
- `page_type_profile_id`: linked page-type profile that owns this report.
- `module_key`: module that generated the report, such as `css_optimization`.
- `report_kind`: concrete report category, such as `performance_scan` or `font_usage`.
- `device_class`: target device variant like `desktop` or `mobile`.
- `status`: report lifecycle state, such as `queued`, `ready`, or `failed`.
- `version`: monotonically increasing version for regenerated reports.
- `is_current`: whether this report is the active report for its kind.
- `summary_json`: compact summary data for fast listing and dashboards.
- `payload_json`: full raw or normalized report payload.
- `generated_at`: timestamp when the report was actually generated.
- `created_at`: row creation timestamp.
- `updated_at`: last report update timestamp.

Examples:
- `module_key = performance_scanning`, `report_kind = performance_scan`
- `module_key = css_optimization`, `report_kind = coverage`
- `module_key = javascript_optimization`, `report_kind = js_audit`
- `module_key = font_optimization`, `report_kind = font_usage`
- `module_key = validation`, `report_kind = visual_validation`

Constraints:
- index `page_type_profile_id + module_key + report_kind + device_class + is_current`

Relationships:
- belongs to `global_page_type_profiles`

### 6. `global_page_type_rules`

Purpose:
- shared rule table for all asset decisions

Columns:
- `id`: primary key of the rule row.
- `page_type_profile_id`: linked page-type profile that owns this rule.
- `module_key`: module that produced this rule, such as `css_optimization`.
- `asset_type`: asset family like `css`, `js`, `font`, or `image`.
- `device_class`: target device variant like `desktop` or `mobile`.
- `asset_ref`: stable asset identifier, usually a URL, family name, or synthetic inline key.
- `recommended_action`: rule action suggested automatically by the engine.
- `effective_action`: final action actually applied after overrides.
- `action_source`: source of the effective action, such as `system` or `user_override`.
- `is_user_override`: whether the rule was manually changed by an admin.
- `reasons_json`: human-readable and machine-readable reasons for the decision.
- `evidence_json`: normalized evidence from scans, reports, or heuristics.
- `meta_json`: extra module-specific metadata needed to apply the rule.
- `created_at`: row creation timestamp.
- `updated_at`: last rule update timestamp.

Examples:
- `asset_type = css`
- `asset_type = js`
- `asset_type = font`
- `asset_type = image`

Examples of `asset_ref`:
- stylesheet URL
- script URL
- font family
- image path
- inline synthetic key like `inline://head/style/1`

Constraints:
- unique `page_type_profile_id + module_key + asset_type + device_class + asset_ref`

Relationships:
- belongs to `global_page_type_profiles`

### 7. `global_artifacts`

Purpose:
- generic generated file registry

Columns:
- `id`: primary key of the generated artifact row.
- `owner_type`: owning model type, usually `page_type_profile` or `optimization_run`.
- `owner_id`: owning model identifier.
- `module_key`: module that generated the artifact.
- `artifact_kind`: artifact category such as `critical_css` or `optimized_html`.
- `device_class`: target device variant like `desktop` or `mobile`.
- `storage_disk`: Laravel storage disk where the file lives.
- `storage_path`: internal path on the selected storage disk.
- `public_url`: public URL used by the storefront or admin UI.
- `checksum`: content hash used for cache busting and integrity checks.
- `bytes`: artifact size in bytes.
- `meta_json`: extra artifact metadata such as source assets or bundle contents.
- `created_at`: row creation timestamp.
- `updated_at`: last artifact metadata update timestamp.

Typical owners:
- `page_type_profile`
- `optimization_run`

Examples of `artifact_kind`:
- `critical_css`
- `used_css`
- `css_bundle`
- `js_bundle`
- `font_css`
- `font_preload_manifest`
- `optimized_html`
- `validation_screenshot`

Constraints:
- index `owner_type + owner_id`

## URL Optimization Runtime

### 8. `global_page_optimization_runs`

Purpose:
- one optimization request per URL

Columns:
- `id`: primary key of the optimization run row.
- `prestashop_shop_url_id`: target URL being optimized.
- `page_type_profile_id`: linked page-type profile reused by this run.
- `trigger_type`: reason the run started, such as `manual` or `auto_prepare`.
- `status`: current run state in the execution pipeline.
- `steps_json`: serialized step-by-step execution progress.
- `result_json`: final summary, outputs, errors, and validation result.
- `started_at`: timestamp when execution actually started.
- `finished_at`: timestamp when execution finished.
- `created_at`: row creation timestamp.
- `updated_at`: last run update timestamp.

Trigger types:
- `manual`
- `auto_prepare`
- `rebuild`
- `rescan`

Statuses:
- `queued`
- `running`
- `validating`
- `publishing`
- `completed`
- `completed_with_errors`
- `failed`

Relationships:
- belongs to `prestashop_shop_urls`
- belongs to `global_page_type_profiles`

### 9. `global_page_optimization_run_logs`

Purpose:
- optional per-run structured logs

Columns:
- `id`: primary key of the run log row.
- `run_id`: linked optimization run identifier.
- `level`: log severity such as `info`, `warning`, or `error`.
- `message`: short log message.
- `context_json`: structured log context payload.
- `created_at`: log creation timestamp.

Relationships:
- belongs to `global_page_optimization_runs`

This table is optional.
If file logs are enough, it can be skipped.

## Recommended Relationships

- `workspace`
  - has many `prestashop_stores`
  - has many `global_workspace_modules`

- `prestashop_store`
  - has many `prestashop_shops`

- `prestashop_shop`
  - has many `prestashop_shop_urls`
  - has many `global_page_type_profiles`
  - has many `global_shop_module_settings`

- `prestashop_shop_url`
  - belongs to `prestashop_shop`
  - has many `global_page_optimization_runs`

- `global_page_type_profile`
  - belongs to `prestashop_shop`
  - belongs to `sample_shop_url`
  - has many `global_page_type_reports`
  - has many `global_page_type_rules`
  - has many `global_artifacts`
  - has many `global_page_optimization_runs`

## Production-Line Flow

For each `shop + page_type`:
1. select sample URL
2. collect performance scan
3. collect CSS / JS / font / image reports
4. resolve rules
5. generate artifacts
6. mark profile `ready`

Then for each single URL optimize request:
1. load the URL
2. link to its `global_page_type_profile`
3. reuse current reports, rules, and artifacts
4. build final optimized HTML
5. validate
6. publish cache

## Why This Structure

This structure stays simple because:
- core remains clean
- Global owns all module metadata in one place
- page-type production is first-class
- reports are always attached to one profile
- rules are generic and editable
- artifacts are generic and reusable
- runtime stays separate from evidence and rules

## Notes

- page-type profiles should be shop-scoped
- shop-level settings are preferred because optimization behavior is shop-specific
- workspace-level module enablement is still correct
- details should live in JSON columns unless they must be queried directly
