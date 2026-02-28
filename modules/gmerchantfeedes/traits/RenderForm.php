<?php
/**
 * 2019 ExtraSolutions
 *
 * NOTICE OF LICENSE
 *
 * @author    ExtraSolutions
 * @copyright 2019 ExtraSolutions
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Symfony\Component\Filesystem\Filesystem;

if (!defined('_PS_VERSION_')) {
    exit;
}

trait RenderForm
{
//    private function copy
    public function processDuplicate($id_gmerchantfeedes)
    {
        if (Validate::isLoadedObject($gmerchantfeedes = new GMerchantFeedConfig((int)$id_gmerchantfeedes))) {
            $id_gmerchantfeedes_old = $gmerchantfeedes->id;
            $gmerchantfeedes->name = 'Copy ' . $gmerchantfeedes->name;

            $gmerchantfeedes->id = null;
            $gmerchantfeedes->id_gmerchantfeedes = null;

            $gmerchantfeedes->add();
            $gmerchantfeedes->id_gmerchantfeedes = $gmerchantfeedes->id;

            if (self::duplicateCustomRows($id_gmerchantfeedes_old, $gmerchantfeedes->id)
                && self::duplicateFeatureRows($id_gmerchantfeedes_old, $gmerchantfeedes->id)
                && self::duplicateAttributeRows($id_gmerchantfeedes_old, $gmerchantfeedes->id)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param $id_gmerchantfeed_old
     * @param $id_gmerchantfeed_new
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function duplicateCustomRows($id_gmerchantfeed_old, $id_gmerchantfeed_new)
    {
        $results = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_rows`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed_old);

        if (!$results) {
            return true;
        }

        $data = array();

        foreach ($results as $row) {
            $data[] = array(
                'id_gmerchantfeedes' => (int)$id_gmerchantfeed_new,
                'id_param' => pSQL($row['id_param']),
                'unit' => pSQL($row['unit'])
            );
        }

        return Db::getInstance()->insert('gmerchantfeedes_custom_rows', $data);
    }

    /**
     * @param $id_gmerchantfeed_old
     * @param $id_gmerchantfeed_new
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function duplicateFeatureRows($id_gmerchantfeed_old, $id_gmerchantfeed_new)
    {
        $results = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_features`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed_old);

        if (!$results) {
            return true;
        }

        $data = array();

        foreach ($results as $row) {
            $data[] = array(
                'id_gmerchantfeedes' => (int)$id_gmerchantfeed_new,
                'id_feature' => pSQL($row['id_feature']),
                'unit' => pSQL($row['unit'])
            );
        }

        return Db::getInstance()->insert('gmerchantfeedes_custom_features', $data);
    }

    /**
     * @param $id_gmerchantfeed_old
     * @param $id_gmerchantfeed_new
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function duplicateAttributeRows($id_gmerchantfeed_old, $id_gmerchantfeed_new)
    {
        $results = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'gmerchantfeedes_custom_attributes`
                WHERE `id_gmerchantfeedes` = ' . (int)$id_gmerchantfeed_old);

        if (!$results) {
            return true;
        }

        $data = array();

        foreach ($results as $row) {
            $data[] = array(
                'id_gmerchantfeedes' => (int)$id_gmerchantfeed_new,
                'id_attribute' => pSQL($row['id_attribute']),
                'unit' => pSQL($row['unit'])
            );
        }

        return Db::getInstance()->insert('gmerchantfeedes_custom_attributes', $data);
    }

    public function postProcess()
    {
        $this->ajaxPostProcess();

        if (Tools::isSubmit('clone') && Tools::getValue('id_gmerchantfeedes')) {
            $cloneGMID = Tools::getValue('id_gmerchantfeedes');
            if ($cloned = $this->processDuplicate($cloneGMID)) {
                $mainPage = AdminController::$currentIndex
                    . '&configure=' . urlencode($this->name)
                    . '&cloned=' . ($cloned ? 1 : 0)
                    . '&token=' . Tools::getAdminTokenLite('AdminModules');
                Tools::redirectAdmin($mainPage);
            }
        }

        if (Tools::isSubmit('cloned')) {
            if (Tools::getValue('cloned') == 1) {
                $this->confirmations[] = $this->displayConfirmation($this->l('Feed successfully duplicated.', 'renderform'));
            } else {
                $this->errors[] = $this->displayError($this->l('An error occurred while creating an object.', 'renderform'));
            }
        }

        if (Tools::isSubmit('verifyTables')) {
            $resUpdate = $this->verifyTableIntegrity();
            if (is_bool($resUpdate) && $resUpdate) {
                $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.', 'renderform'));
            } elseif (is_array($resUpdate) && isset($resUpdate['error']) && $resUpdate['error'] && isset($resUpdate['msg'])) {
                $this->errors[] = $this->displayError($resUpdate['msg']);
            }
        }

        if (Tools::isSubmit('resetHooks')) {
            $this->unregisterHook('displayAdminProductsExtra') &&
            $this->unregisterHook('actionProductUpdate') &&
            $this->unregisterHook('deleteproduct') &&
            $this->unregisterHook('backOfficeHeader');

            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('deleteproduct') &&
            $this->registerHook('backOfficeHeader');

            $this->confirmations[] = $this->displayConfirmation($this->l('The hook positions have been updated.', 'renderform'));
        }

        if (Tools::getValue('submitOptionalEdit')) {
            Configuration::updateValue('GMERCHANTFEEDS_ALT_JS', (bool)Tools::getValue('GMERCHANTFEEDS_ALT_JS', 0));
            Configuration::updateValue('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT', (bool)Tools::getValue('GMERCHANTFEEDS_FORCE_COUNTY_PRODUCT', 0));
            $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.', 'renderform'));
        }

        if (Tools::isSubmit('submitgmerchantfeedESModule') && !Tools::isSubmit('btnActionRemoveExcludeFile')) {
            $response = $this->updatePostModel(new GMerchantFeedConfig(Tools::getValue('id_gmerchantfeedes', null)));
            if (isset($response['error']) && $response['error'] == 1 && isset($response['message'])) {
                $this->errors[] = $this->displayError($response['message']);
            } elseif ($response) {
                $this->confirmations[] = $this->displayConfirmation($this->l('Setting is successfully updated!', 'renderform'));
                if (isset($_FILES['file_exclude_file_ids']) && !empty($_FILES['file_exclude_file_ids'])) {
                    $feedId = (int)Tools::getValue('id_gmerchantfeedes', null);
                    $sourceFolder = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'exclude_csv' . DIRECTORY_SEPARATOR;
                    if (isset($_FILES['file_exclude_file_ids']) && is_uploaded_file($_FILES['file_exclude_file_ids']['tmp_name'])) {
                        if (isset($_FILES['file_exclude_file_ids']['type'])
                            && ($_FILES['file_exclude_file_ids']['type'] == 'text/csv' || $_FILES['file_exclude_file_ids']['type'] == 'application/vnd.ms-excel')) {
                            if (!is_dir($sourceFolder)) {
                                mkdir($sourceFolder);
                                copy(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'index.php', $sourceFolder . 'index.php');
                            }
                            if (!is_dir($sourceFolder . $feedId)) {
                                mkdir($sourceFolder . $feedId);
                                copy(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'index.php', $sourceFolder . $feedId . DIRECTORY_SEPARATOR . 'index.php');
                            }
                            $file_name = 'exclude-csv-ids-' . $feedId . '.csv';
                            if (file_exists($sourceFolder . $file_name)) {
                                unlink($sourceFolder . $file_name);
                            }
                            if (!move_uploaded_file($_FILES['file_exclude_file_ids']['tmp_name'], $sourceFolder . $feedId . DIRECTORY_SEPARATOR . $file_name)) {
                                $this->errors[] = $this->displayError($this->l('Failed to copy the file.', 'renderform'));
                            }
                        } else {
                            $this->errors[] = $this->displayError($this->l('Upload error. Please check your upload file (*.csv) .', 'renderform'));
                        }
                    } elseif (array_key_exists('file_exclude_file_ids', $_FILES) && (int)$_FILES['file_exclude_file_ids']['error'] === 1) {
                        $max_upload = (int)ini_get('upload_max_filesize');
                        $max_post = (int)ini_get('post_max_size');
                        $upload_mb = min($max_upload, $max_post);
                        $this->errors[] = sprintf($this->displayError($this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.', 'renderform')), '', '<b>' . $_FILES['file_exclude_file_ids']['name'] . '</b> ', '<b>' . $upload_mb . '</b>');
                    }
                }
            } else {
                $this->errors[] = $this->displayError($this->l('Settings not updated', 'renderform'));
            }
        }

        if (Tools::isSubmit('submitTaxonomyLangEdit') && Validate::isInt(Tools::getValue('language_id'))) {
            $language_id = (int)Tools::getValue('language_id');
            $language = new Language($language_id);
            if (!Validate::isLoadedObject($language)) {
                return;
            }
            $language_code = $language->language_code;
            $language_code = trim($language_code);
            $language_code = Tools::strtolower($language_code);

            if (isset($_FILES['taxonomy_file']) && is_uploaded_file($_FILES['taxonomy_file']['tmp_name'])) {
                if (isset($_FILES['taxonomy_file']['type']) && $_FILES['taxonomy_file']['type'] == 'text/plain') {
                    if (!is_dir(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code)) {
                        mkdir(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code);
                        copy(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . 'index.php', _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . 'index.php');
                    }
                    $file_name = 'taxonomy-with-ids.' . $language_code . '.txt';
                    if (file_exists(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name)) {
                        unlink(_PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name);
                    }
                    if (!move_uploaded_file($_FILES['taxonomy_file']['tmp_name'], _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'google_taxonomy' . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name)) {
                        $this->errors[] = $this->displayError($this->l('Failed to copy the file.', 'renderform'));
                    } else {
                        $this->confirmations[] = $this->displayConfirmation($this->l('Configuration updated!', 'renderform'));
                    }
                } else {
                    $this->errors[] = $this->displayError($this->l('Upload error. Please check your upload file (*.txt) .', 'renderform'));
                }
            } elseif (array_key_exists('taxonomy_file', $_FILES) && (int)$_FILES['taxonomy_file']['error'] === 1) {
                $max_upload = (int)ini_get('upload_max_filesize');
                $max_post = (int)ini_get('post_max_size');
                $upload_mb = min($max_upload, $max_post);
                $this->errors[] = sprintf($this->displayError($this->l('The file %1$s exceeds the size allowed by the server. The limit is set to %2$d MB.', 'renderform')), '', '<b>' . $_FILES['taxonomy_file']['name'] . '</b> ', '<b>' . $upload_mb . '</b>');
            }
        }

        if (Tools::isSubmit('deleteFeed') && Tools::getValue('id_gmerchantfeedes') > 0) {
            $id_gmerchantfeed = (int)Tools::getValue('id_gmerchantfeedes');
            $feed = new GMerchantFeedConfig($id_gmerchantfeed);
            if (Validate::isLoadedObject($feed)) {
                $feed->delete();
                $this->confirmations[] = $this->displayConfirmation($this->l('The feed configuration has been successfully removed!', 'renderform'));
            }
        }

        if (Tools::isSubmit('btnActionRemoveExcludeFile')) {
            $id_gmerchantfeedes = (int)Tools::getValue('id_gmerchantfeedes');
            $sourceFolder = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'exclude_csv' . DIRECTORY_SEPARATOR . $id_gmerchantfeedes;
            $fs = new Filesystem();
            $fs->remove($sourceFolder);
            $this->confirmations[] = $this->displayConfirmation($this->l('The .csv file has been successfully removed!', 'renderform'));
        }
    }

    /**
     * @param $model
     * @return false|string
     */
    private function validationRules($model)
    {
        $errors = array();

        if (empty($model->name)) {
            $errors[] = 'Properties "Name of your data feed" is required!';
        }

        if (!empty($model->from_product_id) && !Validate::isInt($model->from_product_id)) {
            $errors[] = 'Properties "From product ID" required is integer!';
        }

        if (!empty($model->to_product_id) && !Validate::isInt($model->to_product_id)) {
            $errors[] = 'Properties "To product ID" required is integer!';
        }

        if (!empty($model->filter_qty_from) && !Validate::isInt($model->filter_qty_from)) {
            $errors[] = 'Properties "Product quantity from" required is integer!';
        }

        if (!empty($model->price_change) && !is_numeric($model->price_change)) {
            $errors[] = 'Properties "Price change" required is numeric!';
        }

        if (count($errors)) {
            return join('<br/>', $errors);
        }

        return false;
    }

    public function verifyTableIntegrity()
    {
        $tables = array(
            array(
                'name' => 'gmerchantfeedes',
                'fields' => array(
                    'from_product_id' => 'int',
                    'to_product_id' => 'int',
                    'filter_qty_from' => 'int',
                    'with_suppliers' => 'text',
                    'exclude_suppliers' => 'text',
                    'export_width' => 'tinyint',
                    'export_height' => 'tinyint',
                    'export_depth' => 'tinyint',
                    'export_width_inp' => 'varchar',
                    'export_height_inp' => 'varchar',
                    'export_depth_inp' => 'varchar',
                    'only_available' => 'tinyint',
                    'taxonomy_language' => 'int',
                    'exclude_discount_price_more' => 'float'
                )
            ),
            array(
                'name' => 'gmerchantfeedes_taxonomy',
                'fields' => array()
            ),
            array(
                'name' => 'gmerchantfeedes_custom_features',
                'fields' => array()
            ),
            array(
                'name' => 'gmerchantfeedes_custom_attributes',
                'fields' => array()
            )
        );

        foreach ($tables as $tableKey => $table) {
            $result = Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . pSQL($table['name']) . "'");
            if ($result) {
                $tableColumns = Db::getInstance()->executeS('SHOW COLUMNS FROM ' . _DB_PREFIX_ . pSQL($table['name']));
                if ($tableColumns && is_array($tableColumns) && count($tableColumns)) {
                    foreach ($tableColumns as $tableColumn) {
                        if (isset($tables[$tableKey]['fields'][$tableColumn['Field']])) {
                            unset($tables[$tableKey]['fields'][$tableColumn['Field']]);
                        }
                    }
                }
            }
        }

        foreach ($tables as $itemTable) {
            if (count($itemTable['fields'])) {
                $result = Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . pSQL($table['name']) . "'");
                if (!$result) {
                    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL($table['name']) . '` (';
                    $primary = '';
                    $sql_inner_fields = array();
                    foreach ($table['fields'] as $fieldKey => $field) {
                        $sql_inner_fields[] = '`' . pSQL($fieldKey) . '` ' . $this->getSqlByFieldType($field);
                        if ($field == 'int-autoincrement') {
                            $primary = ', PRIMARY KEY  (`' . pSQL($fieldKey) . '`)';
                        }
                    }
                    $sql .= join(', ', $sql_inner_fields) . $primary . ') ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
                    DB::getInstance()->execute($sql);
                } else {
                    foreach ($itemTable['fields'] as $fieldName => $fieldType) {
                        $tableType = '';
                        switch ($fieldType) {
                            case 'int-autoincrement':
                                $tableType = 'int(11) NOT NULL AUTO_INCREMENT';
                                break;
                            case 'int':
                                $tableType = 'INT(11)';
                                break;
                            case 'tinyint':
                                $tableType = 'TINYINT NOT NULL DEFAULT 0';
                                break;
                            case 'varchar':
                                $tableType = 'VARCHAR(256) NOT NULL';
                                break;
                            case 'text':
                                $tableType = 'TEXT DEFAULT NULL';
                                break;
                            case 'float':
                                $tableType = 'decimal(10, 3) NOT NULL DEFAULT 0';
                                break;
                            case 'date':
                                $tableType = 'DATETIME NULL';
                                break;
                        }
                        try {
                            Db::getInstance()->execute('
                            ALTER TABLE `' . _DB_PREFIX_ . pSQL($itemTable['name']) . '`
                            ADD COLUMN `' . pSQL($fieldName) . '` ' . pSQL($tableType));
                        } catch (Exception $e) {
                            return array(
                                'error' => true,
                                'msg' => $e->getMessage()
                            );
                        }
                    }
                }
            }
        }

        return true;
    }

    private function getSqlByFieldType($fieldType)
    {
        switch ($fieldType) {
            case 'int-autoincrement':
                return 'int(11) NOT NULL AUTO_INCREMENT';
                break;
            case 'int':
                return 'INT(11)';
                break;
            case 'tinyint':
                return 'TINYINT(2) NOT NULL DEFAULT 0';
                break;
            case 'varchar':
                return 'VARCHAR(256) NOT NULL';
                break;
            case 'text':
                return 'TEXT DEFAULT NULL';
                break;
            case 'date':
                return 'DATETIME NULL';
                break;
        }
    }

    private function prepareIDS($data = '')
    {
        $join_exclude_data = array();
        if (!empty($data)) {
            $expExcludeIds = explode(',', $data);
            if (count($expExcludeIds) > 0) {
                foreach ($expExcludeIds as $expExcludeId) {
                    if (Validate::isInt($expExcludeId) && $expExcludeId > 0
                        && !in_array($expExcludeId, $join_exclude_data)) {
                        $join_exclude_data[] = $expExcludeId;
                    }
                }
            }
        }

        return (is_array($join_exclude_data) && count($join_exclude_data)) ? join(',', $join_exclude_data) : '';
    }

    private function updatePostModel($model)
    {
        $fields = $model->getFields();
        foreach ($fields as $fieldName => $fieldVal) {
            if (Tools::getValue('submitgmerchantfeedESModule')) {
                $prepareData = Tools::getValue($fieldName, '');

                if ($fieldName == 'exclude_ids') {
                    $prepareData = $this->prepareIDS($prepareData);
                }
            } else {
                $prepareData = Tools::getValue($fieldName, $fieldVal);
            }

            if (is_array($prepareData)) {
                $model->{$fieldName} = json_encode($prepareData);
            } else {
                $model->{$fieldName} = trim($prepareData);
            }
        }

        if ($err = $this->validationRules($model)) {
            return array(
                'error' => 1,
                'message' => $err
            );
        }

        $model->date_update = date('Y-m-d H:i:s');

        return (bool)$model->save();
    }

    protected function ajaxPostProcess()
    {
        if (Tools::isSubmit('getTaxonomyOptionsLists') && Tools::getValue('getTaxonomyOptionsLists') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('getTaxonomyLang')
            && Validate::isInt(Tools::getValue('getTaxonomyLang')) && Tools::getValue('getTaxonomyLang') > 0
            && Tools::isSubmit('setInd') && Tools::getValue('setInd') > 0) {
            $taxonomyLists = self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false);
            $taxSelected = self::getTaxonomyCategoryLangLinkId((int)Tools::getValue('setInd'), (int)Tools::getValue('getTaxonomyLang'));
            $this->smarty->assign(array(
                'taxonomySelected' => $taxSelected,
                'taxonomyLists' => $taxonomyLists
            ));

            $template_config = $this->display($this->name, 'views/templates/admin/taxonomy_configure.tpl');

            die($template_config);
        }

        if (Tools::isSubmit('setTaxonomyOptionsLists') && Tools::getValue('setTaxonomyOptionsLists') == 1
            && Tools::getValue('ajax') && Tools::isSubmit('setTaxonomyLang') && Tools::getValue('setTaxonomyLang') > 0
            && Tools::getValue('setInd') > 0) {
            if (Validate::isInt(Tools::getValue('taxonomy_selected'))) {
                $update = $this->setTaxonomyCategoryLang((int)Tools::getValue('setInd'), (int)Tools::getValue('taxonomy_selected'), (int)Tools::getValue('setTaxonomyLang'));
            } else {
                $update = $this->setTaxonomyCategoryLang((int)Tools::getValue('setInd'), null, (int)Tools::getValue('setTaxonomyLang'));
            }
            die(json_encode($update));
        }

        if (Tools::getValue('getTaxonomyOptionsListsForBulk') && Tools::getValue('ajax')
            && Tools::isSubmit('getTaxonomyLang') && Validate::isInt(Tools::getValue('getTaxonomyLang'))
            && Tools::getValue('getTaxonomyLang') > 0) {
            $taxonomyLists = self::getGoogleTxtCategoryFeed((int)Tools::getValue('getTaxonomyLang'), false);
            $this->smarty->assign(array(
                'forbulk' => 1,
                'getTaxonomyLang' => (int)Tools::getValue('getTaxonomyLang'),
                'taxonomyLists' => $taxonomyLists));

            $template_config = $this->display($this->name, 'views/templates/admin/taxonomy_configure.tpl');

            die($template_config);
        }

        if ((bool)Tools::isSubmit('taxonomy_trunctable') === true) {
            $id_lang = (int)Tools::getValue('taxonomyLang');
            if (DB::getInstance()->delete(self::$tableKey . '_taxonomy', 'id_lang=' . (int)$id_lang)) {
                $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.', 'renderform'));
            } else {
                $this->errors[] = $this->displayError($this->l('The settings have not been updated!', 'renderform'));
            }
        }

        if ((bool)Tools::isSubmit('update_all_taxonomy_list') === true
            && Tools::getValue('update_all_taxonomy_item')) {
            $item = Tools::getValue('update_all_taxonomy_item');
            $item = explode('___', $item);
            $id_lang = (int)Tools::getValue('taxonomyLang');
            if (is_array($item) && count($item) == 2 && is_numeric($item[0])) {
                $allCat = Category::getAllCategoriesName();
                $rootCat = Configuration::get('PS_ROOT_CATEGORY');
                $data_insert = array();
                foreach ($allCat as $cat) {
                    if ($rootCat == $cat['id_category']) {
                        continue;
                    }
                    $data_insert[] = array(
                        'id_category' => (int)$cat['id_category'],
                        'id_taxonomy' => (int)$item[0],
                        'name_taxonomy' => pSQL($item[1]),
                        'id_lang' => (int)$id_lang
                    );
                }
                DB::getInstance()->delete(self::$tableKey . '_taxonomy', 'id_lang=' . (int)$id_lang);
                $insert = Db::getInstance()->insert(self::$tableKey . '_taxonomy', $data_insert, false, true);
                if ($insert) {
                    $this->confirmations[] = $this->displayConfirmation($this->l('The settings have been updated.', 'renderform'));
                } else {
                    $this->errors[] = $this->displayError($this->l('The settings have not been updated!', 'renderform'));
                }
            }
        }
    }

    private function getFillingTaxonomies()
    {
        $taxList = $this->getTaxLangList();
        foreach ($taxList as $idLang => &$taxomony) {
            if ($taxomony['taxonomy'] == 1) {
                $catQtyFilling = $this->getCountTaxonomiesCategoriesFill($idLang);
                if ($catQtyFilling && $catQtyFilling > 0) {
                    $qtyCategories = $this->getCountActiveCategories();
                    if ($qtyCategories < $catQtyFilling) {
                        $taxomony['filling'] = 2;
                    } else {
                        $taxomony['filling'] = 1;
                    }
                } else {
                    $taxomony['filling'] = 0;
                }
            }
        }

        return $taxList;
    }

    protected static function getCountActiveCategories()
    {
        $sql = 'SELECT count(id_category) FROM ' . _DB_PREFIX_ . 'category 
                WHERE active=1 
                AND id_category  NOT IN (1,2)
                AND is_root_category != 1';

        return Db::getInstance()->getValue($sql);
    }

    protected static function getCountTaxonomiesCategoriesFill($select_lang = '')
    {
        $sql = 'SELECT count(tax.id_category) 
                FROM ' . _DB_PREFIX_ . pSQL(self::$tableKey) . '_taxonomy AS tax
                INNER JOIN ' . _DB_PREFIX_ . 'category AS cat ON (tax.id_category = cat.id_category)'
            . ' WHERE tax.id_lang = ' . (int)$select_lang . ' AND cat.active=1';

        return Db::getInstance()->getValue($sql);
    }

    public function renderMainForm()
    {
        $this->context->smarty->assign(array(
            'languages' => Language::getLanguages(),
            'currentIndex' => self::$currentIndex,
            'taxonomies' => $this->getTaxLangList(),
            'taxonomiesLight' => $this->getFillingTaxonomies()
        ));

        $notice = (!empty($this->confirmations) && count($this->confirmations) > 0) ? join('<br/>', $this->confirmations) : '';
        $notice .= (!empty($this->errors) && count($this->errors) > 0) ? join('<br/>', $this->errors) : '';

        if (Tools::isSubmit('taxonomyForm') && Tools::getValue('language_id') > 0) {
            return $notice . $this->renderTaxonomyForm();
        }

        if (Tools::isSubmit('taxonomyCategoryForm') && Tools::getValue('language_id') > 0) {
            return $notice . $this->renderTaxonomyCategoryForm();
        }

        if (Tools::isSubmit('addNewFeed')
            || Tools::isSubmit('updateFeed') && Tools::getValue('id_gmerchantfeedes') > 0) {
            return $notice
                . $this->getFastLink()
                . $this->renderFeedForm();
        }

        return $notice
            . $this->display($this->name, 'views/templates/admin/main.tpl')
            . $this->renderFeedList();
    }


    public function getFastLink()
    {
        $feed = array();
        $id_gmerchantfeed = Tools::getValue('id_gmerchantfeedes', null);
        $merchantData = $this->getConfigFormValues();

        if (!empty($id_gmerchantfeed)) {
            $feed['cron'] = $this->context->link->getModuleLink(
                $this->name,
                'generation',
                array(
                    'key' => (int)$id_gmerchantfeed,
                    'token' => md5(_COOKIE_KEY_ . $id_gmerchantfeed)
                )
            );
            $feed['cron_rebuild'] = $this->context->link->getModuleLink(
                $this->name,
                'generation',
                array(
                    'key' => (int)$id_gmerchantfeed,
                    'token' => md5(_COOKIE_KEY_ . $id_gmerchantfeed),
                    'only_rebuild' => 1
                )
            );
            $feed['cron_download'] = $this->context->link->getModuleLink(
                $this->name,
                'generation',
                array(
                    'key' => (int)$id_gmerchantfeed,
                    'token' => md5(_COOKIE_KEY_ . $id_gmerchantfeed),
                    'only_download' => 1
                )
            );

            if ($merchantData['local_product_inventory_feed']) {
                $feed['cron_inventory_download'] = $this->context->link->getModuleLink(
                    $this->name,
                    'generation',
                    array(
                        'key' => (int)$id_gmerchantfeed,
                        'token' => md5(_COOKIE_KEY_ . $id_gmerchantfeed),
                        'inventory' => 1
                    )
                );
            }

            $this->context->smarty->assign(array(
                'feed' => $feed
            ));
        }

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/cron_link.tpl');
    }


    public function renderFeedList()
    {
        $this->context->smarty->assign(
            array(
                'feeds' => GMerchantFeedConfig::getFeedsForForms()
            )
        );

        return $this->display($this->name, 'views/templates/admin/feeds.tpl');
    }

    public function getTaxLangList()
    {
        $return = array();
        $langList = $this->context->controller->getLanguages();
        if (count($langList) > 0) {
            foreach ($langList as $langItem) {
                $taxonomy = 0;
                $iso_code = trim($langItem['iso_code']);
                $language_code = trim($langItem['language_code']);
                $language_code = Tools::strtolower($language_code);
                $_pathTaxonomy = _PS_MODULE_DIR_ . '/' . $this->name . '/google_taxonomy/' . $language_code . '/taxonomy-with-ids.' . $language_code . '.txt';

                if (file_exists($_pathTaxonomy)) {
                    $taxonomy = 1;
                }

                $return[$langItem['id_lang']] = array(
                    'name' => $langItem['name'],
                    'iso_code' => $iso_code,
                    'language_code' => $language_code,
                    'taxonomy' => $taxonomy
                );
            }
        }

        return $return;
    }

    public function renderTaxonomyForm()
    {
        $languageId = (int)Tools::getValue('language_id');
        $lang = new Language($languageId);

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Taxonomy List Settings: ', 'renderform') . ((isset($lang->language_code) && !empty($lang->language_code)) ? $lang->language_code : ''),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'file',
                        'label' => 'Taxonomy file ',
                        'name' => 'taxonomy_file',
                        'desc' => 'example file: 
                                   https://www.google.com/basepages/producttype/taxonomy-with-ids.it-IT.txt'
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'language_id',
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'renderform'),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex . '&configure=' . urlencode($this->name)
                            . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Back to main form', 'renderform'),
                        'icon' => 'process-icon-back'
                    )
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = self::$tableKey . '_taxonomy';
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTaxonomyLangEdit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => array(
                'language_id' => (int)Tools::getValue('language_id')
            ),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($form));
    }

    public function renderTaxonomyCategoryForm()
    {
        $select_lang = Tools::getValue('language_id');
        if (!$select_lang || !Validate::isInt($select_lang)) {
            $select_lang = $this->context->language->id;
        }

        $fields_list = array();
        $fields_list['id_category'] = array(
            'title' => $this->l('Category id', 'renderform'),
            'type' => 'text',
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'search' => false,
            'orderby' => false,
            'col' => 1
        );
        $fields_list['name'] = array(
            'title' => $this->l('Category name', 'renderform'),
            'type' => 'text',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-lg'
        );
        $fields_list['taxonomy_id'] = array(
            'title' => $this->l('Taxonomy id', 'renderform'),
            'type' => 'taxonomy_text',
            'search' => false,
            'orderby' => false,
            'class' => 'fixed-width-xs td_taxonomy_id'
        );
        $fields_list['name_taxonomy'] = array(
            'title' => $this->l('Taxonomy path', 'renderform'),
            'type' => 'taxonomy_text',
            'class' => 'taxonomy_breadcrumb',
            'remove_onclick' => true,
            'search' => false,
            'orderby' => false
        );
        $fields_list['taxonomy_lists'] = array(
            'title' => $this->l('Edit', 'renderform'),
            'type' => 'taxonomy_lists',
            'align' => 'right',
            'class' => 'text-right',
            'search' => false,
            'orderby' => false
        );

        $helper = new HelperList();
        $helper->module = $this;
        $helper->simple_header = false;
        $helper->title = $this->l('Google taxonomy link list / Associate google-taxonomy categories', 'renderform');
        $helper->identifier = 'id_category';
        $helper->actions = array(); //'edit'
        $helper->show_toolbar = true;
        $helper->shopLinkType = '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->table = self::$tableKey . '_taxonomy';
        $helper->table_id = 'module-' . self::$tableKey;
        $helper->currentIndex = self::$currentIndex . '&configure=' . urlencode($this->name);
        $helper->tpl_vars = array(
            'fields_value' => array(
                'tableKey' => self::$tableKey,
                'language_id' => $select_lang,
                'language_iso' => Language::getIsoById($select_lang)
            ),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
            'taxonomyLists' => array(), //self::getGoogleTxtCategoryFeed((int)$select_lang, false),
            'currentIndex' => self::$currentIndex . '&configure=' . urlencode($this->name)
                . '&token=' . Tools::getAdminTokenLite('AdminModules')
        );

        return $helper->generateList($this->getTaxonomyLinks($select_lang), $fields_list);
    }

    protected static function getGoogleTxtCategoryFeed($lang = '', $only_list = false)
    {
        if (empty($lang) || !Validate::isInt($lang)) {
            $lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $langInfo = Language::getLanguage((int)$lang);
        $language_code = trim($langInfo['language_code']);
        $language_code = Tools::strtolower($language_code);
        $file_name = 'taxonomy-with-ids.' . $language_code . '.txt';
        $_pathTaxonomy = _PS_MODULE_DIR_ . self::$tableKey . DIRECTORY_SEPARATOR . 'google_taxonomy'
            . DIRECTORY_SEPARATOR . $language_code . DIRECTORY_SEPARATOR . $file_name;

        if (!file_exists($_pathTaxonomy)) {
            return array();
        }

        $googleShoppingFeedCategory = array();
        $handle = fopen($_pathTaxonomy, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if ($line[0] != '#') {
                    $lineGSF = explode(' - ', $line);
                    $keyGSF = array_shift($lineGSF);
                    $keyGSF = trim($keyGSF);
                    $valGSF = join(' - ', $lineGSF);
                    $valGSF = trim($valGSF);
                    if (!$only_list) {
                        $googleShoppingFeedCategory[] = array('key' => $keyGSF, 'name' => $valGSF);
                    } else {
                        $googleShoppingFeedCategory[$keyGSF] = $valGSF;
                    }
                }
            }

            fclose($handle);
        }

        return $googleShoppingFeedCategory;
    }

    protected static function getTaxonomiesLinkID($select_lang = '')
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . pSQL(self::$tableKey) . '_taxonomy'
            . ((!empty($select_lang) && Validate::isInt($select_lang))
                ? ' WHERE `id_lang` = ' . (int)$select_lang : '');

        return Db::getInstance()->executeS($sql);
    }

    public function getTaxonomyLinks($select_lang = '')
    {
        $categoryList = self::getAllCategoriesName(null, $select_lang);
        $categoryLinkId = $this->getTaxonomiesLinkID($select_lang);

        $catListLink = array();
        foreach ($categoryLinkId as $categoryLinkId_item) {
            $catListLink[$categoryLinkId_item['id_category']][$categoryLinkId_item['id_lang']] = $categoryLinkId_item;
            if ($isoLang = Language::getIsoById($categoryLinkId_item['id_lang'])) {
                $catListLink[$categoryLinkId_item['id_category']][$categoryLinkId_item['id_lang']]['iso'] = $isoLang;
            }
        }

        foreach ($categoryList as &$catItem) {
            $taxonomy_inf = array();
            if (isset($catListLink[$catItem['id_category']][$select_lang]['id_taxonomy'])
                && is_numeric($catListLink[$catItem['id_category']][$select_lang]['id_taxonomy'])
                && $catListLink[$catItem['id_category']][$select_lang]['id_taxonomy'] > 0) {
                $taxonomy_inf['taxonomy_id'][] = array(
                    'iso' => $catListLink[$catItem['id_category']][$select_lang]['iso'],
                    'item' => (int)$catListLink[$catItem['id_category']][$select_lang]['id_taxonomy']
                );
            }
            if (isset($catListLink[$catItem['id_category']][$select_lang]['name_taxonomy'])
                && !empty($catListLink[$catItem['id_category']][$select_lang]['name_taxonomy'])) {
                $taxonomy_inf['name_taxonomy'][] = array(
                    'iso' => $catListLink[$catItem['id_category']][$select_lang]['iso'],
                    'item' => $catListLink[$catItem['id_category']][$select_lang]['name_taxonomy']
                );
            }
            if (isset($taxonomy_inf['taxonomy_id']) && isset($taxonomy_inf['name_taxonomy'])) {
                $catItem['taxonomy_id'] = (!empty($taxonomy_inf['taxonomy_id'])) ? $taxonomy_inf['taxonomy_id'] : '-';
                $catItem['name_taxonomy'] = (!empty($taxonomy_inf['name_taxonomy'])) ? $taxonomy_inf['name_taxonomy'] : '-';
            } else {
                $catItem['taxonomy_id'] = '-';
                $catItem['name_taxonomy'] = '-';
            }

            $catItem['class'] = 'text-nowrap';
        }

        return $categoryList;
    }

    public static function getAllCategoriesName($root_category = null, $id_lang = false, $active = true, $use_shop_restriction = true)
    {
        if (isset($root_category) && !Validate::isInt($root_category)) {
            die(Tools::displayError());
        }

        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }

        $cache_id = pSQL(self::$tableKey) . '::getAllCategoriesName_' . md5((int)$root_category . (int)$id_lang . (int)$active . (int)$use_shop_restriction);
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->executeS('SELECT c.id_category, cl.name, cl.id_lang FROM `' . _DB_PREFIX_ . 'category` c ' . ($use_shop_restriction ? Shop::addSqlAssociation('category', 'c') : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
				' . (isset($root_category) ? 'RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int)$root_category . ' AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright`' : '') . '
				WHERE 1 ' . ($id_lang ? 'AND `id_lang` = ' . (int)$id_lang : '') . ' ' . ($active ? ' AND c.`active` = 1' : '')
                . ' ORDER BY c.`level_depth` ASC');
            Cache::store($cache_id, $result);
        } else {
            $result = Cache::retrieve($cache_id);
        }

        return $result;
    }

    /**
     * @param $id_category
     * @param $lang
     * @return array|bool|object|null
     */
    protected static function getTaxonomyCategoryLangLinkId($id_category, $lang)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . pSQL(self::$tableKey) . '_taxonomy
                WHERE `id_category` = ' . (int)$id_category . ' AND `id_lang` = ' . (int)$lang;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * @param $category
     * @param $taxonomy_id
     * @param $lang
     * @return array|string
     * @throws PrestaShopDatabaseException
     */
    public function setTaxonomyCategoryLang($category, $taxonomy_id, $lang)
    {
        $exist_note = Db::getInstance()->getRow('SELECT `id_taxonomy`, `id_category`
                        FROM `' . _DB_PREFIX_ . pSQL(self::$tableKey) . '_taxonomy`
                            WHERE `id_category` = ' . (int)$category . ' AND `id_lang` = ' . (int)$lang);

        $taxonomy_lists = $this->getGoogleTxtCategoryFeed($lang, true);

        if ($taxonomy_id === null) {
            $update = Db::getInstance()->update(pSQL(self::$tableKey) . '_taxonomy', array(
                'id_taxonomy' => null,
                'name_taxonomy' => null
            ), '`id_category` = ' . (int)$category . ' AND `id_lang` = ' . (int)$lang);
            $language = Language::getLanguage($lang);

            return array(
                'taxonomy_id' => '-',
                'name_taxonomy' => '-',
                'language' => $language['iso_code'],
                'deleted' => (bool)$update
            );
        }

        if (!$exist_note) {
            $taxonomy_path = '';
            if (isset($taxonomy_lists[(int)$taxonomy_id])
                && !empty($taxonomy_lists[(int)$taxonomy_id])) {
                $taxonomy_path = (string)$taxonomy_lists[(int)$taxonomy_id];
            }
            $data_insert = array(
                'id_category' => (int)$category,
                'id_taxonomy' => (int)$taxonomy_id,
                'name_taxonomy' => pSQL($taxonomy_path),
                'id_lang' => (int)$lang
            );
            $update = Db::getInstance()->insert(pSQL(self::$tableKey) . '_taxonomy', $data_insert, false, true);
        } else {
            $taxonomy_path = '';
            if (isset($taxonomy_lists[(int)$taxonomy_id])
                && !empty($taxonomy_lists[(int)$taxonomy_id])) {
                $taxonomy_path = (string)$taxonomy_lists[(int)$taxonomy_id];
            }
            $data_update = array(
                'id_taxonomy' => (int)$taxonomy_id,
                'name_taxonomy' => pSQL($taxonomy_path)
            );
            $update = Db::getInstance()->update(pSQL(self::$tableKey) . '_taxonomy', $data_update, '`id_category` = ' . (int)$category . ' AND `id_lang` = ' . (int)$lang);
        }

        if ($update) {
            $language = Language::getLanguage($lang);
            return array(
                'taxonomy_id' => (int)$taxonomy_id,
                'name_taxonomy' => $taxonomy_path,
                'language' => $language['iso_code'],
                'update' => 1
            );
        }

        return '';
    }

    protected function renderFeedForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitgmerchantfeedESModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . ((Tools::isSubmit('updateFeed') ? '&updateFeed' : ''))
            . (((Tools::getValue('id_gmerchantfeedes') > 0) ? '&id_gmerchantfeedes=' . Tools::getValue('id_gmerchantfeedes') : ''));
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(false),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    private function getFieldDataByModel($model)
    {
        return $model->getFields(true);
    }

    protected function getConfigFormValues()
    {
        $data = $this->getFieldDataByModel(new GMerchantFeedConfig(Tools::getValue('id_gmerchantfeedes', null)));

        return $data;
    }

    /**
     * @param $id_gmerchantfeedes
     * @return array|false
     */
    protected function getCsvExcludeFile($id_gmerchantfeedes)
    {
        if (!$id_gmerchantfeedes || !Validate::isInt($id_gmerchantfeedes) || !($id_gmerchantfeedes > 0)) {
            return false;
        }

        $sourceFolder = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'exclude_csv' . DIRECTORY_SEPARATOR;

        if (file_exists($sourceFolder . $id_gmerchantfeedes . DIRECTORY_SEPARATOR . 'exclude-csv-ids-' . $id_gmerchantfeedes . '.csv')) {
            $handle = fopen($sourceFolder . $id_gmerchantfeedes . DIRECTORY_SEPARATOR . 'exclude-csv-ids-' . $id_gmerchantfeedes . '.csv', "r");
            $productsIDSFound = array();
            while (($data = fgetcsv($handle)) !== false) {
                $productsIDSFound[] = $data[0];
            }

            return array(
                'products_ids' => count($productsIDSFound),
                'ids' => $productsIDSFound
            );
        }

        return false;
    }

    protected function getConfigForm()
    {
        $attribute_lists = AttributeGroup::getAttributesGroups((int)$this->context->language->id);
        $feature_lists = Feature::getFeatures((int)$this->context->language->id);

        $articleTypes = array(
            array(
                'name' => $this->l('EAN-13 or JAN barcode', 'renderform'),
                'key' => 'ean_13_jan'
            ),
            array(
                'name' => $this->l('UPC barcode', 'renderform'),
                'key' => 'upc'
            ),
            array(
                'name' => $this->l('ISBN', 'renderform'),
                'key' => 'isbn'
            ),

            array(
                'name' => $this->l('Reference', 'renderform'),
                'key' => 'reference'
            ),
            array(
                'name' => $this->l('Supplier reference', 'renderform'),
                'key' => 'supplier_reference'
            ),
        );

        if (isset($this->gmfOptions['mpn']) && $this->gmfOptions['mpn']) {
            $articleTypes[] = array(
                'name' => $this->l('MPN', 'renderform'),
                'key' => 'mpn'
            );
        }

        $form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Google Shopping Feed Settings:', 'renderform'),
                    'icon' => 'icon-cogs',
                ),
                'tabs' => array(
                    'general' => $this->l('General', 'renderform'),
                    'combination' => $this->l('Combinations', 'renderform'),
                    'custom_option' => $this->l('Custom options', 'renderform'),
                    'configuration' => $this->l('Filter configuration', 'renderform'),
                    'other' => $this->l('Other settings', 'renderform'),
                    'product_inventory' => $this->l('Local product inventory', 'renderform')
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'tab' => 'general',
                        'maxchar' => 50,
                        'class' => 'input fixed-width-xxl',
                        'label' => $this->l('Name of your data feed', 'renderform'),
                        'required' => true,
                        'desc' => $this->l('No more than 50 characters', 'renderform'),
                        'name' => 'name'
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Images type', 'renderform'),
                        'name' => 'type_image',
                        'options' => array(
                            'query' => array_merge(array(
                                array(
                                    'id_image_type' => '0',
                                    'name' => $this->l('Original', 'renderform')
                                )
                            ), ImageType::getImagesTypes(null, true)),
                            'id' => 'id_image_type',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Additional image', 'renderform'),
                        'desc' => $this->l('No more 10 image', 'renderform'),
                        'col' => 4,
                        'name' => 'additional_image',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Description type', 'renderform'),
                        'name' => 'type_description',
                        'options' => array(
                            'query' => $this->getTypeDescription(),
                            'id' => 'desc_key',
                            'name' => 'name'
                        ),
                        'desc' => $this->l('Your product’s description (Max 5000 characters)', 'renderform'),
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Select Lang', 'renderform'),
                        'required' => true,
                        'name' => 'select_lang',
                        'options' => array(
                            'query' => Language::getLanguages(),
                            'id' => 'id_lang',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'required' => true,
                        'label' => $this->l('Currency', 'renderform'),
                        'name' => 'id_currency',
                        'options' => array(
                            'query' => Currency::getCurrencies(false, true, true),
                            'id' => 'id_currency',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'required' => true,
                        'label' => $this->l('Select country, this need to calculate a shipping cost', 'renderform'),
                        'name' => 'id_country',
                        'multiple' => false,
                        'options' => array(
                            'query' => Country::getCountries((int)$this->context->language->id, true),
                            'id' => 'id_country',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'required' => true,
                        'col' => 4,
                        'label' => $this->l('Manage the shipping cost by carrier id', 'renderform'),
                        'desc' => $this->l('"By carrier id" is old approach and will be deprecated soon.', 'renderform'),
                        'name' => 'id_carrier[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => Carrier::getCarriers((int)$this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                            'id' => 'id_carrier',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'required' => true,
                        'col' => 4,
                        'label' => $this->l('Manage the shipping cost by reference id', 'renderform'),
                        'desc' => $this->l('"By reference id" has more priority than "By carrier id"', 'renderform'),
                        'name' => 'id_reference[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => Carrier::getCarriers((int)$this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS),
                            'id' => 'id_reference',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        )
                    ),
                    array(
                        'tab' => 'general',
                        'type' => 'switch_with_inp',
                        'label' => $this->l('Width', 'renderform'),
                        'name' => 'export_width',
                        'placeholder' => $this->l('cm or inch', 'renderform'),
                        'desc' => $this->l('Limits: 1 - 400 for cm, 1 - 150 for inch', 'renderform'),
                        'values' => array(
                            array(
                                'id' => 'enabled_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'enabled_off',
                                'value' => 0
                            )
                        )
                    ),
                    array(
                        'tab' => 'general',
                        'type' => 'switch_with_inp',
                        'label' => $this->l('Height', 'renderform'),
                        'name' => 'export_height',
                        'placeholder' => $this->l('cm or inch', 'renderform'),
                        'desc' => $this->l('Limits: 1 - 400 for cm, 1 - 150 for inch', 'renderform'),
                        'values' => array(
                            array(
                                'id' => 'enabled_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'enabled_off',
                                'value' => 0
                            )
                        )
                    ),
                    array(
                        'tab' => 'general',
                        'type' => 'switch_with_inp',
                        'label' => $this->l('Depth (Length)', 'renderform'),
                        'name' => 'export_depth',
                        'placeholder' => $this->l('cm or inch', 'renderform'),
                        'desc' => $this->l('Limits: 1 - 400 for cm, 1 - 150 for inch', 'renderform'),
                        'values' => array(
                            array(
                                'id' => 'enabled_on',
                                'value' => 1
                            ),
                            array(
                                'id' => 'enabled_off',
                                'value' => 0
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Use Additional shipping cost', 'renderform'),
                        'col' => 4,
                        'name' => 'use_additional_shipping_cost',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Brand', 'renderform'),
                        'col' => 4,
                        'name' => 'brand_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'name' => $this->l('None'),
                                    'key' => ''
                                ),
                                array(
                                    'name' => $this->l('Manufacturer', 'renderform'),
                                    'key' => 'manufacturer'
                                ),
                                array(
                                    'name' => $this->l('Supplier', 'renderform'),
                                    'key' => 'supplier'
                                ),
                            ),
                            'id' => 'key',
                            'name' => 'name',
                        ),
                        'desc' => $this->l('For <g:brand> use manufacturer or supplier (default supplier) field', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('GTIN', 'renderform'),
                        'col' => 4,
                        'name' => 'gtin_type[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => $articleTypes,
                            'id' => 'key',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('A Global Trade Item Number (GTIN) is a unique and internationally recognized identifier for a product.', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Manufacturer Part Number (MPN)', 'renderform'),
                        'col' => 4,
                        'name' => 'mpn_type[]',
                        'class' => 'chosen',
                        'multiple' => true,
                        'options' => array(
                            'query' => $articleTypes,
                            'id' => 'key',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('Use the mpn attribute to submit your product’s Manufacturer Part Number (MPN). Required for all products without a manufacturer-assigned GTIN.', 'renderform')
                    ),
                    array(
                        'type' => 'html',
                        'tab' => 'general',
                        'col' => 4,
                        'name' => 'note_inf',
                        'html_content' => '<p>' . $this->l('Note: If your product is new (which you submit through the condition attribute) and it does not have a gtin and brand or mpn and brand then will submit the products without any gtin or mpn.', 'renderform') . '</p>'
                    ),
                    array(
                        'type' => 'separator',
                        'name' => '',
                        'col' => 4,
                        'tab' => 'general',
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Export products without reference to taxonomy', 'renderform'),
                        'name' => 'taxonomy_ref',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Only Active product', 'renderform'),
                        'name' => 'only_active',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Export product when it is out of stock (Availability preferences Behavior)', 'renderform'),
                        'desc' => $this->l('Works only when the StockManager is active', 'renderform'),
                        'name' => 'rule_out_of_stock',
                        'options' => array(
                            'query' => [
                                [
                                    'id_param' => 0,
                                    'name' => $this->l('None (Ignore this option)', 'renderform'),
                                ],
                                [
                                    'id_param' => 1,
                                    'name' => $this->l('Products with "Deny orders" param', 'renderform'),
                                ],
                                [
                                    'id_param' => 2,
                                    'name' => $this->l('Products with "Allow orders" param', 'renderform'),
                                ]
                            ],
                            'id' => 'id_param',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Exclude out of stock products', 'renderform'),
                        'desc' => $this->l('Works only when the StockManager is active', 'renderform'),
                        'col' => 4,
                        'name' => 'export_non_available',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Only Available for order (yes / no)', 'renderform'),
                        'name' => 'only_available',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Export product quantity', 'renderform'),
                        'col' => 4,
                        'name' => 'export_product_quantity',
                        'desc' => $this->l('If enabled: correct statuses: preorder / in stock / out of stock, else if disabled: all-stock in the status: in_stock', 'renderform'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Do not take into account the option "Allow ordering goods that are out of stock"', 'renderform'),
                        'col' => 4,
                        'name' => 'param_order_out_of_stock_sys',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'general',
                        'label' => $this->l('Enable feature', 'renderform'),
                        'col' => 4,
                        'name' => 'export_feature',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Products gender feature', 'renderform'),
                        'name' => 'get_features_gender',
                        'options' => array(
                            'query' => $feature_lists,
                            'id' => 'id_feature',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('male/female/unisex', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'general',
                        'label' => $this->l('Products age group feature', 'renderform'),
                        'name' => 'get_features_age_group',
                        'options' => array(
                            'query' => $feature_lists,
                            'id' => 'id_feature',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('newborn/infant/toddler/kids/adult Required (For all clothing items that target Brazil, France, Germany, Japan, the UK and the US as well as all products with assigned age groups) Your product’s targeted demographic', 'renderform'),
                        'col' => 4
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('Export attributes combinations', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attributes',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('URLs for combination (Yes / No)', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attribute_url',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('Use Combinations Prices (Yes/No)', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attribute_prices',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('Use Combinations Images (Yes/No)', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attribute_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('Only one default product from Combination', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attributes_only_first',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'combination',
                        'label' => $this->l('Export combinations as separated products (Yes / No)', 'renderform'),
                        'col' => 4,
                        'name' => 'export_attributes_as_product',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'combination',
                        'label' => $this->l('Products color attribute', 'renderform'),
                        'name' => 'get_attribute_color[]',
                        'col' => 4,
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('Black, OR several attribute: Black/Green', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'combination',
                        'label' => $this->l('Products material attribute', 'renderform'),
                        'name' => 'get_attribute_material[]',
                        'col' => 4,
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('Cotton OR several attribute: CottonPolyesterElastane -> /polyester/elastane', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'combination',
                        'label' => $this->l('Products pattern attribute', 'renderform'),
                        'name' => 'get_attribute_pattern[]',
                        'col' => 4,
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('Enabled item group id', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'combination',
                        'label' => $this->l('Products size attribute', 'renderform'),
                        'name' => 'get_attribute_size[]',
                        'col' => 4,
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',

                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        ),
                        'desc' => $this->l('XXS, XS, S, M, L, XL, 1XL, 2XL, 3XL, 4XL, 5XL, 6XL.
                                    00, 0, 02, 04, 06, 08, 10, 12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34.
                                    23, 24, 26, 27, 28, 29, 30, 32, 34, 36, 38, 40, 42, 44...', 'renderform')
                    ),
                    array(
                        'type' => 'custom_attribute',
                        'tab' => 'combination',
                        'label' => $this->l('Custom attribute set', 'renderform'),
                        'name' => 'custom_attribute[]',
                        'col' => 6,
                        'class' => 'chosen fixed-width-xxl',
                        'multiple' => true,
                        'options' => array(
                            'query' => $attribute_lists,
                            'id' => 'id_attribute_group',
                            'name' => 'name',
                            'default' => array(
                                'value' => 0,
                                'label' => $this->l('-- no selected --', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'custom_param',
                        'tab' => 'custom_option',
                        'label' => $this->l('Custom parameters for properties (features)', 'renderform'),
                        'name' => 'features_custom_mod',
                        'features' => Feature::getFeatures($this->context->language->id),
                        'col' => 6
                    ),
                    array(
                        'type' => 'custom_product_param',
                        'tab' => 'custom_option',
                        'label' => $this->l('Other parameters', 'renderform'),
                        'name' => 'custom_product_row',
                        'options' => array(
                            'product_title' => $this->l('Product title', 'renderform'),
                            'product_id' => $this->l('Product id', 'renderform'),
                            'product_category_title' => $this->l('Product category title', 'renderform'),
                            'product_price' => $this->l('Product price', 'renderform'),
                            'product_cost_price' => $this->l('Product cost price', 'renderform'),
                            'product_qty' => $this->l('Product quantity', 'renderform'),
                            'product_condition' => $this->l('Product condition', 'renderform'),
                            'product_brand' => $this->l('Product brand', 'renderform'),
                            'product_supplier' => $this->l('Product supplier', 'renderform'),
                            'price_per_unit' => $this->l('Price per unit', 'renderform'),
                            'product_url' => $this->l('Product URL', 'renderform'),
                        ),
                        'col' => 6
                    ),
                    array(
                        'type' => 'textarea_clean',
                        'tab' => 'custom_option',
                        'col' => 6,
                        'rows' => 4,
                        'label' => $this->l('Additional code for each product', 'renderform'),
                        'desc' => $this->l('Example: <g:{you custom field}>{you custom value}</g:{you custom field}>', 'renderform'),
                        'name' => 'additional_each_product'
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'custom_option',
                        'col' => 6,
                        'class' => 'input',
                        'label' => $this->l('Additional product title', 'renderform'),
                        'desc' => $this->l('Example: PREFIX {title} SUFFIX ( {color}, {material}, {pattern}, {size}, {manufacturer_name}, {main_product_category}, {feature:ID_FEATURE_GROUP})', 'renderform'),
                        'name' => 'title_suffix'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'custom_option',
                        'label' => $this->l('Enable feature title in the product title', 'renderform'),
                        'name' => 'suffix_feature_title_set',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'custom_option',
                        'label' => $this->l('Enable attribute title in the product title', 'renderform'),
                        'name' => 'suffix_attribute_title_set',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'custom_option',
                        'col' => 6,
                        'class' => 'input',
                        'label' => $this->l('Additional for ID (g:id)', 'renderform'),
                        'desc' => $this->l('Example: PREFIX {ID} {reference} SUFFIX', 'renderform'),
                        'name' => 'id_suffix'
                    ),
                    array(
                        'type' => 'textarea_clean',
                        'tab' => 'custom_option',
                        'col' => 6,
                        'rows' => 4,
                        'label' => $this->l('Additional product description', 'renderform'),
                        'desc' => $this->l('Example: PREFIX {description} SUFFIX', 'renderform'),
                        'name' => 'description_suffix'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'configuration',
                        'label' => $this->l('Filter by asssociated categories', 'renderform'),
                        'desc' => $this->l('If NO filtered works by Main category', 'renderform'),
                        'col' => 4,
                        'name' => 'filtered_by_associated_type',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'categories',
                        'label' => $this->l('By categories', 'renderform'),
                        'name' => 'category_filter',
                        'tab' => 'configuration',
                        'tree' => array(
                            'id' => 'categories-tree',
                            'selected_categories' => $this->getCategoryFilterSelected(),
                            'root_category' => (int)Category::getRootCategory()->id,
                            'use_search' => true,
                            'use_checkbox' => true
                        ),
                        'desc' => $this->l('Default, all enabled categories', 'renderform')
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Manufacturers', 'renderform'),
                        'name' => 'manufacturers_filter[]',
                        'class' => 'chosen fixed-width-xxl',
                        'tab' => 'configuration',
                        'multiple' => true,
                        'options' => array(
                            'query' => Manufacturer::getManufacturers(false, (int)Context::getContext()->cookie->id_lang),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude Manufacturers', 'renderform'),
                        'name' => 'manufacturers_exclude_filter[]',
                        'class' => 'chosen fixed-width-xxl',
                        'tab' => 'configuration',
                        'multiple' => true,
                        'options' => array(
                            'query' => Manufacturer::getManufacturers(false, (int)Context::getContext()->cookie->id_lang),
                            'id' => 'id_manufacturer',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select suppliers', 'renderform'),
                        'name' => 'with_suppliers[]',
                        'class' => 'chosen fixed-width-xxl',
                        'tab' => 'configuration',
                        'multiple' => true,
                        'options' => array(
                            'query' => Supplier::getSuppliers(false, (int)Context::getContext()->cookie->id_lang),
                            'id' => 'id_supplier',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Exclude suppliers', 'renderform'),
                        'name' => 'exclude_suppliers[]',
                        'class' => 'chosen fixed-width-xxl',
                        'tab' => 'configuration',
                        'multiple' => true,
                        'options' => array(
                            'query' => Supplier::getSuppliers(false, (int)Context::getContext()->cookie->id_lang, false),
                            'id' => 'id_supplier',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'configuration',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('Minimum product price', 'renderform'),
                        'desc' => $this->l('Only numeric 1, 2.55, 0.99, 3...9999', 'renderform'),
                        'name' => 'min_price_filter'
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'configuration',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('Maximum product price', 'renderform'),
                        'desc' => $this->l('Only numeric 1, 2.55, 0.99, 3...9999', 'renderform'),
                        'name' => 'max_price_filter'
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'configuration',
                        'col' => 6,
                        'class' => 'input',
                        'label' => $this->l('Exclude products from the feed by ID', 'renderform'),
                        'desc' => $this->l('For example: 1,3,25,36...', 'renderform'),
                        'name' => 'exclude_ids'
                    ),
                    array(
                        'type' => 'file_csv',
                        'tab' => 'configuration',
                        'col' => 9,
                        'class' => 'input',
                        'label' => $this->l('Upload .CVS file with exclude product id', 'renderform'),
                        'name' => 'exclude_file_ids',
                        'example_url' => __PS_BASE_URI__ . basename(_PS_MODULE_DIR_)
                            . DIRECTORY_SEPARATOR . $this->name
                            . DIRECTORY_SEPARATOR . 'config'
                            . DIRECTORY_SEPARATOR . 'exclude_csv/exclude-example.csv',
                        'value' => $this->getCsvExcludeFile(Tools::getValue('id_gmerchantfeedes', null))
                    ),
                    array(
                        'type' => 'radio',
                        'tab' => 'configuration',
                        'label' => $this->l('Products with Sale Price', 'renderform'),
                        'name' => 'export_sale',
                        'values' => array(
                            array(
                                'id' => 'all_products',
                                'value' => 0,
                                'label' => $this->l('All products', 'renderform'),
                            ),
                            array(
                                'id' => 'with_sale',
                                'value' => 1,
                                'label' => $this->l('Only products with Sale Price', 'renderform'),
                            ),
                            array(
                                'id' => 'without_sale',
                                'value' => 2,
                                'label' => $this->l('Exclude products with Sale Price', 'renderform'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'configuration',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('From product ID', 'renderform'),
                        'desc' => $this->l('Only integers 0, 1, 300...9999', 'renderform'),
                        'name' => 'from_product_id'
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'configuration',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('To product ID', 'renderform'),
                        'desc' => $this->l('Only integers 0, 1, 300...9999', 'renderform'),
                        'name' => 'to_product_id'
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'configuration',
                        'col' => 6,
                        'class' => 'input',
                        'label' => $this->l('Product quantity from', 'renderform'),
                        'desc' => $this->l('Only numeric 1, 2.55, 0.99, 3...9999', 'renderform'),
                        'name' => 'filter_qty_from'
                    ),

                    array(
                        'type' => 'switch',
                        'tab' => 'configuration',
                        'label' => $this->l('Only New products', 'renderform'),
                        'desc' => $this->l('New means "Number of days for which the product is considered "new""', 'renderform'),
                        'name' => 'export_only_new_products',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'configuration',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('Exclude if the sale discount is more than, %', 'renderform'),
                        'name' => 'exclude_discount_price_more'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'configuration',
                        'label' => $this->l('Only products with image', 'renderform'),
                        'col' => 4,
                        'name' => 'filtered_by_only_with_image',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'configuration',
                        'label' => $this->l('Exclude products if field description is empty', 'renderform'),
                        'col' => 4,
                        'name' => 'exclude_empty_description',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Products descriptions - Max size: 5000', 'renderform'),
                        'col' => 4,
                        'name' => 'description_crop',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Uppercase to lowercase for Title (yes / no)', 'renderform'),
                        'col' => 4,
                        'name' => 'modify_uppercase_title',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Uppercase to lowercase for Description (yes / no)', 'renderform'),
                        'col' => 4,
                        'name' => 'modify_uppercase_description',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Use the product name in the product_type tag (In breadcrums)', 'renderform'),
                        'name' => 'product_title_in_product_type',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Rounding of Prices (Yes/No)', 'renderform'),
                        'desc' => $this->l('Works for Price and Sale Price,
                                                If price < 1.00 than rounding rule is: 0.01-0.99 -> 1.00, 
                                                If price >= 1.00 than rounding rule is: 1.49 -> 1.00 / 1.50 -> 2', 'renderform'),
                        'name' => 'rounding_price',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'textarea',
                        'tab' => 'other',
                        'col' => 6,
                        'class' => 'input',
                        'label' => $this->l('Additional code at the end of the products URL', 'renderform'),
                        'desc' => $this->l('Example: ?SubmitCurrency=1&id_currency=1 or ?id_country=147', 'renderform'),
                        'name' => 'url_suffix'
                    ),
                    array(
                        'type' => 'separator',
                        'name' => '',
                        'col' => 4,
                        'tab' => 'other',
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'other',
                        'label' => $this->l('Enable tax', 'renderform'),
                        'name' => 'instance_of_tax',
                        'desc' => $this->l('Select whether or not to include tax on purchases.', 'renderform'),
                        'options' => array(
                            'query' => $this->getTaxOptions(),
                            'id' => 'id_tax',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'other',
                        'label' => $this->l('"shipping_weight" format', 'renderform'),
                        'name' => 'shipping_weight_format',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id_swf' => '0',
                                    'name' => '5.000',
                                ),
                                array(
                                    'id_swf' => '1',
                                    'name' => '5.00',
                                ),
                                array(
                                    'id_swf' => '2',
                                    'name' => '5',
                                )
                            ),
                            'id' => 'id_swf',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('google_product_category by ID (Yes / No)', 'renderform'),
                        'desc' => $this->l('Using google_product_category ID instead category name', 'renderform'),
                        'name' => 'google_product_category_rewrite',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'select',
                        'tab' => 'other',
                        'required' => true,
                        'label' => $this->l('Taxonomy language', 'renderform'),
                        'name' => 'taxonomy_language',
                        'options' => array(
                            'query' => array_merge(array(
                                array(
                                    'id_lang' => 0,
                                    'name' => $this->l('Default', 'renderform')
                                )
                            ), Language::getLanguages()),
                            'id' => 'id_lang',
                            'name' => 'name'
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Do not show identifier_exists tag in the feed', 'renderform'),
                        'name' => 'disable_tag_identifier_exists',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('if product GTIN doesn\'t exist, identifier_exists will be always NO', 'renderform'),
                        'name' => 'identifier_exists_mpn',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Always show MPN', 'renderform'),
                        'name' => 'mpn_force_on',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Export only Visible products (Yes / No)', 'renderform'),
                        'name' => 'visible_product_hide',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'separator',
                        'name' => '',
                        'col' => 4,
                        'tab' => 'other',
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Installment (for Brasilia or Mexico)', 'renderform'),
                        'name' => 'parts_payment_enabled',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'other',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('Maximum number of parts = k1', 'renderform'),
                        'desc' => $this->l('Only integers 1,2,3...99 / price B = k1 (Price / k1)', 'renderform'),
                        'name' => 'max_parts_payment'
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'other',
                        'class' => 'input fixed-width-md',
                        'label' => $this->l('Interest rates = K', 'renderform'),
                        'desc' => $this->l('0.00 > 1.00 / price A = Price (1 - K)', 'renderform'),
                        'name' => 'interest_rates'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'other',
                        'label' => $this->l('Make Sale_price as Main Price in the feed', 'renderform'),
                        'name' => 'only_once_show_the_price',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'discount_type',
                        'tab' => 'other',
                        'label' => $this->l('Price change'),
                        'name' => 'price_change',
                        'desc' => $this->l('Works for <g:price> and <g:sale_price> / (\'-\' reduces prices)'),
                        'col' => 8
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'id_gmerchantfeedes',
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'product_inventory',
                        'label' => $this->l('Enable Local product inventory feed', 'renderform'),
                        'name' => 'local_product_inventory_feed',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),
                    array(
                        'type' => 'text',
                        'tab' => 'product_inventory',
                        'class' => 'input fixed-width-xxl',
                        'maxchar' => 150,
                        'label' => $this->l('Store code', 'renderform'),
                        'desc' => $this->l('No more than 150 characters', 'renderform'),
                        'name' => 'store_code_inventory_feed'
                    ),
                    array(
                        'type' => 'switch',
                        'tab' => 'product_inventory',
                        'label' => $this->l('Show Sale Price', 'renderform'),
                        'name' => 'show_sale_price',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled', 'renderform')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled', 'renderform')
                            )
                        )
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save', 'renderform')
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex
                            . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Back to list', 'renderform'),
                        'icon' => 'process-icon-back'
                    )
                )
            )
        );

        return $form;
    }

    protected function getTypeDescription()
    {
        return array(
            '0' => array(
                'desc_key' => '0',
                'name' => $this->l('Long description', 'renderform')
            ),
            '1' => array(
                'desc_key' => '1',
                'name' => $this->l('Short description', 'renderform')
            ),
            '3' => array(
                'desc_key' => '2',
                'name' => $this->l('Meta Description', 'renderform')
            ),
            '2' => array(
                'desc_key' => '3',
                'name' => $this->l('Short description + Long description', 'renderform')
            ),
            '4' => array(
                'desc_key' => '4',
                'name' => $this->l('Short description + Long description + Meta Description', 'renderform')
            ),
        );
    }

    public function getTaxOptions()
    {
        return array(
            array(
                'id_tax' => 0,
                'name' => $this->l('Default (Guest)', 'renderform')
            ),
            array(
                'id_tax' => 1,
                'name' => $this->l('With Taxes', 'renderform')
            ),
            array(
                'id_tax' => 2,
                'name' => $this->l('Without Taxes', 'renderform')
            ),
        );
    }

    protected function getCategoryFilterSelected()
    {
        if ((bool)Tools::isSubmit('updateFeed') == true
            && Validate::isInt(Tools::getValue('id_gmerchantfeedes'))) {
            $sql = 'SELECT `category_filter` FROM `' . _DB_PREFIX_ . 'gmerchantfeedes` 
                    WHERE `id_gmerchantfeedes` = ' . (int)Tools::getValue('id_gmerchantfeedes');
            $gmerchantfeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if ($gmerchantfeed['category_filter'] && !empty($gmerchantfeed['category_filter'])) {
                return json_decode($gmerchantfeed['category_filter']);
            }

            return array();
        }

        return array();
    }

    public function getProductsByConf($configuration, $id_lang, $id_taxonomy_lang = 0)
    {
        if ($id_taxonomy_lang <= 0) {
            $id_taxonomy_lang = $id_lang;
        }

        $sql = new DbQuery();
        $sql->select('p.id_product, p.visibility, p.condition, product_shop.id_product, pl.name, pl.link_rewrite,
        stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
        product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . (Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY')) . '" as new');
        switch ($configuration['type_description']) {
            case 1:
                $descField = 'pl.description_short';
                break;
            case 2:
                $descField = 'pl.meta_description';
                break;
            case 3:
                $descField = 'concat(pl.description_short, " ", pl.description)';
                break;
            case 4:
                $descField = 'concat(pl.description_short, " ", pl.description, " ", pl.meta_description)';
                break;
            default:
                $descField = 'pl.description';
                break;
        }
        $sql->select($descField . ' `desc`');

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl'));
        if ($configuration['only_active'] == 1) {
            $sql->where('product_shop.`active` = 1');
        }
        if (isset($configuration['only_available']) && $configuration['only_available']) {
            $sql->where('p.`available_for_order` = 1');
        }
        $sql->select('image.`id_image` id_image');
        if ($configuration['filtered_by_only_with_image']) {
            $sql->innerJoin('image', 'image', 'image.`id_product` = p.`id_product` AND image.cover=1');
        } else {
            $sql->leftJoin('image', 'image', 'image.`id_product` = p.`id_product` AND image.cover=1');
        }

        if ($configuration['exclude_empty_description']) {
            $sql->where($descField . ' is not null AND ' . $descField . ' <> \'\'');
        }

        $includeIdsCategory = '';
        if (is_array($configuration['category_filter']) && count($configuration['category_filter']) > 0) {
            $includeIdsCategory = implode(',', array_map('intval', $configuration['category_filter']));
        }
        if (!empty($includeIdsCategory) && $configuration['filtered_by_associated_type']) {
            $sql->innerJoin('category_product', 'pc', 'pc.`id_product` = p.`id_product`');
            $sql->where('pc.`id_category` IN (' . $includeIdsCategory . ')');
        } elseif (!empty($includeIdsCategory)) {
            $sql->innerJoin('category', 'ct', 'ct.`id_category` = p.`id_category_default`');
            $sql->where('ct.`id_category` IN (' . $includeIdsCategory . ')');
        }

        if ($configuration['export_only_new_products']) {
            $nb_days_new_product = (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
            $sql->where('product_shop.`date_add` > "' . date('Y-m-d', strtotime('-' . $nb_days_new_product . ' DAY')) . '"');
        }

        $sql->select('m.`name` manufacturer_name');
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->join(Product::sqlStock('p', 0));
        $PS_STOCK_MANAGEMENT = (int)Configuration::get('PS_STOCK_MANAGEMENT');
        if ((bool)$configuration['export_non_available'] === true && $PS_STOCK_MANAGEMENT) {
            $sql->where('stock.quantity > 0');
        }

        if (isset($configuration['rule_out_of_stock']) && $configuration['rule_out_of_stock'] > 0) {
            $defaultStockRule = Configuration::get('PS_ORDER_OUT_OF_STOCK');
            switch ($configuration['rule_out_of_stock']) {
                case 1: // for denied:
                    $sql->where('( (stock.quantity > 0) OR (stock.quantity <= 0 && stock.out_of_stock = 0' . (($defaultStockRule == 0) ? ' OR stock.out_of_stock = 2 )) ' : '))'));
                    break;
                case 2: // for allowed:
                    $sql->where('( (stock.quantity > 0) OR (stock.quantity <= 0 && stock.out_of_stock = 1' . (($defaultStockRule == 1) ? ' OR stock.out_of_stock = 2 )) ' : '))'));
                    break;
            }
        }

        $includeIdsManufacturers = '';
        if (is_array($configuration['manufacturers_filter[]']) && count($configuration['manufacturers_filter[]']) > 0) {
            $includeIdsManufacturers = implode(',', array_map('intval', $configuration['manufacturers_filter[]']));
        }
        if (!empty($includeIdsManufacturers)) {
            $sql->where('m.`id_manufacturer` IN (' . $includeIdsManufacturers . ')');
        }

        $excludeIdsManufacturers = '';
        if (is_array($configuration['manufacturers_exclude_filter[]']) && count($configuration['manufacturers_exclude_filter[]']) > 0) {
            $excludeIdsManufacturers = implode(',', array_map('intval', $configuration['manufacturers_exclude_filter[]']));
        }
        if (!empty($excludeIdsManufacturers)) {
            $sql->where('m.`id_manufacturer` NOT IN (' . $excludeIdsManufacturers . ')');
        }

        $sql->select('gtx.id_taxonomy, gtx.name_taxonomy');
        $sql->leftJoin('gmerchantfeedes_taxonomy', 'gtx', 'gtx.`id_category` = p.`id_category_default` 
                               AND gtx.`id_lang`=' . (int)$id_taxonomy_lang);
        if (is_array($configuration['exclude_suppliers[]']) && count($configuration['exclude_suppliers[]'])) {
            $excludeSuppliers = implode(',', array_map('intval', $configuration['exclude_suppliers[]']));
            if (!empty($excludeSuppliers)) {
                $sql->where('p.`id_product` NOT IN (SELECT `id_product` FROM ' . _DB_PREFIX_ . 'product_supplier 
                    WHERE `id_supplier` IN (' . $excludeSuppliers . ') GROUP BY id_product)');
            }
        }

        if (is_array($configuration['with_suppliers[]']) && count($configuration['with_suppliers[]'])) {
            $withSuppliers = implode(',', array_map('intval', $configuration['with_suppliers[]']));
            if (!empty($withSuppliers)) {
                $sql->where('p.`id_product` IN (SELECT `id_product` FROM ' . _DB_PREFIX_ . 'product_supplier
                    WHERE `id_supplier` IN (' . $withSuppliers . ') GROUP BY id_product)');
            }
        }

        $currency_default = Currency::getDefaultCurrency();
        if (!isset($configuration['id_currency']) || empty($configuration['id_currency'])) {
            $configuration['id_currency'] = $currency_default->id;
        }
        $currency_ratio = $currency_default->conversion_rate;
        if ($configuration['min_price_filter'] > 0 || $configuration['max_price_filter'] > 0) {
            if ((int)$configuration['id_currency'] != $currency_default->id) {
                $this_currency = Currency::getCurrency((int)$configuration['id_currency']);
                $currency_ratio = $this_currency['conversion_rate'];
            }
            if ($configuration['min_price_filter'] > 0) {
                $sql->where(' (p.price * ' . (float)$currency_ratio . ') >= ' . $configuration['min_price_filter']);
            }
            if ($configuration['max_price_filter'] > 0) {
                $sql->where(' (p.price * ' . (float)$currency_ratio . ') <= ' . $configuration['max_price_filter']);
            }
        }

        $explodeIds = array();
        if (isset($configuration['exclude_ids']) && !empty($configuration['exclude_ids'])) {
            $explodeIDSArray = explode(',', $configuration['exclude_ids']);
            if (is_array($explodeIDSArray) && count($explodeIDSArray)) {
                foreach ($explodeIDSArray as $explodeId) {
                    if (Validate::isInt($explodeId) && $explodeId > 0) {
                        $explodeIds[] = (int)$explodeId;
                    }
                }
            }
        }
        if (isset($configuration['excludeIdsFromFile']) && is_array($configuration['excludeIdsFromFile'])) {
            $explodeIds = array_merge($explodeIds, $configuration['excludeIdsFromFile']);
            $explodeIds = array_unique($explodeIds);
        }
        if (is_array($explodeIds) && count($explodeIds) > 0) {
            $sql->where('p.id_product NOT IN (' . implode(',', array_map('intval', $explodeIds)) . ')');
        }

        $sql->groupBy('p.id_product');

        if (isset($configuration['visible_product_hide']) && $configuration['visible_product_hide']) {
            $sql->where('p.visibility != "none"');
        }

        if ($configuration['from_product_id'] > 0 && Validate::isInt($configuration['from_product_id'])) {
            $sql->where('p.id_product >= ' . (int)$configuration['from_product_id']);
        }

        if ($configuration['to_product_id'] > 0 && Validate::isInt($configuration['to_product_id'])) {
            $sql->where('p.id_product <= ' . (int)$configuration['to_product_id']);
        }

        if (isset($configuration['filter_qty_from']) && $configuration['filter_qty_from'] != 0) {
            $sql->where('stock.quantity >= ' . (int)$configuration['filter_qty_from']);
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    private function createFeedPath($id_feed)
    {
        $generate_path = _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $id_feed;
        $generate_file = md5(_COOKIE_KEY_ . $id_feed) . '.xml';
        $generate_path_file = $generate_path . DIRECTORY_SEPARATOR . $generate_file;

        if (!is_dir($generate_path)) {
            mkdir($generate_path, 0777, true);
        }

        if (file_exists($generate_path_file)) {
            unlink($generate_path_file);
        }

        return $generate_path_file;
    }

    private function clearHtmlTags($string)
    {
        $string = Tools::getDescriptionClean($string);
        $string = preg_replace('/<[^>]*>/', ' ', $string);
        $string = str_replace("\r", '', $string);
        $string = str_replace("\n", ' ', $string);
        $string = str_replace("\t", ' ', $string);
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
        $string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);

        return $string;
    }

    protected function mbStrToLowerAfterPoint($text = '')
    {
        if (empty($text)) {
            return '';
        }

        $text = Tools::strtolower($text);
        $pointExplode = explode('. ', $text);
        $pointExplode = array_map(function ($data) {
            $fc = mb_strtoupper(mb_substr($data, 0, 1));
            return $fc . mb_substr($data, 1);
        }, $pointExplode);

        return join('. ', $pointExplode);
    }

    public static function getProductPath($id_category, $path = '', $product_title_in_product_type = 0, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_category = (int)$id_category;
        if ($id_category == 1) {
            return $path;
        }

        $pipe = Configuration::get('PS_NAVIGATION_PIPE');
        if (empty($pipe)) {
            $pipe = '>';
        }

        $full_path = '';
        $interval = Category::getInterval($id_category);
        $id_root_category = $context->shop->getCategory();
        $interval_root = Category::getInterval($id_root_category);
        if ($interval) {
            $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite
                    FROM ' . _DB_PREFIX_ . 'category c
                    LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category' . Shop::addSqlRestrictionOnLang('cl') . ')
                    ' . Shop::addSqlAssociation('category', 'c') . '
                    WHERE c.nleft <= ' . (int)$interval['nleft'] . '
                        AND c.nright >= ' . (int)$interval['nright'] . '
                        AND c.nleft >= ' . (int)$interval_root['nleft'] . '
                        AND c.nright <= ' . (int)$interval_root['nright'] . '
                        AND cl.id_lang = ' . (int)$context->language->id . '
                        AND c.active = 1
                        AND c.level_depth > ' . (int)$interval_root['level_depth'] . '
                    ORDER BY c.level_depth ASC';
            $categories = Db::getInstance()->executeS($sql);

            if (!$product_title_in_product_type) {
                $path = '';
            }

            $n = 1;
            $n_categories = count($categories);
            foreach ($categories as $category) {
                $full_path .= $category['name'] . (($n++ != $n_categories || !empty($path)) ? " " . $pipe . " " : '');
            }

            return $full_path . $path;
        }
    }
}
