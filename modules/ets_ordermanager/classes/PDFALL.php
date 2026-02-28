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
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
 
require_once(dirname(__FILE__).'/HTMLTemplateInvoiceAll.php');
require_once(dirname(__FILE__).'/HTMLTemplateDeliverySlipAll.php');
require_once(dirname(__FILE__).'/HTMLTemplateLabelDelivery.php');
require_once(dirname(__FILE__).'/HTMLTemplateLabelDeliveryAll.php');
class PDFAll
{
	public $filename;
	public $pdf_renderer;
	public $objects =array();
	public $template;
    const TEMPLATE_INVOICE = 'Invoice';
    const TEMPLATE_INVOICE_ALL = 'InvoiceAll';
	const TEMPLATE_ORDER_RETURN = 'OrderReturn';
	const TEMPLATE_ORDER_SLIP = 'OrderSlip';
	const TEMPLATE_DELIVERY_SLIP = 'DeliverySlip';
    const TEMPLATE_DELIVERY_SLIP_ALL= 'DeliverySlipAll';
    const TEMPLATE_DELIVERY_LABEL_ALL= 'LabelDeliveryAll';
	const TEMPLATE_SUPPLY_ORDER_FORM = 'SupplyOrderForm';
	public function __construct($objects, $template, $smarty, $orientation = 'P')
	{
		$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'), $orientation);
		$this->template = $template;
		$this->smarty = $smarty;
        if($objects){
            foreach($objects as $object)
            {
            	
        		if (!($object instanceof Iterator) && !is_array($object))
       			{
       			      $this->objects[] = array($object);
       			}
                else
                    $this->objects[] = $object;
            }
        }
	}

	public function renderAll($display = true,$label=false)
	{
		$render = false;
		$this->pdf_renderer->setFontForLang(Context::getContext()->language->iso_code);
        if($this->objects)
            foreach($this->objects as $object_arr)
            {
                
                foreach ($object_arr as $object)
        		{
        			$template = $this->getTemplateObject($object);
        			if (!$template)
        				continue;
        			if (empty($this->filename))
        			{
        				$this->filename = $template->getFilename();
        				if (count($this->objects) > 1)
        					$this->filename = $template->getBulkFilename();
        			}
        			$render = true;
                    $this->pdf_renderer->createHeader($template->getHeader());
            		$this->pdf_renderer->createFooter($template->getFooter());
                    $content = $template->getContent();
            		$this->pdf_renderer->createContent($content);
                    if($label)
                    {
                        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_FOOTER'))
                            $footertop=0;
                        else
                            $footertop = trim(Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER'))!='' ? (float)Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER'): 21;
                        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_HEADER'))
                            $top_content = 2;
                        else
                            $top_content=   trim(Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER'))!='' ? (float)Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER'): 10;
                        $this->pdf_renderer->SetHeaderMargin(0);
                        $this->pdf_renderer->SetFooterMargin($footertop);
                        $this->pdf_renderer->setMargins(10, $top_content, 10);  
                    }
                    else
                    {
                        $this->pdf_renderer->SetHeaderMargin(5);
                        $this->pdf_renderer->SetFooterMargin(21);
                        $this->pdf_renderer->setMargins(10, 10, 10);
                    }
                    $this->pdf_renderer->AddPage();
                    $this->pdf_renderer->writeHTML($content, true, false, true, false, '');
        		}
          }
		if ($render)
		{
			// clean the output buffer
			if (ob_get_level() && ob_get_length() > 0)
				ob_clean();
			return $this->pdf_renderer->render($this->filename, $display);
		}
	}

	public function getTemplateObject($object)
	{
		$class = false;
		$classname = 'HTMLTemplate'.$this->template;
		if (class_exists($classname))
		{
			$class = new $classname($object, $this->smarty);
			if (!($class instanceof HTMLTemplate))
				throw new PrestaShopException('Invalid class. It should be an instance of HTMLTemplate');
		}
		return $class;
	}
}