/*!
*  @author    : Prestamatic
*  @link      : https://prestamatic.co.uk
*  @copyright : Copyright (c) 2018, Prestamatic
*  @license   : Licensed under the terms of the MIT license
* If you need any help, contact enquiries@prestamatic.co.uk - we will help!
*
*/

if ( 'undefined' === typeof CustomShippingRate ) {
	var CustomShippingRate = {};
}

jQuery(function($) {

	CustomShippingRate.init = function() {
		var display_with_carriers = $("#display_with_carriers").val(),
			available_carriers_num = $('#available_carriers_num').val();
		if (display_with_carriers == 1) {
			//$('#unable_to_calculate_custom_shipping').show();
			$('#shipping_container .alert-warning').hide();
			$('#carrier_area .alert-danger').hide();
			$('#checkout-delivery-step .alert-danger').hide();
			$('#noCarrierWarning').hide();
			$('#shipping_container button:eq(0)').hide();
			$('input[value="'+customshippingrate_carrier_id+',"]').closest('.delivery-option').find('.carrier-price').text( '--' );
		} else if (display_with_carriers == 0 && available_carriers_num == 0) {
			$('#unable_to_calculate_custom_shipping').show();
			$('#shipping_container .alert-warning').hide();
			$('#carrier_area .alert-danger').hide();
			$('#checkout-delivery-step .alert-danger').hide();
			$('#noCarrierWarning').hide();
			$('#shipping_container button:eq(0)').hide();
			$('input[value="'+customshippingrate_carrier_id+',"]').closest('.delivery-option').find('.carrier-price').text( '--' );
		}

		if (typeof customshippingrate_shipping_price !== 'undefined' && customshippingrate_shipping_price === '-0.01') {
			$('#cart-subtotal-shipping').find('.value:eq(0)').text('--');
			$('.cart-summary-line.cart-total').find('.value:eq(0)').text(prestashop.cart.subtotals.products.value);
		}

		if ($('.delivery-options').length) {
			$('#unable_to_calculate_custom_shipping').insertAfter('.delivery-options');
		}
		if ($('.delivery_option_radio:checked').length) {
			var el = '.delivery_option_radio:checked';
		} else {
			var el = 'input[name^="delivery_option"]:checked';
		}
		if ($(el).val() === customshippingrate_carrier_id + ',') {
			if (parseInt($('#customshippingrate_applied').val()) === 0) {
				$('#unable_to_calculate_custom_shipping').show();
				$('#delivery_message').parent().hide();
				$('button[name="confirmDeliveryOption"]').hide();
				$('#cart-subtotal-shipping').find('.value').text('--');
				$('#checkout-payment-step')
					.removeClass('-reachable')
					.addClass('-unreachable')
					.find('h1.step-title').addClass('not-allowed');
			}
		} else {
			$('#unable_to_calculate_custom_shipping').hide();
			$('#delivery_message').parent().show();
			$('button[name="confirmDeliveryOption"]').show();
		}
	}
	CustomShippingRate.init();
	if ( 'undefined' !== typeof prestashop ) {
		prestashop.on('changedCheckoutStep', CustomShippingRate.init);
	}

	$(document).on('click', 'input[name^="delivery_option"]', function(e){
		if ($(this).val() != '') {
			if ($(this).val() === customshippingrate_carrier_id + ',') {
				if (parseInt($('#customshippingrate_applied').val()) === 0) {
					$(document).scrollTop($("#unable_to_calculate_custom_shipping").offset().top);
					$('#unable_to_calculate_custom_shipping').show();
					$('#delivery_message').parent().hide();
					$('button[name="confirmDeliveryOption"]').hide();
					$('#cart-subtotal-shipping').find('.value').text('--');
				}
			} else {
				$('#unable_to_calculate_custom_shipping').hide();
				$('#delivery_message').parent().show();
				$('button[name="confirmDeliveryOption"]').show();
			}
		}
	});

	$('body').on('click', '#get_custom_shipping_rate', function(e) {
		e.preventDefault();
		$(this).hide();
		$(this).prev(".row").hide();
		$('#spinner_loader').fadeIn("fast");
		var custom_shipping_message = customshippingrate_message+': '+id_cart+' '+customshippingrate_customer_label+': '+id_customer+' '+$('#order_message').val();
		$.ajax({
			type: 'POST',
			url: contact_url,
			dataType: 'html',
			data: {
				'id_contact': id_contact,
				'from': customer_email,
				'id_order':	0,
				'id_cart':	id_cart,
				'id_address_delivery':	id_address_delivery,
				'id_customer':	id_customer,
				'MAX_FILE_SIZE': 20971520,
				'fileUpload': '',
				'message': custom_shipping_message,
				'token': customshippingrate_token,
				'action': 'submitQuoteRequest',
			},
			success: function(res) {
				$('#spinner_loader').fadeOut("fast");
				var hasErrors = res;
				if(hasErrors == 1){
					if($("#unable_to_calculate_custom_shipping .alert-danger").length == 0){
						$('#spinner_loader').after('<div class="alert-danger" style="width: auto; display: inline-block; padding: 5px 10px; margin-bottom: 25px;">'+decodeURIComponent(customshippingrate_send_error)+'</div>');
					}else{
						$("#unable_to_calculate_custom_shipping .alert-danger").html(decodeURIComponent(customshippingrate_send_error));
					}
				}else{
					if($("#unable_to_calculate_custom_shipping h4.success-message").length == 0){
						$('#spinner_loader').after('<h4 class="success-message text-success"><br>'+decodeURIComponent(customshippingrate_send_success)+'</h4>');
					}else{
						$("#unable_to_calculate_custom_shipping h4.success-message").html(decodeURIComponent(customshippingrate_send_success));
					}
				}
			},
		});
		return false;
	});

	$(document).ajaxComplete(function( event, request, settings ) {
		if ($('.delivery_option_radio:checked').length) {
			var el = '.delivery_option_radio:checked';
		} else {
			var el = 'input[name^="delivery_option"]:checked';
		}
		if ($(el).val() === customshippingrate_carrier_id + ',') {
			if (parseInt($('#customshippingrate_applied').val()) === 0) {
				$('#unable_to_calculate_custom_shipping').show();
				$('#delivery_message').parent().hide();
				$('button[name="confirmDeliveryOption"]').hide();
				$('#cart-subtotal-shipping').find('.value').text('--');
				$('#checkout-payment-step').removeClass('-reachable').addClass('-unreachable');
				$('#checkout-payment-step').find('h1.step-title').addClass('not-allowed');
			}
		} else {
			$('#unable_to_calculate_custom_shipping').hide();
			$('#delivery_message').parent().show();
			$('button[name="confirmDeliveryOption"]').show();
			$('#checkout-payment-step').removeClass('-reachable').addClass('-unreachable');
			$('#checkout-payment-step').find('h1.step-title').addClass('not-allowed');
		}
	});
});
