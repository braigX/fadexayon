<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoConfig
{
    const PREFIX = 'RgPuNo';

    public static function get($key, $idLang = null, $idShopGroup = null, $idShop = null, $default = false)
    {
        return Configuration::get(self::prefix('config') . $key, $idLang, $idShopGroup, $idShop, $default);
    }

    public static function getGlobal($key, $idLang = null)
    {
        return self::get($key, $idLang, 0, 0);
    }

    public static function update($key, $values, $html = false, $idShopGroup = null, $idShop = null)
    {
        return Configuration::updateValue(self::prefix('config') . $key, $values, $html, $idShopGroup, $idShop);
    }

    public static function updateGlobal($key, $values, $html = false)
    {
        return self::update($key, $values, $html, 0, 0);
    }

    public static function delete($key)
    {
        return Configuration::deleteByName(self::prefix('config') . $key);
    }

    /**
     * Gets all the configuration including the prefix, except the lang fields
     *
     * @return array
     */
    public static function getAll()
    {
        static $cache = null;

        if ($cache === null) {
            $sql = new DbQuery();
            $sql->select('`name`');
            $sql->from('configuration');
            $sql->where('`name` LIKE "' . pSQL(self::prefix('config')) . '%"');
            $sql->groupBy('`name`');

            $keys = array_column(Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql), 'name');
            $cache = Configuration::getMultiple($keys);
        }

        return $cache;
    }

    public static function install()
    {
        self::update('OS_APP_ID', null);
        self::update('OS_API_KEY', null);
        self::update('OS_SAFARI_ID', null);

        $languages = Language::getLanguages(false);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = '¿Desea recibir notificaciones con información importante para usted?';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Voulez-vous recevoir des notifications avec des informations importantes pour vous?';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Do you want to receive notifications with important information for you?';

                    break;
            }
        }

        self::update('REQUEST_MSG', $lang_text);
        self::update('WELCOME_SHOW', 1);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Bienvenido a nuestra tienda';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Bienvenue dans notre boutique';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Welcome to our shop';

                    break;
            }
        }

        self::update('WELCOME_TITLE', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Bienvenido a "' . Configuration::get('PS_SHOP_NAME') . '". Por esta vía le estaremos enviando información sobre sus pedidos, descuentos y mensajes importantes en general. Gracias por su preferencia.';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Bienvenue chez "' . Configuration::get('PS_SHOP_NAME') . '". De cette façon, nous vous enverrons des informations sur vos commandes, réductions et messages importants en général. Merci pour votre préférence.';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Welcome to "' . Configuration::get('PS_SHOP_NAME') . '". By this way we will be sending you information about your orders, discounts and important messages in general. Thanks for your preference.';

                    break;
            }
        }

        self::update('WELCOME_MSG', $lang_text);
        self::update('CART_DELIVERY', 'intelligent');
        self::update('CART_MIN_TIME', 48);
        self::update('CART_MAX_TIME', 168);
        self::update('CART_EXPIRATION', 72);
        self::update('CART_PREVIOUS', 120);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Recuerde terminar su compra';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'N\'oubliez pas de finir votre achat';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Remember to finish your purchase';

                    break;
            }
        }

        self::update('CART_TITLE', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Le recordamos que puede completar su compra en nuestra tienda.';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Nous vous rappelons que vous pouvez compléter votre achat dans notre boutique.';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'We remind you that you can complete your purchase in our shop.';

                    break;
            }
        }

        self::update('CART_MSG', $lang_text);
        self::update('BELL_SHOW', 1);
        self::update('BELL_SIZE', 'medium');
        self::update('BELL_POSITION', 'bottom-right');
        self::update('BELL_OFFSET_BOTOM', '15');
        self::update('BELL_OFFSET_RIGHT', '15');
        self::update('BELL_OFFSET_LEFT', '15');
        self::update('BELL_THEME', 'default');
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Subscribirse a las notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Abonnez-vous aux notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Subscribe to notifications';

                    break;
            }
        }

        self::update('BELL_TIP_STATE_UNS', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Estás subscrito a las notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Vous êtes abonné aux notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = "You're subscribed to notifications";

                    break;
            }
        }

        self::update('BELL_TIP_STATE_SUB', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Usted ha bloqueado las notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Vous avez bloqué les notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = "You've blocked notifications";

                    break;
            }
        }

        self::update('BELL_TIP_STATE_BLO', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Haz clic para suscribirte a las notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Cliquez pour vous abonner aux notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Click to subscribe to notifications';

                    break;
            }
        }

        /*self::update('BELL_MSG_PRENOTIFY', $lang_text);
        $lang_text = array();
        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = '¡Gracias por subscribirte!';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Merci pour votre subscription!';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = "Thanks for subscribing!";

                    break;
            }
        }*/
        self::update('BELL_ACTION_SUBS', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Estás subscrito a las notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Vous êtes abonné aux notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = "You're subscribed to notifications";

                    break;
            }
        }

        self::update('BELL_ACTION_RESUB', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'No recibirás notificaciones nuevamente';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Vous ne recevrez plus de notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = "You won't receive notifications again";

                    break;
            }
        }

        self::update('BELL_ACTION_UNS', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Administrar Notificaciones de la Tienda';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Gérer les notifications du magasin';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Manage Shop Notifications';

                    break;
            }
        }

        self::update('BELL_MAIN_TITLE', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'SUBSCRIBIRSE';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'SOUSCRIRE';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'SUBSCRIBE';

                    break;
            }
        }

        self::update('BELL_MAIN_SUB', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'DARSE DE BAJA';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'SE DÉSABONNER';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'UNSUBSCRIBE';

                    break;
            }
        }

        self::update('BELL_MAIN_UNS', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Desbloquear Notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Débloquer les Notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Unblock Notifications';

                    break;
            }
        }

        self::update('BELL_BLOCKED_TITLE', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Siga estas instrucciones para permitir notificaciones';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Suivez ces instructions pour autoriser les notifications';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Follow these instructions to allow notifications';

                    break;
            }
        }

        self::update('BELL_BLOCKED_MSG', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = '¡Sí, por favor!';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Oui!';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Yes, please!';

                    break;
            }
        }

        self::update('REQUEST_DELAY_PAGES_VIEWED', 3);
        self::update('REQUEST_DELAY_TIME', 3);
        self::update('REQUEST_BTN_ACCEPT', $lang_text);
        $lang_text = [];

        foreach ($languages as $lang) {
            switch (RgPuNoTools::findLang($lang['iso_code'])) {
                case 'es':
                    $lang_text[$lang['id_lang']] = 'Ahora no';

                    break;
                case 'fr':
                    $lang_text[$lang['id_lang']] = 'Pas maintenant';

                    break;
                default:
                    $lang_text[$lang['id_lang']] = 'Not now';

                    break;
            }
        }

        self::update('REQUEST_BTN_CANCEL', $lang_text);

        $lang_title = [];
        $lang_msg = [];
        $order_state = OrderState::getOrderStates((int) Context::getContext()->language->id);

        foreach ($order_state as $state) {
            foreach ($languages as $lang) {
                switch (RgPuNoTools::findLang($lang['iso_code'])) {
                    case 'es':
                        switch ($state['id_order_state']) {
                            // Awaiting check payment
                            case Configuration::get('PS_OS_CHEQUE'):
                            // Awaiting bank wire payment
                            case Configuration::get('PS_OS_BANKWIRE'):
                                $lang_title[$lang['id_lang']] = 'En espera de pago';
                                $lang_msg[$lang['id_lang']] = 'Hemos recibido tu pedido {order_reference}, estaremos atentos al pago. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Payment accepted
                            case Configuration::get('PS_OS_PAYMENT'):
                            // Remote payment accepted
                            case Configuration::get('PS_OS_WS_PAYMENT'):
                                $lang_title[$lang['id_lang']] = '¡Su pago ha sido confirmado!';
                                $lang_msg[$lang['id_lang']] = 'Hemos recibido correctamente el pago de su pedido {order_reference}. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Processing in progress
                            case Configuration::get('PS_OS_PREPARATION'):
                                $lang_title[$lang['id_lang']] = '¡Ya estamos preparando su pedido!';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} está siendo preparado. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Shipped
                            case Configuration::get('PS_OS_SHIPPING'):
                                $lang_title[$lang['id_lang']] = '¡Su pedido ha sido enviado!';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} se encuentra en camino. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Delivered
                            case Configuration::get('PS_OS_DELIVERED'):
                                $lang_title[$lang['id_lang']] = '¡Su pedido ha sido entregado!';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} fue entregado con éxito. Disfrútelo y esperamos que vuelva pronto.';

                                break;
                            // Canceled
                            case Configuration::get('PS_OS_CANCELED'):
                                $lang_title[$lang['id_lang']] = 'Pedido cancelado';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} ha sido cancelado. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Refunded
                            case Configuration::get('PS_OS_REFUND'):
                                $lang_title[$lang['id_lang']] = 'Pedido reembolsado';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} ha sido reembolsado. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            // Payment error
                            case Configuration::get('PS_OS_ERROR'):
                                $lang_title[$lang['id_lang']] = 'Error en el pedido';
                                $lang_msg[$lang['id_lang']] = 'Ocurrió un error en el pedido {order_reference}, revisaremos tan pronto como sea posible. Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                            default:
                                $lang_title[$lang['id_lang']] = 'Su pedido ha cambiado de estado';
                                $lang_msg[$lang['id_lang']] = 'Su pedido {order_reference} ahora se encuentra en el estado "{order_state}". Puede obtener más detalles en el historial de pedidos de su cuenta dando click aquí.';

                                break;
                        }

                        break;
                    case 'fr':
                        switch ($state['id_order_state']) {
                            // Awaiting check payment
                            case Configuration::get('PS_OS_CHEQUE'):
                            // Awaiting bank wire payment
                            case Configuration::get('PS_OS_BANKWIRE'):
                                $lang_title[$lang['id_lang']] = 'En attente de paiement';
                                $lang_msg[$lang['id_lang']] = "Nous avons bien reçu votre commande {order_reference}, nous serons attentifs au paiement. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Payment accepted
                            case Configuration::get('PS_OS_PAYMENT'):
                            // Remote payment accepted
                            case Configuration::get('PS_OS_WS_PAYMENT'):
                                $lang_title[$lang['id_lang']] = 'Votre paiement a été confirmé!';
                                $lang_msg[$lang['id_lang']] = "Nous avons bien reçu le paiement de votre commande {order_reference}. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Processing in progress
                            case Configuration::get('PS_OS_PREPARATION'):
                                $lang_title[$lang['id_lang']] = 'Nous préparons déjà votre commande!';
                                $lang_msg[$lang['id_lang']] = "Votre commande {order_reference} est en cours de préparation. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Shipped
                            case Configuration::get('PS_OS_SHIPPING'):
                                $lang_title[$lang['id_lang']] = 'Votre commande a été expédiée!';
                                $lang_msg[$lang['id_lang']] = "Votre commande {order_reference} est en cours d'acheminement. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Delivered
                            case Configuration::get('PS_OS_DELIVERED'):
                                $lang_title[$lang['id_lang']] = 'Votre commande a été livrée!';
                                $lang_msg[$lang['id_lang']] = 'Votre commande {order_reference} a été livrée avec succès, profitez-en et nous espérons que vous reviendrez bientôt.';

                                break;
                            // Canceled
                            case Configuration::get('PS_OS_CANCELED'):
                                $lang_title[$lang['id_lang']] = 'Commande annulée';
                                $lang_msg[$lang['id_lang']] = "Votre commande {order_reference} a été annulée. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Refunded
                            case Configuration::get('PS_OS_REFUND'):
                                $lang_title[$lang['id_lang']] = 'Commande remboursée';
                                $lang_msg[$lang['id_lang']] = "Votre commande {order_reference} a été remboursée. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            // Payment error
                            case Configuration::get('PS_OS_ERROR'):
                                $lang_title[$lang['id_lang']] = 'Erreur dans la commande';
                                $lang_msg[$lang['id_lang']] = "Une erreur s'est produite dans la commande {order_reference}, nous l'examinerons dès que possible. Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                            default:
                                $lang_title[$lang['id_lang']] = 'Le statut de votre commande a changé';
                                $lang_msg[$lang['id_lang']] = "Votre commande {order_reference} est maintenant à l'état \"{order_state}\". Vous pouvez obtenir plus de détails dans l'historique des commandes de votre compte en cliquant ici.";

                                break;
                        }

                        break;
                    default:
                        switch ($state['id_order_state']) {
                            // Awaiting check payment
                            case Configuration::get('PS_OS_CHEQUE'):
                            // Awaiting bank wire payment
                            case Configuration::get('PS_OS_BANKWIRE'):
                                $lang_title[$lang['id_lang']] = 'Awaiting payment';
                                $lang_msg[$lang['id_lang']] = 'We have received your order {order_reference}, we will be attentive to the payment. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Payment accepted
                            case Configuration::get('PS_OS_PAYMENT'):
                            // Remote payment accepted
                            case Configuration::get('PS_OS_WS_PAYMENT'):
                                $lang_title[$lang['id_lang']] = 'Your payment has been confirmed!';
                                $lang_msg[$lang['id_lang']] = 'We have successfully received payment for your order {order_reference}. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Processing in progress
                            case Configuration::get('PS_OS_PREPARATION'):
                                $lang_title[$lang['id_lang']] = 'We are already preparing your order!';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} is being prepared. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Shipped
                            case Configuration::get('PS_OS_SHIPPING'):
                                $lang_title[$lang['id_lang']] = 'Your order has been shipped!';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} is on its way. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Delivered
                            case Configuration::get('PS_OS_DELIVERED'):
                                $lang_title[$lang['id_lang']] = 'Your order has been delivered!';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} was delivered successfully. Enjoy it and we hope you come back soon.';

                                break;
                            // Canceled
                            case Configuration::get('PS_OS_CANCELED'):
                                $lang_title[$lang['id_lang']] = 'Order cancelled';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} has been cancelled. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Refunded
                            case Configuration::get('PS_OS_REFUND'):
                                $lang_title[$lang['id_lang']] = 'Order refunded';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} has been refunded. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            // Payment error
                            case Configuration::get('PS_OS_ERROR'):
                                $lang_title[$lang['id_lang']] = 'Error in the order';
                                $lang_msg[$lang['id_lang']] = 'An error occurred in the order {order_reference}, we will review as soon as possible. You can obtain more details in the order history of your account by clicking here.';

                                break;
                            default:
                                $lang_title[$lang['id_lang']] = 'Your order status has changed';
                                $lang_msg[$lang['id_lang']] = 'Your order {order_reference} is now in the status "{order_state}". You can obtain more details in the order history of your account by clicking here.';

                                break;
                        }

                        break;
                }
            }

            self::update('EVENT_TITLE_' . $state['id_order_state'], $lang_title);
            self::update('EVENT_MSG_' . $state['id_order_state'], $lang_msg);
        }

        return true;
    }

    public static function uninstall()
    {
        foreach (array_keys(self::getAll()) as $key) {
            Configuration::deleteByName($key);
        }

        return true;
    }

    public static function prefix($type = 'class')
    {
        static $cache = [];

        if (isset($cache[$type])) {
            return $cache[$type];
        }

        switch ($type) {
            case 'class':
                return $cache[$type] = self::PREFIX;
            case 'config':
                return $cache[$type] = Tools::strtoupper(self::PREFIX . '_');
            case 'db':
                return $cache[$type] = Tools::strtolower(self::PREFIX . '_');
            case 'dbfull':
                return $cache[$type] = _DB_PREFIX_ . Tools::strtolower(self::PREFIX . '_');
        }

        return false;
    }
}
