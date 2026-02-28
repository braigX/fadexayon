{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2021 Presta.Site
* @license   LICENSE.txt
*}
<li id="comment-{$comment.comment_ID|intval}" class="comment depth">
  <article class="comment-body">
    <footer class="comment-meta">
      <div class="comment-author vcard">
        <b class="fn">{$comment.comment_author|escape:'html':'UTF-8'}</b>
      </div>
      <div class="comment-metadata">
        <time datetime="{date('c', strtotime($comment.comment_date))|escape:'html':'UTF-8'}">{dateFormat date=$comment.comment_date full=1|escape:'html':'UTF-8'}</time>
      </div>
    </footer>
    <div class="comment-content">{$comment.comment_content|strip_tags|escape:'html':'UTF-8'}</div>
  </article>
  {if $pswp->ps_allow_commenting && $pswp_post.comment_status == 'open'}
    <div class="comment-reply">
      <a rel="nofollow" class="comment-reply-link pswp-write-comment-btn"
         href="{$pswp_post.url|escape:'html':'UTF-8'}"
         data-comment-id="{$comment.comment_ID|intval}" data-reply-to="{l s='Reply to %s' mod='prestawp' sprintf=[$comment.comment_author|escape:'html':'UTF-8']}">
        {l s='Reply' mod='prestawp'}
      </a>
      <div class="pswp-comment-form-wrp pswp-comment-reply-wrp"></div>
    </div>
  {/if}

  {if !empty($comment.children)}
    <ol class="sub-comment-list">
      {foreach from=$comment.children item='comment_child'}
        {if $pswp_psv >= 1.7}
          {include 'module:prestawp/views/templates/front/_comment.tpl' comment=$comment_child pswp_post=$pswp_post}
        {else}
          {include './_comment.tpl' comment=$comment_child pswp_post=$pswp_post}
        {/if}
      {/foreach}
    </ol>
  {/if}
</li>