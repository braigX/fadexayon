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
<script type="text/javascript"> 
    var ets_ctf_default_lang = {$ets_ctf_default_lang|intval};
    var ets_ctf_is_updating = {$ets_ctf_is_updating|intval};
    var Copied_text = '{l s='Copied' mod='ets_contactform7' js=1}';
    var PS_ALLOW_ACCENTED_CHARS_URL=true; 
    var detele_confirm ='{l s='Do you want to delete?' mod='ets_contactform7'}';
    var detele_confirm_message ='{l s='Do you want to delete this message?' mod='ets_contactform7'}';
</script>
<script type="text/javascript" src="{$js_dir_path|escape:'quotes':'UTF-8'}contact_form7_admin.js"></script>
<div class="ctf7-left-block">
<ul>
    <li{if $controller=='AdminContactFormContactForm' || $controller=='AdminModules'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormContactForm',true)|escape:'html':'UTF-8'}"><i class="icon icon-envelope-o"> </i> {l s='Contact forms' mod='ets_contactform7'}</a></li>
    <li{if $controller=='AdminContactFormMessage'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormMessage',true)|escape:'html':'UTF-8'}"><i class="icon icon-comments"> </i> {l s='Messages' mod='ets_contactform7'}&nbsp;<span class="count_messages {if !$count_messages}hide{/if}">{$count_messages|intval}</span></a></li>
    <li{if $controller=='AdminContactFormEmail'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormEmail',true)|escape:'html':'UTF-8'}"><i class="icon icon-file-text-o"> </i> {l s='Email templates' mod='ets_contactform7'}</a></li>
    <li{if $controller=='AdminContactFormImportExport'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormImportExport',true)|escape:'html':'UTF-8'}"><i class="icon icon-exchange"> </i> {l s='Import/Export' mod='ets_contactform7'}</a></li>
    <li{if $controller=='AdminContactFormIntegration'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormIntegration',true)|escape:'html':'UTF-8'}"><i class="icon icon-cogs"> </i> {l s='Integration' mod='ets_contactform7'}</a></li>
    <li{if $controller=='AdminContactFormStatistics'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormStatistics',true)|escape:'html':'UTF-8'}"><i class="icon icon-line-chart"> </i> {l s='Statistics' mod='ets_contactform7'}</a></li>
    <li{if $controller=='AdminContactFormHelp'} class="active"{/if}><a href="{$link->getAdminLink('AdminContactFormHelp',true)|escape:'html':'UTF-8'}"><i class="icon icon-question-circle"> </i> {l s='Help' mod='ets_contactform7'}</a></li>
</ul>
</div>