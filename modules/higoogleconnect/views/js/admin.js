/**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 */
$(function(){
    $('#form-higoogleuser').hiPrestaTable({
        friendlyName: 'GoogleUser',
        secureKey: hiGoogleConnectSecureKey,
        ajaxUrl: hiGoogleConnectAdminController,
        identifier: 'id_user'
    });
});

hiGoogleConnect = {
    displayPositionForm: function(id_position = 0, $sel = null) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGoogleConnectAdminController,
            data: {
                ajax: true,
                action: 'displayPositionForm',
                secure_key: hiGoogleConnectSecureKey,
                id_position: id_position
            },
            beforeSend: function() {
                $sel.find('i').removeClass('icon-pencil').addClass('icon-refresh icon-spin');
            },
            success: function(response) {
                $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-pencil');

                if (typeof response.error !== 'undefined' && response.error) {
                    showErrorMessage(response.error);
                } else {
                    $("#hi-google-connect-modal-form .content").html(response.content);
                    $('#hi-google-connect-modal-form').modal('show');

                    hiGoogleConnect.generatePreviewButton();
                }
            },
            error: function(jqXHR, error, errorThrown) {
                $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-pencil');

                if (jqXHR.status && jqXHR.status == 400) {
                    showErrorMessage(jqXHR.responseText);
                } else {
                    showErrorMessage(ajaxErrorMessage);
                }
            }
        });
    },

    savePositionSettings: function() {
        var $form = $('#higoogleposition_form');
        var $button = $('[name="savePositionSettings"]');

        var formdata = new FormData($('#higoogleposition_form')[0]);
        formdata.append('action', 'savePositionSettings');
        formdata.append('secure_key', hiGoogleConnectSecureKey);
        formdata.append('ajax', true);

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGoogleConnectAdminController,
            data: formdata,
            contentType: false,
            processData: false,
            beforeSend: function(){
                $button.find('i.process-icon-save').removeClass('process-icon-save').addClass('process-icon-refresh icon-spin');
            },
            success: function(response) {
                $button.find('i.process-icon-refresh').removeClass('process-icon-refresh icon-spin').addClass('process-icon-save');
                if (response.error) {
                    showErrorMessage(response.error);
                } else {
                    showSuccessMessage(response.message);

                    $('#form-higoogleconnect').replaceWith(response.content);
                    hiGoogleConnect.generatePositionsPreviewButton();

                    $('#hi-google-connect-modal-form').modal('hide');
                }
            },
            error: function(jqXHR, error, errorThrown) {
                $button.find('i.process-icon-refresh').removeClass('process-icon-refresh icon-spin').addClass('process-icon-save');

                if (jqXHR.status && jqXHR.status == 400) {
                    showErrorMessage(jqXHR.responseText);
                } else {
                    showErrorMessage(ajax_error_message);
                }
            }
        });
    },

    updatePositionStatus: function(id_position, status, $sel) {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGoogleConnectAdminController,
            data: {
                ajax: true,
                action: 'updatePositionStatus',
                secure_key: hiGoogleConnectSecureKey,
                id_position: id_position,
                status: status
            },
            beforeSend: function() {
                $sel.find('i').removeClass('icon-check').addClass('icon-refresh icon-spin');
            },
            success: function(response) {
                $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-check');

                if (response.error) {
                    showErrorMessage(response.error);
                } else {
                    showSuccessMessage(response.message);

                    $('#form-higoogleconnect').replaceWith(response.content);
                    hiGoogleConnect.generatePositionsPreviewButton();
                }
            },
            error: function(jqXHR, error, errorThrown) {
                $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-check');

                if (jqXHR.status && jqXHR.status == 400) {
                    showErrorMessage(jqXHR.responseText);
                } else {
                    showErrorMessage(ajax_error_message);
                }
            }
        });
    },

    generatePreviewButton: function() {
        $('#googleButtonPreview').html('');
        let type = $('#buttonType').val();
        let theme = $('#buttonTheme').val();
        let shape = $('#buttonShape').val();
        let text = $('#buttonText').val();
        let size = $('#buttonSize').val();


        google.accounts.id.initialize({
            client_id: googleClientId,
            callback: hiGoogleConnectPreviewResponse
        });
        google.accounts.id.renderButton(
            document.getElementById("googleButtonPreview"),
            { type: type, shape: shape, theme: theme, text: text, size: size }
        );
    },

    generatePositionsPreviewButton: function() {
        $('.hiGoogleButtonPreview').each(function(){
            $(this).html('');

            let type = $(this).attr('data-type');
            let theme = $(this).attr('data-theme');
            let shape = $(this).attr('data-shape');
            let text = $(this).attr('data-text');
            let size = $(this).attr('data-size');

            google.accounts.id.initialize({
                client_id: googleClientId,
                callback: hiGoogleConnectPreviewResponse
            });
            google.accounts.id.renderButton(
                this,
                { type: type, shape: shape, theme: theme, text: text, size: size }
            );
        });
    },

    updateRegistrationsChart: function() {
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: hiGoogleConnectAdminController,
            data: {
                ajax: true,
                action: 'updateRegistrationsChart',
                secure_key: hiGoogleConnectSecureKey,
                dateType: $('[name="pieChartDate"]:checked').val(),
                dateFrom: $('#chartDatepickerFrom').val(),
                dateTo: $('#chartDatepickerTo').val()
            },
            beforeSend: function() {
                // $sel.find('i').removeClass('icon-pencil').addClass('icon-refresh icon-spin');
            },
            success: function(response) {
                // $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-pencil');

                if (typeof response.error !== 'undefined' && response.error) {
                    showErrorMessage(response.error);
                } else {
                    showSuccessMessage(response.message);
                    const chart = Chart.getChart('hi-google-connect-users-chart');
                    chart.data.labels = [googleRegistrationsTxt + ' ( ' + response.registrationData.totalGoogleUsers + ' )', otherTxt + ' ( ' + response.registrationData.totalOtherRegistrations + ' )'];
                    chart.data.datasets[0].data = [response.registrationData.totalGoogleUsers, response.registrationData.totalOtherRegistrations];
                    chart.update();
                }
            },
            error: function(jqXHR, error, errorThrown) {
                // $sel.find('i').removeClass('icon-refresh icon-spin').addClass('icon-pencil');

                if (jqXHR.status && jqXHR.status == 400) {
                    showErrorMessage(jqXHR.responseText);
                } else {
                    showErrorMessage(ajaxErrorMessage);
                }
            }
        });
    },

    initPieChart: function() {
        const totalOtherRegistrations = $('#hi-google-connect-users-chart').attr('data-other-registrations');
        const totalGoogleRegistrations = $('#hi-google-connect-users-chart').attr('data-google-registrations');
        const data = {
            labels: [googleRegistrationsTxt + ' ( ' + totalGoogleRegistrations + ' )', otherTxt + ' ( ' + totalOtherRegistrations + ' )'],
            datasets: [{
                label: registrationsCountTxt,
                data: [totalGoogleRegistrations, totalOtherRegistrations],
                backgroundColor: [
                'rgb(26, 115, 232)',
                'rgb(255, 99, 132)'
                ],
                hoverOffset: 4
            }]
        };
        const config = {
            type: 'pie',
            data: data,
        };

        const ctx = document.getElementById('hi-google-connect-users-chart').getContext("2d");
        ctx.canvas.width = 350;
        ctx.canvas.height = 350;

        new Chart(ctx, config);
    }
};

