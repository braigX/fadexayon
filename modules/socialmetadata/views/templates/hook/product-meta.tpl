{*
*
*
*    Social Meta Data
*    Copyright 2018  Inno-mods.io
*
*    @author    Inno-mods.io
*    @copyright Inno-mods.io
*    @version   1.2.0
*    Visit us at http://www.inno-mods.io
*
*
*}
{if $ogp_general_status}
	{if $ogp_product_status}

		{if $facebook_appid}
			<meta property="fb:app_id" content="{$facebook_appid}">
		{/if}

    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{$shop_name}">
		<meta property="og:url" content="{$ogp_product_url}">
		{if $ogp_product_title}
			<meta property="og:title" content="{$ogp_product_title}">
		{/if}
		{if $ogp_product_description}
			<meta property="og:description" content="{$ogp_product_description}">
		{/if}
		{if $ogp_product_image && $ogp_product_has_cover}
			<meta property="og:image" content="{$ogp_product_image}">
		{else}
			{if $ogp_product_default_image}
				<meta property="og:image" content="{$ogp_product_default_image}">
			{/if}
		{/if}
		<meta property="product:pretax_price:amount" content="{$price_tax_excl}">
		<meta property="product:price:amount" content="{$price}">
		<meta property="product:price:currency" content="{$currency}">
		<meta property="product:weight:value" content="{$weight}">
		<meta property="product:weight:units" content="kg">
	{/if}
{/if}


{if $twitter_cards_status}
	{if $ogp_product_status}
		<meta name="twitter:card" content="summary" />
		{if $twitter_cards_username}
			<meta name="twitter:site" content="@{$twitter_cards_username}" />
		{/if}
		{if $ogp_product_title}
			<meta name="twitter:title" content="{$ogp_product_title}" />
		{/if}
		{if $ogp_product_description}
			<meta name="twitter:description" content="{$ogp_product_description}" />
		{/if}
		{if $ogp_product_image && $ogp_product_has_cover}
			<meta name="twitter:image" content="{$ogp_product_image}">
		{else}
			{if $ogp_product_default_image}
				<meta name="twitter:image" content="{$ogp_product_default_image}">
			{/if}
		{/if}
	{/if}
{/if}




{if $google_rich_snippets_status}
	{if $google_rich_snippets_product_status}
		<script type="application/ld+json">
		{
		  "@context": "http://schema.org/",
		  "@type": "Product",
		  {if $google_rich_snippets_product_title}
		  	  "name": "{$google_rich_snippets_product_title}",
		  {/if}
		  {if $google_rich_snippets_product_image && $ogp_product_has_cover}
			  "image": [
			    "{$google_rich_snippets_product_image}"
			   ],
		  {else}
			{if $google_rich_snippets_product_default_image}
			  "image": [
			    "{$google_rich_snippets_product_default_image}">
			   ],
			{/if}
		  {/if}
		  {if $google_rich_snippets_product_description}
		  	  "description": "{$google_rich_snippets_product_description}",
		  {/if}
		  {if $google_rich_snippets_product_reference_status}
		  	  "sku": "{$sku}",
		  {/if}
		  {if $google_rich_snippets_product_manufacturer_status && $manufacturer_name}
			  "brand": {
			    "@type": "Thing",
			    "name": "{$manufacturer_name}"
			  },
		  {/if}
		  {if $google_rich_snippets_product_price_status}
		  "offers": {
		    "@type": "Offer",
		    "priceCurrency": "{$currency}",
		    "price": "{$price}"
		  }
		  {/if}
		}
		</script>
	{/if}
{/if}
