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

document.addEventListener('DOMContentLoaded', function() {
    if ($('.ap5-buy-block').length > 0) {
        $(document).on('mousedown click', '.ap5-buy-block button', updateEd);
        $(document).on('change', '.ap5-buy-block input', updateEd);
    }
    function updateEd() {
        let id_product = $('input[name="id_product"]').val();
        let id_product_attribute = $('input[name="id_product_attribute"]').val();
        let quantity_wanted = $('#quantity_wanted').val();
        $.ajax({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            url: front_ajax_url + '&rand=' + new Date().getTime(),
            async: false,
            cache: false,
            dataType : "json",
            data: {
                ajaxRefresh: true,
                action: 'cart',
                id_product: id_product,
                id_product_attribute: id_product_attribute,
                quantity_wanted: quantity_wanted
            },
            complete: function(res){
                //console.log('Complete!!!!');
                //console.log(res);
                if(res.statusText == 'OK' || res.statusText == 'success') {
                    if (res.responseText !== 'false') {
                        replaceED = $(res.responseJSON).html();
                        $('#estimateddelivery').fadeOut("slow", function () {
                            $('#estimateddelivery').html(replaceED);
                            $('#estimateddelivery').fadeIn("slow");
                        });
                        return;
                    }
                }
                // No results, therefore the ED is not available
                $('#estimateddelivery').hide();
            }
        });
    }
});
