{**
 * Smart CSV Lists Front Office Feature * Estimated Delivery - Front Office Feature
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
**}

<script type="text/javascript">
    var ed_refresh_delay = {if isset($ed_refresh_delay)}{$ed_refresh_delay|intval}{else}0{/if};
    var ed_hour = '{l s='hours' mod='estimateddelivery'}';
    var ed_minute = '{l s='minutes' mod='estimateddelivery'}';
    var ed_hours = '{l s='hours' mod='estimateddelivery'}';
    var ed_minutes = '{l s='minutes' mod='estimateddelivery'}';
    var ed_and = '{l s='and' mod='estimateddelivery'}';
    var ed_refresh = '{l s='Picking time limit reached please refresh your browser to see your new estimated delivery.' mod='estimateddelivery'}';
    /*var ed_has_combi = {* $ed_has_combi|intval *}; */
    var ed_placement = {$ed_placement|intval};
    var ed_custom_sel = unescapeHTML('{$ed_custom_selector|escape:'htmlall':'UTF-8'}');
    var ed_custom_ins = '{$ed_custom_ins|intval}';
    var ed_sm = {$ed_sm|intval};
    var ed_in_modal = {$ed_cart_modal|intval};
    var front_ajax_url = '{$front_ajax_url nofilter}'; {* Prevent URL parameters from being escaped *}
    var front_ajax_cart_url = '{$front_ajax_cart_url|escape:'html':'UTF-8'}';
    var ps_version = '{$ps_version|escape:'html':'UTF-8'}';
    var ed_display_option = {$ed_display_option|intval};


    function unescapeHTML(html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        return doc.documentElement.textContent;
    }
</script>
