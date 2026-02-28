{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<style>

#clockdiv {
	font-family: sans-serif;
	color: #fff;
	display: inline-block;
	text-align: center;
	font-size: {$banner['text_size']|escape:'htmlall':'UTF-8'}px;
}

#clockdiv > div {
	display: inline-block;
}

#clockdiv .timer_part {
	padding: 4px;
	border-radius: 4px;
	background:  {$banner['timer_background']|escape:'htmlall':'UTF-8'};
	color: {$banner['timer_text_color']|escape:'htmlall':'UTF-8'};
	min-width: 45px;
}

.smalltext {
	font-size: 10px;
	height: 10px;
	line-height: 10px;
}

</style>

<div id="clockdiv">
		<span class="days timer_part days_part"></span>
        <span class="timer_part days_part plural">&nbsp;{l s='days' mod='topbanner'}</span>
        <span class="timer_part days_part singular">&nbsp;{l s='day' mod='topbanner'}</span>
		<span class="hours timer_part"></span><span>&nbsp;:</span>
		<span class="minutes timer_part"></span><span>&nbsp;:</span>
		<span class="seconds timer_part"></span>
</div>

<script>

	window.deadline = new Date(parseFloat({$deadline|escape:'htmlall':'UTF-8'}) * 1000);

    {if $psversion == '17'}
    document.addEventListener('DOMContentLoaded', function() {
    {/if}
        window.initializeClock('clockdiv', window.deadline);
    {if $psversion == '17'}
    }, false);
    {/if}
</script>
