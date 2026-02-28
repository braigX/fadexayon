/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize and bind state updates for #ed_prod_dis
    const edProdDisElement = $('#ed_prod_dis');
    // Initialize and bind state updates for combination inputs
    const combiSelector = 'input.ed_combi_dis';

    if (edProdDisElement.length) {
        updateEDOptions(edProdDisElement);
        edProdDisElement.on('change', function () {
            updateEDOptions($(this));
        });
    }

    $(document).on('change', combiSelector, function () {
        updateCombiState($(this));
    });
    $(combiSelector).each(function () {
        updateCombiState($(this));
    });

    // Toggle Estimated Delivery options
    function updateEDOptions(element) {
        const isChecked = element.prop('checked');
        $('.ed_status').toggleClass('ed_disabled', isChecked);
        toggleReadonly('.ed_status input', isChecked);

        if (isChecked) {
            $('.disabled-warning').stop(true, true).removeClass('d-none').fadeIn(500);
        } else {
            $('.disabled-warning').stop(true, true).fadeOut(500, function () {
                $(this).addClass('d-none');
            });

            // Recheck combination data when reenabling product options
            recheckCombinationStates();
        }
    }

    // Recheck all combination states
    function recheckCombinationStates() {
        $(combiSelector).each(function () {
            updateCombiState($(this));
        });
    }

    // Toggle state for combination inputs
    function updateCombiState(element) {
        const isChecked = element.prop('checked');
        const row = element.closest('tr');
        if (!row.length) {
            console.warn('No parent row found for', element);
            return;
        }

        row.toggleClass('ed_disabled', isChecked)
            .find('input').prop('readonly', isChecked);
    }

    // Helper function to toggle readonly state
    function toggleReadonly(selector, state) {
        $(selector).prop('readonly', state);
    }
});
