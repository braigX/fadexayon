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

function GeodisConfigurationFront(config) {
    this.defaultValues = config.defaultValues;
    this.currentValues = config.currentValues;
    this.defaultValuesTrigger = config.defaultValuesTrigger;

    this.updateForm();
    this.addListener();
};

GeodisConfigurationFront.prototype.updateForm = function() {
    var values;
    if (jQuery(this.defaultValuesTrigger+':checked').val() == "1") {
        this.setCurrentValues();
    } else {
        this.setDefaultValues();
    }
};

GeodisConfigurationFront.prototype.setDefaultValues = function() {
    for (var i in this.defaultValues) {
        var carrier = this.defaultValues[i];

        // Copy current data
        this.currentValues[i].name = jQuery('[name=name_'+carrier.id+']').val(),
        this.currentValues[i].logo = jQuery('[name=logo_'+carrier.id+']').parent().parent().parent().parent().find('img').attr('src')

        var delay = [];
        for (var idLang in carrier.delay) {
            delay[idLang] = jQuery('[name=delay_'+carrier.id+'_'+idLang+']').val();
        }
        this.currentValues[i].delay = delay;


        // Set default data
        jQuery('[name=name_'+carrier.id+']').val(carrier.name);
        jQuery('[name=logo_'+carrier.id+']').parent().parent().parent().parent().find('img').attr('src', carrier.logo)

        for (var idLang in carrier.delay) {
            jQuery('[name=delay_'+carrier.id+'_'+idLang+']').val(carrier.delay[idLang]);
        }

        // Lock form
        jQuery('[name=name_'+carrier.id+']').attr('disabled', 'disabled');
        jQuery('[name=logo_'+carrier.id+']').attr('disabled', 'disabled');
        jQuery('#logo_'+carrier.id+'-selectbutton').attr('disabled', 'disabled');
        for (var idLang in carrier.delay) {
            jQuery('[name=delay_'+carrier.id+'_'+idLang+']').attr('disabled', 'disabled');
        }
    }
};

GeodisConfigurationFront.prototype.setCurrentValues = function() {
    for (var i in this.currentValues) {
        var carrier = this.currentValues[i];

        // Set current data
        jQuery('[name=name_'+carrier.id+']').val(carrier.name);
        jQuery('[name=logo_'+carrier.id+']').parent().parent().parent().parent().find('img').attr('src', carrier.logo)

        for (var idLang in carrier.delay) {
            jQuery('[name=delay_'+carrier.id+'_'+idLang+']').val(carrier.delay[idLang]);
        }

        // Unlock form
        jQuery('[name=name_'+carrier.id+']').attr('disabled', false);
        jQuery('[name=logo_'+carrier.id+']').attr('disabled', false);
        jQuery('#logo_'+carrier.id+'-selectbutton').attr('disabled', false);
        for (var idLang in carrier.delay) {
            jQuery('[name=delay_'+carrier.id+'_'+idLang+']').attr('disabled', false);
        }
    }
};

GeodisConfigurationFront.prototype.submit = function() {
    for (var i in this.currentValues) {
        var carrier = this.currentValues[i];

        // Unlock form
        jQuery('[name=name_'+carrier.id+']').attr('disabled', false);
        jQuery('[name=logo_'+carrier.id+']').attr('disabled', false);
        jQuery('#logo_'+carrier.id+'-selectbutton').attr('disabled', false);
        for (var idLang in carrier.delay) {
            jQuery('[name=delay_'+carrier.id+'_'+idLang+']').attr('disabled', false);
        }
    }
};

GeodisConfigurationFront.prototype.addListener = function() {
    jQuery(this.defaultValuesTrigger).change((function() {
        this.updateForm();
    }).bind(this))

    jQuery('#configuration_form').submit((function() {
        this.submit();
    }).bind(this));
};
