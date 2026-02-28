<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

if (! defined('_PS_VERSION_')) {
    exit;
}

use Lyranetwork\Sogecommerce\Sdk\Form\Api as SogecommerceApi;

/**
 * Class that renders payment module administration interface.
 */
class SogecommerceHelperForm
{
    private function __construct()
    {
        // Do not instantiate this class.
    }

    public static function getAdminFormContext()
    {
        $context = Context::getContext();

        /* @var Sogecommerce */
        $sogecommerce = Module::getInstanceByName('sogecommerce');

        $languages = array();
        foreach (SogecommerceApi::getSupportedLanguages() as $code => $label) {
            $languages[$code] = $sogecommerce->l($label, 'sogecommercehelperform');
        }

        asort($languages);

        $category_options = array(
            'FOOD_AND_GROCERY' => $sogecommerce->l('Food and grocery', 'sogecommercehelperform'),
            'AUTOMOTIVE' => $sogecommerce->l('Automotive', 'sogecommercehelperform'),
            'ENTERTAINMENT' => $sogecommerce->l('Entertainment', 'sogecommercehelperform'),
            'HOME_AND_GARDEN' => $sogecommerce->l('Home and garden', 'sogecommercehelperform'),
            'HOME_APPLIANCE' => $sogecommerce->l('Home appliance', 'sogecommercehelperform'),
            'AUCTION_AND_GROUP_BUYING' => $sogecommerce->l('Auction and group buying', 'sogecommercehelperform'),
            'FLOWERS_AND_GIFTS' => $sogecommerce->l('Flowers and gifts', 'sogecommercehelperform'),
            'COMPUTER_AND_SOFTWARE' => $sogecommerce->l('Computer and software', 'sogecommercehelperform'),
            'HEALTH_AND_BEAUTY' => $sogecommerce->l('Health and beauty', 'sogecommercehelperform'),
            'SERVICE_FOR_INDIVIDUAL' => $sogecommerce->l('Service for individual', 'sogecommercehelperform'),
            'SERVICE_FOR_BUSINESS' => $sogecommerce->l('Service for business', 'sogecommercehelperform'),
            'SPORTS' => $sogecommerce->l('Sports', 'sogecommercehelperform'),
            'CLOTHING_AND_ACCESSORIES' => $sogecommerce->l('Clothing and accessories', 'sogecommercehelperform'),
            'TRAVEL' => $sogecommerce->l('Travel', 'sogecommercehelperform'),
            'HOME_AUDIO_PHOTO_VIDEO' => $sogecommerce->l('Home audio, photo, video', 'sogecommercehelperform'),
            'TELEPHONY' => $sogecommerce->l('Telephony', 'sogecommercehelperform')
        );

        // Get documentation links.
        $doc_languages = array(
            'fr' => 'Français',
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
            'br' => 'Português',
            'pt' => 'Português'
            // Complete when other languages are managed.
        );

        foreach (SogecommerceApi::getOnlineDocUri() as $lang => $docUri) {
            $doc_files[$doc_languages[$lang]] = $docUri . 'prestashop/sitemap.html';
        }

        $placeholders = self::getArrayConfig('SOGECOMMERCE_STD_REST_PLACEHLDR');
        if (empty($placeholders)) {
            $placeholders = array('pan' => '', 'expiry' => '', 'cvv' => '');
        }

        $enabledCountries = Country::getCountries((int) $context->language->id, true);
        $all_countries = Country::getCountries((int) $context->language->id, false);
        $countryList = array();
        $countryList['ps_countries'] = array();
        foreach ($enabledCountries as $value) {
            $countryList['ps_countries'][$value['iso_code']] = $value['name'];
        }

        foreach (SogecommerceTools::$submodules as $key => $module) {
            $module_class_name = 'Sogecommerce' . $module.'Payment';
            $instance_module = new $module_class_name();
            if (method_exists($instance_module, 'getCountries') && $instance_module->getCountries()) {
                $submodule_specific_countries = $instance_module->getCountries();
                foreach ($submodule_specific_countries as $country) {
                    if (isset($countryList['ps_countries'][$country])) {
                        $countryList[$key][$country] = $countryList['ps_countries'][$country];
                    }
                }
            }
        }

        foreach ($all_countries as $value) {
            if ($value['iso_code'] === 'FR') {
                $countryList['FULLCB']['FR'] = $value['name'];
                break;
            }
        }

        $tpl_vars = array(
            'sogecommerce_support_email' => SogecommerceTools::getDefault('SUPPORT_EMAIL'),
            'sogecommerce_formatted_support_email' => SogecommerceApi::formatSupportEmails(SogecommerceTools::getDefault('SUPPORT_EMAIL')),
            'sogecommerce_plugin_version' => SogecommerceTools::getDefault('PLUGIN_VERSION'),
            'sogecommerce_gateway_version' => SogecommerceTools::getDefault('GATEWAY_VERSION'),
            'sogecommerce_contrib' => SogecommerceTools::getContrib(),
            'sogecommerce_installed_modules' => SogecommerceTools::getModulesInstalled(),
            'sogecommerce_card_data_entry_modes' => SogecommerceTools::getCardDataEntryModes(),
            'sogecommerce_employee' => $context->employee,

            'sogecommerce_plugin_features' => SogecommerceTools::$plugin_features,
            'sogecommerce_request_uri' => $_SERVER['REQUEST_URI'],

            'sogecommerce_doc_files' => $doc_files,
            'sogecommerce_enable_disable_options' => array(
                'False' => $sogecommerce->l('Disabled', 'sogecommercehelperform'),
                'True' => $sogecommerce->l('Enabled', 'sogecommercehelperform')
            ),
            'sogecommerce_mode_options' => array(
                'TEST' => $sogecommerce->l('TEST', 'sogecommercehelperform'),
                'PRODUCTION' => $sogecommerce->l('PRODUCTION', 'sogecommercehelperform')
            ),
            'sogecommerce_language_options' => $languages,
            'sogecommerce_validation_mode_options' => array(
                '' => $sogecommerce->l('Bank Back Office configuration', 'sogecommercehelperform'),
                '0' => $sogecommerce->l('Automatic', 'sogecommercehelperform'),
                '1' => $sogecommerce->l('Manual', 'sogecommercehelperform')
            ),
            'sogecommerce_payment_cards_options' => array('' => $sogecommerce->l('ALL', 'sogecommercehelperform')) + SogecommerceTools::getSupportedCardTypes(),
            'sogecommerce_multi_payment_cards_options' => array('' => $sogecommerce->l('ALL', 'sogecommercehelperform')) + SogecommerceTools::getSupportedMultiCardTypes(),
            'sogecommerce_category_options' => $category_options,
            'sogecommerce_yes_no_options' => array(
                'False' => $sogecommerce->l('No', 'sogecommercehelperform'),
                'True' => $sogecommerce->l('Yes', 'sogecommercehelperform')
            ),
            'sogecommerce_std_rest_theme_options' => array(
                'classic' => $sogecommerce->l('Classic', 'sogecommercehelperform'),
                'neon' => $sogecommerce->l('Neon', 'sogecommercehelperform')
            ),
            'sogecommerce_delivery_type_options' => array(
                'PACKAGE_DELIVERY_COMPANY' => $sogecommerce->l('Delivery company', 'sogecommercehelperform'),
                'RECLAIM_IN_SHOP' => $sogecommerce->l('Reclaim in shop', 'sogecommercehelperform'),
                'RELAY_POINT' => $sogecommerce->l('Relay point', 'sogecommercehelperform'),
                'RECLAIM_IN_STATION' => $sogecommerce->l('Reclaim in station', 'sogecommercehelperform')
            ),
            'sogecommerce_delivery_speed_options' => array(
                'STANDARD' => $sogecommerce->l('Standard', 'sogecommercehelperform'),
                'EXPRESS' => $sogecommerce->l('Express', 'sogecommercehelperform'),
                'PRIORITY' => $sogecommerce->l('Priority', 'sogecommercehelperform')
            ),
            'sogecommerce_delivery_delay_options' => array(
                'INFERIOR_EQUALS' => $sogecommerce->l('<= 1 hour', 'sogecommercehelperform'),
                'SUPERIOR' => $sogecommerce->l('> 1 hour', 'sogecommercehelperform'),
                'IMMEDIATE' => $sogecommerce->l('Immediate', 'sogecommercehelperform'),
                'ALWAYS' => $sogecommerce->l('24/7', 'sogecommercehelperform')
            ),
            'sogecommerce_failure_management_options' => array(
                SogecommerceTools::ON_FAILURE_RETRY => $sogecommerce->l('Go back to checkout', 'sogecommercehelperform'),
                SogecommerceTools::ON_FAILURE_SAVE => $sogecommerce->l('Save order and go back to order history', 'sogecommercehelperform')
            ),
            'sogecommerce_cart_management_options' => array(
                SogecommerceTools::EMPTY_CART => $sogecommerce->l('Empty cart to avoid amount errors', 'sogecommercehelperform'),
                SogecommerceTools::KEEP_CART => $sogecommerce->l('Keep cart (PrestaShop default behavior)', 'sogecommercehelperform')
            ),
            'sogecommerce_card_data_mode_options' => array(
                '1' => $sogecommerce->l('Bank data acquisition on payment gateway', 'sogecommercehelperform'),
                '2' => $sogecommerce->l('Card type selection on merchant site', 'sogecommercehelperform'),
                '7' => $sogecommerce->l('Embedded payment fields on merchant site (REST API)', 'sogecommercehelperform'),
                '8' => $sogecommerce->l('Embedded payment fields extended on merchant site with logos (REST API)', 'sogecommercehelperform'),
                '9' => $sogecommerce->l('Embedded payment fields extended on merchant site without logos (REST API)', 'sogecommercehelperform')
            ),
            'sogecommerce_countries_options' => array(
                '1' => $sogecommerce->l('All Allowed Countries', 'sogecommercehelperform'),
                '2' => $sogecommerce->l('Specific Countries', 'sogecommercehelperform')
            ),
            'sogecommerce_countries_list' => $countryList,
            'sogecommerce_card_selection_mode_options' => array(
                '1' => $sogecommerce->l('On payment gateway', 'sogecommercehelperform'),
                '2' => $sogecommerce->l('On merchant site', 'sogecommercehelperform')
            ),
            'sogecommerce_default_multi_option' => array(
                'label' => '',
                'min_amount' => '',
                'max_amount' => '',
                'contract' => '',
                'count' => '',
                'period' => '',
                'first' => ''
            ),
            'sogecommerce_default_oney_option' => array(
                'label' => '',
                'code' => '',
                'min_amount' => '',
                'max_amount' => '',
                'count' => '',
                'rate' => ''
            ),
            'sogecommerce_default_franfinance_option' => array(
                'label' => '',
                'count' => '3',
                'fees' => '-1',
                'min_amount' => '100',
                'max_amount' => '3000'
            ),
            'franfinance_count' => array(
                '3' => '3x',
                '4' => '4x'
            ),
            'oney_cards' => array(
                'ONEY_3X_4X' => $sogecommerce->l('Payment in 3 or 4 times Oney', 'sogecommercehelperform'),
                'ONEY_10X_12X' => $sogecommerce->l('Payment in 10 or 12 times Oney', 'sogecommercehelperform'),
                'ONEY_PAYLATER' => 'Pay Later Oney'
            ),
            'fees_options' => array(
                '-1' => $sogecommerce->l('Bank Back Office configuration', 'sogecommercehelperform'),
                '0' => $sogecommerce->l('Without fees', 'sogecommercehelperform'),
                '1' => $sogecommerce->l('With fees', 'sogecommercehelperform')
            ),
            'sogecommerce_default_other_payment_means_option' => array(
                'title' => '',
                'code' => '',
                'min_amount' => '',
                'max_amount' => '',
                'validation' => '-1',
                'capture' => '',
                'cart' => 'False',
                'embedded' => 'False'
            ),
            'sogecommerce_default_extra_payment_means_option' => array(
                'code' => '',
                'title' => ''
            ),
            'sogecommerce_sepa_mandate_mode_options' => array(
                'PAYMENT' => $sogecommerce->l('One-off SEPA direct debit', 'sogecommercehelperform'),
                'REGISTER_PAY' => $sogecommerce->l('Register a recurrent SEPA mandate with direct debit', 'sogecommercehelperform'),
                'REGISTER' => $sogecommerce->l('Register a recurrent SEPA mandate without direct debit', 'sogecommercehelperform')
            ),
            'sogecommerce_extra_options' => self::getExtraConfig(),

            'prestashop_categories' => Category::getCategories((int) $context->language->id, true, false),
            'prestashop_languages' => Language::getLanguages(false),
            'prestashop_lang' => Language::getLanguage((int) $context->language->id),
            'prestashop_carriers' => Carrier::getCarriers(
                (int) $context->language->id,
                true,
                false,
                false,
                null,
                Carrier::ALL_CARRIERS
            ),
            'prestashop_groups' => self::getAuthorizedGroups(),

            'SOGECOMMERCE_ENABLE_LOGS' => Configuration::get('SOGECOMMERCE_ENABLE_LOGS'),
            'SOGECOMMERCE_ENABLE_CUST_MSG' => Configuration::get('SOGECOMMERCE_ENABLE_CUST_MSG'),

            'SOGECOMMERCE_SITE_ID' => Configuration::get('SOGECOMMERCE_SITE_ID'),
            'SOGECOMMERCE_KEY_TEST' => Configuration::get('SOGECOMMERCE_KEY_TEST'),
            'SOGECOMMERCE_KEY_PROD' => Configuration::get('SOGECOMMERCE_KEY_PROD'),
            'SOGECOMMERCE_MODE' => Configuration::get('SOGECOMMERCE_MODE'),
            'SOGECOMMERCE_SIGN_ALGO' => Configuration::get('SOGECOMMERCE_SIGN_ALGO'),
            'SOGECOMMERCE_PLATFORM_URL' => Configuration::get('SOGECOMMERCE_PLATFORM_URL'),
            'SOGECOMMERCE_NOTIFY_URL' => self::getIpnUrl(),

            'SOGECOMMERCE_PUBKEY_TEST' => Configuration::get('SOGECOMMERCE_PUBKEY_TEST'),
            'SOGECOMMERCE_PRIVKEY_TEST' => Configuration::get('SOGECOMMERCE_PRIVKEY_TEST'),
            'SOGECOMMERCE_PUBKEY_PROD' => Configuration::get('SOGECOMMERCE_PUBKEY_PROD'),
            'SOGECOMMERCE_PRIVKEY_PROD' => Configuration::get('SOGECOMMERCE_PRIVKEY_PROD'),
            'SOGECOMMERCE_RETKEY_TEST' => Configuration::get('SOGECOMMERCE_RETKEY_TEST'),
            'SOGECOMMERCE_RETKEY_PROD' => Configuration::get('SOGECOMMERCE_RETKEY_PROD'),
            'SOGECOMMERCE_REST_NOTIFY_URL' => self::getIpnUrl(),
            'SOGECOMMERCE_REST_SERVER_URL' => Configuration::get('SOGECOMMERCE_REST_SERVER_URL'),
            'SOGECOMMERCE_REST_JS_CLIENT_URL' => Configuration::get('SOGECOMMERCE_REST_JS_CLIENT_URL'),

            'SOGECOMMERCE_DEFAULT_LANGUAGE' => Configuration::get('SOGECOMMERCE_DEFAULT_LANGUAGE'),
            'SOGECOMMERCE_AVAILABLE_LANGUAGES' => ! Configuration::get('SOGECOMMERCE_AVAILABLE_LANGUAGES') ?
                                            array('') :
                                            explode(';', Configuration::get('SOGECOMMERCE_AVAILABLE_LANGUAGES')),
            'SOGECOMMERCE_DELAY' => Configuration::get('SOGECOMMERCE_DELAY'),
            'SOGECOMMERCE_VALIDATION_MODE' => Configuration::get('SOGECOMMERCE_VALIDATION_MODE'),

            'SOGECOMMERCE_THEME_CONFIG' => self::getLangConfig('SOGECOMMERCE_THEME_CONFIG'),
            'SOGECOMMERCE_SHOP_NAME' => Configuration::get('SOGECOMMERCE_SHOP_NAME'),
            'SOGECOMMERCE_SHOP_URL' => Configuration::get('SOGECOMMERCE_SHOP_URL'),

            'SOGECOMMERCE_3DS_MIN_AMOUNT' => self::getArrayConfig('SOGECOMMERCE_3DS_MIN_AMOUNT'),

            'SOGECOMMERCE_DOCUMENT' => Configuration::get('SOGECOMMERCE_DOCUMENT'),
            'SOGECOMMERCE_NUMBER' => Configuration::get('SOGECOMMERCE_NUMBER'),
            'SOGECOMMERCE_NEIGHBORHOOD' => Configuration::get('SOGECOMMERCE_NEIGHBORHOOD'),

            'SOGECOMMERCE_REDIRECT_ENABLED' => Configuration::get('SOGECOMMERCE_REDIRECT_ENABLED'),
            'SOGECOMMERCE_REDIRECT_SUCCESS_T' => Configuration::get('SOGECOMMERCE_REDIRECT_SUCCESS_T'),
            'SOGECOMMERCE_REDIRECT_SUCCESS_M' => self::getLangConfig('SOGECOMMERCE_REDIRECT_SUCCESS_M'),
            'SOGECOMMERCE_REDIRECT_ERROR_T' => Configuration::get('SOGECOMMERCE_REDIRECT_ERROR_T'),
            'SOGECOMMERCE_REDIRECT_ERROR_M' => self::getLangConfig('SOGECOMMERCE_REDIRECT_ERROR_M'),
            'SOGECOMMERCE_RETURN_MODE' => Configuration::get('SOGECOMMERCE_RETURN_MODE'),
            'SOGECOMMERCE_FAILURE_MANAGEMENT' => Configuration::get('SOGECOMMERCE_FAILURE_MANAGEMENT'),
            'SOGECOMMERCE_CART_MANAGEMENT' => Configuration::get('SOGECOMMERCE_CART_MANAGEMENT'),

            'SOGECOMMERCE_SEND_CART_DETAIL' => Configuration::get('SOGECOMMERCE_SEND_CART_DETAIL'),
            'SOGECOMMERCE_COMMON_CATEGORY' => Configuration::get('SOGECOMMERCE_COMMON_CATEGORY'),
            'SOGECOMMERCE_CATEGORY_MAPPING' => self::getArrayConfig('SOGECOMMERCE_CATEGORY_MAPPING'),
            'SOGECOMMERCE_SEND_SHIP_DATA' => Configuration::get('SOGECOMMERCE_SEND_SHIP_DATA'),
            'SOGECOMMERCE_ONEY_SHIP_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_ONEY_SHIP_OPTIONS'),

            'SOGECOMMERCE_STD_TITLE' => self::getLangConfig('SOGECOMMERCE_STD_TITLE'),
            'SOGECOMMERCE_STD_ENABLED' => Configuration::get('SOGECOMMERCE_STD_ENABLED'),
            'SOGECOMMERCE_STD_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_STD_AMOUNTS'),
            'SOGECOMMERCE_STD_DELAY' => Configuration::get('SOGECOMMERCE_STD_DELAY'),
            'SOGECOMMERCE_STD_VALIDATION' => Configuration::get('SOGECOMMERCE_STD_VALIDATION'),
            'SOGECOMMERCE_STD_PAYMENT_CARDS' => ! Configuration::get('SOGECOMMERCE_STD_PAYMENT_CARDS') ?
                                            array('') :
                                            explode(';', Configuration::get('SOGECOMMERCE_STD_PAYMENT_CARDS')),
            'SOGECOMMERCE_STD_CARD_DATA_MODE' => SogecommerceTools::getEntryMode('SOGECOMMERCE_STD_'),
            'SOGECOMMERCE_STD_REST_POPIN_MODE' => Configuration::get('SOGECOMMERCE_STD_REST_POPIN_MODE'),
            'SOGECOMMERCE_STD_REST_THEME' => Configuration::get('SOGECOMMERCE_STD_REST_THEME') ?
                                        Configuration::get('SOGECOMMERCE_STD_REST_THEME') : 'classic',
            'SOGECOMMERCE_STD_SF_COMPACT_MODE' => Configuration::get('SOGECOMMERCE_STD_SF_COMPACT_MODE'),
            'SOGECOMMERCE_STD_SF_THRESHOLD' => Configuration::get('SOGECOMMERCE_STD_SF_THRESHOLD'),
            'SOGECOMMERCE_STD_SF_DISPLAY_TITLE' => Configuration::get('SOGECOMMERCE_STD_SF_DISPLAY_TITLE'),
            'SOGECOMMERCE_STD_REST_PLACEHLDR' => $placeholders,
            'SOGECOMMERCE_STD_REST_LBL_REGIST' => self::getLangConfig('SOGECOMMERCE_STD_REST_LBL_REGIST'),
            'SOGECOMMERCE_STD_REST_ATTEMPTS' => Configuration::get('SOGECOMMERCE_STD_REST_ATTEMPTS'),
            'SOGECOMMERCE_STD_1_CLICK_PAYMENT' => Configuration::get('SOGECOMMERCE_STD_1_CLICK_PAYMENT'),
            'SOGECOMMERCE_STD_USE_WALLET' => Configuration::get('SOGECOMMERCE_STD_USE_WALLET'),
            'SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE' => Configuration::get('SOGECOMMERCE_STD_USE_PAYMENT_MEAN_AS_TITLE'),
            'SOGECOMMERCE_STD_SELECT_BY_DEFAULT' => Configuration::get('SOGECOMMERCE_STD_SELECT_BY_DEFAULT'),

