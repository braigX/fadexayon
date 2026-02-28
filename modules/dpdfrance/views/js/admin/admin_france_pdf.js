/*!
 * DPD France - JavaScript
 * version: 1.0 (Mon, 23 AUT 2021)
 * @requires jQuery v1.6 or later
 *
 *
 * Copyright 2021 DPD France
 *
 */
$(document).ready(function () {
    $(".add-weight").on('click', function () {
        var id     = $(this).data('id');
        var unit   = $(this).data('unit');
        var number = parseInt($(this).data('number'));

        number++;

        $(this).data('number', number);
        $(this).parent().find('.remove-weight').data('number', number);

        var newParecel = multiParcelPrototype;

        newParecel = newParecel.replaceAll("__id_order__", id);
        newParecel = newParecel.replace("__unit_order__", unit);
        newParecel = newParecel.replace("__number_parcel__", number);

        $(this).before(newParecel);

        if ($(this).parent().find('div').length > 2) {
            $(this).parent().find('.remove-weight').show();
        }
    });

    $('.remove-weight').on('click', function () {
        var number = parseInt($(this).data('number'));
        number     = number - 1;
        $(this).data('number', number);
        $(this).parent().find('.add-weight').data('number', number);
        $(this).parent().find('div').last().remove();
        if ($(this).parent().find('div').length < 3) {
            $(this).hide();
        }
    });

    $('body').on('keydown', '.parcelweight', function (e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            $(this).parent().parent().parent().find('.checkbox').prop('checked', true);
        }
    });

    if (dpdfrance_mode_service === "e_print") {
        /**
         * Au clique de l'icone imprimante
         */
        $('.print-click').on('click', function () {
            let id = $(this).data('id');
            printOne(id);
        });

        /**
         * Au clique du bouton "Imprimer les étiquettes"
         * Si on envoie le formulaire et qu'il n'y a pas d'erreurs alors on rafraichit la page
         * Sinon, on arrête l'exécution de la suite du script
         */
        $.when($('#exportLabel').on('click', function () {
            $('#fbody').find('.checkbox-id:checked').each(function () {
                if (isParcelWeightInvalid($(this))) {
                    event.preventDefault();
                }
            });
        }))
        .then(() => refreshPage());
    }

    /**
     * Check Parcel Weight input value is valid
     */
    function isParcelWeightInvalid(orderId) {
        let parcelWeightIsInvalid = false;
        let invalidWeight          = [];
        let validWeight            = [];
        let stop                   = true;
        let isNotInteger           = true;

        if (Number.isInteger(orderId)) {
            isNotInteger = false;
        }

        /**
         * Nous avons
         * => orderId au click de "imprimer les étiquettes"
         * => $("#checkbox_" + orderId) au click de "l'icône imprimante"
         */
        let selector = isNotInteger ? orderId : $("#checkbox_" + orderId);
        let parcelWeightInputList = selector.parent('td').parent('tr').find('.parcelweight');

        parcelWeightInputList.each(function () {
            $(this).attr('value', $(this).val());
            let formattedValue = ($(this).val()).replace(/,+/, '.');
            /**
             * Remove white spaces
             */
            formattedValue = formattedValue.trim();

            /**
             * Check for an empty string or if formattedValue is not a number (regex expression and truthy value)
             */
            if ((/[^0-9.]/.test(formattedValue)) || isNaN(formattedValue) || !formattedValue) {
                /**
                 * Here we know the value is always invalid (empty, NaN or a mix of number and something else e.g. special characters)
                 */
                invalidWeight.push($(this).val());
                stop = false;
            } else {
                /**
                 * Here we know the value is always valid, but we need to check if it is superior to 0
                 */
                if (!(parseFloat(formattedValue) > 0)) {
                    invalidWeight.push($(this).val());
                    stop = false;
                } else {
                    validWeight.push($(this).val());
                }
            }

            let weightLabelSelector = $(this).parent('div').parent('.poidslabel');
            if (stop === false) {
                /**
                 * Display tooltip of the order row
                 */
                $(this).parent('div').parent('td').find('.tooltip_weight').show().delay(4000).fadeOut();

                /**
                 * Add the class of invalid weight input
                 */
                for (let i = 0; i < invalidWeight.length; i++) {
                    weightLabelSelector.find('.parcelweight[value="'+invalidWeight[i]+'"]').addClass('noweight_warning');
                }
                parcelWeightIsInvalid = true;
            }

            /**
             * Remove the class of invalid weight input
             */
            if (validWeight.length !== 0) {
                for (let i = 0; i < validWeight.length; i++) {
                    weightLabelSelector.find('.parcelweight[value="'+validWeight[i]+'"]').removeClass('noweight_warning');
                }
            }
        });
        return parcelWeightIsInvalid;
    }

    function printOne(id) {
        var cheboxCache  = [];

        if (isParcelWeightInvalid(id)) {
            return false;
        }

        let checkBoxlist     = $(".checkbox-id");
        let selectedCheckBox = $("#checkbox_" + id);

        checkBoxlist.each(function (e, data) {
            if ($(this).is(":checked")) {
                cheboxCache[cheboxCache.length] = $(this).val();
            }
        });

        checkBoxlist.prop('checked', false);

        selectedCheckBox.prop('checked', true);

        $('#exportLabel').click();

        selectedCheckBox.prop('checked', false);
        $("#labelprint_" + id).addClass('text-success');

        $('.parcelweight').prop('disabled', false);
        /**
         * On imprime selon les étiquettes selon les order row où la checkbox est cochée
         */
        $.each(cheboxCache, function (e, data) {
            $("#checkbox_" + data).prop('checked', true);
        });
    }

    function refreshPage() {
        $.ajax(
            {
                type    : 'GET',
                url     : dpdfrance_base_dir + '/ajax.php',
                data    : {
                    'action_ajax_dpdfrance': 'ajaxRefreshListPdf'
                },
                dataType: 'json',
                success : function (data) {
                    data = eval(data);
                    if (data == "true") {
                        location.reload();
                    } else {
                        setTimeout(function () {
                            refreshPage();
                        }, 1000);
                    }
                },
                error   : function (data) {
                    setTimeout(function () {
                        refreshPage();
                    }, 1000);
                }
            }
        );
    }
});
