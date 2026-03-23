<div class="panel">
  <h3>PrestaLoad</h3>
  <p>The module is currently limited to store connection.</p>
</div>

<form method="post" action="">
  <div class="panel">
    <h3>Connection</h3>

    <div class="form-group">
      <label for="prestaload_api_base_url">API base URL</label>
      <input
        id="prestaload_api_base_url"
        class="form-control"
        type="text"
        name="PRESTALOAD_API_BASE_URL"
        value="{$pl_api_base_url|escape:'htmlall':'UTF-8'}"
      >
    </div>

    <p><strong>Module version:</strong> {$pl_module_version|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Connected:</strong> {if $pl_connected}Yes{else}No{/if}</p>
    <p><strong>Store key:</strong> {$pl_store_key|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Store ID:</strong> {$pl_store_id|escape:'htmlall':'UTF-8'}</p>
    <p><strong>Connected at:</strong> {$pl_connected_at|escape:'htmlall':'UTF-8'}</p>

    <div class="panel-footer">
      <button type="submit" name="submitPrestaLoadSaveSettings" class="btn btn-default">
        Save settings
      </button>
      <button type="submit" name="submitPrestaLoadConnect" class="btn btn-primary">
        Connect store
      </button>
      <button type="submit" name="submitPrestaLoadPing" class="btn btn-default">
        Ping API
      </button>
      <button type="submit" name="submitPrestaLoadDisconnect" class="btn btn-danger">
        Disconnect
      </button>
    </div>
  </div>
</form>
