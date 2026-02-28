{**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file=$layout}

{*{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}">
{/block}*}

{block name='head' append}
  <meta  content="product">
  <meta  content="{$urls.current_url}">
  <meta  content="{$page.meta.title}">
 <meta  content="{$shop.name}">
  <meta  content="{$page.meta.description}">
  <meta  content="{$product.cover.large.url}">
  {if $product.show_price}
    <meta  content="{$product.price_tax_exc}">
    <meta  content="{$currency.iso_code}">
    <meta  content="{$product.price_amount}">
    <meta  content="{$currency.iso_code}">
  {/if}
  {if isset($product.weight) && ($product.weight != 0)}
  <meta  content="{$product.weight}">
  <meta  content="{$product.weight_unit}">
  {/if}
{/block}

{block name='content'}

  <section id="main"  >
    <meta  content="{$product.url}">

    <div class="container">
      <div class="product-header">
			            {block name='page_header_container'}
              {block name='page_header'}
                <h1 class="h1 product-title" >{block name='page_title'}{$product.name}{/block}</h1>
             
              {/block}
              
            {/block}
            

			</div>






     <div class="row">
      <div class="col-lg-6 col-image">
        {block name='page_content_container'}
          <section class="col-image-inside">
            {block name='page_content'}
              {block name='product_flags'}
                {include file='catalog/_partials/product-flags.tpl'}
              {/block}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}

            {/block}
          </section>
        {/block}
      </div>
      <div class="col-lg-6 col-content">
          <div class="col-content-inside">

            {*block name='page_header_container'}
              {block name='page_header'}
                <h1 class="h1 product-title" >{block name='page_title'}{$product.name}{/block}</h1>
              {/block}
            {/block*}

            {if isset($nb_comments) && $nb_comments > 0}            
              <div class="comments-note">
                {include file='module:productcomments/views/templates/hook/average-grade-stars.tpl' grade=$average_grade}
                <a class="nb-comments noeffect goreviews" href="#tabsection"><span >{l s='%s'|sprintf:$nbComments mod='productcomments'}</span> {if isset($nbComments) && $nbComments == 1}{l s='Review'}{else}{l s='Reviews'}{/if}</a>
                
                <div   >
                  <meta  content="{$nb_comments}" />
                  <meta  content="{$average_grade}" />
                </div>
              </div>
             {/if}
            
             {widget name='productcomments' hook='displayProductExtraContent'}
		         	{*Add with team wassim novatis*}
              {* <div class="info-prod">
                 {block name='product_features'}
                  {if $product.features}
                   {foreach from=$product.features item=feature}
                   {if $feature.id_feature == '3'}
                    <div class="product-material">
                     <label class="label">{$feature.name}:</label>
                     <span >{$feature.value}</span>
                    </div>
                   {/if}
                   {/foreach}
                  {/if}
                {/block}
             </div>*}
            {*End*}
            <div class="product-information">
              {if $product.is_customizable && count($product.customizations.fields)}
                {block name='product_customization'}
                  {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
                {/block}
              {/if}

              <div class="product-actions">
                {block name='product_buy'}
                  <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                    <input type="hidden" name="token" value="{$static_token}">
                    <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                    <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">

                    {block name='product_variants'}
                      {include file='catalog/_partials/product-variants.tpl'}
                    {/block}

                    {block name='product_pack'}
                      {if $packItems}
                        <section class="product-pack">
                          <h3 class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</h3>
                          {foreach from=$packItems item="product_pack"}
                            {block name='product_miniature'}
                              {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                            {/block}
                          {/foreach}
                        </section>
                      {/if}
                    {/block}

                    {block name='product_discounts'}
                      {include file='catalog/_partials/product-discounts.tpl'}
                    {/block}

                    {block name='product_prices'}
                      {include file='catalog/_partials/product-prices.tpl'}
                    {/block}

                    {block name='product_out_of_stock'}
                      <div class="product-out-of-stock">
                        {hook h='actionProductOutOfStock' product=$product}
                      </div>
                    {/block}

                    {block name='product_refresh'}
                      <input class="product-refresh ps-hidden-by-js" name="refresh" type="submit" value="{l s='Refresh' d='Shop.Theme.Actions'}">
                    {/block}
                  </form>
                {/block}
              </div>
            {*Add with team wassim novatis*}
            {if $product.features}
             {foreach from=$product.features item=feature}
               {if $feature.id_feature == '4'}
                          <input type="hidden" name="product_thickness" id="product_thickness" value="{$feature.value}">
               {/if}
               {if $feature.id_feature == '5'}
                   <input type="hidden" name="product_longueur_max" id="product_longueur_max" value="{$feature.value}">
               {/if}
               {if $feature.id_feature == '8'}
                   <input type="hidden" name="product_longueur_min" id="product_longueur_min" value="{$feature.value}">
                      {/if}
               {if $feature.id_feature == '6'}
                   <input type="hidden" name="product_Largeur_max" id="product_Largeur_max" value="{$feature.value}">
               {/if}
               {if $feature.id_feature == '7'}
                   <input type="hidden" name="product_Largeur_min" id="product_Largeur_min" value="{$feature.value}">
               {/if}
               {if $feature.id_feature == '9'}
                   <input type="hidden" name="product_density" id="product_density" value="{$feature.value}">
               {/if}
             {/foreach}
            {/if}
          {*end*}
            {block name='product_additional_info'}
              {include file='catalog/_partials/product-additional-info.tpl'}
            {/block}
          </div>
        </div>
      </div>
      </div>
    </div>
    </section>

    <section>
      <div class="container">
      {*<div class="col-lg-6">*}
      <div class="col-lg-12">
        {block name='product_description_short'}
        
          <div id="product-description-short-{$product.id}" class="product-short-desc">{$product.description_short nofilter}</div>
        {/block}
      </div></div>
      </section>

   {block name='product_accessories'}
      {if $accessories}
        <section class="product-accessories featured-products slider-on clearfix mt-3 first">

          <div class="container">
          <div class="pp_products_wrapper">
            <h2 class="products-section-title">
              {l s='You might also like' d='Shop.Theme.Catalog'}
            </h2>
            <div class="products">
            {foreach from=$accessories item="product_accessory" key="position"}
              {block name='product_miniature'}
                {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory position=$position}
              {/block}
            {/foreach}
            </div>
          </div>
          </div>
        </section>
      {/if}
    {/block}

    {block name='product_tabs'}
    <section class="section--grey">
     <div class="container">
      <div id="product-container-bottom" class="tabs">
        <ul class="nav nav-tabs" role="tablist">
          {if $product.description}
            <li class="nav-item">
               <a
                 class="nav-link{if $product.description} active{/if}"
                 data-toggle="tab"
                 href="#description"
                 role="tab"
                 aria-controls="description"
                 {if $product.description} aria-selected="true"{/if}>{l s='Description' d='Shop.Theme.Catalog'}</a>
            </li>
          {/if}
          {if $product.features}
          <li class="nav-item">
            <a
              class="nav-link {if !$product.description} active{/if}"
              data-toggle="tab"
              href="#product-details"
              role="tab"
              aria-controls="product-details"
              {if !$product.description} aria-selected="true"{/if}>{l s='Product Details' d='Shop.Theme.Catalog'}</a>
          </li>
          {/if}
          {if $product.attachments}
            <li class="nav-item">
              <a
                class="nav-link"
                data-toggle="tab"
                href="#attachments"
                role="tab"
                aria-controls="attachments">{l s='Attachments' d='Shop.Theme.Catalog'}</a>
            </li>
          {/if}
          
          {foreach from=$product.extraContent item=extra key=extraKey}
            <li class="nav-item">
              <a
                class="nav-link"
                data-toggle="tab"
                href="#extra-{$extraKey}"
                role="tab"
                aria-controls="extra-{$extraKey}">{$extra.title}</a>
            </li>
          {/foreach}
        </ul>

        <div class="tab-content" id="tab-content">
         <div class="tab-pane fade in{if $product.description} active{/if}" id="description" role="tabpanel">
           {block name='product_description'}
             <div class="product-description ">{$product.description nofilter}</div>
           {/block}
         </div>

         {block name='product_details'}
           {include file='catalog/_partials/product-details.tpl'}
         {/block}

         {block name='product_attachments'}
           {if $product.attachments}
            <div class="tab-pane fade in" id="attachments" role="tabpanel">
               <section class="product-attachments">
                 <h3 class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</h3>
                 {foreach from=$product.attachments item=attachment}
                   <div class="attachment">
                     <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                     <p>{$attachment.description}</p
                     <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                       {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                     </a>
                   </div>
                 {/foreach}
               </section>
             </div>
           {/if}
         {/block}

         {foreach from=$product.extraContent item=extra key=extraKey}
         <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
           {$extra.content nofilter}
         </div>
         {/foreach}
      </div>
      </div>
      </div>
    </section>
  {/block}
<section class="section">
	<div class="container">
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

{** ajouté par AD pour bloquer catégorie racine **}
{block name='head_seo'}
  {$smarty.block.parent}
  {if isset($product.id_category_default) && $product.id_category_default == 2}
    <meta name="robots" content="noindex,follow">
  {/if}
{/block}

