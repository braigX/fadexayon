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

var ets_ode_module_link = ets_ode_module_link;
var ets_ode_msg_confirm = ets_ode_msg_confirm || 'Do you want to delete this product?';
var ets_ode = {
    search: function () {
        if ($('#ets_ode_product_search').length > 0 && ets_ode_module_link) {
            $('#ets_ode_product_search').autocomplete(ets_ode_module_link, {
                resultsClass: "ets_ode_results",
                minChars: 3,
                appendTo: '.ets_ode_specific_product',
                autoFill: true,
                max: 20,
                matchContains: true,
                mustMatch: true,
                scroll: true,
                scrollHeight: 180,
                extraParams: {
                    excludeIds: $('#specific_product').val(),
                },
                formatItem: function (item) {
                    return item ? '<span data-item-id="' + item[0] + '-' + item[1] + '" class="ets_ode_item_title">' + (item[5] ? '<img src="' + item[5] + '" alt=""/> ' : '') + item[2] + (item[3] ? item[3] : '') + (item[4] ? ' (Ref:' + item[4] + ')' : '') + '</span>':'';
                },
            }).result(function (event, data, formatted) {
                if (data == null)
                    return false;
                if (data) {
                    ets_ode.addProduct(data);
                }
                ets_ode.closeSearch();
                $('#ets_ode_product_search').val('');
                $('#ets_ode_product_search').focus();
            });
        }
    },
    addProduct: function (data) {
        if (data) {
            var productIds = $('#specific_product'),
                product = data[0] + '-' + data[1];
            if ($('.ets_ode_products').length > 0 && productIds.length > 0 && $('.ets_ode_product_loading.active').length <= 0) {
                $('.ets_ode_product_loading').addClass('active');
                $.ajax({
                    url: ets_ode_module_link,
                    data: {
                        ids: product,
                        action: 'odeAddProduct'
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (json) {
                            $('.ets_ode_product_loading.active').removeClass('active').before(json.html);
                            if (!productIds.val()) {
                                productIds.val(product);
                            }
                            else if (productIds.val().split(',').indexOf(product) == -1) {
                                productIds.val(productIds.val() + ',' + product);
                            }
                            else {
                                showErrorMessage(data[2].toString());
                            }
                            $('#ets_ode_product_search').unautocomplete();
                            ets_ode.search();
                            $('#ets_ode_product_search').click();
                        }
                    },
                    error: function (xhr, status, error) {
                        $('.ets_ode_product_loading.active').removeClass('active');
                    }
                });
            }
        }
    },
    removeIds: function (parent, element) {
        var ax = -1;
        if ((ax = parent.indexOf(element)) !== -1)
            parent.splice(ax, 1);
        return parent;
    },
    removeProduct: function (combination) {
        var combination = combination || 0,
            products = $('.ets_ode_products'),
            productIds = $('#specific_product');
        if (confirm(ets_ode_msg_confirm) && products.length > 0 && products.find('li[data-id="' + combination + '"]').length > 0 && productIds.length > 0) {
            $('li.ets_ode_item[data-id="' + combination + '"]').remove();
            productIds.val(ets_ode.removeIds(productIds.val().split(','), combination));
        }
    },
    closeSearch: function () {
        $('#ets_ode_product_search').val('');
        $('.ets_ode_results').hide();
    },
    group_all: function (field) {
        var input_field = field || false;
        if (!input_field) {
            $('input.exported_fields[value=""]').each(function () {
                var group = $(this), count = 0;
                $('.' + group.attr('id') + ':not(input[value=""])').each(function () {
                    if (!$(this).is(':checked'))
                        count++;
                });
                group.prop('checked', (count <= 0 ? true : false));
            });
        } else if (input_field.is(':input[value=""]')) {
            var checked = input_field.is(':checked');
            $('.' + input_field.attr('id') + ':not(input[value=""])').each(function () {
                if ($(this).is(':checked') != checked) {
                    $(this).click();
                }
            });
        } else {
            var index = 0, count = 0, get_class = input_field.attr('class').replace(' ', '.'),
                group = $('.' + get_class + '[value=""]');
            $('.' + get_class + ':not(input[value=""])').each(function () {
                index++;
                if ($(this).is(':checked'))
                    count++;
            });
            group.prop('checked', (count >= index ? true : false));
        }
    },
    gencode: function (input, size) {
        var chars = "123456789abcdefghijklmnpqrstuvwxyz", random = '';
        for (var i = 1; i <= size; ++i)
            random += chars.charAt(Math.floor(Math.random() * chars.length));
        input.attr('value', random);
    },
    server1: function () {
        if ($('#export_to_server1_on').is(':checked')) {
            $('.form-group.server1').show();
        } else {
            $('.form-group.server1').hide();
        }
    },
    server2: function () {
        if ($('#export_to_server2_on').is(':checked')) {
            $('.form-group.server2').show();
        } else {
            $('.form-group.server2').hide();
        }
        ets_ode.global_ftp();
    },
    global_ftp : function () {
        if ($('#export_to_server2_on').is(':checked') && !$('#global_ftp_on').is(':checked')) {
            $('.form-group.server2.ftp').show();
        } else {
            $('.form-group.server2.ftp').hide();
        }
    },
    exported_fields : function($this)
    {
        ets_ode.group_all($this);
        if ($this.is(':input[value=""]')) {
            return;
        }
        var class_item = $this.attr('id').replace('.', '_');
        var id = $this.val();
        var value_filed = $this.attr('id').replace('exported_fields_', '');
        if ($this.is(':checked')) {
            var name = $this.parent().text();
            if($this.hasClass('shipping_fields') && $this.val()!='shipping+address')
                name = 'Shipping '+ ($this.val()== 'a.vat+number' ? name : name.toLowerCase());
            if($this.hasClass('invoice_fields') && $this.val()!='invoice+address')
                name = 'Invoice '+($this.val()=='ainvoice.vat+number' ? name : name.toLowerCase());
            if ($('#exported_list_fields .' + class_item).length == 0) {
                $('#exported_list_fields ul').append('<li id="fields-' + id + '" class="' + class_item + '">' + name + ' <span title="Delete" class="delelte_exported_fields" data-val="'+$this.val()+'">x</span></li>');
                if ($('#exported_fields').val())
                    $('#exported_fields').val($('#exported_fields').val() + ',' + value_filed);
                else
                    $('#exported_fields').val(value_filed);
                $('#exported_fields').change();
                sortFields();
            }
        }
        else {
            if ($('#exported_list_fields .' + class_item).length != 0) {
                $('#exported_list_fields ul .' + class_item).remove();
                var exported_fields = $('#exported_fields').val();
                exported_fields = exported_fields.replace(value_filed + ',', '');
                exported_fields = exported_fields.replace(',' + value_filed, '');
                exported_fields = exported_fields.replace(value_filed, '');
                $('#exported_fields').val(exported_fields);
                $('#exported_fields').change();
                sortFields();
            }
        }
    }
}

