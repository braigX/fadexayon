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
<!-- MODULE ybc_blog -->
{if $author && !$suppened}
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementblog',array(),true)|escape:'html':'UTF-8'}" title="{l s='Blog management' mod='ybc_blog'}">
		<i class="ets_svg user">
			<svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1536 1399q0 109-62.5 187t-150.5 78h-854q-88 0-150.5-78t-62.5-187q0-85 8.5-160.5t31.5-152 58.5-131 94-89 134.5-34.5q131 128 313 128t313-128q76 0 134.5 34.5t94 89 58.5 131 31.5 152 8.5 160.5zm-256-887q0 159-112.5 271.5t-271.5 112.5-271.5-112.5-112.5-271.5 112.5-271.5 271.5-112.5 271.5 112.5 112.5 271.5z"/></svg>
		</i>
        <span>{l s='My blog posts' mod='ybc_blog'}</span>
	</a>
</li>
{/if}
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementcomments',array(),true)|escape:'html':'UTF-8'}" title="{l s='My blog comments' mod='ybc_blog'}">
		<i class="icon-comments">&nbsp;</i>
        <span>{l s='My blog comments' mod='ybc_blog'}</span>
	</a>
</li>
<li class="lnk_ybc_blog">
	<a href="{$link->getModuleLink('ybc_blog','managementmyinfo',array(),true)|escape:'html':'UTF-8'}" title="{l s='My blog info' mod='ybc_blog'}">
		<i class="ets_svg file-text-o"><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1596 380q28 28 48 76t20 88v1152q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1600q0-40 28-68t68-28h896q40 0 88 20t76 48zm-444-244v376h376q-10-29-22-41l-313-313q-12-12-41-22zm384 1528v-1024h-416q-40 0-68-28t-28-68v-416h-768v1536h1280zm-1024-864q0-14 9-23t23-9h704q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64zm736 224q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704zm0 256q14 0 23 9t9 23v64q0 14-9 23t-23 9h-704q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h704z"/></svg>
		</i>
        <span>{l s='My blog info' mod='ybc_blog'}</span>
	</a>
</li>
<!-- END : MODULE ybc_blog -->