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

{if $active_pixel_facebook == '1' && !empty($adp_facebook_admin_id)}
	
	{include file="./og_pixel.tpl"}

{/if}

{if $active_open_graph_social_network == '1'}
	<meta data-module="adp-microdatos-opengraph-begin_p17v{$module_version}" property="microdatos" content="microdatos">
	{include file="./og_facebook.tpl"}
	{include file="./og_twitter.tpl"}	
	<meta data-module="adp-microdatos-opengraph-end_p17v{$module_version}" property="microdatos" content="microdatos">
{/if}
