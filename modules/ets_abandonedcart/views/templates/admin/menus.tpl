{*
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{block name='menu'}
    {if isset($menus) && $menus}
        <div class="aban_menu_height" style="display: block;height:1px;"></div>
        <div class="aband_group_header_fixed">
            {assign var='_breadcrumb' value=''}
            <ul class="ets_abancart_menus aband_group_header">
                {foreach from=$menus key='id' item='menu'}
                    <li class="ets_abancart_menu_li{if $controller_name|trim == $slugTab|cat:$menu.class|trim || $menu.class|trim === 'Campaign' && preg_match('#Reminder#', $controller_name) || $menu.class|trim === 'MailConfigs' && preg_match('#(Mail|Queue|Indexed|Unsubscribed)#', $controller_name) || $menu.class|trim === 'Tracking' && preg_match('#(EmailTracking|DisplayTracking|Discounts|DisplayLog)#', $controller_name)} active{/if}">
                        {assign var='_breadcrumb_first' value=$id}
                        {include file="./menu.tpl"}
                        {if isset($menu.sub_menus) && $menu.sub_menus}
                            <ul class="ets_abancart_sub_menus">
                                {foreach from=$menu.sub_menus key='id' item='sub_menu'}
                                    <li class="ets_abancart_sub_menu_li{if $controller_name|trim === $slugTab|cat:$sub_menu.class|trim}{assign var='_breadcrumb' value=$_breadcrumb_first|cat:','|cat:$id} active{/if}">
                                        {include file="./menu.tpl" menu=$sub_menu}
                                    </li>
                                {/foreach}
                            </ul>
                        {elseif $controller_name !== 'AdminEtsACDashboard' && $controller_name|trim == $slugTab|cat:$menu.class|trim}
                            {assign var='_breadcrumb' value=$id}
                            {assign var='onLv2' value=1}
                        {/if}
                    </li>
                {/foreach}
                <li class="ets_abancart_menu_li more_menu">
                    <span class="more_three_dots"></span>
                </li>
            </ul>
        </div>

    {/if}
{/block}
{block name='breadcrumb'}
    {if isset($isModuleDisabled) && $isModuleDisabled}
        <div class="alert alert-warning">
            {l s='Please enable module to use the features of Abandoned Cart Reminder + Auto Email module' mod='ets_abandonedcart'}
        </div>
    {/if}

    {if $_breadcrumb || $controller_name == 'AdminEtsACCampaign'}
        <div class="ets_abancart_breadcrumb">
            <a href="{$link->getAdminLink($slugTab|cat:'Dashboard', true)|escape:'html':'UTF-8'}" title="{l s='Home' mod='ets_abandonedcart'}"><span class="breadcrumb"><i class="icon-home"></i></span></a>
            {assign var="dot" value=" > "}{$dot|escape:'html':'UTF-8'}
            {assign var='_breadcrumb' value=explode(',', $_breadcrumb)}{assign var="ik" value="0"}
            {if $controller_name !== 'AdminEtsACCampaign'}
                {foreach from=$_breadcrumb item='id'}
                    {assign var="ik" value=$ik+1}
                    {if isset($menus[$id]) && $menus[$id]}
                        {assign var='menu' value=$menus[$id]}
                        {if isset($onLv2) && $onLv2}
                            {if isset($leadFormTitle) && $leadFormTitle}
                                <a href="{$link->getAdminLink($slugTab|cat: $menu.class, true)|escape:'quotes':'UTF-8'}"><span class="breadcrumb">{$menu.label|escape:'html':'UTF-8'}</span></a>
                            {else}
                                <span class="breadcrumb">{$menu.label|escape:'html':'UTF-8'}</span>
                            {/if}
                        {else}
                            <a href="{$link->getAdminLink($slugTab|cat: $menu.class, true)|escape:'quotes':'UTF-8'}"><span class="breadcrumb">{$menu.label|escape:'html':'UTF-8'}</span></a>
                        {/if}
                    {else}
                        {foreach from=$menus key='id_menu' item='menu'}{if !empty($menu.sub_menus)}{foreach from=$menu.sub_menus key='id_menu2' item='menu2'}
                            {if $id_menu2 == $id}
                                {if isset($campaignName) && $campaignName}<a href="{$link->getAdminLink($slugTab|cat: $menu2.class, true)|escape:'quotes':'UTF-8'}">{/if}
                                <span class="breadcrumb">{$menu2.label|escape:'html':'UTF-8'}</span>
                                {if isset($campaignName) && $campaignName}</a>{/if}
                            {/if}
                        {/foreach}{/if}{/foreach}
                    {/if}
                    {if $ik < $_breadcrumb|count}{$dot|escape:'html':'UTF-8'}{/if}
                {/foreach}
            {else}
                {l s='Reminder campaigns' mod='ets_abandonedcart'}
            {/if}
            {if isset($campaignName) && $campaignName}
                {$dot|escape:'html':'UTF-8'}
                <span class="breadcrumb">{$campaignName|escape:'html':'UTF-8'}</span>
            {/if}
            {if isset($leadFormTitle) && $leadFormTitle}
                {$dot|escape:'html':'UTF-8'}
                <span class="breadcrumb">{$leadFormTitle|escape:'html':'UTF-8'}</span>
            {/if}
        </div>
    {/if}
{/block}
