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
<div class="small_panel panel active" id="regenerate_categories"  >
    <div class="panel-heading"><i class="icon-minus"></i> {l s='Compress category images' mod='ultimateimagetool'}</div>
    <div>
    	{l s='In the table bellow, you will see the number of categories not images, it will compress all of the images of each category.' mod='ultimateimagetool'}
    </div>
    {if $category_writable == false}
		<div class="alert alert-warning">
		    <h4>{l s='The image directory for categories ( /img/c/ ) is not writable ! Please change persmissions to 775. Apply changes recursevly to files also' mod='ultimateimagetool'}</h4>
		</div>
    {/if}
    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='ultimateimagetool'}</th>
				<th class="">{l s='Actions' mod='ultimateimagetool'}</th>
				<th style="width:50%" class="">{l s='Status' mod='ultimateimagetool'}</th>
				<th style="width:100px;" class="">{l s='Space Saved' mod='ultimateimagetool'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper">

	    	<tr>
	    		<td>{l s='Image quality' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="image_quality_cat" name="image_quality_cat" rel="category">
		    			{foreach $allowed_qualities as $qty}
		    				<option value="{$qty|escape:'htmlall':'UTF-8'}" {if $uit_quality_cat == $qty} selected="selected" {/if}>{$qty|escape:'htmlall':'UTF-8'}%</option>
		    			{/foreach}
		    		</select>
	    		</td>
	    		<td>{l s='Image quality, must be between 1-100, We recommend testing the image quality before starting a general optimization' mod='ultimateimagetool'}</td>
	    		<td></td>
	    	</tr>

	    	<tr>
	    		<td>{l s='Max parallel requests' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="max_parallel_cat">
		    			<option value="1">1</option>
		    			<option value="2">2</option>
		    			<option value="3" selected="selected" >3</option>
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
	    		<td></td>
	    	</tr>


	    	<tr class="cat_type_original">
	    		<td>{l s='Original' mod='ultimateimagetool'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_category_current" value="0">
	    			<input type="hidden" id="hidden_category_total" value="{$category_count|escape:'htmlall':'UTF-8'}">
	    			<input type="hidden" class="hidden_image_type" value="original">
	    			<button class="UltimateImageTool_categoryplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="UltimateImageTool_categorystop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
	    			<button class="UltimateImageTool_categoryreset"><span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span></button>
				</td>
	    		<td>
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">{$original_offset_cat|escape:'htmlall':'UTF-8'}</span> {l s='of' mod='ultimateimagetool'} <span id="categories_max">{$category_count|escape:'htmlall':'UTF-8'}</span></div><div class="{$category_count|escape:'htmlall':'UTF-8'}_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: {$original_percent_cat|escape:'htmlall':'UTF-8'}%;"></div></div>
	    		</td>
	    		<td  class="saved_after">{$original_saved_space_cat|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
	    		
	    	</tr>

	    	{foreach from=$category_sizes item=reg_item}
	    	<tr class="cat_type_{$reg_item.name|escape:'htmlall':'UTF-8'}">
	    		<td>{$reg_item.name|escape:'htmlall':'UTF-8'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_{$reg_item.name|escape:'htmlall':'UTF-8'}_current" value="0">
	    			<input type="hidden" class="hidden_image_type" value="{$reg_item.name|escape:'htmlall':'UTF-8'}">
	    			
	    			<input type="hidden" id="hidden_category_total" value="{$category_count|escape:'htmlall':'UTF-8'}">
	    			<button class="UltimateImageTool_categoryplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="UltimateImageTool_categorystop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
	    			<button class="UltimateImageTool_categoryreset"><span class="ui-button-icon-primary ui-icon ui-icon-refresh"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">{$reg_item.offset|escape:'htmlall':'UTF-8'}</span> {l s='of' mod='ultimateimagetool'} <span id="categories_max">{$category_count|escape:'htmlall':'UTF-8'}</span></div><div class="{$category_count|escape:'htmlall':'UTF-8'}_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: {$reg_item.percent|escape:'htmlall':'UTF-8'}%;"></div></div>
	    		</td>
	    		<td class="saved_after_cat">{$reg_item.saved_space|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
	    	</tr>
	    	{/foreach}
	    </tbody>

	    	
    </table> 

</div>