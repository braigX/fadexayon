{**
 * Smart CSV Lists Front Office Feature * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 3.3.1
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.3.1                      *
 * ***************************************************
**}

<script type="text/javascript">
    var ed_hours = '{l s='hours' mod='estimateddelivery'}';
    var ed_minutes = '{l s='minutes' mod='estimateddelivery'}';
    var ed_and = '{l s='and' mod='estimateddelivery'}';
    var ed_refresh = '{l s='Picking time limit reached please refresh your browser to see your new estimated delivery.' mod='estimateddelivery'}';
    /*var ed_has_combi = {* $ed_has_combi|intval *}; */
    var ed_placement = {$ed_placement|intval};
    var ed_custom_sel = '{$ed_custom_selector|escape:'htmlall':'UTF-8'}';
    var ed_custom_ins = '{$ed_custom_ins|intval}';
    var ed_sm = {$ed_sm|intval};
</script>
