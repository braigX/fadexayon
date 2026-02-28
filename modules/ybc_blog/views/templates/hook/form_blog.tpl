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
<form id="form_blog" class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post" action="{$action|escape:'html':'UTF-8'}">
    <div class="panel ybc-blog-panel">
        <div class="panel-heading">
            {if $ybc_post->id}
                {l s='Edit blog post' mod='ybc_blog'}
                <a class="edit_view_post btn btn-primary float-xs-right" href="{$link_post|escape:'html':'UTF-8'}">{l s='View post' mod='ybc_blog'}</a>
            {else}
                <h3>{l s='Submit new post' mod='ybc_blog'}</h3>
            {/if}
        </div>
    </div>
    <section class="form-fields">
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="post_title">{l s='Post title' mod='ybc_blog'}<span class="required">*</span></label>
            <div class="col-md-9">
                <input id="post_title" class="form-control" type="text" value="{if isset($smarty.post.title)}{$smarty.post.title|escape:'html':'UTF-8'}{else}{if $ybc_post->id}{$ybc_post->title|escape:'html':'UTF-8'}{/if}{/if}" name="title" title="{if $ybc_post->id}{$ybc_post->title|escape:'html':'UTF-8'}{/if}" />
            </div>
        </div>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="short_description">{l s='Short description' mod='ybc_blog'}<span class="required">*</span></label>
            <div class="col-md-9">
                <textarea class="ets_blog_autoload_rte" name="short_description" id="short_description">{if isset($smarty.post.short_description)}{$smarty.post.short_description nofilter}{else}{if $ybc_post->id}{$ybc_post->short_description nofilter}{/if}{/if}</textarea>
            </div>
        </div>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="description">{l s='Post content' mod='ybc_blog'}<span class="required">*</span></label>
            <div class="col-md-9">
                <textarea class="ets_blog_autoload_rte" name="description" id="description">{if isset($smarty.post.description)}{$smarty.post.description nofilter}{else}{if $ybc_post->id}{$ybc_post->description nofilter}{/if}{/if}</textarea>
            </div>
        </div>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="category">{l s='Categories' mod='ybc_blog'}<span class="required">*</span></label>
            <div class="col-md-9">
                <ul style="float: left; padding: 0; margin-top: 5px;">
                    {$html_content_category_block nofilter}
                </ul>
            </div>
        </div>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="thumb">{l s='Post thumbnail' mod='ybc_blog'}<span class="required">*</span></label>
            <div class="col-md-9">
                <div class="upload_form_custom">
        			<span class="input-group-addon"><i class="ets_svg file">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 512v-472q22 14 36 28l408 408q14 14 28 36h-472zm-128 32q0 40 28 68t68 28h544v1056q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h800v544z"/></svg>
                                </i></span>
        			<span class="input-group-btn">
        				<i class="ets_svg folder-open"><svg width="16" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1943 952q0 31-31 66l-336 396q-43 51-120.5 86.5t-143.5 35.5h-1088q-34 0-60.5-13t-26.5-43q0-31 31-66l336-396q43-51 120.5-86.5t143.5-35.5h1088q34 0 60.5 13t26.5 43zm-343-344v160h-832q-94 0-197 47.5t-164 119.5l-337 396-5 6q0-4-.5-12.5t-.5-12.5v-960q0-92 66-158t158-66h320q92 0 158 66t66 158v32h544q92 0 158 66t66 158z"/></svg>
