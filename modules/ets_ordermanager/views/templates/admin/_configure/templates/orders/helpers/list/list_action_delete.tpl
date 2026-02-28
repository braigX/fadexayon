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

 {if isset($smarty.get.viewtrash) || isset($smarty.post.viewtrash) || Configuration::get('ETS_ODE_BEHAVIOR_DELETE_ORDER')=='permanently'}
    <a href="{$href|escape:'html':'UTF-8'}&viewtrash=1"{if isset($confirm)} onclick="if (confirm('{l s='You are going to delete this order permanently and will not be able to restore it. Do you want to delete this order?' mod='ets_ordermanager'}')){ldelim}return true;{rdelim}else{ldelim}event.stopPropagation(); event.preventDefault();{rdelim};"{/if} class="delete">
    	<i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
    </a>
 {else}
    <a href="{$href|escape:'html':'UTF-8'}&deleteordertrash=1"{if isset($confirm)} onclick="if (confirm('{l s='Order will be removed from list and moved to Trash, do you want to remove it?' mod='ets_ordermanager' js=1}')){ldelim}return true;{rdelim}else{ldelim}event.stopPropagation(); event.preventDefault();{rdelim};"{/if} class="delete">
    	<i class="icon-trash"></i>{$action|escape:'html':'UTF-8'}
    </a>
 {/if}
