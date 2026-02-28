/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */

$(document).ready(function () {
    /*Sorting columns*/
    $('.op-sortable-column').click(function () {
        var activeClass = 'active-op-s';
        var reverseClass = 'active-op-reverse';
        /*if new sorting column*/
        if (!$(this).hasClass(activeClass)) {
            $(this).addClass(activeClass);
            var url = redirectByGridQuery(false, $(this).data('sort-op-name'), 1);
        }
        /*old sorting column*/
        else {
            if (!$(this).hasClass(reverseClass)) {
                $(this).addClass(reverseClass);
                var url = redirectByGridQuery(false, $(this).data('sort-op-name'), 2);
            } else {
                $(this).removeClass(reverseClass);
                var url = redirectByGridQuery(false, $(this).data('sort-op-name'), 1);
            }
        }
        if (url) {
            window.location.href = url;
        }
    })

    /*Sorting columns by js*/
    $('.op-sortable-column-js').click(function () {
        var activeClass = 'active-op-s';
        var reverseClass = 'active-op-reverse';
        /*if new sorting column*/
        if (!$(this).hasClass(activeClass)) {
            $(this).addClass(activeClass);
            sortByJs($(this).data('sort-op-name'), 1);
        }
        /*old sorting column*/
        else {
            if (!$(this).hasClass(reverseClass)) {
                $(this).addClass(reverseClass);
                sortByJs($(this).data('sort-op-name'), 2);
            } else {
                $(this).removeClass(reverseClass);
                sortByJs($(this).data('sort-op-name'), 1);
            }
        }
    })

    /*Choose filter from select*/
    $('.op-filter-chooser-js').click(function () {
        let openBody = '#' + $(this).attr('rel');
        $(openBody).slideToggle();
        hideByClickOnOtherPage(openBody);
    })
    $('.op-active-chooser-dropdown-item-js').click(function () {
        window.location.href = redirectByGridQuery('filterActive', false, $(this).data('chooser'))
    })

    $('.op-hook-chooser-dropdown-item-js').click(function () {
        window.location.href = redirectByGridQuery('filterHook', false, $(this).data('chooser'))
    })

    /*prepare array of hooks beforehand*/
    var basicHooksArray = [];
    $('.op-hook-chooser-dropdown-item-js').each(function () {
        basicHooksArray[$(this).data('chooser')] = $(this).text();
    })

    /*Search in hooks array*/
    $('#op-start-typing').on('input', function () {
        var text = $(this).val();
        var currentHooksArray = [];

        for (var index in basicHooksArray) {
            if (searchInput(text, basicHooksArray[index])) {
                currentHooksArray[index] = basicHooksArray[index];
            }
        }

        $('.op-hook-chooser-dropdown-item-js').remove();
        for (var index in currentHooksArray) {
            $('.op-hook-chooser-dropdown').append(createHookChooserItem(index, currentHooksArray[index]))
        }
        $('.op-hook-chooser-dropdown-item-js').click(function () {
            window.location.href = redirectByGridQuery('filterHook', false, $(this).data('chooser'))
        })
    })

    //Checkboxes bulk
    var blocksToRemove = {};
    $('.grid-checkbox').click(function () {
        let id = $(this).data('row-bulk-id');
        let type = $(this).data('row-bulk-type');
        if (!$(this).hasClass('grid-checkbox-active')) {
            $(this).addClass('grid-checkbox-active').removeClass('fa-square').addClass('fa-check-square');
            blocksToRemove[id+type] = {"id": id, "type": type};
        } else {
            $(this).removeClass('grid-checkbox-active').addClass('fa-square').removeClass('fa-check-square');
            delete blocksToRemove[id+type];
        }
    });

    //Add search query to current url
    $('#op-filter-search-button').click(function () {
        var stringReformed;
        var getInput = $('#op-filter-search-input').val();

        stringReformed = getInput.replace(/\-/g, "").replace(/\_/g, "").replace(/\./g, "").replace(/\!/g, "").replace(/\~/g, "").replace(/\*/g, "").replace(/\'/g, "").replace(/\(/g, "").replace(/\)/g, "").replace(/\//g, "").replace(/\\/g, "");

        stringReformed = encodeURIComponent(stringReformed);

        location.href = window.location.href + '&search=' + stringReformed;
    })

    $('#bulk-chooser').click(function () {
        $('#bulk-chooser-dropdown').slideToggle();
    })

    //Delete blocks forever
    $('#bulk-button-block-delete').click(function () {
        if (!$.isEmptyObject(blocksToRemove)) {
            var r = confirm("Are you sure you want to delete?");
            if (r == true) {
                var string = JSON.stringify(blocksToRemove);
                deleteBlocksAjax(string);
                $(this).prop('disabled', true);
                $(this).find($('.usual-process')).hide();
                $(this).find($('.spin-process')).show()
            } else {
                alert("You canceled deletion");
            }
        }
    })
    $('#bulk-button-block-publish').click(function () {
        if (!$.isEmptyObject(blocksToRemove)) {
            var string = JSON.stringify(blocksToRemove);
            publishBlocksAjax(string);
            $(this).prop('disabled', true);
            $(this).find($('.usual-process')).hide();
            $(this).find($('.spin-process')).show()
        }
    })
    $('#bulk-button-block-unpublish').click(function () {
        if (!$.isEmptyObject(blocksToRemove)) {
            var string = JSON.stringify(blocksToRemove);
            unpublishBlocksAjax(string);
            $(this).prop('disabled', true);
            $(this).find($('.usual-process')).hide();
            $(this).find($('.spin-process')).show()
        }
    })
    $('#bulk-button-block-copy-shop').click(function () {
        if (!$.isEmptyObject(blocksToRemove)) {
            $('#bulk-chooser-dropdown').slideUp()
            $('#op-faqop-copy-to-shops').slideDown()
        } else {
            alert("First choose lists to copy")
        }
    });

    $('#op-copy-to-shops').click(function () {
        if (!$.isEmptyObject(blocksToRemove)) {
            var idsArray = JSON.stringify(blocksToRemove);
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
        } else {
            alert('No blocks selected for copying');
        }
    });

    bindCheckboxClick()

    //Checkboxes bulk
    var itemsToRemove = {};
    $('.grid-checkbox-items').click(function () {
        let id = $(this).data('row-bulk-id');
        if (!$(this).hasClass('grid-checkbox-active')) {
            $(this).addClass('grid-checkbox-active').removeClass('fa-square').addClass('fa-check-square');
            if ($('.bulk-button').prop('disabled') === true) {
                $('.bulk-button').prop('disabled', false).addClass('bulk-button-active')
            }
            itemsToRemove[id] = id;
        }
        else {
            $(this).removeClass('grid-checkbox-active').addClass('fa-square').removeClass('fa-check-square');
            delete itemsToRemove[id];
            if ($.isEmptyObject(itemsToRemove)) {
                $('.bulk-button').prop('disabled', true).removeClass('bulk-button-active')
            }
        }
    });
    //Delete items forever
    $('#bulk-button-delete').click(function () {
        if (!$.isEmptyObject(itemsToRemove)) {
            var r = confirm("Are you sure you want to delete?");
            if (r == true) {
                var string = JSON.stringify(itemsToRemove);
                deleteItemsAjax(string);
                $(this).prop('disabled', true);
                $(this).find($('.usual-process')).hide();
                $(this).find($('.spin-process')).show()
            }
            else {
                alert("You canceled deletion");
            }
        } else {
            alert('Choose items to delete');
        }
    })

    //Add chosen items to
    $('#add_selected_items').click(function () {
        if (!$.isEmptyObject(itemsToRemove)) {
            var string = JSON.stringify(itemsToRemove);
            var listId = $(this).data('list');
            var listType = $(this).data('type');

            addBulkItemsAjax(string, listId, listType);
            $(this).prop('disabled', true);
            $(this).find($('.usual-process')).hide();
            $(this).find($('.spin-process')).show()
        } else {
            alert('Choose items to add');
        }
    })

    //Remove items from page or block
    $('#bulk-button-remove').click(function () {
        if (!$.isEmptyObject(itemsToRemove)) {
            var string = JSON.stringify(itemsToRemove);
            var listId = $(this).data('list');
            var listType = $(this).data('type');

            removeItemsAjax(string, listId, listType);
            $(this).prop('disabled', true);
            $(this).find($('.usual-process')).hide();
            $(this).find($('.spin-process')).show()
        } else {
            alert('Choose items to remove');
        }
    })
});
