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

{literal}
<script type="application/ld+json" id="adpmicrodatos-organization-ps17v{/literal}{$module_version}{literal}">
{
    "name" : {/literal}{$shop.name|@json_encode nofilter}{literal},
    "url" : "{/literal}{$urls.base_url}{literal}",
    "logo" : [
    	{

    	"@type" : "ImageObject",
    	"url" : "{/literal}{if (!$is_p177)}{$urls.shop_domain_url}{/if}{$shop.logo}{literal}"
    	}
    ],
    "email" : "{/literal}{$email_comercio}{literal}",
	{/literal}{if $page.page_name =='index'}{literal}
		"description": {/literal}{$page.meta.description|@json_encode nofilter}{literal},
	{/literal}{/if}{literal}
	{/literal}{if $addressLocality}{literal}
	"address": {
	    "@type": "PostalAddress",
	    "addressLocality": {/literal}{$addressLocality|@json_encode nofilter}{literal},
	    "postalCode": "{/literal}{$postalCode}{literal}",
	    "streetAddress": {/literal}{$streetAddress|@json_encode nofilter}{literal},
	    "addressRegion": {/literal}{$region|@json_encode nofilter}{literal},
	    "addressCountry": {/literal}{$country|@json_encode nofilter}{literal}
	},
	{/literal}{/if}{literal}
	{/literal}{if $telefono_comercio}{literal}
	"contactPoint" : [
		{
			"@type" : "ContactPoint",
	    	"telephone" : "{/literal}{$telefono_comercio}{literal}",
	    	"contactType" : "customer service",
			"contactOption": "TollFree",
	    	"availableLanguage": [ 
	    		{/literal}{foreach key=key from=$nombre_idiomas_disponibles item=nombre_idioma_disponible}{literal}
	    			{/literal}{if $key > 0}{literal}
	    				,"{/literal}{$nombre_idioma_disponible}{literal}"
	    			{/literal}{else}{literal}
	    				"{/literal}{$nombre_idioma_disponible}{literal}"
	    			{/literal}{/if}{literal}
	    		{/literal}{/foreach}{literal}
	    	]
	    } 
	],
	{/literal}{/if}
	{if !empty($shopreviews) && $active_microdata_rich_snippets == '1'}
	{include file="./json_rich_snippets_product.tpl" reviews=$shopreviews}
	{/if}
	{literal}
	"@context": "http://schema.org",
	"@type" : "Organization"
}
</script>
{/literal}