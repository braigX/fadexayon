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
{if $page_name == 'index'}
{foreach from=$tiendas item=tienda}
{literal}
<script type="application/ld+json" id="adpmicrodatos-store-ps17v{/literal}{$module_version}{literal}">
{
    "@context": "http://schema.org",
    "@type": "{/literal}{$tipo_tienda}{literal}",
    "name":{/literal}{$tienda.name|@json_encode nofilter}{literal},
    "url": "{/literal}{$link->getPageLink('index', true)|escape:'html':'UTF-8'}{literal}",
    "address": {
            "@type": "PostalAddress",
            "addressLocality": {/literal}{$tienda.city|@json_encode nofilter}{literal},
            "postalCode": "{/literal}{$tienda.postcode}{literal}",
            "streetAddress": {/literal}{$tienda.streetAddress|@json_encode nofilter}{literal},
            "addressRegion": {/literal}{if !empty($tienda.region)}{$tienda.region|@json_encode nofilter}{else}""{/if}{literal},
            "addressCountry": {/literal}{if !empty($tienda.country)}{$tienda.country|@json_encode nofilter}{else}""{/if}{literal}
        },
    {/literal}{if !empty($tienda.imagen)}{literal}
    "image": {
            "@type": "ImageObject",
            "url":  "{/literal}{$tienda.imagen}{literal}"
    },
    {/literal}{/if}{literal}
    {/literal}{if !empty($tienda.latitude) || !empty($tienda.longitude)}{literal}
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{/literal}{$tienda.latitude}{literal}",
        "longitude": "{/literal}{$tienda.longitude}{literal}"
    },
    {/literal}{/if}{literal}
    "priceRange": "{/literal}{$tienda.min_price} - {$tienda.max_price}{literal}",
    {/literal}{if !empty($tienda.hours.0.opens) || !empty($tienda.hours.1.opens) || !empty($tienda.hours.2.opens) || !empty($tienda.hours.3.opens) || !empty($tienda.hours.4.opens) || !empty($tienda.hours.5.opens) || !empty($tienda.hours.6.opens)}{literal}
    "openingHoursSpecification": [
        {/literal}
            {foreach from=$tienda.hours item=hours name=hoursIterator}
                {if !empty($hours.opens)}
                    {literal}{ "@type": "OpeningHoursSpecification","dayOfWeek": "http://schema.org/{/literal}{$hours.day}{literal}","opens": "{/literal}{$hours.opens}{literal}","closes": "{/literal}{$hours.closes}{literal}" }{/literal}{if !$smarty.foreach.hoursIterator.last},{/if}
                {/if}
            {/foreach}
        {literal}
    ],
    {/literal}{/if}{literal}
    "telephone": "{/literal}{$tienda.phone}{literal}"
}
</script>
{/literal}
{/foreach}
{/if}