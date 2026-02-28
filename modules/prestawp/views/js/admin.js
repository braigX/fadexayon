/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Presta.Site
 * @copyright 2017 Presta.Site
 * @license   LICENSE.txt
 */
$(function () {
    $('.pst-tabs-list a').on('click', function(e) {
        e.preventDefault();

        var tab_id = $(this).attr('href');
        var hash = $(this).data('hash');

        pswp_openTab($(this), tab_id);
        window.location.hash = hash;
    });

    if (window.location.hash) {
        var tab_id = window.location.hash.replace('#tab-', '#psttab-');
        var id = tab_id.replace('#psttab-', '');
        if ($('#psttn-' + id).length) {
            pswp_openTab($('#psttn-' + id), tab_id);
        }
    }

    pswp_initColorPicker();
    pswp_displaySelectedCatsInfo();

    // toggle extra settings
    $(document).on('click', '.pswp_show_more_options', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $parent = $(this).closest('form');

        $(this).hide();
        $parent.find('.pswp_hide_more_options').show();
        $parent.find('.pswp_more_options_row').slideDown(200);
        pswp_updateExtraOptionsDisplay(0);
    });
    $(document).on('click', '.pswp_hide_more_options', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $parent = $(this).closest('form');

        $(this).hide();
        $parent.find('.pswp_show_more_options').show();
        $parent.find('.pswp_more_options_row').slideUp(200);
        pswp_updateExtraOptionsDisplay(0);
    });

    $('[name=SHOW_FEATURED_IMAGE], [name=SHOW_FEATURED_IMAGE_PRODUCT], [name=SHOW_FEATURED_IMAGE_PAGE], [name=CAROUSEL], [name=CAROUSEL_PRODUCT]').on('change', function () {
        pswp_updateExtraOptionsDisplay(200);
    });
    $('#prestawp_block-wrp').on('change', '[name=show_featured_image], [name=strip_tags], [name=carousel]', function () {
        pswp_updateExtraOptionsDisplay(200);
    });

    $('#pswp-hide-guide').on('click', function () {
        $('#pswp-guide').hide(300);
        $.ajax({
            url: pswp_ajax_url,
            data: {ajax: true, action: 'hideGuide'},
            method: 'post',
            success: function () {
                // do nothing
            }
        });
    });

    $(document).on('click', '#pswp-new-block-btn', function (e) {
        e.preventDefault();

        $('#prestawp_block_form').slideToggle('100');
    });

    // Delete block from the list
    var block_del_selector = '#form-prestawp_block .delete';
    if (pswp_psv == 1.5) {
        block_del_selector = 'table.prestawp_block .delete';
    }
    $(document).on('click', block_del_selector, function (e) {
        e.preventDefault();

        var url = $(this).attr('href');
        $.ajax({
            url: url,
            method: 'post',
            beforeSend: function () {
                $('#prestawp_block-wrp').addClass('pst-loading');
            },
            complete: function () {
                pswp_reloadBlocks();
            }
        });
    });

    $(document).on('click', '.pswp_active_toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var id_block = $(this).data('id-block');

        $.ajax({
            url: pswp_ajax_url,
            data: {ajax: true, action: 'changeBlockStatus', id_block: id_block},
            method: 'post',
            beforeSend: function () {
                $('#prestawp_block-wrp').addClass('pst-loading');
            },
            success: function () {
                pswp_reloadBlocks();
            }
        });
    });

    // Edit block in the list
    var block_edit_selector = '#form-prestawp_block .edit, #form-prestawp_block td.pointer';
    if (pswp_psv == 1.5) {
        block_edit_selector = 'table.prestawp_block .edit, table.prestawp_block td.pointer';
    }
    $(document).on('click', block_edit_selector, function (e) {
        e.preventDefault();

        var $edit_row = $(this).parents('tr:first').next('.pswp_edit_row');
        var $edit_content = $edit_row.find('.pswp_edit_row_content');

        if ($edit_row.is(':visible')) {
            $edit_row.slideUp(100, function () {
                $edit_content.html('');
            });
        } else {
            $edit_row.slideDown(200);
            $edit_content.addClass('pst-loading');

            var id_block = $edit_row.data('id-block');
            $('#prestawp_block-list-wrp').find('.pswp-edit-wrp').slideUp(100);

            $.ajax({
                url: pswp_ajax_url,
                data: {ajax: 1, action: 'getBlockForm', id_prestawp_block: id_block},
                method: 'post',
                success: function (data) {
                    // remove the form tag because we're already in the form block
                    var $tmp = $('<div/>');
                    $tmp.html(data);
                    var html = $tmp.find('form').html();
                    $edit_content.html(html);
                    pswp_initColorPicker();
                    pswp_displaySelectedCatsInfo($edit_content);
                },
                complete: function () {
                    $edit_content.removeClass('pst-loading');
                }
            });
        }
    });

    $(document).on('click', '[name=pswp_block_submit]', function (e) {
        e.preventDefault();

        var id_block = 0;
        var $edit_row = $(this).parents('.pswp_edit_row:first');

        if ($edit_row.length) {
            id_block = $edit_row.data('id-block');
        } else {
            $edit_row = $(this).closest('form');
        }

        var data = 'ajax=1&action=saveBlock';
        $edit_row.find('input, select').each(function () {
            if ($(this).is(':checkbox') || $(this).is(':radio')) {
                if ($(this).is(':checked')) {
                    data += '&' + $(this).attr('name') + '=' + $(this).val();
                }
            } else {
                var val = $(this).val();
                val = (val === null ? '' : val);
                data += '&' + $(this).attr('name') + '=' + val;
            }
        });
        
        if (id_block) {
            data += '&id_prestawp_block=' + id_block;
        }

        $.ajax({
            url: pswp_ajax_url,
            data: data,
            method: 'post',
            success: function (data) {
                if (data == '1') {
                    pswp_reloadBlocks();
                } else {
                    $edit_row.find('.pswp-errors-wrp').html(data);
                }
            }
        });
    });
    
    $(document).on('change', '.wp_categories_input', function (e) {
        var cats = $(this).val();
        var $this = $(this);
        var $posts_select;
        if ($('#module_prestawp').length) {
            $posts_select = $('#module_prestawp').find('.wp_posts_input');
        } else {
            $posts_select = $this.parents('.form-group:first').find('.wp_posts_input');
        }
        $posts_select.addClass('loading').html('');

        $.ajax({
            url: pswp_ajax_url,
            data: {ajax: true, action: 'getWpPostsOptions', cats: cats},
            dataType: 'json',
            method: 'post',
            success: function (data) {
                $posts_select.addClass('loading').html('');
                $.each(data, function (index, option) {
                    $posts_select.append('<option value="' + option['id_option'] + '">' + option['name'] + '</option>');
                });
                pswp_displaySelectedCatsInfo();
                $posts_select.removeClass('loading');
            }
        });
    });

    $('#pswp-compose-btn').on('click', function (e) {
        e.preventDefault();

        $('#pswp-compose-wrp').slideToggle(200);
    });

    $('#pswp-compose-wrp').on('click', '.pswp_cpp_close', function (e) {
        e.preventDefault();

        $('#pswp-compose-wrp').slideUp(100);
    });

    $('#pswp-compose-wrp :input').on('change keyup', function () {
        var $parent = $('#pswp-compose-wrp');
        pswp_refreshHookCode($parent);
    });

    $(document).on('click', '#pswp-product-unselect', function (e) {
        e.preventDefault();

        var $selects = $('#module_prestawp').find('select');
        $selects.val('');
        $selects.find('option').prop('selected', false);
    });

    pswp_updateRegularOptionsDisplay(0);
    let regular_options_query_list = [];
    let regular_options = pswp_getRegularDynamicOptionsData();
    $.each(regular_options, function (option_name) {
        regular_options_query_list.push('[name="' + option_name + '"]');
    });
    $(document).on('change', regular_options_query_list.join(), function (e) {
        pswp_updateRegularOptionsDisplay(100);
    });

    // Show / hide category filter
    $(document).on('click', '.pswp-toggle-category-filter', function () {
        $(this).siblings('.pswp-category-wrp').slideToggle(200);
    });

    $(document).on('keyup', '.pswp-post-search', function () {
        let val = $(this).val();

        let $items = $(this).parent().find('.pswp_post_select option, .pswp_post_selected option');
        if (val) {
            $items.filter(function () {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(val) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        } else {
            $items.show();
        }
    });

    // Add selected posts
    $(document).on('click', '.pswp_multiple_select_add', function (e) {
        e.preventDefault();

        var $parent = $(this).parents('.pswp-select-wrp:first');
        pswp_addSelectPosts($parent);
    });
    $(document).on('dblclick', '.pswp_post_select', 'option', function(){
        var $parent = $(this).parents('.pswp-select-wrp:first');
        pswp_addSelectPosts($parent);
    });

    // Remove selected products
    $(document).on('click', '.pswp_multiple_select_del', function (e) {
        e.preventDefault();

        var $parent = $(this).closest('.pswp-select-wrp');
        pswp_removeSelectPosts($parent);
    });
    $(document).on('dblclick', '.pswp_post_selected', 'option', function(){
        var $parent = $(this).closest('.pswp-select-wrp');
        pswp_removeSelectPosts($parent);
    });

    $(document).on('click', '.pswp-multiple-select-all, .pswp-multiple-remove-all', function (e) {
        e.preventDefault();

        let $parent = $(this).closest('.pswp-select-wrp');
        let $select = $parent.find('.pswp_post_select');
        let $selected = $parent.find('.pswp_post_selected');
        let $hidden_data_wrp = $parent.find('.pswp-selected-posts-data');
        let hidden_data_name = $hidden_data_wrp.data('name');

        $select.find('option').prop('disabled', false);
        $selected.find('option').remove();
        $hidden_data_wrp.html('<input type="hidden" name="' + hidden_data_name + '">');

        pswp_updateOptionAllPosts($parent);
    });
});

