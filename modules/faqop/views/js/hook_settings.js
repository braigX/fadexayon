/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */

function addProductOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getProductNameOp&id=" + id
    })
        .done(function (productName) {
            let element = "#block-for-error-product";
            hideErrorOp(element);
            $('#divProducts_op').append('<div class="divItem_op">' + productName + ' (id: ' + id + ') <span class="delProduct_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#product_ids').val()) {
                $('#product_ids').val(id);
            }
            else {
                $('#product_ids').val($('#product_ids').val() + ',' + id);
            }

            $('#product_id_input').val('');
            spinSuccess();
            $('.delProduct_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteProductOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-product";
            showErrorOp(element, message);
        });

}

function deleteProductOp(id) {
    let idsArray = $('#product_ids').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#product_ids').val(idsArray.join(','));
}

function isNewProductInputOp(id) {
    let idsArray = $('#product_ids').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/* Product Brand functions */
function addBrandProductOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getBrandNameOp&id=" + id
    })
        .done(function(brandName) {
            let element = "#block-for-error-brand-p";
            hideErrorOp(element);
            $('#divBrands_p_op').append('<div class="divItem_op">' + brandName + ' (id: ' + id + ') <span class="delBrand_p_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#brand_ids_p').val()) {
                $('#brand_ids_p').val(id);
            }
            else {
                $('#brand_ids_p').val($('#brand_ids_p').val() + ',' + id);
            }

            $('#brand_p_id_input').val('');
            spinSuccess();
            $('.delBrand_p_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteBrandProductOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-brand-p";
            showErrorOp(element, message);
        });
}

function deleteBrandProductOp(id) {
    let idsArray = $('#brand_ids_p').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#brand_ids_p').val(idsArray.join(','));
}

function isNewBrandProductInputOp(id) {
    let idsArray = $('#brand_ids_p').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/* Product Tag functions */
function addTagOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getTagNameOp&id=" + id
    })
        .done(function(tagName) {
            let element = "#block-for-error-tag-p";
            hideErrorOp(element);
            $('#divTags_p_op').append('<div class="divItem_op">' + tagName + ' (id: ' + id + ') <span class="delTag_p_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#tag_ids_p').val()) {
                $('#tag_ids_p').val(id);
            }
            else {
                $('#tag_ids_p').val($('#tag_ids_p').val() + ',' + id);
            }

            $('#tag_p_id_input').val('');
            spinSuccess();
            $('.delTag_p_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteTagOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-tag-p";
            showErrorOp(element, message);
        });
}

function deleteTagOp(id) {
    let idsArray = $('#tag_ids_p').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#tag_ids_p').val(idsArray.join(','));
}

function isNewTagInputOp(id) {
    let idsArray = $('#tag_ids_p').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/* Product Feature functions */
function addFeatureOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getFeatureNameOp&id=" + id
    })
        .done(function(featureName) {
            let element = "#block-for-error-feature-p";
            hideErrorOp(element);
            $('#divFeatures_p_op').append('<div class="divItem_op">' + featureName + ' (id: ' + id + ') <span class="delFeature_p_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#feature_ids_p').val()) {
                $('#feature_ids_p').val(id);
            }
            else {
                $('#feature_ids_p').val($('#feature_ids_p').val() + ',' + id);
            }

            $('#feature_p_id_input').val('');
            spinSuccess();
            $('.delFeature_p_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteFeatureOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-feature-p";
            showErrorOp(element, message);
        });
}

function deleteFeatureOp(id) {
    let idsArray = $('#feature_ids_p').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#feature_ids_p').val(idsArray.join(','));
}

function isNewFeatureInputOp(id) {
    let idsArray = $('#feature_ids_p').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/*Category functions*/
function addCategoryOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getCategoryNameOp&id=" + id
    })
        .done(function(categoryName) {
            let element = "#block-for-error-category";
            hideErrorOp(element);
            $('#divCategories_op').append('<div class="divItem_op">' + categoryName + ' (id: ' + id + ') <span class="delCategory_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#category_ids').val()) {
                $('#category_ids').val(id);
            }
            else {
                $('#category_ids').val($('#category_ids').val() + ',' + id);
            }

            $('#category_id_input').val('');
            spinSuccess();
            $('.delCategory_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteCategoryOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-category";
            showErrorOp(element, message);
        });

}

