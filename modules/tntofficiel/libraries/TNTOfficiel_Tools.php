<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'tntofficiel/libraries/TNTOfficiel_ClassLoader.php';

class TNTOfficiel_Tools
{
    const JSON_PRETTY_NONE = 0;
    const JSON_PRETTY_ONELINE = 1;
    const JSON_PRETTY_MULTILINE = 2;
    const JSON_PRETTY_BULKYLINE = 3;

    /**
     * Prevent Construct.
     */
    final private function __construct()
    {
        trigger_error(sprintf('%s() %s is static.', __FUNCTION__, get_class($this)), E_USER_ERROR);
    }

    /**
     * Get variable type for dump.
     *
     * @param $args
     *
     * @return string
     */
    public static function dumpType($mxdArgValue, $arrExclude = array())
    {
        // Get type.
        $strType = gettype($mxdArgValue);

        if (in_array($strType, $arrExclude)) {
            return '';
        }

        if (is_object($mxdArgValue)) {
            $strType = '(' . $strType . ')' . get_class($mxdArgValue);
        } elseif (is_resource($mxdArgValue)) {
            $strType = '(' . $strType . ')' . get_resource_type($mxdArgValue);
        } else {
            $strType = '(' . $strType . ')';
        }

        return $strType;
    }

    /**
     * Get variable safe.
     * Prevent circular reference, etc ...
     *
     * @param $mxdArgValue
     *
     * @return mixed. null if unable to serialize or encode to JSON.
     */
    public static function getSafe($mxdArgValue)
    {
        try {
            // If unable to serialize.
            serialize($mxdArgValue);
            // If unable to encode to JSON.
            $strTestJSON = json_encode($mxdArgValue);
            if (!is_string($strTestJSON) || ($mxdArgValue !== '' && $strTestJSON === '')) {
                return null;
            }
        } catch (Exception $objException) {
            return null;
        }

        return $mxdArgValue;
    }

    /**
     * Get memory usage estimation.
     *
     * @param $mxdArgValue
     *
     * @return int
     */
    public static function getMem($mxdArgValue)
    {
        $intMemStart = memory_get_usage();

        try {
            // Temporary assignment is required.
            $strTmp = unserialize(serialize($mxdArgValue));
        } catch (Exception $objException) {
            $strTmp = null;
        }

        return memory_get_usage() - $intMemStart;
    }

    /**
     * Get variable safe for dump usage.
     * Prevent out of memory, circular reference and others recursive endless loop.
     *
     * @param     $mxdArgValue
     * @param int $intArgMemLimit Default dump memory limit is 1 Mib.
     * @param int $intArgMaxDepth
     *
     * @return mixed
     */
    public static function dumpSafe($mxdArgValue, $intArgMemLimit = 1048576, $intArgMaxDepth = 8)
    {
        $intMemLimit = (int)$intArgMemLimit;
        $intMaxDepth = (int)$intArgMaxDepth;
        --$intMaxDepth;

        $strType = gettype($mxdArgValue);

        $arrScalarType = array('NULL', 'boolean', 'integer', 'string');
        // If json_encode() preserve fraction for type double ...
        if (defined('JSON_PRESERVE_ZERO_FRACTION')) {
            // ... no need to exclude type double from the scalar list.
            $arrScalarType = array('NULL', 'boolean', 'integer', 'double', 'string');
        }
        $boolIsScalar = in_array($strType, $arrScalarType);

        if ($intMaxDepth < 0 && !$boolIsScalar) {
            return '…';
        } elseif ((is_object($mxdArgValue) || is_array($mxdArgValue))) {
            try {
                $arrValueSafe = array();
                $intPropCount = max(count((array)$mxdArgValue), 1);
                $intPropMemLimit = $intMemLimit / $intPropCount;
                foreach ($mxdArgValue as $k => $mxdPropItem) {
                    $intPropMemSize = TNTOfficiel_Tools::getMem($mxdPropItem);
                    $arrValueSafe[$k] = '…';
                    if ($intPropMemSize <= $intPropMemLimit) {
                        $arrValueSafe[$k] = TNTOfficiel_Tools::dumpSafe(
                            $mxdPropItem,
                            $intPropMemLimit,
                            $intMaxDepth
                        );
                    }
                }

                return array(TNTOfficiel_Tools::dumpType($mxdArgValue, $arrScalarType) => $arrValueSafe);
            } catch (Exception $objException) {
                return '…E';
            }
        }

        $intMemSize = TNTOfficiel_Tools::getMem($mxdArgValue);
        $mxdArgValueSafe = '…';
        if ($intMemSize <= $intMemLimit) {
            $mxdArgValueSafe = TNTOfficiel_Tools::getSafe($mxdArgValue);
        }

        return ($boolIsScalar ? $mxdArgValueSafe : array(
            TNTOfficiel_Tools::dumpType($mxdArgValue, $arrScalarType) => $mxdArgValueSafe,
        ));
    }

    /**
     * Encode to JSON.
     *
     * @param array $mxdArgValue
     * @param int   $intArgPettyLevel
     *
     * @return string
     */
    public static function encJSON($mxdArgValue, $intArgPettyLevel = null/*, $intArgDepth = 512*/)
    {
        if (in_array(
            $intArgPettyLevel,
            array(
                TNTOfficiel_Tools::JSON_PRETTY_NONE,
                TNTOfficiel_Tools::JSON_PRETTY_ONELINE,
                TNTOfficiel_Tools::JSON_PRETTY_MULTILINE,
                TNTOfficiel_Tools::JSON_PRETTY_BULKYLINE,
            ),
            true
        )) {
            // Known value.
            $intPettyLevel = (int)$intArgPettyLevel;
        } else {
            // Default value.
            $intPettyLevel = TNTOfficiel_Tools::JSON_PRETTY_BULKYLINE;
        }

        $flagJSONEncode = 0;

        // PHP 5.3.14+.
        //$flagJSONEncode |= defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0;

        if ($intPettyLevel !== TNTOfficiel_Tools::JSON_PRETTY_NONE) {
            // PHP 5.4.0+.
            $flagJSONEncode |= defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;
        }

        // PHP 5.4.0+. Unescape.
        $flagJSONEncode |= defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
        // PHP 5.4.0+. Unescape.
        $flagJSONEncode |= defined('JSON_UNESCAPED_SLASHES') ? JSON_UNESCAPED_SLASHES : 0;
        // PHP 5.6.6+. Display 0.0.
        $flagJSONEncode |= defined('JSON_PRESERVE_ZERO_FRACTION') ? JSON_PRESERVE_ZERO_FRACTION : 0;
        // PHP 7.3.0+.
        //$flagJSONEncode |= defined('JSON_THROW_ON_ERROR') ? JSON_THROW_ON_ERROR : 0;

        // PHP < 5.3 return null if second parameter is used.
        if ($flagJSONEncode === 0) {
            $strJSON = json_encode($mxdArgValue);
        } else {
            $strJSON = json_encode($mxdArgValue, $flagJSONEncode);
        }

        // PHP 5.5.0+. Depth is a new third parameter.
        //if (PHP_VERSION_ID >= 50500) { /* PHP version >= 5.5.0 */
        //    $strJSON = json_encode($mxdArgValue, $flagJSONEncode, $intArgDepth);
        //}

        // If fail to encode.
        if (!is_string($strJSON) || ($mxdArgValue !== '' && $strJSON === '')) {
            return '';
        }

        if ($intPettyLevel == TNTOfficiel_Tools::JSON_PRETTY_ONELINE
            || $intPettyLevel == TNTOfficiel_Tools::JSON_PRETTY_MULTILINE
        ) {
            // indent to 2 spaces.
            $strJSON = preg_replace_callback('/(^|\n)(\ ++)/ui', array('TNTOfficiel_Tools', 'cbIndentSpace'), $strJSON);
            // before }
            $strJSON = preg_replace('/(?<![\}\]])\n\s*+(?=\}(?!$))/ui', '', $strJSON);
            // before }
            $strJSON = preg_replace('/(?<![\}])\n\s*+(?=\},)/ui', '', $strJSON);
            // before {
            $strJSON = preg_replace('/(?<=\[|,)\n\s*+(?=\{)/ui', '', $strJSON);
            // after }
            $strJSON = preg_replace('/(?<=\})\n\s*+(?=\])/ui', '', $strJSON);
            // after }
            $strJSON = preg_replace('/(?<=\})\n\s++(?=}(?!\]))/ui', '', $strJSON);
            // before ]
            $strJSON = preg_replace('/\n\s*+(?=\])/ui', '', $strJSON);
        }

        if ($intPettyLevel == TNTOfficiel_Tools::JSON_PRETTY_ONELINE) {
            $strJSON = preg_replace('/(?<=,)\n\s*+/ui', ' ', $strJSON);
            $strJSON = preg_replace('/(?<=\{|\[)\n\s*+/ui', '', $strJSON);
            $strJSON = preg_replace('/\n\s*+(?=\})/ui', '', $strJSON);
        }

        return $strJSON;
    }

    /**
     * Use associative array, instead object.
     *
     * @param      $strArgJSON
     * @param int  $intArgDepth
     *
     * @return array
     */
    public static function decJSON($strArgJSON, $intArgDepth = 512)
    {
        $flagJSONDecode = 0;

        // PHP 5.4.0+. Decodes large integers as their original string value.
        //$flagJSONDecode |= defined('JSON_BIGINT_AS_STRING') ? JSON_BIGINT_AS_STRING : 0;
        // PHP 5.4.0+. Decodes JSON objects as PHP array. Like $boolArgAssociative = true.
        //$flagJSONDecode |= defined('JSON_OBJECT_AS_ARRAY') ? JSON_OBJECT_AS_ARRAY : 0;

        // PHP 7.2.0+. Ignore invalid UTF-8 characters.
        //$flagJSONDecode |= defined('JSON_INVALID_UTF8_IGNORE') ? JSON_INVALID_UTF8_IGNORE : 0;
        // PHP 7.2.0+. Convert invalid UTF-8 characters to \0xfffd.
        //$flagJSONDecode |= defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : 0;
        // PHP 7.3.0+. Throws JsonException if an error occurs.
        //$flagJSONDecode |= defined('JSON_THROW_ON_ERROR') ? JSON_THROW_ON_ERROR : 0;

        if ($flagJSONDecode === 0) {
            $arrJSON = json_decode($strArgJSON, true, $intArgDepth);
        } else {
            // PHP 5.4.0+.
            $arrJSON = json_decode($strArgJSON, true, $intArgDepth, $flagJSONDecode);
        }

        // If fail to decode.
        if (!is_array($arrJSON)) {
            return array();
        }

        return $arrJSON;
    }

    /**
     * Callback.
     *
     * @param $arrArgMatches
     *
     * @return string
     */
    public static function cbIndentSpace($arrArgMatches)
    {
        return $arrArgMatches[1] . str_repeat(' ', (int)(strlen($arrArgMatches[2]) / 2));
    }

