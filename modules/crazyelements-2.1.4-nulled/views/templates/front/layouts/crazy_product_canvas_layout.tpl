
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    <head>
      {block name='head'}
        {include file='_partials/head.tpl'}
      {/block}
    </head>
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

      {block name='content'}
        <section id="main" class="classy_layout_parent" itemscope itemtype="https://schema.org/Product">
          <meta itemprop="url" content="{$product.url}">
          <input type="hidden" value="{$parsed}">
          <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
            <input type="hidden" name="token" value="{$static_token}">
            <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
            {$parsed_content nofilter}
          </form>
          {block name='product_images_modal'}
            {include file='catalog/_partials/product-images-modal.tpl'}
          {/block}
        </section>
      {/block}


    </main>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}
  </body>

</html>