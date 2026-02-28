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

"use strict";

var GeodisCarrierSelector = function(idPrestaShopCarrier, jsonConfig, jsonValues, getTemplateUrl, submitUrl, pointListUrl, formatPriceUrl, quoteAddressUrl, intlUtilsUrl, markerShopUrl, markerSelectedShopUrl, markerHomeUrl, sentenceDistanceKilometers, sentenceDistanceMeters, mapEnabled, checkboxIdentifier, quote, module, GeodisJQuery) {
    this.idPrestaShopCarrier = idPrestaShopCarrier;
    this.jsonConfig = jsonConfig;
    this.jsonValues = jsonValues;
    this.submitUrl = submitUrl;
    this.pointListUrl = pointListUrl;
    this.optionsSelectedByCarrier = [];
    this.intlTelephone = [];
    this.intlMobilephone = [];
    this.intlUtilsUrl = intlUtilsUrl;
    this.formatPriceUrl = formatPriceUrl;
    this.quoteAddressUrl = quoteAddressUrl;
    this.markerShopUrl = markerShopUrl;
    this.markerSelectedShopUrl = markerSelectedShopUrl;
    this.markerHomeUrl = markerHomeUrl;
    this.sentenceDistanceKilometers = sentenceDistanceKilometers;
    this.sentenceDistanceMeters = sentenceDistanceMeters;
    this.mapEnabled = mapEnabled;
    this.useGlobalMap = true;
    this.checkboxIdentifier = checkboxIdentifier;
    this.module = module;
    this.GeodisJQuery = GeodisJQuery;
    this.quote = quote;

    this.relayMap = [];
    this.timeoutMapMove = [];

    if (this.jsonValues.idCarrier) {
        this.optionsSelectedByCarrier[this.jsonValues.idCarrier] = this.jsonValues.idOptionList;
    }

    this.template = new GeodisTemplate(getTemplateUrl, this, this.jsonValues.token, this.GeodisJQuery)

    this.isOpen = false;
    this.pickupPointList = [];
    this.mapInitialized = [];
    this.mapDefaultCenter = [];
    this.wasOpened = false;

    this.errors = [];

    this.$popin = this.GeodisJQuery('<div></div>');

    if (this.jsonConfig.carrierList.length === 1) {
        this.jsonValues.idCarrier = this.jsonConfig.carrierList[0].id_carrier;
    }

    // Remove empty idOption from idOptionList
    for (var i in this.jsonValues.idOptionList) {
        if (i != parseInt(i)) {
            continue;
        }

        if (!this.jsonValues.idOptionList[i]) {
            this.jsonValues.idOptionList.splice(i, 1);
        }
    }

    if (!this.jsonValues.info) {
        this.jsonValues.info = [];
    }

    // Classical case (click on the checkbox)
    this.GeodisJQuery(document).on(
        'click',
        this.checkboxIdentifier,
        (function(e) { this.open(); return false; }).bind(this)
    );

    if (this.GeodisJQuery(this.checkboxIdentifier).is(':visible') && this.GeodisJQuery(this.checkboxIdentifier).is(':checked')) {
        this.open();
    }
        // Case of classic theme of PrestaShop (ckeckbox not visible)
/*    this.GeodisJQuery(this.checkboxIdentifier).closest('.delivery-option').click(
        (function(e) { this.open(); return false; }).bind(this)
    );

//    window.setInterval((function() {
        if (this.GeodisJQuery(this.checkboxIdentifier).is(':visible')) {
//            this.GeodisJQuery(this.checkboxIdentifier).closest('.row').click((function() {
            this.GeodisJQuery(this.checkboxIdentifier).closest('.delivery-option').click((function() {
                this.open();
            }).bind(this));

            if (this.wasOpened) {
                return;
            }

            if (this.GeodisJQuery(this.checkboxIdentifier).is(':checked')) {
                this.open();
            }
        }
//    }).bind(this), 250);
*/
    // Reset idOptionList is current carrier do not exist in list of carrier
    this.resetCarrierList();

    this.listenContinueButton();
};

GeodisCarrierSelector.prototype.listenContinueButton = function() {
    this.GeodisJQuery('body').on('click', '[name=confirmDeliveryOption]', (function () {
        if (!this.GeodisJQuery('#delivery_option_'+this.idPrestaShopCarrier).is(':checked')) {
            return true;
        }

        if (!this.validate()) {
            this.open((function() {
                this.validate();
            }).bind(this));
            return false;
        }

        return true;
    }).bind(this));
};

GeodisCarrierSelector.prototype.resetCarrierList = function() {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        if (this.jsonValues.idCarrier == carrier.id_carrier) {
            return;
        }
    }
    this.jsonValues.idOptionList = [];
};

GeodisCarrierSelector.prototype.initListener = function() {
    this.GeodisJQuery(this.$popin).on('click', '[data-action]', (function(e) {
        var $elm = this.GeodisJQuery(e.target).closest('[data-action]');

        this[$elm.data('action')]($elm.data('values'), $elm, $elm.data('parent'));

        return false;
    }).bind(this));

    this.GeodisJQuery(this.$popin).on('change', '[data-change]', (function(e) {
        var $elm = this.GeodisJQuery(e.target);

        this[$elm.data('change')]($elm.data('values'), $elm, $elm.data('parent'));

        return false;
    }).bind(this));
};

GeodisCarrierSelector.prototype.switchRequiredEntry = function() {
    // Email not required if mobilephone is set
    if (this.$popin.find('.js-mobilephone').val().length) {
        this.$popin.find('.geodisInfo__inputContainer--email').removeClass('geodisInfo__inputContainer--required');
    } else {
        this.$popin.find('.geodisInfo__inputContainer--email').addClass('geodisInfo__inputContainer--required');
    }

    // Mobilephone not required if phone is set
    if (this.$popin.find('.js-telephone').val().length) {
        this.$popin.find('.geodisInfo__inputContainer--mobilephone').removeClass('geodisInfo__inputContainer--required');
    } else {
        this.$popin.find('.geodisInfo__inputContainer--mobilephone').addClass('geodisInfo__inputContainer--required');
    }

    // Telephone is not required if mobilephone is set
    if (this.$popin.find('.js-mobilephone').val().length) {
        this.$popin.find('.geodisInfo__inputContainer--telephone').removeClass('geodisInfo__inputContainer--required');
    } else {
        this.$popin.find('.geodisInfo__inputContainer--telephone').addClass('geodisInfo__inputContainer--required');
    }
};

