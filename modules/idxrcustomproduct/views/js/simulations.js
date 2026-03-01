$(function () {
    function request(action, payload) {
        var data = payload || {};
        data.action = action;
        return $.post(idxr_sim_url_ajax, data);
    }

    function parseResponse(resp) {
        if (typeof resp === 'string') {
            try {
                resp = JSON.parse(resp);
            } catch (e) {
                return { success: false, message: idxr_sim_msg_error };
            }
        }
        return resp || { success: false, message: idxr_sim_msg_error };
    }

    $(document).on('click', '.idxr-sim-rename', function () {
        var $btn = $(this);
        var id = $btn.data('id');
        var currentName = $btn.data('name') || '';
        var nextName = window.prompt(idxr_sim_msg_rename || 'Rename simulation', currentName);
        if (nextName === null) {
            return;
        }
        nextName = $.trim(nextName);
        if (!nextName.length) {
            alert(idxr_sim_msg_rename_error || 'Name cannot be empty');
            return;
        }
        request('renameServerCustomization', {
            id_saved_customisation: id,
            name: nextName
        }).done(function (resp) {
            var out = parseResponse(resp);
            if (!out.success) {
                alert(out.message || idxr_sim_msg_error);
                return;
            }
            var $row = $('#idxr-sim-row-' + id);
            $row.find('.idxr-sim-name').text(nextName);
            $row.find('.idxr-sim-rename').data('name', nextName);
        }).fail(function () {
            alert(idxr_sim_msg_error);
        });
    });

    $(document).on('click', '.idxr-sim-duplicate', function () {
        var id = $(this).data('id');
        request('duplicateServerCustomization', {
            id_saved_customisation: id
        }).done(function (resp) {
            var out = parseResponse(resp);
            if (!out.success) {
                alert(out.message || idxr_sim_msg_error);
                return;
            }
            window.location.reload();
        }).fail(function () {
            alert(idxr_sim_msg_error);
        });
    });

    $(document).on('click', '.idxr-sim-delete', function () {
        var id = $(this).data('id');
        if (!window.confirm(idxr_sim_msg_delete || 'Delete this simulation?')) {
            return;
        }
        request('deleteServerCustomization', {
            id_saved_customisation: id
        }).done(function (resp) {
            var out = parseResponse(resp);
            if (!out.success) {
                alert(out.message || idxr_sim_msg_error);
                return;
            }
            $('#idxr-sim-row-' + id).remove();
            if (!$('.idxr-simulations-list tbody tr').length) {
                window.location.reload();
            }
        }).fail(function () {
            alert(idxr_sim_msg_error);
        });
    });

    $(document).on('click', '.idxr-sim-thumb-open', function (e) {
        // Prevent row/button bubbling side effects.
        e.preventDefault();
        e.stopPropagation();
        var html = $(this).find('.idxr-sim-thumb-svg').html() || '';
        if (!html) {
            return;
        }
        $('#idxr-sim-lightbox-svg').html(html);
        var $svg = $('#idxr-sim-lightbox-svg').find('svg').first();
        if ($svg.length) {
            // Force large dimensions regardless of inline width/height in saved markup.
            $svg.removeAttr('id');
            $svg.attr('width', '86vw');
            $svg.attr('height', '84vh');
            $svg.css({
                width: '86vw',
                height: '84vh',
                maxWidth: '88vw',
                maxHeight: '86vh',
                display: 'block'
            });
        }
        $('#idxr-sim-lightbox').addClass('is-open');
    });

    $(document).on('click', '#idxr-sim-lightbox', function (e) {
        if (e.target.id === 'idxr-sim-lightbox') {
            $('#idxr-sim-lightbox').removeClass('is-open');
            $('#idxr-sim-lightbox-svg').html('');
        }
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            $('#idxr-sim-lightbox').removeClass('is-open');
            $('#idxr-sim-lightbox-svg').html('');
        }
    });
});
