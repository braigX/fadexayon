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
class GridHelperFaq extends HelperFaq
{
    public function getBlocksForListBasic()
    {
        $select_array = [];
        $select_array[] = '
            SELECT obi.`id` as id_item, obi.`block_type` as block_type, 
                   obi.`position` as pos, 
                   obi.`active`, obi.`hook_name`, 
                   IF(obi.`admin_name` IS NULL OR obi.`admin_name` = "", obil.`title`, obi.`admin_name`) as title, 
                   obi.`not_all_languages`, obi.`languages`, obi.`not_all_currencies`, obi.`currencies`,
                   obi.`position`, obi.`not_all_pages`, obi.`show_markup` 
            FROM ' . _DB_PREFIX_ . 'op_faq_lists obi 
            LEFT JOIN ' . _DB_PREFIX_ . 'op_faq_lists_lang obil 
            ON (obi.id = obil.id)';

        return $select_array;
    }

    public function addWhereClause($select_array, $whereClause)
    {
        $new_array = [];
        foreach ($select_array as $row) {
            $new_array[] = $row . $whereClause;
        }

        return $new_array;
    }

    public function addOrderClause($sql, $orderClause)
    {
        $sql .= $orderClause;

        return $sql;
    }

    public function getItemsForList($whereClause = false, $orderClause = false)
    {
        $id_shop = Context::getContext()->shop->id;
        $id_lang = Context::getContext()->language->id;
        $where = ' WHERE obi.id_shop = ' . (int) $id_shop .
            ' AND obil.id_lang = ' . (int) $id_lang;
        if ($whereClause) {
            $where .= $whereClause;
        }

        if (Tools::isSubmit('filterHook')) {
            $order = ' ORDER BY position';
        } elseif ($orderClause) {
            $order = $orderClause;
        } else {
            $order = ' ORDER BY title';
        }

        $select_array = $this->getBlocksForListBasic();
        $select_array = $this->addWhereClause($select_array, $where);
        $sql = implode(' UNION ', $select_array);
        $sql = $this->addOrderClause($sql, $order);

        try {
            $blocks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }

        return $blocks;
    }

    public function getBlocksForListFilter()
    {
        $whereClause = false;
        $orderClause = false;

        if (Tools::isSubmit('orderBy')) {
            $orderClause = ' ORDER BY ';
            $orderArray = [];
            if ($orderBy = Tools::getValue('orderHook')) {
                if ($orderBy == 1) {
                    $orderArray[] = 'hook_name ASC';
                } elseif ($orderBy == 2) {
                    $orderArray[] = 'hook_name DESC';
                }
                $orderArray[] = 'pos ASC';
            }
            if ($orderBy = Tools::getValue('orderTitle')) {
                if ($orderBy == 1) {
                    $orderArray[] = 'title ASC';
                } elseif ($orderBy == 2) {
                    $orderArray[] = 'title DESC';
                }
            }

            if ($orderBy = Tools::getValue('orderPos')) {
                if ($orderBy == 1) {
                    $orderArray[] = 'position ASC';
                } elseif ($orderBy == 2) {
                    $orderArray[] = 'position DESC';
                }
            }

            $orderByString = implode(',', $orderArray);
            $orderClause .= $orderByString;
        }

        if (Tools::isSubmit('filterBy')) {
            $where_array = [];
            if ($where = Tools::getValue('filterActive')) {
                if ($where == 1) {
                    $where_array[] = ' AND active = 1';
                } elseif ($where == 2) {
                    $where_array[] = ' AND active = 0';
                }
            }
            if ($where = Tools::getValue('filterHook')) {
                if ($where != 'all') {
                    if ($where == 'empty') {
                        $where_array[] = " AND hook_name = ''";
                    } else {
                        $where_array[] = " AND hook_name = '" . pSQL($where) . "'";
                    }
                }
            }

            $whereClause = implode('', $where_array);
        }

        if (Tools::isSubmit('search')) {
            $searchString = Tools::getValue('search');
            $searchString = pSQL($this->processSearchClause($searchString));

            $searchArray = explode(' ', $searchString);
            $queryArray = [];
            foreach ($searchArray as $word) {
                $queryArray[] = "IF(obi.`admin_name` IS NULL OR obi.`admin_name` = '', obil.`title`, obi.`admin_name`)"
                    . " LIKE '%" . pSQL($word) . "%'";
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
            if ($orderBy = Tools::getValue('orderHook')) {
                if ($orderBy == 1) {
                    $classNames['hook_th_class_name'] = 'active-op-s';
                } elseif ($orderBy == 2) {
                    $classNames['hook_th_class_name'] = 'active-op-s active-op-reverse';
                }
            }
            if ($orderBy = Tools::getValue('orderTitle')) {
                if ($orderBy == 1) {
                    $classNames['title_th_class_name'] = 'active-op-s';
                } elseif ($orderBy == 2) {
                    $classNames['title_th_class_name'] = 'active-op-s active-op-reverse';
                }
            }
            if ($orderBy = Tools::getValue('orderPos')) {
                if ($orderBy == 1) {
                    $classNames['pos_th_class_name'] = 'active-op-s';
                } elseif ($orderBy == 2) {
                    $classNames['pos_th_class_name'] = 'active-op-s active-op-reverse';
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

    public function getSelectedActive()
    {
        $selected_active = 0;
        if ($where = Tools::getValue('filterActive')) {
            if ($where == 1) {
                $selected_active = 1;
            } elseif ($where == 2) {
                $selected_active = 2;
            }
        }

        return $selected_active;
    }

    public function getSelectedType()
    {
        $selected_type = 'all';
        if ($where = Tools::getValue('filterType')) {
            $selected_type = $where;
        }

        return $selected_type;
    }

    public function getSelectedHook()
    {
        $selected_hook = 'all';
        if ($where = Tools::getValue('filterHook')) {
            $selected_hook = $where;
        }

        return $selected_hook;
    }

    public function getAllHooksForList()
    {
        $id_shop = Context::getContext()->shop->id;
        $sql = 'SELECT `hook_name` 
            FROM ' . _DB_PREFIX_ . 'op_faq_lists
            WHERE id_shop = ' . (int) $id_shop;

        try {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        } catch (PrestaShopDatabaseException $e) {
            echo $e->getMessage();
        }
        $final_array = [];
        foreach ($result as $row) {
            if (!empty($row['hook_name'])) {
                $final_array[$row['hook_name']] = $row['hook_name'];
            }
        }

        return $final_array;
    }
}