function pswp_refreshHookCode($parent) {
    var category_ids = $parent.find('.wp_categories_input:first').val();
    category_ids = (category_ids && typeof category_ids === 'object' ? category_ids.join(',') : '');

    var post_ids = $parent.find('.wp_posts_input:first').val();
    post_ids = (post_ids && typeof post_ids === 'object' ? post_ids.join(',') : '');

    var limit = parseInt($parent.find('.wp_limit_input').val());
    var columns = parseInt($parent.find('.wp_columns_input').val());
    var $result = $parent.find('.pswp_cpp_code');
    var code = '{hook h="PSWPposts"';

    if (category_ids && !post_ids) {
        code += ' category_ids="' + category_ids + '"';
    } else if (post_ids) {
        code += ' ids="' + post_ids + '"';
    }

    code += (!isNaN(limit) ? ' limit="' + limit + '"' : '');
    code += (!isNaN(columns) ? ' columns="' + columns + '"' : '');

    code += '}';
    $result.val(code);
}

function pswp_openTab($elem, tab_id) {
    $('.pst-tabs-list a').removeClass('active');
    $elem.addClass('active');
    $('.pst-tab-content').hide();
    $(tab_id).fadeIn(200);
}

function pswp_reloadBlocks() {
    $.ajax({
        url: pswp_ajax_url,
        method: 'post',
        data: {ajax: true, action: 'getBlockList'},
        beforeSend: function () {
            $('#prestawp_block-wrp').addClass('pst-loading');
        },
        success: function (html) {
            $('#prestawp_block-wrp').replaceWith(html);
            pswp_initColorPicker();
        },
        complete: function () {
            $('#prestawp_block-wrp').removeClass('pst-loading');
        }
    });
}

