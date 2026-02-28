/** ROYTHEMES Customizer. Another Theme. Script. */
$(document).ready(function() {
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        mode: "css",
        lineNumbers: true,
        theme: "zenburn"
    });

    /** Hex to rgb */
	function hexToRgb(hex) {
	    var bigint = parseInt(hex, 16);
	    var r = (bigint >> 16) & 255;
	    var g = (bigint >> 8) & 255;
	    var b = bigint & 255;

	    return [r, g, b].join();
	}

	/**
	 * jQuery Cookie plugin
	 *
	 * Copyright (c) 2010 Klaus Hartl (stilbuero.de)
	 * Dual licensed under the MIT and GPL licenses:
	 * http://www.opensource.org/licenses/mit-license.php
	 * http://www.gnu.org/licenses/gpl.html
	 *
	 */
	jQuery.cookie = function (key, value, options) {

	    // key and at least value given, set cookie...
	    if (arguments.length > 1 && String(value) !== "[object Object]") {
	        options = jQuery.extend({}, options);

	        if (value === null || value === undefined) {
	            options.expires = -1;
	        }

	        if (typeof options.expires === 'number') {
	            var days = options.expires, t = options.expires = new Date();
	            t.setDate(t.getDate() + days);
	        }

	        value = String(value);

	        return (document.cookie = [
	            encodeURIComponent(key), '=',
	            options.raw ? value : encodeURIComponent(value),
	            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
	            options.path ? '; path=' + options.path : '',
	            options.domain ? '; domain=' + options.domain : '',
	            options.secure ? '; secure' : ''
	        ].join(''));
	    }

	    // key and possibly options given, get cookie...
	    options = value || {};
	    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
	    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
	};

    /** Show google fonts */
	var googlefont = $("option:selected", "#f_headings").val();
	var googlefontname = googlefont.split(';');
	if ($('head').find('link#headingsflink').length < 1){
		$('head').append('<link id="headingsflink" rel="stylesheet" type="text/css" href="" />');
	}
	$('link#headingsflink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

	$("style#headingsrfstyle").remove();
	var url = $(this).attr('href');

	$('head').append('<style id="headingsrfstyle" type="text/css">#headingexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');


	var googlefont = $("option:selected", '#f_buttons').val();
	var googlefontname = googlefont.split(';');
	if ($('head').find('link#f_buttonslink').length < 1){
		$('head').append('<link id="f_buttonslink" rel="stylesheet" type="text/css" href="" />');
	}
	$('link#f_buttonslink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

	$("style#f_buttonsstyle").remove();
	$('head').append('<style id="f_buttonsstyle" type="text/css">#buttonsexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');


	var googlefont = $("option:selected", '#f_text').val();
	var googlefontname = googlefont.split(';');
	if ($('head').find('link#f_textlink').length < 1){
		$('head').append('<link id="f_textlink" rel="stylesheet" type="text/css" href="" />');
	}
	$('link#f_textlink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

	$("style#f_textstyle").remove();
	$('head').append('<style id="f_textstyle" type="text/css">#textexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');


	var googlefont = $("option:selected", '#f_price').val();
	var googlefontname = googlefont.split(';');
	if ($('head').find('link#fpricelink').length < 1){
		$('head').append('<link id="fpricelink" rel="stylesheet" type="text/css" href="" />');
	}
	$('link#fpricelink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

	$("style#fpricestyle").remove();
	$('head').append('<style id="fpricestyle" type="text/css">#priceexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');


	var googlefont = $("option:selected", '#f_pn').val();
	var googlefontname = googlefont.split(';');
	if ($('head').find('link#fpnlink').length < 1){
		$('head').append('<link id="fpnlink" rel="stylesheet" type="text/css" href="" />');
	}
	$('link#fpnlink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

	$("style#fpnstyle").remove();
	$('head').append('<style id="fnstyle" type="text/css">#pnexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');


	$('#f_headings').change(function(){
	    var googlefont = $("option:selected", this).val();
		var googlefontname = googlefont.split(';');
		if ($('head').find('link#headingsflink').length < 1){
			$('head').append('<link id="headingsflink" rel="stylesheet" type="text/css" href="" />');
		}
		$('link#headingsflink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

		$("style#headingsrfstyle").remove();
		$('head').append('<style id="headingsrfstyle" type="text/css">#headingexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');
	});

	$('#f_buttons').change(function(){
	    var googlefont = $("option:selected", this).val();
		var googlefontname = googlefont.split(';');
		if ($('head').find('link#f_buttonslink').length < 1){
			$('head').append('<link id="f_buttonslink" rel="stylesheet" type="text/css" href="" />');
		}
		$('link#f_buttonslink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

		$("style#f_buttonsstyle").remove();
		$('head').append('<style id="f_buttonsstyle" type="text/css">#buttonsexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');
	});

	$('#f_text').change(function(){
	    var googlefont = $("option:selected", this).val();
		var googlefontname = googlefont.split(';');
		if ($('head').find('link#f_textlink').length < 1){
			$('head').append('<link id="f_textlink" rel="stylesheet" type="text/css" href="" />');
		}
		$('link#f_textlink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

		$("style#f_textstyle").remove();
		$('head').append('<style id="f_textstyle" type="text/css">#textexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');
	});

	$('#f_pn').change(function(){
	    var googlefont = $("option:selected", this).val();
		var googlefontname = googlefont.split(';');
		if ($('head').find('link#fpnlink').length < 1){
			$('head').append('<link id="fpnlink" rel="stylesheet" type="text/css" href="" />');
		}
		$('link#fpnlink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

		$("style#fpnstyle").remove();
		$('head').append('<style id="fpnstyle" type="text/css">#pnexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');
	});

	$('#f_price').change(function(){
	    var googlefont = $("option:selected", this).val();
		var googlefontname = googlefont.split(';');
		if ($('head').find('link#fpricelink').length < 1){
			$('head').append('<link id="fpricelink" rel="stylesheet" type="text/css" href="" />');
		}
		$('link#fpricelink').attr({href:'//fonts.googleapis.com/css?family=' + googlefontname});

		$("style#fpricestyle").remove();
		$('head').append('<style id="fpricestyle" type="text/css">#priceexample{ font-family:' + googlefont.split(':')[0] + ' !important; }</style>');
	});


});
