(function ($) {
    'use strict';

    function esc(text) {
        return $('<div/>').text(text === null || typeof text === 'undefined' ? '' : String(text)).html();
    }

    function asNumber(value) {
        return parseFloat(String(value || '').replace(',', '.')) || 0;
    }

    function labels() {
        return window.idxr_pricing_rates_labels || {};
    }

    function ajaxUrl() {
        return window.idxr_pricing_rates_ajax_url || '';
    }

    function setForm(data) {
        $('#idxr-rate-id').val(data && data.id_rate ? data.id_rate : '');
        $('#idxr-rate-thickness').val(data && data.thickness_mm ? data.thickness_mm : '');
        $('#idxr-rate-cut').val(data && data.cut_rate ? data.cut_rate : '');
        $('#idxr-rate-glue').val(data && data.glue_rate ? data.glue_rate : '');
        $('#idxr-rate-polish').val(data && data.polish_rate ? data.polish_rate : '');
        $('#idxr-rate-active').prop('checked', !data || parseInt(data.active, 10) === 1);
        $('#idxr-rate-cancel').toggle(!!(data && data.id_rate));
    }

    function renderRows(rows) {
        var $tbody = $('#idxr-thickness-rates-table tbody');
        $tbody.empty();
        if (!rows || !rows.length) {
            $tbody.append('<tr><td colspan="6">' + esc(labels().empty || 'No rows') + '</td></tr>');
            return;
        }
        $.each(rows, function (_, row) {
            var activeText = parseInt(row.active, 10) === 1 ? 'Oui' : 'Non';
            var tr = '' +
                '<tr data-id="' + esc(row.id_rate) + '">' +
                '  <td>' + esc(row.thickness_mm) + '</td>' +
                '  <td>' + esc(row.cut_rate) + '</td>' +
                '  <td>' + esc(row.glue_rate) + '</td>' +
                '  <td>' + esc(row.polish_rate) + '</td>' +
                '  <td>' + esc(activeText) + '</td>' +
                '  <td>' +
                '    <button type="button" class="btn btn-default btn-xs idxr-rate-edit">' + esc(labels().edit || 'Edit') + '</button> ' +
                '    <button type="button" class="btn btn-danger btn-xs idxr-rate-delete">' + esc(labels().delete || 'Delete') + '</button>' +
                '  </td>' +
                '</tr>';
            $tbody.append(tr);
        });
    }

    function api(action, payload) {
        payload = payload || {};
        payload.idxr_action = action;
        return $.post(ajaxUrl(), payload).then(function (res) {
            if (typeof res === 'string') {
                try {
                    res = JSON.parse(res);
                } catch (e) {
                    return $.Deferred().reject('Invalid JSON').promise();
                }
            }
            if (!res || res.success !== true) {
                return $.Deferred().reject(res && res.message ? res.message : (labels().error || 'Request failed')).promise();
            }
            return res;
        });
    }

    function loadRows() {
        var $tbody = $('#idxr-thickness-rates-table tbody');
        if (!$tbody.length || !ajaxUrl()) {
            return;
        }
        $tbody.html('<tr><td colspan="6">' + esc(labels().loading || 'Loading...') + '</td></tr>');
        api('list_thickness_rates').then(function (res) {
            renderRows(res.rows || []);
        }).fail(function (err) {
            $tbody.html('<tr><td colspan="6">' + esc(err || labels().error || 'Error') + '</td></tr>');
        });
    }

    function saveRow() {
        var payload = {
            id_rate: $('#idxr-rate-id').val() || '',
            thickness_mm: $('#idxr-rate-thickness').val(),
            cut_rate: $('#idxr-rate-cut').val(),
            glue_rate: $('#idxr-rate-glue').val(),
            polish_rate: $('#idxr-rate-polish').val(),
            active: $('#idxr-rate-active').is(':checked') ? 1 : 0
        };
        if (asNumber(payload.thickness_mm) <= 0) {
            alert('Thickness must be greater than 0.');
            return;
        }
        api('save_thickness_rate', payload).then(function () {
            setForm(null);
            loadRows();
        }).fail(function (err) {
            alert(err || labels().error || 'Error');
        });
    }

    function deleteRow(id) {
        if (!window.confirm(labels().confirmDelete || 'Delete this row?')) {
            return;
        }
        api('delete_thickness_rate', { id_rate: id }).then(function () {
            if ($('#idxr-rate-id').val() === String(id)) {
                setForm(null);
            }
            loadRows();
        }).fail(function (err) {
            alert(err || labels().error || 'Error');
        });
    }

    $(document).ready(function () {
        if (!$('#idxr-thickness-rates-table').length) {
            return;
        }

        loadRows();

        $('#idxr-rate-save').on('click', function () {
            saveRow();
        });

        $('#idxr-rate-cancel').on('click', function () {
            setForm(null);
        });

        $(document).on('click', '.idxr-rate-edit', function () {
            var $tr = $(this).closest('tr');
            var cells = $tr.find('td');
            setForm({
                id_rate: $tr.data('id'),
                thickness_mm: $(cells[0]).text().trim(),
                cut_rate: $(cells[1]).text().trim(),
                glue_rate: $(cells[2]).text().trim(),
                polish_rate: $(cells[3]).text().trim(),
                active: ($(cells[4]).text().trim().toLowerCase() === 'oui') ? 1 : 0
            });
        });

        $(document).on('click', '.idxr-rate-delete', function () {
            var id = $(this).closest('tr').data('id');
            if (!id) {
                return;
            }
            deleteRow(id);
        });
    });
})(jQuery);