GeodisCarrierSelector.prototype.submit = function() {
    if (!this.validate()) {
        this.showErrors();
        return;
    }

    this.processForm();

    this.GeodisJQuery('#delivery_option_'+this.idPrestaShopCarrier).prop('checked', true).change();

    // Add the idPrestaShopCarrier to datas to transfert
    var fullJsonValues = JSON.parse(JSON.stringify(this.jsonValues));
    fullJsonValues['idPrestaShopCarrier'] = this.idPrestaShopCarrier;

    this.GeodisJQuery.ajax({
        url: this.submitUrl,
        data: fullJsonValues,
        dataType: 'json'
    }).done((function(data) {
        // Magento
        this.GeodisJQuery(this.checkboxIdentifier).parent().find('.price').html(data.price);
        this.GeodisJQuery(this.checkboxIdentifier).prop('checked', true);

        // PrestaShop
        this.GeodisJQuery('[for="delivery_option_'+this.idPrestaShopCarrier+'"] .carrier-price').html(data.price);

        // Magento
        if (typeof shippingMethod != 'undefined') {
            shippingMethod.save()
        }

        this.close();
    }).bind(this));
};

GeodisCarrierSelector.prototype.processForm = function() {
    this.$popin.find('[data-process]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);
        if (!$elm.closest('.js-prestation').hasClass('geodisPrestation--selected')) {
            return;
        }

        var callback = $elm.data('process');

        this[callback]($elm.val(), this.GeodisJQuery(elm).data('values'));

    }).bind(this));
};

GeodisCarrierSelector.prototype.processFloor = function(value, values) {
    var carrier = this.getSelectedCarrier();
    for (var i in this.jsonValues.idOptionList) {
        if (i != parseInt(i)) {
            continue;
        }

        var idOption = this.jsonValues.idOptionList[i];
        var option = this.getCarrierOption(carrier.id_carrier, idOption);

        if (option.id_option == values) {
            this.setInfo('floor', value);
        }
    }
};

GeodisCarrierSelector.prototype.processTelephone = function(value, values) {
    var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType, this.jsonValues.idCarrier);

    this.setInfo('telephone', this.intlTelephone[idCarrier].getNumber());
    this.setInfo('dialCodeTelephone', this.intlTelephone[idCarrier].getSelectedCountryData().dialCode);
    this.setInfo('nationalTelephone', this.intlTelephone[idCarrier].getNumber(intlTelInputUtils.numberFormat.NATIONAL));
};

GeodisCarrierSelector.prototype.processMobilephone = function(value, values) {
    var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType, this.jsonValues.idCarrier);

    this.setInfo('mobilephone', this.intlMobilephone[idCarrier].getNumber());
    this.setInfo('dialCodeMobilephone', this.intlMobilephone[idCarrier].getSelectedCountryData().dialCode);
    this.setInfo('nationalMobilephone', this.intlMobilephone[idCarrier].getNumber(intlTelInputUtils.numberFormat.NATIONAL));
};

GeodisCarrierSelector.prototype.processEmail = function(value, values) {
    this.setInfo('email', value);
};

GeodisCarrierSelector.prototype.processDigicode = function(value, values) {
    this.setInfo('digicode', value);
};

GeodisCarrierSelector.prototype.processInstruction = function(value, values) {
    this.setInfo('instruction', value);
};

GeodisCarrierSelector.prototype.setInfo = function(attribute, value) {
    if (typeof this.jsonValues['info'] == 'undefined') {
        this.jsonValues['info'] = [];
    }

    for (var i in this.jsonValues.info) {
        if (i != parseInt(i)) {
            continue;
        }

        var info = this.jsonValues.info[i];

        if (info.name == attribute) {
            info.value = value;
            return;
        }
    }

    this.jsonValues.info.push({
        name: attribute,
        value: value
    });
};

GeodisCarrierSelector.prototype.validate = function() {
    this.errors = [];
    this.$popin.find('.geodisError').removeClass('geodisError');
    this.$popin.find('.js-error').html('');

    this.$popin.find('[data-validate]').each((function(key, elm) {
        var $elm = this.GeodisJQuery(elm);

        if (!$elm.closest('.js-prestation').hasClass('geodisPrestation--selected')) {
            return;
        }

        var callback = $elm.data('validate');

        if (!this[callback]($elm.val(), $elm.data('values'))) {
            $elm.addClass('geodisError');
            $elm.closest('.js-input-container').addClass('geodisError');
            this.errors.push(this.GeodisJQuery(elm).closest('.js-input-container').data('error'));
        };

    }).bind(this));

    if (this.errors.length) {
        return false;
    }

    return true;
};

GeodisCarrierSelector.prototype.showErrors = function() {
    this.errors.forEach((function(message) {
        this.$popin.find('.js-error').append(
            this.GeodisJQuery('<li></li>').text(message)
        );
    }).bind(this));
};

GeodisCarrierSelector.prototype.canSubmit = function(value, values) {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        if (carrier.id_carrier == this.jsonValues.idCarrier) {
            if (this.jsonConfig.carrierType == 'classic' || this.jsonValues.codeWithdrawalAgency || this.jsonValues.codeWithdrawalPoint) {
                return true;
            }
        }
    }

    return false;
};

GeodisCarrierSelector.prototype.validateHasPoint = function(value, values) {
    if (this.jsonValues.codeWithdrawalPoint || this.jsonValues.codeWithdrawalAgency) {
        return true;
    }

    return false;
};

