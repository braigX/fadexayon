/**
  * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

function getCommentForm() {
    if (document.forms)
        return (document.forms['comment_form']);
    else
        return (document.comment_form);
}

function getCommentDeleteForm() {
    if (document.forms)
        return (document.forms['delete_comment_form']);
    else
        return (document.delete_comment_form);
}

function acceptComment(id) {
    var form = getCommentForm();
    if (id)
        form.elements['id_ets_rv_product_comment'].value = id;
    form.elements['action'].value = 'accept';
    form.submit();
}


function deleteComment(id) {
    var form = getCommentForm();
    if (id)
        form.elements['id_ets_rv_product_comment'].value = id;
    form.elements['action'].value = 'delete';
    form.submit();
}

function delComment(id, confirmation) {
    var answer = confirm(confirmation);
    if (answer) {
        var form = getCommentDeleteForm();
        if (id)
            form.elements['delete_id_product_comment'].value = id;
        form.elements['delete_action'].value = 'delete';
        form.submit();
    }
}

function getCriterionForm() {
    if (document.forms)
        return (document.forms['criterion_form']);
    else
        return (document.criterion_form);
}

function editCriterion(id) {
    var form = getCriterionForm();
    form.elements['id_ets_rv_product_comment_criterion'].value = id;
    form.elements['criterion_name'].value = document.getElementById('criterion_name_' + id).value;
    form.elements['criterion_action'].value = 'edit';
    form.submit();
}

function deleteCriterion(id) {
    var form = getCriterionForm();
    form.elements['id_ets_rv_product_comment_criterion'].value = id;
    form.elements['criterion_action'].value = 'delete';
    form.submit();
}

$(document).ready(function () {
    $('select#id_product_comment_criterion_type').change(function () {
        // PS 1.6
        $('#categoryBox').closest('div.form-group').hide();
        $('#ids_product').closest('div.form-group').hide();
        // PS 1.5
        $('#categories-treeview').closest('div.margin-form').hide();
        $('#categories-treeview').closest('div.margin-form').prev().hide();
        $('#ids_product').closest('div.margin-form').hide();
        $('#ids_product').closest('div.margin-form').prev().hide();

        if (this.value == 2) {
            $('#categoryBox').closest('div.form-group').show();
            // PS 1.5
            $('#categories-treeview').closest('div.margin-form').show();
            $('#categories-treeview').closest('div.margin-form').prev().show();
        } else if (this.value == 3) {
            $('#ids_product').closest('div.form-group').show();
            // PS 1.5
            $('#ids_product').closest('div.margin-form').show();
            $('#ids_product').closest('div.margin-form').prev().show();
        }
    });

    $('select#id_product_comment_criterion_type').trigger("change");
});
