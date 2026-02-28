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

{extends file='page.tpl'}

{block name='breadcrumb'}{/block}

{block name='page_content_container'}
    {block name='page_content_top'}{/block}
    {block name='page_content'}
        {if isset($pswp_products) && is_array($pswp_products) && count($pswp_products)}
            <div class="block products_block exclusive featured-products pswp_block {if $pswp_limit_mobile}pswp-mobile-limit-{$pswp_limit_mobile|intval}{/if}">
                <div class="block_content">
                    {if $pswp_wrps}
                        {foreach from=$pswp_wrps item='wrp'}
                            <div {if $wrp.id}id="{$wrp.id|escape:'html':'UTF-8'}"{/if} {if $wrp.classes}class="{$wrp.classes|escape:'html':'UTF-8'}" {/if}>
                        {/foreach}
                    {else}{/if}

                        {include file="catalog/_partials/products.tpl"}

                    {if $pswp_wrps}
                        {foreach from=$pswp_wrps item='wrp'}
                            </div>
                        {/foreach}
                    {else}{/if}
                </div>
            </div>
        {/if}
    {/block}
{/block}

{block name="footer"}{/block}

{block name='pagination'}{/block}