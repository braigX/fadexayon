<?php
/**
 * Override to fix invoice language in multishop
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
        $this->smarty->assign('isTaxEnabled', (bool) Configuration::get('PS_TAX'));

        // FIX: Force the correct language from the order
        $order_language = new Language((int) $this->order->id_lang);
        Context::getContext()->language = $order_language;

        // If shop_address is null, then update it with current one.
        if (empty($this->order_invoice->shop_address)) {
            $this->order_invoice->shop_address = OrderInvoice::getCurrentFormattedShopAddress((int) $this->order->id_shop);
            if (!$bulk_mode) {
                OrderInvoice::fixAllShopAddresses();
            }
        }

        // header informations
        $this->date = Tools::displayDate($order_invoice->date_add);

        // FIX: Use the order's language instead of context language
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
        // FIX: Ensure language is set correctly for translations
        $order_language = new Language((int) $this->order->id_lang);
        Context::getContext()->language = $order_language;

        $this->assignCommonHeaderData();
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Invoice', [], 'Shop.Pdf')]);

        return $this->smarty->fetch($this->getTemplate('header'));
    }

    /**
     * Returns the template's HTML content.
     *
     * @return string HTML content
     */
    public function getContent()
    {
        // FIX: Ensure language is set correctly for the entire content generation
        $order_language = new Language((int) $this->order->id_lang);
        Context::getContext()->language = $order_language;

        // Call parent method to generate content with correct language
        return parent::getContent();
    }

    /**
     * Returns the template filename.
     *
     * @return string filename
     */
    public function getFilename()
    {
        // FIX: Use order's language for filename
        $id_lang = (int) $this->order->id_lang;
        $id_shop = (int) $this->order->id_shop;

        return sprintf(
            '%s.pdf',
            $this->order_invoice->getInvoiceNumberFormatted($id_lang, $id_shop)
        );
    }
}