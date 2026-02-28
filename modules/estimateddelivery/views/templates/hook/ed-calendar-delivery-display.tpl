{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   One time purchase Licence (You can modify or resell the product but just one time per licence)
 * @version 3.3.1
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.pro
 *tpl
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.6.0                      *
 * ***************************************************
*}

<div id="ed_calendar_display">
    <div class="row">
        <div class="col-md-12">
            <h3>{l s='Choose your desired delivery date' mod='estimateddelivery'}</h3>
            <p>{l s='Click on the calendar icon to change your desired delivery date' mod='estimateddelivery'}</p>
            <p><strong>{l s='Selected Delivery date:' mod='estimateddelivery'}</strong> <span class="date_green" id="calendar_current_date">{if isset($ed_calendar_date)}{$ed_calendar_date|escape:'htmlall':'UTF-8'}{/if}</span></p>
            <p><input type="text" {if isset($ed_calendar_date)}data-selected-date="{$ed_calendar_date|escape:'htmlall':'UTF-8'}"{/if} id="delivery_date_picker" name="delivery_date" class="p_ed_delivery_date" value=""/><button id="ed_choose_date" class="btn"><i class="fa fa-calendar ed-blue-color" aria-hidden="true"></i> {l s='Choose a date' mod='estimateddelivery'}</button></p>

            <input type="hidden" name="current_carrier" class="current_carrier_id" value={$id_carrier|intval}>
        </div>
    </div>
    {* Calendar available dates *}
<script>
    var ed_locale = '{$ed_locale|escape:'htmlall':'UTF-8'}';
    var ed_datepicker_format = '{$ed_datepicker_format|escape:'htmlall':'UTF-8'}';
{if $ed_cart|count gt 0}
var calendar_dates = {
    {foreach from=$ed_cart item=delivery key=k name="ed_cart"}
    '{$delivery->dc->id_carrier|intval}': {$delivery->calendar_dates nofilter}{if !$smarty.foreach.ed_cart.last},{/if} {* JSON encoded data *}

    {/foreach}
};
{else}
var calendar_dates = [];
{/if}
</script>
</div>
