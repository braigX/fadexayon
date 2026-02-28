{**
 * Smart CSV Lists Front Office Feature * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 *
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
**}

<style amp-custom>
.hideMe {
    display:none;
}

/* This is for Presta 1.7 to Prevent issues when changing artibutes */
.product-additional-info .hide-default, .estimateddelivery.hide-default {
    display:none;
}
.estimateddelivery div {
    border: 1px solid #ccc;
    padding: 5px 10px;
    margin-bottom: 10px;
    clear:both;
}
p.ed_orderbefore { margin-bottom: 0;}

.estimateddelivery h4:before, .ed_orderbefore:before {
    font-family: "FontAwesome";
    font-size: 19px;
    line-height: 24px;
    font-weight: normal;
    content: "\f0d1";
    margin-right: 7px;
 *zoom: expression(this.runtimeStyle['zoom'] = '1', this.innerHTML = '&#xf0d1;');
}
/*.estimateddelivery span { font-weight:bold; }*/
.ed_lightblue div {
    background: #FCFEFF;
    border-color: #ACD8E4 !important;
}
.ed_softred div {
    background: #FFF5F5;
    border-color: #E4ACAC !important;
}
.ed_lightgreen div {
    background: #F5FFF5;
    border-color: #ADE4AC !important;
}
.ed_lightpurple div {
    background: #FAF5FF;
    border-color: #CDACE4 !important;
}
.ed_lightbrown div {
    background: #FFFDF5;
    border-color: #E4D6AC !important;
}
.ed_lightyellow div {
    background: #FFFFF5;
    border-color: #E4E1AC !important;
}
.ed_orange div {
    background: #FFF5E7;
    border-color: #E6853E !important;
}
.estimateddelivery .ed_tooltip {
    display: inline;
    position: relative;
    opacity: 1;
}
.estimateddelivery .ed_tooltip:hover:after {
    background: #333;
    background: rgba(0,0,0,.8);
    border-radius: 5px;
    bottom: 26px;
    color: #fff;
    content: attr(title);
    left: 20%;
    padding: 5px 15px;
    position: absolute;
    z-index: 98;
    width: auto;
    min-width: 100px;
}
.estimateddelivery .ed_tooltip:hover:before {
    border: solid;
    border-color: #333 transparent;
    border-width: 6px 6px 0 6px;
    bottom: 20px;
    content: "";
    left: 50%;
    position: absolute;
    z-index: 99;
}
.ed_countdown, .date_green {
    color: #44B449;
}
.ed_oos_days_add {
    display:none;
}
.ed_oos_days_add.ed_force_display {
    display:block;
}

.ed_order_list .ed_with_carrier {
    display:none;
}
.ed_header {
    font-size:larger;
    font-weight: bold;
}
.ed_item span::first-letter {
    text-transform: uppercase;
}
.ed_product_summary {
    margin-top: 1em;
}
.ed-product-block, .ed-product, .edp-attributes {
    background: transparent !important;
    border-color: unset !important;
    border: none !important;
    padding: 0px 0px !important;
}
.ed-product-block {
    margin-bottom: 1rem !important;
}
.ed-product {
    margin-top: 3px;
}
.edp-attributes {
    margin-bottom: 0 !important;
    padding: 3px 15px !important;
}
.attr-group-name {

}
.attr-name {
    text-decoration: underline;
    color: #808080;
}
#order-detail .ed_lightblue div {
    background: none;
    border: none !important;
}
.modal-body .estimateddelivery div {
    margin-bottom: 0;
}
.modal-body .estimateddelivery .ed_item p {
    margin-bottom: 0;
}
.modal-body .estimateddelivery .ed_item p span {
    margin-bottom: 2px !important;
}

/* Carriers Popup */
#ed_popup {
    text-align: center;
    width: 100%;
}
#ed_popup_content {
    display: none;
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    box-shadow: 0 0 23px rgb(0 0 0 / 75%);
    padding: 25px 40px;
    z-index: 10;
    width: 70%;

}

#ed_popup_content .carrier_price {
    white-space: nowrap;
}
#ed_popup_content th {
    white-space: nowrap;
}

#ed_popup_content .ed_close_popup {
    position: absolute;
    top: -25px;
    right: 0;
    width: 25px;
    height: 25px;
    line-height: 25px;
    text-align: center;
    background: #000;
    color: #fff;
    cursor: pointer;
}

.ed_close_popup span {
    transform: rotate(-45deg);
}

.estimateddelivery h4:before, .ed_orderbefore:before {
    display:none;
}

p.ed_orderbefore {
    margin-top: 5px;
}
</style>