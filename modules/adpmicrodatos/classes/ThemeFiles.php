<?php
/**
 * 2007-2023 PrestaShop.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    Ádalop <contact@prestashop.com>
 *  @copyright 2023 Ádalop
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/tools/DirectoryTools.php';
require_once dirname(__FILE__) . '/tools/IteratorTools.php';
require_once dirname(__FILE__) . '/tools/TextTools.php';
require_once dirname(__FILE__) . '/customexceptions/OperationTimeOutException.php';

class ThemeFiles
{
    public static $BACKUP_FILE_EXTENSION = '.adpmicrodatos.backup';
    private static $LOG_FILE_NAME = 'adpmicrodatos/tmp/adpmicrodatos-log.txt';
    private static $LOGFULL_FILE_NAME = 'adpmicrodatos/tmp/adpmicrodatos-log-full.txt';
    private static $TMP_FOLDER = 'adpmicrodatos/tmp/';

    private static $patternMatches = [
        '/itemprop="[^"]*"/',
        '/itemscope(="[^"]*")?/',
        '/itemtype="[^"]*"/',
        '/typeof="[^"]*"/',
        "/<script[^>]*type=['\"]application\/ld\+json['\"][^>]*>/",
    ];

    private static $patternReplaces = [
        '/itemprop="[^"]*"/',
        '/itemscope(="[^"]*")?/',
        '/itemtype="[^"]*"/',
        '/typeof="[^"]*"/',
        "/<script[^>]*type=['\"]application\/ld\+json['\"][^>]*>[^<]*<\/script>/",
    ];

    private static function tryCreateZip($name)
    {
        try {
            $zip = new ZipArchive();
            $zip->open(_PS_MODULE_DIR_ . self::$TMP_FOLDER . date('YmdHis') . $name . '_adpmicrodatos.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFromString('Readme.txt', $name);

            return $zip;
        } catch (Throwable $th) {
            // throw $th;
        }
    }

    /**
     * Procesa los ficheros del tema para limpiar los microdatos.
     *
     * @param string  Ruta de la carpeta base donde recuperar los ficheros
     *
     * @return int Número de ficheros procesados
     */
    public static function processing($folders)
    {
        $result = [];

        $logFileHandler = fopen(_PS_MODULE_DIR_ . self::$LOG_FILE_NAME, 'w');
        $logFullFileHandler = fopen(_PS_MODULE_DIR_ . self::$LOGFULL_FILE_NAME, 'w');

        // Fichero comprimido con los fichero alterados
        $zip = self::tryCreateZip('install');

        $result['filesProcessing'] = self::processingFolders($folders, $zip, $logFileHandler, $logFullFileHandler);

        fclose($logFileHandler);
        fclose($logFullFileHandler);

        if ($zip) {
            $zip->close();
        }

        return $result;
    }

    /**
     * Procesa los ficheros de las carpetas especificadas para limpiar los microdatos.
     *
     * @param string  Array con las rutas de las carpeta a procesar
     *
     * @return int Número de ficheros procesados
     */
    public static function reescanFolders($folders)
    {
        $logFileHandler = fopen(_PS_MODULE_DIR_ . self::$LOG_FILE_NAME, 'a');
        $logFullFileHandler = fopen(_PS_MODULE_DIR_ . self::$LOGFULL_FILE_NAME, 'a');

        // Fichero comprimido con los fichero alterados
        $zip = self::tryCreateZip('reescan');

        $count = self::processingFolders($folders, $zip, $logFileHandler, $logFullFileHandler);

        fclose($logFileHandler);
        fclose($logFullFileHandler);
        if ($zip) {
            $zip->close();
        }

        return $count;
    }

    private static function processingFolders($folders, $zip, $logFileHandler, $logFullFileHandler)
    {
        $count = 0;

        foreach ($folders as $folder) {
            foreach (DirectoryTools::enumerateFilesRecursive($folder) as $filePath) {
                if (self::processingPath($filePath)) {
                    $matches = TextTools::fileGrep($filePath, self::$patternMatches);
                    if (count($matches) > 0) {
                        ++$count;
                        // Backup del fichero original
                        if ($zip) {
                            $zip->addFile($filePath);
                        }
                        copy($filePath, $filePath . self::$BACKUP_FILE_EXTENSION);
                        // reemplazamos los patrones por cadenas vacías
                        $content = Tools::file_get_contents($filePath);
                        foreach (self::$patternReplaces as $patternReplace) {
                            $content = preg_replace($patternReplace, '', $content);
                        }
                        // Actualización del contenido del fichero
                        file_put_contents($filePath, $content);
                        // Log
                        $relativePath = ltrim(Tools::substr($filePath, Tools::strlen(_PS_ROOT_DIR_)), '/');
                        fwrite($logFileHandler, $relativePath . "\r\n");
                        fwrite($logFullFileHandler, $relativePath . "\r\n");
                        $content = TextTools::enumerateLines($filePath);
                        foreach ($matches as $line => $match) {
                            fwrite($logFullFileHandler, 'line ' . $line . "\r\n");
                            fwrite($logFullFileHandler, "\told => " . trim($match) . "\r\n");
                            if ($line < count($content)) {
                                fwrite($logFullFileHandler, "\tnew => " . trim($content[$line]) . "\r\n");
                            } else {
                                fwrite($logFullFileHandler, "\tnew => __OVERFLOW__\r\n");
                            }
                        }
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Determina si procesar o no un fichero.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function processingPath($path)
    {
        return
            // Extensiones de ficheros admitidas
            (TextTools::endsWith($path, '.php') || TextTools::endsWith($path, '.tpl') || TextTools::endsWith($path, '.html') || TextTools::endsWith($path, '.js'))
            // Excluimos los ficheros que estén dentro de carpetas cache
            && !TextTools::contains($path, '/cache');
    }

    /**
     * Restaura los ficheros procesados.
     *
     * @param string Ruta de la carpeta base donde recuperar los ficheros
     *
     * @return void
     */
    public static function recovery($folders)
    {
        $zip = self::tryCreateZip('recovery');

        foreach ($folders as $folder) {
            foreach (DirectoryTools::enumerateFilesRecursive($folder) as $filePath) {
                if (TextTools::endsWith($filePath, self::$BACKUP_FILE_EXTENSION)) {
                    $newName = Tools::substr($filePath, 0, Tools::strlen($filePath) - Tools::strlen(self::$BACKUP_FILE_EXTENSION));
                    if ($zip) {
                        $zip->addFile($newName);
                    }
                    rename($filePath, $newName);
                }
            }
        }

        if ($zip) {
            $zip->close();
        }
    }

    /**
     * Devuelve un listado con los ficheros que han sido procesados.
     *
     * @return array Lista de rutas de archivos que han sido procesados
     */
    public static function getLog()
    {
        $logFileContent = Tools::file_get_contents(_PS_MODULE_DIR_ . self::$LOG_FILE_NAME);
        $logEntries = array_filter(explode("\r\n", $logFileContent));
        $result = [];
        foreach ($logEntries as $filePath) {
            if ('/' != $filePath[0]) {
                $filePath = rtrim(_PS_ROOT_DIR_, '/') . '/' . $filePath;
            }
            $result[] = $filePath;
        }

        return $result;
    }

    /**
     * Devuelve la lista de backups.
     */
    public static function listBackupFiles()
    {
        $folder = _PS_MODULE_DIR_ . self::$TMP_FOLDER;
        foreach (DirectoryTools::enumerateFiles($folder) as $filePath) {
            $filePathInfo = pathinfo($filePath);
            if ('zip' != $filePathInfo['extension']) {
                continue;
            }

            $fileName = $filePathInfo['filename'];
            $date = date_create_from_format('YmdHis', Tools::substr($fileName, 0, 14));
            $type = Tools::substr($fileName, 14, strpos($fileName, '_') - 14);

            switch ($type) {
                case 'recovery':
                    $displayType = 'uninstall';
                    break;
                default:
                    $displayType = $type;
                    break;
            }

            yield [
                'date' => $date->format('Y-m-d H:i:s'),
                'type' => $type,
                'displayType' => $displayType,
                'link' => _MODULE_DIR_ . self::$TMP_FOLDER . $fileName . '.zip',
            ];
        }
    }

    /**
     * Devuelve un listado con los ficheros que han sido procesados.
     *
     * @return string Contenido completo del archivo de registro
     */
    public static function getLogFull()
    {
        return Tools::file_get_contents(_PS_MODULE_DIR_ . self::$LOGFULL_FILE_NAME);
    }

    /**
     * Comprueba si en una carpeta existen ficheros que contengan microdatos.
     *
     * @param string $folder
     *
     * @return bool
     */
    public static function folderContainsMicrodata($folderPath, $endTime = null)
    {
        foreach (DirectoryTools::enumerateFilesRecursive($folderPath) as $filePath) {
            if (null != $endTime && time() > $endTime) {
                throw new OperationTimeOutException();
            }
            if (self::processingPath($filePath) && IteratorTools::Any(TextTools::fileGrep($filePath, self::$patternMatches))) {
                return true;
            }
        }

        return false;
    }
}
