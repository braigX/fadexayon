/**
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*/
/* Methods to controll the display of the popup */
document.addEventListener('DOMContentLoaded', function() {
    let popup_opening = false;
    $(document).on('click', '#ed_popup, .ed_popup', function(e) {
        e.preventDefault();
        if ($('.ed_popup_background').length > 1) {
            $('.ed_popup_background').not(':first').remove();
        };
        setTimeout(function() {
            if (!$('#ed_popup_content').parent().is('body')) {
                $('#ed_popup_content').appendTo('body');
            }
            if (!$('.ed_popup_background').next().is('#ed_popup_content, .ed_popup_content')) {
                $('.ed_popup_background').insertBefore($('#ed_popup_content, .ed_popup_content').first());
            }
            $('.ed_popup_background').fadeIn(500);
            $('#ed_popup_content').fadeIn(500);
            popup_opening = true;
            setTimeout(function() {
                popup_opening = false;
            }, )
        }, 500);
    });
    window.addEventListener('click', function(e){
        let pop = document.getElementById('ed_popup_content');
        if (pop && !document.getElementById('ed_popup_content').contains(e.target)){
            closeEdPopup();
        }
    });
    window.addEventListener("keydown", function (e) {
        if (e.key !== undefined && e.key === 'Escape') {
            closeEdPopup();
        }
    });
    $(document).on('click', '.ed_close_popup span', function() {
        closeEdPopup();
    });
    function closeEdPopup() {
        if (popup_opening == false && $('#ed_popup_content').is(':visible')) {
            $('#ed_popup_content').fadeOut(500);
            $('.ed_popup_background').fadeOut(500);
        }
    }
    if ($('.ed_popup_background').length > 0) {
        $('.ed_popup_background').appendTo('body');
    }
})