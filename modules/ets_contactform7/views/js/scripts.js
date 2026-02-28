/**
  * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
( function( $ ) {

	'use strict';
    var wpcf7 = {"apiSettings":{"root":"http:\/\/web.prestahero.com\/chung\/wp494\/wp-json\/contact-form-7\/v1","namespace":"contact-form-7\/v1"},"recaptcha":{"messages":{"empty":"Please verify that you are not a robot."}}};
	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		return;
	}

	wpcf7 = $.extend( {
		cached: 0,
		inputs: []
	}, wpcf7 );
    
	$( function() {
		wpcf7.supportHtml5 = ( function() {
			var features = {};
			var input = document.createElement( 'input' );

			features.placeholder = 'placeholder' in input;

			var inputTypes = [ 'email', 'url', 'tel', 'number', 'range', 'date' ];

			$.each( inputTypes, function( index, value ) {
				input.setAttribute( 'type', value );
				features[ value ] = input.type !== 'text';
			} );
			return features;
		} )();

		$( 'div.wpcf7 > form' ).each( function() {
			var $form = $( this );
			wpcf7.initForm( $form );

			if ( wpcf7.cached ) {
				wpcf7.refill( $form );
			}
		} );
	} );

	wpcf7.getId = function( form ) {
		return parseInt( $( 'input[name="_wpcf7"]', form ).val(), 10 );
	};

	wpcf7.initForm = function( form ) {
		var $form = $( form );
		$form.submit( function( event ) {
			if ( typeof window.FormData !== 'function' ) {
				return;
			}

			wpcf7.submit( $form );
			event.preventDefault();
		} );
        if($form.find('.ajax-loader').length<=0)
            $form.find('.wpcf7-submit').after('<span class="ajax-loader"></span>' );
		wpcf7.toggleSubmit( $form );

		$form.on( 'click', '.wpcf7-acceptance', function() {
			wpcf7.toggleSubmit( $form );
		} );

		// Exclusive Checkbox
		$( '.wpcf7-exclusive-checkbox', $form ).on( 'click', 'input:checkbox', function() {
			var name = $( this ).attr( 'name' );
			$form.find( 'input:checkbox[name="' + name + '"]' ).not( this ).prop( 'checked', false );
		} );

		// Free Text Option for Checkboxes and Radio Buttons
		$( '.wpcf7-list-item.has-free-text', $form ).each( function() {
			var $freetext = $( ':input.wpcf7-free-text', this );
			var $wrap = $( this ).closest( '.wpcf7-form-control' );
			if ( $( ':checkbox, :radio', this ).is( ':checked' ) ) {
				$freetext.prop( 'disabled', false );
			} else {
				$freetext.prop( 'disabled', true );
			}
			$wrap.on( 'change', ':checkbox, :radio', function() {
				var $cb = $( '.has-free-text', $wrap ).find( ':checkbox, :radio' );
				if ( $cb.is( ':checked' ) ) {
					$freetext.prop( 'disabled', false ).focus();
				} else {
					$freetext.prop( 'disabled', true );
				}
			});
		} );

		// Placeholder Fallback
		if ( ! wpcf7.supportHtml5.placeholder ) {
			$( '[placeholder]', $form ).each( function() {
				$( this ).val( $( this ).attr( 'placeholder' ) );
				$( this ).addClass( 'placeheld' );

				$( this ).focus( function() {
					if ( $( this ).hasClass( 'placeheld' ) ) {
						$( this ).val( '' ).removeClass( 'placeheld' );
					}
				} );

				$( this ).blur( function() {
					if ( '' === $( this ).val() ) {
						$( this ).val( $( this ).attr( 'placeholder' ) );
						$( this ).addClass( 'placeheld' );
					}
				} );
			} );
		}

		if ( wpcf7.jqueryUi && ! wpcf7.supportHtml5.date ) {
			$form.find( 'input.wpcf7-date[type="date"]' ).each( function() {
				$( this ).datepicker( {
					dateFormat: 'yy-mm-dd',
					minDate: new Date( $( this ).attr( 'min' ) ),
					maxDate: new Date( $( this ).attr( 'max' ) )
				} );
			} );
		}

		if ( wpcf7.jqueryUi && ! wpcf7.supportHtml5.number ) {
			$form.find( 'input.wpcf7-number[type="number"]' ).each( function() {
				$( this ).spinner( {
					min: $( this ).attr( 'min' ),
					max: $( this ).attr( 'max' ),
					step: $( this ).attr( 'step' )
				} );
			} );
		}

		// Character Count
		$( '.wpcf7-character-count', $form ).each( function() {
			var $count = $( this );
			var name = $count.attr( 'data-target-name' );
			var down = $count.hasClass( 'down' );
			var starting = parseInt( $count.attr( 'data-starting-value' ), 10 );
			var maximum = parseInt( $count.attr( 'data-maximum-value' ), 10 );
			var minimum = parseInt( $count.attr( 'data-minimum-value' ), 10 );

			var updateCount = function( target ) {
				var $target = $( target );
				var length = $target.val().length;
				var count = down ? starting - length : length;
				$count.attr( 'data-current-value', count );
				$count.text( count );

				if ( maximum && maximum < length ) {
					$count.addClass( 'too-long' );
				} else {
					$count.removeClass( 'too-long' );
				}

				if ( minimum && length < minimum ) {
					$count.addClass( 'too-short' );
				} else {
					$count.removeClass( 'too-short' );
				}
			};

			$( ':input[name="' + name + '"]', $form ).each( function() {
				updateCount( this );

				$( this ).keyup( function() {
					updateCount( this );
				} );
			} );
		} );

		// URL Input Correction
		$form.on( 'change', '.wpcf7-validates-as-url', function() {
			var val = $.trim( $( this ).val() );

			if ( val
			&& ! val.match( /^[a-z][a-z0-9.+-]*:/i )
			&& -1 !== val.indexOf( '.' ) ) {
				val = val.replace( /^\/+/, '' );
				val = 'http://' + val;
			}

			$( this ).val( val );
		} );
	};

	wpcf7.submit = function( form ) {
	    if (typeof tinyMCE !== 'undefined' && tinyMCE.editors.length > 0 && $('.autoload_rte_ctf7').length) {
                tinyMCE.triggerSave();
        }
		if ( typeof window.FormData !== 'function' ) {
			return;
		}
		var $form = $( form );
        if($form.hasClass('is-active') )
            return false;
		$( '.ajax-loader', $form ).addClass( 'is-active' );
        $form.addClass( 'is-active' );
		$( '[placeholder].placeheld', $form ).each( function( i, n ) {
			$( n ).val( '' );
		} );

		wpcf7.clearResponse( $form );

		var formData = new FormData( $form.get( 0 ) );

		var detail = {
			id: $form.closest( 'div.wpcf7' ).attr( 'id' ),
			status: 'init',
			inputs: [],
			formData: formData
		};

		$.each( $form.serializeArray(), function( i, field ) {
			if ( '_wpcf7' == field.name ) {
				detail.contactFormId = field.value;
			} else if ( '_etscf7_version' == field.name ) {
				detail.pluginVersion = field.value;
			} else if ( '_etscf7_locale' == field.name ) {
				detail.contactFormLocale = field.value;
			} else if ( '_etscf7_unit_tag' == field.name ) {
				detail.unitTag = field.value;
			} else if ( '_etscf7_container_post' == field.name ) {
				detail.containerPostId = field.value;
			} else if ( field.name.match( /^_etscf7_\w+_free_text_/ ) ) {
				var owner = field.name.replace( /^_etscf7_\w+_free_text_/, '' );
				detail.inputs.push( {
					name: owner + '-free-text',
					value: field.value
				} );
			} else if ( field.name.match( /^_/ ) ) {
				// do nothing
			} else {
				detail.inputs.push( field );
			}
		} );
		wpcf7.triggerEvent( $form.closest( 'div.wpcf7' ), 'beforesubmit', detail );
		var ajaxSuccess = function( data, status, xhr, $form ) {
			detail.id = $( data.into ).attr( 'id' );
			detail.status = data.status;
			detail.apiResponse = data;
			var $message = $( '.wpcf7-response-output', $form );
            $message.removeClass( 'alert-warning' );
            $message.removeClass( 'alert-success' );
			switch ( data.status ) {
				case 'validation_failed':
					$.each( data.invalidFields, function( i, n ) {
						$( n.into, $form ).each( function() {
							wpcf7.notValidTip( this, n.message );
							$( '.wpcf7-form-control', this ).addClass( 'wpcf7-not-valid' );
							$( '[aria-invalid]', this ).attr( 'aria-invalid', 'true' );
						} );
					} );
					$message.addClass( 'alert alert-warning' );
					$form.addClass( 'invalid' );
					wpcf7.triggerEvent( data.into, 'invalid', detail );
					break;
				case 'acceptance_missing':
					$message.addClass( 'alert alert-warning' );
					$form.addClass( 'unaccepted' );
					wpcf7.triggerEvent( data.into, 'unaccepted', detail );
					break;
				case 'spam':
					$message.addClass( 'alert alert-warning' );
					$form.addClass( 'spam' );
					$( '[name="g-recaptcha-response"]', $form ).each( function() {
						if ( '' === $( this ).val() ) {
							var $recaptcha = $( this ).closest( '.wpcf7-form-control-wrap' );
							wpcf7.notValidTip( $recaptcha, wpcf7.recaptcha.messages.empty );
						}
					} );
					wpcf7.triggerEvent( data.into, 'spam', detail );
					break;
				case 'aborted':
					$message.addClass( 'alert alert-warning' );
					$form.addClass( 'aborted' );

					wpcf7.triggerEvent( data.into, 'aborted', detail );
					break;
				case 'mail_sent':
					$message.addClass( 'alert alert-success' );
					$form.addClass( 'sent' );
					wpcf7.triggerEvent( data.into, 'mailsent', detail );
					break;
				case 'mail_failed':
					$message.addClass( 'alert alert-warning' );
					$form.addClass( 'failed' );

					wpcf7.triggerEvent( data.into, 'mailfailed', detail );
					break;
				case 'mail_redirect':
					$message.hide().remove();
					window.location.href = data.message;
					break;
				case 'load_thank_page':
					$message.hide().remove();
					window.location.href = data.message;
					break;
				default:
					var customStatusClass = 'custom-'
						+ data.status.replace( /[^0-9a-z]+/i, '-' );
					$message.addClass( 'wpcf7-' + customStatusClass );
					$form.addClass( customStatusClass );
			}

			wpcf7.refill( $form, data );

			wpcf7.triggerEvent( data.into, 'submit', detail );

			if ( 'mail_sent' == data.status ) {
				$form.each( function() {
					this.reset();
				} );
			}

			$form.find( '[placeholder].placeheld' ).each( function( i, n ) {
				$( n ).val( $( n ).attr( 'placeholder' ) );
			} );

			$message.html( '' ).append( data.message ).slideDown( 'fast' );
			$message.attr( 'role', 'alert' );

			$( '.screen-reader-response', $form.closest( '.wpcf7' ) ).each( function() {
				var $response = $( this );
				$response.html( '' ).attr( 'role', '' ).append( data.message );

				if ( data.invalidFields ) {
					var $invalids = $( '<ul></ul>' );

					$.each( data.invalidFields, function( i, n ) {
						if ( n.idref ) {
							var $li = $( '<li></li>' ).append( $( '<a></a>' ).attr( 'href', '#' + n.idref ).append( n.message ) );
						} else {
							var $li = $( '<li></li>' ).append( n.message );
						}

						$invalids.append( $li );
					} );

					$response.append( $invalids );
				}

				$response.attr( 'role', 'alert' ).focus();
			} );
		};
		$.ajax( {
			type: 'POST',
			url: $form.attr('action'),
			data: formData,
			dataType: 'json',
			processData: false,
			contentType: false
		} ).done( function( data, status, xhr ) {
			ajaxSuccess( data, status, xhr, $form );
			$( '.ajax-loader', $form ).removeClass( 'is-active' );
            $form.removeClass('is-active');
            if($form.find('.pa-captcha-refesh').length)
                refeshImage($form.find('.pa-captcha-refesh'));
            $(document).trigger("wpcf7submit");
		} ).fail( function( xhr, status, error ) {
            $form.find('.wpcf7-response-output').removeClass( 'alert-warning' );
            $form.find('.wpcf7-response-output').removeClass( 'alert-success' );
            $form.find('.wpcf7-response-output').html('There was a technical error when submitting the form. Please contact webmaster for more information');
            $form.find('.wpcf7-response-output').addClass( 'alert alert-warning' );
            $form.find('.wpcf7-response-output').addClass( 'invalid' ).show();
            $form.find('.ajax-loader').removeClass('is-active');
            $form.removeClass('is-active');
            if($form.find('.pa-captcha-refesh'))
                refeshImage($form.find('.pa-captcha-refesh'));
            $(document).trigger("wpcf7submit");
		} );
	};
	wpcf7.triggerEvent = function( target, name, detail ) {
		var $target = $( target );

		/* DOM event */
		var event = new CustomEvent( 'wpcf7' + name, {
			bubbles: true,
			detail: detail
		} );
		/* jQuery event */
		$target.trigger( 'wpcf7:' + name, detail );
		$target.trigger( name + '.wpcf7', detail );
        
	};

	wpcf7.toggleSubmit = function( form, state ) {
		var $form = $( form );
		var $submit = $( 'input:submit', $form );

		if ( typeof state !== 'undefined' ) {
			$submit.prop( 'disabled', ! state );
			return;
		}

		if ( $form.hasClass( 'wpcf7-acceptance-as-validation' ) ) {
			return;
		}
		$submit.prop( 'disabled', false );

		$( '.wpcf7-acceptance', $form ).each( function() {
			var $span = $( this );
			var $input = $( 'input:checkbox', $span );

			if ( ! $span.hasClass( 'optional' ) ) {
				if ( $span.hasClass( 'invert' ) && $input.is( ':checked' )
				|| ! $span.hasClass( 'invert' ) && ! $input.is( ':checked' ) ) {
                    $submit.prop( 'disabled', true );
					return false;
				}
			}
		} );
	};

	wpcf7.notValidTip = function( target, message ) {
		var $target = $( target );
		$( '.wpcf7-not-valid-tip', $target ).remove();
		$( '<span role="alert" class="wpcf7-not-valid-tip"></span>' )
			.text( message ).appendTo( $target );

		if ( $target.is( '.use-floating-validation-tip *' ) ) {
			var fadeOut = function( target ) {
				$( target ).not( ':hidden' ).animate( {
					opacity: 0
				}, 'fast', function() {
					$( this ).css( { 'z-index': -100 } );
				} );
			};

			$target.on( 'mouseover', '.wpcf7-not-valid-tip', function() {
				fadeOut( this );
			} );

			$target.on( 'focus', ':input', function() {
				fadeOut( $( '.wpcf7-not-valid-tip', $target ) );
			} );
		}
	};

	wpcf7.refill = function( form, data ) {
		var $form = $( form );

		var refillCaptcha = function( $form, items ) {
			$.each( items, function( i, n ) {
				$form.find( ':input[name="' + i + '"]' ).val( '' );
				$form.find( 'img.wpcf7-captcha-' + i ).attr( 'src', n );
				var match = /([0-9]+)\.(png|gif|jpeg)$/.exec( n );
				$form.find( 'input:hidden[name="_etscf7_captcha_challenge_' + i + '"]' ).attr( 'value', match[ 1 ] );
			} );
		};

		var refillQuiz = function( $form, items ) {
			$.each( items, function( i, n ) {
				$form.find( ':input[name="' + i + '"]' ).val( '' );
				$form.find( ':input[name="' + i + '"]' ).siblings( 'span.wpcf7-quiz-label' ).text( n[ 0 ] );
				$form.find( 'input:hidden[name="_etscf7_quiz_answer_' + i + '"]' ).attr( 'value', n[ 1 ] );
			} );
		};

		if ( typeof data === 'undefined' ) {
			$.ajax( {
				type: 'GET',
				url: wpcf7.apiSettings.getRoute(
					'/contact-forms/' + wpcf7.getId( $form ) + '/refill' ),
				beforeSend: function( xhr ) {
					var nonce = $form.find( ':input[name="_wpnonce"]' ).val();

					if ( nonce ) {
						xhr.setRequestHeader( 'X-WP-Nonce', nonce );
					}
				},
				dataType: 'json'
			} ).done( function( data, status, xhr ) {
				if ( data.captcha ) {
					refillCaptcha( $form, data.captcha );
				}

				if ( data.quiz ) {
					refillQuiz( $form, data.quiz );
				}
			} );

		} else {
			if ( data.captcha ) {
				refillCaptcha( $form, data.captcha );
			}

			if ( data.quiz ) {
				refillQuiz( $form, data.quiz );
			}
		}
	};

	wpcf7.clearResponse = function( form ) {
		var $form = $( form );
		$form.removeClass( 'invalid spam sent failed' );
		$form.siblings( '.screen-reader-response' ).html( '' ).attr( 'role', '' );

		$( '.wpcf7-not-valid-tip', $form ).remove();
		$( '[aria-invalid]', $form ).attr( 'aria-invalid', 'false' );
		$( '.wpcf7-form-control', $form ).removeClass( 'wpcf7-not-valid' );

		$( '.wpcf7-response-output', $form )
			.hide().empty().removeAttr( 'role' )
			.removeClass( 'wpcf7-mail-sent-ok wpcf7-mail-sent-ng wpcf7-validation-errors wpcf7-spam-blocked' );
	};

	wpcf7.apiSettings.getRoute = function( path ) {
		var url = wpcf7.apiSettings.root;

		url = url.replace(
			wpcf7.apiSettings.namespace,
			wpcf7.apiSettings.namespace + path );

		return url;
	};

} )( jQuery );

