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

{function getZoneValue carrier_zones='' carrier='' zone='' type='min'}
{if isset($carrier_zones[$carrier.id_reference][$zone.id_zone][$type]) && $carrier_zones[$carrier.id_reference][$zone.id_zone][$type] != ''}{$carrier_zones[$carrier.id_reference][$zone.id_zone][$type]|intval}{/if}
{/function}
{function getZonePlaceholder global_carrier='' type='min'}
{$global_carrier[$carrier.id_reference][$type]|intval}
{/function}
{if isset($message)}{$message|escape:'htmlall':'UTF-8'}{/if}
{if $old_ps == 1}
<fieldset id="carrier_delivery_zone">
    <legend><i class="icon-calendar"></i> 4.1 - {l s='Carrier Delivery Zone Intervals (Advanced)' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="carrier_delivery_zone">
    <div class="panel-heading"><i class="icon-calendar"></i> 4.1 - {l s='Carrier Delivery Zone Intervals (Advanced)' mod='estimateddelivery'}</div>
{/if}
    <div class="alert alert-info">{l s='Enable this setting to be able to set up the delivery intervals by zone' mod='estimateddelivery'}.<br><br>
        {l s='[1]The first column contains the value configured in section 4[/1] and will be used in case you don\'t fill the number of days from a setting. Letting you [2]override only the zones that need different delivery days or intervals[/2]' mod='estimateddelivery' tags=['<strong>', '<u>']}</div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Enable Carriers Zone (advanced mode)' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="ed_carrier_zone_adv" id="ed_carrier_zone_adv_on" value="1" {if $ed_carrier_zone_adv == 1}checked="checked"{/if}>
                <label for="ed_carrier_zone_adv_on">Yes</label>
                <input type="radio" name="ed_carrier_zone_adv" id="ed_carrier_zone_adv_off" value="0" {if $ed_carrier_zone_adv == 0}checked="checked"{/if}>
                <label for="ed_carrier_zone_adv_off">No</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div id="carrier_delivery_zone_interval" style="overflow: auto">
        <table id="delivery_zone_interval" class="table" style="max-width: 100%">
            <tr class="zone-field zone-header">
                <td></td>
                <td class="border_bottom center">
                    <div class="">
                        <label for="zone_global">{l s='Global' mod='estimateddelivery'}</label>
                    </div>
                </td>
                {foreach $zones as $zone}
                <td class="border_bottom center">
                    <div class="">
                        <label for="zone_{$zone['id_zone']|intval}">{$zone['name']|escape:'htmlall':'UTF-8'}</label>
                    </div>
                </td>
                {/foreach}
            </tr>
            {foreach $carriers as $carrier}
                <tr id="ed_carrier_zone_{$carrier.id_reference|intval}" class="carrier-zones">
                    <td class="center">
                        <img src="{$base_dir|escape:'htmlall':'UTF-8'}img/s/{$carrier.id_carrier}.jpg" class="carrier_img" />
                        <label for="carrier_{$carrier.id_carrier|intval}" style="center">{$carrier.name|escape:'htmlall':'UTF-8'}</label>
                    </td>
                    <td>
                        <div class="input-group"
                             data-toggle="tooltip" data-placement="top" title="{l s='Global value configured in block 4. This value will be used if you don\'t configure a specific value for the range. To update this go to the section 4' mod='estimateddelivery'}">
                            <span class="input-group-addon">{l s='min' mod='estimateddelivery'}</span>
                            <input id="carrier_global_min_zone_{$carrier.id_reference|intval}" name="carrier_zone[{$carrier.id_reference|intval}][0][min]"
                                   value="{getZonePlaceholder global_carrier=$global_carrier type='min'}"
                                   type="text"
                                   readonly
                                   {if !$ed_carrier_zone_adv}disabled{/if} onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));">
                            <span class="input-group-addon"> {l s='Days' mod='estimateddelivery'} </span>
                        </div>
                        <div class="input-group"
                             data-toggle="tooltip" data-placement="top" title="{l s='Global value configured in block 4. This value will be used if you don\'t configure a specific value for the range. To update this go to the section 4' mod='estimateddelivery'}">
                            <span class="input-group-addon">{l s='max' mod='estimateddelivery'}</span>
                            <input id="carrier_global_max_zone_{$carrier.id_reference|intval}" name="carrier_zone[{$carrier.id_reference|intval}][0][max]"
                                   value="{getZonePlaceholder global_carrier=$global_carrier type='max'}"
                                   type="text"
                                   readonly
                                   {if !$ed_carrier_zone_adv}disabled{/if} onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));">
                            <span class="input-group-addon"> {l s='Days' mod='estimateddelivery'} </span>
                        </div>
                    </td>
                    {foreach $zones as $zone}
                    <td>
                        <div class="input-group"
                             {if !isset($carrier_zones[$carrier.id_reference][$zone.id_zone])}
                             data-toggle="tooltip" data-placement="top" title="{l s='Enable this zone on the carrier configuration in order to be able to set up a delivery range' mod='estimateddelivery'}
                             {/if}">
                            <span class="input-group-addon">{l s='min' mod='estimateddelivery'}</span>
                            <input class="delivery_min" name="carrier_zone[{$carrier.id_reference|intval}][{$zone.id_zone|intval}][min]"
                                   {if isset($carrier_zones[$carrier.id_reference][$zone.id_zone])}
                                       value="{getZoneValue carrier_zones=$carrier_zones carrier=$carrier zone=$zone type='min'}"
                                       placeholder="{getZonePlaceholder global_carrier=$global_carrier type='min'}"
                                   {else}
                                       readonly="readonly"
                                       data-toggle="tooltip" data-placement="top" title="{l s='Enable this zone on the carrier configuration in order to be able to set up a delivery range' mod='estimateddelivery'}"
                                   {/if}
                                   type="text"
                                   {if !$ed_carrier_zone_adv}disabled{/if} onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));">
                            <span class="input-group-addon"> {l s='Days' mod='estimateddelivery'} </span>
                        </div>
                        <div class="input-group"
                            {if !isset($carrier_zones[$carrier.id_reference][$zone.id_zone])}
                             data-toggle="tooltip" data-placement="top" title="{l s='Enable this zone on the carrier configuration in order to be able to set up a delivery range' mod='estimateddelivery'}
                             {/if}">
                            <span class="input-group-addon">{l s='max' mod='estimateddelivery'}</span>
                            <input class="delivery_max" name="carrier_zone[{$carrier.id_reference|intval}][{$zone.id_zone|intval}][max]"
                                   {if isset($carrier_zones[$carrier.id_reference][$zone.id_zone])}
                                       value="{getZoneValue carrier_zones=$carrier_zones carrier=$carrier zone=$zone type='max'}"
                                       placeholder="{getZonePlaceholder global_carrier=$global_carrier type='max'}"
                                   {else}
                                       readonly="readonly"
                                   {/if}
                                   type="text"
                                   {if !$ed_carrier_zone_adv}disabled{/if} onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));">
                            <span class="input-group-addon"> {l s='Days' mod='estimateddelivery'} </span>
                        </div>
                    </td>
                    {/foreach}
                </tr>
            {/foreach}
        </table>
    </div>
    <hr>
    <div style="clear:both"></div>
    <h3 class="text-info modal-title">{l s='Global values' mod='estimateddelivery'}</h3>
    <p>{l s='Global value configured in block 4. This value will be used if you don\'t configure a specific value for the range. To update this go to the section 4' mod='estimateddelivery'}</p>
    <h3 class="text-info modal-title">{l s='Enable the disabled fields' mod='estimateddelivery'}</h3>
    <p>{l s='Edit the carrier and update it\'s zones to enable the fields you want to configure' mod='estimateddelivery'}</p>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit_carrier_delivery_zone" name="submitCarrierDeliveryZone" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
</form>
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}

<script>
    var ref2id = {$carrier_ref_to_id nofilter}; {* JSON object can't be escaped *}
    document.addEventListener('DOMContentLoaded', function() {
        setGlobalZones();
        $(document).on('change', '#carrier_delivery select', function() {
            setGlobalZones();
        });

        function setGlobalZones()
        {
            $('#carrier_delivery select').each(function() {
                let carrier = $(this).attr('id').split('_');
                let id_ref = ref2id[carrier[2]];
                let newvalue = $(this).val();
                if ($('#carrier_global_'+carrier[1]+'_zone_'+id_ref).length > 0) {
                    $('#carrier_global_' + carrier[1] + '_zone_' + id_ref).val(newvalue);
                    $('#ed_carrier_zone_'+id_ref +' input.delivery_'+carrier[1]).each(function() {
                        if ($(this)[0].hasAttribute('placeholder')) {
                            $(this).attr('placeholder', newvalue);
                        }
                    })
                }
            })
        }
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    });

</script>