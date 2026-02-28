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
class PrestaBtwoBRegistrationpendingaccountModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $registrationConfiguration = new PrestaBtwoBRegistrationConfiguration();
        $configuration = $registrationConfiguration->getConfigId();
        $registrationConfigure = new PrestaBtwoBRegistrationConfiguration($configuration, $this->context->language->id);
        $this->context->smarty->assign(
            array(
                'presta_pending_message' => $registrationConfigure->pending_account_message_text,
            )
        );
        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/presta_pending_account.tpl');
    }
}
