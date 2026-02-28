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
*  International Property of ZLab Solutions https://www.facebook.com/ZlabSolutions/
*
*}

<input type="hidden" name="ajaxfields" id="zlc-ajaxfields" value="{$ajaxfields|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="baseuri" id="baseuri" value="{$baseuri|escape:'htmlall':'UTF-8'}">
<input type="hidden" name="baseuri" id="zlab_ajax_link" value="{$zlab_ajax_link|escape:'htmlall':'UTF-8'}">

<form name="zlc-form" id="zlc-form" class="zlc-form">


	<ps-tabs position="top">
	    <ps-tab title="{l s='Products Positions' mod='productsindex'}" active="true" id="tab-products-index">
	        {include file="./tab_products_index.tpl"}
	    </ps-tab>
		<ps-tab title="{l s='Settings' mod='productsindex'}" id="tab-settings" icon="icon-AdminParentModules" >
		    {include file="./tab_settings.tpl"}
		</ps-tab>
		{if $debug_tab == 1}
			<ps-tab title="{l s='Debug' mod='productsindex'}" id="tab-debug">
			    {include file="./tab_debug.tpl"}
			</ps-tab>
		{/if}
	</ps-tabs>
	<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" aria-hidden="true" role="button" data-placement="left">
		<i class="icon-chevron-up" aria-hidden="true"></i>
	</a>
	{include file="./custom_context_menu.tpl"}
</form>
