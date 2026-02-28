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
class AdminInternalLinkingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->className = 'SeoInternalLinkingModel';
        $this->table = 'seointernallinking';
        $this->deleted = false;
        $this->identifier = 'id_seointernallinking';
        $this->lang = true;
        $this->bootstrap = true;
        $this->explicitSelect = true;

        parent::__construct();

        $this->context = Context::getContext();
        $this->fields_list = [
            'id_seointernallinking' => [
                'title' => $this->l('ID'),
                'align' => 'center', 'class' => 'fixed-width-xs',
            ],
            'title' => [
                'title' => $this->l('Title'),
                'align' => 'center',
                'lang' => true,
            ],
            'active' => [
                'title' => $this->l('Enabled'),
                'align' => 'center',
                'active' => 'active',
                'type' => 'bool',
            ],
            'target' => [
                'title' => $this->l('Open in new window'),
                'align' => 'center',
                'active' => 'target',
                'type' => 'bool',
            ],
            'rel' => [
                'title' => $this->l('Nofollow'),
                'align' => 'center',
                'active' => 'rel',
                'type' => 'bool',
            ],
        ];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);
        $type = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<') == true) ? 'radio' : 'switch';
        $types = ['index', 'cms', 'category', 'product'];
        $this->fields_form = [
            'tinymce' => false,
            'legend' => [
                'title' => $this->l('Add/Edit SEO Internal Linking'),
                'icon' => 'icon-link',
            ],
            'input' => [
                [
                    'type' => $type,
                    'label' => $this->l('Status'),
                    'name' => 'active',
                    'required' => true,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $this->l('It will be also used as title attribute of href tag.'),
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Link Color'),
                    'name' => 'color',
                    'desc' => $this->l('It will be color of href tag.'),
                ],
                [
                    'type' => $type,
                    'label' => $this->l('Open in new window'),
                    'name' => 'target',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => $type,
                    'label' => $this->l('Add Nofollow'),
                    'name' => 'rel',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Number of replacements'),
                    'name' => 'replacements',
                    'lang' => false,
                    'col' => 4,
                    'placeholder' => 'integer only',
                    'required' => false,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $this->l(
                        'Number of replacments per page, if this value is
                            1 than only first found text will be linked.'
                    ),
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'types',
                    'label' => $this->l('Target Page Types'),
                    'required' => true,
                    'values' => [
                        'query' => [
                            ['id' => 'index',
                                'name' => $this->l('Home Page'),
                                'val' => 'index',
                            ],
                            ['id' => 'cms',
                                'name' => $this->l('CMS Pages'),
                                'val' => 'cms',
                            ],
                            ['id' => 'product',
                                'name' => $this->l('Product Pages'),
                                'val' => 'product',
                            ],
                            ['id' => 'category',
                                'name' => $this->l('Category Pages'),
                                'val' => 'category',
                            ],
                        ],
                        'id' => 'val',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Target Link'),
                    'name' => 'url',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $this->l('It will used as link for href tag.'),
                ],
                [
                    'type' => 'tags',
                    'label' => $this->l('Keywords'),
                    'name' => 'keywords',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}',
                    'desc' => $this->l('To add words, write something, and then press the "Enter" key OR comma.'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }
        if ($obj->id) {
            $stack = $obj->types;
            $stack = explode(',', $stack);
            foreach ($types as $type) {
                if (is_array($stack) && in_array($type, $stack)) {
                    $this->fields_value['types_' . $type] = true;
                }
            }
        }

        return parent::renderForm();
    }

    public function init()
    {
        parent::init();
        Shop::addTableAssociation($this->table, ['type' => 'shop']);
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'seointernallinking_shop` sa ON (a.`id_seointernallinking` = sa.`id_seointernallinking` AND sa.id_shop = ' .
            (int) $this->context->shop->id . ') ';
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = ' . (int) Context::getContext()->shop->id;
        }
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitAddseointernallinking')) {
            // $lang_def = (int)Configuration::get('PS_LANG_DEFAULT');
            $color = Tools::getValue('color');
            $replacements = Tools::getValue('replacements');
            $languages = Language::getLanguages(false);

            if (!empty($color) && !Validate::isColor($color)) {
                $this->errors[] = $this->l('Please fill correct value in color field.');
            }
            if (!empty($replacements) && !Validate::isInt($replacements)) {
                $this->errors[] = $this->l('Please fill correct value in replacements field.');
            }
            if (!Tools::getValue('types_index') && !Tools::getValue('types_cms') && !Tools::getValue('types_product') && !Tools::getValue('types_category')) {
                $this->errors[] = $this->l('You must select at least one page type or more.');
            }

            foreach ($languages as $lang) {
                $title = Tools::getValue('title_' . $lang['id_lang']);
                $target = Tools::getValue('url_' . $lang['id_lang']);
                $keys = Tools::getValue('keywords_' . $lang['id_lang']);

                if (empty($title) || !Validate::isLabel($title)) {
                    $this->errors[] = sprintf($this->l('Title field value is invalid in %s.'), $lang['name']);
                }

                if (empty($target) || !Validate::isAbsoluteUrl($target)) {
                    $this->errors[] = sprintf($this->l('Please fill Target Link fields with proper link in %s.'), $lang['name']);
                }

                if (empty($keys) || !Validate::isTagsList($keys)) {
                    $this->errors[] = sprintf($this->l('Please fill keywords to target in %s.'), $lang['name']);
                }
            }
        }
        parent::initProcess();
    }

    public function postProcess()
    {
        parent::postProcess();
        $this->loadObject(true);
        if (Tools::isSubmit('active' . $this->table)) {
            $this->object->active = !$this->object->active;
            if (!$this->object->save()) {
                $this->errors[] = $this->l('Unsuccessfully status updated.');
            } else {
                $this->confirmations[] = $this->l('Status updated successfully.');
            }
        } elseif (Tools::isSubmit('target' . $this->table)) {
            $this->object->target = !$this->object->target;
            if (!$this->object->save()) {
                $this->errors[] = $this->l('Unsuccessfully updated.');
            } else {
                $this->confirmations[] = $this->l('Updated successfully.');
            }
        } elseif (Tools::isSubmit('rel' . $this->table)) {
            $this->object->rel = !$this->object->rel;
            if (!$this->object->save()) {
                $this->errors[] = $this->l('Unsuccessfully updated.');
            } else {
                $this->confirmations[] = $this->l('Updated successfully.');
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addjQueryPlugin('tagify', null, false);
    }
}
