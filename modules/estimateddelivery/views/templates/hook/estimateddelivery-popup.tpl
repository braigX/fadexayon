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
    <span class="carrier_price">{$price|escape:'htmlall':'UTF-8'}</span>
{/function}

{function printEstimatedMessage message='' date=''}
    <span class="ed_orderbefore">{$message|escape:'htmlall':'UTF-8'|replace:'{date}':$date}</span>
{/function}
<button id="ed_popup" class="btn btn-default btn-secondary{if $ed_placement > 0 && $is_17 == 1  && !$ed_amp} hide-default{/if}">{l s='Click to see all carriers' mod='estimateddelivery'}</button>
<div id="ed_popup_content" class="table-responsive">
    <h3>{l s='Delivery Options' mod='estimateddelivery'}</h3>
    <table id="ed_popup_table" class="table">
        <thead>
            <tr>
                <th colspan="2">{l s='Carrier' mod='estimateddelivery'}</th>
                {if $ed_popup_options.desc}
                <th>{l s='Description' mod='estimateddelivery'}</th>
                {/if}
                <th>{l s='Estimated Delivery' mod='estimateddelivery'}</th>
                {if $ed_popup_options.price && isset($ed_display_price) && $ed_display_price}
                <th>{l s='price' mod='estimateddelivery'}</th>
                {/if}
            </tr>
        </thead>
        <tbody>
        {foreach from=$popup_deliveries item=delivery name=deliveryLoop}
            <tr>
                {if $ed_popup_options.img}
                <td>{if isset($delivery->dc->img)}<img src="{$delivery->dc->img|escape:'htmlall':'UTF-8'}" />{/if}</td>
                {/if}
                <td {if !$ed_popup_options.img}colspan="2"{/if}>
                    {if $ed_popup_options.name == 'alias'}
                        {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                    {elseif $ed_popup_options.name == 'name:alias'}
                        {if isset($delivery->dc->original_name)}
                            {$delivery->dc->original_name|escape:'htmlall':'UTF-8'}{if isset($delivery->dc->original_name)} ({$delivery->dc->name|escape:'htmlall':'UTF-8'}){/if}
                        {else}
                            {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                        {/if}
                    {elseif $ed_popup_options.name == 'alias:name'}
                        {$delivery->dc->name|escape:'htmlall':'UTF-8'} {if isset($delivery->dc->original_name)}({$delivery->dc->original_name|escape:'htmlall':'UTF-8'}){/if}
                    {else}
                        {* By default use the carrier name *}
                        {if isset($delivery->dc->original_name)}
                            {$delivery->dc->original_name|escape:'htmlall':'UTF-8'}
                        {else}
                            {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                        {/if}
                    {/if}
                </td>
                {if $ed_popup_options.desc}
                <td>{$delivery->dc->delay|escape:'htmlall':'UTF-8'}</td>
                {/if}
                <td>
                    <p><span class="date_green">
                        {if $delivery->dp->msg != ''}
                            {if ($delivery->dp->is_available || $delivery->dp->is_release || ($delivery->dp->is_custom && $enable_custom_days))}
                                {if $delivery->dp->add_custom_days|intval > 0}
                                    {printEstimatedMessage message=$delivery->dp->msg date=$delivery->dp->add_custom_days}
                                {else}
                                    {printEstimatedMessage message=$delivery->dp->msg date=$delivery->dp->formatted_date}
                                {/if}
                            {elseif $delivery->dp->is_virtual || $delivery->dp->is_undefined_delivery}
                                {if $delivery->dp->msg != ''}
                                    {printEstimatedMessage message=$delivery->dp->msg msg = ''}
                                {/if}
                            {/if}
                        {else}
                            {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
                                {$delivery->delivery_min|escape:'htmlall':'UTF-8'}
                            {else}
                                {$delivery->delivery_min|escape:'htmlall':'UTF-8'} - {$delivery->delivery_max|escape:'htmlall':'UTF-8'}
                            {/if}
                        {/if}
                    </span></p>
                </td>
                {if $ed_popup_options.price && isset($ed_display_price) && $ed_display_price}<td>{if isset($delivery->price)}{showEdPrice price=$delivery->price}{else}{l s='Not Available' mod='estimateddelivery'}{/if}</td>{/if}
            </tr>
        {/foreach}
        </tbody>
    </table>
    <div id="ed_popup_list" class="d-md-none d-lg-none">
        {foreach from=$popup_deliveries item=delivery name=deliveryLoop}
            <div class="popup_item">
                <div class="row">
                {if $ed_popup_options.img}
                <div class="col-sm-2">
                    {if isset($delivery->dc->img)}<img src="{$delivery->dc->img|escape:'htmlall':'UTF-8'}" />{/if}
                </div>
                {/if}

                <div class="col-sm-{if $ed_popup_options.img}10{else}12{/if}">
                    <h4>{l s='Carrier' mod='estimateddelivery'}: {if $ed_popup_options.name == 'alias'}
                            {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                        {elseif $ed_popup_options.name == 'name:alias'}
                            {if isset($delivery->dc->original_name)}
                                {$delivery->dc->original_name|escape:'htmlall':'UTF-8'}{if isset($delivery->dc->original_name)} ({$delivery->dc->name|escape:'htmlall':'UTF-8'}){/if}
                            {else}
                                {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                            {/if}
                        {elseif $ed_popup_options.name == 'alias:name'}
                            {$delivery->dc->name|escape:'htmlall':'UTF-8'} {if isset($delivery->dc->original_name)}({$delivery->dc->original_name|escape:'htmlall':'UTF-8'}){/if}
                        {else}
                            {* By default use the carrier name *}
                            {if isset($delivery->dc->original_name)}
                                {$delivery->dc->original_name|escape:'htmlall':'UTF-8'}
                            {else}
                                {$delivery->dc->name|escape:'htmlall':'UTF-8'}
                            {/if}
                        {/if}</h4>
                    {if $ed_popup_options.desc}
                        <p><u>{l s='Description' mod='estimateddelivery'}:</u> {$delivery->dc->delay|escape:'htmlall':'UTF-8'}</p>
                    {/if}
                </div>
                </div>
                <div class="row">
                <div class="col-sm-6">
                <p><strong>{l s='Estimated delivery' mod='estimateddelivery'}:</strong><br>
                    <span class="date_green">
                {if ($delivery->dp->is_available || $delivery->dp->is_release || ($delivery->dp->is_custom && $enable_custom_days)) && $delivery->dp->msg != ''}
                    {if $delivery->dp->add_custom_days|intval > 0}
                        {printEstimatedMessage message=$delivery->dp->msg date=$delivery->dp->add_custom_days}
                    {else}
                        {printEstimatedMessage message=$delivery->dp->msg date=$delivery->dp->formatted_date}
                    {/if}
                {elseif $delivery->dp->is_virtual}
                    {if $delivery->dp->msg != ''}
                        {printEstimatedMessage message=$delivery->dp->msg msg = ''}
                    {/if}
                {else}
                    {if $delivery->delivery_min|escape:'htmlall':'UTF-8' == $delivery->delivery_max|escape:'htmlall':'UTF-8'}
                        {$delivery->delivery_min|escape:'htmlall':'UTF-8'}
                    {else}
                        {$delivery->delivery_min|escape:'htmlall':'UTF-8'} - {$delivery->delivery_max|escape:'htmlall':'UTF-8'}
                    {/if}
                {/if}
                </span></p>
                </div>
                <div class="col-sm-6">
                {if $ed_popup_options.price && isset($ed_display_price) && $ed_display_price}
                    <p><strong>{l s='Price' mod='estimateddelivery'}:</strong><br>
                        {if isset($delivery->price)}{showEdPrice price=$delivery->price}{else}{l s='Not Available' mod='estimateddelivery'}{/if}
                    </p>
                {/if}
                </div>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="ed_close_popup"><span>✖</span></div>
</div>

{if isset($ed_popup_background) && $ed_popup_background}
    {block name='popup_background'}
        <div class="ed_popup_background"></div>
    {/block}
{/if}

<script>
    document.addEventListener('DOMContentLoaded', function() {
    //document.addEventListener('DOMContentLoaded', function() {
        if ($('#ed_popup').closest('.estimateddelivery').length == 0) {
            setTimeout(function () {
                $('#ed_popup').appendTo('#estimateddelivery');
            }, 100);
        }
    });
</script>