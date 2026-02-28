<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoTools
{
    const MAX_PLAYERS_BY_NOTIFICATION = 2000;
    const MAX_NOTIFICATION_MESSAGE_LENGTH = 200;
    const MAX_NOTIFICATION_TITLE_LENGTH = 60;
    const MAX_NOTIFICATIONS_LIMIT = 50;
    const MAX_PLAYERS_LIMIT = 300;

    public static function generateCartReminderNotifications()
    {
        if ((int) RgPuNoConfig::get('CART_REMINDER')) {
            $min_abandoned_hours = (int) RgPuNoConfig::get('CART_MIN_TIME');
            $max_abandoned_hours = (int) RgPuNoConfig::get('CART_MAX_TIME');
            $min_cart_amount = (float) RgPuNoConfig::get('CART_MIN_AMOUNT');
            $max_cart_amount = (float) RgPuNoConfig::get('CART_MAX_AMOUNT');
            $min_cart_qty = (int) RgPuNoConfig::get('CART_MIN_QTY');
            $max_cart_qty = (int) RgPuNoConfig::get('CART_MAX_QTY');
            $w_tax = (int) RgPuNoConfig::get('CART_COUPON_W_TAX');
            $cart_mode = ((int) RgPuNoConfig::get('CART_COUPON_W_SHIP') ? Cart::BOTH : Cart::BOTH_WITHOUT_SHIPPING);
            $previous_notif_hours = (int) RgPuNoConfig::get('CART_PREVIOUS');

            Context::getContext()->dont_send_notifications = true;

            $customer_carts = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_customer`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` > 0
                    AND `id_cart` NOT IN(SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders`)
                    AND `date_upd` BETWEEN "' . date('Y-m-d H:i:s', strtotime('-' . (int) $max_abandoned_hours . ' hours')) . '" AND "' . date('Y-m-d H:i:s', strtotime('-' . (int) $min_abandoned_hours . ' hours')) . '"
                GROUP BY `id_customer`
            ');
            $last_carts_data = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_customer`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` > 0
                GROUP BY `id_customer`
            ');
            $last_carts = array_column($last_carts_data, 'id_cart', 'id_customer');

            foreach ($customer_carts as $cart_data) {
                if ($cart_data['id_cart'] != $last_carts[$cart_data['id_customer']]) {
                    continue;
                }

                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $cart_data['id_customer']);
                // app subscribers
                $id_subscribers_app = false;
                if (Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
                    $id_subscribers_app = RgPuNoSubscriber::getIdSubscribersByCustomer((int) $cart_data['id_customer'], true);
                }

                if (!$id_subscribers && !$id_subscribers_app) {
                    continue;
                }

                $id_cart = (int) $cart_data['id_cart'];
                $cart = new Cart($id_cart);
                $total_cart = $cart->getOrderTotal($w_tax, $cart_mode);
                $qty_cart = $cart->nbProducts();

                if (($min_cart_amount && $total_cart < $min_cart_amount) ||
                    ($max_cart_amount && $total_cart > $max_cart_amount) ||
                    ($min_cart_qty && $qty_cart < $min_cart_qty) ||
                    ($max_cart_qty && $qty_cart > $max_cart_qty)
                ) {
                    continue;
                }

                if ($previous_notif_hours) {
                    $notification = RgPuNoNotification::getLastNotificationByCart((int) $id_cart);

                    if ($notification && $notification['date_add'] > date('Y-m-d H:i:s', strtotime('-' . (int) $previous_notif_hours . ' hours'))) {
                        continue;
                    }
                }

                if ($id_subscribers) {
                    self::createCartReminderNotification($cart, $id_subscribers);
                }
                if ($id_subscribers_app) {
                    self::createCartReminderNotification($cart, $id_subscribers_app, true);
                }
            }

            $guest_carts = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_guest`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` = 0
                    AND `id_cart` NOT IN(SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders`)
                    AND `date_upd` BETWEEN "' . date('Y-m-d H:i:s', strtotime('-' . (int) $max_abandoned_hours . ' hours')) . '" AND "' . date('Y-m-d H:i:s', strtotime('-' . (int) $min_abandoned_hours . ' hours')) . '"
                GROUP BY `id_guest`
            ');
            $last_carts_data = Db::getInstance()->executeS('
                SELECT MAX(`id_cart`) AS `id_cart`, `id_guest`
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE `id_customer` = 0
                GROUP BY `id_guest`
            ');
            $last_carts = array_column($last_carts_data, 'id_cart', 'id_guest');

            foreach ($guest_carts as $cart_data) {
                if ($cart_data['id_cart'] != $last_carts[$cart_data['id_guest']]) {
                    continue;
                }

                $id_subscribers = RgPuNoSubscriber::getIdSubscribersByGuest((int) $cart_data['id_guest']);
                // app subscribers
                $id_subscribers_app = false;
                if (Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
                    $id_subscribers_app = RgPuNoSubscriber::getIdSubscribersByGuest((int) $cart_data['id_guest'], true);
                }

                if (!$id_subscribers && !$id_subscribers_app) {
                    continue;
                }

                $id_cart = (int) $cart_data['id_cart'];
                $cart = new Cart($id_cart);
                $total_cart = $cart->getOrderTotal($w_tax, $cart_mode);
                $qty_cart = $cart->nbProducts();

                if (($min_cart_amount && $total_cart < $min_cart_amount) ||
                    ($max_cart_amount && $total_cart > $max_cart_amount) ||
                    ($min_cart_qty && $qty_cart < $min_cart_qty) ||
                    ($max_cart_qty && $qty_cart > $max_cart_qty)
                ) {
                    continue;
                }

                if ($previous_notif_hours) {
                    $notification = RgPuNoNotification::getLastNotificationByCart((int) $id_cart);

                    if ($notification && $notification['date_add'] > date('Y-m-d H:i:s', strtotime('-' . (int) $previous_notif_hours . ' hours'))) {
                        continue;
                    }
                }

                if ($id_subscribers) {
                    self::createCartReminderNotification($cart, $id_subscribers);
                }
                if ($id_subscribers_app) {
                    self::createCartReminderNotification($cart, $id_subscribers_app, true);
                }
            }
        }
    }

    private static function createCartReminderNotification($cart, $id_subscribers, $from_app = false)
    {
        $expiration_hours = (int) RgPuNoConfig::get('CART_EXPIRATION');

        if (!$expiration_hours) {
            $expiration_hours = 72;
        }

        $id_players = [];

        foreach ($id_subscribers as $id_subscriber) {
            if ($id_player = RgPuNoSubscriber::getIdPlayerBySubscriber((int) $id_subscriber)) {
                $id_players[] = $id_player;
            }
        }

        if ($id_players) {
            $languages = Language::getLanguages(false);
            $os_langs = RgPuNoTools::getLanguagesPSandOS();

            if (RgPuNoConfig::get('CART_COUPON')) {
                $cart_rule = new CartRule();
                $amount = (float) RgPuNoConfig::get('CART_COUPON_DISCOUNT_AMOUNT');

                if (RgPuNoConfig::get('CART_COUPON_DISCOUNT_TYPE') == 'percent') {
                    $cart_rule->reduction_percent = $amount;
                } else {
                    $cart_rule->reduction_amount = $amount;
                }

                $cart_rule->id_customer = (int) $cart->id_customer;
                $cart_rule->date_from = date('Y-m-d H:i:s');
                $cart_rule->date_to = date('Y-m-d H:i:s', strtotime('+' . (int) RgPuNoConfig::get('CART_COUPON_TIME') . ' hours'));
                $cart_rule->partial_use = 0;
                $cart_rule->cart_rule_restriction = 1;

                if (RgPuNoConfig::get('CART_COUPON_AMOUNT')) {
                    $cart_rule->minimum_amount = (float) RgPuNoConfig::get('CART_COUPON_AMOUNT');
                    $cart_rule->minimum_amount_tax = (int) RgPuNoConfig::get('CART_COUPON_W_TAX');
                    $cart_rule->minimum_amount_shipping = (int) RgPuNoConfig::get('CART_COUPON_W_SHIP');
                } else {
                    $cart_rule->minimum_amount = 0;
                }

                $cart_rule->free_shipping = (int) RgPuNoConfig::get('CART_COUPON_FREE_SHIP');
                $cart_rule->highlight = 1;

                foreach ($languages as $lang) {
                    $cart_rule->name[(int) $lang['id_lang']] = 'Cart Reminder (' . (int) $cart->id . ')';
                }

                $cart_rule->add();
            }

            $title = [];
            $message = [];

            foreach ($languages as $lang) {
                if (isset($os_langs[Tools::strtoupper($lang['iso_code'])])) {
                    if (!isset($title[$os_langs[Tools::strtoupper($lang['iso_code'])]]) ||
                        in_array(Tools::strtoupper($lang['iso_code']), ['EN', 'ES', 'PT', 'NO'])
                    ) {
                        $title[$os_langs[Tools::strtoupper($lang['iso_code'])]] = trim(RgPuNoConfig::get('CART_TITLE', (int) $lang['id_lang']));
                    }

                    if (!isset($message[$os_langs[Tools::strtoupper($lang['iso_code'])]]) ||
                        in_array(Tools::strtoupper($lang['iso_code']), ['EN', 'ES', 'PT', 'NO'])
                    ) {
                        $message[$os_langs[Tools::strtoupper($lang['iso_code'])]] = trim(RgPuNoConfig::get('CART_MSG', (int) $lang['id_lang']));
                    }
                }
            }

            if (!isset($title['en'])) {
                $title['en'] = trim(RgPuNoConfig::get('CART_TITLE', (int) Configuration::get('PS_LANG_DEFAULT')));
            }

            if (!isset($message['en'])) {
                $message['en'] = trim(RgPuNoConfig::get('CART_MSG', (int) Configuration::get('PS_LANG_DEFAULT')));
            }

            $module = Module::getInstanceByName('rg_pushnotifications');
            $url = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'cart_reminder_redirect.php';
            $icon_path = $module->getPathUri() . 'uploads/cart.png';
            $icon_url = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'uploads/cart.png';
            $icon_default_url = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_FAVICON');
            $icon = (file_exists($icon_path) ? $icon_url : $icon_default_url);

            $fields = [
                'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                'include_player_ids' => $id_players,
                'contents' => $message,
                'chrome_web_icon' => $icon,
                'firefox_icon' => $icon,
                'web_url' => $url,
                'headings' => $title,
            ];
            if ($from_app && Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
                $fields['app_url'] = str_replace(Tools::getShopProtocol(), Configuration::get('RGMOAPP_SET_APP_NAME') . '://', $fields['web_url']);
            }

            if (RgPuNoConfig::get('CART_DELIVERY') == 'intelligent') {
                $fields['delayed_option'] = 'last-active';
            }

            $response = self::sendRealOneSignalNotification($fields);
            self::checkInvalidPlayers($response);

            if ($id_onesignal = self::getOneSignalNotificationId($response)) {
                foreach ($id_subscribers as $id_subscriber) {
                    $notification = new RgPuNoNotification();
                    $notification->id_onesignal = $id_onesignal;
                    $notification->id_cart = (int) $cart->id;
                    $notification->id_subscriber = (int) $id_subscriber;
                    $notification->title = 'Cart reminder (' . (int) $cart->id . ')';
                    $notification->notification_type = 'reminder';
                    $notification->date_start = date('Y-m-d H:i:s');
                    $notification->date_end = date('Y-m-d H:i:s', strtotime('+' . (int) $expiration_hours . ' hours'));
                    $notification->add();
                }
            }
        }
    }

    public static function sendHookNotification($notification_type, $id_subscribers, $entity, $module, $added = false, $from_app = false)
    {
        if (file_exists($module->getLocalPath() . 'views/img/' . (int) $notification_type . '.png')) {
            $icon = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'views/img/' . (int) $notification_type . '.png';
        } else {
            $icon = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_FAVICON');
        }

        $id_players = [];

        foreach ($id_subscribers as $id_subscriber) {
            if ($id_player = RgPuNoSubscriber::getIdPlayerBySubscriber((int) $id_subscriber)) {
                $id_players[] = $id_player;
            }
        }

        if ($id_players) {
            $os_langs = RgPuNoTools::getLanguagesPSandOS();

            if ($notification_type == 1003) {
                $history_url = Context::getContext()->link->getPageLink('history');
                Context::getContext()->language = new Language((int) $entity->id_lang);

                $heading = [
                    'en' => 'Tracking number registered',
                ];
                $content = [
                    'en' => 'Tracking Number registered for your order ' . $entity->reference . '. For more details go to Orders History in your Account or click here.',
                ];

                if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                    $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Tracking number registered', $module->name);
                    $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Tracking Number registered for your order', $module->name) . ' ' . $entity->reference . '. ' . Translate::getModuleTranslation($module, 'For more details go to Orders History in your Account or click here.', $module->name);
                }

                $fields = [
                    'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                    'include_player_ids' => $id_players,
                    'contents' => $content,
                    'chrome_web_icon' => $icon,
                    'firefox_icon' => $icon,
                    'web_url' => $history_url,
                    'headings' => $heading,
                ];
            } elseif ($notification_type == 1007) {
                if ($messages = $entity->getCustomerMessages((int) $entity->id_customer, true)) {
                    Context::getContext()->language = new Language((int) $entity->id_lang);
                    $heading = [
                        'en' => 'New message',
                    ];
                    $content = [
                        'en' => 'An answer to your message is available: ' . $messages[0]['message'] . ".\n" . 'To reply, click here.',
                    ];

                    if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                        $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'New message', $module->name);
                        $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'An answer to your message is available', $module->name) . ': ' . $messages[0]['message'] . ".\n" . Translate::getModuleTranslation($module, 'To reply, click here.', $module->name);
                    }

                    $url = Tools::url(
                        Context::getContext()->link->getPageLink('contact', true, (int) $entity->id_lang, null, false, $entity->id_shop),
                        'id_customer_thread=' . (int) $entity->id . '&token=' . $entity->token
                    );

                    $fields = [
                        'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                        'include_player_ids' => $id_players,
                        'contents' => $content,
                        'chrome_web_icon' => $icon,
                        'firefox_icon' => $icon,
                        'web_url' => $url,
                        'headings' => $heading,
                    ];
                }
            } elseif ($notification_type == 1008) {
                Context::getContext()->language = new Language(self::getIDLangByCustomer((int) $entity->id_customer));

                if ($added) {
                    $heading = [
                        'en' => 'Voucher generated',
                    ];
                    $content = [
                        'en' => 'A new voucher has been created for you. ' . ($entity->code ? 'Try applying this code in your next purchase: ' . $entity->code . '. ' : '') . 'For more details, click here.',
                    ];

                    if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                        $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Voucher generated', $module->name);
                        $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'A new voucher has been created for you', $module->name) . '. ' . ($entity->code ? Translate::getModuleTranslation($module, 'Try applying this code in your next purchase', $module->name) . ': ' . $entity->code . '. ' : '') . Translate::getModuleTranslation($module, 'For more details, click here.', $module->name);
                    }
                } else {
                    $heading = [
                        'en' => 'Voucher modified',
                    ];
                    $content = [
                        'en' => 'Your voucher has been modified. ' . ($entity->code ? 'Try applying this code in your next purchase: ' . $entity->code . '. ' : '') . 'For more details, click here.',
                    ];

                    if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                        $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Voucher modified', $module->name);
                        $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Your voucher has been modified', $module->name) . '. ' . ($entity->code ? Translate::getModuleTranslation($module, 'Try applying this code in your next purchase', $module->name) . ': ' . $entity->code . '. ' : '') . Translate::getModuleTranslation($module, 'For more details, click here.', $module->name);
                    }
                }

                $url = Context::getContext()->link->getPageLink('order');

                $fields = [
                    'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                    'include_player_ids' => $id_players,
                    'contents' => $content,
                    'chrome_web_icon' => $icon,
                    'firefox_icon' => $icon,
                    'web_url' => $url,
                    'headings' => $heading,
                ];
            } elseif ($notification_type == 1009) {
                Context::getContext()->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
                $heading = [
                    'en' => 'Product availability',
                ];
                $content = [
                    'en' => 'The product ' . $entity->name . ' is already in stock. You can get it by clicking here.',
                ];

                if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                    $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'Product availability', $module->name);
                    $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = Translate::getModuleTranslation($module, 'The product', $module->name) . ' ' . $entity->name . ' ' . Translate::getModuleTranslation($module, 'is already in stock', $module->name) . '. ' . Translate::getModuleTranslation($module, 'You can get it by clicking here.', $module->name);
                }

                $product_link = Context::getContext()->link->getProductLink($entity, $entity->link_rewrite);

                $fields = [
                    'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                    'include_player_ids' => $id_players,
                    'contents' => $content,
                    'chrome_web_icon' => $icon,
                    'firefox_icon' => $icon,
                    'web_url' => $product_link,
                    'headings' => $heading,
                ];
            }

            if (isset($fields) && count($fields)) {
                if ($from_app && Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
                    $fields['app_url'] = str_replace(Tools::getShopProtocol(), Configuration::get('RGMOAPP_SET_APP_NAME') . '://', $fields['web_url']);
                }

                $response = self::sendRealOneSignalNotification($fields);
                self::checkInvalidPlayers($response);

                return self::getOneSignalNotificationId($response);
            }
        }

        return false;
    }

    public static function sendOrderStatusUpdateHookNotification($order_state, $id_subscribers, $order, $module, $from_app = false)
    {
        $id_order_state = $order_state->id;
        $icon = RgPuNoConfig::get('EVENT_ICON_' . $id_order_state);

        if (file_exists($module->getLocalPath() . 'views/img/' . $icon . '.png')) {
            $icon = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'views/img/' . $icon . '.png';
        } elseif (file_exists($module->getLocalPath() . 'uploads/' . $icon . '.png')) {
            $icon = Tools::getShopDomainSsl(true) . $module->getPathUri() . 'uploads/' . $icon . '.png';
        } else {
            $icon = Tools::getShopDomainSsl(true) . _PS_IMG_ . Configuration::get('PS_FAVICON');
        }

        $id_players = [];

        foreach ($id_subscribers as $id_subscriber) {
            if ($id_player = RgPuNoSubscriber::getIdPlayerBySubscriber((int) $id_subscriber)) {
                $id_players[] = $id_player;
            }
        }

        if ($id_players) {
            $os_langs = RgPuNoTools::getLanguagesPSandOS();
            $history_url = Context::getContext()->link->getPageLink('history');
            $id_lang = (int) $order->id_lang;
            $title = RgPuNoConfig::get('EVENT_TITLE_' . $id_order_state, $id_lang);
            $message = RgPuNoConfig::get('EVENT_MSG_' . $id_order_state, $id_lang);
            $title = self::replaceTagsByHTMLFields($title, $order_state, $order);
            $message = self::replaceTagsByHTMLFields($message, $order_state, $order);
            $heading = [
                'en' => $title,
            ];
            $content = [
                'en' => $message,
            ];

            if (isset($os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)])) {
                $heading[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = $title;
                $content[$os_langs[Tools::strtoupper(Context::getContext()->language->iso_code)]] = $message;
            }

            $fields = [
                'app_id' => RgPuNoConfig::get('OS_APP_ID'),
                'include_player_ids' => $id_players,
                'contents' => $content,
                'chrome_web_icon' => $icon,
                'firefox_icon' => $icon,
                'web_url' => $history_url,
                'headings' => $heading,
            ];
            if ($from_app && Module::isEnabled('rg_psmobileapp') && Configuration::get('RGMOAPP_SET_APP_NAME')) {
                $fields['app_url'] = str_replace(Tools::getShopProtocol(), Configuration::get('RGMOAPP_SET_APP_NAME') . '://', $fields['web_url']);
            }

            $response = self::sendRealOneSignalNotification($fields);
            self::checkInvalidPlayers($response);

            return self::getOneSignalNotificationId($response);
        }

        return false;
    }

    public static function sendRealOneSignalNotification($fields)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . RgPuNoConfig::get('OS_API_KEY'), ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getRealOneSignalNotifications()
    {
        $fields = [
            'app_id' => RgPuNoConfig::get('OS_APP_ID'),
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications?' . http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . RgPuNoConfig::get('OS_API_KEY'), ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $notifications = [];
        $total = 0;

        if (($json = json_decode($response)) && isset($json->total_count)) {
            $total = (int) $json->total_count;
            $notifications = $json->notifications;

            for ($offset = self::MAX_NOTIFICATIONS_LIMIT; $offset < $total; $offset += self::MAX_NOTIFICATIONS_LIMIT) {
                $fields['offset'] = $offset;
                curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications?' . http_build_query($fields));
                $response = curl_exec($ch);

                if ($json = json_decode($response)) {
                    $notifications = array_merge($notifications, $json->notifications);
                }
            }
        }

        curl_close($ch);

        return $notifications;
    }

    public static function getRealOneSignalPlayers($offset, &$total)
    {
        $fields = [
            'app_id' => RgPuNoConfig::get('OS_APP_ID'),
            'limit' => self::MAX_PLAYERS_LIMIT,
            'offset' => $offset,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/players?' . http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . RgPuNoConfig::get('OS_API_KEY'), ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($json = json_decode($response)) {
            $players = $json->players;
            $total = (int) $json->total_count;
        } else {
            $players = [];
            $total = 0;
        }

        return $players;
    }

    public static function cancelRealOneSignalNotifications($id_notifications)
    {
        $fields = [
            'app_id' => RgPuNoConfig::get('OS_APP_ID'),
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . RgPuNoConfig::get('OS_API_KEY'), ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        foreach ($id_notifications as $id_notif) {
            curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications/' . $id_notif . '?' . http_build_query($fields));
            curl_exec($ch);
        }

        curl_close($ch);
    }

    public static function getOneSignalNotificationId($response)
    {
        if ($json = json_decode($response)) {
            if (isset($json->id) && $json->id) {
                return $json->id;
            }
        }

        return false;
    }

    public static function testOneSignalCredentials($app_id, $api_key)
    {
        if (!RgPuNoConfig::get('CONNECTED')) {
            $fields = [
                'app_id' => $app_id,
                'limit' => 1,
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/players?' . http_build_query($fields));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8',
                'Authorization: Basic ' . $api_key, ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            curl_close($ch);

            $connected = ($json = json_decode($response)) && isset($json->total_count);

            RgPuNoConfig::update('CONNECTED', $connected);

            return $connected;
        }

        return true;
    }

    public static function checkInvalidPlayers($response)
    {
        $players = [];

        if ($json = json_decode($response)) {
            if (isset($json->errors->invalid_player_ids) && is_array($json->errors->invalid_player_ids)) {
                foreach ($json->errors->invalid_player_ids as $id_player) {
                    RgPuNoSubscriber::setUnsubcribedPlayer($id_player);
                    $players[] = $id_player;
                }
            } elseif (isset($json->errors) && is_array($json->errors)) {
                $messages = implode("\n", $json->errors);
                self::log($messages);
            }
        }

        return $players;
    }

    public static function getIDLangByCustomer($id_customer)
    {
        return (int) Db::getInstance()->getValue('SELECT `id_lang`
            FROM `' . _DB_PREFIX_ . 'customer`
            WHERE `id_customer` = ' . (int) $id_customer);
    }

    public static function refreshCampaignData()
    {
        $campaigns_list = Db::getInstance()->executeS('
            SELECT n.`id_campaign`, GROUP_CONCAT(DISTINCT `id_onesignal`) AS `notifications`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification` n
            INNER JOIN `' . _DB_PREFIX_ . 'rg_pushnotifications_campaign` c ON(n.`id_campaign` = c.`id_campaign`)
            WHERE c.`finished` = 0 AND n.`id_onesignal` != ""
            GROUP BY n.`id_campaign`
        ');
        $single_notification_list = Db::getInstance()->executeS('
            SELECT `id_onesignal`, GROUP_CONCAT(DISTINCT `id_notification`) AS `notifications`
            FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
            WHERE `notification_type` != "message"
                AND `status` IN("queued","scheduled")
                AND `id_onesignal` != ""
            GROUP BY `id_onesignal`
        ');

        if (count($campaigns_list) || count($single_notification_list)) {
            $notifications_data = self::getRealOneSignalNotifications();
        }

        if (count($campaigns_list)) {
            foreach ($campaigns_list as $camp) {
                $delivered = $unreachable = $clicked = $remaining = 0;
                $ids_notification = explode(',', $camp['notifications']);

                foreach ($ids_notification as $id_notification) {
                    if ($notification = self::getNotificationDataFromList($id_notification, $notifications_data)) {
                        $delivered += (int) $notification->successful;
                        $unreachable += (int) $notification->failed;
                        $clicked += (int) $notification->converted;
                        $remaining += (int) $notification->remaining;
                    }
                }

                $campaign = new RgPuNoCampaign((int) $camp['id_campaign']);
                $campaign->total_delivered = $delivered;
                $campaign->total_unreachable = $unreachable;
                $campaign->total_clicked = $clicked;
                $campaign->total_notifications = $delivered + $unreachable + $remaining;
                $campaign->finished = (time() - strtotime($campaign->date_end) > 72 * 60 * 60);
                $campaign->update();

                if ($campaign->total_delivered == $campaign->total_notifications) {
                    $ids_campaign_notification = Db::getInstance()->executeS(
                        'SELECT `id_notification`
                        FROM `' . _DB_PREFIX_ . 'rg_pushnotifications_notification`
                        WHERE `id_campaign` = ' . (int) $campaign->id
                    );
                    $ids_campaign_notification = array_column($ids_campaign_notification, 'id_notification');

                    foreach ($ids_notification as $id_notification) {
                        $notification = new RgPuNoNotification((int) $id_notification);
                        $notification->status = 'delivered';

                        if ($campaign->total_clicked == $campaign->total_notifications) {
                            $notification->clicked = 1;
                        }

                        $notification->update();
                    }
                }
            }
        }

        if (count($single_notification_list)) {
            foreach ($single_notification_list as $notif) {
                $ids_notification = explode(',', $notif['notifications']);

                if ($notification_os = self::getNotificationDataFromList($notif['id_onesignal'], $notifications_data)) {
                    foreach ($ids_notification as $id_notification) {
                        $notification = new RgPuNoNotification((int) $id_notification);

                        if (count($ids_notification) == (int) $notification_os->successful) {
                            $notification->status = 'delivered';
                        } elseif ((bool) $notification_os->canceled) {
                            $notification->status = 'canceled';
                        } elseif (($id_player = RgPuNoSubscriber::getIdPlayerBySubscriber((int) $notification->id_subscriber)) &&
                            !in_array($id_player, $notification_os->include_player_ids)
                        ) {
                            $notification->status = 'norecipients';
                        }

                        if (count($ids_notification) == (int) $notification_os->converted) {
                            $notification->clicked = 1;
                        }

                        $notification->update();
                    }
                }
            }
        }
    }

    /**
     * Refresh the players data based on the information from OneSignal
     *
     * @param int $page Request page starting from 0
     *
     * @return bool true if there are more players available to be processed, false otherwise
     */
    public static function refreshPlayerData($page)
    {
        $platforms = RgPuNoSubscriber::getPlatforms();
        $total = 0;
        $offset = (int) $page * self::MAX_PLAYERS_LIMIT;
        $players_data = self::getRealOneSignalPlayers($offset, $total);

        foreach ($players_data as $player) {
            if ($id_subscriber = RgPuNoSubscriber::getIdSubscriberByPlayer($player->id)) {
                $subscriber = new RgPuNoSubscriber((int) $id_subscriber);
                $subscriber->platform = isset($platforms[(int) $player->device_type]) ? $platforms[(int) $player->device_type] : 'UNKNOWN';
                $subscriber->device = $player->device_model;
                $subscriber->session_count = (int) $player->session_count;
                $subscriber->last_active = date('Y-m-d H:i:s', (int) $player->last_active);
                $subscriber->unsubscribed = (bool) $player->invalid_identifier;
                $subscriber->update();
            }
        }

        $processed = $offset + count($players_data);

        return [
            'continue' => count($players_data) > 0,
            'percent' => ($total ? round($processed / $total * 100) : 100),
        ];
    }

    /**
     * Convert the local datetime of the shop to OS time, and vice versa
     *
     * @param string $datetime [Eg, '2017-12-01 10:00:00']
     * @param string $to ['os' or 'ps']
     *
     * @return string|false [Return FALSE or the converted datetime. For 'os' will include UTC]
     */
    public static function convertDate($datetime, $to = 'os')
    {
        if ($to == 'os') {
            $to_timezone = 'America/New_York';
            $from_timezone = Configuration::get('PS_TIMEZONE');
        } elseif ($to == 'ps') {
            $to_timezone = Configuration::get('PS_TIMEZONE');
            $from_timezone = 'America/New_York';
        } else {
            return false;
        }

        $date = new DateTime($datetime, new DateTimeZone($from_timezone));
        $date->setTimezone(new DateTimeZone($to_timezone));

        if ($to == 'os') {
            return $date->format('Y-m-d H:i:s') . ' GMT' . $date->format('O');
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Find the language iso code with translation available
     *
     * @param string $iso_code [Iso code of the language]
     *
     * @return string|false [Return FALSE or the language iso code with translation available]
     */
    public static function findLang($iso_code)
    {
        $langs = [
            'en' => ['en', 'gb', 'ca', 'gb', 'au', 'ie', 'nz'],
            'es' => ['es', 'mx', 'co', 'ar', 'ec', 'pa', 'ni', 'pe', 've', 'cl', 'uy', 'py'],
            'fr' => ['fr', 'be', 'lu'],
        ];

        foreach ($langs as $key => $arr) {
            if (in_array($iso_code, $arr)) {
                return $key;
            }
        }

        return false;
    }

    public static function getLanguagesPSandOS()
    {
        // PS => OS
        $langs = [
            'AR' => 'ar',
            'BG' => 'bg',
            'CA' => 'ca',
            'ZH' => 'zh-Hans',
            'TW' => 'zh-Hant',
            'HR' => 'hr',
            'CS' => 'cs',
            'DA' => 'da',
            'NL' => 'nl',
            'EN' => 'en',
            'GB' => 'en',
            'ET' => 'et',
            'FI' => 'fi',
            'FR' => 'fr',
            'KA' => 'ka',
            'DE' => 'de',
            'HE' => 'he',
            'HU' => 'hu',
            'ID' => 'id',
            'IT' => 'it',
            'JA' => 'ja',
            'KO' => 'ko',
            'LV' => 'lv',
            'LT' => 'lt',
            'MS' => 'ms',
            'NO' => 'nb',
            'NN' => 'nb',
            'FA' => 'fa',
            'PL' => 'pl',
            'PT' => 'pt',
            'BR' => 'pt',
            'RO' => 'ro',
            'RU' => 'ru',
            'SR' => 'sr',
            'SK' => 'sk',
            'ES' => 'es',
            'AG' => 'es',
            'MX' => 'es',
            'SV' => 'sv',
            'TH' => 'th',
            'TR' => 'tr',
            'UK' => 'uk',
            'VN' => 'vi',
        ];

        return $langs;
    }

    private static function getNotificationDataFromList($id_notification, $list)
    {
        foreach ($list as $notification_data) {
            if ($notification_data->id == $id_notification) {
                return $notification_data;
            }
        }

        return false;
    }

    public static function replaceTagsByHTMLFields($content, $order_state, $order)
    {
        if (preg_match_all('/{.+?}/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $replaces = [];
            $searches = [];
            $customer = null;

            foreach ($matches[0] as $match) {
                $match = $match[0];
                $searches[] = $match;
                $replace = '';

                switch ($match) {
                    case '{firstname}':
                        if ($customer === null) {
                            $customer = new Customer($order->id_customer);
                        }

                        $replace = $customer->firstname;

                        break;
                    case '{lastname}':
                        if ($customer === null) {
                            $customer = new Customer($order->id_customer);
                        }

                        $replace = $customer->lastname;

                        break;
                    case '{order_reference}':
                        $replace = $order->reference;

                        break;
                    case '{order_id}':
                        $replace = $order->id;

                        break;
                    case '{order_state}':
                        $replace = $order_state->name;

                        break;
                }

                $replaces[] = $replace;
            }

            return str_replace($searches, $replaces, $content);
        }

        return $content;
    }

    public static function getRgGroupBoxValue($is_submit, $field, $base_list, $default_values, &$fields_value, $for_save = false, $delimiter = ',')
    {
        if ($is_submit) {
            $fields_value[$field] = Tools::getValue($field) ? (array) Tools::getValue($field) : [];
        } else {
            $fields_value[$field] = is_array($default_values) ? $default_values : explode($delimiter, $default_values);
        }

        foreach ($base_list as $value) {
            if ($for_save === false) {
                $fields_value[$field . '_' . $value] = in_array($value, $fields_value[$field]) ?: false;
            }
        }

        if ($for_save === true) {
            $fields_value[$field] = implode($delimiter, $fields_value[$field]) ?: null;
        }
    }

    public static function getNewModuleVersion($module_name, $current_version)
    {
        static $response = null;

        if ($response !== null) {
            return $response;
        }

        $data = RgPuNoConfig::getGlobal('LAST_VERSION');
        $json = json_decode($data, true);

        if (isset($json['version']) &&
            version_compare($json['version'], $current_version, '>') &&
            (time() - $json['checked_on']) < 86400
        ) {
            return $response = $json['version'];
        }

        $params = [
            'key' => '77919fabe4f694c3aeed566b529c5a60',
            'params' => [
                'module' => $module_name,
            ],
        ];

        $curl = self::rgCurl('moduleinfo', $params);
        $json = json_decode($curl, true);

        if (isset($json['version'])) {
            RgPuNoConfig::updateGlobal(
                'LAST_VERSION',
                json_encode(['version' => $json['version'], 'checked_on' => time()])
            );

            if (version_compare($json['version'], $current_version, '>')) {
                return $response = $json['version'];
            }
        }

        return $response = false;
    }

    public static function rgCurl($service, $params)
    {
        $ch = curl_init('https://www.rolige.com/modules/rg_webservice/api/' . $service);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function getLink($type, $module = null, $lang_iso_code = null)
    {
        if ($lang_iso_code === null || !Validate::isLanguageIsoCode($lang_iso_code)) {
            $lang_iso_code = Context::getContext()->language->iso_code;
        }

        switch ($type) {
            case 'author':
                return isset($module->module_key) && $module->module_key ? $module->addons_author_link : $module->author_link;
            case 'module':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/product.php?id_product=' . $module->addons_module_id
                    : 'https://www.rolige.com/index.php?controller=product&id_product=' . $module->module_id;
            case 'partner':
                return $lang_iso_code === 'es'
                    ? 'https://www.prestashop.com/es/expertos/agencias-web/rolige'
                    : 'https://www.prestashop.com/en/experts/web-agencies/rolige';
            case 'support':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/contact-form.php?id_product=' . $module->addons_module_id
                    : 'https://www.rolige.com/index.php?controller=contact&id_product=' . $module->module_id;
            case 'rate':
                return isset($module->module_key) && $module->module_key
                    ? 'https://addons.prestashop.com/ratings.php'
                    : 'https://www.rolige.com/index.php?controller=product&id_product=' . $module->module_id;
            case 'docs':
                $utm_source = 'rolige';
                if (isset($module->module_key) && $module->module_key) {
                    $utm_source = 'addons';
                }

                return $lang_iso_code === 'es'
                    ? 'https://docs.rolige.com/es/modulos-prestashop/notificaciones-push/introduccion/?utm_source=' . $utm_source
                    : 'https://docs.rolige.com/en/prestashop-modules/push-notifications/getting-started/?utm_source=' . $utm_source;
        }

        return false;
    }

    public static function getProductsMarketing($module_name, $source)
    {
        static $response = null;

        if ($response !== null) {
            return $response;
        }

        $config = 'RG_MARKETING_' . Tools::strtoupper($source) . '_REQUEST';
        $data = Configuration::getGlobalValue($config);
        $json = json_decode($data, true);

        if (!isset($json['next_request']) || (int) $json['next_request'] < time()) {
            $params = [
                'key' => '764438a9bd64fdae8e5b1065d4741eab',
                'params' => [
                    'module' => $module_name,
                    'domain' => Tools::getServerName(),
                    'source' => $source,
                    'country_iso_code' => Country::getIsoById((int) Configuration::get('PS_COUNTRY_DEFAULT')),
                    'currency_iso_code' => Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'))->iso_code,
                    'lang_iso_code' => Context::getContext()->language->iso_code,
                ],
            ];

            $curl = self::rgCurl('productsmarketing', $params);
            $json = json_decode($curl, true);

            if (isset($json['products']) && count($json['products'])) {
                Configuration::updateGlobalValue($config, $curl);
            }
        }

        return $response = isset($json['products']) ? $json['products'] : [];
    }

    public static function validateBasicSettings()
    {
        $val = RgPuNoConfig::getAll();
        $prefix = RgPuNoConfig::prefix('config');

        if (!$val[$prefix . 'OS_APP_ID'] || !Validate::isString($val[$prefix . 'OS_APP_ID'])) {
            return false;
        }

        if (!$val[$prefix . 'OS_API_KEY'] || !Validate::isString($val[$prefix . 'OS_API_KEY'])) {
            return false;
        }

        if (!$val[$prefix . 'OS_SAFARI_ID'] || !Validate::isString($val[$prefix . 'OS_SAFARI_ID'])) {
            return false;
        }

        if (!self::testOneSignalCredentials($val[$prefix . 'OS_APP_ID'], $val[$prefix . 'OS_API_KEY'])) {
            return false;
        }

        return true;
    }

    public static function log($message, $extra_object = null)
    {
        return Tools::error_log(
            '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n"
                . ($extra_object ? print_r($extra_object, true) . "\n" : ''),
            3,
            dirname(__FILE__) . '/../logs/error_log'
        );
    }
}
