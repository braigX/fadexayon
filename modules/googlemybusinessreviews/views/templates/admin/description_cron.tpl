{*
* 2020 AN Eshop Group
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0).
* It is available through the world-wide-web at this URL:
* https://opensource.org/licenses/osl-3.0.php
* If you are unable to obtain it through the world-wide-web, please send an email
* to contact@payplug.com so we can send you a copy immediately.
*
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PayPlug module to newer
 * versions in the future.
*
*  @author  AN Eshop Group
*  @copyright  2020 AN Eshop Group
*  @license   Private
*  AN Eshop Group
*}

<div id="place-map-id" class="panel kpi-container">
    <div class="panel-heading">
        <i class="icon-cog"></i> Cron
    </div>

    <div class="alert alert-info">
        <p>{l s='First of all, check that the "curl" library is installed on your server' mod='googlemybusinessreviews'} :</p>
        <p>{l s='To run your cron tasks, please insert the following line in your cron task editor' mod='googlemybusinessreviews'} :</p>
        <br>
        <ul class="list-unstyled">
            <li>
                <code>0 1 * * * curl -k {$link|escape:'htmlall':'UTF-8'}</code>
            </li>
        </ul>
    </div>
</div>
