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
<p style="margin-top: 15px;">
    <strong>*{l s='Setup a cronjob as below (once per minute) on your server to send exported orders to receivers automatically' mod='ets_ordermanager'}</strong><br/>
    <span class="ets_ode_php_command">* * * * * {$php_path|escape:'html':'UTF-8'} {$dir_cronjob|escape:'html':'UTF-8'}
        secure={$ETS_ODE_CRONJOB_TOKEN|escape:'html':'UTF-8'}</span>
</p>
<p style="margin-top: 5px;">
    <strong>*{l s='Manually send exported orders to receivers by running the following URL on your web browser' mod='ets_ordermanager'}</strong><br />
    <a class="btn btn-default btn-run-cronjob" target="_blank" href="{$link_cronjob|escape:'html':'UTF-8'}">{l s='Execute cronjob manually' mod='ets_ordermanager'}</a>
</p>