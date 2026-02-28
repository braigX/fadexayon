{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{foreach $duplicates as $type => $d}
	<div class="alert alert-warning {$type|escape:'html':'UTF-8'}-duplicates{if !$d.items} hidden{/if}">
		<label class="full-width">{$d.label|escape:'html':'UTF-8'}:</label>
		{foreach $d.items as $item}
			<a href="{$item.link|escape:'html':'UTF-8'}" target="_blank" class="sp-duplicate-item">{$item.link|escape:'html':'UTF-8'}</a>
			{if !empty($item.filters)}
				+ {l s='Applied filters' mod='af_seopages'}:
				{foreach $item.filters as $f}
					<span class="dup-filter"{if !empty($f.info)} title="{$f.info|escape:'html':'UTF-8'}"{/if}>
						{$f.name|escape:'html':'UTF-8'}
					</span>
				{/foreach}
			{/if}
			<br>
		{/foreach}
		{if !empty($d.canonical_action)}
			<div class="canonical-action-txt">{$d.canonical_action|escape:'html':'UTF-8'}</div>
		{/if}
	</div>
{/foreach}
{* since 1.0.0 *}
