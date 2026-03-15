<div class="panel">
  <h3>PrestaLoad Full-Page Cache</h3>
  <p>
    Anonymous pages are cached on disk and served before full Prestashop rendering.
    This is meant to reduce document response time on repeat requests for public pages.
  </p>

  <div class="well">
    <p><strong>Cache directory:</strong> {$prestaload_stats.directory|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Cached pages:</strong> {$prestaload_stats.count|intval}</p>
    <p><strong>Cache size:</strong> {$prestaload_stats.size_bytes|intval} bytes</p>
  </div>

  <div style="display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap;">
    <div style="min-width: 200px; max-width: 240px; flex: 0 0 220px;">
      {foreach from=$prestaload_tabs key=tab_key item=tab}
        <div style="margin-bottom: 12px; border: 1px solid #d3d8db; border-radius: 4px; background: {if $prestaload_active_tab === $tab_key}#f5f8fb{else}#ffffff{/if};">
          <a
            href="{$tab.link|escape:'htmlall':'UTF-8'}"
            style="display: block; padding: 14px 16px; color: #363a41; font-weight: 600; text-decoration: none;"
          >
            {$tab.label|escape:'htmlall':'UTF-8'}
          </a>
        </div>
      {/foreach}
    </div>

    <div style="flex: 1 1 640px; min-width: 320px;">
      {$prestaload_settings_form nofilter}

      {if $prestaload_active_tab === 'cache_lifetimes'}
        <div class="panel" style="margin-top: 16px;">
          <h3>Browser Cache Lifetime Helper</h3>
          <p>
            This feature manages Apache browser cache rules through the shop root <code>.htaccess</code> file.
            If the file cannot be updated automatically, use the generated block below.
          </p>

          <div class="well">
            <p><strong>.htaccess path:</strong> {$prestaload_browser_cache_status.path|escape:'htmlall':'UTF-8'}</p>
            <p><strong>File exists:</strong> {if $prestaload_browser_cache_status.exists}Yes{else}No{/if}</p>
            <p><strong>Writable:</strong> {if $prestaload_browser_cache_status.writable}Yes{else}No{/if}</p>
            <p><strong>Managed block present:</strong> {if $prestaload_browser_cache_status.managed_block_present}Yes{else}No{/if}</p>
          </div>

          <div class="alert alert-info">
            <p style="margin-bottom: 8px;">
              Use this snippet if automatic writing is not possible, or if you prefer to review the server rule before applying it.
            </p>
            <pre style="white-space: pre-wrap; word-break: break-word; margin: 0;">{$prestaload_browser_cache_status.snippet|escape:'htmlall':'UTF-8'}</pre>
          </div>
        </div>
      {/if}

      {if $prestaload_active_tab === 'assets'}
        <div class="panel" style="margin-top: 16px;">
          <h3>Asset Scan Workflow</h3>
          <p>
            Scan a public shop page, inspect the CSS and JavaScript assets reported by Lighthouse, then save page-specific rules to keep, defer, or disable selected assets.
          </p>

          <div class="well" style="margin-bottom: 16px;">
            <p><strong>Detected shop base URL:</strong> {$prestaload_detected_shop_base_url|escape:'htmlall':'UTF-8'}</p>
            <p><strong>Effective scan base URL:</strong> {$prestaload_effective_asset_scan_base_url|escape:'htmlall':'UTF-8'}</p>
          </div>

          <div id="prestaload-asset-scan-feedback" style="display: none; margin-bottom: 16px;" class="alert"></div>
          <div id="prestaload-asset-toast" class="prestaload-toast" style="display: none;"></div>

          <form method="post" action="" style="margin-bottom: 16px;" id="prestaload-asset-scan-form">
            <div style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
              <div style="min-width: 320px; flex: 1 1 420px;">
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Page to scan</label>
                <select name="prestaload_asset_page" class="form-control" id="prestaload-asset-page">
                  {foreach from=$prestaload_asset_pages item=asset_page}
                    <option value="{$asset_page.key|escape:'htmlall':'UTF-8'}" {if $prestaload_selected_asset_page.key|default:'' === $asset_page.key}selected="selected"{/if}>
                      {$asset_page.label|escape:'htmlall':'UTF-8'}
                    </option>
                  {/foreach}
                </select>
              </div>
              <div>
                <button type="button" class="btn btn-primary" id="prestaload-run-asset-scan">
                  Scan selected page
                </button>
              </div>
            </div>
          </form>

          <div id="prestaload-asset-scan-overlay" style="display: none; position: relative; margin-bottom: 16px;">
            <div style="border: 1px solid #d3d8db; border-radius: 4px; background: #f5f8fb; padding: 18px 20px; display: flex; align-items: center; gap: 14px;">
              <span style="display: inline-block; width: 18px; height: 18px; border: 2px solid #b8c7d1; border-top-color: #25b9d7; border-radius: 50%; animation: prestaload-spin 0.8s linear infinite;"></span>
              <div>
                <div style="font-weight: 700;">Scanning page assets</div>
                <div style="color: #6c868e;">The remote scanner is processing the selected page. This can take a while on heavy pages.</div>
              </div>
            </div>
          </div>

          {if $prestaload_selected_asset_page}
            <div class="well" style="margin-bottom: 16px;">
              <p><strong>Selected page:</strong> {$prestaload_selected_asset_page.label|escape:'htmlall':'UTF-8'}</p>
              <p><strong>URL:</strong> {$prestaload_selected_asset_page.url|escape:'htmlall':'UTF-8'}</p>
              {if $prestaload_selected_asset_scan}
                <p><strong>Last scan:</strong> {$prestaload_selected_asset_scan.scanned_at|escape:'htmlall':'UTF-8'}</p>
              {else}
                <p><strong>Last scan:</strong> No scan saved yet for this page.</p>
              {/if}
            </div>
          {/if}

          {if $prestaload_selected_asset_scan}
            <div class="prestaload-score-grid">
              {foreach from=$prestaload_selected_asset_scan.score_cards item=card}
                <div class="prestaload-score-card prestaload-score-card--{$card.status|escape:'htmlall':'UTF-8'}">
                  <div class="prestaload-score-card__label">{$card.label|escape:'htmlall':'UTF-8'}</div>
                  <div class="prestaload-score-card__value">{$card.display_value|default:'-'|escape:'htmlall':'UTF-8'}</div>
                </div>
              {/foreach}
            </div>

            <div class="prestaload-asset-tabs">
              {foreach from=$prestaload_selected_asset_scan.asset_groups item=asset_group name=asset_groups}
                <a
                  href="#prestaload-asset-group-{$asset_group.key|escape:'htmlall':'UTF-8'}"
                  class="prestaload-asset-tabs__link {if $smarty.foreach.asset_groups.first}is-active{/if}"
                  data-prestaload-asset-tab="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                >
                  {$asset_group.label|escape:'htmlall':'UTF-8'} ({$asset_group.assets|@count})
                </a>
              {/foreach}
            </div>

            {foreach from=$prestaload_selected_asset_scan.asset_groups item=asset_group name=asset_panels}
              <div
                id="prestaload-asset-group-{$asset_group.key|escape:'htmlall':'UTF-8'}"
                class="prestaload-asset-panel"
                data-prestaload-asset-panel="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                {if !$smarty.foreach.asset_panels.first}style="display: none;"{/if}
              >
                <div class="prestaload-asset-bulk-actions">
                  <label class="prestaload-asset-bulk-actions__select-all">
                    <input type="checkbox" class="prestaload-asset-select-all" data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}">
                    <span>Select all</span>
                  </label>
                  <div style="display: flex; gap: 8px; align-items: center;">
                    <button
                      type="button"
                      class="btn btn-default prestaload-bulk-rule"
                      data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                      data-prestaload-action="keep"
                      data-default-label="Keep all"
                      data-loading-label="Saving..."
                    >
                      <span class="prestaload-bulk-rule__label">Keep all</span>
                    </button>
                    <button
                      type="button"
                      class="btn btn-default prestaload-bulk-rule"
                      data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                      data-prestaload-action="defer"
                      data-default-label="Defer all"
                      data-loading-label="Deferring..."
                    >
                      <span class="prestaload-bulk-rule__label">Defer all</span>
                    </button>
                    <button
                      type="button"
                      class="btn btn-default prestaload-bulk-rule"
                      data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                      data-prestaload-action="disable"
                      data-default-label="Disable all"
                      data-loading-label="Disabling..."
                    >
                      <span class="prestaload-bulk-rule__label">Disable all</span>
                    </button>
                  </div>
                </div>
                <div style="overflow-x: auto;">
                  <table class="table">
                    <thead>
                      <tr>
                        <th style="width: 46px;"></th>
                        <th>Asset</th>
                        <th>Type</th>
                        <th>Transfer size</th>
                        <th>Usage</th>
                        <th>Unused bytes</th>
                        <th>Blocking ms</th>
                        <th>Signals</th>
                        <th>Rule</th>
                      </tr>
                    </thead>
                    <tbody>
                      {foreach from=$asset_group.assets item=asset}
                        {assign var=asset_rule value=$prestaload_selected_asset_rules[$asset.url]|default:null}
                        <tr>
                          <td style="text-align: center;">
                            <input
                              type="checkbox"
                              class="prestaload-asset-checkbox"
                              data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                              value="{$asset.url|escape:'htmlall':'UTF-8'}"
                              data-asset-type="{$asset.type|escape:'htmlall':'UTF-8'}"
                            >
                          </td>
                          <td style="min-width: 360px;">
                            <div style="font-weight: 600; word-break: break-word;">{$asset.url|escape:'htmlall':'UTF-8'}</div>
                          </td>
                          <td>{$asset.type|escape:'htmlall':'UTF-8'}</td>
                          <td>{$asset.transfer_size|default:0|intval}</td>
                          <td>
                            {if $asset.usage_percent !== null}
                              {$asset.usage_percent|escape:'htmlall':'UTF-8'}% used
                            {elseif $asset.discovered_from|default:'' === 'page_html'}
                              <span style="color: #6c868e;">Detected in source</span>
                            {else}
                              <span style="color: #6c868e;">Unknown</span>
                            {/if}
                          </td>
                          <td>{$asset.unused_bytes|default:0|intval}</td>
                          <td>{$asset.render_blocking_ms|default:0|intval}</td>
                          <td>
                            {foreach from=$asset.signals item=signal}
                              <span style="display: inline-block; margin: 0 6px 6px 0; padding: 3px 8px; border-radius: 999px; background: #edf2f7; font-size: 12px;">
                                {$signal|escape:'htmlall':'UTF-8'}
                              </span>
                            {/foreach}
                          </td>
                          <td style="min-width: 260px;">
                            <form method="post" action="" class="prestaload-asset-rule-form">
                              <input type="hidden" name="prestaload_asset_page" value="{$prestaload_selected_asset_page.key|escape:'htmlall':'UTF-8'}">
                              <input type="hidden" name="prestaload_asset_url" value="{$asset.url|escape:'htmlall':'UTF-8'}">
                              <input type="hidden" name="prestaload_asset_type" value="{$asset.type|escape:'htmlall':'UTF-8'}">
                              <div style="display: flex; gap: 8px; align-items: center;">
                                <select name="prestaload_asset_action" class="form-control">
                                  <option value="keep" {if $asset_rule.action|default:'keep' === 'keep'}selected="selected"{/if}>Keep</option>
                                  <option value="defer" {if $asset_rule.action|default:'' === 'defer'}selected="selected"{/if}>Defer</option>
                                  <option value="disable" {if $asset_rule.action|default:'' === 'disable'}selected="selected"{/if}>Disable</option>
                                </select>
                                <button
                                  type="button"
                                  class="btn btn-default prestaload-asset-rule-save"
                                  data-default-label="Save"
                                  data-loading-label="Saving..."
                                >
                                  <span class="prestaload-asset-rule-save__label">Save</span>
                                </button>
                              </div>
                            </form>
                          </td>
                        </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
              </div>
            {/foreach}
          {/if}
        </div>
      {/if}
    </div>
  </div>
