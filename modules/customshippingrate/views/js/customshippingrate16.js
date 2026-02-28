/*!
*  @author    : Prestamatic
*  @link      : https://prestamatic.co.uk
*  @copyright : Copyright (c) 2018, Prestamatic
*  @license   : Licensed under the terms of the MIT license
* If you need any help, contact enquiries@prestamatic.co.uk - we will help!
*
*/

$(document).ready(function(){
	if ($("#display_with_carriers").val() == 1 && id_customer != 0) {
		$('#shipping_container .alert-warning').hide();
		$('#carrier_area .alert-danger').hide();
		$('#noCarrierWarning').hide();
        $('#shipping_container button:eq(0)').hide();
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('td.delivery_option_price').find('div.delivery_option_price').text( '--' );
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('.best_grade').hide();
	} else if ($("#display_with_carriers").val() == 0 && $('#available_carriers_num').val() == 0 && id_customer != 0) {
		$('#unable_to_calculate_custom_shipping').show();
		$('#shipping_container .alert-warning').hide();
		$('#carrier_area .alert-danger').hide();
		$('#noCarrierWarning').hide();
        $('#shipping_container button:eq(0)').hide();
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('td.delivery_option_price').find('div.delivery_option_price').text( '--' );
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('.best_grade').hide();
	}
	
	if ($('#input_virtual_carrier').val() == 0) {
		$('#uniform-cgv').parent().parent().show();
	}
	
	if ($('.delivery_options_address').length) {
		$('#unable_to_calculate_custom_shipping').insertAfter('.delivery_options_address');
	}
	if ($('.delivery_option_radio:checked').length) {
		var el = '.delivery_option_radio:checked';
	} else {
		var el = 'input[name^="delivery_option"]:checked';
	}
	if ($(el).val() === customshippingrate_carrier_id + ',') {
		if (parseInt($('#customshippingrate_applied').val()) === 0) {
			$('#unable_to_calculate_custom_shipping').show();
			$('#message').parent().hide();
			$('#extra_carrier').next('.carrier_title').hide();
			$('#uniform-cgv').parent().parent().hide();
			$('button[name="processCarrier"]').hide();
			$('#cgv').prop('checked', false);
			if (!!$.prototype.uniform) {
				$.uniform.update('#cgv');
			}
			$('#HOOK_TOP_PAYMENT').hide();
			$('#opc_payment_methods-content #HOOK_PAYMENT').hide();
		}
	} else {
		$('#unable_to_calculate_custom_shipping').hide();
		$('#message').parent().show();
		$('#extra_carrier').next('.carrier_title').show();
		$('#uniform-cgv').parent().parent().show();
		$('button[name="processCarrier"]').show();
		$('#HOOK_TOP_PAYMENT').show();
		$('#opc_payment_methods-content #HOOK_PAYMENT').show();
	}
	
	$(document).on('click', '.delivery_option_radio', function(e){
		if ($(this).val() != '') {
			if ($(this).val() === customshippingrate_carrier_id + ',') {
				if (parseInt($('#customshippingrate_applied').val()) === 0) {
					$(document).scrollTop($("#unable_to_calculate_custom_shipping").offset().top);
					$('#unable_to_calculate_custom_shipping').show();
					$('#message').parent().hide();
					$('#extra_carrier').next('.carrier_title').hide();
					$('#uniform-cgv').parent().parent().hide();
					$('button[name="processCarrier"]').hide();
					$('#cgv').prop('checked', false);
					if (!!$.prototype.uniform) {
						$.uniform.update('#cgv');
					}
					$('#HOOK_TOP_PAYMENT').hide();
					$('#opc_payment_methods-content #HOOK_PAYMENT').hide();
				}
			} else {
				$('#unable_to_calculate_custom_shipping').hide();
				$('#message').parent().show();
				$('#extra_carrier').next('.carrier_title').show();
				$('#uniform-cgv').parent().parent().show();
				$('button[name="processCarrier"]').show();
				$('#HOOK_TOP_PAYMENT').show();
				$('#opc_payment_methods-content #HOOK_PAYMENT').show();
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
						$('#spinner_loader').after('<h4 class="success-message text-success">'+decodeURIComponent(customshippingrate_send_success)+'</h4>');
					}else{
						$("#unable_to_calculate_custom_shipping h4.success-message").html(decodeURIComponent(customshippingrate_send_success));
					}
				}
			},
		});
		return false;
	});
});

$(document).ajaxComplete(function( event, request, settings ) {
    if ($("#display_with_carriers").val() == 1 && id_customer != 0) {
		$('#shipping_container .alert-warning').hide();
		$('#carrier_area .alert-danger').hide();
		$('#noCarrierWarning').hide();
        $('#shipping_container button:eq(0)').hide();
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('td.delivery_option_price').find('div.delivery_option_price').text( '--' );
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('.best_grade').hide();
	} else if ($("#display_with_carriers").val() == 0 && $('#available_carriers_num').val() == 0 && id_customer != 0) {
		$('#unable_to_calculate_custom_shipping').show();
		$('#shipping_container .alert-warning').hide();
		$('#carrier_area .alert-danger').hide();
		$('#noCarrierWarning').hide();
        $('#shipping_container button:eq(0)').hide();
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('td.delivery_option_price').find('div.delivery_option_price').text( '--' );
		$('input[value="'+customshippingrate_carrier_id+',"]').closest('tr').find('.best_grade').hide();
	}
});

$(document).ajaxSuccess(function (event, xhr, settings) {
	if (typeof(settings.data) != 'undefined') {
		var ajax_data = {},
		st;

		st = settings.data.split('&');

		if (st.length) {
			for (var i in st) {
				ajax_data[decodeURIComponent(st[i].split('=')[0])] = decodeURIComponent(st[i].split('=')[1]);
			}

			if (typeof(ajax_data.method) != 'undefined' && (ajax_data.method == 'updateCarrierAndGetPayments')) {

				if ($('.delivery_options_address').length) {
					$('#unable_to_calculate_custom_shipping').insertAfter('.delivery_options_address');
				}
				if ($('.delivery_option_radio:checked').length) {
					var el = '.delivery_option_radio:checked';
				} else {
					var el = 'input[name^="delivery_option"]:checked';
				}
				if ($(el).val() === customshippingrate_carrier_id + ',') {
					if (parseInt($('#customshippingrate_applied').val()) === 0) {
						$('#unable_to_calculate_custom_shipping').show();
						$('#message').parent().hide();
						$('#extra_carrier').next('.carrier_title').hide();
						$('#uniform-cgv').parent().parent().hide();
						$('button[name="processCarrier"]').hide();
						$('#cgv').prop('checked', false);
						if (!!$.prototype.uniform) {
							$.uniform.update('#cgv');
						}
						$('#HOOK_TOP_PAYMENT').hide();
						$('#opc_payment_methods-content #HOOK_PAYMENT').hide();
					}
				} else {
					$('#unable_to_calculate_custom_shipping').hide();
					$('#message').parent().show();
					$('#extra_carrier').next('.carrier_title').show();
					$('#uniform-cgv').parent().parent().show();
					$('button[name="processCarrier"]').show();
					$('#HOOK_TOP_PAYMENT').show();
					$('#opc_payment_methods-content #HOOK_PAYMENT').show();
				}

			}
		}
	}
});