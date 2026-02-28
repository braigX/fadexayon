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
{assign var="is_upload_video" value=isset($productComment) && $productComment->id}
{if $is_upload_video}
    <form class="ets_rv_form_upload_video" action="{$action nofilter}" enctype="multipart/form-data" action="POST">
    	<ul class="ets_rv_videos ets_rv_upload_videos {if $videos|count > 8} multi_videos{/if}" data-count="{$videos|count}">
            {/if}
            {assign var="videoCount" value=isset($videos) && $videos|count > 0 && $path_uri}
            {if !$is_upload_video && $videoCount}
    		<ul class="ets_rv_videos {if $videos|count > 8} multi_videos{/if}" data-count="{$videos|count}">
                {/if}
                {assign var="ik" value=1}
                {if $videoCount}
                    {foreach from=$videos item='video'}
                        {assign var="ik" value=$ik+1}
        				<li class="ets_rv_video_item">
        					 <video controls>
                              <source src="{$video.url|escape:'html':'UTF-8'}" type="{$video.type|escape:'html':'UTF-8'}" />
                            </video> 
        				</li>
                    {/foreach}
					<li class="extra_videos">
						<span class="extra_videos_show" title="{l s='More videos' mod='ets_reviews'}"></span>
					</li>
                {/if}
                {if !$is_upload_video && $videoCount}
    		</ul>
            {/if}
            {if $is_upload_video}
    	</ul>
    </form>
{/if}