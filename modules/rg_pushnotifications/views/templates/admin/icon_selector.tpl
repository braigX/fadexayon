{**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 *}

{if count($rg_pushnotifications_icon_selector.icons)}
  {assign var=id_order_state value=$rg_pushnotifications_icon_selector.id_order_state}
  <div class="panel icons_panel {$rg_pushnotifications_icon_selector.form_group_class|escape:'htmlall':'UTF-8'}">
    <div class="row form-group">
    {foreach $rg_pushnotifications_icon_selector.icons key=id item=icon}
      <div class="col-xs-2">
        <div class="icon-thumbnail">
          <label for="icon_{$id_order_state|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}" class="icon_label">
            <input type="radio" class="icon_radio" name="{$rg_pushnotifications_icon_selector.name|escape:'htmlall':'UTF-8'}" id="icon_{$id_order_state|escape:'htmlall':'UTF-8'}_{$id|escape:'htmlall':'UTF-8'}" value="{$id|escape:'htmlall':'UTF-8'}"{if $rg_pushnotifications_icon_selector.icon == $id} checked="checked"{/if}/>
            <img src="{$rg_pushnotifications_icon_selector._path|escape:'htmlall':'UTF-8'}{$icon|escape:'htmlall':'UTF-8'}">
          </label>
        </div>
      </div>
    {/foreach}
    </div>
  </div>
{/if}
