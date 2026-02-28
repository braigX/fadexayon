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
    new GeodisRemoval();
});

var GeodisRemoval = function() {
    jQuery( function() {
      jQuery( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    } );

    this.removalDelay = geodis.removalDelay;
    jQuery('.js-removalSite').val(geodis.defaultRemovalSite);
    var timeSlotOptions = jQuery('.js-timeSlot').clone();
    var idSite = geodis.defaultRemovalSite;

    this.prestationAccountList = [];

    this.getAccountPrestation();
    this.checkVolume();

    jQuery(document).on("change", '.js-float', (function(event) {
        jQuery(event.target).val(jQuery(event.target).val().replace(/\,/g, '.'));
    }).bind(this));

    jQuery(document).on("change", '.js-removalSite', (function(event) {
        jQuery('.js_alertDayOff').hide();
        this.checkRemovalDate(timeSlotOptions);
    }).bind(this));

    jQuery(document).on("change", '.js-account', (function(event) {
        this.updatePrestation();
    }).bind(this));

    jQuery(document).on("change", '.js-account,.js-prestation', (function(event) {
        jQuery('.js_alertDayOff').hide();
        this.checkRemovalDate(timeSlotOptions);
        this.checkVolume();
    }).bind(this));

    this.removalDateModified = false;
    jQuery(document).on("change", '.js-removalDate', (function(event) {
        this.removalDateModified = true;
        jQuery('.js_alertDayOff').hide();
        this.checkRemovalDate(timeSlotOptions);
    }).bind(this));

    jQuery('.js-box, .js-pallet').change((function() {
        this.checkNumbers();
    }).bind(this));

    jQuery(document).on("change", '.js-reglemented_transport', (function(event) {
        if (jQuery('input[name="reglemented_transport"]:checked').val() == 1) {
            jQuery('.js-legalVolume').parent().parent().show();
            jQuery('.js-totalVolume').parent().parent().show();
            jQuery('.js-fiscalCode').parent().parent().show();
        } else {
            jQuery('.js-legalVolume').parent().parent().hide();
            jQuery('.js-totalVolume').parent().parent().hide();
            jQuery('.js-fiscalCode').parent().parent().hide();
        }
        jQuery('.js_alertDayOff').hide();
        this.checkRemovalDate(timeSlotOptions);
    }).bind(this));

    if (jQuery('input[name="reglemented_transport"]:checked').val() == 0) {
        jQuery('.js-legalVolume').parent().parent().hide();
        jQuery('.js-totalVolume').parent().parent().hide();
        jQuery('.js-fiscalCode').parent().parent().hide();
    }

    jQuery('.js_alertWsDayOff').hide();
    jQuery('.js_alertDayOff').hide();
    this.checkRemovalDate(timeSlotOptions);

    this.updatePrestation();
}

GeodisRemoval.prototype.updatePrestation = function() {
    var idAccount = parseInt(jQuery('.js-account').val());

    $select = jQuery('.js-prestation');
    value = $select.val();
    $select.html('');
    for(var i in this.prestationAccountList) {
        var accountPrestation = this.prestationAccountList[i];
        if (accountPrestation.id_account == idAccount) {
            $select.append(jQuery('<option/>').attr('value', accountPrestation.id_prestation).text(accountPrestation.name));
        }
        if (accountPrestation.id_prestation == value) {
            $select.val(value);
        }
    }
}

GeodisRemoval.prototype.getAccountPrestation = function() {
    jQuery.ajax({
        url: '#',
        async: false,
        data: {
            'call' : 'getAccountPrestation',
            'controller': geodis.admin+'Removal',
            'token': geodis.token
        },
        dataType : 'json',
        type: 'get',
        success: (function(arrayData) {
            this.prestationAccountList = arrayData;
        }).bind(this),
        error: function(response) {
        }
    });
}

GeodisRemoval.prototype.checkVolume = function() {
    var idPrestation = jQuery('.js-prestation').val();
    this.prestationAccountList.forEach((function(idPrestation, prestation) {
        if (prestation.id_prestation == idPrestation) {
            if (prestation.volumeRequired) {
                jQuery('.js-volume').parent().parent().find('label').addClass('required');
            } else {
                jQuery('.js-volume').parent().parent().find('label').removeClass('required');
            }
            return;
        }
    }).bind(this, idPrestation));
};

GeodisRemoval.prototype.checkNumbers = function() {
    var boxes = jQuery('.js-box');
    var pallets = jQuery('.js-pallet');

    if (boxes.val() == '' && pallets.val() == '' || boxes.val() != '' && pallets.val() != '') {
        boxes.parent().parent().find('label').addClass('required')
        pallets.parent().parent().find('label').addClass('required')
    } else if (boxes.val() == '' && pallets.val() != '') {
        boxes.parent().parent().find('label').removeClass('required')
        pallets.parent().parent().find('label').addClass('required')
    } else if (boxes.val() != '' && pallets.val() == '') {
        boxes.parent().parent().find('label').addClass('required')
        pallets.parent().parent().find('label').removeClass('required')
    }
};

GeodisRemoval.prototype.checkRemovalDate = function(timeSlotOptions) {
    if(!jQuery('.js-prestation').val()) {
        return;
    }
    jQuery.ajax({
        url: '#',
        data: {
            'ajaxRemovalDate': 'getDaysOff',
            'idPrestation': jQuery('.js-prestation').val(),
            'idAccount': jQuery('.js-account').val(),
            'idSite': jQuery('.js-removalSite').val(),
            'controller': geodis.admin+'Removal',
            'token': geodis.token,
        },
        dataType : 'json',
        type: 'get',
        success: (function(data) {
            if (data['error'] == true) {
                jQuery('.js_alertWsDayOff').show();
                return;
            }

            jQuery('.js_alertWsDayOff').hide();
            var daysOff = data['daysOff'];
            var dateFormat = jQuery('.js-removalDate').val();

            if (!dateFormat) {
                var finalDate = new Date(data['firstDateAvailable']);
                var dateDelay = this.removalDelay;
            } else {
                var dateTab = jQuery('.js-removalDate').val().split("-");
                var finalDate = new Date(dateTab[0], dateTab[1]-1, dateTab[2]);
                var dateDelay = 0;
            }

            for (var i = 0; i <= dateDelay; i++) {
                if (i != 0) {
                    finalDate.setDate(finalDate.getDate() + 1);
                }

                while (daysOff.indexOf(this.formatDate(finalDate)) != -1) {
                    finalDate.setDate(finalDate.getDate() + 1)
                    jQuery('.js_alertDayOff').show();
                }
            }

            jQuery('.js-removalDate').val(this.formatDate(finalDate));
            jQuery('[name="firstDateAvailable"]').val(data['firstDateAvailable']);
            if (data['express']) {
                if (data['morningAvailable'] == true && data['afternoonAvailable'] == true) {
                    jQuery('.js-timeSlot option').remove();
                    jQuery('.js-timeSlot').append(timeSlotOptions.clone().html());
                } else {
                    jQuery('.js-timeSlot option').remove();
                    jQuery('.js-timeSlot').append(timeSlotOptions.clone().html());
                    var firstDatetimeAvailable = new Date(data['firstDateAvailable']);
                    var removalDateTimeInput = new Date(jQuery('.js-removalDate').val());
                    if (firstDatetimeAvailable.toDateString() === removalDateTimeInput.toDateString()) {
                        if (data['morningAvailable'] == false) {
                            jQuery('.js-timeSlot option[value=0]').remove();
                            jQuery('.js-timeSlot option[value=1]').remove();
                        }
                        if (data['afternoonAvailable'] == false) {
                            jQuery('.js-timeSlot option[value=0]').remove();
                            jQuery('.js-timeSlot option[value=2]').remove();
                        }
                        if (data['afternoonAvailable'] == false && data['morningAvailable'] == false) {
                            jQuery('.js-timeSlot option').remove();
                            jQuery('.js-timeSlot').append(jQuery("<option></option>").attr("value", null).text(geodis.optionNotAvailable));
                        }
                    }
                }
            } else {
                jQuery('.js-timeSlot option').remove();
                jQuery('.js-timeSlot').append(jQuery("<option></option>").attr("value", null).text(geodis.optionNotAvailable));
            }
        }).bind(this),
        error : function(response) {
        }
     });
}

GeodisRemoval.prototype.formatDate = function(date) {
    var month = date.getUTCMonth() + 1;
    var day = date.getDate();
    if (month < 10) {
        month = "0" + month;
    }
    if (day < 10) {
        day = "0" + day;
    }

    return date.getFullYear() + '-' + month + "-" + day;
}
