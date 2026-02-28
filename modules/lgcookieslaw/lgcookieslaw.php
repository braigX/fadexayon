<?php
/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require realpath(dirname(__FILE__)) . '/config/config.inc.php';

class LGCookiesLaw extends Module
{
    public $ps_version;
    public $platform = 'lg';
    public $id_product;
    public $author_address;

    protected $cookie_cipher_tool;

    protected $configurations_list;
    protected $hooks_list;
    protected $tabs_menu;
    protected $active_tab;
    protected $default_tab;
    protected $module_url;
    protected $module_errors = [];
    protected $information_letters = [];
    protected $warning_letters = [];

    protected $module_languages = [];
    protected $default_form_language;
    protected $allow_employee_form_lang;

    /* Helper List Vars */
    protected static $currentIndex;
    protected $list_table;
    protected $list_id;
    protected $list_identifier = false;
    protected $className;
    protected $position_identifier;
    protected $position_group_identifier;
    protected $deleted = false;
    protected $id_object;
    protected $object;
    protected $fieldImageSettings = [];
    protected $imageType = 'jpg';
    protected $max_image_size;
    protected $shopLinkType;
    protected $shopShareDatas = false;
    protected $multishop_context = -1;
    protected $lang = false;
    protected $no_link = false;

    protected $action;
    protected $actions = [];
    protected $list_skip_actions = [];
    protected $bulk_actions = [];
    protected $boxes;

    protected $list_default_pagination = 20;
    protected $list_pagination = [20, 50, 100, 300, 1000];

    protected $list_defaultOrderBy = false;
    protected $list_defaultOrderWay = 'ASC';
    protected $list_orderBy;
    protected $list_orderWay;

    protected $list_filter;
    protected $list_filterHaving;
    protected $list_tmpTableFilter = '';

    protected $list_select;
    protected $list_join;
    protected $list_where;
    protected $list_group;
    protected $list_having;
    protected $list_use_found_rows = true;
    protected $explicitSelect = false;

    protected $fields_list;
    protected $fields_form;
    protected $list_listsql = '';
    protected $list_list = [];
    protected $list_list_error;
    protected $list_listTotal = 0;

    protected $helper_title;
    protected $helper_title_icon;
    protected $helper_token;

    protected $toolbar_btn;
    protected $toolbar_scroll = true;
    protected $show_toolbar = true;

