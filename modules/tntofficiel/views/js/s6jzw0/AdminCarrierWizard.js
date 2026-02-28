/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
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
        if (window.TNTOfficiel
            && window.TNTOfficiel.carrier
            && window.TNTOfficiel.carrier.list
            && window.TNTOfficiel.carrier.list[intCarrierID]
        ) {
            // Disabling Shop Asso checkbox.
            $TNTCarrierShopAssoCheckBox.prop('disabled', true);
        }
    }

});