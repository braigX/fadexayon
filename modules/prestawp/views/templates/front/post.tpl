{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

<div id="pswp-page" class="rte pswp{$psvd|escape:'html':'UTF-8'}">
    {capture name=path}{if $pswp->enable_posts_page}<a href="{$pswp->getModuleLink('prestawp', 'list')|escape:'quotes':'UTF-8'}">{l s='Posts' mod='prestawp'}</a>&nbsp;{$navigationPipe|escape:'html':'UTF-8'}&nbsp;{/if}{$pswp_post.title|escape:'html':'UTF-8'}{/capture}
    <h1 class="page-heading">{$pswp_post.title|escape:'html':'UTF-8'}</h1>

    {if !$pswp_post.post_password}
        <div class="pswp-post-content">
            {if $pswp_post.image}
                <div class="pswp-post-img">
                    <img src="{$pswp_post.image|escape:'html':'UTF-8'}" alt="{$pswp_post.title|escape:'html':'UTF-8'}">
                </div>
            {/if}
            {$pswp_post.post_content nofilter} {* HTML *}
        </div>
    {else}
        <p>
            {l s='This content is password-protected. Please enter your password to view it:' mod='prestawp'}
        </p>
        <form action="" method="post">
            <div class="form-group row">
                <label for="pswp-pass" class="col-md-3 form-control-label">{l s='Password:' mod='prestawp'}</label>
                <div class="col-md-6">
                    <input type="password" name="password" id="pswp-pass" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <input type="submit" class="btn btn-primary">
                </div>
            </div>
        </form>
    {/if}

    {if $pswp->ps_show_comments && (!empty($pswp_post.comments) || $pswp->ps_allow_commenting && $pswp_post.comment_status == 'open')}
        <div id="pswp-comments" class="comments-area">
            <a id="comments"></a>
            <hr>
            <div class="comments-title-wrap">
                <h3 class="comments-title">{l s='Comments' mod='prestawp'} ({$pswp_post.comment_count|intval})</h3>
            </div>
            <div id="pswp-comment-list">
                {if $pswp_psv >= 1.7}
                    {include 'module:prestawp/views/templates/front/_comment_list.tpl' pswp_post=$pswp_post}
                {else}
                    {include './_comment_list.tpl' pswp_post=$pswp_post}
                {/if}
            </div>
            {if $pswp->ps_allow_commenting && $pswp_post.comment_status == 'open'}
                <hr>
                <a id="comment-form"></a>
                <div class="comment-reply comment-reply-main">
                    <a href="{$pswp_post.url|escape:'html':'UTF-8'}" class="btn btn-primary pswp-write-comment-btn">{l s='Write a comment' mod='prestawp'}</a>
                    <div class="pswp-comment-form-wrp"></div>
                </div>
            {/if}
        </div>
    {/if}

    {if $pswp_prev_next_posts && (!empty($pswp_prev_next_posts.prev) || !empty($pswp_prev_next_posts.next))}
        <nav>
            <hr class="pswp-styled-separator">

            <div class="pswp-pagination-single-inner {if empty($pswp_prev_next_posts.prev)}pswp-pagination-only-next{elseif empty($pswp_prev_next_posts.next)}pswp-pagination-only-prev{/if}">
                {if !empty($pswp_prev_next_posts.prev)}
                    <a class="pswp-previous-post" href="{$pswp_prev_next_posts.prev.url|escape:'html':'UTF-8'}">
                        <span class="pswp-arrow">←</span>
                        <span class="pswp-title"><span class="pswp-title-inner">{$pswp_prev_next_posts.prev.post_title|escape:'html':'UTF-8'}</span></span>
                    </a>
                {/if}
                {if !empty($pswp_prev_next_posts.next)}
                    <a class="pswp-next-post" href="{$pswp_prev_next_posts.next.url|escape:'html':'UTF-8'}">
                        <span class="pswp-title"><span class="pswp-title-inner">{$pswp_prev_next_posts.next.post_title|escape:'html':'UTF-8'}</span></span>
                        <span class="pswp-arrow">→</span>
                    </a>
                {/if}
            </div>

            <hr class="pswp-styled-separator">
        </nav>
    {/if}

    {if $pswp->enable_posts_page}
        <p class="pswp-return"><a class="pswp-return-link" href="{$pswp->getModuleLink('prestawp', 'list')|escape:'quotes':'UTF-8'}">{l s='Back to the list' mod='prestawp'}</a></p>
    {/if}
</div>