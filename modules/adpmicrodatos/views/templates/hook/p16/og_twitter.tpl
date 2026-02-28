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

{if !empty($meta_title)}
	<meta name="twitter:title" content="{$meta_title|escape:'htmlall':'UTF-8'}">
{/if}

{if !empty($meta_description)}
	<meta name="twitter:description" content="{$meta_description|strip_tags:false|escape:'htmlall':'UTF-8'}">
{/if}

{if $page_name == 'product'}

	{if !empty($imagen_portada)}
		<meta property="twitter:image" content="{$imagen_portada}"/>
		<meta property="twitter:image:alt" content="{$meta_title|escape:'htmlall':'UTF-8'}"/>
	{/if}


{else if $page_name == 'index' || $page_name == 'category' ||  $page_name == 'contact' ||
	 $page_name == 'manufacturer' || $page_name == 'cms'}

	 {if $page_name == 'index' || $page_name == 'cms' || $page_name == 'contact'}

		{if !empty($adp_url_img_home_og)}
			<meta property="twitter:image" content="{$adp_url_img_home_og}"/>
		{else}
			<meta property="twitter:image" content="{$logo_url}"/>
		{/if}

	{else if $page_name == 'category'}
		
		{if !empty($adp_url_img_category_og)}
			<meta property="twitter:image" content="{$adp_url_img_category_og}"/>
		{else}
			<meta property="twitter:image" content="{$img_category}"/>
		{/if}

	{else if $page_name == 'manufacturer' && !empty({$id_fabricante})}		
		
		{if !empty($adp_url_img_manufacturer_og)}
			<meta property="twitter:image" content="{$adp_url_img_manufacturer_og}"/>
		{else}
			<meta property="twitter:image" content="{$img_manu_dir}{$id_fabricante}.jpg"/>
		{/if}

	{/if}

	<meta property="twitter:image:alt" content="{$meta_title|escape:'htmlall':'UTF-8'}"/>

{/if}

<meta name="twitter:site" content="{$shop_name|escape:'htmlall':'UTF-8'}">

<meta name="twitter:creator" content="{$shop_name|escape:'htmlall':'UTF-8'}">

<meta name="twitter:domain" content="{if !empty($force_ssl)}{$base_dir_ssl|escape:'html':'UTF-8'}{trim($smarty.server.REQUEST_URI,'/')|escape:'html':'UTF-8'}{else}{$base_dir|escape:'html':'UTF-8'}{trim($smarty.server.REQUEST_URI,'/')|escape:'html':'UTF-8'}{/if}">
