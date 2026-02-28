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

require_once _PS_MODULE_DIR_ . 'faqop/classes/helpers/HelperFaq.php';
class GridItemsHelperFaq extends HelperFaq
{
    protected function getItemsForListBasicQuery()
    {
        $sql = '
            SELECT oi.`id` as id_item, oil.`title` as title
            FROM ' . _DB_PREFIX_ . 'op_faq_items oi
            LEFT JOIN ' . _DB_PREFIX_ . 'op_faq_items_lang oil ON (oi.id = oil.id)';

        return $sql;
    }

    protected function addWhereClause($sql, $whereClause)
    {
        $sql .= $whereClause;

        return $sql;
    }

    protected function addOrderClause($sql, $orderClause)
    {
        $sql .= $orderClause;

        return $sql;
    }

    protected function getItemsForList($whereClause = false, $orderClause = false)
    {
        $id_lang = Context::getContext()->language->id;
        $where = ' WHERE oil.id_lang = ' . (int) $id_lang;
        if ($whereClause) {
            $where .= $whereClause;
        }
        $order = ' ORDER BY id_item';
        if ($orderClause) {
            $order = $orderClause;
        }

        $sql = $this->getItemsForListBasicQuery();
        $sql = $this->addWhereClause($sql, $where);
        $sql = $this->addOrderClause($sql, $order);

        $items = [];

        try {
            $items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $items;
    }

    public function getItemsForListFilter()
    {
        $whereClause = false;
        $orderClause = false;

        if (Tools::isSubmit('orderBy')) {
            $orderClause = ' ORDER BY ';
            $orderArray = [];

            if ($orderBy = Tools::getValue('orderId')) {
                if ($orderBy == 1) {
                    $orderArray[] = 'id_item ASC';
                } elseif ($orderBy == 2) {
                    $orderArray[] = 'id_item DESC';
                }
            }
            if ($orderBy = Tools::getValue('orderTitle')) {
                if ($orderBy == 1) {
                    $orderArray[] = 'title ASC';
                } elseif ($orderBy == 2) {
                    $orderArray[] = 'title DESC';
                }
            }

            $orderByString = implode(',', $orderArray);
            $orderClause .= $orderByString;
        }

        if (Tools::isSubmit('search')) {
            $searchString = Tools::getValue('search');
            $searchString = pSQL($this->processSearchClause($searchString));

            $searchArray = explode(' ', $searchString);
            $queryArray = [];
            foreach ($searchArray as $word) {
                $queryArray[] = "title LIKE '%" . pSQL($word) . "%'";
            }

            $searchQuery = implode(' OR ', $queryArray);
            $searchQuery = ' AND (' . $searchQuery . ')';
            if ($whereClause) {
                $whereClause .= $searchQuery;
            } else {
                $whereClause = $searchQuery;
            }
        }

        return $this->getItemsForList($whereClause, $orderClause);
    }

    public function getClassNamesForTh()
    {
        $classNames = false;
        if (Tools::isSubmit('orderBy')) {
            $classNames = [];
            if ($orderBy = Tools::getValue('orderTitle')) {
                if ($orderBy == 1) {
                    $classNames['title_th_class_name'] = 'active-op-s';
                } elseif ($orderBy == 2) {
                    $classNames['title_th_class_name'] = 'active-op-s active-op-reverse';
                }
            }
            if ($orderBy = Tools::getValue('orderId')) {
                if ($orderBy == 1) {
                    $classNames['id_th_class_name'] = 'active-op-s';
                } elseif ($orderBy == 2) {
                    $classNames['id_th_class_name'] = 'active-op-s active-op-reverse';
                }
            }
        }

        return $classNames;
    }

    public function processSearchClause($search)
    {
        $search = str_replace('/', '', $search);
        $search = str_replace('\\', '', $search);
        $search = str_replace('\'', '', $search);

        return $search;
    }

    // add ready
    public function filterBelongingToBlock($items, $id_block, $block_type)
    {
        $new_arrray = $items;
        foreach ($items as $key => $row) {
            if ($this->module->rep->itemBelongsToBlock($row['id_item'], $id_block, $block_type)) {
                unset($new_arrray[$key]);
            }
        }

        return $new_arrray;
    }
}
