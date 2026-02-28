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

{extends file="helpers/options/options.tpl"}
{block name="label"}
    {if $key == 'ETS_SEO_GET_GOOGLE_AUTH_CODE'}
        <div class="col-lg-3"></div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="input"}
    {if $key == 'ETS_SEO_GET_GOOGLE_AUTH_CODE'}
        <div class="col-lg-9">
            <button type="button" class="btn btn-default js-btn-get-google-code">{$field['title']|escape:'html':'UTF-8'}</button>
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}