/**
 * 2020 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$(document).on('click', '.change_taxonomy', function (e) {
    var lang = parseInt($('.lang_google_lists').val());
    var ind = parseInt($(this).siblings('.ind').val());
    var list_container = $(this).parent(0).siblings('.taxonomy_breadcrumb');

    if (typeof (currentIndex) == 'undefined') {
        var currentIndex = $('.lang_google_lists').data('currentindex')
    }

    getTaxonomyOptionsContent(lang, currentIndex, ind, list_container, $(this));
});

function getTaxonomyOptionsContent(lang, currentIndex, ind, obj, th) {
    if (lang > 0) {
        $.ajax({
            type: 'POST',
            headers: {'cache-control': 'no-cache'},
            url: currentIndex,
            data: {
                ajax: 'true',
                getTaxonomyOptionsLists: 1,
                getTaxonomyLang: lang,
                setInd: ind
            },
            beforeSend: function () {
                $('.lang_google_lists').prop('disabled', true);
            },
            success: function (msg) {
                if (msg.length) {
                    obj.html(msg);
                    reloadChosen($(obj).find('.chosen'));
                    $(th).css('display', 'none')
                        .siblings('.change_taxonomy_save')
                        .removeClass('hidden')
                        .css('display', 'inline-block')
                        .siblings('.change_taxonomy_delete')
                        .addClass('hidden');
                }
                $('.lang_google_lists').prop('disabled', false);
            },
            error: function () {
                $('.lang_google_lists').prop('disabled', false);
            }
        });
    }

    return false;
}

function reloadChosen(obj) {
    $(obj).chosen('destroy');
    $(obj).chosen({
        disable_search_threshold: 10,
        search_contains: true
    });
}

$(document).on('click', '.change_taxonomy_delete', function (e) {
    var lang = parseInt($('.lang_google_lists').val());
    var ind = parseInt($(this).siblings('.ind').val());
    if (typeof (currentIndex) == 'undefined') {
        var currentIndex = $('.lang_google_lists').data('currentindex')
    }

    if (confirm('This param will be deleted for good. Please confirm.')) {
        setTaxonomyOptionsContent(lang, currentIndex, ind, null, $(this));
    }
});

// save:
$(document).on('click', '.change_taxonomy_save', function (e) {
    var lang = parseInt($('.lang_google_lists').val());
    var ind = parseInt($(this).siblings('.ind').val());
    var taxonomy_selected = parseInt($(this).parent(0).siblings('.taxonomy_breadcrumb').find('select.taxonomy_option_list').val());

    if (typeof (currentIndex) == 'undefined') {
        var currentIndex = $('.lang_google_lists').data('currentindex')
    }

    setTaxonomyOptionsContent(lang, currentIndex, ind, taxonomy_selected, $(this));
});

function setTaxonomyOptionsContent(lang, currentIndex, ind, taxonomy_selected, obj) {
    if (lang > 0) {
        $.ajax({
            type: 'POST',
            headers: {'cache-control': 'no-cache'},
            url: currentIndex,
            data: {
                ajax: 'true',
                setTaxonomyOptionsLists: 1,
                setTaxonomyLang: lang,
                setInd: ind,
                taxonomy_selected: taxonomy_selected
            },
            beforeSend: function () {
                $('.lang_google_lists').prop('disabled', true);
            },
            success: function (res) {
                objRes = $.parseJSON(res);

                if (!((typeof objRes != 'undefined') && (objRes !== null) && (typeof objRes === 'object'))) {
                    alert('Error save object!');
                    console.log(res);
                }

                if (objRes.deleted === true) {
                    $(obj).css('display', 'none')
                        .siblings('.change_taxonomy').css('display', 'inline-block')
                        .siblings('.change_taxonomy_delete')
                        .addClass('hidden')
                        .css('display', 'inline-block')
                        .parent()
                        .siblings('.td_taxonomy_id').html('-')
                        .siblings('.taxonomy_breadcrumb').html('-');
                } else {
                    $(obj).parent().siblings('.td_taxonomy_id').html('<b>' + objRes.language + '</b>: ' + objRes.taxonomy_id);
                    $(obj).parent().siblings('.taxonomy_breadcrumb').html('<b>' + objRes.language + '</b>: ' + objRes.name_taxonomy);
                    $(obj).css('display', 'none')
                        .siblings('.change_taxonomy').css('display', 'inline-block')
                        .siblings('.change_taxonomy_delete')
                        .removeClass('hidden')
                        .css('display', 'inline-block')
                }

                $('.lang_google_lists').prop('disabled', false);
            },
            error: function () {
                $('.lang_google_lists').prop('disabled', false);
            }
        });
    }
    return false;
}

// load bulk update taxonomy
$(document).on('click', '.load-bulk-taxonomy-js', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var lang = parseInt($('.lang_google_lists').val());
    if (typeof (currentIndex) == 'undefined') {
        var currentIndex = $('.lang_google_lists').data('currentindex')
    }

    $.ajax({
        type: 'POST',
        headers: {'cache-control': 'no-cache'},
        url: currentIndex,
        data: {
            ajax: 'true',
            getTaxonomyOptionsListsForBulk: 1,
            getTaxonomyLang: lang,
            currentIndex: currentIndex
        },
        beforeSend: function () {
            $('.load-bulk-taxonomy-js').prop('disabled', true);
        },
        success: function (msg) {
            $('.g-taxonomy-title').removeClass('col-lg-6').addClass('col-lg-2');
            $('.g-taxonomy-bulk-action').removeClass('col-lg-6').addClass('col-lg-10');
            $('.g-taxonomy-w-title').hide();
            $('.bulk-taxonomy-upd-container').html(msg);
            $('.load-bulk-taxonomy-js').hide();
            reloadAllChosen();
        },
        error: function (msg) {
            $('.load-bulk-taxonomy-js').prop('disabled', false);
        }
    });
});

$(document).on('click', '.js-btn-taxonomy-close', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('.load-bulk-taxonomy-js').show().prop('disabled', false);
    $('.bulk-taxonomy-upd-container').html('');
    $('.g-taxonomy-title').removeClass('col-lg-2').addClass('col-lg-6');
    $('.g-taxonomy-bulk-action').removeClass('col-lg-10').addClass('col-lg-6');
    $('.g-taxonomy-w-title').show();
});

function reloadAllChosen() {
    $('.chosen').chosen('destroy');
    $('.chosen').chosen({
        disable_search_threshold: 10,
        search_contains: true
    });
}

$(function () {
    $('.js-toggle-ref').on('click', function () {
        var ref = $(this).data('ref');
        if ($('.' + ref).length) {
            $('.' + ref).toggle();
        }
    });

    $('.js-toggle-content-links').on('click', function () {
        $('.content-links').toggle();
    })
});

$(document).on('click', '.js-add-new-custom-atr', function (e) {
    e.preventDefault();
    e.stopPropagation();

    if ($('.custom_attribute_name').hasClass('error')) {
        $('.custom_attribute_name').removeClass('error');
    }

    var attrGKeyName = $('.custom_attribute_name').val().trim();
    var attrKey = $('.custom_attribute_section option:selected').val();
    var attrName = $('.custom_attribute_section option:selected').text();

    if (!attrGKeyName.length) {
        $('.custom_attribute_name').addClass('error');
        return;
    }

    $('.custom_attribute_name').val('');

    var cRow = '<div class="row dec-row">' +
        '<input type="hidden" name="custom_attr_key[]" value="' + attrGKeyName + '">' +
        '<input type="hidden" name="custom_attr_id[]" value="' + attrKey + '">' +
        '<div class="col-md-11">' +
        '<span class="example-row">' +
        '&lt;' + attrGKeyName + '> ' + attrName + ' &lt;/' + attrGKeyName + '>' +
        '</span>' +
        '</div>' +
        '<div class="col-lg-1">' +
        '<span class="js-remove-attr-line"><i class="material-icons">delete</i></span>' +
        '</div>' +
        '</div>';
    $(cRow).insertAfter($('.attribute-mod-container'));
});

$(document).on('click', '.js-add-all-custom-atr', function (e) {
    e.preventDefault();
    e.stopPropagation();

    $('.custom_attribute_section option').each(function () {
        var optionItem = {
            id: $(this).val(),
            title: $(this).text(),
            slug: 'g:' + $(this).text().toLowerCase().replaceAll(' ', '_'),
        };

        if (!$('.custom_attribute input[name="custom_attr_id[]"][value=' + $(this).val() + ']').length) {
            var cRow = '<div class="row dec-row">' +
                '<input type="hidden" name="custom_attr_key[]" value="' + optionItem.slug + '">' +
                '<input type="hidden" name="custom_attr_id[]" value="' + optionItem.id + '">' +
                '<div class="col-md-11">' +
                '<span class="example-row">' +
                '&lt;' + optionItem.slug + '> ' + optionItem.title + ' &lt;/' + optionItem.slug + '>' +
                '</span>' +
                '</div>' +
                '<div class="col-lg-1">' +
                '<span class="js-remove-attr-line"><i class="material-icons">delete</i></span>' +
                '</div>' +
                '</div>';

            $(cRow).insertAfter($('.attribute-mod-container'));
        }
    })
})

$(document).on('click', '.js-remove-attr-line', function (e) {
    if (confirm($('.remove_tr_msg').val())) {
        e.currentTarget.closest('.dec-row').remove();
    }
});

$(document).on('click', '.add_new_custom_param', function (event) {
    var feature_id = $('#added_custom_param_feature').val();
    var feature_name = $('#added_custom_param_feature option:selected').text();
    var feature_param = $('#added_custom_param').val();
    if (feature_param.length > 2) {
        $('#added_custom_param').val('');
        $('#features_custom_selected').append(
            '<li>' +
            '<input type="hidden" name="feature_custom_inheritage[]" value="' + feature_id + '">' +
            '<input type="hidden" name="feature_custom_inheritage_param[]" value="' + encodeURI(feature_param) + '">' +
            '<span class="feature_custom">' +
            '&lt;' + feature_param + '> ' + feature_name + ' &lt;/' + feature_param + '> ' +
            '</span>' +
            '<span class="feature_removed"><i class="material-icons">delete</i></span>' +
            '</li>'
        );
    }
});

$(document).on('click', '.add_new_option', function (event) {
    const itemName = $(this).attr('data-ref');
    const feature_id = $('#' + itemName + '_select').val();
    const feature_name = $('#' + itemName + '_select option:selected').text();
    const feature_param = $('#' + itemName + '_param').val();
    if (feature_param.length) {
        $('#' + itemName + '_param').val('');
        $('#' + itemName + '_selected').append(
            '<li class="custom-option-item">' +
            '<input type="hidden" name="' + itemName + '[]" value="' + feature_id + '">' +
            '<input type="hidden" name="' + itemName + '_param[]" value="' + encodeURI(feature_param) + '">' +
            '<span class="feature_custom">' +
            '&lt;' + feature_param + '> ' + feature_name + ' &lt;/' + feature_param + '> ' +
            '</span>' +
            '<span class="feature_removed"><i class="material-icons">delete</i></span>' +
            '</li>'
        );
    }
});

$(document).on('click', '.feature_removed', function (event) {
    if (confirm($('.remove_tr_msg').val())) {
        $(event.currentTarget).parent().remove();
    }
});
