{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL

* @license   INNOVADELUXE
*}

{if $idxr_show_favorite_card}
<li>
    <a title="{l s='Customized products' mod='idxrcustomproduct'}" href="{$link->getModuleLink('idxrcustomproduct','favorite')|escape:'htmlall':'UTF-8'}">
        <i class="fa fa-save icon icon-save"></i>
        <span>{l s='Customized products' mod='idxrcustomproduct'}</span>
    </a>
</li>
{/if}
{if $idxr_show_simulations_card}
<li>
    <a title="{l s='My simulations' mod='idxrcustomproduct'}" href="{$link->getModuleLink('idxrcustomproduct','simulations')|escape:'htmlall':'UTF-8'}">
        <i class="fa fa-flask"></i>
        <span>{l s='My simulations' mod='idxrcustomproduct'}</span>
    </a>
</li>
{/if}
