/**
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
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
var xhrAjax = null;
var time_run_ajax = 500;
var run_ajax = null;
function odm_checkShowReset()
{
    if(!$('.tbody_list_order').hasClass('search'))
        return true;
    var ok= false;
    if($('.with-filters input[type="text"]').length)
    {
        $('.with-filters input[type="text"]').each(function(){
            if($(this).val()!='')
            {
                ok = true;
            }
        })
    }
    if(!ok && $('.with-filters select').length)
    {
        $('.with-filters select').each(function(){
            if($(this).val()!='')
            {
                ok = true;
            }
        });
    }
    if($('button#submitFilterButtonorder').length)
    {
        if(!ok)
        {
            setTimeout(function(){
                $('button#submitFilterButtonorder').prop('disabled',true);
                $('button[name="submitResetorder"]').remove();  
            },500); 
        }
        else
        {
            setTimeout(function(){
                $('button#submitFilterButtonorder').prop('disabled',false);
                $('button[name="submitResetorder"]').remove();    
                $('button#submitFilterButtonorder').after('<button class="btn btn-warning" type="submit" name="submitResetorder"><i class="icon-eraser"></i>Reset</button>');
            },500); 
        }
    }
    else
    {
        if(!ok)
        {
            setTimeout(function(){
                $('button.grid-search-button').prop('disabled',true);
                $('.js-grid-reset-button').remove();  
            },500); 
        }
        else
        {
            setTimeout(function(){
                $('button.grid-search-button').prop('disabled',false);
                $('.js-grid-reset-button').remove();  
                $('.grid-search-button').after('<div class="js-grid-reset-button"><div class="clearfix"></div><button class="btn btn-link ets-js-reset-search float-right"><i class="material-icons">clear</i>Reset</button></div>');
            },500); 
        }
    }
    
}
function runAjaxFilterOrder(url,formData,type)
{  
    if(xhrAjax)
        xhrAjax.abort();
    $('.tbody_list_order').parents('#order_filter_form').addClass('loading');
    $('.bulk-actions,.pagination_list_order').hide();
    if ( $('#ets_warningGradientOuterBarG').length <= 0 ){
        $('.tbody_list_order').parents('#order_filter_form.loading').find('.table-responsive').append('<div id="ets_warningGradientOuterBarG"><div id="ets_warningGradientFrontBarG" class="ets_warningGradientAnimationG"><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div><div class="ets_warningGradientBarLineG"></div></div></div>');
    }
    var numthItems = $('.tbody_list_order > tr').length;
    if ( numthItems < 5){
        $('#order_filter_form.loading').addClass('small_space_top');
    }
    xhrAjax = $.ajax({
        url: url,
        data: formData,
        type: type,
        dataType: 'html',
        processData: false,
        contentType: false,
        success: function(html){
            if(html)
            {
                $('.bulk-actions,.pagination_list_order').show();
               var begin =  html.indexOf('<!--begin_tbody_list_order!-->');
               var end =  html.indexOf('<!--end_tbody_list_order!-->');
               if(begin >0 && end >0)
               {
                    $('body.adminorders').addClass('searched');
                    var list_orders = html.substr(begin,end-begin);
                   $('.tbody_list_order').replaceWith(list_orders);
                   var begin_pagination =  html.indexOf('<!--begin_pagination_list_order!-->');
                   var end_pagination =  html.indexOf('<!--end_pagination_list_order!-->');
                   var list_pagination = html.substr(begin_pagination,end_pagination-begin_pagination);
                   $('.pagination_list_order').replaceWith(list_pagination);
                   $('.tbody_list_order').parents('#order_filter_form').removeClass('loading').removeClass('small_space_top');
                   //$('#ets_warningGradientOuterBarG').remove();
                   if ($(".datepicker2").length > 0) {
                        var dateToday = new Date();
            			$(".datepicker2").ets_datetimepicker({
            				format:'Y-m-d H:m:s',
                            showSecond: true,
                            maxDate: dateToday,
                            defaultDate: new Date(),
            			});
                   }
                   var begin_title =  html.indexOf('<!--begin_header_list_order!-->');
                   var end_title =  html.indexOf('<!--end_header_list_order!-->');
                   var list_title = html.substr(begin_title,end_title-begin_title);
                   $('h3.card-header-title').replaceWith(list_title);
               }
               else
                    $('.tbody_list_order').parents('#order_filter_form').removeClass('loading').removeClass('small_space_top');
            }
            //insertwidthvaluetable();
        },
        error: function(xhr, status, error)
        { 
            //$('#ets_warningGradientOuterBarG').remove();
        }
    }); 
}
function ets_orderManagerChangeInLine($this,$buttom,$length_input,$index)
{
    $index++;
    var id_order= $this.data('order');
    var table= $this.data('table');
    var primary_key = $this.data('key');
    var key_value= $this.data('id');
    var key_change= $this.data('change');
    var value_change= $this.val();
    var validate = $this.data('validate');
    var required = $this.data('required');
    $this.closest('.wapper-change-order').addClass('loading');
    $.ajax({
        url: '',
        data:{
            'changeorderinline' :1,
            'id_order' :id_order,
            'table' :table,
            'primary_key':primary_key,
            'key_value':key_value,
            'key_change':key_change,
            'value_change':value_change,
            'validate':validate,
            'required':required
        },
        type: 'post',
        dataType: 'json',
        success: function(json){
            $this.closest('.group-action-update-order').removeClass('loading');
            if(json.success)
            {
                $this.removeClass('error');
                if($this.hasClass('input_change_order_note'))
                    $('#submitOrderCustomerNote').prop('disabled',true);
                if(json.value_style)
                {
                    $this.closest('.wapper-change-order').prev('.span_change_order').find('span').attr('style',json.value_style).html(json.value_changed);
                    $this.closest('.wapper-change-order').prev('.span_change_order').find('.dropdown button.btn-secondary').attr('style',json.value_style).html(json.value_changed);
                    if(json.badge_success)
                        if(!$this.closest('tr').find('.list_order_price .badge-success').length && $this.closest('tr').find('.list_order_price .span_change_order').length>0)
                        {
                            $this.closest('tr').find('.list_order_price .span_change_order').each(function(){
                                $(this).html('<span class="badge badge-success">'+$(this).text()+'</span>');  
                            });
                        }
                          
                    else
                    {
                        if($this.closest('tr').find('.list_order_price .badge-success').length)
                        {
                            $this.closest('tr').find('.list_order_price .span_change_order').each(function(){
                                $(this).html($(this).find('.badge-success').text());
                            });
                        }  
                    }
                    if(json.PDFIcons)
                    {
                        $this.closest('tr').find('.list_order_pdf').html(json.PDFIcons);
                        $this.closest('tr').find('.span_change_order .justify-content-between.text-right').html(json.PDFIcons);
                    }
                }
                else
                {
                    if($this.closest('.wapper-change-order').prev('.span_change_order').find('span').length >0)
                        $this.closest('.wapper-change-order').prev('.span_change_order').find('span').html(json.value_changed);
                    else
                        $this.closest('.wapper-change-order').prev('.span_change_order').html(json.value_changed);
                }
                if(json.value_order)
                {
                    if(json.is170)
                    {
                        formatCurrencyCldr(parseFloat(json.value_order.total_paid_tax_incl) -parseFloat(json.value_order.total_paid_tax_excl), function(value) {
                			$('#orderTaxesTotal').html(value);
                		});
                        formatCurrencyCldr(parseFloat(json.value_order.total_shipping_tax_excl), function(value) {
                			$('#orderShippingTotal').html(value);
                		});
                        formatCurrencyCldr(parseFloat(json.value_order.total_paid_tax_incl), function(value) {
                			$('#orderTotal').html(value);
                		});
                    }
                    else
                    {
                        updateAmounts(json.value_order);
                        if(prestashop_is17)
                        formatCurrencyCldr(parseFloat(json.value_order.total_paid_tax_incl) -parseFloat(json.value_order.total_paid_tax_excl), function(value) {
                			$('#total_taxes td.amount').html(value);
                		});
                    }
                }
                if(json.total_paid_tax_incl)
                {
                    if($this.closest('tr').find('.total_paid_tax_incl .badge-success').length>0)
                      $this.closest('tr').find('.total_paid_tax_incl .badge-success').html(json.total_paid_tax_incl);
                    else
                        if($this.closest('tr').find('.total_paid_tax_incl').length >0)
                            $this.closest('tr').find('.total_paid_tax_incl').html(json.total_paid_tax_incl);  
                }
                if(!$buttom)
                {
                        $('.ets_order_popup').before(json.success); 
                        setTimeout(function(){$('.bootstrap_sussec').remove();},3000);
                        $this.closest('.wapper-change-order').hide();
                        $this.closest('.wapper-change-order').prev('.span_change_order').show();
                    $this.closest('.wapper-change-order').find('.btn-group-action').show();
                        $this.removeClass('changed');
                }
                else
                {
                    if(json.success)
                    {
                        $('[class*="bootstrap_sussec_"]').remove();
                        $('.ets_order_popup').before('<div class="bootstrap_sussec_'+key_change+'">'+json.success+'</div>'); 
                        setTimeout(function(){$('.bootstrap_sussec_'+key_change).remove();},3000);
                    }
                    if($index== $length_input)
                    {
                        if($this.closest('tr').find('.input_change_order_inline.error').length <=0)
                        {
                            $this.closest('tr').find('.span_change_order').show();
                            $this.closest('tr').find('.btn-group-action').show();
                            $this.closest('tr').find('.wapper-change-order').hide();
                            $this.closest('tr').find('.group-action-order').show();
                            $this.closest('tr').find('.group-action-update-order').hide();
                        }
                        $this.closest('tr').find('.input_change_order_inline:not(.error)').removeClass('changed');
                        $this.closest('tr').find('.group-action-update-order').removeClass('loading');  
                        $this.closest('tr').removeClass('updating');
                    }
                    else
                        ets_orderManagerChangeInLine($buttom.closest('tr').find('.input_change_order_inline.changed').eq($index),$buttom,$length_input,$index);
                    
                }
            }
            if(json.errors)
            {
                $this.closest('.group-action-update-order').removeClass('loading');
                if(!$buttom)
                {
                    $('.ets_order_popup').before(json.errors); 
                    setTimeout(function(){$('.module_error').remove();},5000);
                }
                else
                {
                    $('.ets_order_popup').before('<div class="module_error_'+key_change+'">'+json.errors+'</div>'); 
                    setTimeout(function(){$('.module_error_'+key_change).remove();},5000);
                    if($index== $length_input)
                    {
                        $this.closest('tr').find('.group-action-update-order').removeClass('loading');
                        $this.closest('tr').removeClass('updating');
                    }
                    else
                        ets_orderManagerChangeInLine($buttom.closest('tr').find('.input_change_order_inline.changed').eq($index),$buttom,$length_input,$index);
                }
                $this.addClass('error');
            }
        },
        error: function(xhr, status, error)
        {     
        }
    });
}
$(document).ready(function(){
    if ($(".datepicker2").length > 0) {
        var dateToday = new Date();
		$(".datepicker2").ets_datetimepicker({
			format:'Y-m-d H:m:s',
            showSecond: true,
            maxDate: dateToday,
            defaultDate: new Date(),
		});
    }
    $('#order_ajax_filter').remove();
    odm_checkShowReset();
    $(document).on('click','.searched .table.table .js-choice-options .js-dropdown-item',function(e){
        e.preventDefault();
        var action = $(this).closest(".js-choice-options").data("url");
        var idState = $(this).data('value');
        $form = $('<form action="'+action+'" method="POST"><input name="value" type="hidden" value="'+idState+'"></form>');
        $form.appendTo("body"); $form.submit();
    });
    $(document).on('click','button.ets-js-reset-search,button[name="submitResetorder"]',function(e){
        if(!$('.tbody_list_order').hasClass('search'))
            return true;
        e.preventDefault();
        $('.with-filters input[type="text"]').val('');
        $('#orderFilter_a__date_add_0').val('');
        $('#orderFilter_a__date_add_1').val('');
        if($('.with-filters select').length)
        {
           $('.with-filters select').each(function(){
                if($(this).val()!='')
                {
                    $(this).val('');
                    $(this).find('option').removeAttr('selected');
                    $(this).find('option[value=""]').attr('selected','selected');
                }
           }) 
        }
        var formData = new FormData($(this).parents('form').get(0));
        if(xhrAjax)
            xhrAjax.abort();
        if(run_ajax)
            clearTimeout(run_ajax);
        odm_checkShowReset();                                        
        runAjaxFilterOrder($(this).parents('form').attr('action'),formData,'post');
    });
    $(document).on('keyup input','input[name="paginator_order_jump_page"]',function(e){
            if(!$('.tbody_list_order').hasClass('search'))
                return true;
            var val = parseInt($(this).val());
            if(e.keyCode==13)
            {
                if (parseInt(val) > 0) {
    				var limit = $(this).attr('pslimit');
    				var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
    				var formData = new FormData($('#order_grid_table').parents('form').get(0));
                    if(run_ajax)
                        clearTimeout(run_ajax);
                    runAjaxFilterOrder(url,formData,'get');
            		return false;
    			}
            }
			var max = parseInt($(this).attr('psmax'));
			if (val > max) {
				$(this).val(max);
				return false;
			}
        });
        $(document).on('change','input[name="paginator_order_jump_page"]',function(){
            if(!$('.tbody_list_order').hasClass('search'))
                return true;
            var val = parseInt($(this).val());
			if (parseInt(val) > 0) {
				var limit = $(this).attr('pslimit');
				var url = $(this).attr('psurl').replace(/999999/, (val-1)*limit);
				var formData = new FormData($('#order_grid_table').parents('form').get(0));
                if(run_ajax)
                    clearTimeout(run_ajax);
                runAjaxFilterOrder(url,formData,'get');
        		return false;
			}
        });
        $(document).on('change','select[name="paginator_order_select_page_limit"]',function(){
            if(!$('.tbody_list_order').hasClass('search'))
                return true;
            var url = $(this).attr('psurl').replace(/_limit/, $('option:selected', this).val());
            var $this = $(this);
            url = url.replaceAll('%5B','[');
            url = url.replaceAll('%5D',']');
            $('#order_filter_form').addClass('loading');
            $('#order_filter_form').attr('action',url);
            var formData = new FormData($('#order_grid_table').parents('form').get(0));
            if(run_ajax)
                clearTimeout(run_ajax);
            runAjaxFilterOrder(url,formData,'get');
            window.history.pushState("", "", url);
    		return false;
        });
    $(document).on('click','.pagination_list_order a.page-link',function(){
        if(!$('.tbody_list_order').hasClass('search'))
            return true;
        var $this = $(this);
        $('#order_filter_form').addClass('loading');
        var formData = new FormData($('#order_grid_table').parents('form').get(0));
        if(run_ajax)
            clearTimeout(run_ajax);
        runAjaxFilterOrder($(this).attr('href'),formData,'get');
        return false;
    });
    $(document).on('click','.grid-search-button,button[name="submitFilter"]',function(e){
        if($('.tbody_list_order').hasClass('search'))
        {
            var formData = new FormData($(this).parents('form').get(0));
            formData.append('filter_ajax',1); 
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            odm_checkShowReset();
            runAjaxFilterOrder($(this).parents('form').attr('action'),formData,'post');
            return false;
        }
    });
    $('#order_date_add_from').attr('id','order_date_add_from2');
    $('#order_date_add_to').attr('id','order_date_add_to2');
    $(".with-filters .datepicker > input,input.datepicker").datepicker({
        dateFormat: 'yy-mm-dd',
        onSelect: function() {
            if($(this).next('input').length)
                $(this).next('input').val($(this).val());
            if(!$('.tbody_list_order').hasClass('search'))
                return true;
            var formData = new FormData($(this).parents('form').get(0));
            formData.append('filter_ajax',1); 
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            odm_checkShowReset();
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            var $this = $(this);
            run_ajax = setTimeout(function(){runAjaxFilterOrder($this.parents('form').attr('action'),formData,'post');},time_run_ajax);        
        }
    });
    $(document).on('change','.with-filters select,#orderFilter_a__date_add_0,#orderFilter_a__date_add_1',function(){
        if(!$('.tbody_list_order').hasClass('search'))
            return true;
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('filter_ajax',1); 
        if(xhrAjax)
            xhrAjax.abort();
        if(run_ajax)
            clearTimeout(run_ajax);
        odm_checkShowReset();
        runAjaxFilterOrder($(this).parents('form').attr('action'),formData,'post');
    });
     $(document).on('input','.with-filters input',function(e){
        if(!$('.tbody_list_order').hasClass('search') || $(this).hasClass('js-bulk-action-select-all'))
            return true;
        var $this = $(this);
        $(this).change();
        if(($(this).hasClass('form-min-max') || $(this).hasClass('form-min-min')) && !validate_isFloat($(this).val()))
        {
            return false;
        }
        if($('.with-filters input').length)
        {
            $('.with-filters input').each(function(){
                if($(this).attr('sql')!=undefined)
                    $(this).val($(this).attr('sql'));
            });
        }
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('filter_ajax',1);
        odm_checkShowReset();
        if(xhrAjax)
            xhrAjax.abort();
        if(run_ajax)
            clearTimeout(run_ajax);
        run_ajax = setTimeout(function(){runAjaxFilterOrder($this.parents('form').attr('action'),formData,'post');},time_run_ajax);
    });
    $(document).on('keyup','.with-filters input',function(e){
        if(!$('.tbody_list_order').hasClass('search') || $(this).hasClass('js-bulk-action-select-all'))
            return true;
        var $this = $(this);
        $(this).change();
        if(($(this).hasClass('form-min-max') || $(this).hasClass('form-min-min')) && !validate_isFloat($(this).val()))
        {
            return false;
        }
        if($('.with-filters input').length)
        {
            $('.with-filters input').each(function(){
                if($(this).attr('sql')!=undefined)
                    $(this).val($(this).attr('sql'));
            });
        }
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('filter_ajax',1);
        odm_checkShowReset();
        if(!$(this).hasClass('error') &&( (e.keyCode >=48 && e.keyCode<=57) || (e.keyCode >=96 && e.keyCode<=105) || (e.keyCode >=65 && e.keyCode<=90) || e.keyCode==188 || e.keyCode==190 || e.keyCode==13 || e.keyCode==8) )
        {
            if(xhrAjax)
                xhrAjax.abort();
            if(run_ajax)
                clearTimeout(run_ajax);
            run_ajax = setTimeout(function(){runAjaxFilterOrder($this.parents('form').attr('action'),formData,'post');},time_run_ajax);                                    
        }
    });
    if($('.js-grid-header .card-header-title').data('title'))
    {
        $('.container-fluid h1.title').html($('.js-grid-header .card-header-title').data('title'));
    }
    if($('#order_grid_table tr.empty_row').length)
        $('.btn-ets-odm-empty-trash').hide();
    $(document).on('change','.edit_product_name',function(){
        var $this = $(this);
        $this.addClass('loading');
        var $id_product = $this.data('id_product');
        var $id_order = $this.data('id_order');
        var $id_product_attribute = $this.data('id_product_attribute');
        var $product_name = $(this).val();
        $.ajax({
            url: '',
            data: 'changeProductNameInstall=1&id_product='+$id_product+'&id_order='+$id_order+'&id_product_attribute='+$id_product_attribute+'&product_name='+$product_name,
            type: 'post',
            dataType: 'json',
            success: function(json){
                if(json.success)
                {
                    showSuccessMessage(json.success);
                    $('.edit_product_name[data-id_product="'+$id_product+'"][data-id_order="'+$id_order+'"][data-id_product_attribute="'+$id_product_attribute+'"]').attr('value',json.product_name);
                    $('.edit_product_name[data-id_product="'+$id_product+'"][data-id_order="'+$id_order+'"][data-id_product_attribute="'+$id_product_attribute+'"]').closest('td').find('.productName').html(json.product_name);
                    $this.closest('td').find('.productName').html(json.product_name);
                }
                if(json.errors)
                    showErrorMessage(json.errors);
                $this.removeClass('loading');
            },
            error: function(xhr, status, error)
            { 
                
            }
        });
    });
    $(document).on('click','.js-order-product-edit-btn',function(){
        $('.editProductRow .js-product-shoplicense').removeClass('d-none');
    });
    $(document).on('click','.productEditCancelBtn',function(){
        $('.editProductRow .js-product-shoplicense').addClass('d-none');
    });
    if ( $('.datepicker').length > 0 ){
        $('.datepicker').attr('autocomplete','off');
    }
    $(document).mouseup(function (e){
        if($('#quick-view-order').length)
        {
            var container_quick_view = $('#quick-view-order');
            if (!container_quick_view.is(e.target)&& container_quick_view.has(e.target).length === 0)
            {
                $('.ets_order_popup').removeClass('show');
            }
        }
        if($('.ets_order_popup #form_arrange').length)
        {
            var container_popup = $('.ets_order_popup #form_arrange');
            if (!container_popup.is(e.target)&& container_popup.has(e.target).length === 0)
            {
                $('.ets_order_popup').removeClass('show');
            }
        }
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27 && $('#quick-view-order').length) {
            $('.ets_order_popup').removeClass('show');
        }
    });
    $(document).on('click','.all_arrange_list_order',function(){
        var $list_group = $(this).closest('.list-group');
        if($(this).is(':checked'))
        {
            $list_group.find('input.arrange_list_order').prop('checked',true);
        } else {
            $list_group.find('input.arrange_list_order').prop('checked',false);
        }
            
        $list_group.find('input.arrange_list_order').change();
    });
    $(document).on('click','.close_field',function(){
        var field= $(this).data('field');
        $('#list-order-fields .field_'+field).remove();
        $('input.arrange_list_order[value="'+field+'"]').prop('checked',false);
        $('input.arrange_list_order[value="'+field+'"]').change();
    });
    $(document).on('click','.clear_all_fields',function(e){
       e.preventDefault(); 
       $('#list-order-fields').html('');
       $('.list-group-content input[type="checkbox"]').prop('checked',false);
    });
    $(document).on('change','.arrange_list_order',function(){
        var field = $(this).val();
        var field_title= $(this).data('title');
        if($(this).is(':checked'))
        {
            if($('#list-order-fields .field_'+field).length==0)
            {
               $('#list-order-fields').append('<li class="field_'+field+'"><label><input name="listFieldOrders[]" value="'+field+'" type="hidden">'+field_title+'</label><span class="close_field" data-field="'+field+'"> Close</span></li>')
            }
            if($(this).closest('.list-group').find('input.arrange_list_order:checked').length == $(this).closest('.list-group').find('input.arrange_list_order').length)
            {
                $(this).closest('.list-group').find('.all_arrange_list_order').prop('checked',true);
            }
        }
        else
        {
            $('#list-order-fields .field_'+field).remove();
            $(this).closest('.list-group').find('.all_arrange_list_order').prop('checked',false);
        }
    });
    $(document).on('click','.open_close_list',function(){
       $(this).toggleClass('list_close').toggleClass('list_open');
       $(this).next('.list-group-content').toggle(); 
    });
    $(document).on('click','.ets_editorder_inline .span_change_order',function(){
       $(this).next('.wapper-change-order').show();
       $(this).hide(); 
    });
    $(document).on('click','.close_change_order',function(){
        $(this).parents('.wapper-change-order').hide();
        $(this).parents('.wapper-change-order').prev('.span_change_order').show();
    });
    $(document).on('change','.ets_editorder_inline .input_change_order',function(){
        ets_orderManagerChangeInLine($(this),false,true,0);
    });
    $(document).on('keypress','.ets_editorder_inline .input_change_order',function(e){
        if(e.which == 13) { 
            ets_orderManagerChangeInLine($(this),false,true,0);
            return false;
        } 
    });
    $(document).on('keypress','.input_change_order_note',function(e){
        $('#submitOrderCustomerNote').prop('disabled',false);
    });
    $(document).on('blur','.datepicker .input_change_order_inline',function(){
        $(this).addClass('changed');
    });
    $(document).on('change','.input_change_order_inline',function(){
        $(this).addClass('changed');
        return false;
    });
    $(document).on('click','#submitOrderCustomerNote',function(e){
        e.preventDefault();
        ets_orderManagerChangeInLine($('.input_change_order_note'),false,true,0);
    });
    $(document).on('click','.edit_order_inline',function(e){
        e.preventDefault();
        var $row = $(this).closest('tr');
        if($row.find('.wapper-change-order').length)
        {
            $row.find('.wapper-change-order').prev('.span_change_order').hide();
            $row.find('.wapper-change-order').show();
            $row.find('.group-action-order,.btn-group-action').hide();
            $row.find('.group-action-update-order').show();
        }
        else
        {
            $('body').addClass('loading');
            $.ajax({
                url: ets_omn_link_order_manager,
                data: {
                    'loadInputChangeInline':1,
                    'row_order' : $row.data('order')
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.htmls)
                    {
                        $(json.htmls).each(function(){
                            if($row.find('td[data-column-id="'+this.column_name+'"]').length)
                            {
                                $row.find('td[data-column-id="'+this.column_name+'"]').append(this.form);
                            }
                        });
                        if ($(".datepicker2").length > 0) {
                            var dateToday = new Date();
                            $(".datepicker2").ets_datetimepicker({
                                format:'Y-m-d H:m:s',
                                showSecond: true,
                                maxDate: dateToday,
                                defaultDate: new Date(),
                            });
                        }
                        $('body').removeClass('loading');
                        $row.find('.wapper-change-order').prev('.span_change_order').hide();
                        $row.find('.wapper-change-order').show();
                        $row.find('.group-action-order,.btn-group-action').hide();
                        $row.find('.group-action-update-order').show();
                    }
                },
                error: function(xhr, status, error)
                {
                    $('body').removeClass('loading');
                }
            });
        }
        $row.addClass('updating');
    });
    $(document).on('click','.cancel_order_change_link',function(e){
        e.preventDefault();
        $(this).closest('tr').find('.span_change_order').show();
        $(this).closest('tr').find('.wapper-change-order').hide();
        $(this).closest('tr').find('.group-action-order,.btn-group-action').show();
        $(this).closest('tr').find('.group-action-update-order').hide();
        $(this).closest('tr').removeClass('updating');
    });
    $(document).on('click','button[name="submitOrderChangeInLine"]',function(e){
        e.preventDefault();
        var length_input = $(this).closest('tr').find('.input_change_order_inline.changed').length;
        if(length_input)
        {
            $(this).parent().addClass('loading');
            ets_orderManagerChangeInLine($(this).closest('tr').find('.input_change_order_inline.changed').eq(0),$(this),length_input,0);
        }
    });
    $(document).on('click','.duplicate_order_list,.change_customer_order',function(e){
        e.preventDefault();
        var href= $(this).attr('href');
        var $this = $(this);
        $('body').addClass('loading');
        $.ajax({
            url: href,
            data: '',
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('#block-form-popup-dublicate').html(json.block_html);

            },
            error: function(xhr, status, error)
            { 
                
            }
        });
    });
    $(document).on('click','.arrange_order_list',function(e){
        e.preventDefault();
        var href= $(this).attr('href');
        $('body').addClass('loading');
        $.ajax({
            url: href,
            data: 'ajax=1',
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('#block-form-popup-dublicate').html(json.block_html);
                $('.ets_order_popup').addClass('show');
                
                $('body').removeClass('loading');
            },
            error: function(xhr, status, error)
            { 
                
            }
        });
    });
    $(document).on('click','button[name="btnSubmitDuplicateOrder"],button[name="btnSubmitEditCustomerOrder"]',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            $('.module_error').remove();
            var $this = $(this);
            var formData = new FormData($(this).parents('form').get(0));
            $(this).addClass('loading');
            $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    if(json.success)
                        window.location.href = json.link_redirect;
                    if(json.errors)
                       $this.before(json.errors); 
                    $this.removeClass('loading');
                    //setTimeout(function(){ $('.module_error').remove(); }, 3000);
                },
                error: function(xhr, status, error)
                { 
                    $this.removeClass('loading');  
                }
            });
        }
        return false;
    });
    $(document).on('click','button[name="btnSubmitArrangeListOrder"]',function(e){
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('btnSubmitArrangeListOrder',1);
        $(this).addClass('loading');
        $.ajax({
            url: '',
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $this.removeClass('loading');
                if(json.success)
                    window.location.reload();
                if(json.errors)
                   $this.before(json.errors); 
            },
            error: function(xhr, status, error)
            { 
                $this.removeClass('loading');
            }
        });
    });
    $(document).on('click','button[name="btnSubmitRessetToDefaultList"]',function(e){
        e.preventDefault();
        var $this = $(this);
        $this.addClass('loading');
        $.ajax({
            url: '',
            data: 'btnSubmitRessetToDefaultList=1',
            type: 'post',
            dataType: 'json',
            success: function(json){
                window.location.reload();
                $this.removeClass('loading');
            },
            error: function(xhr, status, error)
            { 
                $this.removeClass('loading');
            }
        });
    });
    $(document).on('click','.change-customer_order',function(e){
       if($('.customerCard.selected-customer').length)
       {
            $('#address_part').show(); 
            $('#customer_part').show();
       }
       else
       {
            $('#address_part').toggle(); 
            $('#customer_part').toggle();
       }
       
    });
    $(document).on('click','.quick_view_order',function(e){
        e.preventDefault();
        var id_order= $(this).data('id');
        var $this= $(this); 
        $('body').addClass('loading');       
        $.ajax({
            url: '',
            data: 'quickvieworder=1&id_order='+id_order,
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('#block-form-popup-dublicate').html(json.order_quickview);
                $('.ets_order_popup').addClass('show');
                $('body').removeClass('loading');
            },
            error: function(xhr, status, error)
            { 
                  
            }
        });
    });
    $(document).on('click','.close_popup,.close_duplicate',function(){
        $('.ets_order_popup').removeClass('show');
    });
    $(document).on('keyup','body',function(e){
        if(e.keyCode == 27) {
           if ($('.ets_order_popup').length)
           {
                $('.ets_order_popup').removeClass('show');
           }
        }
    });
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        if(xhr.responseText && xhr.responseText.indexOf("shipping_html")>=0)
        {
            var data = JSON.parse(xhr.responseText);
            if(data.order)
            {
                if(data.shipping_html && $('#shipping_table').length)
                    $('#shipping_table').parent().replaceWith(data.shipping_html);
                $('#total_products td.amount').fadeIn('slow');
                $('#total_discounts td.amount').fadeIn('slow');
                if (data.order.total_discounts_tax_incl > 0)
                    $('#total_discounts').slideDown('slow');
                if (data.order.total_wrapping_tax_incl > 0)
                    $('#total_wrapping').slideDown('slow');
                $('#total_shipping td.amount').fadeIn('slow');
                $('#total_order td.amount').fadeIn('slow');
                $('.order_action').fadeIn('slow');
                if(prestashop_is17)
                    formatCurrencyCldr(parseFloat(data.order.total_paid_tax_incl) -parseFloat(data.order.total_paid_tax_excl), function(value) {
        			$('#total_taxes td.amount').html(value);
        		  });
            }
        }
    });
    $(document).on('click','.open_payment_information',function(e){
		if ($(this).closest('tr').next('tr').is(':visible'))
			$(this).closest('tr').next('tr').hide();
		else
			$(this).closest('tr').next('tr').show();
		e.preventDefault();
	});

});