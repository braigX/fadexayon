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

<div class="contact-rich">
  <h4 class="page-header">{l s='Store information' d='Shop.Theme.Global'}</h4>
  <div class="block">
    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><path d="M19,9.09c0,3.87-3.13,13-7,13S5,13,5,9.09a7,7,0,0,1,14,0Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><circle cx="12" cy="9.09" r="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/></g></svg></div>
    <div class="data">{$contact_infos.address.formatted nofilter}</div>
  </div>
  {if $contact_infos.phone}
    <div class="block">
      <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><rect x="5" y="2.09" width="14" height="20" rx="2.07" ry="2.07" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><line x1="17.28" y1="16.29" x2="11.78" y2="16.29" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><line x1="5" y1="16.09" x2="7.89" y2="16.09" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><line x1="11" y1="19.09" x2="13" y2="19.09" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/></g></svg></div>
      <div class="data">
        {l s='Call us:' d='Shop.Theme.Global'}<br/>
        <a href="tel:{$contact_infos.phone}">{$contact_infos.phone}</a>
       </div>
    </div>
  {/if}
  {if $contact_infos.fax}
    <div class="block">
      <div class="icon"><i class="material-icons">&#xE0DF;</i></div>
      <div class="data">
        {l s='Fax:' d='Shop.Theme.Global'}<br/>
        {$contact_infos.fax}
      </div>
    </div>
  {/if}
  {if $contact_infos.email}
    <div class="block">
      <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>4</title><g id="Layer_4" data-name="Layer 4"><rect x="2" y="4" width="20" height="16" rx="3" ry="3" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2.29999995231628px"/><path d="M2.06,6.94,10,14s2.31,1.73,4,0l7.75-7.75" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:2px"/></g></svg></div>
      <div class="data email">
        {l s='Email us:' d='Shop.Theme.Global'}<br/>
       </div>
       <a href="mailto:{$contact_infos.email}">{$contact_infos.email}</a>
    </div>
  {/if}
</div>
