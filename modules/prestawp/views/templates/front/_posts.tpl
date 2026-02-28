{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2020 Presta.Site
* @license   LICENSE.txt
*}
{if isset($pswp_module) && is_object($pswp_module) && $pswp_module->show_search_page}
    <div class="pswp-search-wrp {if !empty($smarty.get.q)}pswp-search-visible{/if}">
        <button class="pswp-search-btn" onclick="pswp_toggleSearch(this);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                <circle cx="10" cy="10" r="7"></circle>
                <line x1="14" y1="14" x2="20" y2="20"></line>
            </svg>
            <span class="pswp-search-close">&#10005;</span>
            <div class="pswp-search-text">{l s='Search' mod='prestawp'}</div>
        </button>
        <form class="pswp-search-form" action="{$pswp_module->getModuleLink($pswp_module->name, 'list')|escape:'html':'UTF-8'}">
            <input type="text" class="form-control pswp-search-input" name="q" {if !empty($smarty.get.q)}value="{$smarty.get.q|escape:'html':'UTF-8'}"{/if}>
        </form>
    </div>
{/if}
{if is_array($pswp_posts) && $pswp_posts}
    <div class="prestawpblock block psv{$psvwd|intval} {if !$show_featured_images}pswp-block-no-img{/if}">
        {strip}
            <div class="pswp_grid posts_container {if $show_featured_images}posts_container-fi{/if} {if $pswp_masonry && $grid_columns > 1}pswp_masonry{/if} pswp-cols-{$grid_columns|intval}">
                {foreach from=$pswp_posts item=post}
                    {capture name='pswp_footer'}
                        {if $show_footer}
                            <footer class="entry-footer" {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if}>
                                <span class="posted-on">
                                    <a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark" {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if}>
                                        <time class="entry-date published updated" datetime="{$post.time_created|escape:'html':'UTF-8'}">{$post.date_created|escape:'html':'UTF-8'}</time>
                                    </a>
                                </span>
                                {if isset($post.comment_count)}
                                    &nbsp;/&nbsp;<span class="comments-link"><a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}/#comments" {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if}>{$post.comment_count|escape:'html':'UTF-8'} {if $post.comment_count == 1}{l s='comment' mod='prestawp'}{else}{l s='comments' mod='prestawp'}{/if}</a></span>
                                {/if}
                            </footer>
                        {/if}
                    {/capture}
                    {if $show_featured_images}
                        <article id="pswp-post-{$post.ID|escape:'html':'UTF-8'}" class="pswp-post" style="{if $grid_columns}width: {math equation="x/y" x=100 y=$grid_columns|escape:'html':'UTF-8'}%;{/if}">
                            <div class="pswp-post-wrp">
                                <div class="pswp-post-wrp-1" {if $pswp_theme == 'second' && $pswp_title_bg_color}style="border-bottom-color: {$pswp_title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
                                    {if $post.image}
                                        <a href="{$post.url|escape:'html':'UTF-8'}" class="pswp-post-image-link" {if $pswp_blank}target="_blank"{/if}>
                                            <img class="pswp-post-image" src="{$post.image|escape:'quotes':'UTF-8'}" alt="{$post.post_title|escape:'html':'UTF-8'}" {if !empty($post.image_width) && !empty($post.image_height)}width="{$post.image_width|intval}" height="{$post.image_height|intval}"{/if} />
                                            {if $pswp_theme == 'second' && $pswp_show_preview}
                                                <span class="pswp-post-image-desc">
                                                    {$post.main_content|escape:'html':'UTF-8'|truncate:300}
                                                </span>
                                            {/if}
                                        </a>
                                    {/if}
                                    <div class="pswp-post-fi-title {if !$post.image}pswp-no-image{/if}" {if $pswp_title_bg_color}style="background-color: {$pswp_title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
                                        <a {if $pswp_blank}target="_blank"{/if} class="pswp-post-title" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if} rel="bookmark">
                                            {$post.post_title|escape:'html':'UTF-8'}
                                        </a>
                                        {if $post.image && $pswp_show_preview}
                                            <a class="pswp-post-preview" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_blank}target="_blank"{/if} {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if}>
                                                {$post.main_content|escape:'html':'UTF-8'|truncate:300}
                                            </a>
                                        {/if}
                                        {$smarty.capture.pswp_footer nofilter} {* no filtering for Smarty capture *}
                                    </div>
                                    {if !$post.image && $pswp_show_preview_no_img}
                                        <a class="pswp-no-image-preview" href="{$post.url|escape:'html':'UTF-8'}" {if $pswp_blank}target="_blank"{/if} {if $pswp_title_bg_color}style="border-color: {$pswp_title_bg_color|escape:'quotes':'UTF-8'};" {/if}>
                                            {$post.main_content|escape:'html':'UTF-8'|truncate:300}
                                        </a>
                                    {/if}
                                </div>
                            </div>
                        </article>
                    {else}
                        <article id="pswp-post-{$post.ID|escape:'html':'UTF-8'}" class="pswp-post" style="{if $grid_columns}width: {math equation="x/y" x=100 y=$grid_columns|escape:'html':'UTF-8'}%;{/if}">
                            <div class="pswp-post-wrp">
                                <div class="pswp-post-wrp-wrp">
                                    <header class="entry-header">
                                        <h3 class="entry-title" {if $pswp_title_bg_color}style="background-color: {$pswp_title_bg_color|escape:'quotes':'UTF-8'}; padding: 4px;" {/if}>
                                            <a {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark" {if $pswp_title_color}style="color: {$pswp_title_color|escape:'quotes':'UTF-8'};" {/if}>
                                                {$post.post_title|escape:'html':'UTF-8'}
                                            </a>
                                        </h3>
                                    </header>

                                    <div class="entry-content {if $strip_tags}pswp-entry-content-nohtml{/if}">
                                        {if $strip_tags}
                                            <p>
                                                {$post.main_content|strip_tags|escape:'html':'UTF-8'}
                                            </p>
                                        {else}
                                            {$post.main_content nofilter} {*keep html*}
                                        {/if}

                                        {if $show_full}
                                            {if $strip_tags}
                                                {$post.extended_content|strip_tags|escape:'html':'UTF-8'}
                                            {else}
                                                {$post.extended_content nofilter} {*keep html*}
                                            {/if}
                                        {else}
                                            <p>
                                                <a class="pswp-continue-wrp" {if $pswp_blank}target="_blank"{/if} href="{$post.url|escape:'html':'UTF-8'}" rel="bookmark">{l s='Continue reading' mod='prestawp'}</a>
                                            </p>
                                        {/if}
                                    </div>

                                    {$smarty.capture.pswp_footer nofilter} {* no filtering for Smarty capture *}
                                </div>
                            </div>
                        </article>
                    {/if}
                {/foreach}
            </div>
        {/strip}
    </div>

    {if $pswp_total_pages > 1}
        <div class="pswp-center">
            <div class="pswp-pagination">
                {if $pswp_page == 1}
                    {*<span class="pag disabled">&laquo;</span>*}
                {else}
                    <a href="?p={$pswp_page - 1|intval}{if $q}&q={$q|escape:'html':'UTF-8'}{/if}">&laquo;</a>
                {/if}

                {for $i = 1 to $pswp_total_pages}
                    {if abs($pswp_page - $i) < 4}
                        <a href="?p={$i|intval}{if $q}&q={$q|escape:'html':'UTF-8'}{/if}" class="{if $i == $pswp_page}active{/if}">{$i|intval}</a>
                    {/if}
                {/for}

                {if $pswp_page == $pswp_total_pages}
                    {*<span class="pag disabled">&raquo;</span>*}
                {else}
                    <a href="?p={$pswp_page + 1|intval}{if $q}&q={$q|escape:'html':'UTF-8'}{/if}">&raquo;</a>
                {/if}
            </div>
        </div>
    {/if}
{else}
    <p>{l s='No posts found' mod='prestawp'}</p>
{/if}
