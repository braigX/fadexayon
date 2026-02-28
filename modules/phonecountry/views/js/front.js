/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ether Creation
 *  @copyright Copyright (c) 2010-2016 S.A.R.L Ether Création (http://www.ethercreation.com)
 *  @license   Commercial license
 */

$(document).ready(function () {
    if (tab_phone_country) {
        tab_phone_country = JSON.parse(tab_phone_country);
    }
    
    checkPhone();
    checkPhone('phone_mobile');

    $('body').on('change','input[name=phone]', function() {
        checkPhone();
    });

    $('body').on('change','input[name=phone_mobile]', function() {
        checkPhone('phone_mobile');
    });

    $( document ).ajaxComplete(function(event, xhr, settings) {
        if (settings.data){
            params = settings.data;
            if (params.indexOf("id_country=") >= 0) {
                id_country = $('select[name=id_country]').val();
                prefix = '+'+tab_phone_country[id_country].call_prefix;
                $('input[name=phone]').val(prefix);
                $('input[name=phone_mobile]').val(prefix);
            }
        }
    });

    $(document).on('submit', '.js-address-form form', function(){
        checkPhone('phone_mobile', true)
        test = checkPhone('phone', true);
        return test;
    });
});

function checkPhone(type = 'phone', submit = false)
{
    $('button[name="confirm-addresses"]').removeClass('disabled');
    $('button[name="confirm-addresses"]').prop('disabled', false);
    $('.js-address-form form').data("disabled", false);
    if (!tab_phone_country) {
        return;
    }
    id_country = $('select[name=id_country]').val();
    if (!id_country) {
        return;
    }
    prefix = '+'+tab_phone_country[id_country].call_prefix;
    if (type == 'phone') {
        p_size = tab_phone_country[id_country].fixe;
    } else {
        p_size = tab_phone_country[id_country].mobile;
    }
    val = $('input[name='+type+']').val();
    if (val) {
        if (val[0] == '0') {
            val = val.substring(1);
        }
        val = val.replace(prefix, '');
        val = val.replace(/\.|-| |[a-z]|[A-Z]/g, '');
        
        if (val.length == 0) {
            if (!submit) {
                $('input[name='+type+']').val(prefix);
                $('input[name='+type+']').css('outline', '');
                return false;
            } else {
                if (type == 'phone') {
                    $('input[name='+type+']').val(prefix);
                    $('input[name='+type+']').css('outline', '0.18rem solid red');
                } else {
                    $('input[name='+type+']').val('');
                }
                return false;
                
            }
        } else if (!ecVerifPhone(val, p_size)) {
            console.log('pas ok')
            $('input[name='+type+']').val(prefix);
            $('input[name='+type+']').css('outline', '0.18rem solid red');
            return false;
        } else {
            $('input[name='+type+']').val(prefix+val);
            $('input[name='+type+']').css('outline', '');
            return true;
        }
    } else {
        if (!submit || type == 'phone') {
            $('input[name='+type+']').val(prefix);
            $('input[name='+type+']').css('outline', '');
            return true;
        } else {
            $('input[name='+type+']').val('');
            return true;
        }
        
    }
    
    
}

function ecVerifPhone(num, sizes)
{
    sizes = sizes.toString();
    sizes = sizes.split('|');
    console.log(num);
    console.log(sizes);
    res = false;
    $.each(sizes, function(i, size_len) {
        console.log(parseInt(num.length));
        console.log(parseInt(size_len));
        if (parseInt(num.length) == parseInt(size_len)) {
            res = true;
            return;
        }
    });
    return res;
}