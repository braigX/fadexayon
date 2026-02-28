{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript">
    var link_login_as_customer = '{$link_login_as_customer|escape:'html':'UTF-8'}';
    var ets_omn_link_order_manager ='{$ets_omn_link_order_manager|escape:'html':'UTF-8'}';
    var Login_as_customer_text = '{l s='Login as customer' mod='ets_ordermanager'}';
    $(document).ready(function(){
        if(link_login_as_customer && $('.admincustomers a.btn-help.btn-sidebar').length)
        {
            $('.admincustomers a.btn-help.btn-sidebar').before('<a class="btn btn-outline-secondary ets_odmn_login_as_customer" href="'+link_login_as_customer+'" title="'+Login_as_customer_text+'" data-url="'+link_login_as_customer+'" target="_blank"> '+Login_as_customer_text+' </a>');
        }
    });
</script>