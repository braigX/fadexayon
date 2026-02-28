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

<div class="ets_seo_menu" >
    <ul class="nav navbar-nav">
        {foreach $menus as $menu}
            {assign 'menu_has_sub' 0}
            <li class="{if $current_controller == $menu.controller || $menu.controller == $parent_controller}active {/if}
                {if $menu.controller == 'AdminEtsSeoSearchAppearanceContentType'} hide-on-md{/if}
                {if $current_controller == 'AdminEtsSeoSearchAppearanceContentType' && $menu.controller == 'AdminEtsSeoSettings'} active-only-md{/if}
                {if isset($menu.has_sub) && $menu.has_sub}
            {foreach $submenus as $sub}
                        {if $sub.parent_controller == $menu.controller}
                        seo_menu_has_sub
                        {assign 'menu_has_sub' 1}
                        {break}
                        {/if}
            {/foreach}{/if}">
                <a href="{$menu.link|escape:'html':'UTF-8'}" class="{if isset($menu.menu_icon)}{$menu.menu_icon|escape:'html':'UTF-8'}{/if}">
                    <i class="icon"></i> {$menu.title|escape:'html':'UTF-8'}
                </a>
                {if isset($menu_has_sub) && $menu_has_sub}
                    <ul class="nav submenu">
                    {foreach $submenus as $sub}
                        {if $sub.parent_controller == $menu.controller}
                        <li>
                            <a href="{$sub.link|escape:'html':'UTF-8'}" class="{if isset($sub.menu_icon)}{$sub.menu_icon|escape:'html':'UTF-8'}{/if}">
                                <i class="icon"></i> {$sub.title|escape:'html':'UTF-8'}
                            </a>
                        </li>
                        {/if}
                    {/foreach}
                        {if $menu.controller == 'AdminEtsSeoSettings'}
                            <li class="show-on-md">
                                <a href="{$menus['AdminEtsSeoSearchAppearanceContentType'].link|escape:'html':'UTF-8'}" class="{if isset($menus['AdminEtsSeoSearchAppearanceContentType'].menu_icon)}{$menus['AdminEtsSeoSearchAppearanceContentType'].menu_icon|escape:'html':'UTF-8'}{/if}">
                                    <i class="icon"></i> {$menus['AdminEtsSeoSearchAppearanceContentType'].title|escape:'html':'UTF-8'}
                                </a>
                            </li>
                        {/if}
                    </ul>
                {/if}
            </li>
        {/foreach}
        <li class="more_tab">
            <span class="more_three_dots"></span>
        </li>
    </ul>
</div>


