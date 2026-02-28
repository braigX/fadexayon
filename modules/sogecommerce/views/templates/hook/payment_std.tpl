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
<form action="{$link->getModuleLink('sogecommerce', 'redirect', array(), true)|escape:'html':'UTF-8'}"
      method="post"
      id="sogecommerce_standard"
      style="margin-left: 2.875rem; margin-top: 1.25rem; margin-bottom: 1rem;{if $sogecommerce_is_valid_std_identifier} display: none;{/if}">

  <input type="hidden" name="sogecommerce_payment_type" value="standard" />

  {if $sogecommerce_is_valid_std_identifier}
    <input id="sogecommerce_payment_by_identifier" type="hidden" name="sogecommerce_payment_by_identifier" value="1" />
  {/if}

  {if ($sogecommerce_std_card_data_mode == 2)}
    {assign var=first value=true}
    {foreach from=$sogecommerce_avail_cards key="key" item="card"}
      <div class="sogecommerce-pm">
        {if $sogecommerce_avail_cards|@count == 1}
          <input type="hidden" id="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}" name="sogecommerce_card_type" value="{$key|escape:'html':'UTF-8'}" >
        {else}
          <input type="radio" id="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}" name="sogecommerce_card_type" value="{$key|escape:'html':'UTF-8'}" style="vertical-align: middle;"{if $first == true} checked="checked"{/if} >
        {/if}

        <label for="sogecommerce_card_type_{$key|escape:'html':'UTF-8'}">
          <img src="{$card['logo']}"
               alt="{$card['label']|escape:'html':'UTF-8'}"
               title="{$card['label']|escape:'html':'UTF-8'}">
        </label>

        {assign var=first value=false}
      </div>
    {/foreach}
    <div style="margin-bottom: 12px;"></div>

    {if $sogecommerce_is_valid_std_identifier}
      <ul>
        {if $sogecommerce_std_card_data_mode == 2}
          <li>{l s='You will enter payment data after order confirmation.' mod='sogecommerce'}</li>
        {/if}
        <li style="margin: 8px 0px 8px;">
          <span>{l s='OR' mod='sogecommerce'}</span>
        </li>
        <li>
          <a href="javascript: void(0);" onclick="sogecommerceOneclickPaymentSelect(1)">{l s='Click here to pay with your registered means of payment.' mod='sogecommerce'}</a>
        </li>
      </ul>
    {/if}
  {/if}
</form>

<script type="text/javascript">
  window.onload = function(e) {
    options = document.getElementsByClassName('payment-option');
    if ((typeof options !== null) && (options.length == 1)) {
      document.getElementById('pay-with-payment-option-1-form').classList.add('sogecommerce-show-options');

      let element = document.getElementById('payment-option-1-additional-information');
      if (element !== null) {
        document.getElementById('payment-option-1-additional-information').classList.add('sogecommerce-show-options');
      }
    } else {
      document.getElementById('pay-with-payment-option-1-form').classList.remove('sogecommerce-show-options');

      let element = document.getElementById('payment-option-1-additional-information');
      if (element !== null) {
        document.getElementById('payment-option-1-additional-information').classList.remove('sogecommerce-show-options');
      }

      {if $sogecommerce_std_select_by_default == 'True'}
        var methodTitle = '{$sogecommerce_title|escape:'js'}';
        var spans = document.querySelectorAll("span");
        var found = null;
        spans.forEach(function(span) {
          if (span.textContent.trim() === methodTitle) {
            found = span;
          }
        });
        if (found) {
          var parentDiv = found.closest('div[id*="payment-option-"]');
          var id = parentDiv.getAttribute('id');
          var match = id && id.match(/payment-option-(\d+)/);
          if (match && match.length > 1) {
            var paymentOptionId = match[1];
            $('#payment-option-' + paymentOptionId).prop("checked", true);
          }
        }
      {/if}
    }
  };
</script>