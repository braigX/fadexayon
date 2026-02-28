/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    SeoSA    <885588@bk.ru>
 * @copyright 2012-2022 SeoSA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

var tab_container = null;
var tree_custom = null;
var object_template = null;
var ajax = null;

function needSave()
{
    $('.need_save').show();
}

function hideTable()
{
    if (!$('.sitemap_categories tbody tr').length) {
        $('.sitemap_categories').hide();
    }
    if (!$('.sitemap_products tbody tr').length) {
        $('.sitemap_products').hide();
    }
    if (!$('.sitemap_manufacturer tbody tr').length) {
        $('.sitemap_manufacturer').hide();
    }
    if (!$('.sitemap_supplier tbody tr').length) {
        $('.sitemap_supplier').hide();
    }
}

$(function () {
    object_template = _.template($('#tpl_object_item').html());
    $('[name=search_product]').keyup(function () {
        var search_query = $(this).val();
        var that = this;
        $(that).parent().find('.result_search').remove();
        var exclude_ids = [];
        $('[data-id-product]').each(function () {
            exclude_ids.push($(this).data('id-product'));
        });
        if (ajax != null)
            ajax.abort();
        ajax = $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'search_product',
                categories: tree_custom.getidsSelectedCategories(),
                search_query: search_query,
                exclude_ids: exclude_ids
            },
            success: function (r)
            {
                var result = $('<div class="result_search"></div>');
                for (i in r)
                {
                    result.append('<a data-categories="'+r[i].categories+'" data-id="'+r[i].id_product+'" class="select_product">'+r[i].name+'</a>');
                }
                if (r.length)
                    $(that).after(result);
            }
        });
    });
    $('.select_product').live('click', function () {
        $('.sitemap_products').show();
        var sitemap_products = $('.sitemap_products tbody');
        sitemap_products.append(object_template({
            type_object: 'product',
            id_object: $(this).data('id'),
            categories: $(this).data('categories'),
            name: $(this).text(),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: (typeof conf_products[$(this).data('id')] != 'undefined' ? conf_products[$(this).data('id')].priority : 0.5),
            changefreq_object: (typeof conf_products[$(this).data('id')] != 'undefined' ? conf_products[$(this).data('id')].changefreq : 'always')
        }));
        $('[name=search_product]').val('');
        $(this).parent().remove();
        needSave();
    });
    $.each(conf_categories, function (index, value) {
        $('.sitemap_categories').show();
        var sitemap_categories = $('.sitemap_categories tbody');
        sitemap_categories.append(object_template({
            type_object: 'category',
            id_object: value.id_category,
            name: value.name,
            is_export: (
                typeof conf_categories[value.id_category] != 'undefined'
                ? parseInt(conf_categories[value.id_category].is_export) : 1
            ),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: value.priority,
            changefreq_object: value.changefreq
        }));
    });

    $.each(conf_products, function (index, value) {
        var sitemap_products = $('.sitemap_products tbody');
        sitemap_products.append(object_template({
            type_object: 'product',
            id_object: value.id_product,
            categories: value.categories,
            name: value.name,
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: value.priority,
            changefreq_object: value.changefreq
        }));
    });
    // $.each(cms, function (index, value) {
    //     var sitemap_cms = $('.sitemap_cms tbody');
    //     var cms_page = (typeof conf_cms[value.id_cms] != 'undefined' ? conf_cms[value.id_cms] : false);
    //     sitemap_cms.append(object_template({
    //         type_object: 'cms',
    //         id_object: value.id_cms,
    //         name: value.meta_title,
    //         is_export: (typeof conf_cms[value.id_cms] != 'undefined' ? parseInt(conf_cms[value.id_cms].is_export) : 1),
    //         changefreqs: changefreqs,
    //         priorities: priorities,
    //         priority_object: (cms_page ? cms_page.priority : 0.5),
    //         changefreq_object: (cms_page ? cms_page.changefreq : 'always')
    //     }));
    // });
    $.each(conf_manufacturers, function (index, value) {
        var sitemap_manufacturer = $('.sitemap_manufacturer tbody');
        var manufacturer = false;
        $.each(conf_manufacturers, function (key, m) {
            if (value.id_manufacturer == m.id_manufacturer) {
                manufacturer = m;
            }
        });
        $('.sitemap_manufacturer').show();
        sitemap_manufacturer.append(object_template({
            type_object: 'manufacturer',
            id_object: value.id_manufacturer,
            name: value.name,
            is_export: (manufacturer ? parseInt(manufacturer.is_export) : 1),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: (manufacturer ? manufacturer.priority : 0.5),
            changefreq_object: (manufacturer ? manufacturer.changefreq : 'always')
        }));
    });
    $.each(conf_suppliers, function (index, value) {
        var sitemap_supplier = $('.sitemap_supplier tbody');
        var supplier = false;
        $.each(conf_suppliers, function (key, s) {
            if (value.id_supplier == s.id_supplier) {
                supplier = s;
            }
        });
        $('.sitemap_supplier').show();
        sitemap_supplier.append(object_template({
            type_object: 'supplier',
            id_object: value.id_supplier,
            name: value.name,
            is_export: (supplier ? parseInt(supplier.is_export) : 1),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: (supplier ? supplier.priority : 0.5),
            changefreq_object: (supplier ? supplier.changefreq : 'always')
        }));
    });
    $.each(meta, function (index, value) {
        var sitemap_meta = $('.sitemap_meta tbody');
        var meta_page = (typeof conf_meta[value.id_meta] != 'undefined' ? conf_meta[value.id_meta] : false);
        sitemap_meta.append(object_template({
            type_object: 'meta',
            id_object: value.id_meta,
            name: value.page,
            is_export: (typeof conf_meta[value.id_meta] != 'undefined' ? parseInt(conf_meta[value.id_meta].is_export) : 1),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: (meta_page ? meta_page.priority : 0.5),
            changefreq_object: (meta_page ? meta_page.changefreq : 'always')
        }));
    });
    tab_container = $('.tab_container').tabContainer();

    tree_custom = new TreeCustom('.tree_custom .tree_categories', '.tree_custom .tree_categories_header');

    //var ajax_after_change = null;
    var time_out_after_change = null;
    tree_custom.afterChange = function (init) {
        var self = this;
        if (time_out_after_change != null) {
            clearTimeout(time_out_after_change);
            time_out_after_change = null;
        }

        time_out_after_change = setTimeout(function () {
            //var sitemap_categories = $('.sitemap_categories tbody');
            var product_selected_categories = $('.product_selected_categories');
            product_selected_categories.html('');
            $('.product_selected_categories_block').hide();
            //sitemap_categories.html('');
            var categories = self.getListSelectedCategories();
            // if (ajax_after_change != null) {
            //     ajax_after_change.abort();
            //     ajax_after_change = null;
            // }
            // ajax_after_change = $.ajax({
            //     url: url,
            //     type: 'POST',
            //     dataType: 'json',
            //     data: {
            //         ajax: true,
            //         action: 'products_selected_categories',
            //         categories: categories,
            //     },
            //     success: function (r)
            //     {
            //         $('#product_excluded').html('');
            //         for (i in r)
            //         {
            //             var selected = '';
            //             if (excluded_products.includes(r[i].id_product))
            //             {
            //                 selected = ' selected';
            //             }
            //             $('#product_excluded').append('<option value="'+r[i].id_product+'"'+selected+'>'+r[i].name+'</option>');
            //         }
            //     }
            // });
            _.each(categories, function (category)
            {
                product_selected_categories.append('<div class="product_selected_category">'+category.name+'</div>');
                $('.product_selected_categories_block').show();
            });
        }, 200);
    };
    tree_custom.onUnSelect = function (elem_input, id_category) {
        $('.sitemap_products [data-categories]').each(function () {
            var categories = $(this).data('categories').toString().split(',');
            var choice = false;
            if (categories.length) {
                $.each(categories, function (index, value) {
                    if (value == id_category) {
                        choice = true;
                    }
                });
            }
            if (choice) {
                $(this).remove();
            }
        });
    };

    tree_custom.oldInit = tree_custom.init;
    tree_custom.init = function () {
        this.oldInit();
        this.afterChange();
    };
    tree_custom.init();

    $('#product_excluded').select2({
        ajax: {
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: true,
                    action: 'products_selected_categories',
                    categories: tree_custom.getListSelectedCategories()
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        }
    });

    $('.object_item_priority select, .object_item_changefreq select, .tree_input, .object_item_action input[type=checkbox]').live('change', needSave);
    $('.saveSiteMapConf').live('click', function () {
        $('.loader').removeClass('hidden');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                sitemap: $('[name^=sitemap]:input, [name^=sitemap_excluded]:input, [name^="user_links["]:input, [name^="default_settings["]:input, [name^="categories["]:input').serialize(),
                action: 'save_conf'
            },
            success: function (r)
            {
                $('.loader').addClass('hidden')
                if (r.hasError)
                    MessageViewer.showError(r.message);
                else
                {
                    MessageViewer.showSuccess(r.message);
                    
                    $.each(r.user_links, function (index, link) {
                        $('[name="user_links['+index+'][id_user_link]"]').val(link.id_user_link);
                    });
                    
                    $('.need_save').hide();
                }
            },
            error: function () {
                MessageViewer.showError('Has error');
            }
        });
    });
    $('[data-sitemap-link]').click(function () {

        var self = $(this);
        var old_text = self.val();
        self.attr('disabled', true);
        self.val(please_wait);
        $.ajax({
            url: $(this).data('sitemap-link') + ($(this).data('sitemap-link').indexOf('?') != -1 ? '&' : '?') +'ajax=true',
            type: 'POST',
            dataType: 'json',
            data: {
                ajax: true,
                action: 'generate_site_map',
                id_shop: id_shop
            },
            success: function (r) {
                self.removeAttr('disabled');
                self.val(old_text);
                if (!r.hasError)
                {
                    $('[data-info-sitemap-link="'+self.data('sitemap-link')+'"]').html($('#info_sitemap_link').html()
                        .split('%link%').join(r.link)
                        .split('%date%').join(r.date));
                    
                    $pages = '';
                    if (r.pages.length) {
                        var $pages = $('<ul></ul>');
                        $.each(r.pages, function (index, page) {
                            $pages.append('<li><a target="_blank" href="'+page+'">'+page+'</a></li>');
                        });
                    }

                    $('[data-pages="'+self.data('sitemap-link')+'"]').html($pages);
                    
                    MessageViewer.showSuccess(r.message);
                }
                else
                    MessageViewer.showError(r.message);
            },
            error: function ()
            {
                self.removeAttr('disabled');
                self.val(old_text);

                $('.tn-box_sitemap_not_created.mv_error').addClass('tn-box-active');
                setTimeout(function() {
                    $('.stage_mv.mv_error').fadeOut(300);
                    $('.tn-box_sitemap_not_created').removeClass('tn-box-active');
                }, 5000);
            }
        });
    });

    var protocol = null;
    $('[name="type"]').live('change', function () {
        if (protocol != null)
            protocol.abort();
        protocol = saveConfig('protocol', $('[name="type"]:checked').val());
    });

    var ITEM_PER_SITEMAP = null;
    $('[name="ITEM_PER_SITEMAP"]').keyup(function () {
        if (ITEM_PER_SITEMAP != null)
            ITEM_PER_SITEMAP.abort();
        ITEM_PER_SITEMAP = saveConfig('ITEM_PER_SITEMAP', $(this).val());
    });

    var SYMBOL_LEGEND = null;
    $('[name="SYMBOL_LEGEND"]').keyup(function () {
        if (SYMBOL_LEGEND != null)
            SYMBOL_LEGEND.abort();
        SYMBOL_LEGEND = saveConfig('SYMBOL_LEGEND', $(this).val());
    });

    var ALLOW_IMAGE_CAPTION_ATTR = null;
    $('[name="ALLOW_IMAGE_CAPTION_ATTR"]').change(function () {
        if (ALLOW_IMAGE_CAPTION_ATTR != null)
            ALLOW_IMAGE_CAPTION_ATTR.abort();
        ALLOW_IMAGE_CAPTION_ATTR = saveConfig(
            'ALLOW_IMAGE_CAPTION_ATTR',
            $('[name="ALLOW_IMAGE_CAPTION_ATTR"]:checked').val()
        );
    });

    var EXPORT_IN_ROBOTS = null;
    $('[name="EXPORT_IN_ROBOTS"]').change(function () {
        if (EXPORT_IN_ROBOTS != null)
            EXPORT_IN_ROBOTS.abort();
        EXPORT_IN_ROBOTS = saveConfig(
            'EXPORT_IN_ROBOTS',
            $('[name="EXPORT_IN_ROBOTS"]:checked').val()
        );
    });

    var EXPORT_COMBINATION = null;
    $('[name="EXPORT_COMBINATION"]').change(function () {
        if (EXPORT_COMBINATION != null)
            EXPORT_COMBINATION.abort();
        EXPORT_COMBINATION = saveConfig(
            'EXPORT_COMBINATION',
            $('[name="EXPORT_COMBINATION"]:checked').val()
        );
    });

    var EXPORT_CATEGORY_IMAGE = null;
    $('[name="EXPORT_CATEGORY_IMAGE"]').change(function () {
        if (EXPORT_CATEGORY_IMAGE != null)
            EXPORT_CATEGORY_IMAGE.abort();
        EXPORT_CATEGORY_IMAGE = saveConfig(
            'EXPORT_CATEGORY_IMAGE',
            $('[name="EXPORT_CATEGORY_IMAGE"]:checked').val()
        );
    });

    var EXPORT_COMBINATION_DEF = null;
    $('[name="EXPORT_COMBINATION_DEF"]').change(function () {
        if (EXPORT_COMBINATION_DEF != null)
            EXPORT_COMBINATION_DEF.abort();
        EXPORT_COMBINATION_DEF = saveConfig(
            'EXPORT_COMBINATION_DEF',
            $('[name="EXPORT_COMBINATION_DEF"]:checked').val()
        );
    });



    var INCLUDE_ID_IN_ATTRIBUTE = null;
    $('[name="INCLUDE_ID_IN_ATTRIBUTE"]').change(function () {
        if (INCLUDE_ID_IN_ATTRIBUTE != null)
            INCLUDE_ID_IN_ATTRIBUTE.abort();

        if ($('#INCLUDE_ID_IN_ATTRIBUTE_1:checked').val()) {
            $('#EXPORT_COMBINATION_1').click();
            changeExportCombination();
        }

        INCLUDE_ID_IN_ATTRIBUTE = saveConfig(
            'INCLUDE_ID_IN_ATTRIBUTE',
            $('#INCLUDE_ID_IN_ATTRIBUTE_1:checked').val()
        );
    });

    function saveConfig(name, value)
    {
        return $.ajax({
            url: url,
            type: 'POST',
            data: {
                ajax: true,
                action: 'set_config',
                name: name,
                value: value
            }
        });
    }

    var object_template_user_link = _.template($('#tpl_object_item_user_link').html());
    $.each(user_links, function (index, user_link) {
        var sitemap_user_link = $('.sitemap_user_link tbody');
        $('.sitemap_user_link').show();
        sitemap_user_link.append(object_template_user_link({
            user_link: user_link,
            index: sitemap_user_link.find('tr').length + 1,
            changefreqs: changefreqs,
            priorities: priorities,
            languages: languages,
            shop_url: shop_url
        }));
    });

    $('.addUserLink').live('click', function () {
        var user_link = {
            id_user_link: 0,
            priority: 0,
            changefreq: 'always',
            link: {}
        };

        $.each(languages, function (index, lang) {
            user_link.link[lang.id_lang] = '';
        });
        var sitemap_user_link = $('.sitemap_user_link tbody');
        sitemap_user_link.append(object_template_user_link({
            user_link: user_link,
            index: sitemap_user_link.find('tr').length + 1,
            changefreqs: changefreqs,
            priorities: priorities,
            languages: languages,
            shop_url: shop_url
        }));
        $.triggerChangeLang();
        $('.sitemap_user_link').show();
    });

    $('.deleteUserLink').live('click', function (e) {
        e.preventDefault();
        $(this).closest('tr').hide();
        $(this).closest('tr').find('[name^="user_links["][name$="[deleted]"]').val('1');
        needSave();

        if (!$('.sitemap_user_link tbody tr').is(':visible')) {
            $('.sitemap_user_link').hide();
        }
    });

    //Tab manufacturers
    $('[name="search_manufacturer"]').select2({
        ajax: {
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: true,
                    action: 'search_manufacturer',
                    excl_ids: (function () {
                        var excl_ids = [];
                        $('.sitemap_manufacturer tbody tr').each(function () {
                            excl_ids.push($(this).find('td').eq(0).find('[name$="[id_object]"]').val());
                        });
                        return excl_ids;
                    })()
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        }
    });

    $('[data-add-manufacrurer]').live('click', function (e) {
        var $sitemap_manufacturer = $('.sitemap_manufacturer tbody');
        var $search_manufacturer = $('[name="search_manufacturer"]');
        if (!$search_manufacturer.val()) {
            return false;
        }

        var is_export = $('[name="default_settings[manufacturer][is_export]"]').val();
        var priority = $('[name="default_settings[manufacturer][priority]"]').val();
        var changefreq = $('[name="default_settings[manufacturer][changefreq]"]').val();
        $sitemap_manufacturer.append(object_template({
            type_object: 'manufacturer',
            id_object: $search_manufacturer.val(),
            name: $search_manufacturer.find('option:selected').text(),
            is_export: is_export,
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: priority,
            changefreq_object: changefreq
        }));
        needSave();
        $('.sitemap_manufacturer').show();
        $search_manufacturer.val(0);
    });


    $('[data-add-all-manufacrurer]').live('click', function (e) {
        var $sitemap_manufacturer = $('.sitemap_manufacturer tbody');
        var $search_manufacturer = $('[name="search_manufacturer"]');

        $.ajax({
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: {
                search: '',
                ajax: true,
                action: 'search_manufacturer',
                excl_ids: (function () {
                    var excl_ids = [];
                    $('.sitemap_manufacturer tbody tr').each(function () {
                        excl_ids.push($(this).find('td').eq(0).find('[name$="[id_object]"]').val());
                    });
                    return excl_ids;
                })()
            },
            success: function (json) {
                $.each(json.results, function (index, value) {
                    var is_export = $('[name="default_settings[manufacturer][is_export]"]').val();
                    var priority = $('[name="default_settings[manufacturer][priority]"]').val();
                    var changefreq = $('[name="default_settings[manufacturer][changefreq]"]').val();
                    $sitemap_manufacturer.append(object_template({
                        type_object: 'manufacturer',
                        id_object: value.id,
                        name: value.text,
                        is_export: is_export,
                        changefreqs: changefreqs,
                        priorities: priorities,
                        priority_object: priority,
                        changefreq_object: changefreq
                    }));
                });
            }
        });

        needSave();
        $search_manufacturer.val(0);
    });
    //End tab

    //Tab suppliers
    $('[name="search_supplier"]').select2({
        ajax: {
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: true,
                    action: 'search_supplier',
                    excl_ids: (function () {
                        var excl_ids = [];
                        $('.sitemap_supplier tbody tr').each(function () {
                            excl_ids.push($(this).find('td').eq(0).find('[name$="[id_object]"]').val());
                        });
                        return excl_ids;
                    })()
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        }
    });

    $('[data-add-supplier]').live('click', function (e) {
        e.preventDefault();
        var $sitemap_supplier = $('.sitemap_supplier tbody');
        var $search_supplier = $('[name="search_supplier"]');
        if (!$search_supplier.val()) {
            return false;
        }

        var is_export = $('[name="default_settings[supplier][is_export]"]').val();
        var priority = $('[name="default_settings[supplier][priority]"]').val();
        var changefreq = $('[name="default_settings[supplier][changefreq]"]').val();
        $sitemap_supplier.append(object_template({
            type_object: 'supplier',
            id_object: $search_supplier.val(),
            name: $search_supplier.find('option:selected').text(),
            is_export: is_export,
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: priority,
            changefreq_object: changefreq
        }));
        needSave();
        $('.sitemap_supplier').show();
        $search_supplier.val(0);
    });

    $('[data-add-all-supplier]').live('click', function () {
        $.ajax({
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: {
                search: '',
                ajax: true,
                action: 'search_supplier',
                excl_ids: (function () {
                    var excl_ids = [];
                    $('.sitemap_supplier tbody tr').each(function () {
                        excl_ids.push($(this).find('td').eq(0).find('[name$="[id_object]"]').val());
                    });
                    return excl_ids;
                })()
            },
            success: function (json) {
                var $sitemap_supplier = $('.sitemap_supplier tbody');
                var $search_supplier = $('[name="search_supplier"]');

                $.each(json.results, function (index, value) {
                    var is_export = $('[name="default_settings[supplier][is_export]"]').val();
                    var priority = $('[name="default_settings[supplier][priority]"]').val();
                    var changefreq = $('[name="default_settings[supplier][changefreq]"]').val();
                    $sitemap_supplier.append(object_template({
                        type_object: 'supplier',
                        id_object: value.id,
                        name: value.text,
                        is_export: is_export,
                        changefreqs: changefreqs,
                        priorities: priorities,
                        priority_object: priority,
                        changefreq_object: changefreq
                    }));
                });

                needSave();
                $search_supplier.val(0);
            }
        });
    });
    //End tab

    //Tab categories

    $('[name="search_category"]').select2({
        ajax: {
            url: document.location.href.replace('#'+document.location.hash, ''),
            type: 'POST',
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: true,
                    action: 'search_category',
                    excl_ids: (function () {
                        var excl_ids = [];
                        $('.sitemap_categories tbody tr').each(function () {
                            excl_ids.push($(this).find('td').eq(0).find('[name$="[id_object]"]').val());
                        });
                        return excl_ids;
                    })(),
                    selected_categories: (function () {
                        var ids = [];
                        var selected_categories = tree_custom.getListSelectedCategories();
                        $.each(selected_categories, function (key, item) {
                            ids.push(item.id);
                        })
                        return ids;
                    })()
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            }
        }
    });

    $('[data-add-category]').live('click', function (e) {
        e.preventDefault();
        var $sitemap_category = $('.sitemap_categories tbody');
        var $search_category = $('[name="search_category"]');
        if (!$search_category.val()) {
            return false;
        }
        var is_export = $('[name="default_settings[category][is_export]"]').val();
        var priority = $('[name="default_settings[category][priority]"]').val();
        var changefreq = $('[name="default_settings[category][changefreq]"]').val();

        $('.sitemap_categories').show();

        $sitemap_category.append(object_template({
            type_object: 'category',
            id_object: $search_category.val(),
            name: $search_category.find('option:selected').text(),
            changefreqs: changefreqs,
            priorities: priorities,
            priority_object: priority,
            changefreq_object: changefreq,
            is_export: is_export
        }));
        needSave();
        $search_category.val(0);
    });
    //end tab

    $('[name^="default_settings"]').live('change', function () {
        needSave();
    });

    $('[name="protect"]').live('change', function () {
        var input_block = $('#secret').closest('.secret_block');
        if ($(this).val() == 1) {
            input_block.show();
            var secret = $('#secret').val();
            replaceSecret(secret);
        } else {
            input_block.hide();
            replaceSecret('');
        }
        saveConfig('SMP_PROTECT', $(this).val());
    });

    $('#update_secret').live('click', function () {
        var secret = $('#secret').val();
        saveConfig('SMP_SECRET', secret);
        replaceSecret(secret);
    });

    $('[name="protect_file"]').live('change', function () {
        var input_block = $('#secret_file').closest('.secret_file_block');
        if ($(this).val() == 1) {
            input_block.show();
            var secret_file = $('#secret_file').val();
            replaceSecret(secret_file);
        } else {
            input_block.hide();
            replaceSecret('');
        }
        saveConfig('SMP_PROTECT_FILE', $(this).val());
    });

    $('#update_secret_file').live('click', function () {
        var secret_file = $('#secret_file').val();
        saveConfig('SMP_SECRET_FILE', secret_file);
        replaceSecret(secret_file);
    });

    function replaceSecret(secret)
    {
        if (secret) {
            secret = '&secret=' + secret;
        }
        $('[readonly]').each(function () {
            var val = $(this).val();
            $(this).val(val.replace(/&secret=.+'$|'$/g, secret + '\''));
        });
    }

    $.changeLanguage(id_language);

    function changeExportCombination() {
        if ( $("#EXPORT_COMBINATION_1").prop("checked") ) {
            $(".EXPORT_COMBINATION_DEF_BLOCK").addClass("hidden");
        } else {
            $(".EXPORT_COMBINATION_DEF_BLOCK").removeClass("hidden");
        }
    }

    changeExportCombination();

    $("#EXPORT_COMBINATION_0").click( function(){
        changeExportCombination();
    });

    $("#EXPORT_COMBINATION_1").click( function(){
        changeExportCombination();
    });

    $('.copy-btn').on("click", function(){
        $(this).parent('div').find('input').select();
        document.execCommand("copy");
        $('.tn-box_copy').addClass('tn-box-active');
        setTimeout(function() {
            $('.stage_mv.mv_error').fadeOut(300);
            $('.tn-box_copy').removeClass('tn-box-active');
        }, 5000);
    });

    $(".js-scroll").click(function() {

        var elementClick = $(this).attr("href")
        var destination = $(elementClick).offset().top - 110;

        jQuery("html:not(:animated),body:not(:animated)").animate({
            scrollTop: destination
        }, 800);

        return false;
    });

});
