{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<script type="text/javascript">
    $(document).ready(function () {
        $('#btn-sqlprofiler-faq-li').prependTo('.btn-toolbar ul.nav');
    });
</script>
<ul style="display:none">
    <li id="btn-sqlprofiler-faq-li">
        <a id="sqlprofiler-faq" class="toolbar_btn" href="{$faq_url|escape:'html':'UTF-8'}" target="_blank" style="color:white; background-color: #33bd25">
            <i class="process-icon-help"></i>
            <div>{l s='FAQ SQL Profiler' mod=$module_name}</div>
        </a>
    </li>
</ul>
{if $sql_profiler_enabled}
<style>
    #datasRunsTable, #datasRunsTable_wrapper, #datasRunsTable_wrapper {
        margin-top: 2rem;
    }
    tr.shown + tr > td {
        padding: 1rem !important;
        background-color: #eff1f2;
    }
    #datasQueriesTable tr.shown + tr > td {
        padding: 0 1rem 1rem 1rem !important;
        background-color: #d4e1e7;
    }
    #datasRunsTable th {
        font-weight: bold !important;
        font-size: 0.8rem;
        border-bottom: 2px solid #a0d0eb !important;
        vertical-align: middle;
        padding-right: 20px;
    }
    #datasRunsTable > tbody > tr.even > td, #datasRunsTable > tbody > tr.odd > td {
        cursor: pointer;
    }
    #datasQueriesTable > tbody > tr.even > td, #datasQueriesTable > tbody > tr.odd > td {
        cursor: pointer;
    }
    .dataTables_processing {
        border: 2px solid orange;
        border-radius: 5px;
        padding: 0 !important;
        line-height: 3rem;
        height: auto !important;
        z-index: 99;
        font-weight: bold;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        border: 2px solid #25b9d7;
    }
    .dataTables_length {
        margin-bottom: 0.5rem;
    }
    .dataTables_length label {
        display: inline !important;
    }
    .dataTables_length select {
        display: inline-block !important;
        width: initial !important;
        height: 1.8rem;
        padding: 0 5px;
    }
    th.sorting_desc, th.sorting_asc {
        background-color: #a0d0eb;
        color: white;
        border: 1px solid #a0d0eb;
    }
    td.sorting_1 {
        border-right: 1px solid #a0d0eb !important;
        border-left: 1px solid #a0d0eb !important;
    }
    .percent-bg-10 td { background-color: #E1F5FE !important }
    .percent-bg-20 td { background-color: #B3E0F2 !important }
    .percent-bg-30 td { background-color: #81D4FA !important }
    .percent-bg-40 td { background-color: #4FC3F7 !important }
    .percent-bg-50 td { background-color: #29B6F6 !important }
    .percent-bg-60 td { background-color: #03A9F4 !important }
    .percent-bg-70 td { background-color: #039BE5 !important; color: white; }
    .percent-bg-80 td { background-color: #0288D1 !important; color: white; }
    .percent-bg-90 td { background-color: #0277BD !important; color: white; }
    .percent-bg-100 td { background-color: #01579B !important; color: white; }
    .form-check {
        margin: 0.5rem 2rem;
    }
    .mypanel-footer {
        padding: 10px 15px;
        background-color: #fcfdfe;
        border-top: 1px solid #eee;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        margin: 15px -20px -20px;
        text-align: right;
    }
    .truncateIfLong {
        max-width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tdid{
        max-width: 50px !important;
    }
    .tddate {
        white-space: nowrap !important;
        text-align: center !important;
        max-width: 140px !important;
    }
    .tdright {
        text-align: right !important;
    }
    .sqlprofsubtitle {
        border-bottom: 1px solid #f1f1f1;
        font-size: 1rem;
        margin: 1rem 0 0.5rem 0;
    }
    .inprogress .jsDisplayStopped, .jsDisplayInProgress {
        display : none !important;
    }
    .jsDisplayStopped, .inprogress .jsDisplayInProgress {
        display : initial !important;
    }
    .recording {
        font-size: 0.85rem;
    }
    .suggestions {
        font-size: 0.85rem;
        border: 1px solid #555555;
        background-color: #ccc;
        padding: 0.5rem;
        border-radius: 5px;
        margin-top: 3px;
    }
    .suggestions.suggestions-suggest {
        border: 1px solid orange;
        background-color: #ffecc8;
    }
    .suggestions.suggestions-success {
        border: 1px solid green;
        background-color: #ddfddd;
    }
    .suggestions.suggestions-error {
        border: 1px solid #d20000;
        background-color: #ffd0d0;
    }
    .suggestions.suggestions-tip {
        border: 1px solid #3ed2f0;
        background-color: #c7f6ff;
    }
    .callstack {
        font-family: monospace, monospace;
    }
    .callstack .cs-function {
        font-weight: bold;
    }
    .queryLoading {
        margin: 1rem;
    }
    .help {
        border: 1px solid #72c279;
        padding: 0 0.5rem;
        border-radius: 5px;
    }
    .help h4 {
        color: #72c279;
    }
    .sql pre {
        white-space: break-spaces;
    }
</style>
<script type="text/javascript">
    function initializeRunsTable() {
        return $('#datasRunsTable').on('error.dt', handleDataTableError).DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: '{$datas_run_url|escape:'javascript':'UTF-8'}',
            columns: [
                { width: '2rem', className: 'tdid' },
                { width: '7rem', className: 'tddate' },
                {
                    className: 'truncateIfLong',
                    orderable : false,
                    render: function (data, type, row) {
                        let html = '<span class="badge badge';
                        switch (parseInt(row[5])) {
                            case 1:
                                html += '-success">GET';
                                break;
                            case 2:
                                html += '-warning">POST';
                                break;
                            case 3:
                                html += '-warning">PUT';
                                break;
                            case 4:
                                html += '-danger">DELETE';
                                break;
                            default:
                                html += '">?';
                                break;
                        }
                        html += '</span> ';
                        if (parseInt(row[6])) {
                            // Ajax
                            html += '<span class="badge">ajax<\/span> ';
                        }
                        return html + data;
                    }
                },
                { width: '7rem', className: 'tdright' },
                { width: '7rem', className: 'tdright',
                    render: function (data, type, row) {
                        let html = '';
                        if (parseInt(row[7])) {
                            html += '<i class="material-icons" title="'+parseInt(row[7])+' {l s='suspicious queries' mod=$module_name}" style="vertical-align: bottom;font-size: 1.1rem;color: #f1b746;">warning</i> ';
                        }
                        return html + data;
                    }
                }
            ],
            order: [[1, 'desc']],
            language: getLocalizationSettings(),
            dom: 'Blfrtip',
            lengthMenu: getLengthMenuOptions(),
            buttons: [],
        });
    }

    function initializeQueriesTable(id_run) {
        return $('#datasQueriesTable').on('error.dt', handleDataTableError).DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: '{$datas_run_url|escape:'javascript':'UTF-8'}&id_run=' + id_run,
            columns: [
                { width: '2rem', className: 'tdid'},
                {
                    className: 'truncateIfLong',
                    orderable : false,
                    render: function (data, type, row) {
                        let html = '';
                        if (parseInt(row[8])) {
                            html += '<i class="material-icons" title="{l s='This query should probably be optimized' mod=$module_name}" style="vertical-align: bottom;font-size: 1.1rem;color: #f1b746;">warning</i> ';
                        }
                        return html + data;
                    }
                },
                { width: '7rem', className: 'tdright'},
                { width: '7rem', className: 'tdright'},
                { width: '7rem', className: 'tdright'},
                { width: '7rem', className: 'tdright'},
                {
                    width: '7rem',
                    className: 'tdright',
                    render: function (data, type, row) {
                        return data + ' (' + row[7] + '%)';
                    }
                },
            ],
            order: [[6, 'desc']],
            language: getLocalizationSettings(),
            dom: 'Blfrtip',
            lengthMenu: getLengthMenuOptions(),
            buttons: [],
            createdRow: (row, data, index) => {
                if (data[7] > 1) {
                    let cssClass = getCssClassByPercentage(parseFloat(data[7]));
                    if (cssClass) {
                        row.classList.add(cssClass);
                    }
                }
            }
        });
    }

    function handleDataTableError(e, settings, techNote, message) {
        console.error('SQL Profiler - Cannot display profiling datas: ', message);
    }

    function getLocalizationSettings() {
        return {
            processing: "<i class=\"icon-refresh icon-spin icon-fw\"></i> {l s='Loading datas...' mod=$module_name}",
            search: "{l s='Search' mod=$module_name}:",
            lengthMenu: "{l s='Showing _MENU_ rows' mod=$module_name}",
            info: "{l s='Showing _START_ to _END_ of _TOTAL_ rows' mod=$module_name}",
            infoEmpty: "{l s='Showing 0 to 0 of 0 row' mod=$module_name}",
            infoFiltered: "{l s='Filtered of _MAX_ rows' mod=$module_name}",
            infoPostFix: "",
            loadingRecords: "{l s='Loading datas...' mod=$module_name}",
            zeroRecords: "{l s='No data to display' mod=$module_name}",
            emptyTable: "{l s='No data to display' mod=$module_name}",
            paginate: {
                first: "{l s='First' mod=$module_name}",
                previous: "{l s='Previous' mod=$module_name}",
                next: "{l s='Next' mod=$module_name}",
                last: "{l s='Last' mod=$module_name}"
            }
        }
    }

    function getLengthMenuOptions() {
        return [
            [10, 25, 50, 100],
            ['10', '25', '50', '100'],
        ];
    }

    function deleteAllData(datasTable, keepRelevant = 0) {
        if (keepRelevant || confirm("{l s='Really delete all profiling datas?' mod=$module_name}")) {
            let parameters = datasTable.ajax.params();
            parameters.deleteAll = true;
            parameters.keepRelevant = keepRelevant;
            $.ajax({
                url: datasTable.ajax.url(),
                method: 'post',
                data: parameters,
                success: function (response) {
                    datasTable.ajax.reload();
                },
                error: function (result, status, error) {
                    console.log(result + ' - ' + status + ' - ' + error);
                },
            });
        }
    }

    function jprestaPcSetCookie(cname, cvalue, ttl_minutes, path) {
        let d = new Date();
        d.setTime(d.getTime() + (ttl_minutes*60*1000));
        let expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=" + path;
    }

    function jprestaPcGetCookie(cname, defaultValue) {
        if (defaultValue === undefined) {
            defaultValue = null;
        }
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return defaultValue;
    }

    let profilingCookieName = 'jpresta_profiler_run';
    let datasRunsTable = null;
    let datasQueriesTable = null;

    function startProfiling() {
        jprestaPcSetCookie(profilingCookieName, $('input[name=profilingType]:checked').val(), 60, '/');
        jprestaRefreshProfilingStatus();
    }

    function stopProfiling() {
        document.cookie = profilingCookieName + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        reloadDatas();
        jprestaRefreshProfilingStatus();
    }

    function reloadDatas() {
        if (datasRunsTable) {
            datasRunsTable.ajax.reload();
        }
    }

    function startLoading(id_query) {
        $('#queryAnalysis .btn').attr('disabled', true);
        document.body.style.cursor='wait';
    }

    function stopLoading(id_query) {
        document.body.style.cursor='default';
        $('#queryAnalysis .btn').removeAttr('disabled');
    }

    function displayQueryAnalysis(id_query, run_it = 0) {
        try {
            startLoading(id_query);
            $.get('{$url_query_ctrl|escape:'javascript':'UTF-8'}', { id_query: id_query, run_it: run_it }, function (data) {
                if (data.includes('queryAnalysis' + id_query)) {
                    $('#queryAnalysis').html(data);
                } else {
                    handleErrorResponse();
                }
            }).fail(function () {
                handleErrorResponse();
            }).always(function() {
                stopLoading(id_query);
            });
        } catch (error) {
            handleErrorResponse();
        }

        function handleErrorResponse() {
            $('#queryAnalysis').html("<div class=\"alert alert-danger\">{l s='Sorry, looks like an error occurred during the analysis of this query' mod=$module_name}</div>");
        }
    }

    function createIndex(id_query, tableName, cols) {
        try {
            startLoading(id_query);
            $.get('{$url_query_ctrl|escape:'javascript':'UTF-8'}', { id_query: id_query, add_idx_tbl: tableName, add_idx_cols: cols }, function (data) {
                if (data.includes('queryAnalysis' + id_query)) {
                    $('#queryAnalysis').html(data);
                } else {
                    handleErrorResponse();
                }
            }).fail(function () {
                handleErrorResponse();
            }).always(function() {
                stopLoading(id_query);
            });
        } catch (error) {
            handleErrorResponse();
        }

        function handleErrorResponse() {
            $('#queryAnalysis').html("<div class=\"alert alert-danger\">{l s='Sorry, looks like an error occurred during the analysis of this query' mod=$module_name}</div>");
        }
    }

    function deleteIndex(id_query, tableName, indexName) {
        try {
            startLoading(id_query);
            $.get('{$url_query_ctrl|escape:'javascript':'UTF-8'}', { id_query: id_query, del_idx_tbl: tableName, del_idx_name: indexName }, function (data) {
                if (data.includes('queryAnalysis' + id_query)) {
                    $('#queryAnalysis').html(data);
                } else {
                    handleErrorResponse();
                }
            }).fail(function () {
                handleErrorResponse();
            }).always(function() {
                stopLoading(id_query);
            });
        } catch (error) {
            handleErrorResponse();
        }

        function handleErrorResponse() {
            $('#queryAnalysis').html("<div class=\"alert alert-danger\">{l s='Sorry, looks like an error occurred during the analysis of this query' mod=$module_name}</div>");
        }
    }

    function jprestaRefreshProfilingStatus() {
        let profilingCookieValue = jprestaPcGetCookie(profilingCookieName, 0);
        if (profilingCookieValue !== 0) {
            $('#sqlprofiler').addClass('inprogress');
            if (profilingCookieValue === 'admin') {
                $('#profilingTypeAdmin').prop('checked', true);
            }
            else {
                $('#profilingTypeFront').prop('checked', true);
            }
            $('#sqlprofiler .jsDisabledInProgress input').prop('readonly', true).prop('disabled', true);
        }
        else {
            $('#sqlprofiler').removeClass('inprogress');
            $('#sqlprofiler .jsDisabledInProgress input').prop('readonly', false).prop('disabled', false);
        }
    }

    function getTemplate(tplId) {
        const template = document.querySelector('#' + tplId);
        return template.content.cloneNode(true);
    }

    function getCssClassByPercentage(percentage) {
        if (percentage < 5) {
            return null;
        }
        percentage = Math.min(100, Math.max(0, percentage));
        const index = Math.floor(percentage / 10);
        return `percent-bg-${ (index + 1) * 10 }`;
    }

    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';

        datasRunsTable = initializeRunsTable();

        jprestaRefreshProfilingStatus();

        $('#startProfiling').on('click', function () {
            startProfiling();
        });
        $('#stopProfiling').on('click', function () {
            stopProfiling();
        });
        $('#deleteAll').on('click', function () {
            deleteAllData(datasRunsTable);
        });
        $('#deleteIrrelevant').on('click', function () {
            deleteAllData(datasRunsTable, 1);
        });
        $('#reloadAll').on('click', function(event) {
            reloadDatas();
        });

        // Add event listener for opening and closing run details
        $('#datasRunsTable > tbody').on('click', '> tr.even > td, > tr.odd > td', function () {
            let tr = $(this).closest('tr');
            let row = datasRunsTable.row( tr );
            let open = row.child.isShown();

            datasRunsTable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                if (this.child.isShown()) {
                    this.child.hide();
                    if (datasQueriesTable) {
                        datasQueriesTable.destroy(true);
                    }
                    $(this.node()).removeClass('shown');
                }
            });

            // Now open this row
            if (!open) {
                let datas = row.data();
                if (typeof datas !== 'undefined') {
                    row.child(getTemplate('datasQueriesTableTpl')).show();
                    datasQueriesTable = initializeQueriesTable(datas[0]);

                    // Add event listener for opening and closing query details
                    $('#datasQueriesTable > tbody').on('click', '> tr.even > td, > tr.odd > td', function () {
                        let tr = $(this).closest('tr');
                        let row = datasQueriesTable.row( tr );
                        let open = row.child.isShown();

                        datasQueriesTable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                            if (this.child.isShown()) {
                                this.child.hide();
                                $(this.node()).removeClass('shown');
                            }
                        });

                        // Now open this row
                        if (!open) {
                            let datas = row.data();
                            if (typeof datas !== 'undefined') {
                                row.child(getTemplate('detailsQueryTpl')).show();
                                displayQueryAnalysis(datas[0]);
                                tr.addClass('shown');
                            }
                        }
                    });

                    tr.addClass('shown');
                }
            }
        });
    });

</script>

<div id="sqlprofiler" class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-heading">{l s="SQL Profiler" mod=$module_name}</div>

            <div class="help">
                <h4>{l s="How does it work?" mod=$module_name}</h4>
                <p>{l s="This SQL profiler is a tool that helps you identify slow SQL queries, which are often the cause of your shop's sluggish performance on certain pages or actions." mod=$module_name}</p>
                <p>{l s="It can analyze all SQL queries in the back office (admin) and in the front office (the shop)." mod=$module_name}</p>
                <p>{l s="You can use it on a production server because it has a very low impact on real visitors; it is only enabled in the user's browser (yours) through a cookie." mod=$module_name}</p>
                <p>{l s="To use it, simply start the profiling, then navigate to the pages and/or perform the actions you want to analyze. Return here, stop the profiling, and click on queries to understand the slowness." mod=$module_name}</p>
            </div>

            <div style="margin: 1rem 0;">
                <table>
                    <tr>
                        <td class="jsDisabledInProgress" style="vertical-align: middle">
                            {l s='What do you want to analyze?' mod=$module_name}
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="profilingType"
                                       id="profilingTypeFront" value="front" checked>
                                <label class="form-check-label" for="profilingTypeFront">
                                    {l s='Front office (the shop)' mod=$module_name}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="profilingType"
                                       id="profilingTypeAdmin" value="admin">
                                <label class="form-check-label" for="profilingTypeAdmin">
                                    {l s='Back office (the admin)' mod=$module_name}
                                </label>
                            </div>
                        </td>
                        <td style="vertical-align: middle">
                            <button id="startProfiling" class="btn btn-success jsDisplayStopped"><i
                                    class="material-icons" style="vertical-align: middle">play_arrow</i> {l s='Start profiling' mod=$module_name}
                            </button>
                            <button id="stopProfiling" class="btn btn-danger jsDisplayInProgress"><i
                                    class="material-icons stop" style="vertical-align: middle">stop</i> {l s='Stop profiling' mod=$module_name}
                            </button>
                        </td>
                        <td style="vertical-align: middle">
                            <div class="recording jsDisplayInProgress">
                                <img src="../modules/{$module_name|escape:'html':'UTF-8'}/views/img/eating.gif" style="float:left;vertical-align: ;margin: 0 0 0 1rem;" width="104" height="68">
                                <b>{l s='Profiling is in progress for YOU only!' mod=$module_name}</b>
                                <br/>{l s="Now you should display pages and/or perform actions that you want to analyse" mod=$module_name}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <table id="datasRunsTable" class="display cell-border compact stripe" style="width:100%">
                <thead>
                <tr class="column-headers">
                    <th class="tdid">{l s='ID Run' mod=$module_name}</th>
                    <th class="tddate">{l s='Date' mod=$module_name}</th>
                    <th>{l s='URL' mod=$module_name}</th>
                    <th class="tdright">{l s='Query count' mod=$module_name}</th>
                    <th class="tdright">{l s='Query duration (ms)' mod=$module_name}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="mypanel-footer">
                <button type="button" id="reloadAll" class="btn btn-default">
                    <i class="process-icon-refresh"></i> {l s='Refresh' mod=$module_name}
                </button>
                <button type="button" id="deleteIrrelevant" class="btn btn-warning">
                    <i class="process-icon-delete"></i> {l s='Delete irrelevant data' mod=$module_name}
                </button>
                <button type="button" id="deleteAll" class="btn btn-danger">
                    <i class="process-icon-delete"></i> {l s='Delete all data' mod=$module_name}
                </button>
            </div>
        </div>
    </div>
</div>

<template id="datasQueriesTableTpl">
    <div style="margin-bottom: 0.5rem;">
        <span style="padding: 1px 8px; background-color: #00ABEC; border: 1px solid #ccc; margin-right: 5px;"></span>
        {l s='The blue lines mean that they represent a significant percentage of the time spent querying the database for this request.' mod=$module_name}
    </div>
    <table id="datasQueriesTable" class="display cell-border compact stripe" style="width:100%;">
        <thead>
        <tr class="column-headers">
            <th class="tdid">{l s='ID Query' mod=$module_name}</th>
            <th class="tdcenter">{l s='SQL' mod=$module_name}</th>
            <th class="tdright">{l s='Count' mod=$module_name}</th>
            <th class="tdright">{l s='Max (ms)' mod=$module_name}</th>
            <th class="tdright">{l s='Median (ms)' mod=$module_name}</th>
            <th class="tdright">{l s='Average (ms)' mod=$module_name}</th>
            <th class="tdright">{l s='Total (ms)' mod=$module_name}</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</template>

<template id="detailsQueryTpl">
    <div id="queryAnalysis">
        <div class="queryLoading"><i class="icon-refresh icon-spin icon-fw"></i> {l s='Query analysis in progress...' mod=$module_name}</div>
    </div>
</template>
{/if}
