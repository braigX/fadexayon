{module->_showInfo text="{l s='If you click on the button below, you will undo every changes made to your products/cms/editorial content.' mod='pm_seointernallinking'}<br />{l s='This mean that all the SEO optimization that was made will be lost until you run another optimization.' mod='pm_seointernallinking'}<br /><br />{l s='You are warned !' mod='pm_seointernallinking'}"}

{include file="../../core/clear.tpl"}

<div id="deleteAllContainer">
	<h4>{l s='Undo all changes made by this module' mod='pm_seointernallinking'}</h4>

	{sil_button text={l s='Undo changes' mod='pm_seointernallinking'} href="{$base_config_url|sil_nofilter}&removeAllLinks=1" onclick=false icon_class='ui-button-text-only' class='removealllinks' title={l s='Do you really want to remove all the links ?' mod='pm_seointernallinking'}}

	<p id="progressDeleteAllInformation" style="display:none;">
		{l s='Removing links process is in progress... Please wait, it can take some minutes (depends on your catalog size and the number of CMS pages)' mod='pm_seointernallinking'}
	</p>
</div>