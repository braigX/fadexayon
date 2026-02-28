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
<div id="pspc-rating">
    {if $psv == 1.5}
        <br/><fieldset><legend>{l s='Feedback' mod='prestawp'}</legend>
    {else}
        <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Feedback' mod='prestawp'}
        </div>
    {/if}

        <div>
            <p>
                <a href="http://addons.prestashop.com/ratings.php" target="_blank">
                    <img src="{$module_path|escape:'quotes':'UTF-8'}views/img/rating.png" alt="#">
                </a>
            </p>
            <br>
            <p><b>{l s='Please rate this module!' mod='prestawp'}</b> {l s='It is very important for us and will take just a few seconds.' mod='prestawp'}</p>
            <p>{l s='You can do it in your profile at Addons Marketplace' mod='prestawp'} (<a href="http://addons.prestashop.com/ratings.php" target="_blank">{l s='link' mod='prestawp'}</a>).</p>
            <br>
            <p>{l s='And of course feel free to contact us if you have any questions!' mod='prestawp'}</p>
        </div>

    {if $psv == 1.5}
        </fieldset><br/>
    {else}
        </div>
    {/if}
</div>
