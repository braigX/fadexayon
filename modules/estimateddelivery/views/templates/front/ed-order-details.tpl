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
**}

{function printEstimatedDeliveryByProduct deliveries=''}
    {assign var='del_min' value=''}
    {assign var='del_max' value=''}

    {foreach from=$deliveries item=delivery}
        <div class="ed-product-block">
            {if $del_min != $delivery.delivery_cmp_min && $del_max != $delivery.delivery_cmp_max}
                {assign var='del_min' value=$delivery.delivery_cmp_min}
                {assign var='del_max' value=$delivery.delivery_cmp_max}
                <span>
                    <strong class="date_green">
                        {* TODO include Special message / date types when generating the individual dates *}
                        {if $delivery.undefined_delivery} {* Should add && $delivery->undefined_delivery} *}
                            {l s='Delivery Date to be confirmed' mod='estimateddelivery'}
                        {elseif isset($delivery->dp) && $delivery->dp->is_custom && $delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                            {$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}
                        {else}
                            {$delivery.delivery_min|ucfirst|escape:'htmlall':'UTF-8'}
                            {if $delivery.delivery_min|escape:'htmlall':'UTF-8' != $delivery.delivery_max|escape:'htmlall':'UTF-8'}
                                - {$delivery.delivery_max|escape:'htmlall':'UTF-8'}
                            {/if}
                        {/if}
                    </strong>
                </span>
            {/if}

            <div class="ed-product">
                <span class="edp-product-name">{$delivery.name|escape:'htmlall':'UTF-8'}</span>
                {if isset($delivery.attributes) && ($delivery.attributes|count > 0) && $delivery.attributes != ''}
                    {foreach from=$delivery.attributes item=attribute}
                        <div class="edp-attributes">
                            <span class="attr-group-name">{$attribute['attr_group_name']|escape:'htmlall':'UTF-8'}: </span>
                            <span class="attr-name">{$attribute['attr_name']|escape:'htmlall':'UTF-8'}</span>
                        </div>
                    {/foreach} 
                {/if}
            </div>
        </div>
    {/foreach}
{/function}

{if isset($edcarrier)}
<div class="estimateddelivery estimateddelivery-order{if isset($edclass) && $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if} box">
    <div {if isset($edclass) && $edclass != '' && $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}>
        <h3 class="page-subheading"><i class="icon icon-truck"></i> {l s='Estimated Delivery' mod='estimateddelivery'}</h3>
        {if $dates_by_product}
            {printEstimatedDeliveryByProduct deliveries = $deliveries}
            {if $edcarrier.undefined_delivery}<hr><p><em>{$edcarrier.msg nofilter} {* Can't escape, it's HTML *}</em></p>{/if}
        {else}
            {if isset($edcarrier.msg) && $edcarrier.msg != ''}
            <br>
            <p><em>{$edcarrier.msg nofilter} {* can't escape, it contains HTML *}</em></p>
            {else}
            <p><strong class="dark">{l s='Minimum delivery date:' mod='estimateddelivery'}</strong> <span class="date_green">{$edcarrier['delivery_min']|escape:'htmlall':'UTF-8'}</span><br>
            <strong class="dark">{l s='Maximum delivery date:' mod='estimateddelivery'}</strong> <span class="date_green">{$edcarrier['delivery_max']|escape:'htmlall':'UTF-8'}</span></p>
            {/if}
        {/if}
        {if isset($edcarrier) && !$edcarrier.is_definitive}
            <hr>
            {if $edcarrier.is_virtual}
                <p>{l s='Your product will be ready as soon as we receive and validate the payment.' mod='estimateddelivery'}</p>
            {else}
                {l s='The delivery date will be updated once we receive and confirm the payment.' mod='estimateddelivery'}</p>
                {if $picking != '' && !$edcarrier.undefined_delivery}
                    <p><span class="date_green">{l s='To ensure timely delivery, please complete payment validation by %s on %s.' sprintf=[$picking.picking_limit, $picking.picking_day] mod='estimateddelivery'}.</span></p>
                    <p><span class="date_green"><em>{l s='Payments made after this time may result in a later delivery date' mod='estimateddelivery'}</em></span></p>
                {/if}
            {/if}
            <hr>
            <h5><strong>{l s='Check the Estimated Delivery of your order anytime at:' mod='estimateddelivery'}</strong></h5>
            <p>{l s='Your Account > Orders > Details' mod='estimateddelivery'}</p>
        {/if}
    </div>
</div>
{/if}