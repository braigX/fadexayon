/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL
* @license   INNOVADELUXE
*/

var actual_cover = '';
var es17 = false;
if (typeof(prestashop) != "undefined" && prestashop !== null){
    es17 = true;
}
var block_animation = false;
var debug = false;
var idxcp_newtax = idxcp_originaltax
var isAddingToCart = false;
var idxrRestoreFromUrlInProgress = false;
var idxrRestoreFromUrlHandled = false;
var idxrI18n = {
    loading: window.idxr_tr_loading || 'Loading...',
    saving: window.idxr_tr_saving || 'Saving...',
    restoring: window.idxr_tr_restoring || 'Restoring...',
    saveCustomization: window.idxr_tr_save_customization || 'Save customization',
    restoreCustomization: window.idxr_tr_restore_customization || 'Restore customization',
    loginToSave: window.idxr_tr_login_to_save_customisations || 'Login to save customisations',
    previewCurrentSelection: window.idxr_tr_preview_current_selection || 'Preview of current selection',
    pleaseEnterName: window.idxr_tr_please_enter_name || 'Please enter a name.',
    pleaseSelectOne: window.idxr_tr_please_select_one_customization || 'Please select one customization.',
    cancel: window.idxr_tr_cancel || 'Cancel',
    save: window.idxr_tr_save || 'Save',
    restore: window.idxr_tr_restore || 'Restore',
    noSavedCustomizationsForProduct: window.idxr_tr_no_saved_customizations_for_product || 'No saved customizations for this product.',
    unnamedCustomization: window.idxr_tr_unnamed_customization || 'Unnamed customization',
    noPreview: window.idxr_tr_no_preview || 'No preview',
    noPreviewAvailable: window.idxr_tr_no_preview_available || 'No preview available.',
    myCustomization: window.idxr_tr_my_customization || 'My customization',
    unableLoadSavedCustomizations: window.idxr_tr_unable_load_saved_customizations || 'Unable to load saved customizations.',
    unableSaveCustomization: window.idxr_tr_unable_save_customization || 'Unable to save customization.',
    unableRestoreCustomization: window.idxr_tr_unable_restore_customization || 'Unable to restore customization.',
    unableApplySavedCustomization: window.idxr_tr_unable_apply_saved_customization || 'Unable to apply saved customization.',
    savedPayloadInvalid: window.idxr_tr_saved_payload_invalid || 'Saved customization payload is invalid.',
    requestFailed: window.idxr_tr_request_failed || 'Request failed.'
};

function normalizeExtraPayloadValue(value) {
    if (value === null || typeof value === 'undefined') {
        return 'false';
    }
    var normalized = ('' + value).trim();
    if (!normalized.length) {
        return 'false';
    }
    return normalized.replace('3x7r4', 'extra');
}

function buildExtraInfoPayload() {
    var extraByComponent = {};
    var order = [];

    $('.sel_opt_extra').each(function () {
        var componentId = parseInt($(this).attr('id').split("_")[3], 10);
        if (isNaN(componentId)) {
            return;
        }
        if (!Object.prototype.hasOwnProperty.call(extraByComponent, componentId)) {
            order.push(componentId);
        }
        extraByComponent[componentId] = normalizeExtraPayloadValue($(this).html());
    });

    var processedStep = {};
    $('.js_icp_next_option').each(function () {
        var stepId = ($(this).attr('id') || '').replace('js_icp_next_opt_', '');
        if (!stepId || processedStep[stepId]) {
            return;
        }
        processedStep[stepId] = true;

        var componentId = parseInt(stepId, 10);
        if (isNaN(componentId)) {
            return;
        }

        var stepContainer = $('#component_step_' + stepId);
        if (stepContainer.length && stepContainer.hasClass('hidden')) {
            return;
        }

        var selectedMarker = $('#js_opt_' + stepId + '_value').html();
        var extraMarker = $('#js_opt_extra_' + stepId + '_value').html();
        if (selectedMarker === 'false' && extraMarker === 'false') {
            return;
        }

        var stepType = $(this).attr('data-type');
        var liveValue;
        if (stepType === 'text') {
            liveValue = $('#text_' + stepId).val();
        } else if (stepType === 'textarea') {
            liveValue = $('#textarea_' + stepId).val();
        } else if (stepType === 'file') {
            liveValue = ($('#file_' + stepId).val() || '').replace(/C:\\fakepath\\/i, '');
        } else {
            return;
        }

        if (!Object.prototype.hasOwnProperty.call(extraByComponent, componentId)) {
            order.push(componentId);
        }
        extraByComponent[componentId] = normalizeExtraPayloadValue(liveValue);
    });

    var payload = '';
    var separator = '';
    $.each(order, function (_, componentId) {
        payload += separator + componentId + '_' + JSON.stringify(extraByComponent[componentId]);
        separator = '3x7r4';
    });

    return payload;
}

function syncInputExtraToHidden(stepId) {
    var stepType = $('#js_icp_next_opt_' + stepId).attr('data-type');
    var value = 'false';
    if (stepType === 'text') {
        value = $('#text_' + stepId).val();
    } else if (stepType === 'textarea') {
        value = $('#textarea_' + stepId).val();
    } else if (stepType === 'file') {
        value = ($('#file_' + stepId).val() || '').replace(/C:\\fakepath\\/i, '');
    }
    $('#js_opt_extra_' + stepId + '_value').html(normalizeExtraPayloadValue(value));
}

function idxrServerRequest(action, payload) {
    var data = payload || {};
    data.action = action;
    return $.post(url_ajax, data).then(function (response) {
        if (typeof response === 'string') {
            try {
                response = JSON.parse(response);
            } catch (e) {
                return $.Deferred().reject('invalid_json').promise();
            }
        }
        if (!response || response.success !== true) {
            return $.Deferred().reject(response && response.message ? response.message : 'request_failed').promise();
        }
        return response;
    });
}

function idxrErrorMessage(err, fallbackMessage) {
    if (typeof err === 'string' && err.length) {
        return err;
    }
    if (err && err.responseText) {
        try {
            var parsed = JSON.parse(err.responseText);
            if (parsed && parsed.message) {
                return parsed.message;
            }
        } catch (e) {
            // ignore parse errors and fallback below
        }
    }
    return fallbackMessage || idxrI18n.requestFailed;
}

function idxrSetRestoreButtonLoading(isLoading) {
    var $btn = $('#restore-customization-button-unique-12345');
    if (!$btn.length) {
        return;
    }

    if (isLoading) {
        if (!$btn.data('idxr-original-html')) {
            $btn.data('idxr-original-html', $btn.html());
        }
        if ($btn.data('idxr-original-disabled') === undefined) {
            $btn.data('idxr-original-disabled', $btn.prop('disabled'));
        }
        $btn.prop('disabled', true).addClass('disabled');
        $btn.html('<i class="cart-icon-unique-12345" style="background-color:#1e0978;"><img src="/modules/idxrcustomproduct/img/icon/loading.png" alt="" style="animation:idxrSpin 1s linear infinite;"></i><span>' + idxrEsc(idxrI18n.loading) + '</span>');
        if (!$('#idxr-restore-loading-style').length) {
            $('head').append('<style id="idxr-restore-loading-style">@keyframes idxrSpin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>');
        }
        return;
    }

    var originalHtml = $btn.data('idxr-original-html');
    if (originalHtml) {
        $btn.html(originalHtml);
    }
    var originallyDisabled = !!$btn.data('idxr-original-disabled');
    $btn.prop('disabled', originallyDisabled);
    $btn.toggleClass('disabled', originallyDisabled);
}

function idxrSetModalActionLoading(selector, isLoading, loadingText) {
    var $btn = $(selector);
    if (!$btn.length) {
        return;
    }

    if (isLoading) {
        if (!$btn.data('idxr-original-html')) {
            $btn.data('idxr-original-html', $btn.html());
        }
        if ($btn.data('idxr-original-disabled') === undefined) {
            $btn.data('idxr-original-disabled', $btn.prop('disabled'));
        }
        $btn.prop('disabled', true);
        $btn.html('<span style="display:inline-flex;align-items:center;gap:8px;"><img src="/modules/idxrcustomproduct/img/icon/loading.png" alt="" style="width:16px;height:16px;animation:idxrSpin 1s linear infinite;">' + idxrEsc(loadingText || idxrI18n.loading) + '</span>');
        return;
    }

    var originalHtml = $btn.data('idxr-original-html');
    if (originalHtml) {
        $btn.html(originalHtml);
    }
    var originallyDisabled = !!$btn.data('idxr-original-disabled');
    $btn.prop('disabled', originallyDisabled);
}

function idxrSetGlobalPreloader(isVisible) {
    var $overlay = $('#preloader-overlay-xyz123');
    var $spinner = $('#spinner-abc456');
    if (!$overlay.length && !$spinner.length) {
        return;
    }
    if (isVisible) {
        $overlay.show();
        $spinner.show();
    } else {
        $overlay.hide();
        $spinner.hide();
    }
}

function idxrResetAllBeforeRestore() {
    // Trigger module-specific "none" options first when present.
    if ($('#card_17_0').length) {
        $('#card_17_0').trigger('click');
    }
    if ($('#card_29_0').length) {
        $('#card_29_0').trigger('click');
    }

    $('.component_step').each(function () {
        var stepId = (($(this).attr('id') || '').replace('component_step_', ''));
        if (!stepId || stepId === 'last') {
            return;
        }

        $('input[name=option_' + stepId + ']').prop('checked', false);
        $('[id^="option_' + stepId + '_"][id$="_qty"]').val('');

        $('#text_' + stepId).val('');
        $('#textarea_' + stepId).val('');
        $('#file_' + stepId).val('');

        $('#js_opt_' + stepId + '_value').html('false');
        $('#js_opt_extra_' + stepId + '_value').html('false');
        $('#js_opt_' + stepId + '_value_wqty').html('false');

        $('#step_content_' + stepId).removeClass('finished');
        $('#step_title_' + stepId + ' .check').removeClass('check');
    });

    if (typeof checkFinish === 'function') {
        checkFinish();
    }
}

function idxrGetUrlParam(paramName) {
    if (typeof URLSearchParams !== 'undefined') {
        var params = new URLSearchParams(window.location.search || '');
        return params.get(paramName);
    }
    var match = (window.location.search || '').match(new RegExp('[?&]' + paramName + '=([^&]+)'));
    return match ? decodeURIComponent(match[1]) : null;
}

function idxrHasRestoreFromUrl() {
    return !!parseInt(idxrGetUrlParam('idxr_restore_sim'), 10);
}

function idxrRemoveUrlParam(paramName) {
    if (!window.history || !window.history.replaceState) {
        return;
    }
    var url = new URL(window.location.href);
    url.searchParams.delete(paramName);
    window.history.replaceState({}, document.title, url.toString());
}

function idxrAutoRestoreFromUrlIfNeeded() {
    if (idxrRestoreFromUrlInProgress || idxrRestoreFromUrlHandled) {
        return;
    }
    var savedId = parseInt(idxrGetUrlParam('idxr_restore_sim'), 10);
    if (!savedId) {
        return;
    }

    idxrRestoreFromUrlInProgress = true;
    idxrSetGlobalPreloader(true);
    idxrServerRequest('getServerCustomization', {
        id_saved_customisation: savedId
    }).done(function (response) {
        var snapshot = null;
        if (response.item && response.item.snapshot_json) {
            try {
                snapshot = JSON.parse(response.item.snapshot_json);
            } catch (e) {
                snapshot = null;
            }
        }
        if (!snapshot) {
            idxrRestoreFromUrlInProgress = false;
            idxrRestoreFromUrlHandled = true;
            idxrSetGlobalPreloader(false);
            return;
        }
        idxrResetAllBeforeRestore();
        idxrApplySnapshot(snapshot).then(function () {
            idxrRestoreFromUrlInProgress = false;
            idxrRestoreFromUrlHandled = true;
            idxrSetGlobalPreloader(false);
            idxrRemoveUrlParam('idxr_restore_sim');
        }).catch(function () {
            idxrRestoreFromUrlInProgress = false;
            idxrRestoreFromUrlHandled = true;
            idxrSetGlobalPreloader(false);
        });
    }).fail(function () {
        idxrRestoreFromUrlInProgress = false;
        idxrRestoreFromUrlHandled = true;
        idxrSetGlobalPreloader(false);
    });
}

function idxrAutoRestoreFromUrlWhenReady(attempt) {
    if (!idxrHasRestoreFromUrl()) {
        return;
    }

    var tryNum = parseInt(attempt, 10) || 0;
    var hasSteps = $('.component_step').length > 0;
    var hasContainer = $('#component_steps_container').length > 0;
    var canEvaluateVisibility = (typeof mustBeVisible === 'function');

    idxrSetGlobalPreloader(true);

    if (hasSteps && hasContainer && canEvaluateVisibility) {
        idxrAutoRestoreFromUrlIfNeeded();
        return;
    }

    if (tryNum >= 25) {
        idxrAutoRestoreFromUrlIfNeeded();
        return;
    }

    setTimeout(function () {
        idxrAutoRestoreFromUrlWhenReady(tryNum + 1);
    }, 120);
}

function idxrEsc(text) {
    return $('<div/>').text(text === undefined || text === null ? '' : String(text)).html();
}

function idxrGetSummaryPreviewHtml() {
    var $table = $('#collapsibleSection .table-unique-12345').first();
    if (!$table.length) {
        return '<p class="idxr-empty-preview">' + idxrEsc(idxrI18n.noPreviewAvailable) + '</p>';
    }
    var $clone = $table.clone();
    $clone.find('script,style,.hidden,[data-option="template"]').remove();
    return $('<div/>').append($clone).html();
}

function idxrGetSvgThumbnailMarkup() {
    var svgEl = document.getElementById('actualSvg');
    if (!svgEl) {
        return '';
    }
    try {
        return (svgEl.outerHTML || '').replace(/\s{2,}/g, ' ').trim();
    } catch (e) {
        return '';
    }
}

function idxrCollectCurrentSnapshot(customName) {
    var snapshot = {
        id: 'idxr_' + Date.now() + '_' + Math.floor(Math.random() * 100000),
        name: customName,
        created_at: (new Date()).toISOString(),
        product_id: parseInt(idxcp_id_product, 10) || 0,
        attribute_id: parseInt($('#id_attribute_for_idxr').val(), 10) || 0,
        customization: '',
        extra_info: buildExtraInfoPayload(),
        steps: [],
        preview_html: idxrGetSummaryPreviewHtml(),
        svg_thumbnail: idxrGetSvgThumbnailMarkup()
    };

    var comma = '';
    $('.sel_opt_wqty').each(function () {
        var stepId = parseInt($(this).attr('id').split('_')[2], 10);
        if (isNaN(stepId)) {
            return;
        }
        snapshot.customization += comma + stepId + '_' + $(this).html();
        comma = ',';
    });

    $('.component_step').each(function () {
        var $step = $(this);
        var stepId = ($step.attr('id') || '').replace('component_step_', '');
        if (!stepId || stepId === 'last') {
            return;
        }
        if ($step.hasClass('hidden')) {
            return;
        }
        if (typeof mustBeVisible === 'function' && !mustBeVisible(stepId)) {
            return;
        }
        var stepType = $('#js_icp_next_opt_' + stepId).attr('data-type');
        if (!stepType) {
            return;
        }

        var stepData = {
            step_id: stepId,
            type: stepType
        };

        if (stepType === 'text') {
            stepData.value = $('#text_' + stepId).val() || '';
        } else if (stepType === 'textarea') {
            stepData.value = $('#textarea_' + stepId).val() || '';
        } else if (stepType === 'file') {
            stepData.value = ($('#file_' + stepId).val() || '').replace(/C:\\fakepath\\/i, '');
        } else {
            stepData.options = [];
            $('input[name=option_' + stepId + ']:checked').each(function () {
                if ($(this).is(':disabled') || $(this).closest('.hidden').length) {
                    return;
                }
                var optionId = $(this).attr('data-value');
                if (typeof optionId === 'undefined') {
                    return;
                }
                var qty = '';
                var qtySelector = '#option_' + stepId + '_' + optionId + '_qty';
                if ($(qtySelector).length) {
                    qty = $(qtySelector).val() || '';
                }
                stepData.options.push({
                    option_id: String(optionId),
                    qty: qty
                });
            });
        }

        snapshot.steps.push(stepData);
    });

    return snapshot;
}

