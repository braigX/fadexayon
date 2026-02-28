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
	{if $ogp_homepage_status}

		{if $facebook_appid}
			<meta property="fb:app_id" content="{$facebook_appid}">
		{/if}

	    <meta property="og:type" content="website">
		<meta property="og:url" content="{$shop_url}">
		<meta property="og:site_name" content="{$shop_name}">
		{if $ogp_homepage_title}
			<meta property="og:title" content="{$ogp_homepage_title}">
		{/if}
		{if $ogp_homepage_description}
			<meta property="og:description" content="{$ogp_homepage_description}">
		{/if}
		{if $ogp_homepage_image}
			<meta property="og:image" content="{$ogp_homepage_image}">
		{/if}
	{/if}
{/if}


{if $twitter_cards_status}
	{if $ogp_homepage_status}
		<meta name="twitter:card" content="summary" />
		{if $twitter_cards_username}
			<meta name="twitter:site" content="@{$twitter_cards_username}" />
		{/if}
		{if $ogp_homepage_title}
			<meta name="twitter:title" content="{$ogp_homepage_title}" />
		{/if}
		{if $ogp_homepage_description}
			<meta name="twitter:description" content="{$ogp_homepage_description}" />
		{/if}
		{if $ogp_homepage_image}
			<meta name="twitter:image" content="{$ogp_homepage_image}" />
		{/if}
	{/if}
{/if}


{if $google_rich_snippets_status}
	<script type="application/ld+json">
		{
		  "@context": "http://schema.org",
		  "@type": "Store",
		  "url": "{$shop_url}",
		  {if $google_rich_snippets_logo}
			  "logo": "{$google_rich_snippets_logo}",
			  "image": "{$google_rich_snippets_logo}",
		  {/if}
		  {if $google_rich_snippets_shop_title}
		  	"name": "{$google_rich_snippets_shop_title}",
		  {/if}
		  {if $google_rich_snippets_shop_description}
			"description" : "{$google_rich_snippets_shop_description}",
		  {/if}
		  {if $google_rich_snippets_addresses_status && $addressData}
		  	"address" : [
			  	{assign var=addressCount value=0}
			  	{assign var=addressDataLength value=$addressData|count}
			  	{foreach $addressData as $address}
			  		{capture assign=addressCount}{$addressCount+1}{/capture}
			  		{
					    "@type": "PostalAddress",
					    "streetAddress": "{$address['streetAddress']}",
					    "addressLocality": "{$address['addressLocality']}",
					    "addressRegion": "{$address['addressRegion']}",
					    "postalCode": "{$address['postalCode']}",
					    "addressCountry": "{$address['addressCountry']}"
					} {if $addressCount!=$addressDataLength},{/if}
			  	{/foreach}
		  	],
		  {/if}
		  {if $google_rich_snippets_geo_status && $geoData}
		  	"geo" : [
			  	{assign var=geoCount value=0}
			  	{assign var=geoDataLength value=$geoData|count}
			  	{foreach $geoData as $geo}
			  		{capture assign=geoCount}{$geoCount+1}{/capture}
			  		{
					    "@type": "GeoCoordinates",
					    "latitude": {$geo['latitude']},
					    "longitude": {$geo['longitude']}
					} {if $geoCount!=$geoDataLength},{/if}
			  	{/foreach}
		  	],
		  {/if}
		  {if $google_rich_snippets_phone}
		  	"telephone": "{$google_rich_snippets_phone}",
		  {/if}
		  "sameAs":[
			  	{assign var=socialCount value=0}
			  	{assign var=socialURLsLength value=$socialURLs|count}
			  	{foreach $socialURLs as $socialURL}
			  		{capture assign=socialCount}{$socialCount+1}{/capture}
			  		"{$socialURL}" {if $socialCount!=$socialURLsLength},{/if}
			  	{/foreach}
		  ],
		  "@id": "{$shop_url}"
		}
	</script>
{/if}
