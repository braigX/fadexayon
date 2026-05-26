/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * @author    Innova Deluxe SL
 * @copyright 2017 Innova Deluxe SL
 * @license   INNOVADELUXE
 */

var idxrCustomProductsById = {};
var idxrCustomProductsRequestKey = null;

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

  prestashop.on("updateProductList", function () {
    irxrcustomproduct_updateproductlist();
  });

  irxrcustomproduct_updateproductlist();

  $("#blockcart-content .row").each(function () {
    var idProduct = $(this).find(".remove-from-cart").attr("data-id-product");
    var link = $(this).find(".pb-1 a");

    if (!idProduct) {
      return;
    }

    $.post(url_ajax, {
      action: "getParentLink",
      product_id: idProduct.split("-")[0],
    }).done(function (data) {
      if (data) {
        link.attr("href", data);
      }
    });
  });

  $("#cart-summary-product-list .media").each(function () {
    // Legacy placeholder kept to avoid changing historical behavior.
  });
});

function idxrCollectListingProductIds() {
  var productIds = [];

  $(".js-product-miniature, .elementor-product-miniature").each(function () {
    var productId = parseInt($(this).attr("data-id-product"), 10);

    if (productId && $.inArray(productId, productIds) === -1) {
      productIds.push(productId);
    }
  });

  return productIds;
}

function idxrApplyCustomProductListingState() {
  $(".js-product-miniature").each(function () {
    var idProduct = parseInt($(this).attr("data-id-product"), 10);
    var button = $(this).find(".add-to-cart-or-refresh");
    var price = $(this).find(".product-price-and-shipping .price");

    if (!idxrCustomProductsById[idProduct]) {
      return;
    }

    button.remove();

    if (price.length && !price.text().includes("/m²")) {
      price.html(price.text().trim() + " /m²");
    }
  });

  $(".elementor-product-miniature").each(function () {
    var idProduct = parseInt($(this).attr("data-id-product"), 10);
    var price = $(this).find(".elementor-price");
    var customProduct = idxrCustomProductsById[idProduct];

    if (!customProduct) {
      return;
    }

    if (customProduct.min_price) {
      price.html(min_price_text + " " + customProduct.min_price);
    }
  });
}

function irxrcustomproduct_updateproductlist() {
  var productIds = idxrCollectListingProductIds();
  var requestKey = productIds.slice().sort(function (a, b) {
    return a - b;
  }).join(",");

  if (!productIds.length) {
    return;
  }

  if (requestKey === idxrCustomProductsRequestKey) {
    idxrApplyCustomProductListingState();
    return;
  }

  idxrCustomProductsRequestKey = requestKey;

  $.post(url_ajax, {
    action: "getListingCustomProducts",
    product_ids: productIds,
  }).done(function (data) {
    if (!data) {
      idxrCustomProductsById = {};
      return;
    }

    if (typeof data === "string") {
      try {
        idxrCustomProductsById = $.parseJSON(data);
      } catch (e) {
        idxrCustomProductsById = {};
      }
    } else {
      idxrCustomProductsById = data;
    }

    idxrApplyCustomProductListingState();
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
