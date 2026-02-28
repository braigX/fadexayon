{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{if count($input.values.query) && isset($input.values.query)}
<div class="row rg-group-box{if $input.max_height} sticky-head{/if}"{if $input.max_height} style="max-height: {$input.max_height|escape:'htmlall':'UTF-8'}px;"{/if}>
  <div class="col-lg-6">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th class="fixed-width-xs">
            <span class="title_box">
              <input type="checkbox" name="checkme_{$input.name|escape:'htmlall':'UTF-8'}" id="checkme_{$input.name|escape:'htmlall':'UTF-8'}" onclick="checkDelBoxes(this.form, '{$input.name|escape:'htmlall':'UTF-8'}[]', this.checked)" />
            </span>
          </th>
          {if isset($input.head.id) && $input.head.id}
            <th class="fixed-width-xs"><span class="title_box">{$input.head.id|escape:'htmlall':'UTF-8'}</span></th>
          {/if}
          {if isset($input.head.name) && $input.head.name}
            <th><span class="title_box">{$input.head.name|escape:'htmlall':'UTF-8'}</span></th>
          {/if}
        </tr>
      </thead>
      <tbody>
      {foreach $input.values.query as $key => $value}
        <tr>
          <td>
            {assign var=id_checkbox value=$input.name|cat:'_'|cat:$value[$input.values.id]}
            <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}[]" class="customBox" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" value="{$value[$input.values.id]|escape:'htmlall':'UTF-8'}" {if $fields_value[$id_checkbox]}checked="checked"{/if} />
          </td>
          {if isset($input.head.id) && $input.head.id}
            <td>{$value[$input.values.id]|escape:'htmlall':'UTF-8'}</td>
          {/if}
          {if isset($input.head.name) && $input.head.name}
            <td><label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">{$value[$input.values.name]|escape:'htmlall':'UTF-8'}</label></td>
          {/if}
        </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
</div>
{elseif isset($input.values.warning) && count($input.values.warning)}
<p>{$input.values.warning|escape:'htmlall':'UTF-8'}</p>
{else}
<p>{l s='There are no values' mod='rg_pushnotifications'}</p>
{/if}
