<?php
/**
 * Google-Friendly FAQ Pages and Lists With Schema Markup module
 *
 * @author    Opossum Dev
 * @copyright Opossum Dev
 * @license   You are just allowed to modify this copy for your own use. You are not allowed
 * to redistribute it. License is permitted for one Prestashop instance only, except for test
 * instances.
 * Class OpFaqObjectModel is a parent model for blocks (lists)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/DuplicateHelperFaq.php';
abstract class OpFaqObjectModel extends ObjectModel
{
    public $title;

    public $active;

    public $id_shop;

    public $hook_name;

    public $show_title;

    public $block_tag;

    public $block_class;

    public $title_tag;

    public $title_class;

    public $content_tag;

    public $content_class;

    public $item_tag;

    public $item_class;

    public $question_tag;

    public $question_class;

    public $answer_tag;

    public $answer_class;

    public $show_markup;

    public $accordion;

    protected $module;

    public $block_type;

    public function __construct($module, $id_item = null, $id_lang = null, $id_shop = null)
    {
        try {
            parent::__construct($id_item, $id_lang, $id_shop);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        $this->module = $module;
    }

    public function add($autodate = true, $null_values = false)
    {
        $res = true;

        try {
            $res = parent::add($autodate, $null_values);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        $res &= $this->module->cache_helper->addOneBlockToCacheTable($this->id, $this->block_type);

        return $res;
    }

    public function delete()
    {
        try {
            $res = parent::delete();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
        if ($res) {
            $res &= $this->module->cache_helper->deleteOneBlockFromCacheTable($this->id, $this->block_type);
            if ($this->hook_name) {
                $res &= $this->module->helper->unregisterHooks($this->hook_name);
            }
            $res &= $this->module->rep->removeAllItemsFromBlock($this->id, 'list');
        }

        return $res;
    }

    public function deleteWithRedirect()
    {
        $res = $this->delete();
        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&deleteWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&conf=1');
        }
    }

    public function cloneWithRedirect()
    {
        $id_shop = Context::getContext()->shop->id;
        // we copy to the same shop
        $dup_helper = new DuplicateHelperFaq($this->module);
        $res = $dup_helper->duplicateOneBlock($this->id, $this->block_type, $id_shop, $id_shop);
        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&cloneWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&conf=19');
        }
    }

    public function updateStatus()
    {
        $res = true;
        if ($this->active == 0) {
            $res &= $this->publishOne();
        } else {
            $res &= $this->unpublishOne();
        }
        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&statusWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getBlocksListUrl() . '&conf=5');
        }
    }

    public function publishOne()
    {
        $res = true;
        if ($this->active == 0) {
            $this->active = 1;

            try {
                $res &= parent::update();
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
            $res &= $this->module->cache_helper->addOneBlockToCacheTable($this->id, $this->block_type);
        }

        return $res;
    }

    public function unpublishOne()
    {
        $res = true;
        if ($this->active == 1) {
            $this->active = 0;

            try {
                $res &= parent::update();
            } catch (PrestaShopDatabaseException $e) {
                echo $e->getMessage();
            } catch (PrestaShopException $e) {
                echo $e->getMessage();
            }
            $res &= $this->module->cache_helper->deleteOneBlockFromCacheTable($this->id, $this->block_type);
        }

        return $res;
    }

    public function update($null_values = false)
    {
        $isOldActive = $this->module->rep->isOldActive($this->id, $this->block_type);
        $oldHook = $this->module->rep->getOldHook($this->id, $this->block_type);
        $res = true;

        try {
            $res &= parent::update($null_values);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }
        if ($this->active == $isOldActive) {
            $res &= $this->module->cache_helper->deleteOneBlockFromCacheTable($this->id, $this->block_type);
            $res &= $this->module->cache_helper->addOneBlockToCacheTable($this->id, $this->block_type);
        } elseif ($this->active == 1 && !$isOldActive) {
            $res &= $this->module->cache_helper->addOneBlockToCacheTable($this->id, $this->block_type);
        } elseif ($this->active == 0 && $isOldActive) {
            $res &= $this->module->cache_helper->deleteOneBlockFromCacheTable($this->id, $this->block_type);
        }
        if ($this->hook_name !== $oldHook) {
            if ($oldHook) {
                $res &= $this->module->helper->unregisterHooks($oldHook);
            }
            if ($this->hook_name) {
                $res &= $this->module->helper->registerHooks($this->hook_name);
            }
        }

        return $res;
    }
}