GeodisCarrierSelector.prototype.validateFloor = function(value, values) {
    var carrier = this.getSelectedCarrier();
    for (var i in this.jsonValues.idOptionList) {
        if (i != parseInt(i)) {
            continue;
        }

        var idOption = this.jsonValues.idOptionList[i];
        var option = this.getCarrierOption(carrier.id_carrier, idOption);

        if (option.id_option == values) {
            if (!value.length && option.code == 'livEtage') {
                return false;
            }
            if (null == value.match(/^\d+$/) && value.length) {
                return false;
            }
        }
    }

    return true;
};

GeodisCarrierSelector.prototype.validateTelephone = function(value, values) {
    // Not required if mobilephone is set
    if (!value.length && this.$popin.find('.js-mobilephone').val().length) {
        return true;
    }

    var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType, this.jsonValues.idCarrier);

    if (!this.intlTelephone[idCarrier]) {
        return true;
    }

    if (!this.intlTelephone[idCarrier].isValidNumber()) {
        return false;
    }

    if (this.intlTelephone[idCarrier].getNumberType() == 1) {
        // If it's a mobile copy on mobile phone
        if (!this.$popin.find('.js-mobilephone').val().length) {
            this.$popin.find('.js-mobilephone').val(this.$popin.find('.js-telephone').val());
            this.$popin.find('.js-telephone').val('');
            return true;
        }
        return false;
    }

    return true;
};

GeodisCarrierSelector.prototype.validateMobilephone = function(value, values) {
    // Not required if telephone is set
    if (!value.length && this.$popin.find('.js-telephone').val().length) {
        return true;
    }

    var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType, this.jsonValues.idCarrier);
    if (!this.intlMobilephone[idCarrier]) {
        return true;
    }

    if (!this.intlMobilephone[idCarrier].isValidNumber()) {
        return false;
    }

    if (this.intlMobilephone[idCarrier].getNumberType() != 1 && this.intlMobilephone[idCarrier].getNumberType() != 2 && this.intlMobilephone[idCarrier].getNumberType() != -1) {
        return false;
    }

    return true;
}

GeodisCarrierSelector.prototype.validateEmail = function(value, values) {
    // Not required if mobilephone is set
    if (!value.length && this.$popin.find('.js-mobilephone').val().length) {
        return true;
    }

    if (null == value.match(/^[^@]+@[^@]+\.[A-Za-z]+$/)) {
        return false;
    }

    return true;
};

GeodisCarrierSelector.prototype.selectCarrier = function(idCarrier, $elm) {
    if (idCarrier == this.jsonValues.idCarrier) {
        return;
    }

    this.$popin.find('.geodisPrestation--selected').removeClass('geodisPrestation--selected');

    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        if (carrier.id_carrier == idCarrier) {
            this.jsonValues.idCarrier = carrier.id_carrier;
            $elm.addClass('geodisPrestation--selected');

            this.jsonValues.idOptionList = [];
            if (typeof this.optionsSelectedByCarrier[idCarrier] != 'undefined') {
                this.jsonValues.idOptionList = this.optionsSelectedByCarrier[idCarrier];
            }
        }
    }

    if (this.canSubmit()) {
        this.$popin.find('.js-submit').removeClass('geodisPopinFooter__submit--inactive');
    } else {
        this.$popin.find('.js-submit').addClass('geodisPopinFooter__submit--inactive');
    }


    // Move phone to mobile phone if it's a mobile phone
    var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType, this.jsonValues.idCarrier);
    if (this.intlTelephone[idCarrier] && this.intlTelephone[idCarrier].isValidNumber() && this.intlTelephone[idCarrier].getNumberType() == 1) {
        if (!this.$popin.find('.js-mobilephone').val().length) {
            this.$popin.find('.js-mobilephone').val(this.$popin.find('.js-telephone').val());
            this.$popin.find('.js-telephone').val('');
        }
    }

    this.updateCarrierPrice();
    this.updateOptionPrice();
};

GeodisCarrierSelector.prototype.updateCarrierPrice = function(carrier) {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        var price = carrier.price;

        for (var j in this.optionsSelectedByCarrier[carrier.id_carrier]) {
            if (j != parseInt(j)) {
                continue;
            }

            var idOption = this.optionsSelectedByCarrier[carrier.id_carrier][j];
            var option = this.getCarrierOption(carrier.id_carrier, idOption);

            if (!option) {
                continue;
            }

            price += option.price_impact;
        }

        this.formatPrice(price, (function(carrier, price) {
            if (carrier.id_carrier == this.getSelectedCarrier().id_carrier) {
                this.$popin.find('.js-popin-price').html(price);
            }
        }).bind(this, carrier));

        this.formatPrice(carrier.price, (function(carrier, price) {
            this.$popin.find('.js-price[data-values='+carrier.id_carrier+']').html(price);
        }).bind(this, carrier));
    }
};

GeodisCarrierSelector.prototype.updateOptionPrice = function() {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];

        for (var j in carrier.optionList) {
            if (j != parseInt(j)) {
                continue;
            }
            var option = carrier.optionList[j];
            var price = option.price_impact;
            this.formatPrice(price, (function(carrier, option, price) {
                this.$popin.find('.js-prestation[data-values='+carrier.id_carrier+'] .js-option-price[data-values='+option.id_option+']').html(price);
            }).bind(this, carrier, option));
        }
    }
};


GeodisCarrierSelector.prototype.selectOption = function(idOption, $elm) {

    this.$popin.find('.geodisOptionRow--selected').removeClass('geodisOptionRow--selected');
    $elm.addClass('geodisOptionRow--selected');

    this.jsonValues.idOptionList = [];
    this.optionsSelectedByCarrier[this.getSelectedCarrier().id_carrier] = [];

    var carrier = this.getSelectedCarrier();

    if (idOption) {
        this.jsonValues.idOptionList.push(idOption);
        this.optionsSelectedByCarrier[carrier.id_carrier].push(idOption);
    }

    this.updateCarrierPrice();
    this.addClassToOptions();

    /*
    var index = this.jsonValues.idOptionList.indexOf(idOption);
    if (index !== -1) {
        // Remove option
        $elm.removeClass('option--selected');
        this.jsonValues.idOptionList.splice(index, 1);
    } else {
        // Add option
        $elm.addClass('option--selected');
        this.jsonValues.idOptionList.push(idOption);
    }
    */
};

