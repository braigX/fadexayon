{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="tab-pane panel active" id="regenerate"  >
    <div class="panel-heading"><i class="icon-refresh"></i> {l s='Cache warmer Regenerate & Leverage Cache - - Speed up website' mod='leveragebrowsercache'}</div>

		

		<div class="alert alert-info"><span class="alert_close"></span>
			{l s='Select the pages you want to regenerate.' mod='leveragebrowsercache'}
			<br/> 
			{l s='Just press the' mod='leveragebrowsercache'} <button><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button> {l s='at the start of a line to start regenerating any of the following types.' mod='leveragebrowsercache'}
			<br/>
			{l s='Make sure you regenerate the cache every time you clear it so your visitors will have a good experience every time they visit your website.' mod='leveragebrowsercache'}
			<br/>
			{l s='In some cases it`s best not to run multiple type regenerations in the same type because the hosting may think it`s being attack. So test to make sure' mod='leveragebrowsercache'}
			<br/>
			{l s='All regenerations use 3x Parallel Requests so it`s highly recommend regenerating them one by one' mod='leveragebrowsercache'}
		</div>





    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='leveragebrowsercache'}</th>
				<th class="">{l s='Actions' mod='leveragebrowsercache'}</th>
				<th style="width:50%" class="">{l s='Status' mod='leveragebrowsercache'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper">
	    	<tr>
	    		<td>{l s='Infinity loop' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="infinite_loop">
	    			<option value="0">No - infinite loop</option>
	    			<option value="1">Yes - Infinite loop</option>
	    		</select>
	    		</td>
	    		<td>{l s='If infinite loop is active, the regeneration process will restart once reaching the end.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Max parallel requests' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="max_parallel">
	    			<option value="1">1</option>
	    			<option value="2">2</option>
	    			<option value="3" selected="selected" >3</option>
	    			<option value="4">4</option>
	    			<option value="5">5</option>
	    			<option value="6">6</option>
	    			<option value="7">7</option>
	    			<option value="8">8</option>
	    			<option value="9">9</option>
	    			<option value="10">10</option>
	    		</select>
	    		</td>
	    		<td>{l s='Using many request may cause shared hosting to temporary block the IP.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Generate the product preview cache' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="products_preview_p" name="products_preview_p">
	    			<option value="0" {if $rc_products_preview_p == 0} selected="selected" {/if}>{l s='No' mod='leveragebrowsercache'}</option>
	    			<option value="1" {if $rc_products_preview_p == 1} selected="selected" {/if}>{l s='Yes' mod='leveragebrowsercache'}</option>
	    		</select>
	    		</td>
	    		<td>{l s='If you have the preview button enabled on your theme.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Products' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_product_current" value="0">
	    			<input type="hidden" id="hidden_product_total" value="{$products_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_productplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_productstop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="product_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="products_max">{$products_count|escape:'htmlall':'UTF-8'}</span></div><div class="product_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>
			<tr>
	    		<td>{l s='Categories' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_category_current" value="0">
	    			<input type="hidden" id="hidden_category_total" value="{$categories_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_categoryplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_categorystop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="categories_max">{$categories_count|escape:'htmlall':'UTF-8'}</span></div><div class="category_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>
			<tr>
	    		<td>{l s='Categories & All category pages' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_category_pages_current" value="0">
	    			<input type="hidden" id="hidden_category_pages_total" value="{$categories_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_categorypagesplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_categorypagesstop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="category_pages_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="categories_pages_max">{$categories_count|escape:'htmlall':'UTF-8'}</span></div><div class="category_pages_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>
			<tr>
	    		<td>{l s='Manufacturers' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_manufacturer_current" value="0">
	    			<input type="hidden" id="hidden_manufacturer_total" value="{$manufacturers_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_manufacturerplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_manufacturerstop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="manufacturer_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="manufacturers_max">{$manufacturers_count|escape:'htmlall':'UTF-8'}</span></div><div class="manufacturer_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>

			<tr>
	    		<td>{l s='Suppliers' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_supplier_current" value="0">
	    			<input type="hidden" id="hidden_supplier_total" value="{$suppliers_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_supplierplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_supplierstop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="supplier_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="supplier_max">{$suppliers_count|escape:'htmlall':'UTF-8'}</span></div><div class="supplier_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>


			<tr>
	    		<td>{l s='CMS' mod='leveragebrowsercache'}</td>
	    		<td>
	    			<input type="hidden" id="hidden_cms_current" value="0">
	    			<input type="hidden" id="hidden_cms_total" value="{$cms_count|escape:'htmlall':'UTF-8'}">
	    			<button class="leveragebrowsercache_cmsplay"><span class="ui-button-icon-primary ui-icon ui-icon-play"></span></button>
	    			<button class="leveragebrowsercache_cmsstop"><span class="ui-button-icon-primary ui-icon ui-icon-stop"></span></button>
				</td>
	    		<td>
	    			
	    			<div class="progress ui-progressbar ui-widget ui-widget-content ui-corner-all" ><div class="text"> <span class="cms_current">0</span> {l s='of' mod='leveragebrowsercache'} <span id="cms_max">{$cms_count|escape:'htmlall':'UTF-8'}</span></div><div class="cms_progress  ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div>
	    		</td>
	    	</tr>
	    </tbody>

	    	
    </table> 

</div>
