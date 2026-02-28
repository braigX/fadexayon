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

class PDFGenerator extends PDFGeneratorCore
{
    public function writePage()
    {
        if(!Tools::isSubmit('printdeliverylabelorder'))
            return parent::writePage();
        $this->SetHeaderMargin(0);
        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_FOOTER'))
            $footertop=0;
        else
            $footertop = trim(Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER'))!='' ? (float)Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_FOOTER'): 21;
        $this->SetFooterMargin($footertop);
        if(!Configuration::get('ETS_ODE_DELIVERY_LABEL_USE_HEADER'))
            $top_content = 2;
        else
            $top_content=   trim(Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER'))!='' ? (float)Configuration::get('ETS_ODE_DELIVERY_LABEL_HEIGHT_HEADER'): 10;
        $this->setMargins(10, $top_content, 10);
        $this->AddPage();
        $this->writeHTML($this->content, true, false, true, false, '');
    }
}