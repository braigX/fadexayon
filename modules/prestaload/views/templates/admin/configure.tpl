<div class="panel" style="max-width: 860px; margin: 10px auto;">
  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding: 20px 0 12px;">
    <div>
      <h2 style="margin:0; font-size: 30px; color:#0d2f6f;">PrestaLoad</h2>
      <p style="margin: 8px 0 0; color:#6b7280;">{$pl_i18n.subtitle|escape:'html':'UTF-8'}</p>
    </div>

    <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
      {if $pl_connected}
        <form method="post" style="margin:0;">
          <button type="submit" name="submitPrestaLoadPing" class="btn btn-default" style="display:inline-flex; align-items:center; gap:8px; line-height:1.2; padding:8px 12px; border-radius:999px; font-weight:600;">
            <span style="display:inline-flex; width:22px; height:22px; align-items:center; justify-content:center; border-radius:999px; background:#e8f1ff; color:#0d5bd7; font-size:13px; font-weight:700;">↻</span>
            <span>{$pl_i18n.ping_cta|escape:'html':'UTF-8'}</span>
          </button>
        </form>
        <form method="post" style="margin:0;">
          <button type="submit" name="submitPrestaLoadDisconnect" class="btn btn-danger" style="display:inline-flex; align-items:center; gap:8px; line-height:1.2; padding:8px 12px; border-radius:999px; font-weight:600;">
            <span style="display:inline-flex; width:22px; height:22px; align-items:center; justify-content:center; border-radius:999px; background:rgba(255,255,255,0.2); color:#ffffff; font-size:12px; font-weight:700;">×</span>
            <span>{$pl_i18n.disconnect_cta|escape:'html':'UTF-8'}</span>
          </button>
        </form>
      {/if}
    </div>
  </div>

  <hr/>

  <div style="padding: 12px 8px;">
    <p>
      <strong>{$pl_i18n.connection_status|escape:'html':'UTF-8'}</strong>
      {if $pl_connected}
        <span style="color:#1f8f3d;">{$pl_i18n.connected|escape:'html':'UTF-8'}</span>
      {else}
        <span style="color:#c93d3d;">{$pl_i18n.not_connected|escape:'html':'UTF-8'}</span>
      {/if}
    </p>
    {if $pl_connected}
      <p><strong>{$pl_i18n.connected_at|escape:'html':'UTF-8'}</strong> {$pl_connected_at|escape:'html':'UTF-8'}</p>
    {/if}
  </div>

  {if !$pl_connected}
    <hr/>

    <form method="post" target="_blank" style="padding: 8px; text-align:center;">
      <div style="text-align:center; margin-top: 20px;">
        <button type="submit" name="submitPrestaLoadConnect" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; padding:10px 18px; line-height:1.2; border-radius:999px; font-weight:600;">
          <span style="display:inline-flex; width:22px; height:22px; align-items:center; justify-content:center; border-radius:999px; background:rgba(255,255,255,0.22); color:#ffffff; font-size:13px; font-weight:700;">+</span>
          <span>{$pl_i18n.connect_cta|escape:'html':'UTF-8'}</span>
        </button>
      </div>
    </form>
  {/if}

  <hr/>

  <div style="padding: 8px;">
    <h3 style="margin: 0 0 14px; font-size: 18px; color:#0d2f6f;">{$pl_i18n.details_title|escape:'html':'UTF-8'}</h3>

    <div class="table-responsive">
      <table class="table" style="margin-bottom:0;">
        <tbody>
          <tr>
            <th style="width: 180px;">{$pl_i18n.module_version|escape:'html':'UTF-8'}</th>
            <td>{$pl_module_version|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <th>{$pl_i18n.store_key|escape:'html':'UTF-8'}</th>
            <td style="word-break: break-all;">{$pl_store_key|escape:'html':'UTF-8'}</td>
          </tr>
          <tr>
            <th>{$pl_i18n.store_id|escape:'html':'UTF-8'}</th>
            <td style="word-break: break-all;">{$pl_store_id|escape:'html':'UTF-8'}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div style="margin-top: 18px; font-size: 13px; text-align:center;">
      <a href="{$pl_help_center_url|escape:'html':'UTF-8'}" target="_blank" rel="noopener noreferrer">{$pl_i18n.help_center|escape:'html':'UTF-8'}</a>
      <span style="padding: 0 8px; color: #9ca3af;">|</span>
      <a href="{$pl_terms_url|escape:'html':'UTF-8'}" target="_blank" rel="noopener noreferrer">{$pl_i18n.terms|escape:'html':'UTF-8'}</a>
      <span style="padding: 0 8px; color: #9ca3af;">|</span>
      <a href="{$pl_privacy_url|escape:'html':'UTF-8'}" target="_blank" rel="noopener noreferrer">{$pl_i18n.privacy|escape:'html':'UTF-8'}</a>
    </div>
  </div>
</div>
