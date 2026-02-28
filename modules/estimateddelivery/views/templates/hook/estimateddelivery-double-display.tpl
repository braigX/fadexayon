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

{function printEstimatedDelivery delivery='' hideItems = false}
    <p class="ed_orderbefore ed_{$delivery->dc->id_reference|intval}">
        <span class="ed_title">{$sorting_title[$delivery->dc->id_carrier]|escape:'htmlall':'UTF-8'}: </span>
        <span {if $ed_tooltip}class="ed_tooltip"{/if} title="{$delivery->name|escape:'htmlall':'UTF-8'}: {$delivery->delay|escape:'htmlall':'UTF-8'}"></span>
        {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
            {if !isset($delivery->tot)} {l s='on' mod='estimateddelivery'} {/if}
            <span class="ed_date_last"><strong>{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
        {else}
            <span class="ed_date_init">
                <span class="ed_between">{l s='between' mod='estimateddelivery'}</span>
                <span class="ed_date_min"><strong>{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
                {l s='and' mod='estimateddelivery'}
            </span>
            <span class="ed_date_last"><strong>{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
        {/if}
        <span class="ed_carrier_name">
            <span class="with">&nbsp; {l s='with' mod='estimateddelivery'} &nbsp;</span>
            <strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong>
        </span>
        {if isset($delivery->price)}
            {showEdPrice price=$delivery->price}
        {/if}
    </p>
{/function}

{function printEstimatedMessage message='' date=''}
    <p class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</p>
{/function}

<div id="estimateddelivery" class="estimateddelivery estimateddelivery-product ed-double{if $ed_placement > 0 && $is_17 == 1  && !$ed_amp} hide-default{/if}{if $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}" data-idprod="{$deliveries[0]->id_product|intval}">
{foreach from=$deliveries item=delivery name=deliveryLoop}
    <div class="ed_item {if $smarty.foreach.deliveryLoop.first}first_element{elseif $smarty.foreach.deliveryLoop.last}last_element{else}{$smarty.foreach.deliveryLoop.iteration|intval}_element{/if}
        {if !$is_17}
            {if isset($id_product_attribute) && (count($deliveries) > 1) && $delivery->dp->id_product_attribute != $id_product_attribute}hideMe
            {elseif !(count($deliveries) == 1 || ($delivery->id_product_attribute == $delivery->dp->id_default_attribute))}hideMe
            {/if}"
        {/if}
        {if $edclass != '' && $edclass == 'custom'}" style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}  data-id-product="{$delivery->id_product|intval}" data-id-product-attribute="{$delivery->id_product_attribute|intval}">
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
