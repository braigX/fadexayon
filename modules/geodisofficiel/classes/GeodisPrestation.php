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

class GeodisPrestation extends ObjectModel
{
    public $code_groupe_produits;
    public $code_produit;
    public $code_option;
    public $type;
    public $type_service;
    public $zone;
    public $libelle;
    public $deleted;
    public $withdrawal_point;
    public $withdrawal_agency;
    public $web_appointment;
    public $tel_appointment;
    public $date_add;
    public $date_upd;
    public $code_sa;
    public $code_client;

    const TYPE_PREPA_EXPE = 'PREPA.EXPE';
    const TYPE_REMOVAL = 'ENLEVT.SUR.SITE';
    const ZONE_EUROPE = 'Europe';

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_prestation',
        'primary' => 'id_prestation',
        'fields' => array(
            'code_groupe_produits' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'code_produit' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'code_option' => array('type' => self::TYPE_STRING, 'size' => 15),
            'type' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 10),
            'type_service' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 15),
            'zone' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 30),
            'withdrawal_point' => array('type' => self::TYPE_BOOL, 'required' => false),
            'withdrawal_agency' => array('type' => self::TYPE_BOOL, 'required' => false),
            'web_appointment' => array('type' => self::TYPE_BOOL, 'required' => false),
            'tel_appointment' => array('type' => self::TYPE_BOOL, 'required' => false),
            'libelle' => array('type' => self::TYPE_STRING, 'required' => true, 'size' => 50),
            'deleted' =>  array('type' => self::TYPE_BOOL, 'required' => false),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'code_sa' => array('type' => self::TYPE_STRING, 'size' => 50),
            'code_client' => array('type' => self::TYPE_STRING, 'size' => 50),
        ),
    );

    public static function getCollection()
    {
        $collection = new PrestaShopCollection(self::class);
        $collection->where('deleted', '=', 0);

        return $collection;
    }

    public static function getFromExternal(
        $codeGroupeProduits,
        $codeProduit,
        $codeOption,
        $codeSa,
        $codeClient,
        $typeService,
        $filterDeleted = true
    ) {
        $collection = new PrestaShopCollection(self::class);

        if ($filterDeleted) {
            $collection->where('deleted', '=', 0);
        }

        $collection->where('code_groupe_produits', '=', $codeGroupeProduits);
        $collection->where('code_produit', '=', $codeProduit);
        $collection->where('code_option', '=', $codeOption);
        $collection->where('type_service', '=', $typeService);
        if ($codeSa != null) {
            $collection->where('code_sa', '=', $codeSa);
        } else {
            $collection->where('code_sa', '=', '');
        }
        if ($codeClient != null) {
            $collection->where('code_client', '=', $codeClient);
        } else {
            $collection->where('code_client', '=', '');
        }

        if (($first = $collection->getFirst()) && $collection->getFirst() != null) {
            return $first;
        } else {
            $collection = new PrestaShopCollection(self::class);

            if ($filterDeleted) {
                $collection->where('deleted', '=', 0);
            }

            $collection->where('code_groupe_produits', '=', $codeGroupeProduits);
            $collection->where('code_produit', '=', $codeProduit);
            $collection->where('code_option', '=', $codeOption);
            $collection->where('type_service', '=', $typeService);
            $collection->where('code_sa', '=', '');
            $collection->where('code_client', '=', '');

            if (($first = $collection->getFirst()) && $collection->getFirst() != null) {
                return $first;
            }
        }

        $item = new self();
        $item->code_groupe_produits = $codeGroupeProduits;
        $item->code_produit = $codeProduit;
        $item->code_option = $codeOption;
        $item->type_service = $typeService;
        $item->code_sa = $codeSa;
        $item->code_client = $codeClient;

        return $item;
    }
}
