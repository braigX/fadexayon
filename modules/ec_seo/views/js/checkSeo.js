$(document).ready(function () {
    if ($('#category')) {
        $( document ).ajaxComplete(function(event, xhr, settings) {
            if (settings.url) {
                url = settings.url;
                if (url.match(/page=/)) {
                    $("#ec_seo_description2").hide();
                } else {
                    $("#ec_seo_description2").show();
                }
            }
        });
    }
    
    cpt_img = 0;
    cpt_total_img = 0;
    $( "img" ).each(function() {
        if ($(this).attr('alt')) {
            if ($(this).attr('alt').length == 0) {
                cpt_img++;
            }
        }
        cpt_total_img++;
    });
    /* console.log(cpt_img);
    console.log(cpt_total_img); */
    if (cpt_img == 0) {
        content =$('.seo-txt-item.good.imagegood span.txt').html()+cpt_total_img+').';
        $('.seo-txt-item.good.imagegood span.txt').html(content);
        $('.seo-txt-item.good.imagegood').show();
    } else {
        content =cpt_img+' '+$('.seo-txt-item.bad.imagebad span.txt').html()+cpt_total_img+').';
        $('.seo-txt-item.bad.imagebad span.txt').html(content);
        $('.seo-txt-item.bad.imagebad').show();
    }

    open_graph = ['title','url','description','site_name','image','type','locale'];
    for (i in open_graph) {
        if ($('meta[property="og:'+open_graph[i]+'"]').attr('content')) {
            if ($('meta[property="og:'+open_graph[i]+'"]').attr('content').length == 0) {
                $('.seo-txt-item.bad.og'+open_graph[i]+'bad').show();
            } else {
                $('.seo-txt-item.good.og'+open_graph[i]+'good').show();
            }
        } else {
            $('.seo-txt-item.bad.og'+open_graph[i]+'bad').show();
        }
    }
    lexical = new Object();
    global_content = '';
    tab_h1 = [];
    cpt_h1 = 0;
    $( "h1" ).each(function() {
        content = $(this).html();
        global_content += ' '+content;
        tab_h1.push('"'+content.trim()+'"');
        cpt_h1++;
    });
    $('.cpt_h1').html(cpt_h1);
    if (cpt_h1 == 0) {
        $('.seo-txt-item.bad.baliseh1ine').show();
    } else if (cpt_h1 > 1) {
        $('.seo-txt-item.bad.baliseh1mul .h1_nb').html(cpt_h1);
        $('.seo-txt-item.bad.baliseh1mul .h1_content').html(tab_h1.join(', '));
        $('.seo-txt-item.bad.baliseh1mul').show();
    } else {
        $('.seo-txt-item.good.baliseh1ok').show();
    }

    cpt_h2 = $( "h2" ).length;

    $('.cpt_h2').html(cpt_h2);
    if (cpt_h2 == 0) {
        $('.seo-txt-item.bad.h2bad').show();
    } else if (cpt_h2 > 10) {
        $('.seo-txt-item.bad.h2bad2').show();
    } else {
        $('.seo-txt-item.good.h2good').show();
    }

    cpt_h3 = $( "h3" ).length;
    $('.cpt_h3').html(cpt_h3);
    if (cpt_h3 == 0) {
        $('.seo-txt-item.bad.h3bad').show();
    } else if (cpt_h3 > 10) {
        $('.seo-txt-item.bad.h3bad2').show();
    } else {
        $('.seo-txt-item.good.h3good').show();
    }
    
    cpt_h4 = $( "h4" ).length;
    $('.cpt_h4').html(cpt_h4);

    cpt_h5 = $( "h5" ).length;
    $('.cpt_h5').html(cpt_h5);

    cpt_h6 = $( "h6" ).length;
    $('.cpt_h6').html(cpt_h6);

    cpt_strong = $( "strong" ).length;
    if (cpt_strong == 0) {
        $('.seo-txt-item.bad.strongbad').show();
    } else {
        $('.seo-txt-item.good.stronggood').show();
    }


    if ($('.breadcrumb').length > 0) {
        $('.seo-txt-item.good.breadgood').show();
    } else {
        $('.seo-txt-item.bad.breadbad').show();
    }
    if (ec_ps_version17) {
        baseUri = prestashop.urls.base_url;
    }
    var re = new RegExp(baseUri, 'g');
    tab_interne = [];
    tab_doublon = [];
    temp_doublon = [];
    tab_no_follow = [];
    $( "a" ).each(function() {
        href = $(this).attr('href');
        if (href != '#') {
            rel = $(this).attr('rel');
            if (rel) {
                if (rel == 'nofollow') {
                    tab_no_follow.push(href);
                }
            }
            
            if (href) {
                if (href.match(re)) {
                    tab_interne.push(href);
                }
                if (temp_doublon[href]) {
                    if(jQuery.inArray(href, tab_doublon) == -1) {
                        tab_doublon.push(href);
                    }
                } else {
                    temp_doublon[href] = href;
                }
            }
        }
    });
    $('.ec_lien_int').html(tab_interne.length);
    $('.ec_lien_nf').html(tab_no_follow.length);
    $('.ec_lien_db').html(tab_doublon.length);
});