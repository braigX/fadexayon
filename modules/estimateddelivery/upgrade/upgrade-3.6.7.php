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

function upgrade_module_3_6_7()
{
    // Transform serialized arrays to Json encoded strings
    $picking_limit = false;
    if (Shop::isFeatureActive()) {
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $picking_limit = @unserialize(Configuration::get('ed_picking_limit', null, null, $shop['id_shop']));
            if ($picking_limit !== false) {
                $picking_limit[$shop['id_shop']] = json_encode(edValidatePickingTimes($picking_limit));
            }
        }
    } else {
        $picking_limit = @unserialize(Configuration::get('ed_picking_limit'));
        if ($picking_limit !== false) {
            $picking_limit = json_encode(edValidatePickingTimes($picking_limit));
        }
    }
    Configuration::deleteByName('ed_picking_limit');
    if ($picking_limit !== false) {
        if (is_array($picking_limit)) {
            foreach ($picking_limit as $id_shop => $picking) {
                Configuration::updateValue('ed_picking_limit', $picking_limit, false, null, $id_shop);
            }
        } else {
            Configuration::updateValue('ed_picking_limit', $picking_limit);
        }
    }

    return true;
}

/* Wrap it on a function_exists to prevent issues with update from versions prior to 3.6.6 */

if (!function_exists('edValidatePickingTimes')) {
    /**
     * Validates the picking limit array
     *
     * @param $picking_limit
     *
     * @return array The fixed picking array, if necessary
     */
    function edValidatePickingTimes($picking_limit)
    {
        $pickings = array_fill(0, 7, '23:59');
        if ($picking_limit !== false) {
            // It's serialized data from older versions transform it to a JSON object
            if (is_array($picking_limit)) {
                // Validate the previous time formats only for the first 7 elements
                for ($i = 0; $i < 7; ++$i) {
                    $pickings[$i] = edValidateDate(trim($picking_limit[$i]));
                }
            }
        }

        return $pickings;
    }
}
if (!function_exists('edValidateDate')) {
    /**
     * Validate the date by checking the h:i format (0:00 - 23:59)
     *
     * @param $date
     *
     * @return date
     */
    function edValidateDate($date)
    {
        $dateObj = DateTime::createFromFormat('H:i', $date);
        if ($dateObj === false) {
            $date = '23:59';
        }

        return $date;
    }
}
