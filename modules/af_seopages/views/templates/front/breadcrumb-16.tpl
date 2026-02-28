{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}
	{foreach $af_sp_bc.items as $bc_item}
		<a href="{$bc_item.url|escape:'html':'UTF-8'}">{$bc_item.title|escape:'html':'UTF-8'}</a>
		{if !empty($navigationPipe)}<span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{/if}
	{/foreach}
	<span>{$af_sp_bc.current_item.title|escape:'html':'UTF-8'}</span>
{/capture}
{* since 0.2.0 *}
