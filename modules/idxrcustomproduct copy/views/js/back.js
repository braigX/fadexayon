/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2017 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

$(document).ready(function () {

    if (alert_text) {
        $('#customizable_category').after('<br/><span class="label label-danger">' + alert_text + '</span>');
    }
    
    //Idxautocomplete
    $('.autocomplete-search').each(function() {
        loadAutocomplete($(this), false);
    });

    $('.autocomplete-search').on('buildTypeahead', function() {
        loadAutocomplete($(this), true);
    });
    
    $(document).on('click', '.idxrcustomproduct-autocomplete .typeahead-list .eliminar', function(e){
        e.preventDefault();
        $(this).parent().parent().remove();
    })

    //Fin idxautocomplete

    vistype_actual = $('#configuration_vistype').val();
    $('.vistype_prev').hide();
    $('#vistype_prev_' + vistype_actual).show();
    if (vistype_actual !== 'accordion') {
        $('#first_open_on').closest('.form-group').hide('.form-group');
        $('#resume_open_on').closest('.form-group').hide('.form-group');
    }
    $('#configuration_vistype').on('change', function () {
        vistype_actual = $('#configuration_vistype').val();
        $('.vistype_prev').hide();
        $('#vistype_prev_' + vistype_actual).show();
        if (vistype_actual === 'accordion') {
            $('#first_open_on').closest('.form-group').show('.form-group');
            $('#resume_open_on').closest('.form-group').show('.form-group');
        } else {
            $('#first_open_on').closest('.form-group').hide('.form-group');
            $('#resume_open_on').closest('.form-group').hide('.form-group');
        }
    });

    if ($('input[name=discount]:checked').val() == 0) {
        $('.discount_field').closest('.form-group').hide('.form-group');
    }

    $('input[name=discount]').on('change', function () {
        if ($('input[name=discount]:checked').val() == 0) {
            $('.discount_field').closest('.form-group').hide('.form-group');
        } else {
            $('.discount_field').closest('.form-group').show('.form-group');
        }
    });

    first_type = $('select[name=type]').val();
    if (first_type == 'text' || first_type == 'textarea' || first_type === 'file') {
        $('select[name=columns]').closest('.form-group').hide('.form-group');
        $('#zoom_icon_on').closest('.form-group').hide('.form-group');
        $('#button_impact_on').closest('.form-group').hide('.form-group');
    }

    $('#ajax_add_sel_img_opt').on('click', function (e) {
        e.preventDefault();
        return false;
    });

    $('#renderEditComponent').on('click', '.del_option', function () {
        component = $(this).attr('data-component');
        option = $(this).attr('data-option');
        if (confirm(delete_option_query)) {
            $('#js_optionpanel_' + component + '_' + option).remove();
            $.post(url_ajax, {action: "deleteoption", component: component, option: option, ajax: 1})
                    .done(function (response) {
                        show_result(response);
                    });
        }
    });

    $('#renderEditComponent').on('click', '.upd_option', function () {
        component = $(this).attr('data-component');
        option = $(this).attr('data-option');

        var data = new FormData();
        if ($('#file_' + component + '_' + option).length !== 0) {
            jQuery.each(jQuery('#file_' + component + '_' + option)[0].files, function (i, file) {
                data.append('file', file);
            });
        }
        info = new Array();
        $('#js_optionpanel_' + component + '_' + option + ' :input').each(function () {
            if (typeof $(this).attr('id') != 'undefined' && $(this).attr('type') != 'file') {
                item = {id: $(this).attr('id'), val: $(this).val()};
                info.push(item);
            }
        });
        data_json = JSON.stringify(info);
        data.append('data', data_json);
        data.append('action', 'updateoption');
        data.append('component', component);
        data.append('option', option);
        $.ajax({
            type: 'POST',
            url: url_ajax,
            data: data,
            async: false,
            cache: false,
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            ajax: 1
        }).done(function (response) {
            show_result(response);
            refresh_optionlist();
        }).fail(function () {
            showErrorMessage('Error when try to save the configuration');
        });
    });

    comp_type = $('select[name=type]').val();
    if (comp_type === 'text' || comp_type === 'textarea') {
        $('select[name=columns]').closest('.form-group').hide('.form-group');
    }
    if (comp_type !== 'sel_img') {
        $('#zoom_icon_on').closest('.form-group').hide('.form-group');
    }

    $('select[name=type]').on('change', function () {
        type_selected = $(this).val();
        if (type_selected === 'text' || type_selected === 'textarea' || type_selected === 'file') {
            $('select[name=columns]').closest('.form-group').hide('.form-group');
            $('#zoom_icon_on').closest('.form-group').hide('.form-group');
            $('#button_impact_on').closest('.form-group').hide('.form-group');
        } else if (type_selected === 'sel_img') {
            $('#zoom_icon_on').closest('.form-group').show('.form-group');
            $('select[name=columns]').closest('.form-group').show('.form-group');
            $('#button_impact_on').closest('.form-group').show('.form-group');
        } else {
            $('#zoom_icon_on').closest('.form-group').hide('.form-group');
            $('select[name=columns]').closest('.form-group').show('.form-group');
            $('#button_impact_on').closest('.form-group').show('.form-group');
        }
    });

    configure_sortable_lists();
    checkComponentErrors();

    $(document).on('mouseover', '.combined', function () {
        var parent_id = $(this).attr('data_constraint');
        $('#sortable1 > li').each(function () {
            if ($(this).attr('data-id_component') == parent_id) {
                $(this).css('background-color', '#c5c5c5');
            } else {
                $(this).css('background-color', '');
            }
        });
    });

    $('.edit_component').on('click', function () {
        var component_id = $(this).parent().attr('data-id_component');
        location.replace(mod_back_url + "&updatecomponent&token=" + token + "&id_component=" + component_id);
    });

    $('.edit_component').tooltip();

    $('.add_constraints').on('click', function () {
        var component_id = $(this).parent().attr('data-id_component');
        var configuration_id = $('#id_configuration').val();
        $.post(url_ajax, {action: "showconstraints", component: component_id, configuration: configuration_id, ajax: 1})
                .done(function (data) {
                    var constrains = JSON.parse(data);
                    var actual_constrains = [];
                    var actual_impacts = [];
                    $("#constraints_modal .modal_component_name").html(constrains.component_name);
                    $('#constraint_component_id').attr('data-id', component_id);
                    $('#constraint_configuration_id').attr('data-id', configuration_id);
                    $("#default_options").find('option').not('.fixed_option').remove();
                    $("#constraint_options").empty();
                    $("#constraint_list").empty();
                    $('#impacttrigger_options').empty();
                    $('#impacttarget_options').empty();
                    constrains.configuration.components.forEach(function (component) {
                        if (component.id_component == component_id) {
                            actual_constrains = component.constraint;
                            actual_impacts = component.impact_options;
                        }
                    });

                    actual_constrains.forEach(function (constraint) {
                        var const_array = constraint.split('+');
                        var conts_li = '<tr id="li_' + constraint + '"><td class="constraint_name">';
                        var marker = '';
                        const_array.forEach(function (contraint_part) {
                            constrains.configuration.components.forEach(function (component) {
                                var const_id_component = contraint_part.split('_')[0];
                                var const_id_option = contraint_part.split('_')[1];
                                if (const_id_component == component.id_component) {
                                    component.options.options.forEach(function (option) {
                                        if (const_id_option == option.id) {
                                            conts_li += marker + component.name + ' - ' + option.name;
                                            marker = ' & ';
                                        }
                                    });
                                }
                            });
                        });

                        conts_li += '</td><td class="link_constraint text-center"><i class="icon icon-plus-square icon-2x"></i></td>';
                        conts_li += '<td class="delete_constraint text-center" data-value="' + constraint + '"><i class="icon icon-trash-o icon-2x"></i></td>';
                        conts_li += '</tr>';
                        $('#constraint_list').append(conts_li);
                    });

                    constrains.configuration.components.forEach(function (component) {
                        if (component.type !== 'sel_img' && component.type !== 'sel') {
                            return;
                        }
                        if (component.id_component === component_id) {
                            component.options.options.forEach(function (option) {
                                var option1 = $('<option></option>').attr("value", component.id_component + '_' + option.id).text(component.name + ' - ' + option.name);
                                var option2 = $('<option></option>').attr("value", component.id_component + '_' + option.id).text(component.name + ' - ' + option.name);
                                $("#default_options").append(option1);
                                $("#impacttarget_options").append(option2);
                            });
                            if (constrains.configuration.default_configuration) {
                                var default_configuration = $.parseJSON(constrains.configuration.default_configuration);
                                if (typeof default_configuration[component_id] !== "undefined" && default_configuration[component_id]) {
                                    if (default_configuration[component_id] >= 0) {
                                        var new_value = component_id + '_' + default_configuration[component_id];
                                        $('#default_options').val(new_value);
                                    } else if (default_configuration[component_id] == -1) {
                                        $('#default_options').val('disable');
                                    } else {
                                        $('#default_options').val('inherit');
                                    }
                                }
                            }
                            return;
                        }
                        component.options.options.forEach(function (option) {
                            var option1 = $('<option></option>').attr("value", component.id_component + '_' + option.id).text(component.name + ' - ' + option.name);
                            var option2 = $('<option></option>').attr("value", component.id_component + '_' + option.id).text(component.name + ' - ' + option.name);
                            $("#constraint_options").append(option1);                            
                            $("#impacttrigger_options").append(option2);
                        });
                    });
                    $('#impact_list').html("");
                    actual_impacts.forEach(function(impact){
                        constrains.configuration.components.forEach(function (component) {
                            if (component.type != 'text' && typeof component.options.options !== "undefined" && component.options.options.length) {
                                component.options.options.forEach(function(option) {
                                    if (impact.option_trigger === component.id_component+"_"+option.id){
                                        impact.option_trigger_name = component.name+' - '+option.name;
                                    }
                                    if (impact.option_impacted === component.id_component+"_"+option.id){
                                        impact.option_impacted_name = component.name+' - '+option.name;
                                    }
                                });
                            }
                        });
                        var impact_text = '';
                        if (impact.impact_percent) {
                            impact_text = impact.impact_percent+'%';
                        } else {
                            impact_text = impact.impact_fixed+currency.sign;
                        }
                        
                        var impact_li = '<tr id="li_' + impact.option_trigger+'to'+impact.option_impacted + '"><td class="constraint_name">';
                        impact_li += impact.option_trigger_name+' '+apply_text+' '+impact_text+' '+to_text+' '+impact.option_impacted_name;
                        impact_li += '<td class="delete_impact text-center" data-value="' + impact.option_trigger+'to'+impact.option_impacted + '"><i class="icon icon-trash-o icon-2x"></i></td>';
                        impact_li += '</tr>';
                        $('#impact_list').append(impact_li);
                    });

                    $("#constraints_modal").modal('show');
                });
    });

    $('.add_constraints').tooltip();
    $('.bi-percent').tooltip();

    $('#send_constrain').on('click', function () {
        var constraint = $('#constraint_options').val();
        var expansion = false;
        var name = $("#constraint_options option:selected").text();
        var component = $('#constraint_component_id').attr('data-id');
        var configuration = $('#constraint_configuration_id').attr('data-id');
        if ($(".to_link")[0]) {
            var link_id = $(".to_link").attr('id').replace('li_', '');
            constraint = constraint + '+' + link_id;
            $(".to_link").attr('id', 'li_' + constraint);
            $(".to_link .delete_constraint").attr('data-value', constraint);
            $.post(url_ajax, {action: "delconstraints", component: component, configuration: configuration, constraint: link_id, ajax: 1});
            expansion = true;
        }        
        $.post(url_ajax, {action: "addconstraints", component: component, configuration: configuration, constraint: constraint, ajax: 1})
                .done(function (data) {
                    if (!expansion) {
                        $('#constraint_list').append('<tr id="li_' + constraint + '"><td class="constraint_name">' + name + '</td><td class="link_constraint text-center"><i class="icon icon-plus-square icon-2x"></i></td><td class="delete_constraint text-center" data-value="' + constraint + '"><i class="icon icon-trash-o icon-2x"></i></td></tr>');
                    } else {
                        $('.to_link .constraint_name').append(' & ' + name);
                    }
                });
    });

    $(document).on('click', '.delete_constraint', function () {
        var constraint = $(this).attr('data-value');
        var parent = $(this).closest("tr");
        var component = $('#constraint_component_id').attr('data-id');
        var configuration = $('#constraint_configuration_id').attr('data-id');
        $.post(url_ajax, {action: "delconstraints", component: component, configuration: configuration, constraint: constraint, ajax: 1})
                .done(function (data) {
                    if (data === 'ok') {
                        parent.remove();
                    }
                });
    });

    $(document).on('click', '.link_constraint', function () {
        if ($(this).closest('tr').hasClass('to_link')) {
            $('#constraint_list tr').removeClass('to_link');
        } else {
            $('#constraint_list tr').removeClass('to_link');
            $(this).closest('tr').addClass('to_link');
        }
    });
    
    $(document).on('click', '#send_impact', function() {
        var component = $('#constraint_component_id').attr('data-id');
        var configuration = $('#constraint_configuration_id').attr('data-id');
        var impact = {
            'option_trigger': $('#impacttrigger_options').val(),
            'option_impacted': $('#impacttarget_options').val(),
            'impact_percent': $('#impactoption_percent').val(),
            'impact_fixed': $('#impactoption_fixed').val()
        };
        var impact_name = {
            'option_trigger': $('#impacttrigger_options option:selected').html(),
            'option_impacted' : $('#impacttarget_options option:selected').html()
        }
        
        $.post(url_ajax, {action: "addImpact", component: component, configuration: configuration, impact: impact, ajax: 1})
        .done(function (data) {
            if (data === 'ok') {
                var impact_text = '';
                if (impact.impact_percent) {
                    impact_text = impact.impact_percent+'%';
                } else {
                    impact_text = impact.impact_fixed+currency.sign;
                }
                var impact_li = '<tr id="li_' + impact.option_trigger+'to'+impact.option_impacted + '"><td class="constraint_name">';
                impact_li += impact_name.option_trigger+' '+apply_text+' '+impact_text+' '+to_text+' '+impact_name.option_impacted;
                impact_li += '<td class="delete_impact text-center" data-value="' + impact.option_trigger+'to'+impact.option_impacted + '"><i class="icon icon-trash-o icon-2x"></i></td>';
                impact_li += '</tr>';
                $('#impact_list').append(impact_li);
            }
        });
    });
    
    $(document).on('click', '.delete_impact', function () {
        var impact = $(this).attr('data-value');
        var parent = $(this).closest("tr");
        var component = $('#constraint_component_id').attr('data-id');
        var configuration = $('#constraint_configuration_id').attr('data-id');
        $.post(url_ajax, {action: "delimpact", component: component, configuration: configuration, impact: impact, ajax: 1})
            .done(function (data) {
                if (data === 'ok') {
                    parent.remove();
                }
            });
    });

    $(document).on('submit', '#option-form', function (e) {
        e.preventDefault();
        $(this).validate();
        var formData = new FormData(this);
        formData.append("action", "addoption");
        $.ajax({
            url: url_ajax,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (data, textStatus, jqXHR) {
                show_result(data);
                refresh_optionlist();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //if fails     
            }
        });
        return false;
    });

    $(document).on('change', '#default_options', function () {
        var value = $(this).val();
        var conf_id = $('#constraint_configuration_id').attr('data-id');
        var component_id = $('#constraint_component_id').attr('data-id');
        $.post(url_ajax, {action: "configurationdefault", configuration: conf_id, component: component_id, option: value, ajax: 1});
        showSuccessMessage('Changes saved');
    });

    $('button[name="submitConfiguration"]').on('click', function () {
        var list = '';
        var comma = '';
        $('#sortable1').children('li').each(function () {
            list += comma + $(this).attr('data-id_component');
            comma = ',';
        });
        $('#configuration_components_order').val(list);
    });

    $(document).on('click', 'button[name="submitConfigurationStay"]', function () {
        var list = '';
        var comma = '';
        $('#sortable1').children('li').each(function () {
            list += comma + $(this).attr('data-id_component');
            comma = ',';
        });

        $('#configuration_components_order').val(list);
    });

    //Add save and stay buttons
    //Configurer
    $('button[name=submitConfiguration]').clone().insertBefore("button[name=submitConfiguration]");
    $('button[name=submitConfiguration]').first().attr('id', 'configuration_form_submit_btn_2_stay');
    $('#configuration_form_submit_btn_2_stay').attr('name', 'submitConfigurationStay');
    $('#configuration_form_submit_btn_2_stay').html('<i class="process-icon-save"></i> '+savenstay);
    //Component
    $('button[name=submitComponent]').clone().insertBefore("button[name=submitComponent]");
    $('button[name=submitComponent]').first().attr('id', 'configuration_form_submit_btn_3_stay');
    $('#configuration_form_submit_btn_3_stay').attr('name', 'submitComponentStay');
    $('#configuration_form_submit_btn_3_stay').html('<i class="process-icon-save"></i> '+savenstay);


    $('button[name=editComponent]').clone().insertBefore("button[name=editComponent]");
    $('button[name=editComponent]').first().attr('id', 'configuration_form_submit_btn_1_stay');
    $('#configuration_form_submit_btn_1_stay').attr('name', 'editComponentStay');
    $('#configuration_form_submit_btn_1_stay').html('<i class="process-icon-save"></i> '+savenstay);


    //Defalt switch
    $(document).on('change', '.default_switch', function () {
        var option_id = $(this).attr('id');
        var component_id = $('#id_component').val();
        var id_opt = option_id.replace('default_', '');
        if (!$(this).is(':checked')) {
            option_id = -1;
            id_opt = -1;
        }
        $.post(url_ajax, {ajax: true, action: "componentdefault", component: component_id, option: id_opt})
                .done(function (data) {
                    showSuccessMessage('Changes saved');
                }).fail(function () {
            showErrorMessage('Error when try to save the configuration');
        });
        $('.default_switch').each(function () {
            if ($(this).attr('id') != option_id) {
                $(this).prop("checked", false);
            }
        });
    });
    
    $(document).on('change', '.priceimpacttype_switch', function () {
        var option_id = $(this).attr('id');
        var id_opt = option_id.replace('priceimpacttype_', '');
        if ($(this).is(':checked')) {
            $('#priceimpacttype_' + id_opt).val(1);
            $('#option_priceimpact_' + id_opt).closest('.form-group').hide();
            $('#option_priceimpactcalc_' + id_opt).closest('.form-group').show();
        } else {
            $('#priceimpacttype_' + id_opt).val(0);
            $('#option_priceimpactcalc_' + id_opt).closest('.form-group').hide();
            $('#option_priceimpact_' + id_opt).closest('.form-group').show();
        }
    });

    $(document).on('change', '.productattachtype_switch', function () {
        var option_id = $(this).attr('id');
        var id_opt = option_id.replace('productattachtype_', '');
        if ($(this).is(':checked')) {
            $('#productattachtype_' + id_opt).val(1);
            $('#option_' + id_opt + '_product_attached').closest('.form-group').show();
        } else {
            $('#productattachtype_' + id_opt).val(0);
            $('#option_' + id_opt + '_product_attached').closest('.form-group').hide();
        }
    });

    $('.js-delete-component-icon').on('click', function (e) {
        e.preventDefault();
        var answer = confirm(confirm_text);
        if (answer) {
            var component_id = $(this).attr('data-component_id');
            $.post(url_ajax, {ajax: true, action: "deletecomponenticon", component: component_id})
                    .done(function (data) {
                        showSuccessMessage('Icon deleted');
                        $('.icon-preview').html(empty_icon_text);
                    }).fail(function () {
                showErrorMessage('Error when try to delete icon');
            });
        }
    });

    if ($('#fileuploader').length !== 0) {
        var configuration_id = $('#fileuploader').attr('data-idconfiguration')
        var max_size = $('#fileuploader').attr('data-max-size')
        $("#fileuploader").uploadFile({
            url: url_ajax,
            fileName: "myfile",
            maxFileSize: max_size,
            allowedTypes: "jpg,png,gif,jpeg,wbm,gd2,bmp,webp",
            acceptFiles: "image/*",
            formData: {"action": "massimageupd", "configuration_id": configuration_id},
            onSuccess: function (files, data, xhr, pd)
            {
                show_result(data);
            },

            showPreview: true,
            showDelete: true,
            previewHeight: "100px",
            previewWidth: "100px",
            onLoad: function (obj)
            {
                $.ajax({
                    cache: false,
                    url: url_ajax,
                    data: {"action": "getConfigAllImages", "configuration_id": configuration_id},
                    dataType: "json",
                    success: function (data)
                    {
                        for (var i = 0; i < data.length; i++)
                        {
                            obj.createProgress(data[i]["name"], data[i]["path"], data[i]["size"]);
                        }
                    }
                });
            },
            deleteCallback: function (data, pd) {
                for (var i = 0; i < data.length; i++) {
                    $.post(url_ajax, {"action": "deleteConfigImage", name: data[i]},
                        function (resp) {
                            show_result(resp);
                        });
                }
                pd.statusbar.hide(); //You choice.

            }
        });
    }
    
    $('#submitCloneComponent').on('click', function(){
        //$('#component_clone_form').submit();
    });
    
    $('#js-idxrcustomproduct-componentsearch').on('input', function(){
        var search_text = $(this).val().toLowerCase();
        if (search_text == '') {
            $('#sortable2 li').show();
        } else {
            $('#sortable2 li').each(function(){
                var text = $(this).text();
                var wrapped = $("<div>" + text + "</div>");
                wrapped.find('i').remove();
                var only_content =  wrapped.html();
                if (only_content.toLowerCase().indexOf(search_text) >= 0) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });
});

function show_result(response) {
    var resp = jQuery.parseJSON(response);
    if (resp.result == 'ok') {
        showSuccessMessage(resp.message);
    } else {
        showErrorMessage(resp.message);
    }
}

function refresh_optionlist() {
    var id_component = $('#id_component').val();
    $.post(url_ajax, {ajax: true, action: "getOptionsList", component: id_component})
            .done(function (data) {
                $('#option-form').parent().remove();
                $('#renderEditComponent').append(data);
                configure_sortable_lists();
                $('#option_list_sortable').find('img').map(function () {
                    var image_src = $(this).attr("src") + "?timestamp=" + new Date().getTime();
                    $(this).removeAttr("src").attr("src", image_src);
                });
                $('.multiselect_prod').multiselect({
                    enableFiltering: true,
                    filterBehavior: 'both',
                    enableCaseInsensitiveFiltering: true
                });
                $('.autocomplete-search').each(function() {
                    loadAutocomplete($(this), false);
                });
            });
}

function configure_sortable_lists() {
    if (($("#sortable1").length > 0)) {
        Sortable.create(document.getElementById('sortable1'), {
            group: "components",
            animation: 150,
            onAdd:function() {
                checkComponentErrors();
            }
        });
    }

    if (($("#sortable2").length > 0)) {
        Sortable.create(document.getElementById('sortable2'), {
            group: "components",
            animation: 150,
            onAdd:function() {
                checkComponentErrors();
            }
        });
    }

    if (($("#option_list_sortable").length > 0)) {
        var container = document.getElementById("option_list_sortable");
        Sortable.create(container, {
            animation: 150,
            handle: ".panel-heading",
            onUpdate: function () {
                var list_order = [];
                $('#option_list_sortable').children().each(function () {
                    var option = $(this).attr('id').replace('js_optionpanel_', '');
                    list_order.push(option);
                });

                $.post(url_ajax, {action: "orderOptions", order: list_order, ajax: 1});
                showSuccessMessage('Changes saved');
            }
        });
    }
}

function idxcustomproduct_product_select(product, form_id){
    $('#'+form_id).val(product.name);
    $('#'+form_id+'_value').val(product.id);
}

function idxcustomproduct_select_configuration_products(product, form_id){
    if (!product.used) {
        selectTypeadahead(product, form_id);
    } else {
        alert(product_repeated_text+' '+product.used);
    }
}

function idxcustomproduct_select_configuration_categories(category, form_id){
    if (!category.used) {
        selectTypeadahead(category, form_id);
    } else {
        alert(category_repeated_text+' '+category.used);
    }
}

function selectTypeadahead(item, form_id){
    let tplcollection = $('#tplcollection-' + form_id);
    let tplcollectionHtml = tplcollection.html().replace('%s', item.name);
    var html;
    html = `<li class="media">
    <div class="media-body media-middle">
    `+tplcollectionHtml+`
    </div>
    <input type="hidden" name="`+form_id+`[data][]" value="`+item.id+`" />
    </li>`;
    
    $('#' + form_id + '-data').append(html);
    $('#' + form_id).typeahead('val', '');
    $('#' + form_id + '-data').removeClass('hidden');
}

function checkComponentErrors(){
    var componenteIva = false;
    $('#sortable2 > li').each(function () {
        $(this).css("background-color","");
    });
    $('#sortable1 > li').each(function () {
        $(this).css("background-color","");
    });
    $('#sortable1 > li').each(function () {
        if ($(this).find('.bi-percent').length) {
            if (!componenteIva) {
                componenteIva = true;
            } else {
                $(this).css("background-color","red");
            }
        }
    });
}