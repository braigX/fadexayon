{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* @author    Presta.Site
* @copyright 2018 Presta.Site
* @license   LICENSE.txt
*}

{extends file='page.tpl'}

{block name='head_seo_title'}{$meta_title|escape:'html':'UTF-8'}{/block}
{block name='head_seo_description'}{$pswp_post.main_content|strip_tags|trim|escape:'html':'UTF-8'|truncate:160}{/block}
{block name='head_seo_keywords'}{$meta_keywords|escape:'html':'UTF-8'}{/block}

{block name='page_content_container'}
    <div id="content" class="page-content card card-block page-cms">
        {include 'module:prestawp/views/templates/front/post.tpl'}
    </div>
{/block}