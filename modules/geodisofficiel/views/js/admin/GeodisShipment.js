/**
 * 2024 Novatis (BRAIGUE.COM).
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to aziz@braigue.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    BRAIGUE <hello@Novatis.com>
 *  @copyright 2024 Novatis
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

jQuery(document).ready(function() {
    new GeodisExpeditionForm();
});


var GeodisExpeditionForm = function() {
    jQuery( function() {
        jQuery( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    } );

    $('[data-toggle="popover"]').popover()

    this.hideWarningMessages();
    this.initObservers();

    this.availablePrestationList = null;
    this.currentIdFiscalCode = null;
    this.currentNunberOfCols = null;
    this.currentVolumeCl = null;
    this.currentVolumeL = null;
    this.currentNEa = null;
    this.currentShippingDuration = null;
    this.currentFiscalCodeRef = null;
    this.currentNMvt = null;

    this.userDefinedDate = jQuery('.js-removalDate').val();

    if (geodis.thermalPrintingStatus == 1) {
        this.checkModuleInstallation();
    }

    this.jsonObject = JSON.parse(geodis.json);
    var nbPackages = this.jsonObject.packages.length;
    this.nextIndex = nbPackages;
    this.itemTemplate = this.getItemTemplate();
    this.packageTemplate = this.getPackageTemplate();

    this.postData = null;
    if (geodis.post) {
        this.postData = geodis.post;
    }

    if (nbPackages == 0 && this.postData == null) {
        this.addNewPackage();
    } else {
        for (var index = 0; index < nbPackages; index++) {
            this.fillTemplatePackage(index);
        }
    }

    var quantityElements = jQuery(document).find('.js-quantity');
    for (var i = 0; i < quantityElements.length; i++) {
        this.computeMaxItem(quantityElements[i]);
    }

    if (jQuery('.js-groupCarrierSelect option').length == 0) {
        jQuery('.js-alertNoGroupCarrierAvailable').show();
    }

    var init = false;
    if (this.postData != null) {
        this.fillPostData();
    } else {
        if (geodis.idGroupCarrier != 0 && geodis.idCarrier != 0) {
            jQuery('.js-groupCarrierSelect option[value="' + geodis.idGroupCarrier + '"]').prop('selected', true);
        }
        this.updateCarrierList(geodis.idCarrier);
        init = true;
    }
    this.computeWeightPackage();
    this.computeVolumePackage();//Add with team wassim novatis
    this.autoHideButton();
};

GeodisExpeditionForm.prototype.initObserveForm = function() {
    this.serializedForm = jQuery('.form').serialize().replace(/&firstDayAvailable=(\d{4}-\d{2}-\d{2})*/, '');
    window.setInterval((function() {
        if (jQuery('.js-tooltip').length) {
            if (this.serializedForm != jQuery('.form').serialize().replace(/&firstDayAvailable=(\d{4}-\d{2}-\d{2})*/, '')) {
                if (!geodis.noLabelAvailable) {
                    jQuery('.js-tooltip').removeClass('hidden');
                    jQuery('[name=send]').attr('disabled', true);
                }
            } else {
                jQuery('.js-tooltip').addClass('hidden');
                jQuery('[name=send]').attr('disabled', false);
            }
        }
    }).bind(this), 500);
};

GeodisExpeditionForm.prototype.autoHideButton = function() {
    if (!this.hasMoreItems() || !this.hasItemsDefined()) {
        jQuery('[name=add_package]').hide();
        jQuery('.js-save-and-new').hide();
    } else {
        jQuery('[name=add_package]').show();
        jQuery('.js-save-and-new').show();
    }

    if (!jQuery('.js-package').length) {
        jQuery('[name=add_package]').show();
    }

    if (!this.hasItemsDefined()) {
        jQuery('[name=submit]').attr('disabled', true);
    } else {
        jQuery('[name=submit]').attr('disabled', false);
    }

    var count = 0;
    jQuery('.js-button_remove').each(function() {
        if (jQuery(this).closest('.js-package').find('.js-remove_package').val() != 1) {
            count++;
        }
    });
    if (count == 1) {
        jQuery('.js-button_remove').hide();
    } else {
        jQuery('.js-button_remove').show();
    }
};

GeodisExpeditionForm.prototype.hasItemsDefined = function() {
    var nbPackages = jQuery('.js-package').length;
    var definedQuantities = 0;
    for (lineNumber in this.jsonObject['items']) {
        for (var i = 0; i < nbPackages; i++) {
            definedQuantities += parseInt(jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val());
        }
    }

    return definedQuantities != 0;
};

GeodisExpeditionForm.prototype.hasMoreItems = function() {
    var nbPackages = jQuery('.js-package').length;

    var availableQuantities = 0;
    var definedQuantities = 0;
    for (lineNumber in this.jsonObject['items']) {
        availableQuantities += this.jsonObject['items'][lineNumber].quantity_available;

        for (var i = 0; i < nbPackages; i++) {
            if (!jQuery('[name="remove_package[' + i + '\]"]').val()) {
                definedQuantities += parseInt(jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val());
            }
        }
    }

    return availableQuantities != definedQuantities;
};

GeodisExpeditionForm.prototype.selectMaxQuantities = function(packageNumber) {
    for (lineNumber in this.jsonObject['items']) {
        var nbPackages = jQuery('.js-package').length;

        var definedQuantities = 0;
        var availableQuantities = this.jsonObject['items'][lineNumber].quantity_available;

        for (var i = 0; i < nbPackages; i++) {
            var value = jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val();
            if (value) {
                definedQuantities += parseInt(value);
            }
        }

        jQuery('[name="product_quantity[' + packageNumber + '\]\[' + lineNumber + '\]"]').val(availableQuantities - definedQuantities);
        jQuery('[name="product_quantity[' + packageNumber + '\]\[' + lineNumber + '\]"]').change();
    }
};