/*
 * Polyfill for Internet Explorer
 * See https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
 */
( function () {
	if ( typeof window.CustomEvent === "function" ) return false;

	function CustomEvent ( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };
		var evt = document.createEvent( 'CustomEvent' );
		evt.initCustomEvent( event,
			params.bubbles, params.cancelable, params.detail );
		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;

	window.CustomEvent = CustomEvent;
} )();

$(document).ready(function(){
    $('.wpcf7 input[type="url"]').each(function(){
       if($(this).val())
       {
            $(this).val( $(this).val().replace('default:current_url', window.location.href));
       } 
    });
    $(document).on('click','.ctf_click_open_contactform7',function(){
       var id=$(this).attr('data-id');
       $('#ctf-popup-wapper-'+id).addClass('show'); 
       if(!$(this).hasClass('addlogded'))
       {
            ajaxAddLoger($('#ctf-popup-wapper-'+id).find('.wpcf7').attr('data-id'));
            $(this).addClass('addlogded');
       }
    });
    $(document).on('click','.wpcf7.hook',function(){
           var id=$(this).attr('data-id');
           $('#ctf-popup-wapper-'+id).addClass('show'); 
           if(!$(this).hasClass('addlogded'))
           {
                ajaxAddLoger(id);
                $(this).addClass('addlogded');
           }
    });
    $(document).on('click','.ctf_close_popup',function(){
       $(this).closest('.ctf-popup-wapper').removeClass('show'); 
    });
    $(document).mouseup(function (e)
    {
        var container = $('.ctf-popup-content');
        var datepicker=$('.ui-datepicker');
        if (!container.is(e.target) && container.has(e.target).length === 0 &&!datepicker.is(e.target) && datepicker.has(e.target).length === 0)
        {
            $('.ctf-popup-wapper').removeClass('show');
        }
    });
    $(document).keyup(function(e) {
         if (e.keyCode == 27) { // escape key maps to keycode `27`
            $('.ctf-popup-wapper').removeClass('show');
        }
    });
    if ($(".wpcf7 .datetimepicker").length > 0) {
		$(".wpcf7 .datetimepicker").datetimepicker({
			prevText: '',
			nextText: '',
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			currentText: 'Now',
			closeText: 'Done',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			formatTime :'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: 'Choose Time',
			timeText: 'Time',
			hourText: 'Hour',
			minuteText: 'Minute',
        });
	}
    if ($(".wpcf7 .datepicker").length > 0) {
		$(".wpcf7 .datepicker").datepicker({
			prevText: '',
			nextText: '',
			changeMonth: true,
			changeYear: true,
			dateFormat: 'yy-mm-dd',
			timeFormat: 'hh:mm:ss',
		});
	}
    $('.pa-captcha-refesh').click(function(){
        refeshImage($(this));
        return false;
    });
    if($('input[type="range"]').length)
    {
        $('input[type="range"]').each(function(){
            if($(this).prev('.rang-value').length>0)
                $(this).prev('.rang-value').html($(this).val()); 
        });
    }
    $('input[type="range"]').change(function(){
        if($(this).prev('.rang-value').length>0)
            $(this).prev('.rang-value').html($(this).val()); 
    });
    
    
    //Init MCE
    if(typeof tinyMCE !== 'undefined' && $('.autoload_rte_ctf7').length)
    {
        tinymce.init({
              selector: '.autoload_rte_ctf7',
              plugins: "align link image media code emoticons",
              browser_spellcheck: true,
              themes: "modern",         
            toolbar1: "emoticons,bold,italic,underline,link,align,bulli,numlist,table,image",   
            convert_urls: false   
        });
    }
    
});
function refeshImage($this)
{
    if($this.prev('.pa-captcha-img-data').length)
    {
        originalCapcha = $this.prev('.pa-captcha-img-data').attr('src');
        originalCode = $this.attr('data-rand');
        newCode = Math.random();
        $this.prev('.pa-captcha-img-data').attr('src', originalCapcha.replace(originalCode,newCode));
        $this.attr('data-rand', newCode);
        $('input[type="captcha"]').val('');
    }
    
}
function ajaxAddLoger(id_contact)
{						
    $.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: link_contact_ets,
		dataType : "json",
		data:'action=addLoger&id_contact='+id_contact,
		success: function(jsonData)
		{
            
        }
	});
}
function ctf_loadCaptcha()  {
        var img = $('.pa-captcha-img-data:not(.loaded)').first();
        if (img.length > 0)
        {
            img.load(function() {
                ctf_refreshCaptcha(img);
                if (img[0].complete) {
                    ctf_loadCaptcha();
                }
            }).filter(function() { return this.complete; }).load();
        }
    }
function ctf_refreshCaptcha (img) {
    if (img.length && !img.hasClass('loaded'))
    {
        var orgLink = img.attr('src');
        var orgCode = img.attr('data-rand');
        var rand = Math.random();
        img.attr('src', orgLink.replace(orgCode, rand));
        img.attr('data-rand', rand);
        if (!img.hasClass('loaded')) {
            img.addClass('loaded');
        }
    }
}
document.addEventListener("DOMContentLoaded", function(event) {
    if ( $('.pa-captcha-img-data').length > 1)
    {
        ctf_loadCaptcha();
    }
});

var ets_ctf7_update = {
	init : function () {
		this.check_maxlength_number();
	},
	check_maxlength_number : function () {
		$(document).on("keyup",'.wpcf7-form-control-wrap input[type="number"],.wpcf7-form-control-wrap input[type="number"]', function (e) {
			var $field = $(this),
				val=this.value; // retrieve the index
			if (val.length > Number($field.attr("maxlength"))) {
				val=val.slice(0, $field.attr('maxlength'));
				$field.val(val);
			}
		});
	}
};

$(document).ready(function () {
	ets_ctf7_update.init();
});
