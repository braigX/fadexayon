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



{*{function printEstimatedDelivery deliveries='' count = 0}
        <p>{l s='Order now and receive it...' mod='estimateddelivery'}</p>
        {foreach from=$deliveries item=delivery}
            {assign var=single_date value=$delivery->delivery_min == $delivery->delivery_max}
            <div class="ed_{$delivery->dc->id_reference|intval}">{if $ed_display_checkmark}<span class="checkmark">✔</span> {/if}
                <span {if $ed_tooltip}class="ed_tooltip"{/if} title="{$delivery->name|escape:'htmlall':'UTF-8'}: {$delivery->delay|escape:'htmlall':'UTF-8'}">
                    <span class="ed_date_init">
                    {if !$single_date}
                        <span class="ed_between">{l s='between' mod='estimateddelivery'}</span>
                    {/if}
                        <span><strong>{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
                    </span>
                    {if $single_date}
                        {if !isset($delivery->tot)} {l s='on' mod='estimateddelivery'} {/if}
                    {else}
                        {l s='and' mod='estimateddelivery'}
                        <span class="ed_date_last"><strong>{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
                    {/if}
                    <span class="ed_carrier_name"><span class="with">&nbsp; {l s='with' mod='estimateddelivery'} &nbsp;</span><strong>{$delivery->name|escape:'htmlall':'UTF-8'}</strong>
                    </span>
                </span>
            {if isset($delivery->price)}
                {showEdPrice price=$delivery->price}
            {/if}
            </div>
        {/foreach}
{/function} *}
{*changed by novatis team*}
{function printEstimatedDelivery deliveries='' count = 0}
    {assign var='min_days' value=4}
    {assign var='max_days' value=5}

    {* Get today's date *}
    {assign var='today' value=$smarty.now}

    {* Calculate min and max delivery timestamps *}
    {assign var='delivery_min_ts' value=$today + ($min_days * 86400)}
    {assign var='delivery_max_ts' value=$today + ($max_days * 86400)}

    {* Format the dates *}
    {assign var='delivery_min_date' value=$delivery_min_ts|date_format:"%d %B"}
    {assign var='delivery_max_date' value=$delivery_max_ts|date_format:"%d %B"}

    <p>Délai de livraison estimé : <span style="font-weight:bold;">{$delivery_min_date} - {$delivery_max_date}</span></p>

{/function}

{*changed by novatis team*}


{function printEstimatedMessage message='' date=''}
    <p class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</p>
{/function}
{if $ed_amp}<amp-script layout="fixed-height" height = "200" script="ed-amp-picking-day" data-ampdevmode>{/if}
<div id="estimateddelivery" class="estimateddelivery estimateddelivery-product {if $ed_placement > 0 && $is_17 == 1  && !$ed_amp} hide-default{/if}{if $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}" data-idprod="{$edidprod|intval}" {if $ed_amp}[data-id-product-attribute]:"attributes.id_product_attribute" {/if}>
{foreach from=$deliveries item=delivery name=deliveryLoop}
    <div class="ed_item
        {if isset($id_product_attribute) && ($deliveries|count > 1) && ($delivery[0]->dp->id_product_attribute != $id_product_attribute || (isset($hideItems) && $hideItems == true))} hideMe 
        {elseif !($deliveries|count == 1 || ($delivery[0]->id_product_attribute == $delivery[0]->dp->id_default_attribute))} hideMe
        {/if}"
        {if $edclass != '' && $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}  data-id-product="{$delivery[0]->id_product|intval}" data-id-product-attribute="{$delivery[0]->id_product_attribute|intval}">
        {if isset($delivery[0]->dp->msg) && $delivery[0]->dp->msg != ''}
            {include file='./ed-special-date.tpl'}
        {else}
            {printEstimatedDelivery deliveries = $delivery count=$deliveries|count}
        {/if}
    </div>
{/foreach}
</div>

{if $ed_amp}</amp-script>{/if}


{literal}




<script>
function moveEstimatedDelivery() {
    var estimatedDelivery = document.getElementById('estimateddelivery');
    var alertSection = document.getElementById('submit_idxrcustomproduct_alert');
    if (estimatedDelivery && alertSection) {
        alertSection.insertAdjacentElement('afterend', estimatedDelivery);
        estimatedDelivery.style.display = 'block';
        return true; // done
    }
    return false; // not ready yet
}

// Try every 200ms until the element exists (max 5s)
var attempts = 0;
var interval = setInterval(function() {
    if(moveEstimatedDelivery() || attempts > 25) clearInterval(interval);
    attempts++;
}, 200);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find all estimated delivery <p> elements
    document.querySelectorAll('.ed_item p').forEach(function(p) {
        var span = p.querySelector('span');
        if (!span) return;

        // Get the text inside the span
        var text = span.textContent.trim();
        var parts = text.split(' - ');

        if (parts.length === 2) {
            var firstDate = new Date(parts[0] + ' 2025');
            var secondDate = new Date(parts[1] + ' 2025');

            // If month is same, show only one month
            if (firstDate.getMonth() === secondDate.getMonth()) {
                span.textContent = firstDate.getDate() + ' - ' + secondDate.getDate() + ' ' + parts[0].split(' ')[1];
            }
        }
    });
});
</script>
{/literal}
