/**
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
 */
(function($) {
  if (etsSeoBo && !etsSeoBo.isEnable) {
    return;
  }
  $(document).on('click', '#tab-AdminEtsSeo', function(e) {
    $(this).stop().toggleClass('actived');
    $('#subtab-AdminEtsSeoGeneralDashboard,#subtab-AdminEtsSeoUrlAndRemoveId,#subtab-AdminEtsSeoSearchAppearanceSitemap,#subtab-AdminEtsSeoSearchAppearanceRSS,#subtab-AdminEtsSeoFileEditor,#subtab-AdminEtsSeoRatingSnippet,#subtab-AdminEtsSeoSearchAppearanceContentType,#subtab-AdminEtsSeoSocial,#subtab-AdminEtsSeoTraffic,#subtab-AdminEtsSeoSettings').stop().toggleClass('ets_sidebar_menu_show');
  });
})(jQuery);