$(document).ready(function () {
    if($('input[name="ETS_ODM_ENABLE_INSTANT_FILTER"]').length)
    {
        if($('input[name="ETS_ODM_ENABLE_INSTANT_FILTER"]:checked').val()==1)
            $('.form-group.keep_search').show();
        else
            $('.form-group.keep_search').hide();
        $(document).on('change','input[name="ETS_ODM_ENABLE_INSTANT_FILTER"]',function(){
            if($('input[name="ETS_ODM_ENABLE_INSTANT_FILTER"]:checked').val()==1)
                $('.form-group.keep_search').show();
            else
                $('.form-group.keep_search').hide();
        });
    }
    if ( $('.datepicker').length > 0 ){
        $('.datepicker').attr('autocomplete','off');
    }
    /*init*/
    if ( $('body .is_ps16').length > 0 ){
        var size_header_formats = {A3:40, A4:35, A5:30,A6:20};
    } else {
        var size_header_formats = {A3:20, A4:20, A5:15,A6:15};
    }
    var size_footer_formats = {A3:21, A4:15, A5:13,A6:10};
    if($('#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT').length)
    {
        //if($('#ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER').val())
            size_header_formats[$('#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT').val()] = $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER').val();
        //if($('#ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER').val())
            size_footer_formats[$('#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT').val()] = $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER').val();
        $(document).on('change','#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT',function(){
            $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER').val(size_header_formats[$(this).val()]);
            $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER').val(size_footer_formats[$(this).val()]);
        });
    }
    if($('#ETS_ODE_PASSWORD').length)
    {
        if($('#ETS_ODE_PASSWORD').val()!='')
            $('#ETS_ODE_PASSWORD').attr('type','password');
        else
            $('#ETS_ODE_PASSWORD').attr('type','text');
        $(document).on('keyup','#ETS_ODE_PASSWORD',function(){
            if($(this).val()!='')
                $(this).attr('type','password');
            else
                $(this).attr('type','text');
        });
    } 
    if($('.ets-custom-payment-tab-delivery_label .shortcode').length)
    {
        $('.ets-custom-payment-tab-delivery_label .shortcode').attr('title',Click_to_copy_text);
        $(document).on('click','.ets-custom-payment-tab-delivery_label .shortcode',function(){
            $('.shortcode .copied').remove();
            value = $(this).html();
            var $temp = $("<input>");
            var $temp2= $('<span class="copied">'+Copied_text+'</span>');
            $("body").append($temp);
            $temp.val(value).select();
            document.execCommand("copy");
            $(this).append($temp2);
            setTimeout(function(){$temp2.remove();},2000);
            $temp.remove();
        });
    }
    $(document).on('click','.delelte_exported_fields',function(){
        $('input[value="'+$(this).data('val')+'"]').removeAttr('checked');
        ets_ode.exported_fields($('input[value="'+$(this).data('val')+'"]')); 
    });
    
    $('button[name="saveOrderExport"]').click(function(e){
        e.preventDefault();
        tinyMCE.triggerSave();
        $(this).addClass('loading'); 
        var $this = $(this);
        var formData = new FormData($this.parents('form').get(0));
        formData.append('saveOrderExport', 1);
        formData.append('ajax', 1);
        var url_ajax= $this.closest('#ets_export_order_rule_form').attr('action');
        $('.bootstrap .page_error').remove();
        $.ajax({
            url: url_ajax,
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $this.removeClass('loading');
                if(json.success)
                {
                    
                    $.growl.notice({ message: json.success });
                    $('input[name="id_ets_export_order_rule"]').val(json.id_ets_export_order_rule);
                    window.location.href = json.redirect;
                }
                if(json.error)
                {
                    $('form').closest('.row').before(json.error);
                    $('.module_error').removeClass('module_error').addClass('page_error');
                }
            },
            error: function(xhr, status, error)
            {     
            }
        });
    });
    $('.ets-custom-payment-tab-information').addClass('active');
    $('.config_tab_information').addClass('active');
    $('.confi_tab').click(function () {
        $('.ets-form-group').removeClass('active');
        $('.ets-custom-payment-tab-' + $(this).data('tab-id')).addClass('active');
        $('.confi_tab').removeClass('active');
        $(this).addClass('active');
        $('input[name="current_tab"]').val($(this).data('tab-id'));
        if($('.confi_tab.active').data('tab-id')=='settings')
        {
            $('select[id$=_schedule]').each(function(){
                var parentItem = $(this).attr('id').replace('schedule', '');
                if ($('select[id*=_time_]').length > 0)
                {
                    if($(this).val()=='hourly' || $(this).val()=='5_minutes' || $(this).val()=='30_minutes')
                        $('.ets-form-group.form_'+parentItem+'time').removeClass('active');
                    else
                        $('.ets-form-group.form_'+parentItem+'time').addClass('active');
                    if($(this).val()=='weekly' || $(this).val()=='monthly')
                        $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').show();
                    else
                        $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').hide();
                }
            });
        }
    });
    if($('input[name="current_tab"]').val()!='')
    {
        $('.ets-form-group').removeClass('active');
        $('.ets-custom-payment-tab-' + $('input[name="current_tab"]').val()).addClass('active');
        $('.confi_tab').removeClass('active');
        $('.confi_tab[data-tab-id="'+$('input[name="current_tab"]').val()+'"]').addClass('active');
        if($('.confi_tab.active').data('tab-id')=='settings')
        {
            $('select[id$=_schedule]').each(function(){
                var parentItem = $(this).attr('id').replace('schedule', '');
                if ($('select[id*=_time_]').length > 0)
                {
                    if($(this).val()=='hourly' || $(this).val()=='5_minutes' || $(this).val()=='30_minutes')
                        $('.ets-form-group.form_'+parentItem+'time').removeClass('active');
                    else
                        $('.ets-form-group.form_'+parentItem+'time').addClass('active');
                    if($(this).val()=='weekly' || $(this).val()=='monthly')
                        $('select[id^=' + parentItem + '][id*=_time_][data-time="daily"]').addClass('active');
                    if($(this).val()=='weekly' || $(this).val()=='monthly')
                        $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').show();
                    else
                        $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').hide();
                }
            });
        }
    }
    $('.ets-custom-payment-tab-cronjob').show();
    $('.setting_tab_cronjob').addClass('active');
    $('.ode_setting_tab').click(function () {
        $('.ets-form-group').hide();
        $('.ets-custom-payment-tab-' + $(this).data('tab')).show();
        $('.ode_setting_tab').removeClass('active');
        $(this).addClass('active');
        if($(this).data('tab')=='manager_form')
            $('button[name="saveConfig"]').hide();
        else
            $('button[name="saveConfig"]').show();
    });
    if ($('#content .bootstrap .alert.alert-success').length > 0) {
        setTimeout(function () {
            $('#content .bootstrap .alert.alert-success').hide();
        }, 3500);
    }
    /*end init*/

    ets_ode.search();
    $(document).on('click', '.ets_ode_block_item_close', function () {
        if ($(this).parent('li').data('id') != '') {
            ets_ode.removeProduct($(this).parents('li').data('id'));
        }
    });
    ets_ode.server1();
    ets_ode.server2();
    $('input[name=export_to_server1]').change(function () {
        ets_ode.server1();
    });
    $('input[name=export_to_server2]').change(function () {
        ets_ode.server2();
    });
    $('input[name=global_ftp]').change(function () {
        ets_ode.global_ftp();
    });
    $('select[id$=_schedule]').change(function () {
        var parentItem = $(this).attr('id').replace('schedule', '');
        if ($('select[id*=_time_]').length > 0)
        {
            if($(this).val()=='hourly' || $(this).val()=='5_minutes' || $(this).val()=='30_minutes')
                $('.ets-form-group.form_'+parentItem+'time').removeClass('active');
            else
                $('.ets-form-group.form_'+parentItem+'time').addClass('active');
            $('select[id^=' + parentItem + '][id*=_time_]').removeClass('active');
            var childItem = $('select[id^=' + parentItem + '][id*=_time_][data-time=' + $(this).val() + ']').addClass('active');
            if($(this).val()=='weekly' || $(this).val()=='monthly')
                $('select[id^=' + parentItem + '][id*=_time_][data-time="daily"]').addClass('active');
            $('#' + parentItem + 'time').val(childItem.val());
            if($(this).val()=='weekly' || $(this).val()=='monthly')
                $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').show();
            else
                $('.ets-form-group.form_'+parentItem+'time .ets_mp_hour_at').hide();
        }
    });
    
    $('select[id*=_time_]').change(function () {
        var pseudo_code = $(this).attr('id').replace(/_hours|_weeks|_months/g, '');
        if (pseudo_code != '' && $('#' + pseudo_code).length > 0) {
            $('#' + pseudo_code).val($(this).val());
        }
    });
    $('input[name^=ETS_ODE_PERMISSION_ORDER]').click(function (ev) {
        if ($(this).is('.disabled')) {
            ev.preventDefault();
            return false;
        }
    });

    $('input[name="send_file_via_email"]').click(function () {
        displayFieldCustomFrom();
    })
    $('input[name="date_type"]').click(function () {
        displayFieldCustomFrom();
    });
    if ($('#exported_fields').val())
        $('#exported_list_fields li.no-selected').hide();
    else
        $('#exported_list_fields li.no-selected').show();
    $('#exported_fields').change(function () {
        if ($(this).val())
            $('#exported_list_fields li.no-selected').hide();
        else
            $('#exported_list_fields li.no-selected').show();
    });
    $(document).on('click', '.delete-customer-id', function () {
        var customer_id = $(this).attr('data-id');
        $(this).closest('li').remove();
        var customerIds = $('#specific_customer').val().split(",");
        if (customerIds.indexOf(customer_id) != -1)
            customerIds.remove(customer_id);
        $('#specific_customer').val(customerIds);
        displayFieldCustomFrom();
    });
    sortFields();
    var $myPayment = $("#payment-list");
    $myPayment.sortable({
        opacity: 0.6,
        cursor: "move",
        update: function () {
            var order = $(this).sortable("serialize") + "&action=updatePaymentOrdering";
            $.post("", order);
        },
        stop: function (event, ui) {
            showSuccessMessage("Saved!");
        }
    });
    $myPayment.hover(
        function () {
            $(this).css("cursor", "move");
        },
        function () {
            $(this).css("cursor", "auto");
        }
    );
    SearchCustomer();
    $('.customer_list_id .col-lg-9').append($('#customerlist').html());


    if ($('select[multiple="multiple"]').length > 0) {
        $('select[multiple="multiple"] option[value="all"]:selected').each(function () {
            $(this).parents('select').find('option').prop('selected', true);
        });
    }
    $('select[multiple="multiple"]').change(function () {
        if ($(this).val() && ($(this).val().indexOf('all') !== -1 || ($(this).val().length == $(this).find('option').length - 1))) {
            $(this).find('option').prop('selected', true);
        }
    });

    ets_ode.group_all();
    $(document).on('click', '.exported_fields', function (e) {
        ets_ode.exported_fields($(this));
    });
    displayFieldCustomFrom();

    $('.ets_ode_gencode').click(function (ev) {
        ev.preventDefault();
        ets_ode.gencode($('#ETS_ODE_CRONJOB_TOKEN'), 10);
    });

    if ($('.ets_ode_rules').length > 0) {
        $('.ets_ode_rules').hide();
    }
    $('.ets_ode_rule_toggle').click(function () {
        if ($('.ets_ode_rules').length > 0 && $('.ets_ode_rules').is(':hidden')) {
            $('.ets_ode_rules').slideDown();
        } else  {
            $('.ets_ode_rules').slideUp();
        }
    });
    $(document).on('click','.clear-log-cronjob',function(){
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading');
            var $this = $(this);
            $.ajax({
                url: '',
                data: 'clear_log_cronjob=1',
                type: 'post',
                dataType: 'json',
                success: function(json){
                    
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $this.prev('textarea').val('');
                    }
                    if(json.error)
                        $.growl.error({ message: json.error });
                        
                },
                error: function(xhr, status, error)
                { 
                     $this.removeClass('loading'); 
                }
            });
        }
    });
    $(document).on('click','.change_permistion_ordermanager',function(){
        var perm= $(this).data('perm');
        var id_profile = $(this).data('id_profile');
        var enabled = $(this).is(':checked')  ? 1  :0;
        if($(this).hasClass('all'))
        {
            if(enabled)
                $(this).parent().parent().find('.change_permistion_ordermanager').prop('checked',true);
            else
                $(this).parent().parent().find('.change_permistion_ordermanager').prop('checked',false);
        }
        else
        {
            if(enabled)
            {
                if($(this).parent().parent().find('.change_permistion_ordermanager:checked').length == $(this).parent().parent().find('.change_permistion_ordermanager').length - 1)
                    $(this).parent().parent().find('.change_permistion_ordermanager.all').prop('checked',true);
            }
            else
                $(this).parent().parent().find('.change_permistion_ordermanager.all').prop('checked',false);
        }
        $.ajax({
            url: '',
            data: 'savaOrderManagerConfig=1&id_profile='+id_profile+'&perm='+perm+'&enabled='+enabled,
            type: 'post',
            dataType: 'json',
            success: function(json){
                $.growl.notice({ message: json.success });
            },
            error: function(xhr, status, error)
            { 
                  
            }
        });
    });
    $(document).on('click','.btn-run-cronjob',function(e){
        e.preventDefault();
        if(!$(this).hasClass('loading'))
        {
            var $this = $(this);
            $(this).addClass('loading');
            $.ajax({
                url: '',
                data: 'ajax=1&runCronJob=1',
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if(json.success)
                        $.growl.notice({ message: json.success});
                    else if(json.error)
                        $.growl.error({ message: json.error});
                    else
                        $.growl.notice({ message: json.success});
                    $this.removeClass('loading');
                },
                error: function(xhr, status, error)
                {  
                    $this.removeClass('loading');
                }
            });
        }
        
    });
    $('button[name="saveConfig"]').click(function(e){
        e.preventDefault();
        tinymce.triggerSave();
        if(!$(this).hasClass('loading'))
        {
            $(this).addClass('loading'); 
            var $this = $(this);
            var formData = new FormData($this.parents('form').get(0));
            formData.append('ajax', 1);
            var url_ajax= $this.closest('#module_form').attr('action');
            $('.bootstrap .page_error').remove();
            $.ajax({
                url: url_ajax,
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        $.growl.notice({ message: json.success });
                        $('.form_ets_ode_cronjob_token .ets_ode_help_block').html(json.cronjob_help_block);
                        if($('#ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER').val())
                            size_header_formats[$('#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT').val()] = $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER').val();
                        if($('#ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER').val())
                            size_footer_formats[$('#ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT').val()] = $('#ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER').val();
                    }
                    if(json.error)
                    {
                        $('form').closest('.row').before(json.error);
                        $('.module_error').removeClass('module_error').addClass('page_error');
                    }
                },
                error: function(xhr, status, error)
                {     
                }
            });
        }
    });
        
});

