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

$(document).ready(function(){ 
    if($('.ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').length >0)
    {
        $('.ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').each(function(){
            if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                $(this).hide();   
        });
    }
    if($('.ets_crosssell_tab_content .list-content').length==1)
    {
        $('.ets_crosssell_tab_content .list-content').each(function(){
            if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
            {
                $(this).hide();
                $('ul.ets_crosssell_nav_tabs').hide();
            } 
        });
    }
    if(ets_crosssell_16 && $('#product .pb-center-column .ets_crosssell_block').length)
    {
        $('#product .pb-right-column').after($('#product .pb-center-column .ets_crosssell_block').clone());
        $('#product .pb-center-column .ets_crosssell_block').remove();
    }
    if(ets_crosssell_16 && $('#order-confirmation .cart_navigation').length && $('.ets_crosssell_block').length)
    {
        $('#order-confirmation .cart_navigation').before($('.ets_crosssell_block').clone());
        $('#order_step').next('.ets_crosssell_block').remove();
    } 
    ets_crosssell_slide(true);
    $(document).on('click','.ets_crosssell_tab',function(e){
        e.preventDefault();
        var page_name = $(this).data('page');
        var tab_name = $(this).data('tab');
        if($($(this).attr('href')).length==0 || $('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sub_category.active').length>0)
        {
            if(!$('.ets_crosssell_'+page_name).hasClass('loading'))
            {
                var id_product = $(this).data('id_product');
                $('#'+page_name+'-tabs li').removeClass('active');
                $(this).parent().addClass('active');
                $('.ets_crosssell_'+page_name).addClass('loading');
                $.ajax({
                    url: '',
                    data: {
                        getCrosssellContent:1,
                        page_name :page_name,
                        tab : tab_name,
                        ajax: 1,
                        id_product:id_product,
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function(json){
                        $('#'+page_name+'-contents.ets_crosssell_tab_content .list-content').removeClass('active');
                        $('.ets_crosssell_'+page_name).removeClass('loading');
                        if($('#tab-content-'+page_name+'-'+tab_name).length==0)
                            $('.ets_crosssell_'+page_name+' .ets_crosssell_tab_content').append('<div id="tab-content-'+page_name+'-'+tab_name+'" class="list-content active">'+json.product_list+'</div>');
                        else{
                            $('#tab-content-'+page_name+'-'+tab_name).addClass('active').html(json.product_list);
                        }


                        if($('#tab-content-'+page_name+'-'+tab_name+' .ets_cs_sub_categories').length>0)
                            $('#tab-content-'+page_name+'-'+tab_name).addClass('ets_crosssell_has_sub');
                        ets_crosssell_slide(false);
                    },
                    error: function(xhr, status, error)
                    {
                        $(this).removeClass('loading');
                    }
                });
            }
        }
        else
        {
            $('#'+page_name+'-contents.ets_crosssell_tab_content .list-content').removeClass('active');
            $('#'+page_name+'-tabs li').removeClass('active');
            $(this).parent().addClass('active');
            $($(this).attr('href')).addClass('active');
        }
   });
   $(document).on('click','.ets_crosssell_list',function(e){
        e.preventDefault();
        var page_name = $(this).data('page');
        var tab_name = $(this).data('tab');
        if($('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sub_category.active').length>0)
        {
            if(!$('#tab-content-'+page_name+'-'+tab_name).hasClass('loading'))
            {
                var id_product = $(this).data('id_product');
                $('#'+page_name+'-tabs li').removeClass('active');
                $(this).parent().addClass('active');
                $('#tab-content-'+page_name+'-'+tab_name).addClass('loading');
                $.ajax({
                    url: '',
                    data: {
                        getCrosssellContent:1,
                        page_name :page_name,
                        tab : tab_name,
                        ajax: 1,
                        id_product:id_product,
                    },
                    type: 'post',
                    dataType: 'json',
                    success: function(json){
                        $('.ets_crosssell_tab_content .list-content').removeClass('active');
                        $('#tab-content-'+page_name+'-'+tab_name).removeClass('loading');
                        if($('#tab-content-'+page_name+'-'+tab_name).length==0)
                            $('.ets_crosssell_'+page_name+' .ets_crosssell_tab_content').append('<div id="tab-content-'+page_name+'-'+tab_name+'" class="list-content active">'+json.product_list+'</div>');
                        else
                            $('#tab-content-'+page_name+'-'+tab_name).addClass('active').html(json.product_list);
                        if($('#tab-content-'+page_name+'-'+tab_name+' .ets_cs_sub_categories').length>0)
                            $('#tab-content-'+page_name+'-'+tab_name).addClass('ets_crosssell_has_sub');
                        ets_crosssell_slide(false);
                    },
                    error: function(xhr, status, error)
                    {
                        $('#tab-content-'+page_name+'-'+tab_name).removeClass('loading');
                    }
                });
            }
        }
   });
   $(document).ajaxComplete(function( event, xhr, settings ) {
      if(settings.data && typeof settings.data !='object' &&  (settings.data.indexOf('action=add-to-cart')>=0 || (settings.data.indexOf('controller=cart')>=0 && settings.data.indexOf('add=1')>=0) )&& settings.data.indexOf('getProductPopupAdded')==-1)
      {
            if ( $('#blockcart-modal').prev('.modal-backdrop.in').length ){
                $('#blockcart-modal').prev('.modal-backdrop.in').remove();
            }
            var data_post = settings.data.replace("controller", "page_controller");
            $.ajax({
                url: '',
                data: 'getProductPopupAdded=1&ajax=1&'+data_post,
                type: 'post',
                dataType: 'json',
                success: function(json){
                    if($('#blockcart-modal').length)
                    {
                        $('#blockcart-modal').addClass('loaded_crossell');
                        if ( $('#blockcart-modal').prev('.modal-backdrop.in').length ){
                            $('#blockcart-modal').prev('.modal-backdrop.in').remove();
                        }
                        if($('#blockcart-modal .ets_crosssell_block').length<=0)
                            $('#blockcart-modal .modal-body').append(json.product_lists);
                        else
                            $('#blockcart-modal .ets_crosssell_block').replaceWith(json.product_lists);
                        if($('#blockcart-modal .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').length >0)
                        {
                            $('#blockcart-modal .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').each(function(){
                                if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                                    $(this).hide();   
                            });
                        }
                        if($('#blockcart-modal .ets_crosssell_tab_content .list-content').length==1)
                        {
                            $('#blockcart-modal .ets_crosssell_tab_content .list-content').each(function(){
                                if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                                {
                                    $(this).hide();
                                    $('#blockcart-modal ul.ets_crosssell_nav_tabs').hide();
                                } 
                            });
                        }
                        
                    }
                    else
                    {
                        if($('#layer_cart').length)
                        {
                            if($('#layer_cart .ets_crosssell_block').length<=0)
                                $('#layer_cart > .clearfix').append(json.product_lists);
                            else
                                $('#layer_cart .ets_crosssell_block').replaceWith(json.product_lists);
                        }
                        if($('#layer_cart .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').length >0)
                        {
                            $('#layer_cart .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').each(function(){
                                if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                                    $(this).hide();   
                            });
                        }
                        if($('#layer_cart .ets_crosssell_tab_content .list-content').length==1)
                        {
                            $('#layer_cart .ets_crosssell_tab_content .list-content').each(function(){
                                if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                                {
                                    $(this).hide();
                                    $('#layer_cart ul.ets_crosssell_nav_tabs').hide();
                                } 
                            });
                        }
                    }
                    ets_crosssell_slide(false);
                    
                },
                error: function(xhr, status, error)
                {
                    $(this).removeClass('loading');
                }
            });
      }
      if(xhr.responseText && xhr.responseText.indexOf("quickview_html")>=0)
      { 
            setTimeout(function(){ets_crosssell_slide(false);},500);
            
            if($('.quickview .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').length >0)
            {
                $('.quickview .ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').each(function(){
                    if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                        $(this).hide();   
                });
            }
            if($('.quickview .ets_crosssell_tab_content .list-content').length==1)
            {
                $('.quickview .ets_crosssell_tab_content .list-content').each(function(){
                    if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                    {
                        $(this).hide();
                        $('.quickview ul.ets_crosssell_nav_tabs').hide();
                    } 
                });
            }
            
      }
    });
    if(!ets_crosssell_16 && $('.cart-grid-body .ets_crosssell_block').length >0)
    {
        $('.cart-grid.row').after($('.cart-grid-body .ets_crosssell_block').clone());
        $('.cart-grid-body .ets_crosssell_block').remove();
        ets_crosssell_slide(false);
    }
    if(ets_crosssell_16 && ($('body#category').length||$('body#cms').length||$('body#contact').length))
    {
        $.ajax({
            url: '',
            data: 'getProductExtraPage=1&ajax=1',
            type: 'post',
            dataType: 'json',
            success: function(json){
                $('#center_column').append(json.product_lists);
                ets_crosssell_slide(false);
                if($('.ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').length >0)
                {
                    $('.ets_crosssell_block.layout_list li.ets_crosssell_list_blocks').each(function(){
                        if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                            $(this).hide();   
                    });
                }
                if($('.ets_crosssell_tab_content .list-content').length==1)
                {
                    $('.ets_crosssell_tab_content .list-content').each(function(){
                        if($(this).find('.product-miniature').length==0 && $(this).find('.product-container').length==0)
                        {
                            $(this).hide();
                            $('ul.ets_crosssell_nav_tabs').hide();
                        } 
                    });
                }
            },
            error: function(xhr, status, error)
            {
                
            }
        });
    }
    $(document).on('change','.ets_crosssel_sort_by',function(){
        var page_name = $(this).data('page');
        var tab_name = $(this).data('tab');
        var id_product = $(this).data('id_product');
        var sort_by = $(this).val();
        if(!$('#tab-content-'+page_name+'-'+tab_name).hasClass('loading'))
        {
            $('#tab-content-'+page_name+'-'+tab_name).addClass('loading');
            if($('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sub_category.active').length>0)
            {
                var id_ets_css_sub_category =$('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sub_category.active').data('id_category')
            }
            else
                var id_ets_css_sub_category = 0;
            $.ajax({
                url: '',
                data: {
                    sortProductsCrosssellContent:1,
                    page_name :page_name,
                    tab : tab_name,
                    ajax: 1,
                    id_product:id_product,
                    sort_by : sort_by,
                    id_ets_css_sub_category : id_ets_css_sub_category
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $('#tab-content-'+page_name+'-'+tab_name).removeClass('loading');
                    $('#tab-content-'+page_name+'-'+tab_name).html(json.product_list);
                    ets_crosssell_slide(false);
                },
                error: function(xhr, status, error)
                {
                    $(this).removeClass('loading');
                }
            });
        }
        
    });
    $(document).on('click','.ets_crosssel_sub_category',function(){
        var page_name = $(this).data('page');
        var tab_name = $(this).data('tab');
        var id_product = $(this).data('id_product');
        if($('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sort_by').length>0)
            var sort_by = $('#tab-content-'+page_name+'-'+tab_name+' .ets_crosssel_sort_by').val();
        else
            var sort_by = $(this).val();
        if(!$('#tab-content-'+page_name+'-'+tab_name).hasClass('loading'))
        {
            
            $('#tab-content-'+page_name+'-'+tab_name).addClass('loading');
            var id_ets_css_sub_category = $(this).data('id_category')
            $.ajax({
                url: '',
                data: {
                    sortProductsCrosssellContent:1,
                    page_name :page_name,
                    tab : tab_name,
                    ajax: 1,
                    id_product:id_product,
                    sort_by : sort_by,
                    id_ets_css_sub_category : id_ets_css_sub_category
                },
                type: 'post',
                dataType: 'json',
                success: function(json){
                    $('#tab-content-'+page_name+'-'+tab_name).removeClass('loading');
                    $('#tab-content-'+page_name+'-'+tab_name).html(json.product_list);
                    ets_crosssell_slide(false);
                },
                error: function(xhr, status, error)
                {
                    $(this).removeClass('loading');
                }
            });
        }
        return false;
    });
});
function ets_crosssell_slide(check_cart)
{
    if(check_cart && $('.crosssell_product_list_wrapper').hasClass('cart_page'))
        return false;
    if($('.crosssell_product_list_wrapper.layout-slide:not(.slick-slider):not(.product_list_16)').length >0)
    {
       $('.crosssell_product_list_wrapper.layout-slide:not(.slick-slider):not(.product_list_16)').slick({
          slidesToShow: nbItemsPerLine,
          slidesToScroll: 1,
          arrows: true,
          responsive: [
              {
                  breakpoint: 1199,
                  settings: {
                      slidesToShow: nbItemsPerLine
                  }
              },
              {
                  breakpoint: 992,
                  settings: {
                      slidesToShow: nbItemsPerLineTablet
                  }
              },
              {
                  breakpoint: 768,
                  settings: {
                      slidesToShow: nbItemsPerLineMobile
                  }
              },
              {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 1
                  }
              }
           ]
       });
   }
   
   if($('.crosssell_product_list_wrapper.layout-slide.product_list_16:not(.slick-slider) .product_list').length >0)
    {
       $('.crosssell_product_list_wrapper.layout-slide.product_list_16:not(.slick-slider) .product_list').slick({
          slidesToShow: nbItemsPerLine,
          slidesToScroll: 1,
          arrows: true,
          responsive: [
              {
                  breakpoint: 1199,
                  settings: {
                      slidesToShow: nbItemsPerLine
                  }
              },
              {
                  breakpoint: 992,
                  settings: {
                      slidesToShow: nbItemsPerLineTablet
                  }
              },
              {
                  breakpoint: 768,
                  settings: {
                      slidesToShow: nbItemsPerLineMobile
                  }
              },
              {
                  breakpoint: 480,
                  settings: {
                    slidesToShow: 1
                  }
              }
           ]
       }); 
       
   }
   if ( $('.quickview').prev('.modal-backdrop.in').length ){
        $('.quickview').prev('.modal-backdrop.in').remove();
   }
   if ( $('.quickview').prev('.modal.fade.quickview.in').length ){
        $('.quickview').prev('.modal.fade.quickview.in').remove();
   }
   if ( $('.quickview').prev('.modal.fade').length ){
        $('.quickview').prev('.modal.fade').remove();
   }
   $( ".quick_view_page .product-miniature" ).each(function( ) {
      var flag_online_height = $(this).find('.product-flag.online-only').height();
      var space_top_height = $(this).find('a.thumbnail.product-thumbnail').height() - flag_online_height - 15;
      $(this).find('.product-flag.online-only').css('top',space_top_height+'px');
   });
}