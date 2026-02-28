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

{assign var='role' value=$role|escape:'htmlall':'UTF-8'}
{assign var='type' value=array_keys($rule_history)}

{counter}
<div id="{$role|escape:'htmlall':'UTF-8'}-{$count_rule|intval}" class="panel-collapse collapse{if $count_rule|intval == 1} in{/if}">
	<div class="table-responsive clearfix">
		<table id="table-{$role|escape:'htmlall':'UTF-8'}-{$count_rule|intval}" cellpadding="0" cellspacing="0" border="0" class="datatable table table-striped table-bordered table-hover dataTableHidden">
			<thead>
			<tr>
				<th class="text-center">{l s='Name' mod='seoimg'}</th>
				<th class="text-center">{l s='Lang' mod='seoimg'}</th>
				{if $multishop|intval === 1}<th class="text-center">{l s='Shop' mod='seoimg'}</th>{/if}
				<th class="text-center">{l s='Category' mod='seoimg'}</th>
				<th class="text-center">{l s='Status' mod='seoimg'}</th>
				<th class="text-center">{l s='Last apply' mod='seoimg'}</th>
				<th class="text-center">{l s='Actions' mod='seoimg'}</th>
			</tr>
			</thead>
			<tbody>
			{if isset($node) & !empty($node)}
				{foreach $node[$type[$count_rule-1]] as $prod}
				<tr id="cat_{$prod.id_rule|intval}" data-active="{$prod.active|intval}">
					<td class="fixed-width-sm text-center">{$prod.id_rule|intval}</td>
					<td class="fixed-width-sm text-center">{$prod.lang|escape:'htmlall':'UTF-8'}</td>
					{if $multishop|intval === 1}<td class="fixed-width-sm text-center">{$prod.shop|escape:'htmlall':'UTF-8'}</td>{/if}
					{*
						One Rule to rule them all, One Rule to find them,
						One Rule to bring them all and in the darkness bind them
					*}
					<td class="fixed-width-sm text-center">
						{if $prod.nb_obj|escape:'htmlall':'UTF-8' == 'All'}
							{l s='All categories' mod='seoimg'}
						{else}
							{$prod.nb_obj|escape:'htmlall':'UTF-8'}
						{/if}
					</td>
					<td class="pointer fixed-width-sm text-center">
						{* See ajaxProcessReloadData *}
						<a class="list-action-enable action-{if $prod.active|intval == 1}enabled{else}disabled{/if}" title="{if $prod.active|intval == 1}{l s='Enabled' mod='seoimg'}{else}{l s='Disabled' mod='seoimg'}{/if}">
							<i class="icon-check {if $prod.active|intval != 1}hidden{/if}"></i>
							<i class="icon-remove {if $prod.active|intval == 1}hidden{/if}" ></i>
						</a>
					</td>
					<td class="text-center">
						{if $prod.date_upd|escape:'htmlall':'UTF-8'}
							{dateFormat date=$prod.date_upd}
						{else}
							N/A
						{/if}
					</td>
					<td class="text-right">
						{* See ajaxProcessReloadData *}
						{include file=$actions_tpl_path prod=$prod}
					</td>
				</tr>
				{/foreach}
			{/if}
			</tbody>
		</table>
	</div>
</div>