GeodisCarrierSelector.prototype.addClassToOptions = function() {
    for (var i in this.optionsSelectedByCarrier) {
        if (i != parseInt(i)) {
            continue;
        }

        this.$popin.find('.js-options[data-values='+i+'] .js-option').removeClass('geodisOptionRow--selected');
        if (!this.optionsSelectedByCarrier[i].length) {
                this.$popin.find('.js-options[data-values='+i+'] .js-option[data-values=\'\']').addClass('geodisOptionRow--selected');
        } else {
            for (var j in this.optionsSelectedByCarrier[i]) {
                if (j != parseInt(j)) {
                    continue;
                }

                var idOption = this.optionsSelectedByCarrier[i][j];
                this.$popin.find('.js-options[data-values='+i+'] .js-option[data-values='+idOption+']').addClass('geodisOptionRow--selected');
            }
        }
    }
}

GeodisCarrierSelector.prototype.open = function(callback) {
    if (this.isOpen) {
        return;
    }

    if(callback === undefined) {
       var callback = null;
    }

    this.wasOpened = true;
    this.isOpen = true;

    if (this.quoteAddressUrl) {
        // Update json values address
        this.GeodisJQuery.ajax({
            url: this.quoteAddressUrl,
            dataType: 'json',
            data: {
                token: this.jsonValues.token
            },
            method: 'GET'
        }).done((function(data) {
            if (data.hasResults) {
                this.jsonValues.address = data.address;
                this.jsonValues.countryCode = data.countryCode;
                this.jsonValues.telephone1 = data.telephone1;
                this.jsonValues.email = data.email;
            }
            this.openPopin(callback);
        }).bind(this));
    } else if (null != this.quote) {
        if (this.quote.shippingAddress().street[0]) {
            this.jsonValues.address = {
                'address1': this.quote.shippingAddress().street[0],
                'address2': '',
                'city': this.quote.shippingAddress().city,
                'zipCode': this.quote.shippingAddress().postcode,
                'countryCode': this.quote.shippingAddress().countryId,
            };
        }

        if (this.quote.shippingAddress().countryId) {
            this.jsonValues.countryCode = this.quote.shippingAddress().countryId;
        }

        if (this.quote.shippingAddress().telephone) {
            this.jsonValues.telephone1 = this.quote.shippingAddress().telephone;
        }

        if (this.quote.shippingAddress().email) {
            this.jsonValues.email = this.quote.shippingAddress().email;
        }
        this.openPopin(callback);
    } else {
        this.openPopin(callback);
    }
}

GeodisCarrierSelector.prototype.openPopin  = function(callback) {
    if(callback === undefined) {
       var callback = null;
    }

    this.$overlay = this.GeodisJQuery('<div class="geodisPopinOverlay"></div>');
    this.$popin = this.GeodisJQuery('<div class="geodisPopin geodisPopin--' + this.module + '"></div>');

    this.GeodisJQuery('body').css({'overflow':'hidden'});
    this.GeodisJQuery('body').append(this.$overlay);
    this.GeodisJQuery('body').append(this.$popin);

    var template = this.loadMainTemplate();

    // Check white label
    if (this.jsonConfig.whiteLabel != 0) {
        var carrierId = this.jsonConfig.idCarrier
        this.GeodisJQuery('.geodisPopin--' + this.module + ' .geodisPopinHeader').css('background', "url('"+this.jsonConfig.whiteLabelImageUrl+"') no-repeat 25px center white");
    }
    this.switchRequiredEntry();

    if (!this.mapEnabled) {
        this.renderPointList();
    }


    if (callback != null) {
        callback();
    }
};

GeodisCarrierSelector.prototype.close = function() {
    this.$popin.remove();
    this.$overlay.remove();
    this.GeodisJQuery('body').css({'overflow':'visible'});
    this.isOpen = false;
};

GeodisCarrierSelector.prototype.loadMainTemplate = function() {
    if (this.jsonConfig.carrierType == 'relay') {
        if (this.useGlobalMap) {
            if (this.mapEnabled) {
                var template = 'relay';
            } else {
                var template = 'relayWithoutMap';
            }
        } else {
            var template = 'carriersRelay';
        }
    } else {
        var template = 'carriers';
    }

    this.template.replace(
        template,
        this.$popin,
        [
            {
                name: 'popinTitle',
                value: this.jsonConfig.popinTitle
            },
            {
                name: 'popinSubtitle',
                value: this.jsonConfig.popinSubtitle
            },
            {
                name: 'description',
                value: this.jsonConfig.description
            }
        ]
    );

    this.addClassToOptions();
    this.updateCarrierPrice();
    this.updateOptionPrice();

    this.initInfo();
    this.initMaps();
    this.initListener();
    this.hideShowAppointmentMessage(this.jsonConfig.carrierType2);
}