GeodisExpeditionForm.prototype.validateWLForm = function(dom) {
    var fiscalCode = dom.find('.js-product_id_fiscal_code option:checked').text();

    var codes = [
        'nb_col',
        'volume_cl',
        'volume_l',
        'n_ea',
        'shipping_duration',
        'fiscal_code_ref',
        'n_mvt'
    ];

    var hasError = false;
    var hasMissingFields = false;
    var hasInvalidIntFields = false;
    var hasIncompatibleFicalCode = false;
    var hasInvalidNumberOfDays = false;
    var hasInvalidVolume = false;
    for (var indexCode in codes) {
        var code = codes[indexCode];
        if (typeof this.jsonObject['wsRules'][fiscalCode] != 'undefined'
            && typeof this.jsonObject['wsRules'][fiscalCode][code] != 'undefined'
        ) {
            if (this.jsonObject['wsRules'][fiscalCode][code].required
                && !dom.find('input.js-product_'+code+', select.js-product_'+code).val())
            {
                dom.find('.js-wl-row-'+code).addClass('error');
                hasError = true;
                hasMissingFields = true;
            } else if (this.jsonObject['wsRules'][fiscalCode][code].is_int
                && parseInt(dom.find('input.js-product_'+code+', select.js-product_'+code).val()) != dom.find('input.js-product_'+code+', select.js-product_'+code).val()
            ) {
                dom.find('.js-wl-row-'+code).addClass('error');
                if (code == 'nb_col') {
                    hasInvalidIntFields = true;
                } else if (code == 'shipping_duration') {
                    hasInvalidNumberOfDays = true;
                }

                hasError = true;
            } else {
                if (code == 'volume_l') {
                    var input = dom.find('input.js-product_'+code).val();
                    if (Number(input) != input || input >= 10000) {
                        hasInvalidVolume = true;
                        hasError = true;
                        dom.find('.js-wl-row-'+code).addClass('error');
                    } else {
                        dom.find('.js-wl-row-'+code).removeClass('error');
                    }
                } else {
                    dom.find('.js-wl-row-'+code).removeClass('error');
                }
            }
        } else {
            dom.find('.js-wl-row-'+code).removeClass('error');
        }

        if (fiscalCode == 'DAA') {
            jQuery('select.js-product_id_fiscal_code').each((function(key, item) {
                item = jQuery(item);
                if (item.val() != dom.find('select.js-product_id_fiscal_code').val()) {
                    item.val('');
                    this.updateWl(item.parent().parent());
                }
            }).bind(this));
        } else {
            jQuery('select.js-product_id_fiscal_code option:selected').each((function(key, item) {
                item = jQuery(item);
                if (item.text() == 'DAA') {
                    hasError = true;
                    hasIncompatibleFicalCode = true;
                }
            }).bind(this));
        }
    }

    if (hasError) {
        var errorMessage = [];
        if (hasInvalidIntFields) {
            errorMessage.push(this.jsonObject.errors.invalidIntWLField);
        }
        if (hasMissingFields) {
            errorMessage.push(this.jsonObject.errors.missingWLField);
        }
        if (hasIncompatibleFicalCode) {
            errorMessage.push(this.jsonObject.errors.incompatibleFicalCode);
        }
        if (hasInvalidNumberOfDays) {
            errorMessage.push(this.jsonObject.errors.invalidNumberOfDays);
        }
        if (hasInvalidVolume) {
            errorMessage.push(this.jsonObject.errors.invalidVolume);
        }

        dom.find('.js-error').show().html(errorMessage.join('<br>'));

        return false;
    } else {
        dom.find('.js-error').hide().text('');
        return true;
    }
};

GeodisExpeditionForm.prototype.openWLPopin = function(dom) {
    if (dom.find('select.js-product_id_fiscal_code').val() == '0'
        || dom.find('select.js-product_id_fiscal_code').val() == null
    ) {
        var indexes = dom.find('select.js-product_id_fiscal_code').attr('data-id');
        var indexesTab = indexes.split('_');
        var packageIndex = indexesTab[0];
        var productIndex = indexesTab[1];
        if (typeof this.jsonObject['packages'][packageIndex] == 'undefined'
            || this.jsonObject['packages'][packageIndex]['items'][productIndex]['id_fiscal_code'] == null
            || this.jsonObject['packages'][packageIndex]['items'][productIndex]['id_fiscal_code'] == 0
        ) {
            if (!geodis.isComplete) {
                var defaultFiscalCode = this.jsonObject['defaultFiscalCode'];
                dom.find('select.js-product_id_fiscal_code').val(defaultFiscalCode);
            }
        } else {
            dom.find('select.js-product_id_fiscal_code').val(this.jsonObject['packages'][packageIndex]['items'][productIndex]['id_fiscal_code']);
        }
        this.updateWl(dom);
    }
    jQuery.fancybox.open(
        dom.find('.js-popin-wl'),
        {
            modal: true,
            beforeClose: (function() { return this.validateWLForm(jQuery('.fancybox-inner')); }).bind(this),
            afterShow: (function() {
                var currentIdFiscalCode = this.currentIdFiscalCode;
                var currentNunberOfCols = this.currentNunberOfCols;
                var currentVolumeCl = this.currentVolumeCl;
                var currentVolumeL = this.currentVolumeL;
                var currentNEa = this.currentNEa;
                var currentShippingDuration = this.currentShippingDuration;
                var currentFiscalCodeRef = this.currentFiscalCodeRef;
                var currentNMvt = this.currentNMvt;

                jQuery('.fancybox-inner .js-error').hide();
                jQuery('.fancybox-inner .js-warning').hide();
                jQuery('.fancybox-inner .js-wl-submit').click(function() {
                    jQuery.fancybox.close();
                    return false;
                });
                jQuery('.fancybox-inner .js-wl-cancel').click((function() {
                    jQuery('.fancybox-inner select.js-product_id_fiscal_code').val(currentIdFiscalCode);
                    jQuery('.fancybox-inner .js-product_nb_col').val(currentNunberOfCols);
                    jQuery('.fancybox-inner select.js-product_volume_cl').val(currentVolumeCl);
                    jQuery('.fancybox-inner .js-product_volume_l').val(currentVolumeL);
                    jQuery('.fancybox-inner .js-product_n_ea').val(currentNEa);
                    jQuery('.fancybox-inner .js-product_shipping_duration').val(currentShippingDuration);
                    jQuery('.fancybox-inner .js-product_n_mvt').val(currentNMvt);
                    jQuery('.fancybox-inner .js-product_fiscal_code_ref').val(currentFiscalCodeRef);
                    this.updateWl(jQuery('.fancybox-inner'));
                    jQuery.fancybox.close();
                    return false;
                }).bind(this));
            }).bind(this),
        }
    );
};

GeodisExpeditionForm.prototype.openNoWLPopin = function(dom) {
    jQuery.fancybox.open(
        dom.find('.js-popin-no-wl'),
        {
            modal: true,
            afterShow: function() {
                jQuery('.fancybox-inner .js-error').hide();
                jQuery('.fancybox-inner .js-warning').hide();
                jQuery('.fancybox-inner .js-no-wl-close').click(function() {
                    jQuery.fancybox.close();
                });
            }
        }
    );
};

