{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2020 Presta.Site
* @license   LICENSE.txt
*}
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {l s='You can choose posts for each product on the product edit page.' mod='prestawp'}

    {l s='Example:' mod='prestawp'}
    {if $pswp_psv <= 1.6}
        <a target="_blank" href="{$pswp_link->getAdminLink('AdminProducts')|escape:'quotes':'UTF-8'}&id_product={$pswp_random_product_id|intval}&updateproduct" tabindex="-1">{l s='link' mod='prestawp'}</a>,
        {l s='tab "PrestaShop-WordPress two-way integration"' mod='prestawp'}
    {else}
        <a target="_blank" href="{$pswp_link->getAdminLink('AdminProducts', true, ['id_product' => $pswp_random_product_id])|escape:'quotes':'UTF-8'}" tabindex="-1">{l s='link' mod='prestawp'}</a>,
        {l s='tab "Modules" >> "PrestaShop-WordPress two-way integration"' mod='prestawp'}
    {/if}
</div>