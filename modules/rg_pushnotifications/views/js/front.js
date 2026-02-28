/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

var OneSignal = window.OneSignal || [];

OneSignal.push(function () {
  OneSignal.SERVICE_WORKER_PARAM = { scope: '/modules/rg_pushnotifications/' };
  OneSignal.SERVICE_WORKER_UPDATER_PATH = "OneSignalSDKUpdaterWorker.js.php";
  OneSignal.SERVICE_WORKER_PATH = "OneSignalSDKWorker.js.php";

  OneSignal.init({
    appId: rg_pushnotifications.APP_ID,
    autoRegister: false,
    path: rg_pushnotifications._path,
    persistNotification: rg_pushnotifications.RGPUNO_PERSISTENT_NOTIF,
    notifyButton: {
      enable: rg_pushnotifications.RGPUNO_BELL_SHOW,
      size: rg_pushnotifications.RGPUNO_BELL_SIZE,
      theme: rg_pushnotifications.RGPUNO_BELL_THEME,
      position: rg_pushnotifications.RGPUNO_BELL_POSITION,
      offset: {
        bottom: rg_pushnotifications.RGPUNO_BELL_OFFSET_BOTOM + 'px',
        left: rg_pushnotifications.RGPUNO_BELL_OFFSET_LEFT + 'px',
        right: rg_pushnotifications.RGPUNO_BELL_OFFSET_RIGHT + 'px'
      },
      prenotify: rg_pushnotifications.RGPUNO_BELL_PRENOTIFY,
      showCredit: rg_pushnotifications.RGPUNO_BELL_SHOW_CREDIT,
      text: {
        'tip.state.unsubscribed': rg_pushnotifications.RGPUNO_BELL_TIP_STATE_UNS,
        'tip.state.subscribed': rg_pushnotifications.RGPUNO_BELL_TIP_STATE_SUB,
        'tip.state.blocked': rg_pushnotifications.RGPUNO_BELL_TIP_STATE_BLO,
        'message.prenotify': rg_pushnotifications.RGPUNO_BELL_MSG_PRENOTIFY,
        'message.action.subscribed': rg_pushnotifications.RGPUNO_BELL_ACTION_SUBS,
        'message.action.resubscribed': rg_pushnotifications.RGPUNO_BELL_ACTION_RESUB,
        'message.action.unsubscribed': rg_pushnotifications.RGPUNO_BELL_ACTION_UNS,
        'dialog.main.title': rg_pushnotifications.RGPUNO_BELL_MAIN_TITLE,
        'dialog.main.button.subscribe': rg_pushnotifications.RGPUNO_BELL_MAIN_SUB,
        'dialog.main.button.unsubscribe': rg_pushnotifications.RGPUNO_BELL_MAIN_UNS,
        'dialog.blocked.title': rg_pushnotifications.RGPUNO_BELL_BLOCKED_TITLE,
        'dialog.blocked.message': rg_pushnotifications.RGPUNO_BELL_BLOCKED_MSG
      },
      colors: {
        'circle.background': rg_pushnotifications.RGPUNO_BELL_BACK,
        'circle.foreground': rg_pushnotifications.RGPUNO_BELL_FORE,
        'dialog.button.background.hovering': rg_pushnotifications.RGPUNO_BELL_DIAG_BACK_HOVER,
        'dialog.button.background': rg_pushnotifications.RGPUNO_BELL_DIAG_BACK,
        'dialog.button.foreground': rg_pushnotifications.RGPUNO_BELL_DIAG_FORE
      },
      displayPredicate: function () {
        if (rg_pushnotifications.RGPUNO_BELL_HIDE_SUBS) {
          return OneSignal.isPushNotificationsEnabled()
              .then(function (isPushEnabled) {
                return !isPushEnabled;
              });
        }
        return true;
      }
    },
    promptOptions: {
      slidedown: {
        prompts: [
          {
            type: "push",
            autoPrompt: true,
            siteName: rg_pushnotifications.PS_SHOP_NAME,
            text: {
              actionMessage: rg_pushnotifications.RGPUNO_REQUEST_MSG,
              acceptButton: rg_pushnotifications.RGPUNO_REQUEST_BTN_ACCEPT,
              cancelButton: rg_pushnotifications.RGPUNO_REQUEST_BTN_CANCEL
            },
            delay: {
              timeDelay: rg_pushnotifications.RGPUNO_REQUEST_DELAY_TIME,
              pageViews: rg_pushnotifications.RGPUNO_REQUEST_DELAY_PAGES_VIEWED
            }
          }
        ]
      }
    },
    welcomeNotification: {
      disable: !rg_pushnotifications.RGPUNO_WELCOME_SHOW,
      'title': rg_pushnotifications.RGPUNO_WELCOME_TITLE,
      'message': rg_pushnotifications.RGPUNO_WELCOME_MSG,
      icon: rg_pushnotifications.RGPUNO_WELCOME_ICON,
      'url': rg_pushnotifications.RGPUNO_WELCOME_URL
    },
    'safari_web_id': rg_pushnotifications.SAFARI_WEB_ID
  });

  if (rg_pushnotifications.RGPUNO_DEBUG_MODE) {
    OneSignal.log.setLevel('trace');
  } else {
    OneSignal.log.setLevel('none');
  }

  if (rg_pushnotifications.RGPUNO_POPUP_ALLOWED_SHOW || rg_pushnotifications.RGPUNO_POPUP_DECLINED_SHOW) {
    OneSignal.on('subscriptionChange', function (isSubscribed) {
      if (isSubscribed) {
        if (rg_pushnotifications.RGPUNO_POPUP_ALLOWED_SHOW) {
          $.fancybox(rg_pushnotifications.RGPUNO_POPUP_ALLOWED_MSG);
        }
      } else {
        if (rg_pushnotifications.RGPUNO_POPUP_DECLINED_SHOW) {
          $.fancybox(rg_pushnotifications.RGPUNO_POPUP_DECLINED_MSG);
        }
      }
    });
    OneSignal.once('initialize', function() {
      OneSignal.on('notifyButtonStateChange', function(params) {
        var isSubscribed = (params.to == 'subscribed');
        if (isSubscribed) {
          if (rg_pushnotifications.RGPUNO_POPUP_ALLOWED_SHOW) {
            $.fancybox(rg_pushnotifications.RGPUNO_POPUP_ALLOWED_MSG);
          }
        } else {
          if (rg_pushnotifications.RGPUNO_POPUP_DECLINED_SHOW) {
            $.fancybox(rg_pushnotifications.RGPUNO_POPUP_DECLINED_MSG);
          }
        }
      });
    });
  }

  OneSignal.getUserId().then(function (userId) {
    if (userId) {
      $.ajax({
        url: rg_pushnotifications._path + "/ajaxs/player_subscribed.php?token=" + rg_pushnotifications.token,
        type: "POST",
        data: "id_player=" + userId,
        cache: false
      });
    }
  });
});
