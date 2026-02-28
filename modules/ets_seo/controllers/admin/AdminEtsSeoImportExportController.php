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

require_once _PS_MODULE_DIR_ . 'ets_seo/classes/EtsSeoImportExport.php';
require_once _PS_MODULE_DIR_ . 'ets_seo/classes/EtsSeoDataExporter.php';
require_once _PS_MODULE_DIR_ . 'ets_seo/classes/EtsSeoDataImporter.php';

/**
 * Class AdminEtsSeoImportExportController
 *
 * @property \Context|\ContextCore $context
 * @property \Ets_Seo $module
 */
class AdminEtsSeoImportExportController extends ModuleAdminController
{
    public $import_export_options;
    /**
     * @var \EtsSeoDataExporter
     */
    protected $exporter;
    /**
     * @var \EtsSeoDataImporter
     */
    protected $importer;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();

        $this->fields_options = [
            'inport_export' => [
                'title' => $this->module->l('Backup', 'AdminEtsSeoImportExportController'),
                'icon' => '',
                'fields' => [],
            ],
        ];

        $this->import_export_options = [
            'config' => $this->module->l('Global SEO settings', 'AdminEtsSeoImportExportController'),
            'redirect' => $this->module->l('URL redirects', 'AdminEtsSeoImportExportController'),
            'product' => $this->module->l('Product', 'AdminEtsSeoImportExportController'),
            'category' => $this->module->l('Product category', 'AdminEtsSeoImportExportController'),
            'cms' => $this->module->l('CMS (pages)', 'AdminEtsSeoImportExportController'),
            'cms_category' => $this->module->l('CMS category', 'AdminEtsSeoImportExportController'),
            'supplier' => $this->module->l('Supplier', 'AdminEtsSeoImportExportController'),
            'manufacturer' => $this->module->l('Brand (manufacturer)', 'AdminEtsSeoImportExportController'),
            'meta' => $this->module->l('Other pages', 'AdminEtsSeoImportExportController'),
        ];
        if (!Module::isEnabled('ets_seo')) {
            $this->warnings[] = $this->module->l('You must enable module SEO Audit to configure its features', 'AdminEtsSeoImportExportController');
        }
    }

    public function renderOptions()
    {
        $this->addJS($this->module->getPathUri() . 'views/js/import-export.js');
        $this->context->smarty->assign([
            'ets_seo_shops' => Shop::getShops(true),
            'ets_seo_options' => $this->import_export_options,
            'isExporting' => EtsSeoDataExporter::STATE_IDLE !== $this->getExporter()->detectCurrentState(),
        ]);

        return parent::renderOptions();
    }

    /**
     * @param array $data
     * @param string|null $message
     */
    private function _responseSuccess($data, $message = null)
    {
        exit(json_encode(['ok' => true, 'data' => $data, 'message' => $message]));
    }

    /**
     * @param array $errors
     */
    private function _responseErrors($errors)
    {
        exit(json_encode(['ok' => false, 'hasError' => true, 'errors' => $errors]));
    }

    public function postProcess()
    {
        if (Tools::isSubmit('downloadFile')) {
            $filename = Tools::getValue('filename');
            $cacheDir = EtsSeoDataExporter::DATA_FILE_DIRECTORY . DIRECTORY_SEPARATOR;
            if (!$filename || !file_exists($cacheDir . $filename)) {
                exit($this->module->l('File name is not invalid or file does not exist on server', 'AdminEtsSeoImportExportController'));
            }
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
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            ob_end_flush();
            readfile($cacheDir . $filename);
            @unlink($cacheDir . $filename);
            exit;
        }
        if (Tools::isSubmit('exportData')) {
            $exporter = $this->getExporter();
            $limit = Tools::getValue('nb_product');
            if ($limit && Validate::isUnsignedInt($limit)) {
                $exporter->setProductPerProcess($limit);
            }
            if (Tools::isSubmit('continue')) {
                try {
                    $data = $exporter->process();
                    $data['result'] = true;
                    $data['isCompleted'] = EtsSeoDataExporter::STATE_COMPLETED === $data['currentState'];
                    if (isset($data['fileName'])) {
                        $data['downloadUrl'] = $this->context->link->getAdminLink('AdminEtsSeoImportExport', true, [], ['downloadFile' => 1, 'filename' => $data['fileName']]);
                    }
                    $this->_responseSuccess($data, $data['message']);
                } catch (PrestaShopException $e) {
                    $this->_responseErrors([$e->getMessage()]);
                }
            }
            if (($shops = Tools::getValue('export_shops')) && ($seo_options = Tools::getValue('export_seo_options'))) {
                if (is_array($shops) && Ets_Seo::validateArray($shops) && is_array($seo_options) && Ets_Seo::validateArray($seo_options)) {
                    try {
                        $rs = $exporter->init($shops, $seo_options);
                        $currentState = $exporter->detectCurrentState();
                        $this->_responseSuccess(
                            [
                                'currentState' => $currentState,
                                'result' => $rs,
                                'isCompleted' => EtsSeoDataExporter::STATE_COMPLETED === $currentState,
                            ],
                            $this->module->l('Init export successfully. Begin processing shops', 'AdminEtsSeoImportExportController')
                        );
                    } catch (PrestaShopException $e) {
                        $this->errors[] = $e->getMessage();
                    } catch (RuntimeException $e) {
                        $this->errors[] = $e->getMessage();
                    }
                } else {
                    $this->errors[] = $this->module->l('Options to export are invalid.', 'AdminEtsSeoImportExportController');
                }
            } else {
                $this->errors[] = $this->module->l('Please choose a shop and SEO options to export data.', 'AdminEtsSeoImportExportController');
            }
            $this->_responseErrors($this->errors);
        }

        if (Tools::isSubmit('importData')) {
            if (($shops = Tools::getValue('import_shops'))
                && ($seo_options = Tools::getValue('import_seo_options'))
                && $_FILES['import_file']
            ) {
                if (is_array($shops) && Ets_Seo::validateArray($shops) && is_array($seo_options) && Ets_Seo::validateArray($seo_options)) {
                    $errors = $this->_prepareImport();
                    if ($errors) {
                        $this->errors = $errors;
                    } else {
                        $importer = $this->getImporter();
                        $importer->init($shops, $seo_options);
                        $this->module->_clearCache('*');
                        try {
                            $rs = $importer->process();
                            if ($rs) {
                                $this->confirmations[] = $this->module->l('Import data successfully.', 'AdminEtsSeoImportExportController');
                            } else {
                                $this->errors[] = $this->module->l('An unknown error occurred', 'AdminEtsSeoImportExportController');
                            }
                        } catch (PrestaShopException $e) {
                            $this->errors[] = $e->getMessage();
                            if (_PS_MODE_DEV_) {
                                if (@$importer->currentProcessingItem) {
                                    $this->errors[] = nl2br(sprintf($this->module->l('Current processing item: %s', 'AdminEtsSeoImportExportController'), json_encode($importer->currentProcessingItem, JSON_PRETTY_PRINT)));
                                }
                                $this->errors[] = nl2br($e->getTraceAsString());
                            }
                        }
                    }
                } else {
                    $this->errors[] = $this->module->l('Options to import are invalid.', 'AdminEtsSeoImportExportController');
                }
            } else {
                $this->errors[] = $this->module->l('Please choose a shop and SEO options to import data.', 'AdminEtsSeoImportExportController');
            }
        }
    }

    /**
     * @return \EtsSeoDataExporter
     */
    private function getExporter()
    {
        if (!$this->exporter instanceof EtsSeoDataExporter) {
            $this->exporter = EtsSeoDataExporter::getInstance();
        }

        return $this->exporter;
    }

    /**
     * @return \EtsSeoDataImporter
     */
    public function getImporter()
    {
        if (!$this->importer instanceof EtsSeoDataImporter) {
            $this->importer = EtsSeoDataImporter::getInstance();
        }

        return $this->importer;
    }

    private function _prepareImport()
    {
        $errors = [];
        if ($_FILES['import_file']) {
            $saveDirectory = EtsSeoDataImporter::DATA_FILE_DIRECTORY;
            $zipFilePath = $saveDirectory . DIRECTORY_SEPARATOR . 'ets_seo_data.zip';
            $xmlFilePath = $saveDirectory . DIRECTORY_SEPARATOR . 'ets_seo_data.xml';
            if (!is_dir($saveDirectory)) {
                if (!mkdir($saveDirectory, 0755) && !is_dir($saveDirectory)) {
                    $errors[] = sprintf($this->module->l('Directory "%s" was not created. Import operation aborted', 'AdminEtsSeoImportExportController'), $saveDirectory);

                    return $errors;
                }
            }
            if (@file_exists($zipFilePath)) {
                @unlink($zipFilePath);
            }

            $uploader = new Uploader('import_file');
            $uploader->setMaxSize(1048576000);
            $uploader->setAcceptTypes(['zip']);
            $uploader->setSavePath($saveDirectory);
            $file = $uploader->process();
            if(!empty($file[0]['save_path']))
                $zipFilePath = $file[0]['save_path'];
            else
                $zipFilePath = $saveDirectory . DIRECTORY_SEPARATOR .'ets_seo_data.zip';
            if (0 === $file[0]['error']) {
                if (!Tools::ZipTest($zipFilePath)) {
                    $errors[] = $this->module->l('Zip file seems to be broken', 'AdminEtsSeoImportExportController');
                }
            } else {
                $errors[] = $file[0]['error'];
            }
            if (!@file_exists($zipFilePath)) {
                $errors[] = $this->module->l('Zip file doesn\'t exist', 'AdminEtsSeoImportExportController');
            }
            if (!$errors) {
                $zip = new ZipArchive();
                if (true === $zip->open($zipFilePath)) {
                    if (false === $zip->locateName('ets_seo_data.xml')) {
                        $errors[] = $this->module->l('ets_seo_data.xml doesn\'t exist', 'AdminEtsSeoImportExportController');
                        if  (file_exists($zipFilePath)) {
                            @unlink($zipFilePath);
                        }
                    }
                } else {
                    $errors[] = $this->module->l('Cannot open zip file. It might be broken or damaged', 'AdminEtsSeoImportExportController');
                }
                if (@file_exists($xmlFilePath)) {
                    @unlink($xmlFilePath);
                }
                if (!Tools::ZipExtract($zipFilePath, $saveDirectory)) {
                    $errors[] = $this->module->l('Cannot extract zip data', 'AdminEtsSeoImportExportController');
                }
                if (!@file_exists($xmlFilePath)) {
                    $errors[] = $this->module->l('Neither ets_seo_data.xml exist', 'AdminEtsSeoImportExportController');
                }
                $zip->close();
            }
        } else {
            $errors[] = $this->module->l('Data import is null', 'AdminEtsSeoImportExportController');
        }

        return $errors;
    }

    public function _postImport()
    {
        $saveDirectory = EtsSeoDataImporter::DATA_FILE_DIRECTORY;
        $zipFilePath = $saveDirectory . DIRECTORY_SEPARATOR . 'ets_seo_data.zip';
        $xmlFilePath = $saveDirectory . DIRECTORY_SEPARATOR . 'ets_seo_data.xml';
        if (@file_exists($xmlFilePath)) {
            @unlink($xmlFilePath);
        }
        if (@file_exists($zipFilePath)) {
            @unlink($zipFilePath);
        }
    }

    /**
     * @return \EtsSeoJsDefHelper
     */
    public function getJsDefHelper()
    {
        return $this->module->getJsDefHelper();
    }
}
