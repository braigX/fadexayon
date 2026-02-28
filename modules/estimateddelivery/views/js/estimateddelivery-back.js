/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 3.5.4
 * @category Transport & Logistics
 * Registered Trademark & Property of Smart-Modules.prpo
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.5.4                      *
 * ***************************************************
*/

/* Sort the menu items section 2.1, 2.2, 2.3, 2.4... */
document.addEventListener('DOMContentLoaded', function() {
    //console.log($('#module_form > #basic'));
    $("#ed_documentation").insertBefore("#module_form > #basic");
    $("#ed_cat_picking").insertAfter("#module_form > #picking_advanced");
    $("#ed_cat_oos").insertAfter("#module_form > #ed_cat_picking");
    $("#ed_cat_exclude").insertAfter("#module_form > #ed_cat_oos");
    $("#ed_cat_custom").insertAfter("#module_form > #ed_cat_exclude");
    $("#carrier_delivery_zone").insertAfter("#module_form > #carrier_delivery");
    $("#ed_undefined_deliveries").insertAfter("#module_form > #ed_cat_custom");
    $("#ed_warehouse").insertAfter("#module_form > #ed_undefined_deliveries");
});
function compare(a, b) {
   if (a === b) {
      return 0;
   }
   var a_components = a.split(".");
   var b_components = b.split(".");
   var len = Math.min(a_components.length, b_components.length);
   // loop while the components are equal
   for (var i = 0; i < len; i++) {
       // A bigger than B
       if (a_components[i] > b_components[i]) {
           return 1;
       }
        // B bigger than A
       if (a_components[i] < b_components[i]) {
           return -1;
       }
   }
    // If one's a prefix of the other, the longer one is greater.
   if (a_components.length > b_components.length) {
       return 1;
   }
    if (a_components.length < b_components.length) {
       return -1;
   }
    // Otherwise they are the same.
   return 0;
}

