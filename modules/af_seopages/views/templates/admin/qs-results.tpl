{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if $results}
	{foreach $results as $result}
	<div class="qs-group{if $result.special} special{/if}">
		<div class="qs-group-title">
			{$result.group_name|escape:'html':'UTF-8'}
			<span class="type">({$result.group_prefix|escape:'html':'UTF-8'})</span>
			<span class="info hidden">{$result.group_info|escape:'html':'UTF-8'}</span>
		</div>
		{if !$result.values}
			<div class="qs-value blocked">--</div>
		{else}
			{$cut = [before => $result.before_cut, after => 0]}
			{foreach $result.values as $identifier => $value}
				<div class="qs-value{if !$cut.before} cut{/if}" data-identifier="{$identifier|escape:'html':'UTF-8'}">
					<span class="qs-id">{$value.id|escape:'html':'UTF-8'}</span>
					<span class="qs-name">{$value.name|escape:'html':'UTF-8'}</span>
					{if $result.special} <span class="type">({$result.group_prefix|escape:'html':'UTF-8'})</span>{/if}
				</div>
				{if $cut.before}{$cut.before = $cut.before - 1}{else}{$cut.after = $cut.after + 1}{/if}
			{/foreach}
			{if $cut.after}<a href="#" class="showMoreQs">+{$cut.after|intval} ...</a>{/if}
		{/if}
	</div>
	{/foreach}
{else}
 	<div class="qs-no-matches-">{l s='No matches...' mod='af_seopages'}</div>
{/if}
{* since 0.2.7 *}
