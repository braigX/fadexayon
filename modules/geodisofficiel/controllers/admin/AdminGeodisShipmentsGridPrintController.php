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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisLog.php';

class AdminGeodisShipmentsGridPrintController extends GeodisControllerAdminAbstractMenu
{
    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();

        if (Tools::getIsset('call')) {
            $this->printAction();
        }

        parent::__construct();
        $this->bootstrap = true;
        $this->table = GEODIS_NAME_SQL.'_shipment';
        $this->list_no_link = true;
        $this->identifier = 'id_shipment';
        $this->allow_export = false;
        $this->_orderBy = 'id_shipment';
        $this->_select = '
            a.`id_shipment` as `action`,
            a.`reference` as `shipment_ref`,
            g.`reference` as `group_name`,
            g.`id_group_carrier` as `id_group`,
            p.`libelle` as `prestation_name`
            ';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.GEODIS_NAME_SQL.'_group_carrier` g ON (g.`id_group_carrier` = a.`id_group_carrier`)
            LEFT JOIN `'._DB_PREFIX_.GEODIS_NAME_SQL.'_carrier` c ON (a.`id_carrier` = c.`id_carrier`)
            LEFT JOIN `'._DB_PREFIX_.GEODIS_NAME_SQL.'_prestation` p ON (p.`id_prestation` = c.`id_prestation`)
        ';
        $this->_where = 'and a.recept_number != "" and a.is_label_printed = 0';

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        $availableOrderStates = GeodisServiceConfiguration::getInstance()->get('available_order_states');
        foreach ($statuses as $status) {
            if (empty($availableOrderStates) || in_array($status['id_order_state'], $availableOrderStates)) {
                $this->statusList[$status['id_order_state']] = $status['name'];
            }
        }

        $groupCarrierCollection = GeodisGroupCarrier::getCollection();
        foreach ($groupCarrierCollection as $groupCarrier) {
            $this->groupCarrierList[$groupCarrier->id] = $groupCarrier->reference;
        }

        $this->_use_found_rows = true;

        $this->fields_list = array(
            'id_shipment' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridPrint.index.grid.id'),
                'class' => 'fixed-width-xs js-id',
            ),
            'reference' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridPrint.index.grid.reference'),
                'class' => 'fixed-width-xs',
            ),
            'departure_date' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridPrint.index.grid.departureDate'),
                'havingFilter' => true,
                'type' => 'datetime',
            ),
            'group_name' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.groupCarrier'),
                'havingFilter' => true,
                'type' => 'select',
                'color' => 'color',
                'filter_key' => 'id_group',
                'list' => $this->groupCarrierList,
                'filter_type' => 'int',
                'order_key' => 'status',
            ),
            'prestation_name' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.carrier'),
                'havingFilter' => true,
            ),
            'status_label' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.status'),
                'type' => 'select',
                'color' => 'color',
                'filter_key' => 'status_code',
                'list' => $this->statusList,
                'filter_type' => 'int',
                'order_key' => 'status',
            ),
            'recept_number' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridPrint.index.grid.receptNumber'),
                'havingFilter' => true,
            ),
            'id_order' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridPrint.index.grid.order'),
                'havingFilter' => true,
            ),
        );
        $this->addRowAction('print');
        $this->bulk_actions = array(
            'print' => array(
                'text' =>  GeodisServiceTranslation::get('Admin.shipmentsGrid.index.grid.bulkAction.print'),
                'icon' => 'icon-print',
            ),
        );
    }

    /**
     * Custom action icon "print".
     */
    public function displayPrintLink($id = null)
    {
        $this->context->smarty->assign(
            array(
                'module_dir' => GEODIS_MODULE_DIR,
                'href' => $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint',
                    true,
                    array(),
                    array(
                        'id' => $id,
                        'action' => 'print',
                    )
                ),
                'action' => GeodisServiceTranslation::get('Admin.shipmentsGrid.index.grid.action.print'),
                'icon' => 'print',
                'classes' => 'btn btn-default js-print',
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'geodisofficiel/views/templates/admin/list_action/action.tpl'
        );
    }

    public function printAction()
    {
        $this->processBulkPrint(array(Tools::getValue('id')));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_PS_MODULE_DIR_.'geodisofficiel//views/js/admin/GeodisShipmentsGridPrint.js');
        Media::addJsDef(
            array(
                'geodis' => array(
                    'token' => Tools::getAdminTokenLite(GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint'),
                    'url' => $this->context->link->getAdminLink(GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint'),
                ),
            )
        );
    }

    public function processBulkPrint($idList = null)
    {
        $listRecept = array();
        if ($idList == null) {
            $idShipmentSelected = Tools::getValue(GEODIS_NAME_SQL.'_shipmentBox');
        } else {
            $idShipmentSelected = $idList;
        }

        $shipmentCollection = GeodisShipment::getCollection();
        $shipmentCollection->where("id_shipment", 'IN', $idShipmentSelected);
        foreach ($shipmentCollection as $shipment) {
            if ($shipment->recept_number != null) {
                $listRecept[] = $shipment->recept_number;
            }
        }

        if (empty($listRecept)) {
            return;
        }

        try {
            $response = GeodisServiceWebservice::getInstance()->getPackageLabel($listRecept);
        } catch (Exception $e) {
            Tools::displayError($e->getMessage());
        }

        foreach ($shipmentCollection as $shipment) {
            $shipment->is_label_printed = true;
            $shipment->save();
        }

        $pdfFile = 'delivery-' . time() . '.pdf';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$pdfFile.'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . Tools::strlen($response));
        die($response);
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);

        $this->toolbar_btn['list'] = array(
            'href' => $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'OrdersGrid',
                true,
                array()
            ),
            'desc' =>  GeodisServiceTranslation::get('Admin.OrdersGrid.index.action.viewOrders'),
            'class' => 'icon-list',
        );
        $this->toolbar_btn['transmit'] = array(
            'href' => $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'ShipmentsGridTransmit',
                true,
                array()
            ),
            'desc' =>  GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.link.transmit'),
            'class' => 'icon-send',
        );

        return $this->toolbar_btn;
    }
}
