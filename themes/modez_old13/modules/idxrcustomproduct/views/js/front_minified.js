/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL
* @license   INNOVADELUXE
*/

$(document).ready(function() {
    
    init_selectpicker();
        
    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateProduct', function (params) {
            setTimeout(
            function() 
            {
              init_selectpicker();
            }, 500);
        });
    }
    
    $(document).on('change', '.minified_sel', function() {
        processOption($(this));
    });
        
    $('.minified_sel_img').on('changed.bs.select', function() {
        processOption($(this));
    });
    
    $('.minified_text').on('input', function(){
        processOption($(this));
    });
    
    $('.zoom').click(function(){
        var multiplesel = $(this).closest('.component_step').attr('data-multivalue');
        if (multiplesel == 'multi_simple' || multiplesel == 'multi_qty'){
            var image = $(this).find('.zoom-imagen');
            var target = image.attr('data-target');
            $(target).modal('show');
        }
    });

});

function init_selectpicker()
{
    if (!$.fn.selectpicker) {
        getSelectpickerCDN();
        return;
    }

    $('.minified_sel_img').selectpicker({
        style: 'btn-minified',
        size: 4
    });
    
    $('.minified_sel').selectpicker({
        style: 'btn-minified',
        size: 4
    });
    
    var color = $('#minified_color').val();
    $('.btn-minified').css('background', color);
    
    $('.component_step:not(#component_step_last)').each(function(index){
        var isLastElement = index == $('.component_step:not(#component_step_last)').length -1;
        set_min_default_value($(this), isLastElement);
    });
    
    var status_string = get_status_string();
    refreshProductImage(status_string);
}

function getSelectpickerCDN()
{
    var btjs = document.createElement('script');
    btjs.src = 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js';
    document.body.appendChild(btjs);
    setTimeout(
    function() 
    {
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js';
        document.body.appendChild(s);
        setTimeout(
        function() 
        {
            init_selectpicker();
        },1000);
    }, 1000);
}

function set_min_default_value(step, update_final_price = true)
{
    var default_value = step.attr('data-default');
    if(typeof default_value != 'undefined' && default_value != -1 ){
        var comp_id = step.attr('id').replace('component_step_','');
        if ($("#text_"+comp_id).length > 0){
            $("#text_"+comp_id).val(default_value);
        } else {
            processOption($('#option_'+comp_id), update_final_price);
        }
    }
}

function processOption(source, update_final_price = true) {
    var id = source.attr('id');
    if(id == undefined){
        return false;
    }        
    step_id = id.replace('option_','');
    step_type = source.attr('data-type');
    title = '';
    var option_id = false;
    $('[id^=resume_price_block_'+step_id+']').remove();
    if(step_type === 'textarea'){

    }else if(step_type === 'text'){
        option_id = 1;
        text = $('#option_'+step_id).val();
        if(text.length < 1){
            //mostrar error
        }        
        shortText = jQuery.trim(text);
        displayResumeTextLine(step_id,option_id,shortText);
        title = shortText;
        $('#js_opt_extra_'+step_id+'_value').html(text);
        $('#js_opt_'+step_id+'_value').html(option_id);
    }else{
        price_impact_total = 0;
        limpiaqtyselectextra();
        option_id = source.val();
        if (!$.isArray(option_id)){
            option_id = [option_id];
        }
        refreshImpact(step_id,option_id);
        option_text = '';
        sep_opt = '';
        $.each(option_id, function(i, val) {            
            option_text += sep_opt+val;
            sep_opt = '&';
            //price_impact_total is updated in idxminified_gettext
            displayResumeTextLine(step_id,val,idxminified_gettext(step_id,val));
            title = title.concat(idxminified_gettext(step_id,val,true)," ");
        });
        
        $('#js_resume_opt_'+step_id+'_price').val(Number(price_impact_total).toFixed(2));
        $('#js_opt_'+step_id+'_value').html(option_text);
        $('#js_opt_'+step_id+'_value_wqty').html(option_text);
    }
    
    if (update_final_price) {
        var total = 0;
        $('.js_resume_price').each(function(){
            if ($(this).parent().is(':visible')) {
                total = total + parseFloat($(this).val().replace(',',''));
            }
        });

        total = total + parseFloat($('.js_base_price').val().replace(',',''));
        discount = getMinifiedDiscount(total);
        if (discount) {
            total = total - discount;
        }
        $("#js_resume_total_price").val(total);
        var formatter = formatPrice(total);
        formatter.done(function(data){
            total_formated = jQuery.parseJSON(data);
            $("#resume_total_price").html(total_formated);
            $('#js_topblock_total_price').html(total_formated);
        });
    }
    
    checkconstraint(step_id+'_'+option_id);
    finish = true;
    $('.sel_opt').each(function() {
        if($(this).html() === 'false' && $(this).attr('data-required') === 'true'){
            finish = false;
        }
    });
    
    if (update_final_price) {
        var next_id = next_panel_id();
        open_next_panel(next_id);
    }
    
    var status_string = get_status_string();
    refreshProductImage(status_string);
    if(update_final_price && finish){
        if(es17 && $.isFunction($("#idxrcustomprouct_quantity_wanted").TouchSpin)){
            $("#idxrcustomprouct_quantity_wanted").TouchSpin({
                verticalbuttons: true
            });
        }else{
            if (show_fav) {
                favbutton_text = '<button class="btn btn-link" type="submit" id="idxrcustomproduct_save">'
                    +'<i class="fa fa-save icon icon-save"></i> '+favbutton
                +'</button>';
            } else {
                favbutton_text = '';
            }
            $('#submit_idxrcustomproduct').html(favbutton_text
                +'<span id="quantity_wanted_p" class="pull-right">'
                   +'<input min="'+min_qty+'" name="qty" id="quantity_wanted" class="text" value="'+min_qty+'" type="number">'
                   +'<span class="clearfix"></span>'
                +'</span>'
                +'<button class="btn btn-success pull-right" type="submit" id="idxrcustomproduct_send">'+send_text+'</button>'
            );
        }
        $('#submit_idxrcustomproduct_alert').hide();
        $('#idxrcustomproduct_save').prop("disabled",false);
        $('#idxrcustomproduct_send').prop("disabled",false);
        $('#idxrcustomproduct_topblock_send').removeClass('disabled');
        $('#idxrcustomproduct_topblock_send_message').remove();
    }else{
        $('#submit_idxrcustomproduct_alert').show();
        $('#idxrcustomproduct_topblock_send').addClass('disabled');
        $('#idxrcustomproduct_save').prop("disabled",true);
        $('#idxrcustomproduct_send').prop("disabled",true);
    }
    setTimeout(function() 
    {
        $('.btn-minified[data-id="option_'+step_id+'"]').attr('title',title);
        fixDropdowSelectors();
    }, 200);
}

