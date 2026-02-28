{**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 *}
<div class="col-lg-2">
    <div class="list-group">
        {foreach from=$tabs key=tab_key item=tab}
            <a 
                {if $tab_key == 'version' || $tab_key == 'rateMe'} style="margin-top:30px;" {/if}
                class="list-group-item {if $tab_key == $active_tab || ($active_tab == '' && $tab_key == 'generelSettings')}active{/if}"
                href="{if isset($tab.url)}{$tab.url nofilter}{else}{$module_url|escape:'htmlall':'UTF-8'}&{$module_tab_key|escape:'htmlall':'UTF-8'}={$tab_key|escape:'htmlall':'UTF-8'}{/if}"
                {if isset($tab.url)}
                    target="_blank"
                {/if}
            >
                {if isset($tab.icon)}
                    <i class="{$tab.icon|escape:'htmlall':'UTF-8'}" {if $tab_key == 'rateMe'}style="color: orange;"{/if}></i>
                {/if}
                {if $tab_key != 'version'}
                    {$tab.title|escape:'htmlall':'UTF-8'}
                {else}
                    {$tab.title|escape:'htmlall':'UTF-8'} - {$module_version|escape:'html':'UTF-8'}
                {/if}
            </a>
        {/foreach}
    </div>
</div>