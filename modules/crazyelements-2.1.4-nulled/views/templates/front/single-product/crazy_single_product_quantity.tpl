<div class="product-quantity clearfix">
  <div class="crazy-{$elementprefix} qty">
    <input
      type="number"
      name="qty"
      id="quantity_wanted"
      value="{$product.quantity_wanted}"
      class="input-group"
      min="{$product.minimal_quantity}"
      aria-label="{l s='Quantity' d='Shop.Theme.Actions'}"
      form="add-to-cart-or-refresh"
    >
  </div>
</div>