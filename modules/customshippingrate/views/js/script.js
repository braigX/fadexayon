/*!
*  @author    : Prestamatic
*  @link      : https://prestamatic.co.uk
*  @copyright : Copyright (c) 2018, Prestamatic
*  @license   : Licensed under the terms of the MIT license
* If you need any help, contact enquiries@prestamatic.co.uk - we will help!
*
*/

$(document).ready(function(){
	if(typeof ps_admin_version  !==  "undefined") {
		if (ps_admin_version == '1.6') {
			$('#carrier_form p#shipping_price').parent().parent().after('<div id="custom_shipping_container" style="display:none;"><div class="form-group"><label class="control-label required col-lg-3" for="custom_shipping_name">'+customshippingrate_name_label+': </label><div class="col-lg-6"><input class="ac_input form-control" type="text" name="custom_shipping_name" id="custom_shipping_name" value="'+customshippingrate_name+'"></div></div><div class="form-group"><label class="control-label required col-lg-3" for="custom_shipping_delay">'+customshippingrate_delay_label+': </label><div class="col-lg-6"><input class="ac_input form-control" type="text" name="custom_shipping_delay" id="custom_shipping_delay" value="'+customshippingrate_delay+'"></div></div><div class="form-group"><label class="control-label  required col-lg-3" for="custom_shipping_price_tax_excl">'+customshippingrate_price_label_tax_excl+': </label><div class="col-lg-1"><input class="ac_input form-control fixed-width-sm" type="text" name="custom_shipping_price_tax_excl" id="custom_shipping_price_tax_excl" value="'+customshippingrate_price_tax_excl+'"></div></div><div class="form-group"><label class="control-label  required col-lg-3" for="custom_shipping_price_tax_incl">'+customshippingrate_price_label_tax_incl+': </label><div class="col-lg-1"><input class="ac_input form-control fixed-width-sm" type="text" name="custom_shipping_price_tax_incl" id="custom_shipping_price_tax_incl" value="'+customshippingrate_price_tax_incl+'"></div></div><div class="panel-footer"><a href="#" class="btn btn-default pull-right" id="custom_shipping_price_set"><i class="process-icon-save"></i><span>'+customshippingrate_save_label+'</span></a><a href="#" class="btn btn-danger pull-left" id="custom_shipping_price_unset"><i class="process-icon-cancel"></i><span>'+customshippingrate_delete_label+'</span></a></div></div><div id="spinner_loader" style="text-align: center; display:none;"><i class="icon-spinner icon-spin icon-fw" style="font-size: 22px;"></i></div>');
			
			var display_custom_price_field = function(e) {
				if ($('#delivery_option').val() == customshippingrate_carrier_id + ',') {
					$('#shipping_price').parent().parent().hide();
					var shipping_price = $('#shipping_price').text();
					var custom_shipping_price_tax_excl = $.trim(shipping_price.substring(1));
					if (custom_shipping_price_tax_excl < 0) {
						custom_shipping_price_tax_excl = '0.00';
						$('#custom_shipping_price_tax_excl').val(custom_shipping_price_tax_excl);
					}
					$('#custom_shipping_container').show();
					$('#free_shipping').parent().parent().parent().hide();
				} else {
					$('#shipping_price').parent().parent().show();
					$('#custom_shipping_container').hide();
					$('#free_shipping').parent().parent().parent().show();
				}
			};
			
			$('#custom_shipping_price_tax_excl').keyup(function() {
				setTimeout(function() {
					var price_te = $('#custom_shipping_price_tax_excl').val();
					var price_ti = $('#custom_shipping_price_tax_excl').val() * (1 + (customshippingrate_tax_rate / 100));
					$('#custom_shipping_price_tax_incl').val(parseFloat(price_ti, 2).toFixed(2));
				}, 200);
			});
			$('#custom_shipping_price_tax_incl').keyup(function() {
				setTimeout(function() {
					var price_ti = $('#custom_shipping_price_tax_incl').val();
					var price_te = $('#custom_shipping_price_tax_incl').val() / (1 + (customshippingrate_tax_rate / 100));
					$('#custom_shipping_price_tax_excl').val(parseFloat(price_te, 2).toFixed(6));
				}, 200);
			});
		
			$('#delivery_option').bind('change', display_custom_price_field);
			
			$('#customer_part').on('click','button.setup-customer, a.use_cart',function(e) {
				setTimeout(function() {
					$("#delivery_option")[0].options.add( new Option("Please choose a carrier","0,", true, true) );
				}, 1000);
			});
			
			$('#products_part').on('click','a.delete_product, a.increaseqty_product, a.decreaseqty_product, #submitAddProduct',function(e) {
				setTimeout(function() {
					display_custom_price_field();
				}, 1000);
			});
			
			$('#id_address_delivery').bind('change', function() {
				$('#spinner_loader').fadeIn("fast");
				setTimeout(function() {
					display_custom_price_field();
					$('#spinner_loader').fadeOut("fast");
				}, 6000);
			});
		
			setTimeout(function() {
				if ($('#carriers_part:visible').length) {
					return display_custom_price_field();
				}
			}, 1000);
		
			$('#custom_shipping_price_set').bind('click', function(e) {
				e.preventDefault();
				$("#custom_shipping_container").hide();
				$('#spinner_loader').fadeIn("fast");
				$.ajax({
					type: 'POST',
					url: customshippingrate_ajax_url,
					dataType: 'html',
					data: {
						'ajax': true,
						'token': customshippingrate_token,
						'id_cart': id_cart,
						'id_address_delivery': $('#id_address_delivery').val(),
						'id_customer': id_customer,
						'value': $('#custom_shipping_price_tax_excl').val(),
						'name': $('#custom_shipping_name').val(),
						'delay': $('#custom_shipping_delay').val(),
					},
					success: function(res) {
						updateDeliveryOption();
						setTimeout(function(){ 
							$('#spinner_loader').fadeOut("fast");
							$('#shipping_price').parent().parent().show();
							$('#free_shipping').parent().parent().parent().show();
						}, 2000);
					},
				});
				return false;
			});
			
			$('#custom_shipping_price_unset').bind('click', function(e) {
				e.preventDefault();
				$(this).parent().parent().hide();
				$('#spinner_loader').fadeIn("fast");
				$.ajax({
					type: 'POST',
					url: customshippingrate_ajax_url,
					dataType: 'json',
					data: {
						'ajax': true,
						'token': customshippingrate_token,
						'id_cart': id_cart,
						'id_customer': id_customer,
						'remove': 1,
					},
					success: function(res) {
						updateDeliveryOption();
						setTimeout(function(){ 
							$('#spinner_loader').fadeOut("fast");
							var shipping_price = $('#shipping_price').text();
							var custom_shipping_price_tax_excl = $.trim(shipping_price.substring(1));
							if (custom_shipping_price_tax_excl < 0) {
								custom_shipping_price_tax_excl = 0.00;
							}
							$('#custom_shipping_name').val('');
							$('#custom_shipping_delay').val('');
							$('#custom_shipping_price_tax_excl').val('0.00');
							$('#custom_shipping_container').show();
						}, 2000);
					},
				});
				return false;
			});
		} else {
			$('#shipping-block .js-total-shipping-tax-inc').parent().parent().parent().after('<div id="custom_shipping_container" style="display:none;"><div class="form-group row"><div class="col-3"><span class="float-right">'+customshippingrate_name_label+': </span></div><div class="col-4"><input class="ac_input form-control" type="text" name="custom_shipping_name" id="custom_shipping_name" value="'+customshippingrate_name+'"></div></div><div class="form-group row"><div class="col-3"><span class="float-right">'+customshippingrate_delay_label+': </span></div><div class="col-4"><input class="ac_input form-control" type="text" name="custom_shipping_delay" id="custom_shipping_delay" value="'+customshippingrate_delay+'"></div></div><div class="form-group row"><div class="col-3"><span class="float-right">'+customshippingrate_price_label_tax_excl+': </span></div><div class="col-2"><input class="ac_input form-control fixed-width-sm" type="text" name="custom_shipping_price_tax_excl" id="custom_shipping_price_tax_excl" value="'+customshippingrate_price_tax_excl+'"></div></div><div class="form-group row"><div class="col-3"><span class="float-right">'+customshippingrate_price_label_tax_incl+': </span></div><div class="col-2"><input class="ac_input form-control fixed-width-sm" type="text" name="custom_shipping_price_tax_incl" id="custom_shipping_price_tax_incl" value="'+customshippingrate_price_tax_incl+'"></div></div><div class="card-footer clearfix"><a href="#" class="btn btn-primary float-right" id="custom_shipping_price_set"><i class="process-icon-save"></i><span>'+customshippingrate_save_label+'</span></a><a href="#" class="btn btn-danger float-left" id="custom_shipping_price_unset"><i class="process-icon-cancel"></i><span>'+customshippingrate_delete_label+'</span></a></div></div><div id="spinner_loader" style="text-align: center; display:none;"><i class="icon-spinner icon-spin icon-fw" style="font-size: 22px;"></i></div>');
			
			var display_custom_price_field = function(e) {
				if ($('#delivery-option-select').val() == customshippingrate_carrier_id) {
					$('.js-total-shipping-tax-inc').parent().parent().parent().hide();
					var shipping_price = $('.js-total-shipping-tax-inc').text();
					var custom_shipping_price_tax_excl = $.trim(shipping_price.substring(1));
					if (custom_shipping_price_tax_excl < 0) {
						custom_shipping_price_tax_excl = '0.00';
						$('#custom_shipping_price_tax_excl').val(custom_shipping_price_tax_excl);
					}
					$('#custom_shipping_container').show();
					$('.js-free-shipping-switch').parent().parent().parent().hide();
				} else {
					$('.js-total-shipping-tax-inc').parent().parent().parent().show();
					$('#custom_shipping_container').hide();
					$('.js-free-shipping-switch').parent().parent().parent().show();
				}
			};
			
			$('#custom_shipping_price_tax_excl').keyup(function() {
				setTimeout(function() {
					var price_te = $('#custom_shipping_price_tax_excl').val();
					var price_ti = $('#custom_shipping_price_tax_excl').val() * (1 + (customshippingrate_tax_rate / 100));
					$('#custom_shipping_price_tax_incl').val(parseFloat(price_ti, 2).toFixed(2));
				}, 200);
			});
			$('#custom_shipping_price_tax_incl').keyup(function() {
				setTimeout(function() {
					var price_ti = $('#custom_shipping_price_tax_incl').val();
					var price_te = $('#custom_shipping_price_tax_incl').val() / (1 + (customshippingrate_tax_rate / 100));
					$('#custom_shipping_price_tax_excl').val(parseFloat(price_te, 2).toFixed(6));
				}, 200);
			});
		
			$('#delivery-option-select').bind('change', display_custom_price_field);
			
			$('#customer-search-block').on('click','button.js-use-cart-btn, button.js-use-order-btn',function(e) {
				setTimeout(function() {
					$("#delivery-option-select")[0].options.add( new Option("Please choose a carrier","0,", true, true) );
					display_custom_price_field();
				}, 2000);
			});
			
			$('#cart-block').on('click','button.js-product-remove-btn, #add-product-to-cart-btn',function(e) {
				setTimeout(function() {
					display_custom_price_field();
				}, 1000);
			});
			
			$('#cart-block').on('change','.js-product-qty-input',function(e) {
				setTimeout(function() {
					display_custom_price_field();
				}, 1000);
			});
			
			$('#delivery-address-select').bind('change', function() {
				$('#spinner_loader').fadeIn("fast");
				setTimeout(function() {
					display_custom_price_field();
					$('#spinner_loader').fadeOut("fast");
				}, 6000);
			});
		
			setTimeout(function() {
				if ($('#shipping-block:visible').length) {
					return display_custom_price_field();
				}
			}, 1000);
		
			$('#custom_shipping_price_set').bind('click', function(e) {
				e.preventDefault();
				$("#custom_shipping_container").hide();
				$('#spinner_loader').fadeIn("fast");
				var id_cart = $('#cart_summary_cart_id').val(),
				id_customer = $('.js-customer-id').val();
				$.ajax({
					type: 'POST',
					url: customshippingrate_ajax_url,
					dataType: 'html',
					data: {
						'ajax': true,
						'token': customshippingrate_token,
						'id_cart': id_cart,
						'id_address_delivery': $('#delivery-address-select').val(),
						'id_customer': id_customer,
						'value': $('#custom_shipping_price_tax_excl').val(),
						'name': $('#custom_shipping_name').val(),
						'delay': $('#custom_shipping_delay').val(),
					},
					success: function(res) {
						//updateDeliveryOption();
						setTimeout(function(){ 
							$('#spinner_loader').fadeOut("fast");
							$('.js-total-shipping-tax-inc').parent().parent().show();
							$('.js-free-shipping-switch').parent().parent().parent().show();
							$('#delivery-option-select').trigger('change');
						}, 2000);
					},
				});
				return false;
			});
			
			$('#custom_shipping_price_unset').bind('click', function(e) {
				e.preventDefault();
				$(this).parent().parent().hide();
				$('#spinner_loader').fadeIn("fast");
				var id_cart = $('#cart_summary_cart_id').val(),
				id_customer = $('.js-customer-id').val();
				$.ajax({
					type: 'POST',
					url: customshippingrate_ajax_url,
					dataType: 'json',
					data: {
						'ajax': true,
						'token': customshippingrate_token,
						'id_cart': id_cart,
						'id_customer': id_customer,
						'remove': 1,
					},
					success: function(res) {
						//updateDeliveryOption();
						setTimeout(function(){ 
							$('#spinner_loader').fadeOut("fast");
							var shipping_price = $('.js-total-shipping-tax-inc').text();
							var custom_shipping_price_tax_excl = $.trim(shipping_price.substring(1));
							if (custom_shipping_price_tax_excl < 0) {
								custom_shipping_price_tax_excl = 0.00;
							}
							$('#custom_shipping_name').val('');
							$('#custom_shipping_delay').val('');
							$('#custom_shipping_price_tax_excl').val('0.00');
							$('#custom_shipping_container').show();
							$('#delivery-option-select').trigger('change');
						}, 2000);
					},
				});
				return false;
			});
			
			$(document).ajaxComplete(function( event, request, settings ) {
				if ($("body").hasClass("adminorders")) {
					setTimeout(function(){ 
						if ($('#delivery-option-select').val() == customshippingrate_carrier_id) {
							$('.js-total-shipping-tax-inc').parent().parent().parent().hide();
							var shipping_price = $('.js-total-shipping-tax-inc').text();
							var custom_shipping_price_tax_excl = $.trim(shipping_price.substring(1));
							if (custom_shipping_price_tax_excl < 0) {
								custom_shipping_price_tax_excl = '0.00';
								$('#custom_shipping_price_tax_excl').val(custom_shipping_price_tax_excl);
							}
							$('#custom_shipping_container').show();
							$('.js-free-shipping-switch').parent().parent().parent().hide();
						} else {
							$('.js-total-shipping-tax-inc').parent().parent().parent().show();
							$('#custom_shipping_container').hide();
							$('.js-free-shipping-switch').parent().parent().parent().show();
						}
						console.log('ajaxComplete');
					}, 2000);
				}
			});
		}
	}
	
	/* Configuration */
	if ($('#CUSTOMSHIP_ID_CONTACT').val() != 0) {
		$('#CUSTOMSHIP_EMAIL_TO').parent().parent().fadeOut('fast');
	}
	$('#CUSTOMSHIP_ID_CONTACT').bind('change', function() {
	    if ($(this).val() == 0) {
			$('#CUSTOMSHIP_EMAIL_TO').parent().parent().fadeIn('fast');
		} else {
			$('#CUSTOMSHIP_EMAIL_TO').parent().parent().fadeOut('fast');
		}
	});
});
