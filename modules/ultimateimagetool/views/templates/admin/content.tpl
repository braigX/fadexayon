{*
* 2007-2025 PrestaShop
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
*  @copyright  2007-2025 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900,200italic,300italic,400italic,600italic,700italic,900italic" rel="stylesheet" type="text/css"/>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../modules/ultimateimagetool/views/css/{$css_file|escape:'htmlall':'UTF-8'}" rel="stylesheet" type="text/css"/>
<link href="../modules/ultimateimagetool/views/css/common.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
	var uit_module_path = "{$uit_ajax_url}";
	var uit_token = "{$uit_token|escape:'htmlall':'UTF-8'}";
	
	var uit_domain_url = "{$uit_domain_url}";
	var uit_root_dir = "{$uit_root_dir|escape:'htmlall':'UTF-8'}";
	
</script>
<script src="../modules/ultimateimagetool/views/js/globals2.js?v=2" type="text/javascript"></script>
<div>
	
{if $css_file == 'global_15.css'}
<div class="bootstrap">
{/if}

	<div class="alert alert-warning">
			{l s='If you find the module useful and have the time, please rate the module as it greatly helps us!' mod='ultimateimagetool'} <a class="button btn btn-warning" href="https://addons.prestashop.com/en/ratings.php" target="_blank">{l s='Rate module!' mod='ultimateimagetool'}</a><br/>
		</div>
	{if !$uit_htaccess}
		<div class="alert alert-danger">
			{l s='There was a problem installing the module. The .htaccess file was not updated. Go to the module page list -> find this module -> press the small arrow to see all options -> Press RESET' mod='ultimateimagetool'}<br/>
			{l s='This warning will disappear when the .htaccess file is correctly edited and module installed correctly.' mod='ultimateimagetool'}
			{l s='If it does not dissappear after you have RESET the module, that means PHP does not have permissions to edit the .htaccess file, you need to check permissions and owner of that file' mod='ultimateimagetool'}
		</div>
	{/if}

	{if $uit_disable_server_cache}
		<div class="alert alert-danger">
			{l s='Server cache is enabled and the module will not function correctly, please disable it by going to Performance (scroll to be bottom) -> set Use cache to NO' mod='ultimateimagetool'}<br/>
		</div>
	{/if}

	{if $display_ssl_error}
		<div class="alert alert-danger">
			{l s='Your shop has enabled SSL on its pages, but we did not detect that you are logged in with https:// in the backoffice, please make sure you login with https:// in the backoffice or else you may experience problems with running the functionalities of the module.' mod='ultimateimagetool'}<br/>
		</div>
	{/if}
	
	
{include file="./dashboard_menu.tpl"}
<div class="tab-content db col-lg-10">
	{if $uit_shop_enable == 0}
		<div class="alert alert-danger"><span class="alert_close"></span>
			{l s='Some functionalities do not work correctly when shop is disabled (maintenance mode). Please activate the shop so you can use all of the module functionalities.' mod='ultimateimagetool'}
		</div>
	{/if}

		

		<div id="regenerate" class=" tab-pane">
			<div class="alert alert-info">
				{l s='Select the image type you want to compress.' mod='ultimateimagetool'}
				<br/> 
				{l s='Just press the' mod='ultimateimagetool'} <button><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button> {l s='at the start of a line to start the image compression.' mod='ultimateimagetool'}
				<br/> 
				{l s='If for any reason you want to start rebulding your catalog from the start just press' mod='ultimateimagetool'} <button><span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span></button>
				<br/>
				{l s='You can select how many parallel images you can compress at the same time.' mod='ultimateimagetool'}
				<br/>
				{l s='In some cases it`s best not to run multiple image compressions in the same time because the hosting may think it`s being attack. So test to make sure everything is working properly.' mod='ultimateimagetool'}
				<br/>
				{l s='It`s very important to know that if you use this method and not cron job generation, this page must stay open for the process to run.'  mod='ultimateimagetool'}
				<br/>
				<strong>{l s='Also, please know that for the best compression, the module uses resmush external API, and the compression time for each image may depend on the load of the provider`s server.'  mod='ultimateimagetool'}</strong>
			</div>
			<div class="alert alert-warning">
				{l s='IMPORTANT !!!! Do not compress the ORIGINAL size, until you have optimized all the other images sizes. The compression process cannot be reverted, the only way to remake the picture in case of quality loss is to rebuild the image sizes.' mod='ultimateimagetool'}
				<p>{l s='Test image quality by going to the `Test image quality` tab.' mod='ultimateimagetool'}</p>
			</div>
			<div class="panel">
				<div class="panel-heading">{l s='Settings' mod='ultimateimagetool'}</div>
				    <div class="form-group">
                        <div class="control-label col-lg-5 col-md-4 col-xs-10">
                            <label class="labelbutton">{l s='Enable Gzip and Browser cache' mod='ultimateimagetool'}</label>
                        </div>
                        <div>
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_enable_gzip" rel="uit_enable_gzip" id="uit_enable_gzip_on"  value="1" {if $uit_enable_gzip == '1'}checked="checked"{/if}>
                                <label for="uit_enable_gzip_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_enable_gzip"  rel="uit_enable_gzip"  id="uit_enable_gzip_off" value="0"  {if $uit_enable_gzip == '0'}checked="checked"{/if}>
                                <label for="uit_enable_gzip_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
                    </div>
			</div>	
			
			<div class="alert alert-info">
				<p>{l s='Automatically compressed images (on upload with cron job) do not increase the progress counters below. The counters are only for manual compression, if you compress all the images and the counters shows 10/10 afterwards you add a new product, the counter will show 10/11 images done but the newly uploaded images were compressed.' mod='ultimateimagetool'}</p>
			</div>

			{include file="./compress-products.tpl"}
			{include file="./compress-categories.tpl"}
			{include file="./compress-manufacturers.tpl"}
			{include file="./compress-suppliers.tpl"}
			{include file="./compress-theme.tpl"}	
			{include file="./compress-modules.tpl"}	
			{include file="./compress-custom.tpl"}	
		</div>

		<div id="webp" class=" tab-pane">

			<div class="alert alert-info"><span class="alert_close"></span>
				{l s='Select the image type you want to convert to WebP.' mod='ultimateimagetool'}
				<br/> 
				{l s='Just press the' mod='ultimateimagetool'} <button><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button> {l s='at the start of a line to start the image conversion from jpeg to WebP.' mod='ultimateimagetool'}
				<br/> 
				{l s='You can select how many parallel images you can convert at the same time.' mod='ultimateimagetool'}
				<br/>
				{l s='In some cases it`s best not to run multiple image compressions in the same time because the hosting may think it`s being attack. So test to make sure everything is working properly.' mod='ultimateimagetool'}
				<br/>
				{l s='There is not danger to your current images, as duplicate webp images are created. You can disable the usage of webp images at any time.'  mod='ultimateimagetool'}
			</div>

			{include file="./webp/webp-actions.tpl"}
			{include file="./webp/webp-products.tpl"}
			{include file="./webp/webp-categories.tpl"}
			{include file="./webp/webp-manufacturers.tpl"}
			{include file="./webp/webp-suppliers.tpl"}
			{include file="./webp/webp-theme.tpl"}	
			{include file="./webp/webp-modules.tpl"}	
			{include file="./webp/webp-custom.tpl"}
		</div>

	{include file="./regenerate.tpl"}
	{include file="./delete.tpl"}	
	{include file="./logs.tpl"}
	{include file="./cron.tpl"}
	{include file="./support.tpl"}
	{include file="./test.tpl"}
	{include file="./lazy-load.tpl"}
	{include file="./alt-tags.tpl"}
	{include file="./zoom/zoom.tpl"}
	{include file="./sitemap.tpl"}
	{include file="./image_swap.tpl"}
	{include file="./category-images.tpl"}
	{include file="./documentation.tpl"}
	{include file="./start.tpl"}
	<div class="clear" style="clear:both"></div>
</div>
<div class="clear" style="clear:both"></div>
{if $css_file == 'global_15.css'}
</div>
{/if}
	<div class="clear" style="clear:both"></div>
</div>

<iframe src="https://advancedplugins.com/prestashop-modules/index.php?module=ultimateimagetool" scrolling="no" style="width: 100%;  border: 1px solid #ddd;    border-radius: 5px;   height: 380px; "></iframe>