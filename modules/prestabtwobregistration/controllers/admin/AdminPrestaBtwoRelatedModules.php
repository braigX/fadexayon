<?php
/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class AdminPrestaBtwoRelatedModulesController extends ModuleAdminController
{
    private $tabClassName = 'AdminPrestaBtwoRelatedModules';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        parent::__construct();
    }

    public function initModal()
    {
        parent::initModal();
        $languages = Language::getLanguages(false);
        $translateLinks = array();
        $module = Module::getInstanceByName($this->module->name);
        if (false === $module) {
            return;
        }
        $isNewTranslateSystem = $module->isUsingNewTranslationSystem();
        $link = Context::getContext()->link;
        foreach ($languages as $lang) {
            if ($isNewTranslateSystem) {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslationSf',
                    true,
                    array(
                        'lang' => $lang['iso_code'],
                        'type' => 'modules',
                        'selected' => $module->name,
                        'locale' => $lang['locale']
                    )
                );
            } else {
                $translateLinks[$lang['iso_code']] = $link->getAdminLink(
                    'AdminTranslations',
                    true,
                    array(),
                    array(
                        'type' => 'modules',
                        'module' => $module->name,
                        'lang' => $lang['iso_code']
                    )
                );
            }
        }
        $tabLink = 'index.php?tab=AdminTranslations&token=';
        $adminTranslation = Tools::getAdminTokenLite('AdminTranslations') . '&type=modules&module=';
        $configure = $this->module->name . '&lang=';
        $this->context->smarty->assign(
            array(
                'trad_link' => $tabLink . $adminTranslation . $configure,
                'module_languages' => $languages,
                'module_name' => $this->module->name,
                'translateLinks' => $translateLinks
            )
        );

        $modal_content = $this->context->smarty->fetch('controllers/modules/modal_translation.tpl');
        $this->modals[] = array(
            'modal_id' => 'moduleTradLangSelect',
            'modal_class' => 'modal-sm',
            'modal_title' => $this->l('Translate this module'),
            'modal_content' => $modal_content
        );
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        if ($this->display == 'view') {
            $this->page_header_toolbar_btn['desc-module-back'] = array(
                'href' => 'index.php?controller=AdminModules&token=' . Tools::getAdminTokenLite('AdminModules'),
                'icon' => 'process-icon-back',
                'desc' => $this->l('Go to module manager')
            );
            $this->page_header_toolbar_btn['desc-module-reload'] = array(
                'href' => 'index.php?controller=' .
                    $this->tabClassName .
                    '&token=' .
                    Tools::getAdminTokenLite($this->tabClassName) .
                    '&reload=1',
                'icon' => 'process-icon-refresh',
                'desc' => $this->l('Refresh')
            );
            $this->page_header_toolbar_btn['desc-module-translate'] = array(
                'href' => '#',
                'desc' => $this->l('Translate'),
                'icon' => 'process-icon-flag',
                'modal_target' => '#moduleTradLangSelect'
            );
            $this->page_header_toolbar_btn['desc-module-hook'] = array(
                'href' => 'index.php?tab=AdminModulesPositions&token=' .
                    Tools::getAdminTokenLite('AdminModulesPositions') .
                    '&show_modules=' .
                    Module::getModuleIdByName($this->module->name),
                'icon' => 'process-icon-anchor',
                'desc' => $this->l('Manage hooks')
            );
        }
    }

    public function initToolbarTitle()
    {
        parent::initToolbarTitle();
        switch ($this->display) {
            case '':
            case 'view':
                array_pop($this->toolbar_title);
                    $this->toolbar_title[] = $this->l('Our Other Modules');
                break;
            case 'add':
            case 'edit':
        }
    }

    public function renderView()
    {
        $this->context->smarty->assign(
            array(
                'othr_modules_img_path' => _MODULE_DIR_ . $this->module->name . '/views/img/related_modules/',
            )
        );
        return parent::renderView();
    }

    public function setMedia($isNewTheme = false)
    {
        if ($isNewTheme || !$isNewTheme) {
            parent::setMedia(false);
            $this->context->controller->addCss(
                _MODULE_DIR_ . $this->module->name . '/views/css/admin/presta_related_modules.css'
            );
        }
    }
}
