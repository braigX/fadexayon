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
{function printEstimatedDeliveryByProduct deliveries=''}
    {foreach from=$deliveries item=delivery}
        <div class="ed-product-block" data-id_product="{$delivery.id_product|intval}" data-id_product_attribute="{$delivery.id_product_attribute|intval}">
            {if !isset($del_min) || $del_min != $delivery.delivery_cmp_min || $del_max != $delivery.delivery_cmp_max}
                {assign var='del_min' value=$delivery.delivery_cmp_min}
                {assign var='del_max' value=$delivery.delivery_cmp_max}
                <span style="color: #44B449; font-size: 14px;">
                    <strong class="date_green" style=""> Delivery Between:
                        {$delivery.delivery_min|ucfirst|escape:'htmlall':'UTF-8'}
                        {if $delivery.delivery_min != $delivery.delivery_max}
                            - {$delivery.delivery_max|escape:'htmlall':'UTF-8'}
                        {/if}
                    </strong>
                </span>
            {/if}
            <div class="ed-product" style="margin-top: 3px; margin-bottom: 10px;">
                <span class="edp-product-name"><strong>{$delivery.name|escape:'htmlall':'UTF-8'}</strong></span>
                {if isset($delivery.attributes) && $delivery.attributes|count > 0}
                    {foreach from=$delivery.attributes item=attribute}
                        <div class="edp-attributes" style="margin-left:1em">
                            - <span class="attr-group-name"><u>{$attribute['attr_group_name']|escape:'htmlall':'UTF-8'}</u>: </span>
                            <span class="attr-name">{$attribute['attr_name']|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    {/foreach}
                {/if}
            </div>
        </div>
    {/foreach}
{/function}

<div>
    <font size="2" face="Open-sans, sans-serif" color="#555454">
        {printEstimatedDeliveryByProduct deliveries=$deliveries}
    </font>
</div>
