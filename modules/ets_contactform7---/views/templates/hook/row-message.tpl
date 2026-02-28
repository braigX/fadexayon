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
<td class="message-more-action">
    <input type="checkbox" name="message_readed[{$message.id_contact_message|intval}]" class="message_readed" value="1" data="{$message.readed|intval}"/>
    <div class="star {if $message.special} star-on{/if}" title="{if $message.special}{l s='Unstar this message' mod='ets_contactform7'}{else}{l s='Star this message' mod='ets_contactform7'}{/if}">
        <input type="checkbox" name="message_special[{$message.id_contact_message|intval}]" class="message_special" value="{$message.id_contact_message|intval}" data="{if $message.special}0{else}1{/if}"  />
        <i class="icon-star"></i>
    </div>
</td>
<td class="message-subject"><span data-toggle="tooltip" title="{l s='From' mod='ets_contactform7'}: {$message.sender|escape:'html':'UTF-8'}">{$message.subject|escape:'html':'UTF-8'|truncate:100:'...'}</span></td>
<td class="message-message"> 
    {$message.body|strip_tags|nl2br|truncate:400:'...' nofilter}
    {if $message.attachments}
        <span class="message-attachements">
            <i class="icon icon-paperclip"></i>
        </span>
    {/if}
</td>
<td class="message-title">
    {$message.title|escape:'html':'UTF-8'|truncate:100:'...'}
</td>
<td class="replies text-center">
    {if $message.replies}
        <i class="material-icons action-enabled" title="{l s='Message has been replied' mod='ets_contactform7'}">check</i>
    {else}
        <i class="fa fa-clock-o icon-clock-o" style="color: #fdc107 "></i>
    {/if}
</td>
<td class="text-center msg_date_form">
    {dateFormat date=$message.date_add full=1}
</td>
<td class="text-center">
    <div class="btn-group-action">
        <div class="btn-group">
            <a class="btn ctf_view_message" href="{$link->getAdminLink('AdminContactFormMessage',true)|escape:'html':'UTF-8'}&viewMessage&id_message={$message.id_contact_message|intval}" class="message-view">{l s='View' mod='ets_contactform7'}</a>
            <a class="btn btn-link dropdown-toggle dropdown-toggle-split product-edit" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" title="{l s='Edit' mod='ets_contactform7'}"> <i class="icon-caret-down"></i></a>
            <div x-placement="bottom-end" class="dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-164px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                <a href="{$link->getAdminLink('AdminContactFormMessage',true)|escape:'html':'UTF-8'}&deleteMessage&id_message={$message.id_contact_message|intval}" class="dropdown-item message-delete product-edit" title="{l s='Delete' mod='ets_contactform7'}"><i class="icon icon-trash fa fa-trash"></i>{l s='Delete' mod='ets_contactform7'}</a>
            </div>
        </div>
    </div>
</td>