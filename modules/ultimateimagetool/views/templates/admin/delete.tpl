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
<div id="delete_items" class="tab-pane">
	<div class="small_panel panel active "   >
	    <div class="panel-heading"><i class="icon-eraser"></i> {l s='Delete images' mod='ultimateimagetool'}</div>
	   	<p>{l s='After you deactivate or stop using some image sizes, free server space by removing them because prestashop does not delete those images.' mod='ultimateimagetool'}</p>
	   	<p>{l s='Don`t be afraid to do it, as only the sizes you select, get deleted, and you can always regenerate them, as the main image does not get deleted.' mod='ultimateimagetool'}</p>
	   	<hr>
	    <table class="table">
		    <tbody id="samdha_warper">

		    	<tr>
		    		<td>{l s='Image type' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="delete_type" name="delete_type"  >
			    			<option value="">{l s='-' mod='ultimateimagetool'}</option>
			    			<option value="products" >{l s='Products' mod='ultimateimagetool'}</option>
			    			<option value="categories" >{l s='Categories' mod='ultimateimagetool'}</option>
			    			<option value="manufacturers" >{l s='Manufacturers' mod='ultimateimagetool'}</option>
			    			<option value="suppliers" >{l s='Suppliers' mod='ultimateimagetool'}</option>
			    		</select>

		    		</td>
		    	</tr>

		    	<tr>
		    		<td>{l s='Image size' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="delete_size"></select>
		    		</td>
		    	
		    	</tr>
		    	<tr>
		    		<td><br/><br/>
		    		<button class="button btn btn-primary delete_image_sizes" rel="{$uit_module_dir|escape:'htmlall':'UTF-8'}">{l s='Delete image' mod='ultimateimagetool'}</button>
		    		</td>
		    		<td colspan="3">
		    			
		    		</td>
		    	</tr>
	    	<tr >
	    		<td colspan="4" class="type_delete ">
	    			
	    		</td>

	    		
	    	</tr>
		    </tbody>

		    	
	    </table> 

	</div>

</div>