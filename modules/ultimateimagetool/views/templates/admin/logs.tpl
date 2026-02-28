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
<div class="tab-pane panel " id="logs"  >
    <div class="panel-heading"><i class="icon-tasks"></i> {l s='Compression logs' mod='ultimateimagetool'}</div>
		
	<div class="col-xs-12 nopadding table-responsive-row clearfix">


	<strong>{l s='Saved space' mod='ultimateimagetool'}</strong>
		<table   class="table table-hover table-striped">
			<tr>
				<td>{l s='Products' mod='ultimateimagetool'}: {$total_saved_space_products|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
				<td>{l s='Categories' mod='ultimateimagetool'}: {$total_saved_space_categories|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
				<td>{l s='Suppliers' mod='ultimateimagetool'}: {$total_saved_space_suppliers|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
				<td>{l s='Manufacturers' mod='ultimateimagetool'}: {$total_saved_space_manufacturers|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
				<td>{l s='Theme' mod='ultimateimagetool'}: {$total_saved_space_theme|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
				<td>{l s='Module' mod='ultimateimagetool'}: {$total_saved_space_module|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}</td>
			</tr>
		</table>
	   <div class="clear"></div>

	   <br/>
	   	<p>{l s='Latest processed images are shown first' mod='ultimateimagetool'}</p>
	   	<br/>
	   <table id="table-detail-optimization" class="table table-hover table-striped">
	      <thead>
	         <tr>
	            <th><font><font>{l s='#' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Object ID' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Object type' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Image' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Image type' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Original size' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Compressed size' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Processed' mod='ultimateimagetool'}</font></font></th>
	            <th><font><font>{l s='Date' mod='ultimateimagetool'}</font></font></th>
	         </tr>
	      </thead>
	      <tbody class="logs_inner">
	      {if $logs}
	      	 {foreach from=$logs item=log}
		         <tr class="process_{$log.processed|escape:'htmlall':'UTF-8'}">
		            <td><font><font>{$log.id|escape:'htmlall':'UTF-8'}</font></font></td>
		            <td>{if isset($log.link)}<font><font><a target="_blank" href="{$log.link|escape:'htmlall':'UTF-8'}">{$log.object_id|escape:'htmlall':'UTF-8'}</a></font></font>{/if}</td>
		            <td><font>{$log.object_type|escape:'htmlall':'UTF-8'}</font></td>
		            <td>{if isset($log.image)}{if $log.image != false}<a href="{$log.image|escape:'htmlall':'UTF-8'}" target="_blank"><img height="40" style="    max-height: 30px; max-width: 50px; width: auto; height: auto; " width="auto" src="{$log.image|escape:'htmlall':'UTF-8'}" /></a>{/if}{/if}</td>
		            <td>{if $log.image_size == ""} {l s='Original' mod='ultimateimagetool'}{else} <span title="{$log.image_size|escape:'htmlall':'UTF-8'}">{$log.image_size|escape:'htmlall':'UTF-8'|truncate:25}</span>{/if}</td>
		            <td>{if $log.original_size == 0}-{else}{$log.original_size|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}{/if}</td>
		            <td>{if $log.new_size == 0}-{else}{$log.new_size|escape:'htmlall':'UTF-8'} {l s='Kb' mod='ultimateimagetool'}{/if}</td>	
		            <td>{if $log.processed == 1}{l s='Yes' mod='ultimateimagetool'}{else}{l s='No' mod='ultimateimagetool'}{/if}</td>		
		            <td>{$log.date_add|escape:'htmlall':'UTF-8'}</td>	           
		         </tr>
	         {/foreach}
	       {/if}
	      </tbody>
	   </table>
	   {if $log_pages}
	   <div class="paginator col-md-12 text-left">
	      <ul class="pagination">
	     	 {for $foo=1 to $log_pages}
	         	<li class="item_pagination"><a href="#" class="switch_logs" page="{$foo|escape:'htmlall':'UTF-8'}"><font><font>{$foo|escape:'htmlall':'UTF-8'}</font></font></a></li>
	         {/for}
	      </ul>
	     
	   </div>
	   {/if}
	</div>


    <div class="clear"></div>
</div>
<div class="clear"></div>