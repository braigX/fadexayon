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
class IndexController extends IndexControllerCore
{
    public function initContent()
    {
        parent::initContent();
        if (Module::isEnabled('seointernallinking') == true) {
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<')) {
                $this->addJS(_THEME_JS_DIR_ . 'index.js');
                $page_name = Dispatcher::getInstance()->getController();
                $module = Module::getInstanceByName('seointernallinking');
                $this->context->smarty->assign([
                    'HOOK_HOME' => $module->getFilterContent($page_name, Hook::exec('displayHome')),
                    'HOOK_HOME_TAB' => Hook::exec('displayHomeTab'),
                    'HOOK_HOME_TAB_CONTENT' => Hook::exec('displayHomeTabContent'),
                ]);
                $this->setTemplate(_PS_THEME_DIR_ . 'index.tpl');
            } else {
                $page_name = Dispatcher::getInstance()->getController();
                $module = Module::getInstanceByName('seointernallinking');
                $this->context->smarty->assign([
                    'HOOK_HOME' => $module->getFilterContent($page_name, Hook::exec('displayHome')),
                ]);
                $this->setTemplate('index');
            }
        }
    }
}
