/*
* 2007 - 2018 ZLabSolutions
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
* Do not edit or add to this file if you wish to upgrade module to newer
* versions in the future. If you wish to customize module for your
* needs please contact developer at http://zlabsolutions.com for more information.
*
*  @author    Eugene Zubkov <magrabota@gmail.com>
*  @copyright 2018 ZLab Solutions
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Property of ZLab Solutions https://www.facebook.com/ZLabSolutions/
*
*/

var lang_pac = '';

$(document).ready(function(){
    Zlcpi.loadBinds();
});

/*End ready function*/

Zlcpi = {
    loadBinds: function() {
        Zlcpi.readyBinds();
    },
    readyBinds: function() {

        Zlcpi.readAjaxFields();
        Zlcpi.runChosens();
        //Zlcpi.insertTablePagination();
        Zlcpi.moveCategoriesTrees();
        Zlcpi.indexTypeBinds();
        Zlcpi.bindSettingsActions();
        Zlcpi.bindSearchProductsButton();
        Zlcpi.bindRightClickContext();
        Zlcpi.bindApplyIndexButton();

        $('#zlc-form ps-switch').each(function() {
            if ($(this).attr('active') == 'true') {
                $(this).find('input[value=1]').attr('checked', 'checked');
                $(this).find('input[value=0]').attr('checked', false);
                $(this).find('input[value=1]').prop('checked', true);
                $(this).find('input[value=0]').prop('checked', false);
            } else {
                $(this).find('input[value=0]').prop('checked', true);
                $(this).find('input[value=1]').prop('checked', false);

                $(this).find('input[value=1]').attr('checked', false);
                $(this).find('input[value=0]').attr('checked', 'checked');
            }
        });

    },
    bindRightClickContext: function() {
        $(document).on('contextmenu', 'tr.product_row', function(event) {
            event.preventDefault();
            // Show contextmenu
            var id_product = $(this).attr('data-id-product');
            var top  = event.pageY - 20;
            var left  = event.pageX - 40;

/*
            $(".zlab-custom-menu").finish().toggle(10000).
            css({
                top: top + "px",
                left: left + "px"
            }).
*/


            $(".zlab-custom-menu").finish().show().
            css({
                top: top + "px",
                left: left + "px"
            }).
            attr('data-id-product', id_product);
            setTimeout(function() {
                $('.new_context_position').click().focus();
            }, 50);
        });
        $(document).on('mouseleave', '.zlab-custom-menu', function(event) {
            $(this).attr('data-id-product', '0').css('display', 'none').val('');
            $('.new_context_position').val('');
        });
        //save
        $(document).on('click', 'i.new_position_save', function(event) {
            //console.log(new_position_save);
            var id_product = $('.zlab-custom-menu').attr('data-id-product');
            var position = $('.new_context_position').val();
            Zlcpi.saveNewManualPosition(id_product, position);
        });
        $(document).on('click', 'i.new_position_cancel', function(event) {
            $('.zlab-custom-menu').attr('data-id-product', '0').css('display', 'none').val('');
            $('.new_context_position').val('');
        });
    },

    bindRightClickContext17: function() {
        $(document).on('contextmenu', 'tr.product_row', function(event) {
            event.preventDefault();
            // Show contextmenu
            var id_product = $(this).attr('data-id-product');
            var top  = event.pageY - 20;
            var left  = event.pageX - 40;

            $(".zlab-custom-menu").finish().toggle(10000).
            css({
                top: top + "px",
                left: left + "px"
            }).
            attr('data-id-product', id_product);
            setTimeout(function() {
                $('.new_context_position').click().focus();
            }, 50);
        });
        $(document).on('mouseleave', '.zlab-custom-menu', function(event) {
            $(this).attr('data-id-product', '0').css('display', 'none').val('');
            $('.new_context_position').val('');
        });
        //save
        $(document).on('click', 'i.new_position_save', function(event) {
            //console.log(new_position_save);
            var id_product = $('.zlab-custom-menu').attr('data-id-product');
            var position = $('.new_context_position').val();
            Zlcpi.saveNewManualPosition(id_product, position);
        });
        $(document).on('click', 'i.new_position_cancel', function(event) {
            $('.zlab-custom-menu').attr('data-id-product', '0').css('display', 'none').val('');
            $('.new_context_position').val('');
        });
    },
    saveNewManualPosition: function(id_product, position) {
        //console.log(id_product +" - "+ position);
        var i = position - 2;
        $tr = $('tr.product_row[data-id-product="'+id_product+'"]').clone();
        $('tr.product_row[data-id-product="'+id_product+'"]').remove();
        if (i == -1) {
            $tr.insertBefore($('tr.product_row:eq(0)'));
        } else {
            //$tr.insertAfter($('tr.product_row:eq('+i+')'));
            var max = $('tr.product_row').length - 1;
            $tr.insertAfter($('tr.product_row:eq('+Math.min(max,i)+')'));
        }
        setTimeout(function() {
            TableActions.rebuildViewIndex();
        }, 50);
    },


    
    indexTypeBinds: function() {
        $('select.what_to_edit').change(function() {
            var val = $(this).find('option:selected').val();
            //console.log('brands1'+val);
            if ($(this).find('option:selected').val() == '1') {
                Zlcpi.setIndexTypeCategories();
            } else {
                Zlcpi.setIndexTypeBrands();
                //console.log('brands');
            }
        });
        Zlcpi.setIndexTypeCategories();
    },
    setIndexTypeCategories: function(){
        $('.chosen-brands').addClass('hide');
        //$('.chosen-categories').removeClass('hide');
        $('.zlab-ext-cats').removeClass('hide');
        
    },
    setIndexTypeBrands: function(){
        //$('.chosen-categories').addClass('hide');
        $('#products-filter table tbody').html('');
        $('.zlab-ext-cats').addClass('hide');
        $('.chosen-brands').removeClass('hide');
    },


    readAjaxFields: function(){
        var raw = $('#zlc-ajaxfields').val();
        var json = decodeURIComponent(raw);
        lang_pac = JSON.parse(json);
    },
    runChosens: function() {
        $("select.categoryselector").chosen({width:'600px;'});
        $("select.brandselector").chosen({width:'600px;'});
        $("select.sortselector").chosen({width:'430px;'});
    },
    bindCategoriesTrees: function() {
        $('input[name="categoryBox[]"]').on('click', function() {
            var val = $(this).val();
            if ($('input[name="categoryBox[]"]:checked').length > 1) {
                $('input[name="categoryBox[]"]').prop('checked', false);
                $('input[name="categoryBox[]"][value="'+val+'"]').prop('checked', true);
            }
            //clearTable
            $('#search-products').click();
            return true;
        });
        //brands autosearch
        $('.brandselector').on('change', function(){
            $('#search-products').click();
        });
    },
    getSelectedCategoriesTrees: function() {
        var selected = [];
        $('.zlab-ext-cats input[name="categoryBox[]"]').each(function() {
           if ($(this).is(":checked")) {
               selected.push($(this).attr('value'));
           }
        });
        var categories_list = selected.join(',');
        //console.log(categories_list);
        return categories_list;
    },
    moveCategoriesTrees: function() {
        //start
        $tree = $('.zlab-ext-cats-template:eq(0)');
        $clone = $tree.clone();
        $clone.attr('id', 'ext1').removeClass('hide').removeClass('zlab-ext-cats-template').addClass('zlab-ext-cats');
        $after = $('select.categoryselector').parent().parent();
        $clone.insertAfter($after);
        $tree.remove();
        //end
        Zlcpi.bindCategoriesTrees();
    },

    bindSettingsActions: function(){
        $('#tab-settings input').change(function(){
            var id = $(this).data('id');
            var value = $(this).val();
            if (!!id == false) {
                if ($(this).parent().parent().parent().parent().parent().length > 0) {
                    id = $(this).parent().parent().parent().parent().parent().data('id');
                    if (!!id == false) {
                        id = $(this).parent().parent().parent().parent().parent().parent().data('id');
                    }
                }
            }
            Ajaxsz.updateSettings(id, value);
        });
        $('#tab-settings select').change(function() {
            var id = $(this).data('id');
            var value = '';
            if($(this).hasClass('amz_price_round')) {
                var val = [];
                $(this).find('option:selected').each(function() {
                    val.push($(this).val()); 
                });
                value = val.join(',');
                if (value == '') {
                    value = '0';
                }
            } else {
                value = $(this).val();
            }
            Ajaxsz.updateSettings(id, value);
            if ($(this).hasClass('api-config')) {
                setTimeout(function(){
                    Ajaxsz.amzcategoriesrefresh();
                }, 2000);
            }
        });
        /*LOG*/
        $('.nav-tabs li a[href="#log"]').click(function(){
            //console.log('updateLog');
            Ajaxsz.updateLog();
        });

        $('.clear-log').click(function(){
            //console.log('11111');
            Ajaxsz.clearLog();
            return false;
        });
        /*END LOG*/
        
        $(document).on('keypres', '#amazonshop-form', function(event){
            return event.keyCode != 13;
        });
        $(document).on('keypres', '#amz-add-product-bridge', function(event){
            if(event.keyCode == 13)
            {
                $(this).trigger("enterKey");
                event.preventDefault();
                //console.log('enter');
            }
            return event.keyCode != 13;
        });
    },
    bindSearchProductsButton: function() {
        $('#search-products').click(function() {
            //console.log('ok');
            var config = Zlcpi.readValidateConfig();
            Ajaxsz.getConfigProducts(config);
        })
    },
    bindApplyIndexButton: function() {
        //console.log('apply-index-bind');
        $('#apply-index').click(function(e) {
            e.preventDefault();
            //console.log('apply-index');
            var config = Zlcpi.readValidateConfig();
            var new_index = TableActions.getNewViewIndex();
            //console.log(config);
            if (((config.index_type == '1') && (config.id_category > 0)) || ((config.index_type == '2') && (config.id_manufacturer > 0))) {
                Ajaxsz.applyIndex(config, new_index);
            } else {
                $.growl.error({title:'', message: lang_pac.please_select_category});
            }
        })
    },

    readValidateConfig: function() {
        var products_config = {};
        products_config.index_type = $('select.what_to_edit option:selected').val();
        products_config.id_category = $('#associated-categories-tree input[name="categoryBox[]"]:checked').eq(0).val();
        products_config.id_manufacturer = $('select.brandselector option:selected').val();
        products_config.sort_by = $('select.sortselector option:selected').val();
        products_config.sort_way = $('select.sortway option:selected').val();
        products_config.limit = 0;
        return products_config;
    },
    backOnTop: function() {
     $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
        $('#back-to-top').tooltip('show');
    },

    hideConfigure: function() {
        $('.bootstrap > div.page-head').addClass('hide');
    },

    highlightPlay: function($el, effect = false) {
        $el.focus();
        $el.css('display', 'block');
        if (effect) {
            $el.addClass(effect+' animated');
            setTimeout(function() {
                $el.removeClass(effect+' animated');
            }, 1500);
        } else {
            $el.addClass('bounceIn animated');
            setTimeout(function() {
                $el.removeClass('bounceIn animated');
            }, 1500);
        }
    },
    runNewSortable: function() {

        if ($( "#products-filter table tbody.ui-sortable" ).length > 0) {
            //console.log('destroy');
            $( "#sortable-ui" ).sortable("destroy");
        }

        setTimeout(function() {
            //console.log('runNewSortable');
            $( "#sortable-ui" ).sortable({
                placeholder: "ui-state-highlight",
                stop: function( event, ui ) {
                    TableActions.rebuildViewIndex();
                }
            });
            $( "#sortable-ui" ).disableSelection();
            /*
            $( "#sortable" ).sortable({
                zIndex: 9999,
            });
            */
            //$( "#sortable" ).disableSelection();
        }, 300);
    },
}

