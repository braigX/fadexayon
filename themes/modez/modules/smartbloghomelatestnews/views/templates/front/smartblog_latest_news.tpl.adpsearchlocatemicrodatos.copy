<div class="roy_blog" data-in-row="{if isset($roythemes.bl_row)}{$roythemes.bl_row}{/if}">
    <h2 class='products-section-title text-uppercase'><a href="{smartblog::GetSmartBlogLink('module-smartblog-list')}">{l s='Latest News' d='Shop.Theme.Catalog'}</a></h2>
    <div class="sdsblog-box-content owl-carousel">
        {if isset($view_data) AND !empty($view_data)}
            {foreach from=$view_data item=post}
                {assign var='img_url' value=$smartbloglink->getImageLink($post.link_rewrite, $post.id, 'home-default')}
                <div class="sds_blog_post">
                    <span class="news_module_image_holder news_home_image_holder tip_inside">
                        {if $img_url != 'false'}
                        <a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">
                        <img class="replace-2x img-responsive" src="{$img_url}" alt="{$post.title|escape:'html':'UTF-8'}" title=""  />
                        </a>
                        {/if}
                        <span class="tip">
                          {l s='Read more:' mod='smartbloghomelatestnews'}<br />
                          {SmartBlogPost::subStr($post.title,60)}
                        </span>
                    </span>
                    <div class="news_content">
                      <h4 class="sds_post_title sds_post_title_home"><a href="{$smartbloglink->getSmartBlogPostLink($post.id,$post.link_rewrite)}">{SmartBlogPost::subStr($post.title,60)}</a></h4>
                      <div class="news_date"><span class="news_day">{$post.date_added|date_format:"%d"}</span><span class="news_month">{$post.date_added|date_format:"%b"}</span><span class="news_year">{$post.date_added|date_format:"%Y"} | <span class="info_value">{$post.viewed} </span>{l s='Post views' mod='smartbloghomelatestnews'}</span></div>
                    </div>
                </div>
            {/foreach}
        {/if}
     </div>
</div>
