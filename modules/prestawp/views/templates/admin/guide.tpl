{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
<div id="pswp-guide" class="{if $psv == 1.5}psv15{/if}">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Quick guide' mod='prestawp'}</legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Quick guide' mod='prestawp'}
        </div>
    {/if}

        <div class="form-wrapper">
            <p><a href="{$path|escape:'quotes':'UTF-8'}wordpress/prestawp-wordpress.zip">{l s='Download the WordPress plugin' mod='prestawp'} <i class="icon-download"></i></a></p>
            <br>
            <h4>{l s='How to connect PrestaShop and WordPress:' mod='prestawp'}</h4>
            <p>{l s='1) Install our plugin "PrestaShop-WordPress integration" into your WordPress blog.' mod='prestawp'}</p>
            <p>{l s='2) Enter your WordPress URL in the main settings of the PrestaShop module (this page).' mod='prestawp'}</p>
            <p>{l s='3) Enter your PrestaShop URL in the settings of the WordPress plugin.' mod='prestawp'}</p>
            <p>{l s='4) Copy the "Secure key" value from the PrestaShop module settings to the WordPress plugin settings.' mod='prestawp'}</p>
            <p>{l s='5) Save changes. All done.' mod='prestawp'}</p>
            <button class="btn btn-default" id="pswp-hide-guide">{l s='Hide' mod='prestawp'}</button>
            <i class="pswp-guide-hint">{l s='You can read it also at the additional instructions tab.' mod='prestawp'}</i>
        </div>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
