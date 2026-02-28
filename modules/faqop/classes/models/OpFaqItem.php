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

class OpFaqItem extends ObjectModel
{
    public $question;

    public $answer;

    public $title;

    public $i_class;

    public $q_class;

    public $a_class;

    public $module;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'op_faq_items',
        'primary' => 'id',
        'multilang' => true,
        'fields' => [
            'i_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'q_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            'a_class' => ['type' => self::TYPE_STRING, 'lang' => false, 'validate' => 'isCleanHtml',
                'size' => 255],
            // Lang fields
            'question' => [
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 65535],
            'answer' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 65535],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255],
        ],
    ];

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
        try {
            $res = parent::add($autodate, $null_values);
            if (Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
                $res &= $this->module->rep->addItemToBlock(
                    $this->id,
                    Tools::getValue('id_list'),
                    Tools::getValue('op_type')
                );
            }
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function update($null_values = false)
    {
        try {
            $res = parent::update($null_values);
            $res &= $this->module->cache_helper->recacheListsForItem($this->id);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        return $res;
    }

    public function delete()
    {
        $res = true;

        try {
            $res &= parent::delete();
        } catch (PrestaShopException $e) {
            echo $e->getMessage();
        }

        $res &= $this->removeAllBlocksFromItem();

        return $res;
    }

    public function removeOneItemFromBlock($id_block, $block_type)
    {
        $res = true;
        $res &= $this->module->rep->removeOneItemFromBlockDb($this->id, $id_block, $block_type);
        $res &= $this->module->cache_helper->addOneBlockToCacheTable($id_block, $block_type);

        return $res;
    }

    public function removeAllBlocksFromItemShop()
    {
        $res = true;
        $res &= $this->module->rep->removeAllBlocksFromItemDb($this->id, 'list');
        $res &= $this->module->cache_helper->recacheAllLists();

        return $res;
    }

    public function updateItemForBlocks($blocks)
    {
        $res = true;
        // get list of all blocks by item and shop
        $res &= $this->module->rep->removeAllBlocksFromItemDb($this->id, 'list');
        $res &= $this->addItemToBlocks($blocks, 'list');

        return $res;
    }

    public function addItemToBlocks($blocks, $block_type)
    {
        $res = true;
        foreach ($blocks as $id_block) {
            $res &= $this->module->rep->addItemToBlock($this->id, $id_block, $block_type);
        }

        $res &= $this->module->cache_helper->recacheListsForItemByTypeAndShop($this->id, $block_type);

        return $res;
    }

    public function deleteWithRedirect()
    {
        $res = $this->delete();
        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&deleteWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&conf=1');
        }
    }

    public function cloneWithRedirect()
    {
        $dup_helper = new DuplicateHelperFaq($this->module);
        $res = $dup_helper->duplicateOneItem($this->id);
        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&cloneWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&conf=19');
        }
    }

    public function removeWithRedirect()
    {
        if (Tools::isSubmit('id_list') && Tools::isSubmit('op_type')) {
            $res = $this->removeOneItemFromBlock(Tools::getValue('id_list'), Tools::getValue('op_type'));
        } else {
            $res = false;
        }

        if (!$res) {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&removeWrong=1');
        } else {
            Tools::redirectAdmin($this->module->helper->getItemsListUrl() . '&conf=1');
        }
    }

    public function addToPage()
    {
        $res = true;

        if (!$this->module->rep->itemBelongsToPage($this->id)) {
            $res &= $this->module->rep->addItemToPage($this->id);
            // reindex cache done inside that method
        }

        return $res;
    }

    public function removeFromPage()
    {
        $res = true;
        if ($this->module->rep->itemBelongsToPage($this->id)) {
            $res &= $this->module->rep->removeAllBlocksFromItemDb($this->id, 'page');
        }

        $res &= $this->module->cache_helper->recacheAllLists();

        return $res;
    }

    public function removeAllBlocksFromItem()
    {
        return $this->module->rep->removeAllBlocksFromItemDbWhenDelete($this->id)
            && $this->module->cache_helper->recacheAllLists();
    }
}
