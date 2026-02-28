/*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL Ether Création
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL Ether Création is strictly forbidden.
* In order to obtain a license, please contact us: contact@ethercreation.com
* ...........................................................................
* INFORMATION SUR LA LICENCE D'UTILISATION
*
* L'utilisation de ce fichier source est soumise a une licence commerciale
* concedee par la societe Ether Création
* Toute utilisation, reproduction, modification ou distribution du present
* fichier source sans contrat de licence ecrit de la part de la SARL Ether Création est
* expressement interdite.
* Pour obtenir une licence, veuillez contacter la SARL Ether Création a l'adresse: contact@ethercreation.com
* ...........................................................................
*  @package ec_seo
*  @copyright Copyright (c) 2010-2013 S.A.R.L Ether Création (http://www.ethercreation.com)
*  @author Arthur R.
*  @license Commercial license
*/
function Ec_seo_pagination(page, type) {
    //href = window.location.href;
//    var ec_page = "&ec_page";
//    href = href.split(ec_page);
//    window.location.href = href[0]+'&ec_page='+page;
    var id_shop = $('#id_shop').attr('rel');
    var tok = EC_TOKEN_SEO;
    var base = ec_ps_base;
    search = $('#search'+type+' .conf').serialize();
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '2',
                tok : tok,
                ajax : 1,
                page: page,
                type : type,
                search : search,
                id_shop : id_shop
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#fieldset.'+type).html(data);
        });
}
function Ec_search(type) {
    search = $('#search'+type+' .conf').serialize();
    var id_shop = $('#id_shop').attr('rel');
    var tok = EC_TOKEN_SEO;
    var base = ec_ps_base;
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '3',
                tok : tok,
                ajax : 1,
                type : type,
                search: search,
                id_shop : id_shop
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#fieldset.'+type).html(data);
        });
}

function Ec_resetsearch(type) {
    search = '';
    var id_shop = $('#id_shop').attr('rel');
    var tok = EC_TOKEN_SEO;
    var base = ec_ps_base;
    $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '3',
                tok : tok,
                ajax : 1,
                type : type,
                search: search,
                id_shop : id_shop
            }),
            dataType: "html"
        })
        .done(function (data) {
            $('#fieldset.'+type).html(data);
        });
}


