{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div   class="sdsarticleCat aaaa clearfix сol-xs-12 col-lg-6">
    <div id="smartblogpost-{$post.id_post|escape:'htmlall':'UTF-8'}" class="card card-block">
        <div class="sdsarticleHeader">
           {* <h4 class='h1 products-section-title'><a class="" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}">{$post.meta_title|escape:'htmlall':'UTF-8'}</a></h4>*}

            <span class="meta">{if
$smartshowauthor ==1}{l s='Posted by' mod='smartblog'}
                <span > {if
$smartshowauthorstyle != 0}{$post.firstname|escape:'htmlall':'UTF-8'}
                    {$post.lastname|escape:'htmlall':'UTF-8'}{else}{$post.lastname|escape:'htmlall':'UTF-8'} {$post.firstname|escape:'htmlall':'UTF-8'}{/if}
                           </span> {/if}
                                {$assocCats = BlogCategory::getPostCategoriesFull($post.id_post)}
                                {$catCounts = 0}
                                {if !empty($assocCats)}
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <span >
                                    {foreach $assocCats as $catid=>$assoCat}
                                    {if $catCounts > 0}, {/if}
                                    {$catlink=[]}
                                    {$catlink.id_category = $assoCat.id_category}
                                    {$catlink.slug = $assoCat.link_rewrite}
                                    <a href="{$smartbloglink->getSmartBlogCategoryLink($assoCat.id_category,$assoCat.link_rewrite)|escape:'htmlall':'UTF-8'}">
                                        {$assoCat.name|escape:'htmlall':'UTF-8'}
                                    </a>
                                    {$catCounts = $catCounts + 1}
                                {/foreach}
                            </span>
                        {/if}
                        <span class="comment">
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                            <a href=
                               "{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}#articleComments"
                               title="{$post.totalcomment|escape:'htmlall':'UTF-8'} Comments">{$post.totalcomment} {l s=' Comments'
        mod='smartblog'}</a></span>{if $smartshowviewed ==1}&nbsp;&nbsp;|&nbsp;&nbsp;{l s='Post views' mod='smartblog'}
                        ({$post.viewed|intval}){/if}</span>
						
					<!-- Modified by Team Wassim Novatis -->
					{if isset($post.date_add)}
                    {$post.date_add}
                     {/if}
					<h4 class='h3 products-section-title'><a class="" title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}">{$post.meta_title|escape:'htmlall':'UTF-8'}</a></h4>
					<div class="sdsarticle-des" style="text-align: left;">
                    {$post.short_description}
                   </div>
				   <div class="sdsreadMore">
                    <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="r_more"><span>{l s='Read more' mod='smartblog'}<i class="fas fa-chevron-right"></i></span></a>
                   </div>
				   	<!-- End -->
				    
                </div>



                <div class="articleContent">
                    {if isset($ispost) && !empty($ispost)}
                    <a 
                    href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.cat_link_rewrite)|escape:'htmlall':'UTF-8'}"
                    title="{$post.meta_title|escape:'htmlall':'UTF-8'}" class="imageFeaturedLink">
                    {/if}
                    {assign var="img_link" value=$smartbloglink->getImageLink($post.link_rewrite, $post.id_post, 'single-default')}
                    {if $img_link != 'false'}
                        <img alt="{$post.meta_title|escape:'htmlall':'UTF-8'}"
                            src="{$img_link}"
                            class="imageFeatured">
                    {/if}

                    {if isset($ispost) && !empty($ispost)}
                    </a>
                    {/if}
                   {*<div class="sdsarticle-des" style="text-align: left;">
                    {$post.short_description}
                   </div>*}
                </div>

                {*<div class="sdsreadMore">
                    <a title="{$post.meta_title|escape:'htmlall':'UTF-8'}" href="{$smartbloglink->getSmartBlogPostLink($post.id_post,$post.link_rewrite)|escape:'htmlall':'UTF-8'}" class="r_more"><span>{l s='Read more' mod='smartblog'}</span></a>
                </div>*}



            </div>
        </div>
