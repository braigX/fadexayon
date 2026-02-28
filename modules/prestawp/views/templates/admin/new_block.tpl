{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

<div id="pswp-toolbar" class="ps{$psvd|intval}">
    <div class="pswp-block-instructions">
        {l s='Here you can manage custom blocks of posts in your Front Office.' mod='prestawp'} <br>
        {l s='For example, you can show chosen WordPress posts in some PrestaShop category:' mod='prestawp'}
        <ol>
            <li>{l s='Set the option "Position" to "Left column".' mod='prestawp'}</li>
            <li>{l s='Select posts or categories in the "Select posts to display" option.' mod='prestawp'}</li>
            <li>{l s='Select appropriate PrestaShop category in the "Show only in these categories" option.' mod='prestawp'}</li>
        </ol>
    </div>

    <a id="pswp-new-block-btn" class="toolbar_btn pointer btn btn-default" href="{$form_url|escape:'quotes':'UTF-8'}" title="{l s='Add new block' mod='prestawp'}">
        <i class="icon-plus"></i>
        {l s='New block' mod='prestawp'}
    </a>
</div>
