{*
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{Module::getInstanceByName('ets_contactform7')->hookContactForm7LeftBlok() nofilter}
<div class="ctf7-right-block">
    <div class="panel ctf7_backend_help">
        <div class="panel-heading">
            <i class="icon-question-circle"></i> {l s='Help' mod='ets_contactform7'}
        </div>
        <p>{l s='Click on the following link to download the documentation of this module' mod='ets_contactform7'}: <a target="_blank" href="{$link_doc|escape:'html':'UTF-8'}">{l s='Download documentation' mod='ets_contactform7'}</a></p>
        <p>{l s='Below are some notes you should pay attention to while using [1]Contact Form 7:[/1]' tags = ['<strong>'] mod='ets_contactform7'}</p>
        <h4>{l s='Contact Forms' mod='ets_contactform7'}</h4>

        <p>{l s='To add reCAPTCHA input field, you need to enable reCAPTCHA first. Navigate to [1]"Settings > Integration > reCAPTCHA"[/1], enable reCAPTCHA option and enter your key pair.' tags=['<strong>'] mod='ets_contactform7'}</p>
        <p>{l s='For how to get reCAPTCHA site key and secret key, please read our module documentation' mod='ets_contactform7'}</p>
        <p>{l s='To be able to reply customer messages directly on back office, you need your customer email address. When building a contact form, make sure to add an email input field and mark it as required field.' mod='ets_contactform7'}</p>

        <h4>{l s='Email configurations' mod='ets_contactform7'}</h4>

        <p>{l s='To get the info from your contact form to the email send to admin or auto responder email, please copy and paste the respective mail-tags into [1]"Message body"[/1] field in [1]"Contact Forms > Mail"[/1] subtab' tags=['<strong>'] mod='ets_contactform7'}</p>
        <p>{l s='To receive attachment file from your customer via email, please navigate to [1]"Contact Forms > Mail"[/1] and check the [1]"File attachment"[/1] box.' tags=['<strong>'] mod='ets_contactform7'}</p>
        <p>{l s='To receive attachment file from your customer via “Messages” tab, please navigate to [1]"Contact Forms > Settings"[/1] and turn on the [1]"Save attachments"[/1] option.' tags=['<strong>'] mod='ets_contactform7'}</p>
    </div>
</div>