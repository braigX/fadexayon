<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    $languages = Language::getLanguages(false);
    $order_state = OrderState::getOrderStates((int) Context::getContext()->language->id);
    $return = true;
    $lang_title = [];
    $lang_msg = [];

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

        $return &= RgPuNoConfig::update('EVENT_TITLE_' . $state['id_order_state'], $lang_title);
        $return &= RgPuNoConfig::update('EVENT_MSG_' . $state['id_order_state'], $lang_msg);
    }

    return $return;
}
