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

<script type="application/ld+json" id="adpmicrodatos-product-ps17v{$module_version}">
{
{if !empty($nombre)}
    "name": {$nombre|@json_encode nofilter},
{/if}
{if !empty($imagenes)}
    "image": {$imagenes|@json_encode nofilter},
{/if}
{if !empty($url)}
    "url": "{$url}",
{/if}
{if !empty($id_product)}
    "productID": "{$id_product}",
{/if}
{if !empty($ean13)}
    "gtin13": {$ean13|@json_encode nofilter},
{else if !empty($upc)}
    "gtin13": {"0{$upc}"|@json_encode nofilter},
{/if}
{if !empty($category)}
    "category": "{$category}",
{/if}
{if !empty($sku)}
    "sku": "{$sku}",
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
{if !empty($caracteristicas_producto) && $active_microdata_features_product == '1'}
{include file="./json_product_features.tpl"}
{/if}
{if !empty($ofertas_producto) && $active_microdata_combinations_product == '1'}
{include file="./json_product_aggregateoffer.tpl" ofertas_producto=$ofertas_producto}
{else}
{include file="./json_product_offer.tpl"}
{/if}
{if !empty($productos_relacionados)}
{include file="./json_product_related.tpl"}
{/if}
{if $active_microdata_refund_policy == '1'}
{include file="./json_product_refund_policy.tpl"}
{/if}
{if !empty($reviews) && $active_microdata_rich_snippets == '1'}
{include file="./json_rich_snippets_product.tpl" reviews=$reviews}
{/if}
    "@context": "https://schema.org/",
    "@type": "Product"
}
</script>
