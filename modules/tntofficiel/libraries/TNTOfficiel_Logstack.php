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

class TNTOfficiel_Logstack
{
    /**
     * @var bool Is debugging enabled ?
     * _PS_MODE_DEV_ should be false to catch all error.
     */
    private static $boolEnable = false;
    /**
     * @var array List of allowed client IP. No client IP means all allowed.
     */
    private static $arrRemoteIPAddressAllow = array();
    /**
     * @var array List of excluded request. Use Get and Post matching.
     */
    private static $arrRequestExclude = array(
        array(
            'get' => array(
                'controller' => 'AdminAjaxFaviconBO',
            ),
            'post' => array(
                'ajax' => 'true',
                'action' => 'GetNotifications',
            ),
        ),
    );
    /**
     * @var int Time limit before forcing shutdown. 0 for unlimited.
     */
    private static $intShutdownTimeLimit = 0;

    /**
     * @var int Limit amount of space disk used to 64M (1024 * 1024 * 64). 0 for unlimited.
     */
    private static $intFolderSizeLimit = 67108864;
    /**
     * @var int Limit age of files to 2 years (2 * 365 * 24 * 60 * 60). 0 for unlimited.
     */
    private static $intFileAgeLimit = 63072000;

    /**
     * @var int Backtrace maximum items. 0 to disable backtrace.
     */
    private static $intBackTraceMaxDeepDefault = 0;
    private static $intBackTraceMaxDeepException = 64;
    private static $intBackTraceMaxDeepError = 64;

    /**
     * @var bool
     */
    private static $boolHandlerRegister = false;

    /**
     * @var string
     */
    private $strPath = null;
    /**
     * @var string
     */
    private $strFileLocation = null;

    /**
     * @var array PHP Errors constant name list.
     */
    private static $arrPHPErrorName = array(
        'E_ERROR',
        'E_RECOVERABLE_ERROR',
        'E_WARNING',
        'E_PARSE',
        'E_NOTICE',
        'E_STRICT',
        'E_DEPRECATED', // PHP 5.3+
        'E_CORE_ERROR',
        'E_CORE_WARNING',
        'E_COMPILE_ERROR',
        'E_COMPILE_WARNING',
        'E_USER_ERROR',
        'E_USER_WARNING',
        'E_USER_NOTICE',
        'E_USER_DEPRECATED', // PHP 5.3+
    );
    /**
     * @var array PHP Errors map. Dynamically generated using getPHPErrorType().
     */
    private static $arrPHPErrorMap = null;

    /**
     * @var array JSON Errors constant name list. PHP 5.3.0+.
     */
    private static $arrJSONErrorName = array(
        'JSON_ERROR_NONE',
        'JSON_ERROR_DEPTH',
        'JSON_ERROR_STATE_MISMATCH',
        'JSON_ERROR_CTRL_CHAR',
        'JSON_ERROR_SYNTAX',
        'JSON_ERROR_UTF8', // PHP 5.3.3+
        'JSON_ERROR_RECURSION', // PHP 5.5.0+
        'JSON_ERROR_INF_OR_NAN', // PHP 5.5.0+
        'JSON_ERROR_UNSUPPORTED_TYPE', // PHP 5.5.0+
        'JSON_ERROR_INVALID_PROPERTY_NAME', // PHP 7.0.0+
        'JSON_ERROR_UTF16', // PHP 7.0.0+
    );
    /**
     * @var array JSON Errors map. PHP 5.3.0+.
     */
    private static $arrJSONErrorMap = null;

