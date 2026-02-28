<div id="globalSyncContainer" style="text-align: center">
	{sil_button text={l s='Optimize everything' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseEverything=1" onclick=false icon_class='ui-button-text-only' class='synchroniseeverything' title={l s='Do you really want to optimize everything ?' mod='pm_seointernallinking'}}
	<p id="progressSyncAllInformation" style="display:none;"></p>
</div>

{include file="../../core/clear.tpl"}

<div id="syncProductContainer">
	<h4>{l s='Optimize all products' mod='pm_seointernallinking'}</h4>
	{sil_button text={l s='Optimize' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseAllProducts=1" onclick=false icon_class='ui-button-text-only' class='synchroniseallproducts' title={l s='Do you really want to optimize all products ?' mod='pm_seointernallinking'}}
	<p id="progressSyncProductInformation" style="display:none;">{l s='Products linking process is in progress... Please wait, it can take some minutes (depends on the size of your catalog)' mod='pm_seointernallinking'}</p>
	<p class="progressBar" id="progressSyncProduct" style="display:none;"><span><em style="left:0px;"></em></span></p>
	<p id="progressSyncProductRemainingTime" style="display:none;"></p>
</div>

{include file="../../core/clear.tpl"}

<div id="syncCMSPagesContainer">
	<h4>{l s='Optimize all CMS pages' mod='pm_seointernallinking'}</h4>	
	{sil_button text={l s='Optimize' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseAllCMSPages=1" onclick=false icon_class='ui-button-text-only' class='synchroniseallcmspages' title={l s='Do you really want to optimize all CMS pages ?' mod='pm_seointernallinking'}}
	<p id="progressSyncCMSPagesInformation" style="display:none;">{l s='CMS Pages linking process is in progress... Please wait, it can take some minutes (depends on the number of pages)' mod='pm_seointernallinking'}</p>
	<p class="progressBar" id="progressSyncCMSPages" style="display:none;"><span><em style="left:0px;"></em></span></p>
	<p id="progressSyncCMSPagesRemainingTime" style="display:none;"></p>
</div>

{include file="../../core/clear.tpl"}

<div id="syncCategoriesContainer">
	<h4>{l s='Optimize all categories' mod='pm_seointernallinking'}</h4>
	{sil_button text={l s='Optimize' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseAllCategories=1" onclick=false icon_class='ui-button-text-only' class='synchroniseallcategories' title={l s='Do you really want to optimize all categories ?' mod='pm_seointernallinking'}}
	<p id="progressSyncCategoriesInformation" style="display:none;">{l s='Categories linking process is in progress... Please wait, it can take some minutes (depends on the number of categories)' mod='pm_seointernallinking'}</p>
	<p class="progressBar" id="progressSyncCategories" style="display:none;"><span><em style="left:0px;"></em></span></p>
	<p id="progressSyncCategoriesRemainingTime" style="display:none;"></p>
</div>

{include file="../../core/clear.tpl"}

<div id="syncManufacturersContainer">
	<h4>{l s='Optimize all manufacturers' mod='pm_seointernallinking'}</h4>
	{sil_button text={l s='Optimize' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseAllManufacturers=1" onclick=false icon_class='ui-button-text-only' class='synchroniseallmanufacturers' title={l s='Do you really want to optimize all manufacturers ?' mod='pm_seointernallinking'}}
	<p id="progressSyncManufacturersInformation" style="display:none;">{l s='Manufacturers linking process is in progress... Please wait, it can take some minutes (depends on the number of manufacturers)' mod='pm_seointernallinking'}</p>
	<p class="progressBar" id="progressSyncManufacturers" style="display:none;"><span><em style="left:0px;"></em></span></p>
	<p id="progressSyncManufacturersRemainingTime" style="display:none;"></p>
</div>

{include file="../../core/clear.tpl"}

<div id="syncEditorialContainer{if !$homePageManagementModuleInstalled} inactive{/if}">
	<h4>{l s='Optimise module' mod='pm_seointernallinking'} "{$homePageManagementModuleName}"</h4>
	{if $homePageManagementModuleInstalled}
		{sil_button text={l s='Optimize' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&synchroniseEditorial=1" onclick=false icon_class='ui-button-text-only' class='synchroniseeditorial' title={l s='Do you really want to optimize editorial module ?' mod='pm_seointernallinking'}}
		<p id="progressSyncEditorialInformation" style="display:none;">{l s='Editorial linking process is in progress... Please wait until this text disappear' mod='pm_seointernallinking'}</p>
		<p class="progressBar" id="progressSyncEditorial" style="display:none;"><span><em style="left:0px;"></em></span></p>
	{else}
		{module->_showWarning text="{l s='The module isn\'t installed.' mod='pm_seointernallinking'}"}
	{/if}
</div>