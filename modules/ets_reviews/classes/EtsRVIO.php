<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class EtsRVIO extends EtsRVCore
{
    const MOD_NAME = 'ets_reviews';
    public static $export_tables = [];
    public static $import_tables = [];
    public $id_lang_default;
    public $module;
    public $languages;
    public $forceId = 0;
    public $delete_all_data = 0;
    //choice data:
    public $rv = 0;
    public $cm = 0;
    public $rc = 0;
    public $qa = 0;
    public $qs = 0;
    public $qc = 0;
    public $mc = 0;
    public $ac = 0;
    public $product_comment_criterions = [];
    public $image_type;
    public $headers;

    static $_INSTANCE;

    public static $table = [
        'ets_rv_product_comment',
        'ets_rv_product_comment_usefulness',
        'ets_rv_comment',
        'ets_rv_comment_usefulness',
        'ets_rv_reply_comment',
        'ets_rv_reply_comment_usefulness',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $this->module = Module::getInstanceByName(self::MOD_NAME);
        $this->languages = Language::getLanguages(false);
        $this->context = Context::getContext();
    }

    public static function getInstance()
    {
        if (!self::$_INSTANCE)
            self::$_INSTANCE = new self();
        return self::$_INSTANCE;
    }

    // Export data:
    public function archiveThisFile($obj, $file, $server_path, $archive_path)
    {
        if ($obj instanceof ZipArchive) {
            if (is_dir($server_path . $file)) {
                $dir = scandir($server_path . $file);
                foreach ($dir as $folder) {
                    if ($folder[0] != '.') {
                        $this->archiveThisFile($obj, $folder, $server_path . $file . '/', $archive_path . $file . '/');
                    }
                }
            } else {
                $obj->addFile($server_path . $file, $archive_path . $file);
            }
        }
    }

    public function _count($table, $qa, $answer)
    {
        $dq = new DbQuery();
        $dq
            ->select('COUNT(*)')
            ->from($table, 'a');

        if (in_array($table, self::$table)) {
            $dq
                ->where('a.question=' . (int)$qa);
        }
        switch ($table) {
            case 'ets_rv_comment_usefulness':
                $dq
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment=a.id_ets_rv_comment')
                    ->where('cm.answer=' . (int)$answer);
                break;
            case 'ets_rv_reply_comment_usefulness':
                $dq
                    ->leftJoin('ets_rv_reply_comment', 'rm', 'rm.id_ets_rv_reply_comment=a.id_ets_rv_reply_comment')
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment=rm.id_ets_rv_comment')
                    ->where('cm.answer=' . (int)$answer)
                    ->where('rm.id_ets_rv_reply_comment is NOT NULL AND cm.id_ets_rv_comment is NOT NULL');
                break;
        }

        return Db::getInstance()->getValue($dq, false);
    }

    public function addFileXMl($def, $qa = 0, $answer = 0)
    {

        if (!($count = $this->_count($def['table'], $qa, $answer)))
            return $this->emptyXML();

        self::$export_tables[$def['table']] = $count;

        return $this->exportData($def, $qa, $answer);
    }

    public function toSQL($table, $qa = 0, $answer = 0)
    {
        $qd = new DbQuery();
        $qd
            ->select('a.*')
            ->from(pSQL($table), 'a');
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . bqSQL($table) . '`', true, false);
        if ($columns) {
            foreach ($columns as $column) {
                $key = $column['Field'];
                switch ($key) {
                    case 'id_customer':
                        $qd
                            ->select('c.email `customer_email`')
                            ->leftJoin('customer', 'c', 'c.id_customer = a.id_customer');
                        break;
                    case 'employee':
                        $qd
                            ->select('e.email `employee_email`')
                            ->leftJoin('employee', 'e', 'e.id_employee = a.employee');
                        break;
                    case 'question':
                        $qd
                            ->where('a.question=' . (int)$qa);
                        break;
                    case 'answer':
                        $qd
                            ->where('a.answer=' . (int)$answer);
                        break;
                }
            }
        }
        switch ($table) {
            case 'ets_rv_comment_usefulness':
                $qd
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment=a.id_ets_rv_comment')
                    ->where('cm.answer=' . (int)$answer);
                break;
            case 'ets_rv_reply_comment_usefulness':
                $qd
                    ->leftJoin('ets_rv_reply_comment', 'rm', 'rm.id_ets_rv_reply_comment=a.id_ets_rv_reply_comment')
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment=rm.id_ets_rv_comment')
                    ->where('cm.answer=' . (int)$answer)
                    ->where('rm.id_ets_rv_reply_comment is NOT NULL AND cm.id_ets_rv_comment is NOT NULL');
                break;
        }
        return $qd;
    }

    public function exportData($def, $qa = 0, $answer = 0)
    {
        if (($rows = Db::getInstance()->executeS($this->toSQL($def['table'], $qa, $answer), true, false)) && !empty($def['multilang'])) {
            foreach ($rows as &$raw) {
                $this->exportLang($def['table'], $def['primary'], $raw);
            }
        }
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_output .= EtsRVTools::htmlOpenTag('entity_profile') . PHP_EOL;
        $fields = $def['fields'];
        if ($rows) {
            foreach ($rows as $item) {
                $xml_output .= '<' . $def['table'] . '>' . PHP_EOL;
                $xml_output .= '<' . $def['primary'] . '><![CDATA[' . $item[$def['primary']] . ']]></' . $def['primary'] . '>' . PHP_EOL;
                if ($fields)
                    $xml_output .= $this->exportFields($item, $fields, false, ['languages']);
                if (!empty($item['languages'])) {
                    $this->exportFieldLang($item['languages'], $fields, [], $xml_output);
                }
                $xml_output .= '</' . $def['table'] . '>' . PHP_EOL;
            }
        }
        $xml_output .= EtsRVTools::htmlCloseTag('entity_profile') . PHP_EOL;

        // format data xml:
        return $rows ? $this->sanitizeXML($xml_output) : $xml_output;
    }

    public function emptyXML()
    {
        return false;
    }

    public function addFileXMl14($table, $primary = '', $multiLang = false, $qa = 0, $answer = 0)
    {
        if (!($count = $this->_count($table, $qa, $answer)))
            return $this->emptyXML();

        self::$export_tables[$table] = $count;

        return preg_match('/(publish_lang|origin_lang)$/', $table) ? $this->exportDataLang($table, $qa, $answer) : $this->exportData14($table, $primary, $multiLang, $qa, $answer);
    }

    public function geneInfoXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><!-- Copyright PrestaHero -->' . EtsRVTools::htmlOpenTag('info') . EtsRVTools::htmlCloseTag('info'));
        $xml->addAttribute('export_time', date('l jS \of F Y h:i:s A'));
        $xml->addAttribute('export_source', $this->context->link->getPageLink('index', Configuration::get('PS_SSL_ENABLED')));
        $xml->addAttribute('module_version', $this->module->version);
        if (self::$export_tables) {
            foreach (self::$export_tables as $table => $count) {
                $xml->addChild($table, $count);
            }
        }
        return $xml->asXML();
    }

    public function exportLang($table, $primary, &$item)
    {
        $group_val = isset($item[$primary]) && $item[$primary] ? $item[$primary] : 0;
        if ($group_val) {
            $table .= (preg_match('/^[0-9a-z\_]+(_lang)$/i', $table) ? '' : '_lang');
            $qd = new DbQuery();
            $qd
                ->select('a.*, b.iso_code')
                ->from(pSQL($table), 'a')
                ->innerJoin('lang', 'b', 'a.id_lang = b.id_lang')
                ->where('a.' . pSQL($primary) . ' = ' . (int)$group_val);

            $item['languages'] = Db::getInstance()->executeS($qd, true, false);
        }
    }

    public function exportData14($table, $primary = '', $multiLang = false, $qa = 0, $answer = 0)
    {
        if (($rows = Db::getInstance()->executeS($this->toSQL($table, $qa, $answer), true, false)) && $multiLang && $primary) {
            foreach ($rows as &$raw)
                $this->exportLang($table, $primary, $raw);
        }
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_output .= EtsRVTools::htmlOpenTag('entity_profile') . PHP_EOL;
        if ($rows) {
            foreach ($rows as $item) {
                $xml_output .= '<' . $table . '>' . PHP_EOL;
                $xml_output .= $this->exportFields($item, [], false, ['languages']);
                if (!empty($item['languages']))
                    $this->exportFieldLang($item['languages'], [], $primary, $xml_output);
                $xml_output .= '</' . $table . '>' . PHP_EOL;
            }
        }
        $xml_output .= EtsRVTools::htmlCloseTag('entity_profile') . PHP_EOL;

        return $rows ? $this->sanitizeXML($xml_output) : $xml_output;
    }

    public function exportDataLang($table, $qa, $answer)
    {
        $qd = new DbQuery();
        $qd
            ->select('a.*, l.iso_code')
            ->from($table, 'a')
            ->leftJoin('lang', 'l', 'a.id_lang=l.id_lang');
        switch ($table) {
            case 'ets_rv_comment_origin_lang':
                $qd
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment = a.id_ets_rv_comment')
                    ->where('cm.question = ' . (int)$qa)
                    ->where('cm.answer = ' . (int)$answer)
                    ->where('cm.id_ets_rv_comment is NOT NULL');
                break;
            case 'ets_rv_reply_comment_origin_lang':
                $qd
                    ->leftJoin('ets_rv_reply_comment', 'rm', 'rm.id_ets_rv_reply_comment = a.id_ets_rv_reply_comment')
                    ->leftJoin('ets_rv_comment', 'cm', 'cm.id_ets_rv_comment = rm.id_ets_rv_comment')
                    ->where('rm.question = ' . (int)$qa)
                    ->where('cm.answer = ' . (int)$answer)
                    ->where('cm.id_ets_rv_comment is NOT NULL AND rm.id_ets_rv_reply_comment is NOT NULL');
                break;
        }
        $rows = Db::getInstance()->executeS($qd, true, false);
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_output .= EtsRVTools::htmlOpenTag('entity_profile') . PHP_EOL;
        if ($rows) {
            foreach ($rows as $item) {
                $xml_output .= '<' . $table . '>' . PHP_EOL;
                $xml_output .= $this->exportFields($item, [], false, ['id_lang']);
                $xml_output .= '</' . $table . '>' . PHP_EOL;
            }
        }
        $xml_output .= EtsRVTools::htmlCloseTag('entity_profile') . PHP_EOL;

        return $rows ? $this->sanitizeXML($xml_output) : $xml_output;
    }

    public function exportFieldLang($data, $fields, $primary, &$xml_output)
    {
        if ($data) {
            if (!$fields) {
                foreach ($data as $item) {
                    $attr_datas = ['iso_code' => $item['iso_code']];
                    if ($item['id_lang'] == $this->id_lang_default)
                        $attr_datas['default'] = 1;
                    $xml_output .= EtsRVTools::htmlOpenTag('datalanguage', $attr_datas) . PHP_EOL;
                    $xml_output .= $this->exportFields($item, [], false, ['iso_code', $primary, 'id_lang'], true);
                    $xml_output .= EtsRVTools::htmlCloseTag('datalanguage') . PHP_EOL;
                }
            } else {
                foreach ($data as $item) {
                    $attr_datas = ['iso_code' => $item['iso_code']];
                    if ($item['id_lang'] == $this->id_lang_default)
                        $attr_datas['default'] = 1;
                    $xml_output .= EtsRVTools::htmlOpenTag('datalanguage', $attr_datas) . PHP_EOL;
                    $xml_output .= $this->exportFields($item, $fields, true, [], true);
                    if (isset($item['id_shop'])) {
                        $xml_output .= EtsRVTools::displayText('<![CDATA[' . $item['id_shop'] . ']]>', 'id_shop') . PHP_EOL;
                    }
                    $xml_output .= EtsRVTools::htmlCloseTag('datalanguage') . PHP_EOL;
                }
            }
        }
    }

    public function exportFields($data, $fields = [], $fieldLang = false, $ignore = [], $strip_tags = false)
    {
        $xml_output = null;
        if (!$data)
            return $xml_output;

        if (!$fields) {
            foreach ($data as $key => $val) {
                if (!in_array($key, $ignore))
                    $xml_output .= '<' . $key . '><![CDATA[' . ($strip_tags ? $this->stripTags($val) : $val) . ']]></' . $key . '>' . PHP_EOL;
            }
        } else {
            foreach ($fields as $prop => $field) {
                if ($fieldLang && !empty($field['lang']) || !$fieldLang && empty($field['lang'])) {
                    $xml_output .= '<' . $prop . '><![CDATA[' . (isset($data[$prop]) ? (trim($field['type']) == ObjectModel::TYPE_HTML || $strip_tags ? $this->stripTags($data[$prop]) : $data[$prop]) : '') . ']]></' . $prop . '>' . PHP_EOL;
                }
                if (!$fieldLang) {
                    switch ($prop) {
                        case 'id_customer':
                            $xml_output .= EtsRVTools::displayText('<![CDATA[' . trim($data['customer_email']) . ']]>', 'customer_email') . PHP_EOL;
                            break;
                        case 'employee':
                            $xml_output .= EtsRVTools::displayText('<![CDATA[' . trim($data['employee_email']) . ']]>', 'employee_email') . PHP_EOL;
                            break;
                    }
                }
            }
        }

        return $xml_output;
    }
    // End:

    // Export configs:
    public function exportConfig($key, $config, &$xml_output)
    {
        $id_lang_default = Configuration::get('PS_LANG_DEFAULT');
        $languages = Language::getLanguages(false);
        if (isset($config['lang']) && $config['lang'] && $languages) {
            $xml_output .= '<' . $key . '>' . PHP_EOL;
            foreach ($languages as $l) {
                $attr_datas = [
                    'iso_code' => $l['iso_code'],
                    'default' => (int)$l['id_lang'] == (int)$id_lang_default ? 1 : 0
                ];
                $xml_output .= EtsRVTools::htmlOpenTag('datalanguage', $attr_datas) . PHP_EOL;
                $xml_output .= '<![CDATA[' . Configuration::get($key, (int)$l['id_lang']) . ']]>';
                $xml_output .= EtsRVTools::htmlCloseTag('datalanguage') . PHP_EOL;
            }
            $xml_output .= '</' . $key . '>' . PHP_EOL;
        } else
            $xml_output .= '<' . $key . '><![CDATA[' . Configuration::get($key) . ']]></' . $key . '>' . PHP_EOL;
    }

    public function geneConfigXML()
    {
        if (!($configs = EtsRVDefines::getInstance()->getConfigs()))
            return false;
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_output .= EtsRVTools::htmlOpenTag('config') . PHP_EOL;
        $ik = 0;
        foreach ($configs as $config) {
            if (isset($config['tab']) && $config['tab'] != 'import_export' && isset($config['name'])) {
                $this->exportConfig($config['name'], $config, $xml_output);
                $ik++;
            }
        }
        $xml_output .= EtsRVTools::htmlCloseTag('config');

        self::$export_tables['configuration'] = $ik;

        return $xml_output;
    }
    // End:

    // Common:
    public function stripTags($string)
    {
        $string = preg_replace('/<' . 'script\b[^>]*>(.*?)<\/script>/is', "", $string);
        $string = preg_replace('/<' . 'script\b[^>]*>(.*?)<(?<!\/script)(.+?)' . '>/is', "<'.'$3'.'>", $string);
        return $string;
    }

    public function sanitizeXML($string)
    {
        if (!empty($string)) {
            $string = preg_replace('/(\x{0004}(?:\x{201A}|\x{FFFD})(?:\x{0003}|\x{0004}).)/u', '', $string);
            $regex = '/(
                [\xC0-\xC1] # Invalid UTF-8 Bytes
                | [\xF5-\xFF] # Invalid UTF-8 Bytes
                | \xE0[\x80-\x9F] # Overlong encoding of prior code point
                | \xF0[\x80-\x8F] # Overlong encoding of prior code point
                | [\xC2-\xDF](?![\x80-\xBF]) # Invalid UTF-8 Sequence Start
                | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
                | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
                | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
                | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF] # Overlong Sequence
                | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF]) # Short 3 byte sequence
                | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2}) # Short 4 byte sequence
                | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
            )/x';
            $string = preg_replace($regex, '', $string);
            $string = $this->utf8ForXML($string);
        }
        return $string;
    }

    public function utf8ForXML($string)
    {
        return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $string);
    }
    // End:

    // Import data:

    // Configs:
    public function importXmlConfig($xml)
    {
        if (!$xml ||
            !($configs = EtsRVDefines::getInstance()->getConfigs())
        ) {
            return false;
        }
        $languages = Language::getLanguages(false);
        foreach ($configs as $config) {
            $this->importConfig($xml, $config, $languages);
        }
        return true;
    }

    private function importConfig($xml, $config, $languages)
    {
        $key = isset($config['name']) ? $config['name'] : '';
        if (property_exists($xml, $key)) {
            $global = !empty($config['global']) ? 1 : 0;
            if (isset($config['lang']) && $config['lang']) {
                $values = array();
                if ($nodes = $xml->$key->datalanguage) {
                    $note_default = array();
                    foreach ($nodes as $node) {
                        if (isset($node['default']) && $node['default'])
                            $note_default = $node;
                        if (isset($node['iso_code']) && ($id_lang = Language::getIdByIso($node['iso_code'])))
                            $values[$id_lang] = $node ? (string)$node : (isset($config['default']) ? $config['default'] : '');
                    }
                    if ($languages) {
                        foreach ($languages as $lang) {
                            if (!(isset($values[$lang['id_lang']])))
                                $values[$lang['id_lang']] = (string)$note_default;
                        }
                    }
                }
                $this->updateConf($key, $values, $global, false);
            } else {
                $node = $xml->$key;
                $this->updateConf($key, $node ? (string)$node : (isset($config['default']) ? $config['default'] : ''), $global, true);
            }
        }
    }

    public function updateConf($key, $values, $global = 0, $html = false)
    {
        return $global ? Configuration::updateGlobalValue($key, $values, $html) : Configuration::updateValue($key, $values, $html);
    }

    // End:


    public function importData($xml, $class, $def, $foreign_key = [], $ignore_fields = [], &$errors = [])
    {
        if (!$xml ||
            !$class ||
            !$def ||
            !isset($def['table']) || !trim($def['table']) ||
            !isset($def['primary']) || !trim($def['primary']) ||
            !isset($def['fields']) || !$def['fields']
        ) {
            return false;
        }
        $table = $def['table'];
        if (isset($xml->$table) && $xml->$table) {
            foreach ($xml->$table as $data) {
                if ($data) {
                    $this->addObj($class, $table, $def['primary'], $def['fields'], $data, $foreign_key, $ignore_fields, $errors);
                    if ($errors && $errors !== true)
                        return false;
                }
            }
        }
        return true;
    }

    public function importCriterionProduct($xml, &$errors = [])
    {
        if (!$xml
        ) {
            return false;
        }
        if (isset($xml->ets_rv_product_comment_criterion_product) && $xml->ets_rv_product_comment_criterion_product) {
            foreach ($xml->ets_rv_product_comment_criterion_product as $data) {
                if ($data) {
                    $fields_value = array();
                    $fields_value['id_ets_rv_product_comment_criterion'] = (int)self::$import_tables['ets_rv_product_comment_criterion'][(int)$data->id_ets_rv_product_comment_criterion];
                    $fields_value['id_product'] = Db::getInstance()->getValue(
                        (new DbQuery())
                            ->select('id_product')
                            ->from('product')
                            ->where('id_product=' . (int)$data->id_product)
                        , false);

                    if (!Db::getInstance()->insert('ets_rv_product_comment_criterion_product', $fields_value, false, false, $this->forceId ? Db::ON_DUPLICATE_KEY : Db::INSERT_IGNORE)) {
                        $errors[] = $this->l('Cannot insert into ets_rv_product_comment_criterion_product', 'EtsRVIO');
                    }
                }
            }
        }
    }

    public function importCriterionCategory($xml, &$errors = [])
    {
        if (!$xml
        ) {
            return false;
        }
        if (isset($xml->ets_rv_product_comment_criterion_category) && $xml->ets_rv_product_comment_criterion_category) {
            foreach ($xml->ets_rv_product_comment_criterion_category as $data) {
                if ($data) {
                    $fields_value = array();
                    $fields_value['id_ets_rv_product_comment_criterion'] = (int)self::$import_tables['ets_rv_product_comment_criterion'][(int)$data->id_ets_rv_product_comment_criterion];
                    $fields_value['id_category'] = Db::getInstance()->getValue(
                        (new DbQuery())
                            ->select('id_category')
                            ->from('category')
                            ->where('id_category=' . (int)$data->id_category), false);
                    if (!Db::getInstance()->insert('ets_rv_product_comment_criterion_category', $fields_value, false, false, $this->forceId ? Db::ON_DUPLICATE_KEY : Db::INSERT_IGNORE)) {
                        $errors[] = $this->l('Cannot update ets_rv_product_comment_criterion_category', 'EtsRVIO');
                    }
                }
            }
        }
    }

    public function importGrades($xml, &$errors = [])
    {
        if (!$xml
        ) {
            return false;
        }
        if (isset($xml->ets_rv_product_comment_grade) && $xml->ets_rv_product_comment_grade) {
            foreach ($xml->ets_rv_product_comment_grade as $data) {
                if ($data) {
                    $fields_value = array();
                    $fields_value['id_ets_rv_product_comment'] = (int)self::$import_tables['ets_rv_product_comment'][(int)$data->id_ets_rv_product_comment];
                    $fields_value['id_ets_rv_product_comment_criterion'] = (int)self::$import_tables['ets_rv_product_comment_criterion'][(int)$data->id_ets_rv_product_comment_criterion];
                    $fields_value['grade'] = (float)$data->grade;

                    if (!Db::getInstance()->insert('ets_rv_product_comment_grade', $fields_value, false, false, $this->forceId ? Db::ON_DUPLICATE_KEY : Db::INSERT_IGNORE)) {
                        $errors[] = $this->l('Cannot update ets_rv_product_comment_grade', 'EtsRVIO');
                    }
                }
            }
        }
    }

    public function importUsefulness($xml, $table, $foreign_key = [], &$errors = [])
    {
        if (!$xml ||
            !$table
        ) {
            return false;
        }
        if (isset($xml->$table) && $xml->$table) {
            foreach ($xml->$table as $data) {
                if ($data) {
                    $this->addData($data, $table, $foreign_key);
                }
            }
        }
    }

    public function importOriginLang($xml, $table, $primary, $foreign_key = [])
    {
        if (!$xml ||
            !$table ||
            !$primary
        ) {
            return false;
        }
        if (isset($xml->$table) && $xml->$table) {
            foreach ($xml->$table as $data) {
                if ($data) {
                    $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . bqSQL($table));
                    $fields_value = array();
                    if ($columns) {
                        foreach ($columns as $column) {
                            $field = $column['Field'];
                            if ($field !== 'id_lang') {
                                $field_type = $column['Type'];
                                if (preg_match('/^(int|tinyint)/', $field_type)) {
                                    $fields_value[$field] = (int)$data->$field;
                                } elseif (preg_match('/^(float|decimal)/', $field_type)) {
                                    $fields_value[$field] = (float)$data->$field;
                                } else {
                                    $fields_value[$field] = pSQL((string)$data->$field);
                                }
                            } else {
                                $idLang = isset($data->iso_code) && (string)$data->iso_code ? Language::getIdByIso((string)$data->iso_code) : 0;
                                $fields_value[$field] = $idLang ?: $this->id_lang_default;
                            }
                        }
                    }
                    // foreign-key:
                    $this->getForeignKey($fields_value, $data, $foreign_key);
                    if (!(int)$fields_value[$primary] || !(int)$fields_value['id_lang'])
                        continue;
                    $qd = new DbQuery();
                    $qd
                        ->select($primary)
                        ->from($table)
                        ->where('id_lang=' . (int)$fields_value['id_lang'])
                        ->where($primary . '=' . (int)$fields_value[$primary]);
                    if (Db::getInstance()->getValue($qd, false)) {
                        if ($this->forceId)
                            Db::getInstance()->update($table, $fields_value, 'id_lang=' . (int)$fields_value['id_lang'] . ' AND ' . $primary . '=' . (int)$fields_value[$primary]);
                    } else {
                        Db::getInstance()->insert($table, $fields_value);
                    }
                }
            }
        }
    }

    public function importPublishLang($xml)
    {
        if (!$xml) {
            return false;
        }
        $this->importOriginLang($xml
            , 'ets_rv_product_comment_publish_lang'
            , 'id_ets_rv_product_comment'
            , ['ets_rv_product_comment' => 'id_ets_rv_product_comment']
        );
    }

    public function addData($data, $table, $foreign_key = [])
    {
        $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . bqSQL($table));
        $fields_value = array();
        if ($columns) {
            foreach ($columns as $column) {
                $field = $column['Field'];
                $field_type = $column['Type'];
                if (preg_match('/^(int|tinyint)/', $field_type)) {
                    $fields_value[$field] = (int)$data->$field;
                } elseif (preg_match('/^(float|decimal)/', $field_type)) {
                    $fields_value[$field] = (float)$data->$field;
                } else {
                    $fields_value[$field] = pSQL((string)$data->$field);
                }
            }
        }
        // foreign-key:
        $this->getForeignKey($fields_value, $data, $foreign_key);

        $index = Db::getInstance()->executeS('
            SHOW COLUMNS
            FROM ' . _DB_PREFIX_ . bqSQL($table) . '
            WHERE `Key` = \'PRI\';
        ');
        $where = null;
        if ($index && $fields_value) {
            foreach ($index as $item) {
                $key = $item['Field'];
                $field_type = $item['Type'];
                if ($key !== 'id_lang' && isset($fields_value[$key])) {
                    if (preg_match('/^(int|tinyint)/', $field_type)) {
                        $where .= ' AND ' . $key . '=' . (int)$fields_value[$key];
                    } elseif (preg_match('/^(float|decimal)/', $field_type)) {
                        $where .= ' AND ' . $key . '=' . (float)$fields_value[$key];
                    } else {
                        $where .= ' AND ' . $key . '="' . pSQL($fields_value[$key]) . '"';
                    }
                }

            }
        }
        if ($where !== null && Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . bqSQL($table) . ' WHERE ' . ($where = '1 ' . $where), false)) {
            if ($this->forceId)
                Db::getInstance()->update($table, $fields_value, $where);
        } else {
            Db::getInstance()->insert($table, $fields_value);
        }
    }

    private function addObj($class, $table, $primary, $fields, $data, $foreign_key = array(), $ignore_fields = [], &$errors = [])
    {
        if (!@class_exists($class) ||
            !$fields ||
            !$table ||
            empty($data)
        ) {
            return false;
        }
        $obj = new $class();
        //value.
        foreach ($fields as $key => $val) {
            if ($key == 'id_shop') {
                $obj->id_shop = (int)$this->context->shop->id;
            } elseif ((!isset($val['lang']) || !$val['lang']) && isset($val['type'])) {
                switch ($val['type']) {
                    case ObjectModel::TYPE_STRING:
                    case ObjectModel::TYPE_HTML:
                        $obj->$key = (string)$data->$key;
                        break;
                    case ObjectModel::TYPE_INT:
                        $obj->$key = (int)$data->$key;
                        break;
                    case ObjectModel::TYPE_FLOAT:
                        $obj->$key = (float)$data->$key;
                        break;
                    default:
                        $obj->$key = trim($data->$key);
                        break;
                }
            }
        }
        //multi-lang
        if (isset($data->datalanguage) && $data->datalanguage) {

            // Find lang default:
            $language_xml_default = null;
            foreach ($data->datalanguage as $value) {
                if (isset($value['default']) && (int)$value['default']) {
                    $language_xml_default = $value;
                    break;
                }
            }
            // End:

            // MultiLang:
            $list_language_xml = array();
            foreach ($data->datalanguage as $language_xml) {
                $id_lang = isset($language_xml['iso_code']) && $language_xml['iso_code'] ? Language::getIdByIso((string)$language_xml['iso_code']) : false;
                if ($id_lang) {
                    $list_language_xml[] = $id_lang;
                    foreach ($fields as $key => $val) {
                        if (isset($val['lang']) && $val['lang']) {
                            $values = $obj->$key;
                            $values[$id_lang] = (string)$language_xml->$key;
                            if (!$values[$id_lang] && isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key) && $language_xml_default->$key) {
                                $values[$id_lang] = (string)$language_xml_default->$key;
                            }
                            $obj->$key = $values;
                        }
                    }
                }
            }

            foreach ($this->languages as $l) {
                if (!in_array($l['id_lang'], $list_language_xml)) {
                    foreach ($fields as $key => $val) {
                        if (isset($val['lang']) && $val['lang']) {
                            $values = $obj->$key;
                            if (isset($language_xml_default) && $language_xml_default && isset($language_xml_default->$key) && $language_xml_default->$key) {
                                $values[$l['id_lang']] = $language_xml_default->$key;
                            }
                            $obj->$key = $values;
                        }
                    }
                }
            }
            // End:
        }

        // foreign-key:
        $this->getForeignKey($obj, $data, $foreign_key);
        if ($this->forceId) {
            $obj->force_id = $this->forceId;
            $obj->id = (int)$data->$primary;
        } elseif (isset($obj->$primary) && $obj->$primary) {
            unset($obj->$primary);
        }

        // Ignore fields:
        if (!$this->forceId && $ignore_fields) {
            $qd = new DbQuery();
            $qd
                ->from($table, 'a');
            $isOk = false;
            foreach ($ignore_fields as $item) {
                if (property_exists($obj, $item) && !empty($obj->$item) && !empty($fields[$item])) {
                    if (!empty($fields[$item]['lang'])) {
                        $qd
                            ->select('b.' . $item)
                            ->leftJoin($table . '_lang', 'b', 'a.' . $primary . '=b.' . $primary)
                            ->where('b.id_lang=' . (int)$this->id_lang_default)
                            ->where('b.' . $item . '="' . pSQL($obj->$item[$this->id_lang_default]) . '"');
                    } else {
                        $qd
                            ->select('a.' . $item)
                            ->where('a.' . $item . '="' . pSQL($obj->$item) . '"');
                    }
                    $isOk = true;
                }
            }
            if ($isOk && Db::getInstance()->getValue($qd, false)) {
                if ((int)$data->$primary)
                    self::$import_tables[$table][(int)$data->$primary] = (int)$data->$primary;
                return true;
            }
        }
        // End:

        // Copy image object EtsRVProductCommentImage:
        if ($obj instanceof EtsRVProductCommentImage && $obj->image) {
            if (!@is_dir(($dest = _PS_IMG_DIR_ . $this->module->name . '/r/')))
                @mkdir($dest, 0755, true);
            $imageTypes = EtsRVProductCommentImage::getImageTypes();
            if ($imageTypes) {
                foreach ($imageTypes as $type) {
                    if (@file_exists(($dest_file = $dest . $obj->image . '-' . $type['name'] . '.jpg')))
                        @unlink($dest_file);
                    if (@file_exists(($src_file = _PS_CACHE_DIR_ . '/' . $this->module->name . '/data/img/r/' . $obj->image . '-' . $type['name'] . '.jpg')))
                        @copy($src_file, $dest_file);
                }
            }
        }
        // End:
        // Remove avatar:
        if ($obj instanceof EtsRVProductCommentCustomer && $obj->avatar) {
            $obj->avatar = '';
            if (!$obj->id_customer) {
                self::$import_tables[$table][(int)$data->$primary] = (int)$data->$primary;

                return true;
            }
        }
        // End:

        $error = $obj->validateFields(false, true);
        if ($error && $error !== true) {
            $errors[] = $error;

            return false;
        } else {
            $res = true;
            try {
                if (!$this->itemExist($table, $primary, (int)$data->$primary)) {
                    $res &= $obj->add(false, true);
                } else {
                    $res &= $obj->update(true);
                }
            } catch (Exception $exception) {
                PrestaShopLogger::addLog($exception->getMessage(), 1, null, $class, $obj->id);
                return false;
            }
            if ($res && (int)$data->$primary) {
                self::$import_tables[$table][(int)$data->$primary] = $obj->id;
            }
            return $res;
        }
    }

    public function getForeignKey(&$obj, $data, $foreign_key)
    {
        //foreign.
        if ($foreign_key) {
            foreach ($foreign_key as $foreign_table => $id_foreign_key) {
                if (isset(self::$import_tables[$foreign_table]) && self::$import_tables[$foreign_table]) {
                    $foreign_value = isset(self::$import_tables[$foreign_table][(int)$data->$id_foreign_key]) ? self::$import_tables[$foreign_table][(int)$data->$id_foreign_key] : 0;
                } else {
                    $qd = new DbQuery();
                    $qd
                        ->select($id_foreign_key)
                        ->from($foreign_table);
                    switch ($id_foreign_key) {
                        case 'id_customer':
                            $qd
                                ->where('email="' . trim((string)$data->customer_email) . '"');
                            break;
                        case 'id_employee':
                            $qd
                                ->where('email="' . trim((string)$data->employee_email) . '"');
                            break;
                        default:
                            $qd
                                ->where($id_foreign_key . '=' . (int)$data->$id_foreign_key);
                            break;
                    }
                    $foreign_value = (int)Db::getInstance()->getValue($qd, false);
                }
                if (is_object($obj)) {
                    if ($id_foreign_key !== 'id_employee')
                        $obj->$id_foreign_key = $foreign_value;
                    else
                        $obj->employee = $foreign_value;
                } else {
                    $obj[$id_foreign_key] = $foreign_value;
                }
            }
        }
    }

    public function itemExist($table, $primary, $id)
    {
        if (!$table ||
            !$primary ||
            !$id ||
            !Validate::isUnsignedInt($id)
        ) {
            return false;
        }
        return (int)Db::getInstance()->getValue(
            (new DbQuery())
                ->select($primary)
                ->from($table)
                ->where($primary . '=' . $id), false);
    }

    // End:
    public static function deleteProductCommentCriterion($table)
    {
        if (!$table || !Validate::isTableOrIdentifier($table)) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_' . bqSQL($table) . ' WHERE id_ets_rv_product_comment_criterion IN (SELECT id_ets_rv_product_comment_criterion FROM ' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion)', false);
    }

    public static function deleteProductComment($table, $question = 0)
    {
        if (!$table || !Validate::isTableOrIdentifier($table)) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ets_rv_product_comment_' . bqSQL($table) . ' WHERE 1 ' . ($table == 'usefulness' ? ' AND question=' . (int)$question : '') . ' AND id_ets_rv_product_comment IN (SELECT id_ets_rv_product_comment FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment` WHERE question=' . (int)$question . ')', false);
    }

    public static function deleteComment($table, $question = 0, $answer = 0)
    {
        if (!$table || !Validate::isTableOrIdentifier($table)) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ets_rv_comment_' . bqSQL($table) . ' WHERE 1 ' . ($table == 'usefulness' ? ' AND question=' . (int)$question : '') . ' AND id_ets_rv_comment IN (SELECT id_ets_rv_comment FROM `' . _DB_PREFIX_ . 'ets_rv_comment` WHERE question=' . (int)$question . ($question > 0 ? ' AND answer=' . (int)$answer : '') . ')', false);
    }

    public static function deleteReplyComment($table, $question = 0)
    {
        if (!$table || !Validate::isTableOrIdentifier($table)) {
            return false;
        }
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ets_rv_reply_comment_' . bqSQL($table) . ' WHERE 1 ' . ($table == 'usefulness' ? ' AND question=' . (int)$question : '') . ' AND id_ets_rv_reply_comment IN (SELECT id_ets_rv_reply_comment FROM `' . _DB_PREFIX_ . 'ets_rv_reply_comment` WHERE question=' . (int)$question . ')', false);
    }

    public static function getOldCriterions($context = null)
    {
        if ($context == null)
            $context = Context::getContext();
        if (EtsRVTools::tableExist('product_comment_criterion')) {
            return Db::getInstance()->executeS('
                SELECT * FROM `' . _DB_PREFIX_ . 'product_comment_criterion` pcc
                LEFT JOIN `' . _DB_PREFIX_ . 'product_comment_criterion_lang` pccl ON (pcc.id_product_comment_criterion=pccl.id_product_comment_criterion AND pccl.id_lang="' . (int)$context->language->id . '")
            ');
        }
    }

    public static function getNewCriterions($context = null)
    {
        if ($context == null)
            $context = Context::getContext();
        return Db::getInstance()->executeS('
            SELECT * FROM `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion` pcc
            LEFT JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_lang` pccl ON (pcc.id_ets_rv_product_comment_criterion=pccl.id_ets_rv_product_comment_criterion AND pccl.id_lang="' . (int)$context->language->id . '")
            WHERE pcc.deleted=0
        ');
    }

    public function checkCreatedColumn($table, $column)
    {
        $fieldsCustomers = Db::getInstance()->executeS('DESCRIBE ' . _DB_PREFIX_ . bqSQL($table));
        $check_add = false;
        foreach ($fieldsCustomers as $field) {
            if ($field['Field'] == $column) {
                $check_add = true;
                break;
            }
        }
        return $check_add;
    }

    public function importDataPrestashop($new_criterions)
    {
        if ($new_criterions) {
            foreach ($new_criterions as $id_old => $id_value) {
                if (!$id_value) {
                    $newCriterion = new EtsRVProductCommentCriterion();
                    require_once(_PS_MODULE_DIR_ . 'productcomments/ProductCommentCriterion.php');
                    $oldCriterion = new ProductCommentCriterion($id_old);
                    $newCriterion->id_ets_rv_product_comment_criterion = $oldCriterion->id_product_comment_criterion_type;
                    $newCriterion->name = $oldCriterion->name;
                    $newCriterion->active = $oldCriterion->active;
                    if ($newCriterion->add()) {
                        if ($newCriterion->id_product_comment_criterion_type == 2) {
                            // insert criterion category;
                            Db::getInstance()->execute('
                                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion` (`id_ets_rv_product_comment_criterion`,`id_category`)
                                SELECT "' . (int)$newCriterion->id . '" as `id_ets_rv_product_comment_criterion`,`id_category` 
                                FROM `' . _DB_PREFIX_ . 'product_comment_criterion_category` 
                                WHERE `id_product_comment_criterion`=' . (int)$id_old
                            );
                        }
                        if ($newCriterion->id_product_comment_criterion_type == 3) {
                            // insert criterion product;
                            Db::getInstance()->execute('
                                INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_criterion_product` (`id_ets_rv_product_comment_criterion`,`id_product`) 
                                SELECT "' . (int)$newCriterion->id . '" as `id_ets_rv_product_comment_criterion`,`id_product` 
                                FROM `' . _DB_PREFIX_ . 'product_comment_criterion_product` 
                                WHERE `id_product_comment_criterion`=' . (int)$id_old
                            );
                        }
                    }
                }
            }
            $id_start_import = (int)Configuration::getGlobalValue('ETS_RV_MAX_ID_IMPORT_PRESTASHOP');

            // insert product comment
            $sql1 = 'SELECT id_product_comment,id_product,id_customer,id_guest,customer_name,grade,validate,deleted,"1" as publish_all_language,"0" as question,date_add,date_add as upd_date 
            FROM `' . _DB_PREFIX_ . 'product_comment`
            WHERE id_product_comment>' . (int)$id_start_import;
            if (Db::getInstance()->getRow($sql1)) {

                // edit table
                if (!$this->checkCreatedColumn('ets_rv_product_comment', 'id_old'))
                    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment` ADD `id_old` int(11)');
                if (!$this->checkCreatedColumn('ets_rv_product_comment_grade', 'count'))
                    Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade` ADD `count` int(11)');
                Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment`(`id_old`,`id_product`,`id_customer`,`id_guest`,`customer_name`,`grade`,`validate`,`deleted`,`publish_all_language`,`question`,`verified_purchase`,`date_add`,`upd_date`)
                    SELECT `id_product_comment`,`id_product`,`id_customer`,`id_guest`,`customer_name`,`grade`,`validate`,`deleted`, 1 as `publish_all_language`,"0" as `question`, \'auto\' as `verified_purchase`,`date_add`,`date_add` as `upd_date`
                    FROM  `' . _DB_PREFIX_ . 'product_comment` pc
                    WHERE pc.id_product_comment>' . (int)$id_start_import
                );

                // insert product comment lang
                Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_lang` (id_ets_rv_product_comment,id_lang,title,content)
                    SELECT epc.id_ets_rv_product_comment, l.id_lang, pc.title, pc.content 
                    FROM `' . _DB_PREFIX_ . 'lang`l ,`' . _DB_PREFIX_ . 'product_comment` pc 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` epc ON ( epc.id_old = pc.id_product_comment )
                    WHERE pc.id_product_comment>' . (int)$id_start_import
                );

                // insert product comment origin lang
                Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_origin_lang` (id_ets_rv_product_comment,id_lang,title,content)
                    SELECT epc.id_ets_rv_product_comment, "' . (int)Configuration::get('PS_LANG_DEFAULT') . '" as id_lang, pc.title, pc.content 
                    FROM `' . _DB_PREFIX_ . 'product_comment` pc 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` epc ON ( epc.id_old = pc.id_product_comment )
                    WHERE pc.id_product_comment > ' . (int)$id_start_import
                );

                // insert product comment usefulness;
                Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_usefulness`(id_ets_rv_product_comment,id_customer,usefulness,employee,question) 
                    SELECT epc.id_ets_rv_product_comment,pcu.id_customer,pcu.usefulness,"0" as employee,"0" as question 
                    FROM `' . _DB_PREFIX_ . 'product_comment_usefulness` pcu 
                    INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` epc ON (epc.id_old =pcu.id_product_comment)
                    WHERE pcu.id_product_comment >' . (int)$id_start_import
                );

                // insert product comment grade;
                $sql6 = 'SELECT epc.id_ets_rv_product_comment,pcg.id_product_comment_criterion,pcg.grade 
                FROM `' . _DB_PREFIX_ . 'product_comment_grade` pcg 
                INNER JOIN `' . _DB_PREFIX_ . 'ets_rv_product_comment` epc ON (pcg.id_product_comment= epc.id_old)
                WHERE pcg.id_product_comment >' . (int)$id_start_import;
                $productComments = Db::getInstance()->executeS($sql6);
                if ($productComments) {
                    foreach ($productComments as $productComment) {
                        $id_comment = (int)$productComment['id_ets_rv_product_comment'];
                        $id_product_comment_criterion = (int)$new_criterions[$productComment['id_product_comment_criterion']];
                        $grade = (int)$productComment['grade'];
                        Db::getInstance()->execute('
                            INSERT INTO `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade`(id_ets_rv_product_comment,id_ets_rv_product_comment_criterion,grade,`count`) 
                            VALUES("' . (int)$id_comment . '","' . (int)$id_product_comment_criterion . '","' . (int)$grade . '",1) 
                            ON DUPLICATE KEY UPDATE grade=grade+' . (int)$grade . ', `count`=`count`+1
                        ');
                    }
                }
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade` SET grade=grade/count WHERE count>1');
                // drop column table
                Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment_grade` DROP `count`');
                Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ets_rv_product_comment` DROP `id_old`');
                $maxID = (int)Db::getInstance()->getValue('SELECT MAX(id_product_comment) FROM ' . _DB_PREFIX_ . 'product_comment');
                Configuration::updateGlobalValue('ETS_RV_MAX_ID_IMPORT_PRESTASHOP', $maxID);
                die(json_encode([
                    'errors' => false,
                    'msg' => $this->l('Product review data was successfully imported', 'EtsRVIO'),
                ]));
            } else {
                die(json_encode([
                    'errors' => false,
                    'msg' => $this->l('All reviews are imported', 'EtsRVIO'),
                ]));
            }
        } else {
            die(json_encode([
                'errors' => $this->l('Criterion mapping is required', 'EtsRVIO'),
            ]));
        }
    }

    public function beforeMigrateDataCSV($data)
    {
        $newData = [];
        if ($this->headers) {
            $ik = 0;
            foreach ($this->headers as $header) {
                $newData[$header] = $data[$ik];
                $ik++;
            }
        }
        return $newData ?: $data;
    }

    public function importDataCsvOrXlsx($data, $question = 0)
    {
        $data = $this->beforeMigrateDataCSV($data);
        if (!$this->validateMigrateCSV($data)) {
            return false;
        }
        $productComment = new EtsRVProductComment();
        $productComment->id_product = (int)$data['ProductID'];
        $productComment->id_customer = isset($data['CustomerID']) ? (int)$data['CustomerID'] : 0;
        $productComment->id_guest = isset($data['CustomerID']) && (int)$data['CustomerID'] == 0 ? 1 : 0;
        $productComment->customer_name = isset($data['CustomerName']) ? $data['CustomerName'] : '';
        $productComment->email = isset($data['Email']) && Validate::isEmail($data['Email']) ? $data['Email'] : '';
        $productComment->publish_all_language = 1;
        $productComment->question = $question;
        $productComment->grade = 0;
        $productComment->id_country = isset($data['CountryID']) && Validate::isUnsignedInt($data['CountryID']) ? (int)$data['CountryID'] : 0;
        $productComment->validate = EtsRVProductComment::STATUS_APPROVE;
        $productComment->deleted = 0;
        $productComment->verified_purchase = isset($data['VerifiedPurchase']) && trim($data['VerifiedPurchase']) !== '' ? Tools::strtolower(trim($data['VerifiedPurchase'])) : 'auto';
        $productComment->date_add = (isset($data['DateAdd']) && trim($data['DateAdd']) !== '' && Validate::isDate($data['DateAdd']) ? $data['DateAdd'] : date('Y-m-d H:i:s'));
        $productComment->upd_date = $productComment->date_add;
        $title = isset($data['Title']) ? $data['Title'] : '';
        $content = isset($data['Content']) ? $data['Content'] : '';
        $originIsoLangId = isset($data['IsoLang']) && Language::getIdByIso(Tools::strtolower($data['IsoLang']), true) ? Language::getIdByIso(Tools::strtolower($data['IsoLang']), true) : (int)Configuration::get('PS_LANG_DEFAULT');
        if ($this->languages) {
            foreach ($this->languages as $l) {
                if ($originIsoLangId == (int)$l['id_lang'] || (int)$l['id_lang'] == (int)Configuration::get('PS_LANG_DEFAULT')) {
                    $productComment->title[$l['id_lang']] = $title;
                    $productComment->content[$l['id_lang']] = $content;
                } else {
                    $productComment->title[$l['id_lang']] = '';
                    $productComment->content[$l['id_lang']] = '';
                }
            }
        }
        if ($productComment->save(true, false)) {
            EtsRVProductComment::saveOriginLang($productComment->id, $originIsoLangId, $title, $content);
            if (!$productComment->publish_all_language) {
                EtsRVProductComment::savePublishLang($productComment->id, array($this->languages));
            }
            if (!empty($data['Criterions']) && !$question)
                $this->addCommentGrates($productComment, $data['Criterions']);

            if (!empty($data['Images']) && !$question) {
                foreach ($data['Images'] as $img) {
                    list($imageSourceUrl, $filetype) = $img;
                    $image = new EtsRVProductCommentImage();
                    $image->id_ets_rv_product_comment = (int)$productComment->id;
                    $image->position = $image->getLastPosition((int)$productComment->id) + 1;
                    $salt = Tools::strtolower(Tools::passwdGen(32));
                    $file_dest = _PS_IMG_DIR_ . $this->module->name . '/r/';
                    $tmp_file = tempnam(_PS_TMP_IMG_DIR_, uniqid('ets_reviews', true));
                    if ($imageSourceUrl !== ''
                        && in_array($filetype, array('jpg', 'gif', 'jpeg', 'png'))
                        && self::copy($imageSourceUrl, $tmp_file)
                    ) {
                        if (!ImageManager::checkImageMemoryLimit($tmp_file)) {
                            @unlink($tmp_file);
                        } else {
                            list($sourceWidth, $sourceHeight) = @getimagesize($tmp_file);
                            if ($this->image_type) {
                                foreach ($this->image_type as $image_type) {
                                    $destinationWidth = $sourceWidth > $image_type['width'] ? $image_type['width'] : $sourceWidth;
                                    $destinationHeight = Tools::ps_round($destinationWidth * $sourceHeight) / $sourceWidth;
                                    if (!@ImageManager::resize($tmp_file, $file_dest . $salt . '-' . Tools::stripslashes($image_type['name']) . '.jpg', $destinationWidth, $destinationHeight)) {

                                    }
                                }
                            }
                            if (file_exists($tmp_file))
                                @unlink($tmp_file);
                        }
                    }
                    $image->image = $salt;
                    if (!$image->add() && $this->image_type) {
                        foreach ($this->image_type as $imageType) {
                            $file_name = $file_dest . $salt . '-' . Tools::stripslashes($imageType['name']) . '.jpg';
                            if (@file_exists($file_name))
                                unlink($file_name);
                        }
                    }
                }
            }
            if (!empty($data['Answers']) && $question) {
                $comment = new EtsRVComment();
                $comment->id_ets_rv_product_comment = $productComment->id;
                $comment->id_customer = 0;
                $comment->employee = $this->context->employee->id;
                $comment->date_add = (isset($data['DateAdd']) && trim($data['DateAdd']) !== '' && Validate::isDate($data['DateAdd']) ? $data['DateAdd'] : date('Y-m-d H:i:s'));
                $comment->upd_date = $comment->date_add;
                $comment->question = $question;
                $comment->answer = 1;
                $comment->validate = EtsRVComment::STATUS_APPROVE;
                if ($this->languages) {
                    $multiLang = (int)Configuration::get('ETS_RV_MULTILANG_ENABLED');
                    foreach ($this->languages as $l) {
                        $comment->content[(int)$l['id_lang']] = $multiLang ? $data['Answers'] : null;
                    }
                }
                if ($comment->save(true, false)) {
                    EtsRVComment::saveOriginLang($comment->id, $this->context->language->id, $data['Answers']);
                }
            }
        }

        return true;
    }

    private function addCommentGrates(EtsRVProductComment $productComment, $criterions)
    {
        $averageGrade = 0;
        foreach ($criterions as $criterionId => $grade) {
            EtsRVProductComment::addGrade($productComment->id, $criterionId, $grade);
            $averageGrade += $grade;
        }
        $averageGrade /= count($criterions);
        $productComment->grade = $averageGrade;

        return $productComment->update(true);
    }

    public function validateMigrateCSV(&$data)
    {
        //Product
        if (!isset($data['ProductID']) || !Validate::isUnsignedInt($data['ProductID']) || !Db::getInstance()->getValue('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` WHERE `id_product`=' . (int)$data['ProductID'])) {
            return false;
        }
        //Email
        if (!empty($data['Email']) && !Validate::isEmail($data['Email'])) {
            $data['Email'] = '';
        }
        //Customer
        if (!empty($data['CustomerID']) && !empty($data['Email']) && ($customer = Customer::getCustomersByEmail($data['Email']))) {
            $data['CustomerID'] = (int)$customer[0]['id_customer'];
            if (!isset($data['CustomerName']) || !$data['CustomerName']) {
                $data['CustomerName'] = $customer[0]['firstname'] . ' ' . $customer[0]['lastname'];
            }
        } else {
            $data['CustomerID'] = 0;
        }
        if (!isset($data['CustomerName']) || trim($data['CustomerName']) == '') {
            $data['CustomerName'] = 'Guest';
        }
        //Images
        if (!empty($data['Images'])) {
            $images = explode(';', $data['Images']);
            $newImages = [];
            if ($images) {
                foreach ($images as &$image) {
                    if (preg_match('/^https?:\/\/(?:.+?)(?P' . '<' . 'name' . '>' . '[^\/]+\.(?P' . '<' . 'type' . '>' . 'jpg|jpeg|png|gif))$/', $image, $m)) {
                        $newImages[] = [$image, $m['type'], $m['name']];
                    }
                }
            }
            $data['Images'] = $newImages;
        }
        //Criterions
        if (!empty($data['Criterions'])) {
            $migrateCriterions = $this->product_comment_criterions;
            $criterions = explode(';', $data['Criterions']);
            if ($migrateCriterions && $criterions) {
                $ik = 0;
                foreach ($migrateCriterions as &$migrateCriterion) {
                    if (isset($criterions[$ik]) && Validate::isUnsignedInt($criterions[$ik])) {
                        $migrateCriterion = (int)$criterions[$ik];
                    }
                    $ik++;
                }
            }
            $data['Criterions'] = $migrateCriterions;
        }
        //VerifyPurchase:
        if (isset($data['VerifiedPurchase']) && trim($data['VerifiedPurchase']) !== '' && !in_array(Tools::strtolower(trim($data['VerifiedPurchase'])), ['auto', 'yes', 'no'])) {
            $data['VerifiedPurchase'] = 'auto';
        }
        //Country
        if (isset($data['CountryIsoCode']) && trim($data['CountryIsoCode']) !== '' && Validate::isLanguageIsoCode($data['CountryIsoCode'])) {
            $data['CountryID'] = Country::getByIso($data['CountryIsoCode']) ?: 0;
        } else
            $data['CountryID'] = (int)Configuration::get('PS_COUNTRY_DEFAULT');


        //IsoLang
        if (!empty($data['IsoLang']) && !Validate::isLangIsoCode($data['IsoLang'])) {
            $data['IsoLang'] = '';
        }

        return true;
    }

    public static function copy($source, $destination, $stream_context = null)
    {
        if (null === $stream_context && !preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }
        $content = self::file_get_contents($source, false, $stream_context);
        if (!$content || strpos($content, 'head') !== false) {
            $content = Tools::file_get_contents($source, false, $stream_context);
        }
        if ($content) {
            return strpos($content, 'head') !== false ? false : @file_put_contents($destination, $content);
        }

        return false;
    }

    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60, $opts = [])
    {
        $post = is_array($opts) && count($opts) > 0 ? 1 : 0;
        if ($post) {
            $opts = http_build_query($opts);
        }
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    'method' => $post ? "POST" : "GET",
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                    'content' => $opts
                ),
                "ssl" => array(
                    "allow_self_signed" => true,
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POST => $post,
                CURLOPT_POSTFIELDS => $opts,
                //CURLOPT_POSTREDIR => 7,
                //CURLOPT_HEADER=>1,
                //CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //CURLOPT_CUSTOMREQUEST => 'POST',
                /*CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                )*/
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
}