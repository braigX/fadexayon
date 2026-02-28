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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrierOption.php';

class GeodisCartCarrier extends ObjectModel
{
    public $id_cart;
    public $id_carrier;
    public $id_option_list;
    public $code_withdrawal_point;
    public $code_withdrawal_agency;
    public $info;
    public $date_add;
    public $date_upd;
    protected $carrier;
    protected $cart;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_cart_carrier',
        'primary' => 'id_cart_carrier',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'id_option_list' => array('type' => self::TYPE_STRING, 'size' => '50'),
            'code_withdrawal_point' => array('type' => self::TYPE_STRING, 'size' => '10'),
            'code_withdrawal_agency' => array('type' => self::TYPE_STRING, 'size' => '10'),
            'info' => array('type' => self::TYPE_STRING, 'size' => '2000'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public function getCart()
    {
        if (!$this->id_cart) {
            return false;
        }

        if (!$this->cart) {
            $this->cart = new Cart($this->id_cart);
        }

        return $this->cart;
    }

    public function getCarrier()
    {
        if (!$this->id_carrier) {
            return false;
        }

        if (!$this->carrier) {
            $this->carrier = new GeodisCarrier($this->id_carrier);
        }

        return $this->carrier;
    }

    public static function loadFromIdCart($idCart)
    {
        $collection = self::getCollection();
        $collection->where('id_cart', '=', $idCart);

        return $collection->getFirst();
    }

    public function getDataForJson()
    {
        $data = array(
            'idCarrier' => $this->id_carrier,
            'idOptionList' => $this->getIdOptionList(),
            'codeWithdrawalPoint' => $this->code_withdrawal_point,
            'codeWithdrawalAgency' => $this->code_withdrawal_agency,
            'info' => json_decode($this->info),
        );

        return $data;
    }

    public function getIdOptionList()
    {
        return array_map('intval', explode(',', $this->id_option_list));
    }

    public function getCarrierOptionCollection()
    {
        if (!$this->id_option_list) {
            return array();
        }

        $collection = GeodisCarrierOption::getCollection();
        $collection->where('id_carrier', '=', (int)$this->id_carrier);
        $collection->sqlWhere('id_option IN ('.implode(',', $this->getIdOptionList()).')');

        return $collection;
    }

    public function getInfoAsArray()
    {
        $infoAsArray = array();

        $info = json_decode($this->info, true);
        if ($info) {
            foreach ($info as $line) {
                $infoAsArray[$line['name']] = $line['value'];
            }
        }

        return $infoAsArray;
    }
}
