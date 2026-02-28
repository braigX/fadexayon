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

/**
 * Class AdminEtsSeoAjaxController
 *
 * @property \Ets_Seo $module
 *
 * @mixin \ModuleAdminControllerCore
 */
class AdminEtsSeoAjaxController extends ModuleAdminController
{
    public function postProcess()
    {
        parent::postProcess();
        // Delete logo or avatar
        if ((int) Tools::isSubmit('etsSeoDeleteLogoImg')) {
            if (($name = Tools::getValue('config_name')) && in_array($name, ['ETS_SEO_SITE_ORIG_LOGO', 'ETS_SEO_FACEBOOK_DEFULT_IMG_URL', 'ETS_SEO_SITE_PERSON_AVATAR'])) {
                $image = Configuration::get($name);
                Configuration::updateValue($name, null);
                if (file_exists(_PS_ROOT_DIR_ . '/img/social/' . $image)) {
                    @unlink(_PS_ROOT_DIR_ . '/img/social/' . $image);
                }
                exit(json_encode([
                    'success' => true,
                ]));
            }
            exit(json_encode([
                'success' => false,
                'message' => $this->module->l('Cannot delete this image', 'AdminEtsSeoAjaxController'),
            ]));
        }
        if (Tools::getValue('etsSeoUploadSocialImage')) {
            if ($old_image = Tools::getValue('old_image')) {
                $this->deleteImage($old_image);
            }
            try {
                $img_path = $this->_uploadImage('image');
                if ($img_path) {
                    exit(json_encode([
                        'success' => true,
                        'image' => $img_path,
                    ]));
                }
            } catch (\PrestaShopException $e) {
                exit(json_encode([
                    'success' => false,
                    'image' => '',
                    'message' => $e->getMessage(),
                ]));
            }

            exit(json_encode([
                'success' => false,
                'image' => '',
            ]));
        }

        if (Tools::getValue('etsSeoDeleteSocialImg')) {
            if ($img_path = Tools::getValue('img_path')) {
                if ((int) Tools::getValue('id')) {
                    try {
                        if ($this->deleteDataSocialImg((int) Tools::getValue('id'), Tools::getValue('controller_type'), basename($img_path), (bool) Tools::getValue('is_cms_category'))) {
                            exit(json_encode([
                                'success' => true,
                            ]));
                        }
                    } catch (PrestaShopDatabaseException $e) {
                        exit(json_encode([
                            'success' => false,
                            'message' => $this->module->l('A database error occurred', 'AdminEtsSeoAjaxController'),
                        ]));
                    } catch (PrestaShopException $e) {
                        exit(json_encode([
                            'success' => false,
                            'message' => $this->module->l('Cannot delete this image', 'AdminEtsSeoAjaxController'),
                        ]));
                    }
                } else {
                    if ($this->deleteImage($img_path)) {
                        exit(json_encode([
                            'success' => true,
                        ]));
                    }
                }
            }
            exit(json_encode([
                'success' => false,
                'message' => $this->module->l('Cannot delete this image', 'AdminEtsSeoAjaxController'),
            ]));
        }

        // Save score init
        if (Tools::isSubmit('etsSeoSaveScore')) {
            $id = (int) Tools::getValue('id');
            $type_page = ($type_page = Tools::getValue('page_type')) && Validate::isCleanHtml($type_page) ? $type_page : '';
            $is_cms_category = (int) Tools::getValue('is_cms_category');
            if ($is_cms_category) {
                $type_page = 'AdminCmsCategory';
            }
            $seo_scores = ($seo_scores = Tools::getValue('seo_score')) && is_array($seo_scores) && Ets_Seo::validateArray($seo_scores) ? $seo_scores : [];
            $readability_scores = ($readability_scores = Tools::getValue('readability_score')) && is_array($readability_scores) && Ets_Seo::validateArray($readability_scores) ? $readability_scores : [];
            $content_analysis = ($content_analysis = Tools::getValue('content_analysis')) && is_array($content_analysis) && Ets_Seo::validateArray($content_analysis) ? $content_analysis : [];
            $this->updateSeoScore($id, $type_page, $seo_scores, $readability_scores, $content_analysis);
            exit(json_encode([
                'success' => true,
                'message' => $this->module->l('Success', 'AdminEtsSeoAjaxController'),
            ]));
        }

        if ((bool) Tools::isSubmit('validateLinkRewrite')) {
            $type = ($type = Tools::getValue('type')) && Validate::isCleanHtml($type) ? $type : '';
            $link_rewrites = ($link_rewrites = Tools::getValue('link_rewrites')) && is_array($link_rewrites) ? $link_rewrites : [];
            $id = (int) Tools::getValue('id');
            $is_cms_category = (int) Tools::getValue('is_cms_category');
            EtsSeoSetting::checkLinkRewriteAjax($type, $link_rewrites, $id, $is_cms_category);
        }
        if (Tools::isSubmit('etsSeoGetCategoryName')) {
            $idCate = (int) Tools::getValue('id_category');
            $cate = new Category($idCate);
            if ($cate->id && $cate->id_category) {
                exit(json_encode([
                    'success' => true,
                    'name' => $cate->name,
                ]));
            }
            exit(json_encode([
                'success' => false,
                'name' => [],
            ]));
        }
    }

