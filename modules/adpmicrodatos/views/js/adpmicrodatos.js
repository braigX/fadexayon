/**
* 2007-2023 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Ádalop <contact@prestashop.com>
* @copyright 2023 Ádalop
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*/

$(function () {

    // Al producirse un evento de mostrar tab buscamos el tab correspondiente para activarlo
    $("[data-role='adpmicrodatos-admin'] a[data-toggle='tab']").on("show.bs.tab", function(evt){
        var href = $(evt.target).attr("href");
        var tab = $("#adpmicrodatos-tabs > a[href='" + href + "']");
        tab.siblings().removeClass("active");
        tab.addClass("active");
    });

    $('#tab_help .faq-h2').on('click', function() {
        $('#tab_help .faq-h2').removeClass('faq-open');
        if (!$(this).next().hasClass('hide')) {
            $('#tab_help .faq-text').addClass('hide');
        } else {
            $('#tab_help .faq-text').addClass('hide');
            $(this).next().removeClass('hide');
            $(this).addClass('faq-open');
        }
    });

    $("[data-role='thirparty-richsnippets-modules-tooltip-link']").on("click", function(){
        $('[href=#tab_thirdparty_richsnippets_modules]').click();
    });

    $("[data-toggle='disable-tab']").on("change", function () {
        disableTab($(this));
    });

    $("[data-toggle='disable-tab'][checked]").each(function () {
        disableTab($(this));
    });

    function disableTab($this) {
        var tabId = $this.attr('href');
        var tablink = $(`[data-toggle^='tab'][href='${tabId}']`);

        if ($this.val() == 0) {
            tablink.attr("data-toggle", "tab-disabled");
            tablink.parent().addClass("disabled");

        } else {
            tablink.attr("data-toggle", "tab");
            tablink.parent().removeClass("disabled");
        }
    }

    $(document).on("click", "[data-async-action='getDiff']", function () {
        var $this = $(this);

        $.get("", $this.data())
            .done(function (data) {

                $line = 1;
                $result = [];
                $gen = false;
                for ($i = 0; $i < data.length; ++$i) {
                    $value = data[$i];
                    $text = $value[0];
                    $state = $value[1];
                    switch ($state) {
                        case 0:
                            if ($gen) {
                                $result.push({
                                    line: $line,
                                    text: $("<span class='diff-unmodified'>").text($text)
                                });
                                $gen = false;
                            }
                            ++$line;
                            break;
                        case 1:
                            if (!$gen) {
                                $gen = true;
                                if ($i > 0 && data[$i - 1][1] == 0 && $result[$result.length -1] != $line - 1) {
                                    $prevText = data[$i - 1][0];
                                    $result.push({
                                        line: $line - 1,
                                        text: $("<span class='diff-unmodified'>").text($prevText)
                                    });
                                }
                            }
                            $result.push({
                                line: $line,
                                text: $("<span class='diff-deleted'>").text($text)
                            });
                            break;
                        case 2:
                            if (!$gen) {
                                $gen = true;
                                if ($i > 0 && data[$i - 1][1] == 0 && $result[$result.length -1] != $line - 1) {
                                    $prevText = data[$i - 1][0];
                                    $result.push({
                                        line: $line - 1,
                                        text: $("<span class='diff-unmodified'>").text($prevText)
                                    });
                                }
                            }
                            $result.push({
                                line: $line,
                                text: $("<span class='diff-inserted'>").text($text),
                            });
                            ++$line;
                            break;
                    }
                }

                var $modal = $("#adpmicrodatos_filedetailsmodal");

                var result = $modal.find(".modal-body [data-role='adpmicrodatos-results']");

                result.html("");

                $result.forEach(line => {
                    var item = $("<tr>");
                    item.append($("<td>").text(line.line));
                    item.append($("<td class='result-content'>").html(line.text));
                    result.append(item);
                });

                $modal.modal("show");
            })
            .fail(function () {
                alert("Error a obtener la caomparación de ficheros")
            });
    });

    $("#tab_backups table").DataTable({
        paging: false,
        info: false,
        searching: false,
        columnDefs: [
          { orderable: false, targets: 3 }
        ],
        "order": [[ 0, "desc" ]]
    })
});