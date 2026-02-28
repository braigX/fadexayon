{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if !$items}
	<tr class="sp-item">
		<td colspan="5" class="list-empty">
			<div class="list-empty-msg">
				<i class="icon-warning-sign list-empty-icon"></i>
				{l s='No pages found' mod='af_seopages'}
			</div>
		</td>
	</tr>
{else}
	{foreach $items as $sp}
	<tr class="sp-item{if $sp.is_default} sp-default{/if}{if !$sp.active} inactive{/if}" data-id="{$sp.id_seopage|intval}">
		<td class="sp-id" width="10">{$sp.id_seopage|intval}</td>
		<td class="sp-label">
			{$sp.header|escape:'html':'UTF-8'}
		</td>
		<td class="sp-criteria">
			{if !$sp.is_default}
				{foreach $sp.criteria as $identifier => $cr}
					<span class="sp-criterion-preview"{if !empty($cr.info)} title="{$cr.info|escape:'html':'UTF-8'}"{/if} data-identifier="{$identifier|escape:'html':'UTF-8'}">
						{$cr.name|escape:'html':'UTF-8'}
					</span>
				{/foreach}
			{else}
				<span class="sp-default-label">{l s='Main page without filters' mod='af_seopages'}</span>
			{/if}
		</td>
		<td class="sp-link-preview">
			<i class="icon-link"></i>
			<a href="{$sp.link|escape:'html':'UTF-8'}" target="_blank">{$sp.link_label|escape:'html':'UTF-8'}</a>
		</td>
		<td class="sp-item-actions">
			{if $sp.is_default}
				<span class="icon-check spDefaultStatus"></span>
			{else}
				<a href="#" class="spToggleStatus"><i class="icon-check"></i><i class="icon-remove"></i></a>
			{/if}
			<a href="#" class="spEdit" title="{l s='Edit' mod='af_seopages'}"><i class="icon icon-pencil"></i></a>
			<a href="#" class="toggleExtraActions">•••</a>
			<div class="extra">
				<a href="#" class="spDuplicate"><i class="icon-copy"></i> {l s='Duplicate' mod='af_seopages'}</a>
				<a href="#" class="spDelete b-top"><i class="icon-trash"></i> {l s='Delete' mod='af_seopages'}</a>
			</div>
		</td>
	</tr>
	{/foreach}
{/if}
{* since 1.0.0 *}
