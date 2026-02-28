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

<input
    type="hidden"
    class="js-geodis-carrier-selector"
    data-carrier-id="{$groupCarrierId}"
    data-json-config='{$jsonConfig}'
    data-json-values='{$jsonValues}'
    data-get-template-url="{$getTemplateUrl}"
    data-submit-url="{$submitUrl}"
    data-point-list-url="{$pointListUrl}"
    data-format-price-url="{$formatPriceUrl}"
    data-intl-utils-url="{$intlUtilsUrl}"
    data-marker-shop-url="{$markerShopUrl}"
    data-marker-selected-shop-url="{$markerSelectedShopUrl}"
    data-marker-home-url="{$markerHomeUrl}"
    data-sentence-distance-meters="{$sentenceDistanceMeters}"
    data-sentence-distance-kilometers="{$sentenceDistanceKilometers}"
    data-module="{$geodisModuleName}"
    data-map-enabled="{$geodisMapEnabled}"
/>
