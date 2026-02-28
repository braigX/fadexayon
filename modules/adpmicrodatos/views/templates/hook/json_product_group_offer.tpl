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
     "offers": {
{if $desactive_microdata_product_stock == '0'}
  {if $combination.quantity > 0}
      "availability": "https://schema.org/InStock",
  {else if $permitir_pedidos_fuera_stock}
      "availability": "https://schema.org/BackOrder",
  {else}
      "availability": "https://schema.org/OutOfStock",
  {/if}
{/if}
{if $desactive_microdata_product_price == '0'}
  {if !empty($combination.productPrice)}
          "price": {$combination.productPrice|string_format:'%.2f'|replace:',':'.'|@json_encode nofilter},
  {else}
          "price": "0",
  {/if}
{/if}
{if !empty($combination.moneda)}
        "priceCurrency": {$combination.moneda|@json_encode nofilter},
{/if}
{if !empty($combination.fechaValidaHasta)}
        "priceValidUntil": {$combination.fechaValidaHasta|@json_encode nofilter},
{/if}
{if !empty($combination.url)}
        "url": {$combination.url|@json_encode nofilter},
{/if}
{if !empty($combination.condition)}
  {if $combination.condition == 'new'}
      "itemCondition": "https://schema.org/NewCondition",
  {elseif $combination.condition == 'used'}
      "itemCondition": "https://schema.org/UsedCondition",
  {elseif $combination.condition == 'refurbished'}
      "itemCondition": "https://schema.org/RefurbishedCondition",
  {/if}
{/if}
{if $active_microdata_organization == '1'}
      "seller":{
          "name": {$shop.name|@json_encode nofilter},
          "@type": "Organization"
      },
{/if}
{include file="./json_product_refund_policy.tpl"}
{if $active_microdata_shipping_details == '1'}
{include file="./json_product_shipping_details.tpl"}
{/if}
      "@type": "Offer"
},