GeodisCarrierSelector.prototype.initInfo = function()
{
    this.$popin.find('.js-telephone').each((function(key, item) {
        var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType , this.GeodisJQuery(item).data('values'));
        this.intlTelephone[idCarrier] = window.intlTelInput(item, {
            initialCountry: this.jsonValues.countryCode,
            placeholderNumberType: 'FIXED_LINE',
            utilsScript: this.intlUtilsUr,
            dropdownContainer: document.body
        });
    }).bind(this));

    this.$popin.find('.js-mobilephone').each((function(key, item) {
        var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType , this.GeodisJQuery(item).data('values'));
        this.intlMobilephone[idCarrier] = window.intlTelInput(item, {
            initialCountry: this.jsonValues.countryCode,
            placeholderNumberType: 'MOBILE',
            utilsScript: this.intlUtilsUrl,
            dropdownContainer: document.body
        });
    }).bind(this));

    this.GeodisJQuery('.geodisPopinContent').get(0).addEventListener('scroll', function() {
        var e = document.createEvent('Event');
        e.initEvent('scroll', true, true);
        window.dispatchEvent(e);
    });

    var listenTelephone = true;
    this.$popin.find('.js-telephone').on('countrychange', (function(e) {
        if (!listenTelephone) {
            return;
        }

        listenTelephone = false;
        var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType , this.GeodisJQuery(e.target).data('values'));
        var number = this.intlTelephone[idCarrier].getNumber();

        for (var i in this.intlTelephone) {
            this.intlTelephone[i].setNumber(number);
        }

        listenTelephone = true;
    }).bind(this));

    var listenMobile = true;
    this.$popin.find('.js-mobilephone').on('countrychange', (function(e) {
        if (!listenMobile) {
            return;
        }

        listenMobile = false;
        var idCarrier = this.getIdCarrier(this.jsonConfig.carrierType , this.GeodisJQuery(e.target).data('values'));
        var number = this.intlMobilephone[idCarrier].getNumber();

        for (var i in this.intlMobilephone) {
            this.intlMobilephone[i].setNumber(number);
        }
        listenMobile = true;
    }).bind(this));
    this.$popin.find('.js-telephone').change((function(e) { this.$popin.find('.js-telephone').val(this.GeodisJQuery(e.target).val()); }).bind(this));
    this.$popin.find('.js-mobilephone').change((function(e) { this.$popin.find('.js-mobilephone').val(this.GeodisJQuery(e.target).val()); }).bind(this));
    this.$popin.find('.js-instruction').change((function(e) { this.$popin.find('.js-instruction').val(this.GeodisJQuery(e.target).val()); }).bind(this));
    this.$popin.find('.js-digicode').change((function(e) { this.$popin.find('.js-digicode').val(this.GeodisJQuery(e.target).val()); }).bind(this));
    this.$popin.find('.js-email').change((function(e) { this.$popin.find('.js-email').val(this.GeodisJQuery(e.target).val()); }).bind(this));
}

GeodisCarrierSelector.prototype.renderCarriers = function($elm) {
    // Be sure no relay are set

    this.jsonValues.codeWithdrawalAgency = null;
    this.jsonValues.codeWithdrawalPoint = null;

    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        this.template.append(
            'carrier',
            $elm,
            [
                {
                    name: 'id',
                    value: carrier.id_carrier
                },
                {
                    name: 'name',
                    value: carrier.name
                },
                {
                    name: 'desc',
                    value: carrier.desc
                },
                {
                    name: 'longdesc',
                    value: carrier.longdesc
                },
                {
                    name: 'price',
                    value: carrier.price
                },
                {
                    name: 'appointmentTypeDesc',
                    value: carrier.appointmentType
                }
            ]
        );
    }
};

GeodisCarrierSelector.prototype.renderCarriersRelay = function($elm) {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        this.template.append(
            'carrierRelay',
            $elm,
            [
                {
                    name: 'id',
                    value: carrier.id_carrier
                },
                {
                    name: 'name',
                    value: carrier.name
                },
                {
                    name: 'desc',
                    value: carrier.desc
                },
                {
                    name: 'longdesc',
                    value: carrier.longdesc
                },
                {
                    name: 'price',
                    value: carrier.price
                }
            ]
        );
    }
};


GeodisCarrierSelector.prototype.renderAdditionalOption = function($elm) {
    var option = this.getOption($elm.data('values'));
    if (option.code == 'livEtage'
        || option.code == 'miseLieuUtil'
    ) {
        this.template.append(
            'floor',
            $elm,
            [
                {
                    name: 'id',
                    value: $elm.data('values')
                },
                {
                    name: 'code',
                    value: option.code
                }
            ]
        );
    }
};

GeodisCarrierSelector.prototype.getSelectedCarrier = function() {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];

        if (carrier.id_carrier == this.jsonValues.idCarrier) {
            return carrier;
        }
    }

    return false;
}

GeodisCarrierSelector.prototype.getCarrier = function(idCarrier) {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];

        if (carrier.id_carrier == idCarrier) {
            return carrier;
        }
    }

    return false;
}

GeodisCarrierSelector.prototype.getOption = function(idOption) {
    for (var i in this.jsonConfig.carrierList) {
        if (i != parseInt(i)) {
            continue;
        }

        var carrier = this.jsonConfig.carrierList[i];
        for (var j in carrier.optionList) {
            if (j != parseInt(j)) {
                continue;
            }

            var option = carrier.optionList[j];

            if (option.id_option == idOption) {
                return option;
            }
        }
    }

    return false;
}

GeodisCarrierSelector.prototype.getCarrierOption = function(idCarrier, idOption) {
    var carrier = this.getCarrier(idCarrier);
    for (var i in carrier.optionList) {
        if (i != parseInt(i)) {
            continue;
        }

        var option = carrier.optionList[i];

        if (option.id_option == idOption) {
            return option;
        }
    }

    return false;
}

GeodisCarrierSelector.prototype.renderOptions = function($elm) {
    var carrier = this.getCarrier($elm.data('values'));

    if (!carrier.optionList.length) {
        $elm.remove();
        return;
    }

    var optionList = carrier.optionList.slice(0);
    optionList.reverse();
    for (var i in optionList) {
        if (i != parseInt(i)) {
            continue;
        }

        var option = optionList[i];
        this.template.prepend(
            'option',
            $elm,
            [
                {
                    name: 'id',
                    value: option.id_option
                },
                {
                    name: 'name',
                    value: option.name
                },
                {
                    name: 'desc',
                    value: option.desc
                },
                {
                    name: 'price_impact',
                    value: option.price_impact
                }
            ]
        );
    }
};

GeodisCarrierSelector.prototype.renderOptionsTitle = function($elm) {
    var carrier = this.getCarrier($elm.data('values'));
    if ('nb options', !carrier.optionList.length) {
        $elm.remove();
        return;
    }
};

