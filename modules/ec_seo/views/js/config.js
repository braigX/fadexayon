function Ec_rep_pagination_ba(page) {
    search = $('#table_balisealt .conf').serialize();
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: 16,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                page: page,
                search : search,
                id_shop: ec_id_shop,
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#table_balisealt').html(data);
        });
} 

function Ec_search_ba() {
    search = $('#table_balisealt .conf').serialize();
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: 16,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                search : search,
                id_shop: ec_id_shop,
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#table_balisealt').html(data);
           
        });
}

function Ec_rep_pagination(page, type, type2) {
    search = $('#table_'+type2+'_'+type+' table .conf').serialize();
    majsel = '11';
    if (type2 == 'meta') {
        majsel = '13';
    }
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: majsel,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                page: page,
                type: type,
                search : search,
                id_shop: ec_id_shop,
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#table_'+type2+'_'+type).html(data);
        });
} 
function Ec_search_gcplus(type, type2) {
    search = $('#table_'+type2+'_'+type+' table .conf').serialize();
    majsel = '11';
    if (type2 == 'meta') {
        majsel = '13';
    }
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: majsel,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                search : search,
                type: type,
                id_shop: ec_id_shop,
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#table_'+type2+'_'+type).html(data);
           
        });
}
$(document).ready(function () {
    if (typeof tinySetup !== "undefined") { 
        customconfig = {};
        customconfig['editor_selector'] = 'rte';
        customconfig['height'] = '500px';
        customconfig['width'] = '100%';
        customconfig['menubar'] = 'file edit view insert format tools table';
        customconfig['plugins'] = 'colorpicker link image paste pagebreak  contextmenu filemanager code media autoresize anchor preview';
        customconfig['toolbar1'] = "code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,align,bullist,numlist,image,media,formatselect,preview",
        customconfig['forced_root_block'] = false;
        tinySetup(customconfig);
    }
    $('body').on('click','#refreshTabBaliseAlt', function(e) {
        e.preventDefault();
        Ec_search_ba();
    });
    $('body').on('change','.table_mi_info .conf', function() {
        type = $(this).parents('.table_mi_info').attr('data-type');
        Ec_search_gcplus(type, 'mi');
    });
    $(document).on("keypress", ".table_mi_info input.conf", function(e){
        type = $(this).parents('.table_mi_info').attr('data-type');
        if(e.which == 13){
            Ec_search_gcplus(type, 'mi');
        }
    });

    $('body').on('click','.table_mi_info li a.pagination-items-page', function() {
        pagination = $(this).attr('data-items');
        type = $(this).parent().parent().attr('data-type');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '12',
                type: type,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                pagination : pagination,
                id_shop : ec_id_shop
            }),
            dataType: "json"
        })
        .done(function (data) {
            Ec_search_gcplus(type, 'mi');
        });
    });

    $('body').on('change','.table_meta_info .conf', function() {
        type = $(this).parents('.table_meta_info').attr('data-type');
        Ec_search_gcplus(type, 'meta');
    });
    $(document).on("keypress", ".table_meta_info input.conf", function(e){
        type = $(this).parents('.table_meta_info').attr('data-type');
        if(e.which == 13){
            Ec_search_gcplus(type, 'meta');
        }
    });
    
    $('body').on('click','.table_meta_info li a.pagination-items-page', function() {
        pagination = $(this).attr('data-items');
        type = $(this).parent().parent().attr('data-type');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '14',
                type: type,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                pagination : pagination,
                id_shop : ec_id_shop
            }),
            dataType: "json"
        })
        .done(function (data) {
            Ec_search_gcplus(type, 'meta');
        });
    });

    $('body').on('click','#table_balisealt li a.pagination-items-page', function() {
        pagination = $(this).attr('data-items');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '17',
                type: type,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                pagination : pagination,
                id_shop : ec_id_shop
            }),
            dataType: "json"
        })
        .done(function (data) {
            Ec_search_ba();
        });
    });

    $('body').on('change','#table_balisealt .conf', function() {
        Ec_search_ba();
    });
    $(document).on("keypress", "#table_balisealt input.conf", function(e){
        if(e.which == 13){
            Ec_search_ba();
        }
    });
    
    $('body').on('click','.table_meta_info li a.pagination-items-page', function() {
        pagination = $(this).attr('data-items');
        type = $(this).parent().parent().attr('data-type');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '17',
                type: type,
                tok : EC_TOKEN_SEO,
                ajax : 1,
                pagination : pagination,
                id_shop : ec_id_shop
            }),
            dataType: "json"
        })
        .done(function (data) {
            Ec_search_gcplus(type, 'meta');
        });
    });

    $('#EC_SEO_FRONT_IP').after('<button type="button" class="btn btn-primary add_ip_button">'+ec_add_ip+'</button>');
    $('body').on('click','.add_ip_button', function() {
        current = $('#EC_SEO_FRONT_IP').val();
        if (current.length == 0) {
            val = ec_current_ip;
        } else {
            val = $('#EC_SEO_FRONT_IP').val()+';'+ec_current_ip;
        }
        $('#EC_SEO_FRONT_IP').val(val);
    });
    iso = 'fr';
    if ($('#metageneratorproductgen_form').length) {
        if ($('#metageneratorproductgen_form button.btn.dropdown-toggle:first').length) {
            iso = $('#metageneratorproductgen_form button.btn.dropdown-toggle:first').html().trim().split('<i')[0].trim();
        }
        
    }
    if ($('#meta_spe_product_form').length) {
        if ($('#meta_spe_product_form button.btn.dropdown-toggle:first').length) {
            iso = $('#meta_spe_product_form button.btn.dropdown-toggle:first').html().trim().split('<i')[0].trim();
        }
    }

    if ($('[id^=mi_]').length) {
        if ($('[id^=mi_] button.btn.dropdown-toggle:first').length) {
            iso = $('[id^=mi_] button.btn.dropdown-toggle:first').html().trim().split('<i')[0].trim();
        }
    }

    id_lang = tab_ec_seo_iso[iso];
    var class_active_gen = 'product';
    var ec_current_id_lang = id_lang;
    $('body').on('click','.ec_seo_variables a', function(e) {
        e.preventDefault();
        $(this).html().trim();
        $("#ec_copy").show().val($(this).html().trim())[0].select();
        document.execCommand("copy");
        $("#ec_copy").hide();
        showNoticeMessage($('#m_vcopied').val());
    }); 
    tab_type = ['product', 'category', 'cms', 'manufacturer', 'supplier'];
    var tab_typeB = {
        'product' : 'Products',
        'category' : 'Categories',
        'cms' : 'CMS',
        'manufacturer' : 'Manufacturers',
        'supplier' : 'Suppliers',
    };
    for (i in tab_type) {
        type = tab_type[i];
        $('#ec_footerseo_'+type).append($('#menuec_seo_footer_'+type));
        $('#ec_footerseo_'+type).append($('#footerseo'+type+'gen_form'));
        $('#ec_footerseo_'+type).append($('#form-ec_ListBlockFooterGen'+type));
        $('#ec_footerseo_'+type).append($('#form-ec_ListFooterSpe'+type));
        $('#form-ec_ListFooterSpe'+type).hide();
        $('#ec_meta_'+type).append($('#menuec_seo_meta_'+type));
        $('#ec_meta_'+type).append($('#meta_generator_variables_'+type));
        //$('#ec_meta_'+type).append($('#meta_spe_'+type+'_form'));
        $('#ec_meta_'+type).append($('#metagenerator'+type+'gen_form'));
        $('#ec_meta_'+type).append($('#form-ec_list_meta_'+type));
        $('#ec_meta_'+type).append($('.taskMeta'+type).clone());
        $('#ec_meta_'+type).append($('.tablebackupMeta'+tab_typeB[type]).clone());
        $('#ec_meta_'+type+' .tablebackupMeta'+tab_typeB[type]).addClass('panel');
        $('#ec_meta_'+type+' .tablebackupMeta'+tab_typeB[type]).prepend('<h3>'+ec_trad_history+'</h3>');
        //$('#meta_spe_'+type+'_form').hide();
        $('#form-ec_list_meta_'+type).hide();
        $('#ec_mi_'+type).append($('form[id^=configMI_'+type+'_form]'));
        $('#ec_mi_'+type).append($('#form-ec_list_mi_'+type));
        $('#ec_mi_'+type).append($('.taskIm'+type).clone());
        $('#ec_mi_'+type).append($('.tablebackupInternalMesh'+tab_typeB[type]).clone());
        $('#ec_mi_'+type+' .tablebackupInternalMesh'+tab_typeB[type]).addClass('panel');
        $('#ec_mi_'+type+' .tablebackupInternalMesh'+tab_typeB[type]).prepend('<h3>'+ec_trad_history+'</h3>');
        $('#table_meta_'+type).insertAfter('#ec_meta_'+type+' .taskMeta'+type);
        $('#table_mi_'+type).insertAfter('#ec_mi_'+type+' .taskIm'+type);

        
    }
    $('#balisealt_form').append($('.tablebackupImageAlt').clone());
    $('#balisealt_form .tablebackupImageAlt').addClass('panel');
    $('#balisealt_form .tablebackupImageAlt').prepend('<h3>'+ec_trad_history+'</h3>');

    $('#backup_config_form').insertBefore('#backup_form');
    $('form[id^=report_config_form]').prependTo('#report_form');
    $('#prev_form').insertBefore($('#ec_meta_product .taskMetaproduct'));
    $('#redirection_form form.defaultForm').removeClass('col-lg-8');
    $( "#menuec_seo .list-group-item a,#menuec_seo .list-group-item a.sub-item" ).each(function() {
        if ($(this).parent().hasClass('active') || $(this).hasClass('active')) {
            hashtag = window.location.href.split('#');
            ht = 'product';
            if (hashtag[1]) {
                ht = hashtag[1];
            }
            spe = false;
            if (hashtag[2]) {
                spe = true;
            }
            if ($(this).attr('id')) {
                showForm($(this).attr('id'), ht, spe);
            }
            
            $(this).parents('.list-group-item').addClass('active');
            
            
        }
    });
    
    $("form[id^=form-ec_List]").each(function() {
        th = $(this).find('table th span');
        form = $(this);
        indexActive = false;
        position = false;
        th.each(function(index) {
            if ($(this).html().trim() == 'Active') {
                indexActive = index+1;
            }
            if ($(this).html().trim() == 'Position') {
                position = true;
            }
        });
        if (indexActive != false) {
            td = $(this).find('table tbody tr td:nth-child('+indexActive+')');
            td.each(function() {
                if ($(this).html().trim()=='1') {
                    $(this).html('<i class="icon-check" style="color:#72C279;"></i>');
                } else {
                    $(this).html('<i class="icon-remove" style="color:#E08F95;"></i>');
                }
            });
        }
        if (position) {
            tbody = $(this).find('table tbody');
            tbody.hover(function() {
                $(this).css("cursor","move");
            });
            tbody.sortable({
                revert: true,
                id: test,
                distance: 5,
                delay: 300,
                opacity: 0.6,
                cursor: 'move',
                update: function() {
                    table = $(this)[0].offsetParent;
                    position_block = new Object();
                    th = $(table).find('th span');
                    table_id = $(table).attr('id');
                    majsel = 20;
                    if(table_id.indexOf('BlockLink') != -1){
                        majsel = 21;
                    }
                    indexPosition = false;
                    th.each(function(index) {
                        if ($(this).html().trim() == 'Position') {
                            position = true;
                            indexPosition = index+1;
                        }
                    });
                    td = $(table).find('tbody tr td:nth-child(1)');
                    cpt = 1;
                    td.each(function() {
                        id_block = $(this).html().trim();
                        $(this).parent().find('td:nth-child('+indexPosition+')').html(cpt);
                        position_block[id_block] = cpt;
                        cpt++;
                    });
                    $.ajax({
                        url: ec_seo_ajax,
                        type: "POST",
                        data: ({
                            majsel: majsel,
                            position_block: position_block,
                            tok : EC_TOKEN_SEO,
                            ajax : 1,
                        }),
                        dataType: "json"
                    })
                    .done(function (data) {
                        showNoticeMessage(ec_mess_update);
                    });
                }
            });
            tbody.disableSelection();
        }
    });

    $("form[id^=form-ec_ListFooterSpe] table tbody tr td:nth-child(1)").each(function() {
        id_footer = $(this).html().trim();
        if (ec_tab_link_preview_footer[id_footer]) {
            //console.log($(this).parent().find('.btn-group-action .dropdown-menu'));
            $(this).parent().find('.btn-group-action .dropdown-menu').append('<li class="divider"><li><a href="'+ec_tab_link_preview_footer[id_footer]+'" target="_blank" title="'+ec_mess_preview+'" class=""><i class="process-icon-preview" style="margin: 0;margin-right: 3px;display: inline-block;width: auto;height: auto;"></i>'+ec_mess_preview+'</a></li>');
            //$(this).parent().find('.btn-group-action .dropdown-menu').html('');
        }

    });  
    $('body').on('click','#menuec_seo .list-group-item >  a', function() {
        target = $(this).attr('id');
        showForm(target);
        $(this).parents('.list-group-item').addClass('active');
    });

    $('body').on('click','#menuec_seo_meta a', function() {
        
        $('#menuec_seo_meta a').removeClass('active');
        $(this).addClass('active');

        tclass = $(this).attr('data-type');

        $('#menuec_seo .list-group-item').removeClass('active');
        showForm('menuMetaGenerator', tclass);
        $(this).parents('.list-group-item').addClass('active'); 

        class_active_gen = tclass;
        $('.ec_meta_generator').hide();
        $('#ec_meta_'+tclass).show();
        cleanPreview();

        $('#prev_form').insertBefore($('#ec_meta_'+tclass+' .taskMeta'+tclass));
    });

    $('body').on('click','.menuec_seo_meta a', function() {
        tclass = $(this).attr('data-type');
        $('#menuec_seo_meta_'+tclass+' a').removeClass('active');
        $(this).addClass('active');
        id = $(this).attr('data-id');
        $('#metagenerator'+tclass+'gen_form').hide();
        $('#form-ec_list_meta_'+tclass).hide();
        
        if (id == 'gen') {
            $('#metagenerator'+tclass+'gen_form').show();
        } else {
            $('#form-ec_list_meta_'+tclass).show();
        }
        
    });

    $('body').on('click','#menuec_seo_mi a', function() {
        $('#menuec_seo_mi a').removeClass('active');
        $(this).addClass('active');
        tclass = $(this).attr('data-type');

        $('#menuec_seo .list-group-item').removeClass('active');
        showForm('menuinternalmesh', tclass);
        $(this).parents('.list-group-item').addClass('active'); 

        $('.ec_mi_generator').hide();
        $('#ec_mi_'+tclass).show();
    });

    $('body').on('click','#menuec_seo_footer a', function() {
        
        $('#menuec_seo_footer a').removeClass('active');
        $(this).addClass('active');

        tclass = $(this).attr('data-type');

        $('#menuec_seo .list-group-item').removeClass('active');
        showForm('menuFooterSeo', tclass);
        $(this).parents('.list-group-item').addClass('active'); 

        class_active_gen = tclass;
        $('.ec_footerseo').hide();
        $('#ec_footerseo_'+tclass).show();
    });

    $('body').on('click','.menuec_seo_footer a', function() {
        tclass = $(this).attr('data-type');
        $('#menuec_seo_footer_'+tclass+' a').removeClass('active');
        $(this).addClass('active');
        id = $(this).attr('data-id');
        $('#footerseo'+tclass+'gen_form').hide();
        $('#form-ec_ListBlockFooterGen'+tclass).hide();
        $('#form-ec_ListFooterSpe'+tclass).hide();
        /* $('#metagenerator'+tclass+'gen_form').hide();
        $('#form-ec_list_meta_'+tclass).hide();
        */
        if (id == 'gen') {
            $('#footerseo'+tclass+'gen_form').show();
            $('#form-ec_ListBlockFooterGen'+tclass).show();
        } else {
            $('#form-ec_ListFooterSpe'+tclass).show();
            
        } 
        
    });

    $('body').on('click','#menuec_config a', function() {
        $('#menuec_config a').removeClass('active');
        
        tclass = $(this).attr('data-menu');

        $('#menuec_seo .list-group-item').removeClass('active');
        showForm(tclass);

        $(this).addClass('active');
        $(this).parents('.list-group-item').addClass('active'); 

    });

    $('body').on('click','#menuec_redirection a', function() {
        $('#menuec_redirection a').removeClass('active');
        
        tclass = $(this).attr('data-menu');

        $('#menuec_seo .list-group-item').removeClass('active');
        showForm(tclass);

        $(this).addClass('active');
        $(this).parents('.list-group-item').addClass('active'); 

    });

    $('body').on('click','.deletebackup', function() {
        file = $(this).attr('data-file');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: {
                majsel: 6,
                file: file,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'html',
            success: function (data) {
                refreshBackUp();
            }
        });
    });

    $('body').on('click','.deletereport', function() {
        file = $(this).attr('data-file');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: {
                majsel: 6,
                file: file,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'html',
            success: function (data) {
                refreshReport();
            }
        });
    });

    $('body').on('click','#btn-to-prev', function() {
        id = $('#id_to_prev').val();
        spe = $('#spe_prev').val();
        if (spe) {
            tclass = $('#spe_prev').attr('data-tclass');
            meta_title = $('#meta_spe_'+tclass+'_form #meta_title_'+ec_current_id_lang).val();
            ty_class = tclass;
            meta_description = $('#meta_spe_'+tclass+'_form #meta_description_'+ec_current_id_lang).val();
        } else {
            meta_title = $('#metagenerator'+class_active_gen+'gen_form #meta_title_'+ec_current_id_lang).val();
            meta_description = $('#metagenerator'+class_active_gen+'gen_form #meta_description_'+ec_current_id_lang).val();
            ty_class = class_active_gen;
        }
        
        $.ajax({
            url: ec_seo_ajax,
            data: {
                majsel: 7,
                class: ty_class,
                id: id,
                meta_title: meta_title,
                meta_description: meta_description,
                id_lang: ec_current_id_lang,
                id_shop: ec_id_shop,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'json',
            success: function (data) {
                $('#meta_title_prev').val(data.meta_title);
                $('#meta_description_prev').val(data.meta_description);
            }
        });
    });

    $('body').on('click','#content .dropdown-menu li a', function() {
        if ($(this).attr('href')) {
            id_lang = parseInt($(this).attr('href').split('(')[1]);
            ec_current_id_lang = id_lang;
        }
    });
    //Robots
  /*   $('body').on('click','#ec_seo_edit_robot', function() {
        content = $("#ec_robottxt").val();
        $.ajax({
            ec_seo_ajax,
            data: {
                majsel: 10,
                robot: content,
                id_shop: ec_id_shop,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'html',
            success: function (data) {
                location.reload();
            }
        });
    }); */


    
    $('body').on('click','#testrobot a', function() {
        url = $('#urlrobot').val();
        domain_id_shop = $('select#ec_domains').val();
        if (domain_id_shop == ec_ps_shop_default) {
            domain_id_shop = '';
        }
        if (url.length == 0) {
            alert($('#mess_enterurl').val());
        } else {
            $.ajax({
                url: ec_seo_ajax,
                data: {
                    majsel: 15,
                    url: url,
                    id_shop: ec_id_shop,
                    domain_id_shop: domain_id_shop,
                    tok: EC_TOKEN_SEO,
                    ajax : 1
                },
                dataType: 'html',
                success: function (data) {
                    if (data == 1) {
                        showNoticeMessage($('#mess_url_not_blocked').val());
                    } else {
                        showErrorMessage($('#mess_url_blocked').val());
                    }
                }
            });
        }
        
    });

    $('body').on('click','#gotorobot', function(e) {
        e.preventDefault();
        $('#menurobot').click();
    });

    if ($('select#ec_domains').length > 0) {
        domain_id_shop = $('select#ec_domains').val();
        $('#robot_form form').hide();
        $('#robot_form form#robottxt'+domain_id_shop+'_form').show();
    }

    $('body').on('change','select#ec_domains', function() {
        domain_id_shop = $(this).val();
        $('#robot_form form').hide();
        $('#robot_form form#robottxt'+domain_id_shop+'_form').show();
    });

    $('body').on('click','#dash_report button', function() {
        $('#dash_report .progress').show();
        updateProgExcel();
    });

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
        })();
    var updateProgExcel = function(t_delay = 5000) {    
        delay(function(){
            var current = 0
            var total = 0
            $.ajax({
                url: ec_seo_ajax,
                data: {
                    majsel: 19,
                    tok: EC_TOKEN_SEO,
                    ajax : 1
                },
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        current = parseInt(data.current);
                        total = parseInt(data.total);
                        perc = parseInt((parseInt(current)/parseInt(total))*100);
                        $('#dash_report .progress-bar').html(perc+'%');
                        $('#dash_report .progress-bar').attr('valuenow', perc);
                        $('#dash_report .progress-bar').css('width', perc+'%');
                        if (current < total) {
                            updateProgExcel(500);
                        } else {
                            date = data.date;
                            $('#dash_report #ec_full_report').attr('href', data.last_report);
                            $('#dash_report .ec_date_last_report').html(date);
                        }
                    }
                    
                }
            });
            
            	
        }, t_delay );}
        
    $('#table-ec_ListPageCMSNoIndex tbody tr td:nth-child(1)').each(function(){
        id_cms = $(this).html().trim();
        
        td_index = $(this).next().next().next();
        if (td_index.html().trim() == 1) {
            td_index.html('<a href="#" class="desactIndexCms" data-id_cms="'+id_cms+'" title="'+ec_mess_disabled+'"><i class="icon-check" style="color:#72C279;"></i></a>')
        } else {
            td_index.html('<a href="#" class="activeIndexCms" data-id_cms="'+id_cms+'" title="'+ec_mess_enabled+'"><i class="icon-remove" style="color:#E08F95;"></i></a>')
        }
    });

    $('body').on('click','.desactIndexCms', function(e) {
        e.preventDefault();
        id_cms = $(this).attr('data-id_cms');
        a = $(this);
        $.ajax({
            url: ec_seo_ajax,
            data: {
                majsel: 41,
                id_cms: id_cms,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'html',
            success: function (data) {
                a.parent().html('<a href="#" class="activeIndexCms" data-id_cms="'+id_cms+'" title="'+ec_mess_enabled+'"><i class="icon-remove" style="color:#E08F95;"></i></a>');
                showNoticeMessage(ec_mess_update);
            }
        });
    });

    $('body').on('click','.activeIndexCms', function(e) {
        e.preventDefault();
        id_cms = $(this).attr('data-id_cms');
        a = $(this);
        $.ajax({
            url: ec_seo_ajax,
            data: {
                majsel: 42,
                id_cms: id_cms,
                tok: EC_TOKEN_SEO,
                ajax : 1
            },
            dataType: 'html',
            success: function (data) {
                a.parent().html('<a href="#" class="desactIndexCms" data-id_cms="'+id_cms+'" title="'+ec_mess_disabled+'"><i class="icon-check" style="color:#72C279;"></i></a>');
                showNoticeMessage(ec_mess_update);
            }
        });
    });
});

