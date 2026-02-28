<?php
/**
 * Web Browser Push Notifications using OneSignal
 *
 * @author    Rolige <www.rolige.com>
 * @copyright Since 2011 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class RgPuNoConflicts
{
    private static $folder_permissions = [
        'uploads',
    ];

    /*
     * Permissions checklist
     * the base path is module folder and regular expresion paths are allowed:
     * - controllers/ (controllers folder)
     * - ajaxs/*.php (all php files inside ajaxs folder)
     * - controllers/front/payment.php (specific file)
     */
    private static $file_permissions = [
        'ajaxs/*.php',
        'crons/*.php',
        'logs/*.php',
    ];

    public static function get($module)
    {
        $output = '';

        if ($errors = self::getErrors($module)) {
            $output .= $module->displayError($errors);
        }

        if ($warnings = self::getWarnings($module)) {
            $output .= $module->displayWarning($warnings);
        }

        return $output;
    }

    public static function getErrors($module)
    {
        $errors = self::checkFilesAccess($module);

        return $errors;
    }

    public static function getWarnings($module)
    {
        $warnings = [];

        if (Tools::getShopProtocol() == 'https://' && !file_exists($module->getLocalPath() . 'manifest.json')) {
            $warnings[] = $module->displayWarning($module->l('You should upload manifest.json file to your website root folder to use properly OneSignal services over HTTPS. Please, check documentation for detailed steps.', __CLASS__));
        }

        return $warnings;
    }

    /**
     * Analyze and fix the correct access in files and folders.
     *
     * @return array
     */
    private static function checkFilesAccess($module)
    {
        $module_path = $module->getLocalPath();
        $errors = [];

        $folder_paths = array_unique(array_merge(
            self::$folder_permissions,
            array_map('dirname', self::$file_permissions)
        ));

        foreach ($folder_paths as $folder_path) {
            if (self::isAccessible($folder_path, $module_path) === false) {
                if (!@chmod($module_path . $folder_path, 0755)) {
                    $user_path = str_replace($module_path, '/modules/' . $module->name . '/', $module_path . $folder_path);
                    $errors[] = sprintf(
                        $module->l('Wrong file permissions in the folder "%s", you must change them to "0755".', __CLASS__),
                        $user_path
                    );
                }
            }
        }

        foreach (self::$file_permissions as $file_path) {
            foreach (glob($module_path . $file_path) as $file_local_path) {
                if (basename($file_local_path) !== 'index.php' && self::isAccessible($file_local_path) === false) {
                    if (!@chmod($file_local_path, 0644)) {
                        $user_path = str_replace($module_path, '/modules/' . $module->name . '/', $file_local_path);
                        $errors[] = sprintf(
                            $module->l('Wrong file permissions in the file "%s", you must change them to "0644".', __CLASS__),
                            $user_path
                        );
                    }
                }
            }
        }

        return $errors;
    }

    private static function isAccessible($path, $module_path = '')
    {
        return is_readable($module_path . $path) &&
            is_writeable($module_path . $path) &&
            Tools::substr(sprintf('%o', fileperms($module_path . $path)), -4) !== '0777';
    }
}
