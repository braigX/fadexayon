<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

if (!defined('_PS_VERSION_')) {
	exit;
}

use PQNP\Config;
use PQNP\HistoryExporter;
use PQNP\Version;

ini_set('max_execution_time', '2880');

class AdminNewsletterPro extends AdminController
{
    /**
     * @var NewsletterPro
     */
    public $module = null;

    public $module_name = 'newsletterpro';

    private $np_errors = [];

    public $js_data = [];

    public $init_controllers = [
        'controller_front_subscription' => 'NewsletterProFrontSubscriptionController',
        'controller_template' => 'NewsletterProTemplateController',
        'product_selection' => 'NewsletterProProductSelectionController',
        'dev_mode' => 'NewsletterProDevModeController',
    ];

    public function dev()
    {
    }

    public function devInstallData()
    {
        header('Content-Type: text/plain');
        $errors = [];
        $install_data = NewsletterProInstallData::newInstance($this)->execute($errors);
        echo sprintf('SUCCESS: %s', $install_data ? 'Yes' : 'No')."\n\n";
        if (count($errors) > 0) {
            echo 'ERRORS: '."\n\n";
            print_r($this->module->upgrade->getErrors());
        }
        exit;
    }

    public function devLoadUpdate($version)
    {
        require_once 'upgrade/Upgrade-'.$version.'.php';

        $success = call_user_func_array('upgrade_module_'.str_replace('.', '_', $version), [$this->module]);

        header('Content-Type: text/plain');
        echo sprintf('VERSION: %s', $version)."\n\n";
        echo sprintf('SUCCESS: %s', $success ? 'Yes' : 'No')."\n\n";
        $errors = $this->module->upgrade->getErrors();
        if (count($errors) > 0) {
            echo 'ERRORS: '."\n\n";
            print_r($this->module->upgrade->getErrors());
        }
        exit;
    }

    public function devRenderTemplate($email, $id_lang = null)
    {
        $template = NewsletterproTemplate::newFile(pqnp_config('NEWSLETTER_TEMPLATE'), $email)->load();
        $message = $template->message(null, false, $id_lang, true);

        exit($message['body']);
    }

    public function devSyncChimp($customers = 1, $visitors = 1, $added = 1, $orders = 1)
    {
        $chimp = $this->module->chimp;

        $data = [
            'CUSTOMERS_CHECKBOX' => $customers,
            'VISITORS_CHECKBOX' => $visitors,
            'ADDED_CHECKBOX' => $added,
            'ORDERS_CHECKBOX' => $orders,
        ];

        $chimp->setSyncLists($data);
        NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', '0000-00-00 00:00:00');

        return NewsletterProTools::jsonDecode($chimp->startSyncLists(), true);
    }

    public function devGenerateCustomersAndOrders($customers, $visitors, $visitors_np, $added)
    {
        $count = NewsletterProGenerateCustomers::newInstance()->generate($customers, $visitors, $visitors_np, $added);
        NewsletterProGenerateOrders::newInstance()->generate();

        return $count;
    }

    public function __construct()
    {
        if (Module::isInstalled($this->module_name)) {
            $this->module = Module::getInstanceByName($this->module_name);
        }

        $this->bootstrap = $this->module->bootstrap;

        parent::__construct();

        // solve compatibillity errors
        if (!isset($this->controller_name)) {
            $this->controller_name = __CLASS__;
        }

        $this->initContext();
        $this->smartyConfiguration();
        $this->initControllers();
        $this->dev();
        if (Tools::isSubmit('postman')) {
            $postman = new NewsletterProPostman(Tools::getValue('postman'));
        }
    }

    public function initPageHeaderToolbar()
    {
        // remove the toolbar
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
            parent::initPageHeaderToolbar();

            return;
        }

