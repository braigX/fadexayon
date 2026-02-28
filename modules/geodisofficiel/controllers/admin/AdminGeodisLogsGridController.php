<?php
/**
 * 2024 Novatis Agency - www.novatis-paris.fr.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@novatis-paris.fr so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    NOVATIS <info@novatis-paris.fr>
 *  @copyright 2024 NOVATIS
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Controller/Admin/GeodisControllerAdminAbstractMenu.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';

class AdminGeodisLogsGridController extends GeodisControllerAdminAbstractMenu
{
    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();

        parent::__construct();

        $this->bootstrap = true;
        $this->className = 'GeodisLog';
        $this->table = GEODIS_NAME_SQL.'_log';
        $this->list_no_link = true;
        $this->identifier = 'id_geodis_log';
        $this->allow_export = false;
        $this->_orderBy = 'id_geodis_log';
        $this->_orderWay = 'DESC';
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_geodis_log' => array(
                'title' => GeodisServiceTranslation::get('Admin.LogGrid.index.grid.id'),
                'class' => 'fixed-width-xs',
            ),
            'message' => array(
                'title' => GeodisServiceTranslation::get('Admin.LogGrid.index.grid.message'),
                'havingFilter' => true,
                'class' => 'message',
            ),
            'is_error' => array(
                'title' => GeodisServiceTranslation::get('Admin.LogGrid.index.grid.isError'),
                'type' => 'bool',
                'havingFilter' => true,
            ),
            'date_add' => array(
                'title' => GeodisServiceTranslation::get('Admin.LogGrid.index.grid.logDate'),
                'havingFilter' => true,
                'type' => 'datetime',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
            )
        );
        $this->specificConfirmDelete = false;
    }

    protected function removeToken($url)
    {
        return preg_replace('/&token=.*$/', '', $url);
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(_PS_MODULE_DIR_.'geodisofficiel//views/css/admin/log.css');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }
}
