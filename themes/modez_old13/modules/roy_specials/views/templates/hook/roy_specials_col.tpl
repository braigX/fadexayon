<div id="roy_specials_col" class="sidebar-block">
  <section class="specials-col facet">
    <h4 class="sidebar-title facet-label hidden-sm-down">
      <a href="{$allSpecialProductsLink}">
        {l s='On sale' d='Shop.Theme.Catalog'}
        <i></i>
      </a>
    </h4>
    <div class="title clearfix hidden-md-up" data-target="#side_spec" data-toggle="collapse">
      <span class="h3">{l s='On sale' d='Shop.Theme.Catalog'}</span>
      <span class="float-xs-right">
        <span class="navbar-toggler collapse-icons">
          <i class="material-icons add">&#xE313;</i>
          <i class="material-icons remove">&#xE316;</i>
        </span>
      </span>
    </div>
    <div id="side_spec" class="sidebar-content collapse in">
      <div class="products">
        {foreach from=$products item="product"}
          {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        {/foreach}
      </div>
    </div>
  </section>
</div>
