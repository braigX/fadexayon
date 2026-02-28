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

<li class="geodisPrestation" data-set-class="setCarrierClass" data-values-var="id" data-action="selectCarrier">
    <div class="geodisPrestationHeader">
            <div class="geodisPrestationHeader__name" data-var="name"></div>
            <div class="geodisPrestationHeader__desc" data-var="desc"></div>
            <div class="js-price geodisPrestationHeader__price" data-values-var="id" data-var="price"></div>
    </div>
    <div class="geodisPrestationContent">
        <div class="geodisPrestationContent__container geodisPrestationContent__container">
            <div class="geodisRelayPicker">
                <div class="geodisRelayPicker__column">
                    <div class="geodisRelayPicker__form">
                        <input class="geodisRelayAddress__input js-geodis-relay-address" data-set-value="getAddress" data-values-var="id" placeholder="{__ s='front.popin.address.placeholder'}" />
                        <button class="geodisRelayAddress__action" data-values-var="id" data-action="updateRelayAddress">{__ s='front.popin.address.button'}</button>
                    </div>
                    <div class="geodisRelayPicker__switch">
                        <div class="geodisSwitch geodisSwitch--list geodisSwitch--active js-switch-list" data-action="showList" data-values-var="id">{__ s='front.popin.action.displayList'}</div>
                        <div class="geodisSwitch geodisSwitch--map js-switch-map" data-action="showMap" data-values-var="id">{__ s='front.popin.action.displayMap'}</div>
                    </div>
                    <ul class="geodisRelayPicker__list geodisRelayPicker__list--active js-point-list" data-values-var="id"></ul>
                </div>
                <div class="geodisRelayPicker__column">
                    <div class="geodisRelayPicker__map js-geodis-relay-map" data-values-var="id"></div>
                </div>
            </div>
        </div>
    </div>
</li>
