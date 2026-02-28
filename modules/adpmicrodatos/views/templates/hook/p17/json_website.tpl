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

{assign var="searchUrl" value=$urls.pages.search|regex_replace:"/\?controller=search+$/":""}

{literal}
<script type="application/ld+json" id="adpmicrodatos-website-ps17v{/literal}{$module_version}{literal}">
{
	"@context":	"http://schema.org",
	"@type": "WebSite",
	"url": "{/literal}{$urls.base_url}{literal}",
	"name": {/literal}{$shop.name|@json_encode nofilter}{literal},
	"alternateName": {/literal}{$page.meta.title|@json_encode nofilter}{literal},
	"image": [{
		"@type": "ImageObject",
		"url":  "{/literal}{if (!$is_p177)}{$urls.shop_domain_url}{/if}{$shop.logo}{literal}"
	}],
	"inLanguage": "{/literal}{$iso_code}{literal}",
	"potentialAction": {
		"@type": "SearchAction",
		"target": "{/literal}{$searchUrl}{literal}?controller=search&s={s}",
		"query-input": "required name=s"
	}
}
{/literal}
</script>