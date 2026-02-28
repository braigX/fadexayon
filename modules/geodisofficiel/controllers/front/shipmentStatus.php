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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisShipment.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceConfiguration.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceSynchronize.php';

class GeodisOfficielShipmentStatusModuleFrontController extends ModuleFrontController
{
    protected $idOrder;
    protected $shipments;
    protected $packagesContent;

    public function __construct()
    {
        parent::__construct();
        GeodisServiceTranslation::registerSmarty();
        $this->display_column_left = true;
        $this->display_column_right = true;
        $this->trackingNumber = Tools::getValue('tracking_number');
        $this->shipments = $this->getShipments();

        $receptList = array();
        foreach ($this->shipments as $shipment) {
            if ($shipment->recept_number != null) {
                $receptList[] =  $shipment->recept_number;
            }
        }

        GeodisServiceSynchronize::getInstance()->syncShipmentStatus($receptList);
    }

    public function getLayout()
    {
        return GeodisServiceConfiguration::getInstance()->get('front_columns_customisation');
    }

    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:'.GEODIS_MODULE_NAME.'/views/templates/front/shipmentStatus.tpl');
        if ($this->shipments) {
            $this->context->smarty->assign('error', false);
            $this->context->smarty->assign('shipments', $this->shipments);
            $this->context->smarty->assign('packagesInfos', $this->getPackagesAndProductsInfos());
            $this->context->smarty->assign('history', $this->getHistory());
        } else {
            $this->context->smarty->assign('error', true);
        }
    }

    public function getShipments()
    {
        $shipmentCollection = GeodisShipment::getCollection();
        $shipmentCollection->where('id_order', '=', (int)$this->getOrderId());

        return $shipmentCollection;
    }

    protected function getOrderId()
    {
        $idOrder = Db::getInstance()->getValue(
            'SELECT `id_order`
            FROM `'._DB_PREFIX_.'order_carrier`
            WHERE `tracking_number` = "'.pSql($this->trackingNumber).'"
            ORDER BY date_add DESC'
        );

        if (!$idOrder) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->redirect_after = '404';
            $this->redirect();
        }

        return $idOrder;
    }

    public function getPackagesAndProductsInfos()
    {
        $shipmentsPackages = array();
        foreach ($this->shipments as $shipment) {
            $packages = $shipment->getPackages();
            $link = new Link;
            $packagesProducts = array();
            foreach ($packages as $package) {
                $products = array();
                $geodisPackageOrderDetail = $package->getPackageOrderDetailCollection();
                foreach ($geodisPackageOrderDetail as $packageOrderDetail) {
                    if (!$packageOrderDetail->quantity) {
                        continue;
                    }

                    $orderDetail = $packageOrderDetail->getOrderDetail();
                    $product = new Product(
                        $orderDetail->product_id,
                        false,
                        Context::getContext()->language->id
                    );
                    $image = Image::getCover($orderDetail->product_id);

                    $products[] = array(
                        'productReference' => $product->reference,
                        'productName' => $product->name,
                        'productQuantity' => $packageOrderDetail->quantity,
                        'productUrl' => $product->getLink(),
                        'imagePath' => 'http://'
                        .$link->getImageLink(
                            $product->link_rewrite,
                            $image['id_image'],
                            ImageType::getFormattedName('home')
                        ),
                    );
                }

                $packagesProducts[] = array(
                    'package' => $package,
                    'products' => $products,
                );
            }
            $shipmentsPackages[] = $packagesProducts;
        }

        return $shipmentsPackages;
    }

    public function getHistory()
    {
        $shipmentsHistory = array();
        foreach ($this->shipments as $shipment) {
            $shipmentsHistory[] = $shipment->getHistory();
        }

        return $shipmentsHistory;
    }
}
