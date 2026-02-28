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
require_once __DIR__ . '/EtsSeoProduct.php';
require_once __DIR__ . '/EtsSeoCategory.php';
require_once __DIR__ . '/EtsSeoCms.php';
require_once __DIR__ . '/EtsSeoCmsCategory.php';
require_once __DIR__ . '/EtsSeoManufacturer.php';
require_once __DIR__ . '/EtsSeoSupplier.php';
require_once __DIR__ . '/EtsSeoMeta.php';
require_once __DIR__ . '/EtsSeoRedirect.php';

/**
 * Class EtsSeoImportExport.
 * This class is used for backward compatibility with previous exported files
 *
 * @property \Ets_seo $module
 */
class EtsSeoImportExport extends Module
{
    public $process = [];
    public $export_cache_path;
    public $import_cache_path;

    public $shops_selected;
    public $options_selected;
    public $l;
    public $module;
    public function __construct($shops_selected = [], $options_selected = [])
    {
        $this->name = 'ets_seo';
        parent::__construct();
        $this->module = new Ets_Seo();
        $this->process = [
            'product' => 'category',
            'category' => 'cms',
            'cms' => 'cms_category',
            'cms_category' => 'meta',
            'meta' => 'supplier',
            'supplier' => 'manufacturer',
            'manufacturer' => 'redirect',
            'redirect' => 'config',
            'config' => 'image',
            'image' => null,
        ];

        $this->export_cache_path = _PS_CACHE_DIR_ . 'ets_seo';
        $this->import_cache_path = _PS_CACHE_DIR_ . 'ets_seo';
        $this->shops_selected = $shops_selected;
        $this->options_selected = $options_selected;
    }
    public function archiveThisFile($obj, $file, $server_path, $archive_path)
    {
        if (is_dir($server_path . $file)) {
            $dir = scandir($server_path . $file);

            foreach ($dir as $row) {
                if ('.' != $row[0]) {
                    $this->archiveThisFile($obj, $row, $server_path . $file . '/', $archive_path . $file . '/');
                }
            }
        } else {
            $obj->addFile($server_path . $file, $archive_path . $file);
        }
    }

    public function generateArchive()
    {
        $errors = [];
        $zip = new ZipArchive();
        $cacheDir = _PS_CACHE_DIR_ . 'ets_seo';
        $zip_file_name = 'ets_seo_' . date('dmYHis') . '.zip';
        if (true === $zip->open($cacheDir . $zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE)) {
            if (!$zip->addFromString('ets_seo_data.xml', $this->exportData())) {
                $errors[] = $this->module->l('Cannot create ets_seo_data.xml');
            }
            $zip->close();
            if (!is_file($cacheDir . $zip_file_name)) {
                $errors[] = $this->module->l(sprintf('Could not create %1s', _PS_CACHE_DIR_ . $zip_file_name));
            }
            if (!$errors) {
                if (ob_get_length() > 0) {
                    ob_end_clean();
                }

                ob_start();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: public');
                header('Content-Description: File Transfer');
                header('Content-type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
                header('Content-Transfer-Encoding: binary');
                ob_end_flush();
                if (file_exists($cacheDir . $zip_file_name)) {
                    readfile($cacheDir . $zip_file_name);
                    @unlink($cacheDir . $zip_file_name);
                }
                exit;
            }
        }

        return $errors;
    }

    protected function exportData()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<entity_profile>' . "\n";

        foreach ($this->shops_selected as $id_shop) {
            foreach ($this->options_selected as $option) {
                if ('config' !== $option) {
                    $table_name = $this->getTableNameByType($option);
                    $lists = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . (string) $table_name . ' WHERE id_shop=' . (int) $id_shop);
                    foreach ($lists as $item) {
                        $xml .= '<' . $table_name . '>';
                        foreach ($item as $col => $val) {
                            $xml .= '<' . $col . '><![CDATA[' . (string) $val . ']]></' . $col . '>';
                        }
                        $xml .= '</' . $table_name . '>';
                    }
                } else {
                    $xml .= $this->exportConfig($id_shop);
                }
            }
        }
        $xml .= '</entity_profile>' . "\n";
        $xml = str_replace('&', 'and', $xml);

