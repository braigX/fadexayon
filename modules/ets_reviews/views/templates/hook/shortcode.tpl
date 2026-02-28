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
{if $shortcode|trim=='tracking' && $tracking|trim !== ''}
    <img src="{$tracking nofilter}" width="1" height="1" border="0" style="height:1px!important;width:1px!important;border-width:0!important;margin-top:0!important;margin-bottom:0!important;margin-right:0!important;margin-left:0!important;padding-top:0!important;padding-bottom:0!important;padding-right:0!important;padding-left:0!important"/>
{elseif $shortcode|trim=='logo_img' && $shop_logo|trim !== ''}
    <img height="auto" src="{$shop_logo nofilter}" style="line-height: 100%; -ms-interpolation-mode: bicubic; border: 0; display: inline-block; outline: none; text-decoration: none; height: auto; width: auto; font-size: 13px;margin: 0 auto;" border="0">
{/if}
