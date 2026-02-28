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
    $('#save').on('click', function (e) {
        let orderId = $(this).data('order');
        if (
            $('#street').val() === '' ||
            $('#zip').val() === '' ||
            $('#city').val() === '' ||
            $('#phone').val() === '' ||
            $('#zip').val().length < 5 ||
            $('#zip').val().length > 5
        ) {
            e.preventDefault();
            if ($('#street').val() == '') {
                $('#street').css('background-color', '#e40c37');
            }
            if ($('#zip').val() == '' || $('#zip').val().length < 5 || $('#zip').val().length > 5) {
                $('#zip').css('background-color', '#e40c37');
            }
            if ($('#city').val() == '') {
                $('#city').css('background-color', '#e40c37');
            }
            if ($('#phone').val() == '') {
                $('#phone').css('background-color', '#e40c37');
            }
        } else {
            $.ajax(
                {
                    url    : dpdfrance_base_dir + '/ajax.php',
                    method : 'post',
                    data   : {
                        'action_ajax_dpdfrance': 'setAddressOrder',
                        'order'                : orderId,
                        'street'               : $('#street').val(),
                        'zip'                  : $('#zip').val(),
                        'city'                 : $('#city').val(),
                        'phone'                : $('#phone').val()
                    },
                    success: function (data) {
                        data = $.parseJSON(data);
                        if (data.update == true) {
                            $('#close-button').click();
                        }
                    }
                }
            );
        }
    });

    $('body').on('keyup', '#street', function (e) {
        $('#street').css('background-color', '');
    });

    $('body').on('keyup', '#zip', function (e) {
        $('#zip').css('background-color', '');
    });

    $('body').on('keyup', '#city', function (e) {
        $('#city').css('background-color', '');
    });

    $('body').on('keyup', '#phone', function (e) {
        $('#phone').css('background-color', '');
    });

    $('.modal-form-change').on('click', function (e) {
        let orderId = $(this).data('order');

        $.ajax(
            {
                url    : dpdfrance_base_dir + '/ajax.php',
                method : 'post',
                data   : {
                    'action_ajax_dpdfrance': 'getAddressOrder',
                    'order'                : orderId
                },
                success: function (data) {
                    data = $.parseJSON(data);
                    $('#street').val(data.street);
                    $('#zip').val(data.zip);
                    $('#city').val(data.city);
                    $('#phone').val(data.phone);
                    $('#save').data('order', orderId);
                }
            }
        );
    });

    $('.print-click').on('click', function () {
        let id = $(this).data('id');
        printOne(id);
    });

    $('#exportform').on('submit', function () {
        refreshPage();
    });

    function printOne(id) {
        $(".checkbox-id").prop('checked', false);
        $("#checkbox_" + id).prop('checked', true);
        $('#exportLabel').click();
        $("#checkbox_" + id).prop('checked', false);

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
})