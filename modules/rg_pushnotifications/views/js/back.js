/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

var rg_pushnotifications_players_page = 0;

function calculateClients() {
  $('div#divpreCalculation').remove();
  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: $('#rg_pushnotifications_campaign_form').serialize() + '&controller=AdminRgPuNoCampaigns&action=preCalculation&ajax=1&token=' + token,
    dataType: 'json',
    cache: false,
    async: false,
    success: function(jsonData) {
      if (jsonData) {
        $('.panel-footer').before('<div id="divpreCalculation" class="alert alert-' + jsonData.type + ' col-lg-offset-3">' + jsonData.message + '</div>');
      }
    }
  });
}

function refreshSubscribers() {
  $('#rgpushnotifications_spinner').removeClass('hidden');
  refreshSubscribersAjaxCall();
}

function refreshSubscribersAjaxCall() {
  $.ajax({
    url: 'index.php',
    type: 'POST',
    data: 'controller=AdminRgPuNoSubscribers&action=refreshSubscribers&ajax=1&token=' + suscribers_token + '&page=' + rg_pushnotifications_players_page,
    dataType: 'json',
    cache: false,
    success: function(json) {
      rg_pushnotifications_players_page++;

      if (json && json.continue) {
        $('#rgpushnotifications_processed').text(json.percent + '%');
        refreshSubscribersAjaxCall();
      } else {
        window.location.reload();
      }
    }
  });
}

$(document).ready(function() {
  var icon = $('input[name="icon"]:checked').val();

  $('#icon_image_file').parents('.form-group').parents('.form-group').hide();
  $('#icon_url_file').parents('.form-group').hide();

  if (icon == 'file') {
    $('#icon_image_file').parents('.form-group').parents('.form-group').show();
  } else if (icon == 'url') {
    $('#icon_url_file').parents('.form-group').show();
  }

  $('input[name="icon"]').change(function () {
    if ($(this).is(':checked')) {
      $('#icon_image_file').parents('.form-group').parents('.form-group').hide();
      $('#icon_url_file').parents('.form-group').hide();

      var icon = $(this).val();

      if (icon == 'file') {
        $('#icon_image_file').parents('.form-group').parents('.form-group').show();
      } else if (icon == 'url') {
        $('#icon_url_file').parents('.form-group').show();
      }
    }
  });

  var image = $('input[name="image"]:checked').val();

  $('#image_image_file').parents('.form-group').parents('.form-group').hide();
  $('#image_url_file').parents('.form-group').hide();

  if (image == 'file') {
    $('#image_image_file').parents('.form-group').parents('.form-group').show();
  } else if (image == 'url') {
    $('#image_url_file').parents('.form-group').show();
  }

  $('input[name="image"]').change(function () {
    if ($(this).is(':checked')) {
      $('#image_image_file').parents('.form-group').parents('.form-group').hide();
      $('#image_url_file').parents('.form-group').hide();

      var image = $(this).val();

      if (image == 'file') {
        $('#image_image_file').parents('.form-group').parents('.form-group').show();
      } else if (image == 'url') {
        $('#image_url_file').parents('.form-group').show();
      }
    }
  });

  if ($('input#clients').length) {
    $('input#clients').flexdatalist({
      url: 'index.php',
      valueProperty: 'id_customer',
      textProperty: 'email',
      searchIn: ['firstname', 'lastname', 'email'],
      selectionRequired: true,
      searchContain: true,
      searchDisabled: true,
      multiple: true,
      noResultsText: no_clients_results_text + ' "{keyword}"',
      params: {
        'controller': 'AdminRgPuNoCampaigns',
        'token': token,
        'action': 'filterClients',
        'ajax': true
      }
    });
  }

  if ($('input#bought_product').length) {
    $('input#bought_product').flexdatalist({
      url: 'index.php',
      valueProperty: 'id_product',
      textProperty: 'name',
      searchIn: ["name"],
      selectionRequired: true,
      searchContain: true,
      searchDisabled: true,
      multiple: true,
      noResultsText: no_products_results_text + ' "{keyword}"',
      params: {
        'controller': 'AdminRgPuNoCampaigns',
        'token': token,
        'action': 'filterProducts',
        'ajax': true
      }
    });
  }

  if ($('input#bought_category').length) {
    $('input#bought_category').flexdatalist({
      url: 'index.php',
      valueProperty: 'id_category',
      textProperty: 'name',
      searchIn: ['name'],
      selectionRequired: true,
      searchContain: true,
      searchDisabled: true,
      multiple: true,
      noResultsText: no_categories_results_text + ' "{keyword}"',
      params: {
        'controller': 'AdminRgPuNoCampaigns',
        'token': token,
        'action': 'filterCategories',
        'ajax': true
      }
    });
  }

  if ($('input#bought_manufacturer').length) {
    $('input#bought_manufacturer').flexdatalist({
      url: 'index.php',
      valueProperty: 'id_manufacturer',
      textProperty: 'name',
      searchIn: ['name'],
      selectionRequired: true,
      searchContain: true,
      searchDisabled: true,
      multiple: true,
      noResultsText: no_manufacturers_results_text + ' "{keyword}"',
      params: {
        'controller': 'AdminRgPuNoCampaigns',
        'token': token,
        'action': 'filterManufacturers',
        'ajax': true
      }
    });
  }

  /*
   * Refresh subscribers
   */
  if (typeof refresh_loading_text !== 'undefined') {
    $('body').append('<div id="rgpushnotifications_spinner" class="hidden"><p>' + refresh_loading_text + '<br>' + refresh_processed_text + ' <span id="rgpushnotifications_processed">0%</span></p></div>');
  }
});