function pswp_initColorPicker() {
    $('.pswpColorPickerInput').each(function () {
        if (!$(this).hasClass('spectrumed')) {
            $(this).spectrum({
                preferredFormat: "rgb",
                showAlpha: true,
                allowEmpty: true,
                showInput: true
            });
            $(this).addClass('spectrumed');
        }
    });
}

function pswp_updateExtraOptionsDisplay(delay) {
    var options = {
        'SHOW_FEATURED_IMAGE': {
            'show': ['SHOW_PREVIEW', 'SHOW_PREVIEW_NO_IMG'],
            'hide': ['SHOW_FULL_POSTS', 'POSTS_STRIP_TAGS']
        },
        'SHOW_FEATURED_IMAGE_PRODUCT': {
            'show': ['SHOW_PREVIEW_PRODUCT', 'SHOW_PREVIEW_NO_IMG_PRODUCT'],
            'hide': ['SHOW_FULL_POSTS_PRODUCT', 'POSTS_STRIP_TAGS_PRODUCT']
        },
        'SHOW_FEATURED_IMAGE_PAGE': {
            'show': ['SHOW_PREVIEW_PAGE', 'SHOW_PREVIEW_NO_IMG_PAGE'],
            'hide': ['SHOW_FULL_POSTS_PAGE', 'POSTS_STRIP_TAGS_PAGE']
        },
        'show_featured_image': {
            'show': ['show_preview', 'show_preview_no_img'],
            'hide': ['show_full_posts', 'strip_tags']
        },
        'CAROUSEL': {
            'show': ['CAROUSEL_AUTOPLAY', 'CAROUSEL_DOTS', 'CAROUSEL_ARROWS'],
            'hide': []
        },
        'CAROUSEL_PRODUCT': {
            'show': ['CAROUSEL_AUTOPLAY_PRODUCT', 'CAROUSEL_DOTS_PRODUCT', 'CAROUSEL_ARROWS_PRODUCT'],
            'hide': []
        },
        'carousel': {
            'show': ['carousel_autoplay', 'carousel_dots', 'carousel_arrows'],
            'hide': []
        }
    };

    var fg_selector = '.form-group';
    $.each(options, function(index, value) {
        var $options = $('[name=' + index + ']:checked');

        $options.each(function() {
            var $option = $(this);
            var $parent = $option.closest('form');

            if ($parent.find('.pswp_hide_more_options').is(':visible')) {
                var show = [];
                var hide = [];
                $.each(options[index]['show'], function(i, v) {
                    show.push('[name=' + v + ']');
                });
                $.each(options[index]['hide'], function(i, v) {
                    hide.push('[name=' + v + ']');
                });
                
                if ($option.val() === '1') {
                    $parent.find(show.join()).closest(fg_selector).slideDown(delay);
                    $parent.find(hide.join()).closest(fg_selector).slideUp(delay);
                } else {
                    $parent.find(show.join()).closest(fg_selector).slideUp(delay);
                    $parent.find(hide.join()).closest(fg_selector).slideDown(delay);

                    // custom exception for option "truncate"
                    if (hide.join().indexOf('=truncate]') !== -1) {
                        if ($parent.find('[name=strip_tags]:checked').val() === '1') {
                            $parent.find('[name=truncate]').closest(fg_selector).slideDown(delay);
                        } else {
                            $parent.find('[name=truncate]').closest(fg_selector).slideUp(delay);
                        }
                    }
                }
            }
        });
    });
}

