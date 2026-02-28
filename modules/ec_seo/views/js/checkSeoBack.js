/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
var ec_current_id_lang = false;
var category_rule = {
    'meta_title' : {'min':41, 'max': 65},
    'meta_description' : {'min':101, 'max': 200},
    'h1' : {'min':20, 'max': 100},
    'link_rewrite' : {'min':21, 'max': 100},
};
tab_score_h1 = {};
tab_score_meta_title = {};
tab_score_meta_desc = {};
tab_score_link_rewrite = {};
/* array(
    'meta_title' => array('min' => 46, 'max' => 65),
    'meta_description' => array('min' => 101, 'max' => 200),
    'h1' => array('min' => 20, 'max' => 100),
    'url' => array('min' => 21, 'max' => 100),
); */
$(document).ready(function () {
    var col_lg_label = 3;
    var col_lg = 9;
    if ($('#configuration_form .form-group .control-label').hasClass('col-lg-4')) {
        col_lg_label = 4; 
        col_lg = 8; 
    }
    $('.page-title').after('<div id="refreshDataKeyword"><button style="" class="btn btn-info"><i class="icon-refresh"></i>'+ec_trad_refreshDataKeyword+'</button><div>'+ec_trad_dateupdate+' '+dateUpdateDateKeyword+'</div></div>');
    $('body').on('keyup','#configuration_form [id^=h1]', function() {
        checkh1();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=meta_title]', function() {
        if (ec_type == 'cms' || ec_type == 'meta') {
            return;
        }
        checkMetaTitle();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=title]', function() {
        checkMetaTitle();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=head_seo_title]', function() {
        checkMetaTitle();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=meta_description]', function() {
        checkMetaDesc();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=description]', function() {
        if (ec_type != 'meta') {
            return;
        }
        checkMetaDesc();
        getScore(ec_current_id_lang);
    });
    
    $('body').on('keyup','#configuration_form [id^=link_rewrite]', function() {   
        checkLinkRewrite();
        getScore(ec_current_id_lang);
    });

    $('body').on('keyup','#configuration_form [id^=url_rewrite]', function() {   
        if (ec_type != 'meta') {
            return;
        }
        checkLinkRewrite();
        getScore(ec_current_id_lang);
    });
