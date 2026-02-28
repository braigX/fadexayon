{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<script>
    // Add refund checkboxes for PrestaShop < 1.7.7.
    $(function() {
        var sogecommerceRefund = "{l s='Refund the buyer by Web Services with %s' sprintf='Sogecommerce' mod='sogecommerce'}";

        {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
            // Create Sogecommerce partial refund checkbox.
            if ($('#doPartialRefundSogecommerce').length === 0) {
                var newCheckbox = '<br><input type="checkbox" id="doPartialRefundSogecommerce" name="doPartialRefundSogecommerce" class="button">'
                                + '<label for="doPartialRefundSogecommerce" style="float:none; font-weight:normal;">&nbsp;' + sogecommerceRefund + '</label>';

                $(newCheckbox).insertAfter($('#generateDiscountRefund').next());
            }
    
            // Create Sogecommerce standard refund checkbox.
            if ($('#doStandardRefundSogecommerce').length === 0) {
                var newCheckbox = '<span style="display: none;" class="sogecommerce-standard-refund">\
                                    <br>\
                                    <input type="checkbox" id="doStandardRefundSogecommerce" name="doStandardRefundSogecommerce" class="button">\
                                    <label for="doStandardRefundSogecommerce" style="float:none; font-weight:normal;">&nbsp;' + sogecommerceRefund + '</label>\
                                    </span>';

                $(newCheckbox).insertAfter($('#generateDiscount').next());
            }
        {else}
            // Create Sogecommerce partial refund checkbox.
            if ($('#doPartialRefundSogecommerce').length === 0) {
                var newCheckbox = '<p class="checkbox sogecommerce-partial-refund">\
                                       <label for="doPartialRefundSogecommerce">\
                                           <input type="checkbox" id="doPartialRefundSogecommerce" name="doPartialRefundSogecommerce" value="1">' +
                                               sogecommerceRefund + '\
                                       </label>\
                                   </p>';

                $(newCheckbox).insertAfter($('#generateDiscountRefund').parent().parent());
            }

            // Create Sogecommerce standard refund checkbox.
            if ($('#doStandardRefundSogecommerce').length === 0) {
                var newCheckbox = '<p class="checkbox sogecommerce-standard-refund" style="display: none;">\
                                       <label for="doStandardRefundSogecommerce">\
                                           <input type="checkbox" id="doStandardRefundSogecommerce" name="doStandardRefundSogecommerce" value="1">' +
                                               sogecommerceRefund + '\
                                       </label>\
                                    </p>';
                $(newCheckbox).insertAfter($('#generateDiscount').parent().parent());
            }
        {/if}
    });

    // Click on credit slip creation checkbox, standard payment.
    $(document).on('click', '#generateCreditSlip', function() {
        toggleStandardCheckboxDisplay();
    });

    // Click on voucher creation checkbox, standard payment.
    $(document).on('click', '#generateDiscount', function() {
        toggleStandardCheckboxDisplay();
    });

    // Click on voucher creation checkbox, partial payment.
    $(document).on('click', '#generateDiscountRefund', function() {
        if ($('#generateDiscountRefund').is(':checked')) {
            $('.sogecommerce-partial-refund input').attr('disabled', 'disabled');
            $('.sogecommerce-partial-refund').hide();

            $('#doPartialRefundSogecommerce').attr('disabled', 'disabled');
            $('#doPartialRefundSogecommerce').hide();
            $('label[for="doPartialRefundSogecommerce"]').hide();
        } else {
            $('.sogecommerce-partial-refund input').removeAttr('disabled');
            $('.sogecommerce-partial-refund').show();

            $('#doPartialRefundSogecommerce').removeAttr('disabled');
            $('#doPartialRefundSogecommerce').show();
            $('label[for="doPartialRefundSogecommerce"]').show();
        }
    });

    // Hide "Amount of you choosing" button to avoid confusion.
    $(document).on('click', '#doPartialRefundSogecommerce', function() {
        if ($('#doPartialRefundSogecommerce').is(':checked')) {
            $("#lab_refund_3").parent('div').hide();
        } else {
            $("#lab_refund_3").parent('div').show();
        }
    });

    $(document).on('click', '#doStandardRefundSogecommerce', function() {
        if ($('#doStandardRefundSogecommerce').is(':checked')) {
            $("#lab_refund_total_3").parent('div').hide();
        } else {
            $("#lab_refund_total_3").parent('div').show();
        }
    });

    // Do not allow refund if no credit slip is generated or if a voucher is generated.
    function toggleStandardCheckboxDisplay() {
        if ($('#generateCreditSlip').is(':checked')
            && ! $('#generateDiscount').is(':checked')) {
            $('#doStandardRefundSogecommerce').removeAttr('disabled');
            $('.sogecommerce-standard-refund').show();
        } else {
            $('#doStandardRefundSogecommerce').attr('disabled', 'disabled');
            $('.sogecommerce-standard-refund').hide();
        }
    }
</script>