    /**
     * @param int $id
     * @param string $type
     * @param array $seoScores
     * @param array $readabilityScores
     * @param array $contentAnalysis
     *
     * @throws \PrestaShopException
     */
    private function updateSeoScore($id, $type, $seoScores, $readabilityScores, $contentAnalysis)
    {
        $typeMap = [
            'AdminProducts' => EtsSeoProduct::class,
            'AdminCategories' => EtsSeoCategory::class,
            'AdminCmsContent' => EtsSeoCms::class,
            'AdminCmsCategory' => EtsSeoCmsCategory::class,
            'AdminManufacturers' => EtsSeoManufacturer::class,
            'AdminSuppliers' => EtsSeoSupplier::class,
            'AdminMeta' => EtsSeoMeta::class,
        ];
        if (!array_key_exists($type, $typeMap)) {
            throw new PrestaShopException($this->module->l('Invalid "type" selection. Can not save Seo Score.', 'AdminEtsSeoAjaxController'));
        }
        $idShop = $this->context->shop->id;
        $languages = Language::getLanguages(false);
        foreach ($languages as $language) {
            $idLang = $language['id_lang'];
            if (!Validate::isLoadedObject(new Language($idLang))) {
                continue;
            }
            if (!isset($contentAnalysis[$idLang])) {
                $contentAnalysis[$idLang] = [];
            }
            /** @var EtsSeoProduct|EtsSeoCategory|EtsSeoCms|EtsSeoCmsCategory|EtsSeoManufacturer|EtsSeoSupplier|EtsSeoMeta $model */
            if (($_id = $typeMap[$type]::findOneByRelationId($id, $idLang, $idShop))) {
                $model = new $typeMap[$type]($_id);
            }
            else{
                $model = new $typeMap[$type](null, $idLang, $idShop);
                $keyName = $typeMap[$type]::getRelationIdColumnName();
                $model->{$keyName} = $id;
            }
            $model->setSeoScore($seoScores, $readabilityScores, $contentAnalysis)->save();
        }
    }

    /**
     * @param int $id
     * @param string $type
     * @param string $filename
     * @param bool $isCmsCategory
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function deleteDataSocialImg($id, $type, $filename, $isCmsCategory = false)
    {
        if ($isCmsCategory) {
            $type = 'cms_category';
        }
        $objMap = [
            'product' => \EtsSeoProduct::class,
            'category' => \EtsSeoCategory::class,
            'manufacturer' => \EtsSeoManufacturer::class,
            'supplier' => \EtsSeoSupplier::class,
            'cms' => \EtsSeoCms::class,
            'cms_category' => \EtsSeoCmsCategory::class,
            'meta' => \EtsSeoMeta::class,
            'AdminProducts' => \EtsSeoProduct::class,
            'AdminCategories' => \EtsSeoCategory::class,
            'AdminManufacturers' => \EtsSeoManufacturer::class,
            'AdminSuppliers' => \EtsSeoSupplier::class,
            'AdminCmsContent' => \EtsSeoCms::class,
            'AdminMeta' => \EtsSeoMeta::class,
        ];
        if (!array_key_exists($type, $objMap)) {
            return false;
        }
        /** @var \EtsSeoProduct|\EtsSeoCategory|\EtsSeoManufacturer|\EtsSeoSupplier|\EtsSeoCms|\EtsSeoCmsCategory $model */
        /** @noinspection PhpUndefinedMethodInspection */
        $model = $objMap[$type]::findOneBySocialImg($id, $filename);
        $filePath = _PS_ROOT_DIR_ . '/img/social/' . $filename;
        $exist = @file_exists($filePath);
        $delFile = $exist ? @unlink($filePath) : false;
        if (($exist && $delFile) || !$exist) {
            $model->social_img = '';

            return $model->save();
        }

        return false;
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    private function deleteImage($filePath)
    {
        $filename = basename($filePath);
        $filePath = _PS_ROOT_DIR_ . '/img/social/' . $filename;

        return @unlink(_PS_ROOT_DIR_ . $filePath);
    }

    /**
     * uploadImage.
     *
     * @param string $key
     *
     * @return string|false
     *
     * @throws \PrestaShopException
     */
    private function _uploadImage($key)
    {
        if (isset($_FILES[$key])) {
            $allowExtensions = ['png', 'jpg', 'jpeg', 'gif'];
            $maxFileSize = 2097152;
            $mimeTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
            $ext = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
            if ($_FILES[$key]['error'] <= 0) {
                $img_name = time() . random_int(1111, 99999) . '.' . $ext;
                $img_path = '/img/social/';

                if (!($valid = ImageManager::validateUpload($_FILES[$key], $maxFileSize, $allowExtensions, $mimeTypes)) && move_uploaded_file($_FILES[$key]['tmp_name'], _PS_ROOT_DIR_ . $img_path . $img_name)) {
                    return __PS_BASE_URI__ . ltrim($img_path, '/') . $img_name;
                }
                if ($valid) {
                    throw new \PrestaShopException($valid);
                }
            }
        }

        return false;
    }
}
