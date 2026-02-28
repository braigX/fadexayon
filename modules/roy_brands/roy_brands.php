<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Roy_Brands extends Module implements WidgetInterface
{
    protected $templateFile;

    public function __construct()
    {
        $this->name = 'roy_brands';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'RoyThemes';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans(
            'Roy Brands Slider',
            array(),
            'Modules.RoyBrands.Admin'
        );
        $this->description = $this->trans(
            'Displays a block listing product brands.',
            array(),
            'Modules.RoyBrands.Admin'
        );
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:roy_brands/views/templates/hook/roy_brands.tpl';
    }

    public function install()
    {
        Configuration::updateValue('ROY_BRAND_DISPLAY_TYPE', 'brand_logo');
        Configuration::updateValue('ROY_BRAND_DISPLAY_TEXT_NB', 12);

        return parent::install() &&
            $this->registerHook('displayFooterBefore') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('actionObjectManufacturerDeleteAfter') &&
            $this->registerHook('actionObjectManufacturerAddAfter') &&
            $this->registerHook('actionObjectManufacturerUpdateAfter')
        ;
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('ROY_BRAND_DISPLAY_TYPE')
            && Configuration::deleteByName('ROY_BRAND_DISPLAY_TEXT_NB');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitRoyBrands')) {
            $type = Tools::getValue('ROY_BRAND_DISPLAY_TYPE');
            $text_nb = (int)Tools::getValue('ROY_BRAND_DISPLAY_TEXT_NB');

            if ('brand_text' === $type && !Validate::isUnsignedInt($text_nb)) {
                $errors[] = $this->trans(
                    'There is an invalid number of elements.',
                    array(),
                    'Modules.RoyBrands.Admin'
                );
            } elseif (!in_array($type, array('brand_text', 'brand_logo'))) {
                $errors[] = $this->trans(
                    'Please activate at least one system list.',
                    array(),
                    'Modules.RoyBrands.Admin'
                );
            } else {
                Configuration::updateValue('ROY_BRAND_DISPLAY_TYPE', $type);
                Configuration::updateValue('ROY_BRAND_DISPLAY_TEXT_NB', $text_nb);
                $this->_clearCache('*');
            }

            if (isset($errors) && count($errors)) {
                $output .= $this->displayError(implode('<br />', $errors));
            } else {
                $output .= $this->displayConfirmation($this->trans(
                    'Settings updated.',
                    array(),
                    'Admin.Global'
                ));
            }
        }

        return $output.$this->renderForm();
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        return parent::_clearCache($this->templateFile);
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans(
                        'Settings',
                        array(),
                        'Admin.Global'
                    ),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->trans(
                            'Type of display',
                            array(),
                            'Modules.RoyBrands.Admin'
                        ),
                        'name' => 'ROY_BRAND_DISPLAY_TYPE',
                        'required' => true,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'brand_logo',
                                    'name' => $this->trans(
                                        'Logos list',
                                        array(),
                                        'Modules.RoyBrands.Admin'
                                    ),
                                ),
                                array(
                                    'id' => 'brand_text',
                                    'name' => $this->trans(
                                        'Text list',
                                        array(),
                                        'Modules.RoyBrands.Admin'
                                    ),
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->trans(
                            'Number of elements to display',
                            array(),
                            'Modules.RoyBrands.Admin'
                        ),
                        'desc' => $this->trans(
                            'Total quantity of logos to display',
                            array(),
                            'Modules.RoyBrands.Admin'
                        ),
                        'name' => 'ROY_BRAND_DISPLAY_TEXT_NB',
                        'class' => 'fixed-width-xs'
                    ),
                ),
                'submit' => array(
                    'title' => $this->trans(
                        'Save',
                        array(),
                        'Admin.Actions'
                    ),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang =
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?
            Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') :
            0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitRoyBrands';
        $helper->currentIndex = $this->context->link->getAdminLink(
                'AdminModules',
                false
            ) .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'ROY_BRAND_DISPLAY_TYPE' => Tools::getValue(
                'ROY_BRAND_DISPLAY_TYPE',
                Configuration::get('ROY_BRAND_DISPLAY_TYPE')
            ),
            'ROY_BRAND_DISPLAY_TEXT_NB' => Tools::getValue(
                'ROY_BRAND_DISPLAY_TEXT_NB',
                Configuration::get('ROY_BRAND_DISPLAY_TEXT_NB')
            ),
        );
    }

    public function renderWidget(
        $hookName = null,
        array $configuration = array()
    ) {

          if ($hookName == null && isset($configuration['hook'])) {
              $hookName = $configuration['hook'];
          }

        $cacheId = $this->getCacheId('roy_brands');
        $isCached = $this->isCached($this->templateFile, $cacheId);

        if (!$isCached) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $cacheId);
    }

    public function getWidgetVariables(
        $hookName = null,
        array $configuration = array()
    ) {
        $brands = Manufacturer::getManufacturers(
            false,
            (int)Context::getContext()->language->id,
            $active = true,
            $p = false,
            $n = false,
            $allGroup = false,
            $group_by = false,
            $withProduct = true
        );

        if (!empty($brands)) {
            foreach ($brands as &$brand) {
                $brand['image'] = $this->context->language->iso_code . '-small_default';
                $brand['link'] = $this->context->link->getManufacturerLink($brand['id_manufacturer']);
                $fileExist = file_exists(
                    _PS_MANU_IMG_DIR_ . $brand['id_manufacturer'] . '-' .
                    ImageType::getFormattedName('small') . '.jpg'
                );

                if ($fileExist) {
                    $brand['image'] = $brand['id_manufacturer'];
                }
            }
        }

        return array(
            'brands' => $brands,
            'page_link' => $this->context->link->getPageLink('manufacturer'),
            'manu_dir' => _PS_MANU_IMG_DIR_,
            'text_list_nb' => Configuration::get('ROY_BRAND_DISPLAY_TEXT_NB'),
            'roy_brand_display_type' => Configuration::get('ROY_BRAND_DISPLAY_TYPE'),
            'display_link_brand' => Configuration::get('PS_DISPLAY_SUPPLIERS'),
        );
    }
}
