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

<section>
  <div id="sogecommerce_sepa_oneclick_payment_description">
    <ul id="sogecommerce_sepa_oneclick_payment_description_1">
      <li>
        <span>{l s='You will pay with your registered means of payment' mod='sogecommerce'}<b> {$sogecommerce_sepa_saved_payment_mean|escape:'html':'UTF-8'}. </b>{l s='No data entry is needed.' mod='sogecommerce'}</span>
      </li>

      <li style="margin: 8px 0px 8px;">
        <span>{l s='OR' mod='sogecommerce'}</span>
      </li>

      <li>
        <a href="javascript: void(0);" onclick="sogecommerceSepaOneclickPaymentSelect(0)">{l s='Click here to update the IBAN associated with the SEPA mandate.' mod='sogecommerce'}</a>
      </li>
    </ul>
    <ul id="sogecommerce_sepa_oneclick_payment_description_2" style="display: none;">
      <li>{l s='You will enter payment data after order confirmation.' mod='sogecommerce'}</li>
      <li style="margin: 8px 0px 8px;">
        <span>{l s='OR' mod='sogecommerce'}</span>
      </li>
      <li>
        <a href="javascript: void(0);" onclick="sogecommerceSepaOneclickPaymentSelect(1)">{l s='Click here to pay with your registered means of payment.' mod='sogecommerce'}</a>
      </li>
    </ul>
  </div>
</section>
<script type="text/javascript">
  function sogecommerceSepaOneclickPaymentSelect(paymentByIdentifier) {
    if (paymentByIdentifier) {
      $('#sogecommerce_sepa_oneclick_payment_description_1').show();
      $('#sogecommerce_sepa_oneclick_payment_description_2').hide()
      $('#sogecommerce_sepa_payment_by_identifier').val('1');
    } else {
      $('#sogecommerce_sepa_oneclick_payment_description_1').hide();
      $('#sogecommerce_sepa_oneclick_payment_description_2').show();
      $('#sogecommerce_sepa_payment_by_identifier').val('0');
    }
  }

  window.onload = function() {
    options = $('#payment-option');
    if ((typeof options !== null) && (options.length == 1)) {
      $('#payment-option-1-additional-information').addClass('sogecommerce-show-options');
    } else {
      $('#payment-option-1-additional-information').removeClass('sogecommerce-show-options');
    }

    $("input[data-module-name=sogecommerce]").change(function() {
      if ($(this).is(':checked')) {
        sogecommerceSepaOneclickPaymentSelect(1);
        if (typeof sogecommerceOneclickPaymentSelect == 'function') {
          sogecommerceOneclickPaymentSelect(1);
        }
      }
    });
  };
</script>