GeodisCarrierSelector.prototype.setCarrierClass = function(idCarrier) {
    if (idCarrier == this.jsonValues.idCarrier) {
        return 'geodisPrestation--selected';
    }
};

GeodisCarrierSelector.prototype.setSubmitClass = function() {
    if (this.canSubmit()) {
        return '';
    } else {
        return 'geodisPopinFooter__submit--inactive';
    }
};

GeodisCarrierSelector.prototype.getInfo = function(attribute) {
    for (var i in this.jsonValues.info) {
        if (i != parseInt(i)) {
            continue;
        }

        var info = this.jsonValues.info[i];

        if (info.name == attribute) {
            return info.value;
        }
    }

    return '';
};

GeodisCarrierSelector.prototype.getFloor = function(values) {
    return this.getInfo('floor');
};

GeodisCarrierSelector.prototype.getTelephone = function(values) {
    var telephone = this.getInfo('telephone');

    if (!telephone && this.jsonValues.telephone1.length && !this.getMobilephone()) {
        return this.jsonValues.telephone1;
    }

    return telephone;
};

GeodisCarrierSelector.prototype.getAddress = function(values) {
    var address = this.getInfo('defaultAddress');

    if (!address) {
        if (typeof this.jsonValues.address != 'undefined') {
            address = this.jsonValues.address;
        } else {
            return '';
        }
    }

    return this.formatInlineAddress(address);
};

GeodisCarrierSelector.prototype.getZipCode = function(values) {
    var zipCode = this.getInfo('defaultZipCode');

    if (!zipCode) {
        if (typeof this.jsonValues.address != 'undefined') {
            return this.jsonValues.address.zipCode;
        } else {
            return '';
        }
    }

    return zipCode;
};

GeodisCarrierSelector.prototype.getCountryCode = function(values) {
    var countryCode = this.getInfo('defaultCountryCode');

    if (!countryCode) {
        if (typeof this.jsonValues.address != 'undefined') {
            return this.jsonValues.address.countryCode;
        } else {
            return '';
        }
    }

    return countryCode;
};

GeodisCarrierSelector.prototype.formatInlineAddress = function(address) {
    if (typeof address == 'string') {
        return address;
    }

    return address.address1 + (address.address2.length ? ' ' : '') + address.address2 + ', ' + address.zipCode + ' ' + address.city;
};


GeodisCarrierSelector.prototype.getMobilephone = function(values) {
    var mobilephone = this.getInfo('mobilephone');

    if (!mobilephone && this.jsonValues.telephone2.length) {
        return this.jsonValues.telephone2;
    }

    return mobilephone;
};

GeodisCarrierSelector.prototype.getEmail = function(values) {
    var email = this.getInfo('email');

    if (!email.length) {
        email = this.jsonValues.email;
    }

    return email;
};

GeodisCarrierSelector.prototype.getDigicode = function(values) {
    var digicode = this.getInfo('digicode');

    if (!digicode.length) {
        digicode = this.jsonValues.digicode;
    }
    return digicode;
};

GeodisCarrierSelector.prototype.getInstruction = function(values) {
    var instruction = this.getInfo('instruction');

    if (!instruction.length) {
        instruction = this.jsonValues.instruction;
    }
    return instruction;
};


GeodisCarrierSelector.prototype.initMaps = function() {
    this.$popin.find('.js-geodis-relay-map').each((function(key, item) {
        var $item = this.GeodisJQuery(item);

        var idCarrier = 0;
        if (!this.useGlobalMap) {
            idCarrier = $item.data('values');
        }

        this.relayMap[(this.useGlobalMap ? 0 : idCarrier)] = new GeodisMap($item, {countryCode: this.jsonValues.countryCode}, this.GeodisJQuery);
        this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].autoCenter();
        this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].onChange((function(idCarrier, e) { this.renderPointList(idCarrier, e) }).bind(this, idCarrier));

        this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].getPositionFromAddress(this.getAddress(), (function(idCarrier, position) {
            this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)] = position;
            this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].setCenter(position.latitude, position.longitude);
        }).bind(this, idCarrier));

        this.mapInitialized[(this.useGlobalMap ? 0 : idCarrier)] = false;
    }).bind(this));

    return;
};

GeodisCarrierSelector.prototype.showMap = function(idCarrier, $elm) {
    this.$popin.find('.js-switch-list').removeClass('geodisSwitch--active');
    this.$popin.find('.js-point-list').removeClass('geodisRelayPicker__list--active');
    this.$popin.find('.js-geodis-relay-map').addClass('geodisRelayPicker__map--active');
    $elm.addClass('geodisSwitch--active');

    if (this.jsonValues.codeWithdrawalPoint) {
        this.selectPoint(this.jsonValues.codeWithdrawalPoint, undefined, this.getSelectedCarrier().id_carrier, true);
    }

    if (this.jsonValues.codeWithdrawalAgency) {
        this.selectPoint(this.jsonValues.codeWithdrawalAgency, undefined, this.getSelectedCarrier().id_carrier, true);
    }

    this.mapAutoFit();
};

GeodisCarrierSelector.prototype.showList = function(idCarrier, $elm) {
    this.$popin.find('.js-switch-map').removeClass('geodisSwitch--active');
    this.$popin.find('.js-point-list').addClass('geodisRelayPicker__list--active');
    this.$popin.find('.js-geodis-relay-map').removeClass('geodisRelayPicker__map--active');
    $elm.addClass('geodisSwitch--active');
};

