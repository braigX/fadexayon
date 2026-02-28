{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
<style>
    .show-on-auto {
        display: none;
    }
    .hide-on-auto {
        display: block;
    }
    .cw_create_auto .show-on-auto {
        display: block;
    }
    .cw_create_auto .hide-on-auto {
        display: none;
    }
</style>
<script type="application/javascript">
    function jprestaUpdateCount() {
        let pages_count = 0;
        $('[name="warmup_controllers[]"]:checked').each(function () {
            pages_count += $(this).data('page-count');
        });
        let contexts_count = $('#contexts tbody tr').length - 1;
        $('#pages_count').html(pages_count);
        $('#contexts_count').html(contexts_count);
        $('#total_pages_count').html(pages_count * contexts_count);
        $('#total_pages_count').removeClass('cachewarmer_count_warn').removeClass('cachewarmer_count_danger');
        if (pages_count * contexts_count > 100000) {
            if (pages_count * contexts_count > 200000) {
                $('#total_pages_count').addClass('cachewarmer_count_danger');
            }
            else {
                $('#total_pages_count').addClass('cachewarmer_count_warn');
            }
        }
    }
    function jprestaDeleteContexts(elt) {
        $(elt).parents('tr').remove();
        jprestaUpdateCount();
    }
    function jprestaAddContexts() {
        let newIndex = 0;
        $('#contexts tbody tr').each(function() {
            if ($(this).data('context-index')) {
                newIndex = Math.max(newIndex, $(this).data('context-index'));
            }
        });
        newIndex++;
        let html = $('#contexts tbody tr:first-child').clone().html();
        $('<tr data-context-index="' + newIndex + '">' + html.replaceAll(' disabled="disabled"', '').replaceAll('XXX', newIndex) + '</tr>').appendTo('#contexts tbody');
        jprestaUpdateCount();
    }
    $(function() {
        jprestaUpdateCount();
        $('[name=pagecache_cw_create_auto]').on('change', function() {
            if ($('#pagecache_cw_create_auto_on').is(':checked')) {
                $('#panelCw').addClass('cw_create_auto');
            }
            else {
                $('#panelCw').removeClass('cw_create_auto');
            }
        });
    });
</script>
<div id="panelCw" class="panel{if $pagecache_cw_contexts->contexts_auto|default:false} cw_create_auto{/if}" style="margin-bottom: 10px">
    <h3><a href="{$pagecache_cw_url|escape:'html':'UTF-8'}" target="_blank">{if $avec_bootstrap}<i class="icon-gear"></i>{else}<img width="16" height="16" src="../img/admin/AdminPreferences.gif" alt=""/>{/if}</a>
        &nbsp;{l s='Cache Warmer settings' mod='jprestaspeedpack'}
    </h3>
    {if count($pagecache_cw_contexts->specifics) > 0}
        <form id="pagecache_form_cachewarmer" action="{$request_uri|escape:'html':'UTF-8'}#tabcachewarmer-settings" method="post">
            <input type="hidden" name="submitModule" value="true"/>
            <input type="hidden" name="pctab" value="cachewarmer-settings"/>
            <input type="hidden" name="cachewarmer_id_shop" value="{$pagecache_cw_contexts->id_shop|intval}"/>
            <fieldset>
                <div class="bootstrap">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bootstrap">
                                <div class="alert alert-info" style="display: block;">&nbsp;<b>{l s='These settings will be used by the cache warmer service if you subscribed to it. See below for more informations.' mod='jprestaspeedpack'}</b>
                                </div>
                            </div>
                            <p>{l s='The cache warmer browses your site in different contexts so all visitors will get a page on which the cache is available.' mod='jprestaspeedpack'}</p>
                            <p>{l s='The more you have contexts, the more the warm-up will be long and the cache will consumme resources (database and hard disk).' mod='jprestaspeedpack'}</p>
                            <p>{l s='The purpose of these settings is to select which contexts you want to warm-up.' mod='jprestaspeedpack'}</p>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1rem">
                        <div class="col-md-12">
                            <h4>{l s='Pages to warmup' mod='jprestaspeedpack'}</h4>
                            {foreach $managed_controllers as $controller_name => $controller}
                                {if $controller['warmer']}
                                    <span style="margin-right: 1rem;white-space: nowrap;">
                                    <input type="checkbox"
                                           onchange="jprestaUpdateCount()"
                                           style="vertical-align: middle; margin: 0 2px;"
                                           id="warmup_page_{$controller_name|escape:'html':'UTF-8'}"
                                           name="warmup_controllers[]"
                                           {if isset($pagecache_cw_contexts->controllers[$controller_name]) && $pagecache_cw_contexts->controllers[$controller_name]['checked']}checked="checked" {/if}
                                            {if isset($pagecache_cw_contexts->controllers[$controller_name]) && $pagecache_cw_contexts->controllers[$controller_name]['disabled']}disabled="disabled" {/if}
                                           value="{$controller_name|escape:'html':'UTF-8'}"
                                           data-page-count="{if isset($pagecache_cw_contexts->controllers[$controller_name])}{$pagecache_cw_contexts->controllers[$controller_name]['count']|intval}{else}0{/if}"
                                    >
                                    <label for="warmup_page_{$controller_name|escape:'html':'UTF-8'}" {if isset($pagecache_cw_contexts->controllers[$controller_name]) && $pagecache_cw_contexts->controllers[$controller_name]['count'] != null}title="About {$pagecache_cw_contexts->controllers[$controller_name]['count']|intval} page(s)"{/if}>{$controller['title']|escape:'html':'UTF-8'}</label>
                                </span>
                                {/if}
                            {/foreach}
                            <div style="margin-top: 1rem;font-weight: bold; font-size: 0.9rem; text-transform: uppercase;">{l s='Options' mod='jprestaspeedpack'}:</div>
                            <div class="row" style="margin-top: 0.4rem;">
                                <div class="col-md-3">
                                    <label
                                            for="pagecache_cw_filter_products_cats_ids">{l s='Only warmup products of these categories' mod='jprestaspeedpack'}</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text"
                                           name="pagecache_cw_filter_products_cats_ids"
                                           id="pagecache_cw_filter_products_cats_ids"
                                           value="{$pagecache_cw_contexts->filter_products_cats_ids|default:''|escape:'html':'UTF-8'}"
                                    >
                                    <div>{l s='Comma separated list of categories\' IDs. Leave empty to warmup all products.' mod='jprestaspeedpack'}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1rem">
                        <div class="col-md-12">
                            <h4>{l s='Contexts to warmup' mod='jprestaspeedpack'}</h4>

                            <div class="alert alert-info">
                                <p>
                                    {l s='We recommend you to let the module generates contexts to warmup. They are generated depending on the statistics of your shop to generate the cache of the most viewed pages in priority. Creating contexts manually can be less efficient but you can do it if you need.' mod='jprestaspeedpack'}
                                </p>
                            </div>
                            <div class="row" style="margin-bottom: 1rem">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label col-lg-3" for="">{l s='How to create contexts to warmup?' mod='jprestaspeedpack'}</label>
                                        <div class="col-lg-9">
                                        <span class="switch prestashop-switch fixed-width-xxl">
                                            <input type="radio" name="pagecache_cw_create_auto" id="pagecache_cw_create_auto_on" value="1" {if $pagecache_cw_contexts->contexts_auto|default:0}checked{/if}>
                                            <label for="pagecache_cw_create_auto_on" class="radioCheck">{l s='Automatically' mod='jprestaspeedpack'}</label>
                                            <input type="radio" name="pagecache_cw_create_auto" id="pagecache_cw_create_auto_off" value="0" {if !$pagecache_cw_contexts->contexts_auto|default:0}checked{/if}>
                                            <label for="pagecache_cw_create_auto_off" class="radioCheck">{l s='Manually' mod='jprestaspeedpack'}</label>
                                            <a class="slide-button btn"></a>
                                        </span>
                                            <p class="help-block">
                                                {l s='We highly recommend to create contexts automatically' mod='jprestaspeedpack'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="hide-on-auto">
                                <div class="alert alert-info">
                                    <p>
                                        {l s='Please, create all contexts that you want to warmup' mod='jprestaspeedpack'}
                                    </p>
                                    <hr>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-flag"></i>{else}<img width="16" height="16" src="../img/admin/world.gif" alt=""/>{/if}&nbsp;{l s='Languages' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s='Available languages are the ones enabled for this shop' mod='jprestaspeedpack'}
                                    </p>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-money"></i>{else}<img width="16" height="16" src="../img/admin/money.gif" alt=""/>{/if}&nbsp;{l s='Currencies' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s='Available currencies are the ones enabled for this shop' mod='jprestaspeedpack'}
                                    </p>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-desktop"></i>{else}<img width="16" height="16" src="../img/admin/metatags.gif" alt=""/>{/if}&nbsp;{l s='Devices' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s="You can only select 'mobile' if you enabled the option 'Create separate cache for desktop and mobile' in advanced mode, in menu Cache Key > Devices" mod='jprestaspeedpack'}
                                    </p>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-map-marker"></i>{else}<img width="16" height="16" src="../img/admin/world.gif" alt=""/>{/if}&nbsp;{l s='Countries' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s='Available countries are the ones you selected in advanced mode, in menu Cache Key > Countries' mod='jprestaspeedpack'}
                                    </p>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-users"></i>{else}<img width="16" height="16" src="../img/admin/group.gif" alt=""/>{/if}&nbsp;{l s='User groups combinations' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s="Available user groups are the ones currently used by the cache. To add a user group or a user group combination you just need to connect to the shop with a corresponding customer account when the cache is enabled. If you still don't find it, that means this user group or user group combination does not need a specific cache. More informations in advanced mode, in menu Cache Key > User groups" mod='jprestaspeedpack'}
                                    </p>
                                    <p><strong>{if $avec_bootstrap}<i class="icon-cogs"></i>{else}<img width="16" height="16" src="../img/admin/cogs.gif" alt=""/>{/if}&nbsp;{l s='Specifics' mod='jprestaspeedpack'}</strong>&nbsp;:&nbsp;
                                        {l s='Specifics are mostly used for RGPD law; it creates different cache for visitor accepting cookies or not. The list is based on current cache statistics.' mod='jprestaspeedpack'}
                                    </p>
                                </div>

                                <table id="contexts" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th><a onclick="jprestaAddContexts(); return false;" class="btn btn-xs btn-primary" href="#"><i class="icon-plus"></i></a></th>
                                        <th>{if $avec_bootstrap}<i class="icon-flag"></i>{else}<img width="16" height="16" src="../img/admin/world.gif" alt=""/>{/if}&nbsp;{l s='Languages' mod='jprestaspeedpack'}</th>
                                        <th>{if $avec_bootstrap}<i class="icon-money"></i>{else}<img width="16" height="16" src="../img/admin/money.gif" alt=""/>{/if}&nbsp;{l s='Currencies' mod='jprestaspeedpack'}</th>
                                        <th>{if $avec_bootstrap}<i class="icon-desktop"></i>{else}<img width="16" height="16" src="../img/admin/metatags.gif" alt=""/>{/if}&nbsp;{l s='Devices' mod='jprestaspeedpack'}</th>
                                        <th>{if $avec_bootstrap}<i class="icon-flag"></i>{else}<img width="16" height="16" src="../img/admin/world.gif" alt=""/>{/if}&nbsp;{l s='Countries' mod='jprestaspeedpack'}</th>
                                        <th>{if $avec_bootstrap}<i class="icon-users"></i>{else}<img width="16" height="16" src="../img/admin/group.gif" alt=""/>{/if}&nbsp;{l s='User groups' mod='jprestaspeedpack'}</th>
                                        <th>{if $avec_bootstrap}<i class="icon-cogs"></i>{else}<img width="16" height="16" src="../img/admin/cogs.gif" alt=""/>{/if}&nbsp;{l s='Specifics' mod='jprestaspeedpack'}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr style="display:none">
                                        <td><a onclick="jprestaDeleteContexts(this); return false;" class="btn btn-xs btn-primary deletecontext" href="#"><i class="icon-trash"></i></a></td>
                                        <td>
                                            <select name="contexts[XXX][language]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->languages as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td>
                                            <select name="contexts[XXX][currency]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->currencies as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td>
                                            <select name="contexts[XXX][device]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->devices as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td>
                                            <select name="contexts[XXX][country]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->countries as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td>
                                            <select name="contexts[XXX][group]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->groups as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td>
                                            <select name="contexts[XXX][specifics]" disabled="disabled">
                                                {foreach $pagecache_cw_contexts->specifics as $context}
                                                    <option value="{$context['value']|escape:'html':'UTF-8'}">{$context['label']|escape:'html':'UTF-8'}{if isset($context['count'])} ({$context['count']|intval}){/if}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                    </tr>
                                    {foreach $pagecache_cw_contexts->contexts as $index => $context}
                                        <tr data-context-index="{$index|intval}">
                                            <input type="hidden" name="contexts[{$index|intval}][language]" value="{$context['language']|default:''|escape:'html':'UTF-8'}">
                                            <input type="hidden" name="contexts[{$index|intval}][currency]" value="{$context['currency']|default:''|escape:'html':'UTF-8'}">
                                            <input type="hidden" name="contexts[{$index|intval}][device]" value="{$context['device']|default:''|escape:'html':'UTF-8'}">
                                            <input type="hidden" name="contexts[{$index|intval}][country]" value="{$context['country']|default:''|escape:'html':'UTF-8'}">
                                            <input type="hidden" name="contexts[{$index|intval}][group]" value="{$context['group']|default:''|escape:'html':'UTF-8'}">
                                            <input type="hidden" name="contexts[{$index|intval}][specifics]" value="{$context['specifics']|default:''|escape:'html':'UTF-8'}">
                                            <td><a onclick="jprestaDeleteContexts(this); return false;" class="btn btn-xs btn-primary" href="#"><i class="icon-trash"></i></a></td>
                                            <td>{$pagecache_cw_contexts->languages[$context['language']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                            <td>{$pagecache_cw_contexts->currencies[$context['currency']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                            <td>{$pagecache_cw_contexts->devices[$context['device']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                            <td>{$pagecache_cw_contexts->countries[$context['country']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                            <td>{$pagecache_cw_contexts->groups[$context['group']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                            <td>{$pagecache_cw_contexts->specifics[$context['specifics']]['label']|default:''|escape:'html':'UTF-8'}</td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 1rem">
                        <div class="col-md-12">
                            <h4>{l s='Total pages to warmup' mod='jprestaspeedpack'}</h4>
                            <div class="show-on-auto">
                                <div class="bootstrap">
                                    <div class="alert alert-info" style="display: block;">&nbsp;{l s='When contexts are automatically created the number of pages to warm up will be adapted to your subscription plan' mod='jprestaspeedpack'}
                                    </div>
                                </div>
                            </div>
                            <div class="hide-on-auto">
                                <div class="bootstrap">
                                    <div class="alert alert-info" style="display: block;">&nbsp;{l s='Try to have less than 100000 pages to warmup or it will be too long to be processed by the cache-warmer in a single day' mod='jprestaspeedpack'}
                                    </div>
                                </div>
                                <table class="table" style="width: initial">
                                    <tbody>
                                    <tr>
                                        <td>{l s='Estimated number of pages per context' mod='jprestaspeedpack'}</td>
                                        <td id="pages_count" class="cachewarmer_count"></td>
                                    </tr>
                                    <tr>
                                        <td>{l s='Number of context' mod='jprestaspeedpack'}</td>
                                        <td id="contexts_count" class="cachewarmer_count"></td>
                                    </tr>
                                    <tr>
                                        <td>{l s='Total pages to warmup' mod='jprestaspeedpack'}</td>
                                        <td id="total_pages_count" class="cachewarmer_count"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="submit" value="1" id="submitModuleCacheWarmerSettings" name="submitModuleCacheWarmerSettings"
                            class="btn btn-default pull-right">
                        <i class="process-icon-save"></i> {l s='Save' mod='jprestaspeedpack'}
                    </button>
                </div>
            </fieldset>
        </form>
    {else}
        <div class="alert alert-warning">
            {l s='Before setting the pages that you want to warmup, you, or your visitors, need to browse your shop a little bit. Why? So the module know the different contexts that can be used on your shop. So, you just have to browse different pages of your store and reload this page.' mod='jprestaspeedpack'}
        </div>
    {/if}

</div>
