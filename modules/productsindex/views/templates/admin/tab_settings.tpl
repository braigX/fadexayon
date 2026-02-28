{*
* 2007 - 2017 ZSolutions
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
* Do not edit or add to this file if you wish to upgrade module to newer
* versions in the future. If you wish to customize module for your
* needs please contact developer at http://zlabsolutions.com for more information.
*
*  @author    Eugene Zubkov <magrabota@gmail.com>
*  @copyright 2018 ZLab Solutions
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of ZLab Solutions https://www.facebook.com/ZlabSolutions/
*
*}

<h3 class="modal-title">{l s='View Products Settings' mod='productsindex'}</h3>
<!--
<label class="control-label col-lg-3" for="image_size">
	{l s='Image size' mod='productsindex'}
</label>
<div class="col-lg-9">
	<select name="image_size" class="image_size" data-id="14" >
		<option value="50">50</option>
		<option value="75">75</option>
		<option  value="100">100</option>
		<option  value="150">150</option>
		<option  value="200">200</option>
		<option  value="300">300</option>
		<option  value="350">350</option>
	</select>
</div>
-->
<br style="clear:both; margin-top: 2px;" />
<br>

<label class="control-label col-lg-3" for="image-type-option">
	{l s='Search results image type' mod='productsindex'}
</label>
<div class="col-lg-9">
	<select class="image-type-option" name="image-type-option" data-id="2">
	{foreach from=$image_types item=image_type}
		<option value="{$image_type['name']|escape:'htmlall':'UTF-8'}" {if $image_type['name']==$settings[2][1]} selected{/if}>
			{$image_type['name']|escape:'htmlall':'UTF-8'} - {$image_type['width']|escape:'htmlall':'UTF-8'}x{$image_type['height']|escape:'htmlall':'UTF-8'}
		</option>
	{/foreach}
	</select>
</div>
<div style="clear:both;"></div>
<br>
<label class="control-label col-lg-3" for="PS_PRODUCTS_ORDER_BY">
	<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='The order in which products are displayed in the product list' mod='productsindex'}" data-html="true">
		{l s='Default order by' mod='productsindex'}
	</span>
</label>

<div class="col-lg-9">
	<select class="PS_PRODUCTS_ORDER_BY" name="PS_PRODUCTS_ORDER_BY" data-id="100">default_order
		<option value="0" {if $default_order==0} selected{/if}>{l s='Product name' mod='productsindex'}</option>
		<option value="1" {if $default_order==1} selected{/if}>{l s='Product price' mod='productsindex'}</option>
		<option value="2" {if $default_order==2} selected{/if}>{l s='Product add date' mod='productsindex'}</option>
		<option value="3" {if $default_order==3} selected{/if}>{l s='Product modified date' mod='productsindex'}</option>
		<option value="4" {if $default_order==4} selected{/if}>{l s='Position inside category' mod='productsindex'}</option>
		<option value="5" {if $default_order==5} selected{/if}>{l s='Brand' mod='productsindex'}</option>
		<option value="6" {if $default_order==6} selected{/if}>{l s='Product quantity' mod='productsindex'}</option>
		<option value="7" {if $default_order==7} selected{/if}>{l s='Product reference' mod='productsindex'}</option>
	</select>
</div>
<div style="clear:both;"></div>

<ps-switch name="hide_disabled" label="{l s='Hide disabled products from list' mod='productsindex'}" yes="{l s='Yes' mod='productsindex'}" no="{l s='No' mod='productsindex'}" {if $settings[4][1]==1} active="true"{/if} data-id="4"></ps-switch>
<div style="clear:both;"></div>

<ps-switch name="move_disabled" label="{l s='Move disabled products to the end' mod='productsindex'}" hint="{l s='Move disabled products to the end of list' mod='productsindex'}" yes="{l s='Yes' mod='productsindex'}" no="{l s='No' mod='productsindex'}" {if $settings[5][1]==1} active="true"{/if} data-id="5"></ps-switch>
<div style="clear:both;"></div>

<br>
<label class="control-label col-lg-3" for="PS_PRODUCTS_ORDER_BY">
	<span title=""  data-original-title="{l s='The order in which products are displayed in the product list' mod='productsindex'}" data-html="true">
		{l s='Schedule Out of Stock Category Index Rebuild' mod='productsindex'}
	</span>
</label>

<div class="col-lg-9">
	<p style="padding-top:8px;">
		{l s='Use this ' mod='productsindex'}
		<a href="/modules/productsindex/rebuild_index_by_stock.php?action=run_rebuild&token=AZ6ysYQWY9cr6Zntyg6PKGWNKOwkcileo2npdjtXpVY">{l s='link' mod='productsindex'}</a>
		{l s=' if you want to move out of stock products in categories to the end of list. You can run it in cron' mod='productsindex'}.
	</p>

</div>
<div style="clear:both;"></div>

