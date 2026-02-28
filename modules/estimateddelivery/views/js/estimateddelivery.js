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
document.addEventListener('DOMContentLoaded', function() {
    //console.log('DomContentLoaded');
    // console.log(location.href.search('#'));
    // console.log(location.href.search('#') !== -1);
    if (location.href.search('#') !== -1) {
        ajaxRefresh();
        //console.log('Initial Ajax Search');
    }
    init_combi_check(20);

    function init_combi_check(tries) {
        if (typeof front_ajax_url !== 'undefined' && (typeof combinations !== 'undefined' && combinations.length > 0)) {
            ajaxRefresh();
            $(document).on('click', '.product_quantity_up, .product_quantity_down', function(e){
                ajaxRefresh();
            });
            $(document).on('change', '.quantity_wanted', function(e){
                ajaxRefresh();
            });
            $(document).on('click', '#attributes a, #attributes input', function() {
                ajaxRefresh();
            });
            $(document).on('change', '#attributes select', function() {
                ajaxRefresh();
            });
        } else {
            if (tries > 0) {
                setTimeout(function () {
                    init_combi_check(tries - 1);
                }, 100);
            }
        }
    }
    function ajaxRefresh() {
        setTimeout( () => {
            // console.log('ED: Init Ajax Refresh');
            fieldName = $(this).data('field-qty');
            var currentVal = parseInt($('input[name=' + fieldName + ']').val());
            var curCombination = searchCombi();
            var id_product = $('input[name="id_product"]').val();
            var quantity_wanted = $('#quantity_wanted').val();
            // console.log(curCombination);
            // if( allowBuyWhenOutOfStock && currentVal > quantityAvailable ) {
            // }
            let data = {
                ajaxRefresh: true,
                action: 'cart',
                id_product: id_product,
                id_product_attribute: curCombination.idCombination,
                idsAttributes: curCombination.idsAttributes,
                quantity_wanted: quantity_wanted
            };
            // console.log(data);
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: front_ajax_url + '&rand=' + new Date().getTime(),
                async: false,
                cache: false,
                dataType: "json",
                data: data,
                complete: function (res) {
                    if (res.statusText == 'OK' || res.statusText == 'success') {
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
        }, ed_refresh_delay);
    }

    /*window.addEventListener('load', function() {
        // Was active
        //ajaxRefresh();
    });*/
    function searchCombi()
    {
        var ids = [];
        $('#attributes select').each(function() {
            ids.push(parseInt($(this).val()));
        });
        $('#attributes input[type=radio]:checked').each(function() {
            if ($(this).val() > 0) {
                ids.push(parseInt($(this).val()));
            }
        });
        $('#attributes .attribute_list a.selected').each(function() {
            ids.push(parseInt($(this).attr('id').match(/\d+/g)[0]));
        });
        // Create a string version of the array ids
        var sids = ids.map(function (e) {
            return e.toString();
        });
        for (var i = 0, len = combinations.length; i < len; i++) {
            // TODO iterate combinations and find the combination ID matching the result
            if (arraysEqual(combinations[i]['idsAttributes'], ids) || arraysEqual(combinations[i]['idsAttributes'], sids)) {
                return combinations[i];
            }
        }
        return false;
    }
    function arraysEqual(_arr1, _arr2)
    {
        if (!Array.isArray(_arr1) || ! Array.isArray(_arr2) || _arr1.length !== _arr2.length) {
          return false;
        }
        var arr1 = _arr1.concat().sort();
        var arr2 = _arr2.concat().sort();
        for (var i = 0; i < arr1.length; i++) {
            if (arr1[i] !== arr2[i]) {
                return false;
            }
        }
        return true;
    }
});