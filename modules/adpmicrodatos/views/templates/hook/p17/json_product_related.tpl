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
    "isRelatedTo": [
{foreach from=$productos_relacionados item=producto_relacionado name=productosrelacionados}
        {
{if !empty($producto_relacionado.id_product)}
            "productID": "{$producto_relacionado.id_product}",
{/if}
{if !empty($producto_relacionado.nombre)}
            "name": {$producto_relacionado.nombre|@json_encode nofilter},
{/if}
{if !empty($producto_relacionado.imagenes)}
{foreach from=$producto_relacionado.imagenes item=imagen}
            "image": "{$imagen}",
{/foreach}
{/if}
{if !empty($producto_relacionado.url)}
            "url": "{$producto_relacionado.url}",
{/if}
{if !empty($producto_relacionado.ean13)}
            "gtin13": {$producto_relacionado.ean13|@json_encode nofilter},
{else if !empty($producto_relacionado.upc)}
            "gtin13": {"0{$producto_relacionado.upc}"|@json_encode nofilter},
{/if}
{if !empty($producto_relacionado.category)}
            "category": {$producto_relacionado.category|@json_encode nofilter},
{/if}
{if !empty($producto_relacionado.sku)}
            "sku": {$producto_relacionado.sku|@json_encode nofilter},
{/if}
{if !empty($producto_relacionado.mpn)}
            "mpn": {$producto_relacionado.mpn|@json_encode nofilter},
{/if}
{if !empty($adp_default_manufacturer)}
            "brand": {
                "name": {$adp_default_manufacturer|@json_encode nofilter},
                "@type": "Brand"
            },
{else if !empty($producto_relacionado.fabricante)}
            "brand": {
                "name": {$producto_relacionado.fabricante|@json_encode nofilter},
                "@type": "Brand"
            },
{/if}
{if !empty($producto_relacionado.description)}
            "description": {$producto_relacionado.description|strip_tags:false|@json_encode nofilter},
{/if}
{include file="./json_product_offer.tpl" quantity=$producto_relacionado.quantity productPrice=$producto_relacionado.productPrice moneda=$producto_relacionado.moneda url=$producto_relacionado.url fechaValidaHasta=$producto_relacionado.fechaValidaHasta condition=$producto_relacionado.condition }
{if !empty($producto_relacionado.valoraciones) && $active_microdata_rich_snippets == '1'}
{include file="./json_rich_snippets_product.tpl" reviews=$producto_relacionado.valoraciones}
{/if}
            "@type": "Product"
        }{if !$smarty.foreach.productosrelacionados.last},{/if} 
{/foreach}
    ],