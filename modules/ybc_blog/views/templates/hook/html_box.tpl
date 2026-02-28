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
{if isset($html_content_box) && $html_content_box}
    <div class="page_blog block ybc_block_author {$blog_config.YBC_BLOG_RTL_CLASS|escape:'html':'UTF-8'} {if isset($blog_page) && $blog_page}page_{$blog_page|escape:'html':'UTF-8'}{else}page_blog{/if}">
        <h4 class="title_blog title_block">{$html_title_box|escape:'html':'UTF-8'}</h4>
        <div class="block_content">
            {$html_content_box nofilter}
        </div>
    </div>
{/if}