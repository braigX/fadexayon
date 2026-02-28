{*
* @module       Advanced search (AmbJoliSearch)
* @file         configure.tpl
* @subject      template pour paramétrage du module sur le 'back office'
* @copyright    Copyright (c) 2013-2023 Ambris Informatique SARL (http://www.ambris.com/)
* @author       Richard Stefan (@RicoStefan)
* @license      Licensed under the EUPL-1.2-or-later
* Support by mail: support@ambris.com
*}
<div id="modulecontent" class="clearfix">

    <!-- Nav tabs -->
    <div class="col-lg-2">
        <div class="list-group">
            <a href="#dropdown_settings" class="list-group-item" data-toggle="tab">{l s='Dropdown list settings' mod='ambjolisearch'}</a>
            <a href="#results_page_settings" class="list-group-item" data-toggle="tab">{l s='Search results page settings' mod='ambjolisearch'}</a>
            <a href="#search_settings" class="list-group-item" data-toggle="tab">{l s='Search settings' mod='ambjolisearch'}</a>
        </div>
    </div>
    <!-- Tab panes -->
    <div class="tab-content col-lg-10">
        <div class="tab-pane active" id="dropdown_settings">
            {$forms.design_settings nofilter}
            {$forms.dropdown_list_settings nofilter}
            {$forms.priority_settings nofilter}
        </div>

        <div class="tab-pane" id="results_page_settings">
            {$forms.results_page_settings nofilter}
        </div>

        <div class="tab-pane" id="search_settings">
            {$forms.search_settings nofilter}
            {if isset($forms.compatibility_settings)}
                {$forms.compatibility_settings nofilter}
            {/if}
        </div>
    </div>
</div>

<script type="text/javascript">
(function($){
    var is_ps17 = {if $is_prestashop17}true{else}false{/if};

    var toggleCategoriesOptions = function () {
        if ($('input#AJS_DISPLAY_CATEGORY_on').is(':checked')) {
            $('input[name=AJS_SHOW_PARENT_CATEGORY]').parents('.form-group').fadeIn();
            $('input[name=AJS_FILTER_ON_PARENT_CATEGORY]').parents('.form-group').fadeIn();
        } else {
            $('input[name=AJS_SHOW_PARENT_CATEGORY]').parents('.form-group').fadeOut();
            $('input[name=AJS_FILTER_ON_PARENT_CATEGORY]').parents('.form-group').fadeOut();
        }
    }

    var toggleMobileOptions = function() {

        if ($('input#AJS_ENABLE_AC_PHONE_on').is(':checked')) {
            $('input[name=AJS_USE_MOBILE_UX]').parents('.form-group').fadeIn();
            $('input[name=AJS_MOBILE_MEDIA_BREAKPOINT]').parents('.form-group').fadeIn();
            $('input[name=AJS_MOBILE_OPENING_SELECTOR]').parents('.form-group').fadeIn();
        } else {
            $('input[name=AJS_USE_MOBILE_UX]').parents('.form-group').fadeOut();
            $('input[name=AJS_MOBILE_MEDIA_BREAKPOINT]').parents('.form-group').fadeOut();
            $('input[name=AJS_MOBILE_OPENING_SELECTOR]').parents('.form-group').fadeOut();
        }
    }

    var toggleApproximativeOptions = function() {
    }

    var toggleFinderLikeOptions = function() {
        if (is_ps17 && $('#AJS_JOLISEARCH_THEME').val() == 'finder') {
            $('#sep_AJS_SHOW_ADD_TO_CART_BUTTON').parents('.form-group').fadeIn();
            $('#AJS_SHOW_ADD_TO_CART_BUTTON_on').parents('.form-group').fadeIn();
            $('#AJS_ADD_TO_CART_BUTTON_STYLE').parents('.form-group').fadeIn();
        } else {
            $('#sep_AJS_SHOW_ADD_TO_CART_BUTTON').parents('.form-group').fadeOut();
            $('#AJS_SHOW_ADD_TO_CART_BUTTON_on').parents('.form-group').fadeOut();
            $('#AJS_ADD_TO_CART_BUTTON_STYLE').parents('.form-group').fadeOut();
        }
    }

    $(document).ready(function() {

        toggleCategoriesOptions();
        toggleMobileOptions();
        toggleApproximativeOptions();
        toggleFinderLikeOptions();

        $('input[name=AJS_DISPLAY_CATEGORY]').on('change', toggleCategoriesOptions);
        $('input[name=AJS_ENABLE_AC_PHONE]').on('change', toggleMobileOptions);
        $('input[name=AJS_APPROXIMATIVE_SEARCH]').on('change', toggleApproximativeOptions);
        $('#AJS_JOLISEARCH_THEME').on('change', toggleFinderLikeOptions);

        $('#modulecontent form').on('submit', function(e) {
            var serializedData = '';
            $('#modulecontent form').each(function() {
                serializedData = serializedData + (serializedData.length > 0 ? '&' : '') + $(this).serialize();
            });

            $.ajax({
                url: $(this).attr('action'),
                data: serializedData,
                type: 'POST',
                success: function() {
                    window.location.replace(window.location.href + '&successOk');
                }
            });

            return false;
        });
    });
})($);
</script>
