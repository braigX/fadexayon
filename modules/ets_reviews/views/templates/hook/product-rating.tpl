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

{if !isset($minValue) || !$minValue}{assign var="minValue" value="1"}{/if}
{if !isset($maxValue) || !$maxValue}{assign var="maxValue" value="5"}{/if}
{assign var="ratingValue" value=min(max($minValue, $average_grade), $maxValue)}
{if !isset($starWidth) || !$starWidth}{assign var="starWidth" value=20}{/if}
<div class="ets-rv-star-content ets-rv-star-empty clearfix">
    {for $ik=$minValue to $maxValue}
        {if $ik <= $average_grade|floor}
			<div class="star" style="visibility: hidden"></div>
        {elseif $ik > $average_grade|ceil}
			<div class="star"></div>
        {else}
            {assign var="fullWidth" value=$starWidth*($average_grade - $ik + 1)}
            {assign var="emptyWidth" value=($starWidth - $fullWidth)}
			<div class="star" style="visibility: hidden; width: {$fullWidth|floatval}px;"></div>
            {if $starWidth < 20}
            <div class="star" style="width: {$emptyWidth|floatval}px;background-position:0px -{$fullWidth|floatval}px 0px; margin-left:0">
            </div>
            {/if}
        {/if}
    {/for}
</div>
<div class="ets-rv-star-content ets-rv-star-full clearfix">
    {for $ik=$minValue to $maxValue}
        {if $ik <= $average_grade|floor}
			<div class="ets-rv-star-on"></div>
        {elseif $ik <= $average_grade|ceil}
            {assign var="fullWidth" value=$starWidth*($average_grade - $ik + 1)}
			<div class="ets-rv-star-on" style="width: {$fullWidth|floatval}px;"></div>
        {/if}
    {/for}
</div>