{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{block name='social_sharing'}
  {if $social_share_links}
    <div class="social-sharing">
      <div class="share_text">
        <span>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><circle cx="6" cy="12" r="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/><circle cx="16.86" cy="5.74" r="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/><circle cx="16.86" cy="19.06" r="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/><line x1="9.25" y1="14.75" x2="12.07" y2="15.99" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/><line x1="9.49" y1="9.53" x2="11.82" y2="8.47" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg>
          {l s='Share' d='Shop.Theme.Actions'}
        </span>
      </div>
      <ul>
        {foreach from=$social_share_links item='social_share_link'}
          <li class="{$social_share_link.class} icon-gray tip_inside">
            <a href="{$social_share_link.url}" class="text-hide" title="" target="_blank">
              {$social_share_link.label}
              <span class="tip">{$social_share_link.label}</span>
            </a>
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
{/block}
