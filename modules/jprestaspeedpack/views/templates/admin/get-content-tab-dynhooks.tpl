{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}

{if $avec_bootstrap}
    {assign var=logo value='logo.png'}
{else}
    {assign var=logo value='logo.gif'}
{/if}

<script type="text/javascript">
    function addWidget(widgetDisplayName, widgetName, hookName, emptyBox, widgetVersion, widgetAuthor, widgetDescription, widgetId) {
        $("#widgetTables").append("<tr>" +
            "<td><img width=\"32\" src=\"../modules/"+widgetName+"/logo.png\" title=\""+widgetName+" #"+widgetId+" - "+widgetDescription+"\"/> "+widgetDisplayName+" <small class=\"text-muted\">&nbsp;-&nbsp;v"+widgetVersion+"</small><small class=\"text-muted\">&nbsp;-&nbsp;"+widgetAuthor+"</small></td>" +
            "<td style=\"text-align: center\">"+hookName+"</td>" +
            "<td style=\"text-align: center\"><input" + (emptyBox ? " checked" : "") + " disabled type=\"checkbox\"/></td>" +
            "<td><button type=\"button\" onclick=\"removeWidget(\'"+widgetName+"\', \'"+hookName+"\'); this.closest(\'tr\').remove();\"><i class=\"icon-remove\"></i> {l s='Remove' mod='jprestaspeedpack'}</button><input type=\"hidden\" name=\"pagecache_dynwidgets[]\" value=\""+widgetName+"|"+hookName+"|"+(emptyBox ? "1" : "0")+"\"/></td></tr>");
    }
    function removeWidget(widgetName, hookName) {
    }
    $(function() {
        $("#dynhook_filter").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#dynhooks_table > tbody > tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>
<div class="panel">
<h3>{if $avec_bootstrap}<i class="icon-puzzle-piece"></i>{else}<img width="16" height="16" src="../img/admin/tab-plugins.gif" alt=""/>{/if}&nbsp;{l s='Dynamic modules and widgets' mod='jprestaspeedpack'}</h3>
<form id="pagecache_form_dynhooks" action="{$request_uri|escape:'html':'UTF-8'}" method="post">
    <input type="hidden" name="submitModule" value="true"/>
    <input type="hidden" name="pctab" value="dynhooks"/>
    <fieldset>
        <div style="clear: both;">
            {if !$pagecache_debug}
                {if $avec_bootstrap}
                    <div class="bootstrap"><div class="alert alert-warning" style="display: block;">&nbsp;{l s='To be able to modify dynamic modules and widgets you must go back in "test mode" in first tab' mod='jprestaspeedpack'}</div></div>
                {else}
                    <div class="warn clear" style="display: block;">&nbsp;{l s='To be able to modify dynamic modules and widgets you must go back in "test mode" in first tab' mod='jprestaspeedpack'}</div>
                {/if}
            {/if}

            <p>{l s='You cannot exclude a module from the cache but you can set it as dynamic. A dynamic module will be displayed in "anonymous mode" in the cache, then a background request will refresh it in order to display it with the context of the current visitor.' mod='jprestaspeedpack'}</p>

            {if $avec_bootstrap}
                <div class="bootstrap"><div class="alert alert-info" style="display: block;">&nbsp;{l s='Note that dynamic module Ajax call are done all at once (one HTTP request)' mod='jprestaspeedpack'}</div></div>
            {else}
                <div class="hint clear" style="display: block;">&nbsp;{l s='Note that dynamic module Ajax call are done all at once (one HTTP request)' mod='jprestaspeedpack'}</div>
            {/if}

            <br/><h4 id="tabdynhooksmodules">{l s='Dynamic modules' mod='jprestaspeedpack'}</h4>

            <input type="text" id="dynhook_filter" placeholder="{l s='Filter' mod='jprestaspeedpack'}" style="margin: 5px 0; width: 200px"/>
            <table id="dynhooks_table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="40%">{l s='Module' mod='jprestaspeedpack'}</th>
                        <th width="60%">{l s='Hooks' mod='jprestaspeedpack'}</th>
                    </tr>
                </thead>
                <tbody>
                    {assign var=indexRow value=0}
                    {foreach $modules_hooks as $module_name => $moduleInfos}
                        <tr>
                            <td style="vertical-align: top">
                                <img width="32" src="../modules/{$module_name|escape:'html':'UTF-8'}/logo.png" title="{$module_name|escape:'html':'UTF-8'} #{$moduleInfos['id_module']|intval} - {$moduleInfos['description']|escape:'html':'UTF-8'}" />
                                {$moduleInfos['display_name']|escape:'html':'UTF-8'}
                                {if $moduleInfos['version']}
                                    <small class="text-muted">&nbsp;-&nbsp;v{$moduleInfos['version']|escape:'html':'UTF-8'}</small>
                                {/if}
                                {if $moduleInfos['author']}
                                    <small class="text-muted">&nbsp;-&nbsp;{$moduleInfos['author']|escape:'html':'UTF-8'}</small>
                                {/if}
                            </td>
                            <td>
                                <table class="table">
                                    <colgroup>
                                        <col width="0*">
                                        <col width="*">
                                        <col width="0*">
                                        <col width="50%">
                                    </colgroup>
                                {foreach $moduleInfos['hooks'] as $hook_name => $hook_infos}
                                    <tr>
                                        <td width="15"><input {if $hook_infos['dyn_is_checked']}checked{/if} {if !$pagecache_debug}disabled{/if} type="checkbox" name="pagecache_hooks[]" id="dyn{$indexRow|escape:'html':'UTF-8'}" value="{$hook_name|escape:'html':'UTF-8'}|{$module_name|escape:'html':'UTF-8'}" onclick="$('.emptyspan{$indexRow|escape:'html':'UTF-8'}').toggle();"/></td>
                                        <td><label for="dyn{$indexRow|escape:'html':'UTF-8'}">{$hook_name|escape:'html':'UTF-8'}</label></td>
                                        <td width="15">
                                            <span {if !$hook_infos['dyn_is_checked']}style="display:none"{/if} class="emptyspan{$indexRow|escape:'html':'UTF-8'}">
                                                <input {if $hook_infos['empty_option_checked']}checked{/if} {if !$pagecache_debug}disabled{/if} type="checkbox" name="pagecache_hooks_empty_{$hook_name|escape:'html':'UTF-8'}_{$module_name|escape:'html':'UTF-8'}" id="emptyoption{$indexRow|escape:'html':'UTF-8'}" value="1"/>
                                            </span>
                                        </td>
                                        <td>
                                            <span {if !$hook_infos['dyn_is_checked']}style="display:none"{/if} class="emptyspan{$indexRow|escape:'html':'UTF-8'}">
                                                <label class="t" for="emptyoption{$indexRow|escape:'html':'UTF-8'}">{l s='Display nothing in cache' mod='jprestaspeedpack'}</label>
                                            </span>
                                        </td>
                                    </tr>
                                    {assign var=indexRow value=$indexRow+1}
                                {/foreach}
                                </table>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>

            <h4 id="tabdynhookswidgets" style="margin-top: 20px">{l s='Dynamic widgets' mod='jprestaspeedpack'}</h4>
            <input type="hidden" name="pcdynwidgets" value=""/>
            <p>{l s='Widgets are modules that can be displayed anywhere in the theme; they do not need any hook. This feature has been added in Prestashop 1.7. A widget can be displayed with an optional "hookName" that is used to choose a specific template.' mod='jprestaspeedpack'}</p>
            <p>{l s='Here you can specify which widget must be refreshed dynamically (is relative to the current visitor).' mod='jprestaspeedpack'}</p>

            <table style="margin: 15px">
                <tr>
                    <td style="padding-right: 5px"><label for="widgetName" style="float:inherit">{l s='Widget' mod='jprestaspeedpack'}</label></td>
                    <td><label for="widgetHookName" style="float:inherit; padding-left: 20px">{l s='Hook name (optional)' mod='jprestaspeedpack'}</label></td>
                </tr>
                <tr>
                    <td style="padding-right: 5px">
                        <select {if !$pagecache_debug}disabled{/if} name="widgetName" id="widgetName" style="width: 200px">
                            {foreach $widgets as $widget_name => $widget_infos}
                                <option value="{$widget_name|escape:'html':'UTF-8'}" data-version="{$widget_infos['version']|escape:'html':'UTF-8'}" data-author="{$widget_infos['author']|escape:'html':'UTF-8'}" data-description="{$widget_infos['description']|escape:'html':'UTF-8'}" data-id="{$widget_infos['id_module']|escape:'html':'UTF-8'}">{$widget_infos['display_name']|escape:'html':'UTF-8'} ({$widget_name|escape:'html':'UTF-8'})</option>
                            {/foreach}
                        </select>
                    </td>
                    <td style="padding-right: 5px">
                        <input {if !$pagecache_debug}disabled{/if} id="widgetHookName" name="widgetHookName" style="width: 200px;" value="" type="text"/>
                    </td>
                    <td style="padding-right: 5px">
                        <div class="checkbox"><label for="widgetEmptyBox"><input {if !$pagecache_debug}disabled{/if} id="widgetEmptyBox" name="widgetEmptybox" value="1" type="checkbox"/>{l s='Display nothing in cache' mod='jprestaspeedpack'}</label></div>
                    </td>
                    <td>
                        <button {if !$pagecache_debug}disabled{/if} type="button" onclick="addWidget($('#widgetName option:selected').text(), $('#widgetName').val(), $('#widgetHookName').val(), $('#widgetEmptyBox').is(':checked'), $('#widgetName option:selected').data('version'), $('#widgetName option:selected').data('author'), $('#widgetName option:selected').data('description'), $('#widgetName option:selected').data('id'))" class="btn btn-default"><i class="icon-plus"></i> {l s='Add' mod='jprestaspeedpack'}</button>
                    </td>
                </tr>
            </table>

            <div class="bootstrap">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr><th>{l s='Widget' mod='jprestaspeedpack'}</th><th style="text-align: center">{l s='Hook name' mod='jprestaspeedpack'}</th><th style="text-align: center">{l s='Display nothing in cache' mod='jprestaspeedpack'}</th><th></th></tr>
                    </thead>
                    <tbody id="widgetTables">
                        {foreach $dynamic_widgets as $widgetInfos}
                            <tr>
                                <td>
                                    <img width="32" src="../modules/{$widgetInfos['name']|escape:'html':'UTF-8'}/logo.png" title="{$widgetInfos['name']|escape:'html':'UTF-8'} #{$widgetInfos['id_module']|intval} - {$widgetInfos['description']|escape:'html':'UTF-8'}" />
                                    {$widgetInfos['display_name']|escape:'html':'UTF-8'}
                                    {if $widgetInfos['version']}
                                        <small class="text-muted">&nbsp;-&nbsp;v{$widgetInfos['version']|escape:'html':'UTF-8'}</small>
                                    {/if}
                                    {if $widgetInfos['author']}
                                        <small class="text-muted">&nbsp;-&nbsp;{$widgetInfos['author']|escape:'html':'UTF-8'}</small>
                                    {/if}
                                </td>
                                <td style="text-align: center">{$widgetInfos['hook']|escape:'html':'UTF-8'}</td>
                                <td style="text-align: center"><input {if $widgetInfos['empty_box']}checked{/if} disabled type="checkbox"/></td>
                                <td>{if $pagecache_debug}<button type="button" onclick="removeWidget('{$widgetInfos['name']|escape:'html':'UTF-8'}', '{$widgetInfos['hook']|escape:'html':'UTF-8'}'); this.closest('tr').remove();"><i class="icon-remove"></i> {l s='Remove' mod='jprestaspeedpack'}</button><input type="hidden" name="pagecache_dynwidgets[]" value="{$widgetInfos['name']|escape:'html':'UTF-8'}|{$widgetInfos['hook']|escape:'html':'UTF-8'}|{$widgetInfos['empty_box']|intval}"/>{/if}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>

            <h4 id="tabdynhooksjs" style="margin-top: 20px">{l s='Javascript to execute' mod='jprestaspeedpack'}</h4>
            <div id="cfgadvanced">
                <p>{l s='Here you can modify javascript code that is executed after dynamic modules and widgets have been displayed on the page.' mod='jprestaspeedpack'}</p>
                <p>{l s='If you meet problems with your theme, ask your theme designer what javascript you should add here.' mod='jprestaspeedpack'}</p>
                <textarea {if !$pagecache_debug}disabled{/if} name="cfgadvancedjs" style="width:95%" rows="20">{$pagecache_cfgadvancedjs|escape:'html':'UTF-8'}</textarea>
            </div>

        </div>
        <br/>
        <div class="bootstrap">
            <button type="submit" value="1" id="submitModuleDynhooks" name="submitModuleDynhooks" class="btn btn-default pull-right" {if !$pagecache_debug}disabled{/if}>
                <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
            </button>
        </div>
    </fieldset>
</form>
</div>
