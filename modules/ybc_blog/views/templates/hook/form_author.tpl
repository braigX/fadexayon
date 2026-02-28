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
<div class="blog-managament-information">
    <div class="panel ybc-blog-panel">
        <div class="panel-heading">
            {l s='My information' mod='ybc_blog'}
        </div>
    </div>
    <form class="defaultForm form-horizontal" novalidate="" enctype="multipart/form-data" method="post" action="{$action_link|escape:'html':'UTF-8'}">
        <section class="form-fields">
            <div class="form-group row ">
                <label class="col-md-3 form-control-label" for="author_name">{l s='Name' mod='ybc_blog'}</label>
                <div class="col-md-9">
                    <input id="author_name" class="form-control" readonly="true" type="text" value="{if isset($smarty.post.author_name)}{$smarty.post.author_name|escape:'html':'UTF-8'}{else}{$name_author|escape:'html':'UTF-8'}{/if}" name="author_name" />
                    <p class="help-block"> <a href="{$link->getPageLink('identity')|escape:'html':'UTF-8'}" title="{l s='Update my name' mod='ybc_blog'}">{l s='Update my name' mod='ybc_blog'}</a> </p>
                </div>
            </div>
            <div class="form-group row ">
                <label class="col-md-3 form-control-label" for="author_description">{l s='Introduction info' mod='ybc_blog'}</label>
                <div class="col-md-9">
                    <textarea name="author_description" id="author_description">{if isset($smarty.post.author_description)}{$smarty.post.author_description nofilter}{else}{$author_description nofilter}{/if}</textarea>
                </div>
            </div>
            {if isset($allow_update_avata) && $allow_update_avata}
                <div class="form-group row ">
                    <label class="col-md-3 form-control-label" for="author_avata">{l s='Avatar' mod='ybc_blog'}</label>
                    <div class="col-md-9">
                        <div class="upload_form_custom">
                            <span class="input-group-addon"><i class="ets_svg file">
                                    <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1152 512v-472q22 14 36 28l408 408q14 14 28 36h-472zm-128 32q0 40 28 68t68 28h544v1056q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h800v544z"/></svg>
                                </i></span>
                            <span class="input-group-btn">
                                <i class="ets_svg folder-open"><svg width="16" height="14" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1943 952q0 31-31 66l-336 396q-43 51-120.5 86.5t-143.5 35.5h-1088q-34 0-60.5-13t-26.5-43q0-31 31-66l336-396q43-51 120.5-86.5t143.5-35.5h1088q34 0 60.5 13t26.5 43zm-343-344v160h-832q-94 0-197 47.5t-164 119.5l-337 396-5 6q0-4-.5-12.5t-.5-12.5v-960q0-92 66-158t158-66h320q92 0 158 66t66 158v32h544q92 0 158 66t66 158z"/></svg>
</i>{l s='Add file' mod='ybc_blog'}
                            </span>
                            <input class="form-control" type="file" value="" name="author_avata" id="author_avata" />
                        </div>
                        <p class="help-block">{l s='Recommended size: ' mod='ybc_blog'}&nbsp;{Configuration::get('YBC_BLOG_IMAGE_AVATA_WIDTH',null,null,null,300)|intval}x{Configuration::get('YBC_BLOG_IMAGE_AVATA_HEIGHT',null,null,null,300)|intval} </p>
                        {if $author_avata}
                            <div class="thumb_post">
                                <img style="max-width: 200px;" src="{$author_avata|escape:'html':'UTF-8'}" title="{$name_author|escape:'html':'UTF-8'}" alt="{$name_author|escape:'html':'UTF-8'}" />
                                <a onclick="return confirm('{l s='Do you want to delete avatar image?' mod='ybc_blog'}');" class="delete_url" href="{$link_delete_image|escape:'html':'UTF-8'}" style="display: inline-block; text-decoration: none!important;">
                                    <span style="color: #666">
                                        <i class="ets_svg"><svg width="18" height="18" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                                                                </i> {l s='Delete' mod='ybc_blog'}
                                    </span>
                                </a>
                            </div>
                        {else}
                            <div class="thumb_post">
                                <img style="max-width: 200px;" src="{$avata_default|escape:'html':'UTF-8'}" title="{l s='Default avatar' mod='ybc_blog'}" />
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}
        </section>
        <button class="btn btn-primary float-xs-right" type="submit" name="submitAuthorManagement">{l s='Save' mod='ybc_blog'}</button>
    </form>
</div>