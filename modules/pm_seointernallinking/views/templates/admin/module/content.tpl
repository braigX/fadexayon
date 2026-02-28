{$css_js_assets|sil_nofilter}

<div id="pm_backoffice_wrapper" class="pm_bo_ps_{$ps_major_version|escape:'htmlall':'UTF-8'}">
    {module->_displayTitle text="{$module_display_name}"}

    {if !$permissions_errors|sizeof}
        {if $module_is_up_to_date}
            {$rating_invite|sil_nofilter}
            {$parent_content|sil_nofilter}

            <div id="wrapConfigTab">
                <ul id="configTab">
                    <li>
                        <a href="#config-1">
                            <span>{l s='General Options' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config-2">
                            <span>{l s='List of Groups' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config-3">
                            <span>{l s='List of Expressions' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config-4">
                            <span>{l s='Optimization' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config-5">
                            <span>{l s='Delete Internal Linking' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                    <li>
                        <a href="#config-6">
                            <span>{l s='Crontab' mod='pm_seointernallinking'}</span>
                        </a>
                    </li>
                </ul>

                <div id="config-1">
                    {$global_options_tab|sil_nofilter}
                </div>
                <div id="config-2">
                    {$list_groups_tab|sil_nofilter}
                </div>
                <div id="config-3">
                    {$list_expressions_tab|sil_nofilter}
                </div>
                <div id="config-4">
                    {$optimization_tab|sil_nofilter}
                </div>
                <div id="config-5">
                    {$delete_tab|sil_nofilter}
                </div>
                <div id="config-6">
                    {$cron_tab|sil_nofilter}
                </div>
            </div>

            <script type="text/javascript">
                {literal}
                $(document).ready(function() {
                    $("#wrapConfigTab").tabs({cache:false});
                });
                {/literal}
            </script>
        {else}
            {module->_showWarning text="
                <p>{l s='We have detected that you installed a new version of the module on your shop' mod='pm_seointernallinking'}</p>
                <p style=\"text-align: center\">
                    <a href=\"{$base_config_url|sil_nofilter}&makeUpdate=1\" class=\"button\">
                        {l s='Please click here in order to finish the installation process' mod='pm_seointernallinking'}
                    </a>
                </p>
            "}
        {/if}
    {else}
        {module->_showWarning text="
            {l s='Before being able to configure the module, make sure to set write permissions to files and folders listed below:' mod='pm_seointernallinking'}<br /><br />
            {$permissions_errors|implode:'<br />'|sil_nofilter}
        "}
    {/if}

    {module->_displaySupport}
</div>