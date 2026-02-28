/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */

var mod_url = delivery_action_url; 
var error_msg = 'Product is already in selection list';
function getRelProducts(e) {
    var combination = $('input[name="product_combination"]:checked').val();
    mod_url = mod_url + "&disableCombination=" + combination;
    var search_q_val = $(e).val();
    if (typeof search_q_val !== 'undefined' && search_q_val) {;
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: mod_url + '&q=' + search_q_val,
            success: function(data) {

                if (ps16) {
                    var quicklink_list = '<li class="rel_breaker" onclick="relClearData();"><button class="btn btn-danger">X</button></li>';
                } else {
                    var quicklink_list = '<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
                }
                $.each(data, function(index, value) {
                var fixitname = data[index]['name'];
                if(typeof fixitname !== 'undefined'){
                    if (typeof data[index]['id'] !== 'undefined') quicklink_list += '<li onclick="relSelectThis(' + data[index]['id'] + ',' + data[index]['id_product_attribute'] + ',\'' + fixitname + '\',\'' + data[index]['image'] + '\');"><img src="' + data[index]['image'] + '" width="60"> ' + data[index]['name'] + '</li>';
                } 
                if(typeof fixitname === 'undefined') {
                    return;
                }
                });
                if (data.length == 0) {
                    quicklink_list = '';
                } else {
                    $('#rel_holder').show();
                }
                $('#rel_holder').html('<ul>' + quicklink_list + '</ul>');
            },

            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    } else {
        $('#rel_holder').html('');
    }
}

//select item from dropdown list of product search
function relSelectThis(id, ipa, name, img) {
    var combination = $('input[name="product_combination"]:checked').val();
    if(combination ==1){
        if ($('#row_' + id).length > 0) {
            showErrorMessage(error_msg);
        } else {
            if (ps16) {
                var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img + '" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name + '&nbsp;(ID:' + id + ')</span><button  onclick="relDropThis(this);" class="btn btn-danger">X</button></div><input type="hidden" value="' + id + '" name="product_id[]"></li>'
                relDropThis(id);
            } else {
                var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img + '" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name + '&nbsp;(ID:' + id + ')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' + id + '" name="product_id[]"></li>'
            }
            $('#rel_holder_temp ul').append(draw_html);
        }
    } else{
        if($('#row_'+ipa).length>0){
            showErrorMessage(error_msg);
        } else{
            if (ps16) {
                var draw_html_a = '<li id="row_' + ipa + '" class="media"><div class="media-left"><img src="' + img + '" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name + '&nbsp;(ID Attribute:' + ipa + ')</span><button  onclick="relDropThis(this);" class="btn btn-danger">X</button></div><input type="hidden" value="' + id + '" name="product_id[]"><input type="hidden" value="' + ipa + '" name="product_attribute_id[]"></li>'
            } else {
                var draw_html_a = '<li id="row_' + ipa + '" class="media"><div class="media-left"><img src="' + img + '" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name + '&nbsp;(ID Attribute:' + ipa + ')</span><button  onclick="relDropThis(this);" class="btn btn-danger">X</button></div><input type="hidden" value="' + id + '" name="product_id[]"><input type="hidden" value="' + ipa + '" name="product_attribute_id[]"></li>'
            }
        }
        $('#rel_holder_temp ul').append(draw_html_a);    }
}

//cross button with dropdown of search products
function relClearData() {
    $('#rel_holder').html('');
}

function relDropThis(e) {
    $(e).parent().parent().remove();
}
