<?php
/**
 * Copyright 2023 LÍNEA GRÁFICA E.C.E S.L.
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
class LGCanonicalurls extends Module
{
    public $bootstrap;
    protected $vsn = '';
    protected $id_object = 0;
    protected $type_object = null;
    protected $form_objects_template = '';

    private $postErrors = [];

    public function __construct()
    {
        $this->name = 'lgcanonicalurls';
        $this->tab = 'seo';
        $this->version = '1.3.6';
        $this->author = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->id_product = '21749';
        $this->module_key = '427f80e80afc4f2435b27a14f28ea49d';

        $this->initLGCanonicalUrlModule();
        $this->initLGCanonicalObject();

        if (Module::isInstalled($this->name) && Module::isEnabled($this->name)) {
            $this->getCommonFormValues();
        }

        parent::__construct();

        $this->displayName = $this->l('Canonical URLs to Avoid Duplicate Content - SEO');
        $this->description = $this->l('Add canonical tags to your pages in order to avoid duplicate content and improve your SEO.');

        $this->ps_versions_compliancy = [
            'min' => '1.5',
            'max' => _PS_VERSION_,
        ];

        $this->initContext();
    }

    protected function initLGCanonicalUrlModule()
    {
        $this->bootstrap = true;
        $this->form_objects_template = '/views/templates/admin/form-objects.tpl';

        if (version_compare(_PS_VERSION_, '8.0', '>=')) {
            $this->vsn = '8';
        } elseif (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->vsn = '17';
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=') && version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->vsn = '16';
        } else {
            $this->bootstrap = false;
            $this->vsn = '15';
            $this->form_objects_template = '/views/templates/admin/form-objects_15.tpl';
        }
    }

    protected function initLGCanonicalObject()
    {
        $controller = pSQL(Tools::getValue('controller'));

        if (!$controller) {
            $context = Context::getContext();

            if (isset($context->controller) && $context->controller->php_self) {
                $controller = $context->controller->php_self;
            }
        }

        $controller = Tools::strtolower($controller);

        $enabled_front_controllers = [
            'product',
            'category',
            'supplier',
            'manufacturer',
            'cms',
            'bestsales',
            'newproducts',
            'pricesdrop',
            'index',
        ];

        $enabled_admin_controllers = [
            'admincategories',
            'adminsuppliers',
            'adminmanufacturers',
            'admincmscontent',
            'adminproducts',
        ];

        $all_values = self::getAllValues();
        $submit_regex = '/\Asubmit(Add|Update)[a-zA-Z]+$/';

        if (in_array($controller, $enabled_front_controllers) || (
            in_array($controller, $enabled_admin_controllers) && (
                ($this->strpos($_SERVER['REQUEST_URI'], 'update') > 0 &&
                $this->strpos($_SERVER['REQUEST_URI'], 'updatecms_category') == 0) ||
                ($this->strpos($_SERVER['REQUEST_URI'], 'add') > 0 &&
                $this->strpos($_SERVER['REQUEST_URI'], 'addcms_category') == 0) ||
                self::pregArrayKeyExists($submit_regex, $all_values)
            )) || (
                $controller == 'adminproducts' && ($this->vsn == '17' || $this->vsn == '8')
        )) {
            // El controlador nos va a ayudar a saber que tipo es y que parametro coger como id
            switch ($controller) {
                // FRONTOFFICE
                case 'product': // Productos
                    $this->id_object = (int) Tools::getValue('id_product');
                    $this->type_object = LGCanonicalurlsObj::LGOT_PRODUCT;

                    break;
                case 'category': // Categorias
                    $this->id_object = (int) Tools::getValue('id_category');
                    $this->type_object = LGCanonicalurlsObj::LGOT_CATEGORY;

                    break;
                case 'supplier': // Proveedores
                    $this->id_object = (int) Tools::getValue('id_supplier');
                    $this->type_object = LGCanonicalurlsObj::LGOT_SUPPLIER;

                    break;
                case 'manufacturer': // Fabricantes
                    $this->id_object = (int) Tools::getValue('id_manufacturer');
                    $this->type_object = LGCanonicalurlsObj::LGOT_MANUFACTURER;

                    break;
                case 'cms': // Paginas CMS
                    $this->id_object = (int) Tools::getValue('id_cms');
                    $this->type_object = LGCanonicalurlsObj::LGOT_CMS;

                    break;
                case 'bestsales': // Paginas más vendidos
                    $this->id_object = null;
                    $this->type_object = LGCanonicalurlsObj::LGOT_CATEGORY;

                    break;
                case 'newproducts': // Paginas nuevos productos
                    $this->id_object = null;
                    $this->type_object = LGCanonicalurlsObj::LGOT_CATEGORY;

                    break;
                case 'pricesdrop': // Paginas productos en oferta
                    $this->id_object = null;
                    $this->type_object = LGCanonicalurlsObj::LGOT_CATEGORY;

                    break;
                case 'index': // Paginas Home
                    $this->id_object = null;
                    $this->type_object = LGCanonicalurlsObj::LGOT_HOME;

                    break;
                // BACKOFFICE
                case 'admincategories':
                    $this->id_object = (int) Tools::getValue('id_category');
                    $this->type_object = LGCanonicalurlsObj::LGOT_CATEGORY;

                    break;
                case 'adminsuppliers':
                    $this->id_object = (int) Tools::getValue('id_supplier');
                    $this->type_object = LGCanonicalurlsObj::LGOT_SUPPLIER;

                    break;
                case 'adminmanufacturers':
                    $this->id_object = (int) Tools::getValue('id_manufacturer');
                    $this->type_object = LGCanonicalurlsObj::LGOT_MANUFACTURER;

                    break;
                case 'admincmscontent':
                    $this->id_object = (int) Tools::getValue('id_cms');
                    $this->type_object = LGCanonicalurlsObj::LGOT_CMS;

                    break;
                case 'adminproducts':
                    $this->id_object = (int) Tools::getValue('id_product');
                    $this->type_object = LGCanonicalurlsObj::LGOT_PRODUCT;

                    break;
            }
        }
    }

    protected static function pregArrayKeyExists($pattern, $array) {
        $keys = array_keys($array);

        return (int) preg_grep($pattern, $keys);
    }

    private function getP($template)
    {
        $iso_langs = ['es', 'en', 'fr'];
        $current_iso_lang = $this->context->language->iso_code;
        $iso = (in_array($current_iso_lang, $iso_langs)) ? $current_iso_lang : 'en';

        $this->context->smarty->assign(
            [
                'iso' => $iso,
                'base_url' => _MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR,
            ]
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }

    /* Retrocompatibility 1.4/1.5 */
    private function initContext()
    {
        $this->context = Context::getContext();
    }

    public function proccessQueries($queries)
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->Execute($query)) {
                LGCanonicalurlsLogger::add('ERROR: CONSULTA - ' . $query . "\n");
                return false;
            } else {
                LGCanonicalurlsLogger::add('EXITO: CONSULTA - ' . $query . "\n");
            }
        }

        return true;
    }

    private function createTables()
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcanonicalurls` (\n' .
            ' `id_object` int(10) unsigned NOT NULL, \n' .
            ' `type_object` enum("LGOT_PRODUCT","LGOT_CATEGORY","LGOT_CMS","LGOT_SUPPLIER", "LGOT_MANUFACTURER")' .
            ' NOT NULL, \n' .
            ' `type` enum("LGCU_AUTO","LGCU_CUSTOM","LGCU_DISABLED") NOT NULL DEFAULT "LGCU_AUTO",\n' .
            ' `parameters` TEXT,\n' .
            ' PRIMARY KEY (`id_object`,`type_object`)\n' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8 ',
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcanonicalurls_lang`(\n' .
            ' `id_object` int(10) unsigned NOT NULL, \n' .
            ' `type_object` enum("LGOT_PRODUCT","LGOT_CATEGORY","LGOT_CMS","LGOT_SUPPLIER", "LGOT_MANUFACTURER")' .
            ' NOT NULL, \n' .
            ' `canonical_url` TEXT,\n' .
            ' `id_lang` int(10) unsigned NOT NULL,\n' .
            ' PRIMARY KEY (`id_object`,`type_object`,`id_lang`)\n ' .
            ') ENGINE=InnoDB DEFAULT CHARSET=utf8',
        ];
        return $this->proccessQueries($queries);
    }

    private function deleteTables()
    {
        $queries = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcanonicalurls`;',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'lgcanonicalurls_lang`;',
        ];
        return $this->proccessQueries($queries);
    }

    public function instalarHook($hook)
    {
        $resultado = $this->registerHook($hook);
        if (!$resultado) {
            LGCanonicalurlsLogger::add(
                'ERROR: no se pudo instalar el hook: ' .
                $hook . ' - RESULTADO:' . print_r($resultado, true) . "\n"
            );
            return false;
        } else {
            LGCanonicalurlsLogger::add('EXITO: Instalado el hook: ' . $hook . "\n");
            return true;
        }
    }

    public function desinstalarHook($hook)
    {
        $resultado = $this->unregisterHook($hook);
        if (!$resultado) {
            LGCanonicalurlsLogger::add(
                'ERROR: no se pudo desinstalar el hook: ' . $hook . ' - RESULTADO:' . print_r($resultado, true) . "\n"
            );
            return false;
        } else {
            LGCanonicalurlsLogger::add('EXITO: Desinstalado el hook: ' . $hook . "\n");
            return true;
        }
    }

    private function desintalarConfig($key)
    {
        $resultado = Configuration::deleteByName($key);
        if (!$resultado) {
            LGCanonicalurlsLogger::add('ERROR: Eliminando al variable de configuración: ' . $key . "\n");
            return false;
        } else {
            LGCanonicalurlsLogger::add('EXITO: Eliminando al variable de configuración: ' . $key . "\n");
            return true;
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (parent::install()
            && $this->instalarHook('displayHeader')
            && $this->instalarHook('displayBackOfficeHeader')
            && $this->instalarHook('displayAdminForm')
            && $this->instalarHook('displayAdminProductsExtra')
            && $this->instalarHook('actionObjectProductAddAfter')
            && $this->instalarHook('actionObjectProductUpdateAfter')
            && $this->instalarHook('actionObjectProductDeleteAfter')
            && $this->instalarHook('actionObjectCategoryAddAfter')
            && $this->instalarHook('actionObjectCategoryUpdateAfter')
            && $this->instalarHook('actionObjectCategoryDeleteAfter')
            && $this->instalarHook('actionObjectCmsAddAfter')
            && $this->instalarHook('actionObjectCmsUpdateAfter')
            && $this->instalarHook('actionObjectCmsDeleteAfter')
            && $this->instalarHook('actionObjectSupplierAddAfter')
            && $this->instalarHook('actionObjectSupplierUpdateAfter')
            && $this->instalarHook('actionObjectSupplierDeleteAfter')
            && $this->instalarHook('actionObjectManufacturerAddAfter')
            && $this->instalarHook('actionObjectManufacturerUpdateAfter')
            && $this->instalarHook('actionObjectManufacturerDeleteAfter')
            && $this->instalarHook('actionCategoryFormBuilderModifier')
            && $this->instalarHook('actionManufacturerFormBuilderModifier')
            && $this->instalarHook('actionSupplierFormBuilderModifier')
            && $this->instalarHook('actionCmsPageFormBuilderModifier')
            && $this->createTables()
            && Configuration::updateValue('LGCANONICALURLS_IGNORE_PARAMS', false)
            && Configuration::updateValue('LGCANONICALURLS_PARAMS', 'orderby,orderway,n,search_query')
            && Configuration::updateValue('LGCANONICALURLS_CANHOMESTATUS', 1)
            && Configuration::updateValue('LGCANONICALURLS_CANONICALHOME', '')
            && Configuration::updateValue('LGCANONICALURLS_CANHOME_TEXT', '')
            && Configuration::updateValue('LGCANONICALURLS_CANHOME_TYPE', 'default')
            && Configuration::updateValue('LGCANONICALURLS_FORCEHEADER', 0)
        ) {
            return true;
        }
        return false;
    }

    public function uninstall()
    {
        if (!$this->desinstalarHook('displayHeader')
            || !$this->desinstalarHook('displayBackOfficeHeader')
            || !$this->desinstalarHook('displayAdminForm')
            || !$this->desinstalarHook('displayAdminProductsExtra')
            || !$this->desinstalarHook('actionObjectProductUpdateAfter')
            || !$this->desinstalarHook('actionObjectProductDeleteAfter')
            || !$this->desinstalarHook('actionObjectCategoryAddAfter')
            || !$this->desinstalarHook('actionObjectCategoryUpdateAfter')
            || !$this->desinstalarHook('actionObjectCategoryDeleteAfter')
            || !$this->desinstalarHook('actionObjectCmsAddAfter')
            || !$this->desinstalarHook('actionObjectCmsUpdateAfter')
            || !$this->desinstalarHook('actionObjectCmsDeleteAfter')
            || !$this->desinstalarHook('actionObjectSupplierAddAfter')
            || !$this->desinstalarHook('actionObjectSupplierUpdateAfter')
            || !$this->desinstalarHook('actionObjectSupplierDeleteAfter')
            || !$this->desinstalarHook('actionObjectManufacturerAddAfter')
            || !$this->desinstalarHook('actionObjectManufacturerUpdateAfter')
            || !$this->desinstalarHook('actionObjectManufacturerDeleteAfter')
            || !$this->deleteTables()
            || !$this->desintalarConfig('LGCANONICALURLS_CANONICDOMAIN')
            || !$this->desintalarConfig('LGCANONICALURLS_HTTP_HEADERS')
            || !$this->desintalarConfig('LGCANONICALURLS_FORCEHTTPHTTPS')
            || !$this->desintalarConfig('LGCANONICALURLS_HTTPHTTPS_VAL')
            || !$this->desintalarConfig('LGCANONICALURLS_IGNORE_PARAMS')
            || !$this->desintalarConfig('LGCANONICALURLS_PARAMS')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOMESTATUS')
            || !$this->desintalarConfig('LGCANONICALURLS_CANONICALHOME')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOME_TEXT')
            || !$this->desintalarConfig('LGCANONICALURLS_CANHOME_TYPE')
            || !$this->desintalarConfig('LGCANONICALURLS_FORCEHEADER')
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    private function postProcess()
    {
        // Tools::dieObject($_REQUEST);
        if (Tools::isSubmit('lgcanonicalurls_config_submit')) {
            Configuration::updateValue(
                'LGCANONICALURLS_FORCEHEADER',
                (int) Tools::getValue('lgcanonicalurls_force_header_echo', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CANONICDOMAIN',
                trim(Tools::getValue('lgcanonicalurls_canonical_domain'), '')
            );
            Configuration::updateValue(
                'LGCANONICALURLS_HTTP_HEADERS',
                (int) Tools::getValue('lgcanonicalurls_http_headers', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_FORCEHTTPHTTPS',
                (int) Tools::getValue('lgcanonicalurls_force_http_https', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_HTTPHTTPS_VAL',
                trim(Tools::getValue('lgcanonicalurls_force_http_https_value', ''))
            );
            Configuration::updateValue(
                'LGCANONICALURLS_IGNORE_PARAMS',
                (int) Tools::getValue('lgcanonicalurls_ignoreparams', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CAPRODUCT_ATTR',
                (int) Tools::getValue('lgcanonicalurls_canonicalproduct_attr', 0)
            );
            Configuration::updateValue(
                'LGCANONICALURLS_PARAMS',
                $this->rtrimString(
                    trim(
                        pSQL(Tools::getValue('lgcanonicalurls_params', Configuration::get('LGCANONICALURLS_PARAMS')))
                    ),
                    ','
                )
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CANONICALHOME',
                (int)(Tools::getValue('lgcanonicalurls_canonicalhome',
                    (Configuration::get('LGCANONICALURLS_CANONICALHOME') ? Configuration::get('LGCANONICALURLS_CANONICALHOME') : 0))
                )
            );
            Configuration::updateValue(
                'LGCANONICALURLS_CANHOME_TYPE',
                trim(
                    pSQL(
                        Tools::getValue(
                            'lgcanonicalurls_canonicalhome_type',
                            (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom') ? 'custom' : 'default'
                        )
                    )
                )
            );

            Configuration::updateValue(
                'LGCANONICALURLS_HREFLANG',
                (int) Tools::getValue('lgcanonicalurls_hreflang', 0)
            );

            Configuration::updateValue(
                'LGCANONICALURLS_REGION_CODE',
                (int) Tools::getValue('lgcanonicalurls_region_code', 0)
            );

            Configuration::updateValue(
                'LGCANONICALURLS_LANG_DEFAULT',
                (int) Tools::getValue('lgcanonicalurls_lang_default', 0)
            );

            $languages = LanguageCore::getLanguages();
            $langs_received = [];
            $langs_saved = self::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);
            foreach ($languages as $lang) {
                $aux_text = pSQL(Tools::getValue('LGCANONICALURLS_CANHOME_TEXT_' . $lang['id_lang'], ''));
                $langs_received[$lang['id_lang']] = $aux_text;
            }
            foreach ($languages as $lang) {
                if ((isset($langs_saved['id_lang']) && $langs_saved['id_lang'] != $langs_received[$lang['id_lang']])
                    || !isset($langs_saved['id_lang'])
                ) {
                    $langs_saved[$lang['id_lang']] = $langs_received[$lang['id_lang']];
                }
            }
            Configuration::updateValue('LGCANONICALURLS_CANHOME_TEXT', self::jsonEncode($langs_saved));
        }
    }

    public function getConfigFormValues()
    {
        $defaults = [
            // 'lgcanonicalurls_force_header_info' => $html,
            'lgcanonicalurls_force_header_echo' => Configuration::get('LGCANONICALURLS_FORCEHEADER'),
            'lgcanonicalurls_canonical_domain' => Configuration::get('LGCANONICALURLS_CANONICDOMAIN'),
            'lgcanonicalurls_http_headers' => Configuration::get('LGCANONICALURLS_HTTP_HEADERS'),
            'lgcanonicalurls_force_http_https' => Configuration::get('LGCANONICALURLS_FORCEHTTPHTTPS'),
            'lgcanonicalurls_force_http_https_value' => Configuration::get('LGCANONICALURLS_HTTPHTTPS_VAL'),
            'lgcanonicalurls_ignoreparams' => Configuration::get('LGCANONICALURLS_IGNORE_PARAMS'),
            'lgcanonicalurls_params' => Configuration::get('LGCANONICALURLS_PARAMS'),
            'lgcanonicalurls_canonicalproduct_attr' => Configuration::get('LGCANONICALURLS_CAPRODUCT_ATTR'),
            'description' => '',
            'lgcanonicalurls_canonicalhome' => Configuration::get('LGCANONICALURLS_CANONICALHOME') ?
                    Configuration::get('LGCANONICALURLS_CANONICALHOME') : false,
            'lgcanonicalurls_canonicalhome_type' => (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom') ? 
                    'custom' : 'default',
            'lgcanonicalurls_hreflang' => Configuration::get('LGCANONICALURLS_HREFLANG'),
            'lgcanonicalurls_region_code' => Configuration::get('LGCANONICALURLS_REGION_CODE'),
            'lgcanonicalurls_lang_default' => Configuration::get('LGCANONICALURLS_LANG_DEFAULT'),
        ];

        $languages = Language::getLanguages();
        $langs_saved = self::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);

        foreach ($languages as $lang) {
            if (isset($langs_saved[$lang['id_lang']])) {
                $defaults['LGCANONICALURLS_CANHOME_TEXT'][$lang['id_lang']] = $langs_saved[$lang['id_lang']];
            } else {
                $defaults['LGCANONICALURLS_CANHOME_TEXT'][$lang['id_lang']] = '';
            }
        }

        return $defaults;
    }

    protected function getCommonFormValues()
    {
        $this->fields_value = [];

        $this->fields_value['lgcanonicalurls_type'] = $this->getTipo((int) $this->id_object, (int) $this->type_object);

        if (!$this->fields_value['lgcanonicalurls_type']) {
            $this->fields_value['lgcanonicalurls_type'] = LGCanonicalurlsObj::LGCU_AUTO;
        }

        $languages = $this->formatFormLanguages();

        foreach ($languages as $language) {
            $value = $this->getLocalCanonicalUrl(
                (int) $this->id_object,
                (int) $this->type_object,
                (int) $language['id_lang']
            );

            if ($value === false) {
                $value = '';
            }

            $this->fields_value['lgcanonicalurls_canonical_url'][(int) $language['id_lang']] = $value;
        }
    }

    protected function getCommonFormFields()
    {
        $disabled = true;

        if (!is_null($this->id_object)) {
            $type = $this->getTipo((int) $this->id_object, (int) $this->type_object);

            if ($type == LGCanonicalurlsObj::LGCU_CUSTOM) {
                $disabled = false;
            }
        }

        $input = [
            [
                'type' => 'radio',
                'label' => $this->l('URL configuration:'),
                'desc' => $this->l('If you choose the option "By default",
                            the module will apply the canonical URL configuration set in the module interface.'),
                'name' => 'lgcanonicalurls_type',
                'required' => true,
                'class' => 't',
                'is_bool' => false,
                'values' => [
                    [
                        'id' => 'lgcanonicalurls_type_1',
                        'value' => LGCanonicalurlsObj::LGCU_AUTO,
                        'label' => $this->l('By default'),
                    ],
                    [
                        'id' => 'lgcanonicalurls_type_2',
                        'value' => LGCanonicalurlsObj::LGCU_CUSTOM,
                        'label' => $this->l('Custom URL'),
                    ],
                    [
                        'id' => 'lgcanonicalurls_type_3',
                        'value' => LGCanonicalurlsObj::LGCU_DISABLED,
                        'label' => $this->l('Disabled'),
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => $this->l('Custom URL:'),
                'desc' => $this->l('Write here the custom canonical URL for this page. It must start with http:// or https://.'),
                'name' => 'lgcanonicalurls_canonical_url',
                'required' => false,
                'class' => 't',
                'lang' => true,
            ],
        ];

        return $input;
    }

    private function displayForm()
    {
        $langs = Language::getLanguages();

        $languages = [];
        $option = [];
        $option['id_lang'] = 0;
        $option['name'] = $this->l('No default');

        $languages[] = $option;
        foreach ($langs as $lang) {
            $languages[] = $lang;
        }

        $switch_or_radio = (!($this->vsn == '15')) ? 'switch' : 'radio';
        $fields_form = [];
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('General configuration'),
                'icon' => 'icon-cog',
            ],
            'input' => [
                [
                    'type' => 'free',
                    'label' => $this->l('How it works:'),
                    'desc' => $this->l('The module automatically adds canonical urls to all your product,') .
                              '&nbsp;' .
                              $this->l('category, CMS, manufacturer and supplier pages only (in the source code of') .
                              '&nbsp;' .
                              $this->l('these pages, do a CTRL+F and search for the word "canonical" to find it).') .
                              '&nbsp;' .
                              $this->l(
                                  'By default, the module uses the current url of the page as the default canonical url.'
                              ),
                    'name' => 'description',
                ],
                [
                    'type' => 'free',
                    'desc' => $this->l('- Use the general configuration below to apply general changes') . '&nbsp;' .
                              $this->l('to all your default canonical urls. You can change automatically') . '&nbsp;' .
                              $this->l('the domain (if you have duplicate content between domains),') . '&nbsp;' .
                              $this->l('the protocol (if you have duplicate content between http and https)') . '&nbsp;' .
                              $this->l('or to remove parameters (if you have duplicate content due to urls') . '&nbsp;' .
                              $this->l('with parameters) in the default canonical urls.'),
                    'name' => 'description',
                ],
                [
                    'type' => 'free',
                    'desc' => $this->l('- If you want to set a custom canonical URL for a specific product, category,') .
                              '&nbsp;' .
                              $this->l('CMS page... then go to the configuration of this page,choose the option') .
                              '&nbsp;' .
                              $this->l('"Custom URL" and write the custom canonical url of your choice.'),
                    'name' => 'description',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Main domain:'),
                    'name' => 'lgcanonicalurls_canonical_domain',
                    'required' => true,
                    'desc' => $this->l('Choose the domain name you want to include in the canonical URL ') . '&nbsp;' .
                              $this->l('(ex: www.mydomain.com or mydomain.com). Do not include the last slash "/",') .
                              '&nbsp;' .
                              $this->l('or the "/index.php" suffix, or the "http(s)://" prefix.'),
                    'disabled' => false,
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('Force to use HTTP or HTTPS:'),
                    'name' => 'lgcanonicalurls_force_http_https',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('If you enable this option,') .'&nbsp;' .
                              $this->l('choose below the type of protocol (HTTP or HTTPS) you want to apply.'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_force_http_https_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_force_http_https_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'name' => 'lgcanonicalurls_force_http_https_value',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Automatically add HTTP or HTTPS before the domain of the canonical URL.'),
                    'is_bool' => false,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_force_http_https_value_https',
                            'value' => 'https',
                            'label' => $this->l('Force HTTPS for the canonical URL'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_force_http_https_value_http',
                            'value' => 'http',
                            'label' => $this->l('Force HTTP for the canonical URL'),
                        ],
                    ],
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('Ignore parameters:'),
                    'name' => 'lgcanonicalurls_ignoreparams',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Enable this option if you want to ignore parameters from the canonical url.'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_ignoreparams_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_ignoreparams_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'name' => 'lgcanonicalurls_params',
                    'desc' => $this->l('List the parameters you want to exclude from the canonical URLs') . '&nbsp;' .
                              $this->l('(do not include the sign ? or & and separe parameters with a coma and space).'),
                    'disabled' => Configuration::get('LGCANONICALURLS_PARAMS') ? false : true,
                ],
                [
                    'type' => ($this->vsn == '17' || $this->vsn == '8') ? $switch_or_radio : 'hidden',
                    'name' => 'lgcanonicalurls_canonicalproduct_attr',
                    'label' => $this->l('Ignore attribute:'),
                    'desc' => $this->l('Enable this option if you want to remove attribute ids from the product canonical url.'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_canonicalproduct_attr_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_canonicalproduct_attr_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('HTTP headers:'),
                    'name' => 'lgcanonicalurls_http_headers',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Visible by web browsers only. Enable this option if you want') . '&nbsp;' .
                              $this->l('web browsers to see the canonical URL inside the HTTP header of the page.'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_http_headers_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_http_headers_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('Enable canonical url on homepage:'),
                    'name' => 'lgcanonicalurls_canonicalhome',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Enable this option if you want to have a canonical url on your homepage'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_canonicalhome_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_canonicalhome_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'radio',
                    'label' => $this->l('Type:'),
                    'name' => 'lgcanonicalurls_canonicalhome_type',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => false,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_canonicalhome_type_default',
                            'value' => 'default',
                            'label' => $this->l('Default: ') . $this->getBaseUri() . __PS_BASE_URI__ . '{iso_lang}',
                        ],
                        [
                            'id' => 'lgcanonicalurls_canonicalhome_type_custom',
                            'value' => 'custom',
                            'label' => $this->l('Custom: Please set the canonical url in the field below'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'name' => 'LGCANONICALURLS_CANHOME_TEXT',
                    'label' => $this->l('Custom canonical url:'),
                    'lang' => true,
                    'desc' => $this->l('Write here the Custom Canonical URL for homepage'),
                    'disabled' => Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom' ? false : true,
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('Enable hreflang tag in header:'),
                    'name' => 'lgcanonicalurls_hreflang',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Enable this option if you want to have a alternate urls for each language'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_hreflang_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_hreflang_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => $switch_or_radio,
                    'label' => $this->l('Include lang/region code:'),
                    'name' => 'lgcanonicalurls_region_code',
                    'required' => true,
                    'class' => 't',
                    'desc' => $this->l('Enable this option if you want add region code in hreflang tag') . '&nbsp;' .
                              $this->l('("en-GB" instead of "en")'),
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'lgcanonicalurls_region_code_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'lgcanonicalurls_region_code_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'col' => 6,
                    'type' => 'select',
                    'label' => $this->l('Add a alternative link to language default'),
                    'name' => 'lgcanonicalurls_lang_default',
                    'options' => [
                        'query' => $languages,
                        'id' => 'id_lang',
                        'name' => 'name',
                    ],
                    'desc' => $this->l('If you want add an alternate link to default language') . '&nbsp;' .
                              $this->l('(tag hreflang="x-default")') . ',&nbsp;,' . $this->l('select your option'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'lgcanonicalurls_config_submit',
                'class' => 'button btn btn-default',
                'id' => 'lgcanonicalurls_config_submit',
            ],
        ];

        if ($this->vsn == '17' || $this->vsn == '8') {
            $fields_form[0]['form']['input'][] = [
                'type' => $switch_or_radio,
                'label' => $this->l('Do not use Core canonical option, use hook displayHeader instead.'),
                'name' => 'lgcanonicalurls_force_header_echo',
                'required' => true,
                'class' => 't',
                'desc' => $this->l('Enable this option if do not get a meta canonical tag on Prestashop 1.7.x. ') .
                          $this->l('With this option enabled our module will use standar hook display Header, '),
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'lgcanonicalurls_force_header_echo_on',
                        'value' => 1,
                        'label' => $this->l('Yes'),
                    ],
                    [
                        'id' => 'lgcanonicalurls_force_header_echo_off',
                        'value' => 0,
                        'label' => $this->l('No'),
                    ],
                ],
            ];

            $html = $this->display(
                $this->_path,
                'views' . DIRECTORY_SEPARATOR .
                'templates' . DIRECTORY_SEPARATOR .
                'admin' . DIRECTORY_SEPARATOR .
                'info_message.tpl'
            );
            $fields_form[0]['form']['input'][] = [
                'type' => 'html',
                'label' => $this->l('Explanation of above feature'),
                'name' => 'lgcanonicalurls_force_header_info',
                'html_content' => $html,
                'required' => false,
                'class' => 't',
            ];
        }

        // Tools::dieObject($fields_form[0]['form']);

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->languages = $this->formatFormLanguages(); // Language::getLanguages();
        $helper->module = $this;
        $helper->fields_value = $this->getConfigFormValues();
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $default_lang = $this->context->language->id;

        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to the list'),
            ],
        ];

        // return $this->getP('top').$helper->generateForm($fields_form).$this->getP('bottom');
        return $helper->generateForm($fields_form);
    }

    public function getContent()
    {
        $out = [];
        if (pSQL(Tools::getValue('action')) == 'ajaxSaveProductForm') {
            $result = $this->updateObject((int) Tools::getValue('id_product'), LGCanonicalurlsObj::LGOT_PRODUCT);
            if ($result) {
                $out['status'] = 'ok';
                $out['confirmation'] = $this->l('The changes have been saved successfully');
            } else {
                $out['status'] = 'nok';
                $out['error'] = $this->l('An error occurred while saving the changes');
            }
            echo self::jsonEncode($out);
            die();
        }

        $html_aux = '';
        /*
        $this->context->smarty->assign(['lgcanonical_displayName' => $this->displayName]);
        $html_aux .= $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . 'tag_h2.tpl'
        );
        */

        if (Tools::isSubmit('lgcanonicalurls_config_submit')) {
            if (!sizeof($this->postErrors)) {
                $this->postProcess();
                if (!sizeof($this->postErrors)) {
                    $html_aux .= $this->displayConfirmation($this->l('Configuration saved successfully'));
                } else {
                    $html_aux .= $this->displayError($this->l('Has been an error while trying saving configuration'));
                }
            } else {
                $this->context->smarty->assign(['lgcanonical_errors' => $this->postErrors]);
                $html_aux .= $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->name
                    . DIRECTORY_SEPARATOR . 'views'
                    . DIRECTORY_SEPARATOR . 'templates'
                    . DIRECTORY_SEPARATOR . 'admin'
                    . DIRECTORY_SEPARATOR . 'alert_error.tpl'
                );
            }
        }

        $lg_help_url = is_dir(dirname(__FILE__) . '/views/img/help/' . $this->context->language->iso_code)
            ? _MODULE_DIR_ . $this->name . '/views/img/help/' . $this->context->language->iso_code
            : _MODULE_DIR_ . $this->name . '/views/img/help/en';

        $params = [
            'lg_id_product' => $this->id_product,
            'lg_module_dir' => $this->_path,
            'lg_module_name' => $this->name,
            'lg_base_url' => _MODULE_DIR_ . $this->name . '/',
            'lg_help_url' => $lg_help_url . '/',
            'lg_iso_code' => $this->context->language->iso_code,
        ];

        $this->context->smarty->assign($params);

        switch (Tools::getValue('tab_lg')) {
            case 'help':
                $body = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/help.tpl');

                break;
            default:
                $this->postProcess();
                $form = $this->displayForm();
                $body = $form;

                break;
        }

        $params = [
            'lg_menu' => $this->getMenu(),
        ];

        $this->context->smarty->assign($params);

        return $this->getP('top') . $html_aux . $body . $this->getP('bottom');
    }

    protected function getMenu()
    {
        $tab = Tools::getValue('tab_lg');
        $tab_link = $this->context->link->getAdminLink('AdminModules', true)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&tab_lg=';

        $menu = [];
        $menu[] = [
            'label' => $this->l('Configuration'),
            'link' => $tab_link . 'settings',
            'active' => ($tab == 'settings' || empty($tab) ? 1 : 0),
        ];
        $menu[] = [
            'label' => $this->l('Help'),
            'link' => $tab_link . 'help',
            'active' => ($tab == 'help' ? 1 : 0),
        ];

        return $menu;
    }

    public function hookDisplayHeader($params)
    {
        if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] >= 300) {
            return;
        }

        $rel_canonical = '';
        $canonical_url = $this->getCanonicalUrl();

        if ($canonical_url !== false) {
            if (Configuration::get('LGCANONICALURLS_HTTP_HEADERS') && $canonical_url && !headers_sent()) {
                header('Link: <' . $canonical_url . '>; rel="canonical"');
            }

            if (($this->vsn == '17'|| $this->vsn == '8') && !Configuration::get('LGCANONICALURLS_FORCEHEADER')) {
                $this->assignCanonicalSmarty($canonical_url);

                return;
            }

            $rel_canonical = $this->getCanonicalLinkTag($canonical_url);

            if ($this->context->controller instanceof CategoryController ||
                $this->context->controller instanceof ManufacturerController ||
                $this->context->controller instanceof SupplierController ||
                $this->context->controller instanceof BestSalesController ||
                $this->context->controller instanceof NewProductsController ||
                $this->context->controller instanceof PricesDropController
            ) {
                if (Configuration::get('PS_REWRITING_SETTINGS') && $this->strpos($canonical_url, '?')) {
                    $canonical_url = Tools::substr($canonical_url, 0, $this->strpos($canonical_url, '?'));
                }

                if (($this->vsn == '17' || $this->vsn == '8') && !Configuration::get('LGCANONICALURLS_FORCEHEADER')) {
                    $this->assignCanonicalSmarty($canonical_url);

                    return;
                }

                return $this->getCanonicalLinkTag($canonical_url);
            } else {
                if (($this->vsn == '17' || $this->vsn == '8') && !Configuration::get('LGCANONICALURLS_FORCEHEADER')) {
                    $this->assignCanonicalSmarty($canonical_url);

                    return;
                }

                $rel_canonical = $this->getCanonicalLinkTag($this->quitarParametros($canonical_url));
            }

            return $rel_canonical;
        }
    }

    public function assignCanonicalSmarty($canonical_url = null)
    {
        $context = Context::getContext();

        $smarty_variables = $context->smarty->getTemplateVars();

        if ($context->controller instanceof ProductController &&
            version_compare(_PS_VERSION_, '1.7.8.0', '<') &&
            !empty($smarty_variables['product'])
        ) {
            $customVariable = $smarty_variables['product'];

            if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
                $customVariable->offsetSet('canonical_url', $canonical_url, true);
            } else {
                $customVariable['canonical_url'] = $canonical_url;
            }

            $context->smarty->assign('product', $customVariable);
        } else {
            $customVariable = $smarty_variables['page'];
            $customVariable['canonical'] = $canonical_url;

            $context->smarty->assign('page', $customVariable);
        }

        if (Configuration::get('LGCANONICALURLS_HREFLANG')) {
            $customVariable = $smarty_variables['urls'];
            $customVariable['alternative_langs'] = $this->getAlternativeLangsUrl();

            $context->smarty->assign('urls', $customVariable);
        }
    }

    private function getDefaultCanonicalHome($id_lang)
    {
        $rel_canonical = '';
        $canonical_langs = self::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'));
        if ($canonical_langs[$this->context->language->id]) {
            $rel_canonical = $this->getCanonicalLinkTag($canonical_langs[$id_lang]);
        }

        return $rel_canonical;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        $context = Context::getContext();

        $context->controller->addJquery();

        $controller = Tools::strtolower(pSQL(Tools::getValue('controller')));

        $enabled_admin_controllers = [
            'admincategories',
            'adminmanufacturers',
            'adminsuppliers',
            'admincmscontent',
            'adminproducts',
        ];

        if ((Tools::strtolower($controller) == 'adminmodules' && Tools::getValue('configure') == $this->name) ||
            in_array($controller, $enabled_admin_controllers)
        ) {
            if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
                $context->controller->addCSS($this->_path . 'views/css/admin15.css');
                $context->controller->addJS($this->_path . 'views/js/admin-header-15.js');
            } else {
                $context->controller->addJS($this->_path . 'views/js/admin-header.js');
            }
        }

        if (Tools::strtolower($controller) == 'adminmodules' && Tools::getValue('configure') == $this->name) {
            $context->controller->addCSS($this->_path . 'views/css/publi/lgpubli.css');
        }
    }

    public function hookDisplayAdminForm($params)
    {
        // Esta condición es para que no se repita en la ficha de productos
        if (!$this->context->controller instanceof AdminProductsController) {
            return $this->displayCommonFormFields();
        }
    }

    public function hookActionCategoryFormBuilderModifier($params)
    {
        $this->getNewFormCommonFields($params, LGCanonicalurlsObj::LGOT_CATEGORY);
    }

    public function hookActionRootCategoryFormBuilderModifier($params)
    {
        $this->getNewFormCommonFields($params, LGCanonicalurlsObj::LGOT_CATEGORY);
    }

    public function hookActionManufacturerFormBuilderModifier($params)
    {
        $this->getNewFormCommonFields($params, LGCanonicalurlsObj::LGOT_MANUFACTURER);
    }

    public function hookActionSupplierFormBuilderModifier($params)
    {
        $this->getNewFormCommonFields($params, LGCanonicalurlsObj::LGOT_SUPPLIER);
    }

    public function hookActionCmsPageFormBuilderModifier($params)
    {
        $this->getNewFormCommonFields($params, LGCanonicalurlsObj::LGOT_CMS);
    }

    public function getNewFormCommonFields($params, $type_object)
    {
        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            // Sacamos que configuración tenemos
            $obj = LGCanonicalurlsObj::loadObject((int) $params['id'], $type_object);
            $languages = Language::getLanguages();
            $urls = [];
            if (Validate::isLoadedObject($obj)) {
                foreach ($languages as $lang) {
                    $urls[$lang['id_lang']] = $this->getLocalCanonicalUrl(
                        (int) $obj->id,
                        $type_object,
                        $lang['id_lang']
                    );
                }
                $tipo = $this->getTipo((int) $params['id'], $type_object);
            } else {
                foreach ($languages as $lang) {
                    $urls[$lang['id_lang']] = '';
                }
                $tipo = LGCanonicalurlsObj::LGCU_AUTO;
            }

            $choices = [
                $this->l('By default') => LGCanonicalurlsObj::LGCU_AUTO,
                $this->l('Custom URL') => LGCanonicalurlsObj::LGCU_CUSTOM,
                $this->l('Disabled') => LGCanonicalurlsObj::LGCU_DISABLED,
            ];

            /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
            $formBuilder = $params['form_builder'];
            $formBuilder->add(
                'lgcanonicalurls_type',
                \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,
                [
                    'label' => $this->l('Canonical Url mode'),
                    'required' => false,
                    'constraints' => [
                        new \Symfony\Component\Validator\Constraints\Length([
                            'max' => 20,
                            'maxMessage' => $this->l('Max caracters allowed : 20'),
                        ]),
                    ],
                    'choices' => $choices,
                    'data' => $tipo,
                ]
            );
            $formBuilder->add(
                'lgcanonicalurls_canonical',
                \PrestaShopBundle\Form\Admin\Type\TranslatableType::class,
                [
                    'type' => \Symfony\Component\Form\Extension\Core\Type\TextType::class,
                    'label' => $this->l('Custom URL:'),
                    'options' => [
                        'disabled' => (($tipo == LGCanonicalurlsObj::LGCU_CUSTOM) ? false : true),
                        'required' => false,
                    ],
                    'data' => $urls,
                ]
            );
        }
    }

    public function displayCommonFormFields()
    {
        if (!is_null($this->type_object)) {
            $context = Context::getContext();

            $langs = $this->formatFormLanguages();

            if ($this->type_object != LGCanonicalurlsObj::LGOT_PRODUCT) {
                $context->smarty->assign([
                    'field' => array_merge(
                        [
                            [
                                'type' => 'html',
                                'name' => 'lgcanonicalurls_label',
                                'label' => $this->l('Canonical URL configuration'),
                                'html_content' => '',
                            ],
                        ],
                        $this->getCommonFormFields()
                    ),
                ]);
            } else {
                // Utilizamos la plantilla del formulario que a su vez carga la de form_object
                // pero con lo necesario para un tab
                $this->form_objects_template =
                    'views/templates/admin/controllers/products/moduleLgcanonicalurls.tpl';

                $context->smarty->assign([
                    'field' => $this->getCommonFormFields(),
                ]);
            }

            $controller = Tools::strtolower(pSQL(Tools::getValue('controller')));

            if (version_compare(_PS_VERSION_, '1.6', '>=') &&
                $controller == 'adminproducts' &&
                Tools::getIsset('updateproduct')
            ) {
                $context->smarty->assign([
                    'show_cancel_button' => true,
                    'back_url' => $context->link->getAdminLink($controller),
                    'buttons' => [
                        [
                            'title' => $this->l('Save'),
                            'name' => 'submitAddproduct',
                            'class' => 'pull-right',
                            'type' => 'submit',
                            'icon' => 'process-icon-loading',
                            'disabled' => true,
                        ],
                        [
                            'title' => $this->l('Save and stay'),
                            'name' => 'submitAddproductAndStay',
                            'class' => 'pull-right',
                            'type' => 'submit',
                            'icon' => 'process-icon-loading',
                            'disabled' => true,
                        ],
                    ],
                ]);
            }

            $context->smarty->assign([
                'langs' => $langs,
                'fields_value' => $this->fields_value,
                'languages' => $context->controller->_languages,
                'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
                'defaultFormLanguage' => (int) Configuration::get('PS_LANG_DEFAULT'),
                'lgcanonicalurl_psversion' => $this->vsn,
            ]);

            return $this->display($this->_path, $this->form_objects_template);
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '<')) {
            $this->id_object = (int) Tools::getValue('id_product', 0);

            if (!$this->id_object) {
                return $this->l('Please save this product to continue');
            }
        } else {
            $this->id_object = (int) $params['id_product'];
        }

        $this->type_object = LGCanonicalurlsObj::LGOT_PRODUCT;
        $this->getCommonFormValues();

        return $this->displayCommonFormFields();
    }

    public function formatFormLanguages()
    {
        $langs = Language::getLanguages();
        foreach ($langs as &$lang) {
            $lang['is_default'] = (int) ($lang['id_lang'] == Configuration::get('PS_LANG_DEFAULT'));
        }

        return $langs;
    }

    public function getProductFormValues($id_object = null)
    {
        $out = [];

        if (pSQL(Tools::getValue('controller')) == 'AdminProducts') {
            $out['moduleLgcanonicalurls_loaded'] = 1;
            $out['form_token'] = Tools::getAdminTokenLite('AdminModules');
            $out['lgcanonicalurls_product_id'] = (int) Tools::getValue('id_product');

            $langs = $this->formatFormLanguages();

            $out['lgcanonicalurls_type'] = $this->getTipo($id_object, LGCanonicalurlsObj::LGOT_PRODUCT);
            if (!$out['lgcanonicalurls_type']) {
                $out['lgcanonicalurls_type'] = LGCanonicalurlsObj::LGCU_AUTO;
            }
            foreach ($langs as $lang) {
                $value = $this->getLocalCanonicalUrl($id_object, LGCanonicalurlsObj::LGOT_PRODUCT, $lang['id_lang']);
                if ($value === false) {
                    $value = '';
                }
                $out['lgcanonicalurls_canonical_url'][$lang['id_lang']] = $value;
            }
        }

        return $out;
    }

    public function hookActionObjectProductAddAfter($params)
    {
        return LGCanonicalurlsObj::addObject($params, LGCanonicalurlsObj::LGOT_PRODUCT);
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        return LGCanonicalurlsObj::updateObject($params, LGCanonicalurlsObj::LGOT_PRODUCT);
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        return LGCanonicalurlsObj::deleteObject($params, LGCanonicalurlsObj::LGOT_PRODUCT);
    }

    public function hookActionObjectCategoryAddAfter($params)
    {
        return LGCanonicalurlsObj::addObject($params, LGCanonicalurlsObj::LGOT_CATEGORY);
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        return LGCanonicalurlsObj::updateObject($params, LGCanonicalurlsObj::LGOT_CATEGORY);
    }

    public function hookActionObjectCategoryDeleteAfter($params)
    {
        return LGCanonicalurlsObj::deleteObject($params, LGCanonicalurlsObj::LGOT_CATEGORY);
    }

    public function hookActionObjectCMSAddAfter($params)
    {
        return LGCanonicalurlsObj::addObject($params, LGCanonicalurlsObj::LGOT_CMS);
    }

    public function hookActionObjectCMSUpdateAfter($params)
    {
        return LGCanonicalurlsObj::updateObject($params, LGCanonicalurlsObj::LGOT_CMS);
    }

    public function hookActionObjectCMSDeleteAfter($params)
    {
        return LGCanonicalurlsObj::deleteObject($params, LGCanonicalurlsObj::LGOT_CMS);
    }

    public function hookActionObjectSupplierAddAfter($params)
    {
        return LGCanonicalurlsObj::addObject($params, LGCanonicalurlsObj::LGOT_SUPPLIER);
    }

    public function hookActionObjectSupplierUpdateAfter($params)
    {
        return LGCanonicalurlsObj::updateObject($params, LGCanonicalurlsObj::LGOT_SUPPLIER);
    }

    public function hookActionObjectSupplierDeleteAfter($params)
    {
        return LGCanonicalurlsObj::deleteObject($params, LGCanonicalurlsObj::LGOT_SUPPLIER);
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        return LGCanonicalurlsObj::addObject($params, LGCanonicalurlsObj::LGOT_MANUFACTURER);
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        return LGCanonicalurlsObj::updateObject($params, LGCanonicalurlsObj::LGOT_MANUFACTURER);
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        return LGCanonicalurlsObj::deleteObject($params, LGCanonicalurlsObj::LGOT_MANUFACTURER);
    }

    /*
     * Actualiza la url canonica a nivel local
     */
    private function updateObject($object_id, $tipo)
    {
        $queries = [];
        $canonical_url = [];
        $langs = Language::getLanguages();

        if (Tools::getIsset('lgcanonicalurls_type')) {
            $queries[] = 'REPLACE `' . _DB_PREFIX_ . 'lgcanonicalurls` ' .
                'SET ' .
                ' `type` = ' . (int) Tools::getValue('lgcanonicalurls_type') . ', ' .
                ' `parameters` = "' . pSQL(Tools::getValue('lgcanonicalurls_params', null)) . '", ' .
                ' `id_object`= ' . pSQL($object_id) . ', ' .
                ' `type_object` = ' . pSQL($tipo) . ';';

            // Si se especifica una url canonica
            if ((int) Tools::getValue('lgcanonicalurls_type') == LGCanonicalurlsObj::LGCU_CUSTOM) {
                foreach ($langs as $lang) {
                    if (Tools::getIsset('lgcanonicalurls_canonical_url_' . $lang['id_lang'])) {
                        $canonical_url[$lang['id_lang']] = pSQL(
                            Tools::getValue(
                                'lgcanonicalurls_canonical_url_' . $lang['id_lang']
                            )
                        );
                    }
                }

                if (!empty($canonical_url)) {
                    foreach ($canonical_url as $lang => $url) {
                        $queries[] = 'REPLACE `' . _DB_PREFIX_ . 'lgcanonicalurls_lang` ' .
                            'SET ' .
                            ' `canonical_url` = "' . pSQL($url) . '", ' .
                            ' `id_object`= ' . pSQL($object_id) . ', ' .
                            ' `type_object` = ' . pSQL($tipo) . ', ' .
                            ' `id_lang` = ' . pSQL($lang) . ';';
                    }
                }
            }

            return $this->proccessQueries($queries);
        }
    }

    /*
     * Obtiene la url canonica
     */
    private function getCanonicalUrl()
    {
        $context = Context::getContext();
        $lang = $context->language->id;

        $canonical_url = false;

        if (!is_null($this->type_object)) {
            $tipo = $this->getTipo($this->id_object, $this->type_object);

            if ($tipo == LGCanonicalurlsObj::LGCU_CUSTOM) {
                $canonical_url = $this->getLocalCanonicalUrl($this->id_object, $this->type_object, $lang);

                if (trim($canonical_url) == '') {
                    $canonical_url = $this->getDefaultCanonicalUrl();
                }
            }

            if ($tipo == LGCanonicalurlsObj::LGCU_AUTO) {
                $canonical_url = $this->getDefaultCanonicalUrl(); // 'devolver el link del producto';
            }

            if ($tipo == LGCanonicalurlsObj::LGCU_DISABLED) {
                $canonical_url = null;
            }
        }

        return $canonical_url;
    }

    private function getTipo($id_object, $type_object)
    {
        if (is_null($id_object) || $id_object == '' || is_null($type_object) || $type_object == '') {
            if ($type_object == LGCanonicalurlsObj::LGOT_HOME) {
                if (Configuration::get('LGCANONICALURLS_CANONICALHOME') == 1) {
                    if (Configuration::get('LGCANONICALURLS_CANHOME_TYPE') == 'custom') {
                        return LGCanonicalurlsObj::LGCU_CUSTOM;
                    } else {
                        return LGCanonicalurlsObj::LGCU_AUTO;
                    }
                }
            }
            // return false;
            // Para que devuelva la configuración por defecto en las paginas de lista de proveedores, fabrcantes, ..
            return LGCanonicalurlsObj::LGCU_AUTO;
        }
        $query = 'SELECT type+0 AS type ' .
            'FROM `' . _DB_PREFIX_ . 'lgcanonicalurls` ' .
            'WHERE `id_object` = ' . pSQL($id_object) . ' ' .
            ' AND `type_object` = ' . pSQL($type_object) . ';';
        $return = Db::getInstance()->getValue($query);

        // die("<pre>" . print_r($query,true) . "<pre>");
        if (!empty($return)) {
            // die($return);
            return $return; // $return[0]['type'];
        } else {
            return LGCanonicalurlsObj::LGCU_AUTO;
        }
    }

    private function getLocalCanonicalUrl($id_object, $type_object, $lang)
    {
        if (is_null($id_object) || $id_object == '') {
            if ($type_object == LGCanonicalurlsObj::LGOT_HOME) {
                $home_langs = self::jsonDecode(Configuration::get('LGCANONICALURLS_CANHOME_TEXT'), true);
                $languages = Language::getLanguages();
                $return = '';
                foreach ($languages as $language) {
                    if ($language['id_lang'] == $lang && isset($home_langs[$language['id_lang']])) {
                        $return = $home_langs[$language['id_lang']];
                    }
                }

                return $this->getProtocol() . $return . $this->getBaseDir();
            }

            return false;
        }

        if (is_null($type_object)
            || $type_object == ''
            || is_null($lang)
            || $lang == ''
        ) {
            return false;
        }

        $query = 'SELECT `canonical_url` ' .
            'FROM `' . _DB_PREFIX_ . 'lgcanonicalurls_lang` ' .
            'WHERE `id_object` = ' . pSQL($id_object) . ' ' .
            '  AND `type_object` = ' . pSQL($type_object) . ' ' .
            '  AND `id_lang` = ' . pSQL($lang) . '';

        $result = Db::getInstance()->getValue($query);

        return $result;
    }

    public function getProtocol()
    {
        $base = (
        (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) ?
            'https://' :
            'http://'
        );

        if (Configuration::get('LGCANONICALURLS_FORCEHTTPHTTPS')) {
            $base = Configuration::get('LGCANONICALURLS_HTTPHTTPS_VAL') . '://';
        }

        return $base;
    }

    public function getBaseDir()
    {
        return Context::getContext()->shop->physical_uri;
    }

    public function getBaseUri($id_shop = false)
    {
        $base = $this->getProtocol();

        if (!$id_shop) {
            $id_shop = $this->context->shop->id;
        }

        $shop = new Shop($id_shop);

        /*$languages = Language::getLanguages();
        if (
            $id_lang && count($languages) > 1
            && Configuration::get('PS_REWRITING_SETTINGS', null, null, $shop->id)
        ) {
            $iso = Language::getIsoById($id_lang).'/';
        } else {
            $iso = '';
        }*/

        if (Configuration::get('LGCANONICALURLS_CANONICDOMAIN') != '') {
            $domain = Configuration::get('LGCANONICALURLS_CANONICDOMAIN');
        } else {
            $domain = $shop->domain;
        }

        return $base . $domain; // .$shop->getBaseURI().$iso;
    }

    private function procesarParametros($uri)
    {
        $context = Context::getContext();
        // global $cookie, $protocol_content, $protocol, $page_name, $link;

        if (trim(Configuration::get('LGCANONICALURLS_PARAMS')) != '' // Los parametros no estan vacíos
            && count($params = explode(',', trim(Configuration::get('LGCANONICALURLS_PARAMS')))) > 0
            && $this->strpos($uri, '?')  // La Uri tiene parametros
            && Configuration::get('LGCANONICALURLS_IGNORE_PARAMS') // Ignorar parámetros activado
        ) {
            // Anulamos el paramtero
            if ($context->cookie->id_lang == Configuration::get('PS_LANG_DEFAULT')) {
                $params[] = 'id_lang';
            }
            // Anulamos la eliminación de la paginación, se debe hacer especificándolo
            // if ((int) Tools::getValue('p') <= 1) {
            //     $params[] = 'p';
            // }

            $final_params = [];
            foreach ($params as &$param) {
                if (trim($param) != '') {
                    $final_params[] = '/' . trim($param) . '\=[^\&]*\&?/';
                }
            }
            $params = $final_params;
            $params[] = '/\&$/';
            // $uri = explode('?', $uri);
            // $uri[1] = preg_replace($params, '', $uri[1]);
            // $uri = $uri[0] . ($uri[1] ? '?' . $uri[1] : '');

            // Estan dando muchos errors con esto, lo cambiamos, pero aun asi
            // es mejor utilizar la nueva funcion quitarParametros y no esta
            if (strpos($uri, '?') >= 0) {
                $uri_aux = explode('?', $uri);
                if (count($uri_aux) > 1) {
                    $uri_aux[1] = preg_replace($params, '', $uri_aux[1]);
                    $uri = $uri_aux[0] . ($uri_aux[1] ? ('?' . $uri_aux[1]) : '');
                }
            }
        }

        return $uri;
    }

    private function cambiarParametro($p, $valor, $uri)
    {
        $parsed = parse_url($uri);
        if (!isset($parsed['query'])) {
            $parsed['query'] = $p . '=' . $valor;
        }
        parse_str($parsed['query'], $parametros);
        $params_str = '';
        if (!empty($parametros)) {
            $params_str .= '?';
            $p_aux = [];
            foreach ($parametros as $key => $value) {
                if ($key == $p) {
                    if (!is_null($valor) && trim($valor) != '') {
                        $p_aux[] = $key . '=' . $valor;
                    } else {
                        $p_aux[] = $key;
                    }
                } else {
                    if (!is_null($value) && trim($value) != '') {
                        $p_aux[] = $key . '=' . $value;
                    } else {
                        $p_aux[] = $key;
                    }
                }
            }
            $params_str .= implode('&', $p_aux);
        }

        $final_uri = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'] . $params_str;

        return $final_uri;
    }

    private function quitarParametros($uri, $exclusion_array = null)
    {
        if (Configuration::get('LGCANONICALURLS_IGNORE_PARAMS')) {
            if (trim(Configuration::get('LGCANONICALURLS_PARAMS')) != '') {
                $params = explode(',', Configuration::get('LGCANONICALURLS_PARAMS'));
                foreach ($params as $param) {
                    if (!is_null($exclusion_array) && !empty($exclusion_array)) {
                        if (in_array($param, $exclusion_array)) {
                            continue;
                        } else {
                            $uri = $this->quitarParametro($param, $uri);
                        }
                    } else {
                        $uri = $this->quitarParametro($param, $uri);
                    }
                }
            }
        }

        return $uri == Tools::getHttpHost(true) . __PS_BASE_URI__ . $this->context->language->iso_code . '/'
            ? $uri : $this->rtrimString($uri, '/');
    }

    private function removeAllparameters($uri)
    {
        $parsed = parse_url($uri);

        return Tools::rtrimString($parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'], '/');
    }

    private function quitarParametro($p, $uri)
    {
        $parsed = parse_url($uri);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $parametros);
            $params_str = '';
            if (!empty($parametros)) {
                if (count($parametros) > 1) {
                    $params_str .= '?';
                }
                $p_aux = [];
                foreach ($parametros as $key => $value) {
                    if ($key != $p) {
                        if (!empty($value)) {
                        // if (!is_null($value) && trim($value) != '') {
                        // Esto da un warning a veces, no sabemos muy bien el motivo
                            $p_aux[] = $key . '=' . $value; // $parametros[$key];
                        } else {
                            $p_aux[] = $key; // $parametros[$key];
                        }
                    }
                }
                $params_str .= implode('&', $p_aux);
            }

            $scheme = isset($parsed['scheme']) ?
                $parsed['scheme'] :
                ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
            $host = isset($parsed['host']) ? $parsed['host'] : $_SERVER['HTTP_HOST'];
            $final_uri = $scheme . '://' . $host . $parsed['path'] . $params_str;

            return $final_uri;
        } else {
            return $uri;
        }
    }

    private function getDefaultCanonicalUrl()
    {
        $base = $this->getBaseUri();
        $context = Context::getContext();
        if (($this->vsn == '17' || $this->vsn == '8') &&
            $this->type_object == LGCanonicalurlsObj::LGOT_PRODUCT &&
            Configuration::get('LGCANONICALURLS_CAPRODUCT_ATTR') == 1
        ) {
            $base = (
                Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ?
                'https://' :
                'http://'
            );

            $base_module = false;

            if (Configuration::get('LGCANONICALURLS_FORCEHTTPHTTPS')) {
                $base_module = Configuration::get('LGCANONICALURLS_HTTPHTTPS_VAL') . '://';
            }

            $object = new Product((int) $this->id_object);
            $ipa = $object->cache_default_attribute;
            if (Configuration::get('LGCANONICALURLS_CAPRODUCT_ATTR')) {
                $ipa = 0;
            }

            $canonical_url = $context->link->getProductLink(
                (int) $this->id_object,
                null,
                null,
                null,
                null,
                null,
                $ipa
            );

            // Eliminación del fragmento
            if ($this->strpos($canonical_url, '#')) {
                $canonical_url = explode('#', $canonical_url);
                array_pop($canonical_url);
                $canonical_url = implode('#', $canonical_url);
            }

            if ($base_module && $base != $base_module) {
                $canonical_url = str_replace($base, $base_module, $canonical_url);
            }

            return $canonical_url;
        }

        // La función quitar parámetros está generando errores que afectan incluso a la carga
        // Usamos esta otra en su lugar ya que el objetivo es el mismo y esta parece estable.
        // return $base.$this->quitarParametros($_SERVER['REQUEST_URI']);
        return $base . $this->procesarParametros($_SERVER['REQUEST_URI']);
    }

    public function getCustomField($id_object, $type_object)
    {
        $fields = [];
        $result = $this->getLocalCanonicalUrl($id_object, $type_object);

        if ($result) {
            foreach ($result as $field) {
                $fields[$field['id_lang']] = $field['canonical_url'];
            }
        }

        return $fields;
    }

    private function rtrimString($string, $char = null)
    {
        if (is_null($char)) {
            return $string;
        }

        if (version_compare(_PS_MODULE_DIR_, '1.5.6.2', '>=')) {
            return Tools::rtrimString($string, $char);
        } else {
            return rtrim($string, $char); // NOTE: Tools::rtrimString is not available for versions priors to 1.5.6.2
        }
    }

    public function strpos($str, $find, $offset = 0, $encoding = 'UTF-8')
    {
        if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
            return Tools::strpos($str, $find, $offset, $encoding);
        } else {
            if (function_exists('mb_strpos')) {
                return mb_strpos($str, $find, $offset, $encoding);
            }

            return strpos($str, $find, $offset); // Tools::strpos NOT AVAILABLE FOR VERSIONS PRIORS TO 1.6
        }
    }

    public static function getAllValues()
    {
        if (version_compare(_PS_VERSION_, '1.6.0', '>=')) {
            return Tools::getAllValues();
        } else {
            return $_POST + $_GET;
        }
    }

    public function getCanonicalLinkTag($link)
    {
        $context = Context::getContext();

        $context->smarty->assign([
            'lgcanonical_url' => $link,
            'lgcanonical_alternative_langs' => $this->getAlternativeLangsUrl(),
        ]);

        return $context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/tag_canonical.tpl'
        );
    }

    /**
     * @return array containing the URLs of the same page but for different languages
     */
    protected function getAlternativeLangsUrl()
    {
        $context = Context::getContext();

        $id_shop = $context->shop->id;

        $alternative_langs = [];

        $tipo = null;

        if (!is_null($this->type_object)) {
            $tipo = $this->getTipo((int) $this->id_object, $this->type_object);
        }

        if (Configuration::get('LGCANONICALURLS_HREFLANG') &&
            !is_null($tipo) &&
            $tipo != LGCanonicalurlsObj::LGCU_DISABLED
        ) {
            $languages = Language::getLanguages(true, (int) $id_shop);

            if ($languages > 1) {
                foreach ($languages as $language) {
                    $alternative_lang = $this->getAlternativeLangUrl($tipo, (int) $language['id_lang']);

                    if (!Configuration::get('LGCANONICALURLS_REGION_CODE')) {
                        if (!is_null($alternative_lang) && $alternative_lang != '') {
                            $alternative_langs[$language['iso_code']] = $alternative_lang;
                        }
                    } else {
                        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                            if (!is_null($alternative_lang) && $alternative_lang != '') {
                                $alternative_langs[$language['locale']] = $alternative_lang;
                            }
                        } else {
                            if (!is_null($alternative_lang) && $alternative_lang != '') {
                                $alternative_langs[$language['language_code']] = $alternative_lang;
                            }
                        }
                    }
                }

                if (Configuration::get('LGCANONICALURLS_LANG_DEFAULT')) {
                    $id_default_lang = Configuration::get('LGCANONICALURLS_LANG_DEFAULT');

                    $alternative_lang = $this->getAlternativeLangUrl($tipo, (int) $id_default_lang);

                    if (!is_null($alternative_lang) && $alternative_lang != '') {
                        $alternative_langs['x-default'] = $alternative_lang;
                    }
                }
            }
        }

        return $alternative_langs;
    }

    protected function getAlternativeLangUrl($tipo, $id_lang)
    {
        $context = Context::getContext();

        $alternative_lang = null;

        switch($tipo) {
            case LGCanonicalurlsObj::LGCU_CUSTOM:
                $alternative_lang = $this->getLocalCanonicalUrl(
                    (int) $this->id_object,
                    $this->type_object,
                    (int) $id_lang
                );

                break;

            case LGCanonicalurlsObj::LGCU_AUTO:
            default:
                $alternative_lang = '';

                break;
        }

        if (trim($alternative_lang) == '') {
            $alternative_lang = $context->link->getLanguageLink((int) $id_lang);

            $parameters_position = strpos($_SERVER['REQUEST_URI'], '?');

            if ($parameters_position !== false) {
                $alternative_lang .= substr($_SERVER['REQUEST_URI'], $parameters_position);
            }

            $alternative_lang = $this->procesarParametros($alternative_lang);
        }

        /*
        if (Configuration::get('LGCANONICALURLS_CAPRODUCT_ATTR')) {
            $ipa = 0;
        }
        */

        return $alternative_lang;
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
}
