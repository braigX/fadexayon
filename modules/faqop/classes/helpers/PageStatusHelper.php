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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/BasicHelper.php';
require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/MetaPageHelper.php';
class PageStatusHelper extends BasicHelper
{
    public $metaPageHelper;

    public function __construct($module)
    {
        parent::__construct($module);
        $this->metaPageHelper = new MetaPageHelper($module);
    }

    public function togglePageStatus($status)
    {
        if ($status) {
            return $this->deleteMetaPage();
        }

        return $this->createMetaPage();
    }

    public function createMetaPage()
    {
        $res = $this->metaPageHelper->createMetaPage();
        $res &= Configuration::updateGlobalValue('OP_FAQ_PAGE_ACTIVE', 1);

        return $res;
    }

    public function deleteMetaPage()
    {
        $res = $this->metaPageHelper->deleteMetaPage();
        $res &= Configuration::updateGlobalValue('OP_FAQ_PAGE_ACTIVE', 0);

        return $res;
    }
}
