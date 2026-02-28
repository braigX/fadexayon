<div class="bread_wrapper">
  <div class="container">
    <nav data-depth="{$breadcrumb.count}" class="breadcrumb hidden-sm-down" aria-label="Fil d’Ariane">
      <ol  >
        {foreach from=$breadcrumb.links item=path name=breadcrumb}
          {block name='breadcrumb_item'}
            <li   >
              {if !$smarty.foreach.breadcrumb.last}
                <a  href="{$path.url}" title="Aller à la page {$path.title|escape:'htmlall':'UTF-8'}">
                  <span >{$path.title}</span>
                </a>
              {else}
                <span >{$path.title}</span>
              {/if}
              <meta  content="{$smarty.foreach.breadcrumb.iteration}">
            </li>
          {/block}
        {/foreach}
      </ol>
    </nav>
  </div>

  {if $page.page_name == 'category'}
    <div class="hero_background"></div>
    <div class="container">
      <div class="productpage">
        <div class="nova_content">
          <h1 class="nova_title">{$category.name}</h1>
          <div class="nova_subtitle">Découvrez notre gamme</div>
        </div>
      </div>
    </div>
  {/if}
</div>
