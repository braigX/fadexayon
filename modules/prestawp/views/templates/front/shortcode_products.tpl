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
    <div class="pswp-products-wrp">
        <section class="featured-products clearfix pswp_block">
            <div class="products">
                {if $pswp_psv >= 1.7}
                    {foreach from=$pswp_products item="product" key="position"}
                        {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                    {/foreach}
                {else}
                    {include file="$pswp_tpl_dir./product-list.tpl" class='pswp_products tab-pane' id='pswp_products' products=$pswp_products page_name='index'}
                {/if}
            </div>
        </section>
    </div>
{/if}