{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}

<style>
    #pagecachecfg .dynhooks label{ line-height:18px;}
    #pagecachecfg .tag{ background-color:#eee;border:1px solid #CCCED7;border-radius:4px;display:inline-block;margin:2px;padding:3px;}
    #linkadvanced{ font-weight:700;display:block;margin:15px 5px;}
    #pagecachecfg input[disabled]{ opacity:0.5;filter:alpha(opacity=50);}
    #pagecachecfg .bootstrap .nav-tabs{ margin-left:0;}
    #pagecachecfg .bootstrap .label{ color:black;}
    #pagecachecfg .bootstrap .nav-tabs li a{ font-size:1.2em;white-space: nowrap;}
    #pagecachecfg .bootstrap .nav-tabs li.active a, #pagecachecfg .bootstrap .nav-tabs li.active a:visited,.bootstrap .nav-tabs li.active a:hover, #pagecachecfg .bootstrap .nav-tabs li.active a:focus{ background-color:#ebedf4;}
    #pagecachecfg .nobootstrap fieldset{ border:1px solid #ddd;margin:0;}
    #pagecachecfg .installstep{ font-size:0.9rem;margin:5px 0 20px;}
    #pagecachecfg a.browsebtn{ display:inline-block;color:#FFF;background-color:#F0AD4E;border:1px solid #EEA236;border-radius:3px;text-decoration:none;padding:2px;}
    #pagecachecfg a.browsebtn:hover{ background-color:#F5C177}
    #pagecachecfg .okbtn{ display:inline-block;color:#FFF;background-color:#59C763;border:1px solid #4EA948;border-radius:3px;text-decoration:none;margin:3px;padding:2px;}
    #pagecachecfg .okbtn:hover{ background-color:#7DD385}
    #pagecachecfg a.kobtn{ display:inline-block;color:#DA0000;border-radius:3px;margin:3px;padding:2px;}
    #pagecachecfg a.kobtn:hover{ color:#ED8080}
    #pagecachecfg div.step{ margin:5px 0 5px 20px;}
    #pagecachecfg .step span{ border-radius:.8em;color:#FFF;display:inline-block;font-weight:700;line-height:1.6em;margin-right:15px;text-align:center;width:1.6em;}
    #pagecachecfg .step img{ margin-right:15px;}
    #pagecachecfg .steptodo span{ background:#CCC;}
    #pagecachecfg .stepok span{ background:#5EA226;color:#FFF;}
    #pagecachecfg .stepok{ color:#5EA226;}
    #pagecachecfg .stepdesc{ border-left:2px solid #CCCED7;margin-left:44px;padding:10px 0 10px 24px;}
    #pagecachecfg .stepdesc img{ margin:2px;}
    #pagecachecfg .stepdesc ol,.stephelp ol{ margin:0;padding:0 0 0 24px;}
    #pagecachecfg .stephelp { display:none;border: 1px solid rgb(229, 229, 29);background-color: lightyellow;border-radius: 8px;padding: 10px;margin: 10px 0;}
    #pagecachecfg .morehook { display: none}
    #pagecachecfg .actions { margin: 15px 0 0 15px;}
    #pagecachecfg .btn { margin-right: 5px}
    #pagecachecfg.ps15 .row { background: initial;}
    #pagecachecfg.ps15 ul.nav-tabs li{ display: inline-block; padding: 5px; margin: 0 5px 0 0; border-radius: 5px 5px 0 0; background-color: #EBEDF4; border: 1px solid #CCCED7; border-bottom: none;}
    #pagecachecfg.ps15 ul.nav-tabs li.active{ background-color: #49B2FF; color:white}
    #pagecachecfg.ps15 ul.nav-tabs li a, #pagecachecfg.ps15 a.okbtn, #pagecachecfg.ps15 a.browsebtn { text-decoration: none;}
    #pagecachecfg.ps15 .bootstrap .nav-tabs li.active a { background-color: #49B2FF; color:white;text-decoration: none;}
    #pagecachecfg.ps15 a { text-decoration: underline;}
    #pagecachecfg.ps15 ol { list-style-type: decimal;}
    #pagecachecfg.ps15 .col-sm-2 { width: 15%; float: left;}
    #pagecachecfg.ps15 .col-sm-10 { width: 85%; float: right;}
    #pagecachecfg.ps15 li { margin: 10px;}
    #pagecachecfg.ps15 .hint { display: block; margin-bottom: 5px;}
    #pagecachecfg.ps15 .jprestamenu { display: inline-block; vertical-align: top; width: 20%; padding: 0 16px 5px 5px; margin-right: 10px; border: 1px solid #ccc;}
    #pagecachecfg.ps15 .jprestacontent { display: inline-block; vertical-align: top; width: 70%;}
    #pagecachecfg.ps15 .panel {
        border: 1px solid lightgrey;
        border-radius: 3px;
        padding: 3px;
        margin: 0 0 12px 0;
    }
    #pagecachecfg.ps15 .jprestamenu .panel { border: none;}
    #pagecachecfg.ps15 .panel h3 {
        border-bottom: 1px solid lightgrey;
        margin-top: 1px;
    }
    #pagecachecfg.ps15 fieldset {
        background-color: transparent;
        border: none;
    }
    #pagecachecfg #timeouts .slider-horizontal { margin: 5px 10px;}
    #pagecachecfg #timeouts table td { padding: 3px;text-align:right}
    #pagecachecfg #timeouts table td.slider { text-align:left}
    #pagecachecfg #timeouts table td.label { padding-right: 5px; font-weight: bold;}
    #pagecachecfg #timeouts table .first td { padding-top: 20px;}
    #profilingTable td:nth-child(4) { text-align: right}
    #profilingTable td:nth-child(3) { text-align: center}
    #pagecachecfg .dataTables_length { display:none}
    #pagecachecfg.ps15 ul { display: block;list-style-type: disc;padding-left: 2rem;}
    #pagecachecfg.ps15 pre {
        background-color: #f5f5f5;
        border: 1px solid #ccc;
        border-radius: 3px;
        color: #333;
        display: block;
        font-size: 11px;
        line-height: 1.42857;
        padding: 8px;
        word-break: break-all;
        word-wrap: break-word;
    }
    #toolbar-nav i.process-icon-delete:before {
        color: white;
    }
    .bootstrap .nav-pills>li.error>a, .bootstrap .nav-pills>li.error>a:hover, .bootstrap .nav-pills>li.error>a:focus {
        color: #fff;
        background-color: #f44336;
    }
    #pagecachecfg ul.nav ul.nav {
        margin-left: 30px;
        padding-left: 5px;
        border-left: 1px solid #25b9d7;
    }
    #pagecachecfg .option-off {
        font-style: italic;
    }
    #pagecachecfg .pc_specifics {
        position: absolute;
        border: 1px solid gray;
        background-color: white;
        padding: 3px;
        z-index: 99;
        text-align: left;
        display: none;
        unicode-bidi: embed;
        font-family: monospace;
        white-space: pre;
        border-radius: 3px;
        margin-left: 100px;
    }
    #pagecachecfg .specifics:hover .pc_specifics {
        display: block;
    }
    #lioptions a, #licachekey a {
        color: orange
    }
    #pagecachecfg .bootstrap h4 {
        font-weight: bold;
    }
    #pagecachecfg label.form-check-label {
        margin-right: 1rem;
    }
    #pagecachecfg .table th, #pagecachecfg #datasTable th {
        font-weight: bold !important;
        font-size: 0.8rem;
        border-bottom: 2px solid #a0d0eb !important;
        vertical-align: baseline;
    }
    #pagecachecfg #datasTable th {
        vertical-align: middle;
    }
    #pagecachecfg .btn-xs i.material-icons {
        font-size: 1.3rem;
    }
    #pagecachecfg .btn.btn-xs {
        line-height: 0;
    }
    #pagecachecfg .cachewarmer_count {
        font-size: 1rem;
        font-weight: bold;
        text-align: right;
    }
    #pagecachecfg #total_pages_count.cachewarmer_count {
        color: green;
    }
    #pagecachecfg #total_pages_count.cachewarmer_count_warn {
        color: orange;
    }
    #pagecachecfg #total_pages_count.cachewarmer_count_danger {
        color: red;
    }
    .help a {
        color: #59c763;
        font-weight: bold;
    }
    #pagecachecfg .bootstrap input[type=number] {
        background: #f5f8f9 none;
        border: 1px solid #c7d6db;
        line-height: 27px;
        text-align: right;
        border-radius: 3px;
    }
    #pagecachecfg .bootstrap input:focus[type=number] {
        background-color: #fefbe2;
        border: 1px solid #66afe9 !important;
        box-shadow: none;
        outline: 0;
    }
    #pagecachecfg .chart {
        border: 1px solid #bbcdd2;
        margin: 0.5rem 0;
        border-radius: 4px;
    }
