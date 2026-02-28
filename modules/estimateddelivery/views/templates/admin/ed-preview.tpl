{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
*}

<div id="ed_wrap">
    <div id="ed-display-1" class="ed-display" {if $ed_style != 1}style="display:none"{/if}>
    <h4>{l s='Carriers display' mod='estimateddelivery'}</h4>
        <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
            <h4>{l s='Order now and receive it' mod='estimateddelivery'}...</h4>
            <p>{l s='Between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span></p>
            <p>{l s='Between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Another carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price2|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span></p>
            
        </div>
    </div>
    <div id="ed-display-2" class="ed-display" {if $ed_style != 2}style="display:none"{/if}>
    <h4>{l s='Order Before... Style' mod='estimateddelivery'} ({l s='countdown' mod='estimateddelivery'})</h4>
    <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
        <p class="ed_orderbefore">{l s='Order it before' mod='estimateddelivery'} <span class="ed_countdown">2 {l s='hours' mod='estimateddelivery'} {l s='and' mod='estimateddelivery'} 50 {l s='minutes' mod='estimateddelivery'}</span> {l s='and receive it between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span></p>
    </div>
    </div>
    <div id="ed-display-3" class="ed-display" {if $ed_style != 3}style="display:none"{/if}>
    <h4>{l s='Order Before... Style' mod='estimateddelivery'} ({l s='time to picking' mod='estimateddelivery'})</h4>
    <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
        <p class="ed_orderbefore">{l s='Order it before' mod='estimateddelivery'} <span class="ed_countdown">13:00</span> {l s='and receive it between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span></p>
    </div>
    </div>
    <div id="ed-display-4" class="ed-display" {if $ed_style != 4}style="display:none"{/if}>
        <h4>{l s='Dislplay Picking time  mode' mod='estimateddelivery'}</h4>
        <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
            <p class="ed_orderbefore">{l s='Sent on' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span></p>
        </div>
    </div>
    <div id="ed-display-5" class="ed-display" {if $ed_style != 5}style="display:none"{/if}>
        <h4>{l s='Double Display Mode' mod='estimateddelivery'}</h4>
        <div class="hide-priority-options" style="display:none">
            <div id="ed-double-display-1">
                <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
                    <p class="ed_orderbefore">
                        <span class="ed_title">{$sorting_title[1]|escape:'htmlall':'UTF-8'}: </span>
                        {l s='Receive it between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span>
                    </p>
                </div>
            </div>
            <div id="ed-double-display-2">
                <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
                    <p class="ed_orderbefore">
                        <span class="ed_title">{$sorting_title[2]|escape:'htmlall':'UTF-8'}: </span>
                        {l s='Receive it between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span>
                    </p>
                </div>
            </div>
            <div id="ed-double-display-3">
                <div class="{$ed_class|escape:'htmlall':'UTF-8'} estimateddeliverypreview">
                    <p class="ed_orderbefore">
                        <span class="ed_title">{$sorting_title[3]|escape:'htmlall':'UTF-8'}: </span>
                        {l s='Receive it between' mod='estimateddelivery'} <span class='datemin'>{$datemin_default|escape:'htmlall':'UTF-8'}</span> {l s='and' mod='estimateddelivery'} <span class="datemax">{$datemax_default|escape:'htmlall':'UTF-8'}</span> {l s='with' mod='estimateddelivery'} <strong>{l s='Our amazing carrier' mod='estimateddelivery'}</strong> <span class="ed_price"><span class="ed_price_prefix"></span>{$price1|escape:'htmlall':'UTF-8'}<span class="ed_price_suffix"></span></span>
                    </p>
                </div>
            </div>
        </div>
        <div class="display-priority display-priority-1"></div>
        <div class="display-priority display-priority-2"></div>
    </div>
</div>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        let datemin = 0;
        let datemax = 2;
        let index = $('#ED_DATE_TYPE').val();
        $('#ed_wrap').appendTo('#previewstyle');
        var dates = {$dates nofilter};
        {*
        /*
        var datemin = {$datemin nofilter};
        var datemax = {$datemax nofilter};
        */
            *}
        var classes = ['','ed_lightblue','ed_softred','ed_lightgreen','ed_lightpurple','ed_lightbrown','ed_lightyellow','ed_orange','custom'];
        var headpx =  $('.page-head').height();
        if ($('#header_infos').length > 0) {
            headpx  += $('#header_infos').height();
            
        } else if ($('.navbar-header').length > 0) { 
            headpx += $('.navbar-header').height();
        }
        $(document).on('change', "#ED_STYLE", function()
        {
                $(".ed-display").hide();
                updateDates($('#ED_DATE_TYPE').val(), true);
                $("#ed-display-"+$(this).val()).show();
        });
        $(document).on('change', "#ED_DISPLAY_PRIORITY, #ED_DISPLAY_PRIORITY_2", function()
        {
            updatePriorities($(this), $(this).val());
        });
        $(document).on('change', "#ED_DATE_TYPE", function()
        {
            updateDates($(this).val(), false);
        });
        $(document).on('change', "#ed_class", function()
        {
            if ($(this).val() != 'custom')
            {
                $(".estimateddeliverypreview").css({ 'background-color': '', 'border-color': ''});
                $(".estimateddeliverypreview").attr('class', 'estimateddeliverypreview');
                $(".estimateddeliverypreview").addClass($(this).val());
            }
            else
            {
                updatevalue();
            }
        });
        function updatevalue()
        {
            $(".estimateddeliverypreview").css({ 'background-color': $("#color_0").val(), 'border-color': $("#color_1").val()});
            $(".estimateddeliverypreview").attr('class', 'estimateddeliverypreview');
        }
        $("#color_0").bind('input propertychange change' ,function() {
            updatevalue();
        });
        $("#color_1").bind('input propertychange change' ,function() {
            updatevalue();
        });
        if ($("#ed_class").val() == 'custom') {
            updatevalue();
        }
        function updateDates(i, rand = false, target = '') {
            if (rand) {
                datemin = getRandomInt(0,3);
                datemax = getRandomInt(2,5);
            }
            if (target !== '') {
                target += ' ';
            }
            $(target + ".datemin").html(dates[i][datemin]);
            $(target + ".datemax").html(dates[i][datemax]);
            index = i;
        }

        updatePriorities($('#ED_DISPLAY_PRIORITY'), $('#ED_DISPLAY_PRIORITY').val());
        updatePriorities($('#ED_DISPLAY_PRIORITY_2'), $('#ED_DISPLAY_PRIORITY_2').val());
        function updatePriorities(e, priority_id) {
            let target = e.prop('id').indexOf('_2') >= 0 ? 2 : 1;
            $(".display-priority-" + target).html($('#ed-double-display-' + priority_id).clone());
            updateDates(index, true, ".display-priority-" + target);
        }
        $(window).scroll(function() {
            if ($('#ed_wrap').is(':visible')) {
                if ($(window).scrollTop() + headpx > $('#previewstyle').offset().top) {
                    if (!$('#ed_wrap').hasClass('sticky')) {
                        $('#ed_wrap').addClass('sticky');
                        updatePreviewWidth();
                    }
                    $('#ed_wrap').css('top', headpx);
                } else { 
                    if ($('#ed_wrap').hasClass('sticky')) {
                        $('#ed_wrap').removeClass('sticky');
                    }
                }
            }
        });
        $(window).resize(function() {
            updatePreviewWidth();
        });
        updatePrice();
        updatePriceWrappers();
        function updatePreviewWidth()
        {
            if ($('#ed_wrap').is(':visible')) {
                $('#ed_wrap').width($('#ed_wrap').parent().width());
            }
        }

        $(document).on('change', "#ed_disp_price_on, #ed_disp_price_off", function() {
            updatePrice();
        });
        $(document).on('keydown keyup', "#ed_price_prefix, #ed_price_suffix", function() {
            updatePriceWrappers();
        });
        function updatePrice() {
            if ($('input[type="radio"][name="ed_disp_price"]:checked').val() == "1") {
                if (!$('span.ed_price').is(':visible')) {
                    $('span.ed_price').show();
                }
            } else {
                if ($('span.ed_price').is(':visible')) {
                    $('span.ed_price').hide();
                }
            }
        }
        function updatePriceWrappers()
        {
            $('span.ed_price_prefix').text($('#ed_price_prefix').val());
            $('span.ed_price_suffix').text($('#ed_price_suffix').val());
        }
        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min) + min); // The maximum is exclusive and the minimum is inclusive
        }
    });
</script>