TableActions = {
    getNewViewIndex: function() {
        var index_element_selector = '.id_product_position';
        var id_element_selector = '.id_of_product';
        var new_index = [];
        $('#products-filter tr.ui-sortable-handle').each(function(i) {
            var index = $(this).find(index_element_selector).text();
            var id_product = $(this).find(id_element_selector).text();
            new_index.push(id_product);
        });
        if (new_index.length == 0) {
            $('#products-filter tr.ui-state-default').each(function(i) {
                var index = $(this).find(index_element_selector).text();
                var id_product = $(this).find(id_element_selector).text();
                new_index.push(id_product);
            });
        }
        //console.log(new_index);
        return new_index;
    },
    rebuildViewIndex: function() {
        //console.log('rebuildViewIndex');
        setTimeout(function() {
            $('#products-filter tr.ui-sortable-handle').each(function(i) {
                var p = i + 1;
                $(this).find('.id_product_position').text(p);
            });
        }, 100);
    },
    clearTable: function() {

    },
    loadCategoryProducts: function(a, $row = false, link) {
        if ($( "#products-filter table tbody.ui-sortable" ).length > 0) {
            //console.log('destroy');
            $( "#sortable-ui" ).sortable("destroy");
        }
        $('.link_to_obj').remove();
        $('#search-products').parent().append('<div class="link_to_obj"><br><a href="'+link+'" target="_blank" >'+link+'</a></div>')
        var tb = $('#products-filter table tbody');
        $el = $('#products-filter table tbody');
        $el.attr('id', 'sortable-ui');
        if (!$row) {
            $tb = $(tb);
            $(tb).html('');
        }
        var hide_disabled = parseInt($('input[name="hide_disabled"]:checked').val());
        var move_disabled = parseInt($('input[name="move_disabled"]:checked').val());
        var hide_active = 0;
        if (move_disabled && hide_disabled) {
            hide_active = 1;
        }
        for (i = 0; i < a.length; i++)
        {
            if (a[i]['id_product']!==undefined)
            {
                var position = i + 1;
                var o = '';
                var product_asin = a[i]['reference'];
                var tr_line_color = 'row-light_green';
                var tr_class = '';
                if ((hide_active == 1) && (a[i]['active'] == '0')) {
                    tr_class = ' hide ';
                }
                o = '<tr class="odd product_row ui-state-default '+tr_class+'" data-id-product="'+a[i]['id_product']+'">';

                o += '<td class="center "><span class="id_product_position">'+position+'</span></td>';

                a[i]['id_product_attribute'] = 0;

                var ids = a[i]['id_product'];

                o += '<td class="center ipa_c"><span class="id_of_product">'+a[i]['id_product']+'</span></td>';

                o += '<td class="center"><span><img src="'+a[i]['image']+'"/></span></td>';
                o += '<td class="center"><span>'+a[i]['reference']+'</span></td>';
                //o += '<td class="center"><span><a  class="sync_single_product" href="#" title="'+lang_pac.sync_single+'">'+a[i]['product_asin']+'</a></span></td>';

                var product_url = Ajaxsz.getBaseuri()+'/index.php?id_product='+a[i]['id_product']+'&controller=product';
                o += '<td><span><a href="'+product_url+'" target="_blanc">'+a[i]['name']+'</a></span></td>';

                o += '<td class="center"><span>'+a[i]['category_name']+'</span></td>';

                o += '<td class="center">'+a[i]['price']+'</td>';

                //qty
                o += '<td class="center">'+a[i]['quantity']+'</td>';
                o += '<td class="center">'+a[i]['brand_name']+'</td>';
                o += '<td class="center">'+a[i]['supplier_name']+'</td>';
                //active
                if (a[i]['active'] == '1') {
                    o += '<td class="center"><i class="icon-check status"></i></td>';
                } else {
                    o += '<td class="center"><i class="icon-remove status"></i></td>';
                }   
                var current_position = parseInt(a[i]['position']) + 1;
                if (current_position == 'NaN') {
                    current_position = 'no index'
                }
                o += '<td class="center "><span class="id_current_position">'+current_position+'</span></td>';
                /*
                if (product_asin != null)
                    o += '<td class="center"><i class="icon-check status"></i></td>';
                else
                    o += '<td class="center"></td>';
                */
                /*
                //brand
                if (product_asin != null) {
                    o += '<td class="center"><a data-asin="'+product_asin+'" class="sync_single_product" href="#" title="'+lang_pac.sync_single+'"><i class="icon-gears status"></i></a></td>';
                } else {
                    o += '<td class="center"></td>';
                }
                
                //supplier
                if (product_asin != null) {
                    
                } else {
                    o += '<td class="center"></td>';
                }
                */

                //o += '<td><div class="btn-group-action"><div class="btn-group pull-right"></div></td>';
                o += '</tr>';
                
                if ($row) {
                    $row.after(o);
                } else {
                    $tb.append(o);  
                }
            }
        }
        $('#products-filter span.badge').text(i);
        Zlcpi.runNewSortable();
    },

}

