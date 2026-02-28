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
{function printEstimatedDelivery delivery=''}

    <p class="ed_orderbefore">
        {if $dlf == 0}
            {l s='Deliver it' mod='estimateddelivery'} 
        {elseif $dlf == 1}
            {l s='Receive it' mod='estimateddelivery'} 
        {/if}
        <span class="date_green">
        {if ($dldf_total-$dldf) >= 2}
            {if $delivery->delivery_min == $delivery->delivery_max}
                {if $delivery->tot}
                    {$delivery->delivery_min|escape:'htmlall':'UTF-8'}
                {else}
                    {l s='on' mod='estimateddelivery'} {$delivery->delivery_min|escape:'htmlall':'UTF-8'}
                {/if}
            {else}
                {if $dlf == 2}
                    {l s='Between' mod='estimateddelivery'}
                {else}
                    {l s='between' mod='estimateddelivery'}
                {/if}
                {$delivery->delivery_min|escape:'htmlall':'UTF-8'} </span>
                <span class="date_green">{l s='and' mod='estimateddelivery'}
                {$delivery->delivery_max|escape:'htmlall':'UTF-8'}
            {/if}
        {else}
            {$delivery->delivery_min|escape:'htmlall':'UTF-8'}{if $delivery->delivery_min != $delivery->delivery_max}-{$delivery->delivery_max|escape:'htmlall':'UTF-8'}
            {/if}
            {if $dldf == ($dldf_total-1)}
                {l s='hours' mod='estimateddelivery'}
            {elseif $dldf == $dldf_total}
                {if $delivery->delivery_min == 1}
                    {l s='day' mod='estimateddelivery'}
                {else}
                    {l s='days' mod='estimateddelivery'}
                {/if}
            {/if}
        {/if}
        </span>
        {if isset($delivery->price)}
            {showEdPrice price=$delivery->price}
        {/if}
    </p>
{/function}
<div class="estimateddelivery estimateddelivery-list{if $is_17} is_17{/if}{if $edclass != '' } {$edclass|escape:'htmlall':'UTF-8'}{/if}" data-idprod="{$delivery->id_product|intval}">
    <div class="ed_item" {if $edclass != '' && $edclass == 'custom'}style="background:{$edbackground|escape:'htmlall':'UTF-8'};border:1px solid {$edborder|escape:'htmlall':'UTF-8'}"{/if}>
        {printEstimatedDelivery delivery = $delivery}
    </div>
</div>