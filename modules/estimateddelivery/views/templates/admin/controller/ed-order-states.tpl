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

{if count($order_states) > 0}
    <div class="col-lg-6 col-xs-12">
        <div class="panel">
            <div class="panel-heading">
                {l s='Order states for shipped orders' mod='estimateddelivery'}
            </div>
            <table id="picking_order_states" class="table" style="max-width: 100%">
            {foreach $order_states as $order_state}
                <tr class="carrier-zones">
                    <td>
                        <label>
                            <input type="checkbox" name="order_state[{$order_state.id_order_state|intval}]"
                                   value="{$order_state.id_order_state|intval}"
                                   {if isset($excluded_states[$order_state.id_order_state])} checked="checked"{/if}
                                   {* {foreach $excluded_states as $excluded_state}{if $excluded_state == $order_state.id_order_state}checked{/if}{/foreach} *}>
                            {$order_state.name|escape:'htmlall':'UTF-8'}
                        </label> </br>
                    </td>
                </tr>
            {/foreach}
            </table>

            <div class="form-group" style="padding:5px 0px 8px 0px;">
                <label class="control-label col-lg-12">{l s='Select the order states which represent an order has been already shipped to filter the orders from the list.' mod='estimateddelivery'}</label>
            </div>

            <div class="panel-footer" id="toolbar-footer">
                <button class="btn btn-default pull-right" id="submit-order-state" name="submitOrderState"
                        type="submit"><i class="process-icon-save"></i>
                    <span>{l s='Save and Refresh' mod='estimateddelivery'}</span></button>
            </div>
        </div>
    </div>
{/if}