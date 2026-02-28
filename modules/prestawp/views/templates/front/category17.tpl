{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

{extends file='page.tpl'}

{block name='head_seo_title'}{$meta_title|escape:'html':'UTF-8'}{/block}
{block name='head_seo_description'}{$meta_description|escape:'html':'UTF-8'}{/block}
{block name='head_seo_keywords'}{$meta_keywords|escape:'html':'UTF-8'}{/block}

{block name='page_content'}
    <div id="pswp-page" class="rte pswp{$psvd|escape:'html':'UTF-8'}">
        {capture name=path}{if $pswp_page_title}{$pswp_page_title|escape:'html':'UTF-8'}{else}{l s='Posts' mod='prestawp'}{/if}{/capture}
        <h1 class="page-heading">
            {$pswp_page_title|escape:'html':'UTF-8'}
        </h1>
        {if $pswp_top_text && $pswp_page == 1}
            <div class="pswp-top-text">
                {$pswp_top_text nofilter}  {* HTML *}
            </div>
        {/if}

        <div id="pswp_posts">
            {include 'module:prestawp/views/templates/front/_posts.tpl'}
        </div>
    </div>
{/block}