{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
{if $pswp_rss_link}
    <link rel="alternate" type="application/rss+xml" title="{$pswp_rss_title|escape:'html':'UTF-8'}" href="{$pswp_rss_link|escape:'html':'UTF-8'}" />
{/if}
<style type="text/css">
    {if $custom_css}
        {$custom_css nofilter}
    {/if}
</style>
<script>
    var pswp_theme = "{$pswp_theme|escape:'html':'UTF-8'}";
    var pswp_token = "{$pswp_token|escape:'html':'UTF-8'}";
    var pswp_ajax_url = "{$pswp_ajax_url|escape:'html':'UTF-8'}";
</script>