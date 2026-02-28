/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
function hiGcDisplayLoader () {
    $('body').append('<div class="hi-gc-loader"></div>');
    var top = $('body').scrollTop();
    $('.hi-gc-loader').css('top', top+'px');
    $('body').addClass('hi-gc-overflow');
}
function hiGcHideLoader () {
    $('.hi-gc-loader').remove();
    $('body').removeClass('hi-gc-overflow');
}

function hiGoogleConnectResponse(res)
{
    hiGcDisplayLoader();

    setTimeout(function(){
        hiGcHideLoader();
    }, 3000);
    $.ajax({
        type: "POST",
        dataType: "json",
        url: hiGoogleConnect.frontUrl,
        data: {
            secure_key: hiGoogleConnect.secure_key,
            id_token: res.credential,
            action: 'connectUser'
        },
        beforeSend: function(){
            hiGcDisplayLoader();
        },
        success: function(response) {
            if (response.error) {
                hiGcHideLoader();
                alert(response.error);
            } else {
                $('body').append(response.message);
                setTimeout(function() {
                    window.location.href = window.location.href;
                }, 2000);
            }
        },
        error: function(jqXHR, error, errorThrown) {
            hiGcHideLoader();
        }
    });
}