function deleteCategoryOp(id) {
    let idsArray = $('#category_ids').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#category_ids').val(idsArray.join(','));
}

function isNewCategoryInputOp(id) {
    let idsArray = $('#category_ids').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/*Brand functions*/
function addBrandOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getBrandNameOp&id=" + id
    })
        .done(function(brandName) {
            let element = "#block-for-error-brand";
            hideErrorOp(element);
            $('#divBrands_op').append('<div class="divItem_op">' + brandName + ' (id: ' + id + ') <span class="delBrand_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#brand_ids').val()) {
                $('#brand_ids').val(id);
            }
            else {
                $('#brand_ids').val($('#brand_ids').val() + ',' + id);
            }

            $('#brand_id_input').val('');
            spinSuccess();
            $('.delBrand_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteBrandOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-brand";
            showErrorOp(element, message);
        });

}

function deleteBrandOp(id) {
    let idsArray = $('#brand_ids').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#brand_ids').val(idsArray.join(','));
}

function isNewBrandInputOp(id) {
    let idsArray = $('#brand_ids').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

/*Cms pages functions*/
function addCmsPageOp(id, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=getCmsPageNameOp&id=" + id
    })
        .done(function (pageName) {
            let element = "#block-for-error-cms";
            hideErrorOp(element);
            $('#divCmsPages_op').append('<div class="divItem_op">' + pageName + ' (id: ' + id + ') <span class="delCmsPage_op delItem_op"'  +
                ' name="' + id + '"><i class = "fa fa-trash"></i></span></div>');
            if (!$('#cms_page_ids').val()) {
                $('#cms_page_ids').val(id);
            }
            else {
                $('#cms_page_ids').val($('#cms_page_ids').val() + ',' + id);
            }

            $('#cms_page_id_input').val('');
            spinSuccess();
            $('.delCmsPage_op').click(function () {
                $(this).parent().remove();
                let id = $(this).attr('name');
                deleteCmsPageOp(id);
            })
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-cms";
            showErrorOp(element, message);
        });
}

function deleteCmsPageOp(id) {
    let idsArray = $('#cms_page_ids').val().split(',');
    let pos = idsArray.indexOf(id);
    if (pos != -1) {
        idsArray.splice(pos, 1);
    }
    $('#cms_page_ids').val(idsArray.join(','));
}

function isNewCmsPageInputOp(id) {
    let idsArray = $('#cms_page_ids').val().split(',');
    if (idsArray.indexOf(id) == -1) {
        return true;
    }
    return false;
}

function addCustomHook(customHookValue, url_ajax) {
    $.ajax({
        type: "POST",
        url: url_ajax,
        data: "ajax=true&action=checkCustomHook&hook=" + customHookValue
    })
        .done(function() {
            let element = "#block-for-error-hook";
            hideErrorOp(element);
            $('#hook_name').val(customHookValue);
            spinSuccess();
        })
        .fail(function (response, textStatus, errorThrown) {
            spinSuccess();
            var message = '';
            if (textStatus === 'error') {
                message = 'Error: ' + response.status + ' ' + errorThrown;
                if (response.status === 503) {
                    message += ". If you have Maintenance mode enabled, add to whitelist your IP address";
                }
            }

            try {
                message = $.parseJSON(response.responseText).message;
            } catch(e) {

            }

            let element = "#block-for-error-hook";
            showErrorOp(element, message);
        });
}

