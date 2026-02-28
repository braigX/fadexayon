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
class MetaPageHelper extends BasicHelper
{
    public function createMetaPage()
    {
        if (!$this->getMetaPageId()) {
            try {
                $meta = new Meta();
                $meta->page = ConfigsFaq::PAGE;
                $languages = Language::getLanguages(false);
                foreach ($languages as $language) {
                    $meta->url_rewrite[$language['id_lang']] = ConfigsFaq::PAGE_URL;
                }

                return $meta->add();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return true;
    }

    public function deleteMetaPage()
    {
        if ($id = $this->getMetaPageId()) {
            try {
                $meta = new Meta($id);

                return $meta->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        return true;
    }

    public function getMetaPageId()
    {
        return $this->module->rep->getMetaPageId();
    }
}
