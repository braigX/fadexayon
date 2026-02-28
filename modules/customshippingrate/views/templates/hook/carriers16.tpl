{*
* 2018 Prestamatic
*
* NOTICE OF LICENSE
*
*  @author    Prestamatic
*  @copyright 2018 Prestamatic
*  @license   Licensed under the terms of the MIT license
*  @link      https://prestamatic.co.uk
*}

<div id="unable_to_calculate_custom_shipping" style="display:none;">
    <h4>{l s='Sorry we are unable to calculate the shipping cost for your order at this time.' mod='customshippingrate'}</h4>
    <p>{l s='Please click the button below and we will get back to you as soon as possible with a shipping price for your order.' mod='customshippingrate'}</p>
    <div class="message row">
        <div class="form-group">
            <label class="control-label col-lg-12" for="order_message">{l s='Add Message' mod='customshippingrate'}</label>
                <div class="col-lg-12">
                    <textarea name="order_message" id="order_message" class="form-control" rows="3" cols="45"></textarea>
                </div>
        </div>
    </div>
    <a href="javascript:void(0);" id="get_custom_shipping_rate" class="btn btn-primary">
        {l s='Get back to me with a Shipping Price' mod='customshippingrate'}
    </a>
    <div id="spinner_loader">
        <i class="icon-spinner icon-spin icon-fw"></i>
    </div>
	<input type="hidden" name="customshippingrate_applied" id="customshippingrate_applied" value="{$customshippingrate_applied|escape:'htmlall':'UTF-8'}">
	<input type="hidden" name="display_with_carriers" id="display_with_carriers" value="{$display_with_carriers|escape:'htmlall':'UTF-8'}">
	<input type="hidden" name="available_carriers_num" id="available_carriers_num" value="{$available_carriers_num|escape:'htmlall':'UTF-8'}">
</div>
