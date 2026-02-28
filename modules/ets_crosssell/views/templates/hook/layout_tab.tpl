{*
* 2007-2022 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 website only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses.
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{if $sc_configs}
    <div class="ets_crosssell_block ets_crosssell_layout-tab layout-{$layout_mode|escape:'html':'UTF-8'} block products_block ets_crosssell_{$name_page|escape:'html':'UTF-8'} layout_tab clearfix ">
        <ul id="{$name_page|escape:'html':'UTF-8'}-tabs" class="ets_crosssell_nav_tabs nav nav-tabs clearfix">
            {foreach from=$sc_configs key='key' item='sc_config'}
                <li class="{if $key==0}active{/if}">
                    <a class="ets_crosssell_tab" data-page="{$name_page|escape:'html':'UTF-8'}" data-tab="{$sc_config.tab|escape:'html':'UTF-8'}" data-id_product="{$id_product|intval}" href="#tab-content-{$name_page|escape:'html':'UTF-8'}-{$sc_config.tab|escape:'html':'UTF-8'}">{$sc_config.tab_name|escape:'html':'UTF-8'}</a>
                </li>
            {/foreach}
        </ul>
        <div id="{$name_page|escape:'html':'UTF-8'}-contents" class="ets_crosssell_tab_content tab-content row">
            <div id="tab-content-{$name_page|escape:'html':'UTF-8'}-{$sc_configs[0].tab|escape:'html':'UTF-8'}" class="list-content active{if $sc_configs[0].sub_categories} ets_crosssell_has_sub{/if}">
                {Module::getInstanceByName('ets_crosssell')->excuteHookDisplay($sc_configs[0].hook,$name_page,$id_product) nofilter}
            </div>
        </div>
    </div>
{/if}