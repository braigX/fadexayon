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
<div id="prestawpblock-shortposts" class="block psv{$psvwd|intval}">
	<p class="title_block {if $psv >= 1.7}text-uppercase h6{/if}">{l s='Latest posts' mod='prestawp'}</p>
	<ul class="cats_container">
		{foreach from=$posts item=post}
			<li>
				<a {$blank|escape:'quotes':'UTF-8'} href="{$post.url|escape:'html':'UTF-8'}">{$post.post_title|escape:'html':'UTF-8'}</a>
				{if ($show_date)}
				<time class="pswp-entry-date entry-date published updated" datetime="{$post.time_created|escape:'html':'UTF-8'}">{$post.date_created|escape:'html':'UTF-8'}</time>
                {/if}
			</li>
		{/foreach}
	</ul>
</div>