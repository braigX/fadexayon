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

{extends 'customer/page.tpl'}

{block name='page_title'}
  {l s='Newsletter Settings' mod='newsletterpro'}
{/block}

{block name='page_content'}
<div id="newsletterpro-my-account" class="newsletterpro-my-account clearfix">
	
	<form action="{$my_account_url}" method="POST">
		<section>
			<div class="form-group row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<h4>{l s='Set up your newsletter preferences.' mod='newsletterpro'}</h4>
				</div>
				<div class="col-md-3"></div>
			</div>

			<div class="form-group row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<span class="custom-checkbox">
						<label>
							<input name="newsletter" type="checkbox" value="1" {if $is_subscribed == 1} checked="checked" {/if}>
							<span>
								<i class="material-icons checkbox-checked">&#xE5CA;</i>
							</span>
							<span>{l s='Sign up for our newsletter' mod='newsletterpro'}</span>
						</label>
					</span>
				</div>
				<div class="col-md-3"></div>
			</div>

			{if $customer_subscribe_by_loi_active && count($list_of_interest) > 0}
				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<h4>{l s='Are you interested in:' mod='newsletterpro'}</h4>
					</div>
					<div class="col-md-3"></div>
				</div>
				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<ul class="newsletterpro-list-of-interests">
						{foreach $list_of_interest as $item}
							<li>
								<span class="custom-checkbox">
									<label>
										<input name="list_of_interest[]" type="checkbox" value="{$item.id_newsletter_pro_list_of_interest}" {if $item.checked} checked="checked" {/if}>
										<span>
											<i class="material-icons checkbox-checked">&#xE5CA;</i>
										</span>
										<span>{$item.name}</span>
									</label>
								</span>
							</li>
						{/foreach}
						</ul>
					</div>
					<div class="col-md-3"></div>
				</div>
			{/if}
	
			{if $subscribe_by_category_active}

				{if $subscribed_categories && strlen((string)$subscribed_categories) > 0}
					<input type="hidden" name="subscribed_categories" value="{$subscribed_categories}">
				{/if}

				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<h4>{l s='Choose your categories of interest:' mod='newsletterpro'}</h4>
					</div>
					<div class="col-md-3"></div>
				</div>

				<div class="form-group row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<div id="np-category-tree" class="np-category-tree"></div>
					</div>
					<div class="col-md-3"></div>
				</div>
			{/if}

			<div class="form-group row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					{hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
				</div>
				<div class="col-md-3"></div>
			</div>

		</section>
		<footer>
			<input type="hidden" name="submitNewsletterProSettings" value="1">
			<button class="btn btn-primary form-control-submit pull-xs-right" type="submit">
				{l s='Save' mod='newsletterpro'}
			</button>
		</footer>
	</form>
</div>
{/block}
