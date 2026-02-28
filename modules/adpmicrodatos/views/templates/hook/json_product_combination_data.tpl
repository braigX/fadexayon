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

{if !empty($combinations)}
    "hasVariant" : [
{foreach from=$combinations item=combination name=combinationproduct}
{
{if !empty($combination.nombre)}
    "name": {$combination.nombre|@json_encode nofilter},
{/if}
{if !empty($combination.imagenes)}
    "image": {$combination.imagenes|@json_encode nofilter},
{/if}
{if !empty($combination.url)}
    "url": {$combination.url|@json_encode nofilter},
{/if}
{if !empty($combination.id_product)}
    "productID": {$combination.id_product|@json_encode nofilter},
{/if}
{if !empty($set_configuration_product_gtin)}
    {if $set_configuration_product_gtin == 1}
        "gtin13": {$combination.ean13|@json_encode nofilter},
    {else if $set_configuration_product_gtin == 2}
        "gtin13": {$combination.upc|@json_encode nofilter},
    {else}
        "gtin13": {$combination.isbn|@json_encode nofilter},
    {/if}
{/if}
{if !empty($combination.sku)}
    "sku": {$combination.sku|@json_encode nofilter},
{/if}
{if !empty($combination.mpn)}
    "mpn": {$combination.mpn|@json_encode nofilter},
{/if}
{if !empty($active_microdata_product_weight)}
    "weight": {
        "@type": "QuantitativeValue",
        "value": {$combination.weight|@json_encode nofilter},
        "unitcode": "kg"
    },
{/if}
{if !empty($combination.description)}
    "description": {$combination.description|strip_tags:false|@json_encode nofilter},
{/if}
{include file="./json_product_group_offer.tpl"}

    "@type": "Product"
    }{if !$smarty.foreach.combinationproduct.last},{/if} 
{/foreach}
],
{/if}
