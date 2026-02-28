{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<div style="padding-left: 40px;" id="sogecommerce_sepa_oneclick_payment_description">
  <ul id="sogecommerce_sepa_oneclick_payment_description_1">
    <li>
      <span class="sogecommerce_span">{l s='You will pay with your registered means of payment' mod='sogecommerce'}<b> {$sogecommerce_sepa_saved_payment_mean|escape:'html':'UTF-8'}. </b>{l s='No data entry is needed.' mod='sogecommerce'}</span>
    </li>

    <li style="margin: 8px 0px 8px;">
      <span class="sogecommerce_span">{l s='OR' mod='sogecommerce'}</span>
    </li>

    <li>
      <p class="sogecommerce_link" onclick="sogecommerceSepaOneclickPaymentSelect(0)">{l s='Click here to update the IBAN associated with the SEPA mandate.' mod='sogecommerce'}</p>
    </li>
  </ul>
  <ul id="sogecommerce_sepa_oneclick_payment_description_2" style="display: none;">
    <li>{l s='You will enter payment data after order confirmation.' mod='sogecommerce'}</li>
    <li style="margin: 8px 0px 8px;">
      <span class="sogecommerce_span">{l s='OR' mod='sogecommerce'}</span>
    </li>
    <li>
      <p class="sogecommerce_link" onclick="sogecommerceSepaOneclickPaymentSelect(1)">{l s='Click here to pay with your registered means of payment.' mod='sogecommerce'}</p>
    </li>
  </ul>

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
  </script>
</div>