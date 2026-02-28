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
<div class="tab-pane panel " id="sitemap"  >
    <div class="panel-heading"><i class="icon-cloud"></i> {l s='Image Sitemap' mod='ultimateimagetool'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			<strong>{l s='Image Sitemap link:' mod='ultimateimagetool'}</strong>
			<p>{l s='The sitemap includes caption/alt tags for SEO purposes. Even if you have a traditional sitemap generator, you should use this.' mod='ultimateimagetool'}</p>
			<br/> 
			<p>{l s='Add the url below, to google webmaster tools. Note that sometimes google says url cannot be fetched, but after a while it will work, it`s a webmaster tools bug' mod='ultimateimagetool'}</p>
			<p><a href="{$uit_module_path_short|escape:'htmlall':'UTF-8'}/sitemap?token={$uit_token|escape:'htmlall':'UTF-8'}" target="_blank">{$uit_module_path_short|escape:'htmlall':'UTF-8'}/sitemap?token={$uit_token|escape:'htmlall':'UTF-8'}</a></p>
		

		</div>
    <div class="clear"></div>
    <div>
    	 <table class="table">
			<tr>
				<td>
					<strong>{l s='Sitemap image size:' mod='ultimateimagetool'}</strong>
				</td>
				<td>
					<select id="sitemap_image_size" name="sitemap_image_size">
						<option {if $sitemap_image_size == ''}selected="selected"{/if} value="">{l s='Original' mod='ultimateimagetool'}</option>
						{foreach from=$product_sizes item=reg_item}
							<option {if $sitemap_image_size == $reg_item.name}selected="selected"{/if} value="{$reg_item.name|escape:'htmlall':'UTF-8'}">{$reg_item.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</td>
			</tr>
    	 </table>
    	 <div class="sitemap_html"></div>
    </div>

		
</div>
<div class="clear"></div>