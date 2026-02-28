<div class="tab-pane fade{if !$product.description} in active{/if}"
     id="product-details"
     data-product="{$product.embedded_attributes|json_encode}"
     role="tabpanel"
  >
  {block name='product_features'}
    {if $product.features}
      <section class="product-features">
        {*<h3 class="h6">{l s='Data sheet' d='Shop.Theme.Catalog'}</h3>*}
        <dl class="data-sheet">
          {foreach from=$product.features item=feature}
            <dt class="name">{$feature.name}</dt>
            <dd class="value">{$feature.value}</dd>
          {/foreach}
        </dl>
      </section>
    {/if}
  {/block}

  {* if product have specific references, a table will be added to product details section *}
 {* {block name='product_specific_references'}
    {if isset($product.specific_references)}
      <section class="product-features">
        <h3 class="h6">{l s='Specific References' d='Shop.Theme.Catalog'}</h3>
          <dl class="data-sheet">
            {foreach from=$product.specific_references item=reference key=key}
              <dt class="name">{$key}</dt>
              <dd class="value">{$reference}</dd>
            {/foreach}
          </dl>
      </section>
    {/if}
  {/block}*}

</div>
