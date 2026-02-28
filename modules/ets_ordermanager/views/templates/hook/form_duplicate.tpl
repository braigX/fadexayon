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
<script type="text/javascript">
var id_cart= {$fields.id_cart|intval};
var id_customer_order = {$fields.id_customer_order|intval};
var id_customer;
</script>
<form id="module_duplicate" class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    {if $edit_customer}
        <input name="btnSubmitEditCustomerOrder" value="1" type="hidden" />
    {else}
        <input name="btnSubmitDuplicateOrder" value="1" type="hidden" />
    {/if}
    <div class="panel" id="fieldset_0">											
        <div class="panel-heading">
            {if !$edit_customer}
            <i class="icon-copy"></i> {l s='Duplicate order' mod='ets_ordermanager'}
            {else}
                <i class="icon-pencil"></i> {l s='Change customer' mod='ets_ordermanager'}
            {/if}
        </div>
            <div class="form-wrapper">
				<div class="form-group">
				    <div class="col-lg-9 col-lg-offset-3">	</div>
					<input type="hidden" value="{$fields.id_order|intval}" name="id_order" />	
                    <input type="hidden" value="{$fields.id_cart|intval}" name="id_cart" />	
				</div>
                {if !$edit_customer}
    				<div class="form-group">
        				<label class="control-label col-lg-3">{l s='Order reference' mod='ets_ordermanager'}</label>
        				<div class="col-lg-9">
        				    <input name="reference" id="reference" value="{$fields.reference|escape:'html':'UTF-8'}" class="" type="text" />
                        </div>
    				</div>
    				<div class="form-group">
        				<label class="control-label col-lg-3">{l s='Payment method' mod='ets_ordermanager'}</label>
        				<div class="col-lg-9">
        				    <input name="payment" id="payment" value="{$fields.payment|escape:'html':'UTF-8'}" class="" type="text" />
                        </div>
                    </div>
                    <div class="form-group">
    				    <label class="control-label col-lg-3">{l s='Shipping method' mod='ets_ordermanager'}</label>
    				    <div class="col-lg-9">
    				        <select name="id_carrier" class=" fixed-width-xl" id="id_carrier">
    				            {if $carriers}
                                    {foreach from=$carriers item='carrier'}
                                        <option value="{$carrier.id_carrier|intval}"{if $carrier.id_carrier==$fields.id_carrier} selected="selected"{/if}>{$carrier.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
    				</div>
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Customer' mod='ets_ordermanager'}:</label>
                        <div class="col-lg-9">{$fields.customer|escape:'html':'UTF-8'} <button type="button" class="change-customer_order"><i class="icon-pencil"></i>{l s='Change' mod='ets_ordermanager'}</button> </div>
                    </div>
                {/if}
                <div id="customer_part" class="panel form-horizontal" {if !$edit_customer}style="display:none;"{/if}>
                    <div id="search-customer-form-group" class="form-group">
            			<label class="control-label col-lg-3">
            				<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Search for an existing customer by typing the first letters of his/her name' mod='ets_ordermanager'}.">
            					{l s='Search for a customer' mod='ets_ordermanager'}
            				</span>
            			</label>
            			<div class="col-lg-9">
            				<div class="row">
            					<div class="col-lg-6">
            						<div class="input-group">
            							<input id="search_order_customer" value="" type="text" />
            							<span class="input-group-addon">
            								<i class="icon-search"></i>
            							</span>
            						</div>
            					</div>
            				</div>
            			</div>
            		</div>
                    <div class="row">
                        <div id="order_customers"></div>
                    </div>
                </div>
                <div class="panel" id="address_part" style="display:none;">
            		<div class="panel-heading">
            			<i class="icon-envelope"></i>
            			{l s='Addresses' mod='ets_ordermanager'}
            		</div>
            		<div id="addresses_err" class="alert alert-warning" style="display:none;"></div>
                		<div class="row">
                			<div id="address_delivery" class="col-lg-6">
                				<h4>
                					<i class="icon-truck"></i>
                					{l s='Delivery' mod='ets_ordermanager'}
                				</h4>
                				<div class="row-margin-bottom">
                					<select id="id_address_delivery" name="id_address_delivery"></select>
                				</div>
                				<div class="well">
                					<a href="" id="edit_delivery_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> {l s='Edit' mod='ets_ordermanager'}</a>
                					<div id="address_delivery_detail"></div>
                				</div>
                			</div>
                			<div id="address_invoice" class="col-lg-6">
                				<h4>
                					<i class="icon-file-text"></i>
                					{l s='Invoice' mod='ets_ordermanager'}
                				</h4>
                				<div class="row-margin-bottom">
                					<select id="id_address_invoice" name="id_address_invoice"></select>
                				</div>
                				<div class="well">
                					<a href="" id="edit_invoice_address" class="btn btn-default pull-right fancybox"><i class="icon-pencil"></i> {l s='Edit' mod='ets_ordermanager'}</a>
                					<div id="address_invoice_detail"></div>
                				</div>
                			</div>
                		</div>
                        <div class="row">
                			<div class="col-lg-12">
                				<a class="fancybox btn btn-default" id="new_address" href="{$link_new_adress|escape:'html':'UTF-8'}&liteDisplaying=1&submitFormAjax=1&id_customer={$fields.id_customer_order|intval}#">
                					<i class="icon-plus-sign-alt"></i>
                					{l s='Add a new address' mod='ets_ordermanager'}
                				</a>
                			</div>
                		</div>
            	</div>
            </div><!-- /.form-wrapper -->
            <div class="panel-footer">
                <button class="btn btn-default pull-left close_duplicate" type="button">
                    <i class="process-icon-cancel"></i>
                    {l s='Cancel' mod='ets_ordermanager'}
                </button>
    			<button type="submit" value="1" id="module_form_submit_btn" name="{if $edit_customer}btnSubmitEditCustomerOrder{else}btnSubmitDuplicateOrder{/if}" class="btn btn-default pull-right">
    				<i class="process-icon-save"></i> {l s='Save' mod='ets_ordermanager'}
    			</button>
            </div>
    </div>
</form>
<script type="text/javascript">
    var xhr = false;
    var link_customer_edit = "{$ets_ordermanager->getLinkCustomerAdmin(1) nofilter}";
    var link_address_edit = "{$ets_ordermanager->getLinkAddressAdmin(1) nofilter}&realedit=1&liteDisplaying=1&submitFormAjax=1";
    $(document).on('keyup','#search_order_customer',function(e){
        if($(this).val())
        {
            searchCustomers();
        }
        if(e.which == 13) {
            return false;
        }
    });
    $(document).on('change','#id_address_delivery,#id_address_invoice',function(){
        updateAddresses($(this));
    });
    $(document).ready(function(){
       setupCustomer(id_customer_order);
    });
   function searchCustomers()
   {
        var customer_search = $('#search_order_customer').val();
        if(xhr)
            xhr.abort();
        $('#search_order_customer').next().addClass('loading');
        $('#order_customers').hide();
    	xhr = $.ajax({
    		type:"POST",
    		url : "{$link->getAdminLink('AdminCustomers') nofilter}",
    		async: true,
    		dataType: "json",
    		data : {
    			ajax: "1",
    			tab: "AdminCustomers",
    			action: "searchCustomers",
    			customer_search: customer_search
    		},
    		success : function(res) {
                if (res.found) {
                  var html = '';
                  $('#order_customers').show();
                  $('#search_order_customer').next().removeClass('loading');

                  $.each(res.customers, function () {
                    html += '<div class="customerCard col-lg-4">';
                    html += '<div class="panel">';
                    html += '<div class="panel-heading">' + this.firstname + ' ' + this.lastname;
                    html += '<span class="pull-right">#' + this.id_customer + '</span></div>';
                    html += '<span>' + this.email + '</span><br/>';
                    html += '<span class="text-muted">' + ((this.birthday != '0000-00-00') ? this.birthday : '') + '</span><br/>';
                    html += '<div class="panel-footer">';
                    html += '<a target="_blank" href="'+link_customer_edit.replace('/1/view?','/'+this.id_customer+'/view?').replace('&id_customer=1','&id_customer='+this.id_customer)+'" class="btn btn-default"><i class="fa fa-search"></i> {l s='Details' mod='ets_ordermanager' js=1}</a>';
                    html += '<button type="button" data-customer="' + this.id_customer + '" class="setup-customer btn btn-default pull-right" id="choose_customer_btn"><i class="fa fa-arrow-right"></i> {l s='Choose' mod='ets_ordermanager' js=1}</button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                  });
                }
                else {
                    $('#order_customers').show();
                    $('#search_order_customer').next().removeClass('loading');
                    html = '<div class="alert alert-warning">{l s='No customers found' mod='ets_ordermanager' js=1}</div>';
                }
                $('#order_customers').html(html);
                var search_items = customer_search.split(' ');
                $.each(search_items, function (index, value) {
                    $('#order_customers').highlight(value);
                });
                resetBind();
          },
          error: function (xhr, status, error) {

          }
    	});
    }
    function resetBind()
	{
		$('.fancybox').fancybox({
			'type': 'iframe',
			'width': '90%',
			'height': '90%',
		});

		$('.fancybox_customer').fancybox({
			'type': 'iframe',
			'width': '90%',
			'height': '90%',
			'afterClose' : function () {
				searchCustomers();
			}
		});

	}
    $('#customer_part').on('click','button.setup-customer',function(e){
        e.preventDefault();
		setupCustomer($(this).data('customer'));
		$(this).removeClass('setup-customer').addClass('change-customer').html('<i class="icon-refresh"></i>&nbsp;Change').blur();
		$(this).closest('.customerCard').addClass('selected-customer');
		$('.selected-customer .panel-heading').prepend('<i class="icon-ok text-success"></i>');
		$('.customerCard').not('.selected-customer').remove();
        $('#address_part').show();
        $('#search-customer-form-group').hide();
	});
    $('#customer_part').on('click','button.change-customer',function(e){
		e.preventDefault();
		$('#search-customer-form-group').show();
		$(this).blur();
	});
    function setupCustomer(idCustomer)
	{
		id_customer = idCustomer;
        var address_link = $('#new_address').attr('href');
        //$('#address_part').show();
        $('#new_address').attr('href', address_link.replace(/id_customer=[0-9]+/, 'id_customer='+id_customer));
		$.ajax({
			type:"POST",
			url : "{$link->getAdminLink('AdminCarts')|addslashes nofilter}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "searchCarts",
				id_customer: id_customer,
				id_cart: id_cart
			},
			success : function(res)
			{
				displaySummary(res);
				resetBind();
				$('.ets_order_popup').addClass('show');
				$('body').removeClass('loading');
			},
			error: function(xhr, status, error)
			{
				$('body').removeClass('loading');
			}
		});
	}
    function displaySummary(jsonSummary)
	{
		updateAddressesList(jsonSummary.addresses, jsonSummary.cart.id_address_delivery, jsonSummary.cart.id_address_invoice);
        resetBind();
	}
    function updateAddressesList(addresses, id_address_delivery, id_address_invoice)
	{
		var addresses_delivery_options = '';
		var addresses_invoice_options = '';
		var address_invoice_detail = '';
		var address_delivery_detail = '';
		var delivery_address_edit_link = '';
		var invoice_address_edit_link = '';
		$.each(addresses, function() {
			if (this.id_address == id_address_invoice)
			{
				address_invoice_detail = this.formated_address;
				invoice_address_edit_link = link_address_edit.replace('/1/edit?','/'+this.id_address+'/edit?').replace('&id_address=1','&id_address='+this.id_address)+"#";
			}
			if(this.id_address == id_address_delivery)
			{
				address_delivery_detail = this.formated_address;
				delivery_address_edit_link = link_address_edit.replace('/1/edit?','/'+this.id_address+'/edit?').replace('&id_address=1','&id_address='+this.id_address)+"#";
			}
			addresses_delivery_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_delivery ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
			addresses_invoice_options += '<option value="'+this.id_address+'" '+(this.id_address == id_address_invoice ? 'selected="selected"' : '')+'>'+this.alias+'</option>';
		});
		if (addresses.length == 0)
		{
			$('#addresses_err').show().html('{l s='You must add at least one address to process the order.' mod='ets_ordermanager' js=1}');
			$('#address_delivery, #address_invoice').hide();
		}
		else
		{
			$('#addresses_err').hide();
			$('#address_delivery, #address_invoice').show();
		}

		$('#id_address_delivery').html(addresses_delivery_options);
		$('#id_address_invoice').html(addresses_invoice_options);
		$('#address_delivery_detail').html(address_delivery_detail);
		$('#address_invoice_detail').html(address_invoice_detail);
		$('#edit_delivery_address').attr('href', delivery_address_edit_link);
		$('#edit_invoice_address').attr('href', invoice_address_edit_link);
	}
    function updateAddresses($this)
	{
        $this.parent().next('.well').addClass('address_loading');
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes nofilter}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateAddresses",
				id_customer: id_customer,
				id_cart: id_cart,
				id_address_delivery: $('#id_address_delivery option:selected').val(),
				id_address_invoice: $('#id_address_invoice option:selected').val()
				},
			success : function(res)
			{
				updateDeliveryOption($this);
			}
		});
	}
    function updateDeliveryOption($this)
	{
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes nofilter}",
			async: true,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "updateDeliveryOption",
				delivery_option: $('#delivery_option option:selected').val(),
				gift: $('#order_gift').is(':checked')?1:0,
				gift_message: $('#gift_message').val(),
				recyclable: $('#carrier_recycled_package').is(':checked')?1:0,
				id_customer: id_customer,
				id_cart: id_cart
				},
			success : function(res)
			{
				displaySummary(res);
                $this.parent().next('.well').removeClass('address_loading');
			}
		});
	}
    function getSummary()
	{
		useCart(id_cart);
	}
    function useCart(id_new_cart)
	{
		id_cart = id_new_cart;
		$('#id_cart').val(id_cart);
		$('#id_cart').val(id_cart);
		$.ajax({
			type:"POST",
			url: "{$link->getAdminLink('AdminCarts')|addslashes nofilter}",
			async: false,
			dataType: "json",
			data : {
				ajax: "1",
				token: "{getAdminToken tab='AdminCarts'}",
				tab: "AdminCarts",
				action: "getSummary",
				id_cart: id_cart,
				id_customer: id_customer
				},
			success : function(res)
			{
				displaySummary(res);
			}
		});
	}
</script>