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
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisTranslation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisCarrier.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/GeodisPrestation.php';
require_once _PS_MODULE_DIR_.'geodisofficiel/classes/Service/GeodisServiceLog.php';

class GeodisGroupCarrier extends ObjectModel
{
    public $id_reference_carrier;
    public $reference;
    public $preparation_delay;
    public $active;
    public $date_add;
    public $date_upd;
    protected $carrier;

    public static $definition = array(
        'table' => GEODIS_NAME_SQL.'_group_carrier',
        'primary' => 'id_group_carrier',
        'fields' => array(
            'id_reference_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'reference' => array('type' => self::TYPE_STRING, 'size' => '20'),
            'preparation_delay' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' =>  array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

    public static function getCollection()
    {
        return new PrestaShopCollection(self::class);
    }

    public function updateCarrier($referenceCarrier)
    {
        if (!$referenceCarrier) {
            return;
        }

        $carrier = Carrier::getCarrierByReference($referenceCarrier);
        $carrier->is_module = true;
        $carrier->external_module_name = GEODIS_MODULE_NAME;
        $carrier->shipping_external = true;
        $carrier->is_module = true;
        $carrier->need_range = true;
        $carrier->save();
        $this->id_reference_carrier = $carrier->id_reference;
        $this->carrier = null;
    }

    public function createCarrier()
    {
        $carrier = new Carrier();
        $carrier->name = $this->getDefaultName();
        $carrier->is_module = true;
        $carrier->active = 1;
        $carrier->range_behavior = 1;
        $carrier->need_range = 1;
        $carrier->shipping_external = true;
        $carrier->range_behavior = 0;
        $carrier->external_module_name = GEODIS_MODULE_NAME;
        $carrier->delay = $this->getDefaultDelay();
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_DEFAULT;
        $carrier->url = str_replace(
            '%40',
            '@',
            Context::getContext()->link->getModuleLink(
                GEODIS_MODULE_NAME,
                'shipmentStatus',
                array('tracking_number' => '@')
            )
        );

        if ($carrier->add() == true) {
            @copy($this->getDefaultLogoPath(), _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg');

            // Reload carrier in order to get id_reference
            $carrier = new Carrier($carrier->id);
            $this->id_reference_carrier = $carrier->id_reference;
            $this->save();

            $this->addZones($carrier);
            $this->addGroups($carrier);
            $this->addRanges($carrier);
        }

        $this->carrier = null;

        return false;
    }

    public function getCarrierLogo($tmp = false)
    {
        if ($tmp) {
            $relativePath = (int) $this->getCarrier()->id.'.tmp.jpg';
        } else {
            $relativePath = (int) $this->getCarrier()->id.'.jpg';
        }

        if (!file_exists(_PS_SHIP_IMG_DIR_.'/'.$relativePath)) {
            if ($tmp) {
                return $this->getCarrierLogo(false);
            }
            return false;
        } else {
            return _THEME_SHIP_DIR_.$relativePath;
        }
    }

    public function getCarrier($cache = true)
    {
        if (!$this->id_reference_carrier) {
            return false;
        }

        if (!$this->carrier || !$cache) {
            $this->carrier = Carrier::getCarrierByReference($this->id_reference_carrier);
        }

        return $this->carrier;
    }

    protected function getSuffix()
    {
        return $this->reference;
    }

    public function getDefaultName()
    {
        return (string) GeodisServiceTranslation::get('*.*.carrier.default.name.'.$this->getSuffix())
            ->setDefault(GEODIS_NAME);
    }

    public function getDefaultDelay()
    {
        $delay = array();
        foreach (Language::getLanguages() as $lang) {
            $delay[$lang['id_lang']] = (string) GeodisServiceTranslation::get(
                '*.*.carrier.default.delay.'.$this->getSuffix(),
                $lang['id_lang']
            );
        }
        return $delay;
    }

    protected function getDefaultLogoRelativePath()
    {
        $relativePath = 'carrier_'.$this->getSuffix().'.jpg';
        if (!file_exists(_PS_MODULE_DIR_.'geodisofficiel/views/img/css/'.$relativePath)) {
            $relativePath = 'carrier_image_'.GEODIS_MODULE_NAME.'.jpg';
        }

        return $relativePath;
    }

    public function getDefaultLogoUrl()
    {
        return __PS_BASE_URI__.'modules/'.GEODIS_MODULE_NAME.'/views/img/css/'
            .$this->getDefaultLogoRelativePath();
    }

    public function getDefaultLogoPath()
    {
        return _PS_MODULE_DIR_.'geodisofficiel/views/img/css/'.$this->getDefaultLogoRelativePath();
    }

    public function getCarrierCollection()
    {
        $carrierCollection = GeodisCarrier::getCollection();
        $carrierCollection->where('id_group_carrier', '=', $this->id);

        return $carrierCollection;
    }

    public function getCarrierFilteredCollection()
    {
        $carrierCollection = GeodisCarrier::getCollection();
        $carrierCollection->where('id_group_carrier', '=', $this->id);
        $carrierCollection->where('active', '=', 1);
        $carrierCollection->where('deleted', '=', 0);

        return $carrierCollection;
    }

    protected function addGroups($carrier)
    {
        $groups_ids = array();
        $groups = Group::getGroups(Context::getContext()->language->id);
        foreach ($groups as $group) {
            $groups_ids[] = $group['id_group'];
        }

        $carrier->setGroups($groups_ids);
    }

    protected function addRanges($carrier)
    {
        $rangePrice = new RangePrice();
        $rangePrice->id_carrier = $carrier->id;
        $rangePrice->delimiter1 = '0';
        $rangePrice->delimiter2 = '10000';
        $rangePrice->add();

        $rangeWeight = new RangeWeight();
        $rangeWeight->id_carrier = $carrier->id;
        $rangeWeight->delimiter1 = '0';
        $rangeWeight->delimiter2 = '10000';
        $rangeWeight->add();
    }

    protected function addZones($carrier)
    {
        $zones = Zone::getZones();

        foreach ($zones as $zone) {
            $carrier->addZone($zone['id_zone']);
        }
    }

    public static function loadFromIdCarrier($idCarrier)
    {
        $carrier = new Carrier($idCarrier);

        $idReferenceCarrier = $carrier->id_reference;

        $collection = self::getCollection();
        $collection->where('id_reference_carrier', '=', $idReferenceCarrier);

        return $collection->getFirst();
    }

    public static function getByReference($reference)
    {
        $collection = self::getCollection();
        $collection->where('reference', '=', $reference);

        $object = $collection->getFirst();

        if (!$object) {
            $object = new self();
            $object->reference = $reference;
            $object->active = 0;
        }

        return $object;
    }

    public static function init()
    {
        switch (GEODIS_MODULE_NAME) {
            case 'geodisofficiel':
                $groups = array(
                    'classic',
                    'rdv',
                    'relay',
                );
                break;
            case 'franceexpress':
                $groups = array(
                    'classic',
                    'rdv',
                    'relay',
                );
                break;
        }

        foreach ($groups as $group) {
            $object = self::getByReference($group);
            if (!$object->save()) {
                return false;
            }

            if (!$object->id_reference_carrier) {
                $object->createCarrier();
            }
        }
        unset($groups, $group, $object);

        return true;
    }
}