$(document).ready(function() {
    if (compare(_PS_VERSION_,'1.6') != -1) {
        if($("#ed_class").val() != 'custom') {
            $(".ed_custombg").parent().parent().parent().parent().parent().parent().hide();
            $(".ed_customborder").parent().parent().parent().parent().parent().parent().hide();
        }
        //$("#fieldset_0 .form-wrapper").append('<div id="previewstyle"></div>'); 
        $("#ed_class").change(function() {
            if($(this).val() == 'custom')
                showColorPickers(false);
            else
                showColorPickers(true);
        })
   
        function showColorPickers(hide) {
            if (hide)
            {        
                $("#color_0").parent().parent().parent().parent().parent().parent().hide();
                $("#color_1").parent().parent().parent().parent().parent().parent().hide();
            }
            else
            {
                $("#color_0").parent().parent().parent().parent().parent().parent().show();
                $("#color_1").parent().parent().parent().parent().parent().parent().show();
            }    
        }
       
        $('.smooth').click(function(){
            $('html, body').animate({
                scrollTop: $( $.attr(this, 'href') ).offset().top-100
            }, 1000);
            return false;
        });
    }
    else
    {
        // It's 1.5
        //console.log('1.5');
        if($("#ed_class").val() != 'custom')
        {
            $(".ed_custombg").parent().hide();
            $(".ed_custombg").parent().prev().hide();
            $(".ed_customborder").parent().hide();
            $(".ed_customborder").parent().prev().hide();
        }
           
        //$("#fieldset_0 input:last").parent().before('<div class="margin-form"><div id="previewstyle"></div></div>'); 
        //$("#fieldset_0").css('position', 'relative');
        
        $("#ed_class").change(function()
        {
            if($(this).val() == 'custom')
                showColorPickers(false);
            else
                showColorPickers(true);
        })
   
        function showColorPickers(hide) {
            if (hide) {
            $(".ed_custombg").parent().hide();
            $(".ed_custombg").parent().prev().hide();
            $(".ed_customborder").parent().hide();
            $(".ed_customborder").parent().prev().hide();
            } else {
            $(".ed_custombg").parent().show();
            $(".ed_custombg").parent().prev().show();
            $(".ed_customborder").parent().show();
            $(".ed_customborder").parent().prev().show();
            }
        }
       
        $('.smooth').click(function(){
            $('html, body').animate({
                scrollTop: $( $.attr(this, 'href') ).offset().top-100
            }, 1000);
            return false;
        });
    }

    // ajax procesure for revewing the past orders
    var running = false;
    $('.ajaxcall-review-past-orders').on('click', function(e) {
        e.preventDefault();
        if (running === true) {
            return false;
        }
        running = true;

        showNoticeMessage(review_warning);
        $.ajax({
            url: `${this.href}&ajax=1&action=ReviewPastOrders`,
            context: this,
            dataType: 'json',
            async: false,
            cache: 'false',
            success(res) {
                running = false;
                showSuccessMessage(ajax_message_ok);
            },
            error(res) {
                running = false;
                showErrorMessage(ajax_message_ko);
            },
        });
        return false;
    });
    /* Procedure to be able to select all subcategoríes in Category tree From 1.6 onwards */
    
    // Init tree with all subcategories shown.
    // Init tree with all subcategories shown.
    if (cat_count < max_cat_allowed) {
        setTimeout(function () {
            if (jQuery().tree && (typeof cat_count !== 'undefined')) {
                // Only open all sub categories if the cat count is less than 150, to prevent performance issues
                $('#categories-treeviewcat_exclude').tree('expandAll');
                $('#collapse-all-categories-treeviewed_cat_exclude').show();
                $('#expand-all-categories-treeviewed_cat_exclude').hide();
                $('#categories-treeviewed_cat_picking').tree('expandAll');
                $('#collapse-all-categories-treeviewed_cat_picking').show();
                $('#expand-all-categories-treeviewed_cat_picking').hide();
                $('#categories-treeviewed_cat_oos').tree('expandAll');
                $('#collapse-all-categories-treeviewed_cat_oos').show();
                $('#expand-all-categories-treeviewed_cat_oos').hide();
                $('#categories-treeviewed_cat_custom').tree('expandAll');
                $('#collapse-all-categories-treeviewed_cat_custom').show();
                $('#expand-all-categories-treeviewed_cat_custom').hide();
            }
        }, 1500);
    }

    $(document).on('click', '.cattree input', function() {
        if ($(this).parent().hasClass('tree-folder-name')) {
            //console.log($(this).prop('checked'));
            if ($(this).prop('checked') === false) {
                if (confirm(confirm_subtree_unselect_msg)) {
                    $(this).closest('li').find('input').prop('checked', false);
                }
            } else {
                if (confirm(confirm_subtree_select_msg)) {
                    $(this).closest('li').find('input').prop('checked', true);
                }
            }
        }
    });

    /* Validated 1.7 */
    /* Force selective carriers additional options */
    $(document).on('change', '#ED_ORDER_FORCE', showForceOptions);
    function showForceOptions() {
        if ($('#ED_ORDER_FORCE').val() != 2) {
            $('.force_carriers').hide();
        } else {
            $('.force_carriers').show();
        }
    }
    showForceOptions();


    // Display Summary extra options
    $(document).on('change', '#ED_ORDER_SUMMARY_PRODUCT_on, #ED_ORDER_SUMMARY_PRODUCT_off', function() {
        showSummaryHTML(!+$(this).val(), '#ED_ORDER_SUMMARY_LINE');
    });

    function showSummaryHTML(hide, selector) {
        let e = $(selector).closest('.form-group');
        if (hide) {
            e.hide();
        } else {
            e.show();
        }
    }
    showSummaryHTML($('#ED_ORDER_SUMMARY_PRODUCT_off').prop('checked'), '#ED_ORDER_SUMMARY_LINE');

    // Force ED_ORDER_SUMMARY according to the individual date option
    $(document).on('click', 'input[name="ED_DATES_BY_PRODUCT"]', forceOrderSummary);
    function forceOrderSummary() {
        if( $('#ED_DATES_BY_PRODUCT_on').prop('checked') == true ) {
            if ($('#ED_ORDER_SUMMARY').val() == 1) {
                $('#ED_ORDER_SUMMARY option[value="1"]').prop('disabled', true);
                $('#ED_ORDER_SUMMARY option[value="2"]').prop('selected', true);
            } else {
                if( $('#ED_ORDER_SUMMARY option[value="1"]').prop('disabled') == false ) {
                    $('#ED_ORDER_SUMMARY option[value="1"]').prop('disabled', true);
                }
            }
        } else {
            if( $('#ED_ORDER_SUMMARY option[value="1"]').prop('disabled') == true ) {
                $('#ED_ORDER_SUMMARY option[value="1"]').prop('disabled', false);
            }
        }
    }
    forceOrderSummary();

    $(document).on('change', '#ED_LOCATION', showCustomOptions);
    function showCustomOptions() {
        if ($('#ED_LOCATION').val() == 50) {
            $('.ed_cust_placement').show();
        } else {
            $('.ed_cust_placement').hide();
        }
    }
    showCustomOptions();

    // Display Summary extra options
    $(document).on('change', '#ED_DATE_TYPE', showCustomDateFormat);
    function showCustomDateFormat() {
        $('.ed_custom_format').hide();
        $('.ed_custom_format_regular').hide();

        // console.log($('#ED_DATE_TYPE').val());
        if ($('#ED_DATE_TYPE').val() == -1) {
            // console.log($('#ED_DATE_TYPE').val() == -1);
            $('.ed_custom_format').show();
        }
        if ($('#ED_DATE_TYPE').val() == -2) {
            $('.ed_custom_format_regular').show();
        }
    }
    showCustomDateFormat();

    /* Display extra options for countdown mode */
    $(document).on('change', '#ED_STYLE', showCountdownOptions);
    function showCountdownOptions() {
        if (![2, 3].includes(parseInt($('#ED_STYLE').val()))) {
            $('.ed_countdown_options').hide();
        } else {
            $('.ed_countdown_options').show();
        }
        if ($('#ED_STYLE').val() == 5) {
            $('.ed_display_secondary_option').show();
        } else {
            $('.ed_display_secondary_option').hide();
        }

        if ($('#ED_STYLE').val() == 3) {
            $('.time-to-picking-additional-info').show();
        } else {
            $('.time-to-picking-additional-info').hide();
        }
    }
    showCountdownOptions();
    
    // Advanced modes
    var adv_modes = {
        ed_adv_mode: '.ed_advanced_options',
        ed_picking_adv: '.ed_carrier_picking',
        ed_carrier_adv: '.ed_carrier_advanced',
        ED_ORDER: '.ed_order_options',
        ED_LIST: '.ed_list_options',
        ed_carrier_zone_adv: '.carrier-zones',
    };

    function enableDisableOptions(disable, selector) {
        $(selector + ' input, ' + selector + ' select, ' + selector + ' button').prop('disabled', disable);
        // Special for checklimit type
        $(selector + ' .checklimit').toggleClass('disabled');
        //console.log(selector);
    }

    for (const selector in adv_modes) {
        $(document).on('change', '#'+selector+'_on, #'+selector+'_off', function() {
            enableDisableOptions(!+$(this).val(), adv_modes[selector]);
        });
        if ($('#'+selector+'_off').prop('checked')) {
            enableDisableOptions(true, adv_modes[selector]);
        }
    }

    // Delayed Delivery Warning
    $(document).on('change', '#ed_enable_delayed_delivery_on, #ed_enable_delayed_delivery_off', function() {
        showHideDDRelatedSections(!+$(this).val());
    });
    if ($('#ed_enable_delayed_delivery_off').prop('checked')) {
        showHideDDRelatedSections(true);
    }

    function showHideDDRelatedSections(hide) {
        if (hide) {
            $('#module-nav [href="#cron_jobs"], .delayed_delivery_group').hide();
        } else {
            $('#module-nav [href="#cron_jobs"], .delayed_delivery_group').show();
        }
    }

    /* Copy to Clipboard Procedure */
    $(document).on('click', '.link_copy', function() {
        if (copyToClipboard($(this).data('url'))) {
            showSuccessMessage(ed_link_copied);
        }
    });
    $(document).on('click', 'input.click_to_copy', function() {
        if (copyToClipboard($(this).val())) {
            showSuccessMessage(ed_link_copied);
        }
    });
    const copyToClipboard = str => {
        const el = document.createElement('textarea'); // Create a <textarea> element
        el.value = str; // Set its value to the string that you want copied
        el.setAttribute('readonly', ''); // Make it readonly to be tamper-proof
        el.style.position = 'absolute';
        el.style.left = '-9999px'; // Move outside the screen to make it invisible
        document.body.appendChild(el); // Append the <textarea> element to the HTML document
        const selected =
            document.getSelection().rangeCount > 0 // Check if there is any content selected previously
                ?
                document.getSelection().getRangeAt(0) // Store selection if found
                :
                false; // Mark as false to know no selection existed before
        el.select(); // Select the <textarea> content
        document.execCommand('copy'); // Copy - only works as a result of a user action (e.g. click events)
        document.body.removeChild(el); // Remove the <textarea> element
        if (selected) { // If a selection existed before copying
            document.getSelection().removeAllRanges(); // Unselect everything on the HTML document
            document.getSelection().addRange(selected); // Restore the original selection
        }
        return true;
    };
    $(document).on('keyup', 'input.only-numbers', function() {
        $(this).val($(this).val().replace(/\D+/g, ''));
    });

    $(document).on('click', '.activate_setting', function(e) {
        e.preventDefault();
        let t = $(this).data('target');
        $(t + '_off').prop('checked', false);
        $(t + '_on').prop('checked', true);
        $(this).closest('li').fadeOut(1000);
        showSuccessMessage(remember_to_save);
    });

    $(document).on('click', '.force_select_value', function(e) {
        e.preventDefault();
        $($(this).data('target')).val($(this).data('value'))
        $(this).closest('li').fadeOut(1000);
        showSuccessMessage(remember_to_save);
    });

    $(document).on('click', '.dismiss-locale-check', function(e) {
        let ele = $(this);
        jQuery.ajax({
            // dataType: "JSON",
            url: decodeURI(ed_ajax_url),
            data: {
                ajax: 1,
                action: 'DismissLocaleCheck',
            },
        }).done(function (data, textStatus, jqXHR) {
            window.scrollTo(0,0);
            ele.parent().fadeOut(1000, function () {
                $(this).remove();
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // console.log(jqXHR);
            // console.log(textStatus);
            // console.log(errorThrown);
        });
    });

    /** Select an element from a list of options based on the data-target and data-value options */
    $(document).on('click', '.select_option', function(e) {
        e.preventDefault();
        let targetSelector = $(this).data('target');
        let valueToSelect = $(this).data('value');

        // Check if the target select element exists
        let $targetSelect = $(targetSelector);
        if ($targetSelect.length > 0) {
            let selectedIndex = $targetSelect.find('option[value="' + valueToSelect + '"]').index();

            // Set selectedIndex only if the option is found
            if (selectedIndex !== -1) {
                $targetSelect.prop('selectedIndex', selectedIndex).trigger('change');
                showSuccessMessage(remember_to_save);
            } else {
                console.error('Option with value "' + valueToSelect + '" not found in target select element.');
            }
        } else {
            console.error('Target select element "' + targetSelector + '" not found.');
        }
        return false;
    });

    // Add the Toggle Hook functions
    $('.hook-toggle').on('change', function() {
        const hookName = $(this).data('hook');
        const isEnabled = $(this).val();

        $.ajax({
            url: ed_ajax_url,  // Make sure this URL includes `&ajax=1`
            method: 'POST',
            data: {
                ajax: 1,
                action: 'toggleHook',
                hookName: hookName,
                isEnabled: isEnabled
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage(data.message);
                }
            },
            error: function() {
                showErrorMessage('An error occurred while toggling the hook.');
            }
        });
    });
});

/* ADD IP to Logger */
function addRemoteAddr(input_name) {
    var length = $('input[name='+input_name+']').attr('value').length;
    if (length > 0) {
        if ($('input[name='+input_name+']').attr('value').indexOf(remoteAddr) < 0) {
            $('input[name='+input_name+']').attr('value',$('input[name='+input_name+']').attr('value') + ',' + remoteAddr);
        }
    } else {
        $('input[name='+input_name+']').attr('value', remoteAddr);
    }
}

// TODO HERE continue with the set delay function. Review the variable if it holds multiple parameters or is created for each one. Check if it's possible to normalize the key variable to avoid excessive coding.

/* Dynamic panels data loading */
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function () { setDelays(); }, 2500);
});