        return $xml;
    }

    public function getTableNameByType($table_type)
    {
        $table_name = '';
        switch ($table_type) {
            case 'product':
                $table_name = 'ets_seo_product';
                break;

            case 'category':
                $table_name = 'ets_seo_category';
                break;

            case 'cms':
                $table_name = 'ets_seo_cms';
                break;

            case 'cms_category':
                $table_name = 'ets_seo_cms_category';
                break;

            case 'meta':
                $table_name = 'ets_seo_meta';
                break;

            case 'manufacturer':
                $table_name = 'ets_seo_manufacturer';
                break;

            case 'supplier':
                $table_name = 'ets_seo_supplier';
                break;

            case 'redirect':
                $table_name = 'ets_seo_redirect';
                break;
            case 'config':
                $table_name = 'configuration';
                break;
        }

        return $table_name;
    }

    protected function exportConfig($id_shop)
    {
        $seoDef = Ets_Seo_Define::getInstance();
        $configs = $seoDef->fields_config();
        $languages = Language::getLanguages(false);

        $xml = '';
        foreach ($configs as $group) {
            foreach ($group as $k => $config) {
                if (isset($config['textlang']) || isset($config['textareaLang']) || isset($config['selectLang'])) {
                    $xml .= '<configuration>';
                    $xml .= '<name><![CDATA[' . $k . ']]></name>';
                    $xml .= '<id_shop>' . (int) $id_shop . '</id_shop>';
                    foreach ($languages as $lang) {
                        $xml .= '<language>';
                        $xml .= '<id_lang>' . (int) $lang['id_lang'] . '</id_lang>';
                        $xml .= '<iso_code>' . (string) $lang['iso_code'] . '</iso_code>';
                        $xml .= '<value><![CDATA[' . (string) Configuration::get($k, $lang['id_lang'], null, (int) $id_shop) . ']]></value>';
                        $xml .= '</language>';
                    }
                    $xml .= '</configuration>';
                } else {
                    $xml .= '<configuration>';
                    $xml .= '<name><![CDATA[' . $k . ']]></name>';
                    $xml .= '<id_shop>' . (int) $id_shop . '</id_shop>';
                    $xml .= '<value><![CDATA[' . (string) Configuration::get($k, null, null, (int) $id_shop) . ']]></value>';
                    $xml .= '</configuration>';
                }
            }
        }
        $urlRules = $seoDef->url_rules();
        foreach ($urlRules as $k => $rule) {
            if ($rule) {
            }
            $xml .= '<configuration>';
            $xml .= '<name>PS_ROUTE_' . $k . '</name>';
            $xml .= '<id_shop>' . (int) $id_shop . '</id_shop>';
            $xml .= '<value><![CDATA[' . (string) Configuration::get('PS_ROUTE_' . $k, null, null, (int) $id_shop) . ']]></value>';
            $xml .= '</configuration>';
        }

        return $xml;
    }

    public function processImport($zipfile = false)
    {
        $errors = [];
        if ($_FILES['import_file']) {
            $savePath = _PS_ROOT_DIR_ . '/cache/ets_seo/';
            if (!is_dir($savePath)) {
                mkdir($savePath, 0755);
            }
            if (!$zipfile) {
                if (@file_exists($savePath . 'ets_seo_data.zip')) {
                    @unlink($savePath . 'ets_seo_data.zip');
                }

                $uploader = new Uploader('import_file');
                $uploader->setMaxSize(1048576000);
                $uploader->setAcceptTypes(['zip']);
                $uploader->setSavePath($savePath);
                $file = $uploader->process();
                if(!empty($file[0]['save_path']))
                    $extractUrl = $file[0]['save_path'];
                else
                    $extractUrl = $savePath.'ets_seo_data.zip';
                if (0 === $file[0]['error']) {
                    if (!Tools::ZipTest($extractUrl)) {
                        $errors[] = $this->module->l('Zip file seems to be broken');
                    }
                } else {
                    $errors[] = $file[0]['error'];
                }
            } else {
                $extractUrl = $zipfile;
            }
            if (!@file_exists($extractUrl)) {
                $errors[] = $this->module->l('Zip file doesn\'t exist');
            }
            if (!$errors) {
                $zip = new ZipArchive();
                if (true === $zip->open($extractUrl)) {
                    if (false === $zip->locateName('ets_seo_data.xml')) {
                        $errors[] = $this->module->l('ets_seo_data.xml doesn\'t exist');
                        if (file_exists($extractUrl) && !$zipfile) {
                            @unlink($extractUrl);
                        }
                    }
                } else {
                    $errors[] = $this->module->l('Cannot open zip file. It might be broken or damaged');
                }
            }
            if (!$errors) {
                if (@file_exists($savePath . 'ets_seo_data.xml')) {
                    @unlink($savePath . 'ets_seo_data.xml');
                }
                if (!Tools::ZipExtract($extractUrl, $savePath)) {
                    $errors[] = $this->module->l('Cannot extract zip data');
                }
                if (!@file_exists($savePath . 'ets_seo_data.xml')) {
                    $errors[] = $this->module->l('Neither ets_seo_data.xml exist');
                }
            }
            if (!$errors) {
                if (@file_exists($savePath . 'ets_seo_data.xml')) {
                    $this->importData($savePath . 'ets_seo_data.xml');
                    @unlink($savePath . 'ets_seo_data.xml');
                }
                if(isset($zip))
                    $zip->close();
                if (@file_exists($extractUrl)) {
                    @unlink($extractUrl);
                }
            }
        } else {
            $errors[] = $this->module->l('Data import is null');
        }

        return $errors;
    }

    /**
     * importData.
     *
     * @param string $file_xml path to data xml file
     *
     * @return void
     */
    public function importData($file_xml)
    {
        $configImgName = [
            'ETS_SEO_SITE_ORIG_LOGO',
            'ETS_SEO_SITE_PERSON_AVATAR',
            'ETS_SEO_FACEBOOK_FP_IMG_URL',
            'ETS_SEO_FACEBOOK_DEFULT_IMG_URL',
        ];
        if (file_exists($file_xml)) {
            $xml = simplexml_load_file($file_xml);

            foreach ($this->shops_selected as $id_shop) {
                foreach ($this->options_selected as $option) {
                    if ('config' !== $option) {
                        $table = $this->getTableNameByType($option);
                        if (isset($xml->{$table}) && $xml->{$table}) {
                            foreach ($xml->{$table} as $tbl) {
                                $col_list = [];
                                $val_list = [];
                                foreach ($tbl as $col => $val) {
                                    if ('social_img' == $col) {
                                        $col_list[] = (string) $col;
                                        if ((string) $val && !file_exists(_PS_ROOT_DIR_ . 'img/social/' . (string) $val)) {
                                            $val_list[] = null;
                                        } else {
                                            $val_list[] = (string) $val;
                                        }
                                    } elseif ($col !== 'id_' . $table) {
                                        $col_list[] = (string) $col;

                                        if ('id_lang' == $col) {
                                            $id_lang = isset($tbl->id_lang) ? (int) $tbl->id_lang : '';
                                            if (isset($tbl->id_lang, $tbl->iso_code) && $tbl->iso_code) {
                                                $id_lang = Language::getIdByIso((string)$tbl->iso_code);
                                            }
                                            $val_list[] = (int) $id_lang;
                                        } else {
                                            $val_list[] = (string) $val;
                                        }
                                    }
                                }
                                if ($col_list && $val_list) {
                                    // Force string typed value
                                    $cols = array_map(static function ($v) {
                                        return (string) $v;
                                    }, $col_list);
                                    $cols = implode(',', $cols);
                                    $values = array_map(static function ($v) {
                                        return '"' . pSQL($v) . '"';
                                    }, $val_list);
                                    $values = implode(',', $values);
                                    try {
                                        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . (string) $table . ' (' . $cols . ') VALUES( ' . $values . ')');
                                    } catch (Exception $ex) {
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($xml->configuration)) {
                            foreach ($xml->configuration as $config) {
                                if ((int) $config->id_shop == $id_shop) {
                                    if (isset($config->language)) {
                                        $configLang = [];
                                        foreach ($config->language as $cl) {
                                            $id_lang = Language::getIdByIso((string) $cl->iso_code);
                                            $configLang[(int) $id_lang] = (string) $cl->value;
                                        }

                                        Configuration::updateValue((string) $config->name, $configLang, false, null, $id_shop);
                                    } else {
                                        $configVal = (string) $config->value;
                                        if (in_array((string) $config->name, $configImgName) && $configVal) {
                                            if (!file_exists(_PS_ROOT_DIR_ . 'img/social/' . $configVal)) {
                                                $configVal = null;
                                            }
                                        }
                                        Configuration::updateValue((string) $config->name, $configVal, false, null, $id_shop);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
