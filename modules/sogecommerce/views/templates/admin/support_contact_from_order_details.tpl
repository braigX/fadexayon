{**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 *}

<script>
    // Add support contact from order.
    $(function() {
        // Recover transaction UUID from message if any.
        var uuidTitleSearch = '{$trans_id_title|escape:'html':'UTF-8'}';
        var displayedMessage = $('p:contains(' + uuidTitleSearch + ')').text();
        var uuidTitleIndex = displayedMessage.indexOf(uuidTitleSearch);
        var transUuid = displayedMessage.substring(uuidTitleIndex + uuidTitleSearch.length, uuidTitleIndex + uuidTitleSearch.length + 32);

        var sogecommerceContactSupportDetails = '\
            <contact-support\
                shop-id="{$sogecommerce_site_id|escape:'html':'UTF-8'}"\
                context-mode="{$sogecommerce_mode|escape:'html':'UTF-8'}"\
                sign-algo="{$sogecommerce_sign_algo|escape:'html':'UTF-8'}"\
                contrib="{$sogecommerce_contrib|escape:'html':'UTF-8'}"\
                integration-mode="{$sogecommerce_card_data_entry_modes[$sogecommerce_std_card_data_mode]|escape:'html':'UTF-8'}"\
                plugins="{$sogecommerce_installed_modules|escape:'html':'UTF-8'}"\
                title=""\
                first-name="{$sogecommerce_employee->firstname|escape:'html':'UTF-8'}"\
                last-name="{$sogecommerce_employee->lastname|escape:'html':'UTF-8'}"\
                from-email="{$sogecommerce_employee->email|escape:'html':'UTF-8'}"\
                to-email="{$sogecommerce_support_email|escape:'html':'UTF-8'}"\
                cc-emails=""\
                phone-number=""\
                language="{$prestashop_lang.iso_code|escape:'html':'UTF-8'}"\
                is-order="true"\
                transaction-uuid="' + transUuid + '"\
                order-id="{$id_cart|escape:'html':'UTF-8'}"\
                order-number="{$order_reference|escape:'html':'UTF-8'}"\
                order-status="{$order_status|escape:'html':'UTF-8'}"\
                order-date="{$date_add|escape:'html':'UTF-8'}"\
                order-amount="{$total_paid|escape:'html':'UTF-8'}"\
                cart-amount="{$total_products_wt|escape:'html':'UTF-8'}"\
                shipping-fees="{$total_shipping|escape:'html':'UTF-8'}"\
                order-discounts="{$total_discounts|escape:'html':'UTF-8'}"\
                order-carrier="{$order_carrier|escape:'html':'UTF-8'}">\
            </contact-support>';

        // For Prestashop < 1.7.7.0.
        $('div#status').parent('div.panel div.tab-content.panel').append(sogecommerceContactSupportDetails);

        // For Prestashop >= 1.7.7.0.
        $('div#historyTabContent').parent().append(sogecommerceContactSupportDetails);

        // For PrestaShop 1.5.
        {if version_compare($smarty.const._PS_VERSION_, '1.6', '<')}
            $('form#formAddPayment').parent().append(sogecommerceContactSupportDetails);
        {/if}

        $('contact-support').on('sendmail', function(e) {
            var data = e.originalEvent.detail;
            data.sogecommerce_mail_origine = 'order';
            $.ajax({
                type: 'POST',
                url: "{$sogecommerce_request_uri}",
                data: data,
                success: function(res) {
                    location.reload();
                },
                dataType: 'html'
            });
        });
    });
</script>