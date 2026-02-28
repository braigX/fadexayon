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
  <br/> <br/>
<span style="float:left">{$uit_files|@count|escape:'htmlall':'UTF-8'} {l s='images found.' mod='ultimateimagetool'}</span>
  <button class="button btn btn-primary optimize_theme_images{if $webp == 1}_webp{/if}" style="float:right;" >{if $webp == 1}{l s='Convert all images to webp' mod='ultimateimagetool'}{else}{l s='Optimize all images' mod='ultimateimagetool'}{/if}</button>
 <div class="clear clearfix"></div>
 <br/>
 <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='#' mod='ultimateimagetool'}</th>
				<th class="">{l s='Thumb' mod='ultimateimagetool'}</th>
				<th class="">{l s='Path' mod='ultimateimagetool'}</th>
				<th class="">{l s='Original Size' mod='ultimateimagetool'}</th>
				<th class="">{l s='Compressed Size' mod='ultimateimagetool'}</th>
				<th class="">{l s='Message' mod='ultimateimagetool'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper2">
	    	{foreach from=$uit_files item=file}
	    	<tr rel="{$file.path|escape:'htmlall':'UTF-8'}" url="{$file.url|escape:'htmlall':'UTF-8'}" class="file_to_compress">
	    		<td></td>
	    		<td><a href="{$file.url|escape:'htmlall':'UTF-8'}" target="_blank"><img style="max-height: 30px; max-width: 50px;  width:auto !important;" src="{$file.url|escape:'htmlall':'UTF-8'}"></a></td>
	    		<td><span title="{$file.path|escape:'htmlall':'UTF-8'}">{$file.path|escape:'htmlall':'UTF-8'|truncate:20}</span></td>
	    		<td></td>
	    		<td></td>
	    		<td class="msg_wrapper"></td>
	    	</tr>
	    	{/foreach}
	    </tbody>
	</table>
