{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}

<div class="block-testimonial">
  <h3 class="panel-heading">{l s='What they say about us' mod='ets_reviews'}</h3>
  <div class="panel-testimonial owl-carousel"
       data-carousel='{literal}{"items": 1, "loop": true, "center": false, "margin": 0, "autoWidth": false, "rtl": false, "autoHeight": false, "autoplay": true, "autoplayTimeout": 5000, "nav": false, "dots": true}{/literal}'>
      {if $product_comments}
          {foreach from=$product_comments item='product_comment'}
            <article class="panel-testimonial-item">
              <a href="{$product_comment.link_product|escape:'quotes':'UTF-8'}">
                <img class="panel-testimonial-avatar"
                     src="{$product_comment.link_image_product|escape:'quotes':'UTF-8'}"
                     width="80px" height="80px" alt=""
                     style="width:80px"/>
              </a>
              <div class="panel-testimonial-rating" data-rating='{literal}{"score" : {/literal}{$product_comment.grade|floatval}{literal}, "readOnly": true}{/literal}'></div>
              <p class="panel-testimonial-summary">{$product_comment.content nofilter} </p>
              <h4 class="panel-testimonial-name">{$product_comment.customer_name|escape:'html':'UTF-8'}</h4>
            </article>
          {/foreach}
      {/if}
  </div>
</div>