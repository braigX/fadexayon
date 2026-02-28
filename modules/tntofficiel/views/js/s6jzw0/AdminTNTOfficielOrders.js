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

    var strSelectorBulkActionItems = [
        '.bulk-actions ul.dropdown-menu li a[onclick^="sendBulkAction"][onclick*="submitBulkupdateOrderStatusorder"]',
        '.bulk-actions ul.dropdown-menu li a[onclick^="sendBulkAction"][onclick*="submitBulkupdateDeliveredorder"]'
    ].join(', ');

    // Show loader on click on bulk action.
    jQuery('#form-order')
    .off('click.' + window.TNTOfficiel.module.name, strSelectorBulkActionItems)
    .on('click.' + window.TNTOfficiel.module.name, strSelectorBulkActionItems, function (objEvent) {
            TNTOfficiel_PageSpinner();
        }
    );

    // Correcting action URL for bulk processing in orders list.
    // ex: Selecting order list and click bulk BT, then click bulk manifest but act like bulk BT.
    jQuery('#form-order button')
    .off('click.' + window.TNTOfficiel.module.name)
    .on('click.' + window.TNTOfficiel.module.name, function (objEvent) {
        var $elmtForm = jQuery("#form-order");
        var strAttrAction = $elmtForm.attr('action');
        if (strAttrAction) {
            strAttrAction = strAttrAction
            .replace('&submitBulkgetManifestorder', '')
            .replace('&submitBulkgetBTorder', '');
            $elmtForm.attr('action', strAttrAction);
        }
    });

});