    /**
     * @var array Error type and message pattern to exclude.
     */
    private static $arrPHPErrorExclude = array(
        'E_WARNING' => array(
            <<<'REGEXP'
/^filemtime\(\): stat failed for/ui
REGEXP
        ,
            <<<'REGEXP'
/^file_exists\(\): open_basedir restriction in effect\./ui
REGEXP
        ,
            <<<'REGEXP'
/^mkdir\(\)\: File exists/ui
REGEXP
        ,
            <<<'REGEXP'
/^file_get_contents\([^\)]++\): failed to open stream: No such file or directory/ui
REGEXP
        ,/*
            <<<'REGEXP'
/^Invalid argument supplied for foreach\(\)/ui
REGEXP
*/
        ),
        'E_USER_DEPRECATED' => array(
            <<<'REGEXP'
/\bdeprecated\b/ui
REGEXP
        ,
            <<<'REGEXP'
/^The "[^"]++" service is private/ui
REGEXP
        ,
        ),
        'E_NOTICE' => array(
            <<<'REGEXP'
/^(ArrayObject::)?serialize\(\): &quot;[\S\s]+?&quot; returned as member variable from __sleep\(\) but does not exist$/ui
REGEXP
        ,
        ),
        'E_DEPRECATED' => array(
            <<<'REGEXP'
/^array_key_exists\(\): Using array_key_exists\(\) on objects is deprecated\. Use isset\(\) or property_exists\(\) instead$/ui
REGEXP
        ,
        ),
    );

    /**
     * @var array
     */
    private static $arrLoadedEntities = array();

    /**
     * Construct.
     */
    final private function __construct()
    {
    }

    /**
     * @param string $strArgDirectory
     * @param string $strArgFileName
     *
     * @return TNTOfficiel_Logstack
     */
    public static function loadLogstack($strArgDirectory = null, $strArgFileName = null)
    {
        $objLogstackCreate = new TNTOfficiel_Logstack();
        $objLogstackCreate->setDirectory($strArgDirectory);
        $objLogstackCreate->setFilename($strArgFileName);

        $strEntityID = $objLogstackCreate->getFileLocation();

        // If already loaded.
        if (array_key_exists($strEntityID, TNTOfficiel_Logstack::$arrLoadedEntities)) {
            unset($objLogstackCreate);

            return TNTOfficiel_Logstack::$arrLoadedEntities[$strEntityID];
        }

        TNTOfficiel_Logstack::$arrLoadedEntities[$strEntityID] = $objLogstackCreate;

        return $objLogstackCreate;
    }

    /**
     * Check if enabled and client IP address allowed to use debug.
     *
     * @return bool
     */
    public static function isReady()
    {
        if (TNTOfficiel_Logstack::$boolEnable !== true) {
            return false;
        }

        $strClientIPAddress = TNTOfficiel_Tools::getClientIPAddress();
        $boolIPAllowed = count(TNTOfficiel_Logstack::$arrRemoteIPAddressAllow) === 0
            || in_array($strClientIPAddress, TNTOfficiel_Logstack::$arrRemoteIPAddressAllow, true) === true;

        $boolRequestAllowed = true;
        foreach (TNTOfficiel_Logstack::$arrRequestExclude as $arrRequestItem) {
            if (count(
                    array_diff_assoc(
                        $arrRequestItem['get'],
                        array_intersect_assoc($arrRequestItem['get'], $_GET)
                    )
                ) === 0
                && count(
                    array_diff_assoc(
                        $arrRequestItem['post'],
                        array_intersect_assoc($arrRequestItem['post'], $_POST)
                    )
                ) === 0
            ) {
                $boolRequestAllowed = false;
            }
        }

        $boolAllowed = $boolIPAllowed && $boolRequestAllowed;

        return $boolAllowed;
    }

    /**
     * Get PHP Error name from value.
     *
     * @param int $intArgType
     *
     * @return string
     */
    public static function getPHPErrorType($intArgType)
    {
        // Generate constant name mapping.
        if (TNTOfficiel_Logstack::$arrPHPErrorMap === null) {
            TNTOfficiel_Logstack::$arrPHPErrorMap = array();
            foreach (TNTOfficiel_Logstack::$arrPHPErrorName as $strPHPErrorTypeName) {
                if (defined($strPHPErrorTypeName)) {
                    // Get constant value.
                    $intPHPErrorType = constant($strPHPErrorTypeName);
                    TNTOfficiel_Logstack::$arrPHPErrorMap[$intPHPErrorType] = $strPHPErrorTypeName;
                }
            }
        }

        $strPHPErrorType = (string)$intArgType;
        if (array_key_exists($intArgType, TNTOfficiel_Logstack::$arrPHPErrorMap)) {
            $strPHPErrorType = TNTOfficiel_Logstack::$arrPHPErrorMap[$intArgType];
        }

        return $strPHPErrorType;
    }

    /**
     * Get JSON Error name from value.
     *
     * @param int $intArgType
     *
     * @return string
     */
    public static function getJSONErrorType($intArgType)
    {
        // Generate constant name mapping.
        if (TNTOfficiel_Logstack::$arrJSONErrorMap === null) {
            TNTOfficiel_Logstack::$arrJSONErrorMap = array();
            foreach (TNTOfficiel_Logstack::$arrJSONErrorName as $strJSONErrorTypeName) {
                if (defined($strJSONErrorTypeName)) {
                    // Get constant value.
                    $intJSONErrorType = constant($strJSONErrorTypeName);
                    TNTOfficiel_Logstack::$arrJSONErrorMap[$intJSONErrorType] = $strJSONErrorTypeName;
                }
            }
        }

        $strJSONErrorType = (string)$intArgType;
        if (array_key_exists($intArgType, TNTOfficiel_Logstack::$arrJSONErrorMap)) {
            $strJSONErrorType = TNTOfficiel_Logstack::$arrJSONErrorMap[$intArgType];
        }

        return $strJSONErrorType;
    }

    /**
     * Remove Error handlers like ControllerCore::myErrorHandler().
     */
    public static function removerErrorHandlers()
    {
        $arrRemoved = array();

        $mxdPrevErrorHandler = true;
        // While previous error handler exist.
        while ($mxdPrevErrorHandler !== null) {
            // Get previous error handler.
            $mxdPrevErrorHandler = set_error_handler(array('TNTOfficiel_Logstack', 'error'));
            // Restore previous error handler.
            restore_error_handler();
            // If previous error handler exist.
            if ($mxdPrevErrorHandler !== null) {
                array_push($arrRemoved, $mxdPrevErrorHandler);
                // Remove previous error handler.
                restore_error_handler();
            }
        }

        return $arrRemoved;
    }

    /**
     * Remove Exception handlers.
     */
    public static function removerExceptionHandlers()
    {
        $arrRemoved = array();

        $mxdPrevExceptionHandler = true;
        // While previous exception handler exist.
        while ($mxdPrevExceptionHandler !== null) {
            // Get previous exception handler.
            $mxdPrevExceptionHandler = set_exception_handler(array('TNTOfficiel_Logstack', 'exception'));
            // Restore previous exception handler.
            restore_exception_handler();
            // If previous exception handler exist.
            if ($mxdPrevExceptionHandler !== null) {
                array_push($arrRemoved, $mxdPrevExceptionHandler);
                // Remove previous exception handler.
                restore_exception_handler();
            }
        }

        return $arrRemoved;
    }

    /**
     * Register capture handlers once.
     */
    public static function registerHandlers()
    {
        //TNTOfficiel_Logstack::removerErrorHandlers();
        //TNTOfficiel_Logstack::removerExceptionHandlers();

        if (TNTOfficiel_Logstack::$boolHandlerRegister !== true) {
            TNTOfficiel_Logstack::$boolHandlerRegister = true;
            set_error_handler(array('TNTOfficiel_Logstack', 'error'));
            set_exception_handler(array('TNTOfficiel_Logstack', 'exception'));
            register_shutdown_function(array('TNTOfficiel_Logstack', 'shutdown'));
            //set_time_limit(TNTOfficiel_Logstack::$intShutdownTimeLimit);
        }
    }

    /**
     * Get the script start time.
     *
     * @return float
     */
    public static function getStartTime()
    {
        return (float)(array_key_exists('REQUEST_TIME_FLOAT', $_SERVER) ?
            $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME']);
    }

    /**
     * Get the root path.
     *
     * @return string
     */
    public static function getRootPath()
    {
        // TNTOfficiel::getDirPS().'log/';
        return TNTOfficiel::getDirModule() . 'log' . DIRECTORY_SEPARATOR;
    }

    /**
     * Get the default directory.
     *
     * @return string
     */
    public static function getDefaultDirectory()
    {
        // Get Optional IP folder.
        $strPathClientIP = '';

        $strClientIPAddress = TNTOfficiel_Tools::getClientIPAddress();
        if ($strClientIPAddress) {
            $strPathClientIP = preg_replace('/[^a-z0-9_]+/ui', '-', $strClientIPAddress) . DIRECTORY_SEPARATOR;
        }

        return $strPathClientIP;
    }

    /**
     * Get the default filename.
     *
     * @return string
     */
    public static function getDefaultFilename()
    {
        $floatScriptTime = TNTOfficiel_Logstack::getStartTime();
        $strTimeStampExport = var_export($floatScriptTime, true);
        $strTimeStampMaxWidth = preg_replace('/^([0-9]+(?:\.[0-9]{1,6}))[0-9]*$/ui', '$1', $strTimeStampExport);
        $strTimeStampNum = preg_replace('/\./ui', '', $strTimeStampMaxWidth);
        $strTimeStamp = sprintf('%-016s', $strTimeStampNum);

        $strShopFullContextID = 'G' . (int)Shop::getContextShopGroupID() . 'S' . (int)Shop::getContextShopID();

        $strFileName = $strTimeStamp . '-' . $strShopFullContextID . '-'
            . preg_replace('/[^a-z0-9_]+/ui', '-', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        return $strFileName;
    }

    /**
     * Set log path directory using root directory.
     *
     * @param string $strArgDirectory null for default.
     *
     * @return bool
     */
    public function setDirectory($strArgDirectory = null)
    {
        $this->strPath = null;

        // Keep default.
        if (!is_string($strArgDirectory)) {
            return false;
        }

        $strRoot = TNTOfficiel_Logstack::getRootPath();

        $strPath = $strRoot . $strArgDirectory;
        // Add final separator.
        if (Tools::substr($strPath, -1) !== DIRECTORY_SEPARATOR) {
            $strPath .= DIRECTORY_SEPARATOR;
        }

        // Create folder.
        TNTOfficiel_Tools::makeDirectory($strPath);

        $strPath = realpath($strPath);
        // No real path existing. Not created ?
        if ($strPath === false) {
            return false;
        }

        // Add final separator.
        if (Tools::substr($strPath, -1) !== DIRECTORY_SEPARATOR) {
            $strPath .= DIRECTORY_SEPARATOR;
        }

        // If not a writable directory.
        if (!is_dir($strPath) || !is_writable($strPath)) {
            return false;
        }

        // Save.
        $this->strPath = $strPath;

        return true;
    }

    /**
     * Get the path.
     *
     * @return string|null
     */
    public function getPath()
    {
        // Default path.
        if ($this->strPath === null) {
            $strPath = TNTOfficiel_Logstack::getDefaultDirectory();
            $this->setDirectory($strPath);
        }

        return $this->strPath;
    }

    /**
     * @param string $strArgFileName null for default.
     *
     * @return bool
     */
    public function setFilename($strArgFileName = null)
    {
        $this->strFileLocation = null;

        $strPath = $this->getPath();

        if ($strPath === null) {
            return false;
        }

        // Keep default.
        if (!is_string($strArgFileName) || $strArgFileName === '') {
            return false;
        }

        // Save.
        $this->strFileLocation = $strPath . $strArgFileName . '.json';

        return true;
    }

    /**
     * Get log filename.
     *
     * @return string|null
     */
    public function getFileLocation()
    {
        // Default filename.
        if ($this->strFileLocation === null) {
            $strFileName = TNTOfficiel_Logstack::getDefaultFilename();
            $this->setFilename($strFileName);
        }

        return $this->strFileLocation;
    }

    /**
     * @param string $strArgPath
     * @param array  $arrArgAllowedExt
     * @param int    $intArgFolderSizeLimit
     * @param int    $intArgFileAgeLimit
     *
     * @return array List of deleted files.
     */
    public static function removeFiles(
        $strArgPath,
        $arrArgAllowedExt = array(),
        $intArgFolderSizeLimit = 0,
        $intArgFileAgeLimit = 0
    ) {
        $arrDeleted = array();

        if (!($intArgFolderSizeLimit > 0)) {
            return $arrDeleted;
        }

        if (!($intArgFileAgeLimit > 0)) {
            $intArgFileAgeLimit = 0;
        }

        // Search.
        $arrFileTime = TNTOfficiel_Tools::searchFiles($strArgPath, false, $arrArgAllowedExt, true);
        // No files found.
        if (!is_array($arrFileTime)) {
            return $arrDeleted;
        }

        arsort($arrFileTime);
        array_shift($arrFileTime);

        $fltTotalSize = 0;
        foreach ($arrFileTime as $strFileLocation => $intTime) {
            $fltTotalSize += (float)filesize($strFileLocation);

            $boolFolderSizeExceeded = $fltTotalSize > $intArgFolderSizeLimit;
            $boolFileTimeExceeded = ($intArgFileAgeLimit > 0
                && (TNTOfficiel_Tools::isExpired($intTime, $intArgFileAgeLimit) === true)
            );
            // If limit exceeded.
            if ($boolFolderSizeExceeded || $boolFileTimeExceeded) {
                try {
                    // Delete file.
                    $arrDeleted[] = $strFileLocation;
                    Tools::deleteFile($strFileLocation);
                } catch (Exception $objException) {
                    TNTOfficiel_Logger::logException($objException);
                }
            }
        }

        return $arrDeleted;
    }

    /**
     * Create log file if it does not exist.
     * Log global info at creation.
     *
     * @param array $arrArgWriteInfo
     * @param int   $intArgPettyLevel WRITE_PRETTY_* constant.
     *
     * @return bool
     */
    public function write($arrArgWriteInfo, $intArgPettyLevel = null)
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
            $intPettyLevel = TNTOfficiel_Tools::JSON_PRETTY_MULTILINE;
        }

        $strFileLocation = $this->getFileLocation();
        if ($strFileLocation === null) {
            return false;
        }

        $strWriteInfo = TNTOfficiel_Tools::encJSON($arrArgWriteInfo, $intPettyLevel);
        if (!is_string($strWriteInfo) || $strWriteInfo === '') {
            return false;
        }

        // If file not already exist.
        if (!file_exists($strFileLocation)) {
            // Opportunity to remove old log files to preserve space.
            TNTOfficiel_Logstack::removeFiles(
                dirname($strFileLocation) . DIRECTORY_SEPARATOR,
                array('log', 'json'),
                TNTOfficiel_Logstack::$intFolderSizeLimit,
                TNTOfficiel_Logstack::$intFileAgeLimit
            );

            // Create file.
            touch($strFileLocation);
            @chmod($strFileLocation, 0660);
        }

        // If file empty.
        if (!(strlen(file_get_contents($strFileLocation)) > 0)) {
            $strWriteInfo = '[' . $strWriteInfo;
        } else {
            $rscPointer = fopen($strFileLocation, 'r+');
            // LOCK_EX : EXclusive, to acquire an exclusive lock (for writing).
            // LOCK_NB : Non Blocking.
            if (flock($rscPointer, LOCK_EX /*| LOCK_NB*/)) {
                $arrStat = fstat($rscPointer);
                fseek($rscPointer, -1, SEEK_END);
                $strChar = fgetc($rscPointer);
                if ($strChar === ']') {
                    ftruncate($rscPointer, $arrStat['size'] - 1);
                }
                // LOCK_UN : UNlock, to release a lock (shared or exclusive).
                flock($rscPointer, LOCK_UN);
            } else {
                // No lock (only for LOCK_NB) or error.
            }

            fclose($rscPointer);

            $strPrettyNewLine = ($intPettyLevel <= TNTOfficiel_Tools::JSON_PRETTY_ONELINE ? "\n" : '');
            $strWriteInfo = ',' . $strPrettyNewLine . $strWriteInfo;
        }

        $strWriteInfo = $strWriteInfo . ']';

        return file_put_contents($strFileLocation, $strWriteInfo, FILE_APPEND | LOCK_EX) > 0;
    }

    /**
     * @param bool $boolArgIncludeType
     * @param bool $boolArgIncludeCookie
     * @param bool $boolArgIncludeConfig
     *
     * @return array
     */
    public static function header(
        $boolArgIncludeType = false,
        $boolArgIncludeCookie = false,
        $boolArgIncludeConfig = false
    ) {
        $objDateTimeNow = new DateTime('@' . (int)TNTOfficiel_Logstack::getStartTime());
        $objDateTimeNow->setTimezone(new DateTimeZone('UTC'));
        $strDate = $objDateTimeNow->format('Y-m-d H:i:s P T (e)');

        $arrHeaderInfo = array();

        if ($boolArgIncludeType === true) {
            $arrHeaderInfo['type'] = 'Header';
        }

        $arrHeaderInfo += array(
            'date' => $strDate,
            'client' => TNTOfficiel_Tools::getClientIPAddress(),
            'referer' => array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : null,
            'host' => array_key_exists('SERVER_NAME', $_SERVER) ? $_SERVER['SERVER_NAME'] : null,
            'uri' => array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : null,
            'post' => $_POST,
            'get' => $_GET,
            //'script' => TNTOfficiel_Tools::getScriptInfo(),
            //'context' => TNTOfficiel_Tools::getContextInfo(),
        );

        if ($boolArgIncludeCookie === true) {
            $arrHeaderInfo['cookie'] = $_COOKIE;
        }

        if ($boolArgIncludeConfig === true) {
            $arrHeaderInfo['config'] = array(
                'php' => TNTOfficiel_Tools::getPHPConfig(),
                'ps' => TNTOfficiel_Tools::getPSConfig(),
            );
        }

        return $arrHeaderInfo;
    }

    /**
     * Append stack info.
     *
     * @param array|null                $arrArg
     * @param int|null                  $intArgBackTraceMaxDeep
     * @param TNTOfficiel_Logstack|bool $mxdArgStream TNTOfficiel_Logstack object to use custom stream.
     *                                                true to use default stream.
     *                                                false to skip stream and only get return data.
     *
     * @return array Stack data.
     */
    public static function append($arrArg = null, $intArgBackTraceMaxDeep = null, $mxdArgStream = false)
    {
        $intBackTraceMaxDeep = TNTOfficiel_Logstack::$intBackTraceMaxDeepDefault;
        if ($intArgBackTraceMaxDeep >= 0) {
            $intBackTraceMaxDeep = $intArgBackTraceMaxDeep;
        }

        if ($arrArg === null) {
            $arrArg = array();
        }

        if (!is_array($arrArg)) {
            $arrArg = array('raw' => $arrArg);
        }

        // Process dump.
        if (array_key_exists('dump', $arrArg) && is_array($arrArg['dump'])) {
            $arrDump = array();
            foreach ($arrArg['dump'] as $mxdDumpKey => $mxdDumpItem) {
                $arrDump[$mxdDumpKey] = TNTOfficiel_Tools::dumpSafe($mxdDumpItem);
            }
            $arrArg['dump'] = $arrDump;

            // If no dump.
            if (count($arrArg['dump']) === 0) {
                // Remove key (no line output).
                unset($arrArg['dump']);
            }
        }

        // If message, file and line exist, then concat.
        if (array_key_exists('message', $arrArg)
            && array_key_exists('file', $arrArg)
            && array_key_exists('line', $arrArg)
        ) {
            $arrArg['message'] .= ' \'' . $arrArg['file'] . '\' on line ' . $arrArg['line'];
            unset($arrArg['file']);
            unset($arrArg['line']);
        }

        // If no backtrace and auto backtrace set.
        if ($intBackTraceMaxDeep > 0
            && (!array_key_exists('trace', $arrArg) || !is_array($arrArg['trace']))
        ) {
            // Get current one.
            $arrArg['trace'] = debug_backtrace();
        }

        // Process backtrace.
        if (array_key_exists('trace', $arrArg) && is_array($arrArg['trace'])) {
            // Final backtrace.
            $arrTraceStack = array();
            // Backtrace Path.
            //$arrArg['path'] = array();

            // Get each trace, deeper first.
            while ($arrTrace = array_shift($arrArg['trace'])) {
                $intDeepIndex = count($arrTraceStack);
                //$intTraceIndex = count($arrArg['trace']);

                // Get stack with maximum items.
                if ($intDeepIndex >= $intBackTraceMaxDeep) {
                    continue;
                }

                // function
                if (array_key_exists('class', $arrTrace) && is_string($arrTrace['class'])) {
                    // Exclude this class.
                    if (in_array($arrTrace['class'], array(__CLASS__), true)) {
                        continue;
                    }
                    $arrTrace['function'] = $arrTrace['class'] . $arrTrace['type'] . $arrTrace['function'];
                }
                // file
                if (array_key_exists('file', $arrTrace) && is_string($arrTrace['file'])) {
                    $arrTrace['file'] = '\'' . $arrTrace['file'] . '\' on line ' . $arrTrace['line'];
                } else {
                    $arrTrace['file'] = '[Internal]';
                }

                // arguments
                if (array_key_exists('args', $arrTrace) && is_array($arrTrace['args'])) {
                    $arrTrace['function'] .= '(' . count($arrTrace['args']) . ')';

                    //if (array_key_exists('dump', $arrArg) && is_array($arrArg['dump'])
                    //    && array_key_exists('path', $arrArg) && is_array($arrArg['path'])) {
                    //    $arrArg['path'][$intTraceIndex] = $arrTrace['function'] . ' in ' . $arrTrace['file'];
                    //}
                }

                if (array_key_exists('args', $arrTrace) && is_array($arrTrace['args'])) {
                    $arrCallerArgs = array();
                    foreach ($arrTrace['args'] as $mxdTraceArgKey => $mxdTraceArgItem) {
                        $arrCallerArgs[$mxdTraceArgKey] = TNTOfficiel_Tools::dumpSafe($mxdTraceArgItem);
                    }
                    $arrTrace['args'] = $arrCallerArgs;

                    // If no arguments.
                    if (count($arrTrace['args']) === 0) {
                        // Remove key (no line output).
                        unset($arrTrace['args']);
                    }
                }

                unset($arrTrace['line']);
                unset($arrTrace['class']);
                unset($arrTrace['type']);
                unset($arrTrace['object']);

                $arrTrace = TNTOfficiel_Tools::arrayOrderKey(
                    $arrTrace,
                    array('file', 'function', 'args')
                );

                // Add trace.
                $arrTraceStack[$intDeepIndex] = $arrTrace;
            }

            //if (array_key_exists('path', $arrArg) && is_array($arrArg['path'])) {
            //    if (count($arrArg['path']) > 0) {
            //        $arrTraceStack = array();
            //        $arrArg['path'] = array_reverse(array_reverse($arrArg['path']));
            //    } else {
            //        unset($arrArg['path']);
            //    }
            //}

            // Save processed backtrace.
            $arrArg['trace'] = $arrTraceStack;

            // Remove backtrace key if empty.
            if (count($arrArg['trace']) === 0) {
                unset($arrArg['trace']);
            }
        }

        // Append time and memory consumption.
        $arrArg += array(
            'time' => microtime(true) - TNTOfficiel_Logstack::getStartTime(),
            'mem' => memory_get_peak_usage() / 1024 / 1024,
        );

        $arrArg = TNTOfficiel_Tools::arrayOrderKey(
            $arrArg,
            array('time', 'mem', 'type', 'message', 'file', 'line', 'raw', 'dump', 'path', 'trace')
        );

        // Specified logger.
        if (is_object($mxdArgStream)
            && get_class($mxdArgStream) === __CLASS__
        ) {
            $mxdArgStream->write($arrArg, TNTOfficiel_Tools::JSON_PRETTY_MULTILINE);
        }

        // Default logger.
        if ($mxdArgStream === false) {
            // Default logger if enabled and client IP allowed.
            if (TNTOfficiel_Logstack::isReady() === true) {
                // Register handlers if not.
                TNTOfficiel_Logstack::registerHandlers();
                // Use default.
                $objLogstackDefault = TNTOfficiel_Logstack::loadLogstack();
                // If file don't already exist.
                if (!file_exists($objLogstackDefault->getFileLocation())) {
                    $objLogstackDefault->write(
                        TNTOfficiel_Logstack::header(true, true, true),
                        TNTOfficiel_Tools::JSON_PRETTY_MULTILINE
                    );
                }
                $objLogstackDefault->write($arrArg, TNTOfficiel_Tools::JSON_PRETTY_MULTILINE);

                // Timeout.
                if (array_key_exists('time', $arrArg)
                    && TNTOfficiel_Logstack::$intShutdownTimeLimit > 0
                    && $arrArg['time'] > TNTOfficiel_Logstack::$intShutdownTimeLimit
                ) {
                    $objException = new Exception(sprintf(
                        'Timeout (%s). Forcing shutdown.',
                        TNTOfficiel_Logstack::$intShutdownTimeLimit
                    ));
                    $objLogstackDefault->write(
                        TNTOfficiel_Logstack::exception($objException, true),
                        TNTOfficiel_Tools::JSON_PRETTY_MULTILINE
                    );
                    exit;
                }
            }
        }

        return $arrArg;
    }

    /**
     * Append log info.
     *
     * @param array|null                $arrArg
     * @param int|null                  $intArgBackTraceMaxDeep
     * @param TNTOfficiel_Logstack|bool $mxdArgStream
     *
     * @return array|null
     */
    public static function log($arrArg = null, $intArgBackTraceMaxDeep = null, $mxdArgStream = false)
    {
        $intBackTraceMaxDeep = TNTOfficiel_Logstack::$intBackTraceMaxDeepDefault;
        if ($intArgBackTraceMaxDeep > 0) {
            $intBackTraceMaxDeep = $intArgBackTraceMaxDeep;
        }

        if ($arrArg === null && $intBackTraceMaxDeep === 0) {
            return $arrArg;
        }

        return TNTOfficiel_Logstack::append($arrArg, $intBackTraceMaxDeep, $mxdArgStream);
    }

    /**
     * Append dump info without backtrace.
     *
     * @param array|null                $arrArg
     * @param TNTOfficiel_Logstack|bool $mxdArgStream
     *
     * @return array
     */
    public static function dump($arrArg = null, $mxdArgStream = false)
    {
        return TNTOfficiel_Logstack::append(array('dump' => $arrArg), 0, $mxdArgStream);
    }

    /**
     * Append an Exception. Capture an Exception.
     *
     * @param Exception                 $objArgException
     * @param TNTOfficiel_Logstack|bool $mxdArgStream
     *
     * @return array
     */
    public static function exception($objArgException, $mxdArgStream = false)
    {
        $arrAppendException = array(
            'type' => get_class($objArgException),
        );

        if ($objArgException->getFile() !== null) {
            $arrAppendException['file'] = $objArgException->getFile();
            $arrAppendException['line'] = $objArgException->getLine();
        }

        $arrAppendException['message'] = array();
        if (get_class($objArgException) === 'SoapFault') {
            $arrAppendException['message'][] = '[' . $objArgException->faultcode . ']';
        }
        $arrAppendException['message'][] = 'Code ' . $objArgException->getCode() . ':';
        $arrAppendException['message'][] = $objArgException->getMessage();
        $arrAppendException['message'] = implode(' ', $arrAppendException['message']);

        $arrAppendException['trace'] = $objArgException->getTrace();

        return TNTOfficiel_Logstack::append(
            $arrAppendException,
            TNTOfficiel_Logstack::$intBackTraceMaxDeepException,
            $mxdArgStream
        );
    }

    /**
     * Append backtrace info.
     *
     * @param int|null                  $intArgBackTraceMaxDeep
     * @param TNTOfficiel_Logstack|bool $mxdArgStream
     *
     * @return array
     */
    public static function backtrace($intArgBackTraceMaxDeep = null, $mxdArgStream = false)
    {
        $intBackTraceMaxDeep = TNTOfficiel_Logstack::$intBackTraceMaxDeepException;
        if ($intArgBackTraceMaxDeep > 0) {
            $intBackTraceMaxDeep = $intArgBackTraceMaxDeep;
        }

        return TNTOfficiel_Logstack::append(array(), $intBackTraceMaxDeep, $mxdArgStream);
    }

    /**
     * Capture an Error.
     *
     * @param int         $intArgType
     * @param string      $strArgMessage
     * @param string|null $strArgFile
     * @param int         $intArgLine
     * @param array       $arrArgContext
     * @param bool        $boolArgIsLast
     *
     * @return bool
     */
    public static function error(
        $intArgType,
        $strArgMessage,
        $strArgFile = null,
        $intArgLine = 0,
        $arrArgContext = array(),
        $boolArgIsLast = false
    ) {
        $arrLogError = array(
            'type' => $boolArgIsLast ? 'LastError' : 'Error',
        );

        if ($strArgFile !== null) {
            $arrLogError['file'] = $strArgFile;
            $arrLogError['line'] = $intArgLine;
        }

        if ($boolArgIsLast) {
            $arrLogError['trace'] = array();
        }

        $strPHPErrorType = TNTOfficiel_Logstack::getPHPErrorType($intArgType);
        $arrLogError['message'] = 'Type ' . $strPHPErrorType . ': ' . $strArgMessage;

        if (array_key_exists($strPHPErrorType, TNTOfficiel_Logstack::$arrPHPErrorExclude)
            && is_array(TNTOfficiel_Logstack::$arrPHPErrorExclude[$strPHPErrorType])
        ) {
            if (count(TNTOfficiel_Logstack::$arrPHPErrorExclude[$strPHPErrorType]) === 0) {
                return false;
            }
            foreach (TNTOfficiel_Logstack::$arrPHPErrorExclude[$strPHPErrorType] as $strPattern) {
                if (preg_match($strPattern, $strArgMessage) === 1) {
                    return false;
                }
            }
        }

        TNTOfficiel_Logstack::append($arrLogError, TNTOfficiel_Logstack::$intBackTraceMaxDeepError);

        // Internal error handler continues (displays/log error, …)
        return false;
    }

    /**
     * Capture at shutdown.
     */
    public static function shutdown()
    {
        TNTOfficiel_Logstack::lastError();
        TNTOfficiel_Logstack::connectionStatus();
        TNTOfficiel_Logstack::outputBufferStatus();
    }

    /**
     * Append last error for capture at shutdown.
     * Useful at script end for E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, …
     */
    public static function lastError()
    {
        $arrPHPLastError = error_get_last();

        if (is_array($arrPHPLastError)) {
            TNTOfficiel_Logstack::error(
                $arrPHPLastError['type'],
                $arrPHPLastError['message'],
                $arrPHPLastError['file'],
                $arrPHPLastError['line'],
                array(),
                true
            );
        }

        // PHP 5.3.0+
        if (function_exists('json_last_error')) {
            $intTypeJSONLastError = json_last_error();
            $strJSONLastErrorType = TNTOfficiel_Logstack::getJSONErrorType($intTypeJSONLastError);
            if ($strJSONLastErrorType !== 'JSON_ERROR_NONE') {
                // PHP 5.5.0+
                $strJSONLastErrorMessage = function_exists('json_last_error_msg') ? json_last_error_msg() : 'N/A';
                TNTOfficiel_Logstack::error(
                    $strJSONLastErrorType,
                    $strJSONLastErrorMessage,
                    null,
                    0,
                    array(),
                    true
                );
            }
        }

        // PHP 5.3.0+
        if (function_exists('date_get_last_errors')) {
            $arrTypeDateLastError = date_get_last_errors();
            if (is_array($arrTypeDateLastError)) {
                if (array_key_exists('errors', $arrTypeDateLastError)
                    && count($arrTypeDateLastError['errors']) > 0
                ) {
                    $strDateLastErrorType = 'DATE_ERROR';
                    $strDateLastErrorMessage = TNTOfficiel_Tools::encJSON($arrTypeDateLastError['errors']);
                    TNTOfficiel_Logstack::error(
                        $strDateLastErrorType,
                        $strDateLastErrorMessage,
                        null,
                        0,
                        array(),
                        true
                    );
                }
                if (array_key_exists('warnings', $arrTypeDateLastError)
                    && count($arrTypeDateLastError['warnings']) > 0
                ) {
                    $strDateLastErrorType = 'DATE_WARNING';
                    $strDateLastErrorMessage = TNTOfficiel_Tools::encJSON($arrTypeDateLastError['warnings']);
                    TNTOfficiel_Logstack::error(
                        $strDateLastErrorType,
                        $strDateLastErrorMessage,
                        null,
                        0,
                        array(),
                        true
                    );
                }
            }
        }
    }

    /**
     * Append connection status for capture at shutdown.
     */
    public static function connectionStatus()
    {
        // is non-normal connection status.
        $intStatus = connection_status();
        // connection_aborted()
        if ($intStatus & 1) {
            TNTOfficiel_Logstack::append(
                array(
                    'type' => 'ConnectionStatus',
                    'message' => 'Connection was aborted by user.',
                )
            );
        }
        if ($intStatus & 2) {
            TNTOfficiel_Logstack::append(
                array(
                    'type' => 'ConnectionStatus',
                    'message' => 'Script exceeded maximum execution time.',
                )
            );
        }
    }

    /**
     * Append output buffer status for capture at shutdown.
     */
    public static function outputBufferStatus()
    {
        $strMsg = 'Output was not sent yet.';

        // is output buffer was sent
        $strOutputBufferFile = null;
        $intOutputBufferLine = null;
        $boolOutputBufferSent = headers_sent($strOutputBufferFile, $intOutputBufferLine);
        if ($boolOutputBufferSent) {
            $strMsg = sprintf('Output was sent in \'%s\' on line %s.', $strOutputBufferFile, $intOutputBufferLine);
        }

        TNTOfficiel_Logstack::append(
            array(
                'type' => 'OutputBufferStatus',
                'message' => $strMsg,
                'headers' => headers_list(),
                'level' => ob_get_level(),
            ),
            0
        );
    }
}
