{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2017 Presta.Site
* @license   LICENSE.txt
*}
<div id="prestawpblock-categories" class="prestawpblock-categories block psv{$pswp_psvwd|intval}">
	<p class="title_block {if $pswp_psv >= 1.7}text-uppercase h6{/if}">{l s='Blog categories' mod='prestawp'}</p>
	<ul class="cats_container">
		{foreach from=$pswp_categories item=cat}
			<li><a {if $pswp_blank}target="_blank"{/if} href="{$cat.url|escape:'html':'UTF-8'}">{$cat.name|escape:'html':'UTF-8'}</a></li>
		{/foreach}
	</ul>
</div>