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
<div class="{if isset($class) && $class}{$class|escape:'html':'UTF-8'}{else}product-comment-g-recaptcha{/if}">
    {if $ETS_RV_RECAPTCHA_TYPE != 'recaptcha_v2'}
		<input id="g-recaptcha-response" class="g-recaptcha-response" name="g-recaptcha-response" type="hidden">
    {else}
		<div class="g-recaptcha" data-callback="productCommentRecaptcha"></div>
		<p class="desc_error">{l s='Captcha is required' mod='ets_reviews'}</p>
    {/if}
	<input type="hidden" name="reCaptchaFor" value="{$reCaptchaFor|escape:'html':'UTF-8'}">
</div>