GeodisExpeditionForm.prototype.updateWl = function(dom) {
    var fiscalCodeValue = dom.find('select.js-product_id_fiscal_code').val();
    var fiscalCode = dom.find('.js-product_id_fiscal_code option[value="'+ fiscalCodeValue +'"]').text();

    var codes = [
        'nb_col',
        'volume_cl',
        'volume_l',
        'n_ea',
        'shipping_duration',
        'fiscal_code_ref',
        'n_mvt'
    ];

    for (var indexCode in codes) {
        var code = codes[indexCode];
        if (typeof this.jsonObject['wsRules'][fiscalCode] == 'undefined'
            || typeof this.jsonObject['wsRules'][fiscalCode][code] == 'undefined')
        {
            dom.find('.js-wl-row-'+code).hide();
        } else {
            dom.find('.js-wl-row-'+code).show();
            if (this.jsonObject['wsRules'][fiscalCode][code].required) {
                dom.find('.js-wl-row-'+code).addClass('required');
            } else {
                dom.find('.js-wl-row-'+code).removeClass('required');
            }
            if (this.jsonObject['wsRules'][fiscalCode][code].is_alterable == false) {
                dom.find('.js-product_'+code).prop('readonly', 'readonly');
            } else {
                dom.find('.js-product_'+code).prop('readonly', false);
            }
        }
    }

    if (fiscalCode) {
        jQuery('.js-wl[data-id="'+dom.find('select.js-product_id_fiscal_code').data('id')+'"]').css('opacity', '1');
    } else {
        jQuery('.js-wl[data-id="'+dom.find('select.js-product_id_fiscal_code').data('id')+'"]').css('opacity', '0.5');
    }

    jQuery('.fancybox-inner .js-warning').hide();
    if (fiscalCode == 'DAA') {
        jQuery('.fancybox-inner .js-warning').show().text(this.jsonObject.warnings.daaUniq);
    }
};

GeodisExpeditionForm.prototype.computeMaxItem = function(target) {
    var targetName = jQuery(target).attr("name");
    var lineNumber = parseInt(jQuery(target).attr('data-item-number'));
    var quantityAvailable = parseInt(this.jsonObject['items'][lineNumber]['quantity_available']);
    var nbPackages = jQuery('.js-package').length;
    var totalQuantitySelected = 0;

    for (var i = 0; i < nbPackages; i++) {
        if (jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').closest('.js-package').find('.js-remove_package').val() != 1) {
            var isNan = isNaN(parseInt(jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val()));
            if (!isNan) {
                totalQuantitySelected += parseInt(jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val());
            }
        }
    }

    for (var i = 0; i < nbPackages; i++) {

        var actualValue = parseInt(jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val());
        if (isNaN(actualValue)) {
            actualValue = 0;
        }

        var newMax = quantityAvailable - totalQuantitySelected + actualValue;

        jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').find('option').remove();

        var options = '';
        for (var j = 0; j <= (newMax); j++) {
            options += '<option value="'+ j +'">'+j+'</option>';
        }
        jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').append(options);

        // Reset to actual value
        jQuery('[name="product_quantity[' + i + '\]\[' + lineNumber + '\]"]').val(actualValue);
    }
};


GeodisExpeditionForm.prototype.generatePackageRef = function() {
    var refPackage = "";
    var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    while (refPackage.length < 9) {
        refPackage += alphabet[Math.floor(Math.random() * alphabet.length)];
    }
    return refPackage;
};


GeodisExpeditionForm.prototype.fillPostData = function() {

    jQuery('.js-groupCarrierSelect option[value="' + this.postData['groupCarrier'] + '"]').prop('selected', true);
    this.updateCarrierList(this.postData['carrier']);

    var quantitiesInput = jQuery('.js-quantity');
    var nbPackages = this.postData['package_reference'].length;
    var nbPackagesJson = this.jsonObject['packages'].length;
    var nbItems = this.postData['product_quantity'][0].length;

    for (var i = 0; i < nbPackages - nbPackagesJson; i++) {
        this.addNewPackage(false);
    }


    for (var i = 0; i < nbPackages; i ++) {
        for (var j = 0; j < nbItems; j++ ) {
            jQuery('[name="product_quantity['+i+'\]\['+j+'\]"]').val(this.postData['product_quantity'][i][j]);
            if (parseInt(this.postData['product_quantity'][i][j]) > 0) {
                jQuery('[name="product_selected['+i+'\]\['+j+'\]"]').prop('checked', true);
            }
        }
    }

    for (var i = 0; i < nbPackages; i++) {
        if (this.postData['remove_package'][i]) {
            jQuery('[name="remove_package['+i+']"').parent().hide();
        }

        jQuery('[name="remove_package['+i+']"').attr('value', this.postData['remove_package'][i]);
        jQuery('[name="package_height['+i+']"').attr('value', this.postData['package_height'][i]);
        jQuery('[name="package_weight['+i+']"').attr('value', this.postData['package_weight'][i]);
        jQuery('[name="package_width['+i+']"').attr('value', this.postData['package_width'][i]);
        jQuery('[name="package_depth['+i+']"').attr('value', this.postData['package_depth'][i]);
        jQuery('[name="package_volume['+i+']"').attr('value', this.postData['package_volume'][i]);
        jQuery('[name="package_type['+i+']"').val(this.postData['package_type'][i]);
    }

    jQuery('[name=removal_date]').val(this.postData['removal_date']);
    jQuery('.js-accountSelect').val(this.postData['account']);
}

