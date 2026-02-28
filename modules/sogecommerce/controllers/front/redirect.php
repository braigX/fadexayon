<?php
/**
 * Copyright © Lyra Network.
 * This file is part of Sogecommerce plugin for PrestaShop. See COPYING.md for license details.
 *
 * @author    Lyra Network (https://www.lyra.com/)
 * @copyright Lyra Network
 * @license   https://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

/**
 * This controller prepares form and redirects to payment gateway.
 */
class SogecommerceRedirectModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    private $logger;

    private $accepted_payment_types = array(
        'standard',
        'multi',
        'oney34',
        'fullcb',
        'franfinance',
        'ancv',
        'sepa',
        'sofort',
        'paypal',
        'choozeo',
        'other',
        'grouped_other'
    );

    public function __construct()
    {
        $this->display_column_left = false;
        $this->display_column_right = version_compare(_PS_VERSION_, '1.6', '<');

        parent::__construct();

        $this->logger = SogecommerceTools::getLogger();
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Initializes page header variables
     */
    public function initHeader()
    {
        parent::initHeader();

        // To avoid document expired warning.
        session_cache_limiter('private_no_expire');

        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    /**
     * PrestaShop 1.7: override page name in template to use same styles as checkout page.
     * @return array
     */
    public function getTemplateVarPage()
    {
        if (method_exists(get_parent_class($this), 'getTemplateVarPage')) {
            $page = parent::getTemplateVarPage();
            $page['page_name'] = 'checkout';
            return $page;
        }

        return null;
    }

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;

        // Page to redirect to if errors.
        $page = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';

        // Check cart errors.
        if (! Validate::isLoadedObject($cart) || $cart->nbProducts() <= 0) {
            $this->sogecommerceRedirect('index.php?controller=' . $page);
        } elseif (! $cart->id_customer || ! $cart->id_address_delivery || ! $cart->id_address_invoice || ! $this->module->active) {
            if (version_compare(_PS_VERSION_, '1.7', '<') && ! Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $page .= '&step=1'; // Not one page checkout, goto first checkout step.
            }

            $this->sogecommerceRedirect('index.php?controller=' . $page);
        }

        $type = Tools::getValue('sogecommerce_payment_type', null); // The selected payment submodule.

        if (! in_array($type, $this->accepted_payment_types)) {
            $this->logger->logWarning('Error: payment type "' . $type . '" is not supported. Load standard payment by default.');

            // Do not log sensitive data.
            $sensitive_data = array('sogecommerce_card_number', 'sogecommerce_cvv', 'sogecommerce_expiry_month', 'sogecommerce_expiry_year');
            $dataToLog = array();
            foreach ($_REQUEST as $key => $value) {
                if (in_array($key, $sensitive_data)) {
                    $dataToLog[$key] = str_repeat('*', Tools::strlen($value));
                } else {
                    $dataToLog[$key] = $value;
                }
            }

            $this->logger->logWarning('Request data: ' . print_r($dataToLog, true));

            $type = 'standard'; // Force standard payment.
        }

        $payment = null;
        $data = array();

        $option = '';

        switch ($type) {
            case 'standard':
                $payment = new SogecommerceStandardPayment();

                if ($payment->getEntryMode() === SogecommerceTools::MODE_LOCAL_TYPE) {
                    $data['card_type'] = Tools::getValue('sogecommerce_card_type');
                }

                // Payment by alias.
                if (Configuration::get('SOGECOMMERCE_STD_1_CLICK_PAYMENT') === 'True') {
                    $data['payment_by_identifier'] = Tools::getValue('sogecommerce_payment_by_identifier', '0');
                }

                break;

            case 'multi':
                $data['opt'] = Tools::getValue('sogecommerce_opt');
                $data['card_type'] = Tools::getValue('sogecommerce_card_type');

                $payment = new SogecommerceMultiPayment();
                $options = SogecommerceMultiPayment::getAvailableOptions($cart);
                $option = ' (' . $options[$data['opt']]['count'] . ' x)';
                break;

            case 'oney34':
                $payment = new SogecommerceOney34Payment();

                $data['opt'] = Tools::getValue('sogecommerce_oney34_option');

                $options = SogecommerceOney34Payment::getAvailableOptions($cart);
                $option = ' (' . $options[$data['opt']]['count'] . ' x)';
                break;

            case 'fullcb':
                $payment = new SogecommerceFullcbPayment();

                $data['card_type'] = Tools::getValue('sogecommerce_card_type');

                $options = SogecommerceFullcbPayment::getAvailableOptions($cart);
                $option = ' (' . $options[$data['card_type']]['count'] . ' x)';
                break;

            case 'franfinance':
                $payment = new SogecommerceFranfinancePayment();

                $data['opt'] = Tools::getValue('sogecommerce_ffin_option');

                $options = SogecommerceFranfinancePayment::getAvailableOptions($cart);
                $option = ' (' . $options[$data['opt']]['count'] . ' x)';
                break;

            case 'ancv':
                $payment = new SogecommerceAncvPayment();
                break;

            case 'sepa':
                $payment = new SogecommerceSepaPayment();

                // Payment by alias.
                if (Configuration::get('SOGECOMMERCE_SEPA_1_CLICK_PAYMNT') === 'True') {
                    $data['sepa_payment_by_identifier'] = Tools::getValue('sogecommerce_sepa_payment_by_identifier', '0');
                }

                break;

            case 'sofort':
                $payment = new SogecommerceSofortPayment();
                break;

            case 'paypal':
                $payment = new SogecommercePaypalPayment();
                break;

            case 'choozeo':
                $payment = new SogecommerceChoozeoPayment();
                $data['card_type'] = Tools::getValue('sogecommerce_card_type');
                break;

            case 'other':
                $code = Tools::getValue('sogecommerce_payment_code');
                $label = Tools::getValue('sogecommerce_payment_title');

                $payment = new SogecommerceOtherPayment();
                $payment->init($code, $label);

                $data['card_type'] = $code;
                break;

            case 'grouped_other':
                $payment = new SogecommerceGroupedOtherPayment();

                $data['card_type'] = Tools::getValue('sogecommerce_card_type');
                break;
        }

        // Validate payment data.
        $errors = $payment->validate($cart, $data);
        if (! empty($errors)) {
            $this->context->cookie->sogecommercePayErrors = implode("\n", $errors);

            if (version_compare(_PS_VERSION_, '1.7', '<') && ! Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $page .= '&step=3';

                if (version_compare(_PS_VERSION_, '1.5.1', '<')) {
                    $page .= '&cgv=1&id_carrier=' . $cart->id_carrier;
                }
            }

            $this->sogecommerceRedirect('index.php?controller=' . $page);
        }

        if (Configuration::get('SOGECOMMERCE_CART_MANAGEMENT') !== SogecommerceTools::KEEP_CART) {
            unset($this->context->cookie->id_cart); // To avoid double call to this page.
        }

        // Prepare data for payment gateway form.
        $request = $payment->prepareRequest($cart, $data);
        $fields = $request->getRequestFieldsArray(false, false /* Data escape will be done in redirect template. */);

        $dataToLog = $request->getRequestFieldsArray(true, false);
        $this->logger->logInfo('Data to be sent to payment gateway: ' . print_r($dataToLog, true));

        $this->context->smarty->assign('sogecommerce_params', $fields);
        $this->context->smarty->assign('sogecommerce_url', $request->get('platform_url'));
        $this->context->smarty->assign('sogecommerce_logo', $payment->getLogo());

        // Recover payment method title.
        $title = $payment->getTitle((int) $cart->id_lang) . $option;
        $this->context->smarty->assign('sogecommerce_title', $title);


        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setTemplate('module:sogecommerce/views/templates/front/redirect.tpl');
        } else {
            $this->setTemplate('redirect_bc.tpl');
        }
    }

    private function sogecommerceRedirect($url)
    {
        Tools::redirect($url);
    }
}
