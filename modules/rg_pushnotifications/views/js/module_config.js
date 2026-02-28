/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

function refreshBellColors() {
  var theme = $('input[name=RGPUNO_BELL_THEME]:checked').val();
  $('circle.background').css('fill', (theme == 'custom' ? $('input[name=RGPUNO_BELL_BACK]').prop('style').backgroundColor : ''));
  $('ellipse.stroke').css('stroke', (theme == 'custom' ? $('input[name=RGPUNO_BELL_FORE]').prop('style').backgroundColor : ''));
  $('path.foreground').css('fill', (theme == 'custom' ? $('input[name=RGPUNO_BELL_FORE]').prop('style').backgroundColor : ''));

  $('#subscribe-button')
    .css('color', ($('input[name=RGPUNO_BELL_DIAG_FORE]').val() ? $('input[name=RGPUNO_BELL_DIAG_FORE]').prop('style').backgroundColor : ''))
    .css('background', ($('input[name=RGPUNO_BELL_DIAG_BACK]').val() ? $('input[name=RGPUNO_BELL_DIAG_BACK]').prop('style').backgroundColor : ''));
}

function uploadNotificationIcon() {
  $.ajaxFileUpload({
    url: rg_pushnotifications._path + "ajaxs/upload_icon.php?token=" + rg_pushnotifications.token,
    secureuri: false,
    fileElementId: 'notification_icon_input',
    dataType: 'xml',
    success: function(data) {
      data = data.getElementsByTagName('return')[0];
      var message = data.getAttribute("message");
      alert(message);
      if (data.getAttribute("result") == "success") {
        window.location.reload();
      } else {
        $('#attachement_filename').val('');
      }
    }
  });
}

$(document).ready(function() {
  $('.products-marketing-list').slick({
    arrows: false,
    dots: true,
    autoplay: true,
    infinite: true,
    speed: 800,
    slidesToShow: 4,
    slidesToScroll: 4,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      }, {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      }, {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });

  $('input[type="radio"]').on('change', function() {
    var name = $(this).attr('name'),
      stat = Boolean(parseInt($('input[name="' + name + '"]:checked').val()));

    // Cart Reminder: Create a discount coupon
    if (name == 'RGPUNO_CART_COUPON' && !parseInt($('input[name="RGPUNO_CART_REMINDER"]:checked').val())) {
      return;
    }

    $('.child_of_' + name).toggle(stat);

    // Cart Reminder: Activate
    if (name == 'RGPUNO_CART_REMINDER' && stat === true) {
      $('input[name="RGPUNO_CART_COUPON"]').change();
    }
  }).change();

  $('#RGPUNO_CART_COUPON_DISCOUNT_TYPE').on('change', function() {
    if ($(this).val() == 'amount') {
      $('#RGPUNO_CART_COUPON_DISCOUNT_AMOUNT').next().text(rg_pushnotifications.currency_sign);
    } else {
      $('#RGPUNO_CART_COUPON_DISCOUNT_AMOUNT').next().text('%');
    }
  }).change();

  /*
   * Notification Bell
   */
  $('input[name="RGPUNO_BELL_SIZE"]').change(function () {
    if ($(this).is(':checked')) {
      var size = 'sm';

      if ($(this).val() == 'large') {
        size = 'lg';
      } else if ($(this).val() == 'medium') {
        size = 'md';
      }

      $('#onesignal-bell-launcher')
        .removeClass('onesignal-bell-launcher-lg')
        .removeClass('onesignal-bell-launcher-md')
        .removeClass('onesignal-bell-launcher-sm')
        .addClass('onesignal-bell-launcher-' + size);
    }
  });

  $('input[name="RGPUNO_BELL_THEME"]').change(function () {
    if ($(this).is(':checked')) {
      var theme = $(this).val();

      $('.child_of_RGPUNO_BELL_THEME').toggle(theme == 'custom');

      if (theme == 'custom') {
        theme = 'default';
      }

      $('#onesignal-bell-launcher')
        .removeClass('onesignal-bell-launcher-theme-default')
        .removeClass('onesignal-bell-launcher-theme-inverse')
        .addClass('onesignal-bell-launcher-theme-' + theme);
    }
  });
  $('input[name="RGPUNO_BELL_THEME"]').change();

  $('input[name="RGPUNO_BELL_SHOW_CREDIT"]').change(function () {
    $('.RGPUNO_BELL_SHOW_CREDIT').toggle(Boolean(parseInt($('input[name="RGPUNO_BELL_SHOW_CREDIT"]:checked').val())));
  });

  setInterval(refreshBellColors, 1000);

  /*
   * Icon ajax upload
   */
  $('#attachement_fileselectbutton').click(function() {
    $('#notification_icon_input').trigger('click');
  });

  $('#attachement_filename').click(function() {
    $('#notification_icon_input').trigger('click');
  });

  $('#notification_icon_input').change(function() {
    var name = '';

    if ($(this)[0].files !== undefined) {
      var files = $(this)[0].files;

      $.each(files, function(index, value) {
        name += value.name + ', ';
      });

      $('#attachement_filename').val(name.slice(0, -2));
    } else {
      name = $(this).val().split(/[\\/]/);
      $('#attachement_filename').val(name[name.length - 1]);
    }
  });
});
