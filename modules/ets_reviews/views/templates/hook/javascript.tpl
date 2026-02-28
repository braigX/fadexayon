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

{if !$back_office && $ETS_RV_RECAPTCHA_ENABLED && (!$isLogged || !$ETS_RV_RECAPTCHA_USER_REGISTERED)}
	<script src="https://www.google.com/recaptcha/api.js?hl={$language_code|escape:'html':'UTF-8'}{if $ETS_RV_RECAPTCHA_TYPE != 'recaptcha_v2'}&render={$ETS_RV_RECAPTCHA_SITE_KEY|escape:'html':'UTF-8'}{else}&onload=productCommentOnloadReCaptcha&render=explicit{/if}" async defer></script>
	<script type="text/javascript">
		var productCommentOnloadReCaptcha = function () {
		    reCaptchaETS.onLoad();
        };
        var productCommentRecaptcha = function () {
            ETS_RV_RECAPTCHA_VALID = 1;
            $('.g-recaptcha.error').removeClass('error');
        }
	</script>
{/if}