function idxminified_gettext(step_id,option_id,simple = false) {
    qty = 1;
    if ($('.selected #option_'+step_id+'_'+option_id+'_qty').length && $('.selected #option_'+step_id+'_'+option_id+'_qty').val() > 1) {
        qty = $('.selected #option_'+step_id+'_'+option_id+'_qty').val();
    }
    if (!simple) {
    price_impact = $('#option_'+step_id+'_'+option_id).attr('data-price-impact')*qty;
    price_impact_total += price_impact;
    if(price_impact) {
        displayResumePriceLine(step_id,option_id,price_impact);
    }
    }
    
    if (qty > 1) {
        name = qty+'x'+$('#option_name_'+step_id+'_'+option_id).html();
    } else {
        name = $('#option_name_'+step_id+'_'+option_id).html();
    }

    if(simple) {
        return $.trim(name);
    }
    
    if(step_type === 'sel_img'){
        img = $('#option_'+step_id+'_'+option_id+'_img').attr('src');
        new_html = '<img class="img-element" src="'+img+'" alt="'+name+'"><span>'+name+'</span>';
    }else{
        new_html = '<span>'+name+'</span>';
    }

    return new_html;
}

function idxminified_getprice(step_id,option_id) {
    qty = 1;
    if ($('.selected #option_'+step_id+'_'+option_id+'_qty').length && $('.selected #option_'+step_id+'_'+option_id+'_qty').val() > 1) {
        qty = $('.selected #option_'+step_id+'_'+option_id+'_qty').val();
    }
    price_impact = $('#option_'+step_id+'_'+option_id).attr('data-price-impact')*qty;
                console.log(price_impact);
    return price_impact;
}

function limpiaqtyselectextra() {
    $('.btn-minified .minified_qty_block').remove();
}

function fixDropdowSelectors() {
    $('.dropdown-menu.open').each(function(){
        if(!$(this).is(":visible") && $(this).hasClass('show')) {
            $(this).removeClass('show');
        }
    });
}

function getMinifiedDiscount(total) {
    var range_discount = verifyAmountDiscount();
    var option_discount = 0;
//    $('.js_resume_price_wodiscount').each(function(){
//        var option_wod = $(this).val();
//        var option_wd = $(this).parent().find('.js_resume_price').val();
//        option_discount += (Number(option_wod) - Number(option_wd));
//    });
    
    if($("#idxcp_discount_type").length == 0 && !range_discount && !option_discount) {
        return 0;
    }
    var discount = 0;
    
    if($("#idxcp_discount_type").length && $("#idxcp_discount_type").val()) {
        var type = $("#idxcp_discount_type").val();
        var amount = parseFloat($("#idxcp_discount_amount").val());
        
        if (type == 'fixed' || type == 'amount') {
            discount = amount;
        } else {
            discount = (parseFloat(total-option_discount)*(amount/100));
        }
    }
        
    if (range_discount) {
        if (range_discount.reduction_type == 'amount') {
            discount += range_discount.reduction;
        } else {
            discount += (parseFloat(total-option_discount)*(range_discount.reduction));
        }
    }
    
    if (option_discount) {
        discount += option_discount;
    }

    if (discount) {
        $('#idxcp_discount_value').html(Number(discount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#idxcp_discount_value').closest('tr').show();
        return discount;
    } else {
        $('#idxcp_discount_value').closest('tr').hide();
        return false;
    }
}