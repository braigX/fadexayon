<?php
/**
 * 2007 - 2018 ZLabSolutions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade module to newer
 * versions in the future. If you wish to customize module for your
 * needs please contact developer at http://zlabsolutions.com for more information.
 *
 *  @author    Eugene Zubkov <magrabota@gmail.com>
 *  @copyright 2018 ZLab Solutions
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Property of ZLab Solutions https://www.facebook.com/ZLabSolutions/
 */

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
error_reporting(-1);
*/

require_once _PS_MODULE_DIR_ . '../config/config.inc.php';
require_once _PS_MODULE_DIR_ . '../init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once _PS_MODULE_DIR_ . '../classes/Cookie.php';
include_once _PS_MODULE_DIR_ . 'productsindex/productsindex.php';

@ini_set('max_execution_time', 0);

class Zlabcustomclasszl
{
    public static function getShopMinimumImageType()
    {
        $image_types = ImageType::getImagesTypes(null, true);
        $minimum = [1000, false];
        foreach ($image_types as $image_type) {
            if ($image_type['width'] < $minimum[0]) {
                $minimum = [$image_type['width'], $image_type['name']];
            }
        }

        return $minimum[1];
    }

    public static function getConfigValueByOption($option)
    {
        $sql = 'SELECT value 
                FROM ' . _DB_PREFIX_ . 'zlcpi_settings
                WHERE `option`=\'' . pSQL($option) . '\'';
        $result = Db::getInstance()->getRow($sql);
        $value = $result['value'];

        return $value;
    }

    public static function getConfig()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'zlcpi_settings';
        $result = Db::getInstance()->executeS($sql);
        $settings = [];
        foreach ($result as $r) {
            $settings[$r['id']] = [$r['option'], $r['value']];
        }

