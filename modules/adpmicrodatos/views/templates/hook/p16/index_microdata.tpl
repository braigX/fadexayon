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

{if $active_microdata_organization == '1'}
<!-- Microdatos Organization -->
	{include file="./json_organization.tpl"}
{/if}

{if $active_microdata_localbusiness == '1'}
<!-- Microdatos LocalBusiness -->
	{include file="./json_localbusiness.tpl"}
{/if}

{if $active_microdata_webpage == '1'}
<!-- Microdatos Webpage -->
	{include file="./json_webpage.tpl"}
{/if}

{if $page_name =='index' && $active_microdata_website == '1'}
<!-- Microdatos Website -->
	{include file="./json_website.tpl"}
{/if}

{if $active_microdata_store == '1' && !empty($tiendas)}
<!-- Microdatos Store -->
	{include file="./json_store.tpl"}
{/if}

{if !empty($categorias) && $active_microdata_breadcrumbs == '1'}
<!-- Microdatos Breadcrumb -->
	{include file="./json_breadcrumblist.tpl"}
{/if}

{if !empty($listados_productos) && $active_microdata_list_product == '1'}
<!-- Microdatos ItemList -->
	{include file="./json_itemlist.tpl"}
{/if}

{if $active_microdata_page_product == '1'}
<!-- Microdatos Producto -->
	{include file="./json_product.tpl"}
{/if}

{if !empty($richsnippets_shop_ts) && $active_microdata_rich_snippets == '1'}
<!-- Microdatos Rich snippets Trusted Shop Tienda -->
	{include file="./json_rich_snippets_ts.tpl"}
{/if}
