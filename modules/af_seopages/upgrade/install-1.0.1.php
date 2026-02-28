<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
function upgrade_module_1_0_1($module_obj)
{
    if (!defined('_PS_VERSION_')) {
        exit;
    }
    $module_obj->fillMissingSettings(['base_route' => 'route_base']);
    Configuration::deleteByName('AFSP_BASE_ROUTE');
    $module_obj->af()->log('add', 'SEO Pages module was auto-upgraded to v1.0.1');

    return true;
}
