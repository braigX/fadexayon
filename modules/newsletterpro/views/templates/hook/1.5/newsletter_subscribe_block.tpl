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

<!-- Newsletter Pro Subscribe Footer-->
<div class="clearfix pqnp-subscribe-block {if isset($display_hook)} {$display_hook|escape:'html':'UTF-8'} {/if} np-footer-section-sm">
	<div>
		<h4 class="text-uppercase block-contact-title">{l s='Newsletter' mod='newsletterpro'}</h4>
		<div class="pqnp-subscribe-container category_footer toggle-footer">
			<div class="block_content">
				<div class="form-group np-input-email clearfix">
					<input class="inputNew form-control grey newsletter-input pqnp-email-address" type="text" name="email" size="18" placeholder="{l s='Enter your e-mail' mod='newsletterpro'}">
		            <a href="javascript:{}" name="newsletterProSubscribe" class="btn btn-primary pull-xs-right hidden-xs-down pqnp-subscribe-button">
		            	{l s='Subscribe' mod='newsletterpro'}
		            </a>
					<input type="hidden" name="action" value="0">
				</div>
			</div>
		</div>
	</div>
</div>
<!-- /Newsletter Pro Subscribe Footer -->
