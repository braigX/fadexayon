{*
* Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
*
*    @author    Jpresta
*    @copyright Jpresta
*    @license   See the license of this module in file LICENSE.txt, thank you.
*}
#{$id_context|intval} :
{if isset($flag_lang)}
    <span class="label label-default" title="Language"><img src="../img/l/{$flag_lang|intval}.jpg" width="16" height="11"/></span>
{/if}
{if isset($flag_currency)}
    <span class="label label-default" title="Currency">{$flag_currency|escape:'html':'UTF-8'}</span>
{/if}
{if isset($flag_country)}
    <span class="label label-default" title="Country">{$flag_country|escape:'html':'UTF-8'}</span>
{/if}
{if isset($flag_device)}
    {if $isPs17}
        <span class="label label-default" title="{$flag_device|escape:'html':'UTF-8'} device"><i class="icon-{$flag_device|escape:'html':'UTF-8'}"></i></span>
    {else}
        <span class="label label-default" title="{$flag_device|escape:'html':'UTF-8'} device">{$flag_device|escape:'html':'UTF-8'}</span>
    {/if}
{/if}
{if isset($flag_group)}
    {if $isPs17}
        <span class="label label-default" title="User groups"><i class="icon-users"></i> {$flag_group|escape:'html':'UTF-8'}</span>
    {else}
        <span class="label label-default" title="User groups">Groups: {$flag_group|escape:'html':'UTF-8'}</span>
    {/if}
{/if}
{if isset($flag_tax_manager)}
    <span class="label label-default specifics">Tax: #{$flag_tax_manager|escape:'html':'UTF-8'}
        <div class="pc_specifics"><b>Taxes applied in this cache</b><br/>{$flag_tax_manager_more|escape:'html':'UTF-8'}</div>
    </span>
{/if}
{if isset($flag_specifics)}
    <span class="label label-default specifics">Specifics: #{$flag_specifics|escape:'html':'UTF-8'}
        <div class="pc_specifics"><b>Some specific keys added by some modules</b><br/>{$flag_specifics_more|escape:'html':'UTF-8'}</div>
    </span>
{/if}
{if isset($flag_v_css)}
    <span class="label label-default" title="JS version to avoid the use of obsolete styles">CSS v{$flag_v_css|escape:'html':'UTF-8'}</span>
{/if}
{if isset($flag_v_js)}
    <span class="label label-default" title="JS version to avoid the use of obsolete javascript">JS v{$flag_v_js|escape:'html':'UTF-8'}</span>
{/if}
