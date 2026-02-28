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
        <span class="ed_orderbefore_msg">
        {* Picking days can have a limit of a week, if not it will be confusing *}
        {if $edstyle == 3 && $delivery->days_to_add <= 6}
            {l s='Order it before' mod='estimateddelivery'}
            <span class="ed_days_to date_green">
            {if $delivery->days_to_add <= 1}
                {l s='%s of %s' sprintf=[$delivery->time_limit, $delivery->picking_weekday] mod='estimateddelivery'}
            {else}
                {l s='%s on %s' sprintf=[$delivery->time_limit, $delivery->picking_weekday] mod='estimateddelivery'}
            {/if}
            </span>
        {else}
            {if isset($ed_countdown_limit) && $delivery->rest[0] >= $ed_countdown_limit}
                <span class="date_green">
                {l s='Buy it now' mod='estimateddelivery'}
                </span>
            {else}
                {l s='Order it before' mod='estimateddelivery'}
                <span class="ed_countdown" data-time-limit={$delivery->time_limit|escape:'htmlall':'UTF-8'} data-rest="{$delivery->rest[0]|intval}:{$delivery->rest[1]|intval}">
                    {$delivery->rest_with_format|escape:'htmlall':'UTF-8'}
                </span>
            {/if}
        {/if}
        </span>

        {l s='and receive it' mod='estimateddelivery'}
        {if $delivery->delay != ''}
        <span {if $ed_tooltip}class="ed_tooltip"{/if} title="{$delivery->delay|escape:'htmlall':'UTF-8'}">
        {/if}
        <span class="ed_dates" title="{$delivery->name|escape:'htmlall':'UTF-8'}">
        {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
            {if !isset($delivery->tot) || $delivery->tot == false}
                {l s='on' mod='estimateddelivery'}
            {/if}
            <span [text]="attributes.quantity"><strong>{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
        {else}
            {l s='between' mod='estimateddelivery'}
            {if !isset($delivery->tot) || $delivery->tot == false}
                {l s='on' mod='estimateddelivery'}
            {/if}
            <span><strong [text]="attributes.delivery_min">{$delivery->delivery_min|escape:'htmlall':'UTF-8'}</strong></span>
            {l s='and' mod='estimateddelivery'} 
            <span><strong [text]="attributes.delivery_max">{$delivery->delivery_max|escape:'htmlall':'UTF-8'}</strong></span>
        {/if}
        </span>
        
            <span class="ed_carrier_name"> 
                {l s='with' mod='estimateddelivery'} 
                <strong [text]="attributes.carrier_name">{$delivery->name|escape:'htmlall':'UTF-8'}</strong>
            </span>
        </span>

        {if $delivery->delay != ''}</span>{/if}
        {if isset($ed_display_price) && isset($delivery->price)}
            {showEdPrice price=$delivery->price}
        {/if}
    </p>
{/function}

{function printEstimatedMessage message='' date=''}
    <p class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</p>
{/function}

{if $ed_amp}<amp-script layout="fixed-height" height = "200" script="ed-amp-picking-day" data-ampdevmode>{/if}
<div id="estimateddelivery" class="estimateddelivery estimateddelivery-product{if $ed_placement > 0 && $is_17 == 1  && !$ed_amp} hide-default{/if}{if $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}" data-idprod="{$deliveries[0]->id_product|intval}">

{foreach $deliveries item=delivery name=deliveryLoop}
    <div {if $ed_amp}[data-id-product-attribute]:"attributes.id_product_attribute"{/if} class="ed_item
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

{* If AMP is enabled *}
{if $ed_amp}
</amp-script>

<script id="ed-amp-picking-day" type="text/plain" target="amp-script">
    var ed_hours = '{l s='hours' mod='estimateddelivery'}';
    var ed_minutes = '{l s='minutes' mod='estimateddelivery'}';
    var ed_and = '{l s='and' mod='estimateddelivery'}';
    var ed_refresh = '{l s='Picking time limit reached please refresh your browser to see your new estimated delivery.' mod='estimateddelivery'}';

    const span = document.getElementsByClassName('ed_countdown')[0];
    //const span = document.getElementById('live-time');
    
    if (document.getElementsByClassName("estimateddelivery").length != 0) {
        var myDoc = document.getElementsByClassName('ed_countdown')[0].getAttribute('data-rest');
        
        const countdown = async () => {
            var time = '';
            time_limit[1] -= 1;
            if (time_limit[1] < 0) {
                time_limit[1] += 60;
                time_limit[0]--;
                if (time_limit[0] < 10 && time_limit[0] > 0) {
                    time_limit[0] = '0'+time_limit[0];
                }
                if (time_limit[0] <= 0) {
                    time = ed_refresh;
                }
            }

            if (time_limit[1] < 10 && time_limit[1] > 0) {
                time_limit[1] = '0'+time_limit[1];
            }
            if (time == '') {
                time = (time_limit[0] != 0 ? parseInt(time_limit[0])+' '+ed_hours+' '+ed_and+' ' : '')+(parseInt(time_limit[1])+' '+ed_minutes)
                span.textContent = time;
            } else {
                //console.log('here');
                span.textContent = ed_refresh;
            }
        }

        if ( myDoc.length > 0 ){
            var time_limit = myDoc;
            var curr_hour = new Date();
            curr_hour = curr_hour.getHours()+':'+curr_hour.getMinutes();
            time_limit = time_limit.split(':');
            if (time_limit[0] == 0 && time_limit[1] < 59) {
                time_limit[1]++;
            }

            countdown();
        }

        setInterval(countdown, 60000);
    }
</script>
{/if}
