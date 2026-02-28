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
<script type="text/javascript">
	var ets_ct7_recaptcha_enabled = {if isset($rc_enabled) && $rc_enabled}1{else}0{/if};
	{if isset($rc_enabled) && $rc_enabled}
	var ets_ct7_recaptcha_v3 = {$rc_v3|intval};
	var ets_ct7_recaptcha_key = "{$rc_key|escape:'html':'UTF-8'}";
	{/if}
</script>
{if isset($rc_enabled) && $rc_enabled}
	<script src="https://www.google.com/recaptcha/api.js?hl={$iso_code|escape:'html':'UTF-8'}{if !$rc_v3}&onload=recaptchaCallback&render=explicit{/if}"></script>
{/if}
{if $preview}
    <script src="https://www.google.com/recaptcha/api.js"></script>
{/if}
<div class="wpcf7-form-control-wrap">
    <div {foreach from=$atts key='key' item='item'} {if $item}{$key|escape:'html':'UTF-8'}="{$item|escape:'html':'UTF-8'}"{/if} {/foreach}>
    </div>
    {$html nofilter}
</div>