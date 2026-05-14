<div class="panel assets-analyser" id="assets-analyser" data-ajax-url="{$assets_analyser_ajax_url|escape:'html':'UTF-8'}">
  <div class="panel-heading">
    <i class="icon-search"></i> {l s='Assets Analyser' mod='AssetsAnalyser'}
  </div>

  <div class="assets-analyser__grid">
    <div class="form-group">
      <label class="control-label" for="assets-analyser-page-type">{l s='Page type' mod='AssetsAnalyser'}</label>
      <select id="assets-analyser-page-type" class="form-control">
        {foreach from=$assets_analyser_page_types key=page_type item=page_label}
          <option value="{$page_type|escape:'html':'UTF-8'}" {if $page_type == $assets_analyser_default_page_type}selected{/if}>{$page_label|escape:'html':'UTF-8'}</option>
        {/foreach}
      </select>
    </div>

    <div class="form-group">
      <label class="control-label" for="assets-analyser-url-select">{l s='Generated URL' mod='AssetsAnalyser'}</label>
      <select id="assets-analyser-url-select" class="form-control">
        <option value="">{l s='Loading URLs...' mod='AssetsAnalyser'}</option>
      </select>
    </div>

    <div class="form-group assets-analyser__manual">
      <label class="control-label" for="assets-analyser-manual-url">{l s='Manual URL' mod='AssetsAnalyser'}</label>
      <input id="assets-analyser-manual-url" class="form-control" type="url" placeholder="https://example.com/page" />
    </div>

    <div class="form-group assets-analyser__actions">
      <button type="button" id="assets-analyser-refresh" class="btn btn-default">
        <i class="icon-refresh"></i> {l s='Refresh URLs' mod='AssetsAnalyser'}
      </button>
      <button type="button" id="assets-analyser-analyze" class="btn btn-primary">
        <i class="icon-search"></i> {l s='Analyze URL' mod='AssetsAnalyser'}
      </button>
      <button type="button" id="assets-analyser-coverage" class="btn btn-default" disabled>
        <i class="icon-bar-chart"></i> {l s='Analyze Coverage' mod='AssetsAnalyser'}
      </button>
      <button type="button" id="assets-analyser-export" class="btn btn-default" disabled>
        <i class="icon-download"></i> {l s='Export XML' mod='AssetsAnalyser'}
      </button>
    </div>
  </div>

  <div id="assets-analyser-message" class="alert assets-analyser__message" style="display:none;"></div>

  <div id="assets-analyser-summary" class="assets-analyser__summary" style="display:none;"></div>

  <div id="assets-analyser-results" class="assets-analyser__results" style="display:none;">
    <h3>{l s='CSS assets' mod='AssetsAnalyser'} <span id="assets-analyser-css-count" class="badge">0</span></h3>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>{l s='Source' mod='AssetsAnalyser'}</th>
            <th>{l s='Module' mod='AssetsAnalyser'}</th>
            <th>{l s='Coverage' mod='AssetsAnalyser'}</th>
            <th>{l s='Unused %' mod='AssetsAnalyser'}</th>
            <th>{l s='Confidence' mod='AssetsAnalyser'}</th>
            <th>{l s='Path' mod='AssetsAnalyser'}</th>
            <th>{l s='URL' mod='AssetsAnalyser'}</th>
            <th>{l s='Note' mod='AssetsAnalyser'}</th>
          </tr>
        </thead>
        <tbody id="assets-analyser-css-body"></tbody>
      </table>
    </div>

    <h3>{l s='JavaScript assets' mod='AssetsAnalyser'} <span id="assets-analyser-js-count" class="badge">0</span></h3>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>{l s='Source' mod='AssetsAnalyser'}</th>
            <th>{l s='Module' mod='AssetsAnalyser'}</th>
            <th>{l s='Coverage' mod='AssetsAnalyser'}</th>
            <th>{l s='Unused %' mod='AssetsAnalyser'}</th>
            <th>{l s='Confidence' mod='AssetsAnalyser'}</th>
            <th>{l s='Path' mod='AssetsAnalyser'}</th>
            <th>{l s='URL' mod='AssetsAnalyser'}</th>
            <th>{l s='Note' mod='AssetsAnalyser'}</th>
          </tr>
        </thead>
        <tbody id="assets-analyser-js-body"></tbody>
      </table>
    </div>
  </div>
</div>
