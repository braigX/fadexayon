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
{function printEstimatedDeliveryByProduct deliveries=''}
    {foreach from=$deliveries item=delivery}
        <div class="ed-product-block" data-id_product="{$delivery.id_product|intval}" data-id_product_attribute="{$delivery.id_product_attribute|intval}">
            {if !isset($del_min) || $del_min != $delivery.delivery_cmp_min || $del_max != $delivery.delivery_cmp_max}
                {assign var='del_min' value=$delivery.delivery_cmp_min}
                {assign var='del_max' value=$delivery.delivery_cmp_max}
                <span>
                    <strong class="date_green">
                        {$delivery.delivery_min|ucfirst|escape:'htmlall':'UTF-8'}
                        {if $delivery.delivery_min != $delivery.delivery_max}
                            - {$delivery.delivery_max|escape:'htmlall':'UTF-8'}
                        {/if}
                        {*
                        {if $delivery->dp->is_custom && $delivery->dp->add_custom_days > 0 && $delivery->dp->msg != ''}
                            <br>{$delivery->dp->msg|escape:'htmlall':'UTF-8'|replace:'{date}':$delivery->dp->add_custom_days}
                        {/if}
                        *}
                    </strong>
                </span>
            {/if}
            <div class="ed-product">
                <span class="edp-product-name">{$delivery.name|escape:'htmlall':'UTF-8'}</span>
                {if isset($delivery.attributes) && $delivery.attributes|count > 0}
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

<div id="ed_tabName">
    <li class="nav-item">
        <a class="nav-link" id="estimateddeliveryTab" data-toggle="tab" href="#estimateddeliveryTabContent" role="tab" aria-controls="#estimateddeliveryTabContent" aria-selected="false">
            {if $new_template}<i class="material-icons">local_shipping</i>
            {else}<i class="icon-truck "></i>
            {/if}
            {l s='Estimated Delivery' mod='estimateddelivery'} {if $new_template} (1){else}<span class="badge">1</span>{/if}
        </a>
    </li>
