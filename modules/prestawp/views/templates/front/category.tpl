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

<div id="pswp-page" class="pswp{$psvd|escape:'html':'UTF-8'}">
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
        {include './_posts.tpl'}
    </div>
</div>