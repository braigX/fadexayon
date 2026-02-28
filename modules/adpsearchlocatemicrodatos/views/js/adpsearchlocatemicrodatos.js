/**
* 2007-2022 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Ádalop <contact@prestashop.com>
* @copyright 2022 Ádalop
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/

$(function () {

    reloadAsGet = function () {
        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname + window.location.search;
    }
    // Al producirse un evento de mostrar tab buscamos el tab correspondiente para activarlo
    $("#adpsearchlocatemicrodatos-tabs > a[data-toggle='tab']").on("show.bs.tab", function (evt) {
        var href = $(evt.target).attr("href");
        var tab = $("#adpsearchlocatemicrodatos-tabs > a[href='" + href + "']");
        tab.siblings().removeClass("active");
        tab.addClass("active");
    });

    $(document).on("change", "#url_custom", function(){
        $("#url_custom_link").attr("href", $("#url_custom_link").data("urlPrefix") + $(this).val());
    });

    $(document).on("change", "#url_custom_2", function(){
        $("#url_custom_link_2").attr("href", $("#url_custom_link_2").data("urlPrefix") + $(this).val());
    });

    $(document).on("change", "#url_custom_3", function(){
        $("#url_custom_link_3").attr("href", $("#url_custom_link_3").data("urlPrefix") + $(this).val());
    });

    $(document).on("change", "[name='searchContext']", function () {
        var $this = $(this);

        var container = $this.closest("[data-role='search-section']");

        $("[data-async-action='scanAll']").data("searchContext", $this.val())

        switch ($this.val()) {
            case "1":
                container.find("[data-role='search-themes-section']").show();
                container.find("[data-role='search-modules-section']").show();
                break;
            case "2":
                container.find("[data-role='search-themes-section']").show();
                container.find("[data-role='search-modules-section']").hide();
                break;
            case "3":
                container.find("[data-role='search-themes-section']").hide();
                container.find("[data-role='search-modules-section']").show();
                break;
            default:
                break;
        }
    });

    OnAsyncAction("getScanResult")
        .done(function (data) {
            var $modal = $("#adpsearchlocatemicrodatos_filedetailsmodal");

            var result = $modal.find(".modal-body [data-role='adpsearchlocate-results']");

            result.html("");

            data.forEach(line => {
                var item = $("<tr>");
                item.append($("<td>").text(line.line));
                item.append($("<td class='result-content'>").text(line.currentValue));
                result.append(item);
            });

            $modal.modal("show");
        });

    OnAsyncAction("scanModule")
        .before(function () {
            $(this).find("i").removeClass("icon-search");
            $(this).find("i").addClass("icon-refresh");
            $(this).find("i").addClass("icon-spin");
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
            $(this).find("i").removeClass("icon-refresh");
            $(this).find("i").addClass("icon-search");
        });

    OnAsyncAction("scanTheme")
        .before(function () {
            $(this).find("i").removeClass("icon-search");
            $(this).find("i").addClass("icon-refresh");
            $(this).find("i").addClass("icon-spin");
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
            $(this).find("i").removeClass("icon-refresh");
            $(this).find("i").addClass("icon-search");
        });


    OnAsyncAction("scanAll")
        .before(function () {
            $(this).find("i").removeClass("icon-search");
            $(this).find("i").addClass("icon-refresh");
            $(this).find("i").addClass("icon-spin");
            return confirm($(this).data("confirmMessage"));
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
            $(this).find("i").removeClass("icon-refresh");
            $(this).find("i").addClass("icon-search");
        });


    OnAsyncAction("fixModule")
        .before(function () {
            $(this).find("i").removeClass("icon-magic");
            $(this).find("i").addClass("icon-refresh");
            $(this).find("i").addClass("icon-spin");
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
            $(this).find("i").removeClass("icon-refresh");
            $(this).find("i").addClass("icon-magic");
        });

    OnAsyncAction("recoveryModule")
        .before(function () {
            $(this).find("i").addClass("icon-spin");
            if($(this).data("modified"))
                return confirm($(this).data("confirmMessage"));
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
        });

        OnAsyncAction("fixTheme")
        .before(function () {
            $(this).find("i").removeClass("icon-magic");
            $(this).find("i").addClass("icon-refresh");
            $(this).find("i").addClass("icon-spin");
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
            $(this).find("i").removeClass("icon-refresh");
            $(this).find("i").addClass("icon-magic");
        });

    OnAsyncAction("recoveryTheme")
        .before(function () {
            $(this).find("i").addClass("icon-spin");
            if($(this).data("modified"))
                return confirm($(this).data("confirmMessage"));
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
        });

    OnAsyncAction("fixFile")
        .before(function () {
            $(this).find("i").addClass("icon-spin");
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
        });

    OnAsyncAction("recoveryFile")
        .before(function () {
            $(this).find("i").addClass("icon-spin");
            if($(this).data("modified"))
                return confirm($(this).data("confirmMessage"));
        })
        .done(function (data) {
            reloadAsGet();
        })
        .after(function () {
            $(this).find("i").removeClass("icon-spin");
        });

    OnAsyncAction("getDiff")
        .done(function (data) {
            var $modal = $("#adpsearchlocatemicrodatos_filedetailsmodal");

            var result = $modal.find(".modal-body [data-role='adpsearchlocate-results']");

            result.html("");

            data.forEach(line => {
                var item = $("<tr>");
                item.append($("<td>").text(line.line));
                item.append($("<td class='result-content'>").html(line.text));
                result.append(item);
            });

            $modal.modal("show");
        });

});