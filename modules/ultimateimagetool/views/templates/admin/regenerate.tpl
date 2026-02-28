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
<div id="regenerate_items" class="tab-pane">
	<div class="small_panel panel active "   >
	    <div class="panel-heading"><i class="icon-refresh"></i> {l s='Regenerate images' mod='ultimateimagetool'}</div>
	    <div class="alert alert-info">
	    	{l s='The regeneration tool does not regenerate webp images, if you have changed a specific image size dimension make sure to manually convert the images to webp for that image size.' mod='ultimateimagetool'}
		</div>
	    <table class="table">
		    <tbody id="samdha_warper">
		    	<tr>
		    		<td>{l s='Image type' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="regenerate_type" name="regenerate_type"  >
			    			<option value="">{l s='-' mod='ultimateimagetool'}</option>
			    			<option value="products" >{l s='Products' mod='ultimateimagetool'}</option>
			    			<!--<option value="categories" >{l s='Categories' mod='ultimateimagetool'}</option>
			    			<option value="manufacturers" >{l s='Manufacturers' mod='ultimateimagetool'}</option>
			    			<option value="suppliers" >{l s='Suppliers' mod='ultimateimagetool'}</option> -->
			    		</select>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Image size' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="regenerate_size"></select>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td><br/><br/>
		    			<button class="button btn btn-primary regenerate_image_sizes" rel="{$uit_module_dir|escape:'htmlall':'UTF-8'}">{l s='Regenerate image sizes' mod='ultimateimagetool'}</button>
		    			<button class="button btn btn-secondary resume_regenerate_image_sizes" rel="{$uit_module_dir|escape:'htmlall':'UTF-8'}">{l s='Resume regeneration (if stuck)' mod='ultimateimagetool'}</button>
		    		</td>
		    		<td colspan="3">
		    		</td>
		    	</tr>
		    	<tr>
		    		<td colspan="4" id="uit_regenerate">
		    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">0</span> {l s='of' mod='ultimateimagetool'} <span id="categories_max">0</span></div><div class="0_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
		    		</td>
		    	</tr>
		    </tbody>
	    </table> 
	</div>
</div>