let delay = {};
/*
// Init the flags
for (m in methods) {
    delay[m] = false;
}*/
function setDelays() {
    for (mode in methods) {
        if ($('#cat_tree_' + mode).length == 0 || delay[mode] !== undefined) {
            continue;
        }

        let s = '#cat_tree_' + mode;
        let sel  = s + ' .tree-folder-name, ' + s + ' .tree-item-name, #collapse-all-categories-treeviewed_cat_' + mode + ', #expand-all-categories-treeviewed_cat_' + mode;
        $(document).on('click', sel, {mode: mode}, function(e) {
            let m = e.data.mode;
            setTimeout(function() { setDelay(m, methods[m]) },1000);
        });
        // Set the initial data
        setDelay(mode, methods[mode]);
        delay[mode] = true;
    }
}
function setDelay(mode, column) {
    $('span.cat_'+ mode).remove();
    for (var i = 0; i < cat_delay.length; i++) {
        //console.log(cat_delay[i]['id_category'] + ' >> ' + cat_delay[i][column]+' ' + days_text);
        let sel = '#cat_tree_' + mode + ' input[value="'+cat_delay[i]['id_category']+'"]';
        let span = ' <span class="cat_'+ mode +'">('+cat_delay[i][column]+' ' + days_text + ')</span>';
        $(sel).parent().find('label').after(span);
    }
}