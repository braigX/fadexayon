{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<script>
    // Add refund checkboxes for PrestaShop >= 1.7.7.
    $(function() {
        var sogecommerceRefund = "{l s='Refund the buyer by Web Services with %s' sprintf='Sogecommerce' mod='sogecommerce'}";

        // Create Sogecommerce partial refund checkbox.
        if ($('#doPartialRefundSogecommerce').length === 0) {
            var newCheckbox = '\
                    <div class="cancel-product-element sogecommerce-refund sogecommerce-partial-refund form-group" style="display: block;">\
                        <div class="checkbox">\
                            <div class="md-checkbox md-checkbox-inline">\
                                <label>\
                                    <input type="checkbox" id="doPartialRefundSogecommerce" name="doPartialRefundSogecommerce" material_design="material_design" value="1">\
                                      <i class="md-checkbox-control"></i>' +
                                        sogecommerceRefund + '\
                                </label>\
                            </div>\
                        </div>\
                    </div>';

                $(newCheckbox).insertAfter('.refund-checkboxes-container .refund-voucher');
            }

            // Create Sogecommerce standard refund checkbox.
            if ($('#doStandardRefundSogecommerce').length === 0) {
                var newCheckbox = '\
                    <div class="cancel-product-element sogecommerce-refund sogecommerce-standard-refund form-group" style="display: block;">\
                        <div class="checkbox">\
                            <div class="md-checkbox md-checkbox-inline">\
                                <label>\
                                    <input type="checkbox" id="doStandardRefundSogecommerce" name="doStandardRefundSogecommerce" material_design="material_design" value="1">\
                                      <i class="md-checkbox-control"></i>' +
                                        sogecommerceRefund + '\
                                </label>\
                            </div>\
                        </div>\
                    </div>';

                $(newCheckbox).insertAfter('.refund-checkboxes-container .refund-voucher');
            }
        });

        $(document).on('click', '.partial-refund-display', function() {
            $('.sogecommerce-standard-refund').hide();
        });

        $(document).on('click', '.standard-refund-display', function() {
            $('.sogecommerce-partial-refund').hide();
        });

        $(document).on('click', '.return-product-display', function() {
            $('.sogecommerce-partial-refund').hide();
        });

        // Click on credit slip creation checkbox.
        $(document).on('click', '#cancel_product_credit_slip', function() {
            toggleCheckboxDisplay();
        });

        // Click on voucher creation checkbox.
        $(document).on('click', '#cancel_product_voucher', function() {
            toggleCheckboxDisplay();
        });

        // Do not allow refund if no credit slip is generated or if a voucher is generated.
        function toggleCheckboxDisplay() {
            $('.sogecommerce-refund input').attr('disabled', 'disabled');
            $('.sogecommerce-refund').hide();

            if ($('#cancel_product_credit_slip').is(':checked')
                && ! $('#cancel_product_voucher').is(':checked')) {
                if ($('.shipping-refund').is(":visible") == true) {
                    $('#doStandardRefundSogecommerce').removeAttr('disabled');
                    $('.sogecommerce-standard-refund').show();
                } else {
                    $('#doPartialRefundSogecommerce').removeAttr('disabled');
                    $('.sogecommerce-partial-refund').show();
                }
            }
        }
</script>