</div>

{if $prestaload_active_tab === 'assets'}
  <style>
    @keyframes prestaload-spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    .prestaload-score-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }

    .prestaload-score-card {
      border: 1px solid #d3d8db;
      border-radius: 6px;
      padding: 14px 16px;
      background: #fff;
    }

    .prestaload-score-card__label {
      font-size: 12px;
      color: #6c868e;
      text-transform: uppercase;
    }

    .prestaload-score-card__value {
      font-size: 20px;
      font-weight: 700;
      margin-top: 6px;
      color: #363a41;
    }

    .prestaload-score-card--good {
      border-color: #9ad7b5;
      background: #edf9f2;
    }

    .prestaload-score-card--good .prestaload-score-card__value {
      color: #1f8b4c;
    }

    .prestaload-score-card--warning {
      border-color: #f1d18a;
      background: #fff8e7;
    }

    .prestaload-score-card--warning .prestaload-score-card__value {
      color: #ad7a00;
    }

    .prestaload-score-card--bad {
      border-color: #e7b1b1;
      background: #fff1f1;
    }

    .prestaload-score-card--bad .prestaload-score-card__value {
      color: #c23434;
    }

    .prestaload-asset-tabs {
      display: flex;
      width: 100%;
      margin-bottom: 16px;
      border: 1px solid #d3d8db;
      border-radius: 6px;
      overflow: hidden;
      background: #fff;
    }

    .prestaload-asset-tabs__link {
      flex: 1 1 0;
      text-align: center;
      padding: 12px 10px;
      text-decoration: none;
      color: #495057;
      font-weight: 600;
      border-right: 1px solid #d3d8db;
      background: #fff;
    }

    .prestaload-asset-tabs__link:last-child {
      border-right: 0;
    }

    .prestaload-asset-tabs__link.is-active {
      background: #f5f8fb;
      color: #25b9d7;
      border-bottom: 0;
      margin-bottom: -1px;
      position: relative;
      z-index: 2;
    }

    .prestaload-asset-panel {
      border: 1px solid #d3d8db;
      border-radius: 0 0 6px 6px;
      background: #fff;
      padding: 16px;
      margin-top: -1px;
    }

    .prestaload-asset-panel .table {
      margin-bottom: 0;
      border: 1px solid #d3d8db;
    }

    .prestaload-asset-panel .table > thead > tr > th,
    .prestaload-asset-panel .table > tbody > tr > td {
      border: 1px solid #d3d8db;
    }

    .prestaload-asset-bulk-actions {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    .prestaload-asset-bulk-actions__select-all {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin: 0;
      font-weight: 600;
      color: #495057;
    }

    .prestaload-asset-rule-save.is-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.85;
      padding-left: 34px;
    }

    .prestaload-asset-rule-save.is-loading::before {
      content: '';
      position: absolute;
      left: 12px;
      top: 50%;
      width: 14px;
      height: 14px;
      margin-top: -7px;
      border: 2px solid #b8c7d1;
      border-top-color: #25b9d7;
      border-radius: 50%;
      animation: prestaload-spin 0.8s linear infinite;
    }

    .prestaload-toast {
      position: fixed;
      right: 24px;
      bottom: 24px;
      z-index: 9999;
      min-width: 280px;
      max-width: 420px;
      padding: 14px 16px;
      border-radius: 6px;
      color: #fff;
      box-shadow: 0 10px 30px rgba(54, 58, 65, 0.18);
      font-weight: 600;
    }

    .prestaload-toast--success {
      background: #1f8b4c;
    }

    .prestaload-toast--error {
      background: #c23434;
    }

    .prestaload-bulk-rule.is-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.85;
      padding-left: 34px;
    }

    .prestaload-bulk-rule.is-loading::before {
      content: '';
      position: absolute;
      left: 12px;
      top: 50%;
      width: 14px;
      height: 14px;
      margin-top: -7px;
      border: 2px solid #b8c7d1;
      border-top-color: #25b9d7;
      border-radius: 50%;
      animation: prestaload-spin 0.8s linear infinite;
    }
  </style>
  <script>
    (function () {
      var button = document.getElementById('prestaload-run-asset-scan');
      var pageSelect = document.getElementById('prestaload-asset-page');
      var overlay = document.getElementById('prestaload-asset-scan-overlay');
      var feedback = document.getElementById('prestaload-asset-scan-feedback');
      var toast = document.getElementById('prestaload-asset-toast');
      var ajaxUrl = {$prestaload_asset_scan_ajax_url|json_encode nofilter};
      var assetRuleAjaxUrl = {$prestaload_asset_rule_ajax_url|json_encode nofilter};
      var assetBulkRuleAjaxUrl = {$prestaload_asset_bulk_rule_ajax_url|json_encode nofilter};
      var assetTabLinks = document.querySelectorAll('[data-prestaload-asset-tab]');
      var assetPanels = document.querySelectorAll('[data-prestaload-asset-panel]');
      var assetRuleButtons = document.querySelectorAll('.prestaload-asset-rule-save');
      var bulkRuleButtons = document.querySelectorAll('.prestaload-bulk-rule');
      var selectAllCheckboxes = document.querySelectorAll('.prestaload-asset-select-all');
      var toastTimer = null;

      if (!button || !pageSelect || !ajaxUrl) {
        button = null;
      }

      var setFeedback = function (message, type) {
        if (!feedback) {
          return;
        }

        feedback.className = 'alert ' + (type === 'error' ? 'alert-danger' : 'alert-success');
        feedback.textContent = message;
        feedback.style.display = 'block';
      };

      var showToast = function (message, type) {
        if (!toast) {
          return;
        }

        toast.className = 'prestaload-toast prestaload-toast--' + (type === 'error' ? 'error' : 'success');
        toast.textContent = message;
        toast.style.display = 'block';

        if (toastTimer) {
          window.clearTimeout(toastTimer);
        }

        toastTimer = window.setTimeout(function () {
          toast.style.display = 'none';
        }, 3200);
      };

      var activateAssetTab = function (key) {
        Array.prototype.forEach.call(assetTabLinks, function (link) {
          link.classList.toggle('is-active', link.getAttribute('data-prestaload-asset-tab') === key);
        });

        Array.prototype.forEach.call(assetPanels, function (panel) {
          panel.style.display = panel.getAttribute('data-prestaload-asset-panel') === key ? 'block' : 'none';
        });
      };

      Array.prototype.forEach.call(assetTabLinks, function (link) {
        link.addEventListener('click', function (event) {
          event.preventDefault();
          activateAssetTab(link.getAttribute('data-prestaload-asset-tab'));
        });
      });

      Array.prototype.forEach.call(assetRuleButtons, function (saveButton) {
        saveButton.addEventListener('click', function () {
          if (!assetRuleAjaxUrl) {
            return;
          }

          var form = saveButton.closest('form');
          if (!form) {
            return;
          }

          var formData = new FormData(form);
          var params = new URLSearchParams();
          formData.forEach(function (value, key) {
            params.append(key, value);
          });

          var label = saveButton.querySelector('.prestaload-asset-rule-save__label');
          var defaultLabel = saveButton.getAttribute('data-default-label') || 'Save';
          var loadingLabel = saveButton.getAttribute('data-loading-label') || 'Saving...';

          saveButton.disabled = true;
          saveButton.classList.add('is-loading');
          if (label) {
            label.textContent = loadingLabel;
          }

          fetch(assetRuleAjaxUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: params.toString(),
            credentials: 'same-origin'
          }).then(function (response) {
            return response.json();
          }).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.message ? payload.message : 'Asset rule update failed.');
            }

            showToast(payload.message || 'Asset rule updated.', 'success');
          }).catch(function (error) {
            showToast(error.message || 'Asset rule update failed.', 'error');
          }).finally(function () {
            saveButton.disabled = false;
            saveButton.classList.remove('is-loading');
            if (label) {
              label.textContent = defaultLabel;
            }
          });
        });
      });

      var getGroupCheckboxes = function (group) {
        return document.querySelectorAll('.prestaload-asset-checkbox[data-prestaload-group=\"' + group + '\"]');
      };

      Array.prototype.forEach.call(selectAllCheckboxes, function (selectAll) {
        selectAll.addEventListener('change', function () {
          Array.prototype.forEach.call(getGroupCheckboxes(selectAll.getAttribute('data-prestaload-group')), function (checkbox) {
            checkbox.checked = selectAll.checked;
          });
        });
      });

      Array.prototype.forEach.call(bulkRuleButtons, function (bulkButton) {
        bulkButton.addEventListener('click', function () {
          if (!assetBulkRuleAjaxUrl) {
            return;
          }

          var group = bulkButton.getAttribute('data-prestaload-group');
          var checkboxes = getGroupCheckboxes(group);
          var selected = Array.prototype.filter.call(checkboxes, function (checkbox) {
            return checkbox.checked;
          });

          if (!selected.length) {
            showToast('Select at least one asset.', 'error');
            return;
          }

          var params = new URLSearchParams();
          params.append('prestaload_asset_page', pageSelect ? pageSelect.value : '');
          params.append('prestaload_asset_action', bulkButton.getAttribute('data-prestaload-action') || 'defer');

          selected.forEach(function (checkbox, index) {
            params.append('prestaload_asset_urls[' + index + ']', checkbox.value);
            params.append('prestaload_asset_types[' + index + ']', checkbox.getAttribute('data-asset-type') || 'other');
          });

          var label = bulkButton.querySelector('.prestaload-bulk-rule__label');
          var defaultLabel = bulkButton.getAttribute('data-default-label') || 'Save all';
          var loadingLabel = bulkButton.getAttribute('data-loading-label') || 'Saving...';

          bulkButton.disabled = true;
          bulkButton.classList.add('is-loading');
          if (label) {
            label.textContent = loadingLabel;
          }

          fetch(assetBulkRuleAjaxUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: params.toString(),
            credentials: 'same-origin'
          }).then(function (response) {
            return response.json();
          }).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.message ? payload.message : 'Bulk update failed.');
            }

            showToast(payload.message || 'Asset rules updated.', 'success');
          }).catch(function (error) {
            showToast(error.message || 'Bulk update failed.', 'error');
          }).finally(function () {
            bulkButton.disabled = false;
            bulkButton.classList.remove('is-loading');
            if (label) {
              label.textContent = defaultLabel;
            }
          });
        });
      });

      if (button && pageSelect && ajaxUrl) {
        button.addEventListener('click', function () {
          var params = new URLSearchParams();
          params.set('prestaload_asset_page', pageSelect.value);

          button.disabled = true;
          if (overlay) {
            overlay.style.display = 'block';
          }
          if (feedback) {
            feedback.style.display = 'none';
          }

          fetch(ajaxUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: params.toString(),
            credentials: 'same-origin'
          }).then(function (response) {
            return response.json();
          }).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.message ? payload.message : 'Asset scan failed.');
            }

            setFeedback(payload.message || 'Asset scan completed.', 'success');
            window.location.href = payload.reload_url || window.location.href;
          }).catch(function (error) {
            setFeedback(error.message || 'Asset scan failed.', 'error');
          }).finally(function () {
            button.disabled = false;
            if (overlay) {
              overlay.style.display = 'none';
            }
          });
        });
      }
    }());
  </script>
{/if}
