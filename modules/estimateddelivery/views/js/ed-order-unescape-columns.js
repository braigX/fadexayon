/**
 * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 *
 */

function htmlDecode(input) {
    if (input.indexOf('&lt;span') !== -1) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(input, "text/html");
        return doc.documentElement.textContent;
    }
    return input;
}

function decodeHtmlContent() {
    const selectors = ['.column-picking_date', 'td.picking_date', '.column-ed_date', 'td.ed_date'];
    selectors.forEach(selector => {
        $(selector).each(function () {
            const $element = $(this);
            const decodedHtml = htmlDecode($element.html());
            $element.html(decodedHtml);

            const $span = $element.find('span');
            if ($span.length) {
                $span.prop('title', $span.data('originalTitle'));
            }
        });
    });

    $(`${selectors.join(' [data-toggle="tooltip"], ')} [data-toggle="tooltip"]`).tooltip();
}

document.addEventListener('DOMContentLoaded', function () {
    decodeHtmlContent();

    $(document).ajaxComplete(function () {
        decodeHtmlContent();
    });
});
