{if isset($latesComments) AND !empty($latesComments)}
<div class="block sidebar-block blogModule boxPlain">
  <div class="block_content sdsbox-content sidebar-content">
    <h4 class="text-uppercase h6 hidden-sm-down sidebar-inner-title"><span>{l s='Latest Comments' mod='smartbloglatestcomments'}</span></h4>
    <ul class="recentComments">
      {foreach from=$latesComments item="comment"}
        <li>
          <a title="" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">
            {$comment.name}
          </a>
          <p>
            <a class="title" href="{$smartbloglink->getSmartBlogPostLink($comment.id_post,$comment.link_rewrite)}">{SmartBlogPost::subStr($comment.content,150) nofilter}</a>
          </p>
        </li>
      {/foreach}
    </ul>
  </div>
  <div class="box-footer"><span></span></div>
</div>
{/if}