function pswp_updateCurlOptionsDisplay(delay) {
    var $option = $('[name=CONNECTION_METHOD]:first');
    var $parent = $option.closest('form');

    if ($option.val() === 'curl') {
        $parent.find('.pswp_curl_options_row').slideDown(delay);
    } else {
        $parent.find('.pswp_curl_options_row').slideUp(delay);
    }
}

function pswp_updateViewPsOptionsDisplay(delay) {
    var $option = $('[name=VIEW_IN_PS]:checked:first');
    var $parent = $option.closest('form');

    if ($option.val() === '1') {
        $parent.find('.pswp_view_ps_options_row').slideDown(delay);
    } else {
        $parent.find('.pswp_view_ps_options_row').slideUp(delay);
    }
}

function pswp_getRegularDynamicOptionsData() {
    return {
        'CONNECTION_METHOD': {
            'check_value': 'curl',
            'show': ['SKIP_SSL_CHECK'],
            'hide': []
        },
        'VIEW_IN_PS': {
            'show': ['DISABLE_INDEXATION', 'PS_SHOW_COMMENTS', 'PS_ALLOW_COMMENTING'],
            'hide': []
        },
        'PS_SHOW_COMMENTS': {
            'show': ['PS_ALLOW_COMMENTING'],
            'hide': [],
            'depends': ['VIEW_IN_PS']
        },
    };
}
function pswp_updateRegularOptionsDisplay(delay) {
    let options = pswp_getRegularDynamicOptionsData();

    let fg_selector = '.form-group';
    $.each(options, function(index, value) {
        let $options = $('[name=' + index + ']:checked, select[name=' + index + '] option:checked');

        $options.each(function() {
            let $option = $(this);
            let $parent = $option.closest('form');

            let show_selectors = [];
            let hide_selectors = [];
            $.each(options[index]['show'], function(i, v) {
                show_selectors.push('[name=' + v + ']');
            });
            $.each(options[index]['hide'], function(i, v) {
                hide_selectors.push('[name=' + v + ']');
            });

            let check_value = (value.check_value ? value.check_value : '1');
            if ($option.val() === check_value) {
                // check dependencies:
                let show = true;
                if (value.depends) {
                    $.each(value.depends, function(index, d_name) {
                        if ($('[name="' + d_name + '"]:checked').val() === '0') {
                            show = false;
                        }
                    });
                }
                if (show) {
                    $parent.find(show_selectors.join()).closest(fg_selector).slideDown(delay);
                    $parent.find(hide_selectors.join()).closest(fg_selector).slideUp(delay);
                }
            } else {
                $parent.find(show_selectors.join()).closest(fg_selector).slideUp(delay);
                $parent.find(hide_selectors.join()).closest(fg_selector).slideDown(delay);
            }
        });
    });
}

