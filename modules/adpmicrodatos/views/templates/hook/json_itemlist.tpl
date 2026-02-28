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
<script type="application/ld+json" id="adpmicrodatos-itemlist-ps17v{$module_version|escape:'htmlall':'UTF-8'}">
{
    "@context": "http://schema.org",
    "@type": "ItemList",
    "itemListElement": [
{foreach from=$listados_productos item=listado_producto name=listadoProductos}
        {
{if !empty($listado_producto.url)}
    "mainEntityOfPage": {$listado_producto.url|@json_encode nofilter},
    "url": {$listado_producto.url|@json_encode nofilter},
{/if}
{if !empty($listado_producto.nombre)}
    "name": {$listado_producto.nombre|@json_encode nofilter},
{/if}
{if !empty($listado_producto.imagenes)}
    "image": {$listado_producto.imagenes|@json_encode nofilter},
{/if}
            "position": {$smarty.foreach.listadoProductos.iteration},
            "@type": "ListItem"
        }{if !$smarty.foreach.listadoProductos.last},{/if} 
{/foreach}
    ]
}
</script>