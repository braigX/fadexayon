{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<div style="padding-left: 40px;" id="sogecommerce_oneclick_payment_description">
  <ul id="sogecommerce_oneclick_payment_description_1">
    <li>
      <span class="sogecommerce_span">{l s='You will pay with your registered means of payment' mod='sogecommerce'}<b> {$sogecommerce_saved_payment_mean|escape:'html':'UTF-8'}. </b>{l s='No data entry is needed.' mod='sogecommerce'}</span>
    </li>

    <li style="margin: 8px 0px 8px;">
      <span class="sogecommerce_span">{l s='OR' mod='sogecommerce'}</span>
    </li>

    <li>
      <p class="sogecommerce_link" onclick="sogecommerceOneclickPaymentSelect(0)">{l s='Click here to pay with another means of payment.' mod='sogecommerce'}</p>
    </li>
  </ul>
{if ($sogecommerce_std_card_data_mode == '2')}
  <script type="text/javascript">
    function sogecommerceOneclickPaymentSelect(paymentByIdentifier) {
      if (paymentByIdentifier) {
        $('#sogecommerce_oneclick_payment_description').show();
        $('#sogecommerce_standard').hide();
        $('#sogecommerce_payment_by_identifier').val('1');
      } else {
        $('#sogecommerce_oneclick_payment_description').hide();
        $('#sogecommerce_standard').show();
        $('#sogecommerce_payment_by_identifier').val('0');
      }
    }
  </script>
{else}
  <ul id="sogecommerce_oneclick_payment_description_2" style="display: none;">
    {if $sogecommerce_std_card_data_mode == '7' || $sogecommerce_std_rest_popin_mode == 'True'}
      <li>{l s='You will enter payment data after order confirmation.' mod='sogecommerce'}</li>
    {/if}

      <li style="margin: 8px 0px 8px;">
        <span class="sogecommerce_span">{l s='OR' mod='sogecommerce'}</span>
      </li>
      <li>
        <p class="sogecommerce_link" onclick="sogecommerceOneclickPaymentSelect(1)">{l s='Click here to pay with your registered means of payment.' mod='sogecommerce'}</p>
      </li>
  </ul>

  <script type="text/javascript">
    $(document).ready(function() {
       sessionStorage.setItem('sogecommerceIdentifierToken', "{$sogecommerce_rest_identifier_token|escape:'html':'UTF-8'}");
       sessionStorage.setItem('sogecommerceToken', "{$sogecommerce_rest_form_token|escape:'html':'UTF-8'}");
    });

    function sogecommerceOneclickPaymentSelect(paymentByIdentifier) {
      if (paymentByIdentifier) {
        $('#sogecommerce_oneclick_payment_description_1').show();
        $('#sogecommerce_oneclick_payment_description_2').hide()
        $('#sogecommerce_payment_by_identifier').val('1');
      } else {
        $('#sogecommerce_oneclick_payment_description_1').hide();
        $('#sogecommerce_oneclick_payment_description_2').show();
        $('#sogecommerce_payment_by_identifier').val('0');
      }

      {if ($sogecommerce_std_card_data_mode == '7' || $sogecommerce_std_card_data_mode == '8' || $sogecommerce_std_card_data_mode == '9')}
        $('.sogecommerce .kr-form-error').html('');

        var token;
        if ($('#sogecommerce_payment_by_identifier').val() == '1') {
          token = sessionStorage.getItem('sogecommerceIdentifierToken');
        } else {
          token = sessionStorage.getItem('sogecommerceToken');
        }

        KR.setFormConfig({ formToken: token, language: SOGECOMMERCE_LANGUAGE });
      {/if}
    }
    </script>
{/if}

{if ($sogecommerce_std_card_data_mode != '7' && $sogecommerce_std_card_data_mode != '8' && $sogecommerce_std_card_data_mode != '9')}
    {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
      <input id="sogecommerce_standard_link" value="{l s='Pay' mod='sogecommerce'}" class="button" />
    {else}
      <button id="sogecommerce_standard_link" class="button btn btn-default standard-checkout button-medium">
        <span>{l s='Pay' mod='sogecommerce'}</span>
      </button>
    {/if}
{/if}
</div>