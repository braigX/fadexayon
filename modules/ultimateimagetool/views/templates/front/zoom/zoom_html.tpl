{*
* Advanced Plugins
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Advanced Plugins
*  @copyright  Advanced Plugins SA
*}
<div id="zoom_html">
	<div class="uit-gallery {if $productImagesFull|@count > 1}uit-bx-slider{/if}" itemscope itemtype="http://schema.org/ImageGallery" id="thumbnails">
		{foreach from=$productImagesFull item=image}
			<figure itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
		        <a href="{$image.full|escape:'htmlall':'UTF-8'}" data-thumb="{$image.normal|escape:'htmlall':'UTF-8'}" itemprop="contentUrl">
		            <img data-lazy="false" class="zoomimg"  {if $productImagesFull|@count == 1}style="display:block" {/if} src="{$image.normal|escape:'htmlall':'UTF-8'}" alt="{$image.legend|escape:'htmlall':'UTF-8'}">
		        </a>
		   	</figure>
		{/foreach}
	</div>
	{if $thumbnail_size && $thumb_info_small}
		<div class="uit-gallery-thumbs-container"><ul id="uit-gallery-thumbs" class="uit-gallery-thumbs-list" style="display:none">
			{foreach from=$productImagesFull item=image}
				<li class="thumb-item thumb-container"><div class="thumb"><a href=""><img width="80" class="zoomimgthumb"   src="{$image.thumb|escape:'htmlall':'UTF-8'}" alt="{$image.legend|escape:'htmlall':'UTF-8'}"></a></div></li>
			{/foreach}
		</ul></div>


	{/if}
</div>