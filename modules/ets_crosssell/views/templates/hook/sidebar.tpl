{*
* 2007-2022 ETS-Soft
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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}
<script type="text/javascript">
    var ets_cs_link_search_product ='{$ets_cs_link_search_product nofilter}';
</script>
<div class="etscs-left-panel col-lg-2">
    <div class="list-group">
        {foreach from = $sidebars key='key' item='sidebar'}
            <a class="list-group-item{if $control==$key} active{/if}" href="{$cs_link_module|escape:'html':'UTF-8'}&control={$key|escape:'html':'UTF-8'}">
                <i class="icon-{$key|escape:'html':'UTF-8'}"></i>
                {$sidebar|escape:'html':'UTF-8'}
            </a>
        {/foreach}
    </div>
    {if isset($intro) && $intro}
    <div class="li_othermodules ">
        <a class="{if isset($refsLink) && $refsLink}refs_othermodules{else}link_othermodules{/if}" href="{$other_modules_link|escape:'html':'UTF-8'}"
           {if isset($refsLink) && $refsLink}target="_blank" {/if}>
            <span class="tab-title">{l s='Other modules' mod='ets_crosssell'}</span>
            <span class="tab-sub-title">{l s='Made by ETS-Soft' mod='ets_crosssell'}</span>
        </a>
    </div>
    {/if}
</div>