function idxrApplySnapshot(snapshot) {
    if (!snapshot || !$.isArray(snapshot.steps)) {
        return Promise.resolve(false);
    }

    function idxrPause(ms) {
        return new Promise(function (resolve) {
            setTimeout(resolve, ms);
        });
    }

    function idxrSimulateOptionClick(stepId, optionId) {
        var cardSelector = '#card_' + stepId + '_' + optionId;
        if ($(cardSelector).length) {
            $(cardSelector).trigger('click');
            return true;
        }
        var inputSelector = '#option_' + stepId + '_' + optionId;
        if ($(inputSelector).length) {
            $(inputSelector).prop('checked', true).trigger('change').trigger('click');
            return true;
        }
        return false;
    }

    var chain = Promise.resolve(true);
    $.each(snapshot.steps, function (_, stepData) {
        chain = chain.then(function () {
            var stepId = String(stepData && stepData.step_id ? stepData.step_id : '');
            if (!stepId.length || !$('#component_step_' + stepId).length) {
                return true;
            }
            if ($('#component_step_' + stepId).hasClass('hidden')) {
                return true;
            }
            if (typeof mustBeVisible === 'function' && !mustBeVisible(stepId)) {
                return true;
            }

            var stepType = $('#js_icp_next_opt_' + stepId).attr('data-type') || stepData.type;
            var nextBtnSelector = '#js_icp_next_opt_' + stepId;

            // Replay UI events like a user instead of forcing hidden markers.
            if (stepType === 'text') {
                if ($('#text_' + stepId).length) {
                    $('#text_' + stepId)
                        .val(stepData.value || '')
                        .trigger('input')
                        .trigger('change');
                }
                if ($(nextBtnSelector).length) {
                    $(nextBtnSelector).trigger('click');
                }
                return idxrPause(40);
            }

            if (stepType === 'textarea') {
                if ($('#textarea_' + stepId).length) {
                    $('#textarea_' + stepId)
                        .val(stepData.value || '')
                        .trigger('input')
                        .trigger('change');
                }
                if ($(nextBtnSelector).length) {
                    $(nextBtnSelector).trigger('click');
                }
                return idxrPause(40);
            }

            if (stepType === 'file') {
                return true;
            }

            if ($.isArray(stepData.options) && stepData.options.length) {
                $('input[name=option_' + stepId + ']').prop('checked', false);
                $.each(stepData.options, function (_, opt) {
                    var optionId = String(opt && opt.option_id ? opt.option_id : '');
                    if (!optionId.length) {
                        return;
                    }
                    var qtySelector = '#option_' + stepId + '_' + optionId + '_qty';
                    if ($(qtySelector).length && opt.qty !== undefined && opt.qty !== null && opt.qty !== '') {
                        $(qtySelector).val(opt.qty).trigger('input').trigger('change');
                    }
                    idxrSimulateOptionClick(stepId, optionId);
                });
                if ($(nextBtnSelector).length) {
                    $(nextBtnSelector).trigger('click');
                }
                return idxrPause(60);
            }

            return true;
        });
    });

    return chain.then(function () {
        var nextId = next_panel_id();
        if (nextId) {
            open_next_panel(nextId);
        }
        updateTotal();
        checkFinish();
        return true;
    });
}

function idxrEnsureCustomizationModals() {
    if ($('#idxr-save-customization-modal').length) {
        return;
    }

    var saveTitle = idxrI18n.saveCustomization;
    var restoreTitle = idxrI18n.restoreCustomization;
    var loginText = idxrI18n.loginToSave;

    var style = ''
        + '<style id="idxr-customization-modals-style">'
        + '.idxr-c-modal{position:fixed;inset:0;background:rgba(25,25,40,.45);display:none;align-items:center;justify-content:center;z-index:10020;padding:16px;}'
        + '.idxr-c-modal.is-open{display:flex;}'
        + '.idxr-c-dialog{width:min(760px,96vw);max-height:86vh;overflow:auto;background:#fff;border-radius:12px;box-shadow:0 12px 40px rgba(15,20,60,.25);padding:18px;}'
        + '.idxr-c-title{font-size:18px;font-weight:700;margin:0 0 12px 0;color:#1c1e2c;}'
        + '.idxr-c-row{display:flex;gap:10px;align-items:center;margin-bottom:10px;}'
        + '.idxr-c-input{flex:1;height:42px;border:1px solid #cdd4ea;border-radius:8px;padding:0 12px;font-size:14px;}'
        + '.idxr-c-help{font-size:12px;color:#6a6f86;margin:0 0 10px 0;}'
        + '.idxr-c-preview{border:1px solid #e4e8f5;border-radius:10px;padding:10px;max-height:260px;overflow:auto;background:#fbfcff;}'
        + '.idxr-c-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:14px;}'
        + '.idxr-c-btn{height:40px;border-radius:8px;padding:0 14px;border:1px solid transparent;cursor:pointer;font-weight:600;}'
        + '.idxr-c-btn-secondary{background:#fff;border-color:#cad2ef;color:#334;}'
        + '.idxr-c-btn-primary{background:#2e48c4;color:#fff;}'
        + '.idxr-c-list{display:flex;flex-direction:column;gap:10px;max-height:360px;overflow:auto;}'
        + '.idxr-c-item{border:1px solid #dbe1f5;border-radius:10px;padding:10px;background:#fff;}'
        + '.idxr-c-item-head{display:flex;gap:8px;align-items:center;justify-content:space-between;margin-bottom:6px;}'
        + '.idxr-c-item-name{font-size:14px;font-weight:700;color:#1d2340;}'
        + '.idxr-c-item-date{font-size:12px;color:#6c7395;}'
        + '.idxr-c-empty{font-size:13px;color:#697198;padding:12px;border:1px dashed #cfd6ee;border-radius:10px;background:#fbfcff;}'
        + '.idxr-c-error{font-size:12px;color:#b01933;display:none;margin:6px 0 0 0;}'
        + '.idxr-c-warning{font-size:13px;color:#985f00;padding:8px 10px;background:#fff8e6;border:1px solid #ffe0a6;border-radius:8px;margin-bottom:10px;display:none;}'
        + '</style>';

    var saveModal = ''
        + '<div id="idxr-save-customization-modal" class="idxr-c-modal" role="dialog" aria-hidden="true">'
        + '  <div class="idxr-c-dialog">'
        + '    <h3 class="idxr-c-title">' + idxrEsc(saveTitle) + '</h3>'
        + '    <div class="idxr-c-warning" id="idxr-save-auth-warning">' + idxrEsc(loginText) + '</div>'
        + '    <div class="idxr-c-row">'
        + '      <input id="idxr-customization-name-input" class="idxr-c-input" type="text" maxlength="100" placeholder="' + idxrEsc(saveTitle) + '" />'
        + '    </div>'
        + '    <p class="idxr-c-help">' + idxrEsc(idxrI18n.previewCurrentSelection) + '</p>'
        + '    <div id="idxr-save-preview" class="idxr-c-preview"></div>'
        + '    <p id="idxr-save-error" class="idxr-c-error">' + idxrEsc(idxrI18n.pleaseEnterName) + '</p>'
        + '    <div class="idxr-c-actions">'
        + '      <button type="button" class="idxr-c-btn idxr-c-btn-secondary" data-idxr-close="save">' + idxrEsc(idxrI18n.cancel) + '</button>'
        + '      <button type="button" class="idxr-c-btn idxr-c-btn-primary" id="idxr-save-customization-confirm">' + idxrEsc(idxrI18n.save) + '</button>'
        + '    </div>'
        + '  </div>'
        + '</div>';

    var restoreModal = ''
        + '<div id="idxr-restore-customization-modal" class="idxr-c-modal" role="dialog" aria-hidden="true">'
        + '  <div class="idxr-c-dialog">'
        + '    <h3 class="idxr-c-title">' + idxrEsc(restoreTitle) + '</h3>'
        + '    <div class="idxr-c-warning" id="idxr-restore-auth-warning">' + idxrEsc(loginText) + '</div>'
        + '    <div id="idxr-restore-customization-list" class="idxr-c-list"></div>'
        + '    <p id="idxr-restore-error" class="idxr-c-error">' + idxrEsc(idxrI18n.pleaseSelectOne) + '</p>'
        + '    <div class="idxr-c-actions">'
        + '      <button type="button" class="idxr-c-btn idxr-c-btn-secondary" data-idxr-close="restore">' + idxrEsc(idxrI18n.cancel) + '</button>'
        + '      <button type="button" class="idxr-c-btn idxr-c-btn-primary" id="idxr-restore-customization-confirm">' + idxrEsc(idxrI18n.restore) + '</button>'
        + '    </div>'
        + '  </div>'
        + '</div>';

    $('head').append(style);
    $('body').append(saveModal + restoreModal);
}

function idxrOpenModal(modalId) {
    $('#' + modalId).addClass('is-open').attr('aria-hidden', 'false');
}

function idxrCloseModal(modalId) {
    $('#' + modalId).removeClass('is-open').attr('aria-hidden', 'true');
}

function idxrRenderRestoreList(list) {
    var $container = $('#idxr-restore-customization-list');
    $container.empty();

    if (!$.isArray(list) || !list.length) {
        $container.html('<div class="idxr-c-empty">' + idxrEsc(idxrI18n.noSavedCustomizationsForProduct) + '</div>');
        return;
    }

    $.each(list, function (_, item) {
        var createdAt = item.created_at ? new Date(item.created_at) : null;
        var dateText = createdAt && !isNaN(createdAt.getTime()) ? createdAt.toLocaleString() : '';
        var row = ''
            + '<label class="idxr-c-item">'
            + '  <div class="idxr-c-item-head">'
            + '    <div class="idxr-c-item-name">'
            + '      <input type="radio" name="idxr_restore_pick" value="' + idxrEsc(item.id) + '"> '
            + idxrEsc(item.name || idxrI18n.unnamedCustomization)
            + '    </div>'
            + '    <div class="idxr-c-item-date">' + idxrEsc(dateText) + '</div>'
            + '  </div>'
            + '  <div class="idxr-c-preview">' + (item.preview_html || '<span class="idxr-c-empty">' + idxrEsc(idxrI18n.noPreview) + '</span>') + '</div>'
            + '</label>';
        $container.append(row);
    });
}