GeodisExpeditionForm.prototype.addNewPackage = function(selectMaxQuantities) {
    if (typeof selectMaxQuantities == 'undefined') {
        selectMaxQuantities = true;
    }

    var index = this.nextIndex;
    var tmpPackageTemplate = this.packageTemplate.clone();
    var packageReference = this.generatePackageRef();

    tmpPackageTemplate.attr('data-package-number', index);
    tmpPackageTemplate.find('[data-package]').each((function(packageNumber, index, item) {
        if (jQuery(item).data('package') != packageNumber) {
            jQuery(item).remove();
        }
    }).bind(this, index));

    tmpPackageTemplate.find('.js-remove_package').attr('name', 'remove_package['+index+']');

    tmpPackageTemplate.find('.js-package_reference').attr('name', 'package_reference['+index+']');
    tmpPackageTemplate.find('.js-package_reference').attr('value',  packageReference);

    tmpPackageTemplate.find('.js-package_reference').attr('name', 'package_reference['+index+']');
    tmpPackageTemplate.find('.js-package_reference_label').text(packageReference);
    tmpPackageTemplate.find('.js-package_reference').attr('value', packageReference);
    var nbProducts = this.jsonObject['items'].length;

    tmpPackageTemplate.find('.js-package_id').attr('name', 'package_id['+index+']');
    tmpPackageTemplate.find('.js-package_id').attr('value',  "");

    tmpPackageTemplate.find('.js-package_reference_label').text(packageReference);
    tmpPackageTemplate.find('.js-package_reference_label').text(packageReference);
    tmpPackageTemplate.find(".js-height").attr("value", "");
    tmpPackageTemplate.find(".js-width").attr("value", "");
    tmpPackageTemplate.find('.js-depth').attr("value", "");
    tmpPackageTemplate.find('.js-weight').attr("value", "");
    tmpPackageTemplate.find('.js-volume').attr("value", "");//Add with wassim

    tmpPackageTemplate.find('.js-height').attr("name", 'package_height['+index+']');
    tmpPackageTemplate.find('.js-width').attr("name", 'package_width['+index+']');
    tmpPackageTemplate.find('.js-depth').attr("name", 'package_depth['+index+']');
    tmpPackageTemplate.find('.js-weight').attr("name", 'package_weight['+index+']');
    tmpPackageTemplate.find('.js-volume').attr("name", 'package_volume['+ index +']');

    tmpPackageTemplate.find('.js-package_type').attr('name', 'package_type['+index+']');

    var nbProducts = this.jsonObject['items'].length;
    for (var i = 0; i < nbProducts; i++) {
        var tmpItemTemplate = this.itemTemplate.clone();
        tmpItemTemplate.find('.js-id_order_package_detail').attr('name', 'id_package_order_detail['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-id_order_package_detail').attr('value', this.jsonObject['items'][i]['item_id']);
        tmpItemTemplate.find('.js-product_selected').attr('name', 'product_selected['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-quantity').attr('name', 'product_quantity['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-quantity').attr('data-item-number', i);
        tmpItemTemplate.find('input.js-product_fiscal_code_ref').attr('name', 'product_fiscal_code_ref['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_fiscal_code_ref').attr('id', 'product_fiscal_code_ref_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_fiscal_code_ref').attr('for', 'product_fiscal_code_ref_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_n_ea').attr('name', 'product_n_ea['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_n_ea').attr('id', 'product_n_ea_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_n_ea').attr('for', 'product_n_ea_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_shipping_duration').attr('name', 'product_shipping_duration['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_shipping_duration').attr('id', 'product_shipping_duration_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_shipping_duration').attr('for', 'product_shipping_duration_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_n_mvt').attr('name', 'product_n_mvt['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_n_mvt').attr('id', 'product_n_mvt_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_n_mvt').attr('for', 'product_n_mvt_'+index+'_'+i);

        tmpItemTemplate.find('select.js-product_volume_cl').attr('name', 'product_volume_cl['+index+']['+i+']');
        tmpItemTemplate.find('select.js-product_volume_cl').attr('id', 'product_volume_cl_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_volume_cl').attr('for', 'product_volume_cl_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_volume_l').attr('name', 'product_volume_l['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_volume_l').attr('id', 'product_volume_l_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_volume_l').attr('for', 'product_volume_l_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_nb_col').attr('name', 'product_nb_col['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_nb_col').attr('id', 'product_nb_col_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_nb_col').attr('for', 'product_nb_col_'+index+'_'+i);

        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('name', 'product_id_fiscal_code['+index+']['+i+']');
        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('id', 'product_id_fiscal_code_'+index+'_'+i);
        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('data-id', index+'_'+i);
        tmpItemTemplate.find('label.js-product_id_fiscal_code').attr('for', 'product_id_fiscal_code_'+index+'_'+i);

        tmpItemTemplate.find('.js-wl').attr('data-id', index+'_'+i);

        this.updateWl(tmpItemTemplate);


        var options = '';
        for (var quantity = 0; quantity <= this.jsonObject['items'][i]['quantity_available']; quantity++) {
            options += '<option value="' + quantity + '">' + quantity + '</option>';
        }

        tmpItemTemplate.find('.js-quantity').append(options);
        tmpItemTemplate.find('.js-item_name').text(this.jsonObject['items'][i]['item_name'][1]);
        tmpItemTemplate.find('.js-combination_reference').text(this.jsonObject['items'][i]['combination_reference']);
        tmpItemTemplate.find('.js-item_reference').text(this.jsonObject['items'][i]['item_reference']);
        tmpPackageTemplate.find('.js-items').append(tmpItemTemplate);
    }

    this.nextIndex++;
    jQuery(tmpPackageTemplate).insertBefore('.js-form-content');
    if (selectMaxQuantities) {
        this.selectMaxQuantities(jQuery('.js-package').length - 1);
    }
    this.updateWLIcon();
};

GeodisExpeditionForm.prototype.getPackageTemplate = function () {
    var emptyPackageTemplate = jQuery('.js-package').clone();
    jQuery('.js-package').remove();
    return emptyPackageTemplate;
};

GeodisExpeditionForm.prototype.getItemTemplate = function () {
    var emptyItemTemplate = jQuery('.js-item').clone();
    jQuery('.js-item').remove();
    return emptyItemTemplate;
};

GeodisExpeditionForm.prototype.computeWeightPackage = function () {
    var nbItems = this.jsonObject['items'].length;
    var result = new Array();
    var tabWeight = new Array();
    var nbPackages = this.jsonObject['packages'].length;
    var tabWeightPackage = new Array();
    var weightPackage = 0.00;

    for (var i = 0; i < nbItems; i++) {
        tabWeight.push(this.jsonObject['items'][i]['item_weight']);
        // console.log(tabWeight);
    }

    for (var i = 0; i < nbPackages; i++) {
        tabWeightPackage.push(parseFloat(this.jsonObject['packages'][i]['package_weight']));
        weightPackage += parseFloat(this.jsonObject['packages'][i]['package_weight']);
    }

    jQuery('.js-package').each(function() {
        var weight = 0;
        var tabQuantity = Array();
        var test = 0;
        var ctrl = 0;
        jQuery(this).find('.js-quantity').each(function() {
            tabQuantity.push(jQuery(this).val());
        });
        for (var i = 0; i < nbItems; i ++) {
            weight += parseInt(tabQuantity[i]) * parseFloat(tabWeight[i]);
            if ((parseFloat(tabWeight[i]) > 0)) {
                ctrl = 1;
            }
        }

        if (weight == 0) {
            weight = tabWeightPackage[parseInt(this.dataset.packageNumber)];
//                weight = weightPackage;

        }
        if (jQuery(this).find('.js-weight').attr("persistent")) {
        } else {
           jQuery(this).find('.js-weight').attr("value", weight);
        //  jQuery(this).find('.js-weight').attr("value", weightPackage);
        }
        
    });
};
/*Add with team wassim novatis*/
GeodisExpeditionForm.prototype.computeVolumePackage = function () {
    var nbItems = this.jsonObject['items'].length;
    var tabVolume = new Array();
    var nbPackages = this.jsonObject['packages'].length;

    for (var i = 0; i < nbItems; i++) {
        tabVolume.push(parseFloat(this.jsonObject['items'][i]['item_volume']));
    }

    jQuery('.js-package').each(function() {
        var volume = 0;
        var tabQuantity = new Array();
        var ctrl = 0;

        // Récupérer l'index du colis
        var packageIndex = parseInt(this.dataset.packageNumber);
        console.log(packageIndex);

        jQuery(this).find('.js-quantity').each(function() {
            tabQuantity.push(parseInt(jQuery(this).val()));
        });

        for (var i = 0; i < nbItems; i++) {
            volume += tabQuantity[i] * tabVolume[i];
            if (tabVolume[i] > 0) {
                ctrl = 1;
            }
        }

        // Si le volume du colis est égal à zéro et qu'il y a des produits sélectionnés
        if (volume === 0 && ctrl === 1) {
            volume = parseFloat(this.jsonObject['packages'][packageIndex]['package_volume']);
        }
        // Mettre à jour le champ de volume du colis
        //jQuery(this).find('.js-volume').val(volume.toFixed(3));
    });
};
/*End*/
GeodisExpeditionForm.prototype.fillTemplatePackage = function (index) {
    var tmpPackageTemplate = this.packageTemplate.clone();

    tmpPackageTemplate.attr('data-package-number', index);
    tmpPackageTemplate.find('[data-package]').each((function(packageNumber, index, item) {
        if (jQuery(item).data('package') != packageNumber) {
            jQuery(item).remove();
        }
    }).bind(this, index));

    tmpPackageTemplate.find('.js-remove_package').attr('name', 'remove_package['+index+']');

    tmpPackageTemplate.find('.js-package_reference').attr('name', 'package_reference['+index+']');
    tmpPackageTemplate.find('.js-package_reference').attr('value',  this.jsonObject['packages'][index]['package_reference']);

    tmpPackageTemplate.find('.js-package_id').attr('name', 'package_id['+index+']');
    tmpPackageTemplate.find('.js-package_id').attr('value',  this.jsonObject['packages'][index]['package_id']);


    tmpPackageTemplate.find('.js-package_reference_label').text('#'+this.jsonObject['packages'][index]['package_reference']);
    tmpPackageTemplate.find('.js-package_reference_label').text(this.jsonObject['packages'][index]['package_reference']);
    tmpPackageTemplate.find(".js-height").attr("value", this.jsonObject['packages'][index]['package_height']);
    tmpPackageTemplate.find(".js-width").attr("value", this.jsonObject['packages'][index]['package_width']);
    tmpPackageTemplate.find('.js-depth').attr("value", this.jsonObject['packages'][index]['package_depth']);
    tmpPackageTemplate.find('.js-weight').attr("value", this.jsonObject['packages'][index]['package_weight']);
    tmpPackageTemplate.find('.js-volume').attr("value", this.jsonObject['packages'][index]['package_volume']);

    tmpPackageTemplate.find('.js-height').attr("name", 'package_height['+index+']');
    tmpPackageTemplate.find('.js-width').attr("name", 'package_width['+index+']');
    tmpPackageTemplate.find('.js-depth').attr("name", 'package_depth['+index+']');
    tmpPackageTemplate.find('.js-weight').attr("name", 'package_weight['+index+']');
    tmpPackageTemplate.find('.js-volume').attr("name", 'package_volume['+ index +']');

    tmpPackageTemplate.find('.js-package_type').attr('name', 'package_type['+index+']');

    tmpPackageTemplate.find('select option[value="' + this.jsonObject['packages'][index]['package_type'] + '"]').attr("selected",true);

    var nbProducts = this.jsonObject['items'].length;
    for (var i = 0; i < nbProducts; i++) {
        var tmpItemTemplate = this.itemTemplate.clone();
        tmpItemTemplate.find('.js-id_order_package_detail').attr('name', 'id_package_order_detail['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-id_order_package_detail').attr('value', this.jsonObject['items'][i]['item_id']);
        tmpItemTemplate.find('.js-product_selected').attr('name', 'product_selected['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-quantity').attr('name', 'product_quantity['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-quantity').attr('data-item-number', i);
        tmpItemTemplate.find('input.js-product_fiscal_code_ref').attr('name', 'product_fiscal_code_ref['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_fiscal_code_ref').attr('id', 'product_fiscal_code_ref_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_fiscal_code_ref').attr('for', 'product_fiscal_code_ref_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_n_ea').attr('name', 'product_n_ea['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_n_ea').attr('id', 'product_n_ea_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_n_ea').attr('for', 'product_n_ea_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_shipping_duration').attr('name', 'product_shipping_duration['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_shipping_duration').attr('id', 'product_shipping_duration_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_shipping_duration').attr('for', 'product_shipping_duration_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_n_mvt').attr('name', 'product_n_mvt['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_n_mvt').attr('id', 'product_n_mvt_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_n_mvt').attr('for', 'product_n_mvt_'+index+'_'+i);

        tmpItemTemplate.find('select.js-product_volume_cl').attr('name', 'product_volume_cl['+index+']['+i+']');
        tmpItemTemplate.find('select.js-product_volume_cl').attr('id', 'product_volume_cl_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_volume_cl').attr('for', 'product_volume_cl_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_volume_l').attr('name', 'product_volume_l['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_volume_l').attr('id', 'product_volume_l_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_volume_l').attr('for', 'product_volume_l_'+index+'_'+i);

        tmpItemTemplate.find('input.js-product_nb_col').attr('name', 'product_nb_col['+index+']['+i+']');
        tmpItemTemplate.find('input.js-product_nb_col').attr('id', 'product_nb_col_'+index+'_'+i);
        tmpItemTemplate.find('label.js-product_nb_col').attr('for', 'product_nb_col_'+index+'_'+i);

        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('name', 'product_id_fiscal_code['+index+']['+i+']');
        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('id', 'product_id_fiscal_code_'+index+'_'+i);
        tmpItemTemplate.find('select.js-product_id_fiscal_code').attr('data-id', index+'_'+i);
        tmpItemTemplate.find('label.js-product_id_fiscal_code').attr('for', 'product_id_fiscal_code_'+index+'_'+i);

        tmpItemTemplate.find('.js-wl').attr('data-id', index+'_'+i);

        for (var quantity = 0; quantity <= this.jsonObject['items'][i]['quantity_available']; quantity++) {
            tmpItemTemplate.find('.js-quantity').append(
                jQuery("<option></option>").attr("value", quantity).text(quantity)
            );
        }

        if (this.jsonObject['packages'][index]['items'][i]['quantity'] != null) {
            tmpItemTemplate.find('.js-quantity').val(this.jsonObject['packages'][index]['items'][i]['quantity']);
            if (this.jsonObject['packages'][index]['items'][i]['quantity'] > 0) {
                tmpItemTemplate.find('.js-product_selected').prop('checked', true);
            }
        } else {
            var maxQuantity = tmpItemTemplate.find('.js-quantity option:last-child').val();
            tmpItemTemplate.find('.js-quantity').val(maxQuantity);
        }

        tmpItemTemplate.find('.js-product_selected').attr('name', 'product_selected['+ index +']['+ i +']');
        tmpItemTemplate.find('.js-item_name').text(this.jsonObject['items'][i]['item_name'][1]);
        tmpItemTemplate.find('.js-combination_reference').text(this.jsonObject['items'][i]['combination_reference']);
        tmpItemTemplate.find('.js-item_reference').text(this.jsonObject['items'][i]['item_reference']);
        tmpPackageTemplate.find('.js-items').append(tmpItemTemplate);

        tmpItemTemplate.find('.js-product_wl_active').attr(
            'checked',
            this.jsonObject['packages'][index]['items'][i]['id_fiscal_code'] ? 'checked' : false
        );
        tmpItemTemplate.find('select.js-product_id_fiscal_code').val(this.jsonObject['packages'][index]['items'][i]['id_fiscal_code']);
        tmpItemTemplate.find('input.js-product_nb_col').val(this.jsonObject['packages'][index]['items'][i]['nb_col']);
        tmpItemTemplate.find('select.js-product_volume_cl').val(this.jsonObject['packages'][index]['items'][i]['volume_cl']);
        tmpItemTemplate.find('input.js-product_volume_l').val(this.jsonObject['packages'][index]['items'][i]['volume_l']);
        tmpItemTemplate.find('input.js-product_n_mvt').val(this.jsonObject['packages'][index]['items'][i]['n_mvt']);
        tmpItemTemplate.find('input.js-product_shipping_duration').val(this.jsonObject['packages'][index]['items'][i]['shipping_duration']);
        tmpItemTemplate.find('input.js-product_n_ea').val(this.jsonObject['packages'][index]['items'][i]['n_ea']);
        tmpItemTemplate.find('input.js-product_fiscal_code_ref').val(this.jsonObject['packages'][index]['items'][i]['fiscal_code_ref']);

    }
    jQuery(tmpPackageTemplate).insertBefore('.js-form-content');

    jQuery('.js-popin-wl').each((function(key, item) {
        this.updateWl(jQuery(item));
    }).bind(this));
};

GeodisExpeditionForm.prototype.checkRemovalDate = function() {
    if (!jQuery('.js-carrierSelect').val()) {
        return;
    }

    jQuery('.js_alertDayOff').hide();
    jQuery.ajax({
        url: '#',
        data: {
            'removalDate' : 'getDaysOff',
            'idGeodisCarrier' : jQuery('.js-carrierSelect').val(),
            'controller': geodis.admin+'Shipment',
            'token': geodis.token,
        },
        dataType : 'json',
        type: 'get',
    }).done(
        (function(data) {
            jQuery('.js_alertWl').hide();

            if (data['ok'] == false || data['daysOff'] == null) {
                jQuery('.js_alertWs').show();
                return;
            }

            jQuery('.js-firstDayAvailable').val(data['firstDay']);

            var daysOff = data['daysOff'];
            var groupCarrierDelay = 0;

            var date = new Date(data['firstDay']);
            if (this.userDefinedDate) {
                var userDefinedDate = new Date(this.userDefinedDate);

                if (userDefinedDate > date) {
                    date = userDefinedDate;
                }
            } else {
                var value = jQuery('.js-groupCarrierSelect').val();
                groupCarrierDelay = jQuery('.js-groupCarrierSelect option[value="'+value+'"]').data('delay');
            }

            var finalDate = this.addGroupDelay(date, groupCarrierDelay, daysOff);

            jQuery('.js-removalDate').val(this.formatDate(finalDate));
        }).bind(this)
    ).error(
        (function(data) {
            jQuery('.js_alertWs').show();
        }).bind(this)
    );
}

GeodisExpeditionForm.prototype.getPrestationAvailableFromCache = function(callback) {
    if (!this.availablePrestationList) {
        this.getPrestationAvailable('none', (function(availablePrestationList) {
            this.availablePrestationList = availablePrestationList;
            callback(this.availablePrestationList);
        }).bind(this), true);
    } else {
        callback(this.availablePrestationList);
    }
}

// Check all the prestations available for the weight and numbers of boxes/pallets indicated
GeodisExpeditionForm.prototype.getPrestationAvailable = function(groupCarrierReference, callback, init) {
    var init = init || false;
    var nbPackages = 0;
    var nbPallets = 0;
    var weight = 0;

    if (!init) {
        jQuery('.js-remove_package').filter(function() {
            if (jQuery(this).attr('value') != 1) {
                if (jQuery(this).closest('.js-package').find('.js-package_type').val() == 'box') {
                    nbPackages++;
                } else {
                    nbPallets++;
                }
            }
        });
        jQuery('.js-weight').each(function(item){
            var quantity = jQuery('.js-quantity').val();
//            weight += parseFloat(jQuery(this).val()) * quantity;
            weight += parseFloat(jQuery(this).val());
        });
    }

    jQuery.ajax({
        url: '#',
        data: {
            'call' : 'getPrestationAvailable',
            'weight' : weight,
            'nbPackages' : nbPackages,
            'nbPallets' : nbPallets,
            'idOrder' : geodis.idOrder,
            'controller': geodis.admin+'Shipment',
            'groupCarrierReference': groupCarrierReference,
            'token': geodis.token,
        },
        dataType : 'json',
        type: 'GET'
    }).done((function(data) {
        callback(data);
    }).bind(this)).error((function(data) {
        jQuery('.js-alertWsPrestation').show();
    }).bind(this));
}

// If no quantity selected, disable the submit buttons
GeodisExpeditionForm.prototype.checkQuantity = function() {
    var totalQuantity = 0;
    var quantityElements = jQuery(document).find('.js-package').find('.js-quantity');
    for (var i = 0; i < quantityElements.length; i++) {
        totalQuantity += parseInt(jQuery(quantityElements[i]).val(), 10);
    }

    if (totalQuantity == 0) {
        jQuery('.js-submit').prop("disabled", true);
        jQuery('.js-submitAndNew').prop("disabled", true);

        return;
    }

    jQuery('.js-submit').prop("disabled", false);
    jQuery('.js-submitAndNew').prop("disabled", false);

    return;
}

GeodisExpeditionForm.prototype.checkPrestation = function() {
    var currentIdCarrier = jQuery('.js-carrierSelect').val();
    var groupCarrierReference = jQuery('.js-groupCarrierSelect option:selected').data('reference');

    this.getPrestationAvailable(groupCarrierReference, (function(prestationList) {
        if (prestationList == null || !jQuery('.js-carrierSelect').val()) {
            return;
        }

        var currentIdPrestation = geodis.prestationCollection[currentIdCarrier].idPrestation.toString();

        if (prestationList.indexOf(currentIdPrestation) == -1) {
            jQuery('.js-submit').prop("disabled", true);
            jQuery('.js-submitAndNew').prop("disabled", true);
            jQuery('.js-alertWsPrestation').show();
            return;
        }

        jQuery('.js-submit').prop("disabled", false);
        jQuery('.js-submitAndNew').prop("disabled", false);
        jQuery('.js-alertWsPrestation').hide();
        return;
    }).bind(this));
}

GeodisExpeditionForm.prototype.addGroupDelay = function(startingDate, groupCarrierDelay, daysOff) {
    for (var i = 0; i <= groupCarrierDelay; i++) {
        if (i != 0) {
            startingDate.setDate(startingDate.getDate() + 1);
        }
        while (daysOff.indexOf(this.formatDate(startingDate)) != -1) {
            startingDate.setDate(startingDate.getDate() + 1)
            if (!groupCarrierDelay) {
                jQuery('.js_alertDayOff').show();
            }
        }
    }

    return startingDate;
}

GeodisExpeditionForm.prototype.formatDate = function(date) {
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

GeodisExpeditionForm.prototype.updateCarrierList = function(idCarrierToSelect) {
    this.getPrestationAvailableFromCache((function(availablePrestationList) {
        jQuery('.js_alertNoCarrierAvailable').hide();
        var idGroupCarrier = parseInt(jQuery('.js-groupCarrierSelect').val());
        var carrierCollection = geodis.carrierCollection[idGroupCarrier];
        var prestationCollection =  geodis.prestationCollection;
        var accountCollection = geodis.accountCollection;

        jQuery('.js-carrierSelect option').remove();
        if (typeof(carrierCollection) == "undefined") {
            jQuery('.js_alertNoCarrierAvailable').show();
            return;
        }

        carrierCollection.forEach(function(element) {
            var carrierLibelle = prestationCollection[element['id']].label + ' (' + accountCollection[element['id']] + ')';
            if (availablePrestationList.indexOf(element.idPrestation) != -1 || !availablePrestationList.length) {
                jQuery('.js-carrierSelect').append(
                    jQuery("<option></option>").attr("value", element['id']).text(carrierLibelle)
                );
            }
        });

        if (!jQuery('.js-carrierSelect option').length) {
            jQuery('.js_alertNoCarrierAvailable').show();
        }

        if (idCarrierToSelect != null) {
            jQuery('.js-carrierSelect option[value="' + idCarrierToSelect + '"]').prop('selected', true);
        }

        this.checkPrestation();
        this.updateWLIcon();
        this.checkRemovalDate();
        this.initObserveForm();
    }).bind(this));
}

GeodisExpeditionForm.prototype.hideWarningMessages = function() {
    jQuery('.js-alertModule').hide();
    jQuery('.js_alertNoCarrierAvailable').hide();
    jQuery('.js-alertPrinter').hide();
    jQuery('.js-alertNoGroupCarrierAvailable').hide();
    jQuery('.js_alertDayOff').hide();
    jQuery('.js_alertWl').hide();
    jQuery('.js_alertWs').hide();
    jQuery('.js-alertWsPrestation').hide();
    jQuery('.js-alertWsPrinter').hide();
}

GeodisExpeditionForm.prototype.initObservers = function() {
    jQuery('[name=add_package]').on("click", (function(event) {
        this.addNewPackage();
        var quantityElements = jQuery(document).find('.js-package').find('.js-quantity');
        for (var i = 0; i < quantityElements.length; i++) {
            this.computeMaxItem(quantityElements[i]);
        }
    }).bind(this));

    jQuery(document).on("click", '[name=remove_package]', (function(event) {
        var $elm = jQuery(event.target);
        $elm.closest('.js-package').find('.js-remove_package').val(1);
        $elm.closest('.js-package').hide();

        var quantityElements = $elm.closest('.js-package').find('.js-quantity');
        for (var i = 0; i < quantityElements.length; i++) {
            this.computeMaxItem(quantityElements[i]);
        }

        this.autoHideButton();
    }).bind(this));

    jQuery(document).on("change", '.js-groupCarrierSelect', (function() {
        this.updateCarrierList(null);
    }).bind(this));

    jQuery(document).on("change", '.js-product_volume_cl', (function(event) {
        this.computeWlVolume(event.target);
    }).bind(this));

    jQuery(document).on("change", '.js-product_nb_col', (function(event) {
        this.computeWlVolume(event.target);
    }).bind(this));

    jQuery(document).on("change", '.js-package_type', (function() {
        this.checkPrestation();
    }).bind(this));

    jQuery(document).on("click", '.js-printLabel', (function(event) {
        if (geodis.thermalPrintingStatus == 1) {
            event.preventDefault();
            this.printLabel();
        }
    }).bind(this));

    jQuery(document).on("change", '.js-carrierSelect', (function() {
        this.checkPrestation();
        this.updateWLIcon();
    }).bind(this));

    jQuery(document).on("change", '.js-float', (function(event) {
        jQuery(event.target).val(jQuery(event.target).val().replace(/\,/g, '.'));
    }).bind(this));

    jQuery(document).on("change", '.js-product_selected', function(event) {
        if (!this.checked) {
            jQuery(this).closest('.js-item').find('option[value="0"]').attr("selected",true);
        }
    });

    jQuery(document).on("change", '.js-product_id_fiscal_code', (function(event) {
        this.updateWl(jQuery(event.target).parent().parent().parent().parent().parent());
    }).bind(this));

    jQuery(document).on("change", '.js-product_selected', (function(event) {
        var quantityElements = jQuery(event.target).closest('.js-package').find('.js-quantity');
        for (var i = 0; i < quantityElements.length; i++) {
            this.computeMaxItem(quantityElements[i]);
        }
    }).bind(this));

    jQuery(document).on("change", '.js-weight', (function() {
        this.checkPrestation();
    }).bind(this));

    jQuery(document).on("click", '.js-button_remove', (function() {
        this.checkPrestation();
    }).bind(this));

    jQuery(document).on("click", '.js-button_add', (function() {
        this.checkPrestation();
    }).bind(this));

    jQuery(document).on("keyup", '.js-weight', function() {
        jQuery(this).attr("persistent", "1");
    });

    jQuery(document).on("change", '[name=checkbox]', (function() {
        this.computeWeightPackage();
        this.computeVolumePackage();//Add with team wassim novatis

    }).bind(this));

    jQuery(document).on("change", '.js-quantity', (function(event) {
        this.checkQuantity();
        this.computeMaxItem(event.target);
        this.computeWeightPackage();
        this.computeVolumePackage();//Add with team wassim novatis
        this.autoHideButton();
    }).bind(this));

    jQuery(document).on("change", '.js-quantity', function() {
        if (jQuery(this).val() == 0) {
            jQuery(this).closest('.js-item').find('.js-product_selected').prop('checked', false);
        } else {
            jQuery(this).closest('.js-item').find('.js-product_selected').prop('checked', true);
        }
    });

    jQuery(document).on("click", '.js-wl', (function(event) {
        var idCarrier = jQuery('.js-carrierSelect').val();
        var idGroupCarrier = jQuery('.js-groupCarrierSelect').val();
        var idAccountPrestation = 0;

        geodis.carrierCollection[idGroupCarrier].forEach(function(element) {
            if (element['id'] == idCarrier) {
                idAccountPrestation = element['id_account_prestation'];
            }
        });

        if (idAccountPrestation != 0 && geodis.wl[idAccountPrestation] == 1) {
            this.currentIdFiscalCode = jQuery(event.target).parent().find('select.js-product_id_fiscal_code').val();
            this.currentNunberOfCols = jQuery(event.target).parent().find('input.js-product_nb_col').val();
            this.currentVolumeCl = jQuery(event.target).parent().find('select.js-product_volume_cl').val();
            this.currentVolumeL = jQuery(event.target).parent().find('input.js-product_volume_l').val();
            this.currentNEa = jQuery(event.target).parent().find('input.js-product_n_ea').val();
            this.currentShippingDuration = jQuery(event.target).parent().find('input.js-product_shipping_duration').val();
            this.currentFiscalCodeRef = jQuery(event.target).parent().find('input.js-product_fiscal_code_ref').val();
            this.currentNMvt = jQuery(event.target).parent().find('input.js-product_n_mvt').val();
            this.openWLPopin(jQuery(event.target).parent());
        } else {
            this.openNoWLPopin(jQuery(event.target).parent());
        }
    }).bind(this));

    jQuery(document).on("change", '.js-removalDate', (function(event) {
        this.userDefinedDate = jQuery('.js-removalDate').val();
        jQuery('.js_alertDayOff').hide();
        this.checkRemovalDate();
    }).bind(this));
};

GeodisExpeditionForm.prototype.updateWLIcon = function() {
    var idCarrier = jQuery('.js-carrierSelect').val();
    var idGroupCarrier = jQuery('.js-groupCarrierSelect').val();
    var idAccountPrestation = 0;

    geodis.carrierCollection[idGroupCarrier].forEach(function(element) {
        if (element['id'] == idCarrier) {
            idAccountPrestation = element['id_account_prestation'];
        }
    });

    if (idAccountPrestation != 0 && geodis.wl[idAccountPrestation] == 1) {
        jQuery('.js-wl').show();
    } else {
        jQuery('.js-wl').hide();
    }
};

GeodisExpeditionForm.prototype.printLabel = function() {
    if (!this.checkModuleInstallation()) {
        return;
    }

    jQuery('.js-alertPrinter').hide();
    jQuery('.js-alertWsPrinter').hide();
    var webServiceResponse = null;

    jQuery.ajax({
        url: '#',
        data: {
            'call' : 'getLabels',
            'controller': geodis.admin+'Shipment',
            'token': geodis.token,
            'id' : geodis.idShipment,
        },
        async : false,
//        dataType : 'json',
        type: 'get',
    }).done(
        (function(data) {
            webServiceResponse = data;
        }).bind(this)
    ).error(
        (function(data) {
            jQuery('.js-alertWsPrinter').show();
        }).bind(this)
    );

    try {
        var formData = new FormData();

        formData.append('data', webServiceResponse);
        var request = new XMLHttpRequest();
        request.open("POST", "https://localhost:" + geodis.printingPort + "/printers/print?app=espaceclient", false);
        request.send(formData);

        data = JSON.parse(request.responseText);
        if (!data.id) {
            jQuery('.js-alertPrinter').show();
        }
    } catch (e) {
        jQuery('.js-alertPrinter').show();
    }
};

GeodisExpeditionForm.prototype.checkModuleInstallation = function() {
    jQuery('.js-alertModule').hide();
    var request = new XMLHttpRequest();
    try {
        request.open("GET", "https://localhost:" + geodis.printingPort + "/version", false);
        request.send();

        data = JSON.parse(request.responseText);
        if (!data.version) {
            jQuery('.js-alertModule').show();
            return false;
        }
    } catch (e) {
        jQuery('.js-alertModule').show();
        return false;
    }
    return true;
};

GeodisExpeditionForm.prototype.computeWlVolume = function(target) {
    var fancybox = jQuery(target).closest('.fancybox-inner');
    var nbCol = parseFloat(jQuery(fancybox).find('input.js-product_nb_col').val());
    var capacity = parseFloat(jQuery(fancybox).find('select.js-product_volume_cl').val());

    if (isNaN(nbCol) || isNaN(capacity)) {
        jQuery(fancybox).find('input.js-product_volume_l').val('');
    } else {
        var result = nbCol * capacity / 100;
        jQuery(fancybox).find('input.js-product_volume_l').val(result);
    }

}
