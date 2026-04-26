{if file_exists("$theme_dir/_partials/helpers.tpl")}
    {include file="$theme_dir/_partials/helpers.tpl"}
{else}
  {if isset($parent_theme_dir)}
    {if file_exists("$parent_theme_dir/_partials/helpers.tpl")}
      {include file="$parent_theme_dir/_partials/helpers.tpl"}
    {/if}
  {/if}
{/if}
<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

    <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
    </header>

      {block name='notifications'}
        {include file='_partials/notifications.tpl'}
      {/block}

    {block name='page_content'}
      {$parsed_content nofilter}
    {/block}

    <footer id="footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>
    </main>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}
  </body>

</html>