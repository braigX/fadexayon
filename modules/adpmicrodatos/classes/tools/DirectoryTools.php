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

/**
 * Métodos útiles para enumerar archivos/directorios en directorios y subdirectorios.
 */
class DirectoryTools
{
    /**
     * Enumera los ficheros del directorio especificado.
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFiles($folderPath)
    {
        $result = [];
        // Aseguramos que termine siempre en el carácter separador de directorios
        $folderPath = rtrim($folderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        foreach (scandir($folderPath) as $entry) {
            if (is_file($folderPath . $entry)) {
                $result[] = $folderPath . $entry;
            }
        }

        return $result;
    }

    /**
     * Enumera los ficheros del directorio especificado, incluyendo también los ficheros de subdirectorios.
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFilesRecursive($folderPath)
    {
        $result = [];
        foreach (DirectoryTools::enumerateFiles($folderPath) as $file) {
            $result[] = $file;
        }
        foreach (DirectoryTools::enumerateFolders($folderPath) as $subfolder) {
            foreach (DirectoryTools::enumerateFilesRecursive($subfolder) as $subFile) {
                $result[] = $subFile;
            }
        }

        return $result;
    }

    /**
     * Enumera las subdirectorios del directorio especificado.
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFolders($folderPath)
    {
        $result = [];
        // Aseguramos que termine siempre en el carácter separador de directorios
        $folderPath = rtrim($folderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        foreach (scandir($folderPath) as $entry) {
            if (is_dir($folderPath . $entry) && '.' != $entry && '..' != $entry) {
                $result[] = $folderPath . $entry;
            }
        }

        return $result;
    }

    /**
     * Enumera las subdirectorios del directorio especificado, incluyendo también los ficheros de subdirectorios.
     *
     * @param string $folderPath Ruta del directorio
     *
     * @return Traversable Lista de rutas de los ficheros
     */
    public static function enumerateFoldersRecursive($folderPath)
    {
        $result = [];
        foreach (DirectoryTools::enumerateFolders($folderPath) as $folder) {
            $result[] = $folder;
            foreach (DirectoryTools::enumerateFoldersRecursive($folder) as $subfolder) {
                $result[] = $subfolder;
            }
        }

        return $result;
    }
}
