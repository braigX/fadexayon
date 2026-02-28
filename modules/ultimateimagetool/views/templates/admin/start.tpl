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
<div class="tab-pane panel active " id="start"  >
    <div class="panel-heading"><i class="icon-book"></i> {l s='Quick start guide' mod='ultimateimagetool'}</div>
		<div class="">
			<p>{l s='This is Quick guide for image optimization for image conversion and compression with recommended settings' mod='ultimateimagetool'}</p><br/>
			<div class="step ">
			  <div>
			    <div class="circle">1</div>
			  </div>
			  <div>
			    <div class="title2" style="margin-bottom: 15px;">{l s='Fix critical errors' mod='ultimateimagetool'}</div>
			  	{if (!$webp_exists && !$imagick_exists) || !$uit_htaccess}
				    {if !$uit_htaccess}
						<div class="alert alert-danger">
							{l s='There was a problem installing the module. The .htaccess file was not updated. Go to the module page list -> find this module -> press the small arrow to see all options -> Press RESET' mod='ultimateimagetool'}<br/>
							{l s='This warning will disappear when the .htaccess file is correctly edited and module installed correctly.' mod='ultimateimagetool'}
							{l s='If it does not dissappear after you have RESET the module, that means PHP does not have permissions to edit the .htaccess file, you need to check permissions and owner of that file' mod='ultimateimagetool'}
						</div>
				    {/if}
					{if !$webp_exists && !$imagick_exists}
						<div class="alert alert-danger"><span class="alert_close"></span>
								{if !$webp_exists}
									<p>{l s='php GD extension is not installed or does not support webp conversion' mod='ultimateimagetool'}</p>
								{/if}
								{if !$imagick_exists}
									{l s='php Imagick extension is not installed or does not support webp conversion' mod='ultimateimagetool'}
								{/if}
								{l s='Contact your hosting provider to install any of the above php extensions with webp support (It`s not enough to have the extensions installed, they must have webp support), to use the webp conversion tool' mod='ultimateimagetool'}
								<p><strong>{l s='IMPORTANT -> that does not mean webp php extensions are mandatory!' mod='ultimateimagetool'}</strong></p>
								<p>{l s='As a fallback, you can continue to convert the images, you will use our inhouse conversion software via API (it is FREE), but it`s not optimal as it will add delay to the conversion process, and it will depend on the our server usage and it`s not always available.' mod='ultimateimagetool'}</p>
								<p>{l s='After you finish converting with our inhouse conversion, we strongly encourage you to contact your hosting and enable one of the above php extensions.' mod='ultimateimagetool'}</p>
								<p>{l s='To use our inhouse API, just use ignore these messages and continue using the module normally, it will detect you do not have the php extensions installed and it will automatically use the API.' mod='ultimateimagetool'}</p>
						</div>
					{/if}
				{else}
					<p style="color:green"><i class="fa fa-check"></i> {l s='There are no critical problems to fix, you can move to the next step' mod='ultimateimagetool'}</p> 
				{/if}
			  </div>
			</div>
			<div class="step">
			  <div>
			    <div class="circle">2</div>
			  </div>
			  <div>
			    <div class="title2" style="margin-bottom: 15px;">{l s='Compressing your current images' mod='ultimateimagetool'}</div>
			    <p><strong>- {l s='Even if you want only want to use .webp images for optimization, we strongly recommended you also compress your images, this is because not all browsers support webp, and by serving optimized images to those browsers as well you will maximize the user experience and the site speed' mod='ultimateimagetool'}</strong></p>
			    <p>1. {l s='The recommended image quality when compressing the images is 80, if you are not certain how the images look at this quality you can go to the `Test image quality` tab from the left menu and test the impact that certain quality percent has on the images when compressing them.' mod='ultimateimagetool'}</p>
			    <p>2. {l s='Go to the `Compress images` tab from the left menu, go to each image type select the desired image quality and press `Play` after that wait for images to finish compressing. We recommended compressing one image type at a time, as building many at a time can overload your hosting. We recommend not to compress the ORIGINAL image size, as it is rarely used and can be used to regenerate the image sizes if anything goes wrong.' mod='ultimateimagetool'}</p>
			    <p>3. {l s='Compress Module/Theme/CMS images by scrolling down in the `Compress images` tab, go to each type -> press `Search current ....` from each type -> Wait until scanned images are displayed -> Press the `Optimize All images` button that appeared in the right and wait for the images to compress, you will see a check icon next to each compressed image' mod='ultimateimagetool'}</p>
			    <p>4. {l s='After you finished compressing existing images, go to `Compress Images with Cron` tab and set a cron job that will compress future uploaded images. Please take into account that only product, category, manufacturer and supplier images are compressed with cron job. This step is very important to automatically compress future uploaded images, alternatively you can periodically manually compress missing images.' mod='ultimateimagetool'}</p>
			  </div>
			</div>
			<div class="step">
			  <div>
			    <div class="circle">3</div>
			  </div>
			  <div>
			    <div class="title2" style="margin-bottom: 15px;">{l s='Convert images to webp' mod='ultimateimagetool'}</div>
					<p><strong>- {l s='Converting the images to webp is the very similar to the image compress process' mod='ultimateimagetool'}</strong></p>
					<p>1. {l s='We strongly recommend setting `Enable WebP images` to `YES` in order to enable webp delivery to the browsers that support it' mod='ultimateimagetool'}</p>
			    	<p>2. {l s='The recommended image quality when converting images to webp is 80.' mod='ultimateimagetool'}</p>
					<p>3. {l s='Go to the `Webp Conversion` tab from the left menu, go to each image type select the desired image quality(80 is recommended) and press `Play` after that wait for images to finish converting. We recommended converting one image type at a time, as building many at a time can overload your hosting.' mod='ultimateimagetool'}</p>
			    	<p>4. {l s='Compress Module/Theme/CMS images by scrolling down in the `Webp Conversion` tab, go to each type -> press `Search current ....` from each type -> Wait until scanned images are displayed -> Press the `Convert all images to webp` button that appeared in the right and wait for the images to convert, you will see a check icon next to each compressed image' mod='ultimateimagetool'}</p>
			    	<p>5. {l s='Future images are automatically converted to webp without having to setup a cron job just make sure you set `Auto convert images to webp` to `Yes` from the top selector' mod='ultimateimagetool'}</p>
			    	<p>6. {l s='Test if webp is active by following these steps: ' mod='ultimateimagetool'} <a style="text-decoration: underline;" target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/check_webp.pdf">{l s='Check if webp works guide ' mod='ultimateimagetool'}</a> ({l s='with the recommended version, .jpg will not be changed to .webp termination but your webserver will serve the webp format to the user, if the browser does not support it the original image will be served.' mod='ultimateimagetool'})</p>
					<p>{l s='Some webp images are larger that the original image, but this happens very rare. Official information regarding this can be found here: ' mod='ultimateimagetool'} <a href="https://developers.google.com/speed/webp/faq#can_a_webp_image_grow_larger_than_its_source_image" target="_blank">https://developers.google.com/speed/webp/faq#can_a_webp_image_grow_larger_than_its_source_image</a></p>
					<p>{l s='If you are using nginx webserver and it does not read the htaccess rules, please follow this guide (Only for advanced users, if you do not know how to do this, you should contact a specialist): ' mod='ultimateimagetool'} <a style="text-decoration: underline;" target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/nginx_guide.pdf">{l s='Nginx Guide' mod='ultimateimagetool'}</a></p>
					<li>{l s='If you have changed a specific image size width or height make sure to manually convert the images to webp for that image size.' mod='ultimateimagetool'}</li>
			  </div>
			</div>
			<div class="step">
			  <div>
			    <div class="circle">4</div>
			  </div>
			  <div>
			    <div class="title2" style="margin-bottom: 15px;">{l s='Nginx Webserver' mod='ultimateimagetool'}</div>
					<p><strong>- {l s='If you have Nginx, some extra setup settings need to be done. If you have apache or litespeed, you can skip this step.' mod='ultimateimagetool'}</strong></p>
					<p><a style="text-decoration: underline;" target="_blank" href="{$uit_module_path|escape:'htmlall':'UTF-8'}/nginx_guide.pdf">{l s='Nginx Installation Guide' mod='ultimateimagetool'}</a></p>
			  </div>
			</div>
			<div class="step">
			  <div>
			    <div class="circle">5</div>
			  </div>
			  <div>
			    <div class="title2" style="margin-bottom: 15px;">{l s='More options...' mod='ultimateimagetool'}</div>
					<p>{l s='Explore the other tabs and activate them if you want depending on your needs, but take into account that lazy load/hover images switch may not work properly depending on your theme and custom modules so be sure to test after you activated any of these features.' mod='ultimateimagetool'}</p>
			  </div>
			</div>
		</div>
    <div class="clear"></div>
</div>
<div class="clear"></div>