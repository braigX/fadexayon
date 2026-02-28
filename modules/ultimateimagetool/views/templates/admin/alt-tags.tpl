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
<div class="tab-pane panel " id="alt_tags"  >
    <div class="panel-heading"><i class="icon-quote-right"></i> {l s='Alt tags' mod='ultimateimagetool'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			{l s='From a SEO point of view, images with no ALT TAG are bad for SEO because search engines do not know what they represent.' mod='ultimateimagetool'}
			<br/> 
			{l s='By generating ALT tags  for all your images you will rank better and offer your users with bad internet connection a better experience until they load. ' mod='ultimateimagetool'}
			<p>{l s='For large catalogs, the update process may timeout, you can resume the building process by making certain you select `Only images without ALT TAG ALREADY set` in the dropdown below. ' mod='ultimateimagetool'}</p>
			<strong>{l s='Use this tool, to generate ALT tags for all your images. ' mod='ultimateimagetool'}</strong>

		</div>
    <div class="clear"></div>
    <strong><span class="current_alt_tags">{if $uit_empty_images > 0}{$uit_empty_images|escape:'htmlall':'UTF-8'}/{$uit_total_images|escape:'htmlall':'UTF-8'} </span>{l s='do not have alt tags. This is bad for SEO and user experience.' mod='ultimateimagetool'}{else}{l s='Good Job ! There are no images without ALT tags' mod='ultimateimagetool'}{/if}</strong>
    <br/>
		 <table class="table">
		    <tbody id="samdha_warper">
		    	<tr valign="top">
		    		<td valign="top">{l s='Alt Tag Syntax' mod='ultimateimagetool'}</td>
		    		<td>
			    		<input type="text" name="alt_syntax" id="alt_syntax" value="{$uit_alt_format|escape:'htmlall':'UTF-8'}" /><br/>
			    		<p class="small">{l s='Syntax shortcuts for products' mod='ultimateimagetool'}  {literal}{PARENT_CATEGORY_NAME} {SUPPLIER_NAME} {MANUFACTURER_NAME} {PRODUCT_NAME} {PRODUCT_PRICE} {PRODUCT_SHORT_DESCRIPTION} {PRODUCT_REFERENCE} {IMAGE_POSITION}{/literal}</p>
			    		<div class="uit_altsyntax_html"></div>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Apply to' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="alt_apply" name="alt_apply"  >
			    			<option value="1">{l s='Only images without an ALT TAG already set' mod='ultimateimagetool'}</option>
			    			<option value="2">{l s='All images' mod='ultimateimagetool'}</option>
			    		</select>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Auto apply to newly added products' mod='ultimateimagetool'}</td>
		    		<td>
			    		<select id="alt_tags_auto" name="alt_tags_auto"  >
			    			<option {if $alt_tags_auto == 'no'}selected="selected"{/if} value="no">{l s='No' mod='ultimateimagetool'}</option>
			    			<option {if $alt_tags_auto == 'yes-all'}selected="selected"{/if}  value="yes-all">{l s='Yes, on all newly added products or every product update' mod='ultimateimagetool'}</option>
			    			<option {if $alt_tags_auto == 'yes-only-without-tags'}selected="selected"{/if}  value="yes-only-without-tags">{l s='Yes, only on images without alt tags for newly added products or every update' mod='ultimateimagetool'}</option>
			    		</select>
			    		<div id="uit_alt_auto_html"></div>
		    		</td>
		    	</tr>
	    	<tr>
	    		<td>
	    			<button class="button btn btn-primary generate_tags">{l s='Generate tags now' mod='ultimateimagetool'}</button>
	    		</td>
	    		<td colspan="3" class="alt_message "></td>
	    	</tr>
		    </tbody>
	    </table> 
</div>
<div class="clear"></div>