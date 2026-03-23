{*
 * PSBoost – Admin Configuration Template
 * PrestaShop 8.x
 *}

<link rel="stylesheet" href="{$module_dir}views/css/admin.css">

<div class="psboost-wrap">

  <!-- Header -->
  <div class="psb-header">
    <div class="psb-header-inner">
      <div class="psb-logo">
        <span class="psb-logo-icon">⚡</span>
        <div>
          <h1 class="psb-title">PSBoost</h1>
          <p class="psb-subtitle">PageSpeed 100 Optimizer</p>
        </div>
      </div>
      <div class="psb-score-ring">
        <svg viewBox="0 0 120 120" class="psb-ring-svg">
          <circle cx="60" cy="60" r="50" class="psb-ring-bg"/>
          <circle cx="60" cy="60" r="50" class="psb-ring-fill" id="psb-ring-fill"
            style="stroke-dasharray: {math equation='$score * 3.14'}px 314px"/>
        </svg>
        <div class="psb-score-inner">
          <span class="psb-score-num" id="psb-score">{$score}</span>
          <span class="psb-score-label">/ 100</span>
        </div>
      </div>
    </div>
    <p class="psb-header-desc">
      Toggle optimizations below. Each enabled feature contributes to your PageSpeed score.
      For best results, enable all options.
    </p>
  </div>

  <!-- Form -->
  <form method="POST" action="{$action_url}" id="psboost-form">
    <input type="hidden" name="submit_psboost" value="1">

    {foreach $groups as $group}
    <div class="psb-group">
      <div class="psb-group-header">
        <span class="psb-group-icon">{$group.icon}</span>
        <h2 class="psb-group-title" style="color:{$group.color}">{$group.title}</h2>
      </div>
      <div class="psb-group-items">
        {foreach $group.items as $item}
        {assign var="cfg_key" value="{$config_prefix}{$item.key}"}
        {assign var="is_enabled" value=$config_values[$cfg_key]}
        <label class="psb-toggle-row {if $is_enabled}psb-enabled{/if}" for="toggle_{$item.key}">
          <div class="psb-toggle-info">
            <span class="psb-toggle-label">{$item.label}</span>
            <span class="psb-toggle-desc">{$item.desc}</span>
          </div>
          <div class="psb-toggle-control">
            <input
              type="checkbox"
              id="toggle_{$item.key}"
              name="{$config_prefix}{$item.key}"
              value="1"
              class="psb-checkbox"
              {if $is_enabled}checked{/if}
              data-score-impact="1"
            >
            <span class="psb-toggle-switch" aria-hidden="true"></span>
          </div>
        </label>
        {/foreach}
      </div>
    </div>
    {/foreach}

    <!-- Action Bar -->
    <div class="psb-action-bar">
      <button type="button" class="psb-btn psb-btn-secondary" id="psb-enable-all">
        ✅ Enable All
      </button>
      <button type="button" class="psb-btn psb-btn-secondary" id="psb-disable-all">
        ❌ Disable All
      </button>
      <button type="submit" class="psb-btn psb-btn-primary">
        💾 Save Configuration
      </button>
    </div>
  </form>

  <!-- Tips Panel -->
  <div class="psb-tips">
    <h3 class="psb-tips-title">💡 Pro Tips for 100/100</h3>
    <div class="psb-tips-grid">
      <div class="psb-tip">
        <span class="psb-tip-icon">🖼️</span>
        <div>
          <strong>LCP Image</strong>
          <p>Add <code>fetchpriority="high"</code> and <code>loading="eager"</code> to your hero/banner image to boost Largest Contentful Paint.</p>
        </div>
      </div>
      <div class="psb-tip">
        <span class="psb-tip-icon">📦</span>
        <div>
          <strong>PrestaShop CCC</strong>
          <p>Go to <em>Advanced Parameters → Performance</em> and enable CCC (Combine, Compress, Cache) for CSS and JS.</p>
        </div>
      </div>
      <div class="psb-tip">
        <span class="psb-tip-icon">🌐</span>
        <div>
          <strong>CDN</strong>
          <p>Serve static assets via a CDN (Cloudflare free tier works great) to maximize TTFB and cache hit rates globally.</p>
        </div>
      </div>
      <div class="psb-tip">
        <span class="psb-tip-icon">🔤</span>
        <div>
          <strong>System Fonts</strong>
          <p>Use system fonts or self-host web fonts with <code>font-display: swap</code> to eliminate Google Fonts render-blocking.</p>
        </div>
      </div>
      <div class="psb-tip">
        <span class="psb-tip-icon">🗄️</span>
        <div>
          <strong>PHP OPcache</strong>
          <p>Enable PHP OPcache on your server to dramatically reduce TTFB (Time To First Byte).</p>
        </div>
      </div>
      <div class="psb-tip">
        <span class="psb-tip-icon">📐</span>
        <div>
          <strong>Image Dimensions</strong>
          <p>Always set explicit <code>width</code> and <code>height</code> attributes on images to prevent layout shift (CLS).</p>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="{$module_dir}views/js/admin.js"></script>