$(function() {
    $(document).on('click', '[name="cancelPositionSettings"]', function(){
        $('#hi-google-connect-modal-form').modal('hide');

        return false;
    });

    $(document).on('submit', '#higoogleposition_form', function(){
        hiGoogleConnect.savePositionSettings();

        return false;
    });

    $(document).on('click', '.higoogleconnect .edit', function() {
        var id_position = $(this).attr('href').match(/id_position=([0-9]+)/)[1];
        hiGoogleConnect.displayPositionForm(id_position, $(this));

        return false;
    });

    $(document).on('click', '.higoogleconnect .hi-google-connect-position-status', function() {
        var id_position = $(this).attr('data-id');
        var status = $(this).attr('data-status');
        hiGoogleConnect.updatePositionStatus(id_position, status, $(this));

        return false;
    });

    // button preview handlers
    $(document).on('change', '#buttonType, #buttonTheme, #buttonShape, #buttonText, #buttonSize', function() {
        hiGoogleConnect.generatePreviewButton();
    });

    // button preview for positions list
    setTimeout(function() {
        hiGoogleConnect.generatePositionsPreviewButton();
    }, 2000);

    if ($('.hi-google-users-chart').length > 0) {
        hiGoogleConnect.initPieChart();
        
        $(document).on('change', '[name="pieChartDate"]', function () {
            if ($(this).val() == 'custom') {
                $('.chart-custom-dates').removeClass('hi-hide');
            } else {
                $('.chart-custom-dates').addClass('hi-hide');
            }
        });


        $('.chart-custom-dates .datepicker').datepicker({
            prevText: '',
            nextText: '',
            dateFormat: 'yy-mm-dd'
        });

        $(document).on('change', '[name="pieChartDate"], #chartDatepickerFrom, #chartDatepickerTo', function() {
            hiGoogleConnect.updateRegistrationsChart();
        });

        $(document)
            .on('click', '#desc-googleConnectChart-help', function(e) {
                e.preventDefault();
                e.stopPropagation();

                $('#hmd-modal .hmd-item').hide();
                $('#hmd-modal .hmd-item[data-doc="googleConnectChartHelp"]').show();
                $('#hmd-modal').addClass('hmd-sidebar-open');
            })
    }
});

function hiGoogleConnectPreviewResponse(res) {
    alert(adminPreviewResponseMessage);
}