{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<script type="text/javascript">

    const copied_translate = '{l s='Copied' mod='ets_reviews'}';
    const ETS_RV_REVIEW_LINK = '{$ETS_RV_REVIEW_LINK nofilter}';
    const ETS_RV_ACTIVITY_LINK = '{$ETS_RV_ACTIVITY_LINK nofilter}';

    document.addEventListener("DOMContentLoaded", function () {
        const $ = jQuery;
        var xhr;
        if (xhr)
            xhr.abort();

        xhr = $.ajax({
            url: ETS_RV_ACTIVITY_LINK,
            type: 'POST',
            data: 'action=notify&ajax=1',
            dataType: 'json',
            success: function (json) {
                if (json) {
                    // AdminTab.
                    let badge_danger = '<span class="badge badge-danger ets_rv_activity">' + json.activity + '</span>';
                    if ($('li[id*=-AdminEtsRVActivity] > a > span.ets_rv_activity').length <= 0) {
                        let number_of = $('li[id$=-AdminEtsRVActivity] > a > span');
                        if (number_of.length) {
                            number_of.append(badge_danger);
                        } else  {
                            $('li[id$=-AdminEtsRVActivity] > a').append(badge_danger);
                        }
                    } else {
                        $('li[id$=-AdminEtsRVActivity] > a > span.ets_rv_activity').html(json.activity);
                    }

                    // Menu
                    let menu = $('.form-menu-item.activity');
                    if (menu.length > 0) {
                        menu.attr('data-count', json.activity);
                        if ($('a.form-menu-item-link > span.badge', menu).length > 0) {
                            $('a.form-menu-item-link > span.badge', menu).html(json.activity);
                        } else {
                            $('a.form-menu-item-link', menu).append('&nbsp;<span class="badge badge-danger">' + json.activity + '</span>');
                        }
                    }
                    if (parseInt(json.activity) <= 0) {
                        $('li[id$=-AdminEtsRVActivity] > a .ets_rv_activity, a.form-menu-item-link > span.badge').remove();
                    }
                }
            }
        });

        $(document).on('click','#tab-AdminEtsRV',function(e){
            if ( $(window).width() < 768 ){
                e.preventDefault();
                $(this).stop().toggleClass('mn_active');
                $('#tab-AdminEtsRV ~ li[id^="subtab-AdminEtsRV"]').stop().toggleClass('show_mobile');
            }
        });
    });
</script>