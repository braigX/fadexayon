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
{if isset($content)}
    <div class="block-content">
        {$content nofilter}

    </div>
    {if isset($id_product_comment) && $id_product_comment|intval > 0 && isset($grade) && $grade|intval > 0 && $type==EtsRVActivity::ETS_RV_TYPE_REVIEW && $action==EtsRVActivity::ETS_RV_ACTION_REVIEW}
        {include file="./rating.tpl"}
    {/if}
{/if}