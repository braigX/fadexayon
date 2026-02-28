{if isset($tags) AND !empty($tags)}
<div id="tags_blog_block_left"  class="block sidebar-block tags_block hidden-sm-down">
    <div class="block_content sidebar-content">
      <h4 class="text-uppercase h6 hidden-sm-down sidebar-inner-title"><a href="{smartblog::GetSmartBlogLink('smartblog')}">{l s='Tags Post' mod='smartblogtag'}</a></h4>
        {foreach from=$tags item="tag"}
        {assign var="options" value=null}
            {$options.tag = $tag.name|urlencode}
            {if $tag!=""}
                <a href="{smartblog::GetSmartBlogLink('smartblog_tag',$options)|escape:'html':'UTF-8'}">{$tag.name}</a>
            {/if}
        {/foreach}
   </div>
</div>
{/if}
