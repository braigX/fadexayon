<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/ConfigsFaq.php';
class BasicHelper
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
        $this->logger = new FileLogger(0);
        $this->logger->setFilename(_PS_ROOT_DIR_ . '/var/logs/faqop_debug.log');
    }
}
