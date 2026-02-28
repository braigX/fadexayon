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
<div class="small_panel panel active" id="regenerate_manufacturers"  >
    <div class="panel-heading"><i class="icon-minus"></i> {l s='Compress CMS images' mod='ultimateimagetool'}</div>
    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='ultimateimagetool'}</th>
				<th class="">{l s='Actions' mod='ultimateimagetool'}</th>
				<th style="width:50%" class="">{l s='Status' mod='ultimateimagetool'}</th>
				<th style="width:100px;" class="">{l s='Space Saved' mod='ultimateimagetool'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper" rel="cms">

	    	<tr>
	    		<td>{l s='Image quality' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="image_quality_cms" name="image_quality_cms" rel="cms" style="width:70px;">

		    			<option value="60" {if $uit_quality_cms == 60} selected="selected" {/if}>{l s='60' mod='ultimateimagetool'}</option>
		    			<option value="70" {if $uit_quality_cms == 70} selected="selected" {/if}>{l s='70' mod='ultimateimagetool'}</option>
		    			<option value="80" {if $uit_quality_cms == 80} selected="selected" {/if}>{l s='80' mod='ultimateimagetool'}</option>
		    			<option value="90" {if $uit_quality_cms == 90} selected="selected" {/if}>{l s='90' mod='ultimateimagetool'}</option>
		    			<option value="100" {if $uit_quality_cms == 100} selected="selected" {/if}>{l s='100' mod='ultimateimagetool'}</option>
		    		</select>

	    		</td>
	    		<td style="width:70px;">{l s='Image quality, must be between 1-100, We recommend testing the image quality before starting a general optimization.' mod='ultimateimagetool'}</td>
	    		<td></td>
	    	</tr>

	    	<tr>
	    		<td>{l s='Max parallel requests' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="max_parallel_cms">
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
	    	<tr>
	    		<td>
	    			<button class="button btn btn-primary get_cms_images" rel="{$uit_root_dir|escape:'htmlall':'UTF-8'}/img/cms">{l s='Search current cms for images' mod='ultimateimagetool'}</button>
	    		</td>
	    		<td colspan="3">
	    			
	    		</td>
	    	</tr>
	    	<tr>
	    		<td colspan="4" id="uit_cms_result">
	    			
	    		</td>
	    	</tr>
	    </tbody>

	    	
    </table> 

</div>