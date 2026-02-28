{**
 *  2024 ALGO-FACTORY.COM
 *
 *  NOTICE OF LICENSE
 *
 * @author        Algo Factory <contact@algo-factory.com>
 * @copyright     Copyright (c) 2024 Algo Factory
 * @license   You are just allowed to modify this copy for your own use. You must not redistribute it. License
 *               is permitted for one Prestashop instance only but you can install it on your test instances.
 *
 * @version       1.0.0
 * @website       www.algo-factory.com
 *
 *  You can not resell or redistribute this software.
 *}
<script id="add-to-cart-template" type="x-tmpl-mustache">
{literal}
    <div id="afAddToCart" style="background-color: {{barBackgroundColor}}; color: {{barTextColor}};">
    		<div class="productInfo">
					<div id="titleProduct">
							{{product.name}}
					</div>
					<div id="productPrice">

            {{#product.formatted_price}}
            	<span class="regular-price">{{product.formatted_price}}</span>
            {{/product.formatted_price}}

            {{product.price}}
					</div>
				</div>
        <a href="#" class="btn btn-primary add-to-cart-bar" id="addToCart" style="background-color: {{btnBackgroundColor}}; color: {{btnTextColor}};">
						{/literal}
							{l s='Add to cart' d='Shop.Theme.Actions'}
						{literal}
        </a>
    </div>
{/literal}
</script>
