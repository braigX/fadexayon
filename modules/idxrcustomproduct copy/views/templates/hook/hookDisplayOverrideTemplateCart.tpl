{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2020 Innova Deluxe SL
* @license   INNOVADELUXE
*}

{extends file='checkout/cart.tpl'}

{block name='cart_detailed_product_line'}
    {include file='../../../../../modules/idxrcustomproduct/views/templates/hook/cart-detailed-product-line-override.tpl' product=$product}
{/block}
