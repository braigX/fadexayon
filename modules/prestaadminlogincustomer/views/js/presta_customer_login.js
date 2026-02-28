/**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 */

$(document).on('click', '#dropdown_presta_customer_header', function (e) {
    e.stopPropagation();
});

$(document).on('keyup', '#presta_customer_search', function(){
    var str = $(this).val();
    if (str.length > 2) {
        $.ajax({
            url : presta_customer_search,
            cache : false,
            type : 'POST',
            data : {
                ajax : true,
                action : 'searchCustomer',
                string : str
            },
            beforeSend: function(){
                $('.presta_loader_img').removeClass('prestahide');
               },
            success : function(data) {
                $('#presta_searched_result').empty();
                if (data) {
                    result = JSON.parse(data);
                    $.each(result, function(i, item) {
                        $("#presta_searched_result").append('<li class="clearfix"><span>(#'+item.id_customer +')' + ' ' +item.fname + ' ' +item.lname + ' </span> <span style="margin-left:10px;" class="icon-envelope-o"> <a href="'+ presta_customer_search +'&logincustomer=1&id_customer='+item.id_customer +'" target="_blank" class="btn btn-primary" value="' + item.id_customer + '">'  + item.email +'</a></li>');
                    });
                } else {
                    $('#presta_searched_result').text('No customer found');
                }
            },
            complete : function() {
                $('.presta_loader_img').addClass('prestahide');
            }
        });
    }
});

$(window).load(function() {
    // on window load check view more option is checked or not
    if ($("input[name='PRESTA_CUSTOMER_AS_LOGIN']:checked").val() == '1') {
        $('.presta_check table input[type=checkbox]').removeAttr('disabled','true');
    } else{
        $('.presta_check table input[type=checkbox]').attr('disabled', 'true');
    }

    if ($("input[name='PRESTA_CUSTOMER_LOGIN_HISTORY']:checked").val() == '1') {
        $('.presta_history_no').show('slow');
    } else{
        $('.presta_history_no').hide('slow');
    }
});

$(document).on('change', 'input[name=PRESTA_CUSTOMER_AS_LOGIN]', function() {
    if ($(this).attr('value') == 1){
        $('.presta_check table input[type=checkbox]').removeAttr('disabled');
    } else {
        $('.presta_check table input[type=checkbox]').attr('disabled', 'true');
    }
});

$(document).on('change', 'input[name=PRESTA_CUSTOMER_LOGIN_HISTORY]', function() {
    if ($(this).attr('value') == 1){
        $('.presta_history_no').show('slow');
    } else {
        $('.presta_history_no').hide('slow');
    }
});
