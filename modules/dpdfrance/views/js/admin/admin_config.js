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
    let connectionSetting    = $('#mod_connect');
    let extensionFileSetting = $('#mod_format');
    let printFormatSetting   = $('#print_format');
    let inputPrinterPort     = $('input[name="format_printer_port"]');
    let inputPrinterIp       = $('input[name="format_printer_ip"]');

    $('body').on('change', '#mod_format', function (e) {
        let value      = $(this).val();
        let optionFile = $('#a4');
        if (value === 'pdf') {
            optionFile.show();
        } else {
            optionFile.hide();
            $('#a6').prop('selected', true);
        }
    })

    $('body').on('change', '#services', function (e) {
        let value = $(this).val();

        if (value === 'station_dat') {
            printFormatSetting.addClass('disabled');
            printFormatSetting.attr('disabled', true);
            extensionFileSetting.addClass('disabled');
            extensionFileSetting.attr('disabled', true);
            connectionSetting.addClass('disabled');
            connectionSetting.attr('disabled', true);
            inputPrinterPort.addClass('disabled');
            inputPrinterPort.attr('disabled', true);
            inputPrinterIp.addClass('disabled');
            inputPrinterIp.attr('disabled', true);
        } else if (value === 'e_print') {
            printFormatSetting.removeClass('disabled');
            printFormatSetting.removeAttr('disabled');
            extensionFileSetting.removeClass('disabled');
            extensionFileSetting.removeAttr('disabled');
            connectionSetting.removeClass('disabled');
            connectionSetting.removeAttr('disabled');
            if (connectionSetting.val() === 'usb') {
                inputPrinterPort.addClass('disabled');
                inputPrinterPort.attr('disabled', true);
                inputPrinterIp.addClass('disabled');
                inputPrinterIp.attr('disabled', true);
            } else {
                inputPrinterPort.removeClass('disabled');
                inputPrinterPort.removeAttr('disabled');
                inputPrinterIp.removeClass('disabled');
                inputPrinterIp.removeAttr('disabled');
            }
        }
    });

    $('body').on('change', '#mod_connect', function (e) {
        let value = $(this).val();
        if (value === 'usb') {
            inputPrinterPort.addClass('disabled');
            inputPrinterPort.attr('disabled', true);
            inputPrinterIp.addClass('disabled');
            inputPrinterIp.attr('disabled', true);
            $('#mod_format option[value="zpl"]').hide();
            $('#mod_format option[value="epl"]').hide();
            $('#mod_format option[value="pdf"]').prop('selected', true);
            $('#print_format option[value="a4"]').show();
            $('#print_format option[value="a4"]').prop('selected', true);

        } else if (value === 'ip') {
            inputPrinterPort.removeClass('disabled');
            inputPrinterPort.removeAttr('disabled');
            inputPrinterIp.removeClass('disabled');
            inputPrinterIp.removeAttr('disabled');
            $('#mod_format option[value="zpl"]').show();
            $('#mod_format option[value="epl"]').show();
            $('#mod_format option[value="zpl"]').prop('selected', true);
            $('#print_format option[value="a4"]').hide();
            $('#print_format option[value="a6"]').prop('selected', true);
        }
    });

    $('body').on('change', '#hide_network', function (e) {
        let hide = $(this).val();
        if (hide == 'true') {
            $('#mod_connect option[value="ip"]').show();
        }
    })

    let service = $('#services').val();
    if (service === 'station_dat') {
        printFormatSetting.addClass('disabled');
        printFormatSetting.attr('disabled', true);
        extensionFileSetting.addClass('disabled');
        extensionFileSetting.attr('disabled', true);
        connectionSetting.addClass('disabled');
        connectionSetting.attr('disabled', true);
        inputPrinterPort.addClass('disabled');
        inputPrinterPort.attr('disabled', true);
        inputPrinterIp.addClass('disabled');
        inputPrinterIp.attr('disabled', true);
    } else if (service === 'e_print') {
        printFormatSetting.removeClass('disabled');
        printFormatSetting.removeAttr('disabled');
        extensionFileSetting.removeClass('disabled');
        extensionFileSetting.removeAttr('disabled');
        connectionSetting.removeClass('disabled');
        connectionSetting.removeAttr('disabled');
        if (connectionSetting.val() === 'usb') {
            inputPrinterPort.addClass('disabled');
            inputPrinterPort.attr('disabled', true);
            inputPrinterIp.addClass('disabled');
            inputPrinterIp.attr('disabled', true);
        } else {
            inputPrinterPort.removeClass('disabled');
            inputPrinterPort.removeAttr('disabled');
            inputPrinterIp.removeClass('disabled');
            inputPrinterIp.removeAttr('disabled');
        }
    }
    let leadTimeDisplay = $('select[name="day_definite_mode"]').val();
    leadTimeDisplay === '1' ? $('#leadtime_config').show() : $('#leadtime_config').hide();
    $('body').on('change', 'select[name="day_definite_mode"]', function (e) {
        let hide_day_definite = $(this).val();
        hide_day_definite === '1' ? $('#leadtime_config').show() : $('#leadtime_config').hide();
    })
})

/**
 * Change the carrier value on select list
 */
let dpdfrance_attr_carrier = (element) => {
    var maxValue = undefined;
    $('option', element).each(function () {
        var val = $(this).attr('value');
        val     = parseInt(val, 10);
        if (maxValue === undefined || maxValue < val) {
            maxValue = val;
        }
    });
    element.val(maxValue);
};

/**
 * Open the module doc
 */
const DPDFranceHandleOpenModuleDoc = (pudoUrl = false) => {
    let docPathFinal = pudoUrl ? docPath + '#page=8' : docPath;
    window.open(docPathFinal, 's', 'width= 640, height= 900, left=0, top=0, resizable=yes, toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no');
}