function showForm(target, hasgtag = 'product', spe = false) {
    target = target.replace('menu', '');
    updateOnglet(target);
    $('#menuec_seo .list-group-item').removeClass('active');
    $('#menuec_seo .submenu .sub-item').removeClass('active');
    $('#balisealt_form, #robot_form, #config_form, #redirection_form, .ec_meta_generator, .ec_footerseo, .ec_mi_generator, #backup_config_form, #task_form, #backup_form, #report_form, [id^=ec_seo_config_redirectimage], #form-ec_seo_redirectimage, [id^=opengraph_form], #prev_form, form[id^=report_config_form], #form-ec_ListBockHtml, #form-ec_ListPageNoIndex, #form-ec_ListPageCMSNoIndex').hide();
    $('#dashboard_form').removeClass('active');
    if (target == 'MetaGenerator') {
        //$('#menuec_seo_meta').show();
        $('#ec_meta_'+hasgtag).show();
        $('#prev_form').show();
        $('#menuec_seo_meta a').removeClass('active');
        $('#menuec_seo_meta a[data-type="'+hasgtag+'"]').addClass('active');
        //console.log( $('#menuec_seo_meta a[data-type="'+hasgtag+'"]').attr('class'));
        //$('#menuec_seo_meta a[data-type="'+hasgtag+'"]').click();
    }
    if (target == 'FooterSeo') {
        if (hasgtag == 'product') {
            hasgtag = 'category';
        }
        //$('#menuec_seo_meta').show();
        $('#ec_footerseo_'+hasgtag).show();
        //$('#prev_form').show();
        $('#menuec_seo_footer a').removeClass('active');
        $('#menuec_seo_footer a[data-type="'+hasgtag+'"]').addClass('active');
        if (spe) {
            $('#menuec_seo_footer_'+hasgtag+' a').removeClass('active');
            $('#menuec_seo_footer_'+hasgtag+' a[data-id="spe"]').addClass('active');
            $('#footerseo'+hasgtag+'gen_form').hide();
            $('#form-ec_ListBlockFooterGen'+hasgtag).hide();
            $('#form-ec_ListFooterSpe'+hasgtag).hide();
            $('#form-ec_ListFooterSpe'+hasgtag).show();
        }
    }
    if (target == 'BlockHtml') {
        $('#form-ec_ListBockHtml').show();
    }
    if (target == 'PageNoIndex') {
        $('#form-ec_ListPageNoIndex').show();
        $('#form-ec_ListPageCMSNoIndex').show();
    }
    if (target == 'internalmesh') {
        //$('#menuec_seo_mi').show();
        $('#ec_mi_'+hasgtag).show();
        $('#menuec_seo_mi a').removeClass('active');
        $('#menuec_seo_mi a[data-type="'+hasgtag+'"]').addClass('active');
        //$('#menuec_seo_mi a[data-type="'+hasgtag+'"]').trigger('click');
    }
    if (target == 'RedirectionImage') {
        $('#form-ec_seo_redirectimage').show();
        $('[id^=ec_seo_config_redirectimage]').show();
    }
    if (target == 'Backup') {
        $('#backup_config_form').show();
        refreshBackUp();
    }
    if (target == 'Report' || target == 'report') {
        $('form[id^=report_config_form]').show();
        refreshReport();
    }
    if (target == 'OpenGraph') {
        $('[id^=opengraph_form]').show();
    }
    if (target.toLowerCase() != 'dashboard') {
        target = target.replace('Sub', '');
        target = target.replace('sub', '');
        $('#'+target.toLowerCase()+'_form').show();
    } else {
        /* à l'affichage du dashboard */
        $('#'+target.toLowerCase()+'_form').addClass('active');
        initGauge('gauge_meta_title');
        $('.gauge').each(function(){
            initGauge($(this).attr('id'),$(this).data('score'));
        });
    }
    if (target.toLowerCase() == 'config') {
        $('#menuConfigsub').addClass('active');
    }
    if (target.toLowerCase() == 'redirection') {
        $('#menuRedirectionsub').addClass('active');
    }
}
function refreshBackUp() {
    $.ajax({
        url: ec_seo_ajax,
        data: {
            majsel: 5,
            tok: EC_TOKEN_SEO,
                ajax : 1
        },
        dataType: 'html',
        success: function (data) {
            $('#backup_form').html(data);
        }
    });
}
function refreshReport() {
    $.ajax({
        url: ec_seo_ajax,
        data: {
            majsel: 8,
            tok: EC_TOKEN_SEO,
                ajax : 1
        },
        dataType: 'html',
        success: function (data) {
          /*   conteneur = $('<div></div>');
            page = $(data).appendTo(conteneur);
            $('#report_form .reportlist').html(page.find('.reportlist').html());
            $('#report_form .taskReport').html(page.find('.taskReport').html()); */
            $('#report_form').html(data);
        }
       
    });
}
function cleanPreview()
{
    $('#id_to_prev').val('');
    $('#meta_title_prev').val('');
    $('#meta_description_prev').val('');
}

