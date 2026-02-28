<?php
/**
 * 2016-2018 ZLabSolutions
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLab Solutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions
 */

// require_once(_PS_MODULE_DIR_.'productsindex/classes/ajaxController.php');
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminproductsindexController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->html = '';
        $this->meta_title = $this->l('Products Index');
        $context = Context::getContext();

        if (Tools::getValue('zlab_ajax') == 1) {
            AjaxZlabController::readAjax();
            exit;
        } else {
            if (isset($context->employee) && ($context->employee->id > 0)) {
                $id_employee = $context->employee->id;
                $token = self::getAdminToken($id_employee);
                Tools::redirectAdmin("index.php?controller=AdminModules&token=$token&configure=productsindex");
            } else {
                exit;
            }
        }
    }

    public function renderView()
    {
        $context = Context::getContext();

        if (isset($context->employee) && ($context->employee->id > 0)) {
            $id_employee = $context->employee->id;
            $token = self::getAdminToken($id_employee);
            Tools::redirectAdmin("index.php?controller=AdminModules&token=$token&configure=productsindex");
        } else {
            exit;
        }
    }

    public static function getAdminToken($id_employee)
    {
        $tab = 'AdminModules';

        return Tools::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) $id_employee);
    }
}
