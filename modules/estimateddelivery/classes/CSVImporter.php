<?php
/** * Estimated Delivery - Front Office Feature
 *
 * NOTICE OF LICENSE
 *
 * @author    Pol Rué
 * @copyright Smart Modules 2015
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category Transport & Logistics
 * Registered Trademark & Property of smart-modules.com
 * ***************************************************
 * *               Estimated Delivery                *
 * *          http://www.smart-modules.com           *
 * *                                                  *
 * ***************************************************
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class CSVImporter
{
    protected $shop_ids = [];
    protected $prod = [];
    protected $prod_ref = [];
    protected $combi = [];
    protected $combi_ref = [];
    protected $sep;
    protected $msep;
    protected $allowed_ext = ['txt', 'csv'];
    protected $errors = [];
    protected $warnings = [];
    protected $columns = [];
    protected $c = [];
    protected $ed;
    protected $context;
    private $msg_max_length = 300;

    public function __construct($ed)
    {
        if (isset($_FILES['ED_IMPORT_FILE']['tmp_name'])) {
            $this->sep = Configuration::get('ED_EXPORT_SEP');
            $this->msep = Configuration::get('ED_EXPORT_MULTI_SEP');

            // Get all IDs so we make sure no ED is upload to an inexistent product, shop
            $this->getProdData();
            $this->shop_ids = Shop::getShops(true, null, true);
            $this->ed = $ed;
            $this->context = Context::getContext();
        }
    }

    /**
     * Perform a search on the database and get the products, combinations and references
     * Fills in 4 arrays with the existing product data (prod, prod_ref, combi and combi_ref)
     */
    private function getProdData()
    {
        $sql = 'SELECT id_product, COALESCE(id_product_attribute, 0) as id_product_attribute, COALESCE(pa.reference, p.reference) as reference FROM ' . _DB_PREFIX_ . 'product p LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa USING (id_product) ORDER BY `p`.`id_product` ASC, `pa`.`id_product_attribute` ASC';
        $results = Db::getInstance()->executeS(pSQL($sql));
        if ($results !== false && count($results) > 0) {
            foreach ($results as $result) {
                $this->prod[$result['id_product']] = 1;
                $this->prod_ref[$result['reference']] = $result['id_product'];
                if ($result['id_product_attribute'] > 0) {
                    $this->combi[$result['id_product']][$result['id_product_attribute']] = 1;
                    $this->combi_ref[$result['id_product']][$result['reference']] = $result['id_product_attribute'];
                }
            }
        }
    }

    public function importEDs()
    {
        $this->columns = ['id_product', 'id_product_attribute', 'id_shop', 'available_date', 'out_of_stock_days', 'picking_days', 'release_date', 'customization_days', 'disabled'];
        $this->col_count = count($this->columns);

        $errors = false;
        $fn = $_FILES['ED_IMPORT_FILE']['tmp_name'];
        $ext = pathinfo($_FILES['ED_IMPORT_FILE']['name'], PATHINFO_EXTENSION);

        // Check if the file extension is allowed
        if (!in_array($ext, $this->allowed_ext)) {
            $this->context->controller->errors[] = $this->ed->l('The file appears to be an invalid CSV file or the parameters you set didn\'t match. Please upload it again');

            return;
        }

        $r = fopen($fn, 'r');

        // Check if export header is set
        if (Tools::getValue('ED_EXPORT_HEAD') == 1) {
            // Pull the first value as it's the header
            $this->columns = fgetcsv($r, 0, Configuration::get('ED_EXPORT_SEP'));
            // Ensure column count matches
            $this->columns = array_slice($this->columns, 0, $this->col_count);
        }

        // Map column indices
        $this->mapColumnIndices();

        // Validate and process data
        $this->processData($r);

        // Report errors and warnings
        $this->reportErrorsAndWarnings();
    }

    // Function to map column indices
    private function mapColumnIndices()
    {
        $this->c = array_combine($this->columns, range(0, $this->col_count - 1));
    }

    // Function to validate and process data
    private function processData($r)
    {
        $line_count = (int) Tools::getValue('ED_EXPORT_HEAD');
        $insert = [];

        while (($line = fgetcsv($r, 10000, $this->sep)) !== false) {
            ++$line_count;
            $field = [];

            // Check if the line is valid
            if ($this->isValidLine($line, $line_count)) {
                foreach ($this->columns as $column) {
                    switch ($column) {
                        case 'id_shop':
                            $field[$column] = $this->validateShop($line, $line_count);
                            break;
                        case 'id_product':
                            $field[$column] = $this->validateProduct($line, $line_count);
                            break;
                        case 'id_product_attribute':
                            $field[$column] = $this->validateProductAttribute($line, $line_count);
                            break;
                        case 'available_date':
                            $field['restock_date'] = $this->validateDate($line[$this->c[$column]], $line_count);
                            break;
                        case 'release_date':
                            $field[$column] = $this->validateDate($line[$this->c[$column]], $line_count);
                            break;
                        case 'disabled':
                            $field[$column] = $this->validateDisabled($line);
                            break;
                        default:
                            $field[$column] = (int) $line[$this->c[$column]];
                            break;
                    }
                }
                // Add to insert array if no errors
                $insert[] = $field;
            }
        }

        // Insert data into database
        if (!empty($insert)) {
            $this->insertEDToDB($insert);
        }
    }

    // Functions for validating individual fields
    private function validateShop($line, $line_count)
    {
        $shopId = $line[$this->c['id_shop']];
        if (empty($shopId) || $shopId == 0) {
            return Shop::isFeatureActive() ? implode($this->msep, Shop::getShops(true, null, true)) : $this->context->shop->id;
        } elseif (!in_array($shopId, $this->shop_ids)) {
            $this->errors['shop'][] = [$line_count, $shopId];

            return null;
        } else {
            return $shopId;
        }
    }

    private function validateProduct($line, $line_count)
    {
        $productId = $line[$this->c['id_product']];
        if (!filter_var($productId, FILTER_VALIDATE_INT)) {
            return isset($this->prod_ref[$productId]) ? $this->prod_ref[$productId] : $this->errors['product_ref'][] = [$line_count, $productId];
        } elseif (!isset($this->prod[$productId])) {
            return $this->errors['product'][] = [$line_count, $productId];
        } else {
            return (int) $productId;
        }
    }

    private function validateProductAttribute($line, $line_count)
    {
        $productAttributeId = trim($line[$this->c['id_product_attribute']]);
        if ($productAttributeId !== '') {
            if (!filter_var($productAttributeId, FILTER_VALIDATE_INT)) {
                return isset($this->combi_ref[$line[$this->c['id_product']]][$productAttributeId]) ? $this->combi_ref[$line[$this->c['id_product']]][$productAttributeId] : $this->errors['id_product_attribute_ref'][] = [$line_count, $line[$this->c['id_product']] . ' - ' . $productAttributeId];
            } elseif ($productAttributeId > 0 && !isset($this->combi[$line[$this->c['id_product']]][$productAttributeId])) {
                return $this->errors['id_product_attribute'][] = [$line_count, $line[$this->c['id_product']] . ' - ' . $productAttributeId];
            }
        }

        return $productAttributeId;
    }

    private function validateDate($date, $line_count)
    {
        return $date != '' && strtotime($date) ? $date : null;
    }

    private function validateDisabled($line)
    {
        return (int) $line[$this->c['disabled']];
    }

    private function isValidLine($line, $line_count)
    {
        if (empty(array_filter($line))) {
            $this->warnings['empty'][] = $line_count;

            return false;
        }
        if (count($line) != $this->col_count) {
            $this->warnings['columns'][] = [$line_count, count($line)];

            return false;
        }

        return true;
    }

    /**
     * Validates all the IDs in the array comparing them to a known list
     *
     * @update $errors, $warnings and $id
     *
     * @return false if all the ids do not exist
     * @return error array if some ids do not exist
     **/
    private function validateIds($mode, $list, &$id, $line_count)
    {
        $e = [];
        $c = count($id);
        for ($i = 0; $i < $c; ++$i) {
            // remove any unnecessay spaces, if they exist
            $id[$i] = trim($id[$i]);
            if (!in_array($id[$i], $list)) {
                $e[] = $id[$i];
                unset($id[$i]);
            }
        }
        if (count($e) > 0) {
            if (count($e) == count($id)) {
                $this->errors[$mode][] = [$line_count, $e];

                return false;
            } else {
                $this->warnings[$mode][] = [$line_count, $e];
            }
        }

        return true;
    }

    private function insertEDToDB($insert)
    {
        if (empty($insert)) {
            return false;
        }

        $columns = $this->columns;
        $columns[$this->c['out_of_stock_days']] = 'delay';
        $columns_combi = $columns;

        unset($columns[$this->c['id_product_attribute']], $columns[$this->c['available_date']]);
        $columns = array_values($columns);

        $up_columns = [
            'ed_prod' => array_diff($columns, ['id_product', 'id_shop']),
            'ed_prod_combi' => array_diff($columns_combi, ['id_product', 'id_shop']),
            'product' => ['available_date'],
        ];

        foreach ($up_columns as $key => $type) {
            foreach ($type as $field => $column) {
                if (in_array($column, ['id_product', 'id_shop'])) {
                    unset($up_columns[$key][$field]);
                }
            }
        }

        // Create the columns list to update
        $columns = [
            'ed_prod' => $columns,
            'ed_prod_combi' => $columns_combi,
            'product' => ['id_product', 'available_date'],
        ];

        if (Tools::getValue('ED_EXPORT_DELETE') == 1) {
            DB::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'ed_prod_combi');
            $this->context->controller->confirmations[] = $this->ed->l('All EDs deleted. Importing the CSV...');
        }

        $values = $this->insertToValues($insert);
        //        Tools::dieObject([$insert, $values], false);
        foreach ($values as $table => $insert_data) {
            $sql = 'INSERT INTO ' . _DB_PREFIX_ . bqSQL($table) . ' (' . implode(',', $columns[$table]) . ') VALUES ' . implode(',', $insert_data) . ' ON DUPLICATE KEY UPDATE ' . $this->getUpdateColumns($up_columns[$table]);

            //            echo $sql;
            if (DB::getInstance()->execute($sql) === false) {
                $this->context->controller->errors[] = sprintf($this->ed->l('%s Import Error'), $table == 'ed_prod' ? $this->ed->l('Products') : $this->ed->l('Combinations')) . ': ' . DB::getInstance()->getMsgError() . '<br />' . $this->ed->l('SQL Query') . ': ' . $sql;
            } else {
                $this->context->controller->confirmations[] = sprintf($this->ed->l('%d %s Imported'), count($insert), $table == 'ed_prod' ? $this->ed->l('Products') : $this->ed->l('Combinations'));
            }
        }
        //        die();
    }

    private function insertToValues($insert)
    {
        $values = [];

        foreach ($insert as $ins) {
            $table = 'ed_prod';
            if ($ins['id_product_attribute'] > 0) {
                $table = 'ed_prod_combi';
            } else {
                if (!empty($ins['available_date']) && Validate::isDate($ins['available_date'])) {
                    $values['product'][] = '("' . implode('","', [$ins['id_product'], date('Y-m-d', strtotime($ins['restock_date']))]) . '")';
                }
                unset($ins['id_product_attribute'], $ins['restock_date']);
            }

            if (Shop::isFeatureActive() && strpos($ins['id_shop'], $this->msep) !== false) {
                $shops = explode($this->msep, $ins['id_shop']);
                foreach ($shops as $id_shop) {
                    $ins['id_shop'] = (int) $id_shop;
                    $values[$table][] = '("' . implode('","', $ins) . '")';
                }
            } else {
                $values[$table][] = '("' . implode('","', $ins) . '")';
            }
        }

        return $values;
    }

    private function getUpdateColumns($columns)
    {
        return implode(', ', array_map(function ($column) {
            return "$column = VALUES($column)";
        }, $columns));
    }

    private function reportErrorsAndWarnings()
    {
        // If there are skipped products print the lines containing errors
        if (count($this->errors) > 0) {
            foreach ($this->errors as $key => $value) {
                $msg = '';
                $error_count = count($this->errors[$key]);
                switch ($key) {
                    case 'product':
                        $msg = sprintf($this->ed->l('Invalid product ID (%s):'), 'id_product');
                        break;
                    case 'product_ref':
                        $msg = sprintf($this->ed->l('Invalid Product Reference Id (%s):'), 'reference');
                        break;
                    case 'available_date':
                        $msg = sprintf($this->ed->l('Invalid date format (%s):'), 'date');
                        break;
                    case 'release_date':
                        $msg = sprintf($this->ed->l('Invalid date format (%s):'), 'date');
                        break;
                    case 'shop':
                        $msg = sprintf($this->ed->l('Invalid Shop Id (%s):'), 'id_shop');
                        break;
                    case 'id_product_attribute':
                        $msg = sprintf($this->ed->l('Invalid Product Combination (%s):'), 'id_product_attribute');
                        break;
                    case 'id_product_attribute_ref':
                        $msg = $this->ed->l('Invalid Product Combination reference:');
                        break;
                }
                $last_line = 0;
                if (count($value[0]) > 1) {
                    $msg = '<strong>' . $this->ed->l('ED Import Error: ') . '</strong> ' . count($value) . ' ' . $msg;
                    foreach ($value as $v) {
                        if ($last_line != $v[0]) {
                            $msg .= '<br>- Line ' . $v[0] . ': "' . $v[1] . '"';
                        } else {
                            $msg .= ', "' . $v[1] . '"';
                        }
                    }
                } else {
                    $msg .= implode(', ', $value);
                }
                // If string is too long truncate it and add ...
                if (Tools::strlen($msg) > $this->msg_max_length) {
                    $msg = Tools::truncateString($msg, $this->msg_max_length);
                }
                $this->context->controller->errors[] = $msg;
            }
        }
        if (count($this->warnings) > 0) {
            foreach ($this->warnings as $key => $value) {
                $msg = '';
                switch ($key) {
                    case 'columns':
                        $msg = '<strong>' . $this->ed->l('Invalid column count. Lines skipped:') . '</strong> ';
                        break;
                    case 'prod':
                        $msg = '<strong>' . $this->ed->l('Invalid id Product, product skipped:') . '</strong> ';
                        break;
                    case 'available_date':
                        $msg = '<strong>' . $this->ed->l('wrong date or date format, line skipped:') . '</strong> ';
                        break;
                    case 'release_date':
                        $msg = '<strong>' . $this->ed->l('wrong date or date format, line skipped:') . '</strong> ';
                        break;
                    case 'shop':
                        $msg = '<strong>' . $this->ed->l('Invalid Shop Id, shop skipped:') . '</strong> ';
                        break;
                    case 'empty':
                        $msg = '<strong>' . $this->ed->l('Empty lines') . '</strong>, ' . $this->ed->l('will be ignored:') . ' ';
                        break;
                }
                $last_line = 0;
                if (isset($value[0]) && is_array($value[0]) && count($value[0]) > 1) {
                    foreach ($value as $v) {
                        if (is_array($v[1]) && count($v[1]) > 0) {
                            $v[1] = implode(',', $v[1]);
                        }
                        $msg .= '<br>- Line ' . $v[0] . ': ' . $v[1];
                        /*print_r($v);
                        if ($last_line != $v[0]) {
                            $msg .= '<br>- Line '.$v[0].': "'.$v[1].'"';
                        } else {
                            $msg .= ', "'.$v[1].'"';
                        }*/
                    }
                } else {
                    $msg .= implode(', ', $value);
                }
                // If string is too long truncate it and add ...
                if (Tools::strlen($msg) > $this->msg_max_length) {
                    $msg = Tools::truncateString($msg, $this->msg_max_length);
                }
                $this->context->controller->warnings[] = $msg;
            }
        }
    }
}
