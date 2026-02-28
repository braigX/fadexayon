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
<div class=" panel" id="web-products"  >
    <div class="panel-heading"><i class="icon-minus"></i> {l s='Convert product images' mod='ultimateimagetool'}</div>
    <div>
    	{l s='In the table bellow, you will see the number of products not images, it will convert all of the images of each product.' mod='ultimateimagetool'}
    </div>  

    {if $product_writable == false}
		<div class="alert alert-warning">
		    <h4>{l s='The image directory for products is not writable ! Please change persmissions to 775. Apply changes recursevly to files also ' mod='ultimateimagetool'}</h4>
		</div>
    {/if}

    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='ultimateimagetool'}</th>
				<th class="">{l s='Actions' mod='ultimateimagetool'}</th>
				<th style="width:50%" class="">{l s='Status' mod='ultimateimagetool'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper">


	    	<tr>
	    		<td>{l s='Image quality' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="image_quality_convert" name="image_quality_cat" rel="product">
		    			{foreach $allowed_qualities as $qty}
		    				<option value="{$qty|escape:'htmlall':'UTF-8'}" {if $uit_quality == $qty} selected="selected" {/if}>{$qty|escape:'htmlall':'UTF-8'}%</option>
		    			{/foreach}	
		    		</select>

	    		</td>
	    		<td></td>
	    		<td></td>
	    	</tr>

	    	<tr style="display: none">
	    		<td>{l s='Max parallel requests' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="max_parallel_convert">
		    			<option value="1"  selected="selected">1</option>
		    			<option value="2">2</option>
		    			<option value="3" >3</option>
		    			<option value="4">4</option>
		    			<option value="5">5</option>
		    			<option value="6">6</option>
		    			<option value="7">7</option>
		    			<option value="8">8</option>
		    			<option value="9">9</option>
		    			<option value="10">10</option>
		    		</select>
	    		</td>
	    		<td>{l s='Using many request may cause shared hosting to temporary block the IP.' mod='ultimateimagetool'}</td>
	    	</tr>


	    	<tr class="type_original_convert">
	    		<td>{l s='Original' mod='ultimateimagetool'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_product_current" value="0">
	    			<input type="hidden" id="hidden_product_total" value="{$products_count|escape:'htmlall':'UTF-8'}">
	    			<input type="hidden" class="hidden_image_type" value="original">
	    			<button class="UltimateImageTool_productplayConvert"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="UltimateImageTool_productstopConvert"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
	    			<button class="UltimateImageTool_productresetConvert"><span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span></button>
				</td>
	    		<td>
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">{$original_offset_convert|escape:'htmlall':'UTF-8'}</span> {l s='of' mod='ultimateimagetool'} <span id="categories_max">{$products_count|escape:'htmlall':'UTF-8'}</span></div><div class="{$products_count|escape:'htmlall':'UTF-8'}_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: {$original_percent_convert|escape:'htmlall':'UTF-8'}%;"></div></div>
	    		</td>
	    	</tr>

	    	{foreach from=$product_sizes_convert item=reg_item}
	    	<tr class="type_{$reg_item.name|escape:'htmlall':'UTF-8'}_convert">
	    		<td>{$reg_item.name|escape:'htmlall':'UTF-8'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_{$reg_item.name|escape:'htmlall':'UTF-8'}_current" value="0">
	    			<input type="hidden" class="hidden_image_type" value="{$reg_item.name|escape:'htmlall':'UTF-8'}">
	    			
	    			<input type="hidden" id="hidden_product_total" value="{$products_count|escape:'htmlall':'UTF-8'}">
	    			<button class="UltimateImageTool_productplayConvert"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="UltimateImageTool_productstopConvert"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
	    			<button class="UltimateImageTool_productresetConvert"><span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span></button>
				</td>
	    		<td>
	    		<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">{$reg_item.offset|escape:'htmlall':'UTF-8'}</span> {l s='of' mod='ultimateimagetool'} <span id="categories_max">{$products_count|escape:'htmlall':'UTF-8'}</span></div><div class="{$products_count|escape:'htmlall':'UTF-8'}_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: {$reg_item.percent|escape:'htmlall':'UTF-8'}%;"></div></div>
	    		</td>
	    		
	    	</tr>
	    	{/foreach}
	    </tbody>

	    	
    </table> 

</div>