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
var xhr_gpt = false;
function ybc_reset_position()
{
    var chatgpt_box = $('.ybc-chatgpt-box');
    console.log(chatgpt_box.outerHeight(),$(window).height());
    if(chatgpt_box.outerHeight() + 100 > $(window).height())
    {
        chatgpt_box.css('top','30px');
    }
    $('.ybc-chatgpt-box.resize').resizable();
    if($('.ybc-chatgpt-box .form-wrapper .chatgpt-message').length)
        $('.ybc-chatgpt-box .form-wrapper').animate({scrollTop: $('.ybc-chatgpt-box .form-wrapper').scrollTop() + $('.ybc-chatgpt-box .form-wrapper .chatgpt-message:last-child').position().top+30});
}
function ybc_chatgptSendMessage()
{
    if($('.chatgpt-loading').length==0)
    {
        var input_content = $('textarea[name="message-chatppt"]').val().trim();
        var input_name = $('input[name="input_content_name"]').val();
        if(!input_content)
            alert(Message_is_required_text);
        if(input_content)
        {
            cancel_gpt = false;
            if(xhr_gpt)
                xhr_gpt.abort();
            $('#chatgpt-history-list').append('<li class="chatgpt-message is_customer"><div class="chatgpt-content"><i class="svg_icon" title="'+You_chatgpt_text+'"><svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 0q182 0 348 71t286 191 191 286 71 348q0 181-70.5 347t-190.5 286-286 191.5-349 71.5-349-71-285.5-191.5-190.5-286-71-347.5 71-348 191-286 286-191 348-71zm619 1351q149-205 149-455 0-156-61-298t-164-245-245-164-298-61-298 61-245 164-164 245-61 298q0 250 149 455 66-327 306-327 131 128 313 128t313-128q240 0 306 327zm-235-647q0-159-112.5-271.5t-271.5-112.5-271.5 112.5-112.5 271.5 112.5 271.5 271.5 112.5 271.5-112.5 112.5-271.5z"></path></svg></i><p class="chatgpt-content">'+input_content+'</p></div></li>');
            var idChatgpt = Math.round(Math.random()*1000000000);
            $('#chatgpt-history-list').append('<li id="chatgpt-message-'+idChatgpt+'" class="chatgpt-message is_chatgpt"><p class="chatgpt-content chatgpt-loading"></p></li>');
            $('textarea[name="message-chatppt"]').val('');
            ybc_reset_position();
            xhr_gpt = $.ajax({
                url: '',
                data: {
                    gpt_content: input_content,
                    chatGPT:1,
                    input_content_name:input_name,
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.error){
                        $('#chatgpt-message-'+idChatgpt).html('<div class="chatgpt-error">'+json.error+'</div>');
                    }
                    else
                    {
                        if(json.success)
                        {
                            $('#chatgpt-message-'+idChatgpt).replaceWith(json.message);
                        }
                        else
                            $('#chatgpt-message-'+idChatgpt).html('<div class="chatgpt-error">'+ChatGPT_API_request_error_text+'</div>');
                    }
                    $('.btn-clear-all-message').show();
                    ybc_reset_position();
                },
                error: function(error)
                {
                    if(!cancel_gpt)
                        $('#chatgpt-message-'+idChatgpt).html('<div class="chatgpt-error">'+ChatGPT_API_request_error_text+'</div>');
                    else
                        $('#chatgpt-message-'+idChatgpt).remove();
                }
            });
        }
    }
}
function refreshHeightBoxChatGPT() {
    if ( $('.box-actions').length > 0){
        var textbox_height = $('.chatgpt-box-send').outerHeight() + $('.ybc-chatgpt-box .panel-heading').outerHeight() + 45 + $('.box-actions').outerHeight();
    } else {
        var textbox_height = $('.chatgpt-box-send').outerHeight() + $('.ybc-chatgpt-box .panel-heading').outerHeight() + 45;
    }
    $('.ybc-chatgpt-box > .form-wrapper').css('max-height','calc(100% - '+ textbox_height +'px)');
    if ( $('#chatgpt-history-list li').length > 0 ){
        $('.ybc-chatgpt-box').css('min-height','calc('+ textbox_height +'px + 140px)');
    } else {
        $('.ybc-chatgpt-box').css('min-height','calc('+ textbox_height +'px + 40px)');
    }
}
$(document).ready(function() {
    $(document).on('click','.btn-clear-all-message',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading') && confirm(confirm_clear_all_mesasage_chatgpt))
        {
            $(this).addClass('loading');
            var $this = $(this);
            $.ajax({
                url: '',
                data: {
                    clear_all_message:1,
                    chatGPT:1,
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $this.removeClass('loading');
                    $this.hide();
                    $('.ybc-chatgpt-box').css('height','auto');
                    if(json.error){
                        showErrorMessage(json.error);
                    }
                    else
                    {
                        if(json.success)
                        {
                            showSuccessMessage(json.success);
                            $('#chatgpt-history-list').html('');
                        }
                    }
                },
                error: function(error)
                {
                    $this.removeClass('loading');
                }
            });
        }
    });
    $(document).on('click','.maximize-chat-gpt',function(e){
        $('.ybc-chatgpt-box').addClass('maximize');
        $('.ybc-chatgpt-box').removeClass('minimize');
        $('body').addClass('no_scroll');
    });
    $(document).on('click','.minimize-chat-gpt',function(e){
        $('.ybc-chatgpt-box').addClass('minimize');
        $('.ybc-chatgpt-box').removeClass('maximize');
        $('body').removeClass('no_scroll');
    });
    if($('.ybc-chatgpt-box').length)
    {
        var click = {
            x: 0,
            y: 0
        };
        $(".ybc-chatgpt-box").draggable({
            cursor: "grabbing",
            connectToSortable: "body",
            containment: "body",
            handle: ".panel-heading",
            scroll: false,
            start: function( event, ui ) {
                click.x = event.clientX;
                click.y = event.clientY;
            },
            drag: function(event, ui) {
                var original = ui.originalPosition;
                var left = event.clientX - click.x + original.left;
                var top=event. clientY - click.y + original.top;
                var max_left = $(window).width()- $('.ybc-chatgpt-box').outerWidth();
                var max_top = $(window).height()-$('.ybc-chatgpt-box').outerHeight();
                if(left>max_left)
                    left=max_left;
                if(top>max_top)
                    top=max_top;
                ui.position = {
                    left: left >0 ? left :0,
                    top:  top >0 ? top :0,
                };
            },
            stop: function(event,ui){
                var original = ui.originalPosition;
                var left = event.clientX - click.x + original.left;
                var top=event. clientY - click.y + original.top;
                var max_left = $(window).width()-$('.ybc-chatgpt-box').outerWidth();
                var max_top = $(window).height()-$('.ybc-chatgpt-box').outerHeight();
                if(left>max_left)
                    left=max_left;
                if(top>max_top)
                    top=max_top;
                $( ".ybc-chatgpt-box").attr('data-left',left >0 ? left : 0);
                $( ".ybc-chatgpt-box").attr('data-top',top > 0 ? top :0);
                $( ".ybc-chatgpt-box").css('left',(left> 0 ? left :0)+'px');
                $( ".ybc-chatgpt-box").css('top',(top> 0 ? top :0)+'px');
            }
        });
    }
    $(document).on('click','.gpt-item-template',function(e){
        $('textarea[name="message-chatppt"]').val($(this).data('content'));
    });
    $(document).on('click','.btn-open-chatgpt',function(e){
        e.preventDefault();
        $('input[name="input_content_name"]').val($(this).data('name'));
        $('#container-chatgpt').addClass('show');
        $('.ybc-chatgpt-box').show();
        $('textarea[name="message-chatppt"]').focus();
        refreshHeightBoxChatGPT();
        ybc_reset_position();
    });
    $(document).on('click','.close-chatgpt-box',function(e){
        e.preventDefault();
        $('.ybc-chatgpt-box').hide();
        $('#container-chatgpt').removeClass('show');
        $('body').removeClass('no_scroll');
    });
    var cancel_gpt = false;
    $(document).on('click','.btn-cancel-gpt',function(e){
        e.preventDefault();
        cancel_gpt = true;
        if(xhr_gpt)
            xhr_gpt.abort();
        $('.btn-send-gpt').removeClass('loading');
        $('textarea[name="message-chatppt"]').val('');

    });
    $(document).on('click','.btn-apply-chatgpt',function(e){
        var input_name = $(this).parents('.chatgpt-button-append').find('select[name="content-apply-chatgpt"]').val();
        var input_lang= $('.translatable-field:not(:hidden)').find('[name^='+input_name+']').length ? $('.translatable-field:not(:hidden)').find('[name^='+input_name+']') : $('[name^='+input_name+']');
        var chatgpt_content = $(this).parents('li').find('.message-content').html().trim();
        if(input_lang.hasClass('autoload_rte'))
            tinymce.get(input_lang.attr('id')).setContent(chatgpt_content);
        else
            input_lang.val(chatgpt_content);
        showSuccessMessage(Allplied_successfull_text);
    });
    $(document).on('keypress','textarea[name="message-chatppt"]',function(e){
        if(e.which == 13)
        {
            e.preventDefault();
            ybc_chatgptSendMessage();
        }
    });
    $(document).on('click','.btn-send-gpt',function(e){
        e.preventDefault();
        ybc_chatgptSendMessage();
    });
    $(document).on('click','.close_popup,.cancel_popup',function(e){
        e.preventDefault();
        $('.ybc_blog_popup').removeClass('show');
    });
    $(document).on('click','button[name="saveTemplateGPT"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            $('.module_error.alert-danger').remove();
            $(this).addClass('loading');
            var $button=  $(this);
            var formData = new FormData($(this).parents('form').get(0));
            formData.append('ajax',1);
            $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $button.removeClass('loading');
                    if(json.success)
                    {
                        showSaveMessage(json.success);
                        $('.list-chatgpt').html(json.list);
                        $('.ybc_blog_popup').removeClass('show');
                    }
                    if(json.errors)
                    {
                        $('#ybc_chatgpt_template_form').before(json.errors);
                    }
                },
                error: function(xhr, status, error)
                {
                    $button.removeClass('loading');
                    var err = eval("(" + xhr.responseText + ")");
                    alert(err.Message);
                }
            });
        }
    });
    $(document).on('click','#list-ybc_chatgpt .delete-gpt-template',function(e) {
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            if(confirm($(this).data('confirm')))
            {
                $(this).addClass('loading');
                var $this = $(this);
                $.ajax({
                    url: $this.attr('href'),
                    data: {
                        ajax: 1
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if(json.success)
                        {
                            showSuccessMessage(json.success);
                            $this.closest('tr').remove();
                        }
                    },
                    error: function(xhr, status, error)
                    {
                        $this.removeClass('loading');
                        var err = eval("(" + xhr.responseText + ")");
                        alert(err.Message);
                    }
                });
            }
        }

    });
    $(document).on('click','#list-ybc_chatgpt .edit',function(e){
        var $this = $(this);
        e.preventDefault();
        if(!$this.hasClass('loading'))
        {
            $this.addClass('loading');
            $.ajax({
                url: $this.attr('href'),
                data: {
                    ajax: 1
                },
                type: 'post',
                dataType: 'json',
                success: function (json) {
                    $this.removeClass('loading');
                    if(json.form)
                    {
                        if($('#ybc-blog-form-popup').length==0)
                        {
                            var html ='<div class="ybc_blog_popup show">';
                            html += '<div class="popup_content table">';
                            html +='<div class="popup_content_tablecell">';
                            html +='<div class="popup_content_wrap" style="position: relative">';
                            html +='<span class="close_popup" title="Close">+</span>';
                            html +='<div id="ybc-blog-form-popup"></div>';
                            html +='</div>';
                            html +='</div>';
                            html += '</div>';
                            html +='</div>';
                            $('.ybc_blog_form_content_admin').append(html);
                        }
                        else
                            $('.ybc_blog_popup').addClass('show');
                        $('#ybc-blog-form-popup').html(json.form);
                    }
                },
                error: function(xhr, status, error)
                {
                    $this.removeClass('loading');
                    var err = eval("(" + xhr.responseText + ")");
                    alert(err.Message);
                }
            });
        }
    });
    $(document).on('click','.chatgpt .btn-new-item',function(e){
        e.preventDefault();
        if($('#ybc-blog-form-popup').length==0)
        {
            var html ='<div class="ybc_blog_popup show">';
            html += '<div class="popup_content table">';
            html +='<div class="popup_content_tablecell">';
            html +='<div class="popup_content_wrap" style="position: relative">';
            html +='<span class="close_popup" title="Close">+</span>';
            html +='<div id="ybc-blog-form-popup"></div>';
            html +='</div>';
            html +='</div>';
            html += '</div>';
            html +='</div>';
            $('.ybc_blog_form_content_admin').append(html);
        }
        else
            $('.ybc_blog_popup').addClass('show');
        $('#ybc-blog-form-popup').html($('.box-form-chatgpt').html());
    });
    $(document).on('keyup','body',function(e){
        if(e.keyCode == 27) {
            if ($('.ybc_blog_popup').length)
            {
                $('.ybc_blog_popup').removeClass('show');
            }
        }
    });
    $(document).mouseup(function (e){
        if($('#ybc-blog-form-popup').length)
        {
            var container_dropdown = $('#ybc-blog-form-popup');
            if (!container_dropdown.is(e.target)&& container_dropdown.has(e.target).length === 0)
            {
                $('.ybc_blog_popup').removeClass('show');
            }
        }
    });
});