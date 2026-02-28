{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}


{literal}
<script>
	ps_version = '{/literal}{$ps_version|intval}{literal}';
	multishop = '{/literal}{$multishop|intval}{literal}';
	debug_mode = '{/literal}{$debug_mode|intval}{literal}';
	current_id_tab = '{/literal}{$current_id_tab|intval}{literal}';
	admin_module_ajax_url = '{/literal}{$controller_url|escape:'quotes':'UTF-8'}{literal}';
	admin_module_controller = "{/literal}{$controller_name|escape:'htmlall':'UTF-8'}{literal}";
{/literal}
	next_message = '{l s=' Next' mod='seoimg' js=1}';
	prev_message = '{l s=' Back' mod='seoimg' js=1}';
	skip_message = '{l s=' Skip' mod='seoimg' js=1}';
	save_message = '{l s=' Save' mod='seoimg' js=1}';
	close_message = '{l s='Close' mod='seoimg' js=1}'; 
	delete_message = '{l s='Delete' mod='seoimg' js=1}';
	delete_rule_message = '{l s='Are you sure you want to delete this rule?' mod='seoimg' js=1}';

	records_msg = '{l s='Show' mod='seoimg' js=1}';
	zero_records_msg = '{l s='Nothing found' mod='seoimg' js=1}';
</script>

{if $ps_version == 0}
<div class="bootstrap">
	<!-- Beautiful header -->
	{include file="./header.tpl"}
{/if}
	<!-- Module content -->
	<div id="modulecontent" class="clearfix">

		{if $module_enabled|intval == 0}
			<div class="alert alert-warning">
				<h4>{l s='There is 1 warning' mod='seoimg'}</h4>
				<ul class="list-unstyled">
					<li>
						<a href="{$admin_seo|escape:'htmlall':'UTF-8'}">
							{l s='The module is not enabled, no rules will be applied' mod='seoimg'}
						</a>
					</li>
				</ul>
			</div>
		{/if}

		<!-- Nav tabs -->
		<div class="col-lg-2">
			<div class="list-group">
				<a href="#documentation" class="list-group-item active" data-toggle="tab">
					<i class="icon-book"></i> {l s='Documentation' mod='seoimg'}
				</a>
				<a href="#metas" class="list-group-item" data-toggle="tab">
					<i class="icon-indent" data-target="table-metas-1"></i> {l s='Optimize Images' mod='seoimg'}
				</a>
				
				{if !empty($apifaq)}
					<a href="#faq" class="list-group-item" data-toggle="tab"><i class="icon-info-sign"></i> 
						{l s='FAQ' mod='seoimg'}
					</a>
				{/if}

				<a href="#contacts" class="contacts list-group-item" data-toggle="tab"><i class="icon-envelope"></i> 
					{l s='Contact' mod='seoimg'}
				</a>
			</div>

			<div class="list-group">
				<a class="list-group-item">
					<i class="icon-info"></i> {l s='Version' mod='seoimg'} {$module_version|escape:'htmlall':'UTF-8'}
				</a>
			</div>

			{if $debug_mode|intval === 1}
				<div class="list-group">
					<a id="drop" href="#drop" class="list-group-item pointer" data-toggle="tab">
						<i class="icon-undo"></i> {l s='Reset' mod='seoimg'}
					</a>
				</div>
			{/if}
		</div>

		<!-- Tab panes -->
		<div class="tab-content col-lg-10">
			<div class="tab-pane active panel" id="documentation">
				{include file="./tabs/documentation.tpl"}
			</div>
			<div class="tab-pane" id="metas">
				{include file="./tabs/legends.tpl"}
			</div>

			{if !empty($apifaq)}
				<div class="tab-pane" id="faq">
					{include file="./tabs/faq.tpl"}
				</div>
			{/if}

			{include file="./tabs/contact.tpl"}
		</div>

		{if $showRateModule == true }
			<div id="rateThisModule">
				<p>
					<img src="{$img_path}star_img.png" alt="Shining Star">
					{l s='Enjoy this module ?' mod='seoimg'}
					<a target="_blank" href="https://addons.prestashop.com/{$currentLangIsoCode}/ratings.php">
						{l s='Leave a review on Addons Marketplace' mod='seoimg'}
					</a>
				</p>
			</div>
		{/if}
	</div>

{if $ps_version == 0}
	<!-- Manage translations -->
	{include file="./translations.tpl"}
</div>
{/if}
