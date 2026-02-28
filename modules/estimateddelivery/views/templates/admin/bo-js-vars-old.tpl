{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 *}

{* Add JS Variables for PS versions earlier than 1.6.0.9 since Media::addJsDef does not exist *}
<script>
var cat_count = {$cat_count|intval};
var ed_ajax_url = '{$ed_ajax_url|escape:'htmlall':'UTF-8'}';
var selected_menu = '{$selected_menu|escape:'htmlall':'UTF-8'}';
var input_limit = {$input_limit|intval}
var remoteAddr = '{$remoteAddr|escape:'htmlall':'UTF-8'}';
</script>