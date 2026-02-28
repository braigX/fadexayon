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
    new ValidationForm();
});

var ValidationForm = function() {
    jQuery('.formConnexion-successMessage').hide();
    jQuery('.formConnexion-textError').hide();
    jQuery('.js-formSynchronise').hide();
    jQuery('.js-modify').hide();

    jQuery(document).on("click", '[name="modify"]', function() {
        jQuery('.formConnexion-successMessage').hide();
        jQuery('.formConnexion-form').show();
        jQuery('.js-formSynchronise').hide();
        jQuery(this).hide();
    });

    jQuery(document).on("change", '[name="login"]', function() {
        jQuery('[name="submit"]').removeProp("disabled");
        jQuery('[name="submit"]').show();
        jQuery('.formConnexion-textError').hide();
    });

    jQuery(document).on("change", '[name="secretKey"]', function() {
        jQuery('[name="submit"]').removeProp("disabled");
        jQuery('[name="submit"]').show();
        jQuery('.formConnexion-textError').hide();
    });

    if (geodis.submit === true) {
        this.ok = geodis.ok;
        this.fillForm(geodis.login, geodis.secretKey);
        if (this.ok !== true ) {
            this.displayError(geodis.texteErreur);
            jQuery('[name="submit"]').hide();
        } else {
            this.displaySuccess(geodis.success);
        }
    } else if (geodis.logged) {
        this.displaySuccess('');
    }

}

ValidationForm.prototype.displayError = function(textError) {
    jQuery('.formConnexion-textError').append(textError);
    jQuery('.formConnexion-textError').show();
};

ValidationForm.prototype.fillForm = function(login, secretKey) {
    jQuery(document).find('[name="login"]').val(geodis.login);
    jQuery(document).find('[name="secretKey"]').val(geodis.secretKey);
};

ValidationForm.prototype.displaySuccess = function(text) {

    if (text.length) {
        jQuery('.formConnexion-successMessage').append(text);
        jQuery('.formConnexion-successMessage').show();
    }

    jQuery('.formConnexion-form').hide();
    jQuery('.js-formSynchronise').show();
    jQuery('.js-modify').show();
};
