{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{if count($groups) && isset($groups)}
  <div class="row">
    <div class="col-lg-6">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th class="fixed-width-xs">
              <span class="title_box">
                <input type="checkbox" name="checkme" id="checkme_{$input.name|escape:'htmlall':'UTF-8'}" onclick="checkDelBoxes(this.form, '{$input.name|escape:'htmlall':'UTF-8'}[]', this.checked)" />
              </span>
            </th>
            {if !isset($input.hide_id_column) || !$input.hide_id_column}
              <th class="fixed-width-xs"><span class="title_box">{l s='ID' mod='rg_pushnotifications'}</span></th>
              {/if}
            <th>
              <span class="title_box">
                {l s='Name' mod='rg_pushnotifications'}
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          {foreach $groups as $group}
            <tr>
              <td>
                {assign var=id_checkbox value=$input.name|cat:'_'|cat:$group[$input.id_field]}
                <input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}[]" class="groupBox" id="{$id_checkbox|escape:'htmlall':'UTF-8'}" value="{$group[$input.id_field]|escape:'htmlall':'UTF-8'}" {if isset($fields_value[$input.name]) && is_array($fields_value[$input.name]) && in_array($group[$input.id_field], $fields_value[$input.name])}checked="checked"{/if}/>
              </td>
              {if !isset($input.hide_id_column) || !$input.hide_id_column}
                <td>{$group[$input.id_field]|escape:'htmlall':'UTF-8'}</td>
              {/if}
              <td>
                <label for="{$id_checkbox|escape:'htmlall':'UTF-8'}">{$group[$input.name_field]|escape:'htmlall':'UTF-8'}</label>
              </td>
            </tr>
          {/foreach}
        </tbody>
      </table>
    </div>
  </div>
{else}
  <p>
    {l s='No groups available' mod='rg_pushnotifications'}
  </p>
{/if}
