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
{if $display_slidebar}
    <div class="ybc_blog_sidebar {if !$blog_config.YBC_BLOG_SIDEBAR_ENABLED_ON_MOBILE} hide_mobile{/if}">
        {if !$blog_config.YBC_BLOG_SIDEBAR_ON_MOBILE }
            <div class="ybc-navigation-blog">{if $blog_config.YBC_BLOG_NAVIGATION_TITLE}{$blog_config.YBC_BLOG_NAVIGATION_TITLE|escape:'html':'UTF-8'}{else}{l s='Blog navigation' mod='ybc_blog'}{/if}</div>
            <div class="ybc-navigation-blog-content">
        {/if}
        {foreach from=$sidebars_postion item='position'}
            {$sidebars.$position nofilter}
        {/foreach}
        {if !$blog_config.YBC_BLOG_SIDEBAR_ON_MOBILE }
        </div>
        {/if}
    </div>
{/if}