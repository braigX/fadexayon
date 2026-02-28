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

{if isset($pswp_products) && is_array($pswp_products) && count($pswp_products)}
    <div class="block products_block exclusive pswp_block container {if $pswp_limit_mobile}pswp-mobile-limit-{$pswp_limit_mobile|intval}{/if}">
        <div class="block_content">
            {include file="$tpl_dir./product-list.tpl" class='pswp_products tab-pane' id='pswp_products' products=$pswp_products page_name='index'}
        </div>
    </div>
{/if}