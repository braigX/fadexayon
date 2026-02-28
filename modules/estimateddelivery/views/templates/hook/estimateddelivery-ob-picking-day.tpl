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
{if !isset($ed_tooltip)}
    {assign var='ed_tooltip' value=0}
{/if}
{function showEdPrice price=''}
    <span class="carrier_price">{$ed_price_prefix|escape:'htmlall':'UTF-8'}{$price|escape:'htmlall':'UTF-8'}{$ed_price_suffix|escape:'htmlall':'UTF-8'}</span>
{/function}

{function calculateRest delivery='' rest=''}
    {$rest[0] = $rest[0] + ($delivery->days_to_add * 24)}
    {$rest[0]|intval}:{$rest[1]|intval}
{/function}

{function printEstimatedDelivery delivery='' hideItems = false}
    <p class="ed_orderbefore ed_{$delivery->dc->id_reference|intval}">
        <span class="ed_orderbefore_msg">
        {if $edstyle == 3 && $delivery->days_to_add <= 6}
            {l s='Order it before' mod='estimateddelivery'}
            <span class="ed_days_to date_green">
            {if $delivery->days_to_add <= 1}
                {l s='%s of %s' sprintf=[$time_limit, $picking_weekday] mod='estimateddelivery'}
            {else}
                {l s='%s on %s' sprintf=[$time_limit, $picking_weekday] mod='estimateddelivery'}
            {/if}
            </span>
        {else}
            {if isset($ed_countdown_limit) && $rest[0] >= $ed_countdown_limit}
            <span class="date_green">
                {l s='Buy it now' mod='estimateddelivery'}
            </span>
            {else}
                {l s='Shipping by' mod='estimateddelivery'}
                <span class="ed_picking_date">
                    {$delivery->formatted_date|escape:'htmlall':'UTF-8'}
                </span>
            {/if}
        {/if}
        </span>
    </p>
{/function}

{function printEstimatedMessage message='' date=''}
    <p class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</p>
{/function}

<div id="estimateddelivery" class="estimateddelivery-product picking-date estimateddelivery{if $ed_placement > 0 && $is_17 == 1} hide-default{/if}{if $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}" data-idprod="{$deliveries[0]->id_product|intval}">
{foreach $deliveries item=delivery name=deliveryLoop}
    <div class="ed_item
        {if isset($id_product_attribute) && (count($deliveries) > 1) && $delivery->dp->id_product_attribute != $id_product_attribute}hideMe
        {elseif !(count($deliveries) == 1 || ($delivery->id_product_attribute == $delivery->dp->id_default_attribute))}hideMe
        {/if}"
        {if $edclass != '' && $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}  data-id-product="{$delivery->id_product|intval}" data-id-product-attribute="{$delivery->id_product_attribute|intval}">
        {if $delivery->dp->msg != ''}
            {include file='./ed-special-date.tpl'}
        {else}
            {printEstimatedDelivery delivery = $delivery}
        {/if}
    </div>
{/foreach}
{if $is_17 && ($deliveries|count gt 1)}
<script>
    var force_combi_check = 1;
</script>
{/if}
</div>