Ajaxsz = {
    getBaseuri: function(){
        return $('#baseuri').val();
    },      
    baseuri: function(){
        return $('#zlab_ajax_link').val()+'&zlab_ajax=1';
    },      
    checkToken:  function(){
        var token = getUrlParameter('token');
        var check_e = $('#check_e').val();
        return '&token='+token+'&check_e='+check_e;
    },

    applyIndex: function(config, new_index) {
        new_index = JSON.stringify(new_index);
        //console.log(new_index.length);
        //return;
        $.ajax({
            type: 'POST',
            url: Ajaxsz.baseuri()+'&action=apply_index',
            data: {'products_config': config, 'new_index': new_index},
            beforeSend: function()
            {
                $("body").toggleClass("wait");
            },
            success: function (msg)
            {
                //console.log(msg);
                if (msg == 'true') {
                    $.growl.notice({title:'', message: lang_pac.text_index_updated});
                } else {
                    $.growl.error({title:'', message: lang_pac.error});
                }
                //var products = JSON.parse(msg);
                //TableActions.loadCategoryProducts(products);
            },
            complete: function()
            {
                $("body").toggleClass("wait");        
            },
        });
    },
    getConfigProducts: function(products_config)
    {
        $.ajax({
            type: 'POST',
            url: Ajaxsz.baseuri()+'&action=getconfigproducts',
            data: {'products_config': products_config},
            beforeSend: function()
            {
                $("body").toggleClass("wait");
            },
            success: function (msg)
            {

                //console.log(msg);
                var data = JSON.parse(msg);
                if (data['products'] !== undefined) {
                    var products = data['products'];
                } else {
                    var products = data[0];
                }
                

                //console.log(products);
                TableActions.loadCategoryProducts(products, false, data['link']);
            },
            complete: function()
            {
                $("body").toggleClass("wait");        
            },
        });
    },

    //updateSettings
    updateSettings: function(id, value){
        $.ajax({
            type: 'POST',
            url: Ajaxsz.baseuri()+'&action=updatesettings',
            data: {'value':value, 'id':id},
            beforeSend: function() {
                $("body").toggleClass("wait");
            },
            success: function (msg) {
                if (msg != 'true') {
                    var IS_JSON = true;
                    try {
                        var res = JSON.parse(msg);
                        var id = res[0];
                        var message = res[1];
                        $('#zlc-form [data-id="'+id+'"]').focus();
                        $.growl.error({title:'', message: lang_pac.error+' - '+message});
                    } catch(err) {
                        IS_JSON = false;
                        $.growl.error({title:'', message: lang_pac.error});
                    }
                } else {
                    $.growl.notice({title:'', message: lang_pac.text_updated});
                }
            },
            complete: function() {
                $("body").toggleClass("wait");
            },
        });
    },

    // Load Log
    updateLog: function(){
        //console.log('updateLog');
        $.ajax({
            type: 'POST',
            url: Ajaxsz.baseuri()+'/modules/productsindex/ajax.php?action=getlog'+Ajaxsz.checkToken(),
            beforeSend: function() {
                $("body").toggleClass("wait");
            },
            success: function (msg) {
                //console.log('msg'+msg);
                var IS_JSON = true;
                try {
                    var res = JSON.parse(msg);
                } catch(err) {
                    IS_JSON = false;
                    console.log('error: '+err);
                    //$('#log-block').html('');
                }

                if (IS_JSON == true) {
                    var log = res[0];
                    var next_import_asin = res[1];
                    var next_sync_asin = res[2];
                    $('#log-block').html('');
                    if (next_import_asin != '0') {
                        $('#log-block').append('Next import asin - '+next_import_asin+'<br>');
                    }
                    if (next_sync_asin != '0') {
                        $('#log-block').append('Next synchronization asin - '+next_sync_asin+'<br>');
                    }
                    for (var j = 0; j < log.length; j++) {
                        $('#log-block').append(log[j]+'<br>');
                    }
                    // run again
                    setTimeout(function() {
                        if ($('.nav-tabs li a[href="#log"]').parent().hasClass('active') == true) {
                            Ajaxsz.updateLog();
                        }
                    }, 5000);
                    //Ajaxsz.updateLog();
                }
            },
            complete: function() {
                $("body").toggleClass("wait");
            },
        });
    },

    clearLog: function(){
        $.ajax({
            type: 'POST',
            url: Ajaxsz.baseuri()+'/modules/productsindex/ajax.php?action=clearlog'+Ajaxsz.checkToken(),
            beforeSend: function() {
                $("body").toggleClass("wait");
            },
            success: function (msg){
                //console.log(msg);
                $('#log-block').html('');
                ////console.log('msg'+msg);
            },
            complete: function() {
                $("body").toggleClass("wait");
            },
        });
    },
};

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};