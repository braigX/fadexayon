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
<div class="tab-pane panel " id="cron"  >
    <div class="panel-heading"><i class="icon-book"></i> {l s='Cron Jobs' mod='leveragebrowsercache'}</div>
		<div class="alert alert-info"><span class="alert_close"></span>
			<h2>{l s='Cron job urls' mod='leveragebrowsercache'}</h2>
			<br/> 
			{l s='Cron Products Url:' mod='leveragebrowsercache'} <a target="_blank" href="{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=products&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}">{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=products&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}</a>
			<br/>
			{l s='Cron categories Url:' mod='leveragebrowsercache'} <a target="_blank"  href="{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=categories&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}">{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=categories&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}</a>
			<br/>
			{l s='Cron Manufacturers Url:' mod='leveragebrowsercache'} <a target="_blank"  href="{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=manufacturers&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}">{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=manufacturers&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}</a>
			<br/>
			{l s='Cron Suppliers Url:' mod='leveragebrowsercache'} <a target="_blank"  href="{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=suppliers&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}">{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=suppliers&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}</a>
			<br/>
			{l s='Cron CMS Url:' mod='leveragebrowsercache'} <a target="_blank"  href="{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=cms&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}">{$rc_site|escape:'htmlall':'UTF-8'}/index.php?fc=module&module=leveragebrowsercache&controller=cms&token={$rc_token|escape:'htmlall':'UTF-8'}{if $rc_id_shop}&rc_id_shop={$rc_id_shop|escape:'htmlall':'UTF-8'}{/if}</a>
			<br/>
			<br/>
			<br/>
			<p style="color:black !important;">{l s='The running interval depends on your catalog size, calculate your cron interval based on the number of executations per running time.' mod='leveragebrowsercache'} </p>
			<p style="color:black !important;">{l s='Example: if your catalog has 1000 products, you have set to execute 3 products per execution and you set the cron job every minute, your products catalog will build in 333 minutes. ( 1000/3 * 1. FORMULA: PAGE COUNT/PER_EXECUTION * CRON TIMER )  ' mod='leveragebrowsercache'} </p>	





		</div>
    <div class="clear"></div>


	<br/><br/>
	<form method="post" action="">
    <table class="table">
	    <thead class="">
	    	<tr class="first">
				<th  class="">{l s='Type' mod='leveragebrowsercache'}</th>
				<th class="">{l s='Counter' mod='leveragebrowsercache'}</th>
				<th style="width:50%" class="">{l s='Status' mod='leveragebrowsercache'}</th>
			</tr> 
	    </thead>
	    <tbody id="samdha_warper">
	    	<tr>
	    		<td>{l s='Generate the product preview cache' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="products_preview" name="products_preview">
	    			<option value="0" {if $rc_products_preview == 0} selected="selected" {/if}>{l s='No' mod='leveragebrowsercache'}</option>
	    			<option value="1" {if $rc_products_preview == 1} selected="selected" {/if}>{l s='Yes' mod='leveragebrowsercache'}</option>
	    		</select>
	    		</td>
	    		<td>{l s='If you have the preview button enabled on your theme.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Products per execution' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="product_per_excution" name="product_per_excution">
	    			<option value="1" {if $rc_products == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $rc_products == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $rc_products == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $rc_products == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $rc_products == 5} selected="selected" {/if}>5</option>
	    			<option value="6" {if $rc_products == 6} selected="selected" {/if}>6</option>
	    			<option value="7" {if $rc_products == 7} selected="selected" {/if}>7</option>
	    			<option value="8" {if $rc_products == 8} selected="selected" {/if}>8</option>
	    			<option value="9" {if $rc_products == 9} selected="selected" {/if}>9</option>
	    			<option value="10" {if $rc_products == 10} selected="selected" {/if}>10</option>
	    			<option value="15" {if $rc_products == 15} selected="selected" {/if}>15</option>
	    			<option value="20" {if $rc_products == 20} selected="selected" {/if}>20</option>
	    			<option value="25" {if $rc_products == 25} selected="selected" {/if}>25</option>
	    			<option value="30" {if $rc_products == 30} selected="selected" {/if}>30</option>
	    			<option value="40" {if $rc_products == 40} selected="selected" {/if}>40</option>
	    			<option value="50" {if $rc_products == 50} selected="selected" {/if}>50</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or products parsed on a cron job execution.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Categories per execution' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="categories_per_excution" name="categories_per_excution">
	    			<option value="1" {if $rc_categories == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $rc_categories == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $rc_categories == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $rc_categories == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $rc_categories == 5} selected="selected" {/if}>5</option>
	    			<option value="6" {if $rc_categories == 6} selected="selected" {/if}>6</option>
	    			<option value="7" {if $rc_categories == 7} selected="selected" {/if}>7</option>
	    			<option value="8" {if $rc_categories == 8} selected="selected" {/if}>8</option>
	    			<option value="9" {if $rc_categories == 9} selected="selected" {/if}>9</option>
	    			<option value="10" {if $rc_categories == 10} selected="selected" {/if}>10</option>
	    			<option value="15" {if $rc_categories == 15} selected="selected" {/if}>15</option>
	    			<option value="20" {if $rc_categories == 20} selected="selected" {/if}>20</option>
	    			<option value="25" {if $rc_categories == 25} selected="selected" {/if}>25</option>
	    			<option value="30" {if $rc_categories == 30} selected="selected" {/if}>30</option>
	    			<option value="40" {if $rc_categories == 40} selected="selected" {/if}>40</option>
	    			<option value="50" {if $rc_categories == 50} selected="selected" {/if}>50</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or categories and category pages parsed on a cron job execution.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Manufacturers per execution' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="manufacturers_per_excution" name="manufacturers_per_excution">
	    			<option value="1" {if $rc_manufacturers == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $rc_manufacturers == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $rc_manufacturers == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $rc_manufacturers == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $rc_manufacturers == 5} selected="selected" {/if}>5</option>
	    			<option value="6" {if $rc_manufacturers == 6} selected="selected" {/if}>6</option>
	    			<option value="7" {if $rc_manufacturers == 7} selected="selected" {/if}>7</option>
	    			<option value="8" {if $rc_manufacturers == 8} selected="selected" {/if}>8</option>
	    			<option value="9" {if $rc_manufacturers == 9} selected="selected" {/if}>9</option>
	    			<option value="10" {if $rc_manufacturers == 10} selected="selected" {/if}>10</option>
	    			<option value="15" {if $rc_manufacturers == 15} selected="selected" {/if}>15</option>
	    			<option value="20" {if $rc_manufacturers == 20} selected="selected" {/if}>20</option>
	    			<option value="25" {if $rc_manufacturers == 25} selected="selected" {/if}>25</option>
	    			<option value="30" {if $rc_manufacturers == 30} selected="selected" {/if}>30</option>
	    			<option value="40" {if $rc_manufacturers == 40} selected="selected" {/if}>40</option>
	    			<option value="50" {if $rc_manufacturers == 50} selected="selected" {/if}>50</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or manufacturers parsed on a cron job execution.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='Suppliers per execution' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="suppliers_per_excution" name="suppliers_per_excution">
	    			<option value="1" {if $rc_suppliers == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $rc_suppliers == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $rc_suppliers == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $rc_suppliers == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $rc_suppliers == 5} selected="selected" {/if}>5</option>
	    			<option value="6" {if $rc_suppliers == 6} selected="selected" {/if}>6</option>
	    			<option value="7" {if $rc_suppliers == 7} selected="selected" {/if}>7</option>
	    			<option value="8" {if $rc_suppliers == 8} selected="selected" {/if}>8</option>
	    			<option value="9" {if $rc_suppliers == 9} selected="selected" {/if}>9</option>
	    			<option value="10" {if $rc_suppliers == 10} selected="selected" {/if}>10</option>
	    			<option value="15" {if $rc_suppliers == 15} selected="selected" {/if}>15</option>
	    			<option value="20" {if $rc_suppliers == 20} selected="selected" {/if}>20</option>
	    			<option value="25" {if $rc_suppliers == 25} selected="selected" {/if}>25</option>
	    			<option value="30" {if $rc_suppliers == 30} selected="selected" {/if}>30</option>
	    			<option value="40" {if $rc_suppliers == 40} selected="selected" {/if}>40</option>
	    			<option value="50" {if $rc_suppliers == 50} selected="selected" {/if}>50</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or suppliers parsed on a cron job execution.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    	<tr>
	    		<td>{l s='CMS per execution' mod='leveragebrowsercache'}</td>
	    		<td>
	    		<select id="cms_per_excution" name="cms_per_excution">
	    			<option value="1" {if $rc_cms == 1} selected="selected" {/if}>1</option>
	    			<option value="2" {if $rc_cms == 2} selected="selected" {/if}>2</option>
	    			<option value="3" {if $rc_cms == 3} selected="selected" {/if}>3</option>
	    			<option value="4" {if $rc_cms == 4} selected="selected" {/if}>4</option>
	    			<option value="5" {if $rc_cms == 5} selected="selected" {/if}>5</option>
	    			<option value="6" {if $rc_cms == 6} selected="selected" {/if}>6</option>
	    			<option value="7" {if $rc_cms == 7} selected="selected" {/if}>7</option>
	    			<option value="8" {if $rc_cms == 8} selected="selected" {/if}>8</option>
	    			<option value="9" {if $rc_cms == 9} selected="selected" {/if}>9</option>
	    			<option value="10" {if $rc_cms == 10} selected="selected" {/if}>10</option>
	    			<option value="15" {if $rc_cms == 15} selected="selected" {/if}>15</option>
	    			<option value="20" {if $rc_cms == 20} selected="selected" {/if}>20</option>
	    			<option value="25" {if $rc_cms == 25} selected="selected" {/if}>25</option>
	    			<option value="30" {if $rc_cms == 30} selected="selected" {/if}>30</option>
	    			<option value="40" {if $rc_cms == 40} selected="selected" {/if}>40</option>
	    			<option value="50" {if $rc_cms == 50} selected="selected" {/if}>50</option>
	    		</select>
	    		</td>
	    		<td>{l s='Number or CMS parsed on a cron job execution.' mod='leveragebrowsercache'}</td>
	    	</tr>
	    </tbody>
	   </table>
	   <br/>
	   <button type="submit" name="submitAddproductAndStay" class="btn btn-default"><i class="process-icon-save"></i> {l s='SAVE' mod='leveragebrowsercache'}</button>
	   </form>
</div>
<div class="clear"></div>