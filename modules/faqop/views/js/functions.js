/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */

const url_ajax = window.location.href.split('#')[0] // remove # from the end of URL if necessary

function showErrorOp(element, message) {
    $(element + " .op-error-wrap").html(message).show();
}
function hideErrorOp(element) {
    $(element + " .op-error-wrap").hide();
}
function showSuccessOp(element, message) {
    $(element + " .op-success-wrap").html(message).show();
}
function hideSuccessOp(element) {
    $(element + " .op-success-wrap").hide();
}

function spinSuccess() {
    $('.spin-process').hide();
    $('.spinner-hide-txt').show();
}

function bulkSuccess() {
    $('.bulk-button').find($('.usual-process')).show();
    $('.bulk-button').find($('.spin-process')).hide()
}

/*Reindex buttons*/
function updateItemsIndex() {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=updateItemsIndex"
    })
        .done(function() {
            spinSuccess();
            let elementError = "#block-for-error-grid";
            hideErrorOp(elementError);

            location.reload(true);
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-help";
            showErrorOp(element, message);
        });
}

function updateBlocksWithLanguages() {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=updateBlocksWithLanguages"
    })
        .done(function() {
            spinSuccess();
            let elementError = "#block-for-error-grid";
            hideErrorOp(elementError);

            location.reload(true);
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-help";
            showErrorOp(element, message);
        });
}

function bindCheckboxClick() {
    let checkbox = $('.checkbox-block-custom input[type="checkbox"]');
    checkbox.each(function () {
        if($(this).is(':checked')) {
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
        }
    })

    checkbox.click(function () {

        if($(this).is(':checked')) {
            $(this).attr('checked', true).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
        }
        else {
            $(this).attr('checked', false).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
        }
    })
}



//if any sort queries were in prev. address, remove all sort quieries, add only the necessary one. digit is the order. 1 direct, 2 reverse
function makeOrderQuery(items, sortBy, digit) {
    var listOfOrderQueries = ['orderHook', 'orderId', 'orderTitle', 'orderPos', 'tabOp'];
    $.each(listOfOrderQueries, function (i, v) {
        if (js_isset(items[v])) {
            delete items[v]
        }
    })
    items[sortBy] = digit;
    items['orderBy'] = 1;
    return items;
}

function makeFilterQuery(items, filterBy, digit) {
    items[filterBy] = digit;
    items['filterBy'] = 1;
    return items;
}

//params that delete items or show messages must be removed
function filterFromEditThings(items) {
    var listOfOrderQueries = ['conf', 'delete_id_item', 'id_item'];
    $.each(listOfOrderQueries, function (i, v) {
        if (js_isset(items[v])) {
            delete items[v]
        }
    })
    return items;
}

function redirectByGridQuery(filterBy = false, sortBy = false, digit) {
    var items = getAllQueryVariables();
    if (filterBy) {
        items = makeFilterQuery(items, filterBy, digit);
    }
    if (sortBy) {
        items = makeOrderQuery(items, sortBy, digit);
    }
    items = filterFromEditThings(items);
    var queryString = '';
    for (var index in items) {
        queryString += '&' + index + '=' + items[index]
    }

    return window.location.pathname + '?' + queryString.substring(1);
}

function getAllQueryVariables() {
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    var newArray = [];
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        newArray[pair[0]] = pair[1];
    }
    return (newArray);
}

function js_isset(somevar) {
    return (typeof (somevar) != 'undefined' && somevar !== null);
}

function hideByClickOnOtherPage(element) {
    $(document).click(function (e) {
        let div = $(element);
        let chooserDiv = div.parent().find($('.op-filter-chooser-js'))
        if (!div.is(e.target) && div.has(e.target).length === 0
            && !chooserDiv.is(e.target) && !chooserDiv.find($('span')).is(e.target)) {
            div.slideUp();
        }
    });
}

function searchInput(re, str) {
    return str.search(re) != -1 ? true : false;
}

function createHookChooserItem(index, value) {
    return '<div class="op-active-chooser-dropdown-item op-hook-chooser-dropdown-item-js" data-chooser="' + index + '">' + value + '</div>';
}

