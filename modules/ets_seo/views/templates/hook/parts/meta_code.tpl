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

<div class="ets_seo_meta_code">
    {foreach $list_meta_codes as $key=>$item}
    <button type="button" class="btn btn-default btn-add-met-code js-ets-seo-add-meta-code" data-code="{$item.code|escape:'html':'UTF-8'}">
        <i class="fa fa-plus-circle"></i> {$item.title|escape:'html':'UTF-8'}
    </button>
    {/foreach}
</div>