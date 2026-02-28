<?php
/**
 * 2007 - 2018 ZLabSolutions
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
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future. If you wish to customize module for your
 * needs please contact developer at http://zlabsolutions.com for more information.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLab Solutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions https://www.facebook.com/ZLabSolutions/
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
include_once _PS_MODULE_DIR_ . 'productsindex/classes/Zlabcustom.class.php';

class AjaxZlabController
{
    public static function readAjax()
    {
        switch (Tools::getValue('action')) {
            case 'updatesettings':
                Zlabcustomclasszl::updateSettings();
                break;
            case 'getconfigproducts':
                $obj = new ProductsIndexClass();
                $obj->getConfigProductsAjax();
                break;
            case 'apply_index':
                $obj = new ProductsIndexClass();
                $obj->applyIndexAjax();
                break;
            default:
                exit;
        }
    }
}
