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
<div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper ybc-blog-wrapper-gallery{if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD} ybc-blog-wrapper-blog-list loadmore{/if} ">
    <h1 class="page-heading">{l s='Photo gallery' mod='ybc_blog'}</h1>
    {if isset($blog_galleries)}
        <ul class="ybc-gallery ybc-galley-list">
            {if isset($is17) && $is17}
                {include file='module:ybc_blog/views/templates/hook/more_gallery_list.tpl' blog_galleries=$blog_galleries}
            {else}
                {include file='./more_gallery_list.tpl' blog_galleries=$blog_galleries}
            {/if}
        </ul>
        {if $blog_paggination}
            <div class="blog-paggination">
                {$blog_paggination nofilter}
            </div>
        {/if}
        {if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD}
            <div class="ets_blog_loading autoload">
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
        <p class="alert alert-warning">{l s='No item found' mod='ybc_blog'}</p>
    {/if}
</div>
