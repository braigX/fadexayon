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

<a class="list-action-enable{if $sendmail} action-enabled{else} action-disabled{/if}" href="{$href|escape:'html':'UTF-8'}" title="{if $sendmail}{l s='Enabled' mod='ets_reviews'}{else}{l s='Disabled' mod='ets_reviews'}{/if}">
	<i class="icon-check{if !$sendmail} hidden{/if}"></i>
	<i class="icon-remove{if $sendmail} hidden{/if}"></i>
</a>
