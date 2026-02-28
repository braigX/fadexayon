{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="application/javascript">
    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';

        let datasTable = $('#datasTable')
            .on('error.dt', function (e, settings, techNote, message) {
                console.error('Page Cache - Cannot display cache datas: ', message);
            })
            .DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: '{$pagecache_datas_url|escape:'javascript':'UTF-8'}',
            columns: [
                { orderable: false },
                {  },
                { orderable: false, width: '5rem' },
                { orderable: false, width: '3rem' },
                { width: '7rem' },
                { width: '3rem' },
                { width: '3rem' },
            ],
            order: [],
            language: {
                processing:     "{l s='Loading datas...' mod='jprestaspeedpack'}",
                search:         "{l s='Search' mod='jprestaspeedpack'}:",
                lengthMenu:     "{l s='Showing _MENU_ rows' mod='jprestaspeedpack'}",
                info:           "{l s='Showing _START_ to _END_ of _TOTAL_ rows' mod='jprestaspeedpack'}",
                infoEmpty:      "{l s='Showing 0 to 0 of 0 row' mod='jprestaspeedpack'}",
                infoFiltered:   "{l s='Filtered of _MAX_ rows' mod='jprestaspeedpack'}",
                infoPostFix:    "",
                loadingRecords: "{l s='Loading datas...' mod='jprestaspeedpack'}",
                zeroRecords:    "{l s='No data to display' mod='jprestaspeedpack'}",
                emptyTable:     "{l s='No data to display' mod='jprestaspeedpack'}",
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
            },
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, 100 ],
                [ '10 {l s='rows' mod='jprestaspeedpack'}', '25 {l s='rows' mod='jprestaspeedpack'}', '50 {l s='rows' mod='jprestaspeedpack'}', '100 {l s='rows' mod='jprestaspeedpack'}' ]
            ],
            buttons: [
                'pageLength'
            ],
        });
        let datasContextsTable = $('#datasContextsTable')
            .on('error.dt', function (e, settings, techNote, message) {
                console.error('Page Cache - Cannot display contexts datas: ', message);
            })
            .DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: '{$pagecache_datas_url|escape:'javascript':'UTF-8'}&type=contexts',
            columns: [
                { orderable: false },
                { orderable: false },
                { },
                { },
                { },
                { orderable: false },
            ],
            order: [],
            language: {
                processing:     "{l s='Loading datas...' mod='jprestaspeedpack'}",
                search:         "{l s='Search' mod='jprestaspeedpack'}:",
                lengthMenu:     "{l s='Showing _MENU_ rows' mod='jprestaspeedpack'}",
                info:           "{l s='Showing _START_ to _END_ of _TOTAL_ rows' mod='jprestaspeedpack'}",
                infoEmpty:      "{l s='Showing 0 to 0 of 0 row' mod='jprestaspeedpack'}",
                infoFiltered:   "{l s='Filtered of _MAX_ rows' mod='jprestaspeedpack'}",
                infoPostFix:    "",
                loadingRecords: "{l s='Loading datas...' mod='jprestaspeedpack'}",
                zeroRecords:    "{l s='No data to display' mod='jprestaspeedpack'}",
                emptyTable:     "{l s='No data to display' mod='jprestaspeedpack'}",
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
            },
            dom: 'Bfrtip',
            lengthMenu: [
                [ 10, 25, 50, 100 ],
                [ '10 {l s='rows' mod='jprestaspeedpack'}', '25 {l s='rows' mod='jprestaspeedpack'}', '50 {l s='rows' mod='jprestaspeedpack'}', '100 {l s='rows' mod='jprestaspeedpack'}' ]
            ],
            buttons: [
                'pageLength'
            ],
        });
        $('#searchObject').on('keyup', function () {
            datasTable
                .columns(3)
                .search(this.value, false, false, false)
                .draw();
        });
        $('#searchContext').on('change', function () {
            datasTable
                .columns(1)
                .search(this.value, false, false, true)
                .draw();
        });
        $('#searchController').on('change', function () {
            datasTable
                .columns(2)
                .search(this.value, false, false, true)
                .draw();
        });
        $('#searchURL').on('change', function () {
            datasTable
                .columns(0)
                .search(this.value, false, true, true)
                .draw();
        });
        $('#searchContextDevice,#searchContextLang,#searchContextCur,#searchContextCountry,#searchContextSpecs,#searchContextGroups').on('change', function () {
            datasContextsTable
                .search('id_device=' + $('#searchContextDevice').val() + ',id_lang=' + $('#searchContextLang').val() + ',id_currency=' + $('#searchContextCur').val() + ',id_country=' + $('#searchContextCountry').val() + ',id_specs=' + $('#searchContextSpecs').val() + ',id_group=' + $('#searchContextGroups').val())
                .draw();
        });
        $('#refreshDatas').on('click', function () {
            datasTable.ajax.reload();
        });
        $('#clearCacheDatas').on('click', function () {
            let parameters = datasTable.ajax.params();
            parameters.clear = true;
            $.ajax({ url: datasTable.ajax.url(), method: 'post', data: parameters,
                success: function(response) {
                    datasTable.ajax.reload();
                },
                error: function(result, status, error) {
                    console.log(result + ' - ' + status + ' - ' + error);
                }});
        });
    });