$(document).ready(function() {

    var parentdiv = $("body");
    idxrEnsureCustomizationModals();

    parentdiv.on('click', '[data-idxr-close="save"]', function () {
        idxrCloseModal('idxr-save-customization-modal');
    });

    parentdiv.on('click', '[data-idxr-close="restore"]', function () {
        idxrCloseModal('idxr-restore-customization-modal');
    });

    parentdiv.on('click', '#idxr-save-customization-modal', function (e) {
        if (e.target.id === 'idxr-save-customization-modal') {
            idxrCloseModal('idxr-save-customization-modal');
        }
    });

    parentdiv.on('click', '#idxr-restore-customization-modal', function (e) {
        if (e.target.id === 'idxr-restore-customization-modal') {
            idxrCloseModal('idxr-restore-customization-modal');
        }
    });

    parentdiv.on('click', '#save-customization-button-unique-12345', function (e) {
        if ($(this).prop('disabled')) {
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#idxr-save-error').hide();
        $('#idxr-save-preview').html(idxrGetSummaryPreviewHtml());
        var defaultName = $.trim($('#product-title-unique-12345').text()) || idxrI18n.myCustomization;
        $('#idxr-customization-name-input').val(defaultName).focus().select();
        idxrOpenModal('idxr-save-customization-modal');
    });

    parentdiv.on('click', '#restore-customization-button-unique-12345', function (e) {
        if ($(this).prop('disabled')) {
            return;
        }
        e.preventDefault();
        e.stopImmediatePropagation();
        $('#idxr-restore-error').hide();
        idxrSetRestoreButtonLoading(true);
        var productId = parseInt(idxcp_id_product, 10) || 0;
        var attributeId = parseInt($('#id_attribute_for_idxr').val(), 10) || 0;
        idxrServerRequest('listServerCustomizations', {
            product: productId,
            attribute: attributeId
        }).done(function (response) {
            idxrRenderRestoreList(response.items || []);
            idxrOpenModal('idxr-restore-customization-modal');
            idxrSetRestoreButtonLoading(false);
        }).fail(function (msg) {
            $('#idxr-restore-customization-list').html('<div class="idxr-c-empty">' + idxrEsc(idxrI18n.unableLoadSavedCustomizations) + '</div>');
            $('#idxr-restore-error').text(idxrErrorMessage(msg, idxrI18n.unableLoadSavedCustomizations)).show();
            idxrOpenModal('idxr-restore-customization-modal');
            idxrSetRestoreButtonLoading(false);
        });
    });

    parentdiv.on('click', '#idxr-save-customization-confirm', function () {
        var customName = $.trim($('#idxr-customization-name-input').val());
        if (!customName.length) {
            $('#idxr-save-error').show();
            return;
        }
        idxrSetModalActionLoading('#idxr-save-customization-confirm', true, idxrI18n.saving);
        var snapshot = idxrCollectCurrentSnapshot(customName);
        idxrServerRequest('saveServerCustomization', {
            product: snapshot.product_id,
            attribute: snapshot.attribute_id,
            name: snapshot.name,
            custom: snapshot.customization,
            extra: snapshot.extra_info,
            snapshot_json: JSON.stringify(snapshot),
            preview_html: snapshot.preview_html,
            svg_thumbnail: snapshot.svg_thumbnail || ''
        }).done(function () {
            idxrCloseModal('idxr-save-customization-modal');
            idxrSetModalActionLoading('#idxr-save-customization-confirm', false);
        }).fail(function (msg) {
            $('#idxr-save-error').text(idxrErrorMessage(msg, idxrI18n.unableSaveCustomization)).show();
            idxrSetModalActionLoading('#idxr-save-customization-confirm', false);
        });
    });

    parentdiv.on('click', '#idxr-restore-customization-confirm', function () {
        var selectedId = $('input[name="idxr_restore_pick"]:checked').val();
        if (!selectedId) {
            $('#idxr-restore-error').show();
            return;
        }
        idxrSetModalActionLoading('#idxr-restore-customization-confirm', true, idxrI18n.restoring);
        idxrServerRequest('getServerCustomization', {
            id_saved_customisation: selectedId
        }).done(function (response) {
            var snapshot = null;
            if (response.item && response.item.snapshot_json) {
                try {
                    snapshot = JSON.parse(response.item.snapshot_json);
                } catch (e) {
                    snapshot = null;
                }
            }
            if (!snapshot) {
                $('#idxr-restore-error').text(idxrI18n.savedPayloadInvalid).show();
                idxrSetModalActionLoading('#idxr-restore-customization-confirm', false);
                return;
            }
            idxrSetGlobalPreloader(true);
            idxrResetAllBeforeRestore();
            idxrCloseModal('idxr-restore-customization-modal');
            idxrApplySnapshot(snapshot).then(function () {
                idxrSetGlobalPreloader(false);
                idxrSetModalActionLoading('#idxr-restore-customization-confirm', false);
            }).catch(function () {
                idxrSetGlobalPreloader(false);
                idxrSetModalActionLoading('#idxr-restore-customization-confirm', false);
                $('#idxr-restore-error').text(idxrI18n.unableApplySavedCustomization).show();
            });
        }).fail(function (msg) {
            $('#idxr-restore-error').text(idxrErrorMessage(msg, idxrI18n.unableRestoreCustomization)).show();
            idxrSetModalActionLoading('#idxr-restore-customization-confirm', false);
            idxrSetGlobalPreloader(false);
        });
    });

    view_type = $('#component_steps_container').attr('data-type');
    actual_cover = $(cover_image_id).attr('src');
    if(jQuery.isFunction('tooltip')) {
        $('.icp_optional_info_icon').tooltip();
    }

    $.post( url_ajax, { action: "setCart" });
    idxrAutoRestoreFromUrlWhenReady(0);

    // Remove price and attributes from product page
	
	//   const allowedProductIds = [16648];// json encode $idxr_skipped_product_ids
    //   if (typeof idxr_skipped_product_ids !== 'undefined') {
    //     const allowedProductIds = idxr_skipped_product_ids;
      
    //     if (allowedProductIds.includes(16648)) {
    //       console.log('Product is skipped');
    //     }
    //   }

    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateCart', function (event) {
            $('#preloader-wrapper').remove();
            // minimumcartfees
            if (typeof refreshFeeLine !== 'undefined') {
                setTimeout(refreshFeeLine, 1000);
            }
        });
    }
	  const currentProductId = +$('#product_page_product_id').val();
      
	  if ((typeof idxr_skipped_product_ids !== 'undefined') && (!idxr_skipped_product_ids.includes(currentProductId))) {
		$('.product-variants').remove();
	  }else{
        $('.product-actions').each(function() {
            this.style.setProperty('display', 'block', 'important');
          });
        }
        function initSomeEvents() {
            let selectedThickness = document.querySelector('input[name="group[6]"]:checked');
            if (selectedThickness) {
                const labelSpan = selectedThickness.closest('label').querySelector('.radio-label');
                const mm = labelSpan ? labelSpan.textContent.trim() : '';
                $('#product_thickness').val(mm);
            }

            var $productDetails = $('#product-details');
            if ($productDetails.length) {
                var dataProduct = $productDetails.attr('data-product');
            
                try {
                    var productData = JSON.parse(dataProduct);
                    var idProductAttribute = productData.id_product_attribute;
            
                    // Set the value to your hidden input
                    $('#id_attribute_for_idxr').val(idProductAttribute);
                } catch (e) {
                }
            }
        }
        initSomeEvents();
        if (typeof prestashop !== 'undefined') {
            prestashop.on('updatedProduct', function (event) {
                const basePriceInput = document.querySelector('.js_base_price');
                if (!basePriceInput) return;

                // Get TTC price from input
                const ttcValue = parseFloat(basePriceInput.value.replace(',', '.'));
                if (isNaN(ttcValue)) return;
        
                // Tax rate (e.g. 20%)
                const taxRate = 0.2;
        
                // Compute HT
                const htValue = ttcValue / (1 + taxRate);
        
                // Format prices
                const ttcFormatted = ttcValue.toFixed(2).replace('.', ',') + ' € / m² TTC';
                const htFormatted = htValue.toFixed(2).replace('.', ',') + ' € / m² HT';
        
                // Update TTC display
                const ttcSpan = document.querySelector('.current-price span[content]');
                if (ttcSpan) {
                    ttcSpan.setAttribute('content', ttcValue.toFixed(2));
                    ttcSpan.innerHTML = ttcFormatted.split(' ')[0] + '&nbsp;€'; // just the number part
                }
        
                // Update HT display
                const htBlock = document.querySelector('.product-without-taxes');
                if (htBlock) {
                    htBlock.textContent = htFormatted;
                }

                // Get selected attribute value from group[6]
                
                let selectedThickness = document.querySelector('input[name="group[6]"]:checked');
                if (selectedThickness) {
                    const labelSpan = selectedThickness.closest('label').querySelector('.radio-label');
                    const mm = labelSpan ? labelSpan.textContent.trim() : '';
                    $('#product_thickness').val(mm);
                }
                initSomeEvents();
            });
        }
        
	  	// console.log('product-variants:', currentProductId);
        
	
    if(es17){
        $('.product-prices').remove();
        //Add with team wassim novatis
        // $('.product-variants').remove();
        //$('.product-variants').addClass('custum-variants');
        //End
        $('.product-add-to-cart').remove();
        if(show_topprice == '1') {
            $('.product-information').before($.parseJSON(toppriceblock));
        }
    } else {
        $('.pb-center-column').removeClass('col-sm-4').addClass('col-sm-7');
        $('.product_attributes').remove();
        //Add with team wassim novatis
        // $('.product-variants').remove();
       // $('.product-variants').addClass('custum-variants');
        //End
        $('.box-cart-bottom').remove();
        $('.box-info-product').remove();
        if(show_topprice  == '1') {
            if($('#product_condition').length) {
                $('#product_condition').before($.parseJSON(toppriceblock));
            } else if ($('#product_reference').length) {
                $('#product_reference').after($.parseJSON(toppriceblock));
            }
        }
    }

    // Init configurator    
    // if(view_type !== "minified" && first_open == '1' && !step_active){
    //     loadimages($('#component_steps_container').find('.panel-collapse').first());
    //     $('#component_steps_container').find('.panel-collapse').first().collapse('show');
    // }

    // if(step_active){
    //     if (step_active == 'resume') {
    //         $('#component_step_resume').collapse('show');
    //     } else {
    //         $('#step_title_'+step_active).collapse('show');
    //     }
    // }

    if(view_type != "minified") {
        //set default and customization
        $('.component_step').each(function(index){
            var isLastElement = index == $('.component_step').length -1;
            set_default_value($(this),isLastElement);
        });
        var status_string = get_status_string();
        refreshProductImage(status_string);    
        updateTotal();    
        checkFinish();
        setTimeout(function () {
            $('#js_topblock_total_price_until').attr("id","js_topblock_total_price");        
        }, 3000);
    }
    
    $('#component_steps_container .modal').each(function(){
        $('#component_steps_container').append($(this));
    });

    if (typeof prestashop !== 'undefined') {
        prestashop.on('updateProduct', function (params) {
            $('#add-to-cart-button-unique-12345').prop('disabled', true);
            $('#add-to-cart-button-unique-12345').addClass('disabled');
            setTimeout(
            function() 
            {
                updateTotal();    
                checkFinish();
            }, 500);
        });
    }

    parentdiv.on('shown.bs.collapse', ".panel-collapse",function(){
        loadimages($('#component_steps_container').find('.step_content').first());
        var component_id = $(this).attr('id').replace('step_title_','');
        readjustheight(component_id);
        $('.toggle_arrow').removeClass('icp_rotate');
        if(component_id == 'component_step_resume') {
            $('#toggle_block_last').addClass('icp_rotate');
        } else {
            $('#toggle_block_'+component_id).addClass('icp_rotate');
        }
        
        $('input[name=option_'+component_id+']').removeAttr("disabled");
    });

    parentdiv.on('submit', '#add-to-cart-or-refresh',function(e){
        return false;
    });
    
    parentdiv.on('show.bs.collapse', '#component_steps_container',function (e) {
            loadimages($(this));
    });

    // parentdiv.on('shown.bs.collapse', '#component_steps_container',function (e) {
    //     $('#component_steps_container').find('.panel-collapse').each(function () {
    //         if($(this).attr('id') !== e.target.id) {
    //             if($(this).is('.in, .show')){
    //                 $(this).collapse('hide');
    //             }
    //         }
    //     });
    // });

    parentdiv.on('click', '.js-card-option', function(e){
        if($(this).hasClass('js_out_of_stock')){
            return false;
        }
        if ((!$(e.target).attr('class') || !$(e.target).attr('class').includes("zoom-imagen"))
                && !$(e.target).attr('class').includes("icp-readmore-btn")
                && !$(e.target).attr('class').includes('idxcp_option_qty')) {
            var multiple = $(this).closest('.component_step').attr('data-multivalue');
            var optionid = $(this).attr('id').replace('card_','');
            if(multiple == 'unique' || multiple == 'unique_qty') {
                $('#option_'+optionid).click().click();//fix for input radio need double click
            }else{
                $('#option_'+optionid).click();
            }
            var option_values = optionid.split('_');
            updatechecks(option_values[0]);
            
            var view_type = $('#component_steps_container').attr('data-type');
            
            if(view_type === 'accordion' && (multiple == 'unique' || multiple == 'unique_qty')) {
                $('#js_icp_next_opt_'+option_values[0]).click();
            }
        }
    });

    parentdiv.on('change', '.js-card-fileoption', function(){
        var optionid = $(this).attr('id').replace('file_','');
        if(view_type === 'accordion' || view_type === 'minified') {
            $('#js_icp_next_opt_'+optionid).click();
        }
    });

    // Ensure accordion text fields are committed even if the user doesn't click a "next" link.
    parentdiv.on('input', '.accordion_text', function(){
        var id_option = $(this).attr('id').replace('text_','');
        syncInputExtraToHidden(id_option);
        $('#js_icp_next_opt_'+id_option).click();
    });

    parentdiv.on('click', '.js-text-next-btn', function(){
        var id_option = ($(this).attr('id') || '').replace('js-text-next-btn-','');
        if (id_option) {
            syncInputExtraToHidden(id_option);
            $('#js_icp_next_opt_'+id_option).click();
        }
        var nextId = next_panel_id();
        go_next_panel(nextId);
    });

    parentdiv.on('click', '.js_icp_next_option',function(){
        step_id = $(this).attr('id');
        step_id = step_id.replace('js_icp_next_opt_', ''); // 61
        step_type = $(this).attr('data-type'); // text
        optional = $(this).closest('.component_step').attr('data-optional'); // 0
        multiple = $(this).closest('.component_step').attr('data-multivalue'); // unique
        price_with_discount = false;
        if ($('#show_discount_line').length > 0 && $('#show_discount_line').val() === "0" && $('#idxcp_discount_type').val() != "amount" ) {
            price_with_discount = true; // true
            discount_percentage = parseFloat($("#idxcp_discount_amount").val());
        }
        
        
        let promise = set_option_value(step_type, step_id, optional);

        promise.then(()=>{
            var next_id = next_panel_id();
            open_next_panel(next_id);
            
            var check_impact = [];
            $('#component_steps .js_icp_option').each(function(){
                if ($(this).attr('data-price-option-impact')) {
                    var impact = $(this).attr('data-price-option-impact').split('|');
                    var component_id = $(this).closest('.component_step').attr('id').replace("component_step_","");
                    $.each(impact, function (index,value) {
                        if (value && value.indexOf(step_id+"_") == 0) {
                            check_impact.push(component_id);
                        }
                    });
                }
            });
            check_impact = Array.from(new Set(check_impact));
            $.each(check_impact, function(index,value){
                updateimpact($('#component_step_'+value));
            });
            if(view_type === 'accordion' && step_type !== 'text') {
                go_next_panel(next_id);
            }
        }).catch(()=>{
            return false;
        });
    });

    
     
    parentdiv.on('click', '#idxrcustomproduct_topblock_send', function(){
       $('#idxrcustomproduct_send').click();
    });

    parentdiv.on('click', '#idxrcustomproduct_send', function(event){
        createPreloader();
        $('#add-to-cart-button-unique-12345').prop('disabled', true);
        $('#add-to-cart-button-unique-12345').addClass('disabled');
        event.preventDefault();
        data = new Array();
    
        $('.js_icp_next_option').each(function(){
            if($(this).attr('data-type') === 'file' ){
                var id = $(this).attr('id').replace('js_icp_next_opt_','');
                var visible = mustBeVisible(id);
                var upload = ajaxFileUpload(id);
                var optional = $(this).closest('.component_step').attr('data-optional');
                if (visible && optional !== "1" && !upload) {
                    throw "Invalid file";
                }
            }
        });

        var poidsProduit = $('#product_weight').val();
        var widthsProduit = $('#product_width').val();
        var heightsProduit = $('#product_height').val();
        var volumesProduit = $('#product_volume').val();
        var depthsProduit = $('#product_depth').val();
        data['product_volume'] = volumesProduit;
        data['product_width'] = widthsProduit;
        data['product_height'] = heightsProduit;
        data['product_weight'] = poidsProduit;
        data['product_depth'] = depthsProduit;
        data['product_id'] = idxcp_id_product;
        data['attribute_id'] = $('#id_attribute_for_idxr').val();
        data['customization'] = '';
        data['extra_info'] = '';
        
        comma = '';
        $('.sel_opt').each(function(){
            var sel_opt = $(this);
            if (!$(this).parent().parent().hasClass("hidden") && $(this).html() &&  $(this).html() != "false") {
                var option_text = $(this).html();
                var options_selected = [];
                if ($(this).html().indexOf("&") > 0) {
                    options = option_text.split('&');
                    $.each(options, function(index, value) {                        
                        options_selected.push(parseInt(value.replace('amp;','')));
                    });
                } else {
                    options_selected.push(parseInt(option_text));
                }
                $.each(options_selected, function(index, value) {
                    qty = '';
                    step_id = sel_opt.attr('id').split("_")[2];
                    if ($('#option_'+step_id+'_'+value+'_qty').length && $('#option_'+step_id+'_'+value+'_qty').val() > 1) {
                        qty = $('#option_'+step_id+'_'+value+'_qty').val();
                        qty += 'x';
                    }
                    data['customization'] += comma+qty+(step_id)+'_'+value;
                    comma = ',';
                });
            }
        });
        
        data['extra_info'] = buildExtraInfoPayload();
        
        var quantity = 1;
        if ($('#quantity_wanted').length) {
            quantity = $('#quantity_wanted').val();
        }
        if ($('#idxrcustomprouct_quantity_wanted').length) {
            quantity = $('#idxrcustomprouct_quantity_wanted').val();
        }

        try {
            sendSnaps(data['product_id'], data['attribute_id'], data['customization'], data['extra_info'], quantity, data['product_weight'], data['product_volume'], data['product_width'], data['product_height'], data['product_depth']);
        } catch (error) {
            $('#idxrcustomproduct_send').prop('disabled', false);
            reseto();
            alert('An error occurred while processing your request. Please try again.');
        }
        $('.side_close').addClass('relaod-custum');
        reloadPageProduit();
    });
    
    //Add with team wassim novatis
    function reloadPageProduit() {
        var closeButtons = document.querySelectorAll('.relaod-custum');     
        if (closeButtons) {
          closeButtons.forEach(function (button) {
            button.addEventListener('click', function () {
              var productElement = document.getElementById('product');
              var blockCustum = document.querySelectorAll('.component_step');
              
              if (productElement && blockCustum.length > 0) {
                  location.reload();
              }
            });
          });
        } 
    }
    // End

    parentdiv.on('click','#idxrcustomproduct_save', function(){
        data = new Array();

        data['product_id'] = idxcp_id_product;
        data['attribute_id'] = $('#id_attribute_for_idxr').val();
        data['customization'] = '';
        data['extra_info'] = '';
        comma = '';

        $('.sel_opt_wqty').each(function(){
            data['customization'] += comma+parseInt($(this).attr('id').split("_")[2])+'_'+$(this).html();
            comma = ',';
        });

        data['extra_info'] = buildExtraInfoPayload();

        $.post( url_ajax, { action: "savefav", product: data['product_id'], attribute: data['attribute_id'], custom: data['customization'], extra: data['extra_info']} )
            .done(function(data){
                alert(data);
            });
    });

    if (image_editor) {
        $('#idxcp_imageeditor-slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            centerMode: true,
            slidesToShow: 3,
        });
    }

    parentdiv.on('click','.idxcp_imageeditor_img', function(){
        var id_image = $(this).attr('data-idimage');
        var status_string = get_status_string();
        $.post( url_ajax, { action: "saveimgstatus", id_image: id_image, status: status_string})
        .done(function(data){
            data = jQuery.parseJSON(data);
            if (data == "ok") {
                $("#idxcp_imageeditor-sucess").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
            } else {
                $("#idxcp_imageeditor-error").fadeIn('slow').animate({opacity: 1.0}, 2000).fadeOut('slow');
            }
            refreshProductImage(status_string);
        });
    });
    
    parentdiv.on('click','#component_steps .zoom', function(e){
        if ($(e.target).attr('class').includes("zoom-imagen")) {
            var target = $(this).attr('data-target');        
            $('body').append($(target));
        }
    });
    
    parentdiv.on('click','#component_steps .icp-readmore-btn', function(e){
        $('#Mymodal').modal('show');
    });

    parentdiv.on('change', '.idxrcustomproduct-fileoption', function(e) {
        var input = $(this);
    
        // Fallback to using e.target.files if e.originalEvent is undefined
        var files = (e.originalEvent && e.originalEvent.target && e.originalEvent.target.files) || e.target.files;
    
        // If files is undefined, log an error and exit
        if (!files) {
            console.error("Unable to retrieve files. Make sure the event is triggered by a file input.");
            return;
        }
    
        var allowed_extension = input.parent().find('span').find('b').map(function() {
            return this.innerHTML;
        }).get();
        var max_size = input.attr("data-maxsize");
    
        if (!max_size) {
            max_size = input.parent().find('span').find('b').last().html();
        }
        
        if (max_size) {
            max_size = max_size.replace("MB", "") * 1000000;
            input.parent().find('.idxrcustomproduct-fileoption-error').empty();
            input.parent().find('.idxrcustomproduct-fileoption-error').hide();
            
            for (var i = 0, len = files.length; i < len; i++) {
                // Check file extension
                if ($.inArray('.' + files[i].name.split('.').pop().toLowerCase(), allowed_extension) == -1) {
                    input.parent().find('.idxrcustomproduct-fileoption-error').append('<p>' + file_ext_error + '</p>');
                    input.parent().find('.idxrcustomproduct-fileoption-error').show();
                }
                // Check file size
                if (files[i].size > max_size) {
                    input.parent().find('.idxrcustomproduct-fileoption-error').append('<p>' + file_syze_error + '</p>');
                    input.parent().find('.idxrcustomproduct-fileoption-error').show();
                }
            }
        }
    });    
    
    parentdiv.on('change', '#idxrcustomprouct_quantity_wanted', function(e){
        updateTotal();
        checkFinish();
    });
    
    parentdiv.on('click','.icp-readmore-btn', function(){
        var description = $(this).parent().find('.option_description').html();
        var title = $(this).parent().find('.option_titles').html();
        $('#idxcp-popup .idxcp-modal-body').text(description);
        $('#idxcp-popup .idxcp-modal-title').text(title);
        $('#idxcp-popup').fadeIn(300);
    });
    
    parentdiv.on('click', '.idxcp-popup-closeBtn', function(){
        $('#idxcp-popup').fadeOut(300);
    });
    
    
});
// Function to create and show the preloader
function createPreloader() {
    // Create the preloader structure using jQuery
    const preloaderWrapper = $('<div>', { id: 'preloader-wrapper' }).css({
    position: 'fixed',
    top: '0',
    left: '0',
    width: '100%',
    height: '100%',
    opacity: '.80',
    backgroundColor: '#ffffff', // Background color of the preloader
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: '9999' // Make sure it's on top of everything
    });

    // Create the loader (spinner)
    const loader = $('<div>', { id: 'loader' }).css({
    width: '60px',
    height: '60px',
    border: '8px solid #f3f3f3', // Light gray border
    borderTop: '8px solid #3498db', // Blue top border
    borderRadius: '50%',
    animation: 'spin 1s linear infinite' // Spinner animation
    });

    // Append loader to the wrapper
    preloaderWrapper.append(loader);

    // Add the preloader wrapper to the body
    $('body').append(preloaderWrapper);

    // Inject keyframes for the spinner animation
    const keyframes = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    `;
    $('<style>').text(keyframes).appendTo('head');
}

// Function to remove the preloader
function removePreloader() {
    $('#preloader-wrapper').fadeOut(500, function () {
    $(this).remove(); // Remove from DOM after fade-out
    });
    $('#preloader-wrapper').remove();
}

function get_status_string()
{
    var status_string = '';
    var comma = '';
    $('.sel_opt').each(function(){
        if ($.isNumeric($(this).html()) && !$(this).parent().parent().hasClass("hidden")) {
            status_string += comma+parseInt($(this).attr('id').split("_")[2])+'_'+parseInt($(this).html());
            comma = ',';
        }
    });
    return status_string;
}

function set_default_value(step,update_final_price)
{
    var default_value = step.attr('data-default');
    if(typeof default_value != 'undefined' && default_value != -1 ){
        var array_values = [];
        var comp_id = step.attr('id').replace('component_step_','');
        if (default_value.indexOf(' ') != -1){
            array_values = default_value.split(' ');
        } else {
            array_values = [default_value];
        }
        
        $.each(array_values, function(key, dvalue){
            var qty = 1;
            if(dvalue.indexOf('x') != -1){
                var parts = dvalue.split('x');
                qty = parts[1];
                dvalue = parts[0];
            }
            
            if ($("#text_"+comp_id).length > 0){
                $("#text_"+comp_id).val(dvalue);
            } else {
                var options_default = dvalue.split(' ');
                jQuery.each(options_default, function(key,option_id) {
                    if($("#option_"+comp_id+"_"+option_id).length > 0) {
                        document.getElementById("option_"+comp_id+"_"+option_id).checked = true;
                        $("#selected_"+comp_id+"_"+option_id).show();
                    }
                });
            }
            $('input[name=option_'+comp_id+']').removeAttr("disabled");
            step_type = $('#js_icp_next_opt_'+comp_id).attr('data-type');
            loadimages($('#component_step_'+comp_id));
            if (qty > 1) {
                $('#option_'+comp_id+'_'+dvalue+'_qty').val(qty);
            }
        });
        set_option_value(step_type,comp_id,update_final_price);
        setJsResumeValue(comp_id,default_value);
    }
}

function set_option_value(step_type,step_id,optional = false, update_final_price = true)
{
    let promise = new Promise((resolve, reject)=>{
        var option_id = false;
        if(step_type === 'textarea'){
            text = $('#textarea_'+step_id).val();
            if(text.length < 1){
                if (optional !== "1") {
                    $('#next_alert_'+step_id).show('slow');
                    reject(false);
                }
            }else{
                option_id = 1;
                $('#next_alert_'+step_id).hide('slow');
                shortText = jQuery.trim(text).substring(0, 50).split(" ").slice(0, -1).join(" ") + "...";
                displayResumeTextLine(step_id,option_id,shortText);
                setJsResumeValue(step_id,option_id);
                $('#js_opt_extra_'+step_id+'_value').html(text);
                $('#js_opt_extra_'+step_id+'_value_wqty').html(text);
            }
        }else if(step_type === 'text'){
            option_id = 1;
            text = $('#text_'+step_id).val();
            if(text.length < 1){
                if (optional !== "1") {
                    $('#next_alert_'+step_id).show('slow');
                    reject(false);
                }
            }else{
                option_id = 1;
                $('#next_alert_'+step_id).hide('slow');
                shortText = jQuery.trim(text);
                displayResumeTextLine(step_id,option_id,shortText);
                setJsResumeValue(step_id,option_id);
                $('#js_opt_extra_'+step_id+'_value').html(text);
                setJsResumeValue(step_id, option_id);
            }
        }else if(step_type === 'file'){
            option_id = 1;
            file = $('#file_'+step_id).val().replace(/C:\\fakepath\\/i, '');
            if(file.length < 1){
                if (optional !== "1") {
                    $('#next_alert_'+step_id).show('slow');
                    reject(false);
                }
            }else{
                option_id = 1;
                $('#next_alert_'+step_id).hide('slow');
                shortText = jQuery.trim(file);
                displayResumeTextLine(step_id,option_id,shortText);
                $('#js_opt_extra_'+step_id+'_value').html(file);
                setJsResumeValue(step_id, option_id);
            }
        }else{
            option_id = [];
            $('input[name=option_'+step_id+']:checked').each(function(){
                option_id.push($(this).attr('data-value'));
            });
            refreshImpact(step_id,option_id);
            if(option_id === false || typeof option_id === 'undefined'){
                if (optional !== "1") {
                    $('#next_alert_'+step_id).show('slow');
                    reject(false);
                }
            }else{
                $('#next_alert_'+step_id).hide('slow');
                let price_impact = 0;
                let price_impact_wod = 0;
                let price_impact_byoption = [];
                let price_impact_wod_byoption = [];
                $('#resume_tr_'+step_id+' .resume_price_block[data-option!="template"]').remove();
                $.each(option_id, function( index, value ) {
                    multiplicador = 1;
                    if ($('#option_'+step_id+'_'+value+'_qty').length && $('#option_'+step_id+'_'+value+'_qty').val() > 1) {
                        multiplicador = $('#option_'+step_id+'_'+value+'_qty').val();
                    }
                    option_price = isNaN(parseFloat($('#option_'+step_id+'_'+value).attr('data-price-impact'))) ? 0 : parseFloat($('#option_'+step_id+'_'+value).attr('data-price-impact')*multiplicador);
                    price_impact += option_price;
                    price_impact_byoption[value] = option_price;
                    if ($('#option_'+step_id+'_'+value).attr('data-price-impact-wodiscount') > 0) {
                        option_price_wod = isNaN(parseFloat($('#option_'+step_id+'_'+value).attr('data-price-impact-wodiscount'))) ? 0 : parseFloat($('#option_'+step_id+'_'+value).attr('data-price-impact-wodiscount')*multiplicador);
                        price_impact_wod += option_price_wod;
                        price_impact_wod_byoption[value] = option_price_wod;
                    }
                });
                if(step_type === 'sel_img'){
                    $('#js_resume_opt_'+step_id+'_price').val(Number(price_impact).toFixed(2));
                    if(price_impact && price_impact !== "0") {
                        $('[id^=resume_price_block_'+step_id+']').remove();
                        total_price_wd = 0;
                        $.each(price_impact_byoption, function( index, value ) {
                            if(value) {
                                var price_wd = 0;
                                if(price_impact_wod_byoption[index]) {
                                    price_wd = price_impact_wod_byoption[index];
                                    total_price_wd += Number(price_wd);
                                } else {
                                    total_price_wd += Number(value);
                                }
                                value -= getPercentDiscount(value);
                                displayResumePriceLine(step_id,index,value,price_wd);
                            }
                        });
                        $('#js_resume_opt_'+step_id+'_price_wodiscount').val(Number(total_price_wd).toFixed(2));
                    } else {
                        $('#js_resume_opt_'+step_id+'_price_formated').html('');
                    }
                    setJsResumeValue(step_id,option_id);
                    if ($.isArray(option_id)) {
                        name = '';
                        img = false;
                        sep = '';
                        $.each(option_id, function( index, value ) {
                            if(value.indexOf('x') != -1){
                                value = value.split('x')[0];
                            }
                            name = '';
                            if ($('#option_'+step_id+'_'+value+'_qty').length && $('#option_'+step_id+'_'+value+'_qty').val() > 1) {
                                multiplicador = $('#option_'+step_id+'_'+value+'_qty').val();
                                name += multiplicador+'x';
                            }
                            name += $('#option_'+step_id+'_'+value+'_name').html();
                            img = $('#option_'+step_id+'_'+value+'_img').attr('src');
                            displayResumeTextLine(step_id,value,name,img);
                        });
                    } else {
                        multiplicador = '';
                        if ($('#option_'+step_id+'_'+option_id+'_qty').length && $('#option_'+step_id+'_'+option_id+'_qty').val() > 1) {
                                multiplicador = $('#option_'+step_id+'_'+option_id+'_qty').val();
                                multiplicador += 'x';
                        }
                        name = multiplicador+$('#option_'+step_id+'_'+option_id+'_name').html();
                        img = $('#option_'+step_id+'_'+option_id+'_img').attr('src');
                        displayResumeTextLine(step_id,option_id,name,img);
                    }
                    $('#title_selected_'+step_id).html(name);
                    $('#title_selected_'+step_id).show();
                    checkconstraint(step_id+'_'+option_id);
                } else if (step_type === 'sel') {
                    $('#js_resume_opt_'+step_id+'_price').val(Number(price_impact).toFixed(2));
                    if(price_impact && price_impact !== "0") {
                        $('[id^=resume_price_block_'+step_id+']').remove();
                        total_price_wd = 0;
                        $.each(price_impact_byoption, function( index, value ) {
                            if(value) {
                                var price_wd = 0;
                                if(price_impact_wod_byoption[index]) {
                                    price_wd = price_impact_wod_byoption[index];
                                    total_price_wd += Number(price_wd);
                                } else {
                                    total_price_wd += Number(value);
                                }
                                if (!price_wd) {
                                    price_wd = value;
                                }
                                value -= getPercentDiscount(value);
                                displayResumePriceLine(step_id,index,value,price_wd);
                            }
                        });
                        $('#js_resume_opt_'+step_id+'_price_wodiscount').val(Number(total_price_wd).toFixed(2));
                    } else {
                        $('#js_resume_opt_'+step_id+'_price_formated').html('');
                    }
                    setJsResumeValue(step_id,option_id);
                    if ($.isArray(option_id)) {
                        name = '';
                        sep = '';
                        $.each(option_id, function( index, value ) {
                            name = "";
                            if(value.indexOf('x') != -1){
                                value = value.split('x')[0];
                            }
                            if ($('#option_'+step_id+'_'+value+'_qty').length && $('#option_'+step_id+'_'+value+'_qty').val() > 1) {
                                multiplicador = $('#option_'+step_id+'_'+value+'_qty').val();
                                name += multiplicador+'x ';
                            }
                            name += $('#option_'+step_id+'_'+value+'_name').html();
                            displayResumeTextLine(step_id,value,name);
                        });
                    } else {
                        multiplicador = '';
                        if ($('#option_'+step_id+'_'+option_id+'_qty').length && $('#option_'+step_id+'_'+option_id+'_qty').val() > 1) {
                                multiplicador = $('#option_'+step_id+'_'+option_id+'_qty').val();
                                multiplicador += 'x';
                        }
                        name = multiplicador+$('#option_'+step_id+'_'+option_id+'_name').html();
                        displayResumeTextLine(step_id,option_id,name);
                    }
                    
                    new_html = '<span>'+name+'</span>';
                    $('#title_selected_'+step_id).html(name);
                    $('#title_selected_'+step_id).show();                    
                    
                    checkconstraint(step_id+'_'+option_id);
                }
                updatechecks(step_id);
            }
        }
        if (update_final_price) {
            updateTotal();
        }
        
        var status_string = get_status_string();
        refreshProductImage(status_string);
        checkFinish();
        resolve(true);
    });
    return promise;
}

function displayResumePriceLine(step_id, option_id, price_impact, price_impact_wod) {
    cloneResumeLine(step_id, option_id);
    let formatter = formatPrice(price_impact);
    formatter.old_step_id = step_id;
    formatter.old_option_id = option_id;
    formatter.done(function(data){
        total_formated = jQuery.parseJSON(data);
        $('#resume_price_block_'+formatter.old_step_id+'_'+formatter.old_option_id+' .idxrcp_resume_opt_price').html(total_formated);
    });
    
    if (price_impact_wod > 0 && price_impact_wod != price_impact) {
        let formatter_wod = formatPrice(price_impact_wod);
        formatter_wod.old_step_id = step_id;
        formatter_wod.old_option_id = option_id;
        formatter_wod.done(function(data){
            total_formated_wod = jQuery.parseJSON(data);
            $('#resume_price_block_'+formatter_wod.old_step_id+'_'+formatter_wod.old_option_id+' .idxrcp_resume_opt_price_wodiscount').html(total_formated_wod);
        });
    }
}

function displayResumeTextLine(step_id, option_id, name, image) {
    cloneResumeLine(step_id, option_id);
    if (image === undefined) {
        new_html = name;
    }else{
        new_html = '<img class="img-element" src="'+image+'" alt="'+name+'"><span>'+name+'</span>';
    }
    $('#resume_price_block_'+step_id+'_'+option_id+' .option_title').html(new_html);
}

function cloneResumeLine(step_id, option_id) {
    if (!$('#resume_price_block_'+step_id+'_'+option_id).length) {
        var clone = $('tr [data-option="template"]:first').clone();
        $(clone).attr('id','resume_price_block_'+step_id+'_'+option_id);
        $(clone).attr('data-option',option_id);
        $(clone).insertAfter( "#resume_tr_"+step_id+" .resume_price_block:last");
        $('#resume_tr_'+step_id+' .resume_price_block[data-option="template"]').hide();
        $('#resume_price_block_'+step_id+'_'+option_id).show();
    }
}

function next_panel_id(){
    var next = false;
    $('#component_steps_container').find('.component_step').each(function () {
        if (!next) {
            var thisid = $(this).attr('id').replace('component_step_','');
            var assigned = $('#js_opt_'+thisid+'_value').html();            
            var contraints_hidden = $(this).hasClass('hidden');
            var optional = $(this).attr('data-optional');
            if (assigned === 'false' && !contraints_hidden && optional === "1") {
                $('#component_step_'+thisid).show('slow');
                loadimages($('#component_step_'+thisid));
            }
            if((assigned === 'false' && !contraints_hidden && optional !== "1") || thisid === 'last'){
                next = thisid;
            } else {
                if (assigned !== 'false' && !contraints_hidden && optional !== "1") {
                    $('#component_step_'+thisid).show('slow');
                    loadimages($('#component_step_'+thisid));
                }
                if (optional || assigned !== 'false') {
                    $('input[name=option_'+thisid+']').removeAttr("disabled");
                }
                if (debug) {
                    if(assigned !== 'false') {
                        console.log('component '+thisid+' already assinged with '+assigned);
                    }
                    if(contraints_hidden) {
                        console.log('component '+thisid+' restrictions not fulfilled');
                    }
                    if(optional == "1"){
                        console.log('component '+thisid+' is optional');
                    }
                }
            }
        }        
    });
    return next;
}

function go_next_panel(id){
    // if(id === 'last'){
    //     $('#component_step_resume').collapse('show');
    //     if (!block_animation && $(window).width() < 992) {
    //         setTimeout(
    //         function()
    //         {
    //             $([document.documentElement, document.body]).animate({
    //                 scrollTop: ($('#component_step_resume').offset().top - 100)
    //             }, 1000);
    //         }, 500);
    //     }
    // }else{
    //     $('#step_title_'+id).collapse('show');
    //     if (!block_animation && $(window).width() < 992) {
    //         setTimeout(
    //         function()
    //         {
    //             $([document.documentElement, document.body]).animate({
    //                 scrollTop: ($('#step_title_'+ id).offset().top - 100) //antes 200
    //             }, 1000);
    //         }, 500);
    //     }
    // }
    return false;
}

function open_next_panel(next_id){
    if(next_id === 'last'){
        var next = $('#component_step_resume');
    } else {
        var next = $('#component_step_'+next_id);
        updateimpact(next);
        loadimages(next);
        next.find('.js_icp_option').prop('block-disabled', false);
        $('input[name=option_'+next_id+']').removeAttr("disabled");
    }    
    next.show('slow');
    next.removeClass('block-disabled');    
    var view_type = $('#component_steps_container').attr('data-type');
    if ($(window).width() > 1025 && view_type === 'full' && !block_animation) {
        $('html, body').animate({
            scrollTop: next.offset().top
        }, 1000);
    }
}

function updateimpact(step) {
    if (step.find('.hidden').length) {
        step.find('.hidden').each(function(){
            if ($(this).attr('data-price-option-impact')) {
                setoptionimpacts($(this));
            }
        });
    }
    // For minified version
    if (step.find('.idxcp_minified_option').length) {
        step.find('.idxcp_minified_option').each(function(){
            if ($(this).attr('data-price-option-impact')) {
                setoptionimpacts($(this));
            }
        });
    }
}

function setoptionimpacts(option)
{
    var opt_impacts = option.attr('data-price-option-impact').split('|');
    if (!opt_impacts) {
        return;
    }
    var noimpact = true;
    $.each( opt_impacts, function( key, opt_impact ) {
        if (opt_impact) {
            var parts = false;
            var type = false;
            if (opt_impact.indexOf("p") >= 0) {
                parts = opt_impact.split('p');
                type = 'percentage';
            }else if (opt_impact.indexOf("f") >= 0) {
                parts = opt_impact.split('f');
                type = 'fix';
            }

            var trigger = parts[0].split('_');
            var selected = $('#js_opt_'+trigger[0]+'_value').html();
            if (selected == trigger[1]) {
                noimpact = false;
                var original_impact = option.data('original-price-impact');
                if (!original_impact) {
                    var current_impact = option.attr('data-price-impact');
                    option.data('original-price-impact',current_impact);
                    original_impact = current_impact;
                }
                if (type == 'percentage') {
                    current_impact = original_impact*(1+(parts[1]/100));
                } else {
                    current_impact = parseFloat(original_impact) + parseFloat(parts[1]);
                }
                option.attr('data-price-impact', current_impact);
                var price_span = option.next('.option_div').find('.idxroption_price');
                if (!price_span.length) {
                    var selectorid = option.attr('id');
                    selectorid = selectorid.replace('option','card');
                    price_span = $('#'+selectorid).parent().find('.idxroption_price');
                }
                var formatter = formatPrice(current_impact);
                formatter.done(function(data){
                    current_impact_formated = jQuery.parseJSON(data);
                    price_span.html(current_impact_formated);
                });
            }
        }
    });
    if (noimpact) {
        var original_impact = option.data('original-price-impact');
        if (original_impact) {
            option.attr('data-price-impact', original_impact);
            var formatter = formatPrice(original_impact);
            var price_span = option.next('.option_div').find('.idxroption_price');
            formatter.done(function(data){
                current_impact_formated = jQuery.parseJSON(data);
                price_span.html(current_impact_formated);
            });
        }
    }
    var comp_id = option.attr('name').replace('option_','');
    var step_type = $('#js_icp_next_opt_'+comp_id).attr('data-type');
    set_option_value(step_type,comp_id);
}

function loadimages(step) {
    step.find('img[src=""]').each(function(){
        $(this).attr('src',$(this).attr('data_src'));
    });
    if (idxcp_max_description_height) {
        step.find('.option_description').each(function(){
            var height = $(this).height();
            if (height == 0) {
                height = $(this).html().length;
            }
            if (height > idxcp_max_description_height && $(this).parent().find('.icp-readmore-btn').length == 0) {
                $(this).css({"maxHeight":idxcp_max_description_height+"px"});
                $(this).after('<button type="button" class="btn btn-light icp-readmore-btn">+</button>');
            }
        });
    }
}

function checkconstraint(option_id){
    $('.component_step').each(function() {
        var selected=true;
        var constraint_string = $(this).attr('data-constraints');
        if(constraint_string){
            constraints = constraint_string.split(',');
            var component_id = $(this).attr('id').replace('component_step_','');
            var show = false;
            constraints.forEach(function(constraint){
                var constraint_rules = constraint.split('+');
                var valid = true;
                constraint_rules.forEach(function(rule){
                    rule_parts = rule.split('_');
                    option_parts = option_id.split('_');
                    selected = option_parts[1];
                    if(rule_parts[0] != option_parts[0]){
                        selected = $('#js_opt_'+rule_parts[0]+'_value').html();
                    }
                    if(selected) {
                        selected = selected.split(',');
                        if(rule !== option_id && jQuery.inArray(rule_parts[1],selected) == -1){
                            valid = false;
                        }
                    }
                });
                if(valid){
                    show = true;
                }
            });
            if (debug) {
                console.log('for component '+component_id+' show '+show);
            }
            if(show){                
                $('#component_step_'+component_id).removeClass('hidden');
                $('#component_step_'+component_id).show();
                $('#resume_tr_'+component_id).removeClass('hidden');
                $('#js_opt_'+component_id+'_value').attr('data-required','true');
                $('#js_opt_'+component_id+'_value').parent().parent().show();
            }else{
                $('#resume_tr_'+component_id).addClass('hidden');
                $('#component_step_'+component_id).addClass('hidden');
                $('#component_step_'+component_id).hide();
                $('#js_opt_'+component_id+'_value').attr('data-required','false');
                $('#js_resume_opt_'+component_id+'_price').html(0);
                $('#js_opt_'+component_id+'_value').html('false');
                $('#js_opt_'+component_id+'_value').parent().parent().hide();
                hideChildOptions(component_id);
            }
        }

        var view_type = $('#component_steps_container').attr('data-type');
        if(view_type === 'full') {
            var last_step = false;
            $('.sel_opt').each(function() {
                if($(this).attr('data-required') === 'true'){
                    last_step = $(this).attr('id');
                }
            });
            if (last_step) {
                last_id = last_step.replace('js_opt_','').replace('_value','');
                $('.js_icp_next_option').html(next_text);
                $('#js_icp_next_opt_'+last_id).html(finish_text);
            }
        }
    });
}

function hideChildOptions(component_id) {
//    $('.component_step').each(function() {
//        var constraint_string = $(this).attr('data-constraints');
//        if (constraint_string && $(this).is(":visible")) {
//            var id_to_remove = $(this).attr('id').replace('component_step_','');
//            if (!mustBeVisible(id_to_remove)) {
//                console.log('cerrramos '+id_to_remove);
//                var component_div = $('#component_step_'+id_to_remove);
//                component_div.hide();
//                document.getElementById('component_step_'+String(id_to_remove)).classList.add("hidden");
//                $('#resume_tr_'+String(id_to_remove)).addClass('hidden');
//                $('#component_step_'+String(id_to_remove)).addClass('hidden');
//                $('#component_step_'+String(id_to_remove)).hide();
//                $('#js_opt_'+String(id_to_remove)+'_value').attr('data-required','false');
//                $('#js_resume_opt_'+id_to_remove+'_price').html('');
//                $('#js_opt_'+component_id+'_value').html('false');
//                $('#js_opt_'+String(id_to_remove)+'_value').parent().parent().hide();
//                hideChildOptions(id_to_remove);
//            }
//        }
//    });
}

function mustBeVisible(id_component){
    var constraint_string = $('#component_step_'+id_component).attr('data-constraints');
    if (constraint_string) {
        constraints = constraint_string.split(',');
        if (constraints && constraints.length > 0) {
            var visible = false;
            constraints.forEach(function(constraint){
                var constraint_rules = constraint.split('+');
                constraint_rules.forEach(function(rule){
                    rule_parts = rule.split('_');
                    if ($('#js_opt_'+rule_parts[0]+'_value').html() == rule_parts[1]) {
                        visible = true;
                    }
                });
            });
            return visible;
        }
    }
    return true;
}

function ajaxFileUpload(id_component){
    var resultado = true;
    if ($('#file_'+id_component)[0].files.length == 0) {
        return false;
    }
    var data = new FormData();
    data.append('file', $('#file_'+id_component)[0].files[0]);

    var id_product = idxcp_id_product;
    data.append("action", "customfile");
    data.append("component", id_component);
    data.append("product", id_product);
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(this.responseText!="ok"){
                alert(this.responseText);
                resultado = false;
            }
        } else {
            resultado = false;
        }
    };
    xhr.open("POST", url_ajax, false);
    xhr.send(data);
    return resultado;
}

function convertSvgToPng(svgElement, callback) {
    if (!svgElement) {
        callback(null, new Error("SVG element not found"));
        return;
    }
    
    var serializer = new XMLSerializer();
    var svgStr = serializer.serializeToString(svgElement);
    var img = new Image();
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    var svgBlob = new Blob([svgStr], {type: 'image/svg+xml;charset=utf-8'});
    var url = URL.createObjectURL(svgBlob);

    // Desired output width (400px), keep aspect ratio
    var outputWidth = 1080;

    img.onload = function() {
        // Calculate the aspect ratio to maintain it while resizing
        var aspectRatio = img.height / img.width;
        var outputHeight = outputWidth * aspectRatio;

        // Set canvas dimensions based on the desired output width and aspect ratio
        canvas.width = outputWidth;
        canvas.height = outputHeight;

        // Scale up the quality by increasing the size of the canvas temporarily
        var scaleFactor = 2;  // Increase this to make the PNG more detailed (e.g., 2x or 3x)
        canvas.width = outputWidth * scaleFactor;
        canvas.height = outputHeight * scaleFactor;

        // Draw the image onto the canvas at the scaled size
        ctx.scale(scaleFactor, scaleFactor);
        ctx.drawImage(img, 0, 0, outputWidth, outputHeight);

        URL.revokeObjectURL(url);

        // Convert canvas to blob with 'image/png' format
        canvas.toBlob(function(blob) {
            callback(blob, null);
        }, 'image/png');
    };
    
    img.onerror = function(e) {
        callback(null, new Error("Failed to load SVG image"));
        URL.revokeObjectURL(url);
    };

    // Set image source to the generated SVG data URL
    img.src = url;
}


function reseto(){
    removePreloader();
    $('#add-to-cart-button-unique-12345').prop('disabled', false);
    $('#add-to-cart-button-unique-12345').removeClass('disabled');
    isAddingToCart = false;
}

function sendSnaps(product, attribute, custom, extra, quantity, product_weight, product_volume, product_width, product_height, product_depth) {
    
    if (isAddingToCart) return;
    isAddingToCart = true;

    var prix_de_decouper = 0;
    var prix_de_decoupe = 0;
    var price_from_cube = 0;
    var price_from_cube_input = $('#resume_price_from_cube');
    var decoupe_prices = $('#resume_prix_de_decoupe_price');
    
    // Check if decoupe_prices exists and has a value
    if (decoupe_prices.length && decoupe_prices.val()) {
        prix_de_decoupe =+ parseFloat(decoupe_prices.val().replace(',', '.'));
    }
    if (price_from_cube_input.length && price_from_cube_input.val()) {
        price_from_cube =+ parseFloat(price_from_cube_input.val().replace(',', '.'));
    }

    // Check if prix_de_decoupe is a valid number and greater than 0
    if (!isNaN(prix_de_decoupe) && prix_de_decoupe > 0) {
        prix_de_decouper = prix_de_decoupe;
    }// Reset button functionality
    var svg = $('#actualSvg');
    svg.data('scale', 1);
    svg.data('rotate', 0);
    svg.css('transform', 'none');
    $('.activeDemensions').show();
    var svgElement = document.getElementById('actualSvg');
    // var divElement = document.getElementById('component_step_last');
    var svgMarkup = new XMLSerializer().serializeToString(svgElement);

    // Function to handle SVG conversion and send the result to the server
    new Promise((resolve, reject) => {
        convertSvgToPng(svgElement, function(blob, error) {
            if (error) reject(error);
            else resolve(blob);
        });
    })
    .then(function(svgBlob) {
        var id_product = product;

        var data = new FormData();
        data.append('file1', svgBlob, 'design.png');
        data.append('svgMarkup', svgMarkup);
        data.append("console", idxcp_console_state);
        data.append("action", "handlesnaps");
        data.append("product", id_product);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url_ajax, true);
        xhr.onload = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.id) {
                    $.post(url_ajax, { 
                        action: "createproduct", 
                        snaps: response.id, 
                        product: product, 
                        attribute: attribute, 
                        custom: custom, 
                        extra: extra, 
                        quantity: quantity, 
                        product_weight: product_weight, 
                        product_volume: product_volume, 
                        product_width: product_width, 
                        product_height: product_height, 
                        product_depth: product_depth, 
                        prix_de_decoupe: prix_de_decouper,
                        price_from_cube: price_from_cube,
                    }).done(function(data) {
                        if (icp_editing) {
                            removeFromCart(icp_editing);
                        }
                        var enviado = false;
                        if (typeof ajaxCart !== 'undefined') {
                            ajaxCart.add(data, null, true, $(this), $('#quantity_wanted').val());
                            enviado = true;
                        }
                        if (typeof prestashop !== 'undefined') {
                            id_product = data;
                            id_product_attribute = 0;

                            $.ajax({
                                type: 'POST',
                                headers: { "cache-control": "no-cache" },
                                cache: false,
                                url: prestashop.urls.pages.cart + '?rand=' + new Date().getTime(),
                                async: true,
                                dataType: 'json',
                                data: 'action=update&add=1&ajax=true&qty=' + (quantity ? quantity : '1') + '&id_product=' + data + '&token=' + prestashop.static_token + '&ipa=0',
                                success: function(response) {
                                    $('#add-to-cart-button-unique-12345').prop('disabled', false);
                                    enviado = true;
                                    prestashop.emit('updateCart', {
                                        reason: {
                                            idProduct: id_product,
                                            idProductAttribute: id_product_attribute,
                                            linkAction: 'add-to-cart'
                                        },
                                        resp: response
                                    });

                                        // minimumcartfees
                                        if (typeof refreshFeeLine !== 'undefined') {
                                            setTimeout(refreshFeeLine, 1000);
                                        }

                                    reseto();
                                }
                            });

                        }
                        if (!enviado && typeof cart_link !== "undefined") {
                            $.post(cart_link, { token: cart_token, id_product: data, add: 1, id_product_attribute: 0 });
                            location.reload();
                        }
                        
                    });
                } else {
                    // console.log(response);
                    // alert('Error occurred: ' + response.message);
                    reseto()
                }
            } else {
                console.error('An error occurred during the AJAX request');
            }
        };
        xhr.send(data);
    })
    .catch(function(error) {
        console.error('Error during SVG conversion:', error.message);
        alert('An error occurred while processing your request. Please try again.');
        reseto();
    });

}

/**
 * safely parses a value into a float.
 */
function safeParseFloat(value, defaultValue = 0) {
    const parsed = parseFloat(value?.toString().replace(',', '.'));
    return isNaN(parsed) ? defaultValue : parsed;
}

/*
 * Get prices based on product thickness.
 */
function getPricesByThickness(thicknessValue) {
    const thickness = safeParseFloat(thicknessValue);

    let idxcp_prix_de_decoupe = 0.004;
    let idxcp_prix_de_collage = 0.07;
    let idxcp_prix_de_polissage = 0.04;

    if (thickness <= 4) {
        idxcp_prix_de_decoupe = idxcp_cut_prices['4mm'];
        idxcp_prix_de_collage = idxcp_glue_prices['4mm'];
        idxcp_prix_de_polissage = idxcp_polish_prices['4mm'];
    } else if (thickness === 5) {
        idxcp_prix_de_decoupe = idxcp_cut_prices['5mm'];
        idxcp_prix_de_collage = idxcp_glue_prices['5mm'];
        idxcp_prix_de_polissage = idxcp_polish_prices['5mm'];
    } else if (thickness === 6) {
        idxcp_prix_de_decoupe = idxcp_cut_prices['6mm'];
        idxcp_prix_de_collage = idxcp_glue_prices['6mm'];
        idxcp_prix_de_polissage = idxcp_polish_prices['6mm'];
    } else if (thickness === 8) {
        idxcp_prix_de_decoupe = idxcp_cut_prices['8mm'];
        idxcp_prix_de_collage = idxcp_glue_prices['8mm'];
        idxcp_prix_de_polissage = idxcp_polish_prices['8mm'];
    } else if (thickness >= 10) {
        idxcp_prix_de_decoupe = idxcp_cut_prices['10mm'];
        idxcp_prix_de_collage = idxcp_glue_prices['10mm'];
        idxcp_prix_de_polissage = idxcp_polish_prices['10mm'];
    }

    return {
        decoupe: idxcp_prix_de_decoupe,
        collage: idxcp_prix_de_collage,
        polissage: idxcp_prix_de_polissage,
    };
}


function updateTotal(){
    total = 0;
    
    $('.js_resume_price').each(function(){
        var parent = $(this).closest('tr');
        if (!parent.hasClass('hidden')) {
            if (parent.find('.js_resume_price_wodiscount').val() > 0) {
                total = total + parseFloat(parent.find('.js_resume_price_wodiscount').val().replace(',',''));
            } else {
                total = total + parseFloat($(this).val().replace(',',''));
            }
        }
    });
    const productThicknessElement = $('#product_thickness').val();
    const prices = getPricesByThickness(productThicknessElement);

    var idxcp_prix_de_decoupe = prices.decoupe;
    var idxcp_prix_de_collage = prices.collage;
    var idxcp_prix_de_polissage = prices.polissage;

    // total = total + parseFloat($('.js_base_price').val().replace(',',''));
    var basePricePerSquareMeter = safeParseFloat($('.js_base_price').val());
    var surface = safeParseFloat($('#product_surface').val());
    
    
    // verify if Vitrine and add its pricing. 
    if((typeof DefineCubeShapeToDraw === 'number') && (DefineCubeShapeToDraw === 1)){
        var totaleWithTax = 0;
        var totaleWithoutTax = 0;
        var prixDecoupeSocle = idxcp_cut_prices['10mm'];
        var prixCollageSocle = idxcp_glue_prices['10mm'];

        if (!isNaN(surface) && surface > 0) {
            var surfaceCost = (basePricePerSquareMeter * surface).toFixed(2);
            total = total + parseFloat(surfaceCost.replace(',', '.'));
            // Afficher le coût basé sur la surface dans le span avec " €"
            // $("#js_resume_price_surface").html(surfaceCost + " €");
        } else {
            var surfaceCost = (basePricePerSquareMeter * 0);
            total = total + parseFloat(surfaceCost);
            $("#js_resume_price_surface").html(''); // Effacer le contenu s'il n'y a pas de coût basé sur la surface
        }

        (!isNaN(surface) && surface > 0) ? $('#s_d_capot').text(`${surface} m²`) : $('#s_d_capot').text('0 m²');
        const price_87345 = parseFloat(basePricePerSquareMeter)*parseFloat(surface) || 0;
        $('#price_map_1').text(`${getWithoutTax(basePricePerSquareMeter)} €/m²`);
        $('#price_map_ht_1').text(`${getWithoutTax(price_87345).toFixed(2)} €`);
        $('#price_map_ttc_1').text(`${price_87345.toFixed(2)} €`);
        
        var materiel_price = parseFloat($('#cube_materiaux_price').val().replace(',', '.'));
        const second_surface = $('#cube_second_surface').val();
        totaleWithTax += getWithTax(parseFloat(materiel_price)*parseFloat(second_surface));
        totaleWithoutTax += parseFloat(materiel_price)*parseFloat(second_surface);
        $('#s_d_socle').text(`${second_surface} m²`); // price_map_4
        $('#price_map_2').text(`${materiel_price.toFixed(2)} €/m²`);
        $('#price_map_ht_2').text(`${totaleWithoutTax.toFixed(2)} €`);
        $('#price_map_ttc_2').text(`${totaleWithTax.toFixed(2)} €`);


        var prixEpaulment = 0;
        const epaulment = $('#cube_modele_de_socle').val();
        if (epaulment === 'true') prixEpaulment = 15;
        totaleWithTax += getWithTax(prixEpaulment);
        const price_09832423 = totaleWithTax;
        totaleWithoutTax += prixEpaulment;
        $('#price_map_3').text(prixEpaulment + ' €/m² HT');
        $('#price_map_ht_3').text(prixEpaulment.toFixed(2) + ' €');
        $('#price_map_ttc_3').text(getWithTax(prixEpaulment).toFixed(2) + ' €');

        var diameter_capot = parseFloat($('#cube_diameter_capot').val().replace(',', '.'));
        const unifiedDiameter = parseFloat(diameter_capot) * 1000;
        const price_8023234 = getWithTax(parseFloat(idxcp_prix_de_decoupe) * unifiedDiameter) || 0;
        const price_80232334 = getWithTax(parseFloat(idxcp_prix_de_collage) * unifiedDiameter) || 0;
        $('#p_d_d_map_1').text(`${diameter_capot} m`)
        $('#price_map_4').text(`${parseFloat(idxcp_prix_de_decoupe)*1000} €/m² HT`)
        $('#price_map_ht_4').text(`${(parseFloat(idxcp_prix_de_decoupe) * unifiedDiameter).toFixed(2)} €`)
        $('#price_map_ttc_4').text(`${price_8023234.toFixed(2)} €`)
        $('#p_d_c_map_1').text(`${diameter_capot} m`)
        $('#price_map_5').text(`${parseFloat(idxcp_prix_de_collage)*1000} €/m² HT`)
        $('#price_map_ht_5').text(`${(parseFloat(idxcp_prix_de_collage) * unifiedDiameter).toFixed(2)} €`)
        $('#price_map_ttc_5').text(`${price_80232334.toFixed(2)} €`)

        totaleWithTax += price_8023234;
        totaleWithoutTax += parseFloat(idxcp_prix_de_decoupe) * unifiedDiameter;
        
        totaleWithTax += price_80232334;
        totaleWithoutTax += parseFloat(idxcp_prix_de_collage) * unifiedDiameter;

        const total_price_capot = price_87345+ price_8023234 + price_80232334;
        $('#tr_resume_prix_de_capot').text(`${total_price_capot.toFixed(2)} €`);

        var diameter_socle = parseFloat($('#cube_diameter_socle').val().replace(',', '.'));
        const unifiedDiameterSocle = parseFloat(diameter_socle) * 1000;
        const price_0923525 = getWithTax(parseFloat(prixDecoupeSocle) * unifiedDiameterSocle) || 0;
        const price_923525 = getWithTax(parseFloat(prixCollageSocle) * unifiedDiameterSocle) || 0;
        $('#p_d_d_map_2').text(`${diameter_socle} m`)
        $('#price_map_6').text(`${parseFloat(prixDecoupeSocle)*1000} €/m² HT`)
        $('#price_map_ht_6').text(`${parseFloat(prixDecoupeSocle) * unifiedDiameterSocle} €`)
        $('#price_map_ttc_6').text(`${price_0923525.toFixed(2)} €`)
        $('#p_d_c_map_2').text(`${diameter_socle} m`)
        $('#price_map_7').text(`${(parseFloat(prixCollageSocle)*1000).toFixed(2)} €/m² HT`)
        $('#price_map_ht_7').text(`${(parseFloat(prixCollageSocle) * unifiedDiameterSocle).toFixed(2)} €`)
        $('#price_map_ttc_7').text(`${price_923525.toFixed(2)} €`)

        totaleWithTax += price_0923525;
        totaleWithoutTax += parseFloat(prixDecoupeSocle) * unifiedDiameterSocle;
        
        totaleWithTax += price_923525;
        totaleWithoutTax += parseFloat(prixCollageSocle) * unifiedDiameterSocle;

        const total_price_socel = price_0923525+ price_923525 + price_09832423;
        $('#tr_resume_prix_de_socle').text(`${total_price_socel.toFixed(2)} €`);

        total = total + parseFloat(totaleWithTax);
        
        $('#price_map_totale_ttc').text(`${total.toFixed(2)} €`)
        $('#price_map_totale_ht').text(`${getWithoutTax(total)} €`)
        try{
            if (typeof updateInlinePrices === 'function') {
                var basePricePerSquareMeter = parseFloat($('.js_base_price').val().replace(',', '.'));
                const totalPricing = getWithTax(materiel_price)+basePricePerSquareMeter;
                updateInlinePrices(totalPricing.toFixed(2), (getWithoutTax(totalPricing)));
            }
        }catch(error){
        }
        
        discount = getDiscount(total);
        if (discount) {
            total = total - discount;
        }        

        // check if total is under minimun amount:
        if (typeof idxr_prix_fixe_vitrine !== 'undefined' && idxr_prix_fixe_vitrine && !isNaN(idxr_prix_fixe_vitrine)) {
            if (!isNaN(total) && parseFloat(total) < parseFloat(idxr_prix_fixe_vitrine)) {
                total = parseFloat(idxr_prix_fixe_vitrine);
            }
        }
    }else {
        $('#price_map_ht_7').text(`0 €`);
        $('#price_map_ttc_7').text(`0 €`);
        const is_predcoper = $('#idxr_is_predecoupe').val() === "true";
        if(!is_predcoper){
            if (!isNaN(surface) && surface > 0) {
                var surfaceCost = (basePricePerSquareMeter * surface).toFixed(2);
                total = parseFloat(surfaceCost.replace(',', '.'));
                // Afficher le coût basé sur la surface dans le span avec " €"
                // $("#js_resume_price_surface").html(surfaceCost + " €");
            } else {
                var surfaceCost = (basePricePerSquareMeter * 0);
                total = parseFloat(surfaceCost);
                $("#js_resume_price_surface").html(''); // Effacer le contenu s'il n'y a pas de coût basé sur la surface
            }
            $('#price_map_7').text(`0 €/m² HT`)
            $('#price_map_ht_7').text(`0 €`)
            $('#price_map_ttc_7').text(`0 €`)

            const pholder1 = $('#diameter_de_decoupe_price');
            const pholder2 = $('#diameter_de_decoupe_price2');
            const is_rectangle = $('#idxr_is_rectangle').val();
            const is_rectangle_polissage = $('#idxr_is_rectangle_polissage').val();
            // if (is_rectangle === 'true') idxcp_prix_de_decoupe = safeParseFloat(idxr_prix_de_decoupe_cube);
            
            (!isNaN(surface) && surface > 0) 
                ? $('#s_d_capot').text(`${surface.toFixed(2)} m²`) 
                : $('#s_d_capot').text('0.00 m²');
        
            $('#price_map_1').text(`${getWithoutTax(basePricePerSquareMeter).toFixed(2)} €/m²`);
            $('#price_map_ht_1').text(`${getWithoutTax(parseFloat(basePricePerSquareMeter) * parseFloat(surface)).toFixed(2)} €`);
            $('#price_map_ttc_1').text(`${(parseFloat(basePricePerSquareMeter) * parseFloat(surface)).toFixed(2)} €`);
        
            if ((pholder1.length > 0) && (pholder2.length > 0)) {
                const prixDecoupe1 = safeParseFloat(pholder1.val());
                const prixDecoupe2 = safeParseFloat(pholder2.val());

                const pholder = prixDecoupe1 + prixDecoupe2;
                // Parse and validate the price input
                // const prixDecoupeRaw = pholder.val().replace(',', '.');
                const prixDecoupe = parseFloat(pholder);
        
                if (!isNaN(prixDecoupe)) {
                    // Calculate the price with tax
                    let decoupePriceWithTax = 0;
                    
                    if (is_rectangle === 'true') decoupePriceWithTax = getWithTax(prixDecoupe2 * parseFloat(idxcp_prix_de_decoupe));
                    else decoupePriceWithTax = getWithTax(prixDecoupe * parseFloat(idxcp_prix_de_decoupe));
        
                    if (!isNaN(decoupePriceWithTax)) {
                        total += decoupePriceWithTax;
                    }
        
                    if (is_rectangle_polissage === 'true') {
                        const polishPriceWithTax = getWithTax(prixDecoupe * parseFloat(idxcp_prix_de_polissage));
        
                        if (!isNaN(polishPriceWithTax)) {
                            total += polishPriceWithTax;
                        }

                        $('#p_d_c_map_1').text(`${prixDecoupe.toFixed(4)} m`);
                        $('#price_map_5').text(`${parseFloat(idxcp_prix_de_polissage).toFixed(4)} €/mm HT`);
                        $('#price_map_ht_5').text(`${getWithoutTax(polishPriceWithTax).toFixed(2)} €`);
                        $('#price_map_ttc_5').text(`${polishPriceWithTax.toFixed(2)} €`);
        
                    } else {
                        $('#p_d_c_map_1').text(`0.00 m`);
                        $('#price_map_5').text(`${(parseFloat(idxcp_prix_de_polissage)).toFixed(4)} €/mm HT`);
                        $('#price_map_ht_5').text(`0.00 €`);
                        $('#price_map_ttc_5').text(`0.00 €`);
                    }
        
                    $('#resume_prix_de_decoupe_price').val(decoupePriceWithTax.toFixed(2));
        
                    $('#price_map_4').text(`${(parseFloat(idxcp_prix_de_decoupe) * 1000).toFixed(2)} €/m² HT`);
                    $('#price_map_ht_4').text(`${getWithoutTax(decoupePriceWithTax).toFixed(2)} €`);
                    $('#price_map_ttc_4').text(`${decoupePriceWithTax.toFixed(2)} €`);
                }
            }
        
        
            
            // discount = getDiscount(total);
            // if (discount) {
            //     total = total - discount;
            // }  
            
                    
        }else{
            if (!isNaN(surface) && surface > 0) {
                var surfaceCost = (basePricePerSquareMeter * surface).toFixed(2);
                total = parseFloat(surfaceCost.replace(',', '.'));
                // Afficher le coût basé sur la surface dans le span avec " €"
                // $("#js_resume_price_surface").html(surfaceCost + " €");
            } else {
                var surfaceCost = (basePricePerSquareMeter * 0);
                total = parseFloat(surfaceCost);
                $("#js_resume_price_surface").html(''); // Effacer le contenu s'il n'y a pas de coût basé sur la surface
            }
            
            // const prix_predecouppe = safeParseFloat($('#idxr_prix_de_predecoupe').val());
            // total = prix_predecouppe;
            $('#price_map_7').text(`${total.toFixed(2)} €/m² HT`)
            $('#price_map_ht_7').text(`${getWithoutTax(total)} €`)
            $('#price_map_ttc_7').text(`${total.toFixed(2)} €`)
        }
           
        //
        $('#price_map_totale_ttc').text(`${total.toFixed(2)} €`)
        $('#price_map_totale_ht').text(`${getWithoutTax(total)} €`)
        //
        // check if total is under minimun amount:
        if (typeof idxr_prix_fixe !== 'undefined' && idxr_prix_fixe && !isNaN(idxr_prix_fixe)) {
            if (!isNaN(total) && parseFloat(total) < parseFloat(idxr_prix_fixe)) {
                total = parseFloat(idxr_prix_fixe);
            }
        }
    }
    

    $('#resume_price_from_cube').val(getWithoutTax(total));

    $("#js_resume_total_price").val(total);
    // Format total price to two decimal places
    const totalFormatted = total.toFixed(2).replace(".", ",");

    // Display the formatted total price
    $("#js_resume_total_price").val(totalFormatted + " €");
    $("#resume_total_price").html(totalFormatted + " € TTC");
    $("#braig_ttc_price_set").html(totalFormatted + " €");
    $('#js_topblock_total_price').html(totalFormatted + " €");

    // Calculate and format the HT price
    const newTotal = total / (1 + (idxcp_originaltax / 100));
    const newTotalFormatted = newTotal.toFixed(2).replace(".", ",");

    // Display the HT price with the appropriate formatting
    $("#resume_total_price_ht").html("(" + newTotalFormatted + " € HT)");
    $("#braig_ht_price_set").html(newTotalFormatted + " €");


    // var formatter = formatPrice(total);
    // formatter.done(function(data){
    //     total_formated = jQuery.parseJSON(data);
    //     // $("#resume_total_price").html(total_formated);
    //     var total_cleaned = total_formated.replace(/[^\d,]/g, '');
    //     // Remplacer la virgule par un point pour le format décimal
    //     var total_replaced = total_cleaned.replace(",", ".");
    //     var total_float = parseFloat(total_replaced);
    //     // Calculer le montant hors taxes
    //     var newTotal = total_float / (1 + (idxcp_originaltax / 100));
    //     // Formater le montant hors taxes avec deux chiffres après la virgule
    //     var newTotalFormatted = newTotal.toFixed(2);
    //     // Afficher le montant total TTC
    //     $("#resume_total_price").html(total_formated + " TTC");
    //     $("#braig_ttc_price_set").html(total_formated);
    //     $("#js_resume_total_price").val(total_formated + " €");
    //     // Afficher le montant hors taxes
    //     $("#resume_total_price_ht").html("("+ newTotalFormatted + " € HT)");
    //     $("#braig_ht_price_set").html(newTotalFormatted + " €");
       
    //     $('#js_topblock_total_price').html(total_formated);
    // });
    if ($('#idxrcustomprouct_quantity_wanted').length && $('#idxrcustomprouct_quantity_wanted').val() > 1) {        
        var quantity = $('#idxrcustomprouct_quantity_wanted').val();
        var total_qty = total*quantity;
        var formattet_qty = formatPrice(total_qty);
        formattet_qty.done(function(data){
            total_formated = jQuery.parseJSON(data);
            $("#qty_total_price").html(total_formated + " TTC");
            
        });
        $('#qty_total_price_tr').show();
   } else {
       $('#qty_total_price_tr').hide();
   }
   
   $('#add-to-cart-button-unique-12345').prop('disabled', false);
   $('#add-to-cart-button-unique-12345').removeClass('disabled');
}

function getWithTax(price){
    return parseFloat(price) * (1 + (idxcp_originaltax / 100));
}

function getWithoutTax(price){
    return parseFloat(price) / (1 + (idxcp_originaltax / 100)).toFixed(2);
}

function getDiscount(total){
    var range_discount = verifyAmountDiscount();
    var option_discount = 0;
    $('.js_resume_price_wodiscount').each(function(){
        var option_wod = $(this).val();
        var option_wd = $(this).parent().find('.js_resume_price').val();
        option_discount += (Number(option_wod) - Number(option_wd));
    });
    
    if($("#idxcp_discount_type").length == 0 && !range_discount && !option_discount) {
        return 0;
    }
    var discount = 0;
    
    if($("#idxcp_discount_type").length && $("#idxcp_discount_type").val()) {
        var type = $("#idxcp_discount_type").val();
        var amount = parseFloat($("#idxcp_discount_amount").val());
        
        if (type == 'fixed' || type == 'amount') {
            discount = amount;
        } else {
            discount = (parseFloat(total-option_discount)*(amount/100));
        }
    }
        
    if (range_discount) {
        if (range_discount.reduction_type == 'amount') {
            discount += range_discount.reduction;
        } else {
            discount += (parseFloat(total-option_discount)*(range_discount.reduction));
        }
    }
    
    if (option_discount) {
        discount += option_discount;
    }

    if (discount) {
        $('#idxcp_discount_value').html(Number(discount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#idxcp_discount_value').closest('tr').hide();
        return discount;
    } else {
        $('#idxcp_discount_value').closest('tr').hide();
        return false;
    }
}

function getPercentDiscount(price)
{
    var discount = 0;
    if($("#idxcp_discount_type").length && $("#idxcp_discount_type").val()) {
        var type = $("#idxcp_discount_type").val();
        var amount = parseFloat($("#idxcp_discount_amount").val());
        
        if (type == 'fixed' || type == 'amount') {
            discount = 0;
        } else {
            discount = (parseFloat(price)*(amount/100));
        }
    }
    return discount;
}
 
function toggleCheck(id, state) {
    var stepIdParts = id.split('_');
    if (stepIdParts.length < 3) {
        return;
    }
    
    var stepId = "#step_content_" + stepIdParts[2];
    var $div = $(stepId);
    
    if ($div.length === 0) {
        return;
    }
    
    var $img = $div.find(".step-img");

    if ($img.length > 0) {
        if (state === false) {
            $img.attr("src", "/modules/idxrcustomproduct/img/icon/error.png");
            var $img = $div.find(".check").removeClass('check');
        } else {
            $img.attr("src", "/modules/idxrcustomproduct/img/icon/86.png");
        }
    }
}


function checkFinish(){
    if(!parseInt(show_fav)){
        if (window.matchMedia("(max-width: 767px)").matches)
        {
            $('#idxrcustomproduct_save').css('display', 'none');
        } else {
            $('#idxrcustomproduct_save').css('visibility', 'hidden');
        }        
    }
    finish = true;
    $('.sel_opt').each(function() {
        if($(this).html() === 'false' && $(this).attr('data-required') === 'true' && $(this).attr('data-optional') ==='false'){
            finish = false;
            console.log('remaining false '+$(this).attr('id'));
            toggleCheck($(this).attr('id'), false);
        }else{
            toggleCheck($(this).attr('id'), true);
        }
    });
    if(finish){        
        if(es17 && $.isFunction($("#idxrcustomprouct_quantity_wanted").TouchSpin)){
            $("#idxrcustomprouct_quantity_wanted").TouchSpin({
                min: min_qty,
                verticalbuttons: true
            });
        }else{
            if (show_fav) {
                favbutton_text = '<button class="btn btn-link" type="submit" id="idxrcustomproduct_save">'
                    +'<i class="fa fa-save icon icon-save"></i> '+favbutton
                +'</button>';
            } else {
                favbutton_text = '';
            }
            $('#submit_idxrcustomproduct').html('<button class="btn btn-success pull-right" type="submit" id="idxrcustomproduct_send">'+send_text+'</button>'
                +favbutton_text
                +'<span id="quantity_wanted_p" class="pull-right">'
                   +'<input min="'+min_qty+'" name="qty" id="quantity_wanted" class="text" value="'+min_qty+'" type="number">'
                   +'<span class="clearfix"></span>'
                +'</span>'
            );
        }
        $('#submit_idxrcustomproduct_alert').hide();
        $('#idxrcustomproduct_save').prop("disabled",false);
        $('#idxrcustomproduct_send').prop("disabled",false);
        $('#idxrcustomproduct_topblock_send').removeClass('disabled');
        $('#idxrcustomproduct_topblock_send_message').remove();
        $('#add-to-cart-button-unique-12345').removeClass('disabled');
    }else{
        $('#submit_idxrcustomproduct_alert').show();
        $('#idxrcustomproduct_topblock_send').addClass('disabled');
        $('#idxrcustomproduct_save').prop("disabled",true);
        $('#idxrcustomproduct_send').prop("disabled",true);
        $('#add-to-cart-button-unique-12345').addClass('disabled');
    }
}

function refreshProductImage(status) {
    $.post( url_ajax, { action: "getimgstatus", id_configuration: idxcp_id_configuration, status: status})
    .done(function(data){
        data = jQuery.parseJSON(data);
        if (data && data != "ko") {
            image = data;
            changeImgCover(image);
              // Appeler la fonction ajouterAvecDelai
            restaurerContenu();
        }
    });
}
/**New add with wassim*/
function changeImgCover(image) {
    $(cover_image_id).attr('src',image);
    //Add with team wassim Novatis
        // Ajoute les balises <span> avec les identifiants "width" et "length"
    var $productCover = $(cover_image_id).parent('.product-cover');
    if ($productCover.find('span#width').length === 0) {
        $productCover.append('<div class="width"><span class="arrow-db"></span><span id="width">Longueur</span></div><div class="length"><span id="length">Largeur</span><span class="arrow-db"></span></div><div class="width-c hidden"><span id="width-c">C</span><span class="arrow-db"></div> <div class="length-d hidden"><span class="arrow-db"></span><span id="length-d">D</span></div>');
        $productCover.append('<img class="option-form img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou1 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou2 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou3 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou4 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou5 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
        $productCover.append('<img class="option-forms trou6 img-fluid" src=""  loading="lazy" style="width: 100%; display:none">');
    }
    if ($('.fancybox.shown').length != 0) {
        $('.fancybox.shown').attr("href",image);
    }
    if ($('.zoomWindowContainer .zoomWindow').length != 0) {
        $('.zoomWindowContainer .zoomWindow').css('background-image', 'url(' + image + ')');
    }
    if ($('#product-modal').length != 0) {
        $('#product-modal').find('.js-modal-product-cover').attr("src",image);
        $('#product-modal').find('#thumbnails').remove();
    }
}

function restaurerContenu() {
    var length_img = document.querySelector("#length"); 
    var width_img = document.querySelector("#width");   
    var length_d_img = document.querySelector("#length-d"); 
    /***B D C*/
    var length = document.querySelector(".length"); 
    var length_d = document.querySelector(".length-d"); 
    var width_c = document.querySelector(".width-c");  
    
    // Sauvegarder le contenu initial
    var length_img_contenu_initial = "Longueur"; // Remplacez "A" par le contenu initial
    var width_img_contenu_initial = "Largeur"; // Remplacez "B" par le contenu initial

    var Tr = document.querySelector("#resume_price_block_1_8 .option_title");
    var Tr_s1 = document.querySelector("#resume_price_block_76_2 .option_title");
    var Poly = document.querySelector("#resume_price_block_1_3 .option_title");
    var Poly_s1 = document.querySelector("#resume_price_block_49_0 .option_title");
    var Poly_s2 = document.querySelector("#resume_price_block_49_1 .option_title");
    var Poly_s3 = document.querySelector("#resume_price_block_49_2 .option_title");
    var Poly_s4 = document.querySelector("#resume_price_block_49_4 .option_title");
    var Poly_s5 = document.querySelector("#resume_price_block_49_5 .option_title");
    var Poly_s6 = document.querySelector("#resume_price_block_49_8 .option_title");
    var Poly_s7 = document.querySelector("#resume_price_block_49_9 .option_title");
    var Poly_s8 = document.querySelector("#resume_price_block_49_10 .option_title");
    var Poly_s9 = document.querySelector("#resume_price_block_49_11 .option_title");
    if (Tr)  {
        if (Tr_s1) {
            width_img.innerHTML =  "A"; 
            length_d_img.innerHTML = "B";
            length_d.classList.remove("hidden");
            length.classList.add("hidden");
            width_c.classList.add("hidden");
        } else {
            width_img.innerHTML =  "A";
            length_img.innerHTML = "B";
            length.classList.remove("hidden");
            length_d.classList.add("hidden");
            width_c.classList.add("hidden");
        }

    } else if (Poly) {
      if (Poly_s1) {
        length_img.innerHTML = "B";
        width_img.innerHTML =  "A";
        length_d.classList.add("hidden");
        width_c.classList.add("center");
        width_c.classList.remove("left");
        width_c.classList.remove("hidden");
        length.classList.remove("hidden");
      } else if (Poly_s2) {
        length_img.innerHTML = "C";
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "B";
        length_d.classList.remove("hidden");
        width_c.classList.add("hidden");
        length.classList.remove("hidden");
      } else if (Poly_s3) {
        length_img.innerHTML = "B";
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "C";
        length_d.classList.remove("hidden");
        width_c.classList.add("hidden");
        length.classList.remove("hidden");
      } else if (Poly_s4) {
        length_img.innerHTML = "B";
        width_img.innerHTML =  "A";
        width_c.classList.remove("hidden");
        width_c.classList.remove("center","left");
        length_d.classList.add("hidden");
        length.classList.remove("hidden");
      } else if (Poly_s5) {
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "B";
        width_c.classList.remove("hidden","center");
        width_c.classList.add("left");
        length.classList.add("hidden");
        length_d.classList.remove("hidden");
      } else if (Poly_s6) {
        length_img.innerHTML = "B";
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "D";
        width_c.classList.remove("hidden","center","left");
        length_d.classList.remove("hidden");
        length.classList.remove("hidden");
      } else if (Poly_s7) {
        length_img.innerHTML = "D";
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "B";
        width_c.classList.remove("hidden","center");
        width_c.classList.add("left");
        length_d.classList.remove("hidden");
      } else if (Poly_s8) {
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "B";
        length.classList.add("hidden");
        width_c.classList.remove("hidden","center","left");
        length_d.classList.remove("hidden");
      }  else if (Poly_s9) {
        width_img.innerHTML =  "A";
        length_d_img.innerHTML =  "B";
        length.classList.add("hidden");
        width_c.classList.remove("hidden","center","left");
        width_c.classList.add("left");
        length_d.classList.remove("hidden");
       } 
    }
    else {
      width_c.classList.add("hidden"); 
      length_d.classList.add("hidden");
      length.classList.remove("hidden");
      length_img.innerHTML = length_img_contenu_initial;
      width_img.innerHTML = width_img_contenu_initial;
    }
}

// Associer chaque ID d'élément à un chemin d'image
var elementImageMap = {
    "card_1_0": "/img/idxrcustomproduct/configurations/forms/48.png",
    "card_1_1": "/img/idxrcustomproduct/configurations/forms/34.png",
    "card_1_8": "/img/idxrcustomproduct/configurations/forms/35.png",
    "card_1_2": "/img/idxrcustomproduct/configurations/forms/36.png",
    "card_1_3": "/img/idxrcustomproduct/configurations/forms/37.png",
    "card_1_4": "/img/idxrcustomproduct/configurations/forms/38.png",
  
    "card_74_0": "/img/idxrcustomproduct/configurations/forms/48.png",
    "card_74_1": "/img/idxrcustomproduct/configurations/forms/33.png",
  
    "card_75_0": "/img/idxrcustomproduct/configurations/forms/41.png",
    "card_75_1": "/img/idxrcustomproduct/configurations/forms/34.png",
  
    "card_76_0": "/img/idxrcustomproduct/configurations/forms/35.png",
    "card_76_1": "/img/idxrcustomproduct/configurations/forms/42.png",
    "card_76_2": "/img/idxrcustomproduct/configurations/forms/43.png",
  
    "card_10_1": "/img/idxrcustomproduct/configurations/forms/36.png",
    "card_10_2": "/img/idxrcustomproduct/configurations/forms/45.png",
    "card_10_3": "/img/idxrcustomproduct/configurations/forms/44.png",
  
    "card_49_0": "/img/idxrcustomproduct/configurations/forms/57.png",
    "card_49_1": "/img/idxrcustomproduct/configurations/forms/59.png",
    "card_49_2": "/img/idxrcustomproduct/configurations/forms/60.png",
    "card_49_4": "/img/idxrcustomproduct/configurations/forms/55.png",
    "card_49_5": "/img/idxrcustomproduct/configurations/forms/61.png",
    "card_49_8": "/img/idxrcustomproduct/configurations/forms/62.png",
    "card_49_9": "/img/idxrcustomproduct/configurations/forms/63.png",
    "card_49_10": "/img/idxrcustomproduct/configurations/forms/64.png",
    "card_49_11": "/img/idxrcustomproduct/configurations/forms/65.png",
  
    "card_53_0": "/img/idxrcustomproduct/configurations/forms/52.png",
    "card_53_1": "/img/idxrcustomproduct/configurations/forms/75.png",
  
    "card_54_0": "/img/idxrcustomproduct/configurations/forms/50.png",
    "card_54_1": "/img/idxrcustomproduct/configurations/forms/52.png",
    "card_54_2": "/img/idxrcustomproduct/configurations/forms/51.png",
    "card_54_5": "/img/idxrcustomproduct/configurations/forms/74.png",
    "card_54_3": "/img/idxrcustomproduct/configurations/forms/53.png",
    "card_54_4": "/img/idxrcustomproduct/configurations/forms/54.png",
  
    "card_55_0": "/img/idxrcustomproduct/configurations/forms/68.png",
    "card_55_1": "/img/idxrcustomproduct/configurations/forms/73.png",
    "card_55_2": "/img/idxrcustomproduct/configurations/forms/69.png",
    "card_55_3": "/img/idxrcustomproduct/configurations/forms/75.png",
    "card_55_4": "/img/idxrcustomproduct/configurations/forms/72.png",
    "card_55_5": "/img/idxrcustomproduct/configurations/forms/67.png",
    // Ajoutez d'autres éléments et chemins d'image selon vos besoins
  };
  
  // Fonction réutilisable pour gérer le clic sur un élément
  function handleClick(elementId) {
    return function() {
      setTimeout(function() {
          const image = elementImageMap[elementId];
          changeImgCover(image);
          hideImages();
        restaurerContenu();
        updateTotal();
      }, 1000); // Délai de 3 secondes (3000 millisecondes)
    };
  }
  // Boucle pour ajouter des gestionnaires d'événements de clic
  for (var elementId in elementImageMap) {
    var cardElement = document.getElementById(elementId);
    if (cardElement) {
      cardElement.addEventListener("click", handleClick(elementId));
    }
  }
  // Fonction pour masquer les images dans le tableau donné
  function hideImages() {
    var trou1 = document.querySelector('.trou1');
    if (trou1) {
        trou1.style.display = 'none';
    }
    var trou2 = document.querySelector('.trou2');
    if (trou2) {
        trou2.style.display = 'none';
    }
    var trou3 = document.querySelector('.trou3');
    if (trou3) {
        trou3.style.display = 'none';
    }
    var trou4 = document.querySelector('.trou4');
    if (trou4) {
        trou4.style.display = 'none';
    }
    var trou5 = document.querySelector('.trou5');
    if (trou5) {
        trou5.style.display = 'none';
    }
}
/**New add with wassim*/
function formatPrice(price) {
    return $.post( url_ajax, { action: "formatprice", price: price, idxcp_originaltax: idxcp_originaltax, idxcp_newtax: idxcp_newtax});
}

function refreshImpact(component, option) {
    $.each(option, function( index, value ) {
        checkTaxChange(component, value);
        refreshImpactUnit(component, value);
    });
}

function checkTaxChange(component, option) {
    $.post( url_ajax, { 
        action: "getTaxChange", 
        component: component,
        option: option
    })
    .done(function(data){
        var tax_change =  jQuery.parseJSON(data);
        if (tax_change) {            
            if (idxcp_originaltax != tax_change.rate || idxcp_newtax != tax_change.rate){
                idxcp_newtax = tax_change.rate;
                base_price = parseFloat($('.js_base_price').val().replace(',',''));
                var base_formater = formatPrice(base_price);
                base_formater.done(function(data){
                    total_formated = jQuery.parseJSON(data);
                    $('#idx_resume_base_price').html(total_formated);
                });
                updateTotal();
            }
        }
    });
}

function refreshImpactUnit(component, option) {
    var att_product = $('#option_'+component+'_'+option).attr('data-att-product');
    var base_product = idxcp_id_product;
    $.post( url_ajax, { 
        action: "refreshimpact", 
        configuration: idxcp_id_configuration, 
        component: component,
        option: option,
        att_product: att_product,
        base_product: base_product,
    })
    .done(function(data){
        var changes = jQuery.parseJSON(data);
        $.map(changes, function(value, index) {
            var option = index.split('_');            
            $('#option_'+option[0]+'_'+option[1]).attr('data-price-impact', value.price_impact);
            $('#card_'+option[0]+'_'+option[1]).find('.idxroption_price').html(value.price_impact_formatted);
            updateimpact($('#component_step_'+option[0]));
        });       
    });
}
function verifyAmountDiscount() {
    if (!specific_price_ranges) {
        return false;
    }
    
    var qty = $('#idxrcustomprouct_quantity_wanted').val();
    var final_discount = false;
    $.each(specific_price_ranges, function(index, range) {
        if (parseInt(qty) >= parseInt(range.from_quantity)) {
            final_discount = range;
        }
    });
    
    if (final_discount) {
        return final_discount;
    }
}

function verifyAvailableQty(qty) {
    
}
function removeFromCart(id_product) {
    if (typeof ajaxCart !== 'undefined') {
        ajaxCart.remove(id_product, null, null, null);
    }
    if (typeof prestashop !== 'undefined') {
        $['ajax']({
            type: 'POST',
            headers: { "cache-control": "no-cache" },
            cache: false,
            url: prestashop['urls']['pages']['cart'] + '?rand=' + new Date()['getTime'](),
            async: true,
            cache: false,
            dataType: 'json',
            data: 'delete=1&ajax=true&id_product=' + id_product + '&token=' + prestashop['static_token'] + '&ipa=0',
            success: function() {
                console.log('borrado');
            }
        });
    }
}

function updatechecks(component) {
    $('input[name=option_'+component+']').each(function(){
        var checked = $(this).prop('checked');
        var option = $(this).attr('data-value');
        togglecheck(component, option, checked);
    });
}

function togglecheck(component, option, check) {
    /**Edit with team wassim novatis */
    if (check) {
        $('#selected_'+component+'_'+option).show();
        $('#step_content_'+component).addClass("finished");
        $('#card_'+component+'_'+option).addClass("check");
    } else {
        $('#selected_'+component+'_'+option).hide();
        $('#card_'+component+'_'+option).removeClass("check");
    }
    /*End*/
    if ($.inArray( $('#component_step_'+component).attr('data-multivalue'), ["multi_qty", ""] ) >= 0) {
        if (check) {
            $('#card_'+component+'_'+option+' .option_qty_block').show();
        } else {
            $('#card_'+component+'_'+option+' .option_qty_block').hide();
        }
        readjustheight(component);
    }
}

function readjustheight(component) {
    var max_height = 0;
    var equals = true;
    var component_div = $('#component_step_'+component);
    component_div.find('.option_div').css({'height': 'auto'});
    component_div.find('.option_div').each(function(){
        if ($(this).height() > max_height) {
            if (max_height > 0) {
                equals = false;
            }
            max_height = $(this).height();
        } else if ($(this).height() < max_height) {
            equals = false;
        }
    });

    if (!equals) {
        max_height += 2;
        component_div.find('.option_div').css({'height': max_height});
    }
}

function setJsResumeValue(step_id, option_id) {
    if(option_id !== false && typeof option_id != 'undefined'){
        if ($.isArray(option_id)) {
            if (option_id.length === 0) {
                $('#js_opt_'+step_id+'_value').html('false');
            } else {
                $('#js_opt_'+step_id+'_value').html(option_id.join('&'));
                $.each(option_id,function(index, value) {
                    opt_qty = $('#option_'+step_id+'_'+value+'_qty').val();
                    if (opt_qty > 0) {
                        option_id[index] = value+'x'+opt_qty;
                    }
                });
                $('#js_opt_'+step_id+'_value_wqty').html(option_id.join('&'));
            }
        } else {
            $('#js_opt_'+step_id+'_value').html(option_id);
        }
    }
}
