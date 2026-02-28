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

/**
 * Métodos útiles para enumerar archivos/directorios en directorios y subdirectorios.
 */
class DirectoryToolsSearchLocate
{
    /**
     * Enumera los ficheros del directorio especificado
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFiles($folderPath)
    {
        // Aseguramos que termine siempre en el carácter separador de directorios
        $folderPath = rtrim($folderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        foreach (scandir($folderPath) as $entry) {
            if (is_file($folderPath . $entry)) {
                yield $folderPath . $entry;
            }
        }
    }

    /**
     * Enumera los ficheros del directorio especificado, incluyendo también los ficheros de subdirectorios
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFilesRecursive($folderPath)
    {
        foreach (self::enumerateFiles($folderPath) as $file) {
            yield $file;
        }
        foreach (self::enumerateFolders($folderPath) as $subfolder) {
            foreach (self::enumerateFilesRecursive($subfolder) as $subFile) {
                yield $subFile;
            }
        }
    }

    /**
     * Enumera las subdirectorios del directorio especificado
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFolders($folderPath)
    {
        // Aseguramos que termine siempre en el carácter separador de directorios
        $folderPath = rtrim($folderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        foreach (scandir($folderPath) as $entry) {
            if (is_dir($folderPath . $entry) && $entry != '.' && $entry != '..') {
                yield $folderPath . $entry;
            }
        }
    }

    /**
     * Enumera las subdirectorios del directorio especificado, incluyendo también los ficheros de subdirectorios
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFoldersRecursive($folderPath)
    {
        foreach (self::enumerateFolders($folderPath) as $folder) {
            yield $folder;
            foreach (self::enumerateFoldersRecursive($folder) as $subfolder) {
                yield $subfolder;
            }
        }
    }
}