</i>{l s='Add file' mod='ybc_blog'}
				    </span>
                    <input class="form-control" type="file" value="" name="thumb" id="thumb" />
        		</div>
                <p class="help-block">{l s='Accepted formats: jpg, jpeg, png, gif. Limit: ' mod='ybc_blog'}{Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|intval}Mb. {l s='Recommended size:' mod='ybc_blog'}&nbsp;{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_WIDTH',null,null,null,260)|intval}x{Configuration::get('YBC_BLOG_IMAGE_BLOG_THUMB_HEIGHT',null,null,null,180)|intval}</p>
                {if $ybc_post->id && $ybc_post->thumb}
                    <div class="thumb_post">
                        <img style="max-width: 200px;display: inline-block;" src="{$dir_img|escape:'html':'UTF-8'}post/thumb/{$ybc_post->thumb|escape:'html':'UTF-8'}" title="{$ybc_post->title|escape:'html':'UTF-8'}" alt="{$ybc_post->title|escape:'html':'UTF-8'}" />
                    </div>
                {/if}
            </div>
        </div>
        <div class="form-group row ">
            <label class="col-md-3 form-control-label" for="post_image">{l s='Blog post main image' mod='ybc_blog'}</label>
            <div class="col-md-9">
                <div class="upload_form_custom">
        			<span class="input-group-addon"><i class="ets_svg file">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 512v-472q22 14 36 28l408 408q14 14 28 36h-472zm-128 32q0 40 28 68t68 28h544v1056q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h800v544z"/></svg>
                                </i></span>
        			<span class="input-group-btn">
        				<i class="ets_svg folder-open"><svg width="16" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1943 952q0 31-31 66l-336 396q-43 51-120.5 86.5t-143.5 35.5h-1088q-34 0-60.5-13t-26.5-43q0-31 31-66l336-396q43-51 120.5-86.5t143.5-35.5h1088q34 0 60.5 13t26.5 43zm-343-344v160h-832q-94 0-197 47.5t-164 119.5l-337 396-5 6q0-4-.5-12.5t-.5-12.5v-960q0-92 66-158t158-66h320q92 0 158 66t66 158v32h544q92 0 158 66t66 158z"/></svg>
</i>{l s='Add file' mod='ybc_blog'}
				    </span>
                    <input class="form-control" type="file" value="" name="image" id="post_image" />
        		</div>
                <p class="help-block">{l s='Accepted formats: jpg, jpeg, png, gif. Limit: ' mod='ybc_blog'}{Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')|intval}Mb. {l s='Recommended size: ' mod='ybc_blog'}&nbsp;{Configuration::get('YBC_BLOG_IMAGE_BLOG_WIDTH',null,null,null,1920)|intval}x{Configuration::get('YBC_BLOG_IMAGE_BLOG_HEIGHT',null,null,null,750)|intval}</p>
                {if $ybc_post->id && $ybc_post->image}
                    <div class="thumb_post">
                        <img style="max-width: 200px;" src="{$dir_img|escape:'html':'UTF-8'}post/{$ybc_post->image|escape:'html':'UTF-8'}" title="{$ybc_post->title|escape:'html':'UTF-8'}" alt="{$ybc_post->title|escape:'html':'UTF-8'}" />
                        <a onclick="return confirm('{l s='Do you want to delete blog post main image?' mod='ybc_blog'}');" class="delete_url" href="{$link_delete_image|escape:'html':'UTF-8'}" style="display: inline-block; text-decoration: none!important;">
                            <span style="color: #666">
                                <i class="ets_svg"><svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                                                                </i>
                            </span>
                        </a>
                    </div>
                {/if}
            </div>
        </div>
        <input name="id_post" value="{if $ybc_post->id}{$ybc_post->id|intval}{/if}" type="hidden"/>
        <input name="token" value="{Tools::getToken()|escape:'html':'UTF-8'}" type="hidden">
    </section>
    <a class="btn btn-primary float-xs-left ybc_button_backtolist" href="{$link_back_list|escape:'html':'UTF-8'}">
        {l s='Back to list' mod='ybc_blog'}
    </a>
    <button class="btn btn-primary float-xs-right" name="submitPostStay" type="submit">{if $ybc_post->id}{l s='Save' mod='ybc_blog'}{else}{l s='Submit' mod='ybc_blog'}{/if}</button>
</form>
