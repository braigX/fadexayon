{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<tr id="sogecommerce_multi_option_{$key|escape:'html':'UTF-8'}">
  <td>
    {include file="./input_text_lang.tpl"
      languages=$prestashop_languages
      current_lang=$prestashop_lang
      input_name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][label]"
      field_id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_label"
      input_value=$option.label
      style="width: 140px;"
    }
  </td>
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_min_amount"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][min_amount]"
        value="{$option.min_amount|escape:'html':'UTF-8'}"
        style="width: 75px;"
        type="text">
  </td>
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_max_amount"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][max_amount]"
        value="{$option.max_amount|escape:'html':'UTF-8'}"
        style="width: 75px;"
        type="text">
  </td>
  {if in_array('CB', $sogecommerce_multi_payment_cards_options)}
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_contract"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][contract]"
        value="{$option.contract|escape:'html':'UTF-8'}"
        style="width: 65px;"
        type="text">
  </td>
  {/if}
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_count"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][count]"
        value="{$option.count|escape:'html':'UTF-8'}"
        style="width: 55px;"
        type="text">
  </td>
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_period"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][period]"
        value="{$option.period|escape:'html':'UTF-8'}"
        style="width: 55px;"
        type="text">
  </td>
  <td>
    <input id="SOGECOMMERCE_MULTI_OPTIONS_{$key|escape:'html':'UTF-8'}_first"
        name="SOGECOMMERCE_MULTI_OPTIONS[{$key|escape:'html':'UTF-8'}][first]"
        value="{$option.first|escape:'html':'UTF-8'}"
        style="width: 70px;"
        type="text">
  </td>
  <td>
    <button type="button" style="width: 75px;" onclick="javascript: sogecommerceDeleteMultiOption({$key|escape:'html':'UTF-8'});">{l s='Delete' mod='sogecommerce'}</button>
  </td>
</tr>
