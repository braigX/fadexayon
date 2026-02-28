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

<div class="form-group abancart-image">
    <div class="col-lg-12" {if isset($upf_name)}id="{$upf_name|escape:'html':'UTF-8'}-images-thumbnails"{/if}>
        <div>
            {if isset($upf_link)}<img src="{$upf_link|escape:'quotes':'UTF-8'}" width="220">{/if}
            <p>
                <a class="btn btn-default" href="#">
                    <i class="icon-trash"></i> {l s='Delete' mod='ets_abandonedcart'}
                </a>
            </p>
        </div>
    </div>
</div>