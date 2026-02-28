<?php
if (!defined('_PS_VERSION_')) { exit; }
class Context extends ContextCore
{
    public function getComputingPrecision()
    {
        $precision = isset($this->currency->precision) ? (int) $this->currency->precision : 2;
        return (new \PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision())->getPrecision($precision);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
