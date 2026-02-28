{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{extends file="helpers/form/form.tpl"}

{block name="input"}
  {if $input.type == 'rg-group'}
    {assign var='tmp_input' value=$input}
    {assign var='value_text' value=$fields_value[$tmp_input.name]}
    <div class="rg-group">
      {foreach $tmp_input.input as $input}
        <div>
          {$smarty.block.parent}
        </div>
      {/foreach}
    </div>
  {elseif $input.type == 'rg-multiple-checkbox'}
    {assign var=groups value=$input.values}
    {include file='./form_multiple_selector.tpl'}
  {else}
    {$smarty.block.parent}
  {/if}
{/block}
