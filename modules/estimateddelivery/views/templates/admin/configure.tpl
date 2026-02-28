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

<!--<div class="panel">
    <h3 class="modal-title"><i class="icon icon-cogs"></i> {l s='Estimated Delivery' mod='estimateddelivery'}</h3>
    <p>
        <strong>{l s='Estimated Delivery Configuration Screen' mod='estimateddelivery'}</strong><br />
        <p>{l s='Here you can configure the different module options, like picking days, shipping, days, delivery times and holidays.' mod='estimateddelivery'}</p>
        <p>{l s='Tooltip usage: The carrier descripton Use the forms below to personalize it' mod='estimateddelivery'} </p>
        <p>{l s='Use the forms below to personalize it' mod='estimateddelivery'}
    </p>
</div>
-->
{if isset($old_ps) && $old_ps}
<fieldset id="ed_documentation">
    <legend>{l s='Estimated Delivery' mod='estimateddelivery'} {l s='Documentation' mod='estimateddelivery'}</legend>
{else}
<div class="panel" id="ed_documentation">
    <div class="panel-heading" data-position="1"><i class="icon icon-book"></i> {l s='Estimated Delivery' mod='estimateddelivery'} {l s='Documentation' mod='estimateddelivery'}</div>
{/if}
    <div class="row">
        <div class="col-lg-6 col-xs-12">
            <h2 class="text-primary">{l s='Basic Blocks' mod='estimateddelivery'}</h2>
            <p>{l s='The module configuration is split in several blocks' mod='estimateddelivery'}, <strong>{l s='the fisrt' mod='estimateddelivery'} 4 {l s='blocks have to be configured to make the module work.' mod='estimateddelivery'}</strong></p>
            <h3 class="modal-title text-info">{l s='1st' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Format and Style' mod='estimateddelivery'}</h3>
            <p>{l s='Configure the appearance and the placement for the estimated delivery box, positioning, colours, date format...' mod='estimateddelivery'} </p>
            <h3 class="modal-title text-info">{l s='2nd' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Picking Days' mod='estimateddelivery'}</h3>
            <p>{l s='Set the days you prepare the orders (picking days). the picking is the process between an order is created and its ready to deliver to the carrier.' mod='estimateddelivery'}  </p>
            <p><strong>{l s='For example' mod='estimateddelivery'}</strong> : {l s='Every day from Monday to Friday you send all the orders with Payment Accepted status and the Carrier comes to collect the daily packages at 18:00 pm.' mod='estimateddelivery'}
            {l s='Then you should configure the picking by checking from Monday to Friday and setting the picking limit to 17:30 - 17:45.' mod='estimateddelivery'}    {l s='Once the picking time is over the Estimated Delivery module will set the delivery day +1' mod='estimateddelivery'}  </p>
            <p>{l s='If you don\'t want to use this feature leave all the days checked and leave the picking limit to 23:59' mod='estimateddelivery'}  </p>
            <h3 class="modal-title text-info">{l s='3rd' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Carriers' mod='estimateddelivery'}</h3>
            <p>{l s='Set the shipping days for the carriers, check the days that your carriers do the delivery, for example from Monday to Friday.' mod='estimateddelivery'} </p>
            <h3 class="modal-title text-info">{l s='4th' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Carriers Delivery Intervals' mod='estimateddelivery'}</h3>
            <p>{l s='Delivery intervals: Whats the minium and the maxium time in days for a carrier to deliver an order.' mod='estimateddelivery'} </p>
        </div>
        <div class="col-lg-6 col-xs-12">
            <h2 class="text-primary">{l s='Advanced Blocks' mod='estimateddelivery'}</h2>
            <p>{l s='Those blocks allows a more individualized control like individual carrier configurations, additional days for picking or for products out of stock (OOS)' mod='estimateddelivery'}...</p>
            <h3 class="modal-title text-info">{l s='2.1' mod='estimateddelivery'}
            {l s='Picking Days' mod='estimateddelivery'} {l s='(advanced mode)' mod='estimateddelivery'}</h3>
            <p>{l s='Set the picking days and the picking limit individually for each carrier.' mod='estimateddelivery'} </p>
            <h3 class="modal-title text-info">{l s='2.2' mod='estimateddelivery'}
            {l s='Additional Picking Days' mod='estimateddelivery'}</h3>
            <p>{l s='Sometimes a product need more preparation days' mod='estimateddelivery'}. {l s='Use this block to set the additional days by category, brand or supplier.' mod='estimateddelivery'}</p>
            <p>{l s='If you need a more complete control you can do it in the product edit page at product or combination level!' mod='estimateddelivery'}</p>
            <h3 class="modal-title text-info">{l s='2.3' mod='estimateddelivery'}
            {l s='Out of Stock additional days' mod='estimateddelivery'}</h3>
            <p>{l s='When products out of stock have sales enabled most of the times requires more days to delivery' mod='estimateddelivery'}. {l s='Use this block to set the additional days by category, brand or supplier.' mod='estimateddelivery'}</p>
            <p>{l s='If you need a more complete control you can do it in the product edit page at product or combination level!' mod='estimateddelivery'}</p>
            <h3 class="modal-title text-info">{l s='3.1' mod='estimateddelivery'}
            {l s='Carriers' mod='estimateddelivery'} {l s='(advanced mode)' mod='estimateddelivery'}</h3>
            <p>{l s='Activate this feature to be able to' mod='estimateddelivery'} {l s='Enable / Disable the carriers and give them an alias.' mod='estimateddelivery'}{l s='This is useful when there are carriers ' mod='estimateddelivery'}{l s='It only afects the Estimated Delivery information shown.' mod='estimateddelivery'}</p>
            <p> {l s='It only afects the Estimated Delivery information shown.' mod='estimateddelivery'}</p>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <h2 class="text-primary">{l s='Other Features' mod='estimateddelivery'}</h2>
            <h3 class="modal-title text-info">{l s='5th' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Order process, Order history and Emails' mod='estimateddelivery'} ({l s='Beta Feature' mod='estimateddelivery'})</h3>
            <p>{l s='Enable this block to have the Estimated Delivery in the orders process and save the dates in the database to later show them on the Order Hirstory (FO) and in the Order details (BO)' mod='estimateddelivery'}.</p>
            <h3 class="modal-title text-info">{l s='6th' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='Estimated Delivery on Product Lists' mod='estimateddelivery'}</h3>
            <p>{l s='Enable the ED on product listings such as searches, categories, the homepage or even the checkout page' mod='estimateddelivery'}!</p>
            <h3 class="modal-title text-info">{l s='7th' mod='estimateddelivery'}
            {l s='block' mod='estimateddelivery'} - {l s='No delivery or picking days' mod='estimateddelivery'}</h3>
            <p>{l s='No delivery or picking: Just put your vacations, national holidays here. This way the system will output a more accurated delivery dates.' mod='estimateddelivery'}</p>
            <hr/>
            <h3 class="modal-title">{l s='Have any doubt or difficulty?' mod='estimateddelivery'}<br></h3>
            <h3 class="modal-title"><a href="http://addons.prestashop.com/contact-community.php?id_product=19012">{l s='Clik here to contact us and get fast support!' mod='estimateddelivery'}</a></h3>
        </div>
    </div>
{if isset($old_ps) && $old_ps}
</fieldset>
<br>
{else}
</div>
{/if}
{* Max Input Vars Warning *}
<div id="max_input_warning" class="bootstrap" style="display:none">
<div class="{if $old_ps}alert{else}alert alert-danger{/if}">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <h4>{l s='Max inputs too low' mod='estimateddelivery'} (#1/{$input_limit|intval})</h4>
    <p><strong>{l s='The number of options you can send is %s while the module configuration has #1, this may cause issues when saving the this configuration' mod='estimateddelivery' sprintf=[$input_limit]}</strong>.</p>
    <p>{l s='We recommned you to change the %s value to at least #2 to be able to save all the configurations without having any issues' mod='estimateddelivery' sprintf=['max_input_vars']}. {l s='If you don\'t know how to rise this value please contact your hosting and ask them to rise it for you' mod='estimateddelivery'}.</p>
    <p>{l s='Once the value is high enough this warning will disappear' mod='estimateddelivery'}.</p>
</div>
</div>
<script>
    var selected_menu = '{$selected_menu|escape:'htmlall':'UTF-8'}';
    $(document).ready(function() {
        /* Check Max input vars */
        var inputs = $('#content input').length;
        if ($('#content input').length > {$input_limit|intval}) {
            $('#max_input_warning').html($('#max_input_warning').html().split('#1').join(inputs).replace('#2', (parseInt((inputs+350)/1000)*1000) + 1000)).prependTo('#content').show();
        }
    });
</script>