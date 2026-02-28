/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*/

// console.log('Magic Init');

// Save last section visited before unload (refresh, save...)
// window.addEventListener('beforeunload', (e) => {
//     e.preventDefault();
//     function setCookie(cname, cvalue, exp) {
//         const d = new Date();
//         d.setTime(d.getTime() + (exp*1000));
//         let expires = "expires="+ d.toUTCString();
//         document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
//     }
//     let url = $('#module-nav a.active').prop('href');
//     setCookie(prefix+'last_menu', url.match('#([0-9a-zA-Z-_]*)')[1], 25);
//     return;
// });

// This variant doesn't generate the save message
window.onbeforeunload = function() {
    let url = $('#module-nav a.active').prop('href');
    const d = new Date();
    const exp = 25;
    d.setTime(d.getTime() + (exp*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = prefix+'last_menu' + "=" + url.match('#([0-9a-zA-Z-_]*)')[1] + ";" + expires + ";path=/";
}
document.addEventListener('DOMContentLoaded', function() {
    // Additional Display- show more than 1 section
    let add_dis = {'#no_delivery_days' : '#fieldset_20_20_20'};
    // Ignore section. do not create a navigable menu for that section
    let ignore_panels = ['fieldset_20_20_20']
    // console.log($('#module-body > .panel .panel-heading'));
    // Create the Left menú
    var panels = '';
    var searchTab;
    if ($('#module-body > .panel .panel-heading').length > 0) {
        //console.log('init cool menus');
        panels = $('#module-body .panel').filter( function() {
            return $(this).parents('.panel').length == 0;
        });
        var modContent;
        if ($('#module-body').length > 0) {
            modContent = $('#module-body').append('<div id="module-nav" class="productTabs col-lg-2 col-md-3"><div class="list-group"></div></div>');
        } else {
            modContent = panels.first().before('<div id="module-nav" class="productTabs col-lg-2 col-md-3"><div class="list-group"></div></div>');
        }
        $('#module-nav').after('<div id="module-content" class="col-lg-10 col-md-9"></div>');
        $('#module-body form').appendTo('#module-content');

        //console.log(panels);
        panels.each(function(i, e) {
            if (typeof $(this).attr('id') == 'undefined') {
                $(this).attr('id', 'fieldset_'+i+'_'+i+'_'+i);
            }
            // If the parent element isn't a form move it.
            if ($(this).parents('form').length == 0) {
                $(this).appendTo('#module_form');
            }
        });
        /* Add elements, force the position in the menu if configured. */
        /* Build the navigation menu */
        let el = [];
        panels.find('.panel-heading:first').each(function() { // WAS $('#module-content .panel-heading')
            let id = $(this).parent('.panel').attr('id');
            if (!ignore_panels.includes(id)) {
                $('.productTabs .list-group').append('<a class="list-group-item" href="#' + id + '">' + $(this).html() + '</a>');
            }
        });

        // insert the alerts for "Process previous orders"
        $('.ddw-alert').insertBefore($('#module-content').find('#delayed_delivery_warning').find('.form-wrapper'));
        // Initialize the tabs
        $('.productTabs a:first').addClass('active');
        panels.hide();
        panels.first().show();

        // Keep track of the selected menu
        $('#content form').each(function() {
            $(this).append('<input type="hidden" name="selected_menu" value="">');
        });

        $('.productTabs a').click(function(e) {
            $('.productTabs a').removeClass('active');
            $(this).addClass('active');
            e.preventDefault();
            var searchTab = $(this).attr('href');
            panels.hide();
            $(searchTab).show();
            //console.log(searchTab);
            //console.log(add_dis);
            //console.log(add_dis[searchTab]);
            if (typeof add_dis[searchTab] != undefined) {
                $(add_dis[searchTab]).show();
            }
            // Update the selected menu
            $('input[name="selected_menu"]').val(searchTab);
            // Get the content for the sections when number of categories is too large
            if ($(searchTab + ' .ajax-replace').length > 0) {
                if (typeof ajaxAddContent === 'function') {
                    ajaxAddContent($(searchTab + ' .ajax-replace').data());
                }
            }
        });
        $(document).on('mousedown', '.tab-pane .nav-link', function() {
            var searchTab = $(this).attr('href');
            if ($(searchTab + ' .ajax-replace').length > 0) {
                if (typeof ajaxAddContent === 'function') {
                    ajaxAddContent($(searchTab + ' .ajax-replace').data());
                }
            }
        });
    }
    // PS 1.5. Versions
    if ($('.fieldset legend').length > 0) {
        var form_pos = 100;
        panels = $('#module-body fieldset, #module-body form');
        for (var i = 0; i < panels.length; i++) {
            if (panels[i].tagName == 'FORM') {
                var form_pos = i;
            }
        }
        panels.each(function(i, e) {
            if (form_pos > i) {
                $(this).prependTo('#module-body form');
            } else {
                $(this).appendTo('#module-body form');
            }
        });

        $('form#module_form').addClass('form-horizontal col-lg-10 col-md-9');
        $('form#module_form').before('<div class="productTabs col-lg-2 col-md-3" style="margin-top:11px"><ul class="tab"></ul></div></div>');
        $('fieldset legend').each(function() {
            $('.productTabs .tab').append('<li class="tab-row"><a class="list-group-item tab-page" href="#'+$(this).parent('fieldset').attr('id')+'">'+$(this).html()+'</a></li>');
        });
        $('.productTabs a:first').addClass('selected');
        $('fieldset:not(:first)').hide();
        $('.productTabs a').click(function(e) {
            $('.productTabs a').removeClass('selected');
            $(this).addClass('selected');
            e.preventDefault();
            var searchTab = $(this).attr('href');
            $('fieldset').hide();
            $(searchTab).show();
        });
    }
    selectLastMenu(panels);

    /* Target menu to directly access the section */
    $(document).on('click', '.target-menu', function() {
        var dest = $(this).attr('href');
        $('#module-nav').find('a').each(function() {
            if ($(this).attr('href') === dest) {
                $(this).click();
                window.scrollTo(0,0);
                return;
            }
        });
    });

    function selectLastMenu(panels)
    {
        var to_select = '';
        let cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            if (cookie) {
                let val = cookie.match('=([0-9a-zA-Z-_]*)')[1];
                if (cookie.indexOf(prefix + 'last_menu') !== -1 && val != '') {
                    to_select = val;
                    break;
                }
            }
        }
        if (to_select === '') {
            if (window.location.hash != '') {
                to_select = window.location.hash;
            } else if (typeof selected_menu !== 'undefined' && selected_menu != '') {
                to_select = selected_menu;
            }
        }
        if (to_select != '') {
            $('#module-nav a').each(function() {
                if ($(this).attr('href').indexOf(to_select) !== -1) {
                    $(this).click();
                    window.scrollTo(0,0);
                    return false;
                }
            });
        } else {
            $('#module-nav a').first().click();
        }
    }
    function getSorted(selector, attrName) {
        return $($(selector).toArray().sort(function(a, b){
            var aVal = parseInt(a.getAttribute(attrName)),
                bVal = parseInt(b.getAttribute(attrName));
            return aVal - bVal;
        }));
    }
    function ajaxAddContent(fields) {
        let mode = fields.id.split('_');
        var module = getUrlVars()['configure'];
        jQuery.ajax({
            dataType: "JSON",
            url: decodeURI(ed_ajax_url),
            data: {
                ajax: 1,
                action: 'ContentReplace',
                id : fields.id,
                input_name: fields.inputName,
                selected_cat: fields.selectedCat,
                ed_token: fields.token,
            },
        }).done(function (data, textStatus, jqXHR) {
            var dest = '';
            if ($('#' + fields.id).length > 0) {
                dest = '#' + fields.id
            } else if ($('#' + fields.id + '-nav').length > 0) {
                dest = '#' + fields.id + '-nav';
            } else {
                console.log('Destination not found');
                return false;
            }
            var r = $(data.return); // create a jQuery element to work with
            $(dest).html(r.html()); // Update the code without destroying the element in panel
            $(dest).show();
            // Update dynamic selections
            if (dest.indexOf('tree_categories_panel') !== -1) {
                enableAutocomplete();
            } else if (dest.indexOf('ed_cat_exclude') !== -1) {
                setExcludedCategories();
            }
            // Update the delay generation
            setDelays();
            // Update panels var
            panels_wo_forms = $('#content .panel');
            // Update the check on max input vars
            checkInputVarsLimit();
            if (typeof fields.callback !== 'undefined') {
                $(fields.callback).trigger('change');
            }
            if (typeof fields.callbackFn !== 'undefined') {
                // Dynamic function
                fields.callbackFn;
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // console.log(jqXHR);
            // console.log(textStatus);
            // console.log(errorThrown);
        });
    }
    function getUrlVars() {
        var vars = {};
        var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
            vars[key] = value;
        });
        return vars;
    }
    function checkInputVarsLimit() {
        /* Check Max input vars */
        var inputs = $('#content input').length;
        var recommended = (parseInt((inputs+350)/1000)*1000) + 1000;
        if ($('#content input').length > input_limit) {
            if ($('#content #max_input_vars').length == 0) {
                $('#max_input_warning').html($('#max_input_warning').html().split('#1').join('<span class="max_input_vars">1000</span>').replace('#2', '<span class="max_input_vars_rec">1000</span>')).prependTo('#content').show();
            }
            $('.max_input_vars').html(inputs);
            $('.max_input_vars_rec').html(recommended);
        }
    }
    $(document).ready(function() {
        checkInputVarsLimit();
    });
});