{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-user-md"></i>{else}<img width="16" height="16" src="../img/admin/binoculars.png" alt=""/>{/if}&nbsp;{l s='Configuration' mod='jprestaspeedpack'} ({$diagnostic_count|escape:'html':'UTF-8'})</h3>
<form id="pagecache_form_diagnostic" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="diagnostic"/>
    <fieldset>
        {if $diagnostic_count == 0}
            <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/check.png" alt="ok" width="24" height="24"/> {l s='Everything is good!' mod='jprestaspeedpack'}
        {/if}
        {foreach $diagnostic['error'] as $diagMsg}
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-danger" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div></div>
            {else}
                <div class="error clear" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div>
            {/if}
        {/foreach}
        {foreach $diagnostic['warn'] as $diagMsg}
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-warning" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div></div>
            {else}
                <div class="warn clear" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div>
            {/if}
        {/foreach}
        {foreach $diagnostic['info'] as $diagMsg}
            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-info" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div></div>
            {else}
                <div class="hint clear" style="display: block;">&nbsp;{$diagMsg['msg']|escape:'html':'UTF-8'}.{if array_key_exists('link', $diagMsg)} <a href="{$diagMsg['link']|escape:'html':'UTF-8'}">{$diagMsg['link_title']|escape:'html':'UTF-8'}.</a>{/if}</div>
            {/if}
        {/foreach}
    </fieldset>
</form>
</div>

<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-desktop"></i>{else}<img width="16" height="16" src="../img/admin/informations.png" alt=""/>{/if}&nbsp;{l s='System informations' mod='jprestaspeedpack'}</h3>
    {include file='./get-content-tab-diagnostic-infos.tpl' systemInfos=$systemInfos}
</div>

<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-exclamation-triangle"></i>{else}<img width="16" height="16" src="../img/admin/error.png" alt=""/>{/if}&nbsp;{l s='Slower modules' mod='jprestaspeedpack'}</h3>

    {if $avec_bootstrap}
        <div class="bootstrap">
            <div class="alert alert-info" style="display: block;">
                {l s='This table shows you the slower modules that could slow down your shop' mod='jprestaspeedpack'}
            </div>
            {if $pagecache_profiling_not_available}
                <div class="alert alert-warning" style="display: block;">
                    &nbsp;{l s='This tools is only available from Prestashop 1.7' mod='jprestaspeedpack'}
                </div>
            {else}
                {if !$module_enabled}
                    <div class="alert alert-warning" style="display: block;">
                        &nbsp;{l s='This tools is not available if the module or if the shop is not enabled' mod='jprestaspeedpack'}
                    </div>
                {/if}
                {if $pagecache_profiling_max_reached}
                    <div class="alert alert-warning" style="display: block;">
                        {l s='To preserve performances, the profiling has been suspended because it reaches the maximum number of records' mod='jprestaspeedpack'}: {$pagecache_profiling_max|escape:'html':'UTF-8'}
                    </div>
                {/if}
            {/if}
        </div>
    {else}
        <div class="hint clear">
            <div>
                {l s='This table shows you the slower modules that could slow down your shop' mod='jprestaspeedpack'}
            </div>
        </div>
        {if $pagecache_profiling_not_available}
            <div class="warn clear" style="display: block;">&nbsp;{l s='This tools is only available from Prestashop 1.7' mod='jprestaspeedpack'}</div>
        {else}
            {if !$module_enabled}
                <div class="warn clear" style="display: block;">&nbsp;{l s='This tools is not available if the module or if the shop is not enabled' mod='jprestaspeedpack'}</div>
            {/if}
            {if $pagecache_profiling_max_reached}
                <div class="warn clear" style="display: block;">&nbsp;{l s='To preserve performances, the profiling has been suspended because it reaches the maximum number of records' mod='jprestaspeedpack'}: {$pagecache_profiling_max|escape:'html':'UTF-8'}</div>
            {/if}
        {/if}
    {/if}
    {if $module_enabled && !$pagecache_profiling_not_available}
        <form id="pagecache_form_profiling" action="{$request_uri|escape:'html':'UTF-8'}" method="post" class="form-inline">
            <input type="hidden" name="submitModule" value="true"/>
            <input type="hidden" name="pctab" value="diagnostic"/>
            <fieldset style="margin: 10px 0">
                {if $pagecache_profiling}
                    <div class="form-group">
                        <label for="pagecache_profiling_min_ms">{l s='Only record modules that last more than' mod='jprestaspeedpack'}</label>
                        <div class="input-group">
                            <input type="number" min="0" style="text-align:right" class="form-control" id="pagecache_profiling_min_ms" name="pagecache_profiling_min_ms" value="{$pagecache_profiling_min_ms|escape:'html':'UTF-8'}">
                            <div class="input-group-addon">ms</div>
                        </div>
                    </div>
                    <button type="submit" id="submitModuleProfilingMinMs" name="submitModuleProfilingMinMs" value="1" class="btn btn-default">{l s='Save' mod='jprestaspeedpack'}</button>
                {/if}
            </fieldset>
            <fieldset style="margin: 10px 0">
                <div class="bootstrap">
                    <button type="submit" value="1" id="submitModuleOnOffProfiling" name="submitModuleOnOffProfiling"
                            class="btn btn-default">
                        <i class="process-icon-off"
                           style="color:{if $pagecache_profiling}red{else}rgb(139, 201, 84){/if}"></i> {if $pagecache_profiling}{l s='Disable profiling' mod='jprestaspeedpack'}{else}{l s='Enable profiling' mod='jprestaspeedpack'}{/if}
                    </button>
                    {if $pagecache_profiling}
                        <button type="submit" value="1" id="submitModuleResetProfiling" name="submitModuleResetProfiling"
                                class="btn btn-default">
                            <i class="process-icon-delete"
                               style="color:orange"></i> {l s='Clear profiling datas' mod='jprestaspeedpack'}
                        </button>
                        <button type="button" value="1" id="submitModuleRefreshProfiling" name="submitModuleRefreshProfiling"
                                class="btn btn-default" onclick="$('#profilingTable').DataTable().ajax.reload();return false;">
                            <i class="process-icon-refresh"></i> {l s='Refresh' mod='jprestaspeedpack'}
                        </button>
                    {/if}
                </div>
            </fieldset>
        </form>
        {if $pagecache_profiling}
            <script type="application/javascript">
                $(document).ready(function () {
                    $('#profilingTable').DataTable({
                        processing: true,
                        serverSide: true,
                        searching: false,
                        ajax: '{$pagecache_profiling_datas_url|escape:'javascript':'UTF-8'}',
                        language: {
                            processing:     "{l s='Loading datas...' mod='jprestaspeedpack'}",
                            search:         "{l s='Search...' mod='jprestaspeedpack'}:",
                            lengthMenu:     "{l s='Showing _MENU_ rows' mod='jprestaspeedpack'}",
                            info:           "{l s='Showing _START_ to _END_ of _TOTAL_ rows' mod='jprestaspeedpack'}",
                            infoEmpty:      "{l s='Showing 0 to 0 of 0 row' mod='jprestaspeedpack'}",
                            infoFiltered:   "{l s='Filtered of _MAX_ rows' mod='jprestaspeedpack'}",
                            infoPostFix:    "",
                            loadingRecords: "{l s='Loading datas...' mod='jprestaspeedpack'}",
                            zeroRecords:    "{l s='No module to display' mod='jprestaspeedpack'}",
                            emptyTable:     "{l s='No module to display' mod='jprestaspeedpack'}",
                            paginate: {
                                first:      "{l s='First' mod='jprestaspeedpack'}",
                                previous:   "{l s='Previous' mod='jprestaspeedpack'}",
                                next:       "{l s='Next' mod='jprestaspeedpack'}",
                                last:       "{l s='Last' mod='jprestaspeedpack'}"
                            },
                            aria: {
                                sortAscending:  ": {l s='Click to sort ascending' mod='jprestaspeedpack'}",
                                sortDescending: ": {l s='Click to sort descending' mod='jprestaspeedpack'}"
                            }
                        }
                    });
                });
            </script>
            <table id="profilingTable" class="display cell-border compact stripe" style="width:100%">
                <thead>
                <tr>
                    <th>{l s='Module' mod='jprestaspeedpack'}</th>
                    <th>{l s='Code' mod='jprestaspeedpack'}</th>
                    <th>{l s='Execution date' mod='jprestaspeedpack'}</th>
                    <th>{l s='Duration' mod='jprestaspeedpack'}</th>
                </tr>
                </thead>
            </table>
        {/if}
    {/if}
</div>
