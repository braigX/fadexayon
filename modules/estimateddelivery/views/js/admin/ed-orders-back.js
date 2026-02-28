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

/**
 * Smooth behavior when loading the order detail page to auto-select the Estimated Delivery tab to set the Estimated Delivery date
 */
document.addEventListener('DOMContentLoaded', function() {
    let hash = window.location.hash;
    if (hash) {
        $('#main-div a').each(function() {
            if ($(this).attr('href') == hash) {
                setTimeout(() => {
                    //console.log($(this), $(this).offset());
                    $(this).click();
                    window.scrollTo( {
                        top: $(this).offset().top - 50,
                        left: 0,
                        behavior: 'smooth',
                    });
                }, 500);
                return;
            }
        })
    }
});