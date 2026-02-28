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
   <div class="panel">
	   <div class="panel-heading"><i class="icon-book"></i> {l s='Settings' mod='ultimateimagetool'}</div>
			<div class="alert alert-warning">
				<p>{l s='Future catalog uploaded images are automatically converted to webp if the option is enabled, no cron job is necessary.' mod='ultimateimagetool'}</p>
				<p>{l s='Custom module images such as sliders/carousels or any type of custom module uploaded images cannot be automatically converted as they do not use the prestashop hooks, those images must manually be converted to webp after upload, you can reconvert all of the images in the specific folder as there is no risk if other images have already been converted in that folder. ' mod='ultimateimagetool'}</p>
				<p>{l s='We recommend only disabling automatically converting images to webp if you upload mass products crashes due to timeout.' mod='ultimateimagetool'}</p>
				<p>{l s='In extremely rare cases, the webp generated images are bigger than the original jpg ones, this is normal and you can check googles official documentation for more information.' mod='ultimateimagetool'}</p>
				
			</div>
			<div class="alert alert-info">
				<p>{l s='You can disable it at any time with no risk to your original JPG images.' mod='ultimateimagetool'}</p>
				<p>{l s='If you have updated from a module version that does not support WebP conversion, after you update the module, you should reset the module to reinstall overrides.' mod='ultimateimagetool'}</p>
				<p>{l s='You must manually optimize your existing images, when you first activate the option. It will automatically convert all your future images to webp while this option is active' mod='ultimateimagetool'}</p>
				<p>{l s='WebP images for cms/theme/module images are only displayed with .jpg and if the browser support .webp is automatically served and mime type changed but you will still see .jpg in the image src="" section' mod='ultimateimagetool'}</p>
				<p>{l s='If you convert images to webp, duplicate webp images will be created for each jpg, png image while also keeping the original images, this will increase your disk space usage by a considerable amount. Webp images are generally smaller than the original images.' mod='ultimateimagetool'}</p>
				<p>{l s='Test if webp is active by following these steps:' mod='ultimateimagetool'} <a style="text-decoration: underline;" target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/check_webp.pdf">{l s='Check if webp works guide' mod='ultimateimagetool'}</a></p>

			</div>
	   		<div class="clear"></div>
			<table class="table">
		    	<tr>
		    		<td>
		    			  <label class="labelbutton">{l s='Enable WebP images' mod='ultimateimagetool'}</label>
		    		</td>
		    		<td>

			
				    <div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_use_webp" rel="uit_use_webp" id="uit_use_webp_on"  value="2" {if $uit_use_webp == '2' || $uit_use_webp == '1'}checked="checked"{/if}>
                                <label for="uit_use_webp_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_use_webp"  rel="uit_use_webp"  id="uit_use_webp_off" value="0"  {if $uit_use_webp == '0'}checked="checked"{/if}>
                                <label for="uit_use_webp_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
		    		</td>
		    		<td>{l s='It will keep .jpg termination, serve .webp and change mime type to image/webp, example image will be: http://yoursite.com/img/demoimage.jpg but the format will be WebP. No risk to your original JPG images as they will remain and be served to non webp supported browsers.' mod='ultimateimagetool'}</td>
		    	</tr>
				<tr>
		    		<td>
		    			  <label class="labelbutton">{l s='Use External API' mod='ultimateimagetool'}</label>
		    		</td>
		    		<td>

			
				    <div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_use_external_api" rel="uit_use_external_api" id="uit_use_external_api_on"  value="1" {if $uit_use_external_api == '1'}checked="checked"{/if}>
                                <label for="uit_use_external_api_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_use_external_api"  rel="uit_use_external_api"  id="uit_use_external_api_off" value="0"  {if $uit_use_external_api == '0'}checked="checked"{/if}>
                                <label for="uit_use_external_api_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
		    		</td>
		    		<td>{l s='By activating this, you will force the module to use our inhouse external API to convert the images and ads delay to conversion process, the only time we recommend to enable this is if you have many PNG images and you only have GD library to convert the images, and GD libraries with webp does not support transparancy and PNG are excluded' mod='ultimateimagetool'}</td>
		    	</tr>
	    	<tr>
	    		<td>{l s='Image quality' mod='ultimateimagetool'}</td>
	    		<td>
		    		<select id="uit_image_quality_webp_cron" name="uit_image_quality_webp_cron" rel="uit_image_quality_webp_cron" class="update_config_select">
		    			{foreach $allowed_qualities as $qty}
		    				<option value="{$qty|escape:'htmlall':'UTF-8'}" {if $uit_image_quality_webp_cron == $qty} selected="selected" {/if}>{$qty|escape:'htmlall':'UTF-8'}%</option>
		    			{/foreach}	
		    		</select>
	    		</td>
	    		<td>
	    			{l s='This image quality will be used when converting to WebP on image upload/resize.' mod='ultimateimagetool'}
	    		</td>
	    	</tr>
	    	<tr>
	    		<td colspan="3">
	    			{if $has_cloudflare}
		    			<div class="alert alert-danger">
		    				<div>{l s='You are using cloudflare, we recommend using the following settings' mod='ultimateimagetool'}</div>	
		    				<hr>
		    					<ul>
		    						<li>{l s='set `Use <Picture> Tag` to YES - You can disable this if it creates conflict with custom modules like Zoom, Carousels, etc but than WEBP will not work if you use both Cloudflare + Page Cache Module like `Page Cache`, `LiteSpeed Cache`, etc and this option is OFF' mod='ultimateimagetool'}</li>
		    						<li>{l s='set `Use .webp termination` to YES - This is mandatory with Cloudflare' mod='ultimateimagetool'}</li>
		    					</ul>
		    			</div>
		    		{/if}
	    		</td>
	    	</tr>

		    	<tr>
		    		<td>
		    			  <label class="labelbutton">{l s='Use .webp termination' mod='ultimateimagetool'}</label>
		    		</td>
		    		<td>
					    <div class="form-group">
	                            <div class="input-group fixed-width-lg">
	                                <span class="switch prestashop-switch fixed-width-lg">
	                                <input class="yes" type="radio" name="uit_use_webp_termination" rel="uit_use_webp_termination" id="uit_use_webp_termination_on"  value="1" {if $uit_use_webp_termination == '1'}checked="checked"{/if}>
	                                <label for="uit_use_webp_termination_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
	                                <input class="no" type="radio" name="uit_use_webp_termination"  rel="uit_use_webp_termination"  id="uit_use_webp_termination_off" value="0"  {if $uit_use_webp_termination == '0'}checked="checked"{/if}>
	                                <label for="uit_use_webp_termination_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
	                                <a class="slide-button btn"></a>
	                                </span>
	                            </div>
                        </div>
		    		</td>
		    		<td>
		    			{l s='It will keep not keep.jpg termination, and it will be changed to .webp, this option is not compatible with cache modules, and should be activated only if webp does not work without it active. This may happen due apache/nginx configurations. If you have a cache module and activate this, it will not work on Safari or non supporting browsers. If you are using Cloudflare you can only use webp images with this option active (Very important, there is no way to use the webp with cloudflare + a cache module)' mod='ultimateimagetool'}</td>
		    	</tr>
					<tr>
		    		<td>{l s='Use <Picture> Tag' mod='ultimateimagetool'}</td>
		    		<td>
		    			    <div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_use_picture_webp" rel="uit_use_picture_webp" id="uit_use_picture_webp_on"  value="1" {if $uit_use_picture_webp == '1'}checked="checked"{/if}>
                                <label for="uit_use_picture_webp_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_use_picture_webp"  rel="uit_use_picture_webp"  id="uit_use_picture_webp_off" value="0"  {if $uit_use_picture_webp == '0'}checked="checked"{/if}>
                                <label for="uit_use_picture_webp_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
		    		</td>
		    		<td>
		    			{l s='This will add a `picture` wrapper to your elements' mod='ultimateimagetool'}<br/>
		    			{l s='For example < img src=”img.jpg” / > will be transformed to < picture > < source srcset=”img.webp” type=”image/webp” > < img src=”img.png” alt=” ” > < /picture >' mod='ultimateimagetool'}<br/>
		    			{l s='Activate this version and disable ”Change image extension” and you can use it with a Cache module or Cloudflare, in rare cases this option can break javascript executed functionalities such as sliders/zoom/carousels so be sure to test it if you have such functionalities and disable it if it causes problems.' mod='ultimateimagetool'}<br/>
		    			<strong>{l s='Activating this option may cause issues with your zoom functionality, be sure to test the zoom functionality after you have activated this option and disable it if your zoom breaks.' mod='ultimateimagetool'}</strong><br/>
		    		</td>
		    	</tr>
					<tr>
		    		<td>{l s='Auto convert Images to webp' mod='ultimateimagetool'}</td>
		    		<td>
		    			    <div class="form-group">
                            <div class="input-group fixed-width-lg">
                                <span class="switch prestashop-switch fixed-width-lg">
                                <input class="yes" type="radio" name="uit_auto_webp" rel="uit_auto_webp" id="uit_auto_webp_on"  value="2" {if $uit_auto_webp == '2'}checked="checked"{/if}>
                                <label for="uit_auto_webp_on" class="radioCheck">{l s='Yes' mod='ultimateimagetool'}</label>
                                <input class="no" type="radio" name="uit_auto_webp"  rel="uit_auto_webp"  id="uit_auto_webp_off" value="0"  {if $uit_auto_webp == '0'}checked="checked"{/if}>
                                <label for="uit_auto_webp_off" class="radioCheck">{l s='No' mod='ultimateimagetool'}</label>
                                <a class="slide-button btn"></a>
                                </span>
                            </div>
                        </div>
		    		</td>
		    		<td>{l s='If you mass import products, your script will timeout, you can disable this option to save memory and manually convert them after' mod='ultimateimagetool'}</td>
		    	</tr>
		    </table>
		    <div id="uit_action_status" style="display:none" class="alert alert-success">{l s='WebP image status updated' mod='ultimateimagetool'}</div>
		    	<br/>
   			</div>
			{if !$webp_exists && !$imagick_exists}
				{if !$webp_exists}
					<div class="alert alert-danger"><span class="alert_close"></span>
						<p>{l s='php GD extension is not installed or does not support webp conversion' mod='ultimateimagetool'}</p>
					</div>
				{/if}
				{if !$imagick_exists}
				<div class="alert alert-danger"><span class="alert_close"></span>
					{l s='php Imagick extension is not installed or does not support webp conversion' mod='ultimateimagetool'}
					<br/> 
					
				</div>
				{/if}
				<div class="alert alert-danger"><span class="alert_close"></span>
					{l s='Contact your hosting provider to install any of the above php extensions with webp support (It`s not enough to have the extensions installed, they must have webp support), to use the webp conversion tool' mod='ultimateimagetool'}
					<br/>
				</div>
				<div class="alert alert-info"><span class="alert_close"></span>
					<p>{l s='As a fallback, you can continue to convert the images, you will use our inhouse conversion software via API (it is FREE), but it`s not optimal as it will add delay to the conversion process, and it will depend on the our server usage and it`s not always available.' mod='ultimateimagetool'}</p>
					<p>{l s='After you finish converting with our inhouse conversion, contact your hosting and enable one of the above php extensions.' mod='ultimateimagetool'}</p>
					<br/>
				</div>
			{/if}
			

			<div class="alert alert-info">
				<p>{l s='Automatically converted images (on upload) do not increase the progress counters below. The counters are only for manual conversion, if you convert all the images and the counters shows 10/10 afterwards you add a new product, the counter will show 10/11 images done but the newly uploaded images was compressed.' mod='ultimateimagetool'}</p>
			</div>