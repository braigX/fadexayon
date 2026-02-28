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

{if $old_ps == 1}
<fieldset id="ed_undefined_deliveries">
    <legend><img src="{$uri|escape:'htmlall':'UTF-8'}/img/calendar.gif" alt="{l s='Undefined Delivery Dates' mod='estimateddelivery'}" />2.6 {l s='Undefined OOS Dates' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_undefined_deliveries">
    <div class="panel-heading" data-position="9"><i class="icon-calendar"></i> 2.6 {l s='Undefined OOS Delivery Dates' mod='estimateddelivery'} ({l s='by Supplier' mod='estimateddelivery'})</div>
{/if}
    <input type="hidden" name="undefined_deliveries_submit" value="1">
{*    <div class="col-lg-3"></div>*}

    <div class="col-lg-12">
        <!-- <h2 class="text-primary">{l s='Undefined OOS dates' mod='estimateddelivery'} - {l s='Deliveries with an undefined date' mod='estimateddelivery'}</h2> -->
        <h3 class="modal-title text-info">{l s='Choose which suppliers should display the undefined delivery message' mod='estimateddelivery'}</h3>
    </div>
    <div class="col-lg-3"></div>
    <div class="col-lg-9">
        <!-- <h2 class="text-primary">{l s='OOS Days' mod='estimateddelivery'} - {l s='Additional delivery days for products Out of Stock' mod='estimateddelivery'}</h2> -->
        <h3 class="modal-title text-info">{l s='Undefined delivery days by brand or supplier' mod='estimateddelivery'}</h3>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Select the method:' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <select name="ED_UNDEFINED_DAYS_MODE" class="input fixed-width-xxl fixed-width-xl" id="ED_UNDEFINED_DAYS_MODE">
                <!--<option value="0" {if $ed_undefined_days_mode == 0}selected="selected"{/if}>{l s='Category' mod='estimateddelivery'}</option> -->
                <option value="1" {if $ed_undefined_days_mode == 1}selected="selected"{/if}>{l s='Supplier' mod='estimateddelivery'}</option>
                <option value="2" {if $ed_undefined_days_mode == 2}selected="selected"{/if}>{l s='Manufacturer / Brand' mod='estimateddelivery'}</option>
            </select>
            <div class="desc help-block">{l s='Only one method can be selected to add the undefined delivery days' mod='estimateddelivery'},<br>
                {l s='choose between Supplier or Manufacturer' mod='estimateddelivery'}</div>
        </div>
    </div>
    <div class="form-group supplier_undefined_delivery">
        <!-- <label class="control-label col-lg-3">{l s='Quick Guide:' mod='estimateddelivery'}</label> -->
        <span class="col-lg-12">
           <p>{l s='For products without stock, but with sales allowed' mod='estimateddelivery'}.</p>
            <p>{l s='Select which elements will generate an undefined delivery date for those products without stock and with sales enabled.' mod='estimateddelivery'}</p>
            <br/>
        </span>
    </div>
    <div id="sup_tree_undefined_delivery" class="undefined_detect">
        <div class="form-group">
            <div class="col-lg-12">
                {$supplier_undefined_delivery nofilter} {* it's raw HTML *}
            </div>
        </div>
    </div>
    <div id="manufacturer_undefined_delivery" class="undefined_detect">
        <div class="form-group">
            {$manufacturer_undefined_delivery nofilter} {* it's raw HTML *}
        </div>
    </div>

    <hr>

    {* Days to validate the order *}
    <h3 class="modal-title text-info">{l s='Undefined date validation' mod='estimateddelivery'}</h3>
    <p>{l s='Set up the number you need to be able to set up a deinite date for the orders with an undefined delivery date.' mod='estimateddelivery'}</p>
    <p>{l s='This settings will be used on the email and in the order confirmation to display the user how many days they will have to
wait until they get a deined delivery date for the order' mod='estimateddelivery'}</p>
    <p>{l s='Leave both settings to 0 if you want to disable this feature' mod='estimateddelivery'}</p>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Minimum days to validate the date' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <div class="input-group fixed-width-lg">
            <input name="ed_undefined_validate_min" id="ed_undefined_validate_min" value="{$ed_undefined_validate_min|intval}" class="input only-numbers" type="text">
                <span class="input-group-addon">{l s='days' mod='estimateddelivery'}</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Maximum days to validate the date' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <div class="input-group fixed-width-lg">
                <input name="ed_undefined_validate_max" id="ed_undefined_validate_max" value="{$ed_undefined_validate_max|intval}" class="input  only-numbers" type="text">
                <span class="input-group-addon">{l s='days' mod='estimateddelivery'}</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">
            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Send email when an order with undefined delivery is created?' mod='estimateddelivery'}">{l s='Notify admins on order creation?' mod='estimateddelivery'}</span>
        </label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="ed_undefined_notify" id="ed_undefined_notify_on" value="1" {if $ed_undefined_notify}checked="checked"{/if}>
                    <label for="ed_undefined_notify_on">{l s='Yes' mod='estimateddelivery'}</label>
                    <input type="radio" name="ed_undefined_notify" id="ed_undefined_notify_off" value="0" {if !$ed_undefined_notify}checked="checked"{/if}>
                    <label for="ed_undefined_notify_off">{l s='No' mod='estimateddelivery'}</label>
                    <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='Send email when an order with undefined delivery is created?' mod='estimateddelivery'}.<br>
                {l s='Enable this setting to send an email to the admins when an order with undefined delivery is created' mod='estimateddelivery'}.<br>
            </p>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Emails to notify' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <input name="ed_undefined_notify_email" id="ed_undefined_notify_email" value="{$ed_undefined_notify_email|escape:'htmlall':'UTF-8'}" class="input" type="text">
        </div>
    </div>

    {* END FORM *}
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit-undefined-oos" name="SubmitUndefinedOOs" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}

<script type="text/javascript">
    $(document).ready(function() {
        CustomDaysMode($('#ED_UNDEFINED_DAYS_MODE'));
        // Brand / Manufacturer Selector
        $(document).on('change','#ED_UNDEFINED_DAYS_MODE',function(){
            CustomDaysMode($(this));
        });
        function CustomDaysMode(e) {
            //console.log(e.prop('selectedIndex'));
            $('.undefined_detect').hide();
            if (e.prop('selectedIndex') == -1) {
                // it's category
                //$('.category_custom').show();
                //$('#ed_custom_days_sel').show();
            } else if (e.prop('selectedIndex') == 0) {
                // it's supplier
                // $('.supplier_undefined_delivery').show();
                $('#sup_tree_undefined_delivery').show();
            } else if (e.prop('selectedIndex') == 1) {
                // it's brand / manu
                // $('.manufacturer_custom').show();
                $('#manufacturer_undefined_delivery').show();
            }
        }
    });
</script>