    public function __construct()
    {
        $this->name = 'lgcookieslaw';
        $this->tab = 'front_office_features';
        $this->version = '2.1.11';
        $this->author = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->id_product = 7268;
        $this->module_key = '56c109696b8e3185bc40d38d855f7332';
        $this->platform = 'lg';

        $this->bootstrap = true;

        $this->ps_version = '15';

        if (version_compare(_PS_VERSION_, '1.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $this->ps_version = '16';
        } elseif (version_compare(_PS_VERSION_, '1.7.0', '>=') && version_compare(_PS_VERSION_, '8.0.0', '<')) {
            $this->ps_version = '17';
        } elseif (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $this->ps_version = '8';
        }

        parent::__construct();

        $this->displayName = $this->l('EU Cookie Law (Notification Banner + Cookie Blocker)');
        $this->description = $this->l('Display a cookie banner and block cookies before getting the user consent.');

        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_,
        ];

        $this->hooks_list = [
            'displayAfterTitleTag',
            'displayTop',
            'displayMobileTop',
            'displayHeader',
            'displayBackofficeHeader',
            'displayBackOfficeTop',
            'displayCustomerAccount',
            'displayFooter',
            'displayFooterBuilder',
            'displayAfterBodyOpeningTag',
            'displayBeforeBodyClosingTag',
        ];

        $this->default_tab = 'settings';

        $this->getLanguages();

        $this->configurations_list = [
            'PS_LGCOOKIES_BANNER_HOOK' => [
                'default_value' => 'footer',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_THIRD_PARTIES' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_RELOAD' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BLOCK_NAVIGATION' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_SHOW_CLOSE_BTN' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_CLOSE_BTN_RJCT_CKS' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_SHOW_FIXED_BTN' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_FIXED_BTN_POSITION' => [
                'default_value' => 'left',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_FIXED_BTN_SVG_COLOR' => [
                'default_value' => '#ffffff',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_DISALLOW' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_SHOW_BANNER_IN_CMS' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_SAVE_USER_CONSENT' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_ANONYMIZE_UC_IP' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_DELETE_USER_CONSENT' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_CONSENT_MODE' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_TESTMODE' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_IPTESTMODE' => [
                'default_value' => Tools::getRemoteAddr(),
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_COOKIE_TIMELIFE' => [
                'default_value' => '31536000',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_COOKIE_NAME' => [
                'default_value' => 'lgcookieslaw',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_USE_COOKIE_VAR' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_LOAD_FANCYBOX' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_PUC_COMPATIBILITY' => [
                'default_value' => false,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BOTS' => [
                'default_value' => 'Teoma,alexa,froogle,Gigabot,inktomi,looksmart,URL_Spider_SQL,Firefly,NationalDirectory,' .
                                    'AskJeeves,TECNOSEEK,InfoSeek,WebFindBot,girafabot,crawler,www.galaxy.com,Googlebot,Scooter,' .
                                    'TechnoratiSnoop,Rankivabot,Mediapartners-Google, Sogouwebspider,WebAltaCrawler,TweetmemeBot,' .
                                    'Butterfly,Twitturls,Me.dium,Twiceler',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_POSITION' => [
                'default_value' => '3',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_BG_COLOR' => [
                'default_value' => '#3B3B3B',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_BG_OPACITY' => [
                'default_value' => '0.9',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_SHADOWCOLOR' => [
                'default_value' => '#707070',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_FONTCOLOR' => [
                'default_value' => '#FFFFFF',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_BANNER_MESSAGE' => [
                'default_value' => [],
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => true,
            ],
            'PS_LGCOOKIES_ACPT_BTN_TITLE' => [
                'default_value' => [],
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_ACPT_BTN_BG_COLOR' => [
                'default_value' => '#8BC954',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_ACPT_BTN_FONT_COLOR' => [
                'default_value' => '#FFFFFF',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_INFO_LINK_TITLE' => [
                'default_value' => [],
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_INFO_LINK_ID_CMS' => [
                'default_value' => Configuration::get('PS_CONDITIONS_CMS_ID'),
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_INFO_LINK_TARGET' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_SHOW_RJCT_BTN' => [
                'default_value' => true,
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_RJCT_BTN_TITLE' => [
                'default_value' => [],
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_RJCT_BTN_BG_COLOR' => [
                'default_value' => '#8BC954',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_RJCT_BTN_FONT_COLOR' => [
                'default_value' => '#FFFFFF',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
            'PS_LGCOOKIES_NOTICED_MODULES' => [
                'default_value' => '',
                'auto_proccess' => true,
                'add_field_value' => true,
                'html' => false,
            ],
        ];

        $installation_defaults = $this->getInstallationDefaults();

        foreach ($this->module_languages as $lang) {
            $iso_code = $lang['iso_code'];
            $id_lang = $lang['id_lang'];

            $accept_button_title_index = 'PS_LGCOOKIES_ACPT_BTN_TITLE';

            $accept_button_title_default_value = isset($installation_defaults[$accept_button_title_index][$iso_code]) ?
                $installation_defaults[$accept_button_title_index][$iso_code] :
                $installation_defaults[$accept_button_title_index]['en'];

            $this->configurations_list[$accept_button_title_index]['default_value'][(int) $id_lang] =
                $accept_button_title_default_value;

            $info_link_title_index = 'PS_LGCOOKIES_INFO_LINK_TITLE';

            $info_button_title_default_value = isset($installation_defaults[$info_link_title_index][$iso_code]) ?
                $installation_defaults[$info_link_title_index][$iso_code] :
                $installation_defaults[$info_link_title_index]['en'];

            $this->configurations_list[$info_link_title_index]['default_value'][(int) $id_lang] =
                $info_button_title_default_value;

            $reject_button_title_index = 'PS_LGCOOKIES_RJCT_BTN_TITLE';

            $reject_button_title_default_value = isset($installation_defaults[$reject_button_title_index][$iso_code]) ?
                $installation_defaults[$reject_button_title_index][$iso_code] :
                $installation_defaults[$reject_button_title_index]['en'];

            $this->configurations_list[$reject_button_title_index]['default_value'][(int) $id_lang] =
                $reject_button_title_default_value;

            $banner_message_index = 'PS_LGCOOKIES_BANNER_MESSAGE';

            $banner_message_default_value = isset($installation_defaults[$banner_message_index][$iso_code]) ?
                $installation_defaults[$banner_message_index][$iso_code] :
                $installation_defaults[$banner_message_index]['en'];

            $this->configurations_list[$banner_message_index]['default_value'][(int) $id_lang] =
                $banner_message_default_value;
        }

        $noticed_modules = [];

        foreach (LGCookiesLawPurpose::getInstallationDefaults() as $index => $installation_default) {
            if (!empty($installation_default['locked_modules'])) {
                $noticed_modules = array_merge($noticed_modules, $installation_default['locked_modules']);
            }
        }

        if (!empty($noticed_modules)) {
            $this->configurations_list['PS_LGCOOKIES_NOTICED_MODULES']['default_value'] =
                implode(',', $noticed_modules);
        }

        $this->information_letters = range('A', 'B');
        $this->warning_letters = range('A', 'I');

        $this->cookie_cipher_tool = $this->ps_version == '16' ?
            new Blowfish(_COOKIE_KEY_, _COOKIE_IV_) :
            new PhpEncryption(_NEW_COOKIE_KEY_);

        $this->confirmUninstall = $this->l('Do you want to uninstall this module?');
    }

    public static function getInstallationDefaults()
    {
        $installation_defaults = [
            'PS_LGCOOKIES_ACPT_BTN_TITLE' => [
                'es' => 'Acepto',
                'en' => 'I accept',
                'gb' => 'I accept',
                'fr' => 'J\'accepte',
                'pl' => 'Akceptuję',
                'pt' => 'Aceito',
                'de' => 'Ich akzeptiere',
                'it' => 'Accetto',
                'nl' => 'Ik aanvaard',
            ],
            'PS_LGCOOKIES_INFO_LINK_TITLE' => [
                'es' => 'Política de cookies',
                'en' => 'Cookie policy',
                'gb' => 'Cookie policy',
                'fr' => 'Politique de cookies',
                'pl' => 'Polityka cookies',
                'pt' => 'Política de cookies',
                'de' => 'Weitere Informationen',
                'it' => 'Politica dei cookie',
                'nl' => 'Cookiebeleid',
            ],
            'PS_LGCOOKIES_RJCT_BTN_TITLE' => [
                'es' => 'Rechazar todo',
                'en' => 'Reject All',
                'gb' => 'Reject All',
                'fr' => 'Rejeter tout',
                'pl' => 'Odrzuć wszystko',
                'pt' => 'Rejeitar tudo',
                'de' => 'Alle ablehnen',
                'it' => 'Rifiuta tutti',
                'nl' => 'Alles afwijzen',
            ],
            'PS_LGCOOKIES_BANNER_MESSAGE' => [
                'es' => 'Este sitio web utiliza cookies propias y de terceros para mejorar nuestros servicios ' .
                    'y mostrarle publicidad relacionada con sus preferencias mediante el análisis de sus hábitos ' .
                    'de navegación. Para dar su consentimiento sobre su uso pulse el botón Acepto.',
                'en' => 'This website uses its own and third-party cookies to improve our services and ' .
                    'show you advertising related to your preferences by analyzing your browsing habits. ' .
                    'To give your consent to its use, press the Accept button.',
                'gb' => 'This website uses its own and third-party cookies to improve our services and ' .
                    'show you advertising related to your preferences by analyzing your browsing habits. ' .
                    'To give your consent to its use, press the Accept button.',
                'fr' => 'Ce site Web utilise ses propres cookies et ceux de tiers pour améliorer nos ' .
                    'services et vous montrer des publicités liées à vos préférences en analysant vos ' .
                    'habitudes de navigation. Pour donner votre consentement à son utilisation, appuyez ' .
                    'sur le bouton Accepter.',
                'pl' => 'Ta witryna korzysta z własnych plików cookie i plików cookie stron trzecich w ' .
                    'celu ulepszenia naszych usług i pokazywać Ci reklamy związane z Twoimi preferencjami, ' .
                    'analizując Twoje nawyki nawigacja. Aby wyrazić zgodę na jego użycie, naciśnij przycisk Akceptuj.',
                'pt' => 'Este site usa cookies próprios e de terceiros para melhorar nossos serviços ' .
                    'e mostrar a publicidade relacionada às suas preferências, analisando seus hábitos' .
                    'navegação. Para dar seu consentimento ao seu uso, pressione o botão Aceito.',
                'de' => 'Diese Website verwendet eigene Cookies und Cookies von Drittanbietern, um unsere' .
                    'Dienste zu verbessern. Und zeigen Sie Werbung in Bezug auf Ihre Vorlieben, indem ' .
                    'Sie Ihre Gewohnheiten analysieren navigation. Um Ihre Zustimmung zu seiner Verwendung ' .
                    'zu geben, klicken Sie auf die Schaltfläche Akzeptieren.',
                'it' => 'Questo sito web utilizza cookie propri e di terze parti per migliorare i nostri ' .
                    'servizi e mostrarti pubblicità relativa alle tue preferenze analizzando le tue abitudini' .
                    'di navigazione. Per dare il tuo consenso al suo utilizzo, premi il pulsante Accetta.',
                'nl' => 'Deze website maakt gebruik van eigen cookies en cookies van derden om onze diensten ' .
                    'te verbeteren en om u advertenties te tonen die verband houden met uw voorkeuren door uw ' .
                    'surfgedrag te analyseren. Om uw toestemming te geven voor het gebruik ervan, drukt u op ' .
                    'de knop Accepteren.',
            ],
        ];

        return $installation_defaults;
    }

    public function install()
    {
        return parent::install()
            && $this->installConfigurations()
            && $this->installHooks()
            && $this->installSQL()
            && $this->installationDefaults();
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallConfigurations()
            && $this->uninstallSQL();
    }

    protected function installConfigurations()
    {
        $success = true;

        foreach (Shop::getShops(false, null, true) as $id_shop) {
            $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

            foreach ($this->configurations_list as $configuration_name => $configuration) {
                $success &= Configuration::updateValue(
                    $configuration_name,
                    $configuration['default_value'],
                    $configuration['html'],
                    (int) $id_shop_group,
                    (int) $id_shop
                );

                if (!$success) {
                    break;
                }
            }
        }

        return $success;
    }

    protected function uninstallConfigurations()
    {
        $success = true;

        foreach ($this->configurations_list as $configuration_name => $configuration) {
            $success &= Configuration::deleteByName($configuration_name);

            if (!$success) {
                break;
            }
        }

        unset($configuration_name);
        unset($configuration);

        return $success;
    }

    protected function installHooks()
    {
        $success = true;

        foreach ($this->hooks_list as $hook_name) {
            $success &= $this->registerHook($hook_name);

            if (!$success) {
                break;
            }
        }

        $success &= $this->updatePosition(Hook::getIdByName('displayHeader'), 0, 1);

        return $success;
    }

    public function installSQL()
    {
        return include $this->getLocalPath() . '/sql/install.php';
    }

    public function uninstallSQL()
    {
        return include $this->getLocalPath() . '/sql/uninstall.php';
    }

    public function installationDefaults($default_iso_code = 'en')
    {
        $result = true;

        $cookies_installation_defaults = LGCookiesLawCookie::getInstallationDefaults();

        foreach (Shop::getShops(false, null, true) as $id_shop) {
            $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

            foreach (LGCookiesLawPurpose::getInstallationDefaults() as $index => $installation_default) {
                $lgcookieslaw_purpose = new LGCookiesLawPurpose();

                foreach (Language::getLanguages() as $lang) {
                    $name = isset($installation_default['name'][$lang['iso_code']])
                        ? $installation_default['name'][$lang['iso_code']]
                        : $installation_default['name'][$default_iso_code];
                    $description = isset($installation_default['description'][$lang['iso_code']])
                        ? $installation_default['description'][$lang['iso_code']]
                        : $installation_default['description'][$default_iso_code];

                    $lgcookieslaw_purpose->name[(int) $lang['id_lang']] = $name;
                    $lgcookieslaw_purpose->description[(int) $lang['id_lang']] = $description;
                }

                $lgcookieslaw_purpose->id_shop = (int) $id_shop;
                $lgcookieslaw_purpose->technical = (bool) $installation_default['technical'];
                $lgcookieslaw_purpose->consent_mode = (bool) $installation_default['consent_mode'];
                $lgcookieslaw_purpose->consent_type = $installation_default['consent_type'];
                $lgcookieslaw_purpose->locked_modules = self::jsonEncode($installation_default['locked_modules']);
                $lgcookieslaw_purpose->active = (bool) $installation_default['active'];

                $result &= $lgcookieslaw_purpose->save();

                if (isset($cookies_installation_defaults[(int) $index])) {
                    foreach ($cookies_installation_defaults[(int) $index] as $installation_default) {
                        $lgcookieslaw_cookie = new LGCookiesLawCookie();

                        foreach (Language::getLanguages() as $lang) {
                            $cookie_purpose = isset($installation_default['cookie_purpose'][$lang['iso_code']])
                                ? $installation_default['cookie_purpose'][$lang['iso_code']]
                                : $installation_default['cookie_purpose'][$default_iso_code];
                            $expiry_time = isset($installation_default['expiry_time'][$lang['iso_code']])
                                ? $installation_default['expiry_time'][$lang['iso_code']]
                                : $installation_default['expiry_time'][$default_iso_code];

                            $lgcookieslaw_cookie->cookie_purpose[(int) $lang['id_lang']] = $cookie_purpose;
                            $lgcookieslaw_cookie->expiry_time[(int) $lang['id_lang']] = $expiry_time;
                            $lgcookieslaw_cookie->script_code[(int) $lang['id_lang']] = '';
                        }

                        $lgcookieslaw_cookie->id_shop = (int) $id_shop;
                        $lgcookieslaw_cookie->id_lgcookieslaw_purpose = (int) $lgcookieslaw_purpose->id;
                        $lgcookieslaw_cookie->name = $installation_default['name'];
                        $lgcookieslaw_cookie->provider = $installation_default['provider'];
                        $lgcookieslaw_cookie->provider_url = $installation_default['provider_url'];
                        $lgcookieslaw_cookie->install_script = false;
                        $lgcookieslaw_cookie->script_hook = 'header';
                        $lgcookieslaw_cookie->add_script_tag = false;
                        $lgcookieslaw_cookie->add_script_literal = false;
                        $lgcookieslaw_cookie->script_notes = '';
                        $lgcookieslaw_cookie->active = (bool) $installation_default['active'];

                        $result &= $lgcookieslaw_cookie->save();

                        unset($lgcookieslaw_cookie);
                    }
                }

                unset($lgcookieslaw_purpose);
            }

            $result &= $this->saveCss((int) $id_shop, (int) $id_shop_group);
        }

        return $result;
    }

    public function getContent()
    {
        $context = Context::getContext();

        $this->helper_token = Tools::getAdminTokenLite('AdminModules');
        $this->module_url = $context->link->getAdminLink('AdminModules', false) . '&' .
            'configure=' . $this->name . '&' .
            'tab_lg=';

        $iso_code = $context->language->iso_code;

        $lg_help_path = is_dir($this->getLocalPath() . 'views/img/help/' . $iso_code) ?
            $this->_path . 'views/img/help/' . $iso_code :
            $this->_path . 'views/img/help/en';

        $lg_help_url =
            $this->module_url . 'help&token=' . $this->helper_token;

        $publi_class_name = str_replace('Override', '', get_class($this));
        $publi_class_name .= 'Publi' . Tools::strtoupper($this->platform);

        $publi_class_name::setModule($this);
        $publi_class_name::setModules($publi_class_name::$modules);

        $params = [
            'lg_module_dir' => $this->_path,
            'lg_module_name' => $this->name,
            'lg_base_url' => $this->_path,
            'lg_help_path' => $lg_help_path . '/',
            'lg_help_url' => $lg_help_url,
            'lg_iso_code' => $iso_code,
        ];

        $context->smarty->assign($params);

        $body = '';

        if (Shop::isFeatureActive()
            && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)
        ) {
            $errors = [$this->l('You have to select a shop.')];

            $this->displayError($errors);
        } else {
            $params = [
                'lg_menu' => $this->getMenu(),
            ];

            $context->smarty->assign($params);

            $body = $this->postProcess();

            switch ($this->active_tab) {
                case 'settings':
                    $form = $this->renderForm();

                    $extra_params = [
                        'lg_form' => $form,
                    ];

                    $context->smarty->assign($extra_params);

                    $body = $context->smarty->fetch($this->getTemplatePath(
                        'views/templates/admin/view_body.tpl'
                    ));

                    break;

                case 'help':
                    $body = $context->smarty->fetch($this->getTemplatePath(
                        'views/templates/admin/view_help.tpl'
                    ));

                    break;
            }

            if (!in_array($this->active_tab, ['help', 'settings'])) {
                if (empty($this->display)) {
                    if (Tools::isSubmit('add' . $this->list_table) || Tools::isSubmit('update' . $this->list_table)) {
                        $this->display = 'edit';
                    } else {
                        $this->display = 'list';
                    }
                }

                switch ($this->display) {
                    case 'add':
                    case 'edit':
                        $body .= $this->{'renderForm' . Tools::ucfirst(Tools::toCamelCase($this->active_tab))}();

                        break;

                    case '':
                    case 'list':
                        $body .= $this->renderList();

                        break;
                }
            }

            $informations = [];

            if (method_exists($this, 'getInformations')) {
                $informations = $this->getInformations();
            }

            if (!empty($informations)) {
                $this->displayInformation($informations);
            }

            $warnings = [];

            if (method_exists($this, 'getWarnings')) {
                $warnings = $this->getWarnings();
            }

            if (!empty($warnings)) {
                $this->displayWarning($warnings);
            }
        }

        $header = $publi_class_name::renderHeader();
        $footer = $publi_class_name::renderFooter();

        return $header . $body . $footer;
    }

    protected function renderForm()
    {
        $context = Context::getContext();

        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        $consent_mode_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/consent_mode_content.tpl'
        ));

        $important_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/important_content.tpl'
        ));

        $banner_images = [
            1 => $this->_path . 'views/img/en_banner_top.jpg',
            2 => $this->_path . 'views/img/en_banner_bottom.jpg',
            3 => $this->_path . 'views/img/en_banner_float.jpg',
        ];

        $iso_code = $context->language->iso_code;

        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_top.jpg')) {
            $banner_images[1] = $this->_path . 'views/img/' . $iso_code . '_banner_top.jpg';
        }

        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_bottom.jpg')) {
            $banner_images[2] = $this->_path . 'views/img/' . $iso_code . '_banner_bottom.jpg';
        }

        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_float.jpg')) {
            $banner_images[3] = $this->_path . 'views/img/' . $iso_code . '_banner_float.jpg';
        }

        $configuration = self::getModuleConfiguration();

        $lgcookieslaw_disallow_controller_url = $context->link->getModuleLink(
            $this->name,
            'disallow',
            [
                'token' => md5(_COOKIE_KEY_ . $this->name),
            ],
            true
        );

        $formatted_lgcookieslaw_disallow_controller_url = $this->getLinkTag(
            $lgcookieslaw_disallow_controller_url,
            $lgcookieslaw_disallow_controller_url,
            '_blank',
            $this->l('European Union General Data Protection Rules Law')
        );

        $lgcookieslaw_help_content_url = $this->getLinkTag(
            $this->module_url . 'help&token=' . $this->helper_token . '&help_tab=general_settings',
            $this->l('Read this page for more information'),
            '_blank',
            $this->l('Read this page for more information')
        );

        $context->smarty->assign([
            'lgcookieslaw_help_content_module_name' => $this->name,
            'lgcookieslaw_help_content_url' => $lgcookieslaw_help_content_url,
        ]);

        $help1_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_help_content.tpl'
        ));

        $lgcookieslaw_help_content_url = $this->getLinkTag(
            $this->module_url . 'help&token=' . $this->helper_token . '&help_tab=banner_settings',
            $this->l('Read this page for more information'),
            '_blank',
            $this->l('Read this page for more information')
        );

        $context->smarty->assign([
            'lgcookieslaw_help_content_module_name' => $this->name,
            'lgcookieslaw_help_content_url' => $lgcookieslaw_help_content_url,
        ]);

        $help2_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_help_content.tpl'
        ));

        $lgcookieslaw_help_content_url = $this->getLinkTag(
            $this->module_url . 'help&token=' . $this->helper_token . '&help_tab=button_settings',
            $this->l('Read this page for more information'),
            '_blank',
            $this->l('Read this page for more information')
        );

        $context->smarty->assign([
            'lgcookieslaw_help_content_module_name' => $this->name,
            'lgcookieslaw_help_content_url' => $lgcookieslaw_help_content_url,
        ]);

        $help3_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_help_content.tpl'
        ));

        $lgcookieslaw_help_content_url = $this->getLinkTag(
            $this->module_url . 'help&token=' . $this->helper_token . '&help_tab=troubleshooting',
            $this->l('FAQ: See the common errors'),
            '_blank',
            $this->l('FAQ: See the common errors')
        );

        $context->smarty->assign([
            'lgcookieslaw_help_content_module_name' => $this->name,
            'lgcookieslaw_help_content_url' => $lgcookieslaw_help_content_url,
        ]);

        $help5_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_help_content.tpl'
        ));

        $fields_form = [];

        $fields_form[]['form'] = [
            'legend' => [
                'title' => $this->l('General Settings'),
                'icon' => 'icon-cogs',
            ],
            'tabs' => [
                'general_settings' => $this->l('General Settings'),
                'banner_settings' => $this->l('Banner Settings'),
                'button_settings' => $this->l('Button Settings'),
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->l('Hook position'),
                    'name' => 'PS_LGCOOKIES_BANNER_HOOK',
                    'required' => false,
                    'desc' => $this->l('Choose a different hook if you need.') . ' ' .
                            $this->l('Useful for some themes where hook "top" not present.'),
                    'options' => [
                        'query' => [
                            [
                                'id' => 'top',
                                'name' => 'top',
                            ],
                            [
                                'id' => 'footer',
                                'name' => 'footer',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Enable by default third parties cookies'),
                    'name' => 'PS_LGCOOKIES_THIRD_PARTIES',
                    'required' => false,
                    'desc' => $this->l('If this option is enabled, third parties cookies checkbox') . ' ' .
                            $this->l('will be enabled by default.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_THIRD_PARTIES'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Reload the page after accepting cookies'),
                    'name' => 'PS_LGCOOKIES_RELOAD',
                    'required' => false,
                    'desc' => $this->l('Enable this option if you wish reload the page after a customer accepts cookies. Recommended to leave it deactivated.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_RELOAD'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Block site navigation'),
                    'name' => 'PS_LGCOOKIES_BLOCK_NAVIGATION',
                    'required' => false,
                    'desc' => $this->l('Enable this option if you wish to block your site navigation') . ' ' .
                            $this->l('until the customers push the accept button on the banner.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_BLOCK_NAVIGATION'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Show button to close banner'),
                    'name' => 'PS_LGCOOKIES_SHOW_CLOSE_BTN',
                    'required' => false,
                    'desc' => $this->l('Button that allows you to close the banner so as not to interfere with navigation.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_SHOW_CLOSE_BTN'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Reject cookies when closing the banner'),
                    'name' => 'PS_LGCOOKIES_CLOSE_BTN_RJCT_CKS',
                    'required' => false,
                    'desc' => $this->l('Reject all cookies when you close the banner using the close button.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_CLOSE_BTN_RJCT_CKS'),
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_CLOSE_BTN_on',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Show fixed button to open banner'),
                    'name' => 'PS_LGCOOKIES_SHOW_FIXED_BTN',
                    'required' => false,
                    'desc' => $this->l('The button will appear once the user saves the preferences.') .
                            $this->l('Necessary to be able to open the banner and change your preferences') . ' ' .
                            $this->l('without having to delete cookies.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_SHOW_FIXED_BTN'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Fixed button position'),
                    'name' => 'PS_LGCOOKIES_FIXED_BTN_POSITION',
                    'required' => false,
                    'desc' => $this->l('Choose the position of the fixed buttom.'),
                    'options' => [
                        'query' => [
                            [
                                'id' => 'left',
                                'name' => $this->l('Left'),
                            ],
                            [
                                'id' => 'right',
                                'name' => $this->l('Right'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_FIXED_BTN_on',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Cookie icon color'),
                    'name' => 'PS_LGCOOKIES_FIXED_BTN_SVG_COLOR',
                    'required' => false,
                    'desc' => $this->l('Fixed button cookie icon color.'),
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_FIXED_BTN_on',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Add revoke consent button'),
                    'name' => 'PS_LGCOOKIES_DISALLOW',
                    'required' => false,
                    'desc' => $this->l('Enable this option to add a button on customers acount to revoke cookie consent.') .
                            ' ' . $this->l('It will clean all cookies except Prestashop ones.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_DISALLOW'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgcopy',
                    'label' => $this->l('Disallow URL'),
                    'name' => 'disallow_url_content',
                    'value' => $lgcookieslaw_disallow_controller_url,
                    'readonly' => true,
                    'desc' => $formatted_lgcookieslaw_disallow_controller_url .
                            $this->l('This link will grant the right of revoke their consent to your customers.') . ' ' .
                            $this->l('You can paste this url on your CMS.') . ' ' .
                            $this->l('Your users will be able to clean all cookies except Prestashop ones.'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Show the banner in the CMS of the Cookies Policy'),
                    'name' => 'PS_LGCOOKIES_SHOW_BANNER_IN_CMS',
                    'required' => false,
                    'desc' => $this->l('This option indicates whether you want to hide the banner within') . ' ' .
                            $this->l('the selected CMS page to display the Cookies Policy and thus be able') . ' ' .
                            $this->l('to read it without having the banner in front of you.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_SHOW_BANNER_IN_CMS'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Save user consent'),
                    'name' => 'PS_LGCOOKIES_SAVE_USER_CONSENT',
                    'required' => false,
                    'desc' => $this->l('The user\'s consent will be recorded. It can be downloaded in PDF format.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_SAVE_USER_CONSENT'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Anonymize IP'),
                    'name' => 'PS_LGCOOKIES_ANONYMIZE_UC_IP',
                    'required' => false,
                    'desc' => $this->l('If you wish, you can save the IP address incompletely to preserve') . ' ' .
                            $this->l('the user\'s rights.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_ANONYMIZE_UC_IP'),
                    'class' => 'toggle_PS_LGCOOKIES_SAVE_USER_CONSENT_on',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Delete user consent'),
                    'name' => 'PS_LGCOOKIES_DELETE_USER_CONSENT',
                    'required' => false,
                    'desc' => $this->l('Delete expired user consents.') . ' ' .
                            $this->l('It includes deleting the PDF documents associated with the consent.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_DELETE_USER_CONSENT'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Enable Consent Mode'),
                    'name' => 'PS_LGCOOKIES_CONSENT_MODE',
                    'required' => false,
                    'desc' => $this->l('With consent mode, you can adjust how your Google tags behave based') . ' ' .
                            $this->l('on the consent status of users. With this function, you can indicate') . ' ' .
                            $this->l('whether consent has been given to the use of Analytics and Google Ads cookies.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_CONSENT_MODE'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('About consent mode'),
                    'name' => 'consent_mode_content',
                    'html_content' => $consent_mode_content,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Preview mode'),
                    'name' => 'PS_LGCOOKIES_TESTMODE',
                    'required' => false,
                    'desc' => $this->l('Enable this option to preview the cookie banner in your front-office') . ' ' .
                            $this->l('without bothering your customers (when the preview mode is enabled,') . ' ' .
                            $this->l('the banner doesn´t disappear, the module doesn´t block cookies') . ' ' .
                            $this->l('and only the person using the IP below is able to see the cookie banner).'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_TESTMODE'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Important'),
                    'name' => 'important_content',
                    'html_content' => $important_content,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgip',
                    'label' => $this->l('IP for the preview mode'),
                    'name' => 'PS_LGCOOKIES_IPTESTMODE',
                    'required' => false,
                    'desc' => $this->l('Click on the button "Add IP" to be the only person') . ' ' .
                            $this->l('able to see the banner (if the preview mode is enabled).'),
                    'class' => 'toggle_PS_LGCOOKIES_TESTMODE_on',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Cookie lifetime (seconds)'),
                    'name' => 'PS_LGCOOKIES_COOKIE_TIMELIFE',
                    'required' => false,
                    'desc' => $this->l('Set the duration during which the user consent will be saved (1 year = 31536000s).'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Cookie name'),
                    'name' => 'PS_LGCOOKIES_COOKIE_NAME',
                    'required' => false,
                    'desc' => $this->l('Choose the name of the cookie used by our module to remember user consent') . ' ' .
                            $this->l('(don´t use any space).'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Use $_COOKIE var'),
                    'name' => 'PS_LGCOOKIES_USE_COOKIE_VAR',
                    'required' => false,
                    'desc' => $this->l('Use the PHP COOKIE and not the Prestashop one.') . ' ' .
                            $this->l('It is an advanced option, do not modify it if you do not know what you are doing.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_USE_COOKIE_VAR'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Load the Fancybox plugin from this module'),
                    'name' => 'PS_LGCOOKIES_LOAD_FANCYBOX',
                    'required' => false,
                    'desc' => $this->l('Important:') .
                            $this->l('Do not activate this option if the Fancybox plugin is loading') . ' ' .
                            $this->l('from your store correctly.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_LOAD_FANCYBOX'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Enables compatibility with the Page Ultimate Cache or Super Speed module'),
                    'name' => 'PS_LGCOOKIES_PUC_COMPATIBILITY',
                    'required' => false,
                    'desc' => $this->l('Important:') .
                            $this->l('If you do not have this module do not activate this option.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_PUC_COMPATIBILITY'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('SEO protection'),
                    'name' => 'PS_LGCOOKIES_BOTS',
                    'required' => false,
                    'cols' => '10',
                    'rows' => '5',
                    'desc' => $this->l('The module will prevent the search engine bots above') . ' ' .
                            $this->l('from seeing the cookie warning banner when they crawl your website.'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help1',
                    'html_content' => $help1_content,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help5',
                    'html_content' => $help5_content,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Banner position'),
                    'name' => 'PS_LGCOOKIES_BANNER_POSITION',
                    'required' => false,
                    'desc' => $this->l('Choose the position of the warning banner.'),
                    'options' => [
                        'query' => [
                            [
                                'id' => '1',
                                'name' => $this->l('Top'),
                            ],
                            [
                                'id' => '2',
                                'name' => $this->l('Bottom'),
                            ],
                            [
                                'id' => '3',
                                'name' => $this->l('Floating / Centered'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'lgbanner',
                    'name' => 'banner_type_content',
                    'selected' => (int) $configuration['PS_LGCOOKIES_BANNER_POSITION'],
                    'change_element' => 'PS_LGCOOKIES_BANNER_POSITION',
                    'images' => $banner_images,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Background color'),
                    'name' => 'PS_LGCOOKIES_BANNER_BG_COLOR',
                    'required' => false,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Background opacity'),
                    'name' => 'PS_LGCOOKIES_BANNER_BG_OPACITY',
                    'required' => false,
                    'desc' => $this->l('Choose the opacity of the background color (1 is opaque, 0 is transparent).'),
                    'options' => [
                        'query' => [
                            [
                                'id' => '1',
                                'name' => '1',
                            ],
                            [
                                'id' => '0.9',
                                'name' => '0.9',
                            ],
                            [
                                'id' => '0.8',
                                'name' => '0.8',
                            ],
                            [
                                'id' => '0.7',
                                'name' => '0.7',
                            ],
                            [
                                'id' => '0.6',
                                'name' => '0.6',
                            ],
                            [
                                'id' => '0.5',
                                'name' => '0.5',
                            ],
                            [
                                'id' => '0.4',
                                'name' => '0.4',
                            ],
                            [
                                'id' => '0.3',
                                'name' => '0.3',
                            ],
                            [
                                'id' => '0.2',
                                'name' => '0.2',
                            ],
                            [
                                'id' => '0.1',
                                'name' => '0.1',
                            ],
                            [
                                'id' => '0',
                                'name' => '0',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Shadow color'),
                    'name' => 'PS_LGCOOKIES_BANNER_SHADOWCOLOR',
                    'required' => false,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Font color'),
                    'name' => 'PS_LGCOOKIES_BANNER_FONTCOLOR',
                    'required' => false,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Banner message'),
                    'name' => 'PS_LGCOOKIES_BANNER_MESSAGE',
                    'autoload_rte' => 'true',
                    'lang' => 'true',
                    'required' => false,
                    'cols' => '10',
                    'rows' => '5',
                    'desc' => $this->l('Example: "Our webstore uses cookies to offer a better user experience') . ' ' .
                            $this->l('and we recommend you to accept their use to fully enjoy your navigation."'),
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help2',
                    'html_content' => $help2_content,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help5',
                    'html_content' => $help5_content,
                    'tab' => 'banner_settings',
                ],
                [
                    'type' => 'text',
                    'lang' => 'true',
                    'label' => $this->l('"Accept" button title'),
                    'name' => 'PS_LGCOOKIES_ACPT_BTN_TITLE',
                    'required' => false,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('"Accept" button background color'),
                    'name' => 'PS_LGCOOKIES_ACPT_BTN_BG_COLOR',
                    'required' => false,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('"Accept" button font color'),
                    'name' => 'PS_LGCOOKIES_ACPT_BTN_FONT_COLOR',
                    'required' => false,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'text',
                    'lang' => 'true',
                    'label' => $this->l('"Cookie policy" link title'),
                    'name' => 'PS_LGCOOKIES_INFO_LINK_TITLE',
                    'required' => false,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('CMS URL of "Cookie policy" link'),
                    'name' => 'PS_LGCOOKIES_INFO_LINK_ID_CMS',
                    'required' => false,
                    'desc' => $this->l('When you click on the "Cookie policy" link,') . ' ' .
                            $this->l('it will take you to CMS page you have selected.') .
                            $this->l('Select the CMS page that contains the texts of the Cookies Policy.'),
                    'options' => [
                        'query' => CMS::getCMSPages((int) $id_lang, null, true, (int) $id_shop),
                        'id' => 'id_cms',
                        'name' => 'meta_title',
                    ],
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('"Cookie policy" link target'),
                    'name' => 'PS_LGCOOKIES_INFO_LINK_TARGET',
                    'required' => false,
                    'desc' => $this->l('When you click on the "Cookie policy" link,') . ' ' .
                            $this->l('the CMS page will be opened in a new or the same window of your browser.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_INFO_LINK_TARGET'),
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Show "Reject" button'),
                    'name' => 'PS_LGCOOKIES_SHOW_RJCT_BTN',
                    'required' => false,
                    'desc' => $this->l('Enable this option to Show the Reject button.'),
                    'is_bool' => true,
                    'values' => $this->printDefaultSwitchValues('PS_LGCOOKIES_SHOW_RJCT_BTN'),
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'text',
                    'lang' => 'true',
                    'label' => $this->l('"Reject" button title'),
                    'name' => 'PS_LGCOOKIES_RJCT_BTN_TITLE',
                    'required' => false,
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_RJCT_BTN_on',
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('"Reject" button background color'),
                    'name' => 'PS_LGCOOKIES_RJCT_BTN_BG_COLOR',
                    'required' => false,
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_RJCT_BTN_on',
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('"Reject" button font color'),
                    'name' => 'PS_LGCOOKIES_RJCT_BTN_FONT_COLOR',
                    'required' => false,
                    'class' => 'toggle_PS_LGCOOKIES_SHOW_RJCT_BTN_on',
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help3',
                    'html_content' => $help3_content,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'html',
                    'name' => 'help5',
                    'html_content' => $help5_content,
                    'tab' => 'button_settings',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'PS_LGCOOKIES_NOTICED_MODULES',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;
        $helper->languages = $this->module_languages;

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit' . $this->name;

        foreach ($this->configurations_list as $configuration_name => $configuration) {
            if (isset($configuration['add_field_value']) && $configuration['add_field_value']) {
                if (is_array($configuration['default_value'])) {
                    foreach ($this->module_languages as $language) {
                        $helper->fields_value[$configuration_name][(int) $language['id_lang']] =
                            Configuration::get(
                                $configuration_name,
                                (int) $language['id_lang'],
                                (int) $id_shop_group,
                                (int) $id_shop
                            );
                    }
                } else {
                    $helper->fields_value[$configuration_name] = Configuration::get(
                        $configuration_name,
                        null,
                        (int) $id_shop_group,
                        (int) $id_shop
                    );
                }
            }
        }

        $helper->tpl_vars = [
            'ps_version' => $this->ps_version,
        ];

        if ($this->ps_version == '15'
            && isset($fields_form[0]['form']['tabs'])
            && !empty($fields_form[0]['form']['tabs'])
        ) {
            $helper->tpl_vars['tabs'] = $fields_form[0]['form']['tabs'];
        }

        return $helper->generateForm($fields_form);
    }

    public function renderFormPurposes()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }

        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $configuration = self::getModuleConfiguration();

        $consent_mode_alert = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/consent_mode_alert.tpl'
        ));

        $purpose_locked_modules = !empty($object->locked_modules) ?
            self::jsonDecode($object->locked_modules) : [];

        $context->smarty->assign([
            'lgcookieslaw_field_name' => 'locked_modules',
            'lgcookieslaw_purpose_locked_modules' => $purpose_locked_modules,
            'lgcookieslaw_module_list' => $this->getModuleList(),
        ]);

        $locked_modules_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_lgcookieslaw_purpose_locked_modules.tpl'
        ));

        $list_cookies_content = $this->renderListPurposeCookies();

        $this->active_tab = 'purposes';

        $this->setHelperListPurposesVars();

        if (Validate::isLoadedObject($object)) {
            $context->smarty->assign([
                'lgcookieslaw_id_lgcookieslaw_purpose' => (int) $object->id,
            ]);
        }

        $manual_lock_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_lgcookieslaw_purpose_manual_lock.tpl'
        ));

        $lgcookieslaw_help_content_url = $this->getLinkTag(
            $this->module_url . 'help&token=' . $this->helper_token . '&help_tab=purposes',
            $this->l('Purposes: Consent types'),
            '_blank',
            $this->l('Purposes: Consent types')
        );

        $context->smarty->assign([
            'lgcookieslaw_help_content_module_name' => $this->name,
            'lgcookieslaw_help_content_url' => $lgcookieslaw_help_content_url,
        ]);

        $help1_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_help_content.tpl'
        ));

        $this->fields_form = [];

        $this->fields_form[]['form'] = [
            'legend' => [
                'title' => $this->l('Purposes'),
                'icon' => 'icon-list-ul',
            ],
            'tabs' => [
                'general_settings' => $this->l('General Settings'),
                'advanced_settings' => $this->l('Advanced Settings'),
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => LGCookiesLawPurpose::$definition['primary'],
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_shop',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'desc' => $this->l('Select "Yes" if you want active this purpose.'),
                    'values' => $this->printDefaultSwitchValues(),
                    'is_bool' => true,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'desc' => $this->l('Name of the purpose, it will be displayed on the front') . ' ' .
                            $this->l('to group the set of cookies.'),
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'lang' => true,
                    'col' => 8,
                    'required' => true,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'desc' => $this->l('Description of the purpose, it will be displayed on the front.'),
                    'lang' => true,
                    'col' => 8,
                    'class' => 'lgtextarea',
                    'required' => true,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Technical'),
                    'name' => 'technical',
                    'is_bool' => true,
                    'desc' => $this->l('Select "Yes" if you want this purpose to be mandatory.'),
                    'values' => $this->printDefaultSwitchValues('technical'),
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'html',
                    'label' => $this->l('Locked Modules'),
                    'name' => 'locked_modules',
                    'html_content' => $locked_modules_content,
                    'tab' => 'general_settings',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $additional_inputs = [];

        if (Validate::isLoadedObject($object)) {
            $additional_inputs[] = [
                'type' => 'html',
                'label' => $this->l('Cookies'),
                'name' => 'cookies',
                'desc' => $this->l('Cookies associated with this purpose. You can modify or delete them.'),
                'html_content' => $list_cookies_content,
                'tab' => 'general_settings',
            ];
        }

        $additional_inputs[] = [
            'type' => 'html',
            'label' => $this->l('Manual Lock'),
            'name' => 'manual_lock',
            'html_content' => $manual_lock_content,
            'tab' => 'general_settings',
        ];

        if (!$configuration['PS_LGCOOKIES_CONSENT_MODE']) {
            $additional_inputs[] = [
                'type' => 'html',
                'label' => $this->l('Important'),
                'name' => 'consent_mode_alert',
                'html_content' => $consent_mode_alert,
                'tab' => 'advanced_settings',
            ];
        }

        $additional_inputs[] = [
            'type' => 'lgswitch',
            'label' => $this->l('For Consent Mode'),
            'name' => 'consent_mode',
            'is_bool' => true,
            'desc' => $this->l('Select "Yes" if you want this purpose to add some kind of allowed consent.'),
            'values' => $this->printDefaultSwitchValues('consent_mode'),
            'disabled' => !$configuration['PS_LGCOOKIES_CONSENT_MODE'],
            'tab' => 'advanced_settings',
        ];

        $additional_inputs[] = [
            'type' => 'select',
            'label' => $this->l('Consent Type'),
            'name' => 'consent_type',
            'desc' => $this->l('Select one of the consent types. We strongly recommend that you review') .
                        $this->l('the documentation.') . $help1_content,
            'options' => [
                'query' => [
                    [
                        'id' => 'functionality_storage',
                        'name' => $this->l('functionality_storage'),
                    ],
                    [
                        'id' => 'ad_storage',
                        'name' => $this->l('ad_storage'),
                    ],
                    [
                        'id' => 'analytics_storage',
                        'name' => $this->l('analytics_storage'),
                    ],
                ],
                'id' => 'id',
                'name' => 'name',
            ],
            'disabled' => !$configuration['PS_LGCOOKIES_CONSENT_MODE'],
            'class' => 'toggle_consent_mode_on',
            'tab' => 'advanced_settings',
        ];

        $this->fields_form[0]['form']['input'] = array_merge(
            $this->fields_form[0]['form']['input'],
            $additional_inputs
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = $this->helper_token;
        $helper->currentIndex = self::$currentIndex;

        // Language
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submitAdd' . LGCookiesLawPurpose::$definition['table'];
        $helper->show_cancel_button = true;

        $fields_value = $this->getFieldsValue($object);

        if (isset($fields_value['id_shop']) && !$fields_value['id_shop']) {
            $fields_value['id_shop'] = (int) $id_shop;
        }

        $helper->tpl_vars = [
            'ps_version' => $this->ps_version,
            'fields_value' => $fields_value,
            'languages' => $this->module_languages,
        ];

        if ($this->ps_version == '15'
            && isset($this->fields_form[0]['form']['tabs'])
            && !empty($this->fields_form[0]['form']['tabs'])
        ) {
            $helper->tpl_vars['tabs'] = $this->fields_form[0]['form']['tabs'];
        }

        return $helper->generateForm($this->fields_form);
    }

    public function renderFormCookies()
    {
        if (!($object = $this->loadObject(true))) {
            return;
        }

        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $this->fields_form = [];

        $this->fields_form[]['form'] = [
            'legend' => [
                'title' => $this->l('Cookie'),
                'icon' => 'icon-gears',
            ],
            'tabs' => [
                'general_settings' => $this->l('General Settings'),
                'advanced_settings' => $this->l('Advanced Settings'),
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => LGCookiesLawCookie::$definition['primary'],
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_shop',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Active'),
                    'name' => 'active',
                    'desc' => $this->l('Select "Yes" if you want active this cookie.'),
                    'values' => $this->printDefaultSwitchValues(),
                    'is_bool' => true,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'desc' => $this->l('Name of the cookie, it will be displayed on the front.'),
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'required' => true,
                    'col' => 6,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Purpose'),
                    'name' => 'id_lgcookieslaw_purpose',
                    'desc' => $this->l('Important:') .
                            $this->l('This cookie will be displayed grouped within this purpose.'),
                    'options' => [
                        'query' => LGCookiesLawPurpose::getPurposes(),
                        'id' => LGCookiesLawPurpose::$definition['primary'],
                        'name' => 'name',
                    ],
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Provider'),
                    'name' => 'provider',
                    'desc' => $this->l('Domain associated with the cookie.'),
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'col' => 6,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Provider URL'),
                    'name' => 'provider_url',
                    'hint' => $this->l('Invalid characters:') . ' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:',
                    'col' => 6,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Cookie Purpose'),
                    'name' => 'cookie_purpose',
                    'desc' => $this->l('The purpose of the cookie in the store.'),
                    'lang' => true,
                    'col' => 8,
                    'class' => 'lgtextarea',
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Expiry Time'),
                    'name' => 'expiry_time',
                    'desc' => $this->l('Time the cookie remains installed once it is accepted.'),
                    'lang' => true,
                    'col' => 8,
                    'tab' => 'general_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Install Script'),
                    'name' => 'install_script',
                    'desc' => $this->l('Select "Yes" if you can add a script that installs a cookie.') . ' ' .
                                $this->l('For example the Google Analytics code.'),
                    'values' => $this->printDefaultSwitchValues('install_script'),
                    'is_bool' => true,
                    'tab' => 'advanced_settings',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Script Hook'),
                    'name' => 'script_hook',
                    'desc' => $this->l('Script position in store.'),
                    'options' => [
                        'query' => [
                            [
                                'id' => 'header',
                                'name' => $this->l('Header (header)'),
                            ],
                            [
                                'id' => 'displayAfterBodyOpeningTag',
                                'name' => $this->l('Top of the page (displayAfterBodyOpeningTag)'),
                            ],
                            [
                                'id' => 'displayBeforeBodyClosingTag',
                                'name' => $this->l('Bottom of the page (displayBeforeBodyClosingTag)'),
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'class' => 'toggle_install_script_on',
                    'tab' => 'advanced_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Script Code'),
                    'name' => 'script_code',
                    'desc' => $this->l('You can use HTML, JS or Smarty code.'),
                    'lang' => true,
                    'col' => 8,
                    'class' => 'lgtextarea toggle_install_script_on',
                    'tab' => 'advanced_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Add Script Tag'),
                    'name' => 'add_script_tag',
                    'desc' => $this->l('Place your code between script HTML tags.'),
                    'values' => $this->printDefaultSwitchValues('add_script_tag'),
                    'class' => 'toggle_install_script_on',
                    'is_bool' => true,
                    'tab' => 'advanced_settings',
                ],
                [
                    'type' => 'lgswitch',
                    'label' => $this->l('Add Script Literal'),
                    'name' => 'add_script_literal',
                    'desc' => $this->l('If the code already has the tags SCRIPT or it is not a SMARTY code') . ', ' .
                                $this->l('you must check this option so that it adds the code literally to the chosen area.') .
                                $this->l('Smarty code will not be interpreted.'),
                    'values' => $this->printDefaultSwitchValues('add_script_literal'),
                    'class' => 'toggle_install_script_on',
                    'is_bool' => true,
                    'tab' => 'advanced_settings',
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Script Notes'),
                    'name' => 'script_notes',
                    'desc' => $this->l('You can leave a note regarding the script to be installed,') . ' ' .
                                $this->l('it will only be visible from this section as a reminder.'),
                    'col' => 6,
                    'class' => 'lgtextarea toggle_install_script_on',
                    'tab' => 'advanced_settings',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = $this->helper_token;
        $helper->currentIndex = self::$currentIndex;

        // Language
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submitAdd' . LGCookiesLawCookie::$definition['table'];
        $helper->show_cancel_button = true;

        $fields_value = $this->getFieldsValue($object);

        if (isset($fields_value['id_shop']) && !$fields_value['id_shop']) {
            $fields_value['id_shop'] = (int) $id_shop;
        }

        $helper->tpl_vars = [
            'ps_version' => $this->ps_version,
            'fields_value' => $fields_value,
            'languages' => $this->module_languages,
        ];

        if ($this->ps_version == '15'
            && isset($this->fields_form[0]['form']['tabs'])
            && !empty($this->fields_form[0]['form']['tabs'])
        ) {
            $helper->tpl_vars['tabs'] = $this->fields_form[0]['form']['tabs'];
        }

        return $helper->generateForm($this->fields_form);
    }

    /**
     * Return the list of fields value.
     *
     * @param ObjectModel $obj Object
     *
     * @return array
     */
    public function getFieldsValue($obj)
    {
        foreach ($this->fields_form as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    if (!isset($this->fields_value[$input['name']])) {
                        if (isset($input['type']) && $input['type'] == 'shop') {
                            if ($obj->id) {
                                $result = Shop::getShopById((int) $obj->id, $this->identifier, $this->table);

                                foreach ($result as $row) {
                                    $this->fields_value['shop'][$row['id_' . $input['type']]][] = $row['id_shop'];
                                }
                            }
                        } elseif (isset($input['lang']) && $input['lang']) {
                            foreach ($this->module_languages as $language) {
                                $field_value = $this->getFieldValue($obj, $input['name'], $language['id_lang']);

                                if (empty($field_value)) {
                                    if (isset($input['default_value']) && is_array($input['default_value'])
                                        && isset($input['default_value'][$language['id_lang']])
                                    ) {
                                        $field_value = $input['default_value'][$language['id_lang']];
                                    } elseif (isset($input['default_value'])) {
                                        $field_value = $input['default_value'];
                                    }
                                }

                                $this->fields_value[$input['name']][$language['id_lang']] = $field_value;
                            }
                        } else {
                            $field_value = $this->getFieldValue($obj, $input['name']);

                            if ($field_value === false && isset($input['default_value'])) {
                                $field_value = $input['default_value'];
                            }

                            $this->fields_value[$input['name']] = $field_value;
                        }
                    }
                }
            }
        }

        return $this->fields_value;
    }

    /**
     * Return field value if possible (both classical and multilingual fields).
     *
     * Case 1 : Return value if present in $_POST / $_GET
     * Case 2 : Return object value
     *
     * @param ObjectModel $obj Object
     * @param string $key Field name
     * @param int|null $id_lang Language id (optional)
     *
     * @return string
     */
    public function getFieldValue($obj, $key, $id_lang = null)
    {
        if ($id_lang) {
            $default_value = (isset($obj->id) && $obj->id && isset($obj->{$key}[$id_lang])) ?
                $obj->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($obj->{$key}) ? $obj->{$key} : false;
        }

        return Tools::getValue($key . ($id_lang ? '_' . $id_lang : ''), $default_value);
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        $context = Context::getContext();

        $cookie = $context->cookie;

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        $configuration = Configuration::getMultiple(
            [
                'PS_BO_ALLOW_EMPLOYEE_FORM_LANG',
                'PS_LANG_DEFAULT',
            ],
            null,
            (int) $id_shop_group,
            (int) $id_shop
        );

        $this->allow_employee_form_lang = (int) $configuration['PS_BO_ALLOW_EMPLOYEE_FORM_LANG'];

        if ($this->allow_employee_form_lang && !$cookie->employee_form_lang) {
            $cookie->employee_form_lang = (int) $configuration['PS_LANG_DEFAULT'];
        }

        $lang_exists = false;

        $this->module_languages = Language::getLanguages(false);

        foreach ($this->module_languages as $lang) {
            if (isset($cookie->employee_form_lang) && $cookie->employee_form_lang == $lang['id_lang']) {
                $lang_exists = true;
            }
        }

        $this->default_form_language = $lang_exists ?
            (int) $cookie->employee_form_lang : (int) $configuration['PS_LANG_DEFAULT'];

        foreach ($this->module_languages as $k => $language) {
            $this->module_languages[$k]['is_default'] =
                (int) ($language['id_lang'] == $this->default_form_language);
        }

        return $this->module_languages;
    }

    protected function getMenu()
    {
        $this->active_tab = Tools::getValue('tab_lg', $this->default_tab);

        $this->tabs_menu = [
            [
                'label' => $this->l('Module Settings'),
                'link' => $this->module_url . 'settings&token=' . $this->helper_token,
                'active' => ($this->active_tab == 'settings' ? 1 : 0),
            ],
            [
                'label' => $this->l('Purposes'),
                'link' => $this->module_url . 'purposes&token=' . $this->helper_token,
                'active' => ($this->active_tab == 'purposes' ? 1 : 0),
            ],
            [
                'label' => $this->l('Cookies'),
                'link' => $this->module_url . 'cookies&token=' . $this->helper_token,
                'active' => ($this->active_tab == 'cookies' ? 1 : 0),
            ],
            [
                'label' => $this->l('User Consents'),
                'link' => $this->module_url . 'userConsents&token=' . $this->helper_token,
                'active' => ($this->active_tab == 'userConsents' ? 1 : 0),
            ],
            [
                'label' => $this->l('Help'),
                'link' => $this->module_url . 'help&token=' . $this->helper_token,
                'active' => ($this->active_tab == 'help' ? 1 : 0),
            ],
        ];

        return $this->tabs_menu;
    }

    public function postProcessPurposes()
    {
        $technical = Tools::getValue('technical', false);

        if ($technical) {
            $_POST['locked_modules'] = self::jsonEncode([]);
        } else {
            $locked_modules = Tools::getValue('locked_modules', []);

            if (!empty($locked_modules)) {
                $_POST['locked_modules'] = self::jsonEncode($locked_modules);
            } else {
                $_POST['locked_modules'] = self::jsonEncode([]);
            }
        }

        $consent_mode = Tools::getValue('consent_mode', false);

        if (!$consent_mode) {
            $_POST['consent_type'] = null;
        }

        return true;
    }

    public function postProcess()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        if (!empty($this->active_tab)
            && !in_array($this->active_tab, ['help', 'settings'])
        ) {
            if (method_exists(
                $this,
                'setHelperList' . Tools::ucfirst(Tools::toCamelCase($this->active_tab)) . 'Vars'
            )) {
                $this->{'setHelperList' . Tools::ucfirst(Tools::toCamelCase($this->active_tab)) . 'Vars'}();
            }
        }

        $this->id_object = (int) Tools::getValue($this->list_identifier);

        foreach ($this->bulk_actions as $bulk_action => $params) {
            if (Tools::isSubmit('submitBulk' . $bulk_action . $this->list_table)
                || Tools::isSubmit('submitBulk' . $bulk_action)
            ) {
                if ($bulk_action === 'delete') {
                    $this->boxes = Tools::getValue($this->list_table . 'Box');

                    $log_action = sprintf($this->l('ACTION: Bulk delete %s!'), $this->active_tab);

                    self::writeLog(self::generateLogCSVLineFields($log_action));

                    return $this->processBulkDelete();
                } elseif ($bulk_action === 'enableSelection') {
                    $this->boxes = Tools::getValue($this->list_table . 'Box');

                    $log_action = sprintf($this->l('ACTION: Enable selection %s!'), $this->active_tab);

                    self::writeLog(self::generateLogCSVLineFields($log_action));

                    return $this->processBulkEnableSelection();
                } elseif ($bulk_action === 'disableSelection') {
                    $this->boxes = Tools::getValue($this->list_table . 'Box');

                    $log_action = sprintf($this->l('ACTION: Disable selection %s!'), $this->active_tab);

                    self::writeLog(self::generateLogCSVLineFields($log_action));

                    return $this->processBulkDisableSelection();
                } elseif ($bulk_action === 'download') {
                    $this->boxes = Tools::getValue($this->list_table . 'Box');

                    return $this->processBulkDownload();
                }
            }
        }

        if (Tools::isSubmit('submitFilter' . $this->list_id)
            || $context->cookie->{'submitFilter' . $this->list_id} !== false
            || Tools::getValue($this->list_id . 'Orderby')
            || Tools::getValue($this->list_id . 'Orderway')
        ) {
            $this->processFilter();
        }

        if (Tools::isSubmit('submitAdd' . $this->list_table)
            || Tools::isSubmit('submitAdd' . $this->list_table . 'AndStay')
        ) {
            $continue = true;

            if (method_exists(
                $this,
                'postProcess' . Tools::ucfirst(Tools::toCamelCase($this->active_tab))
            )) {
                $continue = $this->{'postProcess' . Tools::ucfirst(Tools::toCamelCase($this->active_tab))}();
            }

            $log_action = sprintf($this->l('ACTION: Save %s!'), $this->active_tab);

            self::writeLog(self::generateLogCSVLineFields($log_action));

            return $continue ? $this->processSave() : false;
        }

        if (Tools::isSubmit('export' . $this->list_table)) {
            return $this->processExport();
        }

        if (Tools::isSubmit('delete' . $this->list_table)) {
            $log_action = sprintf($this->l('ACTION: Delete %s!'), $this->active_tab);

            self::writeLog(self::generateLogCSVLineFields($log_action));

            return $this->processDelete();
        }

        if ((Tools::isSubmit('status' . $this->list_table) || Tools::isSubmit('status'))
            && Tools::getValue($this->list_identifier)) {
            $log_action = sprintf($this->l('ACTION: Change status %s!'), $this->active_tab);

            self::writeLog(self::generateLogCSVLineFields($log_action));

            return $this->processStatus();
        }

        if (Tools::isSubmit('position')) {
            $log_action = sprintf($this->l('ACTION: Change position %s!'), $this->active_tab);

            self::writeLog(self::generateLogCSVLineFields($log_action));

            return $this->processPosition();
        }

        if (Tools::isSubmit('submit' . $this->name)) {
            $this->module_errors = [];

            foreach ($this->configurations_list as $configuration_name => $configuration) {
                if (strpos($configuration_name, 'COLOR') !== false) {
                    $value = Tools::getValue($configuration_name);

                    $_POST[$configuration_name] = Tools::substr($value, 0, 1) == '#' ?
                        $value : '#' . $value;
                }
            }

            foreach ($this->configurations_list as $configuration_name => $configuration) {
                if (isset($configuration['auto_proccess']) && $configuration['auto_proccess']) {
                    if (is_array($configuration['default_value'])) {
                        foreach (Language::getLanguages(false) as $language) {
                            if (!Tools::getValue($configuration_name . '_' . (int) $language['id_lang'])
                                && isset($configuration['warning_text'])
                            ) {
                                $this->module_errors[] = $configuration['warning_text'];
                            }
                        }
                    } elseif (!Tools::getValue($configuration_name) && isset($configuration['warning_text'])) {
                        $this->module_errors[] = $configuration['warning_text'];
                    }
                }
            }

            if (!count($this->module_errors)) {
                $success = true;

                foreach ($this->configurations_list as $configuration_name => $configuration) {
                    if (isset($configuration['auto_proccess']) && $configuration['auto_proccess']) {
                        if (is_array($configuration['default_value'])) {
                            $values = [];

                            foreach (Language::getLanguages(false) as $language) {
                                $values[(int) $language['id_lang']] =
                                    Tools::getValue($configuration_name . '_' . (int) $language['id_lang']);
                            }

                            $success &= Configuration::updateValue(
                                $configuration_name,
                                $values,
                                $configuration['html'],
                                (int) $id_shop_group,
                                (int) $id_shop
                            );
                        } else {
                            $success &= Configuration::updateValue(
                                $configuration_name,
                                Tools::getValue($configuration_name),
                                $configuration['html'],
                                (int) $id_shop_group,
                                (int) $id_shop
                            );
                        }
                    }
                }

                $success &= $this->saveCss();

                self::writeLog(self::generateLogCSVLineFields($this->l('ACTION: Update settings!')));

                return $success ?
                    $this->displayConfirmation($this->l('Updated configuration.')) :
                    $this->displayError($this->l('An error occurred while saving the configuration.'));
            } else {
                return $this->displayError($this->module_errors);
            }
        }

        if (Tools::isSubmit('submitReset' . $this->list_id)) {
            return $this->processResetFilters($this->list_id);
        }
    }

    public function getInformations()
    {
        $informations = [];

        if (!empty($this->information_letters)) {
            foreach ($this->information_letters as $information_letter) {
                if (method_exists($this, 'information' . Tools::strtoupper($information_letter))) {
                    $information = $this->{'information' . Tools::strtoupper($information_letter)}();

                    if (!empty($information)) {
                        $informations[] = $information;
                    }
                }
            }
        }

        return $informations;
    }

    protected function informationA()
    {
        $information =
            sprintf($this->l('The "%s" module is a tool that allows you to comply'), $this->displayName) . ' ' .
            $this->l('with the European directive in your store.') .
            $this->l('You need to configure the module based on the elements installed in your store') . ' ' .
            $this->l('for proper compliance with the law, and check that no cookie is used') . ' ' .
            $this->l('without the permission of its visitors.') .
            $this->l('This can block any installed module that installs cookies on the client machine') . ' ' .
            $this->l('(if configured correctly), but you cannot prevent third-party scripts') . ' ' .
            $this->l('included directly in your theme that make use of cookies without the consent of the client.');

        return $information;
    }

    protected function informationB()
    {
        $information = '';

        if (isset($this->active_tab) && $this->active_tab == 'cookies') {
            $information .=
                $this->l('You must check which cookies your store uses.') . ' ' .
                $this->l('If you use any of the cookies already created in this list, you must verify') . ' ' .
                $this->l('the configuration and enable it. Otherwise, if it is not created yet,') . ' ' .
                $this->l('you just have to add it.');
        }

        return $information;
    }

    public function getWarnings()
    {
        $warnings = [];

        if (!empty($this->warning_letters)) {
            foreach ($this->warning_letters as $warning_letter) {
                if (method_exists($this, 'warning' . Tools::strtoupper($warning_letter))) {
                    $warning = $this->{'warning' . Tools::strtoupper($warning_letter)}();

                    if (!empty($warning)) {
                        $warnings[] = $warning;
                    }
                }
            }
        }

        return $warnings;
    }

    protected function warningA()
    {
        $warning =
            $this->l('All responsibility for compliance with the law rests with the site administrator.') .
            $this->l('It is highly recommended that you read the help section of the module') . ' ' .
            $this->l('for its correct configuration, and that you check that all the cookies installed') . ' ' .
            $this->l('in your store have been correctly classified and blocked.');

        return $warning;
    }

    protected function warningB()
    {
        $warning = '';

        if (!file_exists(_PS_ROOT_DIR_ . '/override/classes/Hook.php')) {
            $warning =
                $this->l('The Hook.php override is missing.') . ' ' .
                $this->l('Please reset the module or copy the override manually on your FTP.');
        }

        return $warning;
    }

    protected function warningC()
    {
        $warning = '';

        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ((bool) $configuration['PS_DISABLE_OVERRIDES']) {
            $token = Tools::getAdminTokenLite('AdminPerformance');

            $link = $this->getLinkTag(
                'index.php?tab=AdminPerformance&token=' . $token,
                $this->l('here.'),
                '_blank'
            );

            $warning =
                $this->l('The overrides are currently disabled on your store.') . ' ' .
                $this->l('Please change the configuration') . ' ' .
                $link;
        }

        return $warning;
    }

    protected function warningD()
    {
        $warning = '';

        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ((bool) $configuration['PS_DISABLE_NON_NATIVE_MODULE']) {
            $token = Tools::getAdminTokenLite('AdminPerformance');

            $link = $this->getLinkTag(
                'index.php?tab=AdminPerformance&token=' . $token,
                $this->l('here.'),
                '_blank'
            );

            $warning =
                $this->l('Non PrestaShop modules are currently disabled on your store.') . ' ' .
                $this->l('Please change the configuration') . ' ' .
                $link;
        }

        return $warning;
    }

    protected function warningE()
    {
        $warning = '';

        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ((bool) $configuration['PS_LGCOOKIES_TESTMODE']) {
            $warning =
                $this->l('The preview mode of the module is enabled.') . ' ' .
                $this->l('Don\'t forget to disable it once you have finished configuring the banner.');
        }

        return $warning;
    }

    protected function warningF()
    {
        $warning = '';

        $context = Context::getContext();

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        $generated_css_file_name = 'lgcookieslaw_' . (int) $id_shop_group . '_' . (int) $id_shop . '.css';
        $generated_css_file_path = _PS_MODULE_DIR_ . $this->name . '/views/css/' . $generated_css_file_name;

        if (!file_exists($generated_css_file_path)) {
            $warning =
                sprintf($this->l('The file %s has not been generated for this store.'), $generated_css_file_name);
        }

        return $warning;
    }

    protected function warningG()
    {
        $warning = '';

        if (!Module::isEnabled($this->name)) {
            $warning = $this->l('The module is not enabled in this store.');
        }

        return $warning;
    }

    protected function warningH()
    {
        $warning = '';

        if (file_exists(_LGCOOKIESLAW_USER_CONSENT_DOWNLOAD_DIR_)) {
            $warning = $this->l('We have detected the /download directory in your module.') . ' ' .
                $this->l('We recommend that you delete this directory as it has been deprecated since version 2.1.0.');
        }

        return $warning;
    }

    protected function warningI()
    {
        $warning = '';

        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $is_installed_a_jpresta_module_cache =
            Module::isInstalled('jprestaspeedpack') || Module::isInstalled('pagecache');

        if ($is_installed_a_jpresta_module_cache
            && !(bool) $configuration['PS_LGCOOKIES_PUC_COMPATIBILITY']
        ) {
            $warning =
                $this->l('You have the Ultimate Page Cache or Super Speed module installed, we recommend that you') . ' ' .
                $this->l('activate the option "Enables compatibility with the Page Ultimate Cache or Super Speed module"') . ' ' .
                $this->l('to make it compatible with our module.');
        }

        return $warning;
    }

    protected function isBot($agent)
    {
        $configuration = self::getModuleConfiguration();

        $bot_list = explode(',', $configuration['PS_LGCOOKIES_BOTS']);

        $result = false;

        foreach ($bot_list as $bot) {
            if (strpos($agent, $bot) !== false) {
                $result = true;

                break;
            }
        }

        return $result;
    }

    protected function getModuleList()
    {
        $query = new DbQuery();

        $query->select('m.`id_module`, m.`name`');
        $query->from('module', 'm');
        $query->where('m.`name` <> "lgcookieslaw"');

        $module_list = Db::getInstance()->executeS($query);

        foreach ($module_list as $key => &$module) {
            $module['display_name'] = Module::getModuleName($module['name']);

            if ((int) $module['id_module'] === 0) {
                unset($module_list[$key]);
            }

            if ($module['name'] === $this->name) {
                unset($module_list[$key]);
            }
        }

        unset($module);

        usort($module_list, static function ($a, $b) {
            return strnatcasecmp($a['display_name'], $b['display_name']);
        });

        return $module_list;
    }

    public function setHelperListPurposesVars()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $class_name = 'LGCookiesLawPurpose';

        $this->className = $class_name;
        $this->list_table = $class_name::$definition['table'];
        $this->list_id = $this->list_table;
        $this->list_identifier = $class_name::$definition['primary'];

        $this->lang = true;

        $this->actions = ['edit', 'delete'];
        $this->bulk_actions = [
            'enableSelection' => [
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ],
            'disableSelection' => [
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ],
        ];

        if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
            $this->bulk_actions['divider'] = [
                'text' => 'divider',
            ];
        }

        $this->bulk_actions['delete'] = [
            'text' => $this->l('Delete selected'),
            'confirm' => $this->l('Delete selected items?'),
            'icon' => 'icon-trash',
        ];

        $this->list_where = 'AND a.`id_shop` = ' . (int) $id_shop;

        $this->list_defaultOrderBy = 'a.' . $this->list_identifier;
        $this->list_orderBy = $this->list_defaultOrderBy;
        $this->list_orderWay = $this->list_defaultOrderWay;

        $this->fields_list = [
            $class_name::$definition['primary'] => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'align' => 'center',
                'filter_key' => 'b!name',
            ],
            'description' => [
                'title' => $this->l('Description'),
                'align' => 'center',
                'filter_key' => 'b!description',
            ],
            'technical' => [
                'title' => $this->l('Technicals'),
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printTechnical',
                'callback_object' => $this,
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ],
        ];

        $this->shopLinkType = !Shop::isFeatureActive() ? '' : 'shop';

        if ($this->multishop_context == -1) {
            $this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;
        }

        self::$currentIndex = $this->module_url . (!empty($this->active_tab) ?
            $this->active_tab : $this->default_tab);

        $this->helper_title = $this->l('Purposes');
        $this->helper_title_icon = 'icon-list-ul';

        $this->show_toolbar = true;
        $this->toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->list_table . '&token=' . $this->helper_token,
            'desc' => $this->l('Add new'),
        ];
        $this->toolbar_btn['export'] = [
            'href' => self::$currentIndex . '&export' . $this->list_table . '&token=' . $this->helper_token,
            'desc' => $this->l('Export'),
        ];
    }

    public function setHelperListCookiesVars()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $class_name = 'LGCookiesLawCookie';

        $this->className = $class_name;
        $this->list_table = $class_name::$definition['table'];
        $this->list_id = $this->list_table;
        $this->list_identifier = $class_name::$definition['primary'];

        $this->lang = true;

        $this->actions = ['edit', 'delete'];
        $this->bulk_actions = [
            'enableSelection' => [
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ],
            'disableSelection' => [
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ],
        ];

        if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
            $this->bulk_actions['divider'] = [
                'text' => 'divider',
            ];
        }

        $this->bulk_actions['delete'] = [
            'text' => $this->l('Delete selected'),
            'confirm' => $this->l('Delete selected items?'),
            'icon' => 'icon-trash',
        ];

        $this->list_defaultOrderBy = 'a.' . $this->list_identifier;
        $this->list_orderBy = $this->list_defaultOrderBy;
        $this->list_orderWay = $this->list_defaultOrderWay;

        $this->list_where = 'AND a.`id_shop` = ' . (int) $id_shop;

        $lgcookieslaw_purposes_list = [];

        $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes();

        foreach ($lgcookieslaw_purposes as $lgcookieslaw_purpose) {
            $lgcookieslaw_purposes_list[(int) $lgcookieslaw_purpose['id_lgcookieslaw_purpose']] =
                $lgcookieslaw_purpose['name'];
        }

        $this->fields_list = [
            $class_name::$definition['primary'] => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->l('Name'),
                'align' => 'center',
                'filter_key' => 'a!name',
            ],
            'id_lgcookieslaw_purpose' => [
                'title' => $this->l('Purpose'),
                'callback' => 'printPurposeName',
                'callback_object' => $this,
                'type' => 'select',
                'list' => $lgcookieslaw_purposes_list,
                'filter_key' => 'a!id_lgcookieslaw_purpose',
            ],
            'provider' => [
                'title' => $this->l('Provider'),
                'align' => 'center',
            ],
            'expiry_time' => [
                'title' => $this->l('Expiry Time'),
                'align' => 'center',
                'filter_key' => 'b!expiry_time',
            ],
            'install_script' => [
                'title' => $this->l('Install Script'),
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printInstallScript',
                'callback_object' => $this,
                'orderby' => false,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
            ],
        ];

        $this->shopLinkType = !Shop::isFeatureActive() ? '' : 'shop';

        if ($this->multishop_context == -1) {
            $this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;
        }

        self::$currentIndex = $this->module_url . (!empty($this->active_tab) ?
            $this->active_tab : $this->default_tab);

        $this->helper_title = $this->l('Cookies');
        $this->helper_title_icon = 'icon-gears';

        $this->show_toolbar = true;
        $this->toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->list_table . '&token=' . $this->helper_token,
            'desc' => $this->l('Add new'),
        ];
        $this->toolbar_btn['export'] = [
            'href' => self::$currentIndex . '&export' . $this->list_table . '&token=' . $this->helper_token,
            'desc' => $this->l('Export'),
        ];
    }

    public function setHelperListUserConsentsVars()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $class_name = 'LGCookiesLawUserConsent';

        $this->className = $class_name;
        $this->list_table = $class_name::$definition['table'];
        $this->list_id = $this->list_table;
        $this->list_identifier = $class_name::$definition['primary'];

        $this->actions = ['delete'];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
            'divider' => [
                'text' => 'divider',
            ],
            'download' => [
                'text' => $this->l('Download PDF selection'),
                'icon' => 'icon-download',
            ],
        ];

        $this->list_defaultOrderBy = 'a.' . $this->list_identifier;
        $this->list_orderBy = $this->list_defaultOrderBy;
        $this->list_orderWay = $this->list_defaultOrderWay;

        $this->list_select = '1 AS `download_pdf`, `purposes` AS `consent_status`';
        $this->list_where = 'AND a.`id_shop` = ' . (int) $id_shop;

        $this->fields_list = [
            $class_name::$definition['primary'] => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'download_hash' => [
                'title' => $this->l('Download Hash'),
                'align' => 'center',
                'filter_key' => 'a!download_hash',
            ],
            'ip_address' => [
                'title' => $this->l('IP Address'),
                'align' => 'center',
                'filter_key' => 'a!ip_address',
            ],
            'consent_date' => [
                'title' => $this->l('Consent Date'),
                'align' => 'center',
                'filter_key' => 'a!consent_date',
            ],
            'consent_status' => [
                'title' => $this->l('Consent Status'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'showConsentStatus',
                'callback_object' => $this,
                'orderby' => false,
                'search' => false,
            ],
            'download_pdf' => [
                'title' => $this->l('Download PDF'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'callback' => 'printDownloadPDF',
                'callback_object' => $this,
                'orderby' => false,
                'search' => false,
            ],
        ];

        $this->shopLinkType = !Shop::isFeatureActive() ? '' : 'shop';

        if ($this->multishop_context == -1) {
            $this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP | Shop::CONTEXT_SHOP;
        }

        self::$currentIndex = $this->module_url . (!empty($this->active_tab) ?
            $this->active_tab : $this->default_tab);

        $this->helper_title = $this->l('User Consents');
        $this->helper_title_icon = 'icon-file-text';

        $this->no_link = true;
    }

    protected function processBulkDownload()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $result = true;

        if (is_array($this->boxes) && !empty($this->boxes)) {
            $user_consent_file_paths = [];
            $user_consent_to_delete_file_paths = [];

            foreach ($this->boxes as $id) {
                $object = new $this->className((int) $id);

                if (isset($object->purposes) && !empty($object->purposes)) {
                    $user_consent_file_name = $object->download_hash . '%00.jpg.pdf';
                    $user_consent_file_path = sys_get_temp_dir() . '/' . $user_consent_file_name;

                    $user_consent_file_paths[] = $user_consent_file_path;

                    if (!file_exists($user_consent_file_path)) {
                        $user_consent_to_delete_file_paths[] = $user_consent_file_path;

                        $template_vars = [];

                        $template_vars['template_vars']['lgcookieslaw_user_consent'] = $object;
                        $template_vars['template_vars']['lgcookieslaw_purposes'] =
                            self::jsonDecode($object->purposes);

                        $pdf = new PDF(
                            $template_vars,
                            'LGCookiesLawUserConsent',
                            $context->smarty
                        );

                        $pdf_renderer = $pdf->render(false);

                        file_put_contents($user_consent_file_path, $pdf_renderer);
                    }
                }
            }

            if (!empty($user_consent_file_paths)) {
                $zip_name = 'user_consent_files_' . date('d_m_Y_G_i_s') . '.zip';

                $zip_file = new ZipArchive();

                $zip_file->open($zip_name, ZipArchive::CREATE);

                foreach ($user_consent_file_paths as $user_consent_file_path) {
                    $zip_file->addFromString(
                        basename($user_consent_file_path),
                        Tools::file_get_contents($user_consent_file_path)
                    );
                }

                $zip_file->close();

                if (ob_get_level() && ob_get_length() > 0) {
                    ob_end_clean();
                }

                header('Content-Transfer-Encoding: binary');
                header('Content-Type: application/zip');
                header('Content-Length: ' . filesize($zip_name));
                header('Content-Disposition: attachment; filename="' .
                    mb_convert_encoding($zip_name, 'ISO-8859-1', 'UTF-8') . '"');

                @set_time_limit(0);

                $this->readfileChunked($zip_name);

                foreach ($user_consent_to_delete_file_paths as $user_consent_to_delete_file_path) {
                    unlink($user_consent_to_delete_file_path);
                }
            }
        }
    }

    /**
     * @see   http://ca2.php.net/manual/en/function.readfile.php#54295
     */
    public function readfileChunked($file_name, $return_bytes = true)
    {
        $chunk_size = 1 * (1024 * 1024);
        $buffer = '';
        $total_bytes = 0;

        $handle = fopen($file_name, 'rb');

        if ($handle === false) {
            return false;
        }

        while (!feof($handle)) {
            $buffer = fread($handle, $chunk_size);

            echo $buffer;

            ob_flush();
            flush();

            if ($return_bytes) {
                $total_bytes += Tools::strlen($buffer);
            }
        }

        $status = fclose($handle);

        if ($return_bytes && $status) {
            return $total_bytes;
        }

        return $status;
    }

    public function renderListPurposeCookies()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $class_name = 'LGCookiesLawCookie';

        $this->className = $class_name;
        $this->list_table = $class_name::$definition['table'];
        $this->list_id = $this->list_table;
        $this->list_identifier = $class_name::$definition['primary'];

        $this->lang = true;

        $this->actions = ['edit', 'delete'];
        $this->bulk_actions = [];

        $this->list_defaultOrderBy = 'a.' . $this->list_identifier;
        $this->list_orderBy = $this->list_defaultOrderBy;
        $this->list_orderWay = $this->list_defaultOrderWay;

        $this->list_where =
            'AND a.`id_shop` = ' . (int) $id_shop . ' ' .
            'AND a.`' . LGCookiesLawPurpose::$definition['primary'] . '` = ' .
            (int) Tools::getValue(LGCookiesLawPurpose::$definition['primary']);

        $this->fields_list = [
            $class_name::$definition['primary'] => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'align' => 'center',
                'orderby' => false,
                'search' => false,
            ],
            'provider' => [
                'title' => $this->l('Provider'),
                'align' => 'center',
                'orderby' => false,
                'search' => false,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'orderby' => false,
                'search' => false,
            ],
        ];

        $this->shopLinkType = '';

        $this->active_tab = 'cookies';

        self::$currentIndex = $this->module_url . (!empty($this->active_tab) ?
            $this->active_tab : $this->default_tab);

        $this->helper_title = $this->l('Cookies');
        $this->helper_title_icon = 'icon-gears';

        $this->show_toolbar = true;
        $this->toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->list_table . '&token=' . $this->helper_token,
            'desc' => $this->l('Add new'),
        ];

        return $this->renderList();
    }

    public function printTechnical($value)
    {
        $context = Context::getContext();

        $context->smarty->assign([
            'lgcookieslaw_technical' => $value,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/list/list_lgcookieslaw_purpose_technical.tpl'
        ));

        return $content;
    }

    public function printPurposeName($value)
    {
        $context = Context::getContext();

        $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes();

        $lgcookieslaw_purpose = array_filter($lgcookieslaw_purposes, function ($lgcookieslaw_purpose) use ($value) {
            if ((int) $lgcookieslaw_purpose['id_lgcookieslaw_purpose'] == (int) $value) {
                return true;
            }

            return false;
        });

        $lgcookieslaw_purpose = array_pop($lgcookieslaw_purpose);

        $context->smarty->assign([
            'gcookieslaw_purpose_name' => $lgcookieslaw_purpose['name'],
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/list/list_lgcookieslaw_cookie_purpose_name.tpl'
        ));

        return $content;
    }

    public function printInstallScript($value)
    {
        $context = Context::getContext();

        $context->smarty->assign([
            'lgcookieslaw_install_script' => $value,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/list/list_lgcookieslaw_cookie_install_script.tpl'
        ));

        return $content;
    }

    public function printDownloadPDF($value, $object)
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ($configuration['PS_LGCOOKIES_SAVE_USER_CONSENT']
            && !empty($object['purposes'])
        ) {
            $lgcookieslaw_user_consent_download_url = $context->link->getModuleLink(
                $this->name,
                'download',
                [
                    'id_shop' => (int) $object['id_shop'],
                    'download_hash' => $object['download_hash'],
                ],
                true
            );

            $context->smarty->assign([
                'lgcookieslaw_user_consent_download_url' => $lgcookieslaw_user_consent_download_url,
            ]);
        }

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/list/list_lgcookieslaw_user_consent_download_url.tpl'
        ));

        return $content;
    }

    public function showConsentStatus($value, $object)
    {
        $context = Context::getContext();
        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $user_consent = self::jsonDecode($object['purposes']);
        $count_consent = 0;
        foreach ($user_consent as $consent) {
            if ($consent->c == 1) {
                $count_consent++;
            }
        }

        $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop, true);
        $activated_purposes = 0;
        foreach ($lgcookieslaw_purposes as &$lgcookieslaw_purpose) {
            if ($lgcookieslaw_purpose['active'] == 1) {
                $activated_purposes++;
            }
        }

        if ($count_consent == 1) {
            $consent_result = 'Rejected';
        } elseif ($count_consent == $activated_purposes) {
            $consent_result = 'Accepted';
        } else {
            $consent_result = 'Parcially Accepted';
        }

        $context->smarty->assign([
            'consent_result' => $consent_result,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/list/list_lgcookieslaw_user_consent_status.tpl'
        ));

        return $content;
    }

    public function renderList()
    {
        $context = Context::getContext();

        $id_lang = $context->language->id;

        $this->getList((int) $id_lang);

        $helper = new HelperList();

        $helper->table = $this->list_table;
        $helper->shopLinkType = $this->shopLinkType;
        $helper->identifier = $this->list_identifier;
        $helper->_defaultOrderBy = $this->list_defaultOrderBy;
        $helper->orderBy = $this->list_orderBy;
        $helper->orderWay = $this->list_orderWay;
        $helper->listTotal = $this->list_listTotal;
        $helper->actions = $this->actions;
        $helper->show_toolbar = $this->show_toolbar;
        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->bulk_actions = $this->bulk_actions;
        $helper->title = $this->helper_title;
        $helper->title_icon = $this->helper_title_icon;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->helper_token;
        $helper->imageType = $this->imageType;
        $helper->no_link = $this->no_link;
        $helper->module = $this;
        $helper->position_identifier = $this->position_identifier;
        $helper->position_group_identifier = $this->position_group_identifier;

        if (Tools::version_compare(_PS_VERSION_, '1.6.0.11', '>=')) {
            $helper->_default_pagination = $this->list_default_pagination;
        }

        return $helper->generateList($this->list_list, $this->fields_list);
    }

    /**
     * Load class object using identifier in $_GET (if possible)
     * otherwise return an empty object, or die.
     *
     * @param bool $opt Return an empty object if load fail
     *
     * @return ObjectModel|false
     */
    protected function loadObject($opt = false)
    {
        if (!isset($this->className) || empty($this->className)) {
            return true;
        }

        $id = (int) Tools::getValue($this->list_identifier);

        if ($id && Validate::isUnsignedId($id)) {
            if (!$this->object) {
                $this->object = new $this->className($id);
            }

            if (Validate::isLoadedObject($this->object)) {
                return $this->object;
            }

            // throw exception
            $this->module_errors[] = $this->l('The object cannot be loaded (or found)');

            return false;
        } elseif ($opt) {
            if (!$this->object) {
                $this->object = new $this->className();
            }

            return $this->object;
        } else {
            $this->module_errors[] = $this->l('The object cannot be loaded (the identifier is missing or invalid)');

            return false;
        }
    }

    /**
     * Get the current objects' list form the database.
     *
     * @param int $id_lang Language used for display
     * @param string|null $order_by ORDER BY clause
     * @param string|null $order_way Order way (ASC, DESC)
     * @param int $start Offset in LIMIT clause
     * @param int|null $limit Row count in LIMIT clause
     * @param int|bool $id_lang_shop
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->list_table;
        }

        if (!Validate::isTableOrIdentifier($this->list_table)) {
            throw new PrestaShopException(sprintf($this->l('Table name %s is invalid:'), $this->list_table));
        }

        /* Check params validity */
        if (!is_numeric($start) || !Validate::isUnsignedId($id_lang)) {
            throw new PrestaShopException($this->l('get list params is not valid'));
        }

        $limit = $this->checkSqlLimit($limit);
        $start = $this->checkSqlStart($start, $limit);

        // Add SQL shop restriction
        $select_shop = '';

        if ($this->shopLinkType) {
            $select_shop = ', shop.name as shop_name ';
        }

        if ($this->multishop_context && Shop::isTableAssociated($this->list_table) && !empty($this->className)) {
            if (Shop::getContext() != Shop::CONTEXT_ALL || !$this->context->employee->isSuperAdmin()) {
                $test_join = !preg_match(
                    '#`?' . preg_quote(_DB_PREFIX_ . $this->list_table . '_shop') . '`? *sa#',
                    $this->list_join
                );

                if (Shop::isFeatureActive() && $test_join && Shop::isTableAssociated($this->list_table)) {
                    $this->list_where .= ' AND EXISTS (
                        SELECT 1
                        FROM `' . _DB_PREFIX_ . $this->list_table . '_shop` sa
                        WHERE a.`' . bqSQL($this->list_identifier) . '` = sa.`' . bqSQL($this->list_identifier) . '`
                         AND sa.id_shop IN (' . implode(', ', Shop::getContextListShopID()) . ')
                    )';
                }
            }
        }

        $fromClause = $this->getFromClause();
        $joinClause = $this->getJoinClause($id_lang, $id_lang_shop);
        $whereClause = $this->getWhereClause();
        $orderByClause = $this->getOrderByClause($order_by, $order_way);

        $shouldLimitSqlResults = $this->shouldLimitSqlResults($limit);

        do {
            $this->list_listsql = '';

            if ($this->explicitSelect) {
                foreach ($this->fields_list as $key => $array_value) {
                    if (isset($this->list_select)
                        && preg_match('/[\s]`?' . preg_quote($key, '/') . '`?\s*,/', $this->list_select)
                    ) {
                        continue;
                    }

                    if (isset($array_value['filter_key'])) {
                        $this->list_listsql .= str_replace('!', '.`', $array_value['filter_key']) .
                            '` AS `' . $key . '`, ';
                    } elseif ($key == 'id_' . $this->list_table) {
                        $this->list_listsql .= 'a.`' . bqSQL($key) . '`, ';
                    } elseif ($key != 'image' && !preg_match('/' . preg_quote($key, '/') . '/i', $this->list_select)) {
                        $this->list_listsql .= '`' . bqSQL($key) . '`, ';
                    }
                }
                $this->list_listsql = rtrim(trim($this->list_listsql), ',');
            } else {
                $this->list_listsql .= ($this->lang ? 'b.*,' : '') . ' a.*';
            }

            $this->list_listsql .=
                "\n" . (isset($this->list_select) ? ', ' . rtrim($this->list_select, ', ') : '') . $select_shop;

            $limitClause = ' ' . (($shouldLimitSqlResults) ? ' LIMIT ' . (int) $start . ', ' . (int) $limit : '');

            if ($this->list_use_found_rows || isset($this->list_filterHaving) || isset($this->list_having)) {
                $this->list_listsql =
                    'SELECT SQL_CALC_FOUND_ROWS ' . ($this->list_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                        $this->list_listsql .
                        $fromClause .
                        $joinClause .
                        $whereClause .
                        $orderByClause .
                        $limitClause;

                $list_count = 'SELECT FOUND_ROWS() AS `' . _DB_PREFIX_ . $this->list_table . '`';
            } else {
                $this->list_listsql = 'SELECT ' . ($this->list_tmpTableFilter ? ' * FROM (SELECT ' : '') .
                    $this->list_listsql .
                    $fromClause .
                    $joinClause .
                    $whereClause .
                    $orderByClause .
                    $limitClause;

                $list_count = 'SELECT COUNT(*) AS `' . _DB_PREFIX_ . $this->list_table . '` ' .
                    $fromClause .
                    $joinClause .
                    $whereClause;
            }

            $this->list_list = Db::getInstance()->executeS($this->list_listsql, true, false);

            if ($this->list_list === false) {
                $this->list_list_error = Db::getInstance()->getMsgError();

                break;
            }

            $this->list_listTotal = Db::getInstance()->getValue($list_count, false);

            if ($shouldLimitSqlResults) {
                $start = (int) $start - (int) $limit;

                if ($start < 0) {
                    break;
                }
            } else {
                break;
            }
        } while (empty($this->list_list));
    }

    /**
     * @return string
     */
    protected function getFromClause()
    {
        return "\n" . 'FROM `' . _DB_PREFIX_ . $this->list_table . '` a ';
    }

    /**
     * @param $id_lang
     * @param $id_lang_shop
     *
     * @return string
     */
    protected function getJoinClause($id_lang, $id_lang_shop)
    {
        $shopJoinClause = '';

        if ($this->shopLinkType) {
            $shopJoinClause = ' LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->shopLinkType) . '` shop
                ON a.`id_' . bqSQL($this->shopLinkType) . '` = shop.`id_' . bqSQL($this->shopLinkType) . '`';
        }

        return "\n" . $this->getLanguageJoinClause($id_lang, $id_lang_shop) .
            "\n" . (isset($this->list_join) ? $this->list_join . ' ' : '') .
            "\n" . $shopJoinClause;
    }

    /**
     * @param $id_lang
     * @param $id_lang_shop
     *
     * @return string
     */
    protected function getLanguageJoinClause($id_lang, $id_lang_shop)
    {
        $languageJoinClause = '';

        if ($this->lang) {
            $languageJoinClause = 'LEFT JOIN `' . _DB_PREFIX_ . bqSQL($this->list_table) . '_lang` b
                ON (b.`' . bqSQL($this->list_identifier) . '` = a.`' . bqSQL($this->list_identifier) .
                '` AND b.`id_lang` = ' . (int) $id_lang;

            if ($id_lang_shop) {
                if (!Shop::isFeatureActive()) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) Configuration::get('PS_SHOP_DEFAULT');
                } elseif (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    $languageJoinClause .= ' AND b.`id_shop` = ' . (int) $id_lang_shop;
                } else {
                    $languageJoinClause .= ' AND b.`id_shop` = a.id_shop_default';
                }
            }

            $languageJoinClause .= ')';
        }

        return $languageJoinClause;
    }

    /**
     * @return string
     */
    protected function getWhereClause()
    {
        $whereShop = '';

        if ($this->shopLinkType) {
            $whereShop = Shop::addSqlRestriction($this->shopShareDatas, 'a', $this->shopLinkType);
        }

        $whereClause = ' WHERE 1 ' . (isset($this->list_where) ? $this->list_where . ' ' : '') .
            ($this->deleted ? 'AND a.`deleted` = 0 ' : '') .
            (isset($this->list_filter) ? $this->list_filter : '') . $whereShop . "\n" .
            (isset($this->list_group) ? $this->list_group . ' ' : '') . "\n" .
            $this->getHavingClause();

        return $whereClause;
    }

    /**
     * @return string
     */
    protected function getHavingClause()
    {
        $havingClause = '';

        if (isset($this->list_filterHaving) || isset($this->list_having)) {
            $havingClause = ' HAVING ';

            if (isset($this->list_filterHaving)) {
                $havingClause .= ltrim($this->list_filterHaving, ' AND ');
            }

            if (isset($this->list_having)) {
                $havingClause .= $this->list_having . ' ';
            }
        }

        return $havingClause;
    }

    /**
     * @param $orderBy
     * @param $orderDirection
     *
     * @return string
     */
    protected function getOrderByClause($orderBy, $orderDirection)
    {
        $this->list_orderBy = $this->checkOrderBy($orderBy);
        $this->list_orderWay = $this->checkOrderDirection($orderDirection);

        return ' ORDER BY ' . ((str_replace('`', '', $this->list_orderBy) == $this->list_identifier) ? 'a.' : '') .
            $this->list_orderBy . ' ' . $this->list_orderWay .
            ($this->list_tmpTableFilter ? ') tmpTable WHERE 1' . $this->list_tmpTableFilter : '');
    }

    /**
     * Change object status (active, inactive).
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if (!$object->toggleStatus()) {
                $this->module_errors[] = $this->l('An error occurred while updating the status.');
            }
        } else {
            $this->module_errors[] = $this->l('An error occurred while updating the status for an object. ') .
                $this->list_table . $this->l(' (cannot load object)');
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Status changed successfully.'));
    }

    /**
     * Change object position.
     *
     * @return ObjectModel|false
     */
    public function processPosition()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->module_errors[] = $this->l('An error occurred while updating the status for an object. ') .
            $this->list_table . $this->l(' (cannot load object)');
        } elseif (!$object->updatePosition((int) Tools::getValue('way'), (int) Tools::getValue('position'))) {
            $this->module_errors[] = $this->l('Failed to update the position.');
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Position changed successfully.'));
    }

    /**
     * Enable multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    /**
     * Disable multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    /**
     * Toggle status of multiple items.
     *
     * @param bool $status
     *
     * @return bool true if success
     *
     * @throws PrestaShopException
     */
    protected function processBulkStatusSelection($status)
    {
        $result = true;

        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                /** @var ObjectModel $object */
                $object = new $this->className((int) $id);

                $object->active = (int) $status;

                $result &= $object->update();
            }
        }
    }

    /* HelperList Filters */
    public function processResetFilters($list_id = null)
    {
        if ($list_id === null) {
            $list_id = isset($this->list_id) ? $this->list_id : $this->list_table;
        }

        $prefix = $this->getCookieOrderByPrefix();

        $filters = $this->context->cookie->getFamily($prefix . $list_id . 'Filter_');

        foreach ($filters as $cookie_key => $filter) {
            if (strncmp($cookie_key, $prefix . $list_id . 'Filter_', 7 + Tools::strlen($prefix . $list_id)) == 0) {
                $key = Tools::substr($cookie_key, 7 + Tools::strlen($prefix . $list_id));

                if (is_array($this->fields_list) && array_key_exists($key, $this->fields_list)) {
                    $this->context->cookie->$cookie_key = null;
                }

                unset($this->context->cookie->$cookie_key);
            }
        }

        if (isset($this->context->cookie->{'submitFilter' . $list_id})) {
            unset($this->context->cookie->{'submitFilter' . $list_id});
        }

        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderby'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderby'});
        }

        if (isset($this->context->cookie->{$prefix . $list_id . 'Orderway'})) {
            unset($this->context->cookie->{$prefix . $list_id . 'Orderway'});
        }

        $_POST = [];

        $this->list_filter = false;

        unset(
            $this->list_filterHaving,
            $this->list_having
        );
    }

    /* HelperList Filters */
    public function processFilter()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->list_table;
        }

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }

                if (stripos($key, $this->list_id . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->list_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->list_id . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->list_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');

        $definition = false;

        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        $sql_filter = '';

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null
                && !strncmp($key, $prefix . $this->list_id . 'Filter_', 7 + Tools::strlen($prefix . $this->list_id))
            ) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->list_id));

                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ?
                        $field['filter_type'] :
                        (array_key_exists('type', $field) ? $field['type'] : false));

                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = self::jsonDecode($value, true);
                    }

                    $key = isset($tmp_tab[1]) ?
                        $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' :
                        '`' . $tmp_tab[0] . '`';

                    // Assignment by reference
                    if (array_key_exists('tmpTableFilter', $field)) {
                        $sql_filter = &$this->list_tmpTableFilter;
                    } elseif (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->list_filterHaving;
                    } else {
                        $sql_filter = &$this->list_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->module_errors[] = $this->l('The \'From\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .=
                                    ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->module_errors[] =
                                    $this->l('The \'To\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .=
                                    ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->list_identifier || $key == '`' . $this->list_identifier . '`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') .
                                pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = (float) str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . pSQL(trim($value)) . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') .
                                pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * @return mixed
     */
    protected function getCookieOrderByPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }

    /**
     * Set the filters used for the list display.
     */
    protected function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }

    /* HelperList Filters */
    protected function filterToField($key, $filter)
    {
        if (!isset($this->fields_list)) {
            return false;
        }

        foreach ($this->fields_list as $field) {
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key) {
                return $field;
            }
        }

        if (array_key_exists($filter, $this->fields_list)) {
            return $this->fields_list[$filter];
        }

        return false;
    }

    /**
     * @param $orderBy
     *
     * @return false|string
     */
    protected function checkOrderBy($orderBy)
    {
        if (empty($orderBy)) {
            $prefix = $this->getCookieFilterPrefix();

            if ($this->context->cookie->{$prefix . $this->list_id . 'Orderby'}) {
                $orderBy = $this->context->cookie->{$prefix . $this->list_id . 'Orderby'};
            } elseif ($this->list_orderBy) {
                $orderBy = $this->list_orderBy;
            } else {
                $orderBy = $this->list_defaultOrderBy;
            }
        }

        /* Check params validity */
        if (!Validate::isOrderBy($orderBy)) {
            throw new PrestaShopException($this->l('Invalid "order by" clause.'));
        }

        if (!isset($this->fields_list[$orderBy]['order_key'])
            && isset($this->fields_list[$orderBy]['filter_key'])
        ) {
            $this->fields_list[$orderBy]['order_key'] = $this->fields_list[$orderBy]['filter_key'];
        }

        if (isset($this->fields_list[$orderBy]['order_key'])) {
            $orderBy = $this->fields_list[$orderBy]['order_key'];
        }

        if (preg_match('/[.!]/', $orderBy)) {
            $orderBySplit = preg_split('/[.!]/', $orderBy);
            $orderBy = bqSQL($orderBySplit[0]) . '.`' . bqSQL($orderBySplit[1]) . '`';
        } elseif ($orderBy) {
            $orderBy = bqSQL($orderBy);
        }

        return $orderBy;
    }

    /**
     * @param $orderDirection
     *
     * @return string
     */
    protected function checkOrderDirection($orderDirection)
    {
        $prefix = $this->getCookieOrderByPrefix();

        if (empty($orderDirection)) {
            if ($this->context->cookie->{$prefix . $this->list_id . 'Orderway'}) {
                $orderDirection = $this->context->cookie->{$prefix . $this->list_id . 'Orderway'};
            } elseif ($this->list_orderWay) {
                $orderDirection = $this->list_orderWay;
            } else {
                $orderDirection = $this->list_defaultOrderWay;
            }
        }

        if (!Validate::isOrderWay($orderDirection)) {
            throw new PrestaShopException($this->l('Invalid order direction.'));
        }

        return pSQL(Tools::strtoupper($orderDirection));
    }

    /**
     * @param $limit
     *
     * @return int
     */
    protected function checkSqlLimit($limit)
    {
        if (empty($limit)) {
            if (isset($this->context->cookie->{$this->list_id . '_pagination'})
                && $this->context->cookie->{$this->list_id . '_pagination'}
            ) {
                $limit = $this->context->cookie->{$this->list_id . '_pagination'};
            } else {
                $limit = $this->list_default_pagination;
            }
        }

        $limit = (int) Tools::getValue($this->list_id . '_pagination', $limit);

        if (in_array($limit, $this->list_pagination) && $limit != $this->list_default_pagination) {
            $this->context->cookie->{$this->list_id . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->list_id . '_pagination'});
        }

        if (!is_numeric($limit)) {
            throw new PrestaShopException($this->l('Invalid limit. It should be a numeric.'));
        }

        return $limit;
    }

    /**
     * @param $start
     * @param $limit
     *
     * @return int
     */
    protected function checkSqlStart($start, $limit)
    {
        if ((int) Tools::getValue('submitFilter' . $this->list_id)) {
            $start = ((int) Tools::getValue('submitFilter' . $this->list_id) - 1) * $limit;
        } elseif (empty($start)
            && isset($this->context->cookie->{$this->list_id . '_start'})
            && Tools::isSubmit('export' . $this->table)
        ) {
            $start = $this->context->cookie->{$this->list_id . '_start'};
        }

        // Either save or reset the offset in the cookie
        if ($start) {
            $this->context->cookie->{$this->list_id . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->list_id . '_start'})) {
            unset($this->context->cookie->{$this->list_id . '_start'});
        }

        return $start;
    }

    /**
     * @param $limit
     *
     * @return bool
     */
    protected function shouldLimitSqlResults($limit)
    {
        return $limit !== false;
    }

    /**
     * Delete multiple items.
     *
     * @return bool true if success
     */
    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $object = new $this->className();

            if (isset($object->noZeroObject)) {
                $objects_count = count(call_user_func([$this->className, $object->noZeroObject]));

                // Check if all object will be deleted
                if ($objects_count <= 1 || count($this->boxes) == $objects_count) {
                    $this->module_errors[] =
                        $this->l('You need at least one object. ') . $this->list_table .
                        $this->l(' You cannot delete all of the items.');
                }
            } else {
                $result = true;

                foreach ($this->boxes as $id) {
                    /** @var $to_delete ObjectModel */
                    $to_delete = new $this->className((int) $id);

                    $delete_ok = true;

                    if ($this->deleted) {
                        $to_delete->deleted = 1;

                        if (!$to_delete->update()) {
                            $result = false;
                            $delete_ok = false;
                        }
                    } elseif (!$to_delete->delete()) {
                        $result = false;
                        $delete_ok = false;
                    }

                    if ($delete_ok) {
                        if (class_exists('PrestaShopLogger')) {
                            PrestaShopLogger::addLog(
                                sprintf($this->l('%s deletion'), $this->className),
                                1,
                                null,
                                $this->className,
                                (int) $to_delete->id,
                                true,
                                (int) $this->context->employee->id
                            );
                        }
                    } else {
                        $this->module_errors[] = sprintf($this->l('Can\'t delete #%s'), (int) $id);
                    }
                }

                if (!$result) {
                    $this->module_errors[] = $this->l('An error occurred while deleting this selection.');
                }
            }
        } else {
            $this->module_errors[] = $this->l('You must select at least one element to delete.');
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Selection deleted successfully.'));
    }

    /**
     * @param string $text_delimiter
     * @throws PrestaShopException
     */
    public function processExport($text_delimiter = '"')
    {
        $context = Context::getContext();

        $id_lang = $context->language->id;

        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        $this->getList((int) $id_lang, null, null, 0, false);

        if (!count($this->list_list)) {
            return;
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->list_table . '_' . date('Y-m-d_His') . '.csv"');

        $fd = fopen('php://output', 'wb');

        $headers = [];

        foreach ($this->fields_list as $key => $datas) {
            if ('PDF' === $datas['title']) {
                unset($this->fields_list[$key]);
            } else {
                $headers[] = 'ID' === $datas['title'] ?
                    Tools::strtolower(Tools::htmlentitiesDecodeUTF8($datas['title'])) :
                    Tools::htmlentitiesDecodeUTF8($datas['title']);
            }
        }

        fputcsv($fd, $headers, ';', $text_delimiter);

        foreach ($this->list_list as $row) {
            $content = [];
            $path_to_image = false;

            foreach ($this->fields_list as $key => $params) {
                $field_value = isset($row[$key]) ? Tools::htmlentitiesDecodeUTF8(Tools::nl2br($row[$key])) : '';

                if ($key == 'image') {
                    $path_to_image = Tools::getShopDomain(true) . _PS_IMG_ . $params['image'] . '/';

                    if ($params['image'] != 'p' || Configuration::get('PS_LEGACY_IMAGES')) {
                        $path_to_image .=
                            $row['id_' . $this->list_table] .
                            (isset($row['id_image']) ? '-' . (int) $row['id_image'] : '') . '.' . $this->imageType;
                    } else {
                        $path_to_image .=
                            Image::getImgFolderStatic($row['id_image']) .
                            (int) $row['id_image'] . '.' . $this->imageType;
                    }

                    if ($path_to_image) {
                        $field_value = $path_to_image;
                    }
                }

                if (isset($params['callback'])) {
                    $callback_object = (isset($params['callback_object'])) ?
                        $params['callback_object'] :
                        $context->controller;

                    if (!preg_match(
                        '/<([a-z]+)([^<]+)*(?:>(.*)<\/\1>|\s+\/>)/ism',
                        call_user_func_array([$callback_object, $params['callback']], [$field_value, $row])
                    )) {
                        $field_value = call_user_func_array(
                            [$callback_object, $params['callback']],
                            [$field_value, $row]
                        );
                    }
                }

                $content[] = $field_value;
            }

            fputcsv($fd, $content, ';', $text_delimiter);
        }

        @fclose($fd);

        exit;
    }

    /**
     * Object Delete.
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processDelete()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            $res = true;

            // Check if request at least one object with noZeroObject
            if (isset($object->noZeroObject)
                && count(call_user_func([$this->className, $object->noZeroObject])) <= 1
            ) {
                $this->module_errors[] =
                    $this->l('You need at least one object. ') . $this->list_table .
                    $this->l(' You cannot delete all of the items.');
            } elseif (array_key_exists('delete', $this->list_skip_actions)
                && in_array($object->id, $this->list_skip_actions['delete'])
            ) { // Check if some ids are in list_skip_actions and forbid deletion
                $this->module_errors[] = $this->l('You cannot delete this item.');
            } else {
                if ($this->deleted) {
                    if (!empty($this->fieldImageSettings)) {
                        $res = $object->deleteImage();
                    }

                    if (!$res) {
                        $this->module_errors[] = $this->l('Unable to delete associated images.');
                    }

                    $object->deleted = 1;

                    $res = $object->update();
                } else {
                    $res = $object->delete();
                }

                if ($res) {
                    if (class_exists('PrestaShopLogger')) {
                        PrestaShopLogger::addLog(
                            sprintf($this->l('%s deletion'), $this->className),
                            1,
                            null,
                            $this->className,
                            (int) $this->object->id,
                            true,
                            (int) $this->context->employee->id
                        );
                    }

                    $this->afterDelete($object);
                } else {
                    $this->module_errors[] = $this->l('An error occurred during deletion.');
                }
            }
        } else {
            $this->module_errors[] =
                $this->l('An error occurred while deleting the object. ') .
                $this->list_table . $this->l(' (cannot load object)');
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Element deleted successfully.'));
    }

    /**
     * Call the right method for creating or updating object
     *
     * @return mixed
     */
    public function processSave()
    {
        if ($this->id_object) {
            $this->object = $this->loadObject();

            return $this->processUpdate();
        } else {
            return $this->processAdd();
        }
    }

    /**
     * Object creation.
     *
     * @return ObjectModel|false
     *
     * @throws PrestaShopException
     */
    public function processAdd()
    {
        if (!isset($this->className) || empty($this->className)) {
            return false;
        }

        $this->validateRules();

        if (count($this->module_errors) <= 0) {
            $this->object = new $this->className();

            $this->copyFromPost($this->object, $this->list_table);

            $this->beforeAdd($this->object);

            if (method_exists($this->object, 'add') && !$this->object->add()) {
                $this->module_errors[] = $this->l('An error occurred while creating an object.') .
                $this->list_table . ' (' . Db::getInstance()->getMsgError() . ')';
            } elseif (($_POST[$this->list_identifier] = $this->object->id)
                && $this->postImage($this->object->id)
                && !count($this->module_errors)
            ) {
                if (class_exists('PrestaShopLogger')) {
                    PrestaShopLogger::addLog(
                        sprintf($this->l('%s addition'), $this->className),
                        1,
                        null,
                        $this->className,
                        (int) $this->object->id,
                        true,
                        (int) $this->context->employee->id
                    );
                }

                $this->afterAdd($this->object);

                $this->updateAssoShop($this->object->id);
            }
        }

        $this->module_errors = array_unique($this->module_errors);

        if (!empty($this->module_errors)) {
            $this->display = 'edit';
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Successfully added.'));
    }

    /**
     * Called before Add.
     *
     * @param ObjectModel $object Object
     *
     * @return bool
     */
    protected function beforeAdd($object)
    {
        return true;
    }

    /**
     * Called before deletion.
     *
     * @param ObjectModel $object Object
     * @param int $old_id
     *
     * @return bool
     */
    protected function afterDelete($object)
    {
        if ($this->className == 'LGCookiesLawPurpose') {
            $object->deleteAssociatedCookies();
        }

        return true;
    }

    /**
     * @param ObjectModel $object
     *
     * @return bool
     */
    protected function afterAdd($object)
    {
        return true;
    }

    /**
     * @param ObjectModel $object
     *
     * @return bool
     */
    protected function afterUpdate($object)
    {
        return true;
    }

    /**
     * Update the associations of shops.
     *
     * @param int $id_object
     *
     * @return bool|void
     *
     * @throws PrestaShopDatabaseException
     */
    protected function updateAssoShop($id_object)
    {
        if (!Shop::isFeatureActive()) {
            return;
        }

        if (!Shop::isTableAssociated($this->list_table)) {
            return;
        }

        $assos_data = $this->getSelectedAssoShop($this->list_table);

        // Get list of shop id we want to exclude from asso deletion
        $exclude_ids = $assos_data;

        foreach (Db::getInstance()->executeS('SELECT id_shop FROM ' . _DB_PREFIX_ . 'shop') as $row) {
            if (!$this->context->employee->hasAuthOnShop($row['id_shop'])) {
                $exclude_ids[] = $row['id_shop'];
            }
        }

        Db::getInstance()->delete(
            $this->list_table . '_shop',
            '`' . bqSQL($this->list_identifier) . '` = ' . (int) $id_object .
            ($exclude_ids ? ' AND id_shop NOT IN (' . implode(', ', array_map('intval', $exclude_ids)) . ')' : '')
        );

        $insert = [];

        foreach ($assos_data as $id_shop) {
            $insert[] = [
                $this->list_identifier => (int) $id_object,
                'id_shop' => (int) $id_shop,
            ];
        }

        return Db::getInstance()->insert($this->list_table . '_shop', $insert, false, true, Db::INSERT_IGNORE);
    }

    /**
     * Returns an array with selected shops and type (group or boutique shop).
     *
     * @param string $table
     *
     * @return array
     */
    protected function getSelectedAssoShop($table)
    {
        if (!Shop::isFeatureActive() || !Shop::isTableAssociated($table)) {
            return [];
        }

        $shops = Shop::getShops(true, null, true);

        if (count($shops) == 1 && isset($shops[0])) {
            return [$shops[0], 'shop'];
        }

        $assos = [];

        if (Tools::isSubmit('checkBoxShopAsso_' . $table)) {
            foreach (Tools::getValue('checkBoxShopAsso_' . $table) as $id_shop => $value) {
                $assos[] = (int) $id_shop;
            }
        } elseif (Shop::getTotalShops(false) == 1) {
            // if we do not have the checkBox multishop, we can have an admin with only one shop and being in multishop
            $assos[] = (int) Shop::getContextShopID();
        }

        return $assos;
    }

    /**
     * Overload this method for custom checking.
     *
     * @param int $id Object id used for deleting images
     *
     * @return bool
     */
    protected function postImage($id)
    {
        if (isset($this->fieldImageSettings['name'], $this->fieldImageSettings['dir'])) {
            return $this->uploadImage(
                $id,
                $this->fieldImageSettings['name'],
                $this->fieldImageSettings['dir'] . '/'
            );
        } elseif (!empty($this->fieldImageSettings)) {
            foreach ($this->fieldImageSettings as $image) {
                if (isset($image['name'], $image['dir'])) {
                    $this->uploadImage(
                        $id,
                        $image['name'],
                        $image['dir'] . '/'
                    );
                }
            }
        }

        return !count($this->module_errors) ? true : false;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $dir
     * @param string|bool $ext
     * @param int|null $width
     * @param int|null $height
     *
     * @return bool
     */
    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            // Delete old image
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object->deleteImage();
            } else {
                return false;
            }

            // Check image validity
            $max_size = isset($this->max_image_size) ? $this->max_image_size : 0;

            if ($error = ImageManager::validateUpload($_FILES[$name], Tools::getMaxUploadSize($max_size))) {
                $this->module_errors[] = $error;
            }

            $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');

            if (!$tmp_name) {
                return false;
            }

            if (!move_uploaded_file($_FILES[$name]['tmp_name'], $tmp_name)) {
                return false;
            }

            // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
            if (!ImageManager::checkImageMemoryLimit($tmp_name)) {
                $this->module_errors[] =
                    $this->l('Due to memory limit restrictions, this image cannot be loaded.') . ' ' .
                    $this->l('Please increase your memory_limit value via your server\'s configuration settings.');
            }

            // Copy new image
            if (empty($this->module_errors)
                && !ImageManager::resize(
                    $tmp_name,
                    _PS_IMG_DIR_ . $dir . $id . '.' . $this->imageType,
                    (int) $width,
                    (int) $height,
                    $ext ? $ext : $this->imageType
                )
            ) {
                $this->module_errors[] = $this->l('An error occurred while uploading the image.');
            }

            if (count($this->module_errors)) {
                return false;
            }

            if ($this->afterImageUpload()) {
                unlink($tmp_name);

                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Object update.
     *
     * @return ObjectModel|false|void
     *
     * @throws PrestaShopException
     */
    public function processUpdate()
    {
        /* Checking fields validity */
        $this->validateRules();

        if (empty($this->module_errors)) {
            $id = (int) Tools::getValue($this->list_identifier);

            /* Object update */
            if (isset($id) && !empty($id)) {
                /** @var ObjectModel $object */
                $object = new $this->className($id);

                if (Validate::isLoadedObject($object)) {
                    /* Specific to objects which must not be deleted */
                    if ($this->deleted) {
                        // Create new one with old objet values
                        /** @var ObjectModel $object_new */
                        $object_new = $object->duplicateObject();

                        if (Validate::isLoadedObject($object_new)) {
                            // Update old object to deleted
                            $object->deleted = 1;

                            $object->update();

                            $this->afterUpdate($object);

                            // Update new object with post values
                            $this->copyFromPost($object_new, $this->list_table);

                            $result = $object_new->update();
                        }
                    } else {
                        $this->copyFromPost($object, $this->list_table);

                        $result = $object->update();

                        $this->afterUpdate($object);
                    }

                    if ($object->id) {
                        $this->updateAssoShop($object->id);
                    }

                    if (!$result) {
                        $this->module_errors[] =
                            $this->l('An error occurred while updating an object. ') .
                            $this->list_table . ' (' . Db::getInstance()->getMsgError() . ')';
                    } else {
                        $this->postImage($object->id);
                    }

                    if (class_exists('PrestaShopLogger')) {
                        PrestaShopLogger::addLog(
                            sprintf($this->l('%s modification'), $this->className),
                            1,
                            null,
                            $this->className,
                            (int) $object->id,
                            true,
                            (int) $this->context->employee->id
                        );
                    }
                } else {
                    $this->module_errors[] =
                        $this->l('An error occurred while updating an object. ') .
                        $this->list_table . $this->l(' (cannot load object)');
                }
            }
        }

        $this->module_errors = array_unique($this->module_errors);

        if (!empty($this->module_errors)) {
            $this->display = 'edit';
        }

        return count($this->module_errors) ?
            $this->displayError($this->module_errors) :
            $this->displayConfirmation($this->l('Successfully updated.'));
    }

    /* FORM FUNCTIONS */
    public function validateRules($class_name = false)
    {
        if (!$class_name) {
            $class_name = $this->className;
        }

        /** @var $object ObjectModel */
        $object = new $class_name();

        if (method_exists($this, 'getValidationRules' . $this->className)) {
            $definition = $this->{'getValidationRules' . $this->className}();
        } else {
            $definition = ObjectModel::getDefinition($class_name);
        }

        $default_language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($definition['fields'] as $field => $def) {
            $skip = [];

            if (in_array($field, ['passwd', 'no-picture'])) {
                $skip = ['required'];
            }

            if (isset($def['lang']) && $def['lang']) {
                if (isset($def['required']) && $def['required']) {
                    $value = Tools::getValue($field . '_' . $default_language->id);

                    if (!isset($value) || '' == $value) {
                        $this->module_errors[$field . '_' . $default_language->id] = sprintf(
                            $this->l('The field %1s is required at least in %2s.'),
                            $object->displayFieldName($field, $class_name),
                            $default_language->name
                        );
                    }
                }

                foreach ($languages as $language) {
                    $value = Tools::getValue($field . '_' . $language['id_lang']);

                    if (!empty($value)) {
                        if (($error =
                                $object->validateField($field, $value, $language['id_lang'], $skip, true)) !== true
                        ) {
                            $this->module_errors[$field . '_' . $language['id_lang']] = $error;
                        }
                    }
                }
            } elseif (($error = $object->validateField($field, Tools::getValue($field), null, $skip, true)) !== true) {
                $this->module_errors[$field] = $error;
            }
        }
    }

    /**
     * Copy data values from $_POST to object.
     *
     * @param ObjectModel &$object Object
     * @param string $table Object table
     */
    protected function copyFromPost(&$object, $table)
    {
        /* Classical fields */
        foreach ($_POST as $key => $value) {
            if (isset($key, $object) && $key != 'id_' . $table) {
                /* Do not take care of password field if empty */
                if ($key == 'passwd' && Tools::getValue('id_' . $table) && empty($value)) {
                    continue;
                }

                /* Automatically hash password in MD5 */
                if ($key == 'passwd' && !empty($value)) {
                    $value = $this->get('hashing')->hash($value, _COOKIE_KEY_);
                }

                $object->{$key} = $value;
            }
        }

        /* Multilingual fields */
        $class_vars = get_class_vars(get_class($object));

        $fields = [];

        if (isset($class_vars['definition']['fields'])) {
            $fields = $class_vars['definition']['fields'];
        }

        foreach ($fields as $field => $params) {
            if (array_key_exists('lang', $params) && $params['lang']) {
                foreach (Language::getLanguages(false) as $language) {
                    $id_lang = (int) $language['id_lang'];

                    if (Tools::isSubmit($field . '_' . (int) $id_lang)) {
                        $object->{$field}[(int) $id_lang] = Tools::getValue($field . '_' . (int) $id_lang);
                    }
                }
            }
        }
    }

    /**
     * Check rights to view the current tab.
     *
     * @return bool
     */
    protected function afterImageUpload()
    {
        return true;
    }

    public function printDefaultSwitchValues($name = 'active')
    {
        return [
            [
                'id' => $name . '_on',
                'value' => 1,
                'label' => $this->l('Yes'),
            ],
            [
                'id' => $name . '_off',
                'value' => 0,
                'label' => $this->l('No'),
            ],
        ];
    }

    public function getLinkTag($href, $message, $target = null, $title = null)
    {
        $context = Context::getContext();

        $context->smarty->assign([
            'href' => $href,
            'target' => $target,
            'title' => $title,
            'message' => $message,
        ]);

        return $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/_configure/helpers/form/form_message_link.tpl'
        ));
    }

    public static function getModuleConfiguration()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        $configuration = Configuration::getMultiple(
            [
                'PS_LGCOOKIES_BANNER_HOOK',
                'PS_LGCOOKIES_DISALLOW',
                'PS_LGCOOKIES_COOKIE_NAME',
                'PS_LGCOOKIES_COOKIE_TIMELIFE',
                'PS_LGCOOKIES_SAVE_USER_CONSENT',
                'PS_LGCOOKIES_ANONYMIZE_UC_IP',
                'PS_LGCOOKIES_USE_COOKIE_VAR',
                'PS_LGCOOKIES_RELOAD',
                'PS_LGCOOKIES_BLOCK_NAVIGATION',
                'PS_LGCOOKIES_BANNER_POSITION',
                'PS_LGCOOKIES_SHOW_FIXED_BTN',
                'PS_LGCOOKIES_SHOW_CLOSE_BTN',
                'PS_LGCOOKIES_CLOSE_BTN_RJCT_CKS',
                'PS_LGCOOKIES_LOAD_FANCYBOX',
                'PS_LGCOOKIES_INFO_LINK_ID_CMS',
                'PS_LGCOOKIES_SHOW_BANNER_IN_CMS',
                'PS_LGCOOKIES_TESTMODE',
                'PS_LGCOOKIES_IPTESTMODE',
                'PS_LGCOOKIES_BOTS',
                'PS_LGCOOKIES_BANNER_BG_COLOR',
                'PS_LGCOOKIES_BANNER_BG_OPACITY',
                'PS_LGCOOKIES_FIXED_BTN_SVG_COLOR',
                'PS_LGCOOKIES_BANNER_FONTCOLOR',
                'PS_LGCOOKIES_ACPT_BTN_BG_COLOR',
                'PS_LGCOOKIES_ACPT_BTN_FONT_COLOR',
                'PS_LGCOOKIES_BANNER_SHADOWCOLOR',
                'PS_LGCOOKIES_RJCT_BTN_BG_COLOR',
                'PS_LGCOOKIES_RJCT_BTN_FONT_COLOR',
                'PS_LGCOOKIES_THIRD_PARTIES',
                'PS_LGCOOKIES_INFO_LINK_TARGET',
                'PS_LGCOOKIES_SHOW_RJCT_BTN',
                'PS_LGCOOKIES_FIXED_BTN_POSITION',
                'PS_LGCOOKIES_CONSENT_MODE',
                'PS_LGCOOKIES_DELETE_USER_CONSENT',
                'PS_DISABLE_OVERRIDES',
                'PS_DISABLE_NON_NATIVE_MODULE',
                'PS_LGCOOKIES_PUC_COMPATIBILITY',
                'PS_LGCOOKIES_NOTICED_MODULES',
            ],
            null,
            (int) $id_shop_group,
            (int) $id_shop
        );

        return $configuration;
    }

    public static function getModuleConfigurationLang($id_lang = null)
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        if (is_null($id_lang)) {
            $id_lang = $context->language->id;
        }

        $configuration_lang = Configuration::getMultiple(
            [
                'PS_LGCOOKIES_ACPT_BTN_TITLE',
                'PS_LGCOOKIES_INFO_LINK_TITLE',
                'PS_LGCOOKIES_RJCT_BTN_TITLE',
                'PS_LGCOOKIES_BANNER_MESSAGE',
            ],
            (int) $id_lang,
            (int) $id_shop_group,
            (int) $id_shop
        );

        return $configuration_lang;
    }

    public function saveCss($id_shop = null, $id_shop_group = null)
    {
        $context = Context::getContext();

        if (is_null($id_shop)) {
            $id_shop = $context->shop->id;
        }

        if (is_null($id_shop_group)) {
            $id_shop_group = Shop::getGroupFromShop((int) $id_shop);
        }

        $configuration = self::getModuleConfiguration();

        list($r, $g, $b) = sscanf($configuration['PS_LGCOOKIES_BANNER_BG_COLOR'], '#%02x%02x%02x');
        $banner_bg_color = $r . ',' . $g . ',' . $b . ',' . $configuration['PS_LGCOOKIES_BANNER_BG_OPACITY'];
        $fixed_button_svg_color = $configuration['PS_LGCOOKIES_FIXED_BTN_SVG_COLOR'];

        $context->smarty->assign([
            'lgcookieslaw_banner_bg_color' => $banner_bg_color,
            'lgcookieslaw_fixed_button_svg_color' => $fixed_button_svg_color,
            'lgcookieslaw_banner_font_color' => $configuration['PS_LGCOOKIES_BANNER_FONTCOLOR'],
            'lgcookieslaw_banner_shadow_color' => $configuration['PS_LGCOOKIES_BANNER_SHADOWCOLOR'],
            'lgcookieslaw_accept_button_bg_color' => $configuration['PS_LGCOOKIES_ACPT_BTN_BG_COLOR'],
            'lgcookieslaw_accept_button_font_color' => $configuration['PS_LGCOOKIES_ACPT_BTN_FONT_COLOR'],
            'lgcookieslaw_reject_button_bg_color' => $configuration['PS_LGCOOKIES_RJCT_BTN_BG_COLOR'],
            'lgcookieslaw_reject_button_font_color' => $configuration['PS_LGCOOKIES_RJCT_BTN_FONT_COLOR'],
        ]);

        $rendered_template = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/admin/view_styles.tpl'
        ));

        $generated_css_file_name = 'lgcookieslaw_' . (int) $id_shop_group . '_' . (int) $id_shop . '.css';
        $generated_css_file_path = _PS_MODULE_DIR_ . $this->name . '/views/css/' . $generated_css_file_name;

        $file = fopen($generated_css_file_path, 'w');

        $success = true;

        $success &= (bool) fwrite($file, $rendered_template);
        $success &= fclose($file);

        return $success;
    }

    public function renderHook()
    {
        $context = Context::getContext();

        $content = '';

        $access_granted = $this->checkAccessGranted($context);

        // Filtramos para mostrar el banner solo en países que sean de la UE
        if ($access_granted) {
            $link = $context->link;

            $id_lang = $context->language->id;
            $id_shop = $context->shop->id;

            $configuration = self::getModuleConfiguration();
            $configuration_lang = self::getModuleConfigurationLang((int) $id_lang);

            $lgcookieslaw_cookie_values = $this->getCookieValues();

            $lgcookieslaw_accepted_purposes = !empty($lgcookieslaw_cookie_values) ?
                $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes : [];

            $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop, true);

            foreach ($lgcookieslaw_purposes as &$lgcookieslaw_purpose) {
                $id_lgcookieslaw_purpose = (int) $lgcookieslaw_purpose['id_lgcookieslaw_purpose'];

                $associated_cookies = LGCookiesLawCookie::getCookiesByPurpose(
                    (int) $id_lgcookieslaw_purpose,
                    (int) $id_lang,
                    (int) $id_shop,
                    true
                );

                $lgcookieslaw_purpose['associated_cookies'] =
                    !empty($associated_cookies) ? $associated_cookies : [];

                $check_alternative = empty($lgcookieslaw_accepted_purposes)
                    && ($lgcookieslaw_purpose['technical']
                    || (bool) $configuration['PS_LGCOOKIES_THIRD_PARTIES']);

                $lgcookieslaw_purpose['checked'] =
                    in_array((int) $id_lgcookieslaw_purpose, $lgcookieslaw_accepted_purposes) ?
                        true : $check_alternative;
            }

            $lgcookieslaw_info_link_url =
                $link->getCMSLink((int) $configuration['PS_LGCOOKIES_INFO_LINK_ID_CMS']);

            $context->smarty->assign([
                'lgcookieslaw_banner_message' => $configuration_lang['PS_LGCOOKIES_BANNER_MESSAGE'],
                'lgcookieslaw_accept_button_title' => $configuration_lang['PS_LGCOOKIES_ACPT_BTN_TITLE'],
                'lgcookieslaw_info_link_title' => $configuration_lang['PS_LGCOOKIES_INFO_LINK_TITLE'],
                'lgcookieslaw_reject_button_title' => $configuration_lang['PS_LGCOOKIES_RJCT_BTN_TITLE'],
                'lgcookieslaw_info_link_url' => $lgcookieslaw_info_link_url,
                'lgcookieslaw_info_link_target' => $configuration['PS_LGCOOKIES_INFO_LINK_TARGET'],
                'lgcookieslaw_third_parties' => $configuration['PS_LGCOOKIES_THIRD_PARTIES'],
                'lgcookieslaw_banner_position' => $configuration['PS_LGCOOKIES_BANNER_POSITION'],
                'lgcookieslaw_show_reject_button' => $configuration['PS_LGCOOKIES_SHOW_RJCT_BTN'],
                'lgcookieslaw_purposes' => $lgcookieslaw_purposes,
                'lgcookieslaw_enable_google_consent_mode' => (bool) $configuration['PS_LGCOOKIES_CONSENT_MODE'],
                'lgcookieslaw_show_close_button' => (bool) $configuration['PS_LGCOOKIES_SHOW_CLOSE_BTN'],
                'lgcookieslaw_show_fixed_button' => (bool) $configuration['PS_LGCOOKIES_SHOW_FIXED_BTN'],
                'lgcookieslaw_fixed_button_position' => $configuration['PS_LGCOOKIES_FIXED_BTN_POSITION'],
            ]);

            if ((bool) $configuration['PS_LGCOOKIES_PUC_COMPATIBILITY']) {
                $context->smarty->assign([
                    'lgcookieslaw_view_header_content' => $this->getViewHeaderContent(),
                ]);
            }

            $content = $context->smarty->fetch($this->getTemplatePath(
                'views/templates/hook/view_banner.tpl'
            ));
        }

        return $content;
    }

    public function hookDisplayBackOfficeTop()
    {
        // $content = '';
        // return $content;
    }

    public function hookDisplayBackOfficeHeader()
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $css_list = [];
        $js_list = [];

        $content = '';

        if (isset($context->controller)
            && $context->controller instanceof AdminModulesController
            && Tools::getValue('configure')
        ) {
            $views_path = $this->_path . 'views/';

            $css_list = [
                $views_path . 'css/back.css',
                $views_path . 'css/publi/lgpubli.css',
            ];

            $js_list = [
                $views_path . 'js/back.js',
            ];

            $context->smarty->assign([
                'lgcookieslaw_translates_copy_success_message' => $this->l('Successfully copied!'),
                'lgcookieslaw_translates_copy_error_message' => $this->l('Copy failed.'),
            ]);

            $content = $context->smarty->fetch($this->getTemplatePath(
                'views/templates/hook/view_backoffice_header.tpl'
            ));
        }

        foreach ($css_list as $css_url) {
            $context->controller->addCSS($css_url);
        }

        foreach ($js_list as $js_url) {
            $context->controller->addJS($js_url);
        }

        if ($configuration['PS_LGCOOKIES_DELETE_USER_CONSENT']) {
            $expired_user_consents = LGCookiesLawUserConsent::getExpiredUserConsents();

            if (isset($expired_user_consents) && !empty($expired_user_consents)) {
                foreach ($expired_user_consents as $expired_user_consent) {
                    $user_consent =
                        new LGCookiesLawUserConsent((int) $expired_user_consent['id_lgcookieslaw_user_consent']);

                    $user_consent->delete();
                }
            }
        }

        return $content;
    }

    public function hookDisplayHeader($params)
    {
        $context = Context::getContext();

        // Modulo everpspopup (revisa si el usuario es mayor de edad)
        // Compatibilidad con el modulo por fallos de compatibilidad en fancybox
        if (Module::isInstalled('everpspopup')
            && Module::isEnabled('everpspopup')) {
            $everpspopup = false;
            foreach ($_COOKIE as $key => $value) {
                if (str_contains($key, 'everpspopup') && $value == '1') {
                    $everpspopup = true;
                }
            }
            if (!$everpspopup) {
                return;
            }
        }

        if (!$this->checkAccessGranted($context)) {
            return;
        }

        $configuration = self::getModuleConfiguration();

        $id_shop = $context->shop->id;
        $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

        $generated_css_file_name = 'lgcookieslaw_' . (int) $id_shop_group . '_' . (int) $id_shop . '.css';
        $generated_css_file_path = _PS_MODULE_DIR_ . $this->name . '/views/css/' . $generated_css_file_name;

        if (!file_exists($generated_css_file_path)) {
            $this->saveCss((int) $id_shop, (int) $id_shop_group);
        }

        $css_list = [];
        $js_list = [];

        $views_path = $this->_path . 'views/';

        $js_list = [
            $views_path . 'js/plugins/tooltipster/tooltipster.bundle.min.js',
            $views_path . 'js/front.js',
        ];

        $css_list = [
            $views_path . 'css/plugins/tooltipster/tooltipster.bundle.min.css',
            $views_path . 'css/plugins/tooltipster/tooltipster.borderless.min.css',
            $views_path . 'css/' . $generated_css_file_name,
            $views_path . 'css/front.css',
        ];

        if ($configuration['PS_LGCOOKIES_LOAD_FANCYBOX']) {
            $js_list[] =
                $views_path . 'js/plugins/fancybox/jquery.fancybox.js';

            $css_list[] =
                $views_path . 'css/plugins/fancybox/jquery.fancybox.css';
        }

        $context->controller->addJqueryPlugin(['fancybox']);

        foreach ($css_list as $css_url) {
            $context->controller->addCSS($css_url);
        }

        foreach ($js_list as $js_url) {
            $context->controller->addJS($js_url);
        }

        $content = '';

        if (!(bool) $configuration['PS_LGCOOKIES_PUC_COMPATIBILITY']) {
            $content = $this->getViewHeaderContent();
        }

        return $content;
    }

    public function hookDisplayAfterTitleTag($params)
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $lgcookieslaw_cookie_values = $this->getCookieValues(true);

        if (isset($lgcookieslaw_cookie_values->lgcookieslaw_user_consent_consent_date)) {
            $user_consent_expiry_time =
                strtotime($lgcookieslaw_cookie_values->lgcookieslaw_user_consent_consent_date) +
                (int) $configuration['PS_LGCOOKIES_COOKIE_TIMELIFE'];

            if (time() > $user_consent_expiry_time) {
                $this->resetCookie();

                $lgcookieslaw_cookie_values = $this->getCookieValues(true);
            }
        }

        $consent_mode = (bool) $configuration['PS_LGCOOKIES_CONSENT_MODE'];
        $advertising_cookies = 'denied';
        $analytics_cookies = 'denied';

        if ($consent_mode) {
            $consent = $this->getConsentModeContent();

            if (isset($consent)) {
                foreach ($consent as $consent_value) {
                    if (isset($consent_value['name']) && $consent_value['name'] == 'ad_storage') {
                        if (isset($consent_value['value']) && $consent_value['value'] == false) {
                            $advertising_cookies = 'denied';
                        } else {
                            $advertising_cookies = 'granted';
                        }
                    } elseif (isset($consent_value['name']) && $consent_value['name'] == 'analytics_storage') {
                        if (isset($consent_value['value']) && $consent_value['value'] == false) {
                            $analytics_cookies = 'denied';
                        } else {
                            $analytics_cookies = 'granted';
                        }
                    }
                }
            } else {
                $advertising_cookies = 'denied';
                $analytics_cookies = 'denied';
            }
        }

        $context->smarty->assign([
            'lgcookieslaw_consent_mode' => (bool) $configuration['PS_LGCOOKIES_CONSENT_MODE'],
            'advertising_cookies' => $advertising_cookies,
            'analytics_cookies' => $analytics_cookies,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/hook/view_script.tpl'
        ));

        return $content;
    }

    public function hookDisplayAfterTitle($params)
    {
        return $this->hookDisplayAfterTitleTag($params);
    }

    public function getViewHeaderContent()
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $lgcookieslaw_banner_url_ajax_controller =
            $context->link->getModuleLink($this->name, 'ajax', [], true);

        $lgcookieslaw_cookie_values = $this->getCookieValues(true);

        if (isset($lgcookieslaw_cookie_values->lgcookieslaw_user_consent_consent_date)) {
            $user_consent_expiry_time =
                strtotime($lgcookieslaw_cookie_values->lgcookieslaw_user_consent_consent_date) +
                (int) $configuration['PS_LGCOOKIES_COOKIE_TIMELIFE'];

            if (time() > $user_consent_expiry_time) {
                $this->resetCookie();

                $lgcookieslaw_cookie_values = $this->getCookieValues(true);
            }
        }

        $saved_preferences = !empty($lgcookieslaw_cookie_values);

        $name_current_hook = 'header';

        $lgcookieslaw_cookies_scripts_content = $this->getCookiesScriptsContent($name_current_hook);

        $context->smarty->assign([
            'lgcookieslaw_consent_mode' => (bool) $configuration['PS_LGCOOKIES_CONSENT_MODE'],
            'lgcookieslaw_banner_url_ajax_controller' => $lgcookieslaw_banner_url_ajax_controller,
            'lgcookieslaw_cookie_values' => $lgcookieslaw_cookie_values,
            'lgcookieslaw_cookie_values_json' => self::jsonEncode($lgcookieslaw_cookie_values),
            'lgcookieslaw_saved_preferences' => (bool) $saved_preferences,
            'lgcookieslaw_ajax_calls_token' => self::getToken($this->name),
            'lgcookieslaw_reload' => (bool) $configuration['PS_LGCOOKIES_RELOAD'],
            'lgcookieslaw_block_navigation' => (bool) $configuration['PS_LGCOOKIES_BLOCK_NAVIGATION'],
            'lgcookieslaw_banner_position' => $configuration['PS_LGCOOKIES_BANNER_POSITION'],
            'lgcookieslaw_show_fixed_button' => (bool) $configuration['PS_LGCOOKIES_SHOW_FIXED_BTN'],
            'lgcookieslaw_save_user_consent' => $configuration['PS_LGCOOKIES_SAVE_USER_CONSENT'],
            'lgcookieslaw_reject_cookies_when_closing_banner' => (bool) $configuration['PS_LGCOOKIES_SHOW_CLOSE_BTN']
                    && (bool) $configuration['PS_LGCOOKIES_CLOSE_BTN_RJCT_CKS'],
            'lgcookieslaw_cookies_scripts_content' => $lgcookieslaw_cookies_scripts_content,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/hook/view_header.tpl'
        ));

        return $content;
    }

    public function getConsentModeContent()
    {
        $context = Context::getContext();

        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $lgcookieslaw_consent_types[] = [];

        $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop, true);

        $lgcookieslaw_cookie_values = $this->getCookieValues();

        $lgcookieslaw_accepted_purposes = !empty($lgcookieslaw_cookie_values) ?
            $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes : [];

        foreach ($lgcookieslaw_purposes as $lgcookieslaw_purpose) {
            if ($lgcookieslaw_purpose['consent_mode']) {
                $id_lgcookieslaw_purpose = (int) $lgcookieslaw_purpose['id_lgcookieslaw_purpose'];

                $lgcookieslaw_consent_types[] = [
                    'name' => $lgcookieslaw_purpose['consent_type'],
                    'value' => $lgcookieslaw_purpose['technical']
                        || (!empty($lgcookieslaw_accepted_purposes)
                            && in_array((int) $id_lgcookieslaw_purpose, $lgcookieslaw_accepted_purposes)),
                ];
            }
        }

        return $lgcookieslaw_consent_types;
    }

    public function hookDisplayAfterBodyOpeningTag($params)
    {
        $context = Context::getContext();

        if (!$this->checkAccessGranted($context)) {
            return;
        }

        $name_current_hook = 'displayAfterBodyOpeningTag';

        $lgcookieslaw_cookies_scripts_content = $this->getCookiesScriptsContent($name_current_hook);

        $context->smarty->assign([
            'lgcookieslaw_cookies_scripts_content' => $lgcookieslaw_cookies_scripts_content,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/hook/view_after_body_opening_tag_header.tpl'
        ));

        return $content;
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        $context = Context::getContext();

        if (!$this->checkAccessGranted($context)) {
            return;
        }

        $name_current_hook = 'displayBeforeBodyClosingTag';

        $lgcookieslaw_cookies_scripts_content = $this->getCookiesScriptsContent($name_current_hook);

        $context->smarty->assign([
            'lgcookieslaw_cookies_scripts_content' => $lgcookieslaw_cookies_scripts_content,
        ]);

        $content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/hook/view_after_body_opening_tag_header.tpl'
        ));

        return $content;
    }

    public function getCookiesScriptsContent($name_current_hook)
    {
        $context = Context::getContext();

        $lgcookieslaw_cookies_scripts = [];

        if (!empty($name_current_hook)) {
            $id_lang = $context->language->id;
            $id_shop = $context->shop->id;

            $lgcookieslaw_cookie_values = $this->getCookieValues();

            $saved_preferences = !empty($lgcookieslaw_cookie_values);

            if ($saved_preferences) {
                $lgcookieslaw_accepted_purposes = $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes;

                foreach ($lgcookieslaw_accepted_purposes as $lgcookieslaw_accepted_purpose) {
                    $lgcookieslaw_cookies =
                        LGCookiesLawCookie::getCookiesByPurpose(
                            (int) $lgcookieslaw_accepted_purpose,
                            (int) $id_lang,
                            (int) $id_shop,
                            true
                        );

                    foreach ($lgcookieslaw_cookies as $lgcookieslaw_cookie) {
                        if ($lgcookieslaw_cookie['install_script']
                            && !empty($lgcookieslaw_cookie['script_code'])
                            && $lgcookieslaw_cookie['script_hook'] == $name_current_hook
                        ) {
                            $lgcookieslaw_cookies_scripts[] = $lgcookieslaw_cookie;
                        }
                    }
                }
            }
        }

        $context->smarty->assign([
            'lgcookieslaw_cookies_scripts' => $lgcookieslaw_cookies_scripts,
        ]);

        $lgcookieslaw_cookies_scripts_content = $context->smarty->fetch($this->getTemplatePath(
            'views/templates/hook/view_cookies_scripts_content.tpl'
        ));

        return $lgcookieslaw_cookies_scripts_content;
    }

    public function hookDisplayCustomerAccount($params)
    {
        $content = '';

        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ($configuration['PS_LGCOOKIES_DISALLOW']) {
            $lgcookieslaw_image_path = $this->getPathUri();
            $lgcookieslaw_disallow_url = $context->link->getModuleLink(
                $this->name,
                'disallow',
                [
                    'token' => md5(_COOKIE_KEY_ . $this->name),
                ],
                true
            );

            $context->smarty->assign([
                'lgcookieslaw_ps_version' => $this->ps_version,
                'lgcookieslaw_disallow_url' => $lgcookieslaw_disallow_url,
                'lgcookieslaw_image_path' => $lgcookieslaw_image_path . 'views/img/account_button_icon_' . $this->ps_version . '.png',
            ]);

            $content = $context->smarty->fetch($this->getTemplatePath(
                'views/templates/front/view_disallow_account_button.tpl'
            ));
        }

        return $content;
    }

    public function hookDisplayTop($params)
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ($configuration['PS_LGCOOKIES_BANNER_HOOK'] == 'top') {
            return $this->renderHook();
        }
    }

    public function hookDisplayMobileTop($params)
    {
        return $this->hookDisplayTop($params);
    }

    public function hookDisplayFooter($params)
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        if ($configuration['PS_LGCOOKIES_BANNER_HOOK'] == 'footer') {
            return $this->renderHook();
        }
    }

    public function hookDisplayFooterAfter($params)
    {
        return $this->hookDisplayFooter($params);
    }

    public function hookDisplayBelowHeader($params)
    {
        return $this->hookDisplayTop($params);
    }

    public function hookDisplayFooterBefore($params)
    {
        return $this->hookDisplayFooter($params);
    }

    public function hookDisplayFooterBuilder($params)
    {
        return $this->hookDisplayFooter($params);
    }

    public function getHookModuleExecList($modules_to_invoke)
    {
        $context = Context::getContext();

        $access_granted = $this->checkAccessGranted($context);

        if (!empty($modules_to_invoke) && $access_granted) {
            $lgcookieslaw_cookie_values = $this->getCookieValues();

            $lgcookieslaw_accepted_purposes = !empty($lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes) ?
                implode(',', $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes) : null;

            $lgcookieslaw_purposes_locked_modules =
                LGCookiesLawPurpose::getLockedModules($lgcookieslaw_accepted_purposes);

            $lgcookieslaw_all_locked_modules = [];

            if (!empty($lgcookieslaw_purposes_locked_modules)) {
                foreach ($lgcookieslaw_purposes_locked_modules as $lgcookieslaw_purpose_locked_modules) {
                    $locked_modules = !empty($lgcookieslaw_purpose_locked_modules['locked_modules']) ?
                        self::jsonDecode($lgcookieslaw_purpose_locked_modules['locked_modules']) : [];

                    $lgcookieslaw_all_locked_modules = array_unique(
                        array_merge($lgcookieslaw_all_locked_modules, $locked_modules)
                    );
                }
            }

            foreach ($modules_to_invoke as $index => $module_to_invoke) {
                if (in_array($module_to_invoke['module'], $lgcookieslaw_all_locked_modules)) {
                    unset($modules_to_invoke[$index]);
                }
            }
        }

        return $modules_to_invoke;
    }

    public function checkAccessGranted(Context $context)
    {
        $configuration = self::getModuleConfiguration();

        $access_granted = true;

        $its_backoffice =
            $access_granted
            && defined('_PS_ADMIN_DIR_')
            && $context->employee instanceof Employee
            && Validate::isLoadedObject($context->employee);

        if ($its_backoffice) {
            $access_granted = false;
        }

        $not_show_banner_in_cms =
            $access_granted
            && $configuration['PS_LGCOOKIES_SHOW_BANNER_IN_CMS'] == false
            && $context->controller instanceof CmsController
            && (int) Tools::getValue('id_cms', 0) == $configuration['PS_LGCOOKIES_INFO_LINK_ID_CMS'];

        if ($not_show_banner_in_cms) {
            $access_granted = false;
        }

        $not_show_banner_for_this_ip =
            $access_granted
            && $configuration['PS_LGCOOKIES_TESTMODE']
            && $configuration['PS_LGCOOKIES_IPTESTMODE'] != Tools::getRemoteAddr();

        if ($not_show_banner_for_this_ip) {
            $access_granted = false;
        }

        $is_bot =
            $access_granted
            && isset($_SERVER['HTTP_USER_AGENT'])
            && !empty($_SERVER['HTTP_USER_AGENT'])
            && $this->isBot($_SERVER['HTTP_USER_AGENT']);

        if ($is_bot) {
            $access_granted = false;
        }

        $its_in_disallow_controller =
            $access_granted
            && $context->controller instanceof LGCookieslawDisallowModuleFrontController;

        if ($its_in_disallow_controller) {
            $access_granted = false;
        }

        return $access_granted;
    }

    public function deleteCookies($all_cookies = true, $with_logout = false)
    {
        $context = Context::getContext();

        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $configuration = self::getModuleConfiguration();

        $lgcookieslaw_cookie_name = $configuration['PS_LGCOOKIES_COOKIE_NAME'];

        $cookies_to_delete = [];

        if ($all_cookies) {
            $cookies_to_delete = LGCookiesLawCookie::getCookies((int) $id_lang, (int) $id_shop, true, true);
        } else {
            $lgcookieslaw_accepted_purposes = Tools::getValue('accepted_purposes', []);
            $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop, true);

            foreach ($lgcookieslaw_purposes as $lgcookieslaw_purpose) {
                $id_lgcookieslaw_purpose = (int) $lgcookieslaw_purpose['id_lgcookieslaw_purpose'];

                if (!in_array((int) $id_lgcookieslaw_purpose, $lgcookieslaw_accepted_purposes)
                    && !(bool) $lgcookieslaw_purpose['technical']
                ) {
                    $associated_cookies = LGCookiesLawCookie::getCookiesByPurpose(
                        (int) $id_lgcookieslaw_purpose,
                        (int) $id_lang,
                        (int) $id_shop,
                        true
                    );

                    $associated_cookies = !empty($associated_cookies) ? $associated_cookies : [];
                    $cookies_to_delete = array_merge($cookies_to_delete, $associated_cookies);

                    unset($associated_cookies);
                }
            }
        }

        $physical_uri = $context->shop->physical_uri;
        $domain = $context->shop->domain;
        $only_domain = preg_replace(
            '/^([a-zA-Z0-9].*\.)?([a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z.]{2,})$/',
            '$2',
            $domain
        );

        foreach ($cookies_to_delete as $cookie_to_delete) {
            $cookie_name = $cookie_to_delete['name'];

            if (strpos($cookie_name, '#')) {
                $modified_cookie_name = str_replace('#', '', $cookie_name);

                $cookie_pattern = '/^' . $modified_cookie_name . '/';

                $cookie_name = $this->pregArrayKeyExists($cookie_pattern, $_COOKIE);
            }

            if ($cookie_name && isset($_COOKIE[$cookie_name])) {
                setcookie($cookie_name, '', time() - 3600);
                setcookie($cookie_name, '', time() - 3600, '/');
                setcookie($cookie_name, '', time() - 3600, $physical_uri);
                setcookie($cookie_name, '', time() - 3600, $physical_uri, $domain);
                setcookie($cookie_name, '', time() - 3600, $physical_uri, $only_domain);

                unset($_COOKIE[$cookie_name]);
            }
        }

        $success = true;

        if ($configuration['PS_LGCOOKIES_USE_COOKIE_VAR']) {
            $success &= setcookie($lgcookieslaw_cookie_name, '', time() - 3600, '/');

            unset($_COOKIE[$lgcookieslaw_cookie_name]);
        } else {
            $success &= $context->cookie->__unset($lgcookieslaw_cookie_name);
        }

        if ($success && $with_logout) {
            if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
                $context->cookie->logout();
            } else {
                if (isset($context->customer)) {
                    $context->customer->logout();
                } else {
                    Customer::logout();
                }
            }
        }

        return $success;
    }

    public function pregArrayKeyExists($pattern, $array)
    {
        $array_keys = array_keys($array);

        $returned_values = preg_grep($pattern, $array_keys);

        $searched_index = (int) $returned_values ? array_shift($returned_values) : false;

        return $searched_index;
    }

    public function resetCookie()
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $success = true;

        $lgcookieslaw_cookie_name = $configuration['PS_LGCOOKIES_COOKIE_NAME'];

        if ($configuration['PS_LGCOOKIES_USE_COOKIE_VAR']) {
            $lgcookies_cookie_timelife = time() + $configuration['PS_LGCOOKIES_COOKIE_TIMELIFE'] * 86400;

            $success &= setcookie(
                $lgcookieslaw_cookie_name,
                self::jsonEncode([]),
                $lgcookies_cookie_timelife,
                '/'
            );
        } else {
            $success &= $context->cookie->__unset($lgcookieslaw_cookie_name);
        }

        $context->cookie->$lgcookieslaw_cookie_name = self::jsonEncode([]);

        $context->cookie->write();

        return $success;
    }

    public function getCookieValues($add_user_consent_download_urls = false)
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;
        $id_lang = $context->language->id;

        $configuration = self::getModuleConfiguration();

        $lgcookieslaw_cookie_name = $configuration['PS_LGCOOKIES_COOKIE_NAME'];

        $encrypted_lgcookieslaw_cookie_values = $configuration['PS_LGCOOKIES_USE_COOKIE_VAR'] ?
            (isset($_COOKIE[$lgcookieslaw_cookie_name]) ?
                $_COOKIE[$lgcookieslaw_cookie_name] :
                []) :
            $context->cookie->__get($lgcookieslaw_cookie_name);

        $lgcookieslaw_cookie_values = $this->decryptCookie($encrypted_lgcookieslaw_cookie_values ? $encrypted_lgcookieslaw_cookie_values : '');

        if (!empty($lgcookieslaw_cookie_values)) {
            $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposesLite((int) $id_lang, (int) $id_shop, true, true);

            $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes = [];

            foreach ($lgcookieslaw_purposes as $lgcookieslaw_purpose) {
                $id_lgcookieslaw_purpose = $lgcookieslaw_purpose['id'];

                if (isset($lgcookieslaw_cookie_values->{'lgcookieslaw_purpose_' . (int) $id_lgcookieslaw_purpose})
                    && (bool) $lgcookieslaw_cookie_values->{'lgcookieslaw_purpose_' . (int) $id_lgcookieslaw_purpose}
                ) {
                    $lgcookieslaw_cookie_values->lgcookieslaw_accepted_purposes[] = (int) $id_lgcookieslaw_purpose;
                }
            }
        }

        if ($add_user_consent_download_urls
            && !empty($lgcookieslaw_cookie_values)
            && (bool) $configuration['PS_LGCOOKIES_SAVE_USER_CONSENT']
        ) {
            $lgcookieslaw_cookie_values->lgcookieslaw_user_consent_download_url = $context->link->getModuleLink(
                $this->name,
                'download',
                [
                    'id_shop' => (int) $id_shop,
                    'download_hash' => $lgcookieslaw_cookie_values->lgcookieslaw_user_consent_download_hash,
                ],
                true
            );
        }

        return $lgcookieslaw_cookie_values;
    }

    public function encryptCookie($cookie_values)
    {
        $checksum = self::jsonEncode($cookie_values);

        return $this->cookie_cipher_tool->encrypt($checksum);
    }

    public function decryptCookie($cookie)
    {
        $cookie_values = self::jsonDecode($this->cookie_cipher_tool->decrypt($cookie));

        return $cookie_values;
    }

    public function processSaveUserPreferences()
    {
        $context = Context::getContext();

        $configuration = self::getModuleConfiguration();

        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $this->deleteCookies(false);

        $lgcookieslaw_purposes = LGCookiesLawPurpose::getPurposesLite((int) $id_lang, (int) $id_shop, true, false);

        $lgcookieslaw_cookie_values = [];

        $lgcookieslaw_accepted_purposes = Tools::getValue('accepted_purposes', []);

        foreach ($lgcookieslaw_purposes as $lgcookieslaw_purpose) {
            $lgcookieslaw_purpose_index =
                'lgcookieslaw_purpose_' . (int) $lgcookieslaw_purpose['id'];

            $lgcookieslaw_cookie_values[$lgcookieslaw_purpose_index] =
                in_array((int) $lgcookieslaw_purpose['id'], $lgcookieslaw_accepted_purposes);
        }

        $user_consent_ip_address = (bool) $configuration['PS_LGCOOKIES_ANONYMIZE_UC_IP'] ?
            self::anonymizeIPAddress(Tools::getRemoteAddr()) :
            Tools::getRemoteAddr();

        $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'] = date('Y-m-d H:i:s', time());

        do {
            $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'] = md5(
                $user_consent_ip_address .
                $id_shop .
                date('Y-m-d H:i:s') .
                bin2hex(openssl_random_pseudo_bytes(16))
            );

            $download_hash = $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'];
        } while (LGCookiesLawUserConsent::existDownloadHash($download_hash, (int) $id_shop));

        $lgcookieslaw_cookie_name = $configuration['PS_LGCOOKIES_COOKIE_NAME'];

        if ($configuration['PS_LGCOOKIES_USE_COOKIE_VAR']) {
            $lgcookies_cookie_timelife = time() + $configuration['PS_LGCOOKIES_COOKIE_TIMELIFE'];

            setcookie(
                $lgcookieslaw_cookie_name,
                $this->encryptCookie($lgcookieslaw_cookie_values),
                $lgcookies_cookie_timelife,
                '/'
            );
        } else {
            $context->cookie->$lgcookieslaw_cookie_name =
                $this->encryptCookie($lgcookieslaw_cookie_values);

            $context->cookie->write();
        }

        $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_url'] = false;

        if ((bool) $configuration['PS_LGCOOKIES_SAVE_USER_CONSENT']) {
            foreach ($lgcookieslaw_purposes as &$lgcookieslaw_purpose) {
                $id_lgcookieslaw_purpose = (int) $lgcookieslaw_purpose['id'];

                // $associated_cookies = LGCookiesLawCookie::getCookiesLiteByPurpose(
                //     (int) $id_lgcookieslaw_purpose,
                //     (int) $id_lang,
                //     (int) $id_shop,
                //     true
                // );

                // $lgcookieslaw_purpose['associated_cookies'] =
                //     !empty($associated_cookies) ? $associated_cookies : [];

                // c = checked
                $lgcookieslaw_purpose['c'] =
                    (int) in_array((int) $id_lgcookieslaw_purpose, $lgcookieslaw_accepted_purposes);
            }

            $lgcookieslaw_user_consent = new LGCookiesLawUserConsent();

            $lgcookieslaw_user_consent->download_hash =
                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'];
            $lgcookieslaw_user_consent->consent_date =
                $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_consent_date'];
            $lgcookieslaw_user_consent->ip_address = $user_consent_ip_address;
            $lgcookieslaw_user_consent->purposes = self::jsonEncode($lgcookieslaw_purposes);

            $lgcookieslaw_user_consent->save();

            $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_url'] = $context->link->getModuleLink(
                $this->name,
                'download',
                [
                    'id_shop' => (int) $id_shop,
                    'download_hash' => $lgcookieslaw_cookie_values['lgcookieslaw_user_consent_download_hash'],
                ],
                true
            );
        }

        return $lgcookieslaw_cookie_values;
    }

    public function ajaxProcessHideExternalModuleNotice()
    {
        $json = [
            'errors' => [],
        ];

        $external_module_name = Tools::getValue('external_module_name');

        $success = Validate::isModuleName($external_module_name);

        if ($success) {
            $context = Context::getContext();

            $id_shop = $context->shop->id;
            $id_shop_group = Shop::getGroupFromShop((int) $id_shop);

            $configuration = self::getModuleConfiguration();

            $noticed_modules = !empty($configuration['PS_LGCOOKIES_NOTICED_MODULES']) ?
                explode(',', $configuration['PS_LGCOOKIES_NOTICED_MODULES']) : [];

            $noticed_modules[] = $external_module_name;

            $success &= Configuration::updateValue(
                'PS_LGCOOKIES_NOTICED_MODULES',
                implode(',', $noticed_modules),
                false,
                (int) $id_shop_group,
                (int) $id_shop
            );
        }

        $json['status'] = (bool) $success;

        if ($success) {
            $json['message'] = $this->l('Your preferences have been saved successfully.');
        } else {
            $json['errors'][] = $this->l('Your preferences could not be saved.');
        }

        self::returnResponse($json);
    }

    /**
     * Set response code according to PHP version
     *
     * @param null $code
     * @return int|null
     */
    public static function httpResponseCode($code = null)
    {
        if (!function_exists('http_response_code')) {
            if ($code !== null) {
                switch ($code) {
                    case 100:
                        $text = 'Continue';

                        break;

                    case 101:
                        $text = 'Switching Protocols';

                        break;

                    case 200:
                        $text = 'OK';

                        break;

                    case 201:
                        $text = 'Created';

                        break;

                    case 202:
                        $text = 'Accepted';

                        break;

                    case 203:
                        $text = 'Non-Authoritative Information';

                        break;

                    case 204:
                        $text = 'No Content';

                        break;

                    case 205:
                        $text = 'Reset Content';

                        break;

                    case 206:
                        $text = 'Partial Content';

                        break;

                    case 300:
                        $text = 'Multiple Choices';

                        break;

                    case 301:
                        $text = 'Moved Permanently';

                        break;

                    case 302:
                        $text = 'Moved Temporarily';

                        break;

                    case 303:
                        $text = 'See Other';

                        break;

                    case 304:
                        $text = 'Not Modified';

                        break;

                    case 305:
                        $text = 'Use Proxy';

                        break;

                    case 400:
                        $text = 'Bad Request';

                        break;

                    case 401:
                        $text = 'Unauthorized';

                        break;

                    case 402:
                        $text = 'Payment Required';

                        break;

                    case 403:
                        $text = 'Forbidden';

                        break;

                    case 404:
                        $text = 'Not Found';

                        break;

                    case 405:
                        $text = 'Method Not Allowed';

                        break;

                    case 406:
                        $text = 'Not Acceptable';

                        break;

                    case 407:
                        $text = 'Proxy Authentication Required';

                        break;

                    case 408:
                        $text = 'Request Time-out';

                        break;

                    case 409:
                        $text = 'Conflict';

                        break;

                    case 410:
                        $text = 'Gone';

                        break;

                    case 411:
                        $text = 'Length Required';

                        break;

                    case 412:
                        $text = 'Precondition Failed';

                        break;

                    case 413:
                        $text = 'Request Entity Too Large';

                        break;

                    case 414:
                        $text = 'Request-URI Too Large';

                        break;

                    case 415:
                        $text = 'Unsupported Media Type';

                        break;

                    case 500:
                        $text = 'Internal Server Error';

                        break;

                    case 501:
                        $text = 'Not Implemented';

                        break;

                    case 502:
                        $text = 'Bad Gateway';

                        break;

                    case 503:
                        $text = 'Service Unavailable';

                        break;

                    case 504:
                        $text = 'Gateway Time-out';

                        break;

                    case 505:
                        $text = 'HTTP Version not supported';

                        break;

                    default:
                        $text = 'Unknown http status code "' . htmlentities($code) . '"';

                        break;
                }

                $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                header($protocol . ' ' . $code . ' ' . $text);

                $GLOBALS['http_response_code'] = $code;
            } else {
                $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            }

            return $code;
        } else {
            http_response_code($code);
        }
    }

    /**
     * Return AJAX response
     *
     * @param $response
     * @param int $status_code
     */
    public static function returnResponse($response, $status_code = 200)
    {
        if (!headers_sent()) {
            self::httpResponseCode($status_code);

            header('Content-Type: application/json');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        }

        if (!empty($response) || trim($response) != '' || !is_null($response)) {
            exit(self::jsonEncode($response));
        } else {
            exit;
        }
    }

    public static function anonymizeIPAddress($ip_address)
    {
        $anonymized_ip_address =
            preg_replace('/([0-9]+)\\.[0-9]+\\.[0-9]+\\.[0-9]+/', '\\1.XXX.XXX.XXX', $ip_address);

        return $anonymized_ip_address;
    }

    public function displayError($errors)
    {
        $context = Context::getContext();

        $context->smarty->assign('display_errors', $errors);
    }

    public function displayWarning($warnings)
    {
        $context = Context::getContext();

        $context->smarty->assign('display_warnings', $warnings);
    }

    public function displayConfirmation($confirmation)
    {
        $context = Context::getContext();

        $context->smarty->assign('display_confirmation', $confirmation);
    }

    public function displayInformation($informations)
    {
        $context = Context::getContext();

        $context->smarty->assign('display_informations', $informations);
    }

    /**
     * Generate a security token for AJAX calls
     *
     * @return string
     */
    public static function getToken($module_name)
    {
        return md5(
            _COOKIE_KEY_ .
            Configuration::get('PS_SHOP_NAME') .
            $module_name
        );
    }

    public static function jsonEncode($data, $options = 0, $depth = 512)
    {
        return method_exists('Tools', 'jsonEncode') ?
            Tools::jsonEncode($data) :
            json_encode($data, $options, $depth);
    }

    public static function jsonDecode($data, $assoc = false, $depth = 512, $options = 0)
    {
        return method_exists('Tools', 'jsonDecode') ?
            Tools::jsonDecode($data, $assoc) :
            json_decode($data, $assoc, $depth, $options);
    }

    public static function generateLogCSVLineFields($log_action)
    {
        $context = Context::getContext();

        $employee = $context->employee;

        $id_lang = $context->language->id;
        $id_shop = $context->shop->id;

        $configuration = self::getModuleConfiguration();
        $configuration_lang = self::getModuleConfigurationLang((int) $id_lang);

        $all_configuration = array_merge($configuration, $configuration_lang);

        $purposes = LGCookiesLawPurpose::getPurposes((int) $id_lang, (int) $id_shop);
        $cookies = LGCookiesLawCookie::getCookies((int) $id_lang, (int) $id_shop);

        $module_content = [
            'configuration' => $all_configuration,
            'purposes' => $purposes,
            'cookies' => $cookies,
        ];

        $fields = [
            (int) $employee->id . ' # ' . $employee->firstname . ' ' . $employee->lastname,
            $log_action,
            base64_encode(self::jsonEncode($module_content)),
            (int) $id_lang,
            (int) $id_shop,
            date('d-m-Y H:i:s'),
        ];

        return $fields;
    }

    public static function writeLog($fields)
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $file_path = _LGCOOKIESLAW_LOG_DIR_ . 'lgcookieslaw_log_' . (int) $id_shop . '%00.jpg.csv';
        $file = fopen($file_path, 'a');

        fputcsv($file, $fields, ';');

        fclose($file);
    }
}