</style>
<script type="text/javascript">
    let currentTab = null;
    let is_cachewarmer_valid = false;
    $( document ).ready(function() {
        switch (window.location.hash) {
            case "#tabinstall":    displayTab("install"); break;
            case "#tablicense":    displayTab("license"); break;
            case "#tabdynhooks":   displayTab("dynhooks"); break;
            case "#tabdynhooksjs": displayTab("dynhooks"); break;
            case "#tabotheroptions":          displayTab("otheroptions"); break;
            case "#taboptions":    displayTab("options"); break;
            case "#tabcachekey":   displayTab("cachekey"); break;
            case "#tabdatas":      displayTab("datas"); break;
            case "#tabcachewarmer":           displayTab("cachewarmer"); break;
            case "#tabcachewarmer-settings":  displayTab("cachewarmer-settings"); break;
            case "#tabcachewarmer-status":    displayTab("cachewarmer-status"); break;
            case "#tabcachewarmer-report":    displayTab("cachewarmer-report"); break;
            case "#tabtypecache":   displayTab("typecache"); break;
            case "#tabdiagnostic":  displayTab("diagnostic"); break;
            case "#tabtimeouts":    displayTab("timeouts"); break;
            case "#tabcron":        displayTab("cron"); break;
            case "#tabshopsinfos":  displayTab("shopsinfos"); break;
            case "#tabcachemanagement":       displayTab("cachemanagement"); break;
        }
        $('#desc-module-clearcache-li').prependTo('.btn-toolbar ul.nav');
        $('#btn-pagecache-faq-li').prependTo('.btn-toolbar ul.nav');

        // Bug in PS1.7.8.5
        $('.btn-toolbar ul.nav #dingedi-mdtr-app').remove();

        // JPresta-Cache-Warmer tabs
        window.addEventListener(
            "message",
            (event) => {
                if ((event.origin === "http://localhost" || event.origin === "https://cachewarmer.jpresta.com")
                    && typeof event.data === 'object'
                    && typeof event.data.name === 'string'
                    && event.data.name === 'jpresta-cache-warmer-subscription'
                ) {
                    console.log('JPresta-Cache-Warmer subscription informations received.');
                    let subscriptionInfos = event.data;
                    if (subscriptionInfos.is_valid) {
                        is_cachewarmer_valid = true;
                        if (subscriptionInfos.page_status === 'show') {
                            $('#licachewarmer-status').show().removeClass('option-off');
                        }
                        else if (subscriptionInfos.page_status === 'show_disabled') {
                            $('#licachewarmer-status').show().addClass('option-off');
                        }
                        else {
                            $('#licachewarmer-status').hide();
                        }
                        if (subscriptionInfos.page_report === 'show') {
                            $('#licachewarmer-report').show().removeClass('option-off');
                        }
                        else if (subscriptionInfos.page_report === 'show_disabled') {
                            $('#licachewarmer-report').show().addClass('option-off');
                        }
                        else {
                            $('#licachewarmer-report').hide();
                        }
                        $('#cw-submenu').show();
                        if (currentTab === 'cachewarmer') {
                            // This will update the active menu
                            displayTab('cachewarmer');
                        }
                    }
                }
            },
            false
        );

        // If the cache-warmer frame is ready before us we retreive the information again.
        try {
            document.getElementById("jprestaCacheWamer").contentWindow.postMessage('jpresta-admin-ready', '*');
        }
        catch (e) {
            console.log('Cannot send message "jpresta-admin-ready" to iframe #jprestaCacheWamer', e);
        }
    });
    function displayTab(tab) {
        $(".pctab").hide();
        $("#"+tab).show();
        $(".nav-pills .active").removeClass("active");
        if (!is_cachewarmer_valid || tab !== 'cachewarmer') {
            $("#li" + tab).addClass("active");
        }
        else {
            $("#licachewarmer-dashboard").addClass("active");
        }
        currentTab = tab;
        if (tab === 'install' && typeof nv !== 'undefined' && typeof nv.graphs[0] !== 'undefined') {
            nv.graphs[0].update();
        }
    }
