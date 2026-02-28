<?php
/**
* 2007-2022 PrestaShop
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    Ádalop <contact@prestashop.com>
* @copyright 2022 Ádalop
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/
require_once dirname(__FILE__) . '/tools/TextToolsSearchLocate.php';
require_once dirname(__FILE__) . '/tools/DirectoryToolsSearchLocate.php';

class ThemeFilesSearchLocate
{
    private static $BACKUP_FOLDER = 'adpsearchlocatemicrodatos/backups';
    public static $BACKUP_FILE_EXTENSION = '.adpsearchlocatemicrodatos.backup';
    public static $COPY_FILE_EXTENSION = '.adpsearchlocatemicrodatos.copy';

    private static $patternMatches = [
        '/property="og:[^"]*"/',
        '/property="fb:[^"]*"/',
        '/property="product:[^"]*"/',
        '/name="twitter:[^"]*"/',
        '/itemprop="[^"]*"/',
        '/itemscope(="[^"]*")?/',
        '/itemtype="[^"]*"/',
        '/typeof="[^"]*"/',
        "/<script[^>]*type=['\"]application\/ld\+json['\"][^>]*>/",
    ];

    private static $patternReplaces = [
        '/property="og:[^"]*"/',
        '/property="fb:[^"]*"/',
        '/property="product:[^"]*"/',
        '/name="twitter:[^"]*"/',
        '/itemprop="[^"]*"/',
        '/itemscope(="[^"]*")?/',
        '/itemtype="[^"]*"/',
        '/typeof="[^"]*"/',
        "/<script[^>]*type=['\"]application\/ld\+json['\"][^>]*>[^<]*<\/script>/",
    ];

    public static function searchLocate($folder)
    {
        $count = 0;

        foreach (DirectoryToolsSearchLocate::enumerateFilesRecursive($folder) as $filePath) {
            if (self::processingPath($filePath)) {
                $fixed = file_exists($filePath . self::$BACKUP_FILE_EXTENSION);

                $matches = iterator_to_array(TextToolsSearchLocate::fileGrep($filePath, self::$patternMatches));
                if ($fixed || !empty($matches)) {
                    ++$count;
                    $details = [];

                    foreach ($matches as $line => $match) {
                        $details[] = [
                            'line' => $line,
                            'currentValue' => trim($match),
                        ];
                    }

                    yield $count => [
                        'fileName' => basename($filePath),
                        'filePath' => $filePath,
                        'microdataCount' => count($details),
                        'details' => $details,
                        'fixed' => $fixed,
                        'hasPermissions' => is_writable($filePath),
                    ];
                }
            }
        }
    }

    /**
     * Procesa los ficheros del tema para limpiar los microdatos
     *
     * @param string  Ruta de la carpeta base donde recuperar los ficheros
     *
     * @return int Número de ficheros procesados
     */
    public static function processing($folders)
    {
        $count = 0;

        foreach ($folders as $folder) {
            foreach (DirectoryToolsSearchLocate::enumerateFilesRecursive($folder) as $filePath) {
                if (self::processingFile($filePath)) {
                    ++$count;
                }
            }
        }

        return $count;
    }

    public static function processingFile($filePath)
    {
        $result = false;

        if (self::processingPath($filePath) && is_writable($filePath)) {
            $matches = iterator_to_array(TextToolsSearchLocate::fileGrep($filePath, self::$patternMatches));
            if (!empty($matches)) {
                $result = true;
                // Backup del fichero original
                copy($filePath, $filePath . self::$BACKUP_FILE_EXTENSION);
                mkdir(_PS_MODULE_DIR_ . self::$BACKUP_FOLDER . $filePath . '/', 0777, true);
                copy($filePath, _PS_MODULE_DIR_ . self::$BACKUP_FOLDER . $filePath . '/' . date('YmdHis'));

                // reemplazamos los patrones por cadenas vacías
                $content = Tools::file_get_contents($filePath);
                foreach (self::$patternReplaces as $patternReplace) {
                    $content = preg_replace($patternReplace, '', $content);
                }

                // Actualización del contenido del fichero
                file_put_contents($filePath, $content);
                file_put_contents($filePath . self::$COPY_FILE_EXTENSION, $content);
            }
        }

        return $result;
    }

    public static function recovery($folders)
    {
        foreach ($folders as $folder) {
            foreach (DirectoryToolsSearchLocate::enumerateFilesRecursive($folder) as $filePath) {
                if (TextToolsSearchLocate::endsWith($filePath, self::$BACKUP_FILE_EXTENSION)) {
                    $newName = Tools::substr($filePath, 0, Tools::strlen($filePath) - Tools::strlen(self::$BACKUP_FILE_EXTENSION));
                    rename($filePath, $newName);
                }
            }
        }
    }

    public static function recoveryFile($filePath)
    {
        if (file_exists($filePath . self::$BACKUP_FILE_EXTENSION)) {
            rename($filePath . self::$BACKUP_FILE_EXTENSION, $filePath);
        }
    }

    /**
     * Determina si procesar o no un fichero
     *
     * @param string $path
     *
     * @return bool
     */
    private static function processingPath($path)
    {
        return
            // Extensiones de ficheros admitidas
            (TextToolsSearchLocate::endsWith($path, '.php') || TextToolsSearchLocate::endsWith($path, '.tpl') || TextToolsSearchLocate::endsWith($path, '.html') || TextToolsSearchLocate::endsWith($path, '.js'))
            // Excluimos los ficheros que estén dentro de carpetas cache
            && !TextToolsSearchLocate::contains($path, '/cache');
    }
}
