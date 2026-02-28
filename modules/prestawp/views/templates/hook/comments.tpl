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
<div id="prestawpblock-comments" class="prestawpblock-comments block psv{$pswp_psvwd|intval}">
	<p class="title_block {if $pswp_psv >= 1.7}text-uppercase h6{/if}">{l s='Latest comments from our blog:' mod='prestawp'}</p>
	<ul class="cats_container comments_container">
		{foreach from=$pswp_comments item=comment}
			<li>
				{if isset($comment.rss) && $comment.rss}
					<a {if $pswp_blank}target="_blank"{/if} href="{$comment.url|escape:'html':'UTF-8'}">
						{$comment.title|escape:'html':'UTF-8'|truncate:200}
                        {if ($pswp_show_date)}
							/ <time class="entry-date published updated" datetime="{$comment.time_created|escape:'html':'UTF-8'}">{$comment.date_created|escape:'html':'UTF-8'}</time>
                        {/if}
                        {if $pswp_show_content && isset($comment.comment_content)}
							<span class="comment-content">
                                {$comment.comment_content|strip_tags:false|escape:'html':'UTF-8'|truncate:200}
							</span>
                        {/if}
					</a>
				{else}
					<a {if $pswp_blank}target="_blank"{/if} href="{$comment.url|escape:'html':'UTF-8'}">
						{$comment.comment_author|strip_tags:false|escape:'html':'UTF-8'|truncate:200} <span class="comment-on">{l s='on' mod='prestawp'}</span> {$comment.post_title|escape:'html':'UTF-8'|truncate:200}
                        {if ($pswp_show_date)}
							/ <time class="entry-date published updated" datetime="{$comment.time_created|escape:'html':'UTF-8'}">{$comment.comment_date|escape:'html':'UTF-8'}</time>
                        {/if}
                        {if $pswp_show_content && isset($comment.comment_content)}
							<span class="comment-content">
                                {$comment.comment_content|strip_tags:false|escape:'html':'UTF-8'|truncate:200}
							</span>
                        {/if}
					</a>
				{/if}
			</li>
		{/foreach}
	</ul>
</div>