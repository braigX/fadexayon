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

{if isset($position) && $position == 'product' || $nb_comments != 0 && $grade|floatval > 0}
  <div class="comments-note" title="{l s='Read all reviews' mod='ets_reviews'}">
    {if isset($position) && $position=='product'}
        <span>{l s='Rating' mod='ets_reviews'}: </span>
    {/if}
    <div class="ets_rv_grade_stars{if !empty($ETS_RV_DESIGN_COLOR1)} color1{/if}" data-rate-full="★★★★★" data-rate-empty="☆☆☆☆☆" data-grade="{$grade|floatval|round:1}">
        <div class="ets-rv-comments-nb">({$nb_comments|intval})</div>
    </div>
  </div>
{/if}
