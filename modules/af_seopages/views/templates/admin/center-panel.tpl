{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{function sortingButton by = 'id' way = 'DESC'}
<a href="#" class="sp-sorting inline-block{if $sp.default_sorting.by == $by} current{/if}" data-by="{$by|escape:'html':'UTF-8'}">
	<i class="icon-sort-amount-desc{if $way != 'DESC'} hidden{/if}" data-way="DESC"></i>
	<i class="icon-sort-amount-asc{if $way != 'ASC'} hidden{/if}" data-way="ASC"></i>
</a>
{/function}

<div id="sp" class="tab-pane">
	<div class="tab-title">
		{l s='Custom SEO Pages' mod='af_seopages'}
		<a href="#" class="btn-add spAdd"><i class="u-plus"></i> {l s='New' mod='af_seopages'}</a>
	</div>
	<form class="list-params sorting-form hidden">
		<input type="hidden" name="order[by]" value="{$sp.default_sorting.by|escape:'html':'UTF-8'}">
		<input type="hidden" name="order[way]" value="{$sp.default_sorting.way|escape:'html':'UTF-8'}">
		<input type="hidden" name="order_2[by]" value="">
		<input type="hidden" name="order_2[way]" value="">
	</form>
	<table class="sp-table">
		<thead>
			<tr class="sp-labels">
				<th>ID {sortingButton by='sp.id_seopage'}</th>
				<th>{l s='H1 header' mod='af_seopages'} {sortingButton by='spl.header'}</th>
				<th>{l s='Criteria' mod='af_seopages'}</th>
				<th>{l s='Friendly URL' mod='af_seopages'} {sortingButton by='spl.link_rewrite'}</th>
				<th>{l s='Status' mod='af_seopages'} {sortingButton by='sp.active'}</th>
			</tr>
			<tr class="sp-filters">
				<th class="first"><input class="list-filter" name="f[sp.id_seopage]" type="text" value=""><a href="#" class="clearFilter">×</a></th>
				<th><input class="list-filter" name="s[header]" type="text" value=""><a href="#" class="clearFilter">×</a></th>
				<th class="criteria-filter">
					{$tagify_field = ['type' => 'tagify', 'qs_placeholder'  => '']}
					{include file="../../../../amazzingfilter/views/templates/admin/input.tpl" field=$tagify_field name='s[criteria]' value=''}
				</th>
				<th><input class="list-filter" name="s[link_rewrite]" type="text" value=""><a href="#" class="clearFilter">×</a></th>
				<th class="last">
					<select class="list-filter" name="f[active]">
						<option value="">{l s='All' mod='af_seopages'}</option>
						<option value="1">{l s='Active' mod='af_seopages'}</option>
						<option value="0">{l s='Inactive' mod='af_seopages'}</option>
					</select>
				</th>
			</tr>
		</thead>
		<tbody class="dynamic-rows">
			{include file="./sp-items.tpl" items = $sp.items}
		</tbody>
	</table>
	{include file="../../../../amazzingfilter/views/templates/admin/pagination.tpl" pagination = $sp.pagination}
</div>
<div id="sp-templates" class="tab-pane multioptions-container">
	{if !empty($grouped_templates.seopage)}
		{$grouped_templates.seopage.first = 1}
		{include file="../../../../amazzingfilter/views/templates/admin/template-group.tpl" controller_type = 'seopage' group_data = $grouped_templates.seopage}
	{/if}
</div>
<div id="sp-settings" class="tab-pane">
	<form method="post" action="" class="settings_form form-horizontal clearfix" data-type="seopage">
		<div class="tab-title">{l s='SEO Page layout' mod='af_seopages'}</div>
		<div class="clearfix">
			{foreach $settings.seopage as $name => $field}
				{include file="../../../../amazzingfilter/views/templates/admin/form-group.tpl" group_class = 'settings-item' label_class = 'settings-label' input_wrapper_class = 'settings-input'}
			{/foreach}
		</div>
	</form>
	{renderElement type='saveMultipleSettingsBtn'}
</div>
<div id="sp-generate" class="tab-pane multioptions-container">
	{include file="./bulk-generate.tpl"}
</div>
<div id="sp-sitemaps" class="tab-pane">
	<div class="tab-title">{l s='Available sitemaps' mod='af_seopages'}</div>
	{foreach $sp.sitemaps as $id_shop => $data}
		<h4>
			{$data.shop_name|escape:'html':'UTF-8'}
			<span class="grey-note"> - {l s='Total files' mod='af_seopages'}: {$data.files|count|intval}
		</h4>
		<table class="table">
			<tr class="header">
				<td>{l s='File path' mod='af_seopages'}</td>
				<td class="text-center">{l s='Number of links' mod='af_seopages'}</td>
				<td class="text-center">{l s='Last modified' mod='af_seopages'}</td>
				<td class="actions">
					<button type="button" class="btn btn-primary updAllSitemaps"><i class="icon-refresh"></i></button>
				</td>
			</tr>
			{foreach $data.files as $identifier => $file}
				<tr data-id="{$identifier|escape:'html':'UTF-8'}">
					<td><a href="{$file.path|escape:'html':'UTF-8'}" target="_blank">{$file.path|escape:'html':'UTF-8'}</a></td>
					<td class="links-num text-center">{$file.links_num|intval}</td>
					<td class="date-mod text-center grey-note">{$file.date_mod|escape:'html':'UTF-8'}</td>
					<td class="actions"><button type="button" class="btn btn-default updSitemap"><i class="icon-refresh"></i></button></td>
				</tr>
			{/foreach}
		</table>
	{/foreach}
</div>
{* since 1.0.1 *}
