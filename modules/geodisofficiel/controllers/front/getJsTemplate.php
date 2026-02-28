<?php
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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';

class GeodisOfficielGetJsTemplateModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        if (!Tools::getIsset('id')) {
            throw new Exception('Missing parameter id.');
        }

        if (!Tools::getIsset('token')) {
            throw new Exception('Missing parameter token.');
        }

        if (Tools::getIsset('token') != Context::getContext()->cookie->geodisToken) {
            throw new Exception('Invalid token.');
        }

        $id = Tools::getValue('id');

        if (!preg_match('/^[\w-]+$/U', $id)) {
            throw new Exception('id is not valid.');
        }

        $filePath = _PS_MODULE_DIR_.'geodisofficiel/views/templates/front/js/'.$id.'.tpl';

        if (!file_exists($filePath)) {
            throw new Exception('Template do not exists.');
        }

        GeodisServiceTranslation::registerSmarty();

        Context::getContext()->smarty->assign(
            'GeodisIdLang',
            Context::getContext()->language->id
        );
        Context::getContext()->smarty->assign(
            'geodisCountryList',
            Country::getCountries(Context::getContext()->language->id, true)
        );

        $countryCode = Context::getContext()->country->iso_code;
        $idAddressDelivery = Context::getContext()->cart->id_address_delivery;
        $address = new Address($idAddressDelivery);

        if ($address) {
            $country = new Country($address->id_country);
            $countryCode = $country->iso_code;
        }
        Context::getContext()->smarty->assign(
            'geodisCountryCode',
            $countryCode
        );

        echo Context::getContext()->smarty->fetch(
            _PS_MODULE_DIR_.'geodisofficiel/views/templates/front/js/'.$id.'.tpl'
        );

        exit;
    }
}
