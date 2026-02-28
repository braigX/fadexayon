<div class="product-img">
	<span class="image-cont">
		<img class="product-image" src="{$product.cover.small.url}?format=webp" alt="{$product.cover.legend}" title="{$product.cover.legend}">
	</span>
  {if $product.quantity > 1}
    <span class="product-quantity">{$product.quantity}</span>
  {/if}
	<a class="remove-from-cart tip_inside"
	    rel="nofollow"
	    href="{$product.remove_from_cart_url}"
	    data-link-action="remove-from-cart"
	    title=""
	>
	  <i>
	    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><path d="M19.78,17,14.83,12l4.95-4.95a1,1,0,0,0,0-1.41L18.36,4.24a1,1,0,0,0-1.41,0L12,9.19,7.05,4.24a1,1,0,0,0-1.41,0L4.22,5.65a1,1,0,0,0,0,1.41L9.17,12,4.22,17a1,1,0,0,0,0,1.41L5.64,19.8a1,1,0,0,0,1.41,0L12,14.85l4.95,4.95a1,1,0,0,0,1.41,0l1.41-1.41A1,1,0,0,0,19.78,17Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg>
	  </i>
	  <span class="tip">{l s='remove from cart' d='Shop.Theme.Actions'}</span>
	</a>
</div>
<span class="product-name">{$product.name}</span>
<div class="product-atts">
  {foreach from=$product.attributes item="property_value" key="property"}
    <span>{$property}: {$property_value}</span>
  {/foreach}
</div>
<span class="product-price">{$product.price}</span>
