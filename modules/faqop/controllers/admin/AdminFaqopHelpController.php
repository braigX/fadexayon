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

class AdminFaqopHelpController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
        $this->page_header_toolbar_title = $this->l('FAQ Module Help');
    }

    public function initContent()
    {
        $this->content .= $this->module->displayNav('help');
        $this->content .= $this->renderHelp();
        parent::initContent();
    }

    public function renderHelp()
    {
        return $this->module->displayHelp();
    }

    public function ajaxProcessUpdateItemsIndex()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('Error: could not reindex in demo mode'));
        }

        if ($this->module->cache_helper->recacheAllLists()) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not reindex'));
        }
    }

    public function ajaxProcessUpdateBlocksWithLanguages()
    {
        if (ConfigsFaq::DEMO_MODE) {
            $this->module->helper->throwError($this->l('Error: could not update languages for all blocks and items 
                in demo mode'));
        }

        if ($this->module->rep->updateBlocksForLanguages()) {
            exit(1);
        } else {
            $this->module->helper->throwError($this->l('Error: could not update languages for all blocks and items'));
        }
    }
}
