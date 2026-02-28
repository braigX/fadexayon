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

<tr>
    <td style="border: none;background:none;">
        <div>
            <a href="{if isset($productLink)}{$productLink nofilter}{else}#{/if}"{if !empty($productName)} title="{$productName|escape:'html':'UTF-8'}"{/if}>
                <table style="border: none;background:#ffffff;">
                    <tr style="border: none;background:#ffffff;">
                        {if !empty($productCover)}
                            <td style="padding: 10px;border: none;background:#ffffff;">
                                <img src="{$productCover|escape:'html':'UTF-8'}" alt="{if isset($productName)}{$productName|escape:'html':'UTF-8'}{/if}" style="width: 80px;" />
                            </td>
                        {/if}
                        <td style="padding: 10px;border: none;background:#ffffff;">
                            <span>{if isset($productName)}{$productName|escape:'html':'UTF-8'}{/if}</span><br>
                            <span>{if !empty($image_dir)}<img src="{$image_dir nofilter}" />{/if}</span>
                        </td>
                    </tr>
                </table>
            </a>
        </div>
    </td>
</tr>