<style>
.prestaload-wrap { max-width: 680px; margin: 0 auto; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
.prestaload-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; }
.prestaload-logo { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; color: #1a1a1a; }
.prestaload-badge { display: inline-flex; align-items: center; gap: 6px; border-radius: 99px; padding: 4px 12px; font-size: 12px; font-weight: 600; }
.prestaload-badge--connected { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.prestaload-badge--pending { background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; }
.prestaload-dot { width: 7px; height: 7px; border-radius: 50%; background: #16a34a; animation: prestaload-pulse 2s infinite; display: inline-block; }
@keyframes prestaload-pulse { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
.prestaload-alert { padding: 14px 16px; border-radius: 10px; font-size: 14px; margin-bottom: 16px; }
.prestaload-alert--error { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.prestaload-alert--success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.prestaload-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; margin-bottom: 16px; }
.prestaload-card--success { border-color: #bbf7d0; background: #f0fdf4; display: flex; gap: 16px; align-items: flex-start; }
.prestaload-card--muted { background: #f9fafb; }
.prestaload-success-title { margin: 0 0 6px; font-size: 16px; font-weight: 700; color: #15803d; }
.prestaload-success-text { margin: 0; font-size: 14px; color: #166534; line-height: 1.6; }
.prestaload-section-title { margin: 0 0 8px; font-size: 15px; font-weight: 700; color: #111827; }
.prestaload-section-text { margin: 0 0 20px; font-size: 14px; color: #6b7280; line-height: 1.6; }
.prestaload-field { margin-bottom: 20px; }
.prestaload-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #374151; }
.prestaload-input { width: 100%; box-sizing: border-box; padding: 10px 14px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: monospace; color: #1f2937; outline: none; transition: border-color .15s; }
.prestaload-input:focus { border-color: #A7F54A; box-shadow: 0 0 0 3px rgba(167,245,74,.15); }
.prestaload-hint { margin: 8px 0 0; font-size: 12px; color: #9ca3af; line-height: 1.5; }
.prestaload-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; border-radius: 8px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: opacity .15s; }
.prestaload-btn:hover { opacity: .88; }
.prestaload-btn--primary { background: #A7F54A; color: #32412a; }
.prestaload-btn--danger { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.prestaload-meta { margin: 0 0 14px; font-size: 13px; color: #6b7280; line-height: 1.6; }
.prestaload-endpoint-list { display: grid; gap: 12px; }
.prestaload-endpoint { border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #fcfcfd; }
.prestaload-endpoint-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
.prestaload-endpoint-title { margin: 0; font-size: 14px; font-weight: 700; color: #111827; }
.prestaload-pill { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 10px; font-size: 11px; font-weight: 700; }
.prestaload-pill--ok { background: #ecfdf3; color: #15803d; border: 1px solid #bbf7d0; }
.prestaload-pill--bad { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
.prestaload-endpoint-code { display: block; margin: 0 0 8px; font-size: 12px; color: #1f2937; word-break: break-all; }
.prestaload-endpoint-grid { display: grid; grid-template-columns: 120px 1fr; gap: 6px 12px; font-size: 12px; color: #4b5563; }
.prestaload-endpoint-label { font-weight: 700; color: #374151; }
</style>

<div class="prestaload-wrap">

    <div class="prestaload-header">
        <div class="prestaload-logo">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <circle cx="14" cy="14" r="14" fill="#A7F54A"/>
                <path d="M8 14l4 4 8-8" stroke="#32412a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Prestaload
        </div>
        {if $prestaload_connected}
            <span class="prestaload-badge prestaload-badge--connected">
                <span class="prestaload-dot"></span>
                {l s='Connected' mod='prestaload'}
            </span>
        {else}
            <span class="prestaload-badge prestaload-badge--pending">
                {l s='Not connected' mod='prestaload'}
            </span>
        {/if}
    </div>

    {if $prestaload_error}
        <div class="prestaload-alert prestaload-alert--error">{$prestaload_error|escape:'html'}</div>
    {/if}

    {if $prestaload_success}
        <div class="prestaload-alert prestaload-alert--success">{$prestaload_success|escape:'html'}</div>
    {/if}

    {if $prestaload_connected}

        <div class="prestaload-card prestaload-card--success">
            <div style="flex-shrink:0;margin-top:2px;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="m9 12 2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="#16a34a" stroke-width="2"/>
                </svg>
            </div>
            <div>
                <h3 class="prestaload-success-title">
                    {l s='Welcome to Prestaload!' mod='prestaload'}
                </h3>
                <p class="prestaload-success-text">
                    {l s='This store is connected to the' mod='prestaload'}
                    <strong>{$prestaload_integration|escape:'html'}</strong>
                    {l s='integration. Your optimization rules are being applied automatically.' mod='prestaload'}
                </p>
            </div>
        </div>

        <div class="prestaload-card">
            <h3 class="prestaload-section-title">{l s='Endpoint diagnostics' mod='prestaload'}</h3>
            <p class="prestaload-meta">
                {l s='Current shop URL:' mod='prestaload'}
                <strong>{$prestaload_current_shop_url|escape:'html'}</strong>
            </p>
            <div class="prestaload-endpoint-list">
                {foreach from=$prestaload_endpoint_diagnostics item=endpoint}
                    <div class="prestaload-endpoint">
                        <div class="prestaload-endpoint-head">
                            <h4 class="prestaload-endpoint-title">{$endpoint.label|escape:'html'}</h4>
                            {if $endpoint.probe.ok}
                                <span class="prestaload-pill prestaload-pill--ok">
                                    {l s='HTTP' mod='prestaload'} {$endpoint.probe.status|escape:'html'}
                                </span>
                            {else}
                                <span class="prestaload-pill prestaload-pill--bad">
                                    {if $endpoint.probe.status}
                                        {l s='HTTP' mod='prestaload'} {$endpoint.probe.status|escape:'html'}
                                    {elseif $endpoint.probe.error}
                                        {$endpoint.probe.error|escape:'html'}
                                    {else}
                                        {l s='No response' mod='prestaload'}
                                    {/if}
                                </span>
                            {/if}
                        </div>
                        <code class="prestaload-endpoint-code">{$endpoint.url|escape:'html'}</code>
                        <div class="prestaload-endpoint-grid">
                            <div class="prestaload-endpoint-label">{l s='File' mod='prestaload'}</div>
                            <div>
                                {$endpoint.file|escape:'html'}
                                {if $endpoint.file_exists}
                                    <span style="color:#15803d;">({l s='exists' mod='prestaload'})</span>
                                {else}
                                    <span style="color:#be123c;">({l s='missing' mod='prestaload'})</span>
                                {/if}
                            </div>
                            <div class="prestaload-endpoint-label">{l s='Content-Type' mod='prestaload'}</div>
                            <div>{$endpoint.probe.content_type|default:'-'|escape:'html'}</div>
                            <div class="prestaload-endpoint-label">{l s='Redirect' mod='prestaload'}</div>
                            <div>{$endpoint.probe.location|default:'-'|escape:'html'}</div>
                            <div class="prestaload-endpoint-label">{l s='Preview' mod='prestaload'}</div>
                            <div>{$endpoint.probe.body_preview|default:'-'|escape:'html'}</div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>

        <div class="prestaload-card">
            <h3 class="prestaload-section-title">{l s='Disconnect' mod='prestaload'}</h3>
            <p class="prestaload-section-text">
                {l s='Disconnecting will stop Prestaload from managing this store\'s optimizations.' mod='prestaload'}
            </p>
            <form method="post" action="{$prestaload_action_url|escape:'html'}">
                <input type="hidden" name="prestaload_disconnect" value="1">
                <button type="submit" class="prestaload-btn prestaload-btn--danger"
                    onclick="return confirm('{l s='Disconnect Prestaload? Optimizations will stop.' mod='prestaload' js=1}')">
                    {l s='Disconnect' mod='prestaload'}
                </button>
            </form>
        </div>

    {else}

        <div class="prestaload-card">
            <h3 class="prestaload-section-title">{l s='Connect to Prestaload' mod='prestaload'}</h3>
            <p class="prestaload-section-text">
                {l s='Paste your API key from your Prestaload dashboard to connect this PrestaShop store.' mod='prestaload'}
            </p>

            <form method="post" action="{$prestaload_action_url|escape:'html'}">
                <input type="hidden" name="prestaload_connect" value="1">

                <div class="prestaload-field">
                    <label class="prestaload-label" for="prestaload_api_key">
                        {l s='API Key' mod='prestaload'}
                    </label>
                    <input
                        class="prestaload-input"
                        type="text"
                        id="prestaload_api_key"
                        name="api_key"
                        placeholder="{l s='Paste your Prestaload API key here' mod='prestaload'}"
                        autocomplete="off"
                        spellcheck="false"
                    >
                    <p class="prestaload-hint">
                        {l s='Find this key in your Prestaload dashboard under Integrations → Your Integration → API Key.' mod='prestaload'}
                    </p>
                </div>

                <button type="submit" class="prestaload-btn prestaload-btn--primary">
                    {l s='Connect' mod='prestaload'}
                </button>
            </form>
        </div>

        <div class="prestaload-card prestaload-card--muted">
            <p class="prestaload-section-text" style="margin:0;">
                {l s='Don\'t have a Prestaload account?' mod='prestaload'}
                <a href="https://prestaload.com" target="_blank" rel="noopener">
                    {l s='Get started free →' mod='prestaload'}
                </a>
            </p>
        </div>

    {/if}

</div>
