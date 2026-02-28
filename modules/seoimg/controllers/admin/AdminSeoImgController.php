<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
class AdminSeoimgController extends ModuleAdminController
{
    /** @var array cache filled with lang informations */
    protected static $rc;
    protected static $products;

    /** @var SeoImg */
    public $module;

    /**
     * Load the HTML form in the modalbox
     *
     * @return string
     */
    public function ajaxProcessLoadForm()
    {
        $id_objet = (int) trim(pSQL(Tools::getValue('id_rule')));
        $role = trim(pSQL(Tools::getValue('role')));
        $type = trim(pSQL(Tools::getValue('type')));
        exit($this->module->loadForm($id_objet, $role, $type));
    }

    /**
     * Delete all the rules !
     */
    public function ajaxProcessCleanUp()
    {
        if (!$this->module->active || !_PS_MODE_DEV_) {
            return;
        }

        //todo: is cache enabled is taken from parameters.php fine not from database. Is that really okay?
        if (_PS_CACHE_ENABLED_) {
            Tools::clearSmartyCache();
        }

        $rules = Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . bqSQL(SeoImg::$rules_table) . '`');
        $objects = Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . bqSQL(SeoImg::$objects_table) . '`');
        $patterns = Db::getInstance()->Execute('TRUNCATE TABLE `' . _DB_PREFIX_ . bqSQL(SeoImg::$patterns_table) . '`');
        if (!$rules || !$objects || !$patterns) {
            exit('1');
        }

        exit('0');
    }

    /**
     * Load detail of a rule
     */
    public function ajaxProcessRuleDetails()
    {
        $id_cat = (int) trim(Tools::getValue('id_cat'));
        $type = trim(pSQL(Tools::getValue('type')));
        exit($this->module->loadRuleDetails($id_cat, $type));
    }

    /**
     * Get all rules already create
     */
    public function ajaxProcessGetHistory()
    {
        $role = trim(pSQL(Tools::getValue('role')));
        $type = trim(pSQL(Tools::getValue('type')));
        exit($this->module->getHistory($type, $role));
    }

    /**
     * Switch rule status
     */
    public function ajaxProcessSwitchAction()
    {
        $id_rule = (int) trim(Tools::getValue('id_rule'));
        exit($this->module->switchAction($id_rule));
    }

    /**
     * Save all the rule
     */
    public function ajaxProcessSaveRules()
    {
        $metas = [];
        $params = Tools::getValue('params');
        $role = trim(pSQL(Tools::getValue('role')));
        $type = trim(pSQL(Tools::getValue('type')));
        $apply = (int) trim(pSQL(Tools::getValue('apply')));
        $id_rule = (int) trim(pSQL(Tools::getValue('id_rule')));
        $rule_name = '';
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        if (!empty($params)) {
            foreach ($params as &$param) {
                $name = trim($param['name']);
                $value = trim($param['value']);
                $is_meta = stristr($name, 'legend');
                $is_lang = stristr($name, 'select');
                $is_name = stristr($name, 'rule_');
                $is_cat = stristr($name, 'category');

                if ($is_lang !== false && !empty($value)) {
                    $id_lang = (int) $value;
                } elseif ($is_name !== false) {
                    $rule_name = trim(pSQL($value));
                } elseif ($is_cat !== false) {
                    $categories = explode(',', $value);
                } elseif ($is_meta !== false) {
                    $metas[] = [
                        trim(pSQL(pSQL($name))) => trim(pSQL($value)),
                    ];
                }
            }
            unset($params, $param);

            $save_rule = [
                'id_lang' => $id_lang,
                'id_shop' => (int) $this->context->shop->id,
                'name' => $rule_name,
                'type' => $type,
                'role' => $role,
                'active' => 1,
                'date_add' => date('Y-m-d H:i:s'),
            ];

            if ($apply === 1) {
                $save_rule['date_upd'] = date('Y-m-d H:i:s');
            }

            // Check param's
            if (!empty($save_rule) && !empty($metas) && (isset($categories[0]) && !Tools::isEmpty($categories[0]))) {
                if ($id_rule === 0) {
                    $id_rule = $this->module->save(SeoImg::$rules_table, $save_rule);
                } else {
                    $data = [
                        'name' => $rule_name,
                        'id_rule' => (int) $id_rule,
                    ];
                    $this->module->update(SeoImg::$rules_table, $data);
                    TinyCache::clearCache('rule_cache_' . $id_rule);
                    TinyCache::clearCache('cpt_rule_' . $id_rule);
                }

                if (!empty($id_rule) && !empty($metas)) {
                    $this->module->delete($id_rule, SeoImg::$patterns_table);
                    foreach ($metas as &$meta) {
                        foreach ($meta as $key => $value) {
                            $insert_pattern = [
                                'id_rule' => $id_rule,
                                'field' => $key,
                                'pattern' => $value,
                            ];
                            $this->module->saveObj(SeoImg::$patterns_table, $insert_pattern);
                        }
                        unset($meta, $key, $value, $insert_pattern);
                    }
                    unset($metas);

                    $this->module->delete($id_rule, SeoImg::$objects_table);
                    foreach ($categories as &$cat) {
                        $insert_category = [
                            'id_rule' => $id_rule,
                            'id_obj' => $cat,
                        ];
                        $this->module->saveObj(SeoImg::$objects_table, $insert_category);
                    }
                    unset($categories, $cat, $insert_category);

                    exit($this->module->displayConfirmation('Your SEO rule has been saved successfully'));
                } else {
                    exit($this->module->displayError('An error occurred while creating the rule (1)'));
                }
            } else {
                exit($this->module->displayError('An error occurred while creating the rule (2)'));
            }
        }
    }

    /**
     * Delete all rows from one rule
     */
    public function ajaxProcessDeleteRules()
    {
        $id_objet = (int) trim(pSQL(Tools::getValue('id')));
        $this->module->delete($id_objet);

        exit($this->module->displayConfirmation('Your SEO rule has been deleted successfully'));
    }

    /**
     * Check if it's the default rule
     */
    public function ajaxProcessDefaultRule()
    {
        $id_lang = (int) trim(pSQL(Tools::getValue('id_lang')));
        $id_objet = (int) trim(pSQL(Tools::getValue('id_rule')));
        $role = trim(pSQL(Tools::getValue('role')));
        $type = trim(pSQL(Tools::getValue('type')));
        $is_default_rule = (bool) $this->module->isDefaultRule($id_lang, $id_objet, $role, $type);
        exit("$is_default_rule");
    }

    /**
     * Reload DOM after performing an action
     * see (http://legacy.datatables.net/usage/server-side)
     *
     * @return string
     */
    public function ajaxProcessReloadData()
    {
        $filter = $order = $limit = '';
        $role = trim(pSQL(Tools::getValue('role')));
        $type = trim(pSQL(Tools::getValue('type')));
        $echo = (int) trim(pSQL(Tools::getValue('sEcho')));
        $search = trim(pSQL(Tools::getValue('sSearch')));
        $sort_col = (int) trim(pSQL(Tools::getValue('iSortCol_0')));
        $sorting_cols = (int) trim(pSQL(Tools::getValue('iSortingCols')));
        $display_start = (int) trim(pSQL(Tools::getValue('iDisplayStart')));
        $display_length = (int) trim(pSQL(Tools::getValue('iDisplayLength')));
        $columns = ['msr.id_rule', 'l.name', 's.name', 'msr.active', 'msr.date_upd'];
        $count_columns = count($columns);

        /* search column filtering */
        if (!empty($search)) {
            $filter = 'AND (';
            for ($i = 0; $i < $count_columns; ++$i) {
                $filter .= $columns[$i] . " LIKE '%" . $search . "%' OR ";
            }
            $filter = substr_replace($filter, '', -3);
            $filter .= ')';
        }

        /* Individual column filtering */
        for ($i = 0; $i < $count_columns; ++$i) {
            $search_x = trim(pSQL(Tools::getValue('search_' . $i)));
            $searchable_x = trim(pSQL(Tools::getValue('bSearchable_' . $i)));
            if ($searchable_x === 'true' && $search_x !== '') {
                $filter .= ' AND ' . $columns[$i] . " LIKE '%" . $search_x . "%' ";
            }
        }

        /* Order column filtering */
        if ($sort_col) {
            $order = 'ORDER BY ';
            for ($i = 0; $i < $sorting_cols; ++$i) {
                $sort_dir_x = trim(pSQL(Tools::getValue('sSortDir_' . $i)));
                $sort_col_x = (int) trim(pSQL(Tools::getValue('iSortCol_' . $i)));
                if ($sort_col_x) {
                    $order .= $columns[$sort_col_x - 1] . ' ' . ($sort_dir_x === 'asc' ? 'ASC' : 'DESC') . ', ';
                }
            }

            $order = substr_replace($order, '', -2);
            if (trim($order) === 'ORDER BY') {
                $order = '';
            }
        }

        /* Set limit */
        if ($display_length !== -1) {
            $limit = ' LIMIT ' . $display_start . ', ' . $display_length;
        }

        $results = $this->module->getHistory($type, $role, $filter, $order, $limit);
        $total_record = $this->module->countRules($type);
        $filtered_total = count($results);

        $data = [];
        if (Shop::isFeatureActive()) {
            $columns = ['name', 'lang', 'shop', 'nb_obj', 'active', 'date_upd'];
        } else {
            $columns = ['name', 'lang', 'nb_obj', 'active', 'date_upd'];
        }

        foreach ($results as &$result) {
            $row = [];
            $row[] = $this->module->getIcon('plus');
            foreach ($result as $key => $value) {
                if ($key === 'id_lang') {
                    $id_lang = (int) $value;
                }
                if (in_array($key, $columns)) {
                    if ($key === 'nb_obj' && $value === 'All') {
                        $row[] = $this->l('All categories');
                    } elseif ($key === 'lang') {
                        $row[] = $this->module->getIcon('flag', $id_lang); /* @phpstan-ignore-line */
                    } elseif ($key === 'active') {
                        $row[] = $this->module->loadStatus($value);
                    } elseif ($key === 'date_upd') {
                        $row[] = !empty($value) ? SeoToolsImg::displayDate($value, $id_lang) : 'N/A'; /* @phpstan-ignore-line */
                    } else {
                        $row[] = $value;
                    }
                }
                if ($key === 'id_rule') {
                    $row['DT_RowId'] = 'cat_' . $value;
                }
            }
            unset($key, $value);
            $row[] = $this->module->loadActions($result, $type, $role);
            $data[] = $row;
        }
        unset($result, $results);

        $output = [
            'sEcho' => $echo,
            'iTotalRecords' => $total_record,
            'iTotalDisplayRecords' => $filtered_total,
            'aaData' => $data,
        ];
        exit(json_encode($output));
    }

    /**
     * Read counter cache
     */
    public function ajaxProcessGetProgress()
    {
        $result = [];
        $id_rule = (int) trim(pSQL(Tools::getValue('id_rule')));
        $result['value'] = (int) TinyCache::getCache('cpt_rule_' . $id_rule); /* @phpstan-ignore-line */
        echo json_encode($result);
    }

    /**
     * Applies a rule
     */
    public function ajaxProcessGenerateRule()
    {
        $id_rule = (int) trim(pSQL(Tools::getValue('id_rule')));

        self::$rc = TinyCache::getCache('rule_cache_' . $id_rule);
        if (self::$rc === null || empty(self::$rc)) {
            /* Get all rules informations */
            self::$rc = SeoToolsImg::mergeRecursive($this->module->getPatternsRule($id_rule));
            TinyCache::setCache('rule_cache_' . $id_rule, self::$rc);
        }

        $limit = 1000;
        $id_lang = (int) self::$rc['id_lang'];
        $page = (int) trim(pSQL(Tools::getValue('page', 1)));
        $nb_prod = (int) trim(pSQL(Tools::getValue('batch', 0)));

        $id_employee = isset($this->context->employee) ? $this->context->employee->id : '';
        $id_shop = isset(self::$rc['id_shop']) ? (int) self::$rc['id_shop'] : $this->context->shop->id;

        Cache::store('hook_module_exec_list_' . $id_shop . $id_employee, []);

        if (!empty(self::$rc)) {
            $id_category = '';
            $error = [];
            $get_obj = $this->module->getObjectsRule($id_rule);
            foreach ($get_obj as &$obj) {
                $id_category .= (int) $obj['id_obj'] . ', ';
            }
            unset($get_obj, $obj);
            $id_category = Tools::substr($id_category, 0, -2);

            $message = '<b>' . $this->l('Rule') . ': ' . $this->module->getRuleName($id_rule) . '</b><br />';
            $products = SeoToolsImg::getProducts($id_lang, $page, $limit, 'id_product', 'ASC', $id_category);
            $max_pages = SeoToolsImg::getMaxPages($limit);
            if ($page > $max_pages['count']) {
                $page = $max_pages['count'];
            }

            // Fix in case of FOUND_ROWS return 0 but we have product...
            $count_prod = count($products);
            if ($max_pages['max_result'] == 0 && $count_prod > 0) {
                $max_pages['max_result'] = $count_prod;
            }

            self::$products[$id_rule] = $products;
            $calc = 100;
            if ($max_pages['max_result'] > 0) {
                foreach (self::$products[$id_rule] as &$row) {
                    $id = (int) $row['id_product'];
                    $generate = $this->module->generate($id, self::$rc['pattern'], (int) $id_shop, (int) $id_lang);
                    if (!empty($generate) && $generate !== 1) {
                        $error[(int) $row['id_product']] = $generate;
                    } else {
                        ++$nb_prod;
                    }
                    $calc = round((($nb_prod / $max_pages['max_result']) * 100), 2);
                }
                unset($row);

                if ($page === (int) $max_pages['count']) {
                    $message .= $nb_prod . '/' . $max_pages['max_result'] . ' ';
                    $message .= $this->l('product(s) have been updated') . '.<br />';
                    if (!empty($error)) {
                        $message .= '<br />' . count($error) . ' ' . $this->l('error(s) encountered:') . '<br />';
                        foreach ($error as $key => $value) {
                            $message .= $value . ' (' . $key . ')<br />';
                        }
                        unset($error, $key, $value);
                    }
                }
            } else {
                $calc = 100;
                $str = "Your products don't have targeted category as default category in this rule";
                $message .= $this->l($str) . '.<br />';
                $url = 'doc.prestashop.com/display/';
                $url .= "PS16/Managing+Products#ManagingProducts-ManagingtheProduct\'sAssociations";
                $message .= '<a href="' . $url . '">' . $this->l("See Managing the Product's Associations") . '.</a><br />';
            }

            $this->module->updateApply($id_rule);

            Cache::clean('hook_module_exec_list_*');

            exit(json_encode([
                'page' => $page,
                'pourcent' => $calc,
                'message' => $message,
                'batch' => $nb_prod,
                'max_pages' => $max_pages['count'],
                'max_result' => $max_pages['max_result'],
            ]));
        }
    }
}
