/**
 * Quantitydiscounttable
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 *  @category  FMM Modules
 *  @package   Quantitydiscounttable
 *  @author    FME Modules
 *  @copyright 2023 FME Modules All right reserved
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$(document).ready(function () {
  if (qdt_ps_version) {
    prestashop.on('updatedProduct', function () {
      if (qdt_show == 0 || qdt_show == '0') {
        $("div#quantityDiscount").prev('h3').addClass("qdt-display-none");
        $("div#quantityDiscount").addClass("qdt-display-none");
        $(".product-discounts").addClass("qdt-display-none");
        $("#quantity-discount-table .product-discounts").addClass("qdt-display-none");
      }
    });
  }

  if (qdt_show == 0 || qdt_show == '0') {
    $("div#quantityDiscount").prev('h3').addClass("qdt-display-none");
    $("div#quantityDiscount").addClass("qdt-display-none");
    $(".product-discounts").addClass("qdt-display-none");
    $("#quantity-discount-table .product-discounts").addClass("qdt-display-none");
  } else {
    if (qdt_pos != 2) {
      $("div#quantityDiscount").prev('h3').addClass("qdt-display-none");
      $("div#quantityDiscount").addClass("qdt-display-none");
    }
    $(".product-discounts").addClass("qdt-display-none");
    $("#quantity-discount-table .product-discounts").addClass("qdt-displayed");
    $("#quantity-disount-table #quantityDiscount").addClass("qdt-displayed");
    if (qdt_ps_version) {
      prestashop.on('updatedProduct', function () {
        if (qdt_show == 1 || qdt_show == '1') {
          $('.product-discounts').css('display', 'none');
          $("#quantity-discount-table .product-discounts").addClass("qdt-displayed");
          $("#quantity-disount-table #quantityDiscount").addClass("qdt-displayed");
        }
      });
    }
  }

  if (qdt_home == 1) {
    $("#quantity-discount-table-home .product-discounts").addClass("qdt-displayed");
  }
});

function qdtFunctionTogggle(val) {
  var x = document.getElementById("qdt-toggle-" + val);
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}