function initGauge(id, score) {
    if (ec_hasReport) {
        var opts = {
            angle: 0, // The span of the gauge arc
            lineWidth: 0.40, // The line thickness
            radiusScale: 0.5, // Relative radius
            pointer: {
              length: 0.45, // // Relative to gauge radius
              strokeWidth: 0.035, // The thickness
              color: '#000000' // Fill color
            },
            limitMax: false,     // If false, max value increases automatically if value > maxValue
            limitMin: false,     // If true, the min value of the gauge will be fixed
            colorStart: '#6FADCF',   // Colors
            colorStop: '#8FC0DA',    // just experiment with them
            strokeColor: '#E0E0E0',  // to see which ones work best for you
            generateGradient: true,
            highDpiSupport: true,     // High resolution support
            staticZones: [
                {strokeStyle: "red", min: 0, max: 25}, 
                {strokeStyle: "#ffa500", min: 25, max: 50}, 
                {strokeStyle: "yellow", min: 50, max: 75}, 
                {strokeStyle: "#0c6", min: 75, max: 100}, 
            ],
          };
          var target = document.getElementById(id); // your canvas element
          var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
          gauge.maxValue = 100; // set max gauge value
          gauge.setMinValue(0);  // Prefer setter over gauge.minValue = 0
          gauge.animationSpeed = 26; // set animation speed (32 is default value)
          gauge.set(score); // set actual value
    }
}

function updateOnglet(target)
{
    $.ajax({
        url: ec_seo_ajax,
        data: {
            majsel: 18,
            target: target,
            id_employee: ec_id_employee,
            tok: EC_TOKEN_SEO,
                ajax : 1
        },
        dataType: 'html',
        success: function (data) {
        }
    });
}