{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<!-- This meta tag is mandatory to avoid encoding problems caused by \PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}" method="post" style="margin-left: 2.875rem; margin-top: 1.25rem; margin-bottom: 1rem;">
  <input type="hidden" name="sogecommerce_payment_type" value="franfinance">

  {assign var=first value=true}
  {foreach from=$sogecommerce_ffin_options key="key" item="option"}
    <div style="padding-bottom: 5px;">
      {if $sogecommerce_ffin_options|@count == 1}
        <input type="hidden" id="sogecommerce_ffin_option_{$key|escape:'html':'UTF-8'}" name="sogecommerce_ffin_option" value="{$key|escape:'html':'UTF-8'}">
      {else}
        <input type="radio"
               id="sogecommerce_ffin_option_{$key|escape:'html':'UTF-8'}"
               name="sogecommerce_ffin_option"
               value="{$key|escape:'html':'UTF-8'}"
               style="vertical-align: middle;"
               {if $first == true} checked="checked"{/if}>
      {/if}

      <label for="sogecommerce_ffin_option_{$key|escape:'html':'UTF-8'}" style="display: inline;">
        <span style="vertical-align: middle;">{$option.localized_label|escape:'html':'UTF-8'}</span>
      </label>
    {assign var=first value=false}
    </div>
  {/foreach}
</form>

<script type="text/javascript">
  window.onload = function(e) {
    options = document.getElementsByClassName('payment-option');
    if ((typeof options !== null) && (options.length == 1)) {
      document.getElementById('pay-with-payment-option-1-form').classList.add('sogecommerce-show-options');
    } else {
      document.getElementById('pay-with-payment-option-1-form').classList.remove('sogecommerce-show-options');
    }
  };
</script>