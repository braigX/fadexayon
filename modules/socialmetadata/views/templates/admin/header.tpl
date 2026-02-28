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

<div id="socialmetadata" class="bootstrap">
	<div class="socialmetadata-nav">

		<div class="pull-left mod-title">
			<img class="logo" src="{$logoSrc|escape:'htmlall':'UTF-8'}">
			Social Meta Data
		</div>

		<div class="pull-right socialmetadata-menu">

		</div>

	</div>
	<br>
	{if $displayCurrentStore}
		<div class="socialmetadata-current-store-title">
			{l s='Current Store' mod='socialmetadata'}: {$storeName|escape:'htmlall':'UTF-8'}
		</div>
	{/if}
	<div class="socialmetadata-content">
