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

<script type="application/ld+json" id="adpmicrodatos-product-ps17v{$module_version|escape:'htmlall':'UTF-8'}">
{
{if !empty($nombre)}
    "name": {$nombre|@json_encode nofilter},
{/if}
{if !empty($url)}
    "url": {$url|@json_encode nofilter},
{/if}
{if !empty($inProductGroupWithID)}
    "productGroupID": {$inProductGroupWithID|@json_encode nofilter},
{/if}
{if !empty($set_configuration_product_gtin)}
    {if $set_configuration_product_gtin == 1}
        "gtin13": {$ean13|@json_encode nofilter},
    {else if $set_configuration_product_gtin == 2}
        "gtin13": {$upc|@json_encode nofilter},
    {else}
        "gtin13": {$isbn|@json_encode nofilter},
    {/if}
{/if}
{if !empty($category)}
    "category": {$category|@json_encode nofilter},
{/if}
{if !empty($sku)}
    "sku": {$sku|@json_encode nofilter},
{/if}
{if !empty($mpn)}
    "mpn": {$mpn|@json_encode nofilter},
{/if}
{if !empty($adp_default_manufacturer)}
    "brand": {
        "@type": "Brand",
        "name": {$adp_default_manufacturer|@json_encode nofilter}
    },
{else if !empty($fabricante)}
    "brand": {
        "@type": "Brand",
        "name": {$fabricante|@json_encode nofilter}
    },
{/if}
{if !empty($active_microdata_product_weight)}
    "weight": {
        "@type": "QuantitativeValue",
        "value": {$weight|@json_encode nofilter},
        "unitcode": "kg"
    },
{/if}
{if !empty($description)}
    "description": {$description|strip_tags:false|@json_encode nofilter},
{/if}
{if !empty($caracteristica_3d_model)}
    "subjectOf": {
        "@type": "3DModel",
        "encoding": {
            "@type": "MediaObject",
            "contentUrl": {$caracteristica_3d_model|@json_encode nofilter}
        }
    },
{/if}
{include file="./json_product_combination_data.tpl"}
{if !empty($caracteristicas_producto) && $active_microdata_features_product == '1'}
{include file="./json_product_features.tpl"}
{/if}
{if !empty($productos_relacionados)}
{include file="./json_product_related.tpl"}
{/if}
{if !empty($reviews) && $active_microdata_rich_snippets == '1'}
{include file="./json_rich_snippets_product.tpl" reviews=$reviews}
{/if}

    "@context": "https://schema.org/",
    "@type": "ProductGroup"
}
</script>
