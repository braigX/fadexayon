{extends file="layouts/layout-full-width.tpl"}
{block name='content'}
  <section id="main" class="classy_layout_parent"  >
    <div class="product-container">
      <meta  content="{$product.url}">
      <input type="hidden" value="{$parsed}">
      <div class="product-actions">
        <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
          <input type="hidden" name="token" value="{$static_token}">
          <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
          {$parsed_content nofilter}
        </form>
      </div>
      {block name='product_footer'}
        {hook h='displayFooterProduct' product=$product category=$category}
      {/block}
      {block name='product_images_modal'}
        {include file='catalog/_partials/product-images-modal.tpl'}
      {/block}
      {block name='page_footer_container'}
        <footer class="page-footer">
          {block name='page_footer'}
            <!-- Footer content -->
          {/block}
        </footer>
      {/block}
    </div>
  </section>
{/block}