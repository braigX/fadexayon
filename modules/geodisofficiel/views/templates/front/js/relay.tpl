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
    <ul>
        <li class="geodisPrestation geodisPrestation--selected js-prestation" data-set-class="setCarrierClass" data-values="0">
            <div class="geodisPrestationContent">
                <div class="geodisPrestationContent__container geodisPrestationContent__container--relay">
                    <p class="geodisPrestationContent__description" data-var="description"></p>
                    <div class="geodisRelayPicker">
                        <div class="geodisRelayPicker__column">
                            <div class="geodisRelayPicker__form">
                                <input class="geodisRelayAddress__input js-geodis-relay-address" data-set-value="getAddress" data-values="0" placeholder="{__ s='front.popin.address.placeholder'}" />
                                <button class="geodisRelayAddress__action" data-values="0" data-action="updateRelayAddress">{__ s='front.popin.address.button'}</button>
                            </div>
                            <div class="geodisRelayPicker__switch">
                                <div class="geodisSwitch geodisSwitch--list geodisSwitch--active js-switch-list" data-action="showList" data-values="0">{__ s='front.popin.action.displayList'}</div>
                                <div class="geodisSwitch geodisSwitch--map js-switch-map" data-action="showMap" data-values="0">{__ s='front.popin.action.displayMap'}</div>
                            </div>
                            <ul class="geodisRelayPicker__list geodisRelayPicker__list--active js-point-list" data-values="0"></ul>
                        </div>
                        <div class="geodisRelayPicker__column">
                            <div class="geodisRelayPicker__map js-geodis-relay-map" data-values="0"></div>
                        </div>
                    </div>
                    <p class="geodisPrestationContent__subTitle">{__ s='front.popin.contactInformations'}</p>
                    <p class="geodisPrestationContent__description">{__ s='front.popin.contactDescription'}</p>
                    <div class="geodisInfo geodisInfo--relay">
                        <div class="js-input-container geodisInfo__inputContainer geodisInfo__inputContainer--telephone" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.telephone.error'|escape:'html'}">
                            <input class="geodisInfo__input js-telephone geodisInput__input" type="text" placeholder="{__ s='front.popin.telephone.placeholder'|escape:'html'}" data-set-value="getTelephone" data-validate="validateTelephone" data-process="processTelephone" data-change="switchRequiredEntry" data-values-var="id"/>
                        </div>
                        <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--mobilephone" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.mobile.error'|escape:'html'}">
                            <input class="geodisInfo__input js-mobilephone geodisInput__input" type="text" placeholder="{__ s='front.popin.mobile.placeholder'|escape:'html'}" data-set-value="getMobilephone" data-validate="validateMobilephone" data-process="processMobilephone" data-change="switchRequiredEntry" data-values-var="id"/>
                        </div>
                        <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--email" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.email.error'|escape:'html'}">
                            <input class="geodisInfo__input geodisInfo__input--email js-email contact_infos-email geodisInput__input" type="text" placeholder="{__ s='front.popin.email.placeholder'|escape:'html'}" data-set-value="getEmail" data-validate="validateEmail" data-process="processEmail" data-change="switchRequiredEntry"/>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="geodisPopinFooter js-geodisPopinFooter">
    <button class="geodisPopinFooter__submit js-submit" data-set-class="setSubmitClass" data-action="submit" data-validate="canSubmit"><span class="geodisPopinFooter__submit-button-text">{__ s='front.popin.submit'}</span></button>
</div>