/*rows position for blocks*/
function updateBlocksPosition(order) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=updateBlocksPosition&order=" + order
    })
        .done(function () {
            let elementError = "#block-for-error-grid";
            hideErrorOp(elementError);

            $.each(JSON.parse(order), function (position, id) {
                let element = $('#' + id + ' .op-td-position');
                element.html('<i class="icon-arrows"></i>' + position);
            });
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
        });

}

/*delete checked items on forever*/
function deleteBlocksAjax(idsArray) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=deleteBulkBlocks&ids=" + idsArray
    })
        .done(function () {
            location.href = window.location.href + '&conf=2';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });

}

/*publish checked items on All Items page*/
function publishBlocksAjax(idsArray) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=publishBulkBlocks&ids=" + idsArray
    })
        .done(function () {
            location.href = window.location.href + '&conf=5';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });

}

/*unpublish checked items on All Items page*/
function unpublishBlocksAjax(idsArray) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=unpublishBulkBlocks&ids=" + idsArray
    })
        .done(function () {
            location.href = window.location.href + '&conf=5';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });

}

var curShop = 0;
var shopsLength = 0;
/*copy checked items to another shop*/
function copyShopBlocksAjax(idsArray, oldShop, newShop) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=copyBulkBlocksShop&ids=" + idsArray + "&oldShop=" + oldShop +
            "&newShop=" + newShop
    })
        .done(function () {
            curShop++;
            $('.op-copy-cur').html(curShop);
            if (curShop === shopsLength) {
                location.href = window.location.href + '&conf=19';
            }
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            $('#op-popup-copy-shop').fadeOut();
        });

}

//Here we sort blocks in which we place our item on item edit/add page
function sortByJs(sortName, order) {
    var $elements = $('.body-table-blocks tr');
    var $target = $('.body-table-blocks');

    $elements.sort(function (a, b) {
        var an = $(a).find($('.' + sortName)).text(),
            bn = $(b).find($('.' + sortName)).text();

        if (an && bn) {
            if (order == 1) {
                return an.toUpperCase().localeCompare(bn.toUpperCase());
            } else {
                return bn.toUpperCase().localeCompare(an.toUpperCase());
            }
        }

        return 0;
    });

    $elements.detach().appendTo($target);
}

/*delete checked items on All Items page forever*/
function deleteItemsAjax(idsArray) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=deleteBulkItems&ids=" + idsArray
    })
        .done(function() {
            location.href = window.location.href + '&conf=2';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });
}

/*remove checked items from page or block*/
function removeItemsAjax(idsArray, listId, listType) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=removeBulkItems&ids=" + idsArray +
            "&listId=" + listId + "&listType=" + listType
    })
        .done(function() {
            location.href = window.location.href + '&conf=2';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });

}

/*
Bulk add checked items to page or block
item_list_add_ready.tpl
AdminFaqopAddReadyItemController::renderAddReadyGrid()
 */
function addBulkItemsAjax(idsArray, listId, listType) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=addBulkItems&ids=" + idsArray +
            "&listId=" + listId + "&listType=" + listType
    })
        .done(function() {
            location.href = url_redirect + '&conf=4';
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            bulkSuccess();
        });

}

function updateItemsPosition(order) {
    var opCells = $('#opCells');
    var listId = opCells.data('list');
    var listType = opCells.data('type');
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=updateItemsPosition&order=" + order +
        "&listId=" + listId + "&listType=" + listType
    })
        .done(function() {
            let elementError = "#block-for-error-grid";
            hideErrorOp(elementError);

            $.each(JSON.parse(order), function(position, id){
                let element = $('#' + id + ' .op-td-position');
                element.html('<i class="icon-arrows"></i>' + position);
            });

            let message = 'New positions saved';
            let element = "#block-for-success-grid";
            showSuccessOp(element, message);
            setTimeout(hideSuccessOp, 10000, element);
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
        });

}

/*rows position for blocks*/
function updatePageStatus(pageStatus, button) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=updatePageStatus&pageStatus=" + pageStatus
    })
        .done(function () {
            let elementError = "#block-for-error-grid";
            hideErrorOp(elementError);
            location.reload(true);
        })
        .fail(function (response, textStatus, errorThrown) {
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch (e) {

            }

            let element = "#block-for-error-grid";
            showErrorOp(element, message);
            $('#op-spinner-status').hide();
            button.show();
        });

}