$('document').ready( function() {
    

  /*   $('body').on('click','form a[id^=desc-ec_].list-toolbar-btn ', function(e) {
        e.preventDefault();
        id = $(this).attr('id');
        tclass = id.split('_')[1];
        $('#meta_spe_'+tclass+'_form').show();
    });

    $('body').on('click','a[id^=ecmetacancel_]', function() {
        id = $(this).attr('id');
        tclass = id.split('_')[1];
        $('#meta_spe_'+tclass+'_form').hide();
    }); */
    var tok = EC_TOKEN_SEO;
    var base = ec_ps_base;
    var id_shop = $('#id_shop').attr('rel');
    //Pagination
    $('body').on('click','#redirection_form li a.pagination-items-page', function() {
        pagination = $(this).attr('data-items');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '1',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                pagination : pagination
            }),
            dataType: "html"
        })
        .done(function (data) {
            location.reload();
        });
    });
    $('body').on('change','#fieldset .conf', function() {
        Ec_search($(this).parent().parent().attr('data-type'));
    });
    
    $(document).on("keypress", "#fieldset input.conf", function(e){
        if(e.which == 13){
            Ec_search($(this).parent().parent().attr('data-type'));
        }
    });
    
    $('body').on('click','.refresh', function() {
        var xhr = new XMLHttpRequest();
        var base = ec_ps_base;
        var id_shop = $('#id_shop').attr('rel');
        xhr.open("GET",base+'modules/ec_seo/cron.php?id_shop='+id_shop,false);
        xhr.send(null);	
        var onglet = $('#workTabs .active a').attr('href');
        window.location.href = ec_href +'&ok&onglet='+onglet[5];
    });
    
    $('body').on('click','.activeRedirect', function() {
       var id = $(this).attr('rel');
       var statut = $(this).parent().parent().parent().parent().parent().parent().find(' .act').attr('rel');
       var base = ec_ps_base;
       var tok = EC_TOKEN_SEO;
       var onglet = $('#workTabs .active a').attr('href');
       $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '23',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                onglet : onglet,
                activeRedirect : 1,
                id : id,
                statut : statut,
                onglet : onglet[5],
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
        
    });

    $('body').on('click','.suppRedirect', function() {
       var id = $(this).attr('rel');
       var lang = $('#ic_'+id+' .lan').attr('rel');
       var base = ec_ps_base;
       var tok = EC_TOKEN_SEO;
       var xhr = new XMLHttpRequest();
       var onglet = $('#workTabs .active a').attr('href');
       if ($('#ic_'+id).children(':first-child').css("backgroundColor") != 'rgb(128, 128, 128)'){
            $.ajax({
                url: ec_seo_ajax,
                type: "POST",
                data: ({
                    majsel: '24',
                    tok : tok,
                    ajax : 1,
                    id_shop : id_shop,
                    deleteRedirect : 1,
                    idL : lang,
                    id : id,
                }),
                dataType: "html"
            })
            .done(function (data) {
                window.location.href = ec_href +'&ok&onglet='+onglet[5];
            });
       } else {
            $.ajax({
                url: ec_seo_ajax,
                type: "POST",
                data: ({
                    majsel: '25',
                    tok : tok,
                    ajax : 1,
                    id_shop : id_shop,
                    reactivRedirect : 1,
                    idL : lang,
                    id : id,
                }),
                dataType: "html"
            })
            .done(function (data) {
            });
            $.ajax({
                url: ec_seo_ajax,
                type: "POST",
                data: ({
                    majsel: '26',
                    tok : tok,
                    ajax : 1,
                    id_shop : id_shop,
                    majStatut : 1,
                    id : id,
                }),
                dataType: "html"
            })
            .done(function (data) {
                window.location.href = ec_href +'&ok&onglet='+onglet[5];
            });
       }
    });
    
    $('body').on('click','.suppRedUrl', function(e) {
        e.preventDefault();
        var id = $(this).attr('rel');
        var tok = EC_TOKEN_SEO;
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '27',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                suppRedUrl : 1,
                id : id,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    $('body').on('click','.editRedirect', function() {
        var id = $(this).attr('rel');
        $(this).parent().parent().parent().parent().find(' .lienred #editionLien_'+id).show();
        $(this).parent().parent().parent().parent().find(' .lienred #displayLien_'+id).hide();
    });

    $('body').on('click','.majLien', function() {
        var tok = EC_TOKEN_SEO;
        var id = $(this).attr('rel');
        var lien = $(this).parent().find(' #lienRedirect').val();
        var onglet = $('#workTabs .active a').attr('href');
       $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '28',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                lienRedirect : 1,
                id : id,
                lien : lien,
                onglet : onglet[5],
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    $("[id^=display_rgb]").click(function(){
     var id = $(this).attr('id');
     tab = id.split('_');
     var rgb = tab[1];
     var nom_tab = tab[2];
     var oui = tab[3];
     if (oui == 'off'){
      $('td#'+nom_tab).each(function(index,value){
           if ($(this).css('background-color') == rgb){
                $(value).parent().hide();
           }
      });
     }
     else{
        $('td#'+nom_tab).each(function(index,value){
           if ($(this).css('background-color') == rgb){
                $(value).parent().show();
           }
      });
     }
    });
    $('body').on('change','.typeRedirect', function() {
        var tok = EC_TOKEN_SEO;
        var type = $(this).val();
        var id = $(this).attr('name');
        var onglet = $('#workTabs .active a').attr('href');

        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '29',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                typeRedirect : 1,
                id : id,
                type : type,
                onglet : onglet[5],
            }),
            dataType: "html"
        })
        .done(function (data) {
        });
        
        //window.location.href = ec_href +'&ok&onglet='+onglet[5];
    });
    $('body').on('click','.displayInactive', function() { 
        if ($(this).is(":checked")){
            $('tr').each(function(index,value){
                if (value.style.backgroundColor == 'grey'){
                    $(value).show();
                }
            });
        }else{
            $('tr').each(function(index,value){
                if (value.style.backgroundColor == 'grey'){
                    $(value).hide();
                }
            });
        }
    });
    $('body').on('click','[class^=submit_add]', function() {
        var clas = $(this).attr('class');
        var tab = clas.split('_');
        var type = tab[2];
        var TabLang = [];
        if($('#id_version').attr('rel') == 5)
            $('.translatable:first [class^=lang_]').each(function(i,v)
                { 
                    var lang = $(this).attr('class');
                    var res = lang.replace('lang_','')
                    TabLang.push(res);
                })
        else
            $('.translatable-field:first .dropdown-menu:first li').each(function(i,v)
            { 
                var lang = $(this).children().attr('href');
                var res = lang.replace('javascript:hideOtherLanguage(','')
                var lang = res.replace(');','');
                TabLang.push(lang);
            })
        if (!TabLang[0])
            TabLang[0] = 1;
        var tok = EC_TOKEN_SEO;
        var shop = ec_id_shop;
        var onglet = $('#workTabs .active a').attr('href');
        jQuery.each(TabLang, function(index,value){
            //alert(2);
            var id = $('#addID'+type).val();
            var lien = $('#addURL'+type+'_'+value).val();
            var name = $('#addName'+type+'_'+value).val();
            var redi = $('#addtypeRedirect'+type).val();
            var lang = value;  
            $.ajax({
                url: ec_seo_ajax,
                type: "POST",
                data: ({
                    majsel: '30',
                    tok : tok,
                    ajax : 1,
                    id_shop : id_shop,
                    addRedirect : 1,
                    id : id,
                    lien : lien,
                    name : name,
                    lang : lang,
                    redi : redi,
                    shop : shop,
                    type : type,
                    onglet : onglet[5],
                }),
                dataType: "html"
            })
            .done(function (data) {
            });
        })
        window.location.href = ec_href +'&ok&onglet='+onglet[5];

        
    });
    
    $('body').on('click','.submit_url_add', function(e) {
        e.preventDefault();
        var tok = EC_TOKEN_SEO;
        var base = ec_ps_base;
        var shop = ec_id_shop;
        var old_url = $('#addOldUrl').val();
        var new_url = $('#addNewUrl').val();
        var redi = $('#addtypeRedirectUrl').val();
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '31',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                addUrlRedirect : 1,
                old_url : old_url,
                new_url : new_url,
                redi :redi,
                shop :shop,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    $('body').on('click','.allChecked', function() {
        var type = $(this).attr('rel');
        $('.checkOn_'+type).each(function(index,value){
            var id = $(value).attr('rel');
            if ($('#ic_'+id).css("display") != 'none'){
                $(value).attr('checked',true);
            }
        })
    });
    
    $('body').on('click','.allUnchecked', function() {
        var type = $(this).attr('rel');
       $('.checkOn_'+type).each(function(index,value){
           $(value).attr('checked',false);
       })
    });

    $('body').on('click','.activeAllRedirect', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                var statut = $(this).parent().parent().find(' .act').attr('rel');
                if (statut == 0){
                    tab[id] = id;
                    i++;
                }
            }
        })
		var tok = EC_TOKEN_SEO;
        var onglet = $('#workTabs .active a').attr('href');

        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '32',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                activeAllRedirect : 1,
                statut : 0,
                onglet :onglet[5],
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });
    
    $('body').on('click','.desactiveAllRedirect', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                var statut = $(this).parent().parent().find(' .act').attr('rel');
                if (statut == 1){
                    tab[id] = id;
                }
            }
        })
		var tok = EC_TOKEN_SEO;
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '32',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                activeAllRedirect : 1,
                statut : 1,
                onglet :onglet[5],
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    $('body').on('click','.suppAllRedirect', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                if ($('#ic_'+id).children(':first-child').css("backgroundColor") != 'rgb(128, 128, 128)'){
                    tab[id] = id;
                }
            }
        })
        var onglet = $('#workTabs .active a').attr('href');
        var tok = EC_TOKEN_SEO;
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '33',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                deleteAllRedirect : 1,
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });
    
    $('body').on('click','.suppAllRedUrl', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                tab[id] = id;
            }
        })
        var onglet = $('#workTabs .active a').attr('href');
        var tok = EC_TOKEN_SEO;
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '34',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                suppAllRedUrl : 1,
                tab: tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });
    
    $('body').on('click','.restAllRedirect', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                if ($('#ic_'+id).children(':first-child').css("backgroundColor") == 'rgb(128, 128, 128)'){
                    tab[id] = id;
                }
            }
        })
        var tok = EC_TOKEN_SEO;
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '35',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                reactivAllRedirect : 1,
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    $('body').on('click','.editAllRedirect', function() {
        var type = $(this).parent().parent().children(':first-child').children(':first-child').attr('rel');
        $('.displaymajAll_'+type).show();
        $('.displaymajType_'+type).hide()
    });
    
    $('body').on('click','.editAllType', function() {
        var type = $(this).parent().parent().children(':first-child').children(':first-child').attr('rel');
        $('.displaymajType_'+type).show();
        $('.displaymajAll_'+type).hide();
    });

    $('body').on('click','.majAllLien', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                tab[id] = id;
            }
        })
        var lien = $('#lienAllRedirect_'+type).val();
        var tok = EC_TOKEN_SEO;
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '36',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                lienAllRedirect : 1,
                onglet : onglet[5],
                lien : lien,
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });

    
    $('body').on('click','.majAllType', function() {
        var type = $(this).attr('rel');
        tab = new Object();
        $('.checkOn_'+type).each(function(index,value){
            if ($(value).is(':checked')){
                var id = $(value).attr('rel');
                tab[id] = id;
            }
        })
        var tok = EC_TOKEN_SEO;
        var type = $(this).parent().find(' .typeRedirectAll').val();
        var onglet = $('#workTabs .active a').attr('href');
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '37',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                typeAllRedirect : 1,
                onglet : onglet[5],
                type : type,
                tab :tab,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });
    });
    
    $('body').on('click','.addForm', function() {
            var type = $(this).attr('rel');
            if ($('.displayAddForm_'+type).css('display','none'))
                $('.displayAddForm_'+type).show();
            else
                $('.displayAddForm_'+type).hide();
        });
    
    $('body').on('click','.hiddenAddForm', function() {
            var type = $(this).attr('name');
            $('.displayAddForm_'+type).hide();
        });



    $('[class*="RedirectLinkAutre"]').children().each(function(index,value){
        
        if ($(value).val() == 'category_default')
        {
            $('[class*="RedirectLinkAutre"]').children().eq(index).css('display','none');
        }
    });

    $('[class^=default_behavior]').change(function(){
   
        var base = ec_ps_base;
        var tok = EC_TOKEN_SEO;
        var type = this.id;
        var value = $(this).val();
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '38',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                default : 1,
                value : value,
                type : type,
            }),
            dataType: "html"
        })
        .done(function (data) {
        });
        displayLink();
    })

    $('body').on('click','[class^=submit_link]', function() {
        
        var base = ec_ps_base;
        var tok = EC_TOKEN_SEO;
        var clas = $(this).attr('class');
        var tab = clas.split('_');
         var action = tab[2];
         var obj = tab[3];
         var type = $('.updateLink_'+action+'_'+obj).attr('id');
         var value = $('.updateLink_'+action+'_'+obj).val();
         
        var onglet = $('#workTabs .active a').attr('href');  
        $.ajax({
            url: ec_seo_ajax,
            type: "POST",
            data: ({
                majsel: '38',
                tok : tok,
                ajax : 1,
                id_shop : id_shop,
                default : 1,
                value : value,
                type : type,
            }),
            dataType: "html"
        })
        .done(function (data) {
            window.location.href = ec_href +'&ok&onglet='+onglet[5];
        });   
        
    })
    
});

function displayLink()
{
    $('.default_behavior').each(function(){
        var input = this.id;
        if ($(this).val() == '404' || $(this).val() == '301' || $(this).val() == '302')
        {
            $('#input_'+input).show()
        }
        else
        {
            $('#input_'+input).hide()
        }
    })
}