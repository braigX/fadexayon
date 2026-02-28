/* global $ */
$(document).ready(function () {

    // roythemes mod 2020 fix
    var $searchWidget       = $('#header .search-widget');
    var $searchWidgetSide   = $('.side_menu .search-widget');
    var $searchBox          = $searchWidget.find('input[type=text]');
    var $searchBoxSide      = $searchWidgetSide.find('input[type=text]');
    var searchURL           = $searchWidget.attr('data-search-controller-url');
    var searchURLSide       = $searchWidgetSide.attr('data-search-controller-url');

    $.widget('prestashop.psBlockSearchAutocomplete', $.ui.autocomplete, {
        _renderItem: function (ul, product) {
            var line = $("<li>") // roythemes mod
                .append($("<a>").addClass("searchLine")
            				.append('<img src="'+product.cover.small.url+'" class="searchImg">')
            				.append($("<div>").addClass("searchDescription")
              					.append($("<span>").html(product.name).addClass("product"))
              					.append($("<span>").html(product.price).addClass("searchPrice"))
          					)
                ).appendTo(ul);
            return line;
        }
    });

    $searchBox.psBlockSearchAutocomplete({
        source: function (query, response) {
            $.post(searchURL, {
                s: query.term,
                resultsPerPage: 10
            }, null, 'json')
            .then(function (resp) {
                response(resp.products);
            })
            .fail(response);
        },
        select: function (event, ui) {
            var url = ui.item.url;
            window.location.href = url;
        },
    });
    $searchBoxSide.psBlockSearchAutocomplete({
        source: function (query, response) {
            $.post(searchURLSide, {
                s: query.term,
                resultsPerPage: 6
            }, null, 'json')
            .then(function (resp) {
                response(resp.products);
            })
            .fail(response);
        },
        appendTo: "#search_results",
        select: function (event, ui) {
            var url = ui.item.url;
            window.location.href = url;
        },
    });
});
