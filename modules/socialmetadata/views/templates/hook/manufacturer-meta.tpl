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
	{if $ogp_manufacturer_status}

		{if $facebook_appid}
			<meta property="fb:app_id" content="{$facebook_appid}">
		{/if}

	  <meta property="og:type" content="website">
		<meta property="og:site_name" content="{$shop_name}">
		<meta property="og:url" content="{$ogp_manufacturer_url}">
		{if $ogp_manufacturer_title}
			<meta property="og:title" content="{$ogp_manufacturer_title}">
		{/if}
		{if $ogp_manufacturer_description}
			<meta property="og:description" content="{$ogp_manufacturer_description}">
		{/if}
		{if $ogp_manufacturer_image && $ogp_manufacturer_has_cover}
			<meta property="og:image" content="{$ogp_manufacturer_image}">
		{else}
			{if $ogp_manufacturer_default_image}
				<meta property="og:image" content="{$ogp_manufacturer_default_image}">
			{/if}
		{/if}
	{/if}
{/if}


{if $twitter_cards_status}
	{if $ogp_manufacturer_status}
		<meta name="twitter:card" content="summary" />
		{if $twitter_cards_username}
			<meta name="twitter:site" content="@{$twitter_cards_username}" />
		{/if}
		{if $ogp_manufacturer_title}
			<meta name="twitter:title" content="{$ogp_manufacturer_title}" />
		{/if}
		{if $ogp_manufacturer_description}
			<meta name="twitter:description" content="{$ogp_manufacturer_description}" />
		{/if}

		{if $ogp_manufacturer_image && $ogp_manufacturer_has_cover}
			<meta name="twitter:image" content="{$ogp_manufacturer_image}">
		{else}
			{if $ogp_manufacturer_default_image}
				<meta name="twitter:image" content="{$ogp_manufacturer_default_image}">
			{/if}
		{/if}
	{/if}
{/if}
