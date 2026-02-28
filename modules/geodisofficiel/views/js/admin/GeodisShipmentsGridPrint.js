/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

jQuery(document).ready(function() {
    new AdminGeodisShipmentsGridPrint();
});

var AdminGeodisShipmentsGridPrint = function() {
    this.initObservers();
};

AdminGeodisShipmentsGridPrint.prototype.initObservers = function() {
    jQuery('.js-print').on("click", (function(event) {
        event.preventDefault();
        var id = parseInt(jQuery(event.target).closest('tr').find('.js-id').text(), 10);
        this.print(id);
        window.location.reload();
    }).bind(this));
}

AdminGeodisShipmentsGridPrint.prototype.print = function(id) {
    window.open(geodis.url+'&id='+id+'&call', '_blank');
}
