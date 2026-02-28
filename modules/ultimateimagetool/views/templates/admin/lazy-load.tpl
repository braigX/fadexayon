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
<div class="tab-pane panel " id="lazy_load"  >
    <div class="panel-heading"><i class="icon-magic"></i> {l s='Lazy load' mod='ultimateimagetool'}</div>
		<div class="alert alert-info">
			{l s='To offer a better experience to your customers you can activate the lazy load option.' mod='ultimateimagetool'}
			<br/> 
			{l s='Activating the lazy load option, will decrease the page loading time, and give you better SEO ranking' mod='ultimateimagetool'}
			<br/> 
			{l s='Most lazy load scripts make the images, unindexable, this is not the case there, as this script stops the network from loading the image, without editing the `src` of the image in the code, this will offer your users a better browsing experience and preserve your image SEO' mod='ultimateimagetool'}
			<br/> 
			<strong>{l s='Make sure you test this feature after you activate it, so it doesn`t cause problems. ' mod='ultimateimagetool'}</strong>

		</div>
		<div class="alert alert-warning">
			{l s='Lazy load can be incompatible with custom zoom/slider modules, if this is the case manual changes must be done to the module for lazy load to be compatible with your theme/modules.' mod='ultimateimagetool'}
			<p>Make sure you do not have JS errors or any other JS option active in the theme or modules.</p>
		</div>
    <div class="clear"></div>

		 <table class="table">
		    <tbody id="samdha_warper">

		    	<tr>
		    		<td>{l s='Lazy Load' mod='ultimateimagetool'}</td>
		    		<td>
							<div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_lazy_load" rel="uit_lazy_load" id="uit_lazy_load_on"  value="enabled" {if $uit_lazy_load == "enabled" }checked="checked"{/if}>
                                <label for="uit_lazy_load_on" class="radioCheck">{l s='Enabled' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_lazy_load"  rel="uit_lazy_load"  id="uit_lazy_load_off" value="disabled"  {if $uit_lazy_load == "disabled"}checked="checked"{/if}>
                                <label for="uit_lazy_load_off" class="radioCheck">{l s='Disabled' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>


		    		</td>
		    		<td></td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Enable only on product images' mod='ultimateimagetool'}</td>
		    		<td>
			    		 <div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_lazy_op" rel="uit_lazy_op" id="uit_lazy_op_on"  value="1" {if $uit_lazy_op == 1 }checked="checked"{/if}>
                                <label for="uit_lazy_op_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_lazy_op"  rel="uit_lazy_op"  id="uit_lazy_op_off" value="0"  {if $uit_lazy_op == 0}checked="checked"{/if}>
                                <label for="uit_lazy_op_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>

		    		</td>
		    		<td>
		    			{l s='we recommend setting this to NO, set this to yes, only if you have problems with lazy load on other images, if this is set to NO, lazy load will be applied to all images' mod='ultimateimagetool'}
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>{l s='Php performance' mod='ultimateimagetool'}</td>
					<td>
						<select  class="update_config_select" rel="uit_simple_load">
						    <option {if $uit_simple_load == 0} selected="selected"{/if} value="0">{l s='Fast php image replacement (Sometimes faster)' mod='ultimateimagetool'}</option>
						    <option {if $uit_simple_load == 1} selected="selected"{/if} value="1">{l s='Stable php image replacement (Sometimes slower)' mod='ultimateimagetool'}</option>
						</select>
					</td>
					<td>
						{l s='Set this to `Stable` if you are not certain! Php replacement method, so if you are experiencing problems (this can happen due to incompatible themes/modules) with image loading if you use `Fast php image replacement` only than switch to `Stable php image replacement (slower)` ' mod='ultimateimagetool'}<br/>
					</td>
		    	</tr>
				<tr>
					<td>{l s='Loading image' mod='ultimateimagetool'}</td>
						    		<td>
						    		<select  class="update_config_select" rel="uit_lazy_load_image">
						    			<option {if $uit_lazy_load_image == "blank.png"} selected="selected"{/if} value="blank.png">{l s='Blank transparent image' mod='ultimateimagetool'}</option>
						    			<option {if $uit_lazy_load_image == "loading.svg"} selected="selected"{/if} value="loading.svg">{l s='Loading animation' mod='ultimateimagetool'}</option>
						    		</select>
						    		</td>
						    		<td>{l s='The image that will initially be displayed' mod='ultimateimagetool'}</td>
				</tr>
				<tr>
					<td>{l s='Exceptions classes' mod='ultimateimagetool'}</td>
					<td>
						<input type="text" placeholder=".lazy, .slider-image, .input-image" name="uit_exceptions" value="{$uit_exceptions|escape:'htmlall':'UTF-8'}" class="form-control update_config_write">
					</td>
					<td>{l s='Enter image/image parent classes you want to remove from the lazy load. This is useful when you have carousels that do no work correctly, to remove lazy load from them. If you have multiple classes you can separate them by comma.' mod='ultimateimagetool'}<br/>
						{l s='If you enter for example `.thumbnail, .js-item`, all images that have any of these classes will not be lazy loaded, disable lazy load if you have loading problems with javascript manipulated elements like carousels, sliders, zoom modules, etc.' mod='ultimateimagetool'}</td>
				</tr>
	    	<tr >
	    		<td colspan="4" class="lazy_html "></td>
	    	</tr>
		    </tbody>
	    </table> 
</div>
<div class="clear"></div>