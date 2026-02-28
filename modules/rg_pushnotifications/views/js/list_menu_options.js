/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$(document).ready(function () {
  /*
   * Module configuration form
   */
  if ($('#menu-subscribers-list').length == 0) {
    $('ul.nav-pills').prepend(list_menu_options_html);
  }
});
