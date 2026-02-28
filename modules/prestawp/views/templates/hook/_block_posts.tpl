{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2019 Presta.Site
* @license   LICENSE.txt
*}
{foreach from=$posts item=post}
	{if $pswp_block->show_featured_image}
		<article id="pswp-post-{$post.ID|escape:'html':'UTF-8'}" class="pswp-post" style="{if $pswp_block->grid_columns}width: {math equation="x/y" x=100 y=$pswp_block->grid_columns|escape:'html':'UTF-8'}%;{/if}">
			<div class="pswp-post-wrp">
				<div class="pswp-post-wrp-1" {if $pswp_theme == 'second' && $pswp_block->title_bg_color}style="border-bottom-color: {$pswp_block->title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
					{if $post.image}
						<a href="{$post.url|escape:'html':'UTF-8'}" class="pswp-post-image-link" {if $pswp_blank}target="_blank"{/if}>
							<img class="pswp-post-image" src="{$post.image|escape:'quotes':'UTF-8'}" alt="{$post.post_title|strip_tags:false|escape:'html':'UTF-8'}" {if !empty($post.image_width) && !empty($post.image_height)}width="{$post.image_width|intval}" height="{$post.image_height|intval}"{/if} />
							{if $pswp_theme == 'second' && $pswp_block->show_preview}
								<span class="pswp-post-image-desc">
									{if $pswp_block->truncate > 0}
										{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|truncate:intval($pswp_block->truncate)}
									{else}
										{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|truncate:300}
									{/if}
								</span>
							{/if}
						</a>
					{/if}
					<div class="pswp-post-fi-title {if !$post.image}pswp-no-image{/if}" {if $pswp_block->title_bg_color}style="background-color: {$pswp_block->title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
						<a {if $pswp_blank}target="_blank"{/if} class="pswp-post-title" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}
								rel="bookmark">
							{$post.post_title|strip_tags:false|escape:'html':'UTF-8'}
						</a>
						{if $post.image && $pswp_block->show_preview}
							<a class="pswp-post-preview" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_blank}target="_blank"{/if} {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}>
								{if $pswp_block->truncate > 0}
									{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|trim|regex_replace:"/ +/":" "|truncate:intval($pswp_block->truncate)}
								{else}
									{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|trim|regex_replace:"/ +/":" "|truncate:300}
								{/if}
							</a>
						{/if}
						{if $pswp_block->show_article_footer}
							<footer class="entry-footer" {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}>
								<span class="posted-on">
									<a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark" {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}>
										<time class="entry-date published updated" datetime="{$post.time_created|escape:'html':'UTF-8'}">{$post.date_created|escape:'html':'UTF-8'}</time>
									</a>
								</span>
								{if isset($post.comment_count)}
									&nbsp;/&nbsp;<span class="comments-link"><a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}/#comments" {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}>{$post.comment_count|escape:'html':'UTF-8'} {if $post.comment_count == 1}{l s='comment' mod='prestawp'}{else}{l s='comments' mod='prestawp'}{/if}</a></span>
								{/if}
							</footer>
						{/if}
					</div>
					{if !$post.image && $pswp_block->show_preview_no_img}
						<a class="pswp-no-image-preview" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_blank}target="_blank"{/if} {if $pswp_block->title_bg_color}style="border-color: {$pswp_block->title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
							{if $pswp_block->truncate > 0}
								{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|truncate:intval($pswp_block->truncate)}
							{else}
								{$post.main_content|strip_tags:false|escape:'html':'UTF-8'|truncate:300}
							{/if}
						</a>
					{/if}
				</div>
			</div>
		</article>
	{else}
		<article id="pswp-post-{$post.ID|escape:'html':'UTF-8'}" class="pswp-post" style="{if $pswp_block->grid_columns}width: {math equation="x/y" x=100 y=$pswp_block->grid_columns|escape:'html':'UTF-8'}%;{/if}">
			<div class="pswp-post-wrp">
				<div class="pswp-post-wrp-wrp">
					<header class="entry-header">
						<h3 class="entry-title" {if $pswp_block->title_bg_color}style="background-color: {$pswp_block->title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
							<a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark" {if $pswp_block->title_color}style="color: {$pswp_block->title_color|escape:'quotes':'UTF-8'};" {/if}>
								{$post.post_title|strip_tags:false|escape:'html':'UTF-8'}
							</a>
						</h3>
					</header>

					<div class="entry-content">
						{if $pswp_block->strip_tags}
							<p>
								{if $pswp_block->truncate > 0}
									{$post.main_content|strip_tags|escape:'html':'UTF-8'|truncate:intval($pswp_block->truncate)}
								{else}
									{$post.main_content|strip_tags|escape:'html':'UTF-8'}
								{/if}
							</p>
						{else}
							{$post.main_content nofilter} {*keep html*}
						{/if}

						{if $pswp_block->show_full_posts}
							{if $pswp_block->strip_tags}
								{if $pswp_block->truncate > 0}
									{$post.extended_content|strip_tags|escape:'html':'UTF-8'|truncate:intval($pswp_block->truncate)}
								{else}
									{$post.extended_content|strip_tags|escape:'html':'UTF-8'}
								{/if}
							{else}
								{$post.extended_content nofilter} {*keep html*}
							{/if}
						{else}
							<p class="pswp-continue">
								<a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark">{l s='Continue reading' mod='prestawp'}</a>
							</p>
						{/if}
					</div>

					{if $pswp_block->show_article_footer}
						<footer class="entry-footer">
							<span class="posted-on">
								<a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark">
									<time class="entry-date published updated" datetime="{$post.time_created|escape:'html':'UTF-8'}">{$post.date_created|escape:'html':'UTF-8'}</time>
								</a>
							</span>
							{if isset($post.comment_count)}
							&nbsp;/&nbsp;<span class="comments-link"><a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}/#comments">{$post.comment_count|escape:'html':'UTF-8'} {if $post.comment_count == 1}{l s='comment' mod='prestawp'}{else}{l s='comments' mod='prestawp'}{/if}</a></span>
							{/if}
						</footer>
					{/if}
				</div>
			</div>
		</article>
	{/if}
{/foreach}