/*Init functions*/
function initUnfold() {
    if($('#expand_select_products').is(':checked')) {
        $('.how-products-selected').slideDown();
        if($('#select_products_by_id').is(':checked')) {
            $('#select_products_by_id').attr('checked', true).val(1);
            $('#select_products_by_id').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_products').slideDown();
        }
        if($('#select_products_by_category').is(':checked')) {
            $('#select_products_by_category').attr('checked', true).val(1);
            $('#select_products_by_category').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_product_categories').slideDown();
        }
        if($('#select_products_by_brand').is(':checked')) {
            $('#select_products_by_brand').attr('checked', true).val(1);
            $('#select_products_by_brand').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_brands_p').slideDown();
        }
        if($('#select_products_by_tag').is(':checked')) {
            $('#select_products_by_tag').attr('checked', true).val(1);
            $('#select_products_by_tag').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_tags_p').slideDown();
        }
        if($('#select_products_by_feature').is(':checked')) {
            $('#select_products_by_feature').attr('checked', true).val(1);
            $('#select_products_by_feature').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_features_p').slideDown();
        }
    }
    if($('#expand_product_categories').is(':checked')) {
        $('.select_categories').slideDown();
    }
    if($('#expand_brands').is(':checked')) {
        $('.select_brands').slideDown();
    }
    if($('#expand_cms_pages').is(':checked')) {
        $('.select_cms_pages').slideDown();
    }
    if($('#expand_special_pages').is(':checked')) {
        $('.select_special_pages').slideDown();
    }
}
function initUnfoldIndependent() {
    $('.op-radio input[type="radio"]').each(function () {
        if ($(this).is(':checked')) {
            $(this).parent().find($('.op-radio-control')).addClass('op-radio-control-checked');
        }
    })
    $('.op-radio input[type="radio"]').click(function () {
        $(this).parent().parent().parent().find($('.op-radio-control-checked')).removeClass('op-radio-control-checked').parent().find($('input[type="radio"]')).attr('checked', false);
        $(this).attr('checked', true).parent().find($('.op-radio-control')).addClass('op-radio-control-checked');
    })
    $('.select_languages').appendTo($('#expand_select_languages').parent().parent());
    $('.select_currencies').appendTo($('#expand_select_currencies').parent().parent());
    $('.select_customer_groups').appendTo($('#expand_select_customer_groups').parent().parent());

    $('#is_only_default_category_wrap').appendTo($('.select_product_categories'))
    const iodcw_checkbox = $('#is_only_default_category_wrap input[type="checkbox"]')
    if (iodcw_checkbox.is(':checked')) {
        iodcw_checkbox.parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
    }

    if($('#expand_select_languages').is(':checked')) {
        $('.select_languages').slideDown();
    }
    $('#expand_select_languages').click(function(){
        $('.select_languages').slideDown();
    })
    $('#collapse_select_languages').click(function () {
        $('.select_languages').slideUp();
    })
    if($('#expand_select_currencies').is(':checked')) {
        $('.select_currencies').slideDown();
    }
    $('#expand_select_currencies').click(function(){
        $('.select_currencies').slideDown();
    })
    $('#collapse_select_currencies').click(function () {
        $('.select_currencies').slideUp();
    })

    if($('#expand_select_customer_groups').is(':checked')) {
        $('.select_customer_groups').slideDown();
    }
    $('#expand_select_customer_groups').click(function(){
        $('.select_customer_groups').slideDown();
    })
    $('#collapse_select_customer_groups').click(function () {
        $('.select_customer_groups').slideUp();
    })

    bindCheckboxClick();
}
function openCustom(activeClass)
{
    const label = $('#form-group-custom-hook-label');
    label.addClass(activeClass);
    label.find($('.cuop-arrow-down')).hide();
    label.find($('.cuop-arrow-up')).show();
    $('#form-group-custom-hook-body').slideDown();
}

function openSelect(activeClass)
{
    const label = $('#form-group-select-hook-label');
    label.addClass(activeClass);
    label.find($('.cuop-arrow-down')).hide();
    label.find($('.cuop-arrow-up')).show();
    $('#form-group-select-hook-body').slideDown();
}

function closeCustom(activeClass)
{
    const label = $('#form-group-custom-hook-label');
    label.removeClass(activeClass);
    label.find($('.cuop-arrow-up')).hide();
    label.find($('.cuop-arrow-down')).show();
    $('#form-group-custom-hook-body').slideUp();
}

function closeSelect(activeClass)
{
    const label = $('#form-group-select-hook-label');
    label.removeClass(activeClass);
    label.find($('.cuop-arrow-up')).hide();
    label.find($('.cuop-arrow-down')).show();
    $('#form-group-select-hook-body').slideUp();
}