</script>
<style>
    .dataTables_processing {
        border: 2px solid orange;
        border-radius: 5px;
        padding: 0 !important;
        line-height: 3rem;
        height: auto !important;
        z-index: 99;
        font-weight: bold;
    }
    .bootstrap .label-default {
        border: 1px solid #999;
        background-color: transparent;
        padding: 0 2px;
    }
    #datasTable tr td:nth-child(n+3),#datasTable th,
    #datasContextsTable th, #datasContextsTable tr td:nth-child(n+3){
        text-align: center;
    }
    #datasTable tr td:last-child {
        text-align: right;
    }
    #datasTable_filter {
        display: none;
    }
    #datasTable span.label {
        cursor: help;
        padding: 0px 2px;
        margin-right: 1px;
    }
    {if !$avec_bootstrap}tfoot input,tfoot select { width:95%; }{/if}
    #datasContextsTable_filter {
        display: none;
    }
</style>
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-line-chart"></i>{else}<img width="16" height="16" src="../img/admin/AdminStats.gif" alt=""/>{/if}&nbsp;{l s='Cached pages' mod='jprestaspeedpack'}</h3>
    <div class="alert alert-info">{l s='Here you can browse all cached pages. This can be usefull to debug.' mod='jprestaspeedpack'}</div>
    <fieldset class="cachemanagement">
        <table id="datasTable" class="display cell-border compact stripe" style="width:100%">
            <colgroup>
                <col width="*">
                <col width="*">
                <col width="0*">
                <col width="0*">
                <col width="0*">
                <col width="0*">
                <col width="0*">
            </colgroup>
            <thead>
            <tr>
                <th>{l s='URL' mod='jprestaspeedpack'}</th>
                <th>{l s='Context' mod='jprestaspeedpack'}</th>
                <th>{l s='Controller' mod='jprestaspeedpack'}</th>
                <th>{l s='ID' mod='jprestaspeedpack'}</th>
                <th>{l s='Last generation' mod='jprestaspeedpack'}</th>
                <th>{l s='Cleared' mod='jprestaspeedpack'}</th>
                <th>{l s='Hit/Missed' mod='jprestaspeedpack'}</th>
            </tr>
            </thead>
            <tbody>
                <tr><td>-</td><td>-</td><td>--------------</td><td>----</td><td>----/--/-- --:--:--</td><td>-</td><td>- / - (--%)</td></tr>
                <tr><td>-</td><td>-</td><td>--------------</td><td>----</td><td>----/--/-- --:--:--</td><td>-</td><td>- / - (--%)</td></tr>
                <tr><td>-</td><td>-</td><td>--------------</td><td>----</td><td>----/--/-- --:--:--</td><td>-</td><td>- / - (--%)</td></tr>
            </tbody>
            <tfoot>
            <tr>
                <th><input type="text" name="searchURL" id="searchURL" placeholder="{l s='Find in URL (click outside to trigger the search)' mod='jprestaspeedpack'}" style="padding:4px"></th>
                <th>
                    <select name="searchContext" id="searchContext" style="padding:4px">
                        <option></option>
                        {foreach $pagecache_contexts as $context}
                            <option value="{$context.id|intval}">#{$context.id|intval}</option>
                        {/foreach}
                    </select>
                </th>
                <th>
                    <select name="searchController" id="searchController" style="padding:4px">
                        <option></option>
                        {foreach $managed_controllers as $controller_name => $controller}
                            <option value="{$controller['id']|intval}">{$controller_name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </th>
                <th><input type="text" name="searchObject" id="searchObject" placeholder="{l s='Exact ID' mod='jprestaspeedpack'}" style="text-align: center; padding:4px"></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        <div style="margin-top: 5px">
            <div class="alert alert-info">
                <strong>{l s='Reset cache (with stats)' mod='jprestaspeedpack'}:</strong> {l s='This will delete all files of the cache and clear all database tables' mod='jprestaspeedpack'}
                <br/>
                <strong>{l s='Clear cache (only files)' mod='jprestaspeedpack'}:</strong> {l s='This will delete all files of the cache (not stats). If you filtered the table above then it will only delete files listed in the table within the limit of 1000 files.' mod='jprestaspeedpack'}
                <br/>
                <strong>{l s='Purge cache (with stats)' mod='jprestaspeedpack'}:</strong> {l s='This will remove from stats deleted cache older than 24H (and so probably not used anymore)' mod='jprestaspeedpack'}
            </div>
            <form id="pagecache_form_datas" action="{$request_uri|escape:'html':'UTF-8'}#tabdatas" method="post">
                <input type="hidden" name="submitModule" value="true"/>
                <button type="submit" value="1" id="submitModuleResetDatas" name="submitModuleResetDatas"
                        class="btn btn-danger pull-right">
                    <i class="process-icon-delete"></i> {l s='Reset cache (with stats)' mod='jprestaspeedpack'}
                </button>
                <button type="button" id="clearCacheDatas" class="btn btn-warning pull-right">
                    <i class="process-icon-delete"></i> {l s='Clear cache (only files)' mod='jprestaspeedpack'}
                </button>
                <button type="submit" value="1" id="submitModulePurgeDatas" name="submitModulePurgeDatas"
                        class="btn btn-warning pull-right">
                    <i class="process-icon-delete"></i> {l s='Purge cache (with stats)' mod='jprestaspeedpack'}
                </button>
                <button type="button" id="refreshDatas" class="btn btn-default pull-right">
                    <i class="process-icon-refresh"></i> {l s='Refresh' mod='jprestaspeedpack'}
                </button>
            </form>
        </div>
    </fieldset>
</div>
<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-database"></i>{else}<img width="16" height="16" src="../img/admin/AdminStats.gif" alt=""/>{/if}&nbsp;{l s='Contexts' mod='jprestaspeedpack'}</h3>
    <fieldset class="cachemanagement">
        <div class="alert alert-info">{l s='Here you can see which contexts are used on your shop, this is useful to know which ones you should warmup with the cache-warmer.' mod='jprestaspeedpack'}</div>
        <table id="datasContextsTable" class="display cell-border compact stripe" style="width:100%">
            <thead>
            <tr>
                <th>{l s='UUID' mod='jprestaspeedpack'}</th>
                <th>{l s='Context' mod='jprestaspeedpack'}</th>
                <th>{l s='Number of visit' mod='jprestaspeedpack'}</th>
                <th>{l s='Hit rate' mod='jprestaspeedpack'}</th>
                <th>{l s='Visit of bots/crawlers' mod='jprestaspeedpack'}</th>
                <th>{l s='Cleared' mod='jprestaspeedpack'}</th>
            </tr>
            </thead>
            <tbody>
            <tr><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="6">
                    {l s='Filters' mod='jprestaspeedpack'}&nbsp;:
                    <select name="searchContextDevice" id="searchContextDevice" style="width:auto; display: inline-block">
                        <option value="0">{l s='All devices' mod='jprestaspeedpack'}</option>
                        <option value="3">{l s='Mobile' mod='jprestaspeedpack'}</option>
                        <option value="1">{l s='Desktop' mod='jprestaspeedpack'}</option>
                    </select>
                    <select name="searchContextLang" id="searchContextLang" style="width:auto; display: inline-block">
                        <option value="0">{l s='All languages' mod='jprestaspeedpack'}</option>
                        {foreach $pagecache_contexts_languages as $row}
                            <option value="{$row@key|intval}">#{$row@key|intval} - {$row|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <select name="searchContextCur" id="searchContextCur" style="width:auto; display: inline-block">
                        <option value="0">{l s='All currencies' mod='jprestaspeedpack'}</option>
                        {foreach $pagecache_contexts_currencies as $row}
                            <option value="{$row@key|intval}">#{$row@key|intval} - {$row|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <select name="searchContextCountry" id="searchContextCountry" style="width:auto; display: inline-block">
                        <option value="0">{l s='All countries' mod='jprestaspeedpack'}</option>
                        {foreach $pagecache_contexts_countries as $row}
                            <option value="{$row@key|intval}">#{$row@key|intval} - {$row|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <select name="searchContextGroups" id="searchContextGroups" style="width:auto; display: inline-block">
                        <option value="-1">{l s='All user groups' mod='jprestaspeedpack'}</option>
                        {foreach $pagecache_contexts_groups as $row}
                            <option value="{$row@key|intval}">#{$row@key|intval} - {$row|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                    <select name="searchContextSpecs" id="searchContextSpecs" style="width:auto; display: inline-block">
                        <option value="0">{l s='All specific contexts' mod='jprestaspeedpack'}</option>
                        <option value="null">{l s='By default' mod='jprestaspeedpack'}</option>
                        {foreach $pagecache_contexts_specs as $row}
                            <option value="{$row@key|intval}">#{$row@key|intval}</option>
                        {/foreach}
                    </select>
                </th>
            </tr>
            </tfoot>
        </table>
    </fieldset>
</div>
<div class="panel">
    <h3>{if $avec_bootstrap}<i class="icon-database"></i>{else}<img width="16" height="16" src="../img/admin/AdminStats.gif" alt=""/>{/if}&nbsp;{l s='Database' mod='jprestaspeedpack'}</h3>
    <fieldset class="cachemanagement">
        <div class="alert alert-info">{l s='Tables can consumme a lot of space but they are all optimized and stores only necessary informations. This is mainly used by the automatic refresment of the cache.' mod='jprestaspeedpack'}</div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{l s='Table' mod='jprestaspeedpack'}</th>
                    <th>{l s='Row count' mod='jprestaspeedpack'}</th>
                    <th>{l s='Size in MB' mod='jprestaspeedpack'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $pagecache_datas_dbinfos as $row}
                    <tr>
                        {foreach $row as $col}
                            <td>{$col|escape:'html':'UTF-8'}</td>
                        {/foreach}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </fieldset>
</div>
