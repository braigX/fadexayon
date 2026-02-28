{*
* 2007-2023 PrestaShop
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
*  @author    Ádalop <contact@prestashop.com>
*  @copyright 2023 Ádalop
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
"shippingDetails": {
  "@type": "OfferShippingDetails",
  "shippingRate": {
    "@type": "MonetaryAmount",
    "value": {$adp_shipping_details_shipping_rate|@json_encode nofilter},
    "currency": {$moneda|@json_encode nofilter}
  },
  "shippingDestination": {
    "@type": "DefinedRegion",
    "addressCountry": {$adp_shipping_details_address_country|@json_encode nofilter}
  },
  "deliveryTime": {
    "@type": "ShippingDeliveryTime",
    "handlingTime": {
      "@type": "QuantitativeValue",
      "minValue": {$adp_shipping_details_delivery_handling_time_min|@json_encode nofilter},
      "maxValue": {$adp_shipping_details_delivery_handling_time_max|@json_encode nofilter},
      "unitCode": "DAY"
    },
    "transitTime": {
      "@type": "QuantitativeValue",
      "minValue": {$adp_shipping_details_transit_handling_time_min|@json_encode nofilter},
      "maxValue": {$adp_shipping_details_transit_handling_time_max|@json_encode nofilter},
      "unitCode": "DAY"
    }
  }
},