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

<script type="text/javascript">
    var confirm_subtree_select_msg = '{l s='Do you want to select all sub-tree items?' mod='estimateddelivery' js=1}';
    var confirm_subtree_unselect_msg = '{l s='Do you want to unselect all sub-tree items?' mod='estimateddelivery' js=1}';
    var ed_link_copied = '{l s='Link successfully copied to clipboard' mod='estimateddelivery' js=1}';
    var review_warning = '{l s='Reviewing is in progress... Please do not leave this page' mod='estimateddelivery' js=1}';
    var ajax_message_ok = '{l s='Reviewing the past orders finished' mod='estimateddelivery' js=1}';
    var ajax_message_ko = '{l s='Reviewing the past orders failed' mod='estimateddelivery' js=1}';
    {if isset($js_vars)}
    {foreach from=$js_vars key=js_var item=value}
    var {$js_var|escape:'htmlall':'UTF-8'} = '{$value|escape:'htmlall':'UTF-8'}';
    {/foreach}
    {/if}
</script>