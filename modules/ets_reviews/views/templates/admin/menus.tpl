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
{assign var="menu_exist" value=isset($menus) && $menus|count > 0 && isset($currentTab) && currentTab != ''}
{if $menu_exist}
    <div class="ets-pc-panel-heading_height"></div>
    <div class="ets-pc-panel-heading">
        <ul class="form-group-menus {if isset($isSupperAdmin) && $isSupperAdmin} is-supper-admin{/if}">
            {foreach from=$menus key='id' item='menu'}
                {assign var='hasSelected' value=0}
                {if isset($menu.sub) && $menu.sub}
                    {foreach from=$menu.sub item='sub_menu'}{if $tabPrefix|cat:$sub_menu.class == $currentTab}{assign var='hasSelected' value=1}{/if}{/foreach}
                {/if}
                <li class="form-menu-item {$menu.class|lower|escape:'html':'UTF-8'}{if $tabPrefix|cat:$menu.class == $currentTab || $hasSelected} active{/if}{if isset($menu.sub) && $menu.sub|count > 0} has-sub{/if}">
                    <a class="form-menu-item-link {$menu.class|lower|escape:'html':'UTF-8'}" href="{$link->getAdminLink($tabPrefix|cat:$menu.class) nofilter}">{$menu.label|escape:'quotes':'UTF-8'}</a>
                    {if isset($menu.sub) && $menu.sub|count > 0}
                        <ul class="form-group-sub-menus">
                            {foreach from=$menu.sub item='sub_menu'}
                                <li class="form-sub-menu-item {$sub_menu.class|lower|escape:'html':'UTF-8'}{if !empty($sub_menu.tab)} {$sub_menu.tab|escape:'html':'UTF-8'}{/if}{if $tabPrefix|cat:$sub_menu.class == $currentTab && (!isset($sub_menu.tab) || $sub_menu.tab|trim == $tabActive|trim)} active{/if}">
                                    <a class="form-sub-menu-item-link {$sub_menu.class|lower|escape:'html':'UTF-8'}" href="{$link->getAdminLink($tabPrefix|cat:$sub_menu.class) nofilter}{if !empty($sub_menu.tab)}&tab={$sub_menu.tab|escape:'html':'UTF-8'}{/if}">{$sub_menu.label|escape:'quotes':'UTF-8'}</a>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </li>
            {/foreach}
            <li class="form-menu-item more_menu">
                <span class="more_three_dots"></span>
            </li>
        </ul>
    </div>
{/if}

{if $menu_exist && $moduleIsEnabled}
    <div class="ets-pc-breadcrumb">
        <a class="home" href="#" title="{l s='Home' mod='ets_reviews'}">
            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1472 992v480q0 26-19 45t-45 19h-384v-384h-256v384h-384q-26 0-45-19t-19-45v-480q0-1 .5-3t.5-3l575-474 575 474q1 2 1 6zm223-69l-62 74q-8 9-21 11h-3q-13 0-21-7l-692-577-692 577q-12 8-24 7-13-2-21-11l-62-74q-8-10-7-23.5t11-21.5l719-599q32-26 76-26t76 26l244 204v-195q0-14 9-23t23-9h192q14 0 23 9t9 23v408l219 182q10 8 11 21.5t-7 23.5z"/></svg>
        </a>
        {assign var="flag" value=0}
        {foreach from=$menus key='id' item='menu'}
            {if $tabPrefix|cat:$menu.class == $currentTab && ($menu.class|lower !== 'staffs' || (!isset($menu.sub) || $menu.sub|count < 1))}
                <span class="navigation-pipe">&gt;</span>
                <span class="navigation_page">{$menu.label|escape:'quotes':'UTF-8'}</span>
                {break}
            {/if}
            {if isset($menu.sub) && $menu.sub|count > 0}
                {foreach from=$menu.sub item='sub_menu'}
                    {if $tabPrefix|cat:$sub_menu.class == $currentTab && (!isset($smarty.get.tab) || empty($sub_menu.tab) || $smarty.get.tab|trim==$sub_menu.tab|trim)}
                        <span class="navigation-pipe">&gt;</span>
                        <a class="navigation-link {$menu.class|lower|escape:'html':'UTF-8'}" href="{$link->getAdminLink($tabPrefix|cat:$menu.class) nofilter}{if !empty($sub_menu.tab)}&tab={$sub_menu.tab nofilter}{/if}">{$menu.label|escape:'quotes':'UTF-8'}</a>
                        <span class="navigation-pipe">&gt;</span>
                        <span class="navigation_page">{$sub_menu.label|escape:'quotes':'UTF-8'}</span>
                        {assign var="flag" value=1}
                        {break}
                    {/if}
                {/foreach}
            {/if}
            {if $flag}{break}{/if}
        {/foreach}
    </div>
{/if}