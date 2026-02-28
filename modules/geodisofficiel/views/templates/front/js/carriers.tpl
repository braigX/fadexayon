{*
* 2018 GEODIS
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author    GEODIS <contact@geodis.com>
*  @copyright 2018 GEODIS
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

<div class="geodisPopinHeader js-geodisPopinHeader">
    <span class="js-popin_title geodisPopinHeader__title" data-var="popinTitle"></span>
    <br />
    <span class="js-popin_subtitle geodisPopinHeader__subtitle" data-var="popinSubtitle"></span>
    <div class="geodisPopinHeader__price js-popin-price"></div>
    <div class="geodisPopinHeader__close" data-action="close"></div>
</div>

<div class="geodisPopinContent">
    <ul data-render="renderCarriers" class="geodisCarrierlist" ></ul>
</div>

<div class="geodisPopinFooter js-geodisPopinFooter">
    <button class="geodisPopinFooter__submit js-submit" data-set-class="setSubmitClass" data-action="submit" data-validate="canSubmit"><span class="geodisPopinFooter__submit-button-text">{__ s='front.popin.submit'}</span></button>
</div>