/* 
    $('body').on('keyup','#configuration_form [id^=description]', function() {
        checkDesc();
        getScore(ec_current_id_lang);
    }); */
    
    $('body').on('click','#configuration_form .dropdown-menu li a, #keyword_inp_grp .dropdown-menu li a', function() {
        id_lang = parseInt($(this).attr('href').split('(')[1]);
        showEcSeoLang(id_lang);
    });
    $('body').on('click','#keyword_inp_grp .btn', function() {
        
        keyword = $('#keyword_inp_'+ec_current_id_lang).val();
        ec_keyword = keyword;
        cleid = 'id_'+ec_type;
        data = ({
            updateKeyWord: 1,
            keyword: keyword,
            ec_id_lang: ec_current_id_lang
        });
        data[cleid] = ec_id;
        $.ajax({
            url: EcSeoController,
            type: 'POST',
            data: data,
            dataType: 'html'
        })
        .done(function (data) {
            /* $('#configuration_form').html(data);
            initLang();
            ec_tab_keyword[ec_current_id_lang] = keyword;
            getScore(ec_current_id_lang); */
            location.reload();
        });
    });
    
    txt = '';
    keywords = '';
    translatablefield = '';
    cpt = 0;
    for (const [id_lang, iso_lang] of Object.entries(tab_ec_seo_id)) {
        sdisplay = 'block';
        if (id_lang != ec_id_lang_default) {
            sdisplay = "none";
        }
        txt += '<div id="score-lang-'+id_lang+'" class="score-lang"><div class="laimg"><img src="'+ec_base_uri+'/img/tmp/lang_mini_'+id_lang+'_1.jpg"/></div><span class="ec_seo_score_'+id_lang+'"></span></div> ';
        keywords += "<input id='keyword_inp_"+id_lang+"' class='ec_keyword' value='"+ec_tab_keyword[id_lang]+"' type='text'>";
        if ($('#configuration_form button.btn.dropdown-toggle:first').length) {
            translatablefield += '<div class="translatable-field lang-'+id_lang+'" style="display:'+sdisplay+';"><div class="col-lg-2">'+$('.translatable-field.lang-'+id_lang+' div.col-lg-2').html()+'</div></div>';
        }
        cpt++;
    }
    html = "<div id='keyword_inp_grp'><label>"+txt+" Mot clé SEO : </label>"+keywords+"<span class='btn btn-secondary'>OK</span>"+translatablefield+"</div>";
    $(html).appendTo($('.page-head'));

    

    
    initLang();
    if ($('.'+ec_class_mini).length) {
        for (const [id_lang, iso_lang] of Object.entries(tab_ec_seo_id)) {
            score = getScore(id_lang);
        }
    }
    $('#configuration_form').before('<div id="ec_seo_loader" class="hide"><div class="ec_loader"></div><ul><li class="hide">'+ec_trad_searchK+'</li><li class="hide">'+ec_trad_analysis+'</li><li class="hide">'+ec_trad_retieving+'</li><li class="hide">...</li></ul></div>');
    $('body').on('click','#refreshDataKeyword button', function() {
        page_infos = new Object();
        for (const [id_lang, info] of Object.entries(tab_ec_seo)) {
            link = info.url.replace('%rewrite%', info.link_rewrite);
            page_info = {
                'link' : link,
                'keyword' : $('#keyword_inp_'+id_lang).val()
            };
            page_infos[id_lang] = page_info;
        }
        timeout2 = 5000;
        timeout3 = 10000;
        timeout4 = 15000;
        if (dateUpdateDateKeyword.length == 0) {
            timeout2 += 15000;
            timeout3 += 30000;
            timeout4 += 45000;
        }
        setTimeout(function(){
            $('#ec_seo_loader ul li:nth-child(1)').removeClass('hide');
          }, 500);

        setTimeout(function(){
            $('#ec_seo_loader ul li:nth-child(2)').removeClass('hide');
        }, timeout2);

        setTimeout(function(){
            $('#ec_seo_loader ul li:nth-child(3)').removeClass('hide');
        }, timeout3);
    
        setTimeout(function(){
            $('#ec_seo_loader ul li:nth-child(4)').removeClass('hide');
        }, timeout4);
        //console.log(page_infos);
        $('#ec_seo_loader').removeClass('hide');
        $.ajax({
            url: ec_seo_ajax,
            type: 'POST',
            data: {
                majsel: 22,
                tok: EC_TOKEN_SEO,
                page_infos: page_infos,
                page : ec_type,
                id : ec_id,
                ajax: 1
            },
            dataType: 'html'
        })
        .done(function (data) {
           /*  $('#ec_datakeyword').remove();
            $('#configuration_form').before(data);
            showEcSeoLang(ec_current_id_lang); */
            $('#ec_seo_loader').addClass('hide');
            $('#ec_seo_loader ul li').addClass('hide');
            location.reload();
        });
    });

    $(document).on({
        mouseenter: function() {
            $(this).children('.popup_info_keyword').show();    
        },
        mouseleave: function() {
            $(this).children('.popup_info_keyword').hide(); 
        }
    }, ".ec_reco_keyword");

    $('body').on('click','.ec_datakeyword_title_block i', function() {
        $(this).toggleClass('iclose');
        $(this).parent().next().slideToggle();
    });


    function showEcSeoLang(id_lang)
    {
        $('#configuration_form .ec_seo').hide();
        $('#configuration_form .ec_seo_preview').hide();
        $('#configuration_form .ec_rich_snippet').hide();
        $('#configuration_form .ec_breadcrumb').hide();
        $('.global_info_data_keywork').hide();
        $('#configuration_form .ec_seo_'+id_lang).show();
        $('#configuration_form #ec_seo_preview_'+id_lang).show();
        $('#configuration_form #ec_rich_snippet_'+id_lang).show();
        $('#configuration_form #ec_breadcrumb_'+id_lang).show();
        $('#global_info_data_keywork_'+id_lang).show();
        $('.ec_keyword').hide();
        $('#keyword_inp_'+id_lang).show();
        ec_current_id_lang = id_lang;
    }

    function initLang()
    {   
        for (i in tab_ec_seo_iso) {
            id_lang = tab_ec_seo_iso[i];
            meta_title = 'meta_title';
            if (ec_type == 'cms' && ec_ps_version17) {
                meta_title = 'head_seo_title';
            }
            if (ec_type == 'meta') {
                meta_title = 'title';
            }
            /* addhtml = '';
            temp_url = tab_ec_seo[id_lang]['url'];
            if (tab_ec_seo[id_lang]['url'].match(/\.html/i)) {
                temp_url = tab_ec_seo[id_lang]['url'].replace('.html', '');
                addhtml += '.html';
            } 
            link_rewrite_full = temp_url+tab_ec_seo[id_lang]['link_rewrite']+addhtml; */
            link_rewrite_full =  tab_ec_seo[id_lang]['url'].replace('%rewrite%', tab_ec_seo[id_lang]['link_rewrite']);
            content = '<div id="ec_seo_preview_'+id_lang+'" class="form-group ec_seo_preview" style="display:none;"><label class="control-label col-lg-'+col_lg_label+'">Prévisualisation SEO</label><div class="col-lg-'+col_lg+'"><div id="serp"><div class="serp-preview"><div class="serp-title">'+tab_ec_seo[id_lang]['meta_title']+'</div><div class="serp-url"><span class="ec_urlval">'+link_rewrite_full+'</span><span class="serp-arrow"></span></div><div class="serp-description">'+tab_ec_seo[id_lang]['meta_description']+'</div></div></div><small class="form-text">'+ec_trad_preview+'</small></div></div>';
            if (ec_seo_monolangue) {
                $('#'+meta_title+'_1').closest('.form-group').before(content)
            } else {
                $('#'+meta_title+'_1').parent().parent().parent().parent().parent().parent().before(content);
            }
            
            //Breadcrumb
            $('#configuration_form div.form-wrapper > div.form-group:last').after('<div style="display:none;" class="form-group ec_breadcrumb" id="ec_breadcrumb_'+id_lang+'"><label class="control-label col-lg-'+col_lg_label+'">'+ec_trad_breadcrumb+'</label><div class="col-lg-'+col_lg+'" style="padding-top: 8px;"><a target="_blank" href="'+link_rewrite_full+'">'+ec_mess_breadcrumb+'</a></div></div>');

            //Richsnippet
            $('#configuration_form div.form-wrapper > div.form-group:last').after('<div style="display:none;" class="form-group ec_rich_snippet" id="ec_rich_snippet_'+id_lang+'"><label class="control-label col-lg-'+col_lg_label+'">Rich snippet</label><div class="col-lg-'+col_lg+'" style="padding-top: 8px;"><a target="_blank" href="https://search.google.com/test/rich-results?url='+encodeURIComponent(link_rewrite_full)+'">https://search.google.com/test/rich-results?url='+encodeURIComponent(link_rewrite_full)+'</a></div></div>');
            checkMetaTitle(id_lang);
            checkLinkRewrite(id_lang);
            checkMetaDesc(id_lang);
            checkh1(id_lang);
        }
        if ($('#configuration_form button.btn.dropdown-toggle:first').length) {
            iso = $('#configuration_form button.btn.dropdown-toggle:first').html().trim().split('<i')[0].trim();
        }
        id_lang = ec_id_lang_default;
        showEcSeoLang(id_lang);
    }

    function checkh1(cu_lang = null)
    {
        if (ec_type == 'manufacturer' || ec_type == 'supplier' || ec_type == 'meta' || (ec_type == 'cms' && !ec_ps_version17)) {
            tab_score_h1[cu_lang] = 200;
            return;
        }
        if (cu_lang == null) {
            cu_lang = ec_current_id_lang;
        }
        score_h1 = 0;
        val_meta = $('#meta_title_'+cu_lang).val();
        val = $('#h1_'+cu_lang).val();
        if (ec_type == 'cms') {
            val = $('#meta_title_'+cu_lang).val();
            val_meta = $('#head_seo_title_'+cu_lang).val();
        }
        if (!val_meta) {
            $('#ec_seo_preview_'+cu_lang+' .serp-title').html(val);
        }
        
        
        if (val) {
            len = val.length;
            $('#ec_seo_h1_'+cu_lang+' span.h1_len_'+cu_lang).html(len);
            $('#ec_seo_h1_'+cu_lang+' .seo-txt-item').hide();
            if (len < category_rule.h1.min) {
                score_h1 += 70;
                $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.bad.h1min').show();
            } else if (len > category_rule.h1.max) {
                $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.bad.h1max').show();
                if (len < 201) {
                    score_h1 += 30;
                } else {
                    score_h1 += 10;
                }
            } else {
                $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.good.h1lenok').show();
                score_h1 += 100;
            }
        } else {
            $('#ec_seo_h1_'+cu_lang+' .seo-txt-item').hide();
            $('#ec_seo_h1_'+cu_lang+' span.h1_len_'+cu_lang).html(0);
            $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.bad.h1min').show();
        }

        keyword = ec_tab_keyword[cu_lang].split(' ');
        mots_h1 = val.split(' ');
        for (i in mots_h1) {
            mots_h1[i] = mots_h1[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|,/g, "").toLowerCase();
        }
        cpt = 0;
        mot_needed = [];
        for (i in keyword) {
            mot = keyword[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|,/g, "").toLowerCase();
            if(jQuery.inArray(mot, mots_h1) !== -1) {
                cpt++;
            } else {
                mot_needed.push('"'+mot+'"');
            }
        }
        if (cpt == keyword.length) {
            $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.good.keyword').show();
            score_h1 += 100;
        } else {
            $('#ec_seo_h1_'+cu_lang+' span.h1_motneeded_'+cu_lang).html(mot_needed.join(', '));
            $('#ec_seo_h1_'+cu_lang+' .seo-txt-item.bad.keyword').show();
            if (cpt == 0) {
                score_h1 += 10;
            } else {
                score_h1 += 50;
            }
        }
        tab_score_h1[cu_lang] = score_h1;
    }

    function checkMetaTitle(cu_lang = null)
    {
        if (cu_lang == null) {
            cu_lang = ec_current_id_lang;
        }
        meta_title = 'meta_title';
        if (ec_type == 'cms' && ec_ps_version17) {
            meta_title = 'head_seo_title';
        }
        if (ec_type == 'meta') {
            meta_title = 'title';
        }
        score_mt = 0;
        val = $('#'+meta_title+'_'+cu_lang).val();
        if (!val) {
            val = $('#h1_'+cu_lang).val();
            $('#ec_seo_preview_'+cu_lang+' .serp-title').html(val);
            val = 0;
        } else {
            $('#ec_seo_preview_'+cu_lang+' .serp-title').html(val);
        }
        
        
        if (val) {
            len = val.length;
            $('#ec_seo_meta_title_'+cu_lang+' span.meta_title_len_'+cu_lang).html(len);
            $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item').hide();
            if (len < category_rule.meta_title.min) {
                $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.bad.meta_titlemin').show();
                if (len < 20) {
                    score_mt+=20;
                } else {
                    score_mt+=70;
                }
                
            } else if (len > category_rule.meta_title.max) {
                $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.bad.meta_titlemax').show();
                score_mt+=20;
            } else {
                score_mt+=100;
                $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.good.meta_titlelenok').show();
            }
        } else {
            $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item').hide();
            $('#ec_seo_meta_title_'+cu_lang+' span.meta_title_len_'+cu_lang).html(0);
            $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.bad.meta_titlemin').show();
        }

        keyword = ec_tab_keyword[cu_lang].split(' ');
        mots_meta_title = [];
        if (val) {
            mots_meta_title = val.split(' ');
        }
        for (i in mots_meta_title) {
            mots_meta_title[i] = mots_meta_title[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|,/g, "").toLowerCase();
        }
        cpt = 0;
        mot_needed = [];
        for (i in keyword) {
            mot = keyword[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|\.|,/g, "").toLowerCase();
            if(jQuery.inArray(mot, mots_meta_title) !== -1) {
                cpt++;
            } else {
                mot_needed.push('"'+mot+'"');
            }
        }
        if (cpt == keyword.length) {
            $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.good.keyword').show();
            score_mt += 100;
        } else {
            $('#ec_seo_meta_title_'+cu_lang+' span.meta_title_motneeded_'+cu_lang).html(mot_needed.join(', '));
            $('#ec_seo_meta_title_'+cu_lang+' .seo-txt-item.bad.keyword').show();
            if (cpt == 0) {
                score_mt += 10;
            } else {
                score_mt += 50;
            }
        }
        tab_score_meta_title[cu_lang] = score_mt;
    }

    function checkMetaDesc(cu_lang = null)
    {
        if (cu_lang == null) {
            cu_lang = ec_current_id_lang;
        }
        meta_description = 'meta_description';
        if (ec_type == 'meta') {
            meta_description = 'description';
        }
        score_md = 0;
        val = $('#'+meta_description+'_'+cu_lang).val();
        if (!val) {
            val = '';
        }
        $('#ec_seo_preview_'+cu_lang+' .serp-description').html(val);

        if (val) {
            len = val.length;
            $('#ec_seo_meta_description_'+cu_lang+' span.meta_description_len_'+cu_lang).html(len);
            $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item').hide();
            if (len < category_rule.meta_description.min) {
                $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.bad.meta_descriptionmin').show();
                score_md +=30;
            } else if (len > category_rule.meta_description.max) {
                $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.bad.meta_descriptionmax').show();
                if (len < 300) {
                    score_md +=30;
                } else {
                    score_md +=10;
                }
                
            } else {
                $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.good.meta_descriptionlenok').show();
                score_md +=100;
            }
        } else {
            score_md +=30;
            $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item').hide();
            $('#ec_seo_meta_description_'+cu_lang+' span.meta_description_len_'+cu_lang).html(0);
            $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.bad.meta_descriptionmin').show();
        }

        keyword = ec_tab_keyword[cu_lang].split(' ');
        mots_meta_description = [];
        if (val) {
            mots_meta_description = val.split(' ');
        }
        
        for (i in mots_meta_description) {
            mots_meta_description[i] = mots_meta_description[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|,/g, "").toLowerCase();
        }
        /* console.log(val);
        console.log(mots_meta_description);
        console.log(keyword); */

        cpt = 0;
        mot_needed = [];
        for (i in keyword) {
            mot = keyword[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|\.|,/g, "").toLowerCase();
            if(jQuery.inArray(mot, mots_meta_description) !== -1) {
                cpt++;
            } else {
                mot_needed.push('"'+mot+'"');
            }
        }
        if (cpt == keyword.length) {
            $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.good.keyword').show();
            score_md += 100;
        } else {
            $('#ec_seo_meta_description_'+cu_lang+' span.meta_description_motneeded_'+cu_lang).html(mot_needed.join(', '));
            $('#ec_seo_meta_description_'+cu_lang+' .seo-txt-item.bad.keyword').show();
            if (cpt == 0) {
                score_md += 10;
            } else {
                score_md += 50;
            }
        }
        tab_score_meta_desc[cu_lang] = score_md;
    }

    function checkLinkRewrite(cu_lang = null)
    {
        if (ec_type == 'manufacturer' || ec_type == 'supplier') {
            tab_score_link_rewrite[cu_lang] = 200;
            return;
        }
        link_rewrite = 'link_rewrite';
        if (ec_type == 'meta') {
            link_rewrite = 'url_rewrite';
        }
        if (cu_lang == null) {
            cu_lang = ec_current_id_lang;
        }
        val = $('#'+link_rewrite+'_'+cu_lang).val();
        if (!val) {
            val = '';
        }
        score_lw = 0;
        
        /* addhtml = '';
        temp_url = tab_ec_seo[cu_lang]['url'];
        if (tab_ec_seo[cu_lang]['url'].match(/\.html/i)) {
            temp_url = tab_ec_seo[cu_lang]['url'].replace('.html', '');
            addhtml += '.html';
        } 
        link_rewrite_full = temp_url+val+addhtml; */
        link_rewrite_full =  tab_ec_seo[id_lang]['url'].replace('%rewrite%', val);
        $('#link_canonic_'+cu_lang).val(link_rewrite_full);
        $('#ec_seo_link_rewrite_'+cu_lang+' span.link_rewrite_full_'+cu_lang).html(link_rewrite_full);
        $('#ec_seo_preview_'+cu_lang+' .ec_urlval').html(link_rewrite_full);
        len = parseInt(parseInt(link_rewrite_full.length));
        $('#ec_seo_link_rewrite_'+cu_lang+' span.link_rewrite_len_'+cu_lang).html(len);
        $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item').hide();
        if (len < category_rule.link_rewrite.min) {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewritemin').show();
            score_lw+=50;
        } else if (len > category_rule.link_rewrite.max) {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewritemax').show();
            score_lw+=70;
        } else {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.good.link_rewritelenok').show();
            score_lw+=100;
        }

        if (link_rewrite_full.match(/_/i)) {
            //console.log('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewrite_barre');
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewrite_barre').show();
            score_lw-=10;
        }
        if (link_rewrite_full.match(/[*+éèàç'" ]/i)) {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewrite_spe').show();
            score_lw-=10;
        }
        count_slash = (link_rewrite_full.split('/').length)-1;
        if (count_slash > 5) {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewrite_sep').show();
            score_lw-=10;
        }
        /* } else {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item').hide();
            $('#ec_seo_link_rewrite_'+cu_lang+' span.link_rewrite_len_'+cu_lang).html(tab_ec_seo[cu_lang]['urllen']);
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.link_rewritemin').show();
        } */
        
        keyword = ec_tab_keyword[cu_lang].split(' ');
        mots_link_rewrite = val.split('-');
        cpt = 0;
        mot_needed = [];
        for (i in keyword) {
            mot = keyword[i].normalize("NFD").replace(/[\u0300-\u036f]|\.|\.|,/g, "").toLowerCase();
            if(jQuery.inArray(mot, mots_link_rewrite) !== -1) {
                cpt++;
            } else {
                mot_needed.push('"'+mot+'"');
            }
        }
        if (cpt == keyword.length) {
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.good.keyword').show();
            score_lw+=100;
        } else {
            $('#ec_seo_link_rewrite_'+cu_lang+' span.link_rewrite_motneeded_'+cu_lang).html(mot_needed.join(', '));
            $('#ec_seo_link_rewrite_'+cu_lang+' .seo-txt-item.bad.keyword').show();
            score_lw+=70;
        }
        tab_score_link_rewrite[cu_lang] = score_lw;
    }

    function checkDesc(cu_lang = null)
    {
        if (cu_lang == null) {
            cu_lang = ec_current_id_lang;
        }
        val = $('#description_'+cu_lang).val().text();
        //console.log(val);
        if (!val) {
            val = '';
        }
    }

    function getScore(id_lang)
    {
        /* console.log(tab_score_h1);
        console.log(tab_score_meta_title);
        console.log(tab_score_meta_desc);
        console.log(tab_score_link_rewrite); */
        score_desc = parseInt($('#ec_seo_desc_score_'+id_lang).val());
        //console.log(score_desc);
        total = ((tab_score_h1[id_lang]+tab_score_meta_title[id_lang]+tab_score_meta_desc[id_lang]+tab_score_link_rewrite[id_lang]+score_desc)/920)*100;
        //console.log((tab_score_h1[id_lang]+tab_score_meta_title[id_lang]+tab_score_meta_desc[id_lang]+tab_score_link_rewrite[id_lang]+score_desc));
        mycolor = 'red';
        if (parseInt(total) >= 80) {
            mycolor = 'green';
        } else if (parseInt(total) >= 50) {
            mycolor = 'yellow';
        } else if (parseInt(total) >= 25) {
            mycolor = 'orange';
        }
        $('.ec_seo_score_'+id_lang).html(parseInt(total)+'%').parents('.score-lang').removeClass('score-red');
        $('.ec_seo_score_'+id_lang).html(parseInt(total)+'%').parents('.score-lang').removeClass('score-orange');
        $('.ec_seo_score_'+id_lang).html(parseInt(total)+'%').parents('.score-lang').removeClass('score-yellow');
        $('.ec_seo_score_'+id_lang).html(parseInt(total)+'%').parents('.score-lang').removeClass('score-green');
        $('.ec_seo_score_'+id_lang).html(parseInt(total)+'%').parents('.score-lang').addClass('score-'+mycolor);
    }
});