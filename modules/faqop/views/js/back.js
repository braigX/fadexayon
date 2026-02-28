/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */

$('document').ready(function () {
    $('#update-langs').click(function(){
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        updateBlocksWithLanguages();
    })
    $('#update-index-items').click(function(){
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        updateItemsIndex();
    })

    $('.op-toggle-status').click(function (){
        $(this).hide();
        $('#op-spinner-status').show();
        updatePageStatus($(this).data('status'), $(this));
    })

    /* Export page settings to shops */
    $('#bulk-button-block-copy-shop-page').click(function () {
        $('#op-faqop-copy-to-shops').slideToggle()
    });

    $('#op-copy-page-to-shops').click(function () {
            let pageArray = {};
            let id = $(this).data('page-id');
            let type = 'page';
            pageArray[id+type] = {"id": id, "type": type};
            var idsArray = JSON.stringify(pageArray);
            let oldShop = $(this).data('old-shop');
            let shopsChosen = [];
            $('.op-copy-to-shops-wrap input[type="checkbox"]').each(function () {
                if($(this).is(':checked')) {
                    shopsChosen.push($(this).val());
                }
            })
            shopsLength = shopsChosen.length;
            $('.op-copy-all').html(shopsLength);
            $("#op-popup-copy-shop").fadeIn();

            for (let newShop of shopsChosen) {
                copyShopBlocksAjax(idsArray, oldShop, newShop);
            }
    });

    /* Import page settings from shop */
    $('#op-import-page-settings').click(function () {
        $('#op-import-page-settings-body').slideToggle()
    })

    $('#op-import-page-from-shops').click(function () {
        let data = $('#op-import-shop-selector').val();
        if (data) {
            data = data.split('-');
            let oldShop = data[0];
            let pageArray = {};
            let id = data[1];
            let type = 'page';

            pageArray[id+type] = {"id": id, "type": type};
            var idsArray = JSON.stringify(pageArray);
            let newShop = $(this).data('new-shop');

            shopsLength = 1;
            $('.op-copy-all').html(shopsLength);
            $("#op-popup-copy-shop").fadeIn();

            copyShopBlocksAjax(idsArray, oldShop, newShop);
        } else {
            alert("Choose a shop");
        }
    })
})

