{*
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*}

{capture name=path}
<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Subscribe at our newsletter'  mod='newsletterpro'}
{/capture}

<div id="newsletterpro-subscribe">
{include file="$tpl_dir./errors.tpl"}

<div class="pqnp-popup-subscription-container">
	<div class="pqnp-subscription-controller-content"></div>
</div>
