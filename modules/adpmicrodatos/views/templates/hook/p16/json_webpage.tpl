{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{literal}
<script type="application/ld+json" id="adpmicrodatos-webpage-ps16v{/literal}{$module_version}{literal}">
{
    "@context": "http://schema.org",
    "@type" : "WebPage",
    "isPartOf": [{
        "@type":"WebSite",
        "url":  "{/literal}{$link->getPageLink('index', true)}{literal}",
        "name": {/literal}{$shop_name|@json_encode nofilter}{literal}
    }],
    "name": {/literal}{$meta_title|@json_encode nofilter}{literal},
    "url": "{/literal}{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{trim($smarty.server.REQUEST_URI,'/')}{else}{$base_dir}{trim($smarty.server.REQUEST_URI,'/')}{/if}{literal}"
}
</script>
{/literal}