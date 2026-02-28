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
    <div class="panel-heading"><i class="icon-minus"></i> {l s='convert Images by folders' mod='ultimateimagetool'}</div>
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
		    		<select id="image_quality_custom_convert" name="image_quality_custom_convert" rel="custom-folder" style="width:70px;">
		    			{foreach $allowed_qualities as $qty}
		    				<option value="{$qty|escape:'htmlall':'UTF-8'}">{$qty|escape:'htmlall':'UTF-8'}%</option>
		    			{/foreach}	
		    		</select>

	    		</td>
	    		<td style="width:70px;">{l s='Image quality, must be between 60-100, We recommend testing the image quality before starting a general optimization.' mod='ultimateimagetool'}</td>
	    		<td></td>
	    	</tr>

	    
	    	<tr id="custom-convert-wrapper" style="display:none">
	    		<td colspan="4">
	    			<span style="float:left" id="custom-found-convert"></span>
	    			<button class="button btn btn-primary convert_custom_images" style="float:right;">{l s='Convert all images' mod='ultimateimagetool'}</button>
	    		</td>
	    		
	    	</tr>

	    	<tr>
	    		<td colspan="4" id="uit_convert_custom">
	    			<div class="col-lg-4">
	    				{if $root_folders}
	    					<div id="base_jstreeconvert" class="jstree">
							  <ul>
							  	{foreach $root_folders as $folder}
							  		{assign var="folder_expl" value="/"|explode:$folder}
								    <li rel="{$folder|escape:'htmlall':'UTF-8'}">{$folder_expl|@end|escape:'htmlall':'UTF-8'}</li>
							    {/foreach}
							  </ul>
							</div>
	    				{/if}
	    			</div>
	    			<div class="col-lg-8">
						<table class="table" style="    border: 1px solid #eff1f2;">
							    <thead class="">
							    	<tr class="first">
										<th class="">#</th>
										<th class="">Thumb</th>
										<th class="">Path</th>
										<th class="">Original Size</th>
										<th class="">converted Size</th>
										<th class="">Message</th>
									</tr> 
							    </thead>
							    <tbody id="samdha_warper2" class="convert-custom-wrapper" rel='custom-folder'></tbody>
							</table>
	    			</div>
	    		</td>	
	    	</tr>
	    </tbody>

	    	
    </table> 

</div>