function pswp_addSelectPosts($parent) {
    let $options = $parent.find('.pswp_post_select option:selected');
    let $select = $parent.find('.pswp_post_selected');
    let $hidden_data_wrp = $parent.find('.pswp-selected-posts-data');
    let hidden_data_name = $hidden_data_wrp.data('name');

    let html = '';
    let hidden_html = '';
    $options.each(function () {
        let id = $(this).val();
        let name = $(this).text();
        // if it's the "All" option:
        if (!id) {
            // remove all other options
            $select.find('option').each(function() {
                $(this).prop('selected', true);
                pswp_removeSelectPosts($select.closest('.pswp-select-wrp'));
            });
        }
        if (!$select.find('option[value="' + id + '"]').length) {
            html += '<option value="' + id + '">' + name + '</option>';
            hidden_html += '<input type="hidden" name="' + hidden_data_name + '" value="' + id + '">';
            $(this).prop('disabled', true);
        }
    });

    $select.append(html);
    $hidden_data_wrp.append(hidden_html);
    // append default option if nothing selected
    if (!$hidden_data_wrp.find('input').length) {
        $hidden_data_wrp.html('<input type="hidden" name="' + hidden_data_name + '">');
    }

    pswp_updateOptionAllPosts($parent);
}

function pswp_removeSelectPosts($parent) {
    let $options = $parent.find('.pswp_post_selected option:selected');
    let $hidden_data_wrp = $parent.find('.pswp-selected-posts-data');
    let hidden_data_name = $hidden_data_wrp.data('name');

    $options.each(function () {
        let val = $(this).val();
        $(this).remove();
        $hidden_data_wrp.find('input[value="' + val + '"]').remove();
        $parent.find('.pswp_post_select option[value="' + val + '"]').prop('disabled', false)
    });

    if (!$hidden_data_wrp.find('input').length) {
        $hidden_data_wrp.html('<input type="hidden" name="' + hidden_data_name + '">');
    }

    pswp_updateOptionAllPosts($parent);
}

function pswp_displaySelectedCatsInfo($select_wrp) {
    if (!$select_wrp) {
        $select_wrp = $('.pswp-select-wrp');
    }

    $select_wrp.each(function() {
        let selected_names = pswp_getSelectedCategoriesTxt($(this));
        if (selected_names) {
            $(this).find('.pswp-category-filter-names').text(selected_names);
        }

        pswp_updateOptionAllPosts($(this));
    });
}

function pswp_updateOptionAllPosts($select_wrp) {
    if (!$select_wrp) {
        $select_wrp = $('.pswp-select-wrp');
    }

    let is_bo_product_page = $('#module_prestawp').length;

    $select_wrp.each(function() {
        let selected_cats_txt = pswp_getSelectedCategoriesTxt($(this));

        let $select = $select_wrp.find('.pswp_post_select');
        let $selected = $select_wrp.find('.pswp_post_selected');
        let $hidden_data_wrp = $select_wrp.find('.pswp-selected-posts-data');
        // remove the "All" option to update it
        $select.find('option').filter(function() {
            return !$(this).attr('value') || $(this).val().trim() === '';
        }).remove();
        $selected.find('option').filter(function() {
            return !$(this).attr('value') || $(this).val().trim() === '';
        }).remove();
        $hidden_data_wrp.find('input').filter(function() {
            return !$(this).attr('value') || $(this).val().trim() === '';
        }).remove();
        let hidden_data_name = $hidden_data_wrp.data('name');

        if (!is_bo_product_page || (is_bo_product_page && selected_cats_txt)) {
            let option = '<option value="">-- ' + pswp_default_option_name + selected_cats_txt + ' --</option>';
            // update the "All" option in the left select box with category names:
            $select.prepend(option);
        }

        // add the "All" option in the right select box only if there are no selected posts:
        if ((!is_bo_product_page || (is_bo_product_page && selected_cats_txt)) && !$selected.find('option').length) {
            option = '<option value="" selected>-- ' + pswp_default_option_name + selected_cats_txt + ' --</option>';
            // add the "All" option again
            $selected.append(option);
            $hidden_data_wrp.html('<input type="hidden" name="' + hidden_data_name + '">');
            $(this).find('.pswp-multiple-select-all, .pswp-multiple-remove-all').addClass('inactive');
        } else {
            $(this).find('.pswp-multiple-select-all, .pswp-multiple-remove-all').removeClass('inactive');
        }
    });
}

function pswp_getSelectedCategoriesTxt($select_wrp) {
    let selected_names_txt = '';
    let is_bo_product_page = $('#module_prestawp').length;

    let $selected_cat_elems = $select_wrp.find('.wp_categories_input option:selected');

    if ($selected_cat_elems.length) {
        let selected_names = [];
        $selected_cat_elems.each(function() {
            if (!is_bo_product_page || $(this).val()) {
                selected_names.push($.trim($(this).text()));
            }
        });

        selected_names_txt = (selected_names.length ? ' - ' + selected_names.join(', ') : '');
    }

    return selected_names_txt;
}