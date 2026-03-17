<div class="panel">
  <style>
    .prestaload-layout {
      display: flex;
      gap: 24px;
      align-items: flex-start;
      flex-wrap: wrap;
    }

    .prestaload-nav {
      min-width: 200px;
      max-width: 240px;
      flex: 0 0 220px;
    }

    .prestaload-nav__item {
      margin-bottom: 12px;
      border: 1px solid #d3d8db;
      border-radius: 6px;
      background: #fff;
      overflow: hidden;
    }

    .prestaload-nav__item.is-active {
      background: #f5f8fb;
      border-color: #b7dbe7;
    }

    .prestaload-nav__link {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 14px 16px;
      color: #000 !important;
      font-weight: 600;
      text-decoration: none;
      box-sizing: border-box;
    }

    .prestaload-nav__link:hover,
    .prestaload-nav__link:focus,
    .prestaload-nav__item.is-active .prestaload-nav__link {
      color: #000 !important;
      text-decoration: none;
    }

    .prestaload-nav__icon {
      width: 18px;
      text-align: center;
      color: #6c757d !important;
      flex: 0 0 18px;
    }

    .prestaload-main {
      flex: 1 1 640px;
      min-width: 320px;
    }

    @media (max-width: 1024px) {
      .prestaload-layout {
        flex-direction: column;
      }

      .prestaload-nav,
      .prestaload-main {
        width: 100%;
        max-width: none;
        min-width: 0;
        flex: 1 1 100%;
      }

      .prestaload-nav__item {
        width: 100%;
      }
    }
  </style>

  <div class="prestaload-layout">
    <div class="prestaload-nav">
      {foreach from=$prestaload_tabs key=tab_key item=tab}
        <div class="prestaload-nav__item {if $prestaload_active_tab === $tab_key}is-active{/if}">
          <a href="{$tab.link|escape:'htmlall':'UTF-8'}" class="prestaload-nav__link">
            <span class="prestaload-nav__icon">
              <i class="{$tab.icon|escape:'htmlall':'UTF-8'}"></i>
            </span>
            {$tab.label|escape:'htmlall':'UTF-8'}
          </a>
        </div>
      {/foreach}
    </div>

    <div class="prestaload-main">
      {$prestaload_settings_form nofilter}

      {if $prestaload_active_tab === 'general'}
        <div class="panel" style="margin-top: 16px;">
          <h3>Cached pages ({$prestaload_stats.size_bytes|intval} bytes)</h3>
          <p>
            These are the current full-page cache files managed by PrestaLoad.
          </p>

          {if $prestaload_stats.grouped_pages|@count > 0}
            {foreach from=$prestaload_stats.grouped_pages item=cache_group}
              <div class="panel" style="margin-top: 16px; margin-bottom: 0;">
                <h4 style="margin-top: 0; margin-bottom: 8px;">
                  {$cache_group.request_uri|escape:'htmlall':'UTF-8'}
                  <small style="font-weight: 400; color: #6c868e;">
                    {$cache_group.controller|default:'-'|escape:'htmlall':'UTF-8'} | {$cache_group.total_size_bytes|intval} bytes | {$cache_group.variants|@count} cache file(s)
                  </small>
                </h4>

                <div style="overflow-x: auto;">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Variant</th>
                        <th>Status</th>
                        <th>Size</th>
                        <th>Stored at</th>
                        <th>Expires at</th>
                        <th>Shop</th>
                        <th>Language</th>
                        <th>Currency</th>
                        <th>Country</th>
                        <th>Device</th>
                        <th>Cache key</th>
                      </tr>
                    </thead>
                    <tbody>
                      {foreach from=$cache_group.variants item=cache_page}
                        <tr>
                          <td>
                            {if $cache_page.early_alias|default:false}
                              Early bootstrap alias
                            {else}
                              Standard page cache
                            {/if}
                          </td>
                          <td>{$cache_page.status_code|intval}</td>
                          <td>{$cache_page.size_bytes|intval} bytes</td>
                          <td>{if $cache_page.stored_at}{$cache_page.stored_at|date_format:'%Y-%m-%d %H:%M:%S'}{else}-{/if}</td>
                          <td>{if $cache_page.expires_at}{$cache_page.expires_at|date_format:'%Y-%m-%d %H:%M:%S'}{else}-{/if}</td>
                          <td>{$cache_page.cache_parts.shop|default:'-'|escape:'htmlall':'UTF-8'}</td>
                          <td>{$cache_page.cache_parts.lang|default:'-'|escape:'htmlall':'UTF-8'}</td>
                          <td>{$cache_page.cache_parts.currency|default:'-'|escape:'htmlall':'UTF-8'}</td>
                          <td>{$cache_page.cache_parts.country|default:'-'|escape:'htmlall':'UTF-8'}</td>
                          <td>{$cache_page.cache_parts.device|default:'-'|escape:'htmlall':'UTF-8'}</td>
                          <td style="word-break: break-all;">{$cache_page.cache_key|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
              </div>
            {/foreach}
          {else}
            <div class="alert alert-info" style="margin-bottom: 0;">
              No cached pages are stored yet.
            </div>
          {/if}
        </div>
      {/if}

      {if $prestaload_active_tab === 'critical_css'}
        <div class="panel" style="margin-top: 16px;">
          <h3>Beta critical CSS generator</h3>
          <p>
            Generate one stored critical CSS payload per page type. The generated CSS is saved on disk and injected locally on matching pages, without adding remote API latency to storefront rendering.
          </p>

          <div id="prestaload-critical-css-toast" class="prestaload-toast" style="display: none;"></div>

          <div style="overflow-x: auto;">
            <table class="table" style="border: 1px solid #d3d8db;">
              <thead>
                <tr>
                  <th>Page type</th>
                  <th>Representative URL</th>
                  <th>Sample</th>
                  <th>Mobile</th>
                  <th>Desktop</th>
                  <th style="width: 180px;">Action</th>
                </tr>
              </thead>
              <tbody>
                {foreach from=$prestaload_critical_css_pages item=critical_page}
                  <tr>
                    <td><strong>{$critical_page.label|escape:'htmlall':'UTF-8'}</strong></td>
                    <td style="word-break: break-all;">{$critical_page.url|escape:'htmlall':'UTF-8'}</td>
                    <td>{$critical_page.sample_label|default:'-'|escape:'htmlall':'UTF-8'}</td>
                    <td>
                      {if $critical_page.critical_css.mobile.generated}
                        <span class="label label-success">Generated</span>
                        <div style="margin-top: 6px; color: #6c868e;">{$critical_page.critical_css.mobile.size_bytes|intval} bytes</div>
                        <div style="color: #6c868e;">{$critical_page.critical_css.mobile.generated_at|escape:'htmlall':'UTF-8'}</div>
                        {if $critical_page.critical_css.mobile.generator_version ne ''}
                          <div style="color: #6c868e;">Generator v{$critical_page.critical_css.mobile.generator_version|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $critical_page.critical_css.mobile.max_css_bytes}
                          <div style="color: #6c868e;">Budget {$critical_page.critical_css.mobile.max_css_bytes|intval} bytes{if $critical_page.critical_css.mobile.budget_reached} (reached){/if}</div>
                        {/if}
                        {if $critical_page.critical_css.mobile.viewport_element_count !== null || $critical_page.critical_css.mobile.included_element_count !== null}
                          <div style="color: #6c868e;">Viewport {$critical_page.critical_css.mobile.viewport_element_count|intval} / included {$critical_page.critical_css.mobile.included_element_count|intval}</div>
                        {/if}
                        {if $critical_page.critical_css.mobile.stats_summary ne ''}
                          <div style="color: #6c868e;">{$critical_page.critical_css.mobile.stats_summary|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                      {else}
                        <span class="label label-default">Not generated</span>
                      {/if}
                    </td>
                    <td>
                      {if $critical_page.critical_css.desktop.generated}
                        <span class="label label-success">Generated</span>
                        <div style="margin-top: 6px; color: #6c868e;">{$critical_page.critical_css.desktop.size_bytes|intval} bytes</div>
                        <div style="color: #6c868e;">{$critical_page.critical_css.desktop.generated_at|escape:'htmlall':'UTF-8'}</div>
                        {if $critical_page.critical_css.desktop.generator_version ne ''}
                          <div style="color: #6c868e;">Generator v{$critical_page.critical_css.desktop.generator_version|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $critical_page.critical_css.desktop.max_css_bytes}
                          <div style="color: #6c868e;">Budget {$critical_page.critical_css.desktop.max_css_bytes|intval} bytes{if $critical_page.critical_css.desktop.budget_reached} (reached){/if}</div>
                        {/if}
                        {if $critical_page.critical_css.desktop.viewport_element_count !== null || $critical_page.critical_css.desktop.included_element_count !== null}
                          <div style="color: #6c868e;">Viewport {$critical_page.critical_css.desktop.viewport_element_count|intval} / included {$critical_page.critical_css.desktop.included_element_count|intval}</div>
                        {/if}
                        {if $critical_page.critical_css.desktop.stats_summary ne ''}
                          <div style="color: #6c868e;">{$critical_page.critical_css.desktop.stats_summary|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                      {else}
                        <span class="label label-default">Not generated</span>
                      {/if}
                    </td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-primary prestaload-critical-css-generate"
                        data-page-key="{$critical_page.key|escape:'htmlall':'UTF-8'}"
                        data-default-label="Generate Critical CSS"
                        data-loading-label="Generating..."
                      >
                        <span>Generate Critical CSS</span>
                      </button>
                    </td>
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>

        <style>
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

          .prestaload-critical-css-generate.is-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.85;
            padding-left: 34px;
          }

          .prestaload-critical-css-generate.is-loading::before {
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
            var buttons = document.querySelectorAll('.prestaload-critical-css-generate');
            var ajaxUrl = {$prestaload_critical_css_generate_ajax_url|json_encode nofilter};
            var toast = document.getElementById('prestaload-critical-css-toast');
            var toastTimer = null;

            if (!buttons.length || !ajaxUrl) {
              return;
            }

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

            var postForm = function (url, params) {
              return fetch(url, {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: params.toString(),
                credentials: 'same-origin'
              }).then(function (response) {
                return response.json();
              });
            };

            Array.prototype.forEach.call(buttons, function (button) {
              button.addEventListener('click', function () {
                var params = new URLSearchParams();
                params.append('prestaload_critical_css_page', button.getAttribute('data-page-key') || '');

                var label = button.querySelector('span');
                var defaultLabel = button.getAttribute('data-default-label') || 'Generate Critical CSS';
                var loadingLabel = button.getAttribute('data-loading-label') || 'Generating...';

                button.disabled = true;
                button.classList.add('is-loading');
                if (label) {
                  label.textContent = loadingLabel;
                }

                postForm(ajaxUrl, params).then(function (payload) {
                  if (!payload || !payload.success) {
                    throw new Error(payload && payload.message ? payload.message : 'Critical CSS generation failed.');
                  }

                  showToast(payload.message || 'Critical CSS generated successfully.', 'success');
                  window.setTimeout(function () {
                    window.location.reload();
                  }, 500);
                }).catch(function (error) {
                  showToast(error.message || 'Critical CSS generation failed.', 'error');
                }).finally(function () {
                  button.disabled = false;
                  button.classList.remove('is-loading');
                  if (label) {
                    label.textContent = defaultLabel;
                  }
                });
              });
            });
          }());
        </script>
      {/if}

      {if $prestaload_active_tab === 'fonts'}
        <div class="panel" style="margin-top: 16px;">
          <h3>Font usage audit</h3>
          <p>
            Generate a per-page-type font audit from the remote scanner and save it locally. This helps identify used families, unused declarations, duplicate icon fonts, and Google Fonts load count by device.
          </p>

          <div id="prestaload-font-usage-toast" class="prestaload-toast" style="display: none;"></div>

          <div style="overflow-x: auto;">
            <table class="table" style="border: 1px solid #d3d8db;">
              <thead>
                <tr>
                  <th>Page type</th>
                  <th>Representative URL</th>
                  <th>Sample</th>
                  <th>Mobile</th>
                  <th>Desktop</th>
                  <th style="width: 180px;">Action</th>
                </tr>
              </thead>
              <tbody>
                {foreach from=$prestaload_font_usage_pages item=font_page}
                  <tr>
                    <td><strong>{$font_page.label|escape:'htmlall':'UTF-8'}</strong></td>
                    <td style="word-break: break-all;">{$font_page.url|escape:'htmlall':'UTF-8'}</td>
                    <td>{$font_page.sample_label|default:'-'|escape:'htmlall':'UTF-8'}</td>
                    <td>
                      {if $font_page.font_usage.mobile.generated}
                        <span class="label label-success">Generated</span>
                        <div style="margin-top: 6px; color: #6c868e;">Declared {$font_page.font_usage.mobile.declared_count|intval} | used {$font_page.font_usage.mobile.used_count|intval}</div>
                        <div style="color: #6c868e;">Unused {$font_page.font_usage.mobile.unused_count|intval} | duplicates {$font_page.font_usage.mobile.duplicate_icon_count|intval}</div>
                        <div style="color: #6c868e;">Google Fonts {$font_page.font_usage.mobile.google_fonts_count|intval}</div>
                        {if $font_page.font_usage.mobile.used_above_the_fold_text ne ''}
                          <div style="color: #6c868e;">Above fold: {$font_page.font_usage.mobile.used_above_the_fold_text|escape:'htmlall':'UTF-8'}</div>
                        {elseif $font_page.font_usage.mobile.used_font_families_text ne ''}
                          <div style="color: #6c868e;">Used: {$font_page.font_usage.mobile.used_font_families_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $font_page.font_usage.mobile.unused_declared_families_text ne ''}
                          <div style="color: #6c868e;">Unused: {$font_page.font_usage.mobile.unused_declared_families_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $font_page.font_usage.mobile.duplicate_icon_text ne ''}
                          <div style="color: #6c868e;">Duplicate icons: {$font_page.font_usage.mobile.duplicate_icon_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        <div style="color: #6c868e;">{$font_page.font_usage.mobile.generated_at|escape:'htmlall':'UTF-8'}{if $font_page.font_usage.mobile.generator_version ne ''} | v{$font_page.font_usage.mobile.generator_version|escape:'htmlall':'UTF-8'}{/if}</div>
                      {else}
                        <span class="label label-default">Not generated</span>
                      {/if}
                    </td>
                    <td>
                      {if $font_page.font_usage.desktop.generated}
                        <span class="label label-success">Generated</span>
                        <div style="margin-top: 6px; color: #6c868e;">Declared {$font_page.font_usage.desktop.declared_count|intval} | used {$font_page.font_usage.desktop.used_count|intval}</div>
                        <div style="color: #6c868e;">Unused {$font_page.font_usage.desktop.unused_count|intval} | duplicates {$font_page.font_usage.desktop.duplicate_icon_count|intval}</div>
                        <div style="color: #6c868e;">Google Fonts {$font_page.font_usage.desktop.google_fonts_count|intval}</div>
                        {if $font_page.font_usage.desktop.used_above_the_fold_text ne ''}
                          <div style="color: #6c868e;">Above fold: {$font_page.font_usage.desktop.used_above_the_fold_text|escape:'htmlall':'UTF-8'}</div>
                        {elseif $font_page.font_usage.desktop.used_font_families_text ne ''}
                          <div style="color: #6c868e;">Used: {$font_page.font_usage.desktop.used_font_families_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $font_page.font_usage.desktop.unused_declared_families_text ne ''}
                          <div style="color: #6c868e;">Unused: {$font_page.font_usage.desktop.unused_declared_families_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if $font_page.font_usage.desktop.duplicate_icon_text ne ''}
                          <div style="color: #6c868e;">Duplicate icons: {$font_page.font_usage.desktop.duplicate_icon_text|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        <div style="color: #6c868e;">{$font_page.font_usage.desktop.generated_at|escape:'htmlall':'UTF-8'}{if $font_page.font_usage.desktop.generator_version ne ''} | v{$font_page.font_usage.desktop.generator_version|escape:'htmlall':'UTF-8'}{/if}</div>
                      {else}
                        <span class="label label-default">Not generated</span>
                      {/if}
                    </td>
                    <td>
                      <button
                        type="button"
                        class="btn btn-primary prestaload-font-usage-generate"
                        data-page-key="{$font_page.key|escape:'htmlall':'UTF-8'}"
                        data-default-label="Generate font usage"
                        data-loading-label="Generating..."
                      >
                        <span>Generate font usage</span>
                      </button>
                    </td>
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>

        <script>
          (function () {
            var buttons = document.querySelectorAll('.prestaload-font-usage-generate');
            var ajaxUrl = {$prestaload_font_usage_generate_ajax_url|json_encode nofilter};
            var toast = document.getElementById('prestaload-font-usage-toast');

            if (!buttons.length || !ajaxUrl || !toast) {
              return;
            }

            function showToast(message, type) {
              toast.className = 'prestaload-toast ' + (type === 'error' ? 'prestaload-toast--error' : 'prestaload-toast--success');
              toast.textContent = message;
              toast.style.display = 'block';
              window.setTimeout(function () {
                toast.style.display = 'none';
              }, 4000);
            }

            buttons.forEach(function (button) {
              button.addEventListener('click', function () {
                if (button.classList.contains('is-loading')) {
                  return;
                }

                var params = new URLSearchParams();
                params.append('ajax', '1');
                params.append('action', 'generateFontUsage');
                params.append('prestaload_font_usage_page', button.getAttribute('data-page-key') || '');

                var defaultLabel = button.getAttribute('data-default-label') || 'Generate font usage';
                var loadingLabel = button.getAttribute('data-loading-label') || 'Generating...';
                var labelNode = button.querySelector('span');

                button.classList.add('is-loading');
                button.disabled = true;
                if (labelNode) {
                  labelNode.textContent = loadingLabel;
                }

                fetch(ajaxUrl, {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                  },
                  body: params.toString()
                })
                  .then(function (response) { return response.json(); })
                  .then(function (payload) {
                    if (!payload || !payload.success) {
                      throw new Error(payload && payload.message ? payload.message : 'Font usage generation failed.');
                    }

                    showToast(payload.message || 'Font usage generated successfully.', 'success');
                    window.location.reload();
                  })
                  .catch(function (error) {
                    showToast(error.message || 'Font usage generation failed.', 'error');
                  })
                  .finally(function () {
                    button.classList.remove('is-loading');
                    button.disabled = false;
                    if (labelNode) {
                      labelNode.textContent = defaultLabel;
                    }
                  });
              });
            });
          }());
        </script>
      {/if}

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
                    {if $asset_group.key === 'css' || $asset_group.key === 'js'}
                      <button
                        type="button"
                        class="btn btn-default prestaload-bulk-minify"
                        data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                        data-default-label="Minify all"
                        data-loading-label="Minifying..."
                      >
                        <span class="prestaload-bulk-minify__label">Minify all</span>
                      </button>
                      <button
                        type="button"
                        class="btn btn-default prestaload-bulk-clear-minified"
                        data-prestaload-group="{$asset_group.key|escape:'htmlall':'UTF-8'}"
                        data-default-label="Clear Minified"
                        data-loading-label="Clearing..."
                      >
                        <span class="prestaload-bulk-clear-minified__label">Clear Minified</span>
                      </button>
                    {/if}
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
                        {assign var=asset_rule value=$prestaload_selected_asset_rules[$asset.normalized_url|default:$asset.url]|default:null}
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
                          <td style="min-width: 420px;">
                            <form method="post" action="" class="prestaload-asset-rule-form">
                              <input type="hidden" name="prestaload_asset_page" value="{$prestaload_selected_asset_page.key|escape:'htmlall':'UTF-8'}">
                              <input type="hidden" name="prestaload_asset_url" value="{$asset.url|escape:'htmlall':'UTF-8'}">
                              <input type="hidden" name="prestaload_asset_type" value="{$asset.type|escape:'htmlall':'UTF-8'}">
                              <div class="prestaload-asset-flags">
                                <button
                                  type="button"
                                  class="btn prestaload-asset-flag {if $asset_rule.disable|default:false}btn-success is-active{else}btn-danger{/if}"
                                  data-prestaload-flag="disable"
                                  data-enabled="{if $asset_rule.disable|default:false}1{else}0{/if}"
                                >
                                  <span class="prestaload-asset-flag__label">
                                    <i class="icon-{if $asset_rule.disable|default:false}check{else}close{/if}"></i>
                                    Disable
                                  </span>
                                </button>
                                <button
                                  type="button"
                                  class="btn prestaload-asset-flag {if $asset_rule.defer|default:false}btn-success is-active{else}btn-danger{/if}"
                                  data-prestaload-flag="defer"
                                  data-enabled="{if $asset_rule.defer|default:false}1{else}0{/if}"
                                >
                                  <span class="prestaload-asset-flag__label">
                                    <i class="icon-{if $asset_rule.defer|default:false}check{else}close{/if}"></i>
                                    Defer
                                  </span>
                                </button>
                                {if $asset.type === 'css' || $asset.type === 'js'}
                                  <button
                                    type="button"
                                    class="btn prestaload-asset-flag {if $asset_rule.minify|default:false}btn-success is-active{else}btn-danger{/if}"
                                    data-prestaload-flag="minify"
                                    data-enabled="{if $asset_rule.minify|default:false}1{else}0{/if}"
                                  >
                                    <span class="prestaload-asset-flag__label">
                                      <i class="icon-{if $asset_rule.minify|default:false}check{else}close{/if}"></i>
                                      Minify
                                    </span>
                                  </button>
                                {/if}
                                {if $asset.type === 'js'}
                                  <button
                                    type="button"
                                    class="btn prestaload-asset-flag {if $asset_rule.load_after_window_load|default:false}btn-success is-active{else}btn-danger{/if}"
                                    data-prestaload-flag="load_after_window_load"
                                    data-enabled="{if $asset_rule.load_after_window_load|default:false}1{else}0{/if}"
                                  >
                                    <span class="prestaload-asset-flag__label">
                                      <i class="icon-{if $asset_rule.load_after_window_load|default:false}check{else}close{/if}"></i>
                                      After load
                                    </span>
                                  </button>
                                {/if}
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

    .prestaload-asset-flags {
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }

    .prestaload-asset-flag {
      position: relative;
      white-space: nowrap;
    }

    .prestaload-asset-flag__label {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .prestaload-asset-flag.is-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.85;
      padding-left: 34px;
    }

    .prestaload-asset-flag.is-loading::before {
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

    .prestaload-bulk-minify.is-loading,
    .prestaload-bulk-clear-minified.is-loading {
      position: relative;
      pointer-events: none;
      opacity: 0.85;
      padding-left: 34px;
    }

    .prestaload-bulk-minify.is-loading::before,
    .prestaload-bulk-clear-minified.is-loading::before {
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
      var assetRuleAjaxUrl = {$prestaload_asset_toggle_flag_ajax_url|json_encode nofilter};
      var assetBulkRuleAjaxUrl = {$prestaload_asset_bulk_rule_ajax_url|json_encode nofilter};
      var assetBulkMinifyAjaxUrl = {$prestaload_asset_bulk_minify_ajax_url|json_encode nofilter};
      var assetBulkClearMinifiedAjaxUrl = {$prestaload_asset_bulk_clear_minified_ajax_url|json_encode nofilter};
      var assetTabLinks = document.querySelectorAll('[data-prestaload-asset-tab]');
      var assetPanels = document.querySelectorAll('[data-prestaload-asset-panel]');
      var assetFlagButtons = document.querySelectorAll('.prestaload-asset-flag');
      var bulkRuleButtons = document.querySelectorAll('.prestaload-bulk-rule');
      var bulkMinifyButtons = document.querySelectorAll('.prestaload-bulk-minify');
      var bulkClearMinifiedButtons = document.querySelectorAll('.prestaload-bulk-clear-minified');
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

      var postForm = function (url, params) {
        return fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          },
          body: params.toString(),
          credentials: 'same-origin'
        }).then(function (response) {
          return response.json();
        });
      };

      var applyFlagButtonState = function (button, enabled) {
        button.setAttribute('data-enabled', enabled ? '1' : '0');
        button.classList.remove('btn-success', 'btn-danger', 'is-active');
        button.classList.add(enabled ? 'btn-success' : 'btn-danger');

        if (enabled) {
          button.classList.add('is-active');
        }

        var label = button.querySelector('.prestaload-asset-flag__label');
        if (!label) {
          return;
        }

        var icon = label.querySelector('i');
        if (icon) {
          icon.className = enabled ? 'icon-check' : 'icon-close';
        }
      };

      var syncRowFlags = function (form, rule) {
        if (!form || !rule) {
          return;
        }

        Array.prototype.forEach.call(form.querySelectorAll('.prestaload-asset-flag'), function (flagButton) {
          var flag = flagButton.getAttribute('data-prestaload-flag');
          applyFlagButtonState(flagButton, !!rule[flag]);
        });
      };

      Array.prototype.forEach.call(assetFlagButtons, function (flagButton) {
        flagButton.addEventListener('click', function () {
          if (!assetRuleAjaxUrl) {
            return;
          }

          var form = flagButton.closest('form');
          if (!form) {
            return;
          }

          var params = new URLSearchParams();
          params.append('prestaload_asset_page', pageSelect ? pageSelect.value : '');
          params.append('prestaload_asset_url', form.querySelector('input[name="prestaload_asset_url"]').value);
          params.append('prestaload_asset_type', form.querySelector('input[name="prestaload_asset_type"]').value);
          params.append('prestaload_asset_flag', flagButton.getAttribute('data-prestaload-flag'));
          params.append('prestaload_asset_enabled', flagButton.getAttribute('data-enabled') === '1' ? '0' : '1');

          flagButton.disabled = true;
          flagButton.classList.add('is-loading');

          postForm(assetRuleAjaxUrl, params).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.message ? payload.message : 'Asset rule update failed.');
            }

            syncRowFlags(form, payload.rule || {});
            showToast(payload.message || 'Asset rule updated.', 'success');
          }).catch(function (error) {
            showToast(error.message || 'Asset rule update failed.', 'error');
          }).finally(function () {
            flagButton.disabled = false;
            flagButton.classList.remove('is-loading');
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

          postForm(assetBulkRuleAjaxUrl, params).then(function (payload) {
            if (!payload || !payload.success) {
              throw new Error(payload && payload.message ? payload.message : 'Bulk update failed.');
            }

            showToast(payload.message || 'Asset rules updated.', 'success');
            window.setTimeout(function () {
              window.location.reload();
            }, 500);
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

      var runBulkMinifyAction = function (buttons, ajaxUrl, successMessageFallback) {
        Array.prototype.forEach.call(buttons, function (bulkButton) {
          bulkButton.addEventListener('click', function () {
            if (!ajaxUrl) {
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
            selected.forEach(function (checkbox, index) {
              params.append('prestaload_asset_urls[' + index + ']', checkbox.value);
              params.append('prestaload_asset_types[' + index + ']', checkbox.getAttribute('data-asset-type') || 'other');
            });

            var label = bulkButton.querySelector('span');
            var defaultLabel = bulkButton.getAttribute('data-default-label') || 'Run';
            var loadingLabel = bulkButton.getAttribute('data-loading-label') || 'Running...';

            bulkButton.disabled = true;
            bulkButton.classList.add('is-loading');
            if (label) {
              label.textContent = loadingLabel;
            }

            postForm(ajaxUrl, params).then(function (payload) {
              if (!payload || !payload.success) {
                throw new Error(payload && payload.message ? payload.message : 'Bulk action failed.');
              }

              showToast(payload.message || successMessageFallback, 'success');
              window.setTimeout(function () {
                window.location.reload();
              }, 500);
            }).catch(function (error) {
              showToast(error.message || 'Bulk action failed.', 'error');
            }).finally(function () {
              bulkButton.disabled = false;
              bulkButton.classList.remove('is-loading');
              if (label) {
                label.textContent = defaultLabel;
              }
            });
          });
        });
      };

      runBulkMinifyAction(bulkMinifyButtons, assetBulkMinifyAjaxUrl, 'Assets minified successfully.');
      runBulkMinifyAction(bulkClearMinifiedButtons, assetBulkClearMinifiedAjaxUrl, 'Cleared selected minified assets.');

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
