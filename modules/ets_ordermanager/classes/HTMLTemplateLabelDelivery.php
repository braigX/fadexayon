<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if(!defined('_PS_VERSION_'))
	exit;
class HTMLTemplateLabelDeliveryPdf extends HTMLTemplate
{
	public $order;

	public function __construct($object, $smarty)
	{
		$this->order = $object;
		$this->smarty = $smarty;
		// header informations
		$this->title = $this->order->reference ? : Order::generateReference();
        $this->date = Tools::displayDate($this->order->date_add);
		// footer informations
		$this->shop = new Shop(Context::getContext()->shop->id);
        $this->context= Context::getContext();
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
        $content = Configuration::get('ETS_ODE_DELIVERY_LABEL_CONTENT',$this->context->language->id);
		$this->smarty->assign(array(
            'content_html' => $this->replaceShortCode($content),
            'format_size' => Configuration::get('ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT') ? : 'A4',
		));
        if (version_compare(_PS_VERSION_, '1.7', '<') ){
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_content16.tpl');

        } else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_content.tpl');
        }
	}
    public function getHeader()
    {
       
        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_HEADER'))
            return '';
        $header = Configuration::get('ETS_ODE_DELIVERY_LABEL_HEADER',$this->context->language->id);
        $this->smarty->assign(
            array(
                'header_html' => $this->replaceShortCode($header),
                'format_size' => Configuration::get('ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT') ? : 'A4',                                
            )
        );
        if (version_compare(_PS_VERSION_, '1.7', '<') ){
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_header16.tpl');
        } else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_header.tpl');
        }
    }
    public function getFooter()
    {
        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_FOOTER'))
            return '';
        $footer = Configuration::get('ETS_ODE_DELIVERY_LABEL_FOOTER',$this->context->language->id);
        $this->smarty->assign(
            array(
                'footer_html' => $this->replaceShortCode($footer), 
                'format_size' => Configuration::get('ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT') ? : 'A4',                               
            )
        );
        if (version_compare(_PS_VERSION_, '1.7', '<') ){
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_footer16.tpl');
        } else {
            return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/label_delivery_footer.tpl');
        }
        
    }
    public function replaceShortCode($str)
    {
        $short_codes = explode(',',Ode_defines::getInstance()->getFields('short_code'));
        $id_address = $this->order->id_address_delivery;
        if($this->order->id_currency)
            $currency = new Currency($this->order->id_currency);
        else
            $currency = $this->context->currency;
        $address = new Address($id_address);
        $replaces = array(
            '{logo}' => $this->getImgLogo(),
            '{shop_phone}' => Configuration::get('PS_SHOP_PHONE') ?:'--',
            '{shop_fax}' => Configuration::get('PS_SHOP_FAX') ?:'--',
            '{shop_address}' => Configuration::get('PS_SHOP_ADDR1') ? : (Configuration::get('PS_SHOP_ADDR2') ? :'--'),
            '{shop_email}' => Configuration::get('PS_SHOP_EMAIL') ?: '--',
            '{shop_site_name}' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (Configuration::get('PS_SHOP_NAME') ? :''),
            '{shop_name}' => Configuration::get('PS_SHOP_NAME') ?:'--',
            '{shop_city}' => Configuration::get('PS_SHOP_CITY') ?:'--',
            '{shop_postcode}' => Configuration::get('PS_SHOP_CODE') ?:'--',
            '{shop_country}' => (($id_country = Configuration::get('PS_SHOP_COUNTRY_ID')) && ($country = new Country($id_country,$this->context->language->id))) ? $country->name:'--',
            '{shop_state}' => (($id_state = Configuration::get('PS_SHOP_STATE_ID')) && ($state = new State($id_state))) ? $state->name:'--',
            '{customer_lastname}' => $address->lastname ?:'--',
            '{customer_firstname}' => $address->firstname?:'--',
            '{customer_address}' => $address->address1 ? : ($address->address2 ?:'--'),
            '{customer_phone}' => $address->phone ? : ($address->phone_mobile ? :'--'),
            '{customer_company}' => $address->company ?:'--',
            '{customer_country}' => ($address->id_country && ($country = new Country($address->id_country,$this->context->language->id))) ? $country->name:'--',
            '{customer_state}' => ($address->id_state && ($state = new State($address->id_state))) ? $state->name :'--',
            '{customer_city}' => $address->city ?:'--',
            '{customer_postcode}' => $address->postcode ?:'--',
            '{barcode_order}' => Module::getInstanceByName('ets_ordermanager')->displayText('','img',array('src'=>$this->generatorBarcode())),
            '{id_order}' => $this->order->id,
            '{reference_order}' => $this->order->reference ?:'--',
            '{total_weight}' => $this->getTotalWeight(),
            '{product_list_total_order}' => trim($this->getProductsList(true)),
            '{product_list}' => trim($this->getProductsList(false)),
            '{subtotal}' => Tools::displayPrice($this->order->total_products_wt,$currency),
            '{shipping}' => Tools::displayPrice($this->order->total_shipping_tax_excl),
            '{taxes}' => Tools::displayPrice($this->order->total_paid_tax_incl-$this->order->total_paid_tax_excl,$currency),
            '{total}' => Tools::displayPrice($this->order->total_paid_tax_incl,$currency),
            '{discount}' => Tools::displayPrice($this->order->total_discounts_tax_excl,$currency),
            '{payment_method}' => $this->displayPaymentMethod($this->order),
            '{shipping_method}' => ($this->order->id_carrier && ($carrier = new Carrier($this->order->id_carrier)) ) ? ($carrier->name ?: Configuration::get('PS_SHOP_NAME')):'--' 
        );
        if($short_codes)
        {
            foreach($short_codes as $short_code)
            {
                if(isset($replaces[$short_code]))
                    $str = str_replace($short_code,$replaces[$short_code],$str);
            }
        }
        return $str;
    }
    public function displayPaymentMethod($order)
    {
        $payments = Db::getInstance()->executeS('SELECT op.payment_method,op.id_currency,op.amount FROM  `'._DB_PREFIX_.'order_payment` op
        INNER JOIN  `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment AND oip.id_order="'.(int)$order->id.'")
        ');
        if($payments)
        {
            $str ='';
            foreach($payments as &$payment)
            {
                $currnecy = new Currency($payment['id_currency']);
                $str .= ' - '.$payment['payment_method'].' ('.Tools::displayPrice($payment['amount'],$currnecy).')';
            }
            return trim($str,' - ');
        }
        return $this->order->payment ?:'--';
    }
    public function getProductsList($has_total){
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail` WHERE id_order="'.(int)$this->order->id.'"';
        $products = Db::getInstance()->executeS($sql);
        $this->smarty->assign(
            array(
                'products' => $products,
                'has_total' => $has_total, 
                'total_order' => $this->order->total_paid_tax_incl,
            )
        );
        return $this->smarty->fetch(_PS_MODULE_DIR_ . 'ets_ordermanager/views/templates/hook/pdf/products_list.tpl');
    }
    public function generatorBarcode(){
        $reference = $this->order->reference ? : $this->order->id;
        if(!file_exists(dirname(__FILE__).'/'.$reference.'.png'))
        {
            require(dirname(__FILE__).'/barcode/vendor/autoload.php');
            $redColor = [0, 0, 0];
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            $size_format = Configuration::get('ETS_ODE_DELIVERY_LABEL_SIZE_FORMAT') ? : 'A4';
            if(!in_array($size_format,array('A3','A4','A5','A6')))
                $size_format = 'A4';
            if($size_format=='A5'|| $size_format=='A6')
                file_put_contents(dirname(__FILE__).'/'.$reference.'.png',$generator->getBarcode($reference, $generator::TYPE_CODE_128, 1, 25, $redColor));
            else
                file_put_contents(dirname(__FILE__).'/'.$reference.'.png',$generator->getBarcode($reference, $generator::TYPE_CODE_128, 2, 50, $redColor));
        }
        return dirname(__FILE__).'/'.$reference.'.png';
    }
    public function getTotalWeight()
    {
        $weight = Db::getInstance()->getValue('SELECT SUM(product_weight) FROM `'._DB_PREFIX_.'order_detail` WHERE id_order='.(int)$this->order->id);
        return Tools::ps_round($weight)!=0 ? Tools::ps_round($weight).Configuration::get('PS_WEIGHT_UNIT'):'--';
    }
	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return ($this->order->reference ? : Order::generateReference()).'.pdf';
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return ($this->order->reference ? : Order::generateReference()).'.pdf';
	}
    protected function getImgLogo()
    {
        $id_shop = (int) $this->shop->id;

        $invoiceLogo = Configuration::get('PS_LOGO_INVOICE', null, null, $id_shop);
        if ($invoiceLogo && file_exists(_PS_IMG_DIR_ . $invoiceLogo)) {
            return Module::getInstanceByName('ets_ordermanager')->displayText(Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_ . $invoiceLogo,'img');
        }

        $logo = Configuration::get('PS_LOGO', null, null, $id_shop);
        if ($logo && file_exists(_PS_IMG_DIR_ . $logo)) {
            $url = Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_ . $logo;
            return Module::getInstanceByName('ets_ordermanager')->displayText($url,'img');
        }

        return null;
    }
    public function getPagination()
    {
        return '';
    }
}