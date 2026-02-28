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


<!-- TwitterCard -->
<meta name="twitter:card" content="summary_large_image">

{if !empty($page.meta.title)}
	<meta name="twitter:title" content="{$page.meta.title|escape:'htmlall':'UTF-8'}">
{/if}

{if !empty($page.meta.description)}
	<meta name="twitter:description" content="{$page.meta.description|strip_tags:false|escape:'htmlall':'UTF-8'}">
{/if}

{if $page.page_name == 'product'}

	{if (!empty($imagen_portada))}
		<meta property="twitter:image" content="{$imagen_portada}">
		<meta property="twitter:image:alt" content="{$page.meta.title|escape:'htmlall':'UTF-8'}">
	{/if}


{else if $page.page_name == 'index' || $page.page_name == 'category' ||  $page.page_name == 'contact' ||
	 $page.page_name == 'manufacturer' || $page.page_name == 'cms'}

	 {if $page.page_name == 'index' || $page.page_name == 'contact' || $page.page_name == 'cms'}

		{if !empty($adp_url_img_home_og)}
			<meta property="twitter:image" content="{$adp_url_img_home_og}">
		{else}
			{if ($is_p177)}
				<meta property="twitter:image" content="{$shop.logo}">
			{else}
				<meta property="twitter:image" content="{$urls.shop_domain_url}{$shop.logo}">
			{/if}
		{/if}

	{else if $page.page_name == 'category'}
		
		{if !empty($adp_url_img_category_og)}
			<meta property="twitter:image" content="{$adp_url_img_category_og}">
		{else}
			<meta property="twitter:image" content="{$img_category}">
		{/if}

	{else if $page.page_name == 'manufacturer' && !empty({$id_fabricante})}		
		
		{if !empty($adp_url_img_manufacturer_og)}
			<meta property="twitter:image" content="{$adp_url_img_manufacturer_og}">
		{else}
			<meta property="twitter:image" content="{$link->getManufacturerImageLink({$id_fabricante}, 'large_default')}">
		{/if}

	{/if}

	<meta property="twitter:image:alt" content="{$page.meta.title|escape:'htmlall':'UTF-8'}">

{/if}

<meta name="twitter:site" content="{$shop.name|escape:'htmlall':'UTF-8'}">

<meta name="twitter:creator" content="{$shop.name|escape:'htmlall':'UTF-8'}">

<meta name="twitter:domain" content="{$urls.current_url}">