$('document').ready(function () {
    /*Where to display*/
    /*Fold languages settings*/
    initUnfoldIndependent();
    /* Main level (pages) */
    if($('#expand_select_main').is(':checked')) {
        $('.when-not-all').slideDown();
        initUnfold()
    }
    $('#collapse_select_main').click(function () {
        $('.when-not-all-2').slideUp();
        $('.when-not-all').slideUp();
    })
    $('#expand_select_main').click(function () {
        $('.when-not-all').slideDown();
        initUnfold()
    })

    /*Fold-unfold product settings*/

    $('.select_products').appendTo($('#select_products_by_id').parent().parent());
    $('.select_product_categories').appendTo($('#select_products_by_category').parent().parent());
    $('.select_brands_p').appendTo($('#select_products_by_brand').parent().parent());
    $('.select_tags_p').appendTo($('#select_products_by_tag').parent().parent());
    $('.select_features_p').appendTo($('#select_products_by_feature').parent().parent());


    $('#expand_select_products').click(function(){
        $('.how-products-selected').slideDown();
        if($('#select_products_by_id').is(':checked')) {
            $('#select_products_by_id').attr('checked', true).val(1);
            $('#select_products_by_id').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_products').slideDown();
        }
        if($('#select_products_by_category').is(':checked')) {
            $('#select_products_by_category').attr('checked', true).val(1);
            $('#select_products_by_category').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_product_categories').slideDown();
        }
        if($('#select_products_by_brand').is(':checked')) {
            $('#select_products_by_brand').attr('checked', true).val(1);
            $('#select_products_by_brand').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_brands_p').slideDown();
        }
        if($('#select_products_by_tag').is(':checked')) {
            $('#select_products_by_tag').attr('checked', true).val(1);
            $('#select_products_by_tag').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_tags_p').slideDown();
        }
        if($('#select_products_by_feature').is(':checked')) {
            $('#select_products_by_feature').attr('checked', true).val(1);
            $('#select_products_by_feature').parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_features_p').slideDown();
        }
    })
    $('#all_products_0').click(function () {
        $('.how-products-selected').slideUp();
    })
    $('#all_products_1').click(function () {
        $('.how-products-selected').slideUp();
    })

    $('#select_products_by_id').click(function(){
        if($(this).is(':checked')) {
            $(this).attr('checked', true).val(1);
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_products').slideDown();
        }
        else {
            $(this).attr('checked', false).val(0);
            $(this).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
            $('.select_products').slideUp();
        }
    })

    $('#select_products_by_category').click(function(){
        if($(this).is(':checked')) {
            $(this).attr('checked', true).val(1);
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_product_categories').slideDown();
        }
        else {
            $(this).attr('checked', false).val(0);
            $(this).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
            $('.select_product_categories').slideUp();
        }
    })

    $('#select_products_by_brand').click(function(){
        if($(this).is(':checked')) {
            $(this).attr('checked', true).val(1);
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_brands_p').slideDown();
        }
        else {
            $(this).attr('checked', false).val(0);
            $(this).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
            $('.select_brands_p').slideUp();
        }
    })

    $('#select_products_by_tag').click(function(){
        if($(this).is(':checked')) {
            $(this).attr('checked', true).val(1);
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_tags_p').slideDown();
        }
        else {
            $(this).attr('checked', false).val(0);
            $(this).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
            $('.select_tags_p').slideUp();
        }
    })

    $('#select_products_by_feature').click(function(){
        if($(this).is(':checked')) {
            $(this).attr('checked', true).val(1);
            $(this).parent().find($('.op-checkbox-control')).addClass('op-checkbox-control-checked');
            $('.select_features_p').slideDown();
        }
        else {
            $(this).attr('checked', false).val(0);
            $(this).parent().find($('.op-checkbox-control')).removeClass('op-checkbox-control-checked');
            $('.select_features_p').slideUp();
        }
    })

    /*Fold categories settings*/

    $('.select_categories').appendTo($('#expand_product_categories').parent().parent());


    $('#expand_product_categories').click(function(){
        $('.select_categories').slideDown();
    })
    $('#all_categories_0').click(function () {
        $('.select_categories').slideUp();
    })
    $('#all_categories_1').click(function () {
        $('.select_categories').slideUp();
    })

    /*Fold brands settings*/

    $('.select_brands').appendTo($('#expand_brands').parent().parent());

    $('#expand_brands').click(function(){
        $('.select_brands').slideDown();
    })
    $('#all_brands_0').click(function () {
        $('.select_brands').slideUp();
    })
    $('#all_brands_1').click(function () {
        $('.select_brands').slideUp();
    })

    /*Fold cms pages settings*/

    $('.select_cms_pages').appendTo($('#expand_cms_pages').parent().parent());

    $('#expand_cms_pages').click(function(){
        $('.select_cms_pages').slideDown();
    })
    $('#all_cms_pages_0').click(function () {
        $('.select_cms_pages').slideUp();
    })
    $('#all_cms_pages_1').click(function () {
        $('.select_cms_pages').slideUp();
    })

    /*Fold special pages settings*/

    $('.select_special_pages').appendTo($('#expand_special_pages').parent().parent());

    $('#expand_special_pages').click(function(){
        $('.select_special_pages').slideDown();
    })
    $('#all_special_pages_0').click(function () {
        $('.select_special_pages').slideUp();
    })
    $('#all_special_pages_1').click(function () {
        $('.select_special_pages').slideUp();
    })


    /*Choosing by id*/

    $('#product_id_add').click(function () {
        let id = $('#product_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewProductInputOp(id)) {
            addProductOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Product with this id is already added';
            let element = "#block-for-error-product";
            showErrorOp(element, message);
        }
    })
    $('.delProduct_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteProductOp(id);
    })

    /* Display on PDP with brands - add by id */
    $('#brand_p_id_add').click(function () {
        let id = $('#brand_p_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewBrandProductInputOp(id)) {
            addBrandProductOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Brand with this id is already added';
            let element = "#block-for-error-brand-p";
            showErrorOp(element, message);
        }
    })
    $('.delBrand_p_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteBrandProductOp(id);
    })

    /* Display on PDP with tags - add by id */
    $('#tag_p_id_add').click(function () {
        let id = $('#tag_p_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewTagInputOp(id)) {
            addTagOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Tag with this id is already added';
            let element = "#block-for-error-tag-p";
            showErrorOp(element, message);
        }
    })
    $('.delTag_p_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteTagOp(id);
    })

    /* Display on PDP with features - add by id */
    $('#feature_p_id_add').click(function () {
        let id = $('#feature_p_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewFeatureInputOp(id)) {
            addFeatureOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Feature with this id is already added';
            let element = "#block-for-error-feature-p";
            showErrorOp(element, message);
        }
    })
    $('.delFeature_p_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteFeatureOp(id);
    })

    /* Display on category pages - add by id */
    $('#category_id_add').click(function () {
        let id = $('#category_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewCategoryInputOp(id)) {
            addCategoryOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Category with this id is already added';
            let element = "#block-for-error-category";
            showErrorOp(element, message);
        }
    })
    $('.delCategory_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteCategoryOp(id);
    })

    /* Display on brand pages - add by id */
    $('#brand_id_add').click(function () {
        let id = $('#brand_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewBrandInputOp(id)) {
            addBrandOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'Brand with this id is already added';
            let element = "#block-for-error-brand";
            showErrorOp(element, message);
        }
    })
    $('.delBrand_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteBrandOp(id);
    })

    /* Display on CMS pages - add by id */
    $('#cms_page_id_add').click(function () {
        let id = $('#cms_page_id_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if (isNewCmsPageInputOp(id)) {
            addCmsPageOp(id, url_ajax);
        }
        else {
            spinSuccess();
            let message = 'CMS page with this id is already added';
            let element = "#block-for-error-cms";
            showErrorOp(element, message);
        }
    })
    $('.delCmsPage_op').click(function () {
        $(this).parent().remove();
        let id = $(this).attr('name');
        deleteCmsPageOp(id);
    })

    /*Choose hook works*/
    $('#clear-custom-hook').click(function () {
        $('#hook_name').val('');
    })
    $('#hook_id').change(function() {
        if ($(this).val() != 0) {
            let element = "#block-for-error-hook";
            hideErrorOp(element);
            let selectedText = $(this).find($('option:selected')).text();
            $('#hook_name').val(selectedText);
        }
    })
    /*Custom hook works*/
    $('#custom_hook_name_add').click(function () {
        let customHookValue = $('#custom_hook_name_input').val();
        $(this).find($('.spin-process')).show();
        $(this).find($('.spinner-hide-txt')).hide();
        if(customHookValue != '') {
            addCustomHook(customHookValue, url_ajax);
        }
    });

    $('#form-group-select-hook-label').click(function () {
        const activeClass = 'form-group-hook-label-active';
        if ($(this).hasClass(activeClass)) {
            closeSelect(activeClass);
            openCustom(activeClass);
        } else {
            closeCustom(activeClass);
            openSelect(activeClass);
        }
    });

    $('#form-group-custom-hook-label').click(function () {
        const activeClass = 'form-group-hook-label-active';
        if ($(this).hasClass(activeClass)) {
            closeCustom(activeClass);
            openSelect(activeClass);
        } else {
            closeSelect(activeClass);
            openCustom(activeClass);
        }
    });
});