            'SOGECOMMERCE_MULTI_TITLE' => self::getLangConfig('SOGECOMMERCE_MULTI_TITLE'),
            'SOGECOMMERCE_MULTI_ENABLED' => Configuration::get('SOGECOMMERCE_MULTI_ENABLED'),
            'SOGECOMMERCE_MULTI_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_MULTI_AMOUNTS'),
            'SOGECOMMERCE_MULTI_DELAY' => Configuration::get('SOGECOMMERCE_MULTI_DELAY'),
            'SOGECOMMERCE_MULTI_VALIDATION' => Configuration::get('SOGECOMMERCE_MULTI_VALIDATION'),
            'SOGECOMMERCE_MULTI_CARD_MODE' => Configuration::get('SOGECOMMERCE_MULTI_CARD_MODE'),
            'SOGECOMMERCE_MULTI_PAYMENT_CARDS' => ! Configuration::get('SOGECOMMERCE_MULTI_PAYMENT_CARDS') ?
                                            array('') :
                                            explode(';', Configuration::get('SOGECOMMERCE_MULTI_PAYMENT_CARDS')),
            'SOGECOMMERCE_MULTI_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_MULTI_OPTIONS'),

            'SOGECOMMERCE_ANCV_TITLE' => self::getLangConfig('SOGECOMMERCE_ANCV_TITLE'),
            'SOGECOMMERCE_ANCV_ENABLED' => Configuration::get('SOGECOMMERCE_ANCV_ENABLED'),
            'SOGECOMMERCE_ANCV_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_ANCV_AMOUNTS'),
            'SOGECOMMERCE_ANCV_DELAY' => Configuration::get('SOGECOMMERCE_ANCV_DELAY'),
            'SOGECOMMERCE_ANCV_VALIDATION' => Configuration::get('SOGECOMMERCE_ANCV_VALIDATION'),

            'SOGECOMMERCE_ONEY34_TITLE' => self::getLangConfig('SOGECOMMERCE_ONEY34_TITLE'),
            'SOGECOMMERCE_ONEY34_ENABLED' => Configuration::get('SOGECOMMERCE_ONEY34_ENABLED'),
            'SOGECOMMERCE_ONEY34_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_ONEY34_AMOUNTS'),
            'SOGECOMMERCE_ONEY34_DELAY' => Configuration::get('SOGECOMMERCE_ONEY34_DELAY'),
            'SOGECOMMERCE_ONEY34_VALIDATION' => Configuration::get('SOGECOMMERCE_ONEY34_VALIDATION'),
            'SOGECOMMERCE_ONEY34_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_ONEY34_OPTIONS'),

            'SOGECOMMERCE_FFIN_TITLE' => self::getLangConfig('SOGECOMMERCE_FFIN_TITLE'),
            'SOGECOMMERCE_FFIN_ENABLED' => Configuration::get('SOGECOMMERCE_FFIN_ENABLED'),
            'SOGECOMMERCE_FFIN_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_FFIN_AMOUNTS'),
            'SOGECOMMERCE_FFIN_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_FFIN_OPTIONS'),

            'SOGECOMMERCE_FULLCB_TITLE' => self::getLangConfig('SOGECOMMERCE_FULLCB_TITLE'),
            'SOGECOMMERCE_FULLCB_ENABLED' => Configuration::get('SOGECOMMERCE_FULLCB_ENABLED'),
            'SOGECOMMERCE_FULLCB_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_FULLCB_AMOUNTS'),
            'SOGECOMMERCE_FULLCB_ENABLE_OPTS' => Configuration::get('SOGECOMMERCE_FULLCB_ENABLE_OPTS'),
            'SOGECOMMERCE_FULLCB_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_FULLCB_OPTIONS'),

            'SOGECOMMERCE_SEPA_TITLE' => self::getLangConfig('SOGECOMMERCE_SEPA_TITLE'),
            'SOGECOMMERCE_SEPA_ENABLED' => Configuration::get('SOGECOMMERCE_SEPA_ENABLED'),
            'SOGECOMMERCE_SEPA_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_SEPA_AMOUNTS'),
            'SOGECOMMERCE_SEPA_DELAY' => Configuration::get('SOGECOMMERCE_SEPA_DELAY'),
            'SOGECOMMERCE_SEPA_VALIDATION' => Configuration::get('SOGECOMMERCE_SEPA_VALIDATION'),
            'SOGECOMMERCE_SEPA_MANDATE_MODE' => Configuration::get('SOGECOMMERCE_SEPA_MANDATE_MODE'),
            'SOGECOMMERCE_SEPA_1_CLICK_PAYMNT' => Configuration::get('SOGECOMMERCE_SEPA_1_CLICK_PAYMNT'),

            'SOGECOMMERCE_SOFORT_TITLE' => self::getLangConfig('SOGECOMMERCE_SOFORT_TITLE'),
            'SOGECOMMERCE_SOFORT_ENABLED' => Configuration::get('SOGECOMMERCE_SOFORT_ENABLED'),
            'SOGECOMMERCE_SOFORT_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_SOFORT_AMOUNTS'),

            'SOGECOMMERCE_PAYPAL_TITLE' => self::getLangConfig('SOGECOMMERCE_PAYPAL_TITLE'),
            'SOGECOMMERCE_PAYPAL_ENABLED' => Configuration::get('SOGECOMMERCE_PAYPAL_ENABLED'),
            'SOGECOMMERCE_PAYPAL_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_PAYPAL_AMOUNTS'),
            'SOGECOMMERCE_PAYPAL_DELAY' => Configuration::get('SOGECOMMERCE_PAYPAL_DELAY'),
            'SOGECOMMERCE_PAYPAL_VALIDATION' => Configuration::get('SOGECOMMERCE_PAYPAL_VALIDATION'),

            'SOGECOMMERCE_CHOOZEO_TITLE' => self::getLangConfig('SOGECOMMERCE_CHOOZEO_TITLE'),
            'SOGECOMMERCE_CHOOZEO_ENABLED' => Configuration::get('SOGECOMMERCE_CHOOZEO_ENABLED'),
            'SOGECOMMERCE_CHOOZEO_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_CHOOZEO_AMOUNTS'),
            'SOGECOMMERCE_CHOOZEO_DELAY' => Configuration::get('SOGECOMMERCE_CHOOZEO_DELAY'),
            'SOGECOMMERCE_CHOOZEO_OPTIONS' => self::getArrayConfig('SOGECOMMERCE_CHOOZEO_OPTIONS'),

            'SOGECOMMERCE_OTHER_GROUPED_VIEW' => Configuration::get('SOGECOMMERCE_OTHER_GROUPED_VIEW'),
            'SOGECOMMERCE_OTHER_ENABLED' => Configuration::get('SOGECOMMERCE_OTHER_ENABLED'),
            'SOGECOMMERCE_OTHER_TITLE' => self::getLangConfig('SOGECOMMERCE_OTHER_TITLE'),
            'SOGECOMMERCE_OTHER_AMOUNTS' => self::getArrayConfig('SOGECOMMERCE_OTHER_AMOUNTS'),
            'SOGECOMMERCE_OTHER_PAYMENT_MEANS' => self::getArrayConfig('SOGECOMMERCE_OTHER_PAYMENT_MEANS'),
            'SOGECOMMERCE_EXTRA_PAYMENT_MEANS' => self::getArrayConfig('SOGECOMMERCE_EXTRA_PAYMENT_MEANS')
        );

        foreach (SogecommerceTools::$submodules as $key => $module) {
            $tpl_vars['SOGECOMMERCE_' . $key . '_COUNTRY'] = Configuration::get('SOGECOMMERCE_' . $key . '_COUNTRY');
            $tpl_vars['SOGECOMMERCE_' . $key . '_COUNTRY_LST'] = ! Configuration::get('SOGECOMMERCE_' . $key . '_COUNTRY_LST') ?
                array() : explode(';', Configuration::get('SOGECOMMERCE_' . $key . '_COUNTRY_LST'));
        }

        return $tpl_vars;
    }

    private static function getIpnUrl()
    {
        $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));

        // SSL enabled on default shop?
        $id_shop_group = isset($shop->id_shop_group) ? $shop->id_shop_group : $shop->id_group_shop;
        $ssl = Configuration::get('PS_SSL_ENABLED', null, $id_shop_group, $shop->id);

        $ipn = ($ssl ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain)
            . $shop->getBaseURI() . 'modules/sogecommerce/validation.php';

        return $ipn;
    }

    private static function getArrayConfig($name)
    {
        $value = @unserialize(Configuration::get($name));

        if (! is_array($value)) {
            $value = array();
        }

        return $value;
    }

    private static function getLangConfig($name)
    {
        $languages = Language::getLanguages(false);

        $result = array();
        foreach ($languages as $language) {
            $result[$language['id_lang']] = Configuration::get($name, $language['id_lang']);
        }

        return $result;
    }

    private static function getAuthorizedGroups()
    {
        $context = Context::getContext();

        /* @var Sogecommerce */
        $sogecommerce = Module::getInstanceByName('sogecommerce');

        $sql = 'SELECT DISTINCT gl.`id_group`, gl.`name` FROM `' . _DB_PREFIX_ . 'group_lang` AS gl
            INNER JOIN `' . _DB_PREFIX_ . 'module_group` AS mg
            ON (
                gl.`id_group` = mg.`id_group`
                AND mg.`id_module` = ' . (int) $sogecommerce->id . '
                AND mg.`id_shop` = ' . (int) $context->shop->id . '
            )
            WHERE gl.`id_lang` = ' . (int) $context->language->id;

        return Db::getInstance()->executeS($sql);
    }

    private static function getExtraConfig()
    {
        $fields[''] = '-- Select custom field --';
        $customer = Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "customer`");
        foreach ($customer as $k => $v) {
            $input = _DB_PREFIX_ . 'customer.' . $v['Field'];
            $fields[$input] = $input;
        }

        $address = Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "address`");
        foreach ($address as $k => $v) {
            $input = _DB_PREFIX_ . 'address.' . $v['Field'];
            $fields[$input] = $input;
        }

        return $fields;
    }
}
