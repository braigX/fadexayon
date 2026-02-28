{**
 * 2008-2024 Prestaworld
 *
 * NOTICE OF LICENSE
 *
 * The source code of this module is under a commercial license.
 * Each license is unique and can be installed and used on only one website.
 * Any reproduction or representation total or partial of the module, one or more of its components,
 * by any means whatsoever, without express permission from us is prohibited.
 *
 * DISCLAIMER
 *
 * Do not alter or add/update to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author    prestaworld
 * @copyright 2008-2024 Prestaworld
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 * International Registered Trademark & Property of prestaworld
 *}

<div class="panel-heading">
    {l s='General Setting' mod='prestabtwobregistration'}
</div>
<div class="form-wrapper">
    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Disable Normal Registration' mod='prestabtwobregistration'}
        </label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_disable_normal_registration"
                    id="presta_disable_normal_registration_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_disable_normal_registration) && $smarty.post.presta_disable_normal_registration == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->disable_normal_registration == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_disable_normal_registration_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_disable_normal_registration"
                    id="presta_disable_normal_registration_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_disable_normal_registration) && $smarty.post.presta_disable_normal_registration == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_disable_normal_registration) && $presta_config->disable_normal_registration == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_disable_normal_registration) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_disable_normal_registration_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
            {l s=' Use this to enable and disable normal registration.' mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Allow customer to edit custom fields' mod='prestabtwobregistration'}
        </label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_allow_customer_edit"
                    id="presta_allow_customer_edit_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_allow_customer_edit) && $smarty.post.presta_allow_customer_edit == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->customer_edit == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_allow_customer_edit_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_allow_customer_edit"
                    id="presta_allow_customer_edit_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_allow_customer_edit) && $smarty.post.presta_allow_customer_edit == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_allow_customer_edit) && $presta_config->customer_edit == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_allow_customer_edit) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_allow_customer_edit_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='If Enabled, Customer can re-update all custom field information from information page' mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Send Email Notification to Admin' mod='prestabtwobregistration'}
        </label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_send_email_notification_admin"
                    id="presta_send_email_notification_admin_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_send_email_notification_admin) && $smarty.post.presta_send_email_notification_admin == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->send_email_notification_admin == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_send_email_notification_admin_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_send_email_notification_admin"
                    id="presta_send_email_notification_admin_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_send_email_notification_admin) && $smarty.post.presta_send_email_notification_admin == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_send_email_notification_admin) &&$presta_config->send_email_notification_admin == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_send_email_notification_admin) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_send_email_notification_admin_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s=' Send email to admin for every new B2B registration'
                mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix presta-admin-email-id">
        <label class="control-label col-lg-3">{l s='Admin Email ID' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <input
                type="text"
                class="form-control"
                name="presta_admin_email_id"
                autocomplete="off"
                value="{if isset($smarty.post.presta_admin_email_id)}{$smarty.post.presta_admin_email_id|escape:'htmlall':'UTF-8'}{elseif isset($presta_config) && $presta_config->admin_email_id}{$presta_config->admin_email_id|escape:'htmlall':'UTF-8'}{else}{$presta_shop_email|escape:'htmlall':'UTF-8'}{/if}">
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Send Email Notification to Customer' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_send_email_notification_to_customer"
                    id="presta_send_email_notification_to_customer_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_send_email_notification_to_customer) && $smarty.post.presta_send_email_notification_to_customer == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->send_email_notification_to_customer == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_send_email_notification_to_customer_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_send_email_notification_to_customer"
                    id="presta_send_email_notification_to_customer_off"
                    class="form-control"
                    value="0"
                    {if isset($smarty.post.presta_send_email_notification_to_customer) && $smarty.post.presta_send_email_notification_to_customer == 0}
                        checked="checked"
                    {elseif isset($presta_config) && !isset($smarty.post.presta_send_email_notification_to_customer) && $presta_config->send_email_notification_to_customer == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_send_email_notification_to_customer) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_send_email_notification_to_customer_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s='Send email to customer when his/her B2B account is verified by admin.'
                mod='prestabtwobregistration'}
            </p>
        </div>
    </div>

    <div class="form-group clearfix">
        <label class="control-label col-lg-3">{l s='Enable Google reCAPTCHA' mod='prestabtwobregistration'}</label>
        <div class="col-lg-5">
            <span class="switch prestashop-switch fixed-width-lg">
                <input
                    type="radio"
                    name="presta_enable_google_recaptcha"
                    id="presta_enable_google_recaptcha_on"
                    class="form-control"
                    value="1"
                    {if isset($smarty.post.presta_enable_google_recaptcha) && $smarty.post.presta_enable_google_recaptcha == 1}
                        checked="checked"
                    {elseif isset($presta_config) && $presta_config->enable_google_recaptcha == 1}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_google_recaptcha_on">{l s='Yes' mod='prestabtwobregistration'}</label>
                <input
                    type="radio"
                    name="presta_enable_google_recaptcha"
                    id="presta_enable_google_recaptcha_off"
                    class="form-control"
                    value="0"
                    {if isset($presta_config) && !isset($smarty.post.presta_enable_google_recaptcha) && $presta_config->enable_google_recaptcha == 0}
                        checked="checked"
                    {elseif isset($smarty.post.presta_enable_google_recaptcha) && $smarty.post.presta_enable_google_recaptcha == 0}
                        checked="checked"
                    {elseif !isset($smarty.post.presta_enable_google_recaptcha) && !isset($presta_config)}
                        checked="checked"
                    {/if}>
                <label for="presta_enable_google_recaptcha_off">{l s='No' mod='prestabtwobregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
            <p class="help-block">
                {l s=' Send email to admin for every new B2B registration'
                mod='prestabtwobregistration'}
            </p>
        </div>
    </div>
    <div class="presta-recaptcha-content">
        <div class="form-group clearfix">
            <label class="control-label col-lg-3">
                <span
                    class="label-tooltip"
                    data-toggle="tooltip"
                    data-html="true"
                    data-original-title="{l s='Choose google reCaptcha type which suit your website protection' mod='prestabtwobregistration'}">
                    {l s='reCaptcha Type' mod='prestabtwobregistration'}
                </span>
            </label>
            <div class="col-lg-8">
                <select name="presta_recaptcha_type" class="fixed-width-xl">
                    <option
                        {if isset($smarty.post.presta_recaptcha_type) && $smarty.post.presta_recaptcha_type == 1}selected="selected"{elseif isset($presta_config) && $presta_config->recaptcha_type == 1}selected="selected"{/if}
                        value="1">
                        {l s='Google reCaptcha v2' mod='prestabtwobregistration'}
                    </option>
                    <option
                        {if isset($smarty.post.presta_recaptcha_type) && $smarty.post.presta_recaptcha_type == 2}selected="selected"{elseif isset($presta_config) && $presta_config->recaptcha_type == 2}selected="selected"{/if}
                        value="2">
                        {l s='Number Captcha Only' mod='prestabtwobregistration'}
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group clearfix presta_v2_recaptcha">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-6">
                <img width="40%" src="{$presta_module_dir|escape:'htmlall':'UTF-8'}/views/img/googlereCaptcha/robot.gif" />
            </div>
        </div>
        <div class="form-group clearfix presta_number_recaptcha">
            <label class="control-label col-lg-3"></label>
            <div class="col-lg-6">
                <img width="40%" src="{$presta_module_dir|escape:'htmlall':'UTF-8'}/views/img/googlereCaptcha/captcha.png" />
            </div>
        </div>
        <div class="form-group clearfix presta-site-key">
            <label class="control-label col-lg-3 required">{l s='Site Key' mod='prestabtwobregistration'}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_site_key"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_site_key)}{$smarty.post.presta_site_key|escape:'htmlall':'UTF-8'}{elseif isset($presta_config) && $presta_config->site_key}{$presta_config->site_key|escape:'htmlall':'UTF-8'}{/if}">
            </div>
        </div>
        <div class="form-group clearfix presta-secret-key">
            <label class="control-label col-lg-3 required">{l s='Secret Key' mod='prestabtwobregistration'}</label>
            <div class="col-lg-5">
                <input
                    type="text"
                    class="form-control"
                    name="presta_secret_key"
                    autocomplete="off"
                    value="{if isset($smarty.post.presta_secret_key)}{$smarty.post.presta_secret_key|escape:'htmlall':'UTF-8'}{elseif isset($presta_config) && $presta_config->secret_key}{$presta_config->secret_key|escape:'htmlall':'UTF-8'}{/if}">
            </div>
        </div>
    </div>
</div>

