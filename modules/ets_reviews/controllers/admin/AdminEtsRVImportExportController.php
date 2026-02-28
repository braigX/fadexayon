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

if (!defined('_PS_VERSION_')) { exit; }

require_once dirname(__FILE__) . '/../../classes/EtsRVIO.php';
require_once dirname(__FILE__) . '/../../classes/EtsRVSimpleXLSX.php';
require_once dirname(__FILE__) . '/AdminEtsRVBaseController.php';

class AdminEtsRVImportExportController extends AdminEtsRVBaseController
{
    public function postProcess()
    {
        if (trim($this->action) == 'generateArchive') {
            $this->processGenerateArchive();
        } else
            parent::postProcess();
    }

    public function initContent()
    {
        $this->content .= $this->renderForm();

        parent::initContent();
    }

    public function getFieldsForm()
    {
        $values = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled', 'AdminEtsRVImportExportController'),
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled', 'AdminEtsRVImportExportController'),
            ),
        );
        return array(
            array(
                'type' => 'checkboxes',
                'label' => $this->l('Select types of data to import:', 'AdminEtsRVImportExportController'),
                'name' => 'ETS_RV_IE_DATA_IMPORT',
                'values' => array(
                    array(
                        'id' => 'rv',
                        'name' => $this->l('Reviews', 'AdminEtsRVImportExportController'),
                        'class' => 'parent'
                    ),
                    array(
                        'id' => 'cm',
                        'name' => $this->l('Comments', 'AdminEtsRVImportExportController'),
                        'class' => 'sub'
                    ),
                    array(
                        'id' => 'rc',
                        'name' => $this->l('Replies', 'AdminEtsRVImportExportController'),
                        'class' => 'sub'
                    ),
                    array(
                        'id' => 'qa',
                        'name' => $this->l('Questions & Answers', 'AdminEtsRVImportExportController'),
                        'class' => 'parent'
                    ),
                    array(
                        'id' => 'qs',
                        'name' => $this->l('Answers', 'AdminEtsRVImportExportController'),
                        'class' => 'sub'
                    ),
                    array(
                        'id' => 'qc',
                        'name' => $this->l('Comment', 'AdminEtsRVImportExportController'),
                        'class' => 'sub'
                    ),
                    array(
                        'id' => 'mc',
                        'name' => $this->l('Module configuration', 'AdminEtsRVImportExportController'),
                        'class' => 'parent'
                    ),
                    array(
                        'id' => 'ac',
                        'name' => $this->l('Activities', 'AdminEtsRVImportExportController'),
                        'class' => 'parent'
                    )
                ),
                'default' => 'all',
                'form_group_class' => 'import_export',
                'tab' => 'import_export',
            ),
            array(
                'label' => $this->l('Override existing items if exist the same ID', 'AdminEtsRVImportExportController'),
                'name' => 'ETS_RV_IE_OVERRIDE',
                'type' => 'switch',
                'values' => $values,
                'default' => 1,
                'form_group_class' => 'import_export',
            ),
            array(
                'label' => $this->l('Remove all data before importing', 'AdminEtsRVImportExportController'),
                'name' => 'ETS_RV_IE_DELETE_ALL',
                'type' => 'switch',
                'values' => $values,
                'default' => 0,
                'form_group_class' => 'import_export',
            ),
        );
    }

    public function renderForm()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Import/Export', 'AdminEtsRVImportExportController'),
                    'icon' => '',
                ),
                'input' => $this->getFieldsForm(),
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this->module;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->tpl_vars = array(
            'fields_value' => $this->getFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'name_controller' => 'ets_rv_form_import_export',
            'currentIndex' => self::$currentIndex . '&token=' . $this->token,
            'productComment' => Module::isInstalled('productcomments'),
            'old_criterions' => EtsRVIO::getOldCriterions(),
            'new_criterions' => EtsRVIO::getNewCriterions(),
            'controller' => 'ie'
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function getFieldsValues()
    {
        $fields = [];
        $configs = $this->getFieldsForm();
        if ($configs) {
            $languages = Language::getLanguages(false);
            foreach ($configs as $config) {
                $key = $config['name'];
                if (isset($config['lang']) && $config['lang']) {
                    foreach ($languages as $l) {
                        $fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
                    }
                } elseif ($config['type'] == 'select' && isset($config['multiple']) && $config['multiple']) {
                    $fields[$key . ($config['type'] == 'select' ? '[]' : '')] = Tools::getValue($key, array());
                } elseif ($config['type'] == 'group' || $config['type'] == 'checkboxes') {
                    $fields[$key] = Tools::getValue($key, isset($config['default']) ? explode(',', $config['default']) : '');
                } else
                    $fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
            }
        }

        return $fields;
    }

    public function processGenerateArchive()
    {
        $zip = new ZipArchive();
        $cacheDir = _PS_CACHE_DIR_ . '/' . $this->module->name . '/';
        $zip_file_name = 'data_' . date('dmYHis') . '.zip';
        if (!@is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $io = EtsRVIO::getInstance();
        if ($zip->open($cacheDir . $zip_file_name, ZipArchive::OVERWRITE | ZipArchive::CREATE) === true) {
            if (!$zip->addFromString('Config.xml', $io->geneConfigXML())) {
                $this->errors[] = $this->l('Cannot create Config.xml file.', 'AdminEtsRVImportExportController');
            }
            // Group product comments:
            $xml_data = $io->addFileXMl(EtsRVProductCommentCriterion::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVProductCommentCriterion.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVProductCommentCriterion.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_criterion_category');
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_criterion_category.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_criterion_category.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_criterion_product');
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_criterion_product.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_criterion_product.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl(EtsRVProductComment::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVProductComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVProductComment.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl(EtsRVProductCommentImage::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVProductCommentImage.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVProductCommentImage.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_origin_lang', 'id_ets_rv_product_comment', true);
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_publish_lang', 'id_ets_rv_product_comment', true);
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_publish_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_publish_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_usefulness');
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_grade');
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_grade.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_grade.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_cart_rule');
            if ($xml_data && !$zip->addFromString('ets_rv_product_comment_cart_rule.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_product_comment_cart_rule.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl(EtsRVProductCommentCustomer::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVProductCommentCustomer.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVProductCommentCustomer.xml file.', 'AdminEtsRVImportExportController');
            }
            // End:

            // Group comments:
            $xml_data = $io->addFileXMl(EtsRVComment::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVComment.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_comment_origin_lang', 'id_ets_rv_comment', true);
            if ($xml_data && !$zip->addFromString('ets_rv_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_comment_usefulness');
            if ($xml_data && !$zip->addFromString('ets_rv_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }
            // End:

            // Group replies:
            $xml_data = $io->addFileXMl(EtsRVReplyComment::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVReplyComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVReplyComment.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_reply_comment_origin_lang', 'id_ets_rv_reply_comment', true);
            if ($xml_data && !$zip->addFromString('ets_rv_reply_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_reply_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }
            $xml_data = $io->addFileXMl14('ets_rv_reply_comment_usefulness');
            if ($xml_data && !$zip->addFromString('ets_rv_reply_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_reply_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }
            // End:


            // Questions:
            $xml_data = $io->addFileXMl(EtsRVProductComment::$definition, 1);
            if ($xml_data && !$zip->addFromString('EtsRVQAProductComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVQAProductComment.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_origin_lang', 'id_ets_rv_product_comment', true, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_product_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_product_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_publish_lang', 'id_ets_rv_product_comment', true, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_product_comment_publish_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_product_comment_publish_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_product_comment_usefulness', '', false, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_product_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_product_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }
            // End:

            // Comments question:
            $xml_data = $io->addFileXMl(EtsRVComment::$definition, 1);
            if ($xml_data && !$zip->addFromString('EtsRVQAComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVQAComment.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_comment_origin_lang', 'id_ets_rv_comment', true, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }

            $xml_data = $io->addFileXMl14('ets_rv_comment_usefulness', '', false, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }

            // Answers question:
            $xml_data = $io->addFileXMl(EtsRVComment::$definition, 1, 1);
            if ($xml_data && !$zip->addFromString('EtsRVQAAnswerComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVQAAnswerComment.xml file.', 'AdminEtsRVImportExportController');
            }
            $xml_data = $io->addFileXMl14('ets_rv_comment_origin_lang', 'id_ets_rv_comment', true, 1, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_answer_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_answer_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }
            $xml_data = $io->addFileXMl14('ets_rv_comment_usefulness', '', false, 1, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_answer_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_answer_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }

            // Comments answers:
            $xml_data = $io->addFileXMl(EtsRVReplyComment::$definition, 1, 1);
            if ($xml_data && !$zip->addFromString('EtsRVQAReplyComment.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVQAReplyComment.xml file.', 'AdminEtsRVImportExportController');
            }
            $xml_data = $io->addFileXMl14('ets_rv_reply_comment_origin_lang', 'id_ets_rv_reply_comment', true, 1, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_reply_comment_origin_lang.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_reply_comment_origin_lang.xml file.', 'AdminEtsRVImportExportController');
            }
            $xml_data = $io->addFileXMl14('ets_rv_reply_comment_usefulness', '', false, 1, 1);
            if ($xml_data && !$zip->addFromString('ets_rv_qa_reply_comment_usefulness.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create ets_rv_qa_reply_comment_usefulness.xml file.', 'AdminEtsRVImportExportController');
            }

            //Activities:
            $xml_data = $io->addFileXMl(EtsRVActivity::$definition);
            if ($xml_data && !$zip->addFromString('EtsRVActivity.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create EtsRVActivity.xml file.', 'AdminEtsRVImportExportController');
            }

            // Info:
            $xml_data = $io->geneInfoXML();
            if ($xml_data && !$zip->addFromString('DataInfo.xml', $xml_data)) {
                $this->errors[] = $this->l('Cannot create DataInfo.xml', 'AdminEtsRVImportExportController');
            }
            // End:

            // Zip Image:
            if ((int)Tools::getValue('ETS_RV_IE_EXPORT_PHOTOS')) {
                $io->archiveThisFile($zip, 'r', _PS_IMG_DIR_ . $this->module->name . '/', 'img/');
            }
            $zip->close();

            if (!is_file($cacheDir . $zip_file_name)) {
                $this->errors[] = $this->l(sprintf('Could not create %1s', $cacheDir . $zip_file_name));
            }

            if (!$this->errors) {
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
                readfile($cacheDir . $zip_file_name);
                if (@file_exists($cacheDir . $zip_file_name))
                    @unlink($cacheDir . $zip_file_name);
                exit;
            }
        } else {
            $this->errors[] = $this->l('An error occurred during the archive generation', 'AdminEtsRVImportExportController');
        }
        $this->jsonRender([
            'errors' => $this->errors ? implode(PHP_EOL, $this->errors) : false
        ]);
    }

    public function ajaxProcessUploadData()
    {
        $savePath = _PS_CACHE_DIR_ . '/' . $this->module->name . '/';
        if (@file_exists($savePath . 'data.zip')) {
            @unlink($savePath . 'data.zip');
        } elseif (!@is_dir($savePath)) {
            @mkdir($savePath, 0755, true);
        }
        $uploader = new Uploader('data');
        if (method_exists($uploader, 'setCheckFileSize'))
            $uploader->setCheckFileSize(false);
        $uploader->setAcceptTypes(array('zip'));
        $uploader->setSavePath($savePath);
        $files = $uploader->process('data.zip');
        if (isset($files[0]) && isset($files[0]['error'])) {
            if ($files[0]['error'] === 0 && !Tools::ZipTest($savePath . 'data.zip')) {
                $this->errors[] = $this->l('Zip file seems to be broken', 'AdminEtsRVImportExportController');
            } elseif (!empty($files[0]['error'])) {
                $this->errors[] = $files[0]['error'];
            }
        }
        $extractUrl = $savePath . 'data.zip';
        if (!@file_exists($extractUrl))
            $this->errors[] = $this->l('Zip file does not exist', 'AdminEtsRVImportExportController');
        if (!$this->errors) {
            $zip = new ZipArchive();
            if ($zip->open($extractUrl) === true) {
                if ($zip->locateName('DataInfo.xml') === false) {
                    $this->errors[] = $this->l('The uploaded Zip file is invalid', 'AdminEtsRVImportExportController');
                    if (file_exists($extractUrl) && $zip->close())
                        @unlink($extractUrl);
                }
            } else
                $this->errors[] = $this->l('Cannot open Zip file. It might be broken or damaged', 'AdminEtsRVImportExportController');
        }
        $hasError = $this->errors ? 1 : 0;
        $this->jsonRender([
            'errors' => $hasError ? implode(PHP_EOL, $this->errors) : false,
            'msg' => !$hasError ? $this->l('Data package has been uploaded. Please choose the data types below before starting to import', 'AdminEtsRVImportExportController') : '',
        ]);
    }

    public function ajaxProcessImportDataPrestashop()
    {
        EtsRVIO::getInstance()->importDataPrestashop(Tools::getValue('new_criterions'));
    }

    public function ajaxProcessImportData()
    {
        $zip = new ZipArchive();
        $zipFileName = 'data.zip';
        $extractUrl = _PS_CACHE_DIR_ . '/' . $this->module->name . '/';

        $ie_data_import = Tools::getValue('ETS_RV_IE_DATA_IMPORT', []);
        $forceId = (int)Tools::getValue('ETS_RV_IE_OVERRIDE') ? 1 : 0;
        $ie_delete_all = (int)Tools::getValue('ETS_RV_IE_DELETE_ALL') ? 1 : 0;


        if (!$ie_data_import) {
            $this->errors[] = $this->l('You need to select a data type to import', 'AdminEtsRVImportExportController');
        } else {
            if (@is_dir($extractUrl . 'data'))
                $this->module->removeTree($extractUrl . 'data');
            if (!@file_exists($extractUrl . $zipFileName)) {
                $this->errors[] = $this->l('Zip file does not exist', 'AdminEtsRVImportExportController');
            } elseif ($zip->open($extractUrl . $zipFileName) === true) {
                if ($zip->locateName('DataInfo.xml') === false) {
                    $this->errors[] = $this->l('The uploaded Zip file is invalid', 'AdminEtsRVImportExportController');
                    if (file_exists($extractUrl) && $zip->close())
                        @unlink($extractUrl);
                } elseif (!Tools::ZipExtract($extractUrl . $zipFileName, $extractUrl . 'data'))
                    $this->errors[] = $this->l('Cannot extract Zip data', 'AdminEtsRVImportExportController');
            }
        }

        if (!$this->errors) {
            $io = EtsRVIO::getInstance();
            $io->forceId = $forceId;
            $io->delete_all_data = $ie_delete_all;

            // Set choice data:
            foreach ($ie_data_import as $item) {
                $io->$item = 1;
            }

            // Import configs:
            if ($io->mc && $zip->locateName('Config.xml') !== false) {
                $filename = $extractUrl . '/data/Config.xml';
                if (file_exists($filename) && $io->importXmlConfig(@simplexml_load_file($filename))) {
                    @unlink($filename);
                }
            }
            // End:

            // ProductComments:
            if ($io->rv && $zip->locateName('EtsRVProductComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'category',
                        'product',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteProductCommentCriterion($table);
                    }
                    if ($res)
                        EtsRVProductCommentCriterion::deleteAll();

                    $tables = [
                        'lang',
                        'publish_lang',
                        'origin_lang',
                        'usefulness',
                        'grade',
                        'image'
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteProductComment($table);
                    }
                    if ($res) {
                        EtsRVProductComment::deleteAll();
                        EtsRVCartRule::deleteAll();
                        EtsRVProductCommentCustomer::deleteAll();
                    }
                }

                if ($zip->locateName('EtsRVProductCommentCriterion.xml') !== false) {
                    $filename = $extractUrl . '/data/EtsRVProductCommentCriterion.xml';
                    $io->importData(@simplexml_load_file($filename), 'EtsRVProductCommentCriterion', EtsRVProductCommentCriterion::$definition, []
                        , [
                            'name'
                        ], $this->errors
                    );
                    if (!$this->errors && @file_exists($filename)) {
                        @unlink($filename);
                        if ($zip->locateName('ets_rv_product_comment_criterion_category.xml') !== false) {
                            $io->importCriterionCategory(@simplexml_load_file($extractUrl . '/data/ets_rv_product_comment_criterion_category.xml'), $this->errors);
                        }
                        if (!$this->errors && $zip->locateName('ps_ets_rv_product_comment_criterion_product.xml') !== false) {
                            $io->importCriterionProduct(@simplexml_load_file($extractUrl . '/data/ps_ets_rv_product_comment_criterion_product.xml'), $this->errors);
                        }
                    }
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVProductComment.xml'), 'EtsRVProductComment', EtsRVProductComment::$definition
                    , [
                        'product' => 'id_product',
                        'customer' => 'id_customer',
                        'guest' => 'id_guest',
                    ]
                    , [], $this->errors
                );
                // Publish lang:
                if ($zip->locateName('ets_rv_product_comment_publish_lang.xml') !== false) {
                    $io->importPublishLang(@simplexml_load_file($extractUrl . '/data/ets_rv_product_comment_publish_lang.xml'));
                }

                // Origin lang:
                if ($zip->locateName('ets_rv_product_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_product_comment_origin_lang.xml')
                        , 'ets_rv_product_comment_origin_lang'
                        , 'id_ets_rv_product_comment'
                        , ['ets_rv_product_comment' => 'id_ets_rv_product_comment']
                    );
                }

                if (!$this->errors) {
                    if ($zip->locateName('ets_rv_product_comment_grade.xml') !== false) {
                        $io->importGrades(@simplexml_load_file($extractUrl . '/data/ets_rv_product_comment_grade.xml'), $this->errors);
                    }
                    if ($zip->locateName('EtsRVProductCommentImage.xml') !== false) {
                        $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVProductCommentImage.xml'), 'EtsRVProductCommentImage', EtsRVProductCommentImage::$definition
                            , [
                                'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                            ]
                            , [], $this->errors
                        );
                    }
                    if ($zip->locateName('EtsRVProductCommentCustomer.xml') !== false) {
                        $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVProductCommentCustomer.xml'), 'EtsRVProductCommentCustomer', EtsRVProductCommentCustomer::$definition
                            , [
                                'customer' => 'id_customer',
                            ]
                            , [], $this->errors
                        );
                    }
                }
                $foreign_key = [
                    'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                    'customer' => 'id_customer',
                ];
                if ($zip->locateName('ets_rv_product_comment_usefulness.xml') !== false) {
                    $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_product_comment_usefulness.xml'), 'ets_rv_product_comment_usefulness'
                        , $foreign_key
                        , $this->errors
                    );
                }
            }

            // Comments:
            if ($io->rv && $io->cm && $zip->locateName('EtsRVComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteComment($table);
                    }
                    if ($res)
                        EtsRVComment::deleteAll();
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVComment.xml'), 'EtsRVComment', EtsRVComment::$definition
                    , [
                        'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                    ]
                    , [], $this->errors
                );
                if ($zip->locateName('ets_rv_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_comment_origin_lang.xml')
                        , 'ets_rv_comment_origin_lang'
                        , 'id_ets_rv_comment'
                        , ['ets_rv_comment' => 'id_ets_rv_comment']
                    );
                }
                if (!$this->errors) {
                    $foreign_key = [
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_comment_usefulness.xml'), 'ets_rv_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Replies:
            if ($io->rv && $io->cm && $io->rc && $zip->locateName('EtsRVReplyComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteReplyComment($table);
                    }
                    if ($res)
                        EtsRVReplyComment::deleteAll();
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVReplyComment.xml'), 'EtsRVReplyComment', EtsRVReplyComment::$definition
                    , [
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                    ]
                    , [], $this->errors
                );
                if ($zip->locateName('ets_rv_reply_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_reply_comment_origin_lang.xml')
                        , 'ets_rv_reply_comment_origin_lang'
                        , 'id_ets_rv_reply_comment'
                        , ['ets_rv_reply_comment' => 'id_ets_rv_reply_comment']
                    );
                }
                if (!$this->errors) {
                    $foreign_key = [
                        'ets_rv_reply_comment' => 'id_ets_rv_reply_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_reply_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_reply_comment_usefulness.xml'), 'ets_rv_reply_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Questions:
            if ($io->qa && $zip->locateName('EtsRVQAProductComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'publish_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteProductComment($table, $io->qa);
                    }
                    if ($res)
                        EtsRVProductComment::deleteAll(1);
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVQAProductComment.xml'), 'EtsRVProductComment', EtsRVProductComment::$definition
                    , [
                        'product' => 'id_product',
                        'customer' => 'id_customer',
                        'guest' => 'id_guest',
                    ]
                    , [], $this->errors
                );
                if (!$this->errors) {
                    if ($zip->locateName('ets_rv_qa_product_comment_publish_lang.xml') !== false) {
                        $io->importPublishLang(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_product_comment_publish_lang.xml'));
                    }
                    if ($zip->locateName('ets_rv_qa_product_comment_origin_lang.xml') !== false) {
                        $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_product_comment_origin_lang.xml')
                            , 'ets_rv_product_comment_origin_lang'
                            , 'id_ets_rv_product_comment'
                            , ['ets_rv_product_comment' => 'id_ets_rv_product_comment']
                        );
                    }
                    $foreign_key = [
                        'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_qa_product_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_product_comment_usefulness.xml'), 'ets_rv_product_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Comments question:
            if ($io->qa && $io->qc && $zip->locateName('EtsRVQAComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteComment($table, $io->qa);
                    }
                    if ($res)
                        EtsRVComment::deleteAll(1, 0);
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVQAComment.xml'), 'EtsRVComment', EtsRVComment::$definition
                    , [
                        'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                    ]
                    , [], $this->errors
                );
                if ($zip->locateName('ets_rv_qa_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_comment_origin_lang.xml')
                        , 'ets_rv_comment_origin_lang'
                        , 'id_ets_rv_comment'
                        , ['ets_rv_comment' => 'id_ets_rv_comment']
                    );
                }
                if (!$this->errors) {
                    $foreign_key = [
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_qa_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_comment_usefulness.xml'), 'ets_rv_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Answers question:
            if ($io->qa && $io->qs && $zip->locateName('EtsRVQAAnswerComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteComment($table, $io->qa, 1);
                    }
                    if ($res)
                        EtsRVComment::deleteAll(1, 1);
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVQAAnswerComment.xml'), 'EtsRVComment', EtsRVComment::$definition
                    , [
                        'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                    ]
                    , [], $this->errors
                );
                if ($zip->locateName('ets_rv_qa_answer_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_answer_comment_origin_lang.xml')
                        , 'ets_rv_comment_origin_lang'
                        , 'id_ets_rv_comment'
                        , ['ets_rv_comment' => 'id_ets_rv_comment']
                    );
                }
                if (!$this->errors) {
                    $foreign_key = [
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_qa_answer_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_answer_comment_usefulness.xml'), 'ets_rv_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Comments answer:
            if ($io->qa && $io->qc && $zip->locateName('EtsRVQAReplyComment.xml') !== false) {
                if ($ie_delete_all) {
                    $tables = [
                        'lang',
                        'origin_lang',
                        'usefulness',
                    ];
                    $res = true;
                    foreach ($tables as $table) {
                        $res &= EtsRVIO::deleteReplyComment($table, $io->qa);
                    }
                    if ($res)
                        EtsRVReplyComment::deleteAll(1);
                }
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVQAReplyComment.xml'), 'EtsRVReplyComment', EtsRVReplyComment::$definition
                    , [
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                    ]
                    , [], $this->errors
                );
                if ($zip->locateName('ets_rv_qa_reply_comment_origin_lang.xml') !== false) {
                    $io->importOriginLang(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_reply_comment_origin_lang.xml')
                        , 'ets_rv_reply_comment_origin_lang'
                        , 'id_ets_rv_reply_comment'
                        , ['ets_rv_reply_comment' => 'id_ets_rv_reply_comment']
                    );
                }
                if (!$this->errors) {
                    $foreign_key = [
                        'ets_rv_reply_comment' => 'id_ets_rv_reply_comment',
                        'customer' => 'id_customer',
                    ];
                    if ($zip->locateName('ets_rv_qa_reply_comment_usefulness.xml') !== false) {
                        $io->importUsefulness(@simplexml_load_file($extractUrl . '/data/ets_rv_qa_reply_comment_usefulness.xml'), 'ets_rv_reply_comment_usefulness'
                            , $foreign_key
                            , $this->errors
                        );
                    }
                }
            }

            // Activities:
            if ($io->ac && $zip->locateName('EtsRVActivity.xml') !== false) {
                $io->importData(@simplexml_load_file($extractUrl . '/data/EtsRVActivity.xml'), 'EtsRVActivity', EtsRVActivity::$definition
                    , [
                        'ets_rv_product_comment' => 'id_ets_rv_product_comment',
                        'ets_rv_comment' => 'id_ets_rv_comment',
                        'ets_rv_reply_comment' => 'id_ets_rv_reply_comment',
                        'customer' => 'id_customer',
                        'employee' => 'id_employee',
                        'guest' => 'id_guest',
                        'product' => 'id_product',
                    ]
                    , [], $this->errors
                );
            }

            // Remove zip:
            if (file_exists($extractUrl) && $zip->close())
                @unlink($extractUrl);
            // End:
        }

        $this->ajaxError();
    }

    public function ajaxError($question = 0)
    {
        $hasError = $this->errors ? 1 : 0;
        $this->jsonRender([
            'errors' => $hasError ? Tools::nl2br(implode(PHP_EOL, $this->errors)) : false,
            'msg' => !$hasError ? ($question ? $this->l('Product question data was successfully imported', 'AdminEtsRVImportExportController') : $this->l('Product review data was successfully imported', 'AdminEtsRVImportExportController')) : '',
        ]);
    }

    public function uploadDataCsvOrXlsx($filename, $saveFilename)
    {
        if (!isset($_FILES[$filename]) || !isset($_FILES[$filename]['tmp_name']) || !$_FILES[$filename]['tmp_name'] || !$_FILES[$filename]['name']) {
            $this->errors[] = $this->l('File does not exist', 'AdminEtsRVImportExportController');
        } elseif (($filetype = Tools::strtolower(Tools::substr(strrchr($_FILES[$filename]['name'], '.'), 1))) && !in_array($filetype, ['csv', 'xlsx'])) {
            $this->errors[] = sprintf($this->l('File "%s" type is not allowed', 'AdminEtsRVImportExportController'), $_FILES[$filename]['name']);
        }
        if ($this->errors) {
            $this->jsonRender([
                'errors' => implode(PHP_EOL, $this->errors),
            ]);
        }
        $savePath = _PS_CACHE_DIR_ . $this->module->name . '/';
        if (@file_exists($savePath . $saveFilename . '.' . $filetype)) {
            @unlink($savePath . $saveFilename . '.' . $filetype);
        } elseif (!@is_dir($savePath)) {
            @mkdir($savePath, 0755, true);
        }
        $uploader = new Uploader($filename);
        if (method_exists($uploader, 'setCheckFileSize'))
            $uploader->setCheckFileSize(false);
        $uploader->setAcceptTypes(array($filetype));
        $uploader->setSavePath($savePath);
        $files = $uploader->process($saveFilename . '.' . $filetype);
        if (isset($files[0]) && isset($files[0]['error'])) {
            if (!empty($files[0]['error'])) {
                $this->errors[] = $files[0]['error'];
            }
        }
        $extractUrl = $savePath . $saveFilename . '.' . $filetype;
        if (!@file_exists($extractUrl))
            $this->errors[] = sprintf($this->l('%s file does not exist', 'AdminEtsRVImportExportController'), Tools::strtoupper($filetype));
        $hasError = $this->errors ? 1 : 0;
        if ($hasError) {
            $this->jsonRender([
                'errors' => $hasError ? implode(PHP_EOL, $this->errors) : false,
            ]);
        }

        return $filetype;
    }

    public function ajaxProcessImportDataReviews()
    {
        ini_set('max_execution_time', 7200);
        $saveFilename = 'data';
        $filetype = $this->uploadDataCsvOrXlsx('data_csv_or_xlsx', $saveFilename);
        $filename = $saveFilename . '.' . $filetype;

        $this->importData($filename, $filetype, (int)Tools::getValue('delete_all'));
    }

    public function importData($filename, $filetype, $delete_all = 0, $question = 0)
    {
        $pathFilename = _PS_CACHE_DIR_ . $this->module->name . '/';
        if (!@file_exists($pathFilename . $filename)) {
            $this->errors[] = sprintf($this->l('%s file does not exist', 'AdminEtsRVImportExportController'), Tools::strtoupper($filename));
        }
        if (!$this->errors) {
            if ($delete_all) {
                $tables = ['lang', 'publish_lang', 'origin_lang', 'usefulness', 'grade', 'image'];
                $result = true;
                foreach ($tables as $table) {
                    $result &= EtsRVIO::deleteProductComment($table, $question);
                    if (!in_array($table, ['publish_lang', 'grade', 'image'])) {
                        $result &= EtsRVIO::deleteComment($table, $question) && EtsRVIO::deleteReplyComment($table, $question);
                    }
                }
                if ($result) {
                    EtsRVComment::deleteAll($question);
                    EtsRVReplyComment::deleteAll($question);
                    EtsRVActivity::deleteAll($question);
                    if (!$question) {
                        EtsRVCartRule::deleteAll();
                    }
                    EtsRVProductComment::deleteAll($question);
                    $this->module->removeTree(_PS_IMG_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . 'r', false);
                }
            }
            $io = EtsRVIO::getInstance();
            if (!$question) {
                $io->product_comment_criterions = EtsRVProductCommentCriterion::getImportCriterions(true);
                $io->image_type = EtsRVProductCommentImage::getImageTypes();
            }
            if (trim($filetype) === 'csv') {
                $csv = fopen($pathFilename . $filename, "r");
                $ik = 0;
                if ($csv !== false) {
                    while (($data = fgetcsv($csv, 0, ",")) !== false) {
                        if ($ik == 0) {
                            $io->headers = $data;
                        } elseif ($ik >= 1 && $data) {
                            $io->importDataCsvOrXlsx($data, $question);
                        }
                        $ik++;
                    }
                } else
                    $this->errors[] = $this->l('Data is null', 'AdminEtsRVImportExportController');
            } else {
                if ($xlsx = EtsRVSimpleXLSX::parse($pathFilename . $filename)) {
                    if ($xlsx->rows()) {
                        $ik = 0;
                        foreach ($xlsx->rows() as $data) {
                            if ($ik == 0) {
                                $io->headers = $data;
                            } elseif ($ik >= 1 && $data) {
                                $io->importDataCsvOrXlsx($data, $question);
                            }
                            $ik++;
                        }
                    }
                } else {
                    $this->errors[] = EtsRVSimpleXLSX::parseError();
                }
            }

            // Remove zip:
            if (file_exists($pathFilename . $filename))
                @unlink($pathFilename . $filename);
            // End:
        }
        $this->ajaxError($question);
    }

    public function ajaxProcessImportDataQuestions()
    {
        ini_set('max_execution_time', 7200);
        $saveFilename = 'data_qa';
        $filetype = $this->uploadDataCsvOrXlsx('data_qa_csv_or_xlsx', $saveFilename);
        $filename = $saveFilename . '.' . $filetype;

        $this->importData($filename, $filetype, (int)Tools::getValue('delete_all'), 1);
    }

    public function processDownloadFileExample()
    {
        $this->downloadFileExample('Import_reviews_example.xlsx');
    }

    public function processDownloadFileExampleQuestion()
    {
        $this->downloadFileExample('Import_questions_example.xlsx');
    }

    public function downloadFileExample($filename)
    {
        if (!in_array($filename, ['Import_reviews_example.xlsx', 'Import_questions_example.xlsx'])) {
            echo $this->l('Filename is invalid');
            exit;
        }
        $PS_BASE_DIR = 'views/example/';
        $file = $this->module->getLocalPath() . $PS_BASE_DIR . $filename;
        if (!is_file($file)) {
            echo $this->l('File does not exist');
            exit;
        }
        $mimeType = false;
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);
            $mimeType = @finfo_file($finfo, $file);
            @finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($file);
        } elseif (function_exists('exec')) {
            $mimeType = trim(call_user_func('exec', 'file -b --mime-type ' . call_user_func('escapeshellarg', $file)));
            if (!$mimeType) {
                $mimeType = trim(call_user_func('exec', 'file --mime ' . call_user_func('escapeshellarg', $file)));
            }
            if (!$mimeType) {
                $mimeType = trim(@call_user_func('exec', 'file -bi ' . call_user_func('escapeshellarg', $file)));
            }
        }

        if (empty($mimeType)) {
            $bName = basename($filename);
            $bName = explode('.', $bName);
            $bName = Tools::strtolower($bName[count($bName) - 1]);

            $mimeTypes = array(
                'ez' => 'application/andrew-inset',
                'hqx' => 'application/mac-binhex40',
                'cpt' => 'application/mac-compactpro',
                'doc' => 'application/msword',
                'oda' => 'application/oda',
                'pdf' => 'application/pdf',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',
                'smi' => 'application/smil',
                'smil' => 'application/smil',
                'wbxml' => 'application/vnd.wap.wbxml',
                'wmlc' => 'application/vnd.wap.wmlc',
                'wmlsc' => 'application/vnd.wap.wmlscriptc',
                'bcpio' => 'application/x-bcpio',
                'vcd' => 'application/x-cdlink',
                'pgn' => 'application/x-chess-pgn',
                'cpio' => 'application/x-cpio',
                'csh' => 'application/x-csh',
                'dcr' => 'application/x-director',
                'dir' => 'application/x-director',
                'dxr' => 'application/x-director',
                'dvi' => 'application/x-dvi',
                'spl' => 'application/x-futuresplash',
                'gtar' => 'application/x-gtar',
                'hdf' => 'application/x-hdf',
                'js' => 'application/x-javascript',
                'skp' => 'application/x-koan',
                'skd' => 'application/x-koan',
                'skt' => 'application/x-koan',
                'skm' => 'application/x-koan',
                'latex' => 'application/x-latex',
                'nc' => 'application/x-netcdf',
                'cdf' => 'application/x-netcdf',
                'sh' => 'application/x-sh',
                'shar' => 'application/x-shar',
                'swf' => 'application/x-shockwave-flash',
                'sit' => 'application/x-stuffit',
                'sv4cpio' => 'application/x-sv4cpio',
                'sv4crc' => 'application/x-sv4crc',
                'tar' => 'application/x-tar',
                'tcl' => 'application/x-tcl',
                'tex' => 'application/x-tex',
                'texinfo' => 'application/x-texinfo',
                'texi' => 'application/x-texinfo',
                't' => 'application/x-troff',
                'tr' => 'application/x-troff',
                'roff' => 'application/x-troff',
                'man' => 'application/x-troff-man',
                'me' => 'application/x-troff-me',
                'ms' => 'application/x-troff-ms',
                'ustar' => 'application/x-ustar',
                'src' => 'application/x-wais-source',
                'xhtml' => 'application/xhtml+xml',
                'xht' => 'application/xhtml+xml',
                'zip' => 'application/zip',
                'au' => 'audio/basic',
                'snd' => 'audio/basic',
                'mid' => 'audio/midi',
                'midi' => 'audio/midi',
                'kar' => 'audio/midi',
                'mpga' => 'audio/mpeg',
                'mp2' => 'audio/mpeg',
                'mp3' => 'audio/mpeg',
                'aif' => 'audio/x-aiff',
                'aiff' => 'audio/x-aiff',
                'aifc' => 'audio/x-aiff',
                'm3u' => 'audio/x-mpegurl',
                'ram' => 'audio/x-pn-realaudio',
                'rm' => 'audio/x-pn-realaudio',
                'rpm' => 'audio/x-pn-realaudio-plugin',
                'ra' => 'audio/x-realaudio',
                'wav' => 'audio/x-wav',
                'pdb' => 'chemical/x-pdb',
                'xyz' => 'chemical/x-xyz',
                'bmp' => 'image/bmp',
                'gif' => 'image/gif',
                'ief' => 'image/ief',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'jpe' => 'image/jpeg',
                'png' => 'image/png',
                'tiff' => 'image/tiff',
                'tif' => 'image/tif',
                'djvu' => 'image/vnd.djvu',
                'djv' => 'image/vnd.djvu',
                'wbmp' => 'image/vnd.wap.wbmp',
                'ras' => 'image/x-cmu-raster',
                'pnm' => 'image/x-portable-anymap',
                'pbm' => 'image/x-portable-bitmap',
                'pgm' => 'image/x-portable-graymap',
                'ppm' => 'image/x-portable-pixmap',
                'rgb' => 'image/x-rgb',
                'xbm' => 'image/x-xbitmap',
                'xpm' => 'image/x-xpixmap',
                'xwd' => 'image/x-windowdump',
                'igs' => 'model/iges',
                'iges' => 'model/iges',
                'msh' => 'model/mesh',
                'mesh' => 'model/mesh',
                'silo' => 'model/mesh',
                'wrl' => 'model/vrml',
                'vrml' => 'model/vrml',
                'css' => 'text/css',
                'html' => 'text/html',
                'htm' => 'text/html',
                'asc' => 'text/plain',
                'txt' => 'text/plain',
                'rtx' => 'text/richtext',
                'rtf' => 'text/rtf',
                'sgml' => 'text/sgml',
                'sgm' => 'text/sgml',
                'tsv' => 'text/tab-seperated-values',
                'wml' => 'text/vnd.wap.wml',
                'wmls' => 'text/vnd.wap.wmlscript',
                'etx' => 'text/x-setext',
                'xml' => 'text/xml',
                'xsl' => 'text/xml',
                'mpeg' => 'video/mpeg',
                'mpg' => 'video/mpeg',
                'mpe' => 'video/mpeg',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',
                'mxu' => 'video/vnd.mpegurl',
                'avi' => 'video/x-msvideo',
                'movie' => 'video/x-sgi-movie',
                'ice' => 'x-conference-xcooltalk',
            );

            if (isset($mimeTypes[$bName])) {
                $mimeType = $mimeTypes[$bName];
            } else {
                $mimeType = 'application/octet-stream';
            }
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        /* Set headers for download */
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        //prevents max execution timeout, when reading large files
        @set_time_limit(0);
        $fp = fopen($file, 'rb');

        if ($fp && is_resource($fp)) {
            while (!feof($fp)) {
                echo fgets($fp, 16384);
            }
        }

        exit;
    }

}