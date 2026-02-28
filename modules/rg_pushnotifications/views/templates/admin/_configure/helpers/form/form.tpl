{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{extends file="helpers/form/form.tpl"}

{block name="input"}
  {if $input.type == 'rg-group-box'}
    {include file='./form_group_box.tpl'}
  {elseif $input.type == 'rg-group'}
    {assign var='tmp_input' value=$input}
    <div class="{$tmp_input.type|escape:'htmlall':'UTF-8'}">
      {foreach $tmp_input.input as $input}
        <div>
          {$smarty.block.parent}
        </div>
      {/foreach}
    </div>
    <div class="clear"></div>
    {assign var='input' value=$tmp_input}
  {else}
    {$smarty.block.parent}
  {/if}
{/block}{* end block input *}