GeodisCarrierSelector.prototype.renderPointList = function(idCarrier, e) {
    // Set timeout
    if (typeof this.timeoutMapMove[(this.useGlobalMap ? 0 : idCarrier)] != 'undefined') {
        window.clearTimeout(this.timeoutMapMove[(this.useGlobalMap ? 0 : idCarrier)]);
    }

    if (this.useGlobalMap) {
        idCarrier = [];

        this.jsonConfig.carrierList.forEach((function(idCarrier, item) {
            idCarrier.push(item.id_carrier);
        }).bind(this, idCarrier));
    }

    if (this.mapEnabled) {
        var data = {
            idCarrier: idCarrier,
            latitude: e.latitude,
            longitude: e.longitude,
            token: this.jsonValues.token,
            defaultLatitude: (!this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)]) ? '' : this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)].latitude,
            defaultLongitude: (!this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)]) ? '' : this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)].longitude
        };
    } else {
        var data = {
            idCarrier: idCarrier,
            zipCode: this.getZipCode(),
            token: this.jsonValues.token,
            countryCode: this.getCountryCode()
        };
    }

    this.timeoutMapMove[(this.useGlobalMap ? 0 : idCarrier)] = window.setTimeout((function() {
        this.GeodisJQuery.ajax({
            'url': this.pointListUrl,
            data: data,
            dataType: 'json',
            method: 'GET'
        }).done((function(idCarrier, data) {
            var $elm = this.$popin.find('.js-point-list[data-values='+idCarrier+']');

            if (data.status == 'error') {
                return;
            }

            $elm.html('');
            this.pickupPointList = data.pickupPointList;

            if (this.mapEnabled) {
                this.relayMap[idCarrier].flushMarkers();

                // Home
                this.relayMap[idCarrier].addMarker({
                    position: this.mapDefaultCenter[idCarrier],
                    size: {
                        width: 26,
                        height: 43
                    },
                    url: this.markerHomeUrl
                });
            }


            // Shops
            for (var i in data.pickupPointList) {
                if (i != parseInt(i)) {
                    continue;
                }

                var point = data.pickupPointList[i];
                var point2 = data.pickupPointList[i];

                if (this.mapEnabled) {
                    this.relayMap[idCarrier].addMarker({
                        id: point.code,
                        position: {
                            latitude: point.latitude,
                            longitude: point.longitude,
                        },
                        size: {
                            width: 20,
                            height: 33
                        },
                        url: ((this.jsonValues.codeWithdrawalPoint == point.code || this.jsonValues.codeWithdrawalAgency == point.code) ? this.markerSelectedShopUrl : this.markerShopUrl),
                        onClick: (function(idCarrier, codePoint) {
                            this.selectPoint(codePoint, undefined, idCarrier, true);
                        }).bind(this, idCarrier)
                    });
                }

                this.template.append(
                    'point',
                    $elm,
                    [
                        {
                            name: 'idCarrier',
                            value: idCarrier
                        },
                        {
                            name: 'code',
                            value: point.code
                        },
                        {
                            name: 'name',
                            value: point.name
                        },
                        {
                            name: 'address1',
                            value: point.address1
                        },
                        {
                            name: 'address2',
                            value: point.address2
                        },
                        {
                            name: 'zipCode',
                            value: point.zipCode
                        },
                        {
                            name: 'city',
                            value: point.city
                        },
                        {
                            name: 'monday',
                            value: JSON.stringify(point.openingTime[0])
                        },
                        {
                            name: 'tuesday',
                            value: JSON.stringify(point.openingTime[1])
                        },
                        {
                            name: 'wednesday',
                            value: JSON.stringify(point.openingTime[2])
                        },
                        {
                            name: 'thursday',
                            value: JSON.stringify(point.openingTime[3])
                        },
                        {
                            name: 'friday',
                            value: JSON.stringify(point.openingTime[4])
                        },
                        {
                            name: 'saturday',
                            value: JSON.stringify(point.openingTime[5])
                        },
                        {
                            name: 'sunday',
                            value: JSON.stringify(point.openingTime[6])
                        },
                        {
                            name: 'distance',
                            value: this.formatDistance(point.distance)
                        }
                    ]
                );
            }

            if (this.mapEnabled) {
                if (!this.mapInitialized[idCarrier]) {
                    this.mapInitialized[idCarrier] = true;
                    this.mapAutoFit();
                }
            }
        }).bind(this, (this.useGlobalMap ? 0 : idCarrier)));
    }).bind(this), 1000);
};

GeodisCarrierSelector.prototype.mapAutoFit = function() {
    var idCarrier = this.jsonValues.idCarrier;
    var defaultPosition = this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)];

    var positions = [
    ];

    for (var i in this.pickupPointList) {
        if (i != parseInt(i)) {
            continue;
        }

        var point = this.pickupPointList[i];
        if (this.jsonValues.codeWithdrawalPoint || this.jsonValues.codeWithdrawalAgency) {
            if (point.type != 'agency' && this.jsonValues.codeWithdrawalPoint == point.code
                || point.type == 'agency' && this.jsonValues.codeWithdrawalAgency == point.code
            ) {
                positions.push({
                    latitude: point.latitude,
                    longitude: point.longitude
                });
            }
        } else {
            positions.push({
                latitude: point.latitude,
                longitude: point.longitude
            });
        }
    }
    positions.push(this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)]);

    if (this.mapEnabled) {
        this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].fitPositions(positions);
    }
};

GeodisCarrierSelector.prototype.displayTimetable = function(values, $elm) {
    if ($elm.is('.geodisRelay__actionSeeTimetable--active')) {
        $elm.removeClass('geodisRelay__actionSeeTimetable--active');
    } else {
        $elm.addClass('geodisRelay__actionSeeTimetable--active');
    }
};

