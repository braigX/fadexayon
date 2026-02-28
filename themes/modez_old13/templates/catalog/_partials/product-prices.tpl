
{if $product.show_price}
<div class="product-prices js-product-prices">
<div class="product-prices-block">
<div class="prices-block">
<div class="name-price"><span>Prix</span></div>

   {block name='product_without_taxes'}
      {if $priceDisplay == 0}
        <div class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc|number_format:2]}</div>
      {/if}
    {/block}

   {block name='product_price'}
      <div
        class="product-price h5 {if $product.has_discount}has-discount{/if}"
        
        
        
      >
        <link   href="{$product.seo_availability}"/>
        <meta  content="{$currency.iso_code}">
          {if isset($product.specific_prices.to)}
    <meta  content="{$product.specific_prices.to|date_format:'%Y-%m-%d'}">
  {/if}


        <div class="current-price">
          <span  content="{$product.price_amount}">{$product.price}</span>
		  <span>TTC</span>
        </div>
        {if $product.has_discount}
          <div class="product-discount">
            {hook h='displayProductPriceBlock' product=$product type="old_price"}
            <span class="regular-price"><span>{l s='Old price' d='Shop.Theme.Catalog'}</span> {$product.regular_price}</span>
          </div>
          {if $product.discount_type === 'percentage'}
            <span class="discount discount-percentage">{l s='Discount -%percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
          {else}
            <span class="discount discount-amount">
                {l s='Discount -%amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
            </span>
          {/if}
        {/if}

        {block name='product_unit_price'}
          {if $displayUnitPrice}
            <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}</p>
          {/if}
        {/block}
      </div>
    {/block}
</div>
    {block name='product_pack_price'}
      {if $displayPackPrice}
        <p class="product-pack-price"><span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span></p>
      {/if}
    {/block}

    {block name='product_ecotax'}
      {if $product.ecotax.amount > 0}
        <p class="price-ecotax">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
          {if $product.has_discount}
            {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
          {/if}
        </p>
      {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

    {if isset($product.specific_prices.from) || isset($product.specific_prices.to)}
      {if ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $product.specific_prices.from && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' < $product.specific_prices.to)}
        <div class="product_count_block">
          <div class="countcontainer">
            <div class="count_icon">
              <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 viewBox="0 0 162 162" style="enable-background:new 0 0 162 162;" xml:space="preserve">
              <g>
                <path class="hand hand-min" d="M126,87.5H81c-3.6,0-6.5-2.9-6.5-6.5s2.9-6.5,6.5-6.5h45c3.6,0,6.5,2.9,6.5,6.5S129.6,87.5,126,87.5z"/>
              </g>
              <g>
                <path class="hand hand-hour" d="M81,87.5c-3.6,0-6.5-2.9-6.5-6.5V36c0-3.6,2.9-6.5,6.5-6.5s6.5,2.9,6.5,6.5v45C87.5,84.6,84.6,87.5,81,87.5z"/>
              </g>
              <path d="M81,13c37.5,0,68,30.5,68,68s-30.5,68-68,68s-68-30.5-68-68S43.5,13,81,13 M81,0C36.3,0,0,36.3,0,81s36.3,81,81,81
                s81-36.3,81-81S125.7,0,81,0L81,0z"/>
              </svg>
            </div>

            <div class="count_other">
              <div class="roycounttitle">
                <span>{l s='Limited Special Offer! Expires in:' d='Shop.Theme.Catalog'}</span>
              </div>
              <div class="roycountdown">
                <div class="roycount" style="display: none;" data-specific-price-to="{$product.specific_prices.to}" data-days={l s='Days' d='Shop.Theme.Catalog'} data-hours={l s='Hours' d='Shop.Theme.Catalog'} data-minutes={l s='Minutes' d='Shop.Theme.Catalog'} data-seconds={l s='Seconds' d='Shop.Theme.Catalog'}></div>
              </div>
            </div>
          </div>
        </div>
      {/if}
    {/if}
     {block name='product_quantities'}
      {if isset($roythemes.pp_display_q) && $roythemes.pp_display_q == "1"}
       {if $product.show_quantities}
         <div class="product-quantities">
           <label class="label">{l s='In stock' d='Shop.Theme.Catalog'} :</label>
           <span data-stock="{$product.quantity}" data-allow-oosp="{$product.allow_oosp}">{$product.quantity} unités</span>
         </div>
       {/if}
      {/if}
     {/block}
     
    <div class="tax-shipping-delivery-label">
      {if isset($configuration.taxes_enabled) && !$configuration.taxes_enabled}
        {l s='No tax' d='Shop.Theme.Catalog'}
      {elseif $configuration.display_taxes_label}
        {*$product.labels.tax_long*}
      {/if}
      {hook h='displayProductPriceBlock' product=$product type="price"}
      {hook h='displayProductPriceBlock' product=$product type="after_price"}
      {if $product.is_virtual	== 0}
        {if $product.additional_delivery_times == 1}
          {if $product.delivery_information}
            <span class="delivery-information">{$product.delivery_information}</span>
          {/if}
		  {elseif $product.additional_delivery_times == 2}
		  {if $product.quantity > 0}
			<span class="delivery-information">{$product.delivery_in_stock}</span>
		  {* Out of stock message should not be displayed if customer can't order the product. *}
			{elseif $product.quantity <= 0 && $product.add_to_cart_url}
			<span class="delivery-information">{$product.delivery_out_stock}</span>
		  {/if}
		{/if}
      {/if}
    </div>
</div>
    {block name='product_add_to_cart'}
      {include file='catalog/_partials/product-add-to-cart.tpl'}
    {/block}


    {*<div class="product-info">
     {block name='product_quantities'}
      {if isset($roythemes.pp_display_q) && $roythemes.pp_display_q == "1"}
       {if $product.show_quantities}
         <div class="product-quantities">
           <label class="label">{l s='In stock' d='Shop.Theme.Catalog'}</label>
           <span data-stock="{$product.quantity}" data-allow-oosp="{$product.allow_oosp}">{$product.quantity}</span>
         </div>
       {/if}
      {/if}
     {/block}

     {block name='product_reference'}
      {if isset($roythemes.pp_display_refer) && $roythemes.pp_display_refer == "1"}
       {if isset($product.reference_to_display)}
         <div class="product-reference">
           <label class="label">{l s='Reference' d='Shop.Theme.Catalog'} </label>
           <span >{$product.reference_to_display}</span>
         </div>
       {/if}
      {/if}
     {/block}

     {block name='product_availability_date'}
       {if $product.availability_date}
         <div class="product-availability-date">
           <label>{l s='Availability date:' d='Shop.Theme.Catalog'} </label>
           <span>{$product.availability_date}</span>
         </div>
       {/if}
     {/block}

     {block name='product_condition'}
      {if isset($roythemes.pp_display_cond) && $roythemes.pp_display_cond == "1"}
       {if $product.condition}
         <div class="product-condition">
           <label class="label">{l s='Condition' d='Shop.Theme.Catalog'} </label>
           <link  href="{$product.condition.schema_url}"/>
           <span>{$product.condition.label}</span>
         </div>
       {/if}
      {/if}
     {/block}

     {if isset($product_manufacturer->id)}
      {if isset($roythemes.pp_display_brand) && $roythemes.pp_display_brand == "1"}
       <div class="product-manufacturer tip_inside">
         <a href="{$product_brand_url}">
           {if isset($manufacturer_image_url)}
               <img src="{$manufacturer_image_url}" class="img img-thumbnail manufacturer-logo" alt="{$product_manufacturer->name}" height="140" width="140">
           {/if}
           <div class="manu_text">
             <label class="label">{l s='Brand' d='Shop.Theme.Catalog'}</label>
             <span >
               {$product_manufacturer->name}
             </span>
           </div>
         </a>
           <span class='tip'>
             {l s='View all products of ' d='Shop.Theme.Catalog'}{$product_manufacturer->name}
           </span>
       </div>
     {/if}
     {/if}
    </div>*}
  </div>

{/if}
