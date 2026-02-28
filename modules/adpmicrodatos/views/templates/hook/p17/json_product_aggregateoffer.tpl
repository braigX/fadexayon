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
    "offers" : {
{if !empty($rango_precios)}
        "lowPrice": "{$rango_precios.minprice|string_format:'%.2f'|replace:',':'.'}",
        "highPrice": "{$rango_precios.maxprice|string_format:'%.2f'|replace:',':'.'}",
{/if}
        "offerCount": "{$ofertas_producto|count}",
        "priceCurrency": "{$moneda}",
        "offers": [
{foreach from=$ofertas_producto item=oferta name=ofertas}
            {

{if $oferta.quantity > 0}
    "availability": "https://schema.org/InStock",
{else if $permitir_pedidos_fuera_stock}
    "availability": "https://schema.org/BackOrder",
{else}
    "availability": "https://schema.org/OutOfStock",
{/if}
{if !empty($oferta.productPrice)}
                "price":"{$oferta.productPrice|string_format:'%.2f'|replace:',':'.'}",
{else}
                "price":"0",
{/if}
{if !empty($oferta.moneda)}
                "priceCurrency":"{$oferta.moneda}",
{/if}
{if !empty($oferta.nombre)}
                "name":{$oferta.nombre|@json_encode nofilter},
{/if}
{if !empty($oferta.sku)}
                "sku":{$oferta.sku|@json_encode nofilter},
{/if}
{if !empty($oferta.mpn)}
                "mpn": {$oferta.mpn|@json_encode nofilter},
{/if}
{if !empty($oferta.ean13)}
                "gtin13": {$oferta.ean13|@json_encode nofilter},
{else if !empty($oferta.upc)}
                "gtin13": {"0{$oferta.upc}"|@json_encode nofilter},
{/if}
{if !empty($oferta.url)}
                "url": "{$oferta.url}",
{/if}
{if !empty($oferta.condition)}
{if $oferta.condition == 'new'}
                "itemCondition":"https://schema.org/NewCondition",
{elseif $oferta.condition == 'used'}
                "itemCondition":"https://schema.org/UsedCondition",
{elseif $oferta.condition == 'refurbished'}
                "itemCondition":"https://schema.org/RefurbishedCondition",
{/if}
{/if}
{if $active_microdata_organization == '1'}
                "seller":{
                    "name":{$shop.name|@json_encode nofilter},
                    "@type":"Organization"
                },
{/if}
{if !empty($oferta.fechaValidaHasta)}
                "priceValidUntil":"{$oferta.fechaValidaHasta}",
{/if}
                "@type": "Offer"
            }{if !$smarty.foreach.ofertas.last},{/if} 
{/foreach} 
        ],
        "@type": "AggregateOffer"
    },