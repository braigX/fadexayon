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
{block name='head'}
    {$smarty.block.parent}
    {if isset($seo_social.facebook_og) && $seo_social.facebook_og}
      <meta property="og:type" content="product">
      <meta property="og:url" content="{$urls.current_url|escape:'quotes':'UTF-8'}">
      <meta property="og:title" content="{if isset($ets_seo_social.title) && $ets_seo_social.title}{$ets_seo_social.title|escape:'html':'UTF-8'}{else}{$page.meta.title|escape:'html':'UTF-8'}{/if}">
      <meta property="og:site_name" content="{$shop.name|escape:'html':'UTF-8'}">
      <meta property="og:description" content="{if isset($ets_seo_social.desc) && $ets_seo_social.desc}{$ets_seo_social.desc|escape:'html':'UTF-8'}{else}{$page.meta.description|escape:'html':'UTF-8'}{/if}">
      <meta property="og:image" content="{if isset($ets_seo_social.image) && $ets_seo_social.image}{$ets_seo_social.image|escape:'quotes':'UTF-8'}{else}{if isset($product.cover) && $product.cover}{$product.cover.large.url|escape:'quotes':'UTF-8'}{/if}{/if}">
    {/if}
    {if $product.show_price}
      <meta property="product:pretax_price:amount" content="{$product.price_tax_exc|escape:'html':'UTF-8'}">
      <meta property="product:pretax_price:currency" content="{$currency.iso_code|escape:'html':'UTF-8'}">
      <meta property="product:price:amount" content="{$product.price_amount|escape:'html':'UTF-8'}">
      <meta property="product:price:currency" content="{$currency.iso_code|escape:'html':'UTF-8'}">
    {/if}
    {if isset($product.weight) && ($product.weight != 0)}
      <meta property="product:weight:value" content="{$product.weight|escape:'html':'UTF-8'}">
      <meta property="product:weight:units" content="{$product.weight_unit|escape:'html':'UTF-8'}">
    {/if}
    {if isset($ets_seo_social.twitter_card) && $ets_seo_social.twitter_card}
      <meta name="twitter:title" content="{$ets_seo_social.title|escape:'html':'UTF-8'}">
      <meta name="twitter:description" content="{$ets_seo_social.desc|escape:'html':'UTF-8'}">
      <meta name="twitter:image" content="{$ets_seo_social.image|escape:'quotes':'UTF-8'}">
      <meta name="twitter:card" content="{if $ets_seo_social.twitter_card_type}{$ets_seo_social.twitter_card_type|escape:'html':'UTF-8'}{else}summary{/if}">
        {if $ets_seo_social.twitter_name}
          <meta name="twitter:site" content="@{$ets_seo_social.twitter_name|escape:'html':'UTF-8'}" />
          <meta name="twitter:creator" content="@{$ets_seo_social.twitter_name|escape:'html':'UTF-8'}" />
        {/if}
    {/if}
    {if $ets_seo_social.pinterest_verification}
      <meta name="p:domain_verify" content="{$ets_seo_social.pinterest_verification|escape:'html':'UTF-8'}" />
    {/if}
    {if $ets_seo_social.baidu_verification}
      <meta name="baidu-site-verification" content="{$ets_seo_social.baidu_verification|escape:'html':'UTF-8'}" />
    {/if}
    {if $ets_seo_social.bing_verification}
      <meta name="msvalidate.01" content="{$ets_seo_social.bing_verification|escape:'html':'UTF-8'}" />
    {/if}
    {if $ets_seo_social.google_verification}
      <meta name="google-site-verification" content="{$ets_seo_social.google_verification|escape:'html':'UTF-8'}" />
    {/if}
    {if $ets_seo_social.yandex_verification}
      <meta name="yandex-verification" content="{$ets_seo_social.yandex_verification|escape:'html':'UTF-8'}" />
    {/if}
    {if $ets_seo_graph_knowledge}
      <script type='application/ld+json' class='ets-seo-schema-graph--main'>
            {$ets_seo_graph_knowledge nofilter}
        </script>
    {/if}
{/block}