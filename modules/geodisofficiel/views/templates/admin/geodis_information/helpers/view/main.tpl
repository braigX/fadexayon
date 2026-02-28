{*
* 2018 GEODIS
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$menu}

<div class="panel">
    <div class="formConnexion">
        <form method="POST" class="formConnexion-form">
            <h2>{__ s="Admin.Information.index.connection.label"}</h2>
            <input type="text" class="formConnexion--input" name="login" placeholder="{__ s="Admin.Information.index.connection.login.placeholder"}" />
            <div class="secretKey">
                <input type="text" class="formConnexion--input" name="secretKey" placeholder="{__ s="Admin.Information.index.connection.token.placeholder"}" />
                <i class="process-icon-help formConnexion--tooltip" data-toggle="tooltip" data-placement="left" title="{__ s="Admin.Information.index.connection.token.info"}"></i>
            </div>
            <button type="submit" class="formConnexion--button btn btn-primary" name="submit">{__ s="Admin.Information.index.connection.button.connect"}</button>
        </form>
        <div class="formConnexion-successMessage"><i class="process-icon-ok"></i></div>
        <div class="formConnexion-textError"><i class="process-icon-cancel"></i></div>
    </div>
    <div class="js-modify center">
        <button type="button" class="formConnexion--button btn btn-primary" name="modify">{__ s="Admin.Information.index.connection.button.modify"}</button>
    </div>
    <div class="js-formSynchronise center">
        <form method="POST" class="formSynchronise-form">
            <button type="submit" class="formConnexion--button btn btn-primary" name="synchronise">{__ s="Admin.Information.index.connection.button.synchronise"}</button>
        </form>
    </div>
    {if ($lastSynchronizationDate)}
        <div class="information-synchronization text-center">
            <p>{__ s='Admin.Information.index.connection.lastSynchronizationDate.%s' vars=[$lastSynchronizationDate|escape:'htmlall':'UTF-8']}</p>
        </div>
    {/if}
</div>

{if $hasInformation}
    <div class="informations">
        <div class="preamble">
            <h1>{__ s="Admin.Information.index.account.label"}</h1>
            <p>{__ s="Admin.Information.index.explanation"}</p>
        </div>
        <div class="panel col-md-12">
            <div class="form-row col-md-12">
                <div class="form-group col-md-3">
                    <h2>{__ s="Admin.Information.index.society"}</h2>
                    <p>{$infosSociety['society']|escape:'htmlall':'UTF-8'}</p>
                </div>
                <div class="form-group col-md-9">
                    <h2>{__ s="Admin.Information.index.address"}</h2>
                    <p>{$infosSociety['address1']|escape:'htmlall':'UTF-8'}</p>
                    <p>{$infosSociety['address2']|escape:'htmlall':'UTF-8'}</p>
                    <p>{$infosSociety['zip_code']|escape:'htmlall':'UTF-8'} {$infosSociety['city']|escape:'htmlall':'UTF-8'} {$infosSociety['iso_code']|escape:'htmlall':'UTF-8'}</p>
                    <p>{$infosSociety['name']|escape:'htmlall':'UTF-8'}</p>
                </div>
            </div>
        </div>

        <div class="preamble">
            <h1>{__ s="Admin.Information.index.prestation.label"}</h1>
        </div>
        {foreach $preparationServices as $account}
            <div class="panel col-md-12">
                <div class="row">
                    <div class="form-row col-md-12">
                        <div class="form-group col-md-9 col-xs-6"><h3>{__ s="Admin.Information.index.agency.%s" vars=[($account.name)|escape:'htmlall':'UTF-8']}</h3></div>
                        <div class="form-group col-md-3 col-xs-6 text-right"><h3>{__ s="Admin.Information.index.accountNumber.%s" vars=[($account.codeClient)|escape:'htmlall':'UTF-8']}</div>
                    </div>
                </div>
                <div class="row">
                    {foreach $account.services as $service}
                        <div class="form-group col-md-3 col-xs-6">
                            <p>{$service|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/foreach}

        <div class="preamble">
            <h1>{__ s="Admin.Information.index.removalRequest.label"}</h1>
        </div>
        {foreach $expeditionServices as $account}
            <div class="panel col-md-12">
                <div class="row">
                    <div class="form-row col-md-12">
                        <div class="form-group col-md-9 col-xs-6"><h3>{__ s="Admin.Information.index.agency.%s" vars=[($account.name)|escape:'htmlall':'UTF-8']}</h3></div>
                        <div class="form-group col-md-3 col-xs-6 text-right"><h3>{__ s="Admin.Information.index.accountNumber.%s" vars=[($account.codeClient)|escape:'htmlall':'UTF-8']}</div>
                    </div>
                </div>
                <div class="row">
                    {foreach $account.services as $service}
                        <div class="form-group col-md-3 col-xs-6">
                            <p>{$service|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    {/foreach}
                </div>
            </div>
        {/foreach}
    </div>
{/if}
