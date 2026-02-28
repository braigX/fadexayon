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

function GeodisConfigurationBack(config) {
    this.data = config.data;
    this.sourceContainer = config.container.source;
    this.$targetContainer = jQuery(config.container.target);
    this.ajaxTemplateUrl = config.ajaxTemplateLink;
    this.ajaxSaveUrl = config.ajaxSaveLink;
    this.refreshRenderOnChange = [
        'carrierCollection.id_account',
        'carrierCollection.id_prestation'
    ];

    jQuery('#configuration_form').submit(function() {
        jQuery('.selectedSwap option').attr('selected', 'selected');
    });

    jQuery('[name="departure_date_delay"]').closest('.form-group').hide();

    this.render();
}

GeodisConfigurationBack.prototype.updateData = function() {
    var needRender = false;

    this.$targetContainer.find('[data-id]').each((function(key, value) {
            var type = jQuery(value).data('type');
            var val = jQuery(value).val();
        var index = jQuery(value).data('index');

        var keys = jQuery(value).data('id').split('.');

        if (typeof (this.data[keys[0]][index]) == 'undefined') {
            this.data[keys[0]][index] = {};
        }

        // Map this.data with form data
        if (jQuery(value).is('input[type="checkbox"]')) {
            this.data[keys[0]][index][keys[1]] = jQuery(value).is(':checked') ? 1 : 0;
        } else {
            if (type == "float") {
                val = val.replace(/,/g, ".");
            }
            var changed = (this.data[keys[0]][index][keys[1]] != val);
            this.data[keys[0]][index][keys[1]] = val;

        }

        if (changed && this.refreshRenderOnChange.indexOf(jQuery(value).data('id')) != -1) {
            needRender = true;
        }

        /*
        var otherValues = jQuery(value).data('value');
        if (otherValues) {
            otherValues.forEach((function(values) {
                this.data[keys[0]][index][values[0]] = values[1];
            }).bind(this));
        }
        */
    }).bind(this));

    // Add carrierOption

    var oldCarrierOptionCollection = this.data.carrierOptionCollection.slice();
    this.data.carrierOptionCollection = [];
    this.data.carrierCollection.forEach((function(carrier, carrierKey) {
        // Create options
        var prestationFound = false;
        for (var prestationOptionKey in this.data.prestationOptionCollection) {
            var prestationOption = this.data.prestationOptionCollection[prestationOptionKey];
            if (carrier.id_prestation == prestationOption.id_prestation) {
                prestationFound = true;
                if (prestationOption.active == "1") {
                    var found = false;
                    for (var carrierOptionKey in oldCarrierOptionCollection) {
                        var carrierOption = oldCarrierOptionCollection[carrierOptionKey];
                        if (carrier.id) {
                            if (carrierOption.id_carrier == carrier.id && carrierOption.id_option == prestationOption.id_option) {
                                found = true;
                                carrierOption.key_carrier = carrierKey;
                                this.data.carrierOptionCollection.push(carrierOption);
                            }
                        } else {
                            if (carrierOption.key_carrier == carrierKey && carrierOption.id_option == prestationOption.id_option) {
                                found = true;
                                carrierOption.key_carrier = carrierKey;
                                this.data.carrierOptionCollection.push(carrierOption);
                            }
                        }
                    }
                    if (!found) {
                        this.data.carrierOptionCollection.push({
                            id_option: prestationOption.id_option,
                            id_carrier: carrier.id,
                            key_carrier: carrierKey,
                            price_impact: 0,
                            active: 0
                        });
                        needRender = true;
                    }
                }
            }
        }
    }).bind(this));

    if (needRender) {
        this.render();
    }
};

GeodisConfigurationBack.prototype.render = function(first) {
    if (typeof first == 'undefined') {
        first = true;
    }

    this.$targetContainer.addClass('loading');

    jQuery.ajax({
        url: this.ajaxTemplateUrl,
        data: {data: JSON.stringify(this.data)},
        method: 'post'
    }).success((function(result) {
        this.$targetContainer.html(jQuery(result).find(this.sourceContainer).html());
        this.$targetContainer.removeClass('loading');

        if (first) {
            this.updateData();
            //this.render(false);
        }
        this.initListener();
    }).bind(this));
}

