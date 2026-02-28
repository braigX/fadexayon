{*
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{if isset($categories) && is_array($categories)}
	{foreach $categories as $category}
		<tr id="category_{$category.id_category|intval}" class="">
			<td class="pointer col-md-2">
				{$category.name|escape:'htmlall':'UTF-8'}
			</td>
			<td class="pointer col-md-3">
				{$breadcrumbs|escape:'htmlall':'UTF-8'} > {$category.name|escape:'htmlall':'UTF-8'}
			</td>
			<td class="pointer col-md-7">
				<div class="ajax_choose_product" id="ajax_choose_product">
					<div class="input-group">
						<select class="google_list_data  chosen" name="{$input.name|escape:'html':'UTF-8'}_{$category.id_category|intval}"></select>
					</div>
				</div>
			</td>
		</tr>
		{if (isset($category.children))}
			{assign var="breadcrumbs" value="{$breadcrumbs} > {$category.name}"}
			{include file="./item-option.tpl" categories=$category.children breadcrumbs=$breadcrumbs}
			{assign var="breadcrumbs" value="{$category.name}"}
		{/if}
	{/foreach}
{/if}