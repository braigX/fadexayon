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
{if $open_form_by_button}
    <span id="button_{$contact_form->unit_tag|escape:'html':'UTF-8'}" class="ctf_click_open_contactform7 btn btn-primary" data-id="{$contact_form->unit_tag|escape:'html':'UTF-8'}" >{$contact_form->button_label|escape:'html':'UTF-8'}</span>
    <div class="ctf-popup-wapper" id="ctf-popup-wapper-{$contact_form->unit_tag|escape:'html':'UTF-8'}">
    <div class="ctf-popup-table">
    <div class="ctf-popup-tablecell">
    <div class="ctf-popup-content">
    <div class="ctf_close_popup">close</div>
{/if}
<div role="form" class="wpcf7{if $displayHook} hook{/if}" id="{$contact_form->unit_tag|escape:'html':'UTF-8'}" dir="ltr" data-id="{$contact_form->id|intval}">
    <form action="{$link->getModuleLink('ets_contactform7','submit')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" autocomplete="false" novalidate="novalidate">
        {if $displayHook}
            <h3>{$contact_form->title|escape:'html':'UTF-8'}</h3>
        {/if}
        <input type="hidden" name="_wpcf7" value="{$contact_form->id|intval}" />
        <input type="hidden" name="_etscf7_version" value="5.0.1"/>
        <input type="hidden" name="_etscf7_locale" value="en_US"/>
        <input type="hidden" name="_etscf7_unit_tag" value="wpcf7-{$contact_form->unit_tag|escape:'html':'UTF-8'}"/>
        <input type="hidden" name="_etscf7_container_post" value="{$contact_form->id|intval}"/>
        {$form_elements nofilter}
        <div class="wpcf7-response-output wpcf7-display-none"></div>
    </form>
    <div class="clearfix">&nbsp;</div>
</div>
{if $open_form_by_button}
</div></div></div></div>
{/if}