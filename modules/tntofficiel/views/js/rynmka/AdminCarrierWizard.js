/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2023 Inetum, 2016-2023 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

// On Ready.
window.TNTOfficiel_Ready = window.TNTOfficiel_Ready || [];
window.TNTOfficiel_Ready.push(function (jQuery) {

    var $TNTCarrierShopAssoCheckBox = jQuery('body.admincarrierwizard #step_carrier_shops #shop-tree input:checkbox:not(:checked)');
    if ($TNTCarrierShopAssoCheckBox.length > 0) {
        // Get current carrier ID edited in wizard.
        var intCarrierID = jQuery('#id_carrier').val() | 0;
        // If ID is a TNT carrier.
        if (intCarrierID in window.TNTOfficiel.carrier.list) {
            // Disabling Shop Asso checkbox.
            $TNTCarrierShopAssoCheckBox.prop('disabled', true);
        }
    }

});