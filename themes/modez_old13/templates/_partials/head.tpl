{block name='head_charset'}
  <meta charset="utf-8">
{/block}
{block name='head_ie_compatibility'}
  <meta http-equiv="x-ua-compatible" content="ie=edge">
{/block}

{block name='head_seo'}
  {if $smarty.server.HTTP_HOST == 'panneau-polycarbonate.fr' && $page.page_name == 'index'}
    <title>Plaque Polycarbonate Sur Mesure – Découpe & Livraison Rapide</title>
    <meta property="og:title" content="Panneau Polycarbonate Sur Mesure">
    <meta name="description" content="Commandez vos panneaux polycarbonate sur mesure en ligne. Résistants, légers, transparents ou opaques. Livraison rapide & découpe de précision en France.">
    <meta name="keywords" content="panneau polycarbonate, panneau polycarbonate sur mesure, plaque polycarbonate, polycarbonate alvéolaire, polycarbonate compact, découpe polycarbonate, panneau plastique transparent, panneau toiture polycarbonate, panneau polycarbonate transparent, panneau polycarbonate rigide, panneau polycarbonate pas cher">

  {elseif $smarty.server.HTTP_HOST == 'vitrine-plexiglass.fr' && $page.page_name == 'index'}
    <title>Vitrine Plexiglass Sur Mesure – Fabrication & Livraison Rapide</title>
    <meta property="og:title" content="Configurez votre Vitrine Plexiglass Sur Mesure en Ligne">
    <meta name="description" content="Configurez votre vitrine plexiglass sur mesure en ligne : dimensions, finitions, éclairage LED, gravure. Fabrication rapide & livraison France.">
    <meta name="keywords" content="vitrine plexiglass, vitrine sur mesure, caisson acrylique, vitrine plexi transparente, cloche PMMA, fabrication vitrine musée, caisson plexiglas LED">

  {elseif $smarty.server.HTTP_HOST == 'decoupe-plexiglass.fr' && $page.page_name == 'index'}
    <title>Plexiglass Sur Mesure – Découpe Professionnelle & Livraison Rapide</title>
    <meta property="og:title" content="Découpe Plexiglass Sur Mesure en Ligne – Livraison France">
    <meta name="description" content="Découpe de plaque plexiglass sur mesure : transparent, coloré, opale, extrudé ou coulé. Prix en ligne, découpe au laser, livraison rapide partout en France.">
    <meta name="keywords" content="plexiglass sur mesure, découpe plexiglass, plaque plexi, plexiglas, altuglass, panneau acrylique, découpe laser plexi, plastique transparent sur mesure, plexiglass France, plexiglass pas cher">

  {else}
    <title>{block name='head_seo_title'}{$page.meta.title}{/block}</title>
    <meta property="og:title" content="{block name='head_seo_title'}{$page.meta.title}{/block}">
    <meta name="description" content="{block name='head_seo_description'}{$page.meta.description}{/block}">
    <meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords}{/block}">
  {/if}

  <meta name="google-site-verification" content="MzxiT26QcVJbDd3sLmcHOH6lw2DoQogDYMj3XV-caPM">
<meta name="p:domain_verify" content="556e01d9854684f12c7e03b8eea858df"/>
  {if $page.meta.robots !== 'index'}
    <meta name="robots" content="{$page.meta.robots}">
  {/if}

  {if $page.canonical}
    <link rel="canonical" href="{$page.canonical}">
  {/if}

  {block name='head_hreflang'}
    {foreach from=$urls.alternative_langs item=pageUrl key=code}
      <link rel="alternate" href="{$pageUrl}" hreflang="{$code}">
    {/foreach}
  {/block}
{/block}

{block name='head_viewport'}
  <meta name="viewport" content="width=device-width, initial-scale=1">
{/block}

{block name='head_icons'}
  <link rel="icon" type="image/vnd.microsoft.icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
  <link rel="shortcut icon" type="image/x-icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
{/block}

{block name='stylesheets'}
  {include file="_partials/stylesheets.tpl" stylesheets=$stylesheets}
{/block}

{block name='javascript_head'}
  {include file="_partials/javascript.tpl" javascript=$javascript.head vars=$js_custom_vars} 
  {literal}
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-996312213">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-996312213');
</script>
  {/literal}
{/block}
{block name='hook_header'}
  {$HOOK_HEADER nofilter}
{/block}

{block name='hook_extra'}{/block}
{if $page.page_name == 'index'}
  <link rel="stylesheet" href="/modules/idxrcustomproduct/views/css/16/front_header.css" type="text/css" media="all">
  <link rel="stylesheet" href="/modules/idxrcustomproduct/views/css/17/front.css" type="text/css" media="all">
  <link rel="stylesheet" href="/modules/idxrcustomproduct/views/css/17/front_accordion.css" type="text/css" media="all">
  <link rel="stylesheet" href="/modules/idxrcustomproduct/views/css/17/idxopc.css" type="text/css" media="all">
{/if}
