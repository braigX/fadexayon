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
<div class="tab-pane panel " id="test"  >
    <div class="panel-heading"><i class="icon-book"></i> {l s='Test' mod='ultimateimagetool'}</div>
		<div class="alert alert-info">
			{l s='Before you compress your images, make sure you do not over optimize your pictures, please test that the compression percent as the process is not reversable .' mod='ultimateimagetool'}
		</div>
		<div class="alert alert-warning">
			<p>{l s='Here you can test random images and check if you are satisfied with the image quality. The quality is the maximum compression applied, it is applied for all images, it does not matter if the images are product images, manufacturer images, blog images, etc. The limit and quality is the same for any image type so if you are satisfied with the quality of the image here, just apply it to any image type you can find in the module.' mod='ultimateimagetool'}</p>
			<p>{l s='If your images were already compressed at the selected quality or a lower quality the compressed image size will be the same as the original image size.' mod='ultimateimagetool'}</p>
		</div>	
    <div class="clear"></div>
<table class="table">
	    	<tr>
	    		<td>{l s='Image Quality' mod='ultimateimagetool'}</td>
	    		<td>
	    		<select id="test_quality" name="test_quality">
		    			{foreach $allowed_qualities as $qty}
		    				<option value="{$qty|escape:'htmlall':'UTF-8'}" {if $uit_quality == $qty} selected="selected" {/if}>{$qty|escape:'htmlall':'UTF-8'}%</option>
		    			{/foreach}
	    		</select>

	    	
	    		</td>
	    		<td>{l s='These changes are for test purposes and live products will not be affected' mod='ultimateimagetool'}</td>
	    	</tr>
	    </table>
	    <br/>
    <button class="button btn btn-primary generate_test" rel="">{l s='GENERATE TEST IMAGE' mod='ultimateimagetool'}</button>

    <div id="img_test">
    	<br>
    	<table class="table"  id="test_body">
    		<thead>
    		<tr>
    			<td><strong>{l s='Original image' mod='ultimateimagetool'}</strong></td>
    			<td><strong>{l s='Compressed image' mod='ultimateimagetool'}</strong></td>
    		</tr>
    	</thead>
    		<tbody>
    			
    		</tbody>
    	</table>
    </div>
</div>
<div class="clear"></div>