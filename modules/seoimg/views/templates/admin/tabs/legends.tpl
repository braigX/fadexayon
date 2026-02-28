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

<div class="panel-group" id="accordion-metas">
	<!-- Product -->
	<div id="panel-metas-1" class="panel">
		<div class="panel-heading">
			{if $ps_version == 0}<h3>{/if}
			<a data-toggle="collapse" data-parent="#accordion-metas" href="#metas-1">{l s='Images rules for Product' mod='seoimg'}</a>
			<span class="panel-heading-action">
				<a id="configuration-metas-1" class="list-toolbar-btn" data-role="meta" data-type="product" data-toggle="tooltip" data-placement="top" title="{l s='Add new rule for SEO' mod='seoimg'}">
					<span>
						<i class="{if $ps_version == 0}icon-plus{else}process-icon-new{/if}"></i>
					</span>
				</a>
			</span>
			{if $ps_version == 0}</h3>{/if}
		</div>

		<p>{l s='Welcome to the image tag optimization interface of your shop! Here you can create quality tags for your product images. Start with the "Add new rule" button!' mod='seoimg'}</p>
		<br />

		{counter start=0 assign="count_rule" print=false}
		{include file=$table_tpl_path node=$rule_history role='metas'}
		<div id="table-metas-1" class="panel-footer">
			<a data-role="meta" data-type="generate" href="#" class="btn btn-default pull-right hide"><i class="process-icon-magic"></i> {l s='Apply all rules' mod='seoimg'}</a>
			<a data-role="meta" data-type="product" href="#" class="btn btn-default pull-right"><i class="process-icon-new {if $ps_version == 0}icon-plus{/if}"></i> {l s='Add new rule' mod='seoimg'}</a>
		</div>
	</div>
</div>