</script>

<div id="pagecachecfg" {if !$avec_bootstrap}class="ps15"{/if}>

    {foreach $msg_success as $msg}
        <div class="bootstrap">
            <div class="module_confirmation conf confirm alert alert-success">{if $avec_bootstrap}<button type="button" class="close" data-dismiss="alert">&times;</button>{/if}{$msg|escape:'html':'UTF-8'}</div>
        </div>
    {/foreach}
    {foreach $msg_infos as $msg}
        <div class="bootstrap">
            <div class="alert alert-info">{if $avec_bootstrap}<button type="button" class="close" data-dismiss="alert">&times;</button>{/if}{$msg|escape:'html':'UTF-8'}</div>
        </div>
    {/foreach}
    {foreach $msg_warnings as $msg}
        <div class="bootstrap">
            <div class="module_warning alert alert-warning">{if $avec_bootstrap}<button type="button" class="close" data-dismiss="alert">&times;</button>{/if}{$msg|escape:'html':'UTF-8'}</div>
        </div>
    {/foreach}
    {foreach $msg_errors as $msg}
        <div class="bootstrap">
            <div class="module_error alert alert-danger">{if $avec_bootstrap}<button type="button" class="close" data-dismiss="alert">&times;</button>{/if}{$msg|escape:'html':'UTF-8'}</div>
        </div>
    {/foreach}
    {if !$module_enabled}
        <div class="alert alert-warning" style="display: block;">
            &nbsp;{l s='The module is currently disabled' mod='jprestaspeedpack'}
        </div>
    {/if}
    <div class="bootstrap">
        <div class="row">
            <div class="col-md-4 col-lg-3 col-xl-2 jprestamenu">
                <div class="panel">
                    <div class="panel-heading" title="Prestashop {$prestashop_version|escape:'html':'UTF-8'}">
                        <img src="../modules/{$module_name|escape:'html':'UTF-8'}/logo.png" width="32" height="32"/> {$module_displayName|escape:'html':'UTF-8'} <small style="font-size: 0.8rem;color: gray;">v{$module_version|escape:'html':'UTF-8'}</small>
                    </div>
                    <ul class="nav nav-pills nav-stacked">
                        <li id="liinstall" role="presentation" {if $pctab eq 'install'}class="active"{/if}><a href="#tabinstall" onclick="displayTab('install');return true;">{if $avec_bootstrap}<i class="icon-dashboard"></i>{else}<img width="16" height="16" src="../img/admin/prefs.gif" alt=""/>{/if}&nbsp;{l s='Dashboard' mod='jprestaspeedpack'}</a></li>
                        <li id="lilicense" role="presentation" class="{if $pctab eq 'license'}active{/if}{if $pagecache_clone_detected} error{/if}"><a href="#tablicense" onclick="displayTab('license');return true;">{if $avec_bootstrap}<i class="icon-key"></i>{else}<img width="16" height="16" src="../img/admin/htaccess.gif" alt=""/>{/if}&nbsp;{l s='License' mod='jprestaspeedpack'}</a></li>

                        <li id="licachewarmer" role="presentation" {if $pctab eq 'cachewarmer'}class="active"{/if}><a href="#tabcachewarmer" onclick="displayTab('cachewarmer');return true;">{if $avec_bootstrap}<i class="icon-fire"></i>{else}<img width="16" height="16" src="../img/admin/quick.gif" alt=""/>{/if}&nbsp;{l s='JPresta Cache Warmer' mod='jprestaspeedpack'}</a>
                            <ul id="cw-submenu" style="display: none" class="nav nav-pills nav-stacked">
                                <li id="licachewarmer-dashboard" {if $pctab eq 'cachewarmer'}class="active"{/if}><a href="#tabcachewarmer" onclick="displayTab('cachewarmer');return true;">{l s='Dashboard and options' mod='jprestaspeedpack'}</a></li>
                                <li id="licachewarmer-settings" {if $pctab eq 'cachewarmer-settings'}class="active"{/if}><a href="#tabcachewarmer-settings" onclick="displayTab('cachewarmer-settings');return true;">{l s='Pages to warmup' mod='jprestaspeedpack'}</a></li>
                                <li id="licachewarmer-status" {if $pctab eq 'cachewarmer-status'}class="active"{/if}><a href="#tabcachewarmer-status" onclick="displayTab('cachewarmer-status');return true;">{l s='Status of your shop' mod='jprestaspeedpack'}</a></li>
                                <li id="licachewarmer-report" {if $pctab eq 'cachewarmer-report'}class="active"{/if}><a href="#tabcachewarmer-report" onclick="displayTab('cachewarmer-report');return true;">{l s='Monthly reports' mod='jprestaspeedpack'}</a></li>
                            </ul>
                        </li>

                        <li id="lidynhooks" role="presentation" {if $pctab eq 'dynhooks'}class="active"{/if}><a href="#tabdynhooks" onclick="displayTab('dynhooks');return true;">{if $avec_bootstrap}<i class="icon-puzzle-piece"></i>{else}<img width="16" height="16" src="../img/admin/tab-plugins.gif" alt=""/>{/if}&nbsp;{l s='Dynamic modules and widgets' mod='jprestaspeedpack'}</a></li>
                        <li id="litypecache" role="presentation" {if $pctab eq 'typecache'}class="active"{/if}><a href="#tabtypecache" onclick="displayTab('typecache');return true;">{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Caching system' mod='jprestaspeedpack'}</a></li>
                        <li id="liotheroptions" role="presentation" {if $pctab eq 'otheroptions'}class="active"{/if}><a href="#tabotheroptions" onclick="displayTab('otheroptions');return true;">{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Minification and other options' mod='jprestaspeedpack'}</a></li>
                        <li id="litimeouts" role="presentation" {if $pctab eq 'timeouts'}class="active"{/if}><a href="#tabtimeouts" onclick="displayTab('timeouts');return true;">{if $avec_bootstrap}<i class="icon-time"></i>{else}<img width="16" height="16" src="../img/admin/time.gif" alt=""/>{/if}&nbsp;{l s='Pages & timeouts' mod='jprestaspeedpack'}</a></li>
                        <li id="lidatas" role="presentation" {if $pctab eq 'datas'}class="active"{/if}><a href="#tabdatas" onclick="displayTab('datas');return true;">{if $avec_bootstrap}<i class="icon-line-chart"></i>{else}<img width="16" height="16" src="../img/admin/AdminStats.gif" alt=""/>{/if}&nbsp;{l s='Statistics' mod='jprestaspeedpack'}</a></li>
                        <li id="lidiagnostic" role="presentation" {if $pctab eq 'diagnostic'}class="active"{/if}><a href="#tabdiagnostic" onclick="displayTab('diagnostic');return true;">{if $avec_bootstrap}<i class="icon-user-md"></i>{else}<img width="16" height="16" src="../img/admin/binoculars.png" alt=""/>{/if}&nbsp;{l s='Diagnostic & performances' mod='jprestaspeedpack'} <span class="badge">{$diagnostic_count|escape:'html':'UTF-8'}</span></a></li>
                        <li id="licron" role="presentation" {if $pctab eq 'cron'}class="active"{/if}><a href="#tabcron" onclick="displayTab('cron');return true;">{if $avec_bootstrap}<i class="icon-link"></i>{else}<img width="16" height="16" src="../img/admin/subdomain.gif" alt=""/>{/if}&nbsp;{l s='API (URLs to clear the cache)' mod='jprestaspeedpack'}</a></li>
                        {if $advanced_mode}
                            <li id="lioptions" role="presentation" {if $pctab eq 'options'}class="active"{/if}><a href="#taboptions" onclick="displayTab('options');return true;">{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Options' mod='jprestaspeedpack'}</a></li>
                            <li id="licachekey" role="presentation" {if $pctab eq 'cachekey'}class="active"{/if}><a href="#tabcachekey" onclick="displayTab('cachekey');return true;">{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}&nbsp;{l s='Cache key' mod='jprestaspeedpack'}</a></li>
                        {/if}
                        {if count($pagecache_shopsinfos) > 1}
                            <li id="lishopsinfos" role="presentation" {if $pctab eq 'shopsinfos'}class="active"{/if}><a href="#tabshopsinfos" onclick="displayTab('shopsinfos');return true;">{if $avec_bootstrap}<i class="icon-sitemap"></i>{else}<img width="16" height="16" src="../img/admin/multishop_config.png" alt=""/>{/if}&nbsp;{l s='Multistore' mod='jprestaspeedpack'}</a></li>
                        {/if}
                        <li id="lifaq" class="help"><a href="https://jpresta.com/{$jpresta_language_isocode|default:'en'|escape:'javascript':'UTF-8'}/faq?from=jprestaspeedpack" target="_blank">{if $avec_bootstrap}<i class="icon-question-sign"></i>{else}<img width="16" height="16" src="../img/admin/help.png" alt=""/>{/if}&nbsp;{l s='FAQ' mod='jprestaspeedpack'}</a></li>
                    </ul>
                    <ul style="display:none">
                        <li id="btn-pagecache-faq-li">
                            <a id="pagecache-faq" class="toolbar_btn" href="https://jpresta.com/{$jpresta_language_isocode|default:'en'|escape:'javascript':'UTF-8'}/faq?from=jprestaspeedpack" target="_blank" style="color:white; background-color: #33bd25">
                                <i class="process-icon-help" style="color:white;"></i>
                                <div>{l s='FAQ' mod='jprestaspeedpack'}</div>
                            </a>
                        </li>
                    </ul>
                </div>
                {if !$advanced_mode}
                    <div style="text-align: center"><a href="{$advanced_mode_url|escape:'html':'UTF-8'}">{l s='Advanced mode' mod='jprestaspeedpack'}</a></div>
                {/if}
            </div>
            <div class="col-md-8 col-lg-9 col-xl-10 jprestacontent">
                <div id="install" class="pctab" {if $pctab neq 'install'}style="display:none"{/if}>
                    {include file='./get-content-tab-install.tpl'}
                </div>
                <div id="dynhooks" class="pctab" {if $pctab neq 'dynhooks'}style="display:none"{/if}>
                    {include file='./get-content-tab-dynhooks.tpl'}
                </div>
                <div id="timeouts" class="pctab" {if $pctab neq 'timeouts'}style="display:none"{/if}>
                    {include file='./get-content-tab-timeouts.tpl'}
                </div>
                <div id="datas" class="pctab" {if $pctab neq 'datas'}style="display:none"{/if}>
                    {include file='./get-content-tab-datas.tpl'}
                </div>
                <div id="cron" class="pctab" {if $pctab neq 'cron'}style="display:none"{/if}>
                    {include file='./get-content-tab-cron.tpl'}
                </div>

                {if $advanced_mode}
                    <div id="options" class="pctab" {if $pctab neq 'options'}style="display:none"{/if}>
                        {include file='./get-content-tab-options.tpl'}
                    </div>
                    <div id="cachekey" class="pctab" {if $pctab neq 'cachekey'}style="display:none"{/if}>
                        {include file='./get-content-tab-cachekey.tpl'}
                    </div>
                {/if}

                <div id="otheroptions" class="pctab" {if $pctab neq 'otheroptions'}style="display:none"{/if}>
                    {include file='./get-content-tab-other-options.tpl'}
                </div>

                <div id="typecache" class="pctab" {if $pctab neq 'typecache'}style="display:none"{/if}>
                    {include file='./get-content-tab-typecache.tpl'}
                </div>
                <div id="diagnostic" class="pctab" {if $pctab neq 'diagnostic'}style="display:none"{/if}>
                    {include file='./get-content-tab-diagnostic.tpl'}
                </div>

                <div id="license" class="pctab" {if $pctab neq 'license'}style="display:none"{/if}>
                    {include file='./get-content-tab-license.tpl'}
                </div>

                <div id="cachewarmer" class="pctab" {if $pctab neq 'cachewarmer'}style="display:none"{/if}>
                    {include file='./get-content-tab-jpresta.tpl'}
                </div>
                <div id="cachewarmer-settings" class="pctab" {if $pctab neq 'cachewarmer-settings'}style="display:none"{/if}>
                    {include file='./get-content-tab-jpresta-settings.tpl'}
                </div>
                <div id="cachewarmer-status" class="pctab" {if $pctab neq 'cachewarmer-status'}style="display:none"{/if}>
                    {include file='./get-content-tab-jpresta-status.tpl'}
                </div>
                <div id="cachewarmer-report" class="pctab" {if $pctab neq 'cachewarmer-report'}style="display:none"{/if}>
                    {include file='./get-content-tab-jpresta-report.tpl'}
                </div>

                {if count($pagecache_shopsinfos) > 1}
                    <div id="shopsinfos" class="pctab" {if $pctab neq 'shopsinfos'}style="display:none"{/if}>
                        {include file='./get-content-tab-shopsinfos.tpl'}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
