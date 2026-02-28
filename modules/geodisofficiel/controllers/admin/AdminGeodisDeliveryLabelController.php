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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisDeliveryLabel.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceWebservice.php';

class AdminGeodisDeliveryLabelController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $shipment = new GeodisShipment((int) Tools::getValue('idShipment'));
        $response = GeodisServiceWebservice::getInstance()->getDeliveryLabel(array($shipment->recept_number));

        if (!empty($response)) {
            $pdf = $response;
            $pdf = Tools::substr($pdf, 0, strpos($pdf, '%%EOF') + 5);

            $pdfFile = 'delivery-' . Tools::getValue('idShipment') . '.pdf';
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $pdfFile . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . Tools::strlen($pdf, "ASCII"));
            die($pdf);
        } else {
            Tools::redirect(
                $this->context->link->getAdminLink(
                    GEODIS_ADMIN_PREFIX.'Shipment',
                    true,
                    null,
                    array(
                        'id' => Tools::getValue('idShipment'),
                        'errors' => array(
                            (string) GeodisServiceTranslation::get(
                                'Admin.shipment.index.warning.noLabelsAvailable'
                            )
                        ),
                    )
                )
            );
        }
    }
}
