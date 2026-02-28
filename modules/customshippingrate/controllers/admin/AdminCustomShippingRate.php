<?php
/**
 * 2018 Prestamatic
 *
 * NOTICE OF LICENSE
 *
 *  @author    Prestamatic
 *  @copyright 2018 Prestamatic
 *  @license   Licensed under the terms of the MIT license
 *  @link      https://prestamatic.co.uk
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/../../customshippingrate.php';

class AdminCustomShippingRateController extends ModuleAdminControllerCore
{
    protected $CSRmodule = null;

    public function getModule()
    {
        if (is_NULL($this->CSRmodule)) {
            $this->CSRmodule = new CustomShippingRate;
        }

        return $this->CSRmodule;
    }

    protected function l($string, $class = 'AdminCustomShippingRate', $addslashes = false, $htmlentities = true)
    {
        return $this->getModule()->l($string, $class);
    }

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'customshippingrate';
        $this->identifier = 'id_customshippingrate';
        //$this->className = 'CustomShippingRate';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->list_no_link = true;
        $this->_orderWay = 'DESC';

        $this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`,
        a.id_customshippingrate, a.id_cart shipping_price, a.id_cart, a.id_cart id_temp,
        a.id_cart total, ca.name carrier,
        IF (IFNULL(o.id_order, \'' . $this->l('Non ordered') . '\') = \'' . $this->l('Non ordered') . '\',
        IF(TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', a.`date_add`)) > 259200,
        \'' . $this->l('Abandoned cart') . '\', \'' . $this->l('Non ordered') . '\'), o.id_order) AS status,
        IF(o.id_order, 1, 0) badge_success, IF(o.id_order, 0, 1) badge_danger,
        IF(co.id_guest, "' . $this->l('Yes') . '", "' . $this->l('No') . '") id_guest';
        $this->_join = 'INNER JOIN ' . _DB_PREFIX_ . 'customer c ON (c.id_customer = a.id_customer)
        INNER JOIN ' . _DB_PREFIX_ . 'cart cart ON (cart.id_cart = a.id_cart)
        LEFT JOIN ' . _DB_PREFIX_ . 'currency cu ON (cu.id_currency = cart.id_currency)
        LEFT JOIN ' . _DB_PREFIX_ . 'carrier ca ON (ca.id_carrier = cart.id_carrier)
        LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.id_cart = cart.id_cart)
        LEFT JOIN (
            SELECT `id_guest`
            FROM `' . _DB_PREFIX_ . 'connections`
            WHERE
                TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', `date_add`)) < 1800
            LIMIT 1
        ) AS co ON co.`id_guest` = cart.`id_guest`';
        $this->_having = 'id_cart IS NOT NULL';

        // Ajouter le filtre conditionnel pour "Non Livré"
        if (Tools::getValue('action') && Tools::getValue('action') == 'filterOnlyNonDelivered') {
            $this->_having = 'a.email_status = 1';  // Filtrer uniquement les entrées avec "Non Livré"
        } else {
            $this->_use_found_rows = false;
        }

        $this->fields_list = array(
            'id_customshippingrate' => array(
                'title' => $this->l('Quote ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'id_cart' => array(
                'title' => $this->l('Cart ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!id_cart'
            ),
            'status' => array(
                'title' => $this->l('Order ID'),
                'align' => 'text-center',
                'badge_danger' => true,
                //'havingFilter' => true,
                'filter_key' => 'o!id_order'
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'filter_key' => 'c!lastname'
            ),
            'total' => array(
                'title' => $this->l('Total'),
                'callback' => 'getOrderTotalUsingTaxCalculationMethod',
                'orderby' => false,
                'search' => false,
                'align' => 'text-right',
                'badge_success' => true
            ),
            'carrier' => array(
                'title' => $this->l('Carrier'),
                'align' => 'text-left',
                'filter_key' => 'ca!name'
            ),
            'shipping_price' => array(
                'title' => $this->l('Shipping Price'),
                'callback' => 'getCustomShippingRate',
                'filter_key' => 'a!shipping_price'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-left',
                'type' => 'datetime',
                'class' => 'fixed-width-lg',
                'filter_key' => 'a!date_add'
            ),
            'id_guest' => array(
                'title' => $this->l('Online'),
                'align' => 'text-center',
                'type' => 'bool',
                //'havingFilter' => true,
                'class' => 'fixed-width-xs',
                'filter_key' => 'co!id_guest'
            ),
            'id_temp' => array(
                'title' => $this->l('Actions'),
                'align' => 'text-right',
                'search' => false,
                'callback' => 'addShippingCostButton',
            ),
            'email_status' => array(
                'title' => $this->l('Statut de Livraison'),
                'align' => 'text-center',
                'callback' => 'getEmailStatus',
                'filter_key' => 'a!email_status',
                'filter_type' => 'select',
                'list' => array(
                    0 => $this->l('Peut Livré'),//Add with w<assim 
                    1 => $this->l('Non Livré')
                ),
                'search' => false,
                'class' => 'fixed-width-xs'
            ),
        );
        $this->shopLinkType = '';

        parent::__construct();
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['export_cart'] = array(
                'href' => self::$currentIndex . '&exportcart&token=' . $this->token,
                'desc' => $this->l('Export carts'),
                'icon' => 'process-icon-export'
            );
        }

        parent::initPageHeaderToolbar();
    }
    /**Add with w<assim */
    public function addShippingCostButton($id)
    {
        // Check the email status in the database
        $email_status = Db::getInstance()->getValue('SELECT email_status FROM ' . _DB_PREFIX_ . 'customshippingrate WHERE id_cart = ' . (int) $id);

        // Prepare the button URLs
        if (version_compare(_PS_VERSION_, '1.7.7', '>=') === true) {
            $addShippingUrl = $this->context->link->getAdminLink('AdminOrders', true, array(), array('id_cart' => $id, 'addorder' => 1));
            $sendEmailUrl = $this->context->link->getAdminLink('AdminCustomShippingRate', true, array(), array('send_non_shippable_email' => 1, 'id_cart' => $id));

            // Display the button with email status
            return '<span class="btn-group-action">
                <span class="btn-group">
                    <a class="btn btn-default" href="' . $addShippingUrl . '&cartId=' . $id . '#shipping-block">
                        <i class="icon-shopping-cart"></i>&nbsp;' . $this->l('Add Shipping Cost') . '
                    </a>
                    <a class="btn btn-default" href="' . $sendEmailUrl . '" style="margin-left: 5px;">
                        <i class="icon-envelope"></i>&nbsp;' . $this->l('Refuser la demande') .
                    // Show the email status as a badge
                ($email_status == 1 ? '<span class="badge badge-danger" style="margin-left: 5px;background-color: #bd473e;">' . $this->l('Non Livré') . '</span>' : '<span class="badge badge-success" style="margin-left: 5px;background-color: #21834d;">' . $this->l('Peut Livré') . '</span>') . '
                        </a>
                        </span>
            </span>';
        } else {
            $addShippingUrl = $this->context->link->getAdminLink('AdminOrders') . '&id_cart=' . $id . '&addorder#address_part';
            $sendEmailUrl = $this->context->link->getAdminLink('AdminCustomShippingRate') . '&send_non_shippable_email=1&id_cart=' . $id;

            // Display the button with email status
            return '<span class="btn-group-action">
                <span class="btn-group">
                    <a class="btn btn-default" href="' . $addShippingUrl . '">
                        <i class="icon-shopping-cart"></i>&nbsp;' . $this->l('Add Shipping Cost') . '
                    </a>
                    <a class="btn btn-default" href="' . $sendEmailUrl . '"style="margin-left: 5px;">
                        <i class="icon-envelope"></i>&nbsp;' . $this->l('Refuser la demande') .
                    // Show the email status as a badge
                ($email_status == 1 ? '<span class="badge badge-danger" style="margin-left: 5px;background-color: #bd473e;">' . $this->l('Non Livré') . '</span>' : '<span class="badge badge-success" style="margin-left: 5px;background-color: #21834d;">' . $this->l('Peut Livré') . '</span>') . '
                        </a>
                </span>
            </span>';
        }
    }

    /**Add with w<assim */
    public function getEmailStatus($id_customshippingrate)
    {
        // Récupérer le statut de l'email depuis la base de données
        $email_status = Db::getInstance()->getValue('SELECT email_status FROM ' . _DB_PREFIX_ . 'customshippingrate WHERE id_customshippingrate = ' . (int) $id_customshippingrate);

        // Retourner le badge correspondant
        if ($email_status == 1) {
            return '<span class="badge badge-danger">' . $this->l('Non Livré') . '</span>';
        } else {
            return '<span class="badge badge-success">' . $this->l('Peut Livré') . '</span>';
        }
    }
    /**Add with w<assim */
    public function postProcess()
    {
        parent::postProcess();

        // Vérifiez si l'action d'envoi d'email est demandée
        if (Tools::isSubmit('send_non_shippable_email')) {
            $id_cart = (int) Tools::getValue('id_cart');

            // Vérifier si l'email a déjà été envoyé pour ce panier en vérifiant dans la base de données
            $email_status = Db::getInstance()->getValue('SELECT email_status FROM ' . _DB_PREFIX_ . 'customshippingrate WHERE id_cart = ' . (int) $id_cart);

            if ($email_status != 1) { // 1 means email has been sent
                // Envoi de l'email
                if ($this->sendNonShippableEmail($id_cart)) {
                    $this->confirmations[] = $this->l('Email de livraison non effectué envoyé avec succès.');

                    // Mettre à jour le statut de l'email dans la base de données
                    Db::getInstance()->update(
                        'customshippingrate',
                        array('email_status' => 1), // Mettre le statut à "envoyé"
                        'id_cart = ' . (int) $id_cart
                    );
                } else {
                    $this->errors[] = $this->l('Échec de l\'envoi de l\'email de livraison non effectuée.');
                }
            } else {
                // Si l'email a déjà été envoyé pour ce panier
                $this->errors[] = $this->l('Cet email a déjà été envoyé pour ce panier.');
            }
        }
    }
    /**Add with w<assim */
    protected function sendNonShippableEmail($id_cart)
    {
        $cart = new Cart($id_cart);
        $customer = new Customer((int) $cart->id_customer);

        // Génération du lien vers la commande
        $order_link = $this->context->link->getPageLink('order', true, null, 'id_cart=' . $id_cart);

        // Envoi de l'email
        $mail_sent = Mail::Send(
            (int) $this->context->language->id,
            'non_shippable', // Nom du fichier template sans l'extension
            $this->l('Mise à jour concernant votre demande de devis pour la livraison'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{cart_id}' => $id_cart,
                '{order_link}' => $order_link
            ),
            $customer->email,
            null,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_
        );

        // Mise à jour du statut d'email dans la base de données
        if ($mail_sent) {
            Db::getInstance()->update(
                'customshippingrate',
                array('email_status' => 1), // email envoyé
                'id_cart = ' . (int) $id_cart
            );
        }

        return $mail_sent;
    }
    public static function getOrderTotalUsingTaxCalculationMethod($id_cart)
    {
        return Cart::getTotalCart($id_cart, true);
    }

    public static function getCustomShippingRate($id_cart)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'customshippingrate
        WHERE `id_cart` = "' . (int) $id_cart . '"';
        if ($row = Db::getInstance()->getRow($sql)) {
            $cart = new Cart((int) $row['id_cart']);
            $shipping_cost = $row['shipping_price'];
            if ($shipping_cost > 0) {
                return Tools::displayPrice(
                    $shipping_cost,
                    Currency::getCurrencyInstance((int) $cart->id_currency),
                    false
                );
            } else {
                return '--';
            }
        }
    }
}
