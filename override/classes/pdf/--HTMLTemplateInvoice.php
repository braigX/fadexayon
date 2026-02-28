<?php
/**
 * Override for HTMLTemplateInvoice
 * Fixes translation issues by forcing order's language in all contexts
 * 
 * Location: /override/classes/pdf/HTMLTemplateInvoice.php
 */

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
    /**
     * @param OrderInvoice $order_invoice
     * @param Smarty $smarty
     * @param bool $bulk_mode
     *
     * @throws PrestaShopException
     */
    public function __construct(OrderInvoice $order_invoice, Smarty $smarty, $bulk_mode = false)
    {
        $this->order_invoice = $order_invoice;
        $this->order = new Order((int) $this->order_invoice->id_order);
        $this->smarty = $smarty;
        
        // CRITICAL FIX: Force the context language to the order's language
        $order_language = new Language((int) $this->order->id_lang);
        Context::getContext()->language = $order_language;
        
        // Also set the cookie language
        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->id_lang = (int) $this->order->id_lang;
        }
        
        $this->smarty->assign('isTaxEnabled', (bool) Configuration::get('PS_TAX'));

        if (empty($this->order_invoice->shop_address)) {
            $this->order_invoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop);
            if (!$bulk_mode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }

        // header informations
        $this->date = Tools::displayDate($order_invoice->date_add);

        // Use order's language
        $id_lang = (int) $this->order->id_lang;
        $id_shop = Context::getContext()->shop->id;
        $this->title = $order_invoice->getInvoiceNumberFormatted($id_lang, $id_shop);

        $this->shop = new Shop((int) $this->order->id_shop);
    }

    /**
     * Returns the template's HTML header.
     *
     * @return string HTML header
     */
    public function getHeader()
    {
        // Ensure language is still set
        Context::getContext()->language = new Language((int) $this->order->id_lang);
        
        $this->assignCommonHeaderData();
        
        $translator = Context::getContext()->getTranslator();
        $this->smarty->assign(['header' => $translator->trans('Invoice', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Compute layout elements size.
     *
     * @param array $params Layout elements
     *
     * @return array Layout elements columns size
     */
    protected function computeLayout(array $params)
    {
        // Ensure language is set before computing layout
        Context::getContext()->language = new Language((int) $this->order->id_lang);
        
        return parent::computeLayout($params);
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        // FORCE language context again before generating content
        $order_language = new Language((int) $this->order->id_lang);
        Context::getContext()->language = $order_language;
        if (isset(Context::getContext()->cookie)) {
            Context::getContext()->cookie->id_lang = (int) $this->order->id_lang;
        }
        
        $invoiceAddressPatternRules = json_decode(Configuration::get('PS_INVCE_INVOICE_ADDR_RULES'), true);
        $deliveryAddressPatternRules = json_decode(Configuration::get('PS_INVCE_DELIVERY_ADDR_RULES'), true);
        
        //Add with team wassim novatis
        $invoice_address = new Address((int) $this->order->id_address_invoice);
        $state = isset($invoice_address->id_state) ? new State((int) $invoice_address->id_state) : null;
        $customer = new Customer((int) $invoice_address->id_customer);
        $email = isset($customer->email) ? $customer->email : '';
        $country = new Country((int) $invoice_address->id_country);
        
        $formatted_invoice_address = '';
        
        $formatted_invoice_address .= $invoice_address->company ? $invoice_address->company . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->vat_number ? $invoice_address->vat_number . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->firstname . ' ' . $invoice_address->lastname ? $invoice_address->firstname . ' ' . $invoice_address->lastname . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->address1 ? $invoice_address->address1 . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->address2 ? $invoice_address->address2 . '<br/>' : '';
        $formatted_invoice_address .= ($invoice_address->postcode || $invoice_address->city) ? trim($invoice_address->postcode . ' ' . $invoice_address->city) . '<br/>' : '';
        $formatted_invoice_address .= $state ? $state->name . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->country  ? $invoice_address->country  . '<br/>' : '';
        $formatted_invoice_address .= $invoice_address->phone ? $invoice_address->phone . '<br/>' : '';
        $formatted_invoice_address .= $email ? $email . '<br/>' : '';

        $delivery_address = null;
        $formatted_delivery_address = '';
        if (!empty($this->order->id_address_delivery)) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $state_delivery = isset($delivery_address->id_state) ? new State((int) $delivery_address->id_state) : null;
    
            $formatted_delivery_address .= $delivery_address->company ? $delivery_address->company . '<br/>' : '';
            $formatted_delivery_address .= $delivery_address->vat_number ? $delivery_address->vat_number . '<br/>' : '';
            $formatted_delivery_address .= ($delivery_address->firstname && $delivery_address->lastname) ? $delivery_address->firstname . ' ' . $delivery_address->lastname . '<br/>' : '';
            $formatted_delivery_address .= $delivery_address->address1 ? $delivery_address->address1 . '<br/>' : '';
            $formatted_delivery_address .= $delivery_address->address2 ? $delivery_address->address2 . '<br/>' : '';
            $formatted_delivery_address .= ($delivery_address->postcode || $delivery_address->city) ? $delivery_address->postcode . ' ' . $delivery_address->city . '<br/>' : '';
            $formatted_delivery_address .= $state_delivery ? $state_delivery->name . '<br/>' : '';
            $formatted_delivery_address .= $delivery_address->country ? $delivery_address->country . '<br/>' : '';
            $formatted_delivery_address .= $delivery_address->phone ? $delivery_address->phone . '<br/>' : '';
            $formatted_delivery_address .= $email ? $email . '<br/>' : '';
        }

        $customer = new Customer((int) $this->order->id_customer);
        $carrier = new Carrier((int) $this->order->id_carrier);

        $order_details = $this->order_invoice->getProducts();

        $has_discount = false;
        foreach ($order_details as $id => &$order_detail) {
            if ($order_detail['reduction_amount_tax_excl'] > 0) {
                $has_discount = true;
                $order_detail['unit_price_tax_excl_before_specific_price'] = $order_detail['unit_price_tax_excl_including_ecotax'] + $order_detail['reduction_amount_tax_excl'];
            } elseif ($order_detail['reduction_percent'] > 0) {
                $has_discount = true;
                if ($order_detail['reduction_percent'] == 100) {
                    $order_detail['unit_price_tax_excl_before_specific_price'] = 0;
                } else {
                    $order_detail['unit_price_tax_excl_before_specific_price'] = (100 * $order_detail['unit_price_tax_excl_including_ecotax']) / (100 - $order_detail['reduction_percent']);
                }
            }

            $taxes = OrderDetail::getTaxListStatic($id);
            $tax_temp = [];
            foreach ($taxes as $tax) {
                $obj = new Tax($tax['id_tax']);
                $translator = Context::getContext()->getTranslator();
                $tax_temp[] = $translator->trans(
                    '%taxrate%%space%%',
                    [
                        '%taxrate%' => ($obj->rate + 0),
                        '%space%' => '&nbsp;',
                    ],
                    'Shop.Pdf'
                );
            }

            $order_detail['order_detail_tax'] = $taxes;
            $order_detail['order_detail_tax_label'] = implode(', ', $tax_temp);
        }
        unset($tax_temp, $order_detail);

        if (Configuration::get('PS_PDF_IMG_INVOICE')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] . (isset($order_detail['product_attribute_id']) ? '_' . (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PRODUCT_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';

                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );

                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
            unset($order_detail);
        }

        $cart_rules = $this->order->getCartRules();
        $free_shipping = false;
        foreach ($cart_rules as $key => $cart_rule) {
            if ($cart_rule['free_shipping']) {
                $free_shipping = true;
                $cart_rules[$key]['value_tax_excl'] -= $this->order_invoice->total_shipping_tax_excl;
                $cart_rules[$key]['value'] -= $this->order_invoice->total_shipping_tax_incl;

                if ($cart_rules[$key]['value'] == 0) {
                    unset($cart_rules[$key]);
                }
            }
        }

        $product_taxes = 0;
        foreach ($this->order_invoice->getProductTaxesBreakdown($this->order) as $details) {
            $product_taxes += $details['total_amount'];
        }

        $product_discounts_tax_excl = $this->order_invoice->total_discount_tax_excl;
        $product_discounts_tax_incl = $this->order_invoice->total_discount_tax_incl;
        if ($free_shipping) {
            $product_discounts_tax_excl -= $this->order_invoice->total_shipping_tax_excl;
            $product_discounts_tax_incl -= $this->order_invoice->total_shipping_tax_incl;
        }

        $products_after_discounts_tax_excl = $this->order_invoice->total_products - $product_discounts_tax_excl;
        $products_after_discounts_tax_incl = $this->order_invoice->total_products_wt - $product_discounts_tax_incl;

        $shipping_tax_excl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_excl;
        $shipping_tax_incl = $free_shipping ? 0 : $this->order_invoice->total_shipping_tax_incl;
        $shipping_taxes = $shipping_tax_incl - $shipping_tax_excl;

        $wrapping_taxes = $this->order_invoice->total_wrapping_tax_incl - $this->order_invoice->total_wrapping_tax_excl;

        $total_taxes = $this->order_invoice->total_paid_tax_incl - $this->order_invoice->total_paid_tax_excl;

        $footer = [
            'products_before_discounts_tax_excl' => $this->order_invoice->total_products,
            'product_discounts_tax_excl' => $product_discounts_tax_excl,
            'products_after_discounts_tax_excl' => $products_after_discounts_tax_excl,
            'products_before_discounts_tax_incl' => $this->order_invoice->total_products_wt,
            'product_discounts_tax_incl' => $product_discounts_tax_incl,
            'products_after_discounts_tax_incl' => $products_after_discounts_tax_incl,
            'product_taxes' => $product_taxes,
            'shipping_tax_excl' => $shipping_tax_excl,
            'shipping_taxes' => $shipping_taxes,
            'shipping_tax_incl' => $shipping_tax_incl,
            'wrapping_tax_excl' => $this->order_invoice->total_wrapping_tax_excl,
            'wrapping_taxes' => $wrapping_taxes,
            'wrapping_tax_incl' => $this->order_invoice->total_wrapping_tax_incl,
            'ecotax_taxes' => $total_taxes - $product_taxes - $wrapping_taxes - $shipping_taxes,
            'total_taxes' => $total_taxes,
            'total_paid_tax_excl' => $this->order_invoice->total_paid_tax_excl,
            'total_paid_tax_incl' => $this->order_invoice->total_paid_tax_incl,
        ];

        foreach ($footer as $key => $value) {
            $footer[$key] = Tools::ps_round($value, Context::getContext()->getComputingPrecision(), $this->order->round_mode);
        }

        $round_type = null;
        switch ($this->order->round_type) {
            case Order::ROUND_TOTAL:
                $round_type = 'total';
                break;
            case Order::ROUND_LINE:
                $round_type = 'line';
                break;
            case Order::ROUND_ITEM:
                $round_type = 'item';
                break;
            default:
                $round_type = 'line';
                break;
        }

        $display_product_images = Configuration::get('PS_PDF_IMG_INVOICE');
        $tax_excluded_display = Group::getPriceDisplayMethod($customer->id_default_group);

        $layout = $this->computeLayout(['has_discount' => $has_discount]);

        $legal_free_text = Hook::exec('displayInvoiceLegalFreeText', ['order' => $this->order]);
        if (!$legal_free_text) {
            $legal_free_text = Configuration::get('PS_INVOICE_LEGAL_FREE_TEXT', (int) $this->order->id_lang, null, (int) $this->order->id_shop);
        }

        $data = [
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'order_details' => $order_details,
            'carrier' => $carrier,
            'cart_rules' => $cart_rules,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'addresses' => ['invoice' => $invoice_address, 'delivery' => $delivery_address],
            'tax_excluded_display' => $tax_excluded_display,
            'display_product_images' => $display_product_images,
            'layout' => $layout,
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'footer' => $footer,
            'ps_price_compute_precision' => Context::getContext()->getComputingPrecision(),
            'round_type' => $round_type,
            'legal_free_text' => $legal_free_text,
        ];

        if (Tools::getValue('debug')) {
            die(json_encode($data));
        }

        $this->smarty->assign($data);

        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('invoice.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('invoice.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('invoice.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('invoice.product-tab')),
            'tax_tab' => $this->getTaxTabContent(),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('invoice.payment-tab')),
            'note_tab' => $this->smarty->fetch($this->getTemplate('invoice.note-tab')),
            'total_tab' => $this->smarty->fetch($this->getTemplate('invoice.total-tab')),
            'shipping_tab' => $this->smarty->fetch($this->getTemplate('invoice.shipping-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

    /**
     * Returns the tax tab content.
     *
     * @return string|array Tax tab html content
     */
    public function getTaxTabContent()
    {
        // Force language again
        Context::getContext()->language = new Language((int) $this->order->id_lang);
        
        return parent::getTaxTabContent();
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        $id_lang = (int) $this->order->id_lang;
        $id_shop = (int) $this->order->id_shop;

        return sprintf(
            '%s.pdf',
            $this->order_invoice->getInvoiceNumberFormatted($id_lang, $id_shop)
        );
    }
}