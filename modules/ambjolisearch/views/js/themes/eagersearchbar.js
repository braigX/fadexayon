/**
 *  @module     Advanced search (AmbJoliSearch)
 *  @file       ambjolisearch.php
 *  @subject    script principal pour gestion du module (install/config/hook)
 *  @copyright  Copyright (c) 2013-2023 Ambris Informatique SARL
 *  @license    Licensed under the EUPL-1.2-or-later
 *  Support by mail: support@ambris.com
 **/

$(document).ready(function() {
    //search show/hide
    $(document).on('click', '.js-search-btn-toggle', function(e){
        $(this).toggleClass('search-active');
        $('body').toggleClass('search-fixed');
        setTimeout(function() {
            $('.search-block .input-text').focus();
        }, 500);
        e.preventDefault();
    });
});