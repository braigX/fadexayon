{*
* 2007-2023 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 website only.
* If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
* You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
* 
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{extends file="helpers/view/view.tpl"}
    {block name="override_tpl"}
        <div class="quick-view-order" id="quick-view-order">
        {assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
        {assign var="order_documents" value=$order->getDocuments()}
        {assign var="order_shipping" value=$order->getShipping()}
        {assign var="order_return" value=$order->getReturn()}
            <div class="header_poup">
                <div class="group_action">
                    {if $order->invoice_number}
                        <a class="order_file" title="{l s='Download invoice as PDF file' mod='ets_ordermanager'}" href="{$link->getAdminLink('AdminPdf',true)|escape:'html':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id|intval}">
                            <i class="fa fa-file-text"></i>
                        </a>
                    {/if}
                    {if $order->delivery_number}
                        <a class="order_file" title="{l s='Download delivery slip as PDF file' mod='ets_ordermanager'}" href="{$link->getAdminLink('AdminPdf',true)|escape:'html':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id|intval}"><i class="fa fa-truck"></i></a>
                    {/if}
                    <a class="order_print" title="{l s='Print this order' mod='ets_ordermanager'}" href="javascript:window.print();"><i class="fa fa-print"></i></a>
                </div>
                {l s='Order details' mod='ets_ordermanager'}<span class="id_order">(<a title="{l s='View order' mod='ets_ordermanager'}" href="{$link_view_order|escape:'html':'UTF-8'}">#{$order->id|intval} {if $order->reference != ''}| {$order->reference|escape:'html':'UTF-8'} {/if}</a>)</span>
            </div>
    <div class="row form-group main-order">
        <div class="col-sm-6">
            <span><strong><i class="fa fa-calendar" aria-hidden="true"></i> {l s='Date' mod='ets_ordermanager'}</strong>: {$order->date_add|escape:'html':'UTF-8'}</span>
        </div>
        <div class="col-sm-6">
            <span><strong><i class="fa fa-user" aria-hidden="true"></i> {l s='Customer' mod='ets_ordermanager'}</strong>: {if ($customer->isGuest())}{l s='This order has been placed by a guest.' mod='ets_ordermanager'}{else}<a href="{$link_view_customer|escape:'html':'UTF-8'}">{$customer->firstname|escape:'html':'UTF-8'}&nbsp;{$customer->lastname|escape:'html':'UTF-8'}</a>{/if}</span>
        </div>
        <div class="col-sm-6">
            <span><strong><i class="fa fa-credit-card" aria-hidden="true"></i> {l s='Payment method' mod='ets_ordermanager'}</strong>: {$order->payment|escape:'html':'UTF-8'}</span>
        </div>

        <div class="col-sm-6">
            <span><strong><i class="fa fa-envelope-o" aria-hidden="true"></i> {l s='Email' mod='ets_ordermanager'}</strong>: {$customer->email|escape:'html':'UTF-8'}</span>
        </div>
        <div class="col-sm-6">
            <span><strong><i class="fa fa-clock-o" aria-hidden="true"></i> {l s='Order status' mod='ets_ordermanager'}</strong>: {$order_state->name|escape:'html':'UTF-8'}</span>
        </div>
        <div class="col-sm-6">
            <span><strong><i class="fa fa-phone" aria-hidden="true"></i> {l s='Phone' mod='ets_ordermanager'}</strong>: {if $addresses.delivery->phone}{$addresses.delivery->phone|escape:'html':'UTF-8'}{elseif $addresses.delivery->phone_mobile}{$addresses.delivery->phone_mobile|escape:'html':'UTF-8'}{else}--{/if}</span>
        </div>
    </div>
    <div class="row" id="start_products">
        <div class="col-lg-12">
          <form class="container-command-top-spacing" action="{$current_index|escape:'html':'UTF-8'}&amp;vieworder&amp;token={if isset($smarty.get.token)}{$smarty.get.token|escape:'html':'UTF-8'}{/if}&amp;id_order={$order->id|intval}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.' mod='ets_ordermanager'}', '{l s='Quantity to cancel is greater than quantity available.' mod='ets_ordermanager'}');">
            <input type="hidden" name="id_order" value="{$order->id|escape:'html':'UTF-8'}" />
            <div style="display: none">
              <input type="hidden" value="{$order->getWarehouseList()|implode|escape:'html':'UTF-8'}" id="warehouse_list" />
            </div>
            <div class="prdouct-list">
              <div id="refundForm">
              </div>
              {capture "TaxMethod"}
                {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                  {l s='Tax excluded' mod='ets_ordermanager'}
                {else}
                  {l s='Tax included' mod='ets_ordermanager'}
                {/if}
              {/capture}
              {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                <input type="hidden" name="TaxMethod" value="0">
              {else}
                <input type="hidden" name="TaxMethod" value="1">
              {/if}
              <div class="table-responsive">
                <table class="table" id="orderProducts">
                  <thead>
                    <tr>
                      <th></th>
                      <th><span class="title_box ">{l s='Product' mod='ets_ordermanager'}</span></th>
                      <th>
                        <span class="title_box ">{l s='Price per unit' mod='ets_ordermanager'}</span>
                        <small class="text-muted">{$smarty.capture.TaxMethod|escape:'html':'UTF-8'}</small>
                      </th>
                      <th class="text-center"><span class="title_box ">{l s='Qty' mod='ets_ordermanager'}</span></th>
                      {if $display_warehouse}<th><span class="title_box ">{l s='Warehouse' mod='ets_ordermanager'}</span></th>{/if}
                      {if ($order->hasBeenPaid())}<th class="text-center"><span class="title_box ">{l s='Refunded' mod='ets_ordermanager'}</span></th>{/if}
                      {if ($order->hasBeenDelivered() || $order->hasProductReturned())}
                        <th class="text-center"><span class="title_box ">{l s='Returned' mod='ets_ordermanager'}</span></th>
                      {/if}
                      {if $stock_location_is_available}<th class="text-center"><span class="title_box ">{l s='Stock location' mod='ets_ordermanager'}</span></th>{/if}
                      {if $stock_management}<th class="text-center"><span class="title_box ">{l s='Available quantity' mod='ets_ordermanager'}</span></th>{/if}
                      <th>
                        <span class="title_box ">{l s='Total' mod='ets_ordermanager'}</span>
                        <small class="text-muted">{$smarty.capture.TaxMethod|escape:'html':'UTF-8'}</small>
                      </th>
                      <th style="display: none;" class="add_product_fields"></th>
                      <th style="display: none;" class="edit_product_fields"></th>
                      <th style="display: none;" class="standard_refund_fields">
                        <i class="icon-minus-sign"></i>
                        {if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
                          {l s='Return' mod='ets_ordermanager'}
                        {elseif ($order->hasBeenPaid())}
                          {l s='Refund' mod='ets_ordermanager'}
                        {else}
                          {l s='Cancel' mod='ets_ordermanager'}
                        {/if}
                      </th>
                      <th style="display:none" class="partial_refund_fields">
                        <span class="title_box ">{l s='Partial refund' mod='ets_ordermanager'}</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                  {foreach from=$products item=product key=k}
                    {* Include customized datas partial *}
                    {include file='controllers/orders/_customized_data.tpl'}
                    {* Include product line partial *}
                    {include file='controllers/orders/_product_line.tpl'}
                  {/foreach}
                  {if $can_edit}
                    {include file='controllers/orders/_new_product.tpl'}
                  {/if}
                  </tbody>
                </table>
              </div>
              {if $can_edit}
              <div class="row-margin-bottom row-margin-top order_action">
              {if !$order->hasBeenDelivered()}
                <button type="button" id="add_product" class="btn btn-default">
                  <i class="icon-plus-sign"></i>
                  {l s='Add a product' mod='ets_ordermanager'}
                </button>
              {/if}
                <button id="add_voucher" class="btn btn-default" type="button" >
                  <i class="icon-ticket"></i>
                  {l s='Add a new discount' mod='ets_ordermanager'}
                </button>
              </div>
              {/if}
              <div class="clear">&nbsp;</div>
              <div class="row">
                <div class="col-xs-6 col-6">
                  
                </div>
                <div class="col-xs-6 col-6">
                  <div class="panel panel-vouchers" style="{if !sizeof($discounts)}display:none;{/if}">
                    {if (sizeof($discounts) || $can_edit)}
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>
                              <span class="title_box ">
                                {l s='Discount name' mod='ets_ordermanager'}
                              </span>
                            </th>
                            <th>
                              <span class="title_box ">
                                {l s='Value' mod='ets_ordermanager'}
                              </span>
                            </th>
                            {if $can_edit}
                            <th></th>
                            {/if}
                          </tr>
                        </thead>
                        <tbody>
                          {foreach from=$discounts item=discount}
                          <tr>
                            <td>{$discount['name']|escape:'html':'UTF-8'}</td>
                            <td>
                            {if $discount['value'] != 0.00}
                              -
                            {/if}
                            {displayPrice price=$discount['value'] currency=$currency->id}
                            </td>
                            {if $can_edit}
                            <td>
                              <a href="{$current_index|escape:'html':'UTF-8'}&amp;submitDeleteVoucher&amp;id_order_cart_rule={$discount['id_order_cart_rule']|intval}&amp;id_order={$order->id|intval}&amp;token={if isset($smarty.get.token)}{$smarty.get.token|escape:'html':'UTF-8'}{/if}">
                                <i class="icon-minus-sign"></i>
                                {l s='Delete voucher' mod='ets_ordermanager'}
                              </a>
                            </td>
                            {/if}
                          </tr>
                          {/foreach}
                        </tbody>
                      </table>
                    </div>
                    <div class="current-edit" id="voucher_form" style="display:none;">
                      {include file='controllers/orders/_discount_form.tpl'}
                    </div>
                    {/if}
                  </div>
                  <div class="panel panel-total">
                    <div class="table-responsive">
                      <table class="table">
                        {* Assign order price *}
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                          {assign var=order_product_price value=($order->total_products)}
                          {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                          {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                          {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                          {assign var=shipping_refundable value=$shipping_refundable_tax_excl}
                        {else}
                          {assign var=order_product_price value=$order->total_products_wt}
                          {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                          {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                          {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                          {assign var=shipping_refundable value=$shipping_refundable_tax_incl}
                        {/if}
                        <tr id="total_products">
                          <td class="text-right"><strong>{l s='Products:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap">
                            {displayPrice price=$order_product_price currency=$currency->id}
                          </td>
                          <td class="partial_refund_fields current-edit" style="display:none;"></td>
                        </tr>
                        <tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
                          <td class="text-right"><strong>{l s='Discounts:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap">
                            -{displayPrice price=$order_discount_price currency=$currency->id}
                          </td>
                          <td class="partial_refund_fields current-edit" style="display:none;"></td>
                        </tr>
                        <tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
                          <td class="text-right"><strong>{l s='Wrapping:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap">
                            {displayPrice price=$order_wrapping_price currency=$currency->id}
                          </td>
                          <td class="partial_refund_fields current-edit" style="display:none;"></td>
                        </tr>
                        <tr id="total_shipping">
                          <td class="text-right"><strong>{l s='Shipping:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap" >
                            {displayPrice price=$order_shipping_price currency=$currency->id}
                          </td>
                          <td class="partial_refund_fields current-edit" style="display:none;">
                            <div class="input-group">
                              <div class="input-group-addon">
                                {$currency->sign|escape:'html':'UTF-8'}
                              </div>
                              <input type="text" name="partialRefundShippingCost" value="0" />
                            </div>
                            <p class="help-block"><i class="icon-warning-sign"></i> {l
                                s='(Max %s %s)'
                                sprintf=[Tools::displayPrice(Tools::ps_round($shipping_refundable, 2), $currency->id) , $smarty.capture.TaxMethod]
                                mod='ets_ordermanager'
                                }
                            </p>
                          </td>
                        </tr>
                        {if isset($payment_fee)}
                            <tr id="total_payment_fee">
                              <td class="text-right"><strong>{$ETS_PMF_TEXT_PAYMENT_FEE|escape:'html':'UTF-8'}:</strong></td>
                              <td class="amount text-right nowrap" >{$payment_fee|escape:'html':'UTF-8'}</td>
                              <td class="partial_refund_fields current-edit" style="display:none;"></td>
                            </tr>
                        {/if}
                        {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC) && $order->total_paid_tax_incl-$order->total_paid_tax_excl >0}
                        <tr id="total_taxes">
                          <td class="text-right"><strong>{l s='Taxes:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap" >{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$currency->id}</td>
                          <td class="partial_refund_fields current-edit" style="display:none;"></td>
                        </tr>
                        {/if}
                        {assign var=order_total_price value=$order->total_paid_tax_incl}
                        <tr id="total_order">
                          <td class="text-right"><strong>{l s='Total:' mod='ets_ordermanager'}</strong></td>
                          <td class="amount text-right nowrap">
                            <strong>{displayPrice price=$order_total_price currency=$currency->id}</strong>
                          </td>
                          <td class="partial_refund_fields current-edit" style="display:none;"></td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div style="display: none;" class="standard_refund_fields form-horizontal panel">
                <div class="form-group">
                  {if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
                  <p class="checkbox">
                    <label for="reinjectQuantities">
                      <input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" />
                      {l s='Re-stock products' mod='ets_ordermanager'}
                    </label>
                  </p>
                  {/if}
                  {if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
                  <p class="checkbox">
                    <label for="generateCreditSlip">
                      <input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
                      {l s='Generate a credit slip' mod='ets_ordermanager'}
                    </label>
                  </p>
                  <p class="checkbox">
                    <label for="generateDiscount">
                      <input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
                      {l s='Generate a voucher' mod='ets_ordermanager'}
                    </label>
                  </p>
                  <p class="checkbox" id="spanShippingBack" style="display:none;">
                    <label for="shippingBack">
                      <input type="checkbox" id="shippingBack" name="shippingBack" />
                      {l s='Repay shipping costs' mod='ets_ordermanager'}
                    </label>
                  </p>
                  {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
                  <br/><p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund:' mod='ets_ordermanager'}</p>
                  <p class="radio">
                    <label id="lab_refund_total_1" for="refund_total_1">
                      <input type="radio" value="0" name="refund_total_voucher_off" id="refund_total_1" checked="checked" />
                      {l s='Include amount of initial voucher: ' mod='ets_ordermanager'}
                    </label>
                  </p>
                  <p class="radio">
                    <label id="lab_refund_total_2" for="refund_total_2">
                      <input type="radio" value="1" name="refund_total_voucher_off" id="refund_total_2"/>
                      {l s='Exclude amount of initial voucher: ' mod='ets_ordermanager'}
                    </label>
                  </p>
                  <div class="nowrap radio-inline">
                    <label id="lab_refund_total_3" class="pull-left" for="refund_total_3">
                      {l s='Amount of your choice: ' mod='ets_ordermanager'}
                      <input type="radio" value="2" name="refund_total_voucher_off" id="refund_total_3"/>
                    </label>
                    <div class="input-group col-lg-1 pull-left">
                      <div class="input-group-addon">
                        {$currency->sign|escape:'html':'UTF-8'}
                      </div>
                      <input type="text" class="input fixed-width-md" name="refund_total_voucher_choose" value="0"/>
                    </div>
                  </div>
                  {/if}
                {/if}
                </div>
                {if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
                <div class="row">
                  <input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products' mod='ets_ordermanager'}{elseif $order->hasBeenPaid()}{l s='Refund products' mod='ets_ordermanager'}{else}{l s='Cancel products' mod='ets_ordermanager'}{/if}" class="btn btn-default" />
                </div>
                {/if}
              </div>
              <div style="display:none;" class="partial_refund_fields">
                <p class="checkbox">
                  <label for="reinjectQuantitiesRefund">
                    <input type="checkbox" id="reinjectQuantitiesRefund" name="reinjectQuantities" />
                    {l s='Re-stock products' mod='ets_ordermanager'}
                  </label>
                </p>
                <p class="checkbox">
                  <label for="generateDiscountRefund">
                    <input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" onclick="toggleShippingCost()" />
                    {l s='Generate a voucher' mod='ets_ordermanager'}
                  </label>
                </p>
                {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
                <p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund: ' mod='ets_ordermanager'}</p>
                <p class="radio">
                  <label id="lab_refund_1" for="refund_1">
                    <input type="radio" value="0" name="refund_voucher_off" id="refund_1" checked="checked" />
                    {l s='Product(s) price: ' mod='ets_ordermanager'}
                  </label>
                </p>
                <p class="radio">
                  <label id="lab_refund_2" for="refund_2">
                    <input type="radio" value="1" name="refund_voucher_off" id="refund_2"/>
                    {l s='Product(s) price, excluding amount of initial voucher: ' mod='ets_ordermanager'}
                  </label>
                </p>
                <div class="nowrap radio-inline">
                    <label id="lab_refund_3" class="pull-left" for="refund_3">
                      {l s='Amount of your choice: ' mod='ets_ordermanager'}
                      <input type="radio" value="2" name="refund_voucher_off" id="refund_3"/>
                    </label>
                    <div class="input-group col-lg-1 pull-left">
                      <div class="input-group-addon">
                        {$currency->sign|escape:'html':'UTF-8'}
                      </div>
                      <input type="text" class="input fixed-width-md" name="refund_voucher_choose" value="0"/>
                    </div>
                  </div>
                {/if}
                <br/>
                <button type="submit" name="partialRefund" class="btn btn-default">
                  <i class="fa fa-check"></i> {l s='Partial refund' mod='ets_ordermanager'}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
            <hr/>
      <div class="row">
        <div class="form-group">
            <div class="col-xs-12 col-sm-12">
                <span><i class="fa fa-truck"></i> <strong>{l s='Shipping method' mod='ets_ordermanager'}</strong>: {$carrier->name|escape:'html':'UTF-8'}</span>
            </div>
        </div>
      </div>
      <div class="row">
            <div class="customer-address col-xs-12 col-sm-12">
                <div class="row">
                <div id="addressShipping" class="col-sm-6">
                  <div class="title">
                       <i class="fa fa-map-marker"></i> <strong>{l s='Shipping address' mod='ets_ordermanager'}</strong>
                  </div>
                  {if !$order->isVirtual()}
                     <div class="row">
                      <!-- Shipping address -->
                        <div class="col-sm-6 address_order">
                          {displayAddressDetail address=$addresses.delivery newLine='<br />'}
                          {if $addresses.delivery->other}
                            <hr />{$addresses.delivery->other|escape:'html':'UTF-8'}<br />
                          {/if}
                        </div>
                        <div class="col-sm-6 hidden-print">
                          <div id="map-delivery-canvas" style="height: 100px"></div>
                        </div>
                     </div>
                  {/if}
                </div>
                      <!-- Invoice address -->
                <div id="invoiceShipping" class="col-sm-6">
                  <div class="title">
                      <i class="fa fa-file-text"></i> <strong>{l s='Invoice address' mod='ets_ordermanager'}</strong>
                  </div>
                  <div class="row">
                     <div class="col-sm-6 address_order">
                        {displayAddressDetail address=$addresses.invoice newLine='<br />'}
                        {if $addresses.invoice->other}
                            <hr />{$addresses.invoice->other|escape:'html':'UTF-8'}<br />
                        {/if}
                     </div>
                      <div class="col-sm-6 hidden-print">
                            <div id="map-invoice-canvas" style="height: 100px"></div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
      </div>
       <script>
          $('#tabAddresses a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          });
      </script>
      {if (sizeof($messages))}
          <div class="row order-messages">
            <div class="messages-heading">
                <i class="fa fa-envelope"></i> {l s='Messages' mod='ets_ordermanager'} <span class="badge">{sizeof($customer_thread_message)|escape:'html':'UTF-8'}</span>
            </div>
            {if (sizeof($messages))}
              <div class="panel panel-highlighted">
                    <div class="message-item">
                      {foreach from=$messages item=message}
                        <div class="message-avatar">
                          <div class="avatar-md">
                            <i class="fa fa-user icon-2x"></i>
                          </div>
                        </div>
                        <div class="message-body">
                              <span class="message-date">&nbsp;<i class="fa fa-calendar"></i>
                                {dateFormat date=$message['date_add']} -
                              </span>
                              <h4 class="message-item-heading">
                                {if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
                                  {$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
                                {/if}
                                {if ($message['private'] == 1)}
                                  <span class="badge badge-info">{l s='Private' mod='ets_ordermanager'}</span>
                                {/if}
                              </h4>
                              <p class="message-item-text">
                                {$message['message']|escape:'html':'UTF-8'|nl2br}
                              </p>
                        </div>
                      {/foreach}
                    </div>
              </div>
            {/if}
          </div>
      {/if}
</div>
<script type="text/javascript">
function PrintElem(elem)
{
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + document.title  + '</h1>');
    mywindow.document.write(document.getElementById(elem).innerHTML);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
</script>
{/block}