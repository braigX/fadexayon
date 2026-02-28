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

class AdminGeodisShipmentsGridTransmitController extends GeodisControllerAdminAbstractMenu
{
    protected $statusList = array();
    protected $carrierList = array();
    protected $groupCarrierList = array();

    public function __construct()
    {
        GeodisServiceTranslation::registerSmarty();

        if (Tools::getIsset('action')) {
            $this->transmitAction();
        }

        parent::__construct();

        $this->bootstrap = true;
        $this->table = GEODIS_NAME_SQL.'_shipment';
        $this->list_no_link = true;
        $this->identifier = 'id_shipment';
        $this->allow_export = false;
        $this->_orderBy = 'id_shipment';
        $this->page_header_toolbar_title = GeodisServiceTranslation::get(
            'Admin.shipmentsGridTransmit.index.headerTitle'
        );
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
        $this->_where = 'and a.is_label_printed = 1 and a.is_complete = 0';

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
            'shipment_ref' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.reference'),
                'class' => 'fixed-width-xs',
                'havingFilter' => true,
            ),
            'departure_date' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.departureDate'),
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
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.receptNumber'),
                'havingFilter' => true,
            ),
            'id_order' => array(
                'title' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.order'),
                'havingFilter' => true,
            ),
        );

        $this->addRowAction('transmit');
        $this->bulk_actions = array(
            'transmit' => array(
                'text' =>  GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.grid.action.transmit'),
                'icon' => 'icon-send',
                'confirm' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.confirm.transmit'),
            )
        );
    }

    public function transmitAction()
    {
        $this->processBulkTransmit(array(Tools::getValue('id')));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }

    public function processBulkTransmit($idList = null)
    {
        $listRecept = array();
        if ($idList == null) {
            $idShipmentSelected = Tools::getValue(GEODIS_NAME_SQL.'_shipmentBox');
        } else {
            $idShipmentSelected = $idList;
        }

        $shipmentCollection = GeodisShipment::getCollection();
        $shipmentCollection->where("id_shipment", 'IN', $idShipmentSelected);

        $listRecept = array();
        foreach ($shipmentCollection as $shipment) {
            if ($shipment->recept_number != null) {
                $listRecept[] = $shipment->recept_number;
            }
        }

        if (empty($listRecept)) {
            return;
        }

        try {
            $response = GeodisServiceWebservice::getInstance()->sendShipment($listRecept);
            if ($response['ok'] == false) {
                GeodisServiceLog::getInstance()->error($response['contenu']['codeErreur'].' '
                .$response['contenu']['texteErreur']);
                throw new Exception($response['contenu']['codeErreur'].' '.$response['contenu']['texteErreur']);
            }

            $listRecept = array();
            foreach ($response['contenu'] as $record) {
                if ($record['erreur'] == false) {
                    GeodisServiceLog::getInstance()->error($record['noRecepisse'].' '.$record['messageErreur']);
                } else {
                    $listRecept[] = $record['noRecepisse'];
                }
            }

            $shipmentCollection->where("recept_number", 'IN', $listRecept);
            foreach ($shipmentCollection as $shipment) {
                $shipment->is_complete = true;
                $shipment->save();
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
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

        $this->toolbar_btn['print'] = array(
            'href' => $this->context->link->getAdminLink(
                GEODIS_ADMIN_PREFIX.'ShipmentsGridPrint',
                true,
                array()
            ),
            'class' => 'icon-print',
            'desc' => GeodisServiceTranslation::get('Admin.OrdersGrid.index.grid.link.print'),
        );

        return $this->toolbar_btn;
    }

    /**
     * Custom action icon "transmit".
     */
    public function displayTransmitLink($token = null, $id = null)
    {
        $this->context->smarty->assign(
            array(
                'module_dir' => GEODIS_MODULE_DIR,
                'href' => $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'ShipmentsGridTransmit',
                    true,
                    array(),
                    array(
                        'id' => $id,
                        'action' => 'transmit',
                    )
                ),
                'action' => GeodisServiceTranslation::get('Admin.shipmentsGridTransmit.index.action.TransmisLabel'),
                '' => 'Admin.shipmentsGridTransmit.index.action.TransmisLabel',
                'icon' => 'send',
                'classes' => 'btn btn-default js-transmit',
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.'geodisofficiel/views/templates/admin/list_action/action.tpl'
        );
    }
}
