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
    </div>
  </div>
</div>
