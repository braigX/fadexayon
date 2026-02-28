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

{if count($groups) && isset($groups) && isset($groups.query) && $groups.query|count}
<div class="row">
	<div class="col-lg-6">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="fixed-width-xs">
						<span class="title_box">
							<input type="checkbox" name="checkme" id="checkme" onclick="checkDelBoxes(this.form, '{$input.name|escape:'html':'UTF-8'}[]', this.checked)" />
						</span>
					</th>
					<th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='ets_reviews'}</span></th>
					<th>
						<span class="title_box">
							{l s='Name' mod='ets_reviews'}
						</span>
					</th>
				</tr>
			</thead>
			<tbody>
			{foreach $groups.query as $key => $group}
				<tr>
					<td>
						{assign var=id_checkbox value=groupBox|cat:'_'|cat:$group[$groups.id]}
						<input type="checkbox" name="{$input.name|escape:'html':'UTF-8'}[]" class="groupBox" id="{$id_checkbox|escape:'html':'UTF-8'}" value="{$group[$groups.id]|intval}" {if in_array($group[$groups.id], $fields_value[$input.name])}checked="checked"{/if} />
					</td>
					<td>{$group[$groups.id]|escape:'quotes':'UTF-8'}</td>
					<td>
						<label for="{$id_checkbox|escape:'html':'UTF-8'}">{$group[$groups.name]|escape:'html':'UTF-8'}</label>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
</div>
{else}
<p>
	{l s='No group created' mod='ets_reviews'}
</p>
{/if}
