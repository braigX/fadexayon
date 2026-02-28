{*
* Since 2013 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
* @version   Release: 4
*}

{if isset($fix_document_write) && $fix_document_write == 1}
<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">
{else}
<script type="text/javascript"> 
	if(window.location.hash == '#privacy') {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: block;">');
	{rdelim} else {ldelim}
		document.write('<div id="{$tab_id|escape:'html':'UTF-8'}" style="display: none;">');
	{rdelim} 
</script>
{/if}
	<h4>{l s='Privacy' mod='newsletterpro'}</h4>
	<div class="separation"></div>

    <div>
        <div class="form-group">
            <h4>{l s='Subscription Consent' mod='newsletterpro'}</h4>
        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon" style="width: 40px;">
                    <span id="privacy-datagrid-serach-loading" class="btn-ajax-loader" style="display: none;"></span>
                    <i id="privacy-datagrid-serach-icon" class="icon icon-search fa fa-search"></i>
                </span>
                <input type="text" id="privacy-datagrid-serach" class="form-constrol fixed-width-xxl" placeholder="{l s='email address' mod='newsletterpro'}">
            </div>
        </div>

        <div class="from-group">
            <table id="privacy-datagrid" class="table table-bordered send-history">
                <thead>
                    <tr>
                        <th class="consent_date" data-field="consent_date">{l s='Consent Date' mod='newsletterpro'}</th>
                        <th class="email" data-field="email">{l s='Email' mod='newsletterpro'}</th>
                        <th class="subscribed" data-field="subscribed">{l s='Subscribed' mod='newsletterpro'}</th>
                        <th class="ip_address" data-field="ip_address">{l s='IP' mod='newsletterpro'}</th>
                        <th class="date_add" data-field="date_add">{l s='Date Add' mod='newsletterpro'}</th>
                        <th class="actions" data-template="actions">{l s='Actions' mod='newsletterpro'}</th>
                    </tr>
                </thead>
            </table>
        </div>

        <br>

        <div class="form-group">
            <h4>{l s='Delete Email Address and related data' mod='newsletterpro'}</h4>
        </div>

        <div class="form-group">
            <div class="form-inline">
                <div class="input-group" style="width: auto; margin: 0; margin-right: 5px;">
                    <span class="input-group-addon" style="width: 40px;">
                        <span id="delete-email-serach-loading" class="btn-ajax-loader" style="display: none;"></span>
                        <i id="delete-email-serach-icon" class="icon icon-search fa fa-search"></i>
                    </span>
                    <input type="text" id="delete-email-serach" class="form-constrol fixed-width-xxl" placeholder="{l s='email address' mod='newsletterpro'}">
                </div>
                <button id="delete-email-serach-button" type="button" href="javascript:{}" class="btn btn-primary">{l s='Search' mod='newsletterpro'}</button>
                <button id="clear-email-serach-button" type="button" href="javascript:{}" class="btn btn-default">{l s='Clear Search' mod='newsletterpro'}</button>
            </div>
        </div>
        <p class="help-block">{l s='You must search for complete email address. Partial search will not find the desired email address.' mod='newsletterpro'}</p>
        
        <div id="delete-email-content" class="form-group clearfix" style="display: none;"></div>

        <div class="alert alert-info">{l s='If you synchronize the email address with MailChimp, you must delete the manually from MailChimp.' mod='newsletterpro'}</div>
        <div class="alert alert-info">{l s='If you have activated the Google Analytics you must check for the email address in your Google Analytics account.' mod='newsletterpro'}</div>
    </div>
</div>
 