/**
 /** * Estimated Delivery - Front Office Feature
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
 */

document.addEventListener('DOMContentLoaded', function() {
   // Add the listener to refresh the calendar for 1.7
   // console.log('Prepare Calendar Refresh');
   if (typeof prestashop === 'object') {
      prestashop.on('updateCart', function (event) {
         //console.log('Checking parameters to detect...');
         if (typeof event.reason !== 'undefined' && (typeof event.reason.updateUrl !== 'undefined' || event.reason.linkAction.indexOf('delete') != -1)) {
            //console.log('Update in the cart detected! Executing update function');
            // It's a product cart update / delete
            $.ajax({
               type: 'POST',
               headers: { "cache-control": "no-cache" },
               url: front_ajax_url + '&rand=' + new Date().getTime(),
               async: false,
               cache: false,
               dataType : "json",
               data: {
                  ajaxRefresh: true,
                  action: 'calendarRefresh',
               },
               complete: function(res){
                  r = res.responseJSON
                  if (r.return === 'error') {
                     console.log('Estimated Delivery: Error while trying to update the calendar');
                     console.log(r.message);
                  } else {
                     // Update the calendar
                     // console.log($(r.message).html());
                     $('#ed_calendar_display').html($(r.message).html());
                     $('.p_ed_delivery_date').datepicker("destroy");
                     addEdDatePicker();
                  }
               }
            });
         }
      });
   }
});