GeodisCarrierSelector.prototype.renderTimeline = function($elm, values) {
    if (typeof values == 'undefined') {
        values = {
            morningStart: false,
            morningStop: false,
            eveningStart: false,
            eveningStop: false
        }
    }

    if (!values.morningStart || values.morningStart == values.morningStop) {
        if (!values.eveningStop) {
            // Closed
            this.template.append(
                'point-timeline-closed',
                $elm
            );
            return;
        }

        // Opened the afternoon only
        this.template.append(
            'point-timeline-one',
            $elm,
            [
                {
                    name: 'start',
                    value: values.eveningStart
                },
                {
                    name: 'end',
                    value: values.eveningStop
                }
            ]
        );
        return;
    }

    // Opened the morning only
    if (!values.eveningStop) {
        this.template.append(
            'point-timeline-one',
            $elm,
            [
                {
                    name: 'start',
                    value: values.morningStart
                },
                {
                    name: 'end',
                    value: values.morningStop
                }
            ]
        );
        return;
    }

    // Opened the morning and afternoon, no cut for lunch
    if (!values.eveningStart) {
        if (values.morningStart == values.eveningStop) {
            // Closed
            this.template.append(
                'point-timeline-closed',
                $elm
            );
        } else {
            this.template.append(
                'point-timeline-one',
                $elm,
                [
                    {
                        name: 'start',
                        value: values.morningStart
                    },
                    {
                        name: 'end',
                        value: values.eveningStop
                    }
                ]
            );
        }
        return;
    }

    // Opened the morning and afternoon, cut for lunch
    this.template.append(
        'point-timeline-two',
        $elm,
        [
            {
                name: 'start1',
                value: values.morningStart
            },
            {
                name: 'end1',
                value: values.morningStop
            },
            {
                name: 'start2',
                value: values.eveningStart
            },
            {
                name: 'end2',
                value: values.eveningStop
            }
        ]
    );
    return;
};

GeodisCarrierSelector.prototype.formatDistance = function(distance) {
    if (distance === '') {
        return;
    }

    if (distance > 1000) {
        var distanceKm = parseFloat(distance) / 1000;
        if (distanceKm == parseInt(distanceKm)) {
            return this.sentenceDistanceKilometers.replace('@', parseInt(distanceKm));
        } else {
            return this.sentenceDistanceKilometers.replace('@', parseFloat(distance / 1000).toFixed(1));
        }
    }

    return this.sentenceDistanceMeters.replace('@', distance);
}

GeodisCarrierSelector.prototype.selectPoint = function(codePoint, $elm, idCarrier, autoFit) {
    this.$popin.find('.geodisRelay--selected').removeClass('geodisRelay--selected');

    if (typeof $elm != 'undefined') {
        var idCarrier = $elm.data('parent');
    }

    if (!this.useGlobalMap) {
        this.jsonValues.idCarrier = idCarrier;
    }

    for (var i in this.pickupPointList) {
        if (i != parseInt(i)) {
            continue;
        }

        var point = this.pickupPointList[i];
        if (point.code == codePoint) {
            if (point.type == 'agency') {
                this.jsonValues.codeWithdrawalPoint = null;
                this.jsonValues.codeWithdrawalAgency = codePoint;
            } else {
                this.jsonValues.codeWithdrawalPoint = codePoint;
                this.jsonValues.codeWithdrawalAgency = null;
            }

            if (this.useGlobalMap) {
                this.jsonValues.idCarrier = point.idCarrier;
            }

            this.setInfo('instructionsEnlevement', point.instructionsEnlevement);
            this.setInfo('instructionsLivraison', point.instructionsLivraison);
            this.setInfo('address', {
                name: point.name,
                address1: point.address1,
                address2: point.address2,
                zipCode: point.zipCode,
                city: point.city,
                countryCode: point.countryCode
            });

            this.$popin.find('.js-geodis-relay-address[data-values='+(this.useGlobalMap ? 0 : idCarrier)+']').val(this.getAddress());
            if (typeof $elm != 'undefined') {
                $elm.closest('.geodisRelay').addClass('geodisRelay--selected');
            }

            if (typeof autoFit == 'undefined' || autoFit === true) {
                this.mapAutoFit();
            }
        }
    }

    if (this.canSubmit()) {
        this.$popin.find('.js-submit').removeClass('geodisPopinFooter__submit--inactive');
    } else {
        this.$popin.find('.js-submit').addClass('geodisPopinFooter__submit--inactive');
    }

    this.updateCarrierPrice();
};

GeodisCarrierSelector.prototype.setPointClass = function(codePoint, idCarrier) {
    if (this.jsonValues.codeWithdrawalPoint == codePoint || this.jsonValues.codeWithdrawalAgency == codePoint) {
        if (!this.mapInitialized[(this.useGlobalMap ? 0 : idCarrier)]) {
            this.selectPoint(codePoint, undefined, idCarrier, false);
        }
        return 'geodisRelay--selected';
    }
}

GeodisCarrierSelector.prototype.updateRelayAddress = function(idCarrier) {
    var address = this.GeodisJQuery('.js-geodis-relay-address[data-values='+(this.useGlobalMap ? 0 : idCarrier)+']').val();

    this.setInfo('defaultAddress', address);
    this.relayMap[(this.useGlobalMap ? 0 : idCarrier)].setAddress(address, (function(idCarrier, position) {
        this.mapDefaultCenter[(this.useGlobalMap ? 0 : idCarrier)] = position;
    }).bind(this, idCarrier));
};

GeodisCarrierSelector.prototype.updateRelayShortAddress = function(idCarrier) {
    var zipCode = this.GeodisJQuery('.js-geodis-relay-zipcode').val();
    var countryCode = this.GeodisJQuery('.js-geodis-relay-country').val();

    this.setInfo('defaultZipCode', zipCode);
    this.setInfo('defaultCountryCode', countryCode);

    this.renderPointList();
};

GeodisCarrierSelector.prototype.formatPrice = function(price, callback) {
    this.GeodisJQuery.ajax({
        url: this.formatPriceUrl,
        data: {
            token: this.jsonValues.token,
            price: price
        },
        dataType: 'json'
    }).done((function(data) {
        callback(data.price);
    }).bind(this));
};

GeodisCarrierSelector.prototype.hideShowAppointmentMessage = function(carrierType) {
    if (carrierType == 'rdv') {
        this.GeodisJQuery('.js-description').show();
    } else {
        this.GeodisJQuery('.js-description').hide();
    }
};

GeodisCarrierSelector.prototype.getIdCarrier = function (carrierType, carrierId) {
    if (carrierType == 'relay') {
        return 0;
    }

    return carrierId;
}
