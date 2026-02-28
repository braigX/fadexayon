<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class BulkGenerator
{
    public $compatible_criteria = ['c' => 1, 'a' => 2, 'f' => 2, 'm' => 1, 's' => 1, 'q' => 1];

    public function __construct($sp_module)
    {
        $this->context = Context::getContext();
        $this->sp = $sp_module;
        $this->af = $this->sp->af();
        $this->sp->defineSettings();
    }

    public function getSmartyVariables()
    {
        if (!isset($this->context->smarty->tpl_vars['available_languages'])) {
            $this->af->assignLanguageVariables();
        }
        $lang_fields = $this->sp->fields('configurable')['lang'];

        return [
            'criteria' => $this->getGroupedCriteria(),
            'configurable_fields' => $this->sp->decorateFields($lang_fields),
        ];
    }

    public function getGroupedCriteria()
    {
        $grouped_criteria = [];
        foreach ($this->af->getAvailableFilters(false) as $key => $f) {
            $first_char = Tools::substr($key, 0, 1);
            if (isset($this->compatible_criteria[$first_char]) && empty($f['warning'])) {
                if ($this->compatible_criteria[$first_char] == 2) {
                    if (!isset($grouped_criteria[$first_char])) {
                        $grouped_criteria[$first_char] = ['name' => $f['prefix']];
                    }
                    $grouped_criteria[$first_char]['groups'][$key] = $f;
                } else {
                    $grouped_criteria[$key] = ['name' => $f['name']];
                }
            }
        }

        return $grouped_criteria;
    }

    public function renderCriteriaOptions($type)
    {
        $smarty_vars = [
            'label' => '--',
            'name' => 'values',
            'data' => ['options' => $this->getGroupOptions($type), 'value' => []],
        ];
        if ($type == 'c') {
            $smarty_vars['data']['id_root'] = Configuration::get('PS_ROOT_CATEGORY');
            $smarty_vars['data']['checkable_root'] = false;
        }
        $this->context->smarty->assign($smarty_vars);
        $af_local_path = _PS_MODULE_DIR_ . 'amazzingfilter/';

        return $this->af->display($af_local_path, 'views/templates/admin/options.tpl');
    }

    public function getGroupOptions($type)
    {
        $controllers = ['m' => 'manufacturer', 's' => 'supplier'];
        if (isset($controllers[$type])) {
            $options = $this->af->getOptions($controllers[$type]);
        } elseif ($type == 'c') {
            // temporary: only active options
            $categories = $this->sp->db->executeS('
                SELECT DISTINCT(c.id_category), c.id_parent, cl.name
                FROM ' . _DB_PREFIX_ . 'category c ' . Shop::addSqlAssociation('category', 'c') . '
                INNER JOIN ' . _DB_PREFIX_ . 'category_lang cl
                    ON cl.id_category = c.id_category AND cl.id_lang = ' . (int) $this->sp->id_lang . '
                    AND cl.id_shop = category_shop.id_shop
                WHERE c.active = 1
                ORDER BY c.id_parent, c.position, cl.id_shop = ' . (int) $this->sp->id_shop . ' DESC
            ');
            $options = [];
            foreach ($categories as $cat) {
                $options[$cat['id_parent']][$cat['id_category']] = $cat['name'];
            }
        } elseif ($type == 'special') {
            $options = $this->af->getSpecialFilters();
        } else {
            $f = ['first_char' => $type[0], 'id_group' => (int) Tools::substr($type, 1)];
            $options = array_column($this->af->getRawFilterValues($f), 'name', 'id');
        }

        return $options;
    }

    public function ajaxAction()
    {
        $action = Tools::getValue('bulk_action_name');
        $ret = [];
        switch ($action) {
            case 'callCriteriaOptions':
                $ret['html'] = $this->renderCriteriaOptions(Tools::getValue('type'));
                break;
            case 'generate':
            case 'update':
            case 'delete':
                $this->sp->bulk_process = $action;
                $items_per_request = Tools::getValue('items_per_request', 100);
                $identifier = Tools::getValue('identifier');
                if (!$data = $this->data('get', ['identifier' => $identifier])) {
                    if (!$form_data = $this->af->parseStr(Tools::getValue('data'))) {
                        $this->af->throwError($this->sp->getText('no_data'));
                    }
                    $data = $this->prepareDataForProcessing($action, $form_data, $identifier);
                    $data['skip'] = ['already_exist' => 0, 'no_products' => 0, 'not_found' => 0];
                }
                do {
                    if ($cr_params = array_shift($data['to_process'])) {
                        $page = $this->preparePageForProcessing($cr_params, $data['fields'], $action);
                        if (!empty($page['skip'])) {
                            ++$data['skip'][$page['skip']];
                        } else {
                            $page_action = $action == 'delete' ? 'delete' : 'save';
                            $data['processed'][] = $this->sp->pageData($page_action, $page);
                        }
                    } else {
                        break;
                    }
                } while (--$items_per_request > 0);
                $ret['info'] = sprintf($this->sp->getText($action . 'd'), count($data['processed']));
                foreach (array_filter($data['skip']) as $key => $num) {
                    $ret['skip'][$key] = sprintf($this->sp->getText($key), $num);
                }
                if ($ret['complete'] = !$data['to_process']) {
                    $this->sp->sitemap()->updateAll();
                    $this->data('erase');
                } else {
                    $this->data('save', $data);
                }
                break;
        }

        return $ret ?: $this->af->throwError($this->sp->getText('no_output'));
    }

    public function prepareDataForProcessing($action, $form_data, $identifier)
    {
        $data = [
            'identifier' => $identifier,
            'to_process' => $this->af->getPossibleCombinations($form_data['criteria']),
            'processed' => [],
            'fields' => [],
        ];
        $required_fields = ['link_rewrite', 'meta_title', 'header'];
        if ($action == 'update') {
            if (!empty($form_data['upd_fields'])) {
                $form_data['fields'] = array_intersect_key($form_data['fields'], array_flip($form_data['upd_fields']));
                $required_fields = array_keys($form_data['fields']);
            }
            if (!empty($form_data['upd_langs'])) {
                foreach ($form_data['fields'] as $name => $values) {
                    $form_data['fields'][$name] = array_intersect_key($values, array_flip($form_data['upd_langs']));
                }
            }
        } elseif ($action == 'delete') {
            $form_data['fields'] = [];
        }
        foreach ($form_data['fields'] as $f_name => $multilang) {
            foreach ($multilang as $id_lang => $value) {
                if ($value !== '' || in_array($f_name, $required_fields)) {
                    $value = str_replace(array_keys($form_data['ui_vars']), $form_data['ui_vars'], $value);
                    $data['fields'][$f_name][$id_lang] = $value;
                }
            }
        }

        return $data;
    }

    public function preparePageForProcessing($cr_params, $fields, $action)
    {
        $criteria = [];
        foreach ($cr_params as $k => $id) {
            $criteria[] = $var_keys[$k] = $this->criterionKey($k, $id);
        }
        $criteria = $this->sp->criteria('sort', $criteria);
        $page = $this->sp->db->getRow('
            SELECT * FROM ' . $this->sp->sqlTable() . ' WHERE criteria = \'' . pSQL($criteria) . '\'
        ') ?: ['id_seopage' => 0, 'criteria' => $criteria, 'active' => 1];
        if ($action == 'generate') {
            if ($page['id_seopage']) {
                $page['skip'] = 'already_exist';
            } elseif (!$this->hasMatchingProducts($cr_params)) {
                $page['skip'] = 'no_products';
            }
        } elseif (!$page['id_seopage']) {
            $page['skip'] = 'not_found';
        }
        if (empty($page['skip']) && $fields) {
            $multilang_vars = $this->getVarsByCriteria($var_keys, array_keys(current($fields)));
            foreach ($fields as $f_name => $multilang_value) {
                foreach ($multilang_value as $id_lang => $f_value) {
                    $vars = isset($multilang_vars[$id_lang]) ? $multilang_vars[$id_lang] : [];
                    $f_value = !$f_value ? implode(' ', $vars) : str_replace(array_keys($vars), $vars, $f_value);
                    if ($f_name == 'link_rewrite') {
                        $f_value = Tools::str2url($f_value);
                    }
                    $page[$f_name][$id_lang] = $f_value;
                }
            }
        }

        return $page;
    }

    public function criterionKey($group, $id)
    {
        return $group == 'special' ? $id . ':1' : $group . ':' . $id;
    }

    public function getVarsByCriteria($var_keys, $lang_ids)
    {
        $vars = [];
        $l_orig = ['sp' => $this->sp->id_lang, 'af' => $this->af->id_lang];
        foreach ($lang_ids as $id_lang) {
            $this->sp->id_lang = $this->af->id_lang = $id_lang;
            foreach ($var_keys as $var => $criterion) {
                $vars[$id_lang]['{' . $var . '}'] = $this->sp->criteria('getTxt', $criterion);
            }
        }
        $this->sp->id_lang = $l_orig['sp'];
        $this->af->id_lang = $l_orig['af'];

        return $vars;
    }

    public function hasMatchingProducts($cr_params)
    {
        foreach ($cr_params as $key => $value) {
            $cr_params[$key] = 'FIND_IN_SET(\'' . pSQL($value) . '\', ' . $this->sp->sqlColumn($key[0]) . ') > 0';
        }

        return (bool) $this->sp->db->getValue('
            SELECT id_product FROM `' . bqSQL($this->af->i['table']) . '` WHERE ' . implode(' AND ', $cr_params)
        );
    }

    public function data($action, $params = [])
    {
        $ret = [];
        $path = _PS_MODULE_DIR_ . 'amazzingfilter/data/spgdata';
        switch ($action) {
            case 'get':
                $ret = file_exists($path) ? json_decode(Tools::file_get_contents($path), true) : [];
                if ($ret && $ret['identifier'] != $params['identifier'] && $this->data('canBeReset')) {
                    $this->data('erase');
                    $ret = [];
                }
                break;
            case 'save':
                $ret = file_put_contents($path, is_string($params) ? $params : json_encode($params));
                break;
            case 'canBeReset':
                $time_before_reset = 60;
                $age = time() - filemtime($path);
                $time_diff = $time_before_reset - $age;
                if (!$ret = $time_diff < 1) {
                    $this->af->throwError(sprintf($this->sp->getText('wait'), $time_diff));
                }
                break;
            case 'erase':
                $ret = file_exists($path) ? unlink($path) : true;
                break;
        }

        return $ret;
    }
}
