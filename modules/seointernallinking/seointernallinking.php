<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

if (!defined('_MYSQL_ENGINE_')) {
    define('_MYSQL_ENGINE_', 'MyISAM');
}

include_once dirname(__FILE__) . '/lib/simple_html_dom.php';
include_once dirname(__FILE__) . '/classes/SeoInternalLinkingModel.php';

class SeoInternalLinking extends Module
{
    public function __construct()
    {
        $this->name = 'seointernallinking';
        $this->tab = 'seo';
        $this->version = '1.1.0';
        $this->author = 'FMM Modules';
        $this->module_key = 'e5cfe8ab47f95e74b13849c0522a1541';
        $this->author_address = '0xcC5e76A6182fa47eD831E43d80Cd0985a14BB095';

        $this->bootstrap = true;

        parent::__construct();

        $this->tabClass = 'AdminSeoInternalLinking';

        $this->displayName = $this->l('SEO Internal Linking');
        $this->description = $this->l('Generate internal linking between all kind of pages for better SEO.');
    }

    public function install()
    {
        return parent::install()
        && $this->registerHook('filterCategoryContent')
        && $this->registerHook('filterCmsContent')
        && $this->registerHook('displayBackofficeHeader')
        && $this->installTab()
        && $this->installDb()
        && $this->moveFiles()
        && Configuration::updateValue('SEOINTERNALLINKING_TAGS', 'span, textarea, p, ul, ol, li, table, th, tr, td, dl, dt, dd');
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        $this->uninstallTab();
        $this->delFiles();
        $this->dropDatabase();
        Configuration::deleteByName('SEOINTERNALLINKING_TAGS');

        return true;
    }

