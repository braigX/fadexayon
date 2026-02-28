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
<script type="application/ld+json" id="adpmicrodatos-localbusine-ps16v{/literal}{$module_version}{literal}">
{
	"name":{/literal}{$shop_name|@json_encode nofilter}{literal},
    "url": "{/literal}{$ruta}{literal}",
    "email" : {/literal}{$email_comercio|@json_encode nofilter}{literal},
    "address": {
	    "@type": "PostalAddress",
	    "addressLocality": {/literal}{$addressLocality|@json_encode nofilter}{literal},
	    "postalCode": {/literal}{$postalCode|@json_encode nofilter}{literal},
	    "streetAddress": {/literal}{$streetAddress|@json_encode nofilter}{literal},
	    "addressRegion": {/literal}{$region|@json_encode nofilter}{literal},
	    "addressCountry": {/literal}{$country|@json_encode nofilter}{literal}
	},
    {/literal}{if !empty($logo_url)}{literal}
    "image": {
            "@type": "ImageObject",
            "url":  "{/literal}{$logo_url}{literal}"
    },
    {/literal}{/if}{literal}
    {/literal}{if !empty($latitude) || !empty($longitude)}{literal}
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{/literal}{$latitude|escape:'htmlall':'UTF-8'}{literal}",
        "longitude": "{/literal}{$longitude|escape:'htmlall':'UTF-8'}{literal}"
    },
    {/literal}{/if}{literal}
    "priceRange": "{/literal}{$min_price|escape:'htmlall':'UTF-8'} - {$max_price|escape:'htmlall':'UTF-8'}{literal}",
    "telephone": "{/literal}{$telefono_comercio|escape:'htmlall':'UTF-8'}{literal}",
    {/literal}
    {if !empty($shopreviews) && $active_microdata_rich_snippets == '1'}
    {include file="./json_rich_snippets_product.tpl" reviews=$shopreviews}
    {/if}
    {literal}
    "@type": "LocalBusiness",
    "@context":	"http://schema.org"
}
</script>
{/literal}