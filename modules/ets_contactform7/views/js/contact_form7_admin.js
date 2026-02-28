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
function select_v2_v3(){
    if ($('#id_recaptcha_v2').is(':checked')) {
        $('.form_group_contact.google.google2.capv2').show();
        $('.form_group_contact.google.google3.capv3').hide();
    } else {
        $('.form_group_contact.google.google2.capv2').hide();
        $('.form_group_contact.google.google3.capv3').show();
    }
}
$(document).ready(function(){
    $(document).on('change','.paginator_select_limit',function(e){
        $(this).parents('form').submit();
    });
    $(document).on('click','#list-replies li',function(e){
        if($('.content-reply-full .content-message').has(e.target).length === 0)
        {
            if(!$(this).hasClass('opened'))
                $('#list-replies li').removeClass('opened');
            $(this).toggleClass('opened');
        }

   });
    if($('.mailtag.code').length)
    {
        $(document).on('click','.mailtag.code',function(){
            $('.ctf-text-copy').remove();
            if($('input#ctf_select_code').length==0)
                $('body').prepend('<input id="ctf_select_code" value="'+$(this).html()+'" type="text">');
            else
                $('#ctf_select_code').val($(this).html());
            $('#ctf_select_code').select();
            document.execCommand("copy");
            var copy_text = $('<span class="ctf-text-copy">'+Copied_text+'</span>');
            $(this).append(copy_text);
            setTimeout(function() { copy_text.remove(); }, 2000);
            return false;
        });
    }
   $('.date_col input.datepicker').attr('autocomplete','off');
   if($('.ctf7-left-block').length >1)
   {
        var i=1;
        $('.ctf7-left-block').each(function(){
           if(i>1)
               $(this).addClass('hidden');
           i++;
        });
   }
    $(document).on('click','.message-delete',function () {
        var result = confirm(detele_confirm_message);
        if (result) {
            return true;
        }
        return false;
    });
    $('.message_readed_all').click(function(){
        if (this.checked) {
           $('.message_readed').prop('checked', true);
        } else {
            $('.message_readed').prop('checked', false);
        }
        displayBulkAction();
    });
    $(document).on('click','.message_readed',function(){
        displayBulkAction();
    });
    $(document).on('change','input[type="range"]',function(){
        if($(this).prev('.rang-value').length>0)
            $(this).prev('.rang-value').html($(this).val());
    });
    $(document).on('click','.message_special',function(){
        $('body').addClass('formloading');
        special = $(this).attr('data');
        id_contact_message=$(this).val();
        $.ajax({
            url: '',
            data: 'submitSpecialActionMessage='+special+'&id_contact_message='+id_contact_message,
            type: 'post',
            dataType: 'json',
            async: true,
            cache: false,
            success: function(json){
                $('body').removeClass('formloading');
              console.log(json.messages)
                for(var k in json.messages)
                {
                    $('#tr-message-'+k).html(json.messages[k]);
                }
            },
            error: function(xhr, status, error)
            {
                $('body').removeClass('formloading');
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    });
    $(document).on('change','#bulk_action_message',function(){
        $('.alert.alert-success').hide();
        if($('#bulk_action_message').val()=='delete_selected')
        {
            var result = confirm(detele_confirm);
            if(!result)
            {
                $(this).val('');
                return false;
            }

        }
        $('body').addClass('formloading');
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('submitBulkActionMessage', 1);
        $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('body').removeClass('formloading');
                if($('#bulk_action_message').val()=='delete_selected')
                {
                    if(json.url_reload)
                       window.location.href=json.url_reload;
                    else
                        location.reload();
                }
                else
                {
                    for(var k in json.messages)
                    {
                        $('#tr-message-'+k).html(json.messages[k]);
                        $('#tr-message-'+k+' .message_readed').prop('checked', true);
                        if($('#bulk_action_message').val()=='mark_as_read')
                            {
                                $('#tr-message-'+k).removeClass('no-reaed');
                            }
                        else
                           $('#tr-message-'+k).addClass('no-reaed');
                    }
                    $('.count_messages').html(json.count_messages);
                    if(json.count_messages>0)
                        $('.count_messages').removeClass('hide');
                    else
                        $('.count_messages').addClass('hide');
                    displayBulkAction();
                    $('#bulk_action_message').val('');
                }
            },
            error: function(xhr, status, error)
            {
                $('body').removeClass('formloading');
                var err = eval("(" + xhr.responseText + ")");
                alert(err.Message);
            }
        });
    });
    if($('#list-contactform').length)
    {
        var $myContactform = $("#list-contactform");
    	$myContactform.sortable({
    		opacity: 0.6,
            handle: ".dragHandle",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updateContactFormOrdering";
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
                        $('#form-contact').append('<div class="ets_sussecfull_ajax"><span>'+text_update_position+'</span></div>');
                        setTimeout(function(){
                        $('.ets_sussecfull_ajax').remove();
                        }, 1500);
                        var i=1;
                        $('.dragGroup span').each(function(){
                            $(this).html(i+(jsonData.page-1)*20);
                            i++;
                        });

                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }

    if($('input[name="current_tab"]').val())
    {
        $('.ets_form_tab_header span').removeClass('active');
        $('.ets_form_tab_header span[data-tab="'+$('input[name="current_tab"]').val()+'"]').addClass('active');
    }
    $('#title_'+ets_ctf_default_lang).change(function(){
        if(!ets_ctf_is_updating)
        {
            $('#title_alias_'+ets_ctf_default_lang).val(str2url($(this).val(), 'UTF-8'));
        }
        else
        if($('#title_alias_'+ets_ctf_default_lang).val() == '')
            $('#title_alias_'+ets_ctf_default_lang).val(str2url($(this).val(), 'UTF-8'));
    });

    $(document).on('change','.ets_ctf_tab.source_code textarea',function(){
        $(this).removeAttr('is_load');
    });

    $(document).on('click','.ets_ctf_tab_source li',function(){
        if(!$(this).hasClass('active'))
        {
            $('.ets_ctf_tab_source li').removeClass('active');
            $(this).addClass('active');
            if($(this).attr('data-id')=='preview')
            {
                var id_lang = 0;
                if($('.translatable-field').length >0){

                    if ( typeof is_15 !== "undefined" && is_15){
                        $texteara = $('.translatable-field >div:not(:hidden) textarea.wpcf7-form' );
                        var id_lang = $texteara.parent('div').attr('class').split('_')[1];
                    }else{
                        $texteara = $('.translatable-field:not(:hidden) textarea.wpcf7-form' );
                        var id_lang = $texteara.attr('id').split('_')[1];
                    }
                }
                else
                    $texteara = $('textarea.wpcf7-form');

                if ( $texteara.attr('is_load') ){
                    $('.ets_ctf_tab').removeClass('active');
                    $('.ets_ctf_tab.preview').addClass('active');
                    $('.ets_ctf_tab.preview .prevew_'+id_lang).addClass('active');
                    return false;
                }
                $('body').addClass('formloading');
                $texteara.attr('is_load',true);

                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:{
        			 'getFormElementAjax':1,
                     'short_code':$texteara.val()
                    },
        			success: function(jsonData)
        			{
        		          if($('.ets_ctf_tab_source li.active').attr('data-id')=='preview')
                          {
                               if ($('.ets_ctf_tab.preview .prevew_'+id_lang).length >0 ){
                                   $('.ets_ctf_tab.preview .prevew_'+id_lang).html( jsonData.form_html).addClass('active');
                               }else{
                                   $('.ets_ctf_tab.preview').append( (id_lang =0 ? '':'<div class="active prevew_'+id_lang+'">')+jsonData.form_html+(id_lang=0?'':'</div>'));
                               }

                               $texteara.removeAttr('change_text');
                               $('.ets_ctf_tab').removeClass('active');
                               $('.ets_ctf_tab.preview').addClass('active');
                               if($('input[type="range"]').length)
                               {
                                    $('input[type="range"]').each(function(){
                                        if($(this).prev('.rang-value').length>0)
                                            $(this).prev('.rang-value').html($(this).val());
                                    });
                               }
                               if ($(".ets_ctf_tab .datepicker").length > 0) {
                                    $(".ets_ctf_tab .datepicker").datepicker({
                                        prevText: '',
                                        nextText: '',
                                        dateFormat: 'yy-mm-dd',
                                    });
                               }
                               if($('.autoload_rte_ctf7').length && typeof tinyMCE !== 'undefined' && tinyMCE.editors.length > 0)
                               {
                                    tinySetup({
                        				editor_selector :"autoload_rte_ctf7"
                        			});
                               }
                          }
                          $('body').removeClass('formloading');
                    }
        		});
            }
            else
            {
                $('.ets_ctf_tab.preview .active').removeClass('active');
                $('.ets_ctf_tab').removeClass('active');
                $('.ets_ctf_tab.'+$(this).attr('data-id')).addClass('active');
            }
        }
    });
    $(document).on('click','.ctf_view_message',function(){
        $('body').addClass('formloading');
        message_readed = $(this).closest('tr').find('.message_readed').attr('data');
        $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: $(this).attr('href'),
			async: true,
			cache: false,
			dataType : "json",
            data:'ajax_ets=1&message_readed='+message_readed,
			success: function(jsonData)
			{
	           $('.ctf-popup-wapper-admin #form-message-preview').html(jsonData.message_html);
               $('.ctf-popup-wapper-admin').addClass('show');
               if(message_readed==0)
               {
                    for(var k in jsonData.messages)
                    {
                        $('#tr-message-'+k).html(jsonData.messages[k]);
                        $('#tr-message-'+k).removeClass('no-reaed');
                    }
                    $('.count_messages').html(jsonData.count_messages);
                    if(jsonData.count_messages>0)
                        $('.count_messages').removeClass('hide');
                    else
                        $('.count_messages').addClass('hide');
                    displayBulkAction();
                }
               $('body').removeClass('formloading');
			}
		});
        return false;
    });
    $(document).on('click','.ctf-short-code',function(){
         $(this).select();
         document.execCommand("copy");
         $(this).next().addClass('copied');
         setTimeout(function() { $('.copied').removeClass('copied'); }, 2000);
    });
    $(document).on('click','.action-reply-message',function(){
        $('.view-message').hide();
        $('#module_form_reply-message').show();
        $('.view-message .success').hide();
        $('textarea[name="message_reply"]').focus();
    });
    $(document).on('click','button[name="backReplyMessage"]',function(){
        $('.view-message').show();
        $('#module_form_reply-message').hide();
        $('.view-message .success').hide();
    });
    $(document).on('click','button[name="submitReplyMessage"]',function(){
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading');
            $('.module_error').parent().remove();
            $('.view-message .success').hide();
            var formData = new FormData($(this).parents('form').get(0));
            formData.append('submitReplyMessage', 1);
            $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $('button[name="submitReplyMessage"]').removeClass('loading');
                    if(json.error)
                    {
                        $('#module_form_reply-message .form-wrapper').append(json.error);
                    }
                    else
                    {
                        $('.view-message').show();
                        $('#module_form_reply-message').hide();
                        $('ul#list-replies').append(json.reply);
                        $('tr#tr-message-'+json.id_message+' td.replies').html('<i class="material-icons action-enabled">check</i>');
                        $('.view-message .success').show();
                        $('textarea[name="message_reply"]').val('');
                        $('input[name="attachment"]').val('');
                        $("#attachment-name").val('');
                    }
                },
                error: function(xhr, status, error)
                {
                    $('button[name="submitReplyMessage"]').removeClass('loading');
                    var err = eval("(" + xhr.responseText + ")");
                    alert(err.Message);
                }
            });
        }

       return false;
    });
   $('.form-group.form_group_contact').hide();
   $('.form-group.form_group_contact.'+$('.ets_form_tab_header .active').attr('data-tab')).show();
   if($('.ets_form_tab_header .active').attr('data-tab')=='mail')
   {
        if($('input[name="use_email2"]:checked').val()==1)
            $('.form-group.form_group_contact.mail2').show();
        else
            $('.form-group.form_group_contact.mail2').hide();
   }
   if($('.ets_form_tab_header .active').attr('data-tab')=='general_settings')
   {
        if($('input[name="open_form_by_button"]:checked').val()==1)
            $('.form-group.form_group_contact.general_settings2').show();
        else
            $('.form-group.form_group_contact.general_settings2').hide();
        if($('input[name="save_message"]:checked').val()==1)
            $('.form-group.form_group_contact.general_settings4').show();
        else
            $('.form-group.form_group_contact.general_settings4').hide();
   }
   if($('.ets_form_tab_header .active').attr('data-tab')=='google')
   {
       select_v2_v3()
   }
    if($('input[name="ETS_CTF7_ENABLE_TEAMPLATE"]:checked').val()==1)
        $('.form-group.form_group_contact.template2').show();
    else
        $('.form-group.form_group_contact.template2').hide();
   $(document).on('submit','#form-contact-preview form',function(){
        return false;
   });
   $(document).on('click','.wpcf7-submit',function(){
        return false;
   });
   $(document).on('click','.ctf_close_popup',function(){
        $(this).closest('.ctf-popup-wapper-admin').removeClass('show');
   });
   $(document).on('click','.preview-contact',function(){
        $('body').addClass('formloading');
        $(this).closest('.btn-group').removeClass('open');
        $.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: $(this).attr('href'),
			async: true,
			cache: false,
			dataType : "json",
			data:'',
			success: function(jsonData)
			{
                $('.ctf-popup-wapper-admin').addClass('show');
                $('.ctf-popup-wapper-admin #form-contact-preview').html(jsonData.form_html);
                $('body').removeClass('formloading');
            }
		});
        return false;
   });
   $(document).on('click','.ets_form_tab_header span',function(){
        if(!$(this).hasClass('active'))
        {
            $('.form-group.form_group_contact').hide();
            $('.ets_form_tab_header span').removeClass('active');
            $(this).addClass('active');
            if($(this).attr('data-tab')=='export_import'){
                $('button[name="btnSubmit"]').hide();
            }
            else
                $('button[name="btnSubmit"]').show();
            $('.form-group.form_group_contact.'+$('.ets_form_tab_header .active').attr('data-tab')).show();
            $('input[name="current_tab"]').val($(this).attr('data-tab'));
            if($('.ets_form_tab_header .active').attr('data-tab')=='mail')
            {
                if($('input[name="use_email2"]:checked').val()==1)
                    $('.form-group.form_group_contact.mail2').show();
                else
                    $('.form-group.form_group_contact.mail2').hide();
            }
            if($('.ets_form_tab_header .active').attr('data-tab')=='seo')
            {
                if($('input[name="enable_form_page"]:checked').val()==1)
                    $('.form-group.form_group_contact.seo3').show();
                else
                    $('.form-group.form_group_contact.seo3').hide();
            }
            if($('.ets_form_tab_header .active').attr('data-tab')=='general_settings')
            {
                if($('input[name="open_form_by_button"]:checked').val()==1)
                    $('.form-group.form_group_contact.general_settings2').show();
                else
                    $('.form-group.form_group_contact.general_settings2').hide();
                if($('input[name="save_message"]:checked').val()==1)
                    $('.form-group.form_group_contact.general_settings4').show();
                else
                    $('.form-group.form_group_contact.general_settings4').hide();
            }
            if($('.ets_form_tab_header .active').attr('data-tab')=='google')
            {
                select_v2_v3();
            }
            if($('.ets_form_tab_header .active').attr('data-tab')=='template')
            {
                if($('input[name="ETS_CTF7_ENABLE_TEAMPLATE"]:checked').val()==1)
                    $('.form-group.form_group_contact.template2').show();
                else
                    $('.form-group.form_group_contact.template2').hide();
            }
            if($('.ets_form_tab_header .active').attr('data-tab')=='chart')
            {
                createChart();
            }
            if ($(this).attr('data-tab') == 'thank_you'){
                handle_switch_thank_page();
            }
        }
   });
   $(document).on('click','input[name="use_email2"]',function(){
        if($('input[name="use_email2"]:checked').val()==1)
            $('.form-group.form_group_contact.mail2').show();
        else
            $('.form-group.form_group_contact.mail2').hide();
   });
   $(document).on('click','input[name="open_form_by_button"]',function(){
        if($('input[name="open_form_by_button"]:checked').val()==1)
            $('.form-group.form_group_contact.general_settings2').show();
        else
            $('.form-group.form_group_contact.general_settings2').hide();
   });
   $(document).on('click','input[name="ETS_CTF7_ENABLE_TEAMPLATE"]',function(){
        if($('input[name="ETS_CTF7_ENABLE_TEAMPLATE"]:checked').val()==1)
            $('.form-group.template2').show();
        else
            $('.form-group.template2').hide();
   });
   $(document).on('click','input[name="enable_form_page"]',function(){
        if($('input[name="enable_form_page"]:checked').val()==1)
            $('.form-group.form_group_contact.seo3').show();
        else
            $('.form-group.form_group_contact.seo3').hide();
   });
   $(document).on('click','input[name="save_message"]',function(){
        if($('input[name="save_message"]:checked').val()==1)
            $('.form-group.form_group_contact.general_settings4').show();
        else
            $('.form-group.form_group_contact.general_settings4').hide();
   });
   $(document).on('click','#tag-generator-list .thickbox',function(e){
        $('body').append('<div id="TB_overlay" class="TB_overlayBG"></div>');
        var $html_content='<div id="TB_window" class="thickbox-loading" style="margin-left: -315px; width: 630px; margin-top: -270px; visibility: visible;">';
            $html_content +='<div id="TB_title">';
                $html_content +='<div id="TB_ajaxWindowTitle">Form-tag Generator: '+$(this).html()+'</div>';
                $html_content +='<div id="TB_closeAjaxWindow">';
                    $html_content +='<button id="TB_closeWindowButton" type="button"><span class="screen-reader-text">Close</span><span class="tb-close-icon"></span></button>';
                $html_content +='</div>';
            $html_content +='</div>';
            $html_content +='<div id="TB_ajaxContent" style="width:600px;height:495px">';
                $html_content += $($(this).attr('href')).html();
            $html_content +='</div>';
        $html_content +='</div>'
        $('body').append($html_content);
        var $form=$('#TB_ajaxContent form');
        ctf_update( $form );
        $($(this).attr('href')).html('');
   });
   $(document).on('click','#TB_closeWindowButton',function(e){
        tb_remove();
   });
   $(document).on('click','form.tag-generator-panel .control-box :input',function(e){
        var $form = $( this ).closest( 'form.tag-generator-panel' );
		ctf_normalize( $( this ) );
		ctf_update( $form );
   });
   $(document).on('change','form.tag-generator-panel .control-box :input',function(e){
		var $form = $( this ).closest( 'form.tag-generator-panel' );
		ctf_normalize( $( this ) );
		ctf_update( $form );
    });
    $(document).on('click','input.insert-tag',function(e){
        var $form = $( this ).closest( 'form.tag-generator-panel' );
    	var tag = $form.find( 'input.tag' ).val();
    	ctf_insert( tag );
    	tb_remove(); // close thickbox
    	return false;
    });
    $(document).mouseup(function (e)
    {
        var container = $("#TB_ajaxContent,#TB_title");
        var colorpanel = $('#mColorPicker');
        if (!container.is(e.target)
            && container.has(e.target).length === 0 && !colorpanel.is(e.target) && colorpanel.has(e.target).length === 0
            && ($('#mColorPicker').length <=0 || ($('#mColorPicker').length > 0 && $('#mColorPicker').css('display')=='none'))
        )
        {
            tb_remove();
        }

    });
    $(document).keyup(function(e) {
         if (e.keyCode == 27) {
            $('.ctf-popup-wapper-admin').removeClass('show');
            tb_remove();
        }
    });
});
function ctf_insert(content)
{
    if($('.translatable-field').length >0)
        $texteara = $('.translatable-field:not(:hidden) textarea.wpcf7-form' );
    else
        $texteara = $('textarea.wpcf7-form');
    $texteara.each( function() {
        this.focus();
        if ( document.selection ) { // IE
            var selection = document.selection.createRange();
            selection.text = content;
        } else if ( this.selectionEnd || 0 === this.selectionEnd ) {
            var val = $( this ).val();
            var end = this.selectionEnd;
            $( this ).val( val.substring( 0, end ) +
                content + val.substring( end, val.length ) );
            this.selectionStart = end + content.length;
            this.selectionEnd = end + content.length;
        } else {
            $( this ).val( $( this ).val() + content );
        }
        this.focus();
    });
}
function ctf_normalize($input)
{
    var val = $input.val();
	if ( $input.is( 'input[name="name"]' ) ) {
		val = val.replace( /[^0-9a-zA-Z:._-]/g, '' ).replace( /^[^a-zA-Z]+/, '' );
	}

	if ( $input.is( '.numeric' ) ) {
		val = val.replace( /[^0-9.-]/g, '' );
	}

	if ( $input.is( '.idvalue' ) ) {
		val = val.replace( /[^-0-9a-zA-Z_]/g, '' );
	}

	if ( $input.is( '.classvalue' ) ) {
		val = $.map( val.split( ' ' ), function( n ) {
			return n.replace( /[^-0-9a-zA-Z_]/g, '' );
		} ).join( ' ' );

		val = $.trim( val.replace( /\s+/g, ' ' ) );
	}

	if ( $input.is( '.color' ) ) {
		val = val.replace( /[^0-9a-fA-F]/g, '' );
	}

	if ( $input.is( '.filesize' ) ) {
		val = val.replace( /[^0-9kKmMbB]/g, '' );
	}

	if ( $input.is( '.filetype' ) ) {
		val = val.replace( /[^0-9a-zA-Z.,|\s]/g, '' );
	}

	if ( $input.is( '.date' ) ) {
		// 'yyyy-mm-dd' ISO 8601 format
		if ( ! val.match( /^\d{4}-\d{2}-\d{2}$/ ) ) {
			val = '';
		}
	}

	if ( $input.is( ':input[name="values"]' ) ) {
		val = $.trim( val );
	}

	$input.val( val );

	if ( $input.is( ':checkbox.exclusive' ) ) {
		ctf_exclusiveCheckbox( $input );
	}
}
function ctf_exclusiveCheckbox($cb)
{
    if ( $cb.is( ':checked' ) ) {
			$cb.siblings( ':checkbox.exclusive' ).prop( 'checked', false );
		}
}
function ctf_update($form)
{
    var id = $form.attr( 'data-id' );
		var name = '';
		var name_fields = $form.find( 'input[name="name"]' );
		if ( name_fields.length ) {
			name = name_fields.val();
			if ( '' === name ) {
				name = id + '-' + Math.floor( Math.random() * 1000 );
				name_fields.val( name );
			}
		}
        //return ctf_update[ id ].call( this, $form );

		$form.find( 'input.tag' ).each( function() {
			var tag_type = $( this ).attr( 'name' );

			if ( $form.find( ':input[name="tagtype"]' ).length ) {
				tag_type = $form.find( ':input[name="tagtype"]' ).val();
			}

			if ( $form.find( ':input[name="required"]' ).is( ':checked' ) ) {
				tag_type += '*';
			}

			var components = ctf_compose( tag_type, $form );
			$( this ).val( components );
		} );

		$form.find( 'span.mail-tag' ).text( '[' + name + ']' );

		$form.find( 'input.mail-tag' ).each( function() {
			$( this ).val( '[' + name + ']' );
		} );
}
function ctf_compose( tagType, $form ) {
    var name = $form.find( 'input[name="name"]' ).val();
    var scope = $form.find( '.scope.' + tagType );

    if ( ! scope.length ) {
    	scope = $form;
    }

    var options = [];

    scope.find( 'input.option' ).not( ':checkbox,:radio' ).each( function( i ) {
    	var val = $( this ).val();

    	if ( ! val ) {
    		return;
    	}

    	if ( $( this ).hasClass( 'filetype' ) ) {
    		val = val.split( /[,|\s]+/ ).join( '|' );
    	}

    	if ( $( this ).hasClass( 'color' ) ) {
    		val = '#' + val;
    	}

    	if ( 'class' == $( this ).attr( 'name' ) ) {
    		$.each( val.split( ' ' ), function( i, n ) {
    			options.push( 'class:' + n );
    		} );
    	} else {
    		options.push( $( this ).attr( 'name' ) + ':' + val );
    	}
    } );

    scope.find( 'input:checkbox.option' ).each( function( i ) {
    	if ( $( this ).is( ':checked' ) ) {
    		options.push( $( this ).attr( 'name' ) );
    	}
    } );

    scope.find( 'input:radio.option' ).each( function( i ) {
    	if ( $( this ).is( ':checked' ) && ! $( this ).hasClass( 'default' ) ) {
    		options.push( $( this ).attr( 'name' ) + ':' + $( this ).val() );
    	}
    } );

    if ( 'radio' == tagType ) {
    	options.push( 'default:1' );
    }

    options = ( options.length > 0 ) ? options.join( ' ' ) : '';

    var value = '';

    if ( scope.find( ':input[name="values"]' ).val() ) {
    	$.each(
    		scope.find( ':input[name="values"]' ).val().split( "\n" ),
    		function( i, n ) {
    			value += ' "' + n.replace( /["]/g, '&quot;' ) + '"';
    		}
    	);
    }

    var components = [];

    $.each( [ tagType, name, options, value ], function( i, v ) {
    	v = $.trim( v );

    	if ( '' != v ) {
    		components.push( v );
    	}
    } );

    components = $.trim( components.join( ' ' ) );
    components = '[' + components + ']';

    var content = scope.find( ':input[name="content"]' ).val();
    content = $.trim( content );

    if ( content ) {
    	components += ' ' + content + ' [/' + tagType + ']';
    }
    return components;
}
function tb_remove()
{
    var content=$('#TB_ajaxContent').html();
    var id_content=$('#TB_ajaxContent .tag-generator-panel').attr('data-id');
    $('#tag-generator-panel-'+id_content).html(content);
    $('#TB_overlay').remove();
    $('#TB_window').remove();
}
function displayBulkAction()
{
    if($('.message_readed:checked').length )
    {
        $('#bulk_action_message').show();
    }
    else
    {
        $('#bulk_action_message').hide();
    }
    if($('.message_readed:checked').length==$('.message_readed[data="1"]:checked').length)
        $('#bulk_action_message option[value="mark_as_read"]').hide();
    else
        $('#bulk_action_message option[value="mark_as_read"]').show();
    if($('.message_readed:checked').length==$('.message_readed[data="0"]:checked').length)
        $('#bulk_action_message option[value="mark_as_unread"]').hide();
    else
        $('#bulk_action_message option[value="mark_as_unread"]').show();
}

function handle_switch_thank_page(){
    if ($('.thank_you_active').is(':hidden')){
        return;
    }
    if ($.trim($('#thank_you_page').children("option:selected").val()) === 'thank_page_default') {
        $('.form_group_contact.thank_you_message').show();
        $('.form_group_contact.thank_you_url').hide();
    } else {
        $('.form_group_contact.thank_you_message').hide();
        $('.form_group_contact.thank_you_url').show();
    }
}
$(document).on('change','.title_tk_page',function () {
    var name = $(this).attr('name'),
        obj =  name.split('_'),
        id_lang = obj[obj.length-1];
    if (!ets_ctf_is_updating) {
        $('#thank_you_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
    } else if ($('#thank_you_alias_' + id_lang).val() == ''){
        $('#thank_you_alias_' + id_lang).val(str2url($(this).val(), 'UTF-8'));
    }

});



$(document).ready(function () {
    if ($('#thank_you_page').length > 0){
        handle_switch_thank_page();
    }
    $(document).on('change',$('#thank_you_page'),function () {
        handle_switch_thank_page();
    });
    //select_v2_v3();
    $(document).on('change', 'input[name="ETS_CTF7_RECAPTCHA_TYPE"]', function () {
        select_v2_v3();
    });

});
