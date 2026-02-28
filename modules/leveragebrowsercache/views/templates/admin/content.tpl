{*
* 2007-2017 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic" rel="stylesheet" type="text/css"/>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../modules/leveragebrowsercache/views/css/{$css_file|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css"/>
<link href="../modules/leveragebrowsercache/views/css/common.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
	var rc_module_path = "../modules/leveragebrowsercache/ajax.php";
	var rc_token = "{$rc_token|escape:'htmlall':'UTF-8'}";
	var rc_id_shop = {$rc_id_shop|escape:'htmlall':'UTF-8'};
</script>
<script src="../modules/leveragebrowsercache/views/js/globals.js" type="text/javascript"></script>
<div>
	

{if $css_file == 'global_15.css'}
<div class="bootstrap">
{/if}

{if $rc_confirmation}
	<div class="alert alert-success">
		{$rc_confirmation|escape:'htmlall':'UTF-8'}
	</div>	
{/if}
{include file="./dashboard_menu.tpl"}
<div class="tab-content db col-lg-10">
	{include file="./documentation.tpl"}
	{include file="./regenerate.tpl"}
	{include file="./cron.tpl"}
	<div class="clear" style="clear:both"></div>
</div>
<div class="clear" style="clear:both"></div>
{if $css_file == 'global_15.css'}
</div>
{/if}
	<div class="clear" style="clear:both"></div>
</div>