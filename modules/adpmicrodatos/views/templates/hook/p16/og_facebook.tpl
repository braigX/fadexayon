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


<meta property="og:url" content="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'html':'UTF-8'}{trim($smarty.server.REQUEST_URI,'/')|escape:'html':'UTF-8'}{else}{$base_dir|escape:'html':'UTF-8'}{trim($smarty.server.REQUEST_URI,'/')|escape:'html':'UTF-8'}{/if}" />

{if !empty($adp_facebook_admin_id)}
	<meta property="fb:app_id" content="{$adp_facebook_admin_id|escape:'htmlall':'UTF-8'}" />
{/if}

{if !empty($meta_title)}
	<meta property="og:title" content="{$meta_title|escape:'htmlall':'UTF-8'}" />
{/if}

{if !empty($meta_description)}
	<meta property="og:description" content="{$meta_description|strip_tags:false|escape:'htmlall':'UTF-8'}" />
{/if}

{if !empty($shop_name)}
	<meta property="og:site_name" content="{$shop_name|escape:'htmlall':'UTF-8'}" />
{/if}

{if !empty($language_code)}
	<meta property="og:locale" content="{$language_code}" />
{/if}	

{foreach from=$others_language_code item=other_language_code}
	{if !empty($other_language_code) && ($other_language_code != $language_code)}
		<meta property="og:locale:alternate" content="{$other_language_code}" />
	{/if}
{/foreach}

{if $page_name == 'product'}
	<meta property="og:type" content="product" />

	{if !empty($id_product)}
		<meta property="product:retailer_item_id" content="{$id_product}"/>
	{/if}

	{if !empty($imagen_portada)}
		<meta property="og:image" content="{$imagen_portada}"/>
		<meta property="og:image:type" content="{$mime}"/>
		<meta property="og:image:width" content="{$ancho_imagen_portada}"/>
		<meta property="og:image:height" content="{$alto_imagen_portada}"/>
		<meta property="og:image:alt" content="{$meta_title|escape:'htmlall':'UTF-8'}"/>

	{/if}

	{if $quantity > 0}
		<meta property="product:availability" content="in stock"/>
	{else if !$permitir_pedidos_fuera_stock}
		<meta property="product:availability" content="out of stock"/>
	{else}
		<meta property="product:availability" content="available for order"/>
	{/if}	

	{if !empty($adp_default_manufacturer)}
      	<meta property="product:brand" content="{$adp_default_manufacturer}" />
	{else if !empty($fabricante)}
      	<meta property="product:brand" content="{$fabricante}" />
 	{/if}

 	{if !empty($condition)}
		<meta property="product:condition" content="{$condition}" />
	{/if}

	{if !empty($moneda)}
		{if !empty($pretax_price) && $pretax_price != 0}
			<meta property="product:pretax_price:amount" content="{$pretax_price}" />
			<meta property="product:pretax_price:currency" content="{$moneda}" />
		{/if}
		{if !empty($productPrice) && $productPrice != 0}
			<meta property="product:price:amount" content="{$productPrice|string_format:'%.2f'}" />
			<meta property="product:price:currency" content="{$moneda}" />
		{/if}
	{/if}

	{if !empty($weight) && ($weight != 0)}
	  	<meta property="product:weight:value" content="{$weight}"/>
	  	<meta property="product:weight:units" content="{$weight_unit}"/>
	{/if}

{else if $page_name == 'index' || $page_name == 'category' ||  $page_name == 'contact' ||
	 $page_name == 'manufacturer' || $page_name == 'cms'}
	
	<meta property="og:type" content="website" />
	{if $page_name == 'index' || $page_name == 'cms' || $page_name == 'contact'}
		{if !empty($adp_url_img_home_og)}
			<meta property="og:image" content="{$adp_url_img_home_og}"/>
		{else}
			<meta property="og:image" content="{$logo_url}"/>
			<meta property="og:image:width" content="{$logo_ancho}"/>
			<meta property="og:image:height" content="{$logo_alto}"/>
			<meta property="og:image:type" content="{$mime}"/>
		{/if}
	{else if $page_name == 'category'}
		{if !empty($adp_url_img_category_og)}
			<meta property="og:image" content="{$adp_url_img_category_og}"/>
		{else}
			<meta property="og:image" content="{$img_category}"/>
			<meta property="og:image:width" content="{$ancho_img_category}"/>
			<meta property="og:image:height" content="{$alto_img_category}"/>
			<meta property="og:image:type" content="{$mime}"/>
		{/if}
	{else if $page_name == 'manufacturer' && !empty({$id_fabricante})}		
		{if !empty($adp_url_img_manufacturer_og)}
			<meta property="og:image" content="{$adp_url_img_manufacturer_og}"/>
		{else}
			<meta property="og:image" content="{$img_manu_dir}{$id_fabricante}.jpg"/>
			<meta property="og:image:width" content="{$ancho_imagen_fabricante}"/>
			<meta property="og:image:height" content="{$alto_imagen_fabricante}"/>
			<meta property="og:image:type" content="{$mime}"/>
		{/if}
	{/if}
	<meta property="og:image:alt" content="{$meta_title|escape:'htmlall':'UTF-8'}"/>
{/if}
