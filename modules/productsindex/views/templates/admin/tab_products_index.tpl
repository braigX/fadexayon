{*
* 2007 - 2018 ZLabSolutions
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
*  International Property of ZLab Solutions https://zlabsolutions.com
*
*}

<!--Update Products-->
<br>
<br style="clear:both">

<label class="control-label col-lg-3 " for="what_to_edit">
	{l s='What to edit?' mod='productsindex'}
</label>
<div class="col-lg-9">
	<select class="what_to_edit" name="what_to_edit">
		<option value="1">{l s='Categories' mod='productsindex'}</option>
		<option value="2">{l s='Brands (Manufacturers)' mod='productsindex'}</option>
	</select>
</div>
<br style="clear:both; margin-top: 5px;" />
<br>

<br>
<div class="chosen-categories hide">
	<label class="control-label col-lg-3 " for="categoryselector">
		{l s='Categories' mod='productsindex'}
	</label>
	<div class="col-lg-9">
		<select class="categoryselector" name="categoryselector">
			<option value="0" selected disabled>{l s='Please select a category' mod='productsindex'}</option>
			{foreach from=$categories item=category}
				<option value="{$category['id_option']|escape:'htmlall':'UTF-8'}">{$category['id_option']|escape:'htmlall':'UTF-8'} {$category['name']|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
	</div>
</div>
<div class="chosen-brands hide">
	<label class="control-label col-lg-3 " for="brandselector">
		{l s='Brands' mod='productsindex'}
	</label>
	<div class="col-lg-9">
		<select class="brandselector" name="brandselector">
			<option value="0" selected disabled>{l s='Please select a brand' mod='productsindex'}</option>
			{foreach from=$manufacturers item=manufacturer}
				<option value="{$manufacturer['id_option']|escape:'htmlall':'UTF-8'}">
					 {$manufacturer['name']|escape:'htmlall':'UTF-8'} - {$manufacturer['id_option']|escape:'htmlall':'UTF-8'}
				</option>
			{/foreach}
		</select>
	</div>
</div>
<br style="clear:both; margin-top: 0px;" />
<hr>
<br>

<label class="control-label col-lg-3 " for="sortselector">
	{l s='Sort products by' mod='productsindex'}
</label>
<div class="col-lg-9">
	<select class="sortselector" name="sortselector">
		<option value="0" selected>{l s='Position' mod='productsindex'}</option>
		<option value="1" >{l s='Stock (quantity)' mod='productsindex'}</option>
		<option value="2" >{l s='Name' mod='productsindex'}</option>
		<!--<option value="3" >{l s='Bestseller' mod='productsindex'}</option>-->
		<option value="4" >{l s='Price' mod='productsindex'}</option>
		<option value="5" >{l s='Date update' mod='productsindex'}</option>
		<option value="6" >{l s='ID' mod='productsindex'}</option>
	</select>

	<div id="sortway-container " class="chosen-container massa_parameters_edit_container">
		<select class="sortway" name="sortway">
			<option value="1" selected>{l s='Ascending' mod='productsindex'}</option>
			<option value="2">{l s='Descending' mod='productsindex'}</option>
		</select>
	</div>
</div>
<br style="clear:both; margin-top: 5px;" />
<br>

<label class="control-label col-lg-3" for="sync-all-products">
</label>
<div class="col-lg-9">
	<input type="button" name="search-products" class="btn btn-default btn-block" id="search-products" value="{l s='Show Products' mod='productsindex'}">
</div>
<div style="clear:both;"></div>
<br>

<!--Products sheet-->
<ps-table header="{l s='Products' mod='productsindex'}" icon="icon-users" id="products-filter" content="{$productsfound|escape:'htmlall':'UTF-8'}" no-items-text="{l s='No products found' mod='productsindex'}"></ps-table>
<br>
<br>
<br>
<label class="control-label col-lg-3" for="sync-all-products">
</label>
<div class="col-lg-9">
	<input type="button" name="apply-index" class="btn btn-default btn-block" id="apply-index" value="{l s='Save New Positions' mod='productsindex'}">
</div>
<div style="clear:both;"></div>
<br>