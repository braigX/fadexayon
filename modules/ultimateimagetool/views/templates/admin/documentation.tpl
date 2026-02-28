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
<div class="tab-pane panel " id="documentation"  >
    <div class="panel-heading"><i class="icon-book"></i> {l s='Documentation' mod='ultimateimagetool'}</div>
		<div class="">
			<h2>{l s='Compression' mod='ultimateimagetool'}</h2>	
			{l s='To offer a better experience to your customers you can compress your entire image catalog. For the best optimization, the module uses the external service resmush.it, you can compress an unlimited number of images' mod='ultimateimagetool'}
			<br/> 
			{l s='Compressing multiple types at once is not recommended because the compression mechanism can consume a lot of resources.' mod='ultimateimagetool'}
			<br/> 
			<br/> 
			{l s='First steps' mod='ultimateimagetool'}
			<ul>
				<li>1. {l s='First you must go to the `Test image quality tab` section of this module, and you can select an image quality and press Generate Test Image,' mod='ultimateimagetool'}</li>
				<li>2. {l s='You can select an image quality and press Generate Test Image, the goal is to have the lowest Image Quality in the drop drown an image quality you are satisfied with, the lower the image quality, the greater the compression, the lower the image size and the greater the chance to loose image quality' mod='ultimateimagetool'}</li>
				<li>3. {l s='After you have found the desired image quality, go to the Compress Images tab of this module' mod='ultimateimagetool'}</li>
				<li>4. {l s='Go to each sub-section where you want to compress the images, and in the field `Image Quality` use the image quality you were satisfied in steps 1-2, in the `Test image quality tab`' mod='ultimateimagetool'}</li>
				<li>5. {l s='We recommend to compress the `Original` size only after you have compressed all the other image sizes and you have verified that the quality is acceptable, if for some reason you think the image quality is too low, you can go to the `Regenerate Images&Thumbnails` section of this module and regenerate the image sizes you want, if you compress the image size, regeneration will use the compressed image and the quality loss will be visible on all images, after the compression of the original image size, changes cannot be reverted' mod='ultimateimagetool'}</li>
				<li>5. {l s='To automatically compress future images, you must set a CRON JOB, so you must go o the `Compress Images with Cron` section of this module and get the `Cron Products URL` and set it. Note that this is available only for future images, the ones until the module installation, you must manually compress like it says in step 3-5' mod='ultimateimagetool'}</li>
				<li>6. {l s='Your browser and the page must be opened to manually compress the current images, as it uses ajax requests' mod='ultimateimagetool'}</li>
			</ul>
			<br/>
			{l s='Recommendations' mod='ultimateimagetool'}
			<ul>
				<li>{l s='After the installation, you should compress all your existing images.' mod='ultimateimagetool'}</li>
				<li>{l s='Image quality around `80` should be enough so that your pictures do not loose quality.' mod='ultimateimagetool'}</li>
				<li>{l s='Do not worry about overoptimization, if an image has been previously optimized, it will not be compressed again (as long as you use the same image quality).' mod='ultimateimagetool'}</li>
			</ul>
			<br/><br/>
			<h2>{l s='Web Conversion' mod='ultimateimagetool'}</h2>	
			{l s='To offer a better experience to your customers and rank better in google page speed, you can use next gen image format WEBP, webp images are 20-25% smaller than traditional image formats' mod='ultimateimagetool'}
			<br/> 
			{l s='Compressing multiple types at once is not recommended because the compression mechanism can consume a lot of resources.' mod='ultimateimagetool'}
			<br/> 
			<br/> 
			{l s='First steps' mod='ultimateimagetool'}
			<ul>
				<li>1. {l s='First you must go to the `WebP Conversion` section of this module, and you can select an image quality' mod='ultimateimagetool'}</li>
				<li>2. {l s='Go to each sub-section where you want to compress the images, and in the field `Image Quality` select the quality you want' mod='ultimateimagetool'}</li>
				<li>3. {l s='Your browser and the page must be opened to manually convert the current images to webp, as it uses ajax requests' mod='ultimateimagetool'}</li>
			</ul>
			<br/>
			{l s='Good to know' mod='ultimateimagetool'}
			<ul>
				<li>{l s='The module creates extra copies of the JPG images, so there is no danger of breaking your current images.' mod='ultimateimagetool'}</li>
				<li>{l s='You can regenerate the webp images, as many times as you want, with different image qualities' mod='ultimateimagetool'}</li>
				<li>{l s='If a picture does not have a webp image format generated, the jpg version is served so there is no risk of getting penalized' mod='ultimateimagetool'}</li>
				<li>{l s='Only product, category, supplier, manufacturer images can be converted to webp' mod='ultimateimagetool'}</li>
				<li>{l s='Future images are automatically converted to webp, so you do not have to rebuild them periodically, as long as the `Enable WebP images` option from this page is enabled.' mod='ultimateimagetool'}</li>
				<li>{l s='If you have changed a specific image size width or height make sure to manually convert the images to webp for that image size.' mod='ultimateimagetool'}</li>
			</ul>
			<br/><br/>
			<h2>{l s='Regenerate Images & Thumbnails' mod='ultimateimagetool'}</h2>	
				{l s='Prestashop usually timesout when regenerating a large number of images, you can rebuild it here without having to worry about that' mod='ultimateimagetool'}
			<br/> 
			<br/> 
			{l s='First steps' mod='ultimateimagetool'}
			<ul>
				<li>1. {l s='First you must go to the `Regenerate Images & Thumbnails` section of this module' mod='ultimateimagetool'}</li>
				<li>2. {l s='Select the Image Type and Image Size' mod='ultimateimagetool'}</li>
				<li>3. {l s='Press `Regenerate image sizes` and wait until it finishes' mod='ultimateimagetool'}</li>
			</ul>
			<br/><br/>
			<h2>{l s='Delete' mod='ultimateimagetool'}</h2>	
				{l s='After you deactivate or stop using some image sizes, free server space by removing them because prestashop does not delete those images' mod='ultimateimagetool'}
			<br/> 
			<br/> 
			{l s='First steps' mod='ultimateimagetool'}
			<ul>
				<li>1. {l s='First you must go to the `Delete` section of this module' mod='ultimateimagetool'}</li>
				<li>2. {l s='Select the Image Type and Image Size' mod='ultimateimagetool'}</li>
				<li>3. {l s='Press `Delete image` and wait until it finishes' mod='ultimateimagetool'}</li>
			</ul>
			<br/>
			{l s='Good to know' mod='ultimateimagetool'}
			<ul>
				<li>{l s='If you delete an image size by mistake, just go to the `Regenerate Images & Thumbnails` section and rebuild it' mod='ultimateimagetool'}</li>
			</ul>
			<br/><br/>
			<h2>{l s='Alt tags' mod='ultimateimagetool'}</h2>	
				{l s='Alt tags, tell people and search bots what the image is about, so it is recommended that all your images have alt tags' mod='ultimateimagetool'}
			<br/> 
			<br/> 
			{l s='First steps' mod='ultimateimagetool'}
			<ul>
				<li>1. {l s='First you must go to the `Alt tags` section of this module' mod='ultimateimagetool'}</li>
				<li>2. {l s='In the `Alt Tag Syntax` field, you must define the pattern you want your images to have ' mod='ultimateimagetool'}</li>
				<li>3. {l s='In the `Apply to` field, you must select to which  images the current condition will be applied' mod='ultimateimagetool'}</li>
				<li>4. {l s='In the `Auto apply to newly added products` field, you can select if the defined pattern will automatically be applied to future product images without having to regenerate the tags ' mod='ultimateimagetool'}</li>
				<li>5. {l s='Press `Generate tags now` and wait until it finishes' mod='ultimateimagetool'}</li>
			</ul>
			<br/>
			{l s='Good to know' mod='ultimateimagetool'}
			<ul>
				<li>{l s='Having alt tags for all your images, can boost your google ranking and traffic' mod='ultimateimagetool'}</li>
			</ul>
		</div>
    <div class="clear"></div>
</div>
<div class="clear"></div>