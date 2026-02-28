{**
 * 2012 - 2024 HiPresta
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 *
 * @author    HiPresta <support@hipresta.com>
 * @copyright HiPresta 2024
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @website   https://hipresta.com
 *}
{extends file="helpers/list/list_content.tpl"}
{block name="td_content"}
    {if $key == 'status'}
        <a data-id="{$tr.id_position|escape:'htmlall':'UTF-8'}" data-status="{$tr.settings.active|intval}" class="hi-google-connect-position-status btn {if $tr.settings.active == 0}btn-danger{else}btn-success{/if}" 
        href="#" title="{if $tr.settings.active == 0}{l s='Disabled' mod='higoogleconnect'}{else}{l s='Enabled' mod='higoogleconnect'}{/if}">
            <i class="{if $tr.settings.active == 0}icon-remove {else}icon-check{/if}"></i>
        </a>
    {elseif $key == 'preview'}
        <div
            class="hiGoogleButtonPreview"
            data-type="{$tr.settings.buttonType|escape:'htmlall':'UTF-8'}"
            data-theme="{$tr.settings.buttonTheme|escape:'htmlall':'UTF-8'}"
            data-shape="{$tr.settings.buttonShape|escape:'htmlall':'UTF-8'}"
            data-text="{$tr.settings.buttonText|escape:'htmlall':'UTF-8'}"
            data-size="{$tr.settings.buttonSize|escape:'htmlall':'UTF-8'}"
        ></div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}




