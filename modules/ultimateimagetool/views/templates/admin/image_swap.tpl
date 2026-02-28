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

<div class="tab-pane panel " id="image_swap"  >
    <div class="panel-heading"><i class="icon-th-large"></i> {l s='Image Swap' mod='ultimateimagetool'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			<strong>{l s='Display an extra image on mouse hover, in the product-list:' mod='ultimateimagetool'}</strong>
			<p>{l s='If it does not work on your theme, make sure that the img element in the list has the an wlement with the class `ajax_add_to_cart_button` and the attribute `data-id-product` on it, with the product ID.' mod='ultimateimagetool'}</p>

		</div>
		<div class="alert alert-warning">
			<p>{l s='The image swap may cause problems if your theme has changed the default structure of the prestashop theme and is not compatible with infinite scroll' mod='ultimateimagetool'}</p>
			
		</div>	
    <div class="clear"></div><br/><br/>
   	<h3>{l s='Swap image on mouse hover' mod='ultimateimagetool'}</h3>
 	<table class="table">
		    <tbody id="samdha_warper">
		    	<tr>
		    		<td>{l s='Display image on mouse hover' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="uit_mouse_hover" name="uit_mouse_hover"  >
			    			<option value="disabled"{if $uit_mouse_hover == 'disabled'} selected{/if}>{l s='Disabled' mod='ultimateimagetool'}</option>
			    			<option value="enabled" {if $uit_mouse_hover == 'enabled'} selected{/if}>{l s='Enabled' mod='ultimateimagetool'}</option>
			    		</select>

		    		</td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Display which image' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="uit_image_position" name="uit_image_position"  >
			    			<option value="last_image"{if $uit_mouse_hover_position == 'last_image'} selected{/if}>{l s='Last Product Image' mod='ultimateimagetool'}</option>
			    			<option value="second_image" {if $uit_mouse_hover_position == 'second_image'} selected{/if}>{l s='Second Product Image' mod='ultimateimagetool'}</option>
			    		</select>

		    		</td>
		    	</tr>
				<tr>
		    		<td>{l s='Image size on hover' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="uit_hover_image_type" name="uit_hover_image_type"  >
			    			{foreach from=$product_sizes item=reg_item}
			    				<option value="{$reg_item.name|escape:'htmlall':'UTF-8'}"{if $uit_hover_image_type == $reg_item.name} selected{/if}>{$reg_item.name|escape:'htmlall':'UTF-8'}</option>
			    			{/foreach}
			    			
			    	
			    		</select>

		    		</td>
		    	</tr>
	    	<tr >
	    		<td colspan="4" id="uit_mouse_html"></td>
	    	</tr>
		    </tbody>
	    </table> 

	   <br/><br/>
	    <h3>{l s='Display all image thumbnails on mouse hover' mod='ultimateimagetool'}</h3>
	    <p>{l s='Swap image on mouse hover must be enabled for this to work' mod='ultimateimagetool'}</p>
		<table class="table">
			    <tbody id="samdha_warper">
			    	<tr>
			    		<td>{l s='Display all thumbnails on mouse hover?' mod='ultimateimagetool'}</td>
			    		<td>
				    		<select id="uit_mouse_hover_thumb" name="uit_mouse_hover_thumb"  >
				    			<option value="disabled"{if $uit_mouse_hover_thumb == 'disabled'} selected{/if}>{l s='Disabled' mod='ultimateimagetool'}</option>
				    			<option value="enabled" {if $uit_mouse_hover_thumb == 'enabled'} selected{/if}>{l s='Enabled' mod='ultimateimagetool'}</option>
				    		</select>

			    		</td>
			    	</tr>
					<tr>
			    		<td>{l s='Thumbnail image size ( the size of the small images displayed )' mod='ultimateimagetool'}</td>
			    		<td>
				    		<select id="uit_mouse_hover_ts" name="uit_mouse_hover_ts"  >
				    			{foreach from=$product_sizes item=reg_item}
				    				<option value="{$reg_item.name|escape:'htmlall':'UTF-8'}"{if $uit_mouse_hover_ts == $reg_item.name} selected{/if}>{$reg_item.name|escape:'htmlall':'UTF-8'}</option>
				    			{/foreach}
				    		</select>
			    		</td>
			    	</tr>
						<tr>
			    		<td>{l s='Switched image size ( the size of the image in the product list)' mod='ultimateimagetool'}</td>
			    		<td>
				    		<select id="uit_mouse_hover_ps" name="uit_mouse_hover_ps"  >
				    			{foreach from=$product_sizes item=reg_item}
				    				<option value="{$reg_item.name|escape:'htmlall':'UTF-8'}"{if $uit_mouse_hover_ps == $reg_item.name} selected{/if}>{$reg_item.name|escape:'htmlall':'UTF-8'}</option>
				    			{/foreach}
				    		</select>
			    		</td>
			    	</tr>			    	
		    	<tr >
		    		<td colspan="4" id="uit_mouse_html2"></td>
		    	</tr>
			    </tbody>
		    </table> 
</div>
<div class="clear"></div>