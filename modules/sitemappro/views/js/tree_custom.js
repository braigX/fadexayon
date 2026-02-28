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

function TreeCustom(tree_selector, tree_header_selector)
{
    this.ts = $(tree_selector);
    this.th = $(tree_header_selector);
    this.cache_selected_categories = null;
    this.updateCounter = function () {
        this.ts.find('ul').each(function() {
            var all_child_selected = $(this).find('span.tree_selected').length;
            $(this).closest('.tree_item').find('> .tree_label .tree_counter').text(all_child_selected ? '('+all_child_selected+')' : '');
        });
    };
    this.clearCache = function () {
       this.cache_selected_categories = null;
    };
    this.afterChange = function() {};
    this.onUnSelect = function(elem_input, id_item) {};
    this.onSelect = function(elem_input, id_item) {};
    this.serialize = function()
    {
        return this.ts.find(':input').serialize();
    };
    this.getListSelectedCategories = function()
    {
        if (this.cache_selected_categories == null) {
            var categories = [];
            this.ts.find('.tree_input:checked').each(function () {
                categories.push({
                    id: parseInt($(this).val()),
                    name: $(this).data('name')
                });
            });
            this.cache_selected_categories = categories;
        }

        return this.cache_selected_categories;
    };
    this.getidsSelectedCategories = function()
    {
        var categories = [];
        $.each(this.getListSelectedCategories(), function (index, category) {
            categories.push(category.id);
        });
        return categories;
    };
    this.collapseAll = function ()
    {
        this.ts.find('ul').each(function () {
            var tree_item = $(this).closest('.tree_item');
            tree_item.find('> ul').removeClass('tree_open').addClass('tree_close');
            if (!tree_item.find('> .tree_label i.tree-dot').length)
                tree_item.find('> .tree_label i').attr('class', 'icon-folder-close');
        });
        this.th.find('.collapse_all').hide();
        this.th.find('.expand_all').show();
    };
    this.expandAll = function ()
    {
        this.ts.find('ul').each(function () {
            var tree_item = $(this).closest('.tree_item');
            tree_item.find('> ul').removeClass('tree_close').addClass('tree_open');
            if (!tree_item.find('> .tree_label i.tree-dot').length)
                tree_item.find('> .tree_label i').attr('class', 'icon-folder-open');
        });
        this.th.find('.expand_all').hide();
        this.th.find('.collapse_all').show();
    };
    this.uncheckAllAssociatedCategories = function ()
    {
        this.ts.find('.tree_label.tree_selected').each(function () {
            $(this).removeClass('tree_selected').find('input[type=checkbox]').prop('checked', false);
            $(this).removeClass('tree_selected').find('input[type=checkbox]').trigger('change');
        });
        this.updateCounter();
        this.clearCache();
        this.afterChange();
    };
    this.checkAllAssociatedCategories = function ()
    {
        this.ts.find('.tree_label').not('.tree_selected').each(function () {
            $(this).addClass('tree_selected').find('input[type=checkbox]').prop('checked', true);
            $(this).removeClass('tree_selected').find('input[type=checkbox]').trigger('change');
        });
        this.expandAll();
        this.updateCounter();
        this.clearCache();
        this.afterChange();
    };
    this.checkAssociatedCategory = function (id)
    {
        if (!id) return false;
        var input = this.ts.find('.tree_input[value='+id+']');
        if (input.is(':checked'))
            return false;
        input.prop('checked', true);
        // input.closest('.tree_label').addClass('.tree_selected');
        // input.closest('.tree_item').find('> ul').removeClass('tree_close').addClass('tree_open');
        input.trigger('change');
        return true;
    };
    this.search = function (string) {
        var query = String(string.replace(/[^a-zA-Zа-яА-Я0-9 ]+/, '')).toLowerCase();
        var elements = [];

        this.ts.find('.tree_input[data-name]').not(':checked').each(function () {
            var name = String($(this).data('name')).toLowerCase();
            if (name.indexOf(query) != -1) {
                elements.push($(this));
            }
        });

        if (!elements.length)
            return '';
        var html_response = '';
        $.each(elements, function (key, item) {
            html_response += '<a href="#" data-id="'+item.val()+'" class="select_search">'+item.data('name')+'</a>';
        });
        return '<div class="result_search">'+html_response+'</div>';
    };
    this.init = function()
    {
        var self = this;
        this.th.find('.collapse_all').unbind('click').click(function (e) {
            e.preventDefault();
            self.collapseAll();
        });
        this.th.find('.expand_all').unbind('click').click(function (e) {
            e.preventDefault();
            self.expandAll();
        });
        this.th.find('.check_all_associated_categories').unbind('click').click(function (e) {
            e.preventDefault();
            self.checkAllAssociatedCategories();
        });
        this.th.find('.uncheck_all_associated_categories').unbind('click').click(function (e) {
            e.preventDefault();
            self.uncheckAllAssociatedCategories();
        });
        this.th.find('.search_category').unbind('keyup').keyup(function () {
            var html = self.search($(this).val());
            $(this).parent().find('.result_search').remove();
            $(this).parent().append(html);
        });
        this.th.find('.select_search').unbind('click').live('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (self.checkAssociatedCategory(id))
            {
                $(this).closest('.result_search').remove();
                self.th.find('.search_category').val('');
            }
        });
        this.ts.find('ul').each(function() {
            var length_child_selected = $(this).find('> li > span.tree_selected').length;
            if (length_child_selected)
            {
                $(this).addClass('tree_open');
                $(this).closest('.tree_item').find('> .tree_label i:not(.tree-dot)').attr('class', 'icon-folder-open');
            }
            else
                $(this).addClass('tree_close');
            var all_child_selected = $(this).find('span.tree_selected').length;
            $(this).closest('.tree_item').find('> .tree_label .tree_counter').text(all_child_selected ? '('+all_child_selected+')' : '');
        });
        this.ts.find('.tree_toogle').unbind('click').click(function () {
            var tree_item = $(this).closest('.tree_item');
            var child_list = tree_item.find('> ul');
            if (child_list.is('.tree_open'))
            {
                child_list.removeClass('tree_open').addClass('tree_close');
                if (!$(this).find('i.tree-dot').length)
                    $(this).find('i').attr('class', 'icon-folder-close');
            }
            else
            {
                child_list.removeClass('tree_close').addClass('tree_open');
                if (!$(this).find('i.tree-dot').length)
                    $(this).find('i').attr('class', 'icon-folder-open');
            }
        });
        this.ts.find('.tree_input').unbind('change').change(function () {
            tree_item = null;
            if ($(this).is(':checked'))
            {
                var tree_item = $(this).closest('.tree_item');
                tree_item.find('> .tree_label').addClass('tree_selected');
                if (!tree_item.find('> .tree_label span i.tree-dot').length)
                    tree_item.find('> .tree_label span i').attr('class', 'icon-folder-open');
                var child_list = tree_item.find('> ul');
                child_list.removeClass('tree_close').addClass('tree_open');
                self.onSelect($(this), $(this).val());
            }
            else
            {
                var tree_item = $(this).closest('.tree_item');
                tree_item.find('> .tree_label').removeClass('tree_selected');
                self.onUnSelect($(this), $(this).val());
            }
            self.updateCounter();
            self.clearCache();
            self.afterChange();
        });
    }
}