    private function installDb()
    {
        $return = true;
        $return = Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seointernallinking` (
                `id_seointernallinking` int(10) NOT NULL auto_increment,
                `target` int(10) NOT NULL DEFAULT 0,
                `color` varchar(255) NOT NULL,
                `rel` int(10) NOT NULL DEFAULT 0,
                `replacements` int(10) NOT NULL DEFAULT 0,
                `types` varchar(255) NOT NULL,
                `active` int(10) unsigned NOT NULL,
                PRIMARY KEY (`id_seointernallinking`))');
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seointernallinking_shop` (
                `id_seointernallinking` int(10) NOT NULL,
                `id_shop` int(10) NOT NULL,
                PRIMARY KEY (`id_seointernallinking`, `id_shop`),
                KEY `id_shop` (`id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'seointernallinking_lang` (
                `id_seointernallinking` int(10) NOT NULL,
                `id_lang` int(10) NOT NULL,
                `title` varchar(255) NOT NULL,
                `url` varchar(255) NOT NULL,
                `keywords` text,
                PRIMARY KEY (`id_seointernallinking`, `id_lang`),
                KEY `id_shop` (`id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;');

        return $return;
    }

    private function dropDatabase()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'seointernallinking`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'seointernallinking_shop`')
            && Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'seointernallinking_lang`');
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = $this->tabClass;
        $tab->id_parent = 0;
        $tab->module = $this->name;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('SEO Internal Linking');
        $tab->add();
        $tab_i = new Tab();
        $tab_i->class_name = 'AdminInternalLinking';
        $tab_i->id_parent = Tab::getIdFromClassName($this->tabClass);
        $tab_i->module = $this->name;
        if (true === Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $tab_i->icon = 'share';
        }
        $tab_i->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('SEO Internal Linking');
        $tab_i->add();

        return true;
    }

    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminInternalLinking');
        $id_tab_ii = (int) Tab::getIdFromClassName($this->tabClass);
        if ($id_tab_ii) {
            $tab = new Tab($id_tab);
            $tab_ii = new Tab($id_tab_ii);
            $tab->delete();

            return $tab_ii->delete();
        } else {
            return true;
        }
    }

    public function moveFiles()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
            Tools::copy(
                _PS_MODULE_DIR_ . 'seointernallinking/includes/CmsController.php',
                _PS_OVERRIDE_DIR_ . 'controllers/front/CmsController.php'
            );
            if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
                rename(
                    _PS_CACHE_DIR_ . 'class_index.php',
                    _PS_CACHE_DIR_ . 'class_index' .
                    rand(
                        pow(10, 3 - 1),
                        pow(10, 3) - 1
                    ) . '.php'
                );
            }
        }

        return true;
    }

    public function delFiles()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) {
            unlink(_PS_OVERRIDE_DIR_ . 'controllers/front/CmsController.php');
        }

        return true;
    }

    public function getContent()
    {
        if (Tools::isSubmit('saveSeointernalTags')) {
            $this->postProcess();
        }
        $this->html = $this->display(__FILE__, 'views/templates/hook/info.tpl');

        return $this->html . $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveSeointernalTags';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $this->context->controller->addJqueryPlugin('tagify');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'col' => 6,
                        'type' => 'tags',
                        'name' => 'SEOINTERNALLINKING_TAGS',
                        'label' => $this->l('HTML Tags'),
                        'hint' => $this->l('special characters are not allowed.'),
                        'desc' => $this->l('module will search selected html tags to add internal linking. Please use comma separated tags without wrapping with symbols "<" and ">". (eaxmple: p, textarea, span)'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'SEOINTERNALLINKING_TAGS' => (string) Configuration::get(
                'SEOINTERNALLINKING_TAGS',
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ),
        ];
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $value = trim(Tools::getValue('SEOINTERNALLINKING_TAGS', ''));
        if (!empty($value) && $value) {
            $tags = explode(',', $value);
            if (isset($tags) && $tags) {
                $tags = array_unique($tags);
                // remove duplicate entry and reassign value
                $value = implode(',', $tags);
                if (!Validate::isTagsList($value)) {
                    $this->context->controller->errors[] = $this->l('Invalid tags list.');
                } else {
                    foreach ($tags as $tag) {
                        if (!Validate::isString($tag)) {
                            $this->context->controller->errors[] = sprintf($this->l('Invalid HTML tags %s in tags list.'), $tag);
                        }
                    }
                }
            }
        }

        if (!count($this->context->controller->errors)) {
            Configuration::updateValue(
                'SEOINTERNALLINKING_TAGS',
                $value,
                false,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            $this->context->controller->confirmations[] = $this->l('Settings updated successfully.');
        }
    }

    public function hookFilterCategoryContent($params)
    {
        $page_name = 'category';   
        // Vérifiez si 'additional_description' est défini et non vide
        if (isset($params['object']['additional_description']) && !empty($params['object']['additional_description'])) {
            // Appliquez le filtre à 'additional_description'
            $params['object']['additional_description'] = $this->getFilterContent($page_name, $params['object']['additional_description']);
        }
        return $params;
    }

    public function hookFilterCmsContent($params)
    {
        $page_name = 'cms';
        if (isset($params['object']['content']) && !empty($params['object']['content'])) {
            $params['object']['content'] = $this->getFilterContent($page_name, $params['object']['content']);
        }

        return $params;
    }

    public function hookDisplayBackofficeHeader()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') === true) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
    }

    public function getFilterContent($page_name, $io)
    {
        $class = new SeoInternalLinkingModel();
        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) $this->context->language->id;
        $rules = $class->getActiveRule($page_name, $id_lang, $id_shop);
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $terminate = (int) $rule['replacements'];
                $title = empty($rule['title']) ? '' : ' title="' . $rule['title'] . '"';
                $color = empty($rule['color']) ? 'color:inherit;' : 'color:' . $rule['color'];
                $target = ((int) $rule['target'] > 0) ? ' target="_blank"' : '';
                $rel = ((int) $rule['rel'] > 0) ? ' rel="nofollow"' : '';

                if ((int) strpos($rule['keywords'], ',') <= 0) {// if its a single word
                    $a_tag = '<a href="' . $rule['url'] . '"' . $title . $target . $rel . ' style="' . $color . '">';
                    $a_tag_close = '</a>';
                    $new_html_tag = $a_tag . $rule['keywords'] . $a_tag_close;
                    // traverser and replace keywords
                    $io = $this->domTraversing($io, $rule['keywords'], $new_html_tag, $terminate);
                } else {
                    $keys_array = explode(',', $rule['keywords']);
                    foreach ($keys_array as $keyword) {
                        $a_tag = '<a href="' . $rule['url'] . '"' . $title . $target . $rel . ' style="' . $color . '">';
                        $a_tag_close = '</a>';
                        $new_html_tag = $a_tag . $keyword . $a_tag_close;
                        // traverser and replace keywords
                        $io = $this->domTraversing($io, $keyword, $new_html_tag, $terminate);
                    }
                }
            }
        }

        return $io;
    }

    public function domTraversing($html_content, $find, $replace, $count = null)
    {
        $html = str_get_html($html_content);
        // array('span', 'textarea', 'p', 'ul', 'ol', 'li', 'table', 'th', 'tr', 'td', 'dl', 'dt', 'dd');
        $tagList = Configuration::get(
            'SEOINTERNALLINKING_TAGS',
            false,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        );

        if (!empty($tagList) && $tagList) {
            $tagList = explode(',', $tagList);
            if (!empty($tagList) && $tagList) {
                foreach ($tagList as $tag) {
                    $htmlTags = $html->find($tag);
                    if (isset($htmlTags) && $htmlTags) {
                        foreach ($htmlTags as $tg) {
                            if (trim($tg->innertext)) {
                                if (!preg_match('/<[^>]*>/', $tg->innertext)) {
                                    $tg->innertext = str_ireplace($find, $replace, $tg->innertext, $count);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $html->innertext;
    }
}
