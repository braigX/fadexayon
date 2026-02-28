/**
 * 2007-2022 ETS-Soft
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
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
var name_input_search_curent ='';
$(document).ready(function(){
    ets_cs.search();
    $(document).on('click','.ets_cs_search_product',function(){
        name_input_search_curent = $(this).next('.ets_cs_product_ids').attr('name');
    });
   $(document).on('click','.field-positions .setting',function(){
        $('.ets-cs-form-group-field').removeClass('active');
        $('.ets-cs-form-group-field.'+$(this).data('setting')).addClass('active');
   }); 
   $(document).on('click','.close-setting-field,.module_form_cancel_btn_filed',function(){
        $('.ets-cs-form-group-field').removeClass('active');
   });
   $(document).on('click','.field-positions .ets_sc_field',function(){
        var field = $(this).data('field');
        if($(this).is(':checked'))
        {
            $(this).parent().addClass('active');
            if($('#'+field+'_on').length)
            {
                $('#'+field+'_on').click();
            }
            var value_filed=1;
        }
        else
        {
            $(this).parent().removeClass('active');
            if($('#'+field+'_off').length)
            {
                $('#'+field+'_off').click();
            }
             var value_filed=0;
        }
        $.ajax({
            url: '',
            data: 'action=updateBlock&field='+field+'&value_filed='+value_filed,
            type: 'post',
            dataType: 'json',
            async: true,
			cache: false,
            success: function(json){
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                }
                if(json.errors)
                    $.growl.error({message:json.errors});
            },
            error: function(xhr, status, error)
            {
                
            }
        }); 
   });
   if($('#field-positions').length)
   {
        var $myfield = $("#field-positions");
    	$myfield.sortable({
    		opacity: 0.6,
            handle: ".position_number",
            cursor: 'move',
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updateFieldOrdering";	
                var $this=  $(this);					
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(json)
        			{
                        if(json.success)
                        {
                            $.growl.notice({ message: json.success });
                            var i=1;
                            $('.field-positions li').each(function(){
                                $(this).find('.position_number').html('<span>'+i+'</span>');
                                i++;
                            });
                        }
                        if(json.errors)
                        {
                            $.growl.error({message:json.errors});
                            $myfield.sortable("cancel");
                        }
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }
    $(document).on('click','.module_form_submit_btn_filed',function(){
        $('#module_form_submit_btn').click();
    });
    $(document).on('click','button[name="saveConfig"]',function(e){
        e.preventDefault();
        if(!$('#module_form').hasClass('loading'))
        {
            $('#module_form').addClass('loading');
            var formData = new FormData($(this).parents('form').get(0));
            formData.append(name, 1);
            formData.append('ajax', 1);
            var url_ajax= $('#module_form').attr('action');
            $('.bootstrap .module_error').remove();
            $.ajax({
                url: url_ajax,
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $('#module_form').removeClass('loading');
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        if($('.ets-cs-form-group-field.active').length)
                            $('.ets-cs-form-group-field.active').removeClass('active');
                    }
                    else if(json.errors)
                    {  
                        if($('.ets-cs-form-group-field.active').length)
                            $('.ets-cs-form-group-field.active .popup_footer').before(json.errors);
                        else if($('#fieldset_1 .form-wrapper').length)
                            $('#fieldset_1 .form-wrapper').append(json.errors);
                        else
                            $('#module_form .form-wrapper').append(json.errors);
                    }
                },
                error: function(xhr, status, error)
                {     
                    $('#module_form').removeClass('loading');
                }
            });
        }
    });
    $(document).mouseup(function (e)
    {
        if($('.ets-cs-form-group-field.active').length)
        {
            if (!$('.ets-cs-form-group-field.active .ets-cs-form-group-field-wapper').is(e.target)&& $('.ets-cs-form-group-field.active .ets-cs-form-group-field-wapper').has(e.target).length === 0 && !$('.ets_cs_results').is(e.target) && $('.ets_cs_results').has(e.target).length === 0  )
            {
                $('.ets-cs-form-group-field.active').removeClass('active');
            }
        }
    });
    $(document).keyup(function(e) { 
        if(e.keyCode == 27) {
            if($('.ets-cs-form-group-field.active').length)
            {
                $('.ets-cs-form-group-field.active').removeClass('active');
            }
        }
    });
    $(document).on('click','.ets_cs_clear_cache',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading');
            var $this= $(this);
            $.ajax({
                url: '',
                data: 'action=clearCache',
                type: 'post',
                dataType: 'json',
                async: true,
    			cache: false,
                success: function(json){
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                    }
                    if(json.errors)
                        $.growl.error({message:json.errors});
                    $this.removeClass('loading');
                },
                error: function(xhr, status, error)
                {
                    $this.removeClass('loading');
                }
            }); 
        }
        
    });
});
ets_cs ={
    search : function() {
        if ($('.ets_cs_product_ids').length > 0 && $('.ets_cs_search_product').length > 0 && typeof ets_cs_link_search_product !== "undefined")
        {
            var ets_cs_autocomplete = $('.ets_cs_search_product');
            ets_cs_autocomplete.autocomplete(ets_cs_link_search_product, {
                resultsClass: "ets_cs_results",
                minChars: 1,
                delay: 300,
                appendTo: '.ets_cs_search_product_form',
                autoFill: false,
                max: 20,
                matchContains: false,
                mustMatch: false,
                scroll: true,
                cacheLength: 100,
                scrollHeight: 180,
                extraParams: {
                    excludeIds: '',
                },
                formatItem: function (item) {
                    return '<span data-item-id="'+item[0]+'-'+item[1]+'" class="ets_cs_item_title">' + (item[5] ? '<img src="'+item[5]+'" alt=""/> ' : '') + item[2] + (item[3]? item[3] : '') + (item[4] ? ' (Ref:' + item[4] + ')' : '') + '</span>';
                },
            }).result(function (event, data, formatted) {
                if (data)
                {
                    ets_cs.addProduct(data, $('input[name="'+name_input_search_curent+'"]'));
                }
                ets_cs.closeSearch();
            });
        }
        $(document).on('click', '.ets_cs_block_item_close', function () {
            if ($(this).parent('li').data('id') != '')
                ets_cs.removeProduct($(this).parents('li'));
        });
        if ($('.ets_cs_products').length > 0) {
            ets_cs.sortProductList();
        }
    },
    addProduct: function (data, ets_cs_product_ids) {
        if ($('#block_search_'+name_input_search_curent).length > 0 && $('#block_search_'+name_input_search_curent+' .ets_cs_product_item[data-id="'+data[0]+'"]').length==0)
        {
            if ($('#block_search_'+name_input_search_curent+' .ets_cs_product_loading.active').length <=0)
            {
                $('#block_search_'+name_input_search_curent+' .ets_cs_product_loading').addClass('active');
                $.ajax({
                    url: '',
                    data: {
                        ids : data[0],
                        add_specific_product : 1
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function(json)
                    {
                        $('.ets_cs_search_product').val('');
                        if (json) 
                        {
                            $('#block_search_'+name_input_search_curent+' .ets_cs_product_loading.active').before(json.html);
                            if (!ets_cs_product_ids.val()) 
                            {
                                ets_cs_product_ids.val(data[0]).change();
                            } 
                            else 
                            {
                                if (ets_cs_product_ids.val().split(',').indexOf(data[0]) == -1) 
                                {
                                    ets_cs_product_ids.val(ets_cs_product_ids.val() + ',' + data[0]).change();

                                } 
                                else 
                                {
                                    alert(data[2].toString() + ' has been tagged.');
                                }
                            }
                        }
                        $('#block_search_'+name_input_search_curent+' .ets_cs_product_loading.active').removeClass('active');
                    },
                    error: function(xhr, status, error)
                    {
                        $('#block_search_'+name_input_search_curent+' .ets_cs_product_loading.active').removeClass('active');
                    }
                });
            }
        }
    },
    removeIds: function (parent, element) {
        var ax = -1;
        if ((ax = parent.indexOf(element)) !== -1)
        {
            parent.splice(ax, 1);
        }
        return parent;
    },
    removeProduct : function($li) {
        var ID = $li.attr('data-id');
        var $ul = $li.parent();
        if ($ul.length >0 &&  $ul.prev('.ets_cs_product_ids').length > 0)
        {
            $li.remove();
            var IDs = '';
            if ($ul.find('li.ets_cs_product_item').length)
            {
                $ul.find('li.ets_cs_product_item').each(function(){
                    IDs += $(this).attr('data-id')+',';
                });
            }
            $ul.prev('.ets_cs_product_ids').val(IDs.trim(',')).change();
        }
    },
    closeSearch: function () {
        $('.ets_cs_search_product').val('');
    },
    sortProductList: function () {
        $('.ets_cs_products').sortable({
            update: function (e, ui) {
                if (this === ui.item.parent()[0])
                {
                    var $sort = '';
                    $(this).find ('.ets_cs_product_item').each(function () {
                        $sort += $(this).data('id') + ',';
                    });
                    if ($sort && $(this).prev('.ets_cs_product_ids').length > 0)
                        $(this).prev('.ets_cs_product_ids').val($sort).change();
                }
            }
        }).disableSelection();
    }
}