<?php
/**
 * Loulou66
 * LpsTextBanner module for Prestashop
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php*
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Loulou66.fr <contact@loulou66.fr>
 *  @copyright loulou66.fr
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/lpstextbannerclass.php';
require_once dirname(__FILE__) . '/classes/lpstextbannerconfig.php';

class LpsTextBanner extends Module
{
    public $html;

    public $errors = [];

    public $_default_pagination;

    public $_pagination;

    public $list_id;

    public $orderby;

    public $fields_list;

    public $fields_Form;

    public $fields_value;

    public function __construct()
    {
        $this->name = 'lpstextbanner';
        $this->tab = 'front_office_features';
        $this->version = '8.0.1';
        $this->author = 'Loulou66';
        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => _PS_VERSION_];
        parent::__construct();
        $this->displayName = $this->l('LPS Top Banner PRO');
        $this->description = $this->l('Add banner with scrolling text in the header of your store');
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->_default_pagination = '10';
        $this->_pagination = ['10', '30', '50', '100', '300', '500'];
        $this->module_key = '2fb82e05bb1088ea23173d871fde1666';
    }
    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        return parent::install()
        && $this->registerHook('displayBanner')
        && $this->registerHook('displayHeader')
        && $this->registerHook('actionAdminControllerSetMedia')
        && $this->saveDefaultconfig();
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';
        return parent::uninstall();
    }
    public function saveDefaultconfig()
    {
        $shops = Shop::getShops(true);
        foreach ($shops as $shop) {
            $conf = new LpsTextBannerConfig();
            $conf->display_banner = 1;
            $conf->fixed_banner = 1;
            $conf->banner_background_color = '#333333';
            $conf->banner_text_color = '#FFFFFF';
            $conf->transition_effect = 'scrolling';
            $conf->directionH = 'righttoleft';
            $conf->directionV = 'bottomtotop';
            $conf->speedScroll = 20;
            $conf->displayTime = 3000;
            $conf->id_shop = (int) $shop['id_shop'];
            $conf->add();
        }
        return true;
    }
    public function getWarningMultishopHtml()
    {
        return $this->displayWarning(
            sprintf(
                $this->l('You cannot manage %s a "All Shops" or a "Group Shop" context,'),
                $this->displayName
            ) . ' ' .
            $this->l('select directly the shop you want to edit')
        );
    }
    public function hookActionAdminControllerSetMedia($params)
    {
        if ($this->context->controller->controller_name == 'AdminModules'
            && Tools::getValue('configure') == $this->name
        ) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/' . $this->name . 'admin.css');
            $this->context->controller->addJS($this->_path . 'views/js/' . $this->name . 'admin.js');
        }
    }
    public function getContent()
    {
        $this->html = '';
        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
                return $this->getWarningMultishopHtml();
            }
        }
        if (Tools::isSubmit('add' . $this->name) || Tools::isSubmit('update' . $this->name)) {
            return $this->renderFormMessage();
        }
        $id_lpstextbanner = Tools::getValue('id_lpstextbanner');
        if (Tools::isSubmit('save' . $this->name)) {
            $this->postProcess();
            if (!count($this->errors)) {
                if ($id_lpstextbanner) {
                    $lpsTextBannerClass = new LpsTextBannerClass((int) $id_lpstextbanner);
                } else {
                    $lpsTextBannerClass = new LpsTextBannerClass();
                }
                $lpsTextBannerClass->copyFromPost();
                $def_lang = Configuration::get('PS_LANG_DEFAULT');
                foreach (Language::getLanguages(true, (int) $this->context->shop->id) as $lang) {
                    if (empty($lpsTextBannerClass->message[$lang['id_lang']])) {
                        $lpsTextBannerClass->message[$lang['id_lang']] = $lpsTextBannerClass->message[$def_lang];
                    }
                    if (!empty($lpsTextBannerClass->link[$def_lang])
                        && empty($lpsTextBannerClass->link[$lang['id_lang']])) {
                        $lpsTextBannerClass->link[$lang['id_lang']] = $lpsTextBannerClass->link[$def_lang];
                    }
                }
                if (!$lpsTextBannerClass->save()) {
                    $this->errors[] = $this->displayError($this->l('Unable to save message'));
                } else {
                    $this->html = $this->displayConfirmation($this->l('Successful creation'));
                }
            } else {
                foreach ($this->errors as $error) {
                    $this->html .= $error;
                }
                return $this->html . $this->renderFormMessage();
            }
        }
        if (Tools::isSubmit('saveBannerConfig')) {
            $this->postProcess();
            if (!count($this->errors)) {
                $lpsTextBannerConfig = LpsTextBannerConfig::getByIdShop((int) $this->context->shop->id);
                $lpsTextBannerConfig->copyFromPost();
                if (!$lpsTextBannerConfig->save()) {
                    $this->errors[] = $this->displayError($this->l('Unable to save configuration'));
                } else {
                    $this->html = $this->displayConfirmation($this->l('Settings updated'));
                }
            }
        }
        if (Tools::isSubmit('updatePositions') && Tools::getValue('configure') == $this->name) {
            LpsTextBannerClass::updatePositions();
        }
        if (Tools::isSubmit('status' . $this->name)) {
            if (!LpsTextBannerClass::statusToggle((int) $id_lpstextbanner)) {
                $this->errors[] = $this->displayError($this->l('Unable to update status'));
            } else {
                $this->html .= $this->displayConfirmation($this->l('The status has been updated'));
            }
        }
        if (Tools::isSubmit('delete' . $this->name)) {
            $lpsTextBannerClass = new LpsTextBannerClass((int) $id_lpstextbanner);
            if (!$lpsTextBannerClass->delete()) {
                $this->html .= $this->displayError($this->l('Unable to delete the message'));
            } else {
                $this->updatePositionsAfterDelete();
                $this->html .= $this->displayConfirmation($this->l('Successful deletion.'));
            }
        }
        if (Tools::isSubmit('submitReset' . $this->name)) {
            $this->context->controller->processResetFilters($this->name);
        }
        if (Tools::isSubmit('submitBulkdelete' . $this->name)) {
            $boxSelected = Tools::getValue('lpstextbannerBox');
            $res = true;
            foreach ($boxSelected as $id) {
                $lpsTextBannerClass = new LpsTextBannerClass((int) $id);
                $res &= $lpsTextBannerClass->delete();
            }
            if ($res) {
                $this->updatePositionsAfterDelete();
                $this->html .= $this->displayConfirmation($this->l('The selection has been successfully deleted.'));
            }
        }
        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                $this->html .= $error;
            }
        }
        return $this->html . $this->initList() . $this->initFormConfig();
    }
    public function postProcess()
    {
        if (Tools::isSubmit('save' . $this->name)) {
            $deflang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            if (empty(Tools::getValue('message_' . Configuration::get('PS_LANG_DEFAULT')))) {
                $this->errors[] = $this->displayError(
                    sprintf($this->l('The « message » field is required at least in %s.'), $deflang->name)
                );
            }
            if ((Tools::getValue('display_link') == 1)
                && empty(Tools::getValue('link_' . Configuration::get('PS_LANG_DEFAULT')))
            ) {
                $this->errors[] = $this->displayError(
                    sprintf($this->l('The « Link » field is required at least in %s.'), $deflang->name)
                );
            }
            foreach (Language::getLanguages(true, (int) $this->context->shop->id) as $lang) {
                if (!Validate::isMessage(Tools::getValue('message_' . $lang['id_lang']))) {
                    $this->errors[] = $this->displayError(
                        sprintf($this->l('The « message » field (%s) is invalid.'), $lang['name'])
                    );
                }
                if (Tools::strlen(Tools::getValue('message_' . $lang['id_lang'])) > 100) {
                    $this->errors[] = $this->displayError(
                        sprintf(
                            $this->l('The length of message must not exceed 100 characters in %s.'),
                            $lang['name']
                        )
                    );
                }
                if ((Tools::getValue('display_link') == 1)
                    && !empty(Tools::getValue('link_' . $lang['id_lang']))
                    && !Validate::isAbsoluteUrl(Tools::getValue('link_' . $lang['id_lang']))
                ) {
                    $this->errors[] = $this->displayError(
                        sprintf($this->l('The « Link » field (%s) is invalid.'), $lang['name'])
                    );
                }
            }
        }
        if (Tools::isSubmit('saveBannerConfig')) {
            if (!Validate::isColor(Tools::getValue('banner_background_color'))) {
                $this->errors[] = $this->displayError(
                    $this->l('The « Banner background color » field is invalid.')
                );
            }
            if (!Validate::isColor(Tools::getValue('banner_text_color'))) {
                $this->errors[] = $this->displayError(
                    $this->l('The « Banner Text Color » field is invalid.')
                );
            }
        }
    }
    public function updatePositionsAfterDelete()
    {
        $allLpsTextBanner = LpsTextBannerClass::getTextBannerIds((int) $this->context->shop->id);
        $position = 0;
        foreach ($allLpsTextBanner as $textBanner) {
            $lpsTextBannerClass = new LpsTextBannerClass((int) $textBanner['id_lpstextbanner']);
            $lpsTextBannerClass->position = $position;
            $lpsTextBannerClass->save();
            ++$position;
        }
    }
    public function initList()
    {
        $this->list_id = 'lpstextbanner';
        $this->orderby = Tools::getValue($this->list_id . 'Orderby')
            ? Tools::getValue($this->list_id . 'Orderby')
            : 'position';
        $this->fields_list = [
            'id_lpstextbanner' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'orderby' => true,
                'search' => true,
            ],
            'message' => [
                'title' => $this->l('Message'),
                'type' => 'text',
                'orderby' => true,
                'search' => true,
            ],
            'link' => [
                'title' => $this->l('Link'),
                'type' => 'text',
                'orderby' => false,
                'search' => false,
            ],
            'position' => [
                'title' => $this->l('Position'),
                'position' => 'position',
                'align' => 'center',
                'orderby' => false,
                'search' => false,
            ],
            'active' => [
                'title' => $this->l('Active'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'text-center',
                'orderby' => false,
                'search' => false,
            ],
        ];
        $helperList = new HelperList();
        $helperList->simple_header = false;
        $helperList->name_controller = $this->name;
        $helperList->table_id = 'module-' . $this->name;
        $helperList->table = $this->list_id;
        $helperList->identifier = 'id_' . $this->list_id;
        $helperList->_default_pagination = $this->_default_pagination;
        $helperList->_pagination = $this->_pagination;
        $helperList->orderBy = $this->orderby;
        $helperList->orderWay = 'ASC';
        $helperList->position_identifier = 'position';
        $helperList->position_group_identifier = 0;
        $helperList->module = $this;
        $helperList->actions = ['edit', 'delete'];
        $helperList->no_link = true;
        if (Shop::isFeatureActive()
            && (Shop::getContext() != Shop::CONTEXT_SHOP || Shop::getContext() != Shop::CONTEXT_ALL)
        ) {
            $helperList->shopLinkType = true;
        } else {
            $helperList->shopLinkType = false;
        }
        $helperList->title = $this->l('Message list');
        $helperList->token = Tools::getAdminTokenLite('AdminModules');
        $helperList->show_toolbar = 1;
        $helperList->currentIndex = AdminController::$currentIndex .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helperList->toolbar_btn = [
            'new' => [
                'href' => AdminController::$currentIndex .
                    '&configure=' . $this->name .
                    '&tab_module=' . $this->tab .
                    '&module_name=' . $this->name .
                    '&add' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Add new message'),
            ],
        ];
        $helperList->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('These message will be deleted. Please confirm.'),
            ],
        ];
        $helperList->listTotal = count($this->getMessagesList(true));
        return $helperList->generateList($this->getMessagesList(false), $this->fields_list);
    }
    public function getMessagesList($listTotal = false, $start = 0, $limit = null)
    {
        $sql = 'SELECT lpstb.*,  lpstbl.*
            FROM `' . _DB_PREFIX_ . 'lpstextbanner` lpstb
            LEFT JOIN `' . _DB_PREFIX_ . 'lpstextbanner_lang` lpstbl
            ON (lpstb.`id_lpstextbanner`= lpstbl.`id_lpstextbanner`)
            WHERE lpstb.`id_shop`=' . (int) $this->context->shop->id . '
            AND lpstbl.`id_lang`=' . (int) $this->context->language->id;
        if (Tools::getValue('submitFilter' . $this->list_id)) {
            foreach ($this->fields_list as $f => $field) {
                $filter = Tools::getValue($this->list_id . 'Filter_' . $f);
                $myfield = $field;
                if (!empty($filter)) {
                    $sql .= ' AND ';
                    $sql .= (isset($field['lang']) && $field['lang']) ? 'lpstbl.`' . $f . '`' : 'lpstb.`' . $f . '`';
                    $sql .= ' LIKE "%' . $filter . '%"';
                }
                unset($myfield);
            }
        }
        if ($orderby = Tools::getValue($this->list_id . 'Orderby')) {
            $alias = ($orderby == 'label') ? 'lpstbl.`' : 'lpstb.`';
            $sql .= ' ORDER BY ' . $alias . $orderby . '` ' . Tools::getValue($this->list_id . 'Orderway');
            $this->context->cookie->{$this->list_id . 'Orderby'} = $orderby;
            $this->context->cookie->{$this->list_id . 'Orderway'} = Tools::getValue($this->list_id . 'Orderway');
        } else {
            $sql .= ' ORDER BY lpstb.`position` ASC';
            $this->context->cookie->{$this->list_id . 'Orderby'} = 'position';
            $this->context->cookie->{$this->list_id . 'Orderway'} = 'ASC';
        }
        if (empty($limit)) {
            if (isset($this->context->cookie->{$this->list_id . '_pagination'})
                && $this->context->cookie->{$this->list_id . '_pagination'}
            ) {
                $limit = $this->context->cookie->{$this->list_id . '_pagination'};
            } else {
                $limit = $this->_default_pagination;
            }
        }
        $limit = (int) Tools::getValue($this->list_id . '_pagination', $limit);
        if (in_array($limit, $this->_pagination) && $limit != $this->_default_pagination) {
            $this->context->cookie->{$this->list_id . '_pagination'} = $limit;
        } else {
            unset($this->context->cookie->{$this->list_id . '_pagination'});
        }
        if ((int) Tools::getValue('submitFilter' . $this->list_id)) {
            $start = ((int) Tools::getValue('submitFilter' . $this->list_id) - 1) * $limit;
        } elseif (empty($start) && isset($this->context->cookie->{$this->list_id . '_start'})) {
            $start = $this->context->cookie->{$this->list_id . '_start'};
        }
        if ($start) {
            $this->context->cookie->{$this->list_id . '_start'} = $start;
        } elseif (isset($this->context->cookie->{$this->list_id . '_start'})) {
            unset($this->context->cookie->{$this->list_id . '_start'});
        }
        if (!$listTotal) {
            $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;
        }
        $list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($list as &$row) {
            if (Shop::isFeatureActive()) {
                $row['shop_name'] = $this->context->shop->name;
            }
        }
        return $list;
    }
    public function initFormConfig()
    {
        $list_transition = [
            ['id' => 'scrolling', 'name' => $this->l('Scrolling')],
            ['id' => 'typewriter', 'name' => $this->l('Typewriter')],
            ['id' => 'horizontal_slider', 'name' => $this->l('Horizontal Slider')],
            ['id' => 'vertical_slider', 'name' => $this->l('Vertical Slider')],
        ];
        $directionH = [
            ['id' => 'righttoleft', 'name' => $this->l('From right to left')],
            ['id' => 'lefttoright', 'name' => $this->l('From left to right')],
        ];
        $directionV = [
            ['id' => 'bottomtotop', 'name' => $this->l('From bottom to top')],
            ['id' => 'toptobottom', 'name' => $this->l('From top to bottom')],
        ];
        $this->fields_Form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_lpstextbanner_config',
                        'default_value' => '',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display banner'),
                        'name' => 'display_banner',
                        'hint' => $this->l('Display banner on website'),
                        'desc' => $this->l('Display banner on website'),
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
                        'type' => 'switch',
                        'label' => $this->l('Fixed banner'),
                        'name' => 'fixed_banner',
                        'hint' => $this->l('Display banner in fixed position'),
                        'desc' => $this->l('Display banner in fixed position'),
                        'is_bool' => true,
                        'form_group_class' => 'display_banner',
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
                        'type' => 'color',
                        'label' => $this->l('Banner background color'),
                        'name' => 'banner_background_color',
                        'class' => 'colorpicker',
                        'desc' => $this->l('Set the background color of the banner'),
                        'hint' => $this->l('Set the background color of the banner'),
                        'form_group_class' => 'display_banner colorpicker',
                        'default_value' => '#333333',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Banner text color'),
                        'name' => 'banner_text_color',
                        'class' => 'colorpicker',
                        'desc' => $this->l('Set the text color of the banner'),
                        'hint' => $this->l('Set the text color of the banner'),
                        'form_group_class' => 'display_banner colorpicker',
                        'default_value' => '#333333',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Transition effect'),
                        'name' => 'transition_effect',
                        'desc' => $this->l('Select the transition effect of the banner text'),
                        'hint' => $this->l('Select the transition effect of the banner text'),
                        'default_value' => 'scrolling',
                        'form_group_class' => 'display_banner',
                        'options' => [
                            'query' => $list_transition,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'html',
                        'label' => '',
                        'html_content' => $this->displayWarnigSlider(),
                        'name' => '',
                        'form_group_class' => 'display_banner displayWarnigSlider',
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Direction'),
                        'name' => 'directionH',
                        'desc' => $this->l('Set the direction of scrolling of horizontal slider'),
                        'hint' => $this->l('Set the direction of scrolling of horizontal slider'),
                        'default_value' => 'lefttoright',
                        'form_group_class' => 'display_banner directionH',
                        'options' => [
                            'query' => $directionH,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Direction'),
                        'name' => 'directionV',
                        'desc' => $this->l('Set the direction of scrolling of vertical slider'),
                        'hint' => $this->l('Set the direction of scrolling of vertical slider'),
                        'default_value' => 'toptobottom',
                        'form_group_class' => 'display_banner directionV',
                        'options' => [
                            'query' => $directionV,
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Scrolling speed'),
                        'html_content' => $this->displaySpeedScrollRange(),
                        'desc' => $this->l('Set the scrolling speed of all messages'),
                        'hint' => $this->l('Set the scrolling speed of all messages'),
                        'name' => '',
                        'form_group_class' => 'display_banner speedScroll',
                        'default_value' => 1000,
                    ],
                    [
                        'type' => 'html',
                        'label' => $this->l('Display Time'),
                        'html_content' => $this->displayDisplayTime(),
                        'desc' => $this->l('Define the display time of each message'),
                        'hint' => $this->l('Define the display time of each message'),
                        'name' => '',
                        'form_group_class' => 'display_banner displayTime',
                        'default_value' => 1000,
                    ],
                ],
                'submit' => [
                    'name' => 'saveBannerConfig',
                    'title' => $this->l('Save'),
                    'class' => 'button btn btn-default',
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->submit_action = 'saveBannerConfig';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        Media::addJsDef(['isformconfig' => true]);
        return $helper->generateForm([$this->fields_Form]);
    }
    public function displayWarnigSlider()
    {
        $tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/displaywarnigslider.tpl';
        return $this->context->smarty->createTemplate($tpl)->fetch();
    }
    public function displaySpeedScrollRange()
    {
        $tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/speedrange.tpl';
        $cookieRange = $this->context->smarty->createTemplate($tpl);
        $cookieRange->assign([
            'rangeid' => 'speedScroll',
            'rangeMin' => 1,
            'rangeStep' => 1,
            'rangeMax' => 20,
            'rangeValue' => LpsTextBannerConfig::getConfig('speedScroll'),
            'sufix' => 'S',
        ]);
        return $cookieRange->fetch();
    }
    public function displayDisplayTime()
    {
        $tpl = _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/speedrange.tpl';
        $displayTime = $this->context->smarty->createTemplate($tpl);
        $displayTime->assign([
            'rangeid' => 'displayTime',
            'rangeMin' => 1000,
            'rangeStep' => 100,
            'rangeMax' => 10000,
            'rangeValue' => LpsTextBannerConfig::getConfig('displayTime'),
            'sufix' => 'ms',
        ]);
        return $displayTime->fetch();
    }
    public function getConfigValues()
    {
        $configValues = [];
        $lpsTextBannerConfig = LpsTextBannerConfig::getByIdShop((int) $this->context->shop->id);
        $configValues['id_lpstextbanner_config'] = $lpsTextBannerConfig->id_lpstextbanner_config;
        $configValues['display_banner'] = $lpsTextBannerConfig->display_banner;
        $configValues['fixed_banner'] = $lpsTextBannerConfig->fixed_banner;
        $configValues['banner_background_color'] = $lpsTextBannerConfig->banner_background_color;
        $configValues['banner_text_color'] = $lpsTextBannerConfig->banner_text_color;
        $configValues['transition_effect'] = $lpsTextBannerConfig->transition_effect;
        $configValues['directionH'] = $lpsTextBannerConfig->directionH;
        $configValues['directionV'] = $lpsTextBannerConfig->directionV;
        return $configValues;
    }
    public function renderFormMessage()
    {
        $this->fields_Form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Message'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_lpstextbanner',
                        'default_value' => '',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Message'),
                        'name' => 'message',
                        'lang' => true,
                        'hint' => $this->l('Invalid characters:') .
                            ' <>{}. ' .
                            $this->l('Maximum message length: 100 characters'),
                        'desc' => $this->l('Invalid characters:') .
                            ' <>{}. ' .
                            $this->l('Maximum message length: 100 characters'),
                        'default_value' => '',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Add a link'),
                        'name' => 'display_link',
                        'hint' => $this->l('Add a clickable link on the message'),
                        'desc' => $this->l('Add a clickable link on the message'),
                        'is_bool' => true,
                        'default_value' => false,
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
                        'label' => $this->l('Link'),
                        'name' => 'link',
                        'lang' => true,
                        'hint' => $this->l('If defined, the whole message will be clickable.') .
                            ' ' .
                            $this->l('Please enter the full URL (including http:// or https://)'),
                        'desc' => $this->l('If defined, the whole message will be clickable.') .
                            ' ' .
                            $this->l('Please enter the full URL (including http:// or https://)'),
                        'default_value' => '',
                        'form_group_class' => 'display_link',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Open link in a new tab'),
                        'name' => 'target',
                        'hint' => $this->l('Open message link in a new tab'),
                        'desc' => $this->l('Open message link in a new tab'),
                        'is_bool' => true,
                        'default_value' => true,
                        'form_group_class' => 'display_link',
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
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'active',
                        'hint' => $this->l('Activate the message'),
                        'desc' => $this->l('Activate the message'),
                        'is_bool' => true,
                        'default_value' => true,
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
                ],
                'submit' => [
                    'name' => 'save' . $this->name,
                    'title' => $this->l('Save'),
                    'class' => 'button btn btn-default',
                ],
                'buttons' => [
                    [
                        'id' => $this->name . '_form_cancel_btn',
                        'href' => AdminController::$currentIndex .
                            '&configure=' . $this->name .
                            '&tab_module=' . $this->tab .
                            '&module_name=' . $this->name .
                            '&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Cancel'),
                        'icon' => 'process-icon-cancel',
                    ],
                ],
            ],
        ];
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->submit_action = 'save' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex .
            '&configure=' . $this->name .
            '&tab_module=' . $this->tab .
            '&module_name=' . $this->name;
        $helper->tpl_vars = [
            'fields_value' => $this->getFieldsValue(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        Media::addJsDef(['isformmessage' => true]);
        return $helper->generateForm([$this->fields_Form]);
    }
    public function getFieldsValue()
    {
        $id_lpstextbanner = Tools::getValue('id_lpstextbanner');
        $object = [];
        if ($id_lpstextbanner) {
            $object = new LpsTextBannerClass((int) $id_lpstextbanner);
        }
        foreach ($this->fields_Form as $fieldset) {
            if (isset($fieldset['input'])) {
                foreach ($fieldset['input'] as $input) {
                    if (!isset($this->fields_value[$input['name']])) {
                        if (isset($input['lang']) && $input['lang']) {
                            foreach (Language::getIDs(true, (int) $this->context->shop->id) as $id_lang) {
                                $field_value = $this->getFieldValue($object, $input['name'], $id_lang);
                                $this->fields_value[$input['name']][$id_lang] = $field_value;
                            }
                        } else {
                            $field_value = $this->getFieldValue($object, $input['name']);
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
    public function getFieldValue($object, $key, $id_lang = null)
    {
        if ($id_lang) {
            $default_value = (isset($object->id) && $object->id && isset($object->{$key}[$id_lang])) ?
                $object->{$key}[$id_lang] : false;
        } else {
            $default_value = isset($object->{$key}) ? $object->{$key} : false;
        }
        return Tools::getValue($key . ($id_lang ? '_' . $id_lang : ''), $default_value);
    }
    public function hookDisplayHeader()
    {
        if (Module::isEnabled($this->name)) {
            $this->context->controller->addCSS($this->_path . 'views/css/front/' . $this->name . 'front.css', 'all');
            $this->context->controller->addCSS($this->_path . 'libs/slick/css/slick.css', 'all');
            $this->context->controller->addCSS($this->_path . 'libs/slick/css/slick-theme.css', 'all');
            $this->context->controller->addJS($this->_path . 'libs/slick/js/slick.js');
            $this->context->controller->addJS($this->_path . 'libs/typedjs/typed.min.js');
            $this->context->controller->addJS($this->_path . 'views/js/' . $this->name . '.js');
        }
    }
    public function hookDisplayBanner()
    {
        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) $this->context->language->id;
        $lpsTextBannerConfig = LpsTextBannerConfig::getByIdShop((int) $this->context->shop->id);
        $messages = LpsTextBannerClass::getAllMessages($id_lang, $id_shop);
        if ($messages) {
            $this->context->smarty->assign(['messages' => $messages, 'lpsTextBannerConfig' => $lpsTextBannerConfig]);
            return $this->display(__FILE__, $this->name . '.tpl');
        }
    }
}
