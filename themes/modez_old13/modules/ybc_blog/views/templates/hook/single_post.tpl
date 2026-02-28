{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
{if $blog_config.YBC_BLOG_CAPTCHA_TYPE=='google3'}
    <script src="https://www.google.com/recaptcha/api.js?render={$blog_config.YBC_BLOG_CAPTCHA_SITE_KEY3|escape:'html':'UTF-8'}"></script>
{/if}
{if $blog_post &&  $blog_post.enabled==-2 && isset($smarty.get.preview)}
    <div class="alert alert-warning row" role="alert">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <i class="material-icons float-xs-left icon-preview">&#xE001;</i>
                    <p class="alert-text">{l s='This post is not visible to your customers.' mod='ybc_blog'}</p>
                </div>
            </div>
        </div>
    </div>
{/if}
{if $blog_post && isset($blog_post.enabled) && ($blog_post.enabled ==1 || $blog_post.pending==1 || ($blog_post.enabled==-2 && isset($smarty.get.preview))) }
    <script type="text/javascript">
        ybc_blog_report_url = '{$report_url nofilter}';
        ybc_blog_report_warning ="{l s='Do you want to report this comment?' mod='ybc_blog'}";
        ybc_blog_error = "{l s='There was a problem while submitting your report. Try again later' mod='ybc_blog'}";
        ybc_blog_delete_comment ="{l s='Do you want to delete this comment?' mod='ybc_blog'}";
        prettySkin = '{$prettySkin|addslashes|escape:'html':'UTF-8'}';
        var placeholder_reply= "{l s='Enter your message...' mod='ybc_blog'}";
        prettyAutoPlay = false;
        var number_product_related_per_row ={$blog_config.YBC_BLOG_RELATED_PRODUCT_ROW|intval};
        var number_post_related_per_row ={$blog_config.YBC_BLOG_RELATED_POST_ROW|intval};
        var YBC_BLOG_LABEL_TABLE_OF_CONTENT ='{if isset($blog_config.YBC_BLOG_LABEL_TABLE_OF_CONTENT) && $blog_config.YBC_BLOG_LABEL_TABLE_OF_CONTENT}{$blog_config.YBC_BLOG_LABEL_TABLE_OF_CONTENT|escape:'html':'UTF-8'}{else}{l s='Table of contents' mod='ybc_blog' js=1}{/if}'
    </script>
    {if $blog_post.enabled==-1}
        <div class="alert alert-warning">
            {l s='Your post is in preview process, it will be published once our moderator approve it' mod='ybc_blog'}
        </div>
    {/if}
    <div class="ybc_blog_layout_{$blog_layout|escape:'html':'UTF-8'} ybc-blog-wrapper-detail"  itemType="http://schema.org/newsarticle">
        <div   >
            <meta  content="{Configuration::get('PS_SHOP_NAME')|escape:'html':'UTF-8'}" />
            {if Configuration::get('PS_LOGO')}
                <div   >
                    <meta  content="{$blog_config.YBC_BLOG_SHOP_URI|escape:'html':'UTF-8'}img/{Configuration::get('PS_LOGO')|escape:'html':'UTF-8'}" />
                    <meta  content="200px" />
                    <meta  content="100px" />
                </div>
            {/if}
        </div>
        {if $blog_post.image}
            <div class="ybc_blog_img_wrapper"   >
                {if $enable_slideshow}<a href="{$blog_post.image|escape:'html':'UTF-8'}" class="prettyPhoto">{/if}
                    <div class="ybc_image-single{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}">
                        <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT'|escape:'html':'UTF-8')}" title="{$blog_post.title|escape:'html':'UTF-8'}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD) && $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$blog_post.image|escape:'html':'UTF-8'}{/if}" alt="{$blog_post.title|escape:'html':'UTF-8'}"  {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$blog_post.image|escape:'html':'UTF-8'}" class="lazyload"{/if}/>
                    </div>
                    <meta  content="600px" />
                    <meta  content="300px" />
                    {if $enable_slideshow}</a>{/if}
            </div>
        {/if}
        <div class="ybc-blog-wrapper-content{if isset($blog_config.YBC_BLOG_SIDEBAR_POSITION) && $blog_config.YBC_BLOG_SIDEBAR_POSITION=='left'} content-right{else} content-left{/if}" >
            {if $blog_post}
                <h1 class="page-heading product-listing" ><span  class="title_cat" >{$blog_post.title|escape:'html':'UTF-8'}</span></h1>
                <div class="post-details">
                    <div class="blog-extra">
                        <div class="ybc-blog-latest-toolbar">
                            {if $show_views}
                                <span title="{l s='Page views' mod='ybc_blog'}" class="ybc-blog-latest-toolbar-views">
                                    {$blog_post.click_number|intval}
                                    {if $blog_post.click_number != 1}
                                        <span><i class="ets_svg">
                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                            </i> {l s='Views' mod='ybc_blog'}</span>
                                    {else}
                                        <span><i class="ets_svg">
                                                <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                            </i> {l s='View' mod='ybc_blog'}</span>
                                    {/if}
                                </span>
                            {/if}
                            {if $allow_like}
                                <span title="{if $likedPost}{l s='Unlike this post' mod='ybc_blog'}{else}{l s='Like this post' mod='ybc_blog'}{/if}" class="ybc-blog-like-span ybc-blog-like-span-{$blog_post.id_post|intval}{if $likedPost} active{/if}"  data-id-post="{$blog_post.id_post|intval}">
                                    <i class="ets_svg">
                                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"></path></svg>
                                        </i> <span class="ben_{$blog_post.id_post|intval}">{$blog_post.likes|intval}</span>
                                    <span class="blog-post-like-text blog-post-like-text-{$blog_post.id_post|intval}"><span>{l s='Liked' mod='ybc_blog'}</span></span>
                                </span>
                            {/if}
                            {if $allowComments}
                                <div class="blog_rating_wrapper">
                                    {if $total_review}
                                        <span title="{l s='Comments' mod='ybc_blog'}" class="blog_rating_reviews">
                                             <span class="total_views">
                                                 <i class="ets_svg">
                                                     <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"></path></svg>
                                                 </i> {$total_review|intval}
                                             </span>
                                             <span>
                                                {if $total_review != 1}
                                                    {l s='Comments' mod='ybc_blog'}
                                                {else}
                                                    {l s='Comment' mod='ybc_blog'}
                                                {/if}
                                            </span>
                                        </span>
                                    {/if}
                                    {if $allow_rating && $everage_rating}
                                        <div title="{l s='Average rating' mod='ybc_blog'}" class="ybc_blog_review"  data-rate="{$everage_rating|escape:'html':'UTF-8'}">
                                            {if $everage_rating == 1 || $everage_rating == '1.5'}★☆☆☆☆
                                            {elseif $everage_rating == 2 || $everage_rating == '2.5'}★★☆☆☆
                                            {elseif $everage_rating == 3 || $everage_rating == '3.5'}★★★☆☆
                                            {elseif $everage_rating == 4 || $everage_rating == '4.5'}★★★★☆
                                            {elseif $everage_rating == 5}★★★★★{/if}
                                            <span class="ybc-blog-rating-value">({number_format((float)$everage_rating, 1, '.', '')|escape:'html':'UTF-8'})</span>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                            {if $show_date}
                                <span class="post-date">
                                    <span class="be-label"><i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>
</i> {l s='Posted on' mod='ybc_blog'}: </span>
                                    <span>{dateFormat date=$blog_post.datetime_added full=0}</span>
                                    <meta  content="{dateFormat date=$blog_post.datetime_added full=0}" />
                                    <meta  content="{dateFormat date=$blog_post.datetime_modified full=0}" />
                                </span>
                            {/if}
                            {if $show_author && isset($blog_post.employee) &&  $blog_post.employee}
                                <div class="author-block"   >
                                    <span class="post-author-label">{l s='By ' mod='ybc_blog'}</span>
                                    <a  href="{$blog_post.author_link|escape:'html':'UTF-8'}">
                                        <span class="post-author-name" >
                                            {if isset($blog_post.employee.name) && $blog_post.employee.name}
                                                {ucfirst($blog_post.employee.name)|escape:'html':'UTF-8'}
                                            {else}
                                                {ucfirst($blog_post.employee.firstname)|escape:'html':'UTF-8'} {ucfirst($blog_post.employee.lastname)|escape:'html':'UTF-8'}
                                            {/if}
                                        </span>
                                    </a>
                                </div>
                            {/if}
                            {if isset($blog_post.link_edit) && $blog_post.link_edit}
                                <a class="ybc-block-post-edit" href="{$blog_post.link_edit|escape:'html':'UTF-8'}" title="{l s='Edit' mod='ybc_blog'}">
                                    <i class="ets_svg">
                                        <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>
                                    </i>&nbsp;{l s='Edit' mod='ybc_blog'}</a>
                            {/if}

                        </div>
                        <div class="ybc-blog-tags-social">
                            {if $use_google_share || $use_facebook_share || $use_twitter_share || $blog_config.YBC_BLOG_ENABLE_PINTEREST_SHARE || $blog_config.YBC_BLOG_ENABLE_LIKED_SHARE}
                                <div class="blog-extra-item blog-extra-facebook-share">
                                    <ul>
                                        {if $use_facebook_share}
                                            <li class="facebook icon-gray">
                                                <a target="_blank" title="{l s='Facebook' mod='ybc_blog'}" class="text-hide" href="http://www.facebook.com/sharer.php?u={$post_url|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1343 12v264h-157q-86 0-116 36t-30 108v189h293l-39 296h-254v759h-306v-759h-255v-296h255v-218q0-186 104-288.5t277-102.5q147 0 228 12z"/></svg>
                                                    </i> {l s='Facebook' mod='ybc_blog'}</a>
                                            </li>
                                        {/if}
                                        {if $use_twitter_share}
                                            <li class="twitter icon-gray">
                                                <a target="_blank" title="{l s='Twitter' mod='ybc_blog'}" class="text-hide" href="https://twitter.com/intent/tweet?text={$blog_post.title|escape:'html':'UTF-8'} {$post_url|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1684 408q-67 98-162 167 1 14 1 42 0 130-38 259.5t-115.5 248.5-184.5 210.5-258 146-323 54.5q-271 0-496-145 35 4 78 4 225 0 401-138-105-2-188-64.5t-114-159.5q33 5 61 5 43 0 85-11-112-23-185.5-111.5t-73.5-205.5v-4q68 38 146 41-66-44-105-115t-39-154q0-88 44-163 121 149 294.5 238.5t371.5 99.5q-8-38-8-74 0-134 94.5-228.5t228.5-94.5q140 0 236 102 109-21 205-78-37 115-142 178 93-10 186-50z"/></svg>
                                                    </i> {l s='Twitter' mod='ybc_blog'}</a>
                                            </li>
                                        {/if}
                                        {if $blog_config.YBC_BLOG_ENABLE_PINTEREST_SHARE}
                                            <li class="pinterest icon-gray">
                                                <a target="_blank" title="{l s='Pinterest' mod='ybc_blog'}" class="text-hide" href="http://www.pinterest.com/pin/create/button/?media={$blog_post.image|escape:'html':'UTF-8'}&url={$post_url|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 896q0 209-103 385.5t-279.5 279.5-385.5 103q-111 0-218-32 59-93 78-164 9-34 54-211 20 39 73 67.5t114 28.5q121 0 216-68.5t147-188.5 52-270q0-114-59.5-214t-172.5-163-255-63q-105 0-196 29t-154.5 77-109 110.5-67 129.5-21.5 134q0 104 40 183t117 111q30 12 38-20 2-7 8-31t8-30q6-23-11-43-51-61-51-151 0-151 104.5-259.5t273.5-108.5q151 0 235.5 82t84.5 213q0 170-68.5 289t-175.5 119q-61 0-98-43.5t-23-104.5q8-35 26.5-93.5t30-103 11.5-75.5q0-50-27-83t-77-33q-62 0-105 57t-43 142q0 73 25 122l-99 418q-17 70-13 177-206-91-333-281t-127-423q0-209 103-385.5t279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                                                    </i> {l s='Pinterest' mod='ybc_blog'}</a>
                                            </li>
                                        {/if}
                                        {if $blog_config.YBC_BLOG_ENABLE_LIKED_SHARE}
                                            <li class="linkedin icon-gray">
                                                <a target="_blank" title="{l s='LinkedIn' mod='ybc_blog'}" class="text-hide" href="https://www.linkedin.com/shareArticle?mini=true&url={$post_url|escape:'html':'UTF-8'}&title={$blog_post.title|escape:'html':'UTF-8'}&summary={$blog_post.short_description|strip_tags:'UTF-8'|truncate:500|escape:'html':'UTF-8'}&source=LinkedIn">
                                                    <i class="ets_svg">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M477 625v991h-330v-991h330zm21-306q1 73-50.5 122t-135.5 49h-2q-82 0-132-49t-50-122q0-74 51.5-122.5t134.5-48.5 133 48.5 51 122.5zm1166 729v568h-329v-530q0-105-40.5-164.5t-126.5-59.5q-63 0-105.5 34.5t-63.5 85.5q-11 30-11 81v553h-329q2-399 2-647t-1-296l-1-48h329v144h-2q20-32 41-56t56.5-52 87-43.5 114.5-15.5q171 0 275 113.5t104 332.5z"/></svg>
                                                    </i> {l s='LinkedIn' mod='ybc_blog'}</a>
                                            </li>
                                        {/if}
                                        {if $blog_config.YBC_BLOG_ENABLE_TUMBLR_SHARE}
                                            <li class="tumblr icon-gray">
                                                <a target="_blank" title="{l s='Tumblr' mod='ybc_blog'}" class="text-hide" href="http://www.tumblr.com/share/link?url={$post_url|escape:'html':'UTF-8'}">
                                                    <i class="ets_svg">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1328 1329l80 237q-23 35-111 66t-177 32q-104 2-190.5-26t-142.5-74-95-106-55.5-120-16.5-118v-544h-168v-215q72-26 129-69.5t91-90 58-102 34-99 15-88.5q1-5 4.5-8.5t7.5-3.5h244v424h333v252h-334v518q0 30 6.5 56t22.5 52.5 49.5 41.5 81.5 14q78-2 134-29z"/></svg>
                                                    </i> {l s='Tumblr' mod='ybc_blog'}</a>
                                            </li>
                                        {/if}
                                    </ul>
                                </div>
                            {/if}
                        </div>
                    </div>
                    <div class="blog_description{if $enable_slideshow} popup_image{/if} {if isset($blog_config.YBC_BLOG_ALLOW_TABLE_OF_CONTENT)&& $blog_config.YBC_BLOG_ALLOW_TABLE_OF_CONTENT} ybc_create_table_content{/if}">
                        <div class="ets_begin_heading_table">&nbsp;</div>
                        {if $blog_post.description}
                            {$blog_post.description nofilter}
                        {else}
                            {$blog_post.short_description nofilter}
                        {/if}
                        <div class="ets_end_heading_table">&nbsp;</div>
                    </div>
                    {if $blog_config.YBC_BLOG_ENABLE_POLLS && $allowPolls}
                        <form>
                            <div class="form-polls">
                                <div class="form-group polls-title noactive">
                                    {if $blog_config.YBC_BLOG_POLLS_TEXT}
                                        <span>{$blog_config.YBC_BLOG_POLLS_TEXT|escape:'html':'UTF-8'}</span>
                                    {else}
                                        <span>{l s='Was this blog post helpful to you?' mod='ybc_blog'}</span>
                                    {/if}
                                    <label for="polls_post_1" {if $polls_class && $polls_class->polls==1}class="disabled"{/if}>
                                        <i class="ets_svg thumbs-o-up">
                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"/></svg>
                                        </i> {l s='Yes' mod='ybc_blog'} <span id="polls_post_helpful_yes">({$polls_post_helpful_yes|intval})</span>
                                        <input id="polls_post_1" type="radio" name="polls_post" value="1" {if $polls_class && $polls_class->polls==1}disabled="disabled"{/if}/>
                                    </label>
                                    <label for="polls_post_0" {if $polls_class && $polls_class->polls==0}class="disabled"{/if}>
                                        <i class="ets_svg thumbs-o-down">
                                            <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 448q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152 576q0-35-21.5-81t-53.5-47q15-17 25-47.5t10-55.5q0-69-53-119 18-31 18-69 0-37-17.5-73.5t-47.5-52.5q5-30 5-56 0-85-49-126t-136-41h-128q-131 0-342 73-5 2-29 10.5t-35.5 12.5-35 11.5-38 11-33 6.5-31.5 3h-32v640h32q16 0 35.5 9t40 27 38.5 35.5 40 44 34.5 42.5 31.5 41 23 30q55 68 77 91 41 43 59.5 109.5t30.5 125.5 38 85q96 0 128-47t32-145q0-59-48-160.5t-48-159.5h352q50 0 89-38.5t39-89.5zm128 1q0 103-76 179t-180 76h-176q48 99 48 192 0 118-35 186-35 69-102 101.5t-151 32.5q-51 0-90-37-34-33-54-82t-25.5-90.5-17.5-84.5-31-64q-48-50-107-127-101-131-137-155h-274q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h288q22 0 138-40 128-44 223-66t200-22h112q140 0 226.5 79t85.5 216v5q60 77 60 178 0 22-3 43 38 67 38 144 0 36-9 69 49 73 49 163z"/></svg>
                                        </i> {l s='No' mod='ybc_blog'}<span id="polls_post_helpful_no">({$polls_post_helpful_no|intval})</span>
                                        <input id="polls_post_0" type="radio" name="polls_post" value="0" {if $polls_class && $polls_class->polls==0}disabled="disabled"{/if}/>
                                    </label>
                                </div>
                                <div class="form-polls-body hidden">
                                    <div class="form-group polls-name">
                                        <input name="polls_name" id="polls_name" placeholder="{l s='Your name' mod='ybc_blog'}" value="{if $polls_customer}{$polls_customer->lastname|escape:'html':'UTF-8'} {$polls_customer->firstname|escape:'html':'UTF-8'}{/if}" {if $polls_customer}readonly="true"{/if}/>
                                    </div>
                                    <div class="form-group polls-email">
                                        <input name="polls_email" id="polls_email" placeholder="{l s='Your email' mod='ybc_blog'}" {if $polls_customer}readonly="true"{/if} value="{if $polls_customer}{$polls_customer->email|escape:'html':'UTF-8'}{/if}"/>
                                    </div>
                                    <div class="form-group polls-feedback">
                                        <textarea name="polls_feedback" id="polls_feedback" placeholder="{l s='Please leave us your feedback' mod='ybc_blog'}"></textarea>
                                    </div>
                                    {if $blog_config.YBC_BLOG_ENABLE_POLLS_CAPCHA}
                                        {if $blog_config.YBC_BLOG_CAPTCHA_TYPE!=='google' && $blog_config.YBC_BLOG_CAPTCHA_TYPE!=='google3'}
                                            <div class="form-group polls-capcha">
                                               <span class="poll-capcha-wrapper">
                                                    <img rel="{$blog_poll_random_code|escape:'html':'UTF-8'}" class="ybc-captcha-img-data" id="ybc-blog-polls-capcha-img" src="{$polls_capcha_image|escape:'html':'UTF-8'}" alt="{l s='Security code' mod='ybc_blog'}" />
                                                    <input placeholder="{l s='Secure code' mod='ybc_blog'}" class="form-control" name="polls_capcha_code" type="text" id="polls-capcha" value="" />
                                                    <span id="ybc-blog-polls-capcha-refesh" title="{l s='Refresh code' mod='ybc_blog'}">
                                                        <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1639 1056q0 5-1 7-64 268-268 434.5t-478 166.5q-146 0-282.5-55t-243.5-157l-129 129q-19 19-45 19t-45-19-19-45v-448q0-26 19-45t45-19h448q26 0 45 19t19 45-19 45l-137 137q71 66 161 102t187 36q134 0 250-65t186-179q11-17 53-117 8-23 30-23h192q13 0 22.5 9.5t9.5 22.5zm25-800v448q0 26-19 45t-45 19h-448q-26 0-45-19t-19-45 19-45l138-138q-148-137-349-137-134 0-250 65t-186 179q-11 17-53 117-8 23-30 23h-199q-13 0-22.5-9.5t-9.5-22.5v-7q65-268 270-434.5t480-166.5q146 0 284 55.5t245 156.5l130-129q19-19 45-19t45 19 19 45z"/></svg>

                                                    </span>
                                                </span>
                                            </div>
                                        {else}
                                        {if $blog_config.YBC_BLOG_CAPTCHA_TYPE=='google'}
                                            <script src="https://www.google.com/recaptcha/api.js?onload=ybc_polls_lonloadCallback&render=explicit" async defer></script>
                                            <div id="ybc_blog_polls_g_recaptcha" class="ybc_blog_g_recaptcha" ></div>
                                        {/if}
                                        {if $blog_config.YBC_BLOG_CAPTCHA_TYPE=='google3'}
                                        <input type="hidden" id="ybc_blog_polls_g_recaptcha" name="g-recaptcha-response" />
                                            <script type="text/javascript">
                                                ybc_polls_lonloadCallback();
                                            </script>
                                        {/if}
                                        {/if}
                                    {/if}
                                    <div class="form_action_footer">
                                        <input type="hidden" value="1" name="polls_submit"/>
                                        <button type="submit" name="polls_submit">{l s='Submit' mod='ybc_blog'}</button>
                                        <button type="button" name="polls_cancel" style="margin-right: 10px;">{l s='Cancel' mod='ybc_blog'}</button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </form>
                    {/if}
                    {if ($show_tags && $blog_post.tags) || ($show_categories && $blog_post.categories)}
                        <div class="extra_tag_cat">
                            {if $show_tags && $blog_post.tags}
                                <div class="ybc-blog-tags">
                                    {assign var='ik' value=0}
                                    {assign var='totalTag' value=count($blog_post.tags)}
                                    <span class="be-label">
                                    {if $totalTag > 1}{l s='Tags' mod='ybc_blog'}
                                    {else}{l s='Tag' mod='ybc_blog'}{/if}:
                                </span>
                                    {foreach from=$blog_post.tags item='tag'}
                                        {assign var='ik' value=$ik+1}
                                        <a href="{$tag.link|escape:'html':'UTF-8'}">{ucfirst($tag.tag)|escape:'html':'UTF-8'}</a>{if $ik < $totalTag}, {/if}
                                    {/foreach}
                                </div>
                            {/if}
                            {if $show_categories && $blog_post.categories}
                                <div class="ybc-blog-categories">
                                    {assign var='ik' value=0}
                                    {assign var='totalCat' value=count($blog_post.categories)}
                                    <div class="be-categories">
                                        <span class="be-label">{l s='Posted in' mod='ybc_blog'}: </span>
                                        {foreach from=$blog_post.categories item='cat'}
                                            {assign var='ik' value=$ik+1}
                                            <a href="{$cat.link|escape:'html':'UTF-8'}">{ucfirst($cat.title)|escape:'html':'UTF-8'}</a>{if $ik < $totalCat}, {/if}
                                        {/foreach}
                                    </div>
                                </div>
                            {/if}
                        </div>
                    {/if}
                    {if isset($blog_config.YBC_BLOG_AUTHOR_INFORMATION)&& $blog_config.YBC_BLOG_AUTHOR_INFORMATION && isset($blog_post.employee.description)&& $blog_post.employee.description}
                        <div class="ybc-block-author ybc-block-author-avata {if $blog_post.employee.avata} ybc-block-author-avata{/if}">
                            {if $blog_post.employee.avata}
                                <div class="avata_img">
                                    <img class="avata" src="{$link->getMediaLink("`$smarty.const._PS_YBC_BLOG_IMG_`avata/`$blog_post.employee.avata|escape:'htmlall':'UTF-8'`")}"/>
                                </div>

                            {/if}
                            <div class="ybc-des-and-author">
                                <div class="ybc-author-name">
                                    <a href="{$blog_post.author_link|escape:'html':'UTF-8'}">
                                        {l s='Author' mod='ybc_blog'}: {$blog_post.employee.name|escape:'html':'UTF-8'}
                                    </a>
                                </div>
                                {if isset($blog_post.employee.description)&&$blog_post.employee.description}
                                    <div class="ybc-author-description">
                                        {$blog_post.employee.description nofilter}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {/if}
                    {if $display_related_products && $blog_post.products}
                        <div id="ybc-blog-related-products" class="" >
                            <h4 class="title_blog">
                                {if count($blog_post.products) > 1}{l s='Related products ' mod='ybc_blog'}
                                {else}{l s='Related product' mod='ybc_blog'}{/if}
                            </h4>
                            <div class="ybc-blog-related-products-wrapper ybc-blog-related-products-list">
                                {assign var='product_row' value=$blog_config.YBC_BLOG_RELATED_PRODUCT_ROW|intval}
                                <div class="dt-{$product_row|intval} blog-product-list product_list grid row ybc_related_products_type_{if $blog_related_product_type}{$blog_related_product_type|escape:'html':'UTF-8'}{else}default{/if} ybc_blog_{$blog_related_product_type|escape:'html':'UTF-8'}">
                                    {assign var='product_row' value=$blog_config.YBC_BLOG_RELATED_PRODUCT_ROW|intval}
                                    {foreach from=$blog_post.products item='product'}
                                        <div class="ajax_block_product col-xs-12 col-sm-4 col-lg-{12/$product_row|intval}">
                                            <div class="product-container"  itemscop >
                                                <div class="left-block">
                                                    <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$product.link|escape:'html':'UTF-8'}">
                                                        <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD) && $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$product.img_url|escape:'html':'UTF-8'}{/if}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$product.img_url|escape:'html':'UTF-8'}" data-src="{$product.img_url|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                                        {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                                            <div class="loader_lady_custom"></div>
                                                        {/if}
                                                    </a>
                                                </div>
                                                <div class="right-block">
                                                    <h5><a href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></h5>
                                                    {if $show_price}
                                                        <div class="blog-product-extra content_price">
                                                            {if $product.price!=$product.old_price}
                                                                <span class="bp-price-old old-price"><span class="bp-price-old-label">{l s='Old price: ' mod='ybc_blog'}</span><span class="bp-price-old-display">{$product.old_price|escape:'html':'UTF-8'}</span></span>
                                                            {/if}
                                                            <span class="bp-price price product-price"><span class="bp-price-label">{l s='Price:  ' mod='ybc_blog'}</span><span class="bp-price-display"  content="{$product.price|escape:'html':'UTF-8'}">{$product.price|escape:'html':'UTF-8'}</span></span>
                                                            {if $product.price!=$product.old_price}
                                                                <span class="bp-percent price-percent-reduction"><span class="bp-percent-label">{l s='Discount: ' mod='ybc_blog'}</span><span class="bp-percent-display">{$product.discount_percent|escape:'html':'UTF-8'}{l s='%' mod='ybc_blog'}</span></span>
                                                                <span class="bp-save"><span class="bp-save-label">{l s='Save up: ' mod='ybc_blog'}</span><span class="bp-save-display">-{$product.discount_amount|escape:'html':'UTF-8'}</span></span>
                                                            {/if}
                                                        </div>
                                                    {/if}
                                                    {hook h='displayProductListReviews' product=$product}
                                                    {if $product.short_description}
                                                        <div class="blog-product-desc">
                                                            {$product.short_description|strip_tags:'UTF-8'|truncate:80:'...'|escape:'html':'UTF-8'}
                                                        </div>
                                                    {/if}

                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div class="ybc-blog-wrapper-comment">
                        {if $allowComments}
                            {if $allowComments==2}
                                <div id="fb-root"></div>
                                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/{$langLocale|escape:'html':'UTF-8'}/sdk.js#xfbml=1&version=v8.0" nonce="xHtLYTmH"></script>
                                <h4 class="title_blog">{l s='Facebook comment' mod='ybc_blog'}</h4>
                                <div class="fb-comments" data-width="760" data-href="{$post_url|escape:'html':'UTF-8'}" data-numposts="5" data-width=""></div>
                            {else}
                                <div class="ybc_comment_form_blog">
                                    <h4 class="title_blog">{l s='Leave a comment' mod='ybc_blog'}</h4>
                                    <div class="ybc-blog-form-comment" id="ybc-blog-form-comment">
                                        {if $hasLoggedIn || $allowGuestsComments}
                                            <form action="#ybc-blog-form-comment" method="post">
                                                {if isset($comment_edit->id) && $comment_edit->id && !$justAdded}
                                                    <input type="hidden" value="{$comment_edit->id|intval}" name="id_comment" />
                                                {/if}
                                                {if !$hasLoggedIn}
                                                    <div class="blog-comment-row blog-name">
                                                        <label for="bc-name">{l s='Name' mod='ybc_blog'}</label>
                                                        <input class="form-control" name="name_customer" id="bc-name" type="text" value="{if isset($name_customer)}{$name_customer|escape:'html':'UTF-8'}{elseif isset($comment_edit->name) && !$justAdded}{$comment_edit->name|escape:'html':'UTF-8'}{/if}" />
                                                    </div>
                                                    <div class="blog-comment-row blog-email">
                                                        <label for="bc-email">{l s='Email' mod='ybc_blog'}</label>
                                                        <input class="form-control" name="email_customer" id="bc-email" type="text" value="{if isset($email_customer)}{$email_customer|escape:'html':'UTF-8'}{elseif isset($comment_edit->email)&& !$justAdded}{$comment_edit->email|escape:'html':'UTF-8'}{/if}" />
                                                    </div>
                                                {/if}
                                                <div class="blog-comment-row blog-title">
                                                    <label for="bc-subject">{l s='Subject ' mod='ybc_blog'}</label>
                                                    <input class="form-control" name="subject" id="bc-subject" type="text" value="{if isset($subject)}{$subject|escape:'html':'UTF-8'}{elseif isset($comment_edit->subject)&& !$justAdded}{$comment_edit->subject|escape:'html':'UTF-8'}{/if}" />
                                                </div>
                                                <div class="blog-comment-row blog-content-comment">
                                                    <label for="bc-comment">{l s='Comment ' mod='ybc_blog'}</label>
                                                    <textarea   class="form-control" name="comment" id="bc-comment">{if isset($comment)}{$comment|escape:'html':'UTF-8'}{elseif isset($comment_edit->comment)&& !$justAdded}{$comment_edit->comment|escape:'html':'UTF-8'}{/if}</textarea>
                                                </div>
                                                <div class="blog-comment-row flex_space_between flex-bottom">
                                                    {if $allow_rating || $use_capcha}
                                                        <div class="blog-rate-capcha">
                                                            {if $allow_rating}
                                                                <div class="blog-rate-post">
                                                                    <label>{l s='Rating: ' mod='ybc_blog'}</label>
                                                                    <div class="blog_rating_box">
                                                                        {if $default_rating > 0 && $default_rating <5}
                                                                            <input id="blog_rating" type="hidden" name="rating" value="{$default_rating|intval}" />
                                                                            {for $i = 1 to $default_rating}
                                                                                <div rel="{$i|intval}" class="star star_on blog_rating_star blog_rating_star_{$i|intval}">★</div>
                                                                            {/for}
                                                                            {for $i = $default_rating + 1 to 5}
                                                                                <div rel="{$i|intval}" class="star blog_rating_star blog_rating_star_{$i|intval}">☆</div>
                                                                            {/for}
                                                                        {else}
                                                                            <input id="blog_rating" type="hidden" name="rating" value="5" />
                                                                            {for $i = 1 to 5}
                                                                                <div rel="{$i|intval}" class="star star_on blog_rating_star blog_rating_star_{$i|intval}">★</div>
                                                                            {/for}
                                                                        {/if}
                                                                    </div>
                                                                </div>
                                                            {/if}
                                                            {if $use_capcha}
                                                                {if $blog_config.YBC_BLOG_CAPTCHA_TYPE!=='google' && $blog_config.YBC_BLOG_CAPTCHA_TYPE!=='google3'}
                                                                    <div class="blog-capcha">
                                                                        <label for="bc-capcha">{l s='Security code: ' mod='ybc_blog'}</label>
                                                                        <span class="bc-capcha-wrapper">
                                                                        <img rel="{$blog_random_code|escape:'html':'UTF-8'}" class="ybc-captcha-img-data" id="ybc-blog-capcha-img" src="{$capcha_image|escape:'html':'UTF-8'}" alt="{l s='Security code' mod='ybc_blog'}" />
                                                                        <input class="form-control" name="capcha_code" type="text" id="bc-capcha" value="" />
                                                                        <span id="ybc-blog-capcha-refesh" title="{l s='Refresh code' mod='ybc_blog'}"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1639 1056q0 5-1 7-64 268-268 434.5t-478 166.5q-146 0-282.5-55t-243.5-157l-129 129q-19 19-45 19t-45-19-19-45v-448q0-26 19-45t45-19h448q26 0 45 19t19 45-19 45l-137 137q71 66 161 102t187 36q134 0 250-65t186-179q11-17 53-117 8-23 30-23h192q13 0 22.5 9.5t9.5 22.5zm25-800v448q0 26-19 45t-45 19h-448q-26 0-45-19t-19-45 19-45l138-138q-148-137-349-137-134 0-250 65t-186 179q-11 17-53 117-8 23-30 23h-199q-13 0-22.5-9.5t-9.5-22.5v-7q65-268 270-434.5t480-166.5q146 0 284 55.5t245 156.5l130-129q19-19 45-19t45 19 19 45z"></path></svg></span>
                                                                    </span>
                                                                    </div>
                                                                {else}
                                                                {if $blog_config.YBC_BLOG_CAPTCHA_TYPE=='google'}
                                                                    <script src="https://www.google.com/recaptcha/api.js?onload=ybc_comment_lonloadCallback&render=explicit" async defer></script>
                                                                    <div id="ybc_blog_comment_g_recaptcha" class="ybc_blog_g_recaptcha" ></div>
                                                                {/if}
                                                                {if $blog_config.YBC_BLOG_CAPTCHA_TYPE=='google3'}
                                                                <input type="hidden" id="ybc_blog_comment_g_recaptcha" name="g-recaptcha-response" />
                                                                    <script type="text/javascript">
                                                                        ybc_comment_lonloadCallback();
                                                                    </script>
                                                                {/if}
                                                                {/if}
                                                            {/if}
                                                        </div>
                                                    {/if}
                                                    <div class="blog-submit-form">
                                                        {if !Configuration::get('YBC_BLOG_DISPLAY_GDPR_NOTIFICATION')}
                                                            <div class="blog-submit">
                                                                <input name="bcsubmit" type="hidden" value="1">
                                                                <input class="button" type="submit" value="{l s='Submit Comment' mod='ybc_blog'}" name="bcsubmit" />
                                                            </div>
                                                        {/if}
                                                    </div>
                                                    {if $blog_errors && is_array($blog_errors) && !isset($replyCommentsave)}
                                                        <div class="alert alert-danger ybc_alert-danger">
                                                            <button class="close" type="button" data-dismiss="alert">×</button>
                                                            <ul>
                                                                {foreach from=$blog_errors item='error'}
                                                                    <li>{$error|escape:'html':'UTF-8'}</li>
                                                                {/foreach}
                                                            </ul>
                                                        </div>
                                                    {/if}
                                                </div>
                                                <div class="blog-comment-row">
                                                    <div class="blog-submit-form">
                                                        {if Configuration::get('YBC_BLOG_DISPLAY_GDPR_NOTIFICATION')}
                                                            <label for="check_gpdr">
                                                                <input id="check_gpdr" type="checkbox" type="check_gpdr" value="1"/>&nbsp;{$text_gdpr|escape:'html':'UTF-8'}
                                                                <a href="{if Configuration::get('YBC_BLOG_TEXT_GDPR_NOTIFICATION_URL_MORE',$id_lang)}{Configuration::get('YBC_BLOG_TEXT_GDPR_NOTIFICATION_URL_MORE',$id_lang)|escape:'html':'UTF-8'}{else}#{/if}">{if Configuration::get('YBC_BLOG_TEXT_GDPR_NOTIFICATION_TEXT_MORE',$id_lang)}{Configuration::get('YBC_BLOG_TEXT_GDPR_NOTIFICATION_TEXT_MORE',$id_lang)|escape:'html':'UTF-8'}{else}{l s='View more details here' mod='ybc_blog'}{/if}</a>
                                                            </label>
                                                            <div class="blog-submit">
                                                                <input name="bcsubmit" type="hidden" value="1">
                                                                <input class="button" type="submit" disabled="disabled" value="{l s='Submit Comment' mod='ybc_blog'}" name="bcsubmit" />
                                                            </div>
                                                        {/if}
                                                    </div>
                                                </div>
                                                {if $blog_success && !$replyCommentsaveok}
                                                    <p class="alert alert-success ybc_alert-success">
                                                        <button class="close" type="button" data-dismiss="alert">×</button>
                                                        {$blog_success|escape:'html':'UTF-8'}
                                                    </p>
                                                {/if}
                                            </form>
                                        {else}
                                            <p class="alert alert-warning">{l s='Log in to post comments' mod='ybc_blog'}</p>
                                        {/if}
                                    </div>
                                </div>
                            {if count($comments)}
                                <div class="ybc_blog-comments-list">
                                    <h4 class="title_blog">{l s='Comments' mod='ybc_blog'}</h4>
                                    <ul id="blog-comments-list" class="blog-comments-list">
                                        {foreach from=$comments item='comment'}
                                            <li id="blog_comment_line_{$comment.id_comment|intval}" class="blog-comment-line">
                                                <div class="ybc-blog-detail-comment">
                                                    <h5 class="comment-subject">{$comment.subject|escape:'html':'UTF-8'}</h5>
                                                    {if $comment.name}<span class="comment-by">{l s='By: ' mod='ybc_blog'}<b>{ucfirst($comment.name)|escape:'html':'UTF-8'}</b></span>{/if}
                                                    <span class="comment-time"><span>{l s='On' mod='ybc_blog'} </span>{dateFormat date=$comment.datetime_added full=0}</span>
                                                    {if $allow_rating && $comment.rating > 0}
                                                        <div class="comment-rating" >
                                                            <span>{l s='Rating: ' mod='ybc_blog'}</span>
                                                            <div title="{l s='Average rating' mod='ybc_blog'}" class="ybc_blog_review"  data-rate="{$comment.rating|escape:'html':'UTF-8'}">
                                                                {assign var='everage_rating' value=$comment.rating}
                                                                {if $everage_rating == 1}★☆☆☆☆
                                                                {elseif  $everage_rating == 2}★★☆☆☆
                                                                {elseif  $everage_rating == 3}★★★☆☆
                                                                {elseif  $everage_rating == 4}★★★★☆
                                                                {elseif  $everage_rating == 5}★★★★★{/if}
                                                                <span class="ybc-blog-rating-value"> ({number_format((float)$comment.rating, 1, '.', '')|escape:'html':'UTF-8'})</span>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    <div class="ybc-block-report-reply-edit-delete">
                                                        {if $allow_report_comment}
                                                            {if !($reportedComments && is_array($reportedComments) && in_array($comment.id_comment, $reportedComments))}
                                                                <span class="ybc-block-comment-report comment-report-{$comment.id_comment|intval}" rel="{$comment.id_comment|intval}">
                                                                    <i class="ets_svg" aria-hidden="true" title="{l s='Report this comment as abused' mod='ybc_blog'}"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1333 566q18 20 7 44l-540 1157q-13 25-42 25-4 0-14-2-17-5-25.5-19t-4.5-30l197-808-406 101q-4 1-12 1-18 0-31-11-18-15-13-39l201-825q4-14 16-23t28-9h328q19 0 32 12.5t13 29.5q0 8-5 18l-171 463 396-98q8-2 12-2 19 0 34 15z"/></svg>
</i>
                                                                </span>
                                                            {/if}
                                                        {/if}
                                                        {if isset($comment.reply) && $comment.reply}
                                                            <span class="ybc-block-comment-reply comment-reply-{$comment.id_comment|intval}" rel="{$comment.id_comment|intval}"><i class="ets_svg" aria-hidden="true" title="{l s='Reply' mod='ybc_blog'}"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1120q0 166-127 451-3 7-10.5 24t-13.5 30-13 22q-12 17-28 17-15 0-23.5-10t-8.5-25q0-9 2.5-26.5t2.5-23.5q5-68 5-123 0-101-17.5-181t-48.5-138.5-80-101-105.5-69.5-133-42.5-154-21.5-175.5-6h-224v256q0 26-19 45t-45 19-45-19l-512-512q-19-19-19-45t19-45l512-512q19-19 45-19t45 19 19 45v256h224q713 0 875 403 53 134 53 333z"/></svg>
</i></span>
                                                        {/if}
                                                        {if isset($comment.url_edit)}
                                                            <a class="ybc-block-comment-edit comment-edit-{$comment.id_comment|intval}" href="{$comment.url_edit|escape:'html':'UTF-8'}"><i class="ets_svg" aria-hidden="true" title="{l s='Edit this comment' mod='ybc_blog'}">
                                                                    <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg>

                                                                </i></a>
                                                        {/if}
                                                        {if isset($comment.url_delete)}
                                                            <a class="ybc-block-comment-delete delete-edit-{$comment.id_comment|intval}" href="{$comment.url_delete|escape:'html':'UTF-8'}"><i class="ets_svg" aria-hidden="true" title="{l s='Delete this comment' mod='ybc_blog'}">
                                                                    <svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>

                                                                </i></a>
                                                        {/if}
                                                    </div>
                                                    {if $comment.comment}<p class="comment-content">{$comment.comment nofilter}</p>{/if}
                                                    {if $comment.replies}
                                                        {foreach $comment.replies item='reply'}
                                                            <p class="comment-reply">
                                                                <span class="ybc-blog-replied-by">
                                                                    <i class="ets_svg" aria-hidden="true" title="Reply"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1792 1120q0 166-127 451-3 7-10.5 24t-13.5 30-13 22q-12 17-28 17-15 0-23.5-10t-8.5-25q0-9 2.5-26.5t2.5-23.5q5-68 5-123 0-101-17.5-181t-48.5-138.5-80-101-105.5-69.5-133-42.5-154-21.5-175.5-6h-224v256q0 26-19 45t-45 19-45-19l-512-512q-19-19-19-45t19-45l512-512q19-19 45-19t45 19 19 45v256h224q713 0 875 403 53 134 53 333z"></path></svg>
</i> {l s='Replied by: ' mod='ybc_blog'}
                                                                    <span class="ybc-blog-replied-by-name">
                                                                        {ucfirst($reply.name)|escape:'html':'UTF-8'}
                                                                    </span>
                                                                </span>
                                                                <span class="comment-time"><span>{l s='On' mod='ybc_blog'} </span>{dateFormat date =$reply.datetime_added full=0}</span>
                                                                <span class="ybc-blog-reply-content">
                                                                    {$reply.reply nofilter}
                                                                </span>
                                                            </p>
                                                        {/foreach}
                                                    {/if}
                                                    {if isset($replyCommentsave) && $replyCommentsave==$comment.id_comment}
                                                        {if isset($replyCommentsaveok) && $blog_success}
                                                            <p class="alert alert-success ybc_alert-success">
                                                                <button class="close" type="button" data-dismiss="alert">×</button>{$blog_success|escape:'html':'UTF-8'}
                                                            </p>
                                                        {else}
                                                            {if isset($comment.reply) && $comment.reply}
                                                                <form class="form_reply_comment" action="#blog_comment_line_{$comment.id_comment|intval}" method="post">
                                                                    {if $blog_errors && is_array($blog_errors) && isset($replyCommentsave)}
                                                                        <div class="alert alert-danger ybc_alert-danger">
                                                                            <button class="close" type="button" data-dismiss="alert">×</button>
                                                                            <ul >
                                                                                {foreach from=$blog_errors item='error'}
                                                                                    <li>{$error|escape:'html':'UTF-8'}</li>
                                                                                {/foreach}
                                                                            </ul>
                                                                        </div>
                                                                    {/if}
                                                                    <input type="hidden" name="replyCommentsave" value="{$comment.id_comment|intval}" />
                                                                    <textarea name="reply_comment_text" placeholder="{l s='Enter your message...' mod='ybc_blog'}">{$reply_comment_text nofilter}</textarea>
                                                                    <input type="submit" value="Send" />
                                                                </form>
                                                            {else}
                                                                {if $blog_errors && is_array($blog_errors) && isset($replyCommentsave)}
                                                                    <div class="alert alert-danger ybc_alert-danger">
                                                                        <button class="close" type="button" data-dismiss="alert">×</button>
                                                                        <ul >
                                                                            {foreach from=$blog_errors item='error'}
                                                                                <li>{$error|escape:'html':'UTF-8'}</li>
                                                                            {/foreach}
                                                                        </ul>
                                                                    </div>
                                                                {/if}
                                                            {/if}
                                                        {/if}
                                                    {/if}
                                                </div>
                                            </li>
                                        {/foreach}
                                    </ul>
                                    {if isset($link_view_all_comment)}
                                        <div class="blog_view_all_button">
                                            <a href="{$link_view_all_comment|escape:'html':'UTF-8'}" class="view_all_link">{l s='View all comments' mod='ybc_blog'}</a>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                            {/if}
                        {/if}
                    </div>
                </div>
                {else}
                    <p class="warning">{l s='No posts found' mod='ybc_blog'}</p>
                {/if}
                {if $blog_post.related_posts}
                    <div data-items="{$blog_config.YBC_BLOG_RELATED_POST_ROW|intval}" class="ybc-blog-related-posts tttt ybc_blog_related_posts_type_{if $blog_related_posts_type}{$blog_related_posts_type|escape:'html':'UTF-8'}{else}default{/if} ybc_blog_{$blog_related_posts_type|escape:'html':'UTF-8'}">
                        <h4 class="title_blog">{l s='Related posts' mod='ybc_blog'}</h4>
                        <div class="ybc-blog-related-posts-wrapper">
                            {assign var='post_row' value=$blog_config.YBC_BLOG_RELATED_POST_ROW|intval}
                            <div class="ybc-blog-related-posts-list dt-{$post_row|intval}">
                                {foreach from=$blog_post.related_posts item='rpost'}
                                    <div class="ybc_blog_content_block_item ybc-blog-related-posts-list-li col-xs-12 col-sm-4 col-lg-{12/$post_row|intval} thumbnail-container">
                                        {if $rpost.thumb}
                                            <a class="ybc_item_img{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} ybc_item_img_ladyload{/if}" href="{$rpost.link|escape:'html':'UTF-8'}">
                                                <img width="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH'|escape:'html':'UTF-8')}" height="{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT'|escape:'html':'UTF-8')}" src="{if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}{$link->getMediaLink("`$smarty.const._MODULE_DIR_`ybc_blog/views/img/bg-grey.png")|escape:'html':'UTF-8'}{else}{$rpost.thumb|escape:'html':'UTF-8'}{/if}" alt="{$rpost.title|escape:'html':'UTF-8'}" {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD} data-original="{$rpost.thumb|escape:'html':'UTF-8'}" class="lazyload"{/if} />
                                                {if isset($blog_config.YBC_BLOG_LAZY_LOAD)&& $blog_config.YBC_BLOG_LAZY_LOAD}
                                                    <div class="loader_lady_custom"></div>
                                                {/if}
                                            </a>

                                    {/if}
                                    <a class="ybc_title_block" href="{$rpost.link|escape:'html':'UTF-8'}">{$rpost.title|escape:'html':'UTF-8'}</a>
                                    <div class="ybc-blog-sidear-post-meta">
                                        {if $rpost.categories}
                                            {assign var='ik' value=0}
                                            {assign var='totalCat' value=count($rpost.categories)}
                                            <div class="ybc-blog-categories">
                                                <span class="be-label">{l s='Posted in' mod='ybc_blog'}: </span>
                                                {foreach from=$rpost.categories item='cat'}
                                                    {assign var='ik' value=$ik+1}
                                                    <a href="{$cat.link|escape:'html':'UTF-8'}">{ucfirst($cat.title)|escape:'html':'UTF-8'}</a>{if $ik < $totalCat}, {/if}
                                                {/foreach}
                                            </div>
                                        {/if}
                                        <span class="post-date">{dateFormat date=$rpost.datetime_added full=0}</span>
                                    </div>
                                    {if $allowComments || $show_views || $allow_like}
                                        <div class="ybc-blog-latest-toolbar">
                                            {if $show_views}
                                                <span class="ybc-blog-latest-toolbar-views">
                                                        <i class="ets_svg">
                                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1664 960q-152-236-381-353 61 104 61 225 0 185-131.5 316.5t-316.5 131.5-316.5-131.5-131.5-316.5q0-121 61-225-229 117-381 353 133 205 333.5 326.5t434.5 121.5 434.5-121.5 333.5-326.5zm-720-384q0-20-14-34t-34-14q-125 0-214.5 89.5t-89.5 214.5q0 20 14 34t34 14 34-14 14-34q0-86 61-147t147-61q20 0 34-14t14-34zm848 384q0 34-20 69-140 230-376.5 368.5t-499.5 138.5-499.5-139-376.5-368q-20-35-20-69t20-69q140-229 376.5-368t499.5-139 499.5 139 376.5 368q20 35 20 69z"/></svg>
                                                            </i> {$rpost.click_number|intval}
                                                    {if $rpost.click_number!=1}{l s='views' mod='ybc_blog'}
                                                        {else}{l s='view' mod='ybc_blog'}{/if}
                                                    </span>
                                            {/if}
                                            {if $allow_like}
                                                <span class="ybc-blog-like-span ybc-blog-like-span-{$rpost.id_post|intval} {if isset($rpost.liked) && $rpost.liked}active{/if}"  data-id-post="{$rpost.id_post|intval}">
                                                        <i class="ets_svg">
                                            <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M384 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm1152-576q0-51-39-89.5t-89-38.5h-352q0-58 48-159.5t48-160.5q0-98-32-145t-128-47q-26 26-38 85t-30.5 125.5-59.5 109.5q-22 23-77 91-4 5-23 30t-31.5 41-34.5 42.5-40 44-38.5 35.5-40 27-35.5 9h-32v640h32q13 0 31.5 3t33 6.5 38 11 35 11.5 35.5 12.5 29 10.5q211 73 342 73h121q192 0 192-167 0-26-5-56 30-16 47.5-52.5t17.5-73.5-18-69q53-50 53-119 0-25-10-55.5t-25-47.5q32-1 53.5-47t21.5-81zm128-1q0 89-49 163 9 33 9 69 0 77-38 144 3 21 3 43 0 101-60 178 1 139-85 219.5t-227 80.5h-129q-96 0-189.5-22.5t-216.5-65.5q-116-40-138-40h-288q-53 0-90.5-37.5t-37.5-90.5v-640q0-53 37.5-90.5t90.5-37.5h274q36-24 137-155 58-75 107-128 24-25 35.5-85.5t30.5-126.5 62-108q39-37 90-37 84 0 151 32.5t102 101.5 35 186q0 93-48 192h176q104 0 180 76t76 179z"></path></svg>
                                        </i> <span class="ben_{$rpost.id_post|intval}">
                                                        {$rpost.likes|intval}
                                                        </span>
                                                        <span class="blog-post-like-text blog-post-like-text-{$rpost.id_post|intval}">
                                                            {l s='Liked' mod='ybc_blog'}
                                                        </span>
                                                    </span>
                                            {/if}
                                            {if $allowComments}
                                                {if $rpost.comments_num > 0 }
                                                    <span class="ybc-blog-latest-toolbar-comments">
                                                        <i class="ets_svg"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 384q-204 0-381.5 69.5t-282 187.5-104.5 255q0 112 71.5 213.5t201.5 175.5l87 50-27 96q-24 91-70 172 152-63 275-171l43-38 57 6q69 8 130 8 204 0 381.5-69.5t282-187.5 104.5-255-104.5-255-282-187.5-381.5-69.5zm896 512q0 174-120 321.5t-326 233-450 85.5q-70 0-145-8-198 175-460 242-49 14-114 22h-5q-15 0-27-10.5t-16-27.5v-1q-3-4-.5-12t2-10 4.5-9.5l6-9 7-8.5 8-9q7-8 31-34.5t34.5-38 31-39.5 32.5-51 27-59 26-76q-157-89-247.5-220t-90.5-281q0-174 120-321.5t326-233 450-85.5 450 85.5 326 233 120 321.5z"/></svg>
                                                                </i> {$rpost.comments_num|intval}
                                                        {if $rpost.comments_num!=1}{l s='comments' mod='ybc_blog'}
                                                            {else}{l s='comment' mod='ybc_blog'}
                                                        {/if}
                                                        </span>
                                                {/if}
                                            {/if}
                                        </div>
                                    {/if}
                                    {if $display_desc}
                                        {if $rpost.short_description}
                                            <div class="blog_description">{$rpost.short_description|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}</div>
                                        {elseif $rpost.description}
                                            <div class="blog_description">{$rpost.description|strip_tags:'UTF-8'|truncate:120:'...'|escape:'html':'UTF-8'}</div>
                                        {/if}
                                    {/if}
                                    {*<a class="read_more" href="{$rpost.link|escape:'html':'UTF-8'}">{if $blog_config.YBC_BLOG_TEXT_READMORE}{$blog_config.YBC_BLOG_TEXT_READMORE|escape:'html':'UTF-8'}{else}{l s='Read More' mod='ybc_blog'}{/if}</a>*}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{else}
    <p class="alert alert-warning">{l s='This blog post is not available' mod='ybc_blog'}</p>
{/if}