</div>
<div id="ed_tabContent">
    <div class="tab-pane d-print-block fade" id="estimateddeliveryTabContent" role="tabpanel" aria-labelledby="estimateddeliveryTab">
    <!--<div class="tab-pane" id="estimateddelivery"> -->
        <h2>{l s='Estimated Delivery' mod='estimateddelivery'}</h2>
        {if isset($edcarrier['delivery_min']) && isset($edcarrier['delivery_max']) || isset($dates_by_product)}
            {if $dates_by_product}
                {printEstimatedDeliveryByProduct deliveries = $deliveries}
            {else}
                <dl id="ed_delivery_date" class="well list-detail">
                    {if $edcarrier.is_virtual}
                        <dt>{l s='Virtual Delivery' mod='estimateddelivery'}:</dt>
                        <dd>{l s='Only virtual products, no delivery date has been generated' mod='estimateddelivery'}</dd>
                    {elseif $edcarrier.undefined_delivery}
                        <dt>{l s='Undefined Delivery' mod='estimateddelivery'}:</dt>
                        <dd>{l s='The date will remain as undefined until you generate a delivery date' mod='estimateddelivery'}</dd>
                    {else}
                        <dt>{l s='Minimum delivery date' mod='estimateddelivery'}:</dt>
                        <dd id="delivery_min">{$edcarrier['delivery_min']|escape:'htmlall':'UTF-8'}</dd>
                        <dt>{l s='Maximum delivery date' mod='estimateddelivery'}:</dt>
                        <dd id="delivery_max">{$edcarrier['delivery_max']|escape:'htmlall':'UTF-8'}</dd>
                    {/if}
                </dl>
            {/if}
            {if !$edcarrier.is_virtual}
                <div class="ajax-update-ed">
                <hr>
                    <h4>{l s='Estimated Delivery Update' mod='estimateddelivery'}:</h4>
                    <div id="pre_update_ed">
                    <p>{l s='If, for any reason, you need to update the delivery date click on the following button' mod='estimateddelivery'}.</p>
                    <button id="update_ed" class="btn btn-primary">{l s='Estimated Delivery Update' mod='estimateddelivery'}</button>
                    </div>
                    <div id="update_ed_form" style="margin-top:40px; display: none;">
                        <form id="ed_order_update" method="post">
                            <p>1 - {l s='Choose from there if has to calculate the Estimated Delivery' mod='estimateddelivery'}.</p>
                            <p>2 - {l s='Set up the new date' mod='estimateddelivery'}.</p>
                            <p>2 - {l s='Save' mod='estimateddelivery'}.</p>
                            <br style="margin-top:15px" />
                            <input type="hidden" value="{$ed_token|escape:'htmlall':'UTF-8'}" name="ed_token" id="ed_token"/>
                            <input type="hidden" value="{$id_order|intval}" name="id_order" id="id_order"/>
                            <input type="hidden" value="{if isset($edcarrier)}{$edcarrier.id_carrier|intval}{/if}" name="id_carrier"/>
                            <!--<input type="radio" name="ed_update_type" value="picking" disabled>{l s='Picking' mod='estimateddelivery'}<br>
                            <input type="radio" name="ed_update_type" value="shipping" disabled>{l s='Shipping' mod='estimateddelivery'}<br> -->
                            {if !$calendar_order}
                                {if !$edcarrier.undefined_delivery}
                                <label for="ed_up"><input type="radio" id="ed_up" name="ed_update_type" value="confirmation" checked>{l s='New Confirmation date' mod='estimateddelivery'}<br><em>{l s='Picking settings as additional days will be taken into account' mod='estimateddelivery'}</em></label>
                                <br><br>
                                <label for="ed_up_picking"><input id="ed_up_picking" type="radio" name="ed_update_type" value="picking">{l s='From picking' mod='estimateddelivery'}<br><em>{l s='Set up the picking date for the delivery, shipping will be calculated from this date' mod='estimateddelivery'}</em></label>
                                <br><br>
                                {/if}
                                {if !$dates_by_product}
                                    <label for="ed_up_shipping">
                                        <input id="ed_up_shipping" type="radio" name="ed_update_type" value="shipping">{l s='New Shipping Date' mod='estimateddelivery'}<br><em>{l s='Set up the minimum delivery date, delivery maximum date will be calculated from this date' mod='estimateddelivery'}</em>
                                    </label>
                                {/if}
                            {else}
                                <label for="ed_up_calendar">
                                    <input id="ed_up_calendar" type="radio" name="ed_update_type" value="calendar" checked>{l s='New Calendar Date' mod='estimateddelivery'}<br><em>{l s='Set up the new calendar date for this order' mod='estimateddelivery'}</em>
                                </label>
                            {/if}
                                <br style="margin-top:15px" />
                            {if $new_template}
                                <div class="input-group datepicker fixed-width-xxl">
                                    <input autocomplete="off" id="ed_update_date" name="ed_update_date" class="simpleDatepicker" value="{if empty($valid_date)}{$date_add|escape:'htmlall':'UTF-8'}{else}{$valid_date|escape:'htmlall':'UTF-8'}{/if}" data-format="YYYY-MM-DD H:m:s" style="text-align: center" type="text">
                                    <div class="input-group-append"><div class="input-group-text"><i class="material-icons">date_range</i></div></div>
                                </div>
                            {else}
                                <div class="input-group fixed-width-xxl">
                                    <input autocomplete="off" id="ed_update_date" name="ed_update_date" class="simpleDatepicker" value="{if empty($valid_date)}{$date_add|escape:'htmlall':'UTF-8'}{else}{$valid_date|escape:'htmlall':'UTF-8'}{/if}" data-format="YYYY-MM-DD H:m:s" style="text-align: center" type="text">
                                    <div class="input-group-addon"> <i class="icon-calendar-empty"></i></div>
                                </div>
                            {/if}
                            <br style="margin-top:15px" />
                            <p><button id="submit_update_ed" class="btn btn-primary" type="submit">{l s='Estimated Delivery Update' mod='estimateddelivery'}</button></p>
                        </form>
                    </div>
                </div>
            {/if}
        {else} {* This order doesn't have a date stored allow the possibility to create it *}
        <div class="ajax-update-ed">
            <div id="new_ed_form">
                <dl id="ed_delivery_date" class="well list-detail" style="display:none">
                    <dt>{l s='Minimum delivery date' mod='estimateddelivery'}:</dt>
                    <dd id="delivery_min"></dd>
                    <dt>{l s='Maximum delivery date' mod='estimateddelivery'}:</dt>
                    <dd id="delivery_max"></dd>
                </dl>
                <form id="ed_order_new" method="post">
                    <input type="hidden" value="{$ed_token|escape:'htmlall':'UTF-8'}" name="ed_token"/>
                    <input type="hidden" value="{$id_order|intval}" name="id_order"/>
                    {* <input type="hidden" value="AdminOrders" name="controller"/> *}
                    <input type="hidden" value="confirmation" name="ed_update_type"/>
                    <input type="hidden" value="{$force_date|escape:'htmlall':'UTF-8'}" name="force_date"/>
                    <br style="margin-top:15px" />
                    <p><button id="submit_update_ed" class="btn btn-primary" type="submit">{l s='Generate the Estimated Delivery' mod='estimateddelivery'}</button></p>
                </form>
            </div>
        </div>
        {/if}
    </div>
</div>
{capture name="date_not_updated"}{l s='Date could not be updated' mod='estimateddelivery'}{/capture}
{capture name="ed_updated"}{l s='The Estimated Delivery date for this order has been created or updated' mod='estimateddelivery'}{/capture}
{capture name="min_date_msg"}{l s='Minimum delivery date' mod='estimateddelivery'}{/capture}
{capture name="max_date_msg"}{l s='Maximum delivery date' mod='estimateddelivery'}{/capture}
{capture name="undefined_delivery_msg"}{l s='This order has an undefined delivery. To update the delivery date use the option to set the minimum delivery date' mod='estimateddelivery'}{/capture}
{capture name="ed_send_email"}{l s='Send an email to customer?' mod='estimateddelivery'}{/capture}
{capture name="ed_email_sent"}{l s='The customer has been successfully notified' mod='estimateddelivery'}{/capture}
{capture name="need_refresh_message"}{l s='To see the generated dates, refresh this page' mod='estimateddelivery'}{/capture}

<script type="text/javascript">
    var dates_by_product = {if isset($dates_by_product)}{$dates_by_product|intval}{else}0{/if};
    document.addEventListener("DOMContentLoaded", function(event) {
        if ($('#myTab').length > 0) {
            $("#ed_tabName").contents().insertAfter($('#myTab li:first-child'));
            $("#ed_tabContent").contents().insertAfter($('#shipping'));
        } else if ($('#orderShippingTab').length > 0) {
            $("#ed_tabName").contents().insertBefore($('#orderShippingTab').parent());
            $("#ed_tabContent").contents().insertBefore($('#orderShippingTabContent'));
        }
        $('#myTab a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        if ($(".simpleDatepicker").length > 0) {
            if ($('.content-div').length == 0) {
                $(".simpleDatepicker").datetimepicker({
                    prevText: '',
                    nextText: '',
                    dateFormat: 'yy-mm-dd'
                });
            } else {
                // from 1.7.7 onwards
                //$(".simpleDatepicker").val($(".simpleDatepicker").val() + ' 00:00:00')
                //$(".simpleDatepicker").datetimepicker();
            }
        }

        $(document).on('click', '#update_ed', function() {
            $('#pre_update_ed').slideUp();
            $('#update_ed_form').slideDown();
        });
        $('#ed_order_update, #ed_order_new').submit(function(e) {
            e.preventDefault();
            var admin_url = "{$admin_url|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}"; {* Can't escape the URL *}
            $.ajax({
                type: "POST",
                url: admin_url,
                data: 'ajax=1&action=EDUpdate&' + $(this).serialize(),
                success: function(data)
                {
                    data = JSON.parse(data);
                    //console.log(data);
                    if (data.success == 1) {
                        showSuccessMessage("{$smarty.capture.ed_updated nofilter}");
                        //showSuccessMessage("{$smarty.capture.min_date_msg nofilter} " + data.delivery_min);
                        //showSuccessMessage("{$smarty.capture.max_date_msg nofilter} " + data.delivery_max);
                        if (dates_by_product) {
                            var ed_dates = data['individual_data'];
                            for(var i=0; i<ed_dates.length; i++) {
                                var ed = ed_dates[i];
                                var id_product = ed['dp']['id_product'];
                                var id_product_attribute = ed['dp']['id_product_attribute'];
                                if ($('.ed-product-block').length > 0) {
                                    $('.ed-product-block').each(function () {
                                        if ($(this).data('id_product') == id_product && $(this).data('id_product_attribute') == id_product_attribute) {
                                            if (ed['delivery_min'] != ed['delivery_max']) {
                                                $(this).find('strong.date_green').html(ed['delivery_min'] + ' - ' + ed['delivery_max']);
                                            } else {
                                                $(this).find('strong.date_green').html(ed['delivery_min']);
                                            }
                                        }
                                    });

                                } else {
                                    $('#ed_tabContent h2').after('<div class="alert alert-info">{$smarty.capture.need_refresh_message nofilter}</div>');
                                }
                            }
                        } else {
                            if ($('#delivery_min').length > 0) {
                                $('#delivery_min').text(data.delivery_min);
                                $('#delivery_max').text(data.delivery_max);
                            }
                            if (!$('#ed_delivery_date').is(':visible')) {
                                $('#ed_delivery_date').parent().show();
                            }
                        }
                        let sendEmail = '';
                        if ((typeof data.dp !== 'undefined' && typeof data.dp.is_undefined_delivery !== 'undefined' && parseInt(data.dp.is_undefined_delivery)) || (typeof data.undefined_delivery !== 'undefined' && parseInt(data.undefined_delivery))) {
                            sendEmail = "{$smarty.capture.ed_updated nofilter}\n\n{$smarty.capture.undefined_delivery_msg nofilter} \n\n{$smarty.capture.ed_send_email nofilter}";
                        } else if (dates_by_product) {
                            sendEmail = "{$smarty.capture.ed_updated nofilter}\n\n{$smarty.capture.ed_send_email nofilter}";
                        } else {
                            sendEmail = "{$smarty.capture.ed_updated nofilter}\n\n{$smarty.capture.min_date_msg nofilter}: " + data.delivery_min + "\n{$smarty.capture.max_date_msg nofilter}: " + data.delivery_max + "\n\n{$smarty.capture.ed_send_email nofilter}";
                        }
                        if (confirm(sendEmail)) {
                            $.ajax({
                                type: "POST",
                                url: admin_url,
                                data: {
                                    ajax: "1",
                                    action: 'SendEmail',
                                    id_order: $("#id_order").val(),
                                    ed_token: $("#ed_token").val(),
                                    ed_update_mail: 1,
                                },
                                success: function(data) {
                                    data = JSON.parse(data);
                                    if (data.success == 1) {
                                        showSuccessMessage("{$smarty.capture.ed_email_sent nofilter}");
                                    }
                                }
                            });
                        }
                    } else {
                        showErrorMessage("{$smarty.capture.date_not_updated nofilter}");
                    }
                }
            });
        });
    });
</script>