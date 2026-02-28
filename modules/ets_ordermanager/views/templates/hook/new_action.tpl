{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
{foreach from=$toolbar_btn item=btn key=k}
    <li>
      <a id="page-header-desc-{$table|escape:'html':'UTF-8'}-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}"
         class="toolbar_btn {if isset($btn.target) && $btn.target} _blank{/if} pointer{if isset($btn.class)} {$btn.class|escape:'html':'UTF-8'}{/if}"{if isset($btn.href)} href="{$btn.href|escape}"{/if}
         title="{if isset($btn.help)}{$btn.help|escape:'html':'UTF-8'}{else}{$btn.desc|escape|escape:'html':'UTF-8'}{/if}"{if isset($btn.js) && $btn.js} onclick="{$btn.js|escape:'html':'UTF-8'}"{/if}{if isset($btn.modal_target) && $btn.modal_target} data-target="{$btn.modal_target|escape:'html':'UTF-8'}" data-toggle="modal"{/if}{if isset($btn.help)} data-toggle="tooltip" data-placement="bottom"{/if}>
        <i class="{if isset($btn.icon)}{$btn.icon|escape:'html':'UTF-8'}{else}process-icon-{if isset($btn.imgclass)}{$btn.imgclass|escape:'html':'UTF-8'}{else}{$k|escape:'html':'UTF-8'}{/if}{/if}{if isset($btn.class)} {$btn.class|escape:'html':'UTF-8'}{/if}"></i>
        <div{if isset($btn.force_desc) && $btn.force_desc == true } class="locked"{/if}>{$btn.desc|escape:'html':'UTF-8'}</div>
      </a>
    </li>
{/foreach}