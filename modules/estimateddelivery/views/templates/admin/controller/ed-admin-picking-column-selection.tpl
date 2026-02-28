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
                {l s='Columns to display' mod='estimateddelivery'}
            </div>
            <table id="picking_column_selection" class="table" style="max-width: 100%">
            {foreach $available_columns as $column}
                <tr class="carrier-zones">
                    <td>
                        <label>
                            <input type="checkbox" name="selected_columns[{$column.id|escape:'htmlall':'UTF-8'}]"
                                   value="{$column.id|escape:'htmlall':'UTF-8'}"
                                    {if !isset($selected_columns) || isset($selected_columns[$column.id])} checked="checked"{/if}>
                            {$column.name|escape:'htmlall':'UTF-8'}
                        </label> </br>
                    </td>
                </tr>
            {/foreach}
            </table>
            <div class="form-group" style="padding:5px 0px 8px 0px;">
                <label class="control-label col-lg-12">{l s='Select which columns you want to display in this page.' mod='estimateddelivery'}</label>
            </div>

            <div class="panel-footer" id="toolbar-footer">
                <button class="btn btn-default pull-right" id="submit-order-state" name="submitSelectedColumns"
                        type="submit"><i class="process-icon-save"></i>
                    <span>{l s='Save and Refresh' mod='estimateddelivery'}</span></button>
            </div>
        </div>
    </div>
{/if}