Array.prototype.remove = function () {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

function SearchCustomer() {
    
    if($('#specific_customer_select').length)
    {
        var input = $('#specific_customer_select');
        $(input).autocomplete(ets_odm_link_customer_search, {
            minChars: 1,
            autoFill: true,
            max: 20,
            matchContains: true,
            mustMatch: true,
            scroll: false,
            cacheLength: 0,
            formatItem: function (item) {
                return item[1] + ' ' + item[2] + ' (' + item[3] + ')';
            }
        }).result(ybcAddAccessory);
    }
}

var ybcAddAccessory = function (event, data, formatted) {
    if (data == null)
        return false;
    var customerId = data[0];
    var customerFirstname = data[1];
    var customerLastName = data[2];
    var customerEmail = data[3];
    if (!$('#specific_customer').val()) {
        $('#specific_customer').val(customerId);
        if($('.customer_list_id .col-lg-9 ul').length >0)
            $('.customer_list_id .col-lg-9 ul').append('<li><span class="customer-name">' + customerFirstname + ' ' + customerLastName + ' (' + customerEmail + ')</span> <span class="delete-customer-id" data-id="' + customerId + '">delete</span></li>');
        else
            $('.customer_list_id .col-lg-9').append('<ul><li><span class="customer-name">' + customerFirstname + ' ' + customerLastName + ' (' + customerEmail + ')</span> <span class="delete-customer-id" data-id="' + customerId + '">delete</span></li></ul>');
        displayFieldCustomFrom();
    }
    else {
        var customerIds = $('#specific_customer').val().split(",");
        if (customerIds.indexOf(customerId) == -1) {
            $('#specific_customer').val($('#specific_customer').val() + ',' + customerId);
            if($('.customer_list_id .col-lg-9 ul').length >0)
                $('.customer_list_id .col-lg-9 ul').append('<li ><span class="customer-name">' + customerFirstname + ' ' + customerLastName + ' (' + customerEmail + ')</span> <span class="delete-customer-id" data-id="' + customerId + '">delete</span></li>');
            else
                $('.customer_list_id .col-lg-9').append('<ul><li ><span class="customer-name">' + customerFirstname + ' ' + customerLastName + ' (' + customerEmail + ')</span> <span class="delete-customer-id" data-id="' + customerId + '">delete</span></li></ul>');
            displayFieldCustomFrom();
        }
    }
    $('#specific_customer_select').val('');
    $('#specific_customer_select').focus();
};

function hideOtherCurrency(id_currency) {
    $('.currency-field').hide();
    $('.currency-' + id_currency).show();
}

function displayFieldCustomFrom() {

    if ($('#date_type_from_to').is(':checked')) {
        $('.form-group.date_from_to').show();
    }
    else
        $('.form-group.date_from_to').hide();

    if ($('#the_day_before').is(':checked')) {
        $('.form-group.the_day_before').show();
    } else {
        $('.form-group.the_day_before').hide();
    }
    
    if ($('#specific_customer').val())
        $('.form-group.customer_form').hide();
    else
        $('.form-group.customer_form').show();

    if ($('#send_file_via_email_on').is(':checked')) {
        $('.form-group.send_mail').show();
    }
    else {
        $('.form-group.send_mail').hide();
    }
}

function sortFields() {
    var $myfield = $("#list_fields");
    if($myfield.length >0)
    {
        $myfield.sortable({
            axis: 'y',
            update: function (event, ui) {
                var data = $(this).sortable("serialize") + "&action=updateFieldsOrdering";
                $.ajax({
                    data: data,
                    type: 'POST',
                    url: window.location.url,
                    success:function(json) {
                        $('#exported_fields').val(json);
                        $('#exported_fields').change();
                        //showSuccessMessage("Saved!");
                    }
                });
            }
        });
        $myfield.hover(function () {
                $(this).css("cursor", "move");
            },
            function () {
                $(this).css("cursor", "auto");
            });
    }
    
}