        if (method_exists('AdminController', 'initPageHeaderToolbar')) {
            $page_header_toolbar = pqnp_config('PAGE_HEADER_TOOLBAR');

            if ($page_header_toolbar['CSV']) {
                $this->page_header_toolbar_btn['csv'] = [
                    'href' => '#csv',
                    'desc' => $this->module->l('CSV'),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#csv\')',
                    'icon' => 'icon-tab icon-file-excel-o',
                ];
            }

            if ($page_header_toolbar['MANAGE_IMAGES']) {
                $this->page_header_toolbar_btn['manage_images'] = [
                    'href' => '#manageImages',
                    'desc' => Translate::getModuleTranslation($this->module, 'Images', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#manageImages\')',
                    'icon' => 'icon-tab icon-picture-o',
                ];
            }

            if ($page_header_toolbar['SELECT_PRODUCTS']) {
                $this->page_header_toolbar_btn['select_products'] = [
                    'href' => '#selectProducts',
                    'desc' => Translate::getModuleTranslation($this->module, 'Products', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#selectProducts\')',
                    'icon' => 'icon-tab icon-search',
                ];
            }

            if ($page_header_toolbar['CREATE_TEMPLATE']) {
                $this->page_header_toolbar_btn['create_template'] = [
                    'href' => '#createTemplate',
                    'desc' => Translate::getModuleTranslation($this->module, 'Template', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#createTemplate\')',
                    'icon' => 'icon-tab icon-file-o',
                ];
            }

            if ($page_header_toolbar['SEND_NEWSLETTERS']) {
                $this->page_header_toolbar_btn['send_newsletters'] = [
                    'href' => '#sendNewsletters',
                    'desc' => Translate::getModuleTranslation($this->module, 'Send', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#sendNewsletters\')',
                    'icon' => 'icon-tab icon-send',
                ];
            }

            if ($page_header_toolbar['TASK']) {
                $this->page_header_toolbar_btn['task'] = [
                    'href' => '#task',
                    'desc' => Translate::getModuleTranslation($this->module, 'Task', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#task\')',
                    'icon' => 'icon-tab icon icon-clock-o',
                ];
            }

            if ($page_header_toolbar['HISTORY']) {
                $this->page_header_toolbar_btn['history'] = [
                    'href' => '#history',
                    'desc' => Translate::getModuleTranslation($this->module, 'History', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#history\')',
                    'icon' => 'icon-tab icon icon-book',
                ];
            }

            if ($page_header_toolbar['STATISTICS']) {
                $this->page_header_toolbar_btn['statistics'] = [
                    'href' => '#statistics',
                    'desc' => Translate::getModuleTranslation($this->module, 'Statistics', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#statistics\')',
                    'icon' => 'icon-tab icon icon-bar-chart',
                ];
            }

            if ($page_header_toolbar['CAMPAIGN']) {
                $this->page_header_toolbar_btn['campaign'] = [
                    'href' => '#campaign',
                    'desc' => Translate::getModuleTranslation($this->module, 'Campaign', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#campaign\')',
                    'icon' => 'icon-tab icon icon-line-chart',
                ];
            }

            if ($page_header_toolbar['SMTP']) {
                $this->page_header_toolbar_btn['smtp'] = [
                    'href' => '#smtp',
                    'desc' => Translate::getModuleTranslation($this->module, 'E-mail', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#smtp\')',
                    'icon' => 'icon-tab icon icon-envelope',
                ];
            }

            if ($page_header_toolbar['MAILCHIMP']) {
                $this->page_header_toolbar_btn['mailchimp'] = [
                    'href' => '#mailchimp',
                    'desc' => Translate::getModuleTranslation($this->module, 'MailChimp', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#mailchimp\')',
                    'icon' => 'icon-tab icon icon-refresh',
                ];
            }

            if ($page_header_toolbar['FORWARD']) {
                $this->page_header_toolbar_btn['forward'] = [
                    'href' => '#forward',
                    'desc' => Translate::getModuleTranslation($this->module, 'Forwarders', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#forward\')',
                    'icon' => 'icon-tab icon icon-mail-forward',
                ];
            }

            if ($page_header_toolbar['FRONT_SUBSCRIPTION']) {
                $this->page_header_toolbar_btn['front_subscription'] = [
                    'href' => '#frontSubscription',
                    'desc' => Translate::getModuleTranslation($this->module, 'Subscription', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#frontSubscription\')',
                    'icon' => 'icon-tab icon icon-envelope-o',
                ];
            }

            if ($page_header_toolbar['SETTINGS']) {
                $this->page_header_toolbar_btn['settings'] = [
                    'href' => '#settings',
                    'desc' => Translate::getModuleTranslation($this->module, 'Settings', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#settings\')',
                    'icon' => 'icon-tab icon icon-gear',
                ];
            }

            if ($page_header_toolbar['TUTORIALS']) {
                $this->page_header_toolbar_btn['tutorials'] = [
                    'href' => '#tutorials',
                    'desc' => Translate::getModuleTranslation($this->module, 'Tutorials', __CLASS__),
                    'js' => 'NewsletterProComponents.objs.tabItems.triggerHref(\'#tutorials\')',
                    'icon' => 'icon-tab icon icon-video-camera',
                ];
            }

            $this->page_header_toolbar_btn['our_modules'] = [
                'href' => 'http://addons.prestashop.com/'.$this->context->language->iso_code.'/93_proquality',
                'target' => true,
                'desc' => Translate::getModuleTranslation($this->module, 'Modules', __CLASS__),
                'icon' => 'icon-tab icon icon-puzzle-piece',
            ];
            /*
            // Removed because of the large size of the PDF
            $this->page_header_toolbar_btn['documentation'] = array(
                'href' => $this->module->url_location.'readme_en.pdf',
                'target' => true,
                'desc' => Translate::getModuleTranslation($this->module, 'Doc', __CLASS__),
                'icon' => 'icon-tab icon icon-file-pdf-o',
            );
            */

            parent::initPageHeaderToolbar();
        }
    }

    public function initControllers()
    {
        foreach ($this->init_controllers as $variable => $class_name) {
            $this->{$variable} = new $class_name();
        }
    }

    public function smartyConfiguration()
    {
        if ((int) pqnp_config('DEBUG_MODE')) {
            NewsletterProAjaxController::enableForceCompile($this->context->smarty);
        } else {
            // disable the smarty compilation when is an ajax request
            if (NewsletterProAjaxController::isXHR()) {
                NewsletterProAjaxController::disableForceCompile($this->context->smarty);
            }
        }
    }

    public function viewAccess($disable = false)
    {
        if (version_compare(_PS_VERSION_, '1.5.1.0', '<=')) {
            return true;
        }

        return parent::viewAccess($disable);
    }

    public function displayErrors()
    {
        $this->context->smarty->assign([
            'errors' => $this->_errors,
        ]);

        echo $this->context->smarty->fetch(pqnp_template_path($this->module->dir_location.'views/templates/admin/ps_tab_error.tpl'));
    }

    public function isJSLowerVersion($string)
    {
        $js_version = explode('.', _PS_JQUERY_VERSION_);
        $js_str_version = explode('.', $string);

        foreach ($js_version as $key => $value) {
            if (isset($js_str_version[$key])) {
                $js_str_val = $js_str_version[$key];

                if ((int) $value < (int) $js_str_val) {
                    return true;
                } elseif ((int) $value > (int) $js_str_val) {
                    return false;
                }
            } else {
                break;
            }
        }

        return false;
    }

    private function initContext()
    {
        $this->context = Context::getContext();
        $this->context->cookie->setExpire(strtotime('+1 months'));
    }

    public function getAjaxLink()
    {
        return $this->getLink();
    }

    public function initContent()
    {
        if (null == $this->module) {
            $this->content = $this->l('This section is available after installing the module "Newsletter Pro"');
            parent::initContent();

            return false;
        } elseif (isset($this->module->exception_message) && $this->module->exception_message) {
            // this will protect the front office for the uncatched exceptions
            $this->content = $this->module->exception_message;
            parent::initContent();

            return false;
        }

        $loaded_extensions = get_loaded_extensions();

        if (!in_array('mbstring', $loaded_extensions)) {
            $this->np_errors[] = sprintf($this->l('The php extension "%s" is not installed on your server. For a better performance ask your hosting provider to install it.'), 'mbstring');
        }

        $update_assign = $this->module->getUpdateDetails();
        $this->context->smarty->assign([
            'tpl_location' => $this->module->dir_location.'views/',
            'update' => $update_assign,
            'AJAX_URL' => $this->getAjaxLink(),
            'jsData' => pqnp_addcslashes(NewsletterProTools::jsonEncode([
                'ajax_url' => $this->getAjaxLink(),
            ])),
            'module_url' => $this->module->uri_location,
        ]);

        if ((bool) $update_assign['needs_update']) {
            Config::write('LEFT_MENU_ACTIVE', '1');
        }

        try {
            $languages = Language::getLanguages(true, $this->context->shop->id);
            $languages_js = [];
            $languages_need_update = true;
            foreach ($languages as $key => &$language) {
                $language['img_path'] = $this->module->getLangImageById($language['id_lang']);

                if ((string) $language['id_lang'] == (string) pqnp_config('LANG')) {
                    $languages_need_update = false;
                    $language['selected'] = true;
                } else {
                    $language['selected'] = false;
                }

                $languages_js[$key]['id_lang'] = $language['id_lang'];
                $languages_js[$key]['name'] = $language['name'];
                $languages_js[$key]['iso_code'] = $language['iso_code'];
                $languages_js[$key]['img_path'] = $language['img_path'];
                $languages_js[$key]['selected'] = $language['selected'];
            }

            if ($languages_need_update && isset($languages[0])) {
                $languages[0]['img_path'] = $this->module->getLangImageById($languages[0]['id_lang']);

                $languages_js[0]['id_lang'] = $languages[0]['id_lang'];
                $languages_js[0]['name'] = $languages[0]['name'];
                $languages_js[0]['iso_code'] = $languages[0]['iso_code'];
                $languages_js[0]['img_path'] = $languages[0]['img_path'];
                $languages_js[0]['selected'] = $languages[0]['selected'];

                $languages[0]['selected'] = true;
                pqnp_config('LANG', $languages[0]['id_lang']);
            }

            $currencies = NewsletterPro::getCurrenciesByIdShop($this->context->shop->id);

            $currencies_js = [];
            $currencies_need_update = true;
            foreach ($currencies as $key => &$currency) {
                if ((string) $currency['id_currency'] == (string) pqnp_config('CURRENCY')) {
                    $currencies_need_update = false;
                    $currency['selected'] = true;
                } else {
                    $currency['selected'] = false;
                }

                $currencies_js[$key]['id_currency'] = $currency['id_currency'];
                $currencies_js[$key]['name'] = $currency['name'];
                $currencies_js[$key]['iso_code'] = $currency['iso_code'];
                $currencies_js[$key]['iso_code_num'] = $currency['iso_code_num'];
                $currencies_js[$key]['sign'] = $currency['sign'];
                $currencies_js[$key]['selected'] = $currency['selected'];
            }

            if ($currencies_need_update && isset($currencies[0])) {
                $currencies_js[0]['id_currency'] = $currencies[0]['id_currency'];
                $currencies_js[0]['name'] = $currencies[0]['name'];
                $currencies_js[0]['iso_code'] = $currencies[0]['iso_code'];
                $currencies_js[0]['iso_code_num'] = $currencies[0]['iso_code_num'];
                $currencies_js[0]['sign'] = $currencies[0]['sign'];
                $currencies_js[0]['selected'] = $currencies[0]['selected'];

                $currencies[0]['selected'] = true;
                pqnp_config('CURRENCY', $currencies[0]['id_currency']);
            }

            // load the default template
            try {
                $template = NewsletterProTemplate::newFile(pqnp_config('NEWSLETTER_TEMPLATE'))->load();
            } catch (NewsletterProTemplateException $e) {
                $template = NewsletterProTemplate::newFile('default.html')->load();
                pqnp_config('NEWSLETTER_TEMPLATE', 'default.html');
            }

            $product_template_path = $this->module->tpl_location.'product/'.pqnp_config('PRODUCT_TEMPLATE');

            if (!file_exists($product_template_path)) {
                pqnp_config('PRODUCT_TEMPLATE', 'default.html');
            }

            $product_tpl_content = $this->getProductTplContent();

            $jquery_date_format = $this->module->dateToJQuery($this->context->language->date_format_lite);
            $jquery_date_format_full = $this->module->dateToJQuery($this->context->language->date_format_full);

            $d_birthday = ['L', 'o', 'Y', 'y'];

            $d_split = preg_split('/\W+/', trim($this->context->language->date_format_lite));
            preg_match('/(?P<delimitor>\W+)/', trim($this->context->language->date_format_lite), $d_match);
            if (isset($d_match['delimitor'])) {
                $d_delimitor = $d_match['delimitor'];

                $d_birthday_lite = implode($d_delimitor, array_diff($d_split, $d_birthday));
            }

            if (isset($d_birthday_lite)) {
                $jquery_date_birthday = $this->module->dateToJQuery($d_birthday_lite);
            } else {
                $jquery_date_birthday = $this->module->dateToJQuery($this->context->language->date_format_lite);
            }

            $tutorial_link = 'https://www.youtube.com/watch?v=NAbw7HhJfSo';

            if ('fr' == $this->context->language->iso_code) {
                $tutorial_link = 'https://www.youtube.com/watch?v=f6HMi4-k3EA';
            }

            if ('es' == $this->context->language->iso_code) {
                $tutorial_link = 'https://www.youtube.com/watch?v=dUddLn4QAJA';
            }

            if (!((bool) pqnp_config('SEND_ANTIFLOOD_ACTIVE')) && !((bool) pqnp_config('SEND_THROTTLER_ACTIVE'))) {
                pqnp_config('SEND_ANTIFLOOD_ACTIVE', 1);
            }

            $show_custom_columns_format = [];

            $show_custom_columns = pqnp_config('SHOW_CUSTOM_COLUMNS');
            if (!empty($show_custom_columns)) {
                foreach (pqnp_config('SHOW_CUSTOM_COLUMNS') as $name) {
                    $show_custom_columns_format[$name] = $this->formatColumn($name);
                }
            }

            $locale = isset($this->context->language->locale) ? $this->context->language->locale : $this->context->language->language_code;

            $language_info = [
                'id' => (int) $this->context->language->id,
                'iso' => $this->context->language->iso_code,
                'locale' => $locale,
                'name' => $this->context->language->name,
            ];

            $this->js_data = [
                'ps_version' => _PS_VERSION_,
                'csv_export_list_ref' => [
                    'LIST_CUSTOMERS' => NewsletterPro::LIST_CUSTOMERS,
                    'LIST_VISITORS' => NewsletterPro::LIST_VISITORS,
                    'LIST_VISITORS_NP' => NewsletterPro::LIST_VISITORS_NP,
                    'LIST_ADDED' => NewsletterPro::LIST_ADDED,
                ],
                'bootstrap' => (int) $this->module->bootstrap,
                'isPS17' => NewsletterProTools::is17(),
                'token_newsletterpro' => NewsletterPro::getNewsletterProToken(),
                'bounce_link' => NewsletterProApi::getLink('bounce', ['bounceAction' => 'unsubscribe', 'email' => 'demo@demo.com'], true, true),
                'module_url' => $this->module->url_location,
                'shop_logo_url' => $this->module->getShopLogoUrl(),
                'jquery_date_format' => $jquery_date_format,
                'jquery_date_format_full' => $jquery_date_format_full,
                'jquery_date_birthday' => $jquery_date_birthday,
                'all_smtp' => $this->module->getAllSMTP(),
                'templates' => $this->getNewsletterDirTemplates(),
                'subscription_template_defined' => NewsletterProSubscriptionTpl::getDefined(),
                'subscription_controller_templates' => NewsletterProSubscriptionTpl::getControllerTemplates(),
                'filter_groups' => $this->getGroups(),
                'filter_shops' => $this->getShops(),
                'csv_name' => $this->getCSVNameFilters(),
                'filter_languages' => $this->getLanguages(),
                'filter_genders' => $this->getGenders(),
                'languages' => $languages_js,
                'language' => $language_info,
                'currencies' => $currencies_js,
                'images_size' => $this->getImagesSize(),
                'product_tpl_nr' => (int) $product_tpl_content['product_nr'],
                'product_tpl_header' => $product_tpl_content['header'],
                'is_product_template' => false,
                'currency_default' => get_object_vars(new Currency((int) pqnp_config('PS_CURRENCY_DEFAULT'))),
                'ajax_url' => $this->getAjaxLink(),
                'product_template' => $this->getProductTemplate(),
                'new_image_size_link' => $this->_getAdminTabLink('AdminImages'),
                'isPS16' => (int) $this->module->isPS16(),
                'chimpIsInstalled' => (int) $this->module->chimp->isInstalled(),
                'chimp_config' => pqnp_config('CHIMP'),
                'chimp_sync' => NewsletterProConfig::getArray('CHIMP_SYNC'),
                'smtp_active' => (int) pqnp_config('SMTP_ACTIVE'),
                'all_languages' => $this->getAllLanguages(false),
                'all_active_languages' => $this->getAllLanguages(),
                'id_current_lang' => $this->getCurrentLangId(),
                'module_img_path' => $this->module->uri_location.'views/img/',
                'default_lang' => (int) pqnp_config('PS_LANG_DEFAULT'),
                'view_active_only' => (int) pqnp_config('VIEW_ACTIVE_ONLY'),
                'left_menu_active' => (int) pqnp_config('LEFT_MENU_ACTIVE'),
                'email_sleep' => (int) pqnp_config('SLEEP'),
                'categories_list' => $this->getCategories(),
                'configuration' => [
                    'NEWSLETTER_TEMPLATE' => pqnp_config('NEWSLETTER_TEMPLATE'),
                    'SLEEP' => (int) pqnp_config('SLEEP'),
                    'SEND_METHOD' => (int) pqnp_config('SEND_METHOD'),
                    'SEND_ANTIFLOOD_ACTIVE' => (int) pqnp_config('SEND_ANTIFLOOD_ACTIVE'),
                    'SEND_ANTIFLOOD_EMAILS' => (int) pqnp_config('SEND_ANTIFLOOD_EMAILS'),
                    'SEND_ANTIFLOOD_SLEEP' => (int) pqnp_config('SEND_ANTIFLOOD_SLEEP'),
                    'SEND_THROTTLER_ACTIVE' => (int) pqnp_config('SEND_THROTTLER_ACTIVE'),
                    'SEND_THROTTLER_LIMIT' => (int) pqnp_config('SEND_THROTTLER_LIMIT'),
                    'SEND_THROTTLER_TYPE' => (int) pqnp_config('SEND_THROTTLER_TYPE'),
                    'PS_SHOP_EMAIL' => pqnp_config('PS_SHOP_EMAIL'),
                    'CROSS_TYPE_CLASS' => pqnp_config('CROSS_TYPE_CLASS'),
                    'SHOW_CLEAR_CACHE' => pqnp_config('SHOW_CLEAR_CACHE'),
                    'SHOW_CUSTOM_COLUMNS' => pqnp_config('SHOW_CUSTOM_COLUMNS'),
                    'CURRENCY' => pqnp_config('CURRENCY'),
                    'PS_CURRENCY_DEFAULT' => pqnp_config('PS_CURRENCY_DEFAULT'),
                    'IMAGE_TYPE' => pqnp_config('IMAGE_TYPE'),
                    'DISPLAY_PRODUCT_IMAGE' => pqnp_config('DISPLAY_PRODUCT_IMAGE'),
                    'SUBSCRIPTION_ACTIVE' => pqnp_config('SUBSCRIPTION_ACTIVE'),
                    'VIEW_ACTIVE_ONLY' => pqnp_config('VIEW_ACTIVE_ONLY'),
                    'SEND_LIMIT_END_SCRIPT' => pqnp_config('SEND_LIMIT_END_SCRIPT'),
                ],
                'custom_field' => [
                    'types_cost' => NewsletterProSubscribersCustomField::getTypesJs(),
                    'types' => NewsletterProSubscribersCustomField::getTypes(),
                    'types_editable' => NewsletterProSubscribersCustomField::getEditableTypes(),
                    'variables' => NewsletterProSubscribersCustomField::getVariables(),
                    'variables_types' => NewsletterProSubscribersCustomField::getVariablesWithTypes(),
                    'current_field_id' => 0,                                                              // this value is set later in javascript
                ],
                'search_conditions' => [
                    'conditions' => NewsletterPro::getSearchConstions(),
                    'conditions_const' => NewsletterPro::getSearchConstionsJs(),
                    'visitors_np_columns' => $this->module->getVisitorsNpColumns(),
                    'selected_condition' => null,
                    'selected_field' => null,
                ],
                'count_send_connections' => NewsletterProSendConnection::countConnections(),
            ];

            $this->context->smarty->assign([
                'pqnpModuleVersion' => $this->module->version,
                'pqnp_upgrade_warning' => NewsletterProUpgrade::getWarningContent(),
                'cron_link' => NewsletterProApi::getLink('task', [], true, true),
                'sync_chimp_link' => NewsletterProApi::getLink('syncChimp', [], true, true),
                'sync_newsletter_block' => NewsletterProApi::getLink('syncNewsletterBlock', [], true, true),
                'webhook_chimp_link' => NewsletterProApi::getLink('mailchimp', [], true, true),
                'textarea_tpl' => pqnp_template_path($this->module->dir_location.'views/templates/admin/textarea.tpl'),
                'textarea_tpl_multilang' => pqnp_template_path($this->module->dir_location.'views/templates/admin/textarea_multilang.tpl'),
                'import_details_tpl' => pqnp_template_path($this->module->dir_location.'views/templates/admin/import_details.tpl'),
                'dir_location' => $this->module->dir_location,
                'css_path' => $this->module->getCssPath(),
                'mails_path' => $this->module->uri_location.'mail_templates/',
                'css_mails_path' => $this->module->uri_location.'views/css/mails/',
                'new_image_size_link' => $this->_getAdminTabLink('AdminImages'),
                'iso_tiny_mce' => (file_exists(_PS_JS_DIR_.'tiny_mce/langs/'.$this->context->language->iso_code.'.js') ? $this->context->language->iso_code : 'en'),
                'lang_iso_code' => $this->context->language->iso_code,
                'ad' => dirname($_SERVER['PHP_SELF']),
                'controller_path' => $this->getLink(),
                'fix_document_write' => (int) pqnp_ini_config('fix_document_write'),
                'isPS16' => $this->module->isPS16(),
                'isPS17' => NewsletterProTools::is17(),
                'newsletter_template' => $template->htmlInvert(),
                'newsletter_table_exists' => NewsletterProDefaultNewsletterTable::getTableName(),
                'product_tpl_nr' => $product_tpl_content['product_nr'],
                'product_tpl_content' => $product_tpl_content['content'],
                'images_size' => $this->getImagesSize(),
                'newsletter_template_list' => $this->getNewsletterTemplates(),
                'product_template_list' => $this->getProductTemplates(),
                'filters_selection' => NewsletterProFiltersSelection::getFilters(),
                'shop_email' => pqnp_config('PS_SHOP_EMAIL'),
                'display_product_image' => pqnp_config('DISPLAY_PRODUCT_IMAGE'),
                'newsletter_templates' => $this->getNewsletterDirTemplates(),
                'product_templates' => $this->getProductDirTemplates(),
                'email_sleep' => pqnp_config('SLEEP'),
                'module_img_path' => $this->module->uri_location.'views/img/',
                'currencies' => $currencies,
                'languages' => $languages,
                'all_languages' => $this->getAllLanguages(false),
                'all_active_languages' => $this->getAllLanguages(),
                'default_lang' => (int) pqnp_config('PS_LANG_DEFAULT'),
                'groups' => $this->getGroups(),
                'pqShops' => $this->getShops(),
                'page_link' => $this->module->link,
                'users_lists_shop_count_message' => $this->getUsersListsShopCountMessage(),
                'CONFIGURATION' => Config::get(),
                'INI_CONFIGURATION' => [
                    'demo_mode' => (bool) pqnp_ini_config('demo_mode'),
                ],
                'CAMPAIGN_PARAMETERS' => Configuration::get('NEWSLETTER_PRO_CAMPAIGN'),
                'csv_import_files' => $this->getCSVFiles($this->module->dir_location.'csv/import/'),
                'href_replace' => Tools::isSubmit('downloadImportSample'),
                'tutorial_link' => $tutorial_link,
                'clear_cache' => method_exists('Tools', 'clearSmartyCache'),
                'fwd_limit' => NewsletterProForward::FOREWORD_LIMIT,
                'log_files' => $this->getLogFiles(),
                'last_date_chimp_sync' => NewsletterProConfig::get('LAST_DATE_CHIMP_SYNC'),
                'chimp_last_date_sync_orders' => NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS'),
                'exclusion_emails_count' => NewsletterProEmailExclusion::newInstance()->countList(),
                'subscribe_hooks' => $this->module->getNewsletterProSubscriptionHooks(),
                'blocknewsletter_info' => $this->module->getBlockNewsletterInfo(),
                'show_custom_columns_format' => $show_custom_columns_format,
                'np_errors' => $this->np_errors,
                'load_subscription_hook_header' => (int) pqnp_ini_config('load_subscription_hook_header'),
                'subscription_controller_link' => $this->context->link->getModuleLink($this->module->name, 'subscription', []),
            ]);

            // assign controller content
            // for example use the smarty variables {$CONTROLLER_FRONT_SUBSCRIPTION}
            foreach (array_keys($this->init_controllers) as $controller_instance) {
                $this->{$controller_instance}->initContent();

                if (!empty($this->{$controller_instance}->js_data)) {
                    $this->js_data = array_merge($this->js_data, $this->{$controller_instance}->js_data);
                }

                $this->context->smarty->assign([
                    Tools::strtoupper($controller_instance) => $this->{$controller_instance}->getContent(),
                ]);
            }

            $this->context->smarty->assign([
                'jsData' => pqnp_addcslashes(NewsletterProTools::jsonEncode($this->js_data)),
            ]);

            $this->content .= $this->context->smarty->fetch(pqnp_template_path($this->module->dir_location.'views/templates/admin/newsletter.tpl'));
        } catch (Exception $e) {
            $this->context->smarty->assign([
                'errors' => $e->getMessage().' - '.$e->getFile().' - '.$e->getLine().'<br>'.str_replace("\n", '<br>', $e->getTraceAsString()),
            ]);

            $this->content .= $this->context->smarty->fetch(pqnp_template_path($this->module->dir_location.'views/templates/admin/fatal_error.tpl'));
        }

        parent::initContent();
    }

    public static function getCSSMedia()
    {
        $module = NewsletterPro::getInstance();

        return [
            _PS_JS_DIR_.'jquery/ui/themes/bsase/jquery.ui.all.css',
            _PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css',

            $module->uri_location.'views/css/'.(version_compare(_PS_VERSION_, '1.6.0.0', '<') ? NewsletterProTools::getMin('app_style_15.css') : NewsletterProTools::getMin('app_style_16.css')),
            $module->uri_location.'views/css/'.NewsletterProTools::getMin('app.css'),
        ];
    }

    public static function getJSMedia()
    {
        return [
            NewsletterPro::getInstance()->uri_location.'views/js/'.NewsletterProTools::getMin('app.js'),
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        // for prestashop 1.6, 1.7 for 1.5 is in the function NewsletterPro->hookBackOfficeHeader()
        if (method_exists('Media', 'addJsDef')) {
            Media::addJsDef([
                'NewsletterProAppStorage' => NewsletterProAppStorage::get('app'),
                'NewsletterProAppTranslate' => NewsletterProAppTranslate::get('app'),
            ]);
        }

        if (NewsletterPro::isUniformRequired()) {
            NewsletterPro::includeUniform($this->context->controller);
        }

        $css_path = $this->module->getCssPath();

        $this->addCss(self::getCSSMedia());

        if ($this->module->bootstrap) {
            $this->addCss([
                $this->module->uri_location.'views/css/bootstrap/newsletterpro.css',
            ]);
        }

        if (NewsletterPro::isFontAwesomeRequired()) {
            NewsletterPro::includeFontAwesome($this->context->controller);
        }

        if (Version::isLower('1.6.0.0')) {
            $this->addCss([
                $this->module->uri_location.'views/css/timepicker/jquery-ui-timepicker-addon.css',
                $this->module->uri_location.'views/css/1.5/our_modules_fix.css',
            ]);
        }

        if ($this->isJSLowerVersion('1.7.2')) {
            Controller::addJquery('1.7.2', $this->module->uri_location.'views/js/');
        }

        $this->addJqueryUI('ui.core');
        $this->addJqueryUI('ui.datepicker');
        $this->addJqueryUI('ui.sortable');
        $this->addJqueryUI('ui.slider');

        $this->addJS([
            $this->module->uri_location.'views/js/jscolor/jscolor.js',
        ]);

        if (Version::isLower('1.6.0.0')) {
            $this->addJS([
                $this->module->uri_location.'views/js/timepicker/jquery-ui-timepicker-addon.js',
            ]);
        }

        $this->addJS(self::getJSMedia());

        foreach (array_keys($this->init_controllers) as $controller_instance) {
            $this->{$controller_instance}->setMedia();
        }

        // solve the tinymce path for prestashop 1.6.0.12 and higher
        $tiny_path = (file_exists(_PS_ROOT_DIR_.'/js/admin/tinymce.inc.js') ? _PS_JS_DIR_.'admin/tinymce.inc.js' : _PS_JS_DIR_.'tinymce.inc.js');

        $this->addJS([
            _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
            $tiny_path,
        ]);

        // $this->module->addTreeViewFiles($this);

        $this->addJS([
            // $this->module->uri_location.'views/js/categories-tree.js',
            _PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js',
        ]);
    }

    private function _getAdminTabLink($tab_name)
    {
        return "index.php?controller={$tab_name}&token=".Tools::getAdminToken($tab_name.(int) Tab::getIdFromClassName($tab_name).(int) $this->context->cookie->id_employee);
    }

    private function getProductTplContent()
    {
        $template = pqnp_config('PRODUCT_TEMPLATE');
        $path = $this->module->dir_location.'mail_templates/product/'.$template;
        if (false != $template && file_exists($path)) {
            $data = Tools::file_get_contents($path);

            if (preg_match('/\{columns=\d+\}/', $data, $product_nr)) {
                $product_nr = str_replace(['{columns=', '}'], '', $product_nr[0]);
            } else {
                $product_nr = '';
            }

            // remove comments
            // $content = trim(preg_replace('/<!--[\s\S]*?-->/', '', $data));

            // remove columns for the old templates
            // $content = preg_replace('/\{columns=\d+\}/', '', $content);

            $content = $data;

            $header_content = '';
            if (preg_match('/<!-- start header -->\s*?<!--(?P<header>[\s\S]*)-->\s*?<!-- end header -->/', $content, $match)) {
                $header_content = trim($match['header']);
            }

            $image_type = pqnp_config('IMAGE_TYPE');

            $img_name = (string) $this->context->language->iso_code.'-default-'.$image_type.'.jpg';
            $image_path = _PS_PROD_IMG_DIR_.$img_name;

            if (file_exists($image_path)) {
                $image_path = Tools::getHttpHost(true)._THEME_PROD_DIR_.$img_name;
            } else {
                $files = scandir(_PS_PROD_IMG_DIR_);
                $files = preg_grep('/^('.$this->context->language->iso_code.').*'.$image_type.'.*.jpg$/', $files);

                $img_name = array_values($files);
                $image_path = isset($img_name[0]) ? $img_name[0] : '';
                $image_path = Tools::getHttpHost(true)._THEME_PROD_DIR_.$image_path;
            }

            $size = Image::getSize($image_type);

            if (!preg_match('/content\s+=\s+template/', $header_content)) {
                $content = str_replace(['{image_path}', '{image_width}', '{image_height}'], [$image_path, $size['width'], $size['height']], $content);
            }

            return ['content' => $content, 'product_nr' => $product_nr, 'header' => $header_content];
        }

        return false;
    }

    private function getNewsletterTemplates()
    {
        return $this->controller_template->getNewsletterTemplates(false);
    }

    private function getProductTemplates()
    {
        $path = $this->module->dir_location.'mail_templates/product/';
        if (file_exists($path)) {
            $files = scandir($path);
            $files = preg_grep('/^.*.html$/', $files);

            $template_db = pqnp_config('PRODUCT_TEMPLATE');

            foreach ($files as $key => $template_name) {
                unset($files[$key]);
                if ($template_name == $template_db) {
                    $files[$key]['selected'] = true;
                    $files[$key]['name'] = $template_name;
                } else {
                    $files[$key]['selected'] = false;
                    $files[$key]['name'] = $template_name;
                }
            }

            return $files;
        }

        return false;
    }

    private function getProductTemplate()
    {
        $tempalte = pqnp_config('PRODUCT_TEMPLATE');

        $path = $this->module->dir_location.'mail_templates/product/'.$tempalte;

        $content = '';
        if (file_exists($path)) {
            $content = Tools::file_get_contents($path);
        }

        return $content;
    }

    private function getImagesSize()
    {
        $sql = 'SELECT `id_image_type` as `id`, `name`, `width`, `height` FROM `'._DB_PREFIX_.'image_type` WHERE `products` = 1 ORDER BY `width` ASC;';
        $images = Db::getInstance()->executeS($sql);

        $image_type = pqnp_config('IMAGE_TYPE');

        foreach ($images as $key => $image) {
            if ($image['name'] == $image_type) {
                $images[$key]['selected'] = true;
            } else {
                $images[$key]['selected'] = false;
            }
        }

        return $images;
    }

    private function getCategories()
    {
        $id_lang = is_numeric(pqnp_config('LANG')) ? (int) pqnp_config('LANG') : (int) Configuration::get('PS_LANG_DEFAULT');

        if (Version::isBetween('1.5.0.0', '1.5.0.4')) {
            $sql = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`
					FROM `'._DB_PREFIX_.'category` c
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` =c.`id_category`
					WHERE c.`active` = 1
					AND cl.`id_lang` = '.(int) $id_lang.'
					AND cl.`id_shop` = '.(int) $this->context->shop->id.'
					AND c.`level_depth` = "'.pSQL(pqnp_config('CATEGORIES_DEPTH')).'"
					ORDER BY c.`level_depth` ASC, c.`position` ASC;';
        } else {
            $sql = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`
					FROM `'._DB_PREFIX_.'category` c
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` =c.`id_category`
					INNER JOIN `'._DB_PREFIX_.'category_shop` cs
						ON (cs.`id_category` = c.`id_category`
							AND cl.`id_shop` = cs.`id_shop`
							AND cs.`id_shop` = '.(int) $this->context->shop->id.')
					WHERE c.`active` = 1
					AND cl.`id_lang` = '.(int) $id_lang.'
					AND c.`level_depth` = "'.pSQL(pqnp_config('CATEGORIES_DEPTH')).'"
					ORDER BY c.`level_depth` ASC, cs.`position` ASC;';
        }

        $result = Db::getInstance()->executeS($sql);
        $result = is_array($result) ? $result : [];

        $this->_subcategories = $this->getSubcategories();

        $level = pqnp_config('CATEGORIES_DEPTH');

        $this->scanSubcategories($result, $level);

        return $result;
    }

    private function getSubcategories()
    {
        $id_lang = is_numeric(pqnp_config('LANG')) ? (int) pqnp_config('LANG') : (int) Configuration::get('PS_LANG_DEFAULT');

        if (Version::isBetween('1.5.0.0', '1.5.0.4')) {
            $sql = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, c.`id_parent`
					FROM `'._DB_PREFIX_.'category` c
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` = c.`id_category`
					WHERE c.`active` = 1
					AND cl.`id_lang` = '.(int) $id_lang.'
					AND cl.`id_shop` = '.(int) $this->context->shop->id.'
					ORDER BY c.`position` ASC;';
        } else {
            $sql = 'SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, c.`id_parent`
					FROM `'._DB_PREFIX_.'category` c
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` = c.`id_category`
					INNER JOIN `'._DB_PREFIX_.'category_shop` cs
						ON (cs.`id_category` = c.`id_category`
							AND cl.`id_shop` = cs.`id_shop`
							AND cs.`id_shop` = '.(int) $this->context->shop->id.')
					WHERE c.`active` = 1
					AND cl.`id_lang` = '.(int) $id_lang.'
					ORDER BY cs.`position` ASC;';
        }

        return Db::getInstance()->executeS($sql);
    }

    private function scanSubcategories(&$result, $level)
    {
        foreach ($result as $key => $category) {
            $result[$key]['level'] = $level;
            $result[$key]['subcategory'] = $this->filterCategory($category['id_category']);

            if (!empty($result[$key]['subcategory'])) {
                $subcategory = &$result[$key]['subcategory'];
                $this->scanSubcategories($subcategory, ++$level);
            }
        }
        --$level;
    }

    private function filterCategory($id_category)
    {
        $filtred = [];

        foreach ($this->_subcategories as $subcategory) {
            if ($subcategory['id_parent'] == $id_category) {
                $filtred[] = $subcategory;
            }
        }

        return $filtred;
    }

    private function getUsersListsShopCountMessage()
    {
        $shop_list = NewsletterProTools::getActiveShops();

        $case = 'all';
        $array_keys = array_keys($shop_list);
        $first_key = array_shift($array_keys);
        $id_shop_group = 1;
        foreach (array_keys($shop_list) as $key) {
            if ($key == $first_key) {
                $id_shop_group = $shop_list[$key]['id_shop_group'];
            }

            if ($id_shop_group != $shop_list[$key]['id_shop_group']) {
                $case = 'all';
                break;
            } else {
                if (count($shop_list) > 1) {
                    $case = 'group';
                } else {
                    $case = 'shop';
                    break;
                }
            }
        }
        $message = '';
        switch ($case) {
            case 'all':
                $message = $this->l('from All Shops');
                break;

            case 'group':
                $group_id = $shop_list[$first_key]['id_shop_group'];
                $sql = 'SELECT `name` FROM `'._DB_PREFIX_.'shop_group` WHERE `id_shop_group` = '.(int) $group_id.' LIMIT 1;';
                $result = Db::getInstance()->executeS($sql);

                $result = (empty($result[0]['name']) ? $this->l('Default') : $result[0]['name']).' '.$this->l('Group');
                $message = $this->l('from').' '.$result;
                break;

            case 'shop':
                $name = $shop_list[$first_key]['name'];
                $message = $this->l('from shop').' '.$name;
                break;

            default:
                $message = $this->l('from All Shops');
                break;
        }

        return $message;
    }

    private function getNewsletterDirTemplates()
    {
        return $this->controller_template->getNewsletterTemplates(false);
    }

    private function getProductDirTemplates()
    {
        $files = [];
        $path = $this->module->dir_location.'mail_templates/product/';
        if (file_exists($path)) {
            $files = scandir($path);
            $files = preg_grep('/^.*.html$/', $files);
            $files = preg_grep('/^((?!default.html).)*$/', $files);
        }

        return $files;
    }

    private function getGroups()
    {
        $sql = 'SELECT g.`id_group` AS `value`, gl.`name` AS `title` FROM `'._DB_PREFIX_.'group` g
				INNER JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.`id_group` = gl.`id_group`)
				AND gl.id_lang = '.(int) $this->context->language->id.';';

        return Db::getInstance()->executeS($sql);
    }

    private function getShops()
    {
        $sql_shops_id = '';
        $get_active_shops_id = NewsletterProTools::getActiveShopsId();
        foreach ($get_active_shops_id as $id_shop) {
            $sql_shops_id .= 's.`id_shop` = '.(int) $id_shop.(end($get_active_shops_id) == $id_shop ? '' : ' OR ');
        }

        $sql = 'SELECT s.`id_shop` AS `value`, s.`name` AS `title` FROM `'._DB_PREFIX_.'shop` s WHERE ('.$sql_shops_id.');';

        return Db::getInstance()->executeS($sql);
    }

    private function getCSVNameFilters()
    {
        $data = [];

        $result = Db::getInstance()->executeS('
			SELECT `filter_name` FROM `'._DB_PREFIX_.'newsletter_pro_email` 
			WHERE `filter_name` IS NOT NULL 
			AND `filter_name` != "" 
			GROUP BY `filter_name`
		');

        foreach ($result as $key => $value) {
            $data[$key]['value'] = $value['filter_name'];
            $data[$key]['title'] = $value['filter_name'];
        }

        return $data;
    }

    public function getLanguages()
    {
        $langs = [];
        $languages = Language::getLanguages(true, $this->context->shop->id);
        foreach ($languages as $lang) {
            $langs[] = ['title' => $lang['name'], 'value' => $lang['id_lang'], 'img_path' => $this->module->getLangImageById($lang['id_lang'])];
        }

        return $langs;
    }

    public function getAllLanguages($active = true)
    {
        $languages = Language::getLanguages($active, $this->context->shop->id);

        foreach ($languages as &$lang) {
            if ((int) $lang['id_lang'] == $this->getCurrentLangId()) {
                $lang['selected'] = true;
            } else {
                $lang['selected'] = false;
            }
        }

        return $languages;
    }

    private function getCurrentLangId()
    {
        // return (int)pqnp_config('PS_LANG_DEFAULT');
        return (int) $this->context->language->id;
    }

    public function getGenders()
    {
        $genders = [];
        $genders_select = Gender::getGenders();

        foreach ($genders_select as $gender) {
            $genders[] = [
                            'title' => $gender->name,
                            'value' => $gender->id,
                        ];
        }

        $genders[] = [
                        'title' => 'Mx.',
                        'value' => 0,
                    ];

        return $genders;
    }

    public static function getLink($params = [])
    {
        return 'index.php?controller='.__CLASS__.'&token='.Tools::getAdminTokenLite(__CLASS__).(!empty($params) ? '&'.http_build_query($params) : '');
    }

    public function postProcess()
    {
        if ((bool) Tools::isSubmit('recompileTemplates')) {
            $this->module->clearCache();
            Tools::redirectAdmin($this->getLink());
        }

        if (_PS_MAGIC_QUOTES_GPC_ && class_exists('NewsletterPro')) {
            $_POST = NewsletterPro::strip($_POST);
            $_GET = NewsletterPro::strip($_GET);
        }

        parent::postProcess();

        if (Tools::isSubmit('loadMin')) {
            $value = Tools::getValue('loadMin');
            if ('true' === $value || '1' == $value) {
                $value = true;
            } elseif ('false' === $value || '0' == $value) {
                $value = false;
            } else {
                $value = false;
            }

            pqnp_config('LOAD_MINIFIED', $value);

            $link = preg_replace('/(\?|\&)loadMin/', '', $_SERVER['REQUEST_URI']);
            Tools::redirectAdmin($link);
        }

        foreach (array_keys($this->init_controllers) as $controller_instance) {
            $this->{$controller_instance}->postProcess();
        }

        if (Tools::isSubmit('action') && 'test' == Tools::getValue('action')) {
            $output = $this->l('Everything is working!');
            $output .= '<br><a href="'.$this->getLink().'">Go Back</a>';
            exit($output);
        }

        $ajax_controller = new NewsletterProAjaxController();

        if (Tools::isSubmit('submit')) {
            $ajax_controller->process(Tools::getValue('submit'));
        } elseif (Tools::isSubmit('chimp')) {
            $ajax_controller->processChimp(Tools::getValue('chimp'));
        } elseif (Tools::isSubmit('submit_custom_field')) {
            $ajax_controller->processCustomField(Tools::getValue('submit_custom_field'));
        }

        if (NewsletterProAjaxController::isXHR()) {
            exit('Invalid Requrest!');
        }

        $message = [
            'status' => false,
            'image_msg' => '',
            'csv_msg' => '',
            'csv_export_message' => '',
        ];

        if (Tools::isSubmit('export_email_addresses')) {
            $errors = [];

            $list_ref = (int) Tools::getValue('list_ref');
            $columns = Tools::getValue('export_csv_selected_columns');
            $csv_separator = trim(Tools::getValue('csv_separator'));

            $export_range = Tools::getValue('export_range');
            $export_all_columns = (int) Tools::getValue('export_all_columns');

            if (';' != $csv_separator && ',' != $csv_separator) {
                $csv_separator = ';';
            }

            $is_export_range = $export_range && $export_all_columns;

            try {
                if (!$is_export_range) {
                    if (empty($columns)) {
                        throw new Exception($this->l('You must select at least a column.'));
                    }

                    // escape the csv separator
                    foreach ($columns as $key => $column) {
                        if (false !== strpos($column, $csv_separator)) {
                            $columns[$key] = '"'.$column.'"';
                        }
                    }

                    $header = implode($csv_separator, $columns)."\r\n";
                }

                $table_name = '';
                $id = '';

                switch ($list_ref) {
                    case NewsletterPro::LIST_CUSTOMERS:
                        $id = 'id_customer';
                        $table_name = 'customers';
                        $results = Db::getInstance()->executeS('
							SELECT * FROM `'._DB_PREFIX_.'customer`
						');
                        break;

                    case NewsletterPro::LIST_VISITORS:
                        $id = 'id';

                        if (!($tn = NewsletterProDefaultNewsletterTable::getTableName())) {
                            throw new Exception($this->l('The table does not exists.'));
                        }
                        $table_name = 'visitors_block_newsletter';
                        $results = Db::getInstance()->executeS('
							SELECT * FROM `'._DB_PREFIX_.bqSQL($tn).'`
						');
                        break;

                    case NewsletterPro::LIST_VISITORS_NP:
                        $id = 'id_newsletter_pro_subscribers';
                        $table_name = 'visitors_newsletter_pro';
                        $results = Db::getInstance()->executeS('
							SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_subscribers`
						');
                        break;

                    case NewsletterPro::LIST_ADDED:
                        $id = 'id_newsletter_pro_email';
                        $table_name = 'added';
                        $results = Db::getInstance()->executeS('
							SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_email`
						');
                        break;

                    default:
                        throw new Exception($this->l('Invalid list id.'));
                        break;
                }

                $body_array = [];

                if ($is_export_range) {
                    $range_ids = [];

                    foreach ($export_range as $value) {
                        if (false !== strpos($value, '-')) {
                            $val = explode('-', $value);

                            for ($index = $val[0]; $index <= $val[1]; ++$index) {
                                $range_ids[] = (int) $index;
                            }
                        } else {
                            $range_ids[] = (int) $value;
                        }
                    }
                }

                foreach ($results as $key => $row) {
                    if ($is_export_range) {
                        // escape the csv separator
                        if (0 == $key) {
                            $row_columns = array_keys($row);

                            foreach ($row_columns as $key => $column) {
                                if (false !== strpos($column, $csv_separator)) {
                                    $row_columns[$key] = '"'.$column.'"';
                                }
                            }

                            $header = implode($csv_separator, $row_columns)."\r\n";
                        }

                        if (in_array((int) $row[$id], $range_ids)) {
                            // send newsletters exprot section
                            $line = '';
                            foreach ($row_columns as $column) {
                                if (isset($row[$column])) {
                                    $value = $row[$column];

                                    // escape csv separator
                                    if (false !== strpos($value, $csv_separator)) {
                                        $value = '"'.$value.'"';
                                    }

                                    $line .= $value.$csv_separator;
                                } else {
                                    $line .= $csv_separator;
                                }
                            }

                            if (!empty($line)) {
                                $body_array[] = rtrim($line, $csv_separator);
                            }
                        }
                    } else {
                        // export csv section
                        $line = '';
                        foreach ($columns as $column) {
                            if (isset($row[$column])) {
                                $value = $row[$column];

                                // escape csv separator
                                if (false !== strpos($value, $csv_separator)) {
                                    $value = '"'.$value.'"';
                                }

                                $line .= $value.$csv_separator;
                            } else {
                                $line .= $csv_separator;
                            }
                        }

                        if (!empty($line)) {
                            $body_array[] = rtrim($line, $csv_separator);
                        }
                    }
                }

                $body = implode("\r\n", $body_array);

                ob_clean();

                header('Content-Type: application/csv; charset=UTF-8');
                header('Content-Disposition: attachment; filename='.$table_name.'_'.uniqid().'.csv');
                header('Pragma: no-cache');
                echo $header;
                echo $body;
                exit;
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            $this->context->smarty->assign([
                'export_email_addresses_errors' => $errors,
            ]);
        } elseif (Tools::isSubmit('export_send_history')) {
            $id_history = (int) Tools::getValue('id_history');
            $csv_separator = Tools::getValue('csv_separator');

            $export_emails_to_send = (int) Tools::getValue('export_emails_to_send');
            $export_emails_sent = (int) Tools::getValue('export_emails_sent');

            $this->exportSendHistory($id_history, $csv_separator, $export_emails_to_send, $export_emails_sent);
        } elseif (Tools::isSubmit('downloadImportSample')) {
            $filename = $this->module->dir_location.'csv/sample.csv';
            if (file_exists($filename)) {
                ob_clean();
                header('Content-Type: application/csv');
                header('Content-Disposition: attachment; filename=Import Sample.csv');
                header('Pragma: no-cache');
                readfile($filename);
                exit;
            }
        } elseif (Tools::isSubmit('exportHTML')) {
            try {
                $this->controller_template->exportHTML(Tools::isSubmit('renderView'));
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        } elseif (Tools::isSubmit('exportTemplateHistory')) {
            try {
                $id = (int) Tools::getValue('idTemplateHistory', 0);
                $historyExporter = new HistoryExporter();
                $historyExporter->downloadByIdHistory($id);
                exit;
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
            }
        }

        $this->context->smarty->assign(['message' => $message]);
    }

    private function exportSendHistory($id_history, $csv_separator, $export_emails_to_send = 0, $export_emails_sent = 0)
    {
        try {
            if (!$export_emails_to_send && !$export_emails_sent) {
                throw new Exception($this->l('You haven\'t specified the list you want to export.'));
            }

            $history = NewsletterProTplHistory::newInstance((int) $id_history);

            if (!Validate::isLoadedObject($history)) {
                throw new Exception(sprintf($this->l('Invalid hisotry id "%s".'), $id_history));
            }

            $id_send = $history->getSendId();

            $send = NewsletterProSend::newInstance((int) $id_send);

            if (!Validate::isLoadedObject($send)) {
                throw new Exception(sprintf($this->l('Invalid send object id "%s".'), $id_send));
            }

            $steps_ids = $send->getStepsIds();

            $emails_sent = [];
            $emails_to_send = [];

            foreach ($steps_ids as $id_step) {
                $step = NewsletterProSendStep::newInstance((int) $id_step);

                if (Validate::isLoadedObject($step)) {
                    if ($export_emails_sent) {
                        $emails_sent = array_merge($emails_sent, $step->getEmailsSent());
                    }

                    if ($export_emails_to_send) {
                        $emails_to_send = array_merge($emails_to_send, $step->getEmailsToSend());
                    }
                }
            }

            $header = '';
            $body = [];

            if ($export_emails_to_send) {
                $body = $emails_to_send;
            } elseif ($export_emails_sent) {
                $header = 'email'.$csv_separator.'status'."\r\n";

                foreach ($emails_sent as $row) {
                    $body[] = $row['email'].$csv_separator.(int) $row['status'];
                }
            }

            $body = implode("\r\n", $body);

            ob_clean();
            header('Content-Type: application/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename='.$id_history.'_'.$id_send.'_'.uniqid().'.csv');
            header('Pragma: no-cache');
            echo $header;
            echo $body;
            exit;
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    public function getCSVFiles($path)
    {
        if (file_exists($path)) {
            return preg_grep('/.csv/i', scandir($path));
        }

        return [];
    }

    public function getLogFiles()
    {
        $result = [];

        foreach (NewsletterProLog::getFiles() as $file) {
            $filename = NewsletterProLog::getLogFile($file);

            if (file_exists($filename) && filesize($filename)) {
                $result[] = [
                    'name' => $file,
                    'path' => $this->module->url_location.'logs/'.$file,
                ];
            }
        }

        return $result;
    }

    private function formatColumn($name)
    {
        $name = explode('_', str_replace('np_', '', $name));

        foreach ($name as &$value) {
            $value = Tools::ucfirst($value);
        }

        $name = implode(' ', $name);

        return $name;
    }
}
