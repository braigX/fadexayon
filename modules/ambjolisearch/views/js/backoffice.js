/**
 *  @module     Advanced search (AmbJoliSearch)
 *  @file       ambjolisearch.php
 *  @subject    script principal pour gestion du module (install/config/hook)
 *  @copyright  Copyright (c) 2013-2023 Ambris Informatique SARL
 *  @license    Licensed under the EUPL-1.2-or-later
 *  Support by mail: support@ambris.com
 **/


$(document).ready(function(){

    callNextStep = function(elt, url) {
        $('.status').show();
        $.ajax({
            dataType: "json",
            async: true,
            url: url,
            method: 'GET',
            success: function(data) {

                if (typeof(data.status) != 'undefined'){
                    $('.status').html($('.rebuild-index').data('processing')+' '+data.status);
                }

                if (typeof(data.indexed) != 'undefined' && typeof(data.total) != 'undefined'){
                    $('.indexed-products').html(data.indexed+' / '+data.total);
                }

                if (typeof(data.url) != 'undefined' && data.url !== false) {
                    callNextStep(elt, data.url);
                } else {
                    $('.status').html($('.rebuild-index').data('done'));
                    elt.removeClass('waiting');
                    elt.find('.icon-refresh').removeClass('icon-spin');
                    elt.attr('disabled', false);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr);
            },
            always: function() {
                $(this).removeClass('icon-spin');
            }
        });
    }

    $(document).on('click', '.rebuild-index', function(e){
        e.preventDefault();
        $(this).addClass('waiting');
        $(this).find('.icon-refresh').addClass('icon-spin');
        $(this).attr('disabled', true);
        $('.status').html($('.rebuild-index').data('starting'));

        callNextStep($(this), $(this).data('url'), 0);
    });

});