    /**
     * Get client IP.
     *
     * @return null|string
     */
    public static function getClientIPAddress()
    {
        $strClientIPAddress = null;
        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            $strClientIPAddress = $_SERVER['REMOTE_ADDR'];
        }
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $strClientIPAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $strClientIPAddress;
    }

    /**
     * Get Controller, Employee and Customer infos from context.
     *
     * @return array
     */
    public static function getContextInfo()
    {
        $arrResult = array(
            'controller' => null,
            'employee' => array(
                'id' => 0,
                'email' => null,
                'profile' => null,
            ),
            'customer' => array(
                'id' => 0,
                'email' => null,
                'groups' => array(Configuration::get('PS_UNIDENTIFIED_GROUP')),
            ),
        );

        $objController = null;
        $objPSCustomer = null;
        $objEmployee = null;


        $objContext = Context::getContext();

        if (property_exists($objContext, 'controller')) {
            /** @var \Controller $objController */
            $objController = $objContext->controller;
        }
        if (property_exists($objContext, 'customer')) {
            /** @var \Customer $objPSCustomer */
            $objPSCustomer = $objContext->customer;
        }
        if (property_exists($objContext, 'employee')) {
            /** @var \Employee $objEmployee */
            $objEmployee = $objContext->employee;
        }

        $arrResult['controller'] = TNTOfficiel_Tools::getControllerName($objController);

        if (Validate::isLoadedObject($objEmployee)) {
            $arrResult['employee'] = array(
                'id' => (int)$objEmployee->id,
                'email' => sprintf(
                    '%s %s <%s>',
                    $objEmployee->firstname,
                    $objEmployee->lastname,
                    $objEmployee->email
                ),
            );

            $objProfile = Profile::getProfile($objEmployee->id_profile);
            if (Validate::isLoadedObject($objProfile)) {
                $arrResult['employee']['profile'] = $objProfile->name;
            }
        }

        if (Validate::isLoadedObject($objPSCustomer)) {
            $arrResult['customer'] = array(
                'id' => (int)$objPSCustomer->id,
                'email' => sprintf(
                    '%s %s <%s>',
                    $objPSCustomer->firstname,
                    $objPSCustomer->lastname,
                    $objPSCustomer->email
                ),
                'groups' => $objPSCustomer->getGroups(),
            );
        }

        return $arrResult;
    }

    /**
     * Get script filename info (location, module, admin, front).
     *
     * @return array
     */
    public static function getScriptInfo()
    {
        $arrResult = array(
            //'prestashop' => false,
            'front' => false,
            'admin' => false,
            //'admin_legacy' => false,
            'module' => null,
            // default is internal (eg: shutdown).
            'file' => '[Internal]',
            'line' => 0,
        );

        if (array_key_exists('SCRIPT_FILENAME', $_SERVER)) {
            $arrResult['file'] = $_SERVER['SCRIPT_FILENAME'];
        }

        $arrBack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $arrTrace = array_pop($arrBack);
        if (array_key_exists('file', $arrTrace) && is_string($arrTrace['file'])) {
            $arrResult['file'] = $arrTrace['file'];
            $arrResult['line'] = $arrTrace['line'];
        }

        if (defined('_PS_ADMIN_DIR_')) {
            $arrResult['admin'] = true;
        }
        //if (defined('ADMIN_LEGACY_CONTEXT')) {
        //    $arrResult['admin_legacy'] = true;
        //}

        $strFile = realpath($arrResult['file']);
        // No real path existing.
        if ($strFile === false) {
            return $arrResult;
        }

        // Compare entry file to Front file.
        if ($strFile === TNTOfficiel::getDirPS() . 'index.php') {
            //$arrResult['prestashop'] = true;
            $arrResult['front'] = true;
        }

        if (defined('_PS_ADMIN_DIR_')
            && $strFile === realpath(_PS_ADMIN_DIR_ . '/index.php')
        ) {
            //$arrResult['prestashop'] = true;
        }

        // Compare entry file to get module.
        $strModule = realpath(_PS_MODULE_DIR_) . '/';
        if ($strModule == substr($strFile, 0, strlen($strModule))) {
            $arrResult['module'] = substr(
                $strFile,
                strlen($strModule),
                strpos($strFile, '/', strlen($strModule)) - strlen($strModule)
            );
        }

        return $arrResult;
    }

    /**
     * Get Controller Name.
     *
     * @param \Controller $objArgController
     *
     * @return mixed
     */
    public static function getControllerName($objArgController)
    {
        // Exclude dummy 'Admin' controller.
        if (isset($objArgController->controller_name)
            && $objArgController->controller_name !== 'Admin'
        ) {
            // Admin.
            $strControllerName = $objArgController->controller_name;
        } elseif (isset($objArgController->php_self)) {
            // Front.
            $strControllerName = $objArgController->php_self;
        } else {
            // Admin and Front Fallback.
            $strControllerName = Dispatcher::getInstance()->getController();
            //$strControllerName = get_class($objArgController);
        }

        $strControllerName = preg_replace('/[^a-z0-9_]+/ui', '', $strControllerName);
        $strControllerName = Tools::strtolower($strControllerName . 'controller');

        return $strControllerName;
    }

    /**
     * Check if a Controller is an Admin one.
     *
     * class Admin<Controller>ControllerCore
     *   extends AdminController
     *   extends AdminControllerCore
     *
     * class AdminLegacyLayoutControllerCore
     *   extends AdminController
     *   extends AdminControllerCore
     *
     * class \PrestaShopBundle\Controller\Admin\<Controller>Controller
     *   extends \PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController
     *
     * @param $objArgController
     *
     * @return mixed
     */
    public static function isAdminController($objArgController)
    {
        return (
            ($objArgController instanceof AdminController)
            || ($objArgController instanceof AdminControllerCore)
            || ($objArgController instanceof \PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController)
        );
    }

    /**
     * Get Bootstrap HTML alert.
     *
     * @param array $arrArgAlert
     *
     * @return array
     */
    public static function getAlertHTML($arrArgAlert)
    {
        // Define message sort.
        $arrAlertHTML = array(
            'info' => null,
            'warning' => null,
            'success' => null,
            'error' => null,
        );

        foreach ($arrArgAlert as $strAlertType => $arrAlertMsg) {
            if (count($arrAlertMsg) > 0) {
                foreach ($arrAlertMsg as $k => $a) {
                    if (is_array($a)) {
                        $arrAlertMsg[$k] = $k . ": " . implode("\n ", $a);
                    }
                }

                $arrAlertMsg = array_map('htmlentities', $arrAlertMsg);
                if ($strAlertType === 'info') {
                    $arrAlertHTML[$strAlertType] = '<div class="alert alert-info">'
                        . (count($arrAlertMsg) === 1 ?
                            array_shift($arrAlertMsg) : ('<ul><li>' . implode('</li><li>', $arrAlertMsg) . '</li></ul>'))
                        . '</div>';
                } elseif ($strAlertType === 'warning') {
                    $arrAlertHTML[$strAlertType] = '<div class="alert alert-warning">'
                        . (count($arrAlertMsg) === 1 ?
                            array_shift($arrAlertMsg) : ('<ul><li>' . implode('</li><li>', $arrAlertMsg) . '</li></ul>'))
                        . '</div>';
                } elseif ($strAlertType === 'success') {
                    $arrAlertHTML[$strAlertType] = '<div class="alert alert-success">'
                        . (count($arrAlertMsg) === 1 ?
                            array_shift($arrAlertMsg) : ('<ul><li>' . implode('</li><li>', $arrAlertMsg) . '</li></ul>'))
                        . '</div>';
                } elseif ($strAlertType === 'error') {
                    $arrAlertHTML[$strAlertType] = '<div class="alert alert-danger">'
                        . (count($arrAlertMsg) === 1 ?
                            array_shift($arrAlertMsg) : ('<ul><li>' . implode('</li><li>', $arrAlertMsg) . '</li></ul>'))
                        . '</div>';
                }
            }

            if (array_key_exists($strAlertType, $arrAlertHTML) && !$arrAlertHTML[$strAlertType]) {
                unset($arrAlertHTML[$strAlertType]);
            }
        }

        return $arrAlertHTML;
    }

    /**
     * Validate a Fixed Phone (FR,MC only).
     *
     * @param string $strArgCountryISO The ISO Country Code.
     * @param string $strArgPhoneFixed The Fixed Phone Number.
     *
     * @return bool|string The Formated Fixed Phone String if valid, else false.
     */
    public static function validateFixedPhone($strArgCountryISO, $strArgPhoneFixed)
    {
        if (!is_string($strArgCountryISO)) {
            return false;
        }
        if (!is_string($strArgPhoneFixed)) {
            return false;
        }

        // Format par pays.
        $arrPhoneFormatCountry = array(
            'FR' => array(
                'strCountryCode' => '33',
                'strTrunkp' => '0',
                'strFixed' => '([1234589])([0-9]{8})',
            ),
            'MC' => array(
                'strCountryCode' => '377',
                'strTrunkp' => '',
                'strFixed' => '([89])([0-9]{7})',
            ),
        );

        $strCountryISO = Tools::strtoupper(trim($strArgCountryISO));
        if (!array_key_exists($strCountryISO, $arrPhoneFormatCountry)) {
            return false;
        }

        // Check allowed character.
        if (!Validate::isPhoneNumber($strArgPhoneFixed)) {
            return false;
        }

        // Get Country Data.
        $arrPhoneFormat = $arrPhoneFormatCountry[$strCountryISO];
        // Cleaning Phone Input.
        $strPhoneFixedClean = preg_replace('/[^+0-9()]/ui', '', $strArgPhoneFixed);
        // Root.
        $strRoot = '(?:(?:(?:\+|00)' . $arrPhoneFormat['strCountryCode']
            . '(?:\(' . $arrPhoneFormat['strTrunkp'] . '\))?)|' . $arrPhoneFormat['strTrunkp'] . ')';

        if (preg_match('/^' . $strRoot . '(' . $arrPhoneFormat['strFixed'] . ')$/ui', $strPhoneFixedClean, $arrMatches)) {
            $strPhoneFixedID = $arrPhoneFormat['strTrunkp'] . $arrMatches[1];
            $strPhoneFixedIDLength = Tools::strlen($strPhoneFixedID);

            if ($strPhoneFixedIDLength < 1 || $strPhoneFixedIDLength > 63) {
                return false;
            }

            return $strPhoneFixedID;
        }

        return false;
    }

    /**
     * Validate a Mobile Phone (FR,MC only).
     *
     * @param string $strArgCountryISO  The ISO Country Code.
     * @param string $strArgPhoneMobile The Mobile Phone Number.
     *
     * @return bool|string The Formated Mobile Phone String if valid, else false.
     */
    public static function validateMobilePhone($strArgCountryISO, $strArgPhoneMobile)
    {
        if (!is_string($strArgCountryISO)) {
            return false;
        }
        if (!is_string($strArgPhoneMobile)) {
            return false;
        }

        // Format par pays.
        $arrPhoneFormatCountry = array(
            // FR : Mobile + Numéros étendus (ex : 07009999999999)
            'FR' => array(
                'strCountryCode' => '33',
                'strTrunkp' => '0',
                'strMobile' => '([67])([0-9]{8})|([7]00)([0-9]{9,10})',
            ),
            'MC' => array(
                'strCountryCode' => '377',
                'strTrunkp' => '',
                'strMobile' => '(?:([34])([0-9]{7})|([6])([0-9]{8}))',
            ),
        );

        $strCountryISO = Tools::strtoupper(trim($strArgCountryISO));
        if (!array_key_exists($strCountryISO, $arrPhoneFormatCountry)) {
            return false;
        }

        // Check allowed character.
        if (!Validate::isPhoneNumber($strArgPhoneMobile)) {
            return false;
        }

        // Get Country Data.
        $arrPhoneFormat = $arrPhoneFormatCountry[$strCountryISO];
        // Cleaning Phone Input.
        $strPhoneMobileClean = preg_replace('/[^+0-9()]/ui', '', $strArgPhoneMobile);
        // Root.
        $strRoot = '(?:(?:(?:\+|00)' . $arrPhoneFormat['strCountryCode']
            . '(?:\(' . $arrPhoneFormat['strTrunkp'] . '\))?)|' . $arrPhoneFormat['strTrunkp'] . ')';

        if (preg_match('/^' . $strRoot . '(' . $arrPhoneFormat['strMobile'] . ')$/ui', $strPhoneMobileClean, $arrMatches)) {
            $strPhoneMobileID = $arrPhoneFormat['strTrunkp'] . $arrMatches[1];
            $strPhoneMobileIDLength = Tools::strlen($strPhoneMobileID);

            if ($strPhoneMobileIDLength < 1 || $strPhoneMobileIDLength > 63) {
                return false;
            }

            return $strPhoneMobileID;
        }

        return false;
    }

    /**
     * Create a new directory with default index.php file.
     * Don't do log here.
     *
     * @param string|array[string] $arrArgDirectoryList an array of directories.
     *
     * @return bool
     */
    public static function makeDirectory($arrArgDirectoryList, $strRoot = '')
    {
        $strIndexFileContent = <<<PHP
<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Location: ../');
exit;
PHP;

        $arrDirectoryList = (array)$arrArgDirectoryList;

        foreach ($arrDirectoryList as $strDirectory) {
            // If directory does not exist, create it.
            $boolSuccess = true;

            if (!is_string($strDirectory)) {
                continue;
            }

            // Add final separator.
            if (Tools::substr($strDirectory, -1) !== DIRECTORY_SEPARATOR) {
                $strDirectory .= DIRECTORY_SEPARATOR;
            }

            $strPath = $strRoot . $strDirectory;

            // If directory does not exist, create it.
            if (!is_dir($strPath)) {
                $intUMask = umask(0);
                $boolSuccess = mkdir($strPath, 0770, true) && $boolSuccess;
                umask($intUMask);

                if (!$boolSuccess) {
                    return false;
                }
            }

            $strFileName = $strPath . 'index.php';
            // If index file does not exist, create it.
            if (!file_exists($strFileName)) {
                touch($strFileName);
                @chmod($strFileName, 0660);

                $rscFile = fopen($strFileName, 'w');
                if ($rscFile === false) {
                    return false;
                }
                fwrite($rscFile, $strIndexFileContent);
                fclose($rscFile);
            }
        }

        return true;
    }

    /**
     * Remove a list of files or directories.
     *
     * @param string $strModuleDirSrc
     * @param array  $arrRemoveFileList
     * @param array  $arrRemoveDirList
     *
     * @return bool
     */
    public static function removeFiles($strModuleDirSrc, $arrRemoveFileList = array(), $arrRemoveDirList = array())
    {
        foreach ($arrRemoveFileList as $strFile) {
            $strFQFile = $strModuleDirSrc . $strFile;

            try {
                // Delete file if it exists.
                if (file_exists($strFQFile)) {
                    Tools::deleteFile($strFQFile);
                }
            } catch (Exception $objException) {
                TNTOfficiel_Logger::logException($objException);

                return false;
            }
        }

        foreach ($arrRemoveDirList as $strDir) {
            $strFQDir = $strModuleDirSrc . $strDir;

            try {
                // Delete dir if exist.
                if (file_exists($strFQDir)) {
                    Tools::deleteDirectory($strFQDir);
                }
            } catch (Exception $objException) {
                TNTOfficiel_Logger::logException($objException);

                return false;
            }
        }

        return true;
    }

    /**
     * Return error message from ZipArchive error code.
     *
     * @param $intArgErrorCode
     *
     * @return string|null
     */
    public static function getZipArchiveError($intArgErrorCode)
    {
        $arrZipErrorList = array(
            // 0
            'ZipArchive::ER_OK' => 'No error',
            // 1
            'ZipArchive::ER_MULTIDISK' => 'Multi-disk zip archives not supported',
            // 2
            'ZipArchive::ER_RENAME' => 'Renaming temporary file failed',
            // 3
            'ZipArchive::ER_CLOSE' => 'Closing zip archive failed',
            // 4
            'ZipArchive::ER_SEEK' => 'Seek error',
            // 5
            'ZipArchive::ER_READ' => 'Read error',
            // 6
            'ZipArchive::ER_WRITE' => 'Write error',
            // 7
            'ZipArchive::ER_CRC' => 'CRC error',
            // 8
            'ZipArchive::ER_ZIPCLOSED' => 'Containing zip archive was closed',
            // 9
            'ZipArchive::ER_NOENT' => 'No such file',
            // 10
            'ZipArchive::ER_EXISTS' => 'File already exists',
            // 11
            'ZipArchive::ER_OPEN' => 'Can\'t open file',
            // 12
            'ZipArchive::ER_TMPOPEN' => 'Failure to create temporary file',
            // 13
            'ZipArchive::ER_ZLIB' => 'Zlib error',
            // 14
            'ZipArchive::ER_MEMORY' => 'Malloc failure',
            // 15
            'ZipArchive::ER_CHANGED' => 'Entry has been changed',
            // 16
            'ZipArchive::ER_COMPNOTSUPP' => 'Compression method not supported',
            // 17
            'ZipArchive::ER_EOF' => 'Premature EOF',
            // 18
            'ZipArchive::ER_INVAL' => 'Invalid argument',
            // 19
            'ZipArchive::ER_NOZIP' => 'Not a zip archive',
            // 20
            'ZipArchive::ER_INTERNAL' => 'Internal error',
            // 21
            'ZipArchive::ER_INCONS' => 'Zip archive inconsistent',
            // 22
            'ZipArchive::ER_REMOVE' => 'Can\'t remove file',
            // 23
            'ZipArchive::ER_DELETED' => 'Entry has been deleted',
            // Available as of PHP 7.4.3 and PECL zip 1.16.1
            'ZipArchive::ER_ENCRNOTSUPP' => 'Encryption method not supported.',
            // Available as of PHP 7.4.3 and PECL zip 1.16.1
            'ZipArchive::ER_RDONLY' => 'Read-only archive.',
            // Available as of PHP 7.4.3 and PECL zip 1.16.1
            'ZipArchive::ER_NOPASSWD' => 'No password provided.',
            // Available as of PHP 7.4.3 and PECL zip 1.16.1
            'ZipArchive::ER_WRONGPASSWD' => 'Wrong password provided.',
        );

        foreach ($arrZipErrorList as $strConst => $strError) {
            if (defined($strConst) && constant($strConst) == $intArgErrorCode) {
                return $strError;
            }
        }

        return null;
    }

    /**
     * Generate an archive containing all the logs files
     *
     * @param       $strArgPath
     * @param array $arrArgAllowedExt
     *
     * @return string Zip file content.
     */
    public static function getZip($strArgPath, $arrArgAllowedExt = array())
    {
        $strZipContent = TNTOfficiel_Tools::inflate('C/BmZWPAAAA=');

        if (!extension_loaded('zip')) {
            TNTOfficiel_Logger::logException(new Exception('PHP Zip extension is required'));

            return $strZipContent;
        }

        // Folder must exist.
        if (!is_dir($strArgPath)) {
            return $strZipContent;
        }

        $strZipFileName = 'logs_tmp.zip';
        $strZipLocation = $strArgPath . $strZipFileName;

        // Remove existing file archive.
        Tools::deleteFile($strZipLocation);

        // Search.
        $arrFileTime = TNTOfficiel_Tools::searchFiles($strArgPath, true, $arrArgAllowedExt);
        // No files found.
        if (!is_array($arrFileTime)) {
            return $strZipContent;
        }

        $objZipArchive = new ZipArchive();
        $intCreateStatus = $objZipArchive->open($strZipLocation, ZipArchive::CREATE);
        if ($intCreateStatus !== true) {
            $strMsgError = TNTOfficiel_Tools::getZipArchiveError($intCreateStatus);
            if ($strMsgError === null) {
                $strMsgError = 'Can\'t open file';
            }
            TNTOfficiel_Logger::logException(new Exception(sprintf('[%s] %s', $strZipLocation, $strMsgError)));

            return $strZipContent;
        }

        foreach ($arrFileTime as $strFileName => $intTime) {
            $strFileLocation = $strArgPath . $strFileName;
            if (file_exists($strFileLocation)) {
                $objZipArchive->addFile($strFileLocation, $strFileName);
                //$objZipArchive->addFromString($strFileName, Tools::file_get_contents($strFileLocation));
            }
        }

        $objZipArchive->close();

        if (file_exists($strZipLocation)) {
            $strZipContent = file_get_contents($strZipLocation);
            // Remove existing file archive.
            Tools::deleteFile($strZipLocation);
        }

        return $strZipContent;
    }

    /**
     * Get string  binary size in bytes.
     *
     * @param $strArgSubject
     *
     * @return int
     */
    public static function strByteLength($strArgSubject)
    {
        return strlen($strArgSubject);
    }

    /**
     * Split a string in array of strings with a maximum of chars.
     *
     * @param string $strArgSubject
     * @param int    $intArgLength
     *
     * @return array
     */
    public static function strSplitter($strArgSubject, $intArgLength = 32)
    {
        $intLength = (int)$intArgLength;
        $strSubject = trim((string)$strArgSubject);

        if (!($intLength > 0) || Tools::strlen($strSubject) <= $intLength) {
            return array(
                $strSubject,
            );
        }

        $arrResult = array();

        while (Tools::strlen($strSubject) > 0) {
            $rxpSplitter = '/^\s*+([^\n]{0,' . $intLength . '})(:?\s+([^\n]*?))?\s*$/ui';
            if (preg_match($rxpSplitter, $strSubject, $arrBackRefList) === 1) {
                // warp line.
                $arrResult[] = $arrBackRefList[1];
                if (array_key_exists(3, $arrBackRefList)) {
                    $strSubject = $arrBackRefList[3];
                } else {
                    $strSubject = '';
                }
            } else {
                // cut word.
                $arrResult[] = Tools::substr($strSubject, 0, $intLength);
                $strSubject = Tools::substr($strSubject, $intLength);
            }
        }

        return $arrResult;
    }

    /**
     * Convert string to ASCII.
     * Mainly for compatibility with webservice.
     *
     * Input:
     * a-z0-9àâäéèêëîïôöùûüñ²&"#'{}()[]|_\/ç^@°=+-£$¤%µ*<>?,.;:§!
     * Â’€ê^²ç#~&まa-z0-9âéïùñ
     * `²'|"/°-£¤µ*,.;:§()[]
     * Output:
     * a-z0-9aaaeeeeiioouuun²&"#'{}()[]|_\/c^@°=+-£$¤%µ*<>?,.;:§! A'Ee^²c#-&maa-z0-9aeiun '²'|"/°-£¤µ*,.;:§()[]
     *
     * @param     $strArgInput
     * @param int $intArgLength
     *
     * @return string
     */
    public static function translitASCII($strArgInput, $intArgLength = 0)
    {
        $strNoControlChars = preg_replace('/[\p{Cn}]++/u', '?', $strArgInput);

        // PHP 7+
        $boolExistTransliterator = function_exists('transliterator_transliterate');
        if ($boolExistTransliterator) {
            $strASCII = transliterator_transliterate('Any-Latin; Latin-ASCII;', $strNoControlChars);
        } else {
            $arrRegExTranslitMap = array(
                '/[            ]/u' => ' ',
                '/[©]/u' => '(C)',
                '/[«≪]/u' => '<<',
                '/[­˗‐‑‒–—―−﹘﹣－]/u' => '-',
                '/[®]/u' => '(R)',
                '/[»≫]/u' => '>>',
                '/[¼]/u' => ' 1/4',
                '/[½]/u' => ' 1/2',
                '/[¾]/u' => ' 3/4',
                '/[ÀÁÂÃÄÅĀĂĄǍǞǠǺȀȂȦȺΆΑḀẠẢẤẦẨẪẬẮẰẲẴẶÅＡ]/u' => 'A',
                '/[ÆǢǼ]/u' => 'AE',
                '/[ÇĆĈĊČƇȻḈℂℭⅭＣ]/u' => 'C',
                '/[ÈÉÊËĒĔĖĘĚƐȄȆȨɆΈΉΕΗḔḖḘḚḜẸẺẼẾỀỂỄỆℰＥ]/u' => 'E',
                '/[ÌÍÎÏĨĪĬĮİƖƗǏȈȊɪΊΙΪḬḮỈỊℐℑⅠＩ]/u' => 'I',
                '/[ÐĎĐƉƊƋΔḊḌḎḐḒⅅⅮＤ]/u' => 'D',
                '/[ÑŃŅŇŊƝǸɴΝṄṆṈṊℕＮ]/u' => 'N',
                '/[ÒÓÔÕÖØŌŎŐƠǑǪǬǾȌȎȪȬȮȰΌΏΟΩṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢΩＯ]/u' => 'O',
                '/[×⁎﹡＊]/u' => '*',
                '/[ÙÚÛÜŨŪŬŮŰŲƯǓǕǗǙǛȔȖɄṲṴṶṸṺỤỦỨỪỬỮỰＵ]/u' => 'U',
                '/[ÝŶŸƳȲɎʏΎΥΫϒϓϔẎỲỴỶỸỾＹ]/u' => 'Y',
                '/[ÞΘϴ]/u' => 'TH',
                '/[ß]/u' => 'ss',
                '/[àáâãäåāăąǎǟǡǻȁȃȧάαḁẚạảấầẩẫậắằẳẵặａ]/u' => 'a',
                '/[æǣǽ]/u' => 'ae',
                '/[çćĉċčƈȼɕḉⅽｃ]/u' => 'c',
                '/[èéêëēĕėęěȅȇȩɇɛέήεηϵḕḗḙḛḝẹẻẽếềểễệℯⅇｅ]/u' => 'e',
                '/[ìíîïĩīĭįıǐȉȋɨͺΐίιϊḭḯỉịℹⅈⅰｉ]/u' => 'i',
                '/[ðďđƌȡɖɗδḋḍḏḑḓⅆⅾｄ]/u' => 'd',
                '/[ñńņňŋƞǹȵɲɳνṅṇṉṋｎ]/u' => 'n',
                '/[òóôõöøōŏőơǒǫǭǿȍȏȫȭȯȱοωόώṍṏṑṓọỏốồổỗộớờởỡợℴｏ]/u' => 'o',
                '/[÷⁄∕／]/u' => '/',
                '/[ùúûüũūŭůűųưǔǖǘǚǜȕȗʉṳṵṷṹṻụủứừửữựｕ]/u' => 'u',
                '/[ýÿŷƴȳɏΰυϋύẏẙỳỵỷỹỿｙ]/u' => 'y',
                '/[þθϑ]/u' => 'th',
                '/[ĜĞĠĢƓǤǦǴɢʛΓḠＧ]/u' => 'G',
                '/[ĝğġģǥǧǵɠɡγḡℊｇ]/u' => 'g',
                '/[ĤĦȞʜḢḤḦḨḪℋℍＨ]/u' => 'H',
                '/[ĥħȟɦɧḣḥḧḩḫẖℎｈ]/u' => 'h',
                '/[Ĳ]/u' => 'IJ',
                '/[ĳ]/u' => 'ij',
                '/[ĴɈＪ]/u' => 'J',
                '/[ĵǰȷɉɟʝϳⅉｊ]/u' => 'j',
                '/[ĶƘǨΚḰḲḴKＫ]/u' => 'K',
                '/[ķƙǩκϰḱḳḵｋ]/u' => 'k',
                '/[ĸʠｑ]/u' => 'q',
                '/[ĹĻĽĿŁȽʟΛḶḸḺḼℒⅬＬ]/u' => 'L',
                '/[ĺļľŀłƚȴɫɬɭλḷḹḻḽℓⅼｌ]/u' => 'l',
                '/[ŉ]/u' => '\'n',
                '/[Œɶ]/u' => 'OE',
                '/[œ]/u' => 'oe',
                '/[ŔŖŘȐȒɌʀΡṘṚṜṞℛℜℝＲ]/u' => 'R',
                '/[ŕŗřȑȓɍɼɽɾρϱṙṛṝṟｒ]/u' => 'r',
                '/[ŚŜŞŠȘΣϷϹϺṠṢṤṦṨＳ]/u' => 'S',
                '/[śŝşšſșȿʂςσϲϸϻṡṣṥṧṩẛẜẝｓ]/u' => 's',
                '/[ŢŤŦƬƮȚȾΤṪṬṮṰＴ]/u' => 'T',
                '/[ţťŧƫƭțȶʈτṫṭṯṱẗｔ]/u' => 't',
                '/[ŴẀẂẄẆẈＷ]/u' => 'W',
                '/[ŵẁẃẅẇẉẘｗ]/u' => 'w',
                '/[ŹŻŽƵȤΖẐẒẔℤℨＺ]/u' => 'Z',
                '/[źżžƶȥɀʐʑζẑẓẕｚ]/u' => 'z',
                '/[ƀƃɓβϐḃḅḇｂ]/u' => 'b',
                '/[ƁƂɃʙΒḂḄḆℬＢ]/u' => 'B',
                '/[ƑḞℱＦ]/u' => 'F',
                '/[ƒḟｆ]/u' => 'f',
                '/[ƕ]/u' => 'hv',
                '/[Ƣ]/u' => 'OI',
                '/[ƣ]/u' => 'oi',
                '/[ƤΠṔṖℙＰ]/u' => 'P',
                '/[ƥπϖṕṗｐ]/u' => 'p',
                '/[ƲṼṾỼⅤＶ]/u' => 'V',
                '/[ǄǱ]/u' => 'DZ',
                '/[ǅǲ]/u' => 'Dz',
                '/[ǆǳʣʥ]/u' => 'dz',
                '/[Ǉ]/u' => 'LJ',
                '/[ǈ]/u' => 'Lj',
                '/[ǉ]/u' => 'lj',
                '/[Ǌ]/u' => 'NJ',
                '/[ǋ]/u' => 'Nj',
                '/[ǌ]/u' => 'nj',
                '/[Ǯ]/u' => 'Ʒ',
                '/[ǯ]/u' => 'ʒ',
                '/[ȸ]/u' => 'db',
                '/[ȹ]/u' => 'qp',
                '/[ɱμḿṁṃⅿｍ]/u' => 'm',
                '/[ʋṽṿỽⅴｖ]/u' => 'v',
                '/[ʦ]/u' => 'ts',
                '/[ʪ]/u' => 'ls',
                '/[ʫ]/u' => 'lz',
                '/[ʹʻʼʽˈʹ‘’‛′＇]/u' => '\'',
                '/[ʺ“”‟″＂]/u' => '"',
                '/[˂‹﹤＜]/u' => '<',
                '/[˃›﹥＞]/u' => '>',
                '/[˄ˆ＾]/u' => '^',
                '/[ˋ｀]/u' => '`',
                '/[ː﹕：]/u' => ':',
                '/[˖﹢＋]/u' => '+',
                '/[˜～]/u' => '~',
                '/[̀]/u' => '̀',
                '/[́]/u' => '́',
                '/[̓]/u' => '̓',
                '/[̈́]/u' => '̈́',
                '/[;﹔；]/u' => ';',
                '/[·]/u' => '·',
                '/[ΜḾṀṂℳⅯＭ]/u' => 'M',
                '/[ΞẊẌⅩＸ]/u' => 'X',
                '/[Φ]/u' => 'PH',
                '/[Χ]/u' => 'CH',
                '/[Ψ]/u' => 'PS',
                '/[ξẋẍℌⅹｘ]/u' => 'x',
                '/[φϕ]/u' => 'ph',
                '/[χ]/u' => 'ch',
                '/[ψ]/u' => 'ps',
                '/[ẞ]/u' => 'SS',
                '/[Ỻ]/u' => 'LL',
                '/[ỻ]/u' => 'll',
                '/[‖∥]/u' => '||',
                '/[‚﹐﹑，]/u' => ',',
                '/[„]/u' => ',,',
                '/[․﹒．]/u' => '.',
                '/[‥]/u' => '..',
                '/[…]/u' => '...',
                '/[‼]/u' => '!!',
                '/[⁅﹝［]/u' => '[',
                '/[⁆﹞］]/u' => ']',
                '/[⁇]/u' => '??',
                '/[⁈]/u' => '?!',
                '/[⁉]/u' => '!?',
                '/[₠]/u' => 'CE',
                '/[₢]/u' => 'Cr',
                '/[₣]/u' => 'Fr.',
                '/[₤]/u' => 'L.',
                '/[₧]/u' => 'Pts',
                '/[₹]/u' => 'Rs',
                '/[₺]/u' => 'TL',
                '/[℀]/u' => 'a/c',
                '/[℁]/u' => 'a/s',
                '/[℅]/u' => 'c/o',
                '/[℆]/u' => 'c/u',
                '/[№]/u' => 'No',
                '/[ℚＱ]/u' => 'Q',
                '/[℞]/u' => 'Rx',
                '/[℡]/u' => 'TEL',
                '/[℻]/u' => 'FAX',
                '/[⅓]/u' => ' 1/3',
                '/[⅔]/u' => ' 2/3',
                '/[⅕]/u' => ' 1/5',
                '/[⅖]/u' => ' 2/5',
                '/[⅗]/u' => ' 3/5',
                '/[⅘]/u' => ' 4/5',
                '/[⅙]/u' => ' 1/6',
                '/[⅚]/u' => ' 5/6',
                '/[⅛]/u' => ' 1/8',
                '/[⅜]/u' => ' 3/8',
                '/[⅝]/u' => ' 5/8',
                '/[⅞]/u' => ' 7/8',
                '/[⅟]/u' => ' 1/',
                '/[Ⅱ]/u' => 'II',
                '/[Ⅲ]/u' => 'III',
                '/[Ⅳ]/u' => 'IV',
                '/[Ⅵ]/u' => 'VI',
                '/[Ⅶ]/u' => 'VII',
                '/[Ⅷ]/u' => 'VIII',
                '/[Ⅸ]/u' => 'IX',
                '/[Ⅺ]/u' => 'XI',
                '/[Ⅻ]/u' => 'XII',
                '/[ⅱ]/u' => 'ii',
                '/[ⅲ]/u' => 'iii',
                '/[ⅳ]/u' => 'iv',
                '/[ⅵ]/u' => 'vi',
                '/[ⅶ]/u' => 'vii',
                '/[ⅷ]/u' => 'viii',
                '/[ⅸ]/u' => 'ix',
                '/[ⅺ]/u' => 'xi',
                '/[ⅻ]/u' => 'xii',
                '/[∖﹨＼]/u' => '\\',
                '/[∣｜]/u' => '|',
                '/[﹖？]/u' => '?',
                '/[﹗！]/u' => '!',
                '/[﹙（]/u' => '(',
                '/[﹚）]/u' => ')',
                '/[﹛｛]/u' => '{',
                '/[﹜｝]/u' => '}',
                '/[﹟＃]/u' => '#',
                '/[﹠＆]/u' => '&',
                '/[﹦＝]/u' => '=',
                '/[﹩＄]/u' => '$',
                '/[﹪％]/u' => '%',
                '/[﹫＠]/u' => '@',
                '/[０]/u' => '0',
                '/[１]/u' => '1',
                '/[２]/u' => '2',
                '/[３]/u' => '3',
                '/[４]/u' => '4',
                '/[５]/u' => '5',
                '/[６]/u' => '6',
                '/[７]/u' => '7',
                '/[８]/u' => '8',
                '/[９]/u' => '9',
                '/[＿]/u' => '_',
                '/[｟]/u' => '((',
                '/[｠]/u' => '))',
            );

            $strASCII = preg_replace(array_keys($arrRegExTranslitMap), $arrRegExTranslitMap, $strNoControlChars);
        }

        $arrRegExMap = array(
            '/[~]/u' => '-',
            '/[’`]/u' => '\'',
            '/[€]/u' => 'E',
        );

        $strASCIICompat = preg_replace(array_keys($arrRegExMap), $arrRegExMap, $strASCII);

        $strRegExp = <<<'REGEXP'
/[^a-z0-9àâäéèêëîïôöùûüñ£$¤%µ*<>?,.;:§!²&"#'|_\\\/ç^@°=+{}()\[\]\-]++/ui
REGEXP;

        $strASCIIFilter = preg_replace($strRegExp, ' ', $strASCIICompat);
        $strASCIIFilterTrim = trim($strASCIIFilter);

        $strASCIIFilterFinal = $strASCIIFilterTrim;
        if ($intArgLength > 0) {
            $strASCIIFilterFinal = Tools::substr($strASCIIFilterTrim, 0, $intArgLength);
        }

        return $strASCIIFilterFinal;
    }

    /**
     * Slug safe for filename.
     *
     * @param     $strArgInput
     * @param int $intArgLength
     *
     * @return string
     */
    public static function slugName($strArgInput, $intArgLength = 0)
    {
        $strASCII = TNTOfficiel_Tools::translitASCII($strArgInput);

        // Space.
        $strASCIINoSpace = preg_replace('/\s++/u', '-', $strASCII);
        // Compat.
        $strASCIICompat = preg_replace('/[^a-zA-Z0-9._-]++/u', '_', $strASCIINoSpace);

        $strASCIISlug = $strASCIICompat;
        if ($intArgLength > 0) {
            $strASCIISlug = Tools::substr($strASCIICompat, 0, $intArgLength);
        }

        return $strASCIISlug;
    }

    /**
     * @param $mxdArgValue
     *
     * @return string
     */
    public static function serialize($mxdArgValue)
    {
        $strSerializedValue = serialize($mxdArgValue);

        return $strSerializedValue;
    }

    /**
     * @param string $strArgSerializedValue
     * @param bool   $object
     *
     * @return mixed
     */
    public static function unserialize($strArgSerializedValue, $object = false)
    {
        $mxdValue = Tools::unSerialize($strArgSerializedValue, $object);

        return $mxdValue;
    }

    /**
     * Used for PDF Label.
     *
     * @param $strArgValue
     *
     * @return string
     */
    public static function encodeBase64($strArgValue)
    {
        return (string)base64_encode($strArgValue);
    }

    /**
     * @param $strArgValue
     *
     * @return string
     */
    public static function decodeBase64($strArgValue)
    {
        return (string)base64_decode($strArgValue);
    }

    /**
     * Used for DeliveryPoint Data.
     *
     * @param $strArgInflateValue
     *
     * @return string
     */
    public static function deflate($strArgInflateValue)
    {
        return (string)base64_encode(gzdeflate($strArgInflateValue));
    }

    /**
     * @param $strArgDeflateValue
     *
     * @return string
     */
    public static function inflate($strArgDeflateValue)
    {
        return (string)gzinflate(base64_decode($strArgDeflateValue));
    }

    /**
     * @param $strArgData
     *
     * @return bool|string
     */
    public static function B64URLDeflate($strArgData)
    {
        // Must be a string.
        if (!is_string($strArgData)) {
            return false;
        }
        // Compress GZ.
        $strCompress = gzdeflate($strArgData);
        if (!is_string($strCompress) || $strCompress === '') {
            return false;
        }
        // Encode Base64.
        $strEncode = base64_encode($strCompress);
        if (!is_string($strEncode) || $strEncode === '') {
            return false;
        }

        // URL friendly.
        $strEncode = strtr($strEncode, '+/', '-_');
        $strDeflateB64URL = rtrim($strEncode, '=');

        return $strDeflateB64URL;
    }

    /**
     * TNTOfficiel_Tools::inflate
     *
     * @param $strArgDeflateB64URL
     *
     * @return bool|string
     */
    public static function B64URLInflate($strArgDeflateB64URL)
    {
        // Must be a non-empty string.
        if (!is_string($strArgDeflateB64URL) || $strArgDeflateB64URL === '') {
            return false;
        }

        // Add missing right padding =.
        $intOriginalLength = strlen($strArgDeflateB64URL);
        $intPadSectionLength = ($intOriginalLength % 4);
        $intFinalLength = ($intPadSectionLength > 0 ? ($intOriginalLength + 4 - $intPadSectionLength) : 0);
        $strEncode = str_pad($strArgDeflateB64URL, $intFinalLength, '=', STR_PAD_RIGHT);
        // URL friendly revert.
        $strEncode = strtr($strEncode, '-_', '+/');

        // Decode Base64.
        $strCompress = base64_decode($strEncode);
        if (!is_string($strCompress) || $strCompress === ''
            || $strEncode !== base64_encode($strCompress)
        ) {
            return false;
        }
        // Decompress GZ.
        $strData = gzinflate($strCompress);
        if (!is_string($strData)) {
            return false;
        }

        return $strData;
    }

    /**
     * Get DateTime object from a validated timestamp, string or DateTime.
     *
     * @param string|int|DateTime $mxdArgDateTime
     *
     * @return null|DateTime
     */
    public static function getDateTime($mxdArgDateTime, $objArgDateTimeZone = null)
    {
        TNTOfficiel_Logstack::log();

        // Default DateTimeZone object.
        $strDefaultTZ = date_default_timezone_get();
        $objDateTimeZone = new DateTimeZone($strDefaultTZ);

        // DateTimeZone string.
        if (is_string($objArgDateTimeZone)) {
            $objArgDateTimeZone = new DateTimeZone($objArgDateTimeZone);
        }
        // DateTimeZone object.
        if ($objArgDateTimeZone instanceof DateTimeZone) {
            $objDateTimeZone = $objArgDateTimeZone;
        }

        // Check DateTime object.
        if ($mxdArgDateTime instanceof DateTime) {
            $objDateTimeCheck = clone $mxdArgDateTime;
            $objDateTimeCheck->setTimezone($objDateTimeZone);
            return $objDateTimeCheck;
        }

        // Check datetime string.
        if (is_string($mxdArgDateTime)
            && preg_match(
                '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$/ui',
                $mxdArgDateTime,
                $arrBackRef
            ) === 1
            && checkdate((int)$arrBackRef[2], (int)$arrBackRef[3], (int)$arrBackRef[1])
        ) {
            $objDateTimeCheck = DateTime::createFromFormat('Y-m-d H:i:s', $mxdArgDateTime, $objDateTimeZone);
            if (is_object($objDateTimeCheck)) {
                $objDateTimeCheck->setTimezone($objDateTimeZone);
                if ($mxdArgDateTime === $objDateTimeCheck->format('Y-m-d H:i:s')) {
                    return $objDateTimeCheck;
                }
            }
        }

        // Check date string.
        if (is_string($mxdArgDateTime)
            && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/ui', $mxdArgDateTime, $arrBackRef) === 1
            && checkdate((int)$arrBackRef[2], (int)$arrBackRef[3], (int)$arrBackRef[1])
        ) {
            $objDateCheck = DateTime::createFromFormat('Y-m-d', $mxdArgDateTime, $objDateTimeZone);
            if (is_object($objDateCheck)) {
                $objDateCheck->setTimezone($objDateTimeZone);
                $objDateCheck->modify('midnight');
                if ($mxdArgDateTime === $objDateCheck->format('Y-m-d')) {
                    return $objDateCheck;
                }
            }
        }

        // Check time string.
        if (is_string($mxdArgDateTime)
            && preg_match('/^([0-9]{2}):([0-9]{2}):([0-9]{2})$/ui', $mxdArgDateTime, $arrBackRef) === 1
        ) {
            $objTimeCheck = DateTime::createFromFormat('H:i:s', $mxdArgDateTime, $objDateTimeZone);
            if (is_object($objTimeCheck)) {
                $objTimeCheck->setTimezone($objDateTimeZone);
                if ($mxdArgDateTime === $objTimeCheck->format('H:i:s')) {
                    return $objTimeCheck;
                }
            }
        }

        // String timestamp to int.
        if (is_string($mxdArgDateTime)
            && preg_match('/^[0-9]+$/ui', $mxdArgDateTime) === 1
        ) {
            $mxdArgDateTime = (int)$mxdArgDateTime;
        }

        // Check timestamp.
        if (is_int($mxdArgDateTime) && $mxdArgDateTime >= 0) {
            //$objTimeStampCheck = DateTime::createFromFormat('U', (string)$mxdArgDateTime);
            try {
                $objTimeStampCheck = new DateTime('@' . $mxdArgDateTime);
            } catch (Exception $objException) {
                $objTimeStampCheck = null;
            }
            if (is_object($objTimeStampCheck)) {
                $objTimeStampCheck->setTimezone($objDateTimeZone);
                if ($mxdArgDateTime === (int)$objTimeStampCheck->format('U')) {
                    return $objTimeStampCheck;
                }
            }
        }

        return null;
    }

    /**
     * Check if a date is in weekday (not weekend).
     *
     * @param $mxdArgDateTime
     *
     * @return bool|null
     */
    public static function isWeekDay($mxdArgDateTime)
    {
        TNTOfficiel_Logstack::log();

        $objDateTime = TNTOfficiel_Tools::getDateTime($mxdArgDateTime);
        if ($objDateTime === null) {
            return null;
        }

        $objDateTimeWeekDay = clone $objDateTime;
        $objDateTimeWeekDay->modify('previous weekday')->modify('next weekday');

        return ($objDateTime == $objDateTimeWeekDay);
    }

    /**
     * Get the first weekday from date, offset n weekdays.
     *
     * @param string|int|DateTime $mxdArgDateTime Current date.
     * @param int $intDaysOffset 0 for first available week days.
     * @param string $strArgFormat
     *
     * @return null
     */
    public static function getFirstWeekDay($mxdArgDateTime, $strArgFormat = 'U', $intDaysOffset = 0)
    {
        TNTOfficiel_Logstack::log();

        $objDateTime = TNTOfficiel_Tools::getDateTime($mxdArgDateTime);
        if ($objDateTime === null) {
            return null;
        }

        $objDateTimeWeekDay = clone $objDateTime;
        $objDateTimeWeekDay->modify('previous weekday')->modify('next weekday');
        if ($intDaysOffset < 0 || $intDaysOffset > 0) {
            $objDateTimeWeekDay->modify($intDaysOffset . ' weekday');
        }

        return TNTOfficiel_Tools::getDateTimeFormat($objDateTimeWeekDay, $strArgFormat);
    }

    /**
     * Get the date, offset n weekdays.
     *
     * @param string|int|DateTime $mxdArgDateTime Current date.
     * @param int $intDaysOffset 1 for next available week days.
     * @param string $strArgFormat
     *
     * @return null
     */
    public static function getNextWeekDay($mxdArgDateTime, $strArgFormat = 'U', $intDaysOffset = 1)
    {
        TNTOfficiel_Logstack::log();

        $objDateTime = TNTOfficiel_Tools::getDateTime($mxdArgDateTime);
        if ($objDateTime === null) {
            return null;
        }

        $objDateTimeNextWeekDay = clone $objDateTime;
        if ($intDaysOffset < 0 || $intDaysOffset > 0) {
            $objDateTimeNextWeekDay->modify('midnight')->modify($intDaysOffset . ' weekday');
        }

        return TNTOfficiel_Tools::getDateTimeFormat($objDateTimeNextWeekDay, $strArgFormat);
    }

    /**
     * Check if a date is today or in the future.
     *
     * @param $mxdArgDateTime
     *
     * @return bool|null
     */
    public static function isTodayOrLater($mxdArgDateTime)
    {
        TNTOfficiel_Logstack::log();

        $objDateTime = TNTOfficiel_Tools::getDateTime($mxdArgDateTime);
        if ($objDateTime === null) {
            return null;
        }

        $objDateTimeDay = clone $objDateTime;
        $objDateTimeDay->modify('midnight');

        $objDateTimeToday = new DateTime('midnight');

        return ($objDateTimeDay >= $objDateTimeToday);
    }

    /**
     * Get a formatted date (default is timestamp).
     *
     * @param string|int|DateTime $mxdArgDateTime
     * @param string              $strArgFormat
     * @param string|int|null     $objArgDefault
     *
     * @return null|int|string
     */
    public static function getDateTimeFormat($mxdArgDateTime, $strArgFormat = 'U', $objArgDefault = null)
    {
        TNTOfficiel_Logstack::log();

        $objDateTime = TNTOfficiel_Tools::getDateTime($mxdArgDateTime);
        // If fail.
        if ($objDateTime === null) {
            // Use default.
            $objDateTime = TNTOfficiel_Tools::getDateTime($objArgDefault);
        }
        // If fail again.
        if ($objDateTime === null) {
            return null;
        }

        $strDateTimeFormat = $objDateTime->format($strArgFormat);
        if ($strArgFormat === 'U') {
            return (int)$strDateTimeFormat;
        }

        $arrRegExLangMap = array(
            '/\b1st\b/u' => '1er',
            '/\b([0-9]+)(st|nd|rd|th)\b/u' => '\1',
        );

        $strDateTimeFormat = preg_replace(array_keys($arrRegExLangMap), $arrRegExLangMap, $strDateTimeFormat);

        $arrRegExLangMap = array(
            '/\bMonday\b/u' => 'Lundi',
            '/\bTuesday\b/u' => 'Mardi',
            '/\bWednesday\b/u' => 'Mercredi',
            '/\bThursday\b/u' => 'Jeudi',
            '/\bFriday\b/u' => 'Vendredi',
            '/\bSaturday\b/u' => 'Samedi',
            '/\bSunday\b/u' => 'Dimanche',
            '/\bJanuary\b/u' => 'Janvier',
            '/\bFebruary\b/u' => 'Février',
            '/\bMarch\b/u' => 'Mars',
            '/\bApril\b/u' => 'Avril',
            '/\bMay\b/u' => 'Mai',
            '/\bJune\b/u' => 'Juin',
            '/\bJuly\b/u' => 'Juillet',
            '/\bAugust\b/u' => 'Août',
            '/\bSeptember\b/u' => 'Septembre',
            '/\bOctober\b/u' => 'Octobre',
            '/\bNovember\b/u' => 'Novembre',
            '/\bDecember\b/u' => 'Décembre',
        );

        $strDateTimeFormat = preg_replace(array_keys($arrRegExLangMap), $arrRegExLangMap, $strDateTimeFormat);

        return $strDateTimeFormat;
    }

    /**
     * Compare last update timestamp with a refresh delay, to current timestamp.
     *
     * @param string|int|DateTime $mxdArgLastUpdate
     * @param int                 $intArgRefreshDelay
     *
     * @return bool|null true if last update timestamp is outdated. null if invalid date.
     */
    public static function isExpired($mxdArgLastUpdate, $intArgRefreshDelay = 0)
    {
        TNTOfficiel_Logstack::log();

        $mxdArgLastUpdate = TNTOfficiel_Tools::getDateTimeFormat($mxdArgLastUpdate);
        if ($mxdArgLastUpdate === null) {
            return null;
        }

        $intLastUpdate = (int)$mxdArgLastUpdate;
        if (!($intLastUpdate >= 0)) {
            $intLastUpdate = 0;
        }

        $intRefreshDelay = (int)$intArgRefreshDelay;
        if (!($intRefreshDelay >= 0)) {
            $intRefreshDelay = 0;
        }

        $objDateTimeNow = new DateTime('now');
        $intTSNow = (int)$objDateTimeNow->format('U');

        // If delay is passed.
        if ($intTSNow >= ($intLastUpdate + $intRefreshDelay)) {
            return true;
        }

        return false;
    }

    /**
     * Download an existing file or content.
     *
     * @param string      $strFileLocation
     * @param string|null $strContent
     * @param string      $strContentType
     *
     * @return bool false if error.
     */
    public static function download($strFileLocation, $strContent = null, $strContentType = 'application/octet-stream')
    {
        // File location must be a string.
        if (!is_string($strFileLocation)) {
            return false;
        }
        // If content, must be a string.
        if ($strContent !== null && !is_string($strContent)) {
            return false;
        }
        // If no content, file must exist.
        if ($strContent === null && !file_exists($strFileLocation)) {
            return false;
        }

        // End output buffer.
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        // Set header.
        ob_start();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-type: ' . $strContentType);
        header('Content-Disposition: attachment; filename="' . basename($strFileLocation) . '"');
        header('Content-Transfer-Encoding: binary');
        ob_end_flush();

        // Output content.
        if ($strContent !== null) {
            echo $strContent;
        } else {
            readfile($strFileLocation);
        }

        // We want to be sure that download content is the last thing this method will do.
        exit;
    }

    /**
     * Update the App cacert.pem file if needed.
     *
     * @return null|string The downloaded CA bundle location.
     */
    public static function getLastCACert()
    {
        static $boolStaticCallOnce = false;
        static $strStaticCached = null;

        if ($boolStaticCallOnce) {
            return $strStaticCached;
        }

        $boolStaticCallOnce = true;

        // https://curl.haxx.se/docs/caextract.html
        $strCACertURL = 'https://curl.haxx.se/ca/cacert.pem';
        $strCACertFilename = TNTOfficiel::getDirModule() . 'libraries/certs/cacert.pem';
        $intCACertTimestamp = file_exists($strCACertFilename) ? @filemtime($strCACertFilename) : 0;

        // If modification time exist and is less than 15 days old.
        if (($intCACertTimestamp > 0)
            && ((time() - $intCACertTimestamp) <= 60 * 60 * 24 * 15)
        ) {
            $strStaticCached = $strCACertFilename;
            // No update needed.
            return $strCACertFilename;
        }

        // Download last cacert.pem file content.
        $arrResult = TNTOfficiel_Tools::cURLRequest($strCACertURL);
        // Check content is valid.
        if ($arrResult['response'] === false
            || !preg_match('/(.*-----BEGIN CERTIFICATE-----.*-----END CERTIFICATE-----){50}$/Uims', $arrResult['response'])
            || substr(rtrim($arrResult['response']), -1) !== '-'
        ) {
            $strStaticCached = null;
            // Download Error.
            return null;
        }

        // If file does not exist.
        if (!file_exists($strCACertFilename)) {
            // Create it.
            @touch($strCACertFilename);
            @chmod($strCACertFilename, 0660);
        }

        // If writable file does not exist (Prevent fopen warning : failed to open stream).
        if (!file_exists($strCACertFilename)
            || !is_writable($strCACertFilename)
        ) {
            $strStaticCached = null;
            // Create Error.
            return null;
        }

        // Write content.
        $rscFile = fopen($strCACertFilename, 'w');
        if ($rscFile === false) {
            $strStaticCached = null;
            // Write Error.
            return null;
        }
        fwrite($rscFile, $arrResult['response']);
        fclose($rscFile);

        $strStaticCached = $strCACertFilename;

        return $strCACertFilename;
    }

    /**
     * Locate cacert bundle from App, PHP settings or OS.
     *
     * @return null|string The CA bundle located.
     */
    public static function locateCABundle()
    {
        static $boolStaticCallOnce = false;
        static $strStaticCached = null;

        if ($boolStaticCallOnce) {
            return $strStaticCached;
        }

        $boolStaticCallOnce = true;

        $arrCAfilesApp = array(
            // Prestashop
            _PS_CACHE_CA_CERT_FILE_,
            // Module
            TNTOfficiel_Tools::getLastCACert(),
        );

        foreach ($arrCAfilesApp as $strCACertFilename) {
            if (is_string($strCACertFilename)
                && $strCACertFilename !== ''
                && file_exists($strCACertFilename)
                && is_readable($strCACertFilename)
            ) {
                $strStaticCached = $strCACertFilename;

                return $strStaticCached;
            }
        }

        $arrCAfilesPHP = array(
            // Default for CURLOPT_CAINFO PHP 5.3.7+
            ini_get('curl.cainfo'),
            // Alternative PHP 5.6+
            ini_get('openssl.cafile'),
        );

        $arrCAfilesOS = array(
            // Red Hat, CentOS, Fedora (ca-certificates package)
            '/etc/pki/tls/certs/ca-bundle.crt',
            // Ubuntu, Debian (ca-certificates package)
            '/etc/ssl/certs/ca-certificates.crt',
            // FreeBSD (ca_root_nss package)
            '/usr/local/share/certs/ca-root-nss.crt',
            // SUSE Linux Enterprise Server 12 (ca-certificates package)
            '/var/lib/ca-certificates/ca-bundle.pem',
            // OS X homebrew (using the default path)
            '/usr/local/etc/openssl/cert.pem',
            // Google app engine
            '/etc/ca-certificates.crt',
            // Windows
            'C:\\windows\\system32\\curl-ca-bundle.crt',
            'C:\\windows\\curl-ca-bundle.crt',
        );

        $arrCAfiles = array_merge($arrCAfilesPHP, $arrCAfilesOS);

        foreach ($arrCAfiles as $strCACertFilename) {
            if (is_string($strCACertFilename)
                && $strCACertFilename !== ''
                && file_exists($strCACertFilename)
                && is_readable($strCACertFilename)
            ) {
                $strStaticCached = $strCACertFilename;

                return $strStaticCached;
            }
        }

        return $strStaticCached;
    }

    /**
     * Get X509 error message from error code.
     *
     * @param $intArgCode
     *
     * @return string|null
     */
    public static function getSSLVerifyResult($intArgCode)
    {
        $arrSSLVerifyResult = array(
            0 => 'X509_V_OK The operation was successful.',
            2 => 'X509_V_ERR_UNABLE_TO_GET_ISSUER_CERT The issuer certificate of an untrusted certificate could not be found.',
            3 => 'X509_V_ERR_UNABLE_TO_GET_CRL The CRL of a certificate could not be found.',
            4 => 'X509_V_ERR_UNABLE_TO_DECRYPT_CERT_SIGNATURE The certificate signature could not be decrypted. This means that the actual signature value could not be determined rather than it not matching the expected value. This is only meaningful for RSA keys.',
            5 => 'X509_V_ERR_UNABLE_TO_DECRYPT_CRL_SIGNATURE The CRL signature could not be decrypted. This means that the actual signature value could not be determined rather than it not matching the expected value. Unused.',
            6 => 'X509_V_ERR_UNABLE_TO_DECODE_ISSUER_PUBLIC_KEY The public key in the certificate SubjectPublicKeyInfo could not be read.',
            7 => 'X509_V_ERR_CERT_SIGNATURE_FAILURE The signature of the certificate is invalid.',
            8 => 'X509_V_ERR_CRL_SIGNATURE_FAILURE The signature of the certificate is invalid.',
            9 => 'X509_V_ERR_CERT_NOT_YET_VALID The certificate is not yet valid: the notBefore date is after the current time. See Verify return code: 9 (certificate is not yet valid) below for more information',
            10 => 'X509_V_ERR_CERT_HAS_EXPIRED The certificate has expired; that is, the notAfter date is before the current time. See Verify return code: 10 (certificate has expired) below for more information.',
            11 => 'X509_V_ERR_CRL_NOT_YET_VALID The CRL is not yet valid.',
            12 => 'X509_V_ERR_CRL_HAS_EXPIRED The CRL has expired.',
            13 => 'X509_V_ERR_ERROR_IN_CERT_NOT_BEFORE_FIELD The certificate notBefore field contains an invalid time.',
            14 => 'X509_V_ERR_ERROR_IN_CERT_NOT_AFTER_FIELD The certificate notAfter field contains an invalid time.',
            15 => 'X509_V_ERR_ERROR_IN_CRL_LAST_UPDATE_FIELD The CRL lastUpdate field contains an invalid time.',
            16 => 'X509_V_ERR_ERROR_IN_CRL_NEXT_UPDATE_FIELD The CRL nextUpdate field contains an invalid time.',
            17 => 'X509_V_ERR_OUT_OF_MEM An error occurred trying to allocate memory. This should never happen.',
            18 => 'X509_V_ERR_DEPTH_ZERO_SELF_SIGNED_CERT The passed certificate is self-signed and the same certificate cannot be found in the list of trusted certificates.',
            19 => 'X509_V_ERR_SELF_SIGNED_CERT_IN_CHAIN The certificate chain could be built up using the untrusted certificates but the root could not be found locally.',
            // AKA no certificate.
            20 => 'X509_V_ERR_UNABLE_TO_GET_ISSUER_CERT_LOCALLY The issuer certificate of a locally looked up certificate could not be found. This normally means the list of trusted certificates is not complete.',
            21 => 'X509_V_ERR_UNABLE_TO_VERIFY_LEAF_SIGNATURE No signatures could be verified because the chain contains only one certificate and it is not self-signed. See Verify return code: 21 (unable to verify the first certificate) below for more information.',
            22 => 'X509_V_ERR_CERT_CHAIN_TOO_LONG The certificate chain length is greater than the supplied maximum depth. Unused.',
            23 => 'X509_V_ERR_CERT_REVOKED The certificate has been revoked.',
            24 => 'X509_V_ERR_INVALID_CA A CA certificate is invalid. Either it is not a CA or its extensions are not consistent with the supplied purpose.',
            25 => 'X509_V_ERR_PATH_LENGTH_EXCEEDED The basicConstraints pathlength parameter has been exceeded.',
            26 => 'X509_V_ERR_INVALID_PURPOSE The supplied certificate cannot be used for the specified purpose.',
            27 => 'X509_V_ERR_CERT_UNTRUSTED The root CA is not marked as trusted for the specified purpose.',
            28 => 'X509_V_ERR_CERT_REJECTED The root CA is marked to reject the specified purpose.',
            29 => 'X509_V_ERR_SUBJECT_ISSUER_MISMATCH The current candidate issuer certificate was rejected because its subject name did not match the issuer name of the current certificate. Only displayed when the -issuer_checks option is set.',
            30 => 'X509_V_ERR_AKID_SKID_MISMATCH The current candidate issuer certificate was rejected because its subject key identifier was present and did not match the authority key identifier current certificate. Only displayed when the -issuer_checks option is set.',
            31 => 'X509_V_ERR_AKID_ISSUER_SERIAL_MISMATCH The current candidate issuer certificate was rejected because its issuer name and serial number were present and did not match the authority key identifier of the current certificate. Only displayed when the -issuer_checks option is set.',
            32 => 'X509_V_ERR_KEYUSAGE_NO_CERTSIGN The current candidate issuer certificate was rejected because its keyUsage extension does not permit certificate signing.',
            50 => 'X509_V_ERR_APPLICATION_VERIFICATION An application specific error. Unused.',
        );

        if (array_key_exists($intArgCode, $arrSSLVerifyResult)) {
            return $arrSSLVerifyResult[$intArgCode];
        }

        return null;
    }

    /**
     * @param string $strArgURL
     * @param array  $arrArgOptions
     *
     * @return array
     */
    public static function cURLRequest($strArgURL, $arrArgOptions = null)
    {
        TNTOfficiel_Logstack::log();

        $strURL = trim($strArgURL);

        $strCACertFilename = TNTOfficiel_Tools::locateCABundle();
        $intCACertTimestamp = file_exists($strCACertFilename) ? @filemtime($strCACertFilename) : 0;

        $arrResult = array(
            'options' => array(
                // Check server certificate's name against host.
                // 0: disable, 2: enable.
                CURLOPT_SSL_VERIFYHOST => 0,
                // Check server peer's certificate authenticity through certification authority (CA) for SSL/TLS.
                CURLOPT_SSL_VERIFYPEER => $intCACertTimestamp > 0,
                // Path to Certificate Authority (CA) bundle.
                // https://curl.haxx.se/docs/caextract.html
                // https://curl.haxx.se/ca/cacert.pem
                // Default : ini_get('curl.cainfo') PHP 5.3.7+
                // Alternative : ini_get('openssl.cafile') PHP 5.6+
                CURLOPT_CAINFO => $strCACertFilename,
                // Start a new cookie session (ignore all previous cookies session)
                CURLOPT_COOKIESESSION => true,
                // Follow HTTP 3xx redirects.
                CURLOPT_FOLLOWLOCATION => true,
                // Max redirects allowed.
                CURLOPT_MAXREDIRS => 8,
                // curl_exec return response string instead of true (no direct output).
                CURLOPT_RETURNTRANSFER => true,
                // Include response header in output.
                //CURLOPT_HEADER => false,
                // Include request header ?
                //CURLINFO_HEADER_OUT => false,
                // HTTP code >= 400 considered as error. Use curl_error (curl_exec return false ?).
                //CURLOPT_FAILONERROR => true,
                // Proxy.
                //CURLOPT_PROXY => $strProxy
                //CURLOPT_PROXYUSERPWD => 'user:password',
                //CURLOPT_PROXYAUTH => 1,
                //CURLOPT_PROXYPORT => 80,
                //CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
                // Timeout for connection to the server.
                CURLOPT_CONNECTTIMEOUT => TNTOfficiel::REQUEST_CONNECTTIMEOUT,
                // Timeout global.
                CURLOPT_TIMEOUT => TNTOfficiel::REQUEST_TIMEOUT,
            ),
            'response' => null,
            'info' => array(
                'http_code' => 0,
            ),
            'error' => null,
        );

        if (is_array($arrArgOptions)) {
            $arrResult['options'] = $arrArgOptions + $arrResult['options'];
        }

        // Check extension.
        if (!extension_loaded('curl')) {
            $objException = new Exception('PHP cURL extension is required');
            TNTOfficiel_Logger::logException($objException);
            // Communication Error.
            $arrResult['response'] = false;
            $arrResult['error'] = 'PHP cURL extension is required';

            return $arrResult;
        }

        $rscCURLHandler = curl_init();

        foreach ($arrResult['options'] as $intCURLConst => $mxdValue) {
            // May warn if open_basedir or deprecated safe_mode set.
            if ((ini_get('safe_mode') || ini_get('open_basedir'))
                && $intCURLConst === CURLOPT_FOLLOWLOCATION
            ) {
                continue;
            }
            curl_setopt($rscCURLHandler, $intCURLConst, $mxdValue);
        }

        curl_setopt($rscCURLHandler, CURLOPT_URL, $strURL);

        // curl_exec return false on error.
        $arrResult['response'] = curl_exec($rscCURLHandler);

        $arrResult['info'] = curl_getinfo($rscCURLHandler);
        // CURLINFO_SSL_VERIFYRESULT : Result of SSL certification verification (CURLOPT_SSL_VERIFYPEER).
        if (is_array($arrResult['info'])
            && array_key_exists('ssl_verify_result', $arrResult['info'])
        ) {
            $arrResult['info']['ssl_verify_result'] = TNTOfficiel_Tools::getSSLVerifyResult(
                $arrResult['info']['ssl_verify_result']
            );
        }

        $arrResult['error'] = curl_error($rscCURLHandler);

        curl_close($rscCURLHandler);

        return $arrResult;
    }

    /**
     * Select, show, explain or describe queries.
     *
     * @param string $strArgSQL
     * @param bool   $boolArgUseCache
     *
     * @return array|string string on error.
     */
    public static function getDbSelect($strArgSQL, $boolArgUseCache = true)
    {
        TNTOfficiel_Logstack::log();

        $boolSuccess = false;
        $objDB = Db::getInstance();
        $arrDBResult = null;
        $objException = null;

        $fltRequestTimeStart = microtime(true);

        try {
            // Get.
            $fltRequestTimeStart = microtime(true);
            $arrDBResult = $objDB->executeS($strArgSQL, true, $boolArgUseCache);
            $fltRequestTimeEnd = microtime(true);

            if ($arrDBResult === false) {
                $objException = new Exception($objDB->getMsgError());
            } else {
                $boolSuccess = true;
            }
        } catch (Exception $objException) {
            $fltRequestTimeEnd = microtime(true);
            // Exception processed next.
        }

        $fltRequestDelay = $fltRequestTimeEnd - $fltRequestTimeStart;

        // Error.
        if (is_object($objException)) {
            // Log request.
            TNTOfficiel_Logger::logRequest(
                $boolSuccess,
                'SQL-ERROR',
                array(
                    'query' => (string)$strArgSQL,
                    'cache' => $boolArgUseCache,
                ),
                $fltRequestDelay,
                $objException
            );
        }

        // SlowLog > 275ms.
        if (!is_object($objException) && $fltRequestDelay > 0.055 * 5) {
            // Log request.
            TNTOfficiel_Logger::logRequest(
                $boolSuccess,
                'SQL-READ-SLOW',
                array(
                    'query' => (string)$strArgSQL,
                    'cache' => $boolArgUseCache,
                ),
                $fltRequestDelay,
                $objException
            );
        }

        /*
        TNTOfficiel_Logstack::dump(
            array(
                'method' => sprintf('%s::%s', __CLASS__, __FUNCTION__),
                'query' => (string)$strArgSQL,
                'cache' => $boolArgUseCache,
                'delay' => $fltRequestDelay,
                'exception' => $objException,
                'result' => $arrDBResult,
            )
        );
        */

        if ($objException !== null) {
            TNTOfficiel_Logger::logException($objException);

            return $objException->getMessage();
        }

        return $arrDBResult;
    }

    /**
     * Create table, alter table, etc. queries.
     *
     * @param string $strArgSQL
     * @param bool   $boolArgUseCache
     *
     * @return true|string string on error.
     */
    public static function getDbExecute($strArgSQL, $boolArgUseCache = true)
    {
        TNTOfficiel_Logstack::log();

        $boolSuccess = false;
        $objDB = Db::getInstance();
        $boolDBResult = null;
        $objException = null;

        $fltRequestTimeStart = microtime(true);

        try {
            // Get.
            $fltRequestTimeStart = microtime(true);
            $boolDBResult = $objDB->execute($strArgSQL, $boolArgUseCache);
            $fltRequestTimeEnd = microtime(true);

            if ($boolDBResult === false) {
                $objException = new Exception($objDB->getMsgError());
            } else {
                $boolSuccess = true;
            }
        } catch (Exception $objException) {
            $fltRequestTimeEnd = microtime(true);
            // Exception processed next.
        }

        $fltRequestDelay = $fltRequestTimeEnd - $fltRequestTimeStart;

        // Error.
        if (is_object($objException)) {
            // Log request.
            TNTOfficiel_Logger::logRequest(
                $boolSuccess,
                'SQL-ERROR',
                array(
                    'query' => (string)$strArgSQL,
                    'cache' => $boolArgUseCache,
                ),
                $fltRequestDelay,
                $objException
            );
        }

        // Log request.
        TNTOfficiel_Logger::logRequest(
            $boolSuccess,
            'SQL-WRITE',
            array(
                'query' => (string)$strArgSQL,
                'cache' => $boolArgUseCache,
            ),
            $fltRequestDelay,
            $objException
        );

        if ($objException !== null) {
            TNTOfficiel_Logger::logException($objException);

            return $objException->getMessage();
        }

        return $boolDBResult;
    }

    /**
     * Delete queries.
     *
     * @param string $strArgTable
     * @param string $strArgWhere
     * @param int    $strArgLimit
     * @param bool   $boolArgUseCache
     *
     * @return true|string string on error.
     */
    public static function getDbDelete($strArgTable, $strArgWhere, $strArgLimit = 0, $boolArgUseCache = true)
    {
        TNTOfficiel_Logstack::log();

        $boolSuccess = false;
        $objDB = Db::getInstance();
        $boolDBResult = null;
        $objException = null;

        $fltRequestTimeStart = microtime(true);

        try {
            // Get.
            $fltRequestTimeStart = microtime(true);
            $boolDBResult = $objDB->delete($strArgTable, $strArgWhere, $strArgLimit, $boolArgUseCache);
            $fltRequestTimeEnd = microtime(true);

            if ($boolDBResult === false) {
                $objException = new Exception($objDB->getMsgError());
            } else {
                $boolSuccess = true;
            }
        } catch (Exception $objException) {
            $fltRequestTimeEnd = microtime(true);
            // Exception processed next.
        }

        $fltRequestDelay = $fltRequestTimeEnd - $fltRequestTimeStart;

        // Error.
        if (is_object($objException)) {
            // Log request.
            TNTOfficiel_Logger::logRequest(
                $boolSuccess,
                'SQL-ERROR',
                array(
                    'type' => 'DELETE',
                    'table' => $strArgTable,
                    'where' => $strArgWhere,
                    'limit' => $strArgLimit,
                    'cache' => $boolArgUseCache,
                ),
                $fltRequestDelay,
                $objException
            );
        }

        // Log request.
        TNTOfficiel_Logger::logRequest(
            $boolSuccess,
            'SQL-WRITE',
            array(
                'type' => 'DELETE',
                'table' => $strArgTable,
                'where' => $strArgWhere,
                'limit' => $strArgLimit,
                'cache' => $boolArgUseCache,
            ),
            $fltRequestDelay,
            $objException
        );

        if ($objException !== null) {
            TNTOfficiel_Logger::logException($objException);

            return $objException->getMessage();
        }

        return $boolDBResult;
    }

    /**
     * Insert (ignore) queries.
     *
     * @param string $strArgTable
     * @param array  $arrArgData
     * @param bool   $strArgNull
     * @param bool   $boolArgUseCache
     *
     * @return true|string string on error.
     */
    public static function getDbInsert($strArgTable, $arrArgData, $strArgNull = false, $boolArgUseCache = true)
    {
        TNTOfficiel_Logstack::log();

        $boolSuccess = false;
        $objDB = Db::getInstance();
        $boolDBResult = null;
        $objException = null;

        $fltRequestTimeStart = microtime(true);

        try {
            // Get.
            $fltRequestTimeStart = microtime(true);
            $boolDBResult = $objDB->insert($strArgTable, $arrArgData, $strArgNull, $boolArgUseCache, Db::INSERT_IGNORE);
            $fltRequestTimeEnd = microtime(true);

            if ($boolDBResult === false) {
                $objException = new Exception($objDB->getMsgError());
            } else {
                $boolSuccess = true;
            }
        } catch (Exception $objException) {
            $fltRequestTimeEnd = microtime(true);
            // Exception processed next.
        }

        $fltRequestDelay = $fltRequestTimeEnd - $fltRequestTimeStart;

        // Error.
        if (is_object($objException)) {
            // Log request.
            TNTOfficiel_Logger::logRequest(
                $boolSuccess,
                'SQL-ERROR',
                array(
                    'type' => 'INSERT IGNORE',
                    'table' => $strArgTable,
                    'data' => $arrArgData,
                    'null' => $strArgNull,
                    'cache' => $boolArgUseCache,
                ),
                $fltRequestDelay,
                $objException
            );
        }

        // Log request.
        TNTOfficiel_Logger::logRequest(
            $boolSuccess,
            'SQL-WRITE',
            array(
                'type' => 'INSERT IGNORE',
                'table' => $strArgTable,
                'data' => $arrArgData,
                'null' => $strArgNull,
                'cache' => $boolArgUseCache,
            ),
            $fltRequestDelay,
            $objException
        );

        if ($objException !== null) {
            TNTOfficiel_Logger::logException($objException);

            return $objException->getMessage();
        }

        return $boolDBResult;
    }

    /**
     * Check if a table name exist.
     *
     * @param $strArgTableName
     *
     * @return bool|string. true if it exists, false if it does not exist, string on error.
     */
    public static function isTableExist($strArgTableName)
    {
        TNTOfficiel_Logstack::log();

        // Test if table exist.
        $strSQLTableExist = <<<SQL
SHOW TABLES LIKE '${strArgTableName}';
SQL;

        // Get table (cache must be disabled).
        $arrDBResult = TNTOfficiel_Tools::getDbSelect($strSQLTableExist, false);
        if (!is_array($arrDBResult)) {
            return null;
        }

        // if the table exists.
        if (count($arrDBResult) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Check if columns name list exist.
     *
     * @param $strArgTableName
     * @param $arrArgStrColumnNameList
     *
     * @return bool|string. true if it exists, false if it does not exist, string on error.
     */
    public static function isTableColumnsExist($strArgTableName, $arrArgStrColumnNameList)
    {
        TNTOfficiel_Logstack::log();

        // List columns in table.
        $strSQLTableColumns = <<<SQL
SHOW COLUMNS FROM `${strArgTableName}`;
SQL;

        if (TNTOfficiel_Tools::isTableExist($strArgTableName) !== true) {
            return false;
        }

        // Get existing columns (cache must be disabled).
        $arrDBResultColumns = TNTOfficiel_Tools::getDbSelect($strSQLTableColumns, false);
        if (!is_array($arrDBResultColumns)) {
            return null;
        }

        $arrStrColumnNameExistingList = array();
        foreach ($arrDBResultColumns as $arrRowColumns) {
            if (array_key_exists('Field', $arrRowColumns)) {
                $arrStrColumnNameExistingList[] = $arrRowColumns['Field'];
            }
        }

        $arrStrColumnNameMissingList = array();

        // Search columns.
        $arrStrColumnNameSearchList = (array)$arrArgStrColumnNameList;
        foreach ($arrStrColumnNameSearchList as $strColumnNameSearch) {
            if (!in_array($strColumnNameSearch, $arrStrColumnNameExistingList)) {
                $arrStrColumnNameMissingList[] = $strColumnNameSearch;
            }
        }

        // Missing columns.
        if (count($arrStrColumnNameMissingList) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Method for setting field data according to the size of fields that may change,
     * without having to write the size in hard copy several times.
     * Also allows truncation logging.
     *
     * @param $objArgModel
     * @param $strArgFieldName
     * @param $strArgFieldValue
     *
     * @return bool
     */
    public static function setField($objArgModel, $strArgFieldName, $strArgFieldValue)
    {
        // Max fields length.
        $arrDefinition = ObjectModel::getDefinition($objArgModel);

        if (is_array($arrDefinition)
            && array_key_exists('fields', $arrDefinition)
            && is_array($arrDefinition['fields'])
            && array_key_exists($strArgFieldName, $arrDefinition['fields'])
            && is_array($arrDefinition['fields'][$strArgFieldName])
        ) {
            if (array_key_exists('type', $arrDefinition['fields'][$strArgFieldName])
                && $arrDefinition['fields'][$strArgFieldName]['type'] > ObjectModel::TYPE_STRING
                && array_key_exists('size', $arrDefinition['fields'][$strArgFieldName])
                && $arrDefinition['fields'][$strArgFieldName]['size'] > 0
            ) {
                $intSize = (int)$arrDefinition['fields'][$strArgFieldName]['size'];
                $objArgModel->{$strArgFieldName} = Tools::substr($strArgFieldValue, 0, $intSize);

                if ($objArgModel->{$strArgFieldName} !== $strArgFieldValue) {
                    // log truncation.
                }

                return true;
            }

            $objArgModel->{$strArgFieldName} = $strArgFieldValue;
            return true;
        }

        return false;
    }

    /**
     * @param $arrArg
     * @param $arrArgOrderKey
     *
     * @return array. Sorted array.
     */
    public static function arrayOrderKey($arrArg, $arrArgOrderKey)
    {
        // List of sorted selected key.
        $arrKeyExistSort = array_intersect_key(array_flip($arrArgOrderKey), $arrArg);
        // List of unsorted key left.
        $arrKeyUnExistUnSort = array_diff_key($arrArg, $arrKeyExistSort);
        // Append unsorted list to sorted.
        $arrOrdered = array_merge($arrKeyExistSort, $arrArg) + $arrKeyUnExistUnSort;

        return $arrOrdered;
    }

    /**
     * Get HTTP Request state.
     *
     * @param      $strArgTargetURL
     * @param null $strArgMyIPURL
     *
     * @return array
     */
    public static function getHTTPRequestState($strArgTargetURL, $strArgMyIPURL = null)
    {
        $arrTarget = TNTOfficiel_Tools::cURLRequest($strArgTargetURL);
        // First line, max 512 chars.
        $arrTarget['response'] = substr(strtok($arrTarget['response'], "\n"), 0, 512);

        $arrState = array(
            // Browser IP.
            'Browser IP' => TNTOfficiel_Tools::getClientIPAddress(),
        );

        $strServerName = array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : null;
        $arrState += array(
            // Server Domain Name.
            'Server Domain Name Resolution' => $strServerName === null ? null : sprintf(
                '%s : %s [%s] : %s',
                $strServerName,
                gethostbyname($strServerName),
                (gethostbynamel($strServerName . '.') === false) ? 'HOST' : 'DNS',
                preg_replace('/"/ui', '\'', TNTOfficiel_Tools::encJSON(@dns_get_record($strServerName), 0))
            ),
        );

        $arrState += array(
            // Server Internal IP.
            'Server Internal IP' => $_SERVER['SERVER_ADDR'],
            // Server Local IP.
            'Server Local IP' => $arrTarget['info']['local_ip'],
        );

        if (is_string($strArgMyIPURL)) {
            $arrMyIP = TNTOfficiel_Tools::cURLRequest($strArgMyIPURL);
            $arrState += array(
                // Server IP.
                'Server IP' => trim($arrMyIP['response']),
            );
        }

        $strHostNameTNT = parse_url($strArgTargetURL, PHP_URL_HOST);

        $arrState += array(
            // Target Internal IP.
            'Target Internal IP' => $arrTarget['info']['primary_ip'],
            // Target Domain Name.
            'Target Domain Name Resolution' => sprintf(
                '%s : %s [%s] : %s',
                $strHostNameTNT,
                gethostbyname($strHostNameTNT),
                (gethostbynamel($strHostNameTNT . '.') === false) ? 'HOST' : 'DNS',
                preg_replace('/"/ui', '\'', TNTOfficiel_Tools::encJSON(@dns_get_record($strHostNameTNT), 0))
            ),
            'Target Status' => array(
                'options' => array(
                    'ca_info' => $arrTarget['options'][CURLOPT_CAINFO],
                    'verify_peer' => $arrTarget['options'][CURLOPT_SSL_VERIFYPEER],
                ),
                'url' => $strArgTargetURL,
                'code' => $arrTarget['info']['http_code'],
                'wsdl' => $arrTarget['response'],
                'error' => $arrTarget['error'],
                'check' => (
                    ($arrTarget['info']['http_code'] === 200)
                    && (preg_match('/<wsdl:definitions/ui', $arrTarget['response']) === 1)
                    && ($arrTarget['error'] === '')
                ),
            ),
        );

        return $arrState;
    }

    /**
     * Get Module State.
     *
     * @param null $strArgName
     *
     * @return array
     */
    public static function getModuleState($strArgName = null)
    {
        $strName = Tools::strtolower($strArgName);

        $arrAllowedExt = array('php', 'tpl', 'js', 'css');
        $objModule = false;

        if (Validate::isModuleName($strName)) {
            $objModule = Module::getInstanceByName($strName);
        }

        if (!$objModule) {
            return array();
        }

        //$objModule->name === $strName;

        // Overrides installed by the module.
        $arrModuleOverride = null;
        $strModulePath = $objModule->getLocalPath() . 'override';
        // Folder must exist.
        if (is_dir($strModulePath)) {
            $arrModuleOverride = array(
                'getOverrides' => $objModule->getOverrides(),
                $strModulePath => TNTOfficiel_Tools::searchFiles($strModulePath, true, $arrAllowedExt),
            );
        }

        // Module override from theme.
        $arrThemeOverride = null;
        $strThemePath = _PS_THEME_DIR_ . 'modules/' . $strName;
        // Folder must exist.
        if (is_dir($strThemePath)) {
            $arrThemeOverride = array(
                _PS_DEFAULT_THEME_NAME_ => file_exists($strThemePath . '/' . $strName . '.php'),
                $strThemePath => TNTOfficiel_Tools::searchFiles($strThemePath, true, $arrAllowedExt),
            );
        }

        // Module override.
        $arrClassOverride = null;
        $strClassPath = _PS_OVERRIDE_DIR_ . 'modules/' . $strName;
        // Folder must exist.
        if (is_dir($strClassPath)) {
            // Module Class Override.
            $arrClassOverride = array(
                $strName . 'Override' => file_exists($strClassPath . '/' . $strName . '.php'),
                $strClassPath => TNTOfficiel_Tools::searchFiles($strClassPath, true, $arrAllowedExt),
            );
        }

        return array(
            'module' => array(
                // === _PS_MODULE_DIR_.$strName.DIRECTORY_SEPARATOR,
                'getLocalPath' => $objModule->getLocalPath(),
                // === __PS_BASE_URI__.'modules/'.$strName.'/';
                'getPathUri' => $objModule->getPathUri(), // BO
                'isEnabled' => Module::isEnabled($strName),
                'isEnabledForShopContext' => $objModule->isEnabledForShopContext(),
            ),
            'override' => array(
                'module' => $arrModuleOverride,
                'theme' => $arrThemeOverride,
                'global' => $arrClassOverride,
            ),
        );
    }

    /**
     * Get filesystem and url location.
     *
     * @return array
     */
    public static function getLocationInfo()
    {
        return array(
            'dir' => array(
                // === _PS_ROOT_DIR_.DIRECTORY_SEPARATOR
                'TNTOfficiel::getDirPS()' => TNTOfficiel::getDirPS(),
                // === _PS_MODULE_DIR_.TNTOfficiel::MODULE_NAME.DIRECTORY_SEPARATOR,
                // === $objTNTOfficiel->getLocalPath()
                'TNTOfficiel::getDirModule()' => TNTOfficiel::getDirModule(),
                'TNTOfficiel::getDirModule(js)' => TNTOfficiel::getDirModule('js'),
                'TNTOfficiel::getDirModule(css)' => TNTOfficiel::getDirModule('css'),
                'TNTOfficiel::getDirModule(image)' => TNTOfficiel::getDirModule('image'),
                'TNTOfficiel::getDirModule(template)' => TNTOfficiel::getDirModule('template'),
                TNTOfficiel::getDirPS() => array(
                    'TNTOfficiel::getFolderBase()' => TNTOfficiel::getFolderBase(),
                    'TNTOfficiel::getFolderBase(_THEMES_DIR_)' => TNTOfficiel::getFolderBase('_THEMES_DIR_'),
                    'TNTOfficiel::getFolderBase(_THEME_DIR_)' => TNTOfficiel::getFolderBase('_THEME_DIR_'),
                    //'_PS_JS_DIR_' => _PS_JS_DIR_,  // PS ASSET
                    'TNTOfficiel::getFolderBase(_PS_JS_DIR_)' => TNTOfficiel::getFolderBase('_PS_JS_DIR_'),
                    'TNTOfficiel::getFolderBase(_PS_CSS_DIR_)' => TNTOfficiel::getFolderBase('_PS_CSS_DIR_'),
                    'TNTOfficiel::getFolderBase(_MODULE_DIR_)' => TNTOfficiel::getFolderBase('_MODULE_DIR_'),
                    'TNTOfficiel::getFolderModule()' => TNTOfficiel::getFolderModule(),
                    'TNTOfficiel::getFolderModule(js)' => TNTOfficiel::getFolderModule('js'),
                    'TNTOfficiel::getFolderModule(css)' => TNTOfficiel::getFolderModule('css'),
                    'TNTOfficiel::getFolderModule(image)' => TNTOfficiel::getFolderModule('image'),
                    'TNTOfficiel::getFolderModule(template)' => TNTOfficiel::getFolderModule('template'),
                    TNTOfficiel::getFolderModule() => array(
                        'TNTOfficiel::getPathJS()' => TNTOfficiel::getPathJS(),
                        'TNTOfficiel::getPathCSS()' => TNTOfficiel::getPathCSS(),
                        'TNTOfficiel::getPathImage()' => TNTOfficiel::getPathImage(),
                        'TNTOfficiel::getPathTemplate()' => TNTOfficiel::getPathTemplate(),
                    ),
                ),
            ),
            'url' => array(
                'TNTOfficiel::getURLDomain()' => TNTOfficiel::getURLDomain(),
                TNTOfficiel::getURLDomain() => array(
                    'TNTOfficiel::getURIBase(__PS_BASE_URI__, true)' => TNTOfficiel::getURIBase('__PS_BASE_URI__', true),
                    'TNTOfficiel::getURIBase(_THEMES_DIR_, true)' => TNTOfficiel::getURIBase('_THEMES_DIR_', true),
                    'TNTOfficiel::getURIBase(_THEME_DIR_, true)' => TNTOfficiel::getURIBase('_THEME_DIR_', true),
                    'TNTOfficiel::getURIBase(_PS_JS_DIR_, true)' => TNTOfficiel::getURIBase('_PS_JS_DIR_', true),
                    'TNTOfficiel::getURIBase(_PS_CSS_DIR_, true)' => TNTOfficiel::getURIBase('_PS_CSS_DIR_', true),
                    'TNTOfficiel::getURIBase(_MODULE_DIR_, true)' => TNTOfficiel::getURIBase('_MODULE_DIR_', true),
                    // === __PS_BASE_URI__.'modules/'.TNTOfficiel::MODULE_NAME.'/';
                    // === $objTNTOfficiel->getPathUri(), // BO only
                    'TNTOfficiel::getURLModulePath()' => TNTOfficiel::getURLModulePath(), // BO + FO
                    'TNTOfficiel::getURLModulePath(js)' => TNTOfficiel::getURLModulePath('js'), // BO + FO
                    'TNTOfficiel::getURLModulePath(css)' => TNTOfficiel::getURLModulePath('css'), // BO + FO
                    'TNTOfficiel::getURLModulePath(image)' => TNTOfficiel::getURLModulePath('image'), // BO + FO
                    'TNTOfficiel::getURLModulePath(template)' => TNTOfficiel::getURLModulePath('template'), // BO + FO
                    TNTOfficiel::getURLModulePath() => array(
                        'TNTOfficiel::getPathJS()' => TNTOfficiel::getPathJS(),
                        'TNTOfficiel::getPathCSS()' => TNTOfficiel::getPathCSS(),
                        'TNTOfficiel::getPathImage()' => TNTOfficiel::getPathImage(),
                        'TNTOfficiel::getPathTemplate()' => TNTOfficiel::getPathTemplate(),
                    ),
                ),
                'TNTOfficiel::getURLFrontBase()' => TNTOfficiel::getURLFrontBase(),
            ),
        );
    }

    /**
     * Search for files.
     *
     * @param string $strArgPath       Path.
     * @param bool   $boolArgRecursive Include search in sub-folders.
     * @param array  $arrArgExt        Filter using this list of extensions (case-insensitive). Empty list for no filter.
     * @param bool   $boolArgAbsolute  Include absolute resolved path in result.
     *
     * @return array|false false if error.
     */
    public static function searchFiles(
        $strArgPath,
        $boolArgRecursive = false,
        $arrArgExt = array(),
        $boolArgAbsolute = false
    ) {
        $arrFileTime = array();

        if (!is_string($strArgPath)) {
            return false;
        }

        $strPath = realpath($strArgPath);
        // No real path existing.
        if ($strPath === false) {
            return false;
        }

        // Add final separator.
        if (Tools::substr($strPath, -1) !== DIRECTORY_SEPARATOR) {
            $strPath .= DIRECTORY_SEPARATOR;
        }

        if (!is_dir($strPath)) {
            return false;
        }

        if (!is_array($arrArgExt)) {
            return false;
        }

        $arrExt = array();
        foreach ($arrArgExt as $strExt) {
            $strExt = Tools::strtolower($strExt);
            $arrExt[$strExt] = $strExt;
        }

        $arrFiles = Tools::scandir($strPath, false, '', $boolArgRecursive);
        foreach ($arrFiles as $strFileName) {
            $strFileLocation = $strPath . $strFileName;
            if (file_exists($strFileLocation)) {
                $strCmpFileName = Tools::strtolower($strFileName);
                $arrCmpFileName = explode('.', $strCmpFileName);
                $strCmpFileExt = array_pop($arrCmpFileName);
                if (count($arrExt) === 0 || array_key_exists($strCmpFileExt, $arrExt)) {
                    $arrFileTime[$boolArgAbsolute ? $strFileLocation : $strFileName] = filemtime($strFileLocation);
                }
            }
        }

        return $arrFileTime;
    }

    /**
     * @return array
     */
    public static function getPHPConfig()
    {
        /*
         * User
         */

        $arrUser = posix_getpwuid(posix_geteuid());

        /*
         * Environment
         */

        $arrEnv = array(
            'http_proxy' => getenv('http_proxy'),
            'https_proxy' => getenv('https_proxy'),
            'ftp_proxy' => getenv('ftp_proxy'),
        );

        /*
         * Constant
         */

        $arrPHPConstants = array(
            'PHP_OS' => PHP_OS,
            'PHP_VERSION' => PHP_VERSION,
            'PHP_SAPI' => PHP_SAPI,
            'PHP_INT_SIZE (bits)' => PHP_INT_SIZE * 8,
        );

        /*
         * Extension
         */

        $arrPHPExtensions = array_intersect_key(
            array_flip(get_loaded_extensions()),
            array(
                'curl' => true,
                'soap' => true,
                'session' => true,
                'mcrypt' => true,
                'mhash' => true,
                'mbstring' => true,
                'iconv' => true,
                'zip' => true,
                'zlib' => true,
                'dom' => true,
                'xml' => true,
                'SimpleXML' => true,
                'Zend OPcache' => true,
                'ionCube Loader' => true,
            )
        );

        /*
         * Configuration
         */

        $arrPHPConfigurationDefault = array(

            /* php */

            'max_execution_time' => '30',
            'memory_limit' => '128M',
            'max_input_vars' => '8192',
            // Deprecated in PHP 5.3. Removed in PHP 5.4.
            'magic_quotes' => 'Off',
            'magic_quotes_gpc' => 'Off',

            /* core - file uploads */

            // https://www.php.net/manual/en/ini.core.php#ini.upload-max-filesize
            'upload_max_filesize' => '4M',

            /* core - language options */

            // https://www.php.net/manual/en/ini.core.php#ini.disable-functions
            'disable_functions' => '',
            // https://www.php.net/manual/en/ini.core.php#ini.disable-classes
            'disable_classes' => '',

            /* core - paths and directories */

            // https://www.php.net/manual/en/ini.core.php#ini.open-basedir
            'open_basedir' => '',

            /* core - data handling */

            // Deprecated in PHP 5.3. Removed in PHP 5.4.
            'register_globals' => 'Off',
            'default_charset' => 'UTF-8',

            /* safe mode */

            // Deprecated in PHP 5.3. Removed in PHP 5.4.
            'safe_mode' => '',
            'safe_mode_gid' => '',
            'safe_mode_exec_dir' => '',
            'safe_mode_include_dir' => '',

            /* filesystem */

            'allow_url_fopen' => 'On',
            // Deprecated in PHP 5.2. Removed in PHP 7.4.
            'allow_url_include' => 'Off',
            'default_socket_timeout' => '60',

            /* opcache */

            'opcache.enable' => 'true',
        );

        $arrPHPConfiguration = array_intersect_key(ini_get_all(null, false), $arrPHPConfigurationDefault);
        $arrPHPConfiguration = TNTOfficiel_Tools::arrayOrderKey(
            $arrPHPConfiguration,
            array_keys($arrPHPConfigurationDefault)
        );

        if (array_key_exists('open_basedir', $arrPHPConfiguration)) {
            $arrPHPConfiguration['open_basedir'] = explode(PATH_SEPARATOR, $arrPHPConfiguration['open_basedir']);
        }

        /*
         * Time
         */

        $arrPHPTime = array(
            'date_default_timezone_set' => date_default_timezone_get(),
            'date.timezone' => ini_get('date.timezone'),
            'date' => date('Y-m-d H:i:s P T (e)'),
        );

        return array(
            'user' => $arrUser,
            'env' => $arrEnv,
            'constants' => $arrPHPConstants,
            'extensions' => $arrPHPExtensions,
            'configuration' => $arrPHPConfiguration,
            'time' => $arrPHPTime,
        );
    }

    public static function getPSConfig()
    {
        /*
         * Shop Context
         */

        $flagShopContext = Shop::getContext();
        $arrConstShopContext = array();

        if ($flagShopContext & Shop::CONTEXT_SHOP) {
            $arrConstShopContext[] = 'Shop::CONTEXT_SHOP';
        }
        if ($flagShopContext & Shop::CONTEXT_GROUP) {
            $arrConstShopContext[] = 'Shop::CONTEXT_GROUP';
        }
        if ($flagShopContext & Shop::CONTEXT_ALL) {
            $arrConstShopContext[] = 'Shop::CONTEXT_ALL';
        }

        $objContext = Context::getContext();
        $objPSShop = $objContext->shop;

        $arrPSContext = array(
            'Shop' => array(
                'getContext()' => $arrConstShopContext,
                'isFeatureActive()' => Shop::isFeatureActive(),
                'getContextShopGroupID()' => (int)Shop::getContextShopGroupID(),
                'getContextShopGroupID(true)' => (int)Shop::getContextShopGroupID(true),
                'getContextShopID()' => (int)Shop::getContextShopID(),
            ),
            'Context::getContext()->shop' => array(
                'id' => $objPSShop->id,
                'id_shop_group' => $objPSShop->id_shop_group,
                'physical_uri' => $objPSShop->physical_uri,
                'virtual_uri' => $objPSShop->virtual_uri,

                'getBaseURL(true)' => $objPSShop->getBaseURL(true),
                // === $objPSShop->getBaseURI() === $objPSShop->physical_uri . $objPSShop->virtual_uri
                // FO
                'getBaseURI()' => $objPSShop->getBaseURI(),
            ),
            // BO + FO
            '__PS_BASE_URI__' => __PS_BASE_URI__,
        );

        /*
         * Configuration
         */

        $arrPSConfig = Configuration::getMultiple(
            array(

                //'PS_INSTALL_VERSION',

                /*
                 * Carrier
                 */

                /* Shipping */

                'PS_SHIPPING_HANDLING',
                'PS_SHIPPING_FREE_PRICE',
                'PS_SHIPPING_FREE_WEIGHT',

                'PS_CARRIER_DEFAULT',
                'PS_CARRIER_DEFAULT_SORT',
                'PS_CARRIER_DEFAULT_ORDER',

                /*
                 * Localization
                 */

                /* Localization */

                'PS_LANG_DEFAULT',
                'PS_COUNTRY_DEFAULT',
                'PS_CURRENCY_DEFAULT',

                'PS_WEIGHT_UNIT',
                'PS_DISTANCE_UNIT',
                'PS_VOLUME_UNIT',
                'PS_DIMENSION_UNIT',

                'PS_LOCALE_LANGUAGE',
                'PS_LOCALE_COUNTRY',
                //'PS_TIMEZONE',

                /* Country */

                'PS_RESTRICT_DELIVERED_COUNTRIES',

                /* Taxes */

                'PS_TAX',
                'PS_TAX_DISPLAY',
                'PS_TAX_ADDRESS_TYPE',
                'PS_USE_ECOTAX',
                'PS_ECOTAX_TAX_RULES_GROUP_ID',

                /*
                 * Preferences
                 */

                /* General */

                'PS_SSL_ENABLED',
                'PS_SSL_ENABLED_EVERYWHERE',
                'PS_PRICE_ROUND_MODE',
                'PS_ROUND_TYPE',
                'PS_PRICE_DISPLAY_PRECISION',
                'PS_MULTISHOP_FEATURE_ACTIVE',
                // PS 1.6.1.16+
                'PS_API_KEY',

                /* Order */

                // General
                //'PS_ORDER_PROCESS_TYPE',
                'PS_GUEST_CHECKOUT_ENABLED',
                'PS_DISALLOW_HISTORY_REORDERING',
                'PS_PURCHASE_MINIMUM',
                'PS_SHIP_WHEN_AVAILABLE',
                'PS_CONDITIONS',
                // Multi-Shipping (deprecated)
                'PS_ALLOW_MULTISHIPPING',
                // Gift Wrapping
                'PS_GIFT_WRAPPING',
                'PS_GIFT_WRAPPING_PRICE',
                'PS_GIFT_WRAPPING_TAX_RULES_GROUP',
                'PS_RECYCLABLE_PACK',

                /* Product */

                'PS_ATTRIBUTE_ANCHOR_SEPARATOR',
                'PS_ORDER_OUT_OF_STOCK',
                'PS_STOCK_MANAGEMENT',
                'PS_ADVANCED_STOCK_MANAGEMENT',

                /* Customer */

                'PS_REGISTRATION_PROCESS_TYPE',
                'PS_ONE_PHONE_AT_LEAST',
                'PS_B2B_ENABLE',

                /* SEO & URL */

                'PS_REWRITING_SETTINGS',
                'PS_ALLOW_ACCENTED_CHARS_URL',
                'PS_CANONICAL_REDIRECT',

                /* Stores */
                'PS_SHOP_NAME',
                'PS_SHOP_EMAIL',
                'PS_SHOP_PHONE',
                //'PS_SHOP_DOMAIN',
                //'PS_SHOP_DOMAIN_SSL',
                //'PS_SHOP_ACTIVITY',

                /*
                 * Advanced Parameters
                 */

                /* Performance */

                // Compile template 0: never 1: if template updated, 2: on each call.
                'PS_SMARTY_FORCE_COMPILE',
                // Smarty cache enabled ?
                'PS_SMARTY_CACHE',
                // If enabled, cache using filesystem or mysql ?
                'PS_SMARTY_CACHING_TYPE',
                // Clear cache never or everytime ?
                'PS_SMARTY_CLEAR_CACHE',

                'PS_DISABLE_OVERRIDES',

                'PS_CSS_THEME_CACHE',
                'PS_JS_THEME_CACHE',
                'PS_HTML_THEME_COMPRESSION',
                'PS_JS_HTML_THEME_COMPRESSION',
                'PS_JS_DEFER',
                'PS_HTACCESS_CACHE_CONTROL',

                'PS_CIPHER_ALGORITHM',

            )
        );

        /*
         * Constant
         */

        //$__constants = get_defined_constants(true);
        $arrPSConstant = array(
            '_PS_VERSION_' => _PS_VERSION_,
            //'Smarty::SMARTY_VERSION' => preg_replace('/^Smarty-/ui', '', Smarty::SMARTY_VERSION),
            '_PS_JQUERY_VERSION_' => _PS_JQUERY_VERSION_,

            '_PS_MODE_DEV_' => _PS_MODE_DEV_,
            '_PS_DEBUG_PROFILING_' => _PS_DEBUG_PROFILING_,

            '_PS_MAGIC_QUOTES_GPC_' => _PS_MAGIC_QUOTES_GPC_,
            '_PS_USE_SQL_SLAVE_' => _PS_USE_SQL_SLAVE_,

            '_PS_CACHE_ENABLED_' => _PS_CACHE_ENABLED_,
            '_PS_CACHING_SYSTEM_' => _PS_CACHING_SYSTEM_,

            '_PS_DEFAULT_THEME_NAME_' => _PS_DEFAULT_THEME_NAME_,
            '_PS_THEME_DIR_' => _PS_THEME_DIR_,
            //'_PS_THEME_OVERRIDE_DIR_' => _PS_THEME_OVERRIDE_DIR_,
            //'_PS_THEME_MOBILE_DIR_' => _PS_THEME_MOBILE_DIR_,
            //'_PS_THEME_MOBILE_OVERRIDE_DIR_' => _PS_THEME_MOBILE_OVERRIDE_DIR_,
            //'_PS_THEME_TOUCHPAD_DIR_' => _PS_THEME_TOUCHPAD_DIR_

            '_PS_CACHE_CA_CERT_FILE_' => _PS_CACHE_CA_CERT_FILE_,
        );

        return array(
            'context' => $arrPSContext,
            'configuration' => $arrPSConfig,
            'constants' => $arrPSConstant,
        );
    }
}
