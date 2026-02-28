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
<div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper ybc-blog-wrapper-blog-list{if isset($blog_config.YBC_BLOG_AUTO_LOAD) &&$blog_config.YBC_BLOG_AUTO_LOAD} loadmore{/if} ybc-page-auhors">
    {if $authors}
        <div class="page_blog block ybc_block_author {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'} page_author">
            <h4 class="title_blog title_block">{l s='Authors' mod='ybc_blog'}</h4>
            {assign var='product_row' value=$blog_config.YBC_BLOG_HOME_PER_ROW|intval}
            <ul class="block_content ybc-blog-list">
                {if isset($is17) && $is17}
                    {include file='module:ybc_blog/views/templates/hook/more_authors_list.tpl' authors=$authors}
                {else}
                    {include file='./more_authors_list.tpl' authors=$authors}
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
        </div>
    {/if}
</div>