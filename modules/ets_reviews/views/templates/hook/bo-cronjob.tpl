{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $ETS_RV_OVERTIME > 0 || $ETS_RV_LAST_CRONJOB}
	<div class="ets_rv_cronjobs panel">
		<h3 class="ets_rv_title">{l s='Cronjob notification' mod='ets_reviews'}</h3>
		{if $ETS_RV_OVERTIME > 0}
			<div class="cronjob alert alert-danger">{l s='It has been 12 hours since the last time Cronjob was executed. Automated emails may not be sent!' mod='ets_reviews'} <a href="{if isset($automationLink)}{$automationLink nofilter}{else}#{/if}">{l s='Configure cronjob' mod='ets_reviews'}</a></div>
		{elseif $ETS_RV_LAST_CRONJOB}
			<div class="cronjob alert alert-info">
				<span>{l s='The last time Cronjob was executed: %s ago' sprintf=[$ETS_RV_LAST_CRONJOB] mod='ets_reviews'}. <a href="{if isset($automationLink)}{$automationLink nofilter}{else}#{/if}">{l s='Configure cronjob' mod='ets_reviews'}</a></span>
			</div>
		{/if}
	</div>
{/if}