/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2017 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

$(document).ready(function () {
  if (prestashop.page.page_name == "order-confirmation") {
    idxropc_resume();
  }
  $(document).on("idxropc_resumeload", function () {
    idxropc_resume();
  });
  $(document).on("show.bs.modal", ".quickview", function () {
    var id = $(this).attr("id").replace("quickview-modal-", "");
    $.post(url_ajax, {
      action: "isCustomized",
      product_id: id.split("-")[0],
    }).done(function (data) {
      if (data) {
        $(".add-to-cart").remove();
        $(".product-add-to-cart").remove();
        $(".product-prices").remove();
        $(".modal-footer").remove();
        $(".product-actions").append(
          '<a href="' +
            data +
            '"><button class="btn btn-primary add-to-cart"><i class="material-icons">mode_edit</i>' +
            add_text +
            "</button></a>"
        );
      }
    });
  });

  //Change add to cart button for configure
  prestashop.on("updateProductList", function () {
    irxrcustomproduct_updateproductlist();
  });
  irxrcustomproduct_updateproductlist();

  //Change link in cart modal
  $("#blockcart-content .row").each(function (i, obj) {
    var id_product = $(this).find(".remove-from-cart").attr("data-id-product");
    var link = $(this).find(".pb-1 a");
    $.post(url_ajax, {
      action: "getParentLink",
      product_id: id_product.split("-")[0],
    }).done(function (data) {
      if (data) {
        link.attr("href", data);
      }
    });
  });

  //Change link in order page
  $("#cart-summary-product-list .media").each(function (i, obj) {
    //        var id_product = $(this).find('.remove-from-cart').attr('data-id-product');
    //        var link = $(this).find('.pb-1 a');
    //        $.post(url_ajax, {action: "getParentLink", product_id: id_product.split('-')[0]})
    //        .done(function (data) {
    //            if (data) {
    //                link.attr("href",data);
    //            }
    //        });
  });
});

function irxrcustomproduct_updateproductlist() {
  $(".js-product-miniature").each(function (i, obj) {
    var id_product = $(this).attr("data-id-product");
    var button = $(this).find(".add-to-cart-or-refresh");
    var price = $(this).find(".product-price-and-shipping .price");

    $.each(custom_products, function (index, value) {
      var products = value["products"].split(",");
      if (jQuery.inArray(id_product, products) !== -1) {
        button.remove();

        // Vérifier si le prix ne contient pas déjà "/m²" pour éviter les doublons
        if (!price.text().includes("/m²")) {
          price.html(price.text().trim() + " /m²");
        }
      }
    });
  });

  //theme Zonetheme compatibility
  $(".elementor-product-miniature").each(function (i, obj) {
    var id_product = $(this).attr("data-id-product");
    var price = $(this).find(".elementor-price");
    $.each(custom_products, function (index, value) {
      var products = value["products"].split(",");
      if (jQuery.inArray(id_product, products) !== -1) {
        if (value["min_price"]) {
          price.html(min_price_text + " " + value["min_price"]);
        }
      }
    });
  });
}

function idxropc_resume() {
  var id_cart = false;
  if (prestashop.page.page_name == "order-confirmation") {
    id_cart = getUrlParameter("id_cart");
  }
  $.post(url_ajax, {
    action: "getCustomizedData",
    clean: true,
    id_cart: id_cart,
  }).done(function (data) {
    if (data) {
      customized_data = $.parseJSON(data);
      var products_edited = 0;
      $(".cell-product").each(function () {
        var id_product = $(this)
          .find(".cart_quantity_change")
          .attr("data-idproduct");
        var title = $(this).find(".opc-name-product");
        customized_data.forEach(function (customized) {
          if (customized.id_product == id_product) {
            title.after(
              '<div id="custom_info_' +
                id_product +
                '" class="js_hide">' +
                customized.customization +
                "</div>"
            );
            title.html(
              '<a id="custom_info_button_' +
                id_product +
                '" href="#custom_info_' +
                id_product +
                '"><button class="btn-config">' +
                show_conf_text +
                "</button></a>"
            );
            $("#custom_info_button_" + id_product).fancybox({
              hideOnContentClick: true,
            });
            products_edited++;
          }
        });
      });
      //Order confirmation page
      $("#order-items .order-line").each(function () {
        var image_url = $(this).find(".image img").attr("src");
        var title = $(this).find(".details");
        customized_data.forEach(function (customized) {
          if (customized.product_image_url == image_url) {
            title.append(customized.customization.replace("\\", ""));
            products_edited++;
          }
        });
      });
      //If there are no customized products (breakdown) but must show the info by conf
      if (
        products_edited == 0 &&
        idxcp_show_breakdowninfo &&
        customized_data.length > 0
      ) {
        customized_data.forEach(function (customized) {
          $("#order-details").append(
            customized.customization.replace("\\", "")
          );
        });
      }
    }
  });
}

function getUrlParameter(sParam) {
  var sPageURL = window.location.search.substring(1),
    sURLVariables = sPageURL.split("&"),
    sParameterName,
    i;

  for (i = 0; i < sURLVariables.length; i++) {
    sParameterName = sURLVariables[i].split("=");

    if (sParameterName[0] === sParam) {
      return typeof sParameterName[1] === undefined
        ? true
        : decodeURIComponent(sParameterName[1]);
    }
  }
  return false;
}
