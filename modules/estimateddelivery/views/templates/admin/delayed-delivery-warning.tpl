{** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @version 3.5.4
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                    V 3.5.4                      *
 * ***************************************************
 *}

{if isset($message)}{$message|escape:'htmlall':'UTF-8'}{/if}
{if $old_ps == 1}
<fieldset id="delayed_delivery_warning">
    <legend><i class="icon-warning"></i> 5.1 - {l s='Delayed delivery warning' mod='estimateddelivery'} ({l s='beta' mod='estimateddelivery'})</legend>
{else}
<div class="panel" id="delayed_delivery_warning">
    <div class="panel-heading"><i class="icon-warning"></i> 5.1 - {l s='Delayed delivery warning' mod='estimateddelivery'} ({l s='beta' mod='estimateddelivery'})</div>
{/if}

    <div id="review-warning" class="alert alert-warning" style="display: none">
        {l s='Reviewing is in progress. Please do not leave this page' mod='estimateddelivery'}
    </div>
    <div id="ajax-message-ok" class="conf ajax-message alert alert-success" style="display: none">
        <span class="message">{l s='Reviewing the past orders finished' mod='estimateddelivery'}</span>
    </div>
    <div id="ajax-message-ko" class="error ajax-message alert alert-danger" style="display: none">
        <span class="message">{l s='Reviewing the past orders failed' mod='estimateddelivery'}</span>
    </div>

    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Enable the Delayed Delivery Messages' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="enable_delayed_delivery" id="enable_delayed_delivery_on" value="1" {if $enable_delayed_delivery == 1}checked="checked"{/if}>
                <label for="enable_delayed_delivery_on">Yes</label>
                <input type="radio" name="enable_delayed_delivery" id="enable_delayed_delivery_off" value="0" {if $enable_delayed_delivery == 0}checked="checked"{/if}>
                <label for="enable_delayed_delivery_off">No</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Order states to confirm the order' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <select name="dd_order_state" id="dd_order_state" class="input fixed-width-xxl fixed-width-xl">
                {foreach from=$orderStates item=state}
                    <option value="{$state.id_order_state|intval}" {if $selectedOrderState == $state.id_order_state}selected{/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Hours to notify the admin' mod='estimateddelivery'}</label>
        <div class="col-lg-4">
            <div class="input-group fixed-width-xl">
                <input type="text" name="ed_dd_admin_hours" id="ed_dd_admin_hours" class="fixed-width-xl" onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));" value="{$dd_admin_hours|intval}" />
                <span class="input-group-addon"> {l s='Hours' mod='estimateddelivery'} </span>
            </div>
            <div class="help-block">
                {l s='Number of hours prior of the minimum delivery date to send the message.' mod='estimateddelivery'} <br>
                {l s='Configure the number of hours you want to receive the delayed message before the order\'s minimum delivery date.' mod='estimateddelivery'}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="EMAIL_TO_NOTIFY_ADMIN" class="control-label col-lg-3">{l s='Email address to notify the Admin' mod='estimateddelivery'}</label>
        <div class="col-lg-4">
            <input type="text" id="dd_admin_email" name="dd_admin_email" value="{$dd_admin_email|escape:'htmlall':'UTF-8'}">
            <div class="help-block">{l s='If no change, it will use the default email address of the  shop admin.' mod='estimateddelivery'}</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Enable to send a copy of the customer email to the admin' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input type="radio" name="enable_cc_email" id="enable_cc_email_on" value="1" {if $enable_cc_email == 1}checked="checked"{/if}>
                <label for="enable_cc_email_on">Yes</label>
                <input type="radio" name="enable_cc_email" id="enable_cc_email_off" value="0" {if $enable_cc_email == 0}checked="checked"{/if}>
                <label for="enable_cc_email_off">No</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Hours to notify the customer' mod='estimateddelivery'}</label>
        <div class="col-lg-4">
            <div class="input-group fixed-width-xl">
                <input type="text" name="dd_customer_hours" id="dd_customer_hours" class="fixed-width-xl" onkeypress="return /\d/.test(String.fromCharCode(((event||window.event).which||(event||window.event).which)));" value="{$dd_customer_hours|intval}" />
                <span class="input-group-addon"> {l s='Hours' mod='estimateddelivery'} </span>
            </div>
            <div class="help-block">
                {l s='Number of hours prior of the minimum delivery date to send the message.' mod='estimateddelivery'} <br>
                {l s='Configure the number of hours you want to notify the customer about the delayed delivery before the order\'s minimum delivery date.' mod='estimateddelivery'}
            </div>
        </div>
    </div>
    <div class="form-group" style="display: none;">
        <label class="control-label col-lg-3">{l s='Secret Key' mod='estimateddelivery'}</label>
        <div class="col-lg-4">
            <input type="text" name="cron_secret_key" id="cron_secret_key" value="{$cron_secret_key|escape:'htmlall':'UTF-8'}" readonly />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-lg-3">{l s='Cron Job URL' mod='estimateddelivery'}</label>
        <div class="col-lg-9">
            <input type="text" name="cron_job_url" id="cron_job_url" value="{$DD_CRON_URL|escape:'htmlall':'UTF-8'}" readonly />
            <div class="help-block">
                {l s='Open above URL to check the orders and send the messages.' mod='estimateddelivery'}<br>
                {l s='If you want to automatize this process, please create a cron job.' mod='estimateddelivery'}<br>
                {l s='For more details about how to create a cron job' mod='estimateddelivery'} {l s='visit' mod='estimateddelivery'} <a href="#cron_jobs" class="target-menu badge">{l s='Cron Job' mod='estimateddelivery'}</a> {l s='section in this module' mod='estimateddelivery'}
            </div>
        </div>
    </div>
    <div class="row">
        <p>
            <a class="ajaxcall-review-past-orders btn btn-default" href="{$review_past_orders_ajax_url|escape:'htmlall':'UTF-8'}" style="float: right;">{l s='Review past orders' mod='estimateddelivery'}</a>
        </p>
    </div>
    <hr>
    <div style="clear:both"></div>
    <div class="panel-footer" id="toolbar-footer">
        <button class="btn btn-default pull-right" id="submit_for_ddm" name="SubmitDDM" type="submit"><i class="process-icon-save"></i> <span>{l s='Save' mod='estimateddelivery'}</span></button>
    </div>
</form>
{if $old_ps == 1}
    </fieldset>
{else}
    </div>
{/if}