GeodisConfigurationBack.prototype.initListener = function() {
    this.$targetContainer.find('.js-add-carrier').click((function(e) {
        this.data.carrierCollection.push({id:null, active: true, id_account: null, id_prestation: null,
            reference_carrier: null, price: 0, enable_price_fixed: 0, enable_price_according:0, enable_free_shipping:0,
            free_shipping_from: 0, additional_shipping_cost: 1, key_group_carrier: jQuery(e.target).data('index')});
        this.render();
    }).bind(this));

    this.$targetContainer.find('.js-add-group').click((function() {
        this.data.groupCarrierCollection.push({id:null, id_reference_carrier: null});
        this.render();
    }).bind(this));


    this.$targetContainer.find('.js-save').click((function() {
        this.save();
    }).bind(this));

    this.$targetContainer.find('[data-id]:not([data-update-on-change=true])').change((function() {
        this.updateData();
    }).bind(this));

    this.$targetContainer.find('[data-update-on-change=true][data-id]').change((function() {
        this.updateData();
    }).bind(this));

    this.$targetContainer.find('.js-enable_free_shipping').change((function() {
        this.updateData();
    }).bind(this));

    this.$targetContainer.find('.js-remove-carrier').click((function(e) {
        this.removeCarrier(jQuery(e.target).data('index'));
    }).bind(this));

    this.$targetContainer.find('.js-enable-group').click((function(e) {
        this.enableGroup(jQuery(e.target).data('index'));
    }).bind(this));

    this.$targetContainer.find('.js-disable-group').click((function(e) {
        this.disableGroup(jQuery(e.target).data('index'));
    }).bind(this));
};

GeodisConfigurationBack.prototype.removeCarrier = function(index) {
    this.data.carrierCollection[index].removed = true;
    for (var carrierOptionKey in this.data.carrierOptionCollection) {
        if (this.data.carrierOptionCollection[carrierOptionKey].key_carrier == index) {
            this.data.carrierOptionCollection[carrierOptionKey].removed = true;
        }
    }
    this.render();
};

GeodisConfigurationBack.prototype.restoreCarrier = function(index) {
    delete this.data.carrierCollection[index].removed;
    for (var carrierOptionKey in this.data.carrierOptionCollection) {
        if (this.data.carrierOptionCollection[carrierOptionKey].key_carrier == index) {
            delete this.data.carrierOptionCollection[carrierOptionKey].removed;
        }
    }
    this.render();
};

GeodisConfigurationBack.prototype.enableGroup = function(index) {
    this.data.groupCarrierCollection[index].active = 1;

    this.render();
};

GeodisConfigurationBack.prototype.disableGroup = function(index) {
    this.data.groupCarrierCollection[index].active = 0;

    this.render();
};

GeodisConfigurationBack.prototype.save = function() {
    this.$targetContainer.addClass('loading');
    jQuery.ajax({
        url: this.ajaxSaveUrl,
        data: {data: JSON.stringify(this.data)},
        method: 'post'
    }).success((function(result) {
        result = JSON.parse(result);
        if (result.status == 'success') {
            this.data = result.data;
            this.render();
            this.emptyMessages();
            this.displayConfirmation(result.message);
            if (result.noticeMessageList) {
                for (var i in result.noticeMessageList) {
                    this.displayNotice(result.noticeMessageList[i]);
                }
            }
        } else {
            this.emptyMessages();
            this.displayError(result.message);
            this.$targetContainer.removeClass('loading');
        }
        window.scrollTo(0, 0);
    }).bind(this));
};

GeodisConfigurationBack.prototype.emptyMessages = function() {
    jQuery('.js-message').html('');
}

GeodisConfigurationBack.prototype.displayConfirmation = function(message) {
    jQuery('.js-message').append(
        jQuery(
            '<div class="alert alert-success">'
            + '<button type="button" class="close" data-dismiss="alert">×</button>'
            + message
            + '</div>'
        )
    );
}

GeodisConfigurationBack.prototype.displayNotice = function(message) {
    jQuery('.js-message').append(
        jQuery(
            '<div class="alert alert-info">'
            + '<button type="button" class="close" data-dismiss="alert">×</button>'
            + message
            + '</div>'
        )
    );
}

GeodisConfigurationBack.prototype.displayError = function(message) {
    jQuery('.js-message').append(
        jQuery(
            '<div class="alert alert-danger">'
            + '<button type="button" class="close" data-dismiss="alert">×</button>'
            + message
            + '</div>'
        )
    );
}
