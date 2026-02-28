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

<div class="tab-pane panel " id="zoom"  >
    <div class="panel-heading"><i class="icon-zoom-in"></i> {l s='Zoom' mod='ultimateimagetool'}</div>
    <div class="clear"></div>

	<div class="col-lg-12 col-md-12 col-xs-12">
                    <div class="form-group">
                        <div class="control-label col-lg-5 col-md-4 col-xs-10">
                            <label class="labelbutton">{l s='Enable Ultimate Zoom' mod='ultimateimagetool'}</label>
                        </div>
                        <div>
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_enable_zoom" rel="uit_zoom" id="uit_enable_zoom_on"  value="1" {if $uit_zoom == '1'}checked="checked"{/if}>
                                <label for="uit_enable_zoom_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_enable_zoom"  rel="uit_zoom"  id="uit_enable_zoom_off" value="0"  {if $uit_zoom == '0'}checked="checked"{/if}>
                                <label for="uit_enable_zoom_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
                    </div>
                        <div class="clear"></div>
                        <hr>

						<table class="table">
					    	<tbody>
					    		<tr>
						    		<td>{l s='Full image size' mod='ultimateimagetool'}</td>
						    		<td>
						    		<select class="update_config_select" rel="uit_zoom_full_image_size">
						    			{foreach $uit_image_type as $it}
						    				<option {if $uit_zoom_full_image_size == $it.name} selected="selected"{/if} value="{$it.name|escape:'htmlall':'UTF-8'}">{$it.name|escape:'htmlall':'UTF-8'}</option>
						    			{/foreach}
						    			<option value=""  {if $uit_zoom_full_image_size == ''} selected="selected"{/if}>{l s='Original' mod='ultimateimagetool'}</option>
						    		</select>
						    		</td>
						    		<td>{l s='The image size when zoom is pressed' mod='ultimateimagetool'}</td>
					    		</tr>
					    		<tr>
						    		<td>{l s='Normal image size' mod='ultimateimagetool'}</td>
						    		<td>
						    		<select  class="update_config_select" rel="uit_zoom_normal_image_size">
						    			{foreach $uit_image_type as $it}
						    				<option {if $uit_zoom_normal_image_size == $it.name} selected="selected"{/if} value="{$it.name|escape:'htmlall':'UTF-8'}">{$it.name|escape:'htmlall':'UTF-8'}</option>
						    			{/foreach}
						    			<option value=""  {if $uit_zoom_normal_image_size == ''} selected="selected"{/if}>{l s='Original' mod='ultimateimagetool'}</option>
						    		</select>
						    		</td>
						    		<td>{l s='The image size when images are displayed in the site' mod='ultimateimagetool'}</td>
					    		</tr>
					    		<tr>
						    		<td>{l s='Thumbnail image size' mod='ultimateimagetool'}</td>
						    		<td>
						    		<select  class="update_config_select" rel="uit_zoom_thumb_image_size">
						    			<option value="">{l s='No thumbnails' mod='ultimateimagetool'}</option>
						    			{foreach $uit_image_type as $it}
						    				<option {if $uit_zoom_thumb_image_size == $it.name} selected="selected"{/if} value="{$it.name|escape:'htmlall':'UTF-8'}">{$it.name|escape:'htmlall':'UTF-8'}</option>
						    			{/foreach}
						    		</select>
						    		</td>
						    		<td>{l s='The image size when images are displayed in the site' mod='ultimateimagetool'}</td>
					    		</tr>
					    		{*
						    	<tr>
						    		<td>{l s='Zoom type' mod='ultimateimagetool'}</td>
						    		<td>
						    		<select  class="update_config_select" rel="uit_zoom_type">
						    			<option {if $uit_zoom_type == '0'} selected="selected"{/if} value="0">{l s='Big image with thumbnails' mod='ultimateimagetool'}</option>
						    			<option {if $uit_zoom_type == '1'} selected="selected"{/if} value="1">{l s='Big images with slider' mod='ultimateimagetool'}</option>
						    			<option {if $uit_zoom_type == '2'} selected="selected"{/if} value="2">{l s='Display all images' mod='ultimateimagetool'}</option>
						    		</select>
						    		</td>
						    		<td>{l s='The image size when images are displayed in the site' mod='ultimateimagetool'}</td>
					    		</tr>
					    		*}
					    </tbody>
					</table>

                    <div class="clear"></div>
                </div>
                    <div class="clear"></div>


          <div class="clear"></div>


</div>
<div class="clear"></div>