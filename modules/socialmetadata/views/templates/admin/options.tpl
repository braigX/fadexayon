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
<div id="container" class="row">
	<div class="sidebar navigation col-md-3">
		<nav class="list-group categorieList">

			<a class="list-group-item {if ($current_options_page == 'ogp_general' || $current_options_page =='')} active {/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_general">{l s='Open Graph Protocol' mod='socialmetadata'}</a>
			<a class="list-group-item {if ($current_options_page == 'facebook')} active {/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=facebook">{l s='Facebook' mod='socialmetadata'}</a>
			<a class="list-group-item {if ($current_options_page == 'twitter_cards')} active {/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=twitter_cards">{l s='Twitter Cards' mod='socialmetadata'}</a>

			<a class="list-group-item{if ($current_options_page == 'ogp_homepage')} active {/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_homepage">{l s='Home page settings' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_product')} active {/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_product">{l s='Product settings' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_category')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_category">{l s='Category settings' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_manufacturer')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&data=open_graph_protocol&options=ogp_manufacturer">{l s='Manufacturer settings' mod='metadatapro'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_supplier')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_supplier">{l s='Supplier settings' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_cms')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_cms">{l s='CMS settings' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'ogp_cms_category')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=ogp_cms_category">{l s='CMS category settings' mod='socialmetadata'}</a>
            <br>
            <br>
            <a class="list-group-item{if ($current_options_page == 'google_rich_snippets')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=google_rich_snippets">{l s='Google Rich Snippets' mod='socialmetadata'}</a>
            <a class="list-group-item{if ($current_options_page == 'google_rich_snippets_product')} active{/if}" href="{$uri|escape:'htmlall':'UTF-8'}&moduleController=options&options=google_rich_snippets_product">{l s='Product Rich Snippets' mod='socialmetadata'}</a>
		</nav>
	</div>
	<div class="col-md-9">
		{if $saveSuccess}
			<div class="alert alert-success">{l s='Your settings have been saved successfully!' mod='socialmetadata'}</div>
		{/if}
		{$form}
	</div>

</div>
