<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_6_6()
{
    // Update the picking limits of the carriers
    $sql = 'SELECT id_reference, id_shop, picking_limit FROM ' . _DB_PREFIX_ . 'ed_carriers';
    $results = Db::getInstance()->executeS(pSQL($sql));
    if ($results !== false && (count($results) > 0)) {
        foreach ($results as $result) {
            $picking_limit = @unserialize($result['picking_limit']);
            if ($picking_limit !== false) {
                $picking_limit = json_encode(edValidatePickingTimes($picking_limit));
                if (Db::getInstance()->update('ed_carriers', ['picking_limit' => $picking_limit], 'id_reference = ' . (int) $result['id_reference'] . ' AND id_shop = ' . (int) $result['id_shop']) === false) {
                    return false;
                }
            }
        }
    }

    return true;
}

/*
 * Validates the picking limit array
 * @param $picking_limit
 * @return array The fixed picking array, if necessary
 */
if (!function_exists('edValidatePickingTimes')) {
    function edValidatePickingTimes($picking_limit)
    {
        $pickings = array_fill(0, 7, '23:59');
        if ($picking_limit !== false) {
            // It's serialized data from older versions transform it to a JSON object
            if (is_array($picking_limit)) {
                // Validate the previous time formats only for the first 7 elements
                for ($i = 0; $i < 7; ++$i) {
                    if (isset($picking_limit[$i])) {
                        $pickings[$i] = edValidateDate(trim($picking_limit[$i]));
                    }
                }
            }
        }

        return $pickings;
    }
}
/*
 * Validate the date by checking the h:i format (0:00 - 23:59)
 * @param $date
 * @return date
 */
if (!function_exists('edValidateDate')) {
    function edValidateDate($date)
    {
        $dateObj = DateTime::createFromFormat('H:i', $date);
        if ($dateObj === false) {
            $date = '23:59';
        }

        return $date;
    }
}
