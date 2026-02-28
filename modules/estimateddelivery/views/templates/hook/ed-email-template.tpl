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
{function printReleaseAvailable item = ''}
    <p>
    {if $hasDeliveries}
        ✔ <strong>{$item.name|escape:'htmlall':'UTF-8'}</strong>: {$item.preorder_msg|escape:'htmlall':'UTF-8'|replace:'{date}':$item.ed_release_date}
    {else}
        {$item.preorder_msg|escape:'htmlall':'UTF-8'|replace:'{date}':$item.ed_release_date}
    {/if}
    </p>
{/function}

{function printEstimatedDeliveryByProduct deliveries=''}
    {foreach from=$deliveries item=delivery key=k name="ed_order"}
        {if !isset($last_d) || $last_d.delivery_cmp_min != $delivery.delivery_cmp_min || $last_d.delivery_cmp_max != $delivery.delivery_cmp_max}
            {if isset($last_d)}</div>{/if}
            <div class="ed-product-block" style="margin-bottom: 1rem; border-bottom: 1px solid #cdcdcd">
            {assign var="last_d" value=$delivery}
            <span style="color: #44B449; font-size: 14px;">
                    <strong class="date_green">
                        {$delivery.delivery_min|ucfirst|escape:'htmlall':'UTF-8'}
                        {if $delivery.delivery_min|escape:'htmlall':'UTF-8' != $delivery.delivery_max|escape:'htmlall':'UTF-8'}
                            - {$delivery.delivery_max|escape:'htmlall':'UTF-8'}
                        {/if}
                    </strong>
                </span>
        {/if}
        <div class="ed-product" style="margin-top: 3px; margin-bottom: 10px;">
            <span class="edp-product-name">{$delivery['name']|escape:'htmlall':'UTF-8'}</span>{if isset($delivery['attributes'])} - <span style="color:#666666">{$delivery['attributes']|escape:'htmlall':'UTF-8'}</span>{/if}
        </div>
    {/foreach}
    </div> {* Close the last opened div inside the foreach *}
{/function}
<div style="{$edbasestyles|escape:'htmlall':'UTF-8'} {$edcolor|escape:'htmlall':'UTF-8'}">
    {if isset($individual_dates) && $individual_dates}
        <font size="2" face="Open-sans, sans-serif" color="#555454">
            <p style="border-bottom:1px solid #d6d4d4;margin:3px 0 7px;font-weight:500;font-size:18px;padding-bottom:10px">
                {$ed_header|escape:'htmlall':'UTF-8'}
            </p>
            {printEstimatedDeliveryByProduct deliveries = $deliveries}
        </font>
    {else}
        <font size="2" face="Open-sans, sans-serif" color="#555454">
            <p style="border-bottom:1px solid #d6d4d4;margin:3px 0 7px;font-weight:500;font-size:18px;padding-bottom:10px">
                {if $old_ts}
                    {l s='Estimated Delivery' mod='estimateddelivery'}
                {else}
                    {$ed_header|escape:'htmlall':'UTF-8'}
                {/if}
            </p>
            <span style="color:#44B449">
                <span style="font-weight:bold">
                {if isset($ed.msg) && $ed.msg != '' && $ed.undefined_delivery && $undefined_validate_range.min > 0}
                    <p><strong class="dark">
                            <em>{$ed.msg nofilter}</em>
                        </strong>
                    </p>
                {else}
                    {if $old_ts}
                        {if $ed.delivery_min == $ed.delivery_max}
                            {l s='On %s' sprintf=[$ed.delivery_min] mod='estimateddelivery'}
                        {else}
                            {l s='Between %s and %s' sprintf=[$ed.delivery_min, $ed.delivery_max] mod='estimateddelivery'}
                        {/if}
                    {else}
                        {$delivery|escape:'htmlall':'UTF-8'}
                    {/if}
                {/if}
                </span>
            </span>
            {if isset($ed_relandavail) && count($ed_relandavail) > 0}
                {foreach from=$ed_relandavail item=item}
                    {printReleaseAvailable item = $item}
                {/foreach}
            {/if}
        </font>
        {if $edmayvary}
        <font size="2" face="Open-sans, sans-serif" color="#999999">
        <br><br><span>
            {if $old_ts}
                <small>*{l s='If your payment method requires a manual confirmation the estimated delivery of your order may change' mod='estimateddelivery'}</small>
            {else}
                *{$require_validation|escape:'htmlall':'UTF-8'}
            {/if}
            </span>
        </font>
        {/if}
    {/if}
</div>
