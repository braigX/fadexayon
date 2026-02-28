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
<div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper ybc-blog-wrapper-blog-list{if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD} loadmore{/if}">
    {if $blog_categories}
        <h2 class="page-heading product-listing">{l s='All categories' mod='ybc_blog'}</h2>
        <ul class="ybc-blog-list row">
            {if isset($is17) && $is17}
                {include file='module:ybc_blog/views/templates/hook/more_categories_list.tpl' blog_categories=$blog_categories}
            {else}
                {include file='./more_categories_list.tpl' blog_categories=$blog_categories}
            {/if}
        </ul>
        {if $blog_paggination}
            <div class="blog-paggination">
                {$blog_paggination nofilter}
            </div>
        {/if}
        {if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD}
            <div class="ets_blog_loading">
                        <span id="squaresWaveG">
                            <span id="squaresWaveG_1" class="squaresWaveG"></span>
                            <span id="squaresWaveG_2" class="squaresWaveG"></span>
                            <span id="squaresWaveG_3" class="squaresWaveG"></span>
                            <span id="squaresWaveG_4" class="squaresWaveG"></span>
                            <span id="squaresWaveG_5" class="squaresWaveG"></span>
                        </span>
            </div>
            <div class="clearfix"></div>
        {/if}
    {else}
        <p>{l s='No category found' mod='ybc_blog'}</p>
    {/if}
</div>