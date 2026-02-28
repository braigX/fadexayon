{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2020 Presta.Site
* @license   LICENSE.txt
*}
<div id="pswp-the-page-info-wrp" class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {l s='Current URL of the articles page:' mod='prestawp'}
    <a target="_blank" href="{$pswp_page_link|escape:'html':'UTF-8'}">{$pswp_page_link|escape:'html':'UTF-8'}</a>
    <hr>
        <p>
            {l s='RSS feed:' mod='prestawp'}
            {foreach from=$pswp_rss_urls key='rss_iso' item='rss_url' name="pswp_rss_urls"}
                <a target="_blank" href="{$rss_url|escape:'html':'UTF-8'}">{$rss_iso|escape:'html':'UTF-8'}</a>{if !$smarty.foreach.pswp_rss_urls.last},{/if}
            {/foreach}
        </p>
        <hr>
        <p>
            {l s='XML sitemap:' mod='prestawp'} <a target="_blank" href="{$pswp_sitemap_url|escape:'html':'UTF-8'}">{l s='link' mod='prestawp'}</a>
            <span class="label-tooltip pswp-tooltip-qm" data-toggle="tooltip" data-html="true"
                  title="{l s='This is a separate sitemap containing the list of all pages of this module. You can submit it to Google in addition to your main sitemap.' mod='prestawp'} {l s='If you use the standard "Google Sitemap" module, this is not necessary. All the pages are already included in the main sitemap.' mod='prestawp'}"
                  data-original-title="{l s='This is a separate sitemap containing the list of all pages of this module. You can submit it to Google in addition to your main sitemap.' mod='prestawp'} {l s='If you use the standard "Google Sitemap" module, this is not necessary. All the pages are already included in the main sitemap.' mod='prestawp'}"
            >?</span>
        </p>
</div>