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

<li class="geodisPrestation js-prestation" data-set-class="setCarrierClass" data-values-var="id" data-action="selectCarrier">
    <div class="geodisPrestationHeader">
            <div class="geodisPrestationHeader__name" data-var="name"></div>
            <div class="geodisPrestationHeader__desc" data-var="desc"></div>
            <div class="js-price geodisPrestationHeader__price" data-values-var="id" data-var="price"></div>
    </div>
    <div class="geodisPrestationContent">
        <div class="geodisPrestationContent__container">
            <p class="geodisPrestationContent__description" data-var="longdesc"></p>
            <p class="geodisPrestationContent__subTitle js-option-title" data-render="renderOptionsTitle" data-values-var="id">{__ s='front.popin.services.available'}</p>
            <ul class="js-options" data-render="renderOptions" data-values-var="id">
                <li class="js-option geodisOptionRow" data-values="" data-action="selectOption">
                    <div class="optionInformation">
                        <span class="geodisOptionRow__name">{__ s='front.popin.option.none.label'}</span>
                    </div>
                </li>
            </ul>

            <p class="geodisPrestationContent__subTitle">{__ s='front.popin.contactInformations'}</p>
            <p class="geodisPrestationContent__description" data-var="appointmentTypeDesc"></p>
            <div class="geodisInfo">
                <div class="js-input-container geodisInfo__inputContainer geodisInfo__inputContainer--telephone" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.telephone.error'|escape:'html'}">
                    <input class="geodisInfo__input js-telephone geodisInput__input" type="text" placeholder="{__ s='front.popin.telephone.placeholder'|escape:'html'}" data-set-value="getTelephone" data-validate="validateTelephone" data-change="switchRequiredEntry" data-process="processTelephone" data-values-var="id"/>
                </div>
                <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--mobilephone" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.mobile.error'|escape:'html'}">
                    <input class="geodisInfo__input js-mobilephone geodisInput__input" type="text" placeholder="{__ s='front.popin.mobile.placeholder'|escape:'html'}" data-set-value="getMobilephone" data-validate="validateMobilephone" data-change="switchRequiredEntry" data-process="processMobilephone" data-values-var="id"/>
                </div>
                <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--email" data-required-text="{__ s='front.popin.requiredEntry'|escape:'html'}" data-error="{__ s='front.popin.email.error'|escape:'html'}">
                    <input class="geodisInfo__input geodisInfo__input--email js-email contact_infos-email geodisInput__input" type="text" placeholder="{__ s='front.popin.email.placeholder'|escape:'html'}" data-set-value="getEmail" data-validate="validateEmail" data-change="switchRequiredEntry" data-process="processEmail"/>
                </div>
                <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--digicode" data-error="{__ s='front.popin.digicode.error'|escape:'html'}">
                    <input class="js-digicode geodisInfo__input geodisInfo__input--digicode geodisInput__input" type="text" placeholder="{__ s='front.popin.digicode.placeholder'|escape:'html'}" data-set-value="getDigicode" data-process="processDigicode"/>
                </div>
                <div class="js-input-container geodisInput geodisInfo__inputContainer geodisInfo__inputContainer--instruction" data-error="{__ s='front.popin.instruction.error'|escape:'html'}">
                    <textarea class="js-instruction geodisInfo__input geodisInfo__input--instruction geodisInput__input" type="text" placeholder="{__ s='front.popin.instruction.placeholder'|escape:'html'}" data-set-value="getInstruction" data-process="processInstruction"></textarea>
                </div>
            </div>
        </div>
    </div>

</li>
