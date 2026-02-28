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

    $(document).on('click', '.idxrcustomproduct_send', function(){
        
        $id = $(this).attr('id');
        $row = $id.split('_');
        $row_id = $row[2];
        data = new Array();
        
        data['product_id'] = $('#icp_info_'+$row_id).attr('data-productid');
        data['attribute_id'] = 0;
        data['customization'] = $('#icp_info_'+$row_id).attr('data-customization').replace(/\-/g, "_");
        
        $.post( url_ajax, { action: "createproduct", product: data['product_id'], attribute: data['attribute_id'], custom: data['customization']} )
            .done(function( data ) {
                if (typeof ajaxCart !== 'undefined') {
                    ajaxCart.add(data, null, true, $(this),$('#quantity_wanted').val());
                }
                if (typeof prestashop !== 'undefined') {
                    id_product = data;
                    id_product_attribute = 0;                    
                    var quantity = 1;
                    if ($('#quantity_wanted').length) {
                         quantity = $('#quantity_wanted').val();
                    }
                    if ($('#idxrcustomprouct_quantity_wanted').length) {
                         quantity = $('#idxrcustomprouct_quantity_wanted').val();
                    }
                    
                    $['ajax']({
                        type: 'POST',
                        headers: { "cache-control": "no-cache" },
                        cache: false,
                        url: prestashop['urls']['pages']['cart'] + '?rand=' + new Date()['getTime'](),
                        async: true,
                        cache: false,
                        dataType: 'json',
                        data: 'action=update&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + data + '&token=' + prestashop['static_token'] + '&ipa=0',
                        success: function() {
                            prestashop['emit']('updateCart', {
                                reason: {
                                    idProduct: id_product,
                                    idProductAttribute: id_product_attribute,
                                    linkAction: 'add-to-cart'
                                },
                                resp: {
                                    cart: {
                                        products: [{
                                            id_product: data,
                                            name: $('.product-detail-name').html(),
                                            quantity: $('#quantity_wanted').val(),
                                            price_wt: $('#js_resume_total_price').html()
                                        }]
                                    },
                                    success: true
                                }
                            });
                        }
                    });
                }              
            });
    });
    
    
    $(document).on('click', '.idxrcustomproduct_view', function(){
        
        $id = $(this).attr('id');
        $row = $id.split('_');
        $row_id = $row[2];
        
        $url = $('#icp_info_'+$row_id).attr('data-url')+'?icp='+$('#icp_info_'+$row_id).attr('data-customization').replace(/\-/g, "_").replace(/&amp;/g,"+");
       
        window.location.replace($url);//Falta la customizacion
    });
    
    $(document).on('click', '.idxrcustomproduct_del', function(){
        
        if(!confirm(confirm_text)){
            return true;
        }
        
        $id = $(this).attr('id');
        $row = $id.split('_');
        $row_id = $row[2];
        
        $fav_id = $('#icp_info_'+$row_id).attr('data-favid');
        
        $.post( url_ajax, { action: "deletefav", favid: $fav_id} );
        
        $('#idxrcustomproduct_panel_'+$row_id).remove();
    });
    
});