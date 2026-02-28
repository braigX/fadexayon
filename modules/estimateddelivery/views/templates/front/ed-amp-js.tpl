{**
 * Smart CSV Lists Front Office Feature * Estimated Delivery - Front Office Feature
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
**}
<script id="ed-amp-picking-day" type="text/plain" target="amp-script">
    var ed_hours = '{l s='hours' mod='estimateddelivery'}';
    var ed_minutes = '{l s='minutes' mod='estimateddelivery'}';
    var ed_and = '{l s='and' mod='estimateddelivery'}';
    var ed_refresh = '{l s='Picking time limit reached please refresh your browser to see your new estimated delivery.' mod='estimateddelivery'}';

    const span = document.getElementsByClassName('ed_countdown')[0];
    //const span = document.getElementById('live-time');

    if (document.getElementsByClassName("estimateddelivery").length != 0) {
        var myDoc = document.getElementsByClassName('ed_countdown')[0].getAttribute('data-rest');

        const countdown = async () => {
            var time = '';
            time_limit[1] -= 1;
            if (time_limit[1] < 0) {
                time_limit[1] += 60;
                time_limit[0]--;
                if (time_limit[0] < 10 && time_limit[0] > 0) {
                    time_limit[0] = '0'+time_limit[0];
                }
                if (time_limit[0] <= 0) {
                    time = ed_refresh;
                }
            }

            if (time_limit[1] < 10 && time_limit[1] > 0) {
                time_limit[1] = '0'+time_limit[1];
            }
            if (time == '') {
                time = (time_limit[0] != 0 ? parseInt(time_limit[0])+' '+ed_hours+' '+ed_and+' ' : '')+(parseInt(time_limit[1])+' '+ed_minutes)
                //console.log("Time " + time);
                span.textContent = time;
            } else {
                //console.log('here');
                span.textContent = ed_refresh;
            }
        }

        if ( myDoc.length > 0 ){
            var time_limit = myDoc;
            var curr_hour = new Date();
            curr_hour = curr_hour.getHours()+':'+curr_hour.getMinutes();
            time_limit = time_limit.split(':');
            if (time_limit[0] == 0 && time_limit[1] < 59) {
                time_limit[1]++;
            }

            countdown();
        }

        setInterval(countdown, 60000);
    }
</script>