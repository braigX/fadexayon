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
<div class="container">
  <div class="row_zero">
    {block name='hook_footer_before'}
      {hook h='displayFooterBefore'}
    {/block}
  </div>
</div>
<div class="footer-container">
  <div class="container">
    {if isset($roythemes.footer_lay) && $roythemes.footer_lay !== "4"}
    <div class="row">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
    </div>
    {/if}
  {*  <div class="row social ">
      {widget name='ps_socialfollow'}
      {if isset($roythemes.footer_lay) && $roythemes.footer_lay !== "4"}
        {widget name='ps_emailsubscription' hook='displayFooterBefore'}
      {/if}
    </div>*}
    <div class="row">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
    </div>
  </div>
</div>
<div class="product_add_mini">
    {*<i class="material-icons rtl-no-flip">&#xE876;</i>*}
    <i aria-hidden="true" class="fas fa-check rtl-no-flip"></i>
    {l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}
    <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div id="is_media"></div>
{literal}
 <script> window.addEventListener('load', function() { var widgetElement = document.createElement('charla-widget'); widgetElement.setAttribute("p", "58d653bb-c2c5-406b-8c76-83ed98746339"); document.body.appendChild(widgetElement) ; var widgetCode = document.createElement('script'); widgetCode.src = 'https://app.getcharla.com/widget/widget.js'; document.body.appendChild(widgetCode); }) </script> 
{/literal}