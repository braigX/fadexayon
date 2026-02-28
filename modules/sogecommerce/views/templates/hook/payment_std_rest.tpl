{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

{if $sogecommerce_std_rest_popin_mode == 'True'}
  <style type="text/css">
    .kr-smart-button-wrapper, button.kr-smart-form-modal-button {
      display: none !important;
    }
  </style>
{/if}

<!-- This meta tag is mandatory to avoid encoding problems caused by \PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<section id="sogecommerce_standard_rest_wrapper" style="margin-bottom: 3rem;">
  {if $sogecommerce_std_card_data_mode === '7' || $sogecommerce_std_card_data_mode === '8' || $sogecommerce_std_card_data_mode === '9'}
    <div class="kr-smart-form" {if $sogecommerce_std_rest_popin_mode == 'True'} kr-popin {/if} {if $sogecommerce_std_card_data_mode === '8' || $sogecommerce_std_card_data_mode === '9'} kr-card-form-expanded {/if} {if $sogecommerce_std_card_data_mode === '9'} kr-no-card-logo-header {/if} kr-form-token="{$sogecommerce_rest_identifier_token|escape:'html':'UTF-8'}"></div>
  {/if}
</section>

<script type="text/javascript">
  $(document).ready(function() {
    $('input[type="radio"][name="payment-option"]').on('click', function(e) {
      sogecommerceManageButtonDisplay();
    });

    var paymentOptions = $('.payment-option');
    if (paymentOptions && paymentOptions.length == 1) {
      $("#payment-option-1").prop("checked", true);
      $('#payment-option-1-additional-information').addClass('sogecommerce-show-options');
      {if $sogecommerce_std_display_title != 'True'}
        $('#payment-option-1-container').hide();
      {/if}
    } else {
      $('#payment-option-1-additional-information').removeClass('sogecommerce-show-options');
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

    {if $sogecommerce_std_smartform_compact_mode == 'True'}
      KR.setFormConfig({ cardForm: { layout: 'compact' }, smartForm: { layout: 'compact'} });
    {/if}

    {if $sogecommerce_std_smartform_payment_means_grouping_threshold != 'False'}
      KR.setFormConfig({ smartForm: { groupingThreshold: "{$sogecommerce_std_smartform_payment_means_grouping_threshold|escape:'html':'UTF-8'}" } });
    {/if}

    KR.onFormReady(() => {
      sogecommerceManageButtonDisplay();

      {if $sogecommerce_std_rest_popin_mode == 'True'}
        var element = $(".kr-smart-button");
        if (element.length > 0) {
          element.hide();
        } else {
          element = $(".kr-smart-form-modal-button");
          if (element.length > 0) {
            element.hide();
          }
        }
      {/if}
    })
  });

  var sogecommerceManageButtonDisplay = async function() {
    {if ($sogecommerce_std_rest_popin_mode === 'True')}
      return;
    {/if}
 
    var methods = await KR.getPaymentMethods().then(function(result) {
       return result;
    });

    // If only the card form is available, hide our payment button and use Prestashop button.
    if ((methods.paymentMethods.length == 1) && (methods.paymentMethods[0] == 'CARDS')) {
      $(".kr-payment-button").hide();
      return;
    }

    var currentOptionId = $("input[type='radio'][name='payment-option']:checked").attr('id');
    if ($("#" + currentOptionId + "-additional-information").find("#sogecommerce_standard_rest_wrapper").length > 0) {
      $("#payment-confirmation").addClass('sogecommerce-hide-confirmation');
    } else {
      $("#payment-confirmation").removeClass('sogecommerce-hide-confirmation');
    }
  };

  var sogecommerceSubmit = async function(e) {
    e.preventDefault();

    if (!$('#sogecommerce_standard').data('submitted')) {
      var isSmartform = $('.kr-smart-form');
      var smartformModalButton = $('.kr-smart-form-modal-button');

      {if $sogecommerce_is_valid_std_identifier && $sogecommerce_std_rest_popin_mode != 'True'}
        $('#sogecommerce_oneclick_payment_description').hide();
      {/if}

      {if $sogecommerce_std_rest_popin_mode == 'True'}
        KR.openPopin();

        $('#payment-confirmation button').removeAttr('disabled');
      {else}
        $('#sogecommerce_standard').data('submitted', true);
        $('.sogecommerce .processing').css('display', 'block');
        $('#payment-confirmation button').attr('disabled', 'disabled');

        if (SOGECOMMERCE_LAST_CART == false) {
            await sogecommerceRefreshToken(true);
        }

        KR.submit();
      {/if}
    }

    return false;
  };
</script>