        return $settings;
    }

    public static function validateOptions($id, $value)
    {
        $module = new Productsindex();
        $type = '';
        $integers = [1, 3, 4, 5];
        $strings = [2];
        $langs = [];
        $floats = [];

        if (in_array($id, $integers)) {
            $type = 'integer';
        } elseif (in_array($id, $strings)) {
            $type = 'string';
        } elseif (in_array($id, $langs)) {
            $type = 'lang';
        } elseif (in_array($id, $floats)) {
            $type = 'float';
        }
        if ($id == 100) {
            // Configuration::get('PS_PRODUCTS_ORDER_BY');
            Configuration::updateValue('PS_PRODUCTS_ORDER_BY', (int) $value);

            return false;
        }
        switch ($type) {
            case 'string':
                if (Validate::isString($value)) {
                    return false;
                } else {
                    return $module->l('Option must be string');
                }
                break;
            case 'integer':
                if (Validate::isInt($value)) {
                    return false;
                } else {
                    return $module->l('Option must be integer');
                }
                break;
            case 'lang':
                if (Validate::isLanguageIsoCode($value)) {
                    return false;
                } else {
                    return $module->l('Locale must be iso_code format');
                }
                break;
            case 'float':
                if (Validate::isFloat($value)) {
                    return false;
                } else {
                    return $module->l('Coefficient must be float (0.9, 1.3 etc)');
                }
                break;
            default:
                exit;
        }
    }

    public static function updateSettings()
    {
        $id = (int) Tools::getValue('id');
        $value = Tools::getValue('value');
        if (in_array($id, [1, 2, 3, 4])) {
            $value = trim($value);
        }
        $validation = self::validateOptions($id, $value);
        if (!$validation) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'zlcpi_settings
                    SET value=\'' . pSQL($value) . '\'
                    WHERE id=' . (int) $id;
            $result = Db::getInstance()->execute($sql);
            if ($result) {
                echo 'true';
            } else {
                echo json_encode([9, 'Can not update setting']);
            }
        } else {
            $message = $validation;
            $error = [$id, $message];
            echo json_encode($error);
        }
    }

    public static function updateSetting($id, $value)
    {
        if (in_array($id, [1, 2, 3, 4])) {
            $value = trim($value);
        }
        $validation = self::validateOptions($id, $value);
        if (!$validation) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'zlcpi_settings
                    SET value=\'' . pSQL($value) . '\'
                    WHERE id=' . (int) $id;
            $result = Db::getInstance()->execute($sql);
            if ($result) {
                return true;
            } else {
                return [9, 'Can not update setting'];
            }
        } else {
            $message = $validation;
            $error = [$id, $message];

            return $error;
        }
    }

    public static function getSettings()
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'zlcpi_settings;';
        $res = Db::getInstance()->executeS($sql);
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    /* LOG */
    public static function writeLog($log)
    {
        $timestamp = time();
        $date = date('m/d/Y h:i:s a', time());
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'zlcpi_log
                SET date = \'' . pSQL($date) . '\',
                    log = \'' . pSQL($log) . '\',
                    timestamp = ' . (int) $timestamp;
        $res = Db::getInstance()->execute($sql);

        return $res;
    }

    public static function getLastLogTimestamp()
    {
        $sql = 'SELECT `timestamp` FROM ' . _DB_PREFIX_ . 'zlcpi_log ORDER BY id DESC';
        $res = Db::getInstance()->getRow($sql);
        if ($res) {
            return (int) $res['timestamp'];
        }

        return false;
    }

    public static function clearLog()
    {
        $sql = 'TRUNCATE TABLE ' . _DB_PREFIX_ . 'zlcpi_log';
        $res = Db::getInstance()->execute($sql);
        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public static function getLog()
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'zlcpi_log ORDER BY id DESC LIMIT 0, 500';
        $res = Db::getInstance()->executeS($sql);
        if ($res) {
            $log = [];
            foreach ($res as $r) {
                $log[] = $r['date'] . ' - ' . $r['log'];
            }

            return $log;
        } else {
            return false;
        }
    }

    public static function ajaxGetLog()
    {
        $log = self::getLog();

        $next_import = 1;
        if (Tools::strlen($next_import[0])) {
            $next_import_asin = str_replace('\'', '', $next_import[0]);
        } else {
            $next_import_asin = 0;
        }

        $next_sync = 1;
        if (Tools::strlen($next_sync[0])) {
            $next_sync_asin = str_replace('\'', '', $next_sync[0]);
        } else {
            $next_sync_asin = 0;
        }

        echo json_encode([$log, $next_import_asin, $next_sync_asin]);
    }
    /* END LOG */

    public static function deleteBridgeProductAjax()
    {
        $asin = Tools::getValue('asin');

        if (self::deleteFromGateByAsin($asin)) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    public static function getLangIds()
    {
        $sql = 'SELECT id_lang FROM ' . _DB_PREFIX_ . 'lang;';
        $result = Db::getInstance()->executeS($sql);
        $ids = [];
        foreach ($result as $lang) {
            $ids[] = $lang['id_lang'];
        }

        return $ids;
    }

    public static function createMultiLangField($field)
    {
        $res = [];
        foreach (self::getLangIds() as $id_lang) {
            $res[$id_lang] = $field;
        }

        return $res;
    }

    public static function getAdminToken($id_employee)
    {
        $tab = 'AdminModules';

        return Tools::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) $id_employee);
    }

    public static function backupModuleTables()
    {
        // settings
        $db = Db::getInstance();
        $db->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'zlcpi_settings_old`');
        if (count($db->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'zlcpi_settings"')) > 0) {
            $db->execute('RENAME TABLE `' . _DB_PREFIX_ . 'zlcpi_settings` TO `' . _DB_PREFIX_ . 'zlcpi_settings_old`');
            $have_old_table = true;
        } else {
            $have_old_table = false;
        }

        return $have_old_table;
    }

    public static function restoreModuleTables()
    {
        $db = Db::getInstance();
        foreach ($db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'zlcpi_settings_old`') as $field) {
            $db->execute('UPDATE `' . _DB_PREFIX_ . 'zlcpi_settings`
                            SET value = \'' . pSQL($field['value']) . '\'
                            WHERE id = ' . (int) $field['id']);
        }
        $db->execute('DROP TABLE IF EXISTS`' . _DB_PREFIX_ . 'zlcpi_settings_old`');

        return true;
    }
}
