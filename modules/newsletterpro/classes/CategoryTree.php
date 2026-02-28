<?php
/**
* Since 2013 Ovidiu Cimpean.
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright Since 2013 Ovidiu Cimpean
* @license   Do not edit, modify or copy this file
*
* @version   Release: 4
*/

namespace PQNP;

if (!defined('_PS_VERSION_')) {
	exit;
}


use Category;
use Context;
use Db;

class CategoryTree
{
    public function getTree()
    {
        $root = Category::getRootCategory();
        $root_id = $root->id;

        $categories = Db::getInstance()->executeS('
    		SELECT c.`id_category`, cl.`name`, c.`id_parent`, c.`level_depth`, c.`active`
    		FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (
				c.`id_category` = cl.`id_category`
				AND cl.`id_lang` = '.(int) Context::getContext()->language->id.'
				AND cl.`id_shop` = '.(int) Context::getContext()->shop->id.'
			)
    	');

        $categories_ids = [];

        foreach ($categories as $category) {
            $categories_ids[$category['id_category']] = $category;
        }

        unset($categories);

        $tree = [];

        $tree = $categories_ids[$root_id];
        $this->addTreeChildrens($categories_ids, $tree);

        return $tree;
    }

    public function getRoot()
    {
        $root = Category::getRootCategory();

        return [
            'id_category' => (int) $root->id,
            'name' => $root->name,
            'id_parent' => (int) $root->id_parent,
            'level_depth' => (int) $root->level_depth,
            'active' => (int) $root->active,
            'childrens' => count($root->getChildrenWs()),
        ];
    }

    public function getChildrens($id_category)
    {
        $results = Db::getInstance()->executeS('
            SELECT c.`id_category`, cl.`name`, c.`id_parent`, c.`level_depth`, c.`active`, 
                (
                    SELECT COUNT(gc_c.`id_category`) 
                    FROM `'._DB_PREFIX_.'category` gc_c 
                    WHERE gc_c.`id_parent` = c.`id_category`
                ) AS `childrens`
            FROM `'._DB_PREFIX_.'category` c
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (
                c.`id_category` = cl.`id_category`
                AND cl.`id_lang` = '.(int) Context::getContext()->language->id.'
                AND cl.`id_shop` = '.(int) Context::getContext()->shop->id.'
            )
            WHERE c.`id_parent` = '.(int) $id_category.'
        ');

        return $results;
    }

    private function addTreeChildrens(&$categories_ids, &$node)
    {
        if (!isset($node['childrens'])) {
            $node['childrens'] = [];
        }

        foreach ($categories_ids as $id_category => $category) {
            if ($node['id_category'] == $category['id_parent']) {
                $node['childrens'][] = $category;

                if (count($node['childrens']) > 0) {
                    $category_ref = &$node['childrens'][count($node['childrens']) - 1];
                    $this->addTreeChildrens($categories_ids, $category_ref);
                }
            }
        }
    }
}
