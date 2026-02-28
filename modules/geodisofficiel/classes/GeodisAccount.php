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

require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Db/GeodisDbSchema.php';

class GeodisAccount extends ObjectModel
{
    public $id_agency;
    public $id_customer_account;
    public $code_sa;
    public $code_client;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_account',
        'primary' => 'id_account',
        'fields' => array(
            'id_agency' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'id_customer_account' => array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isUnsignedInt'),
            'code_sa' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'code_client' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public static function getFromExternal($codeSa, $codeClient)
    {
        $collection = self::getCollection();
        $collection->where('code_sa', '=', $codeSa);
        $collection->where('code_client', '=', $codeClient);

        if (($first = $collection->getFirst())) {
            return $first;
        }

        $item = new self();
        $item->code_sa = $codeSa;
        $item->code_client = $codeClient;

        return $item;
    }

    public function getCustomerAccount()
    {
        return new GeodisSite($this->id_customer_account);
    }

    public function getName()
    {
        return $this->getCustomerAccount()->name;
    }
}
