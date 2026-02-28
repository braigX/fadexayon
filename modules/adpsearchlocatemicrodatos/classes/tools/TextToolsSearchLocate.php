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
require_once dirname(__FILE__) . '/DirectoryToolsSearchLocate.php';

/**
 * Utilidades para trabajar con ficheros de texto
 */
class TextToolsSearchLocate
{
    /**
     * Enumera las líneas de un fichero de texto.
     *
     * @param string $filePath ruta del fichero
     *
     * @return Traversable enumeración de pares clave valor con el número de línea y el contenido de esta
     */
    public static function enumerateLines($filePath)
    {
        $fileHandler = fopen($filePath, 'r');
        if ($fileHandler) {
            $line = 1;
            while (($text = fgets($fileHandler)) !== false) {
                yield $line => $text;
                ++$line;
            }
            fclose($fileHandler);
        }
    }

    /**
     * Enumera las líneas de un fichero que contengan coincidencias para alguna de las expresiones regulares
     *
     * @param string $filePath Ruta del fichero
     * @param array $patterns un array con los patrones de búsqueda como valores de cadena
     *
     * @return Traversable Enumera pares de clave valor con el número de línea y el texto coincidente
     */
    public static function fileGrep($filePath, $patterns)
    {
        foreach (self::enumerateLines($filePath) as $line => $text) {
            if (self::PatternMatch($text, $patterns)) {
                yield $line => $text;
            }
        }
    }

    /**
     * Enumera los ficheros que contienen líneas con alguna expresión regular coincidente
     *
     * @param string $folderPath Ruta del directorio a escanear
     * @param array $patterns Array con los patrones de búsqueda como valores de cadena
     *
     * @return Traversable Enumera pares de clave valor con el nombre del fichero y las líneas coincidentes con algún patrón
     */
    public static function filesGrep($folderPath, $patterns)
    {
        foreach (DirectoryToolsSearchLocate::enumerateFilesRecursive($folderPath) as $filePath) {
            $valor = iterator_to_array(self::fileGrep($filePath, $patterns));
            if (!empty($valor)) {
                yield $filePath => $valor;
            }
        }
    }

    /**
     * Busca en la cadena de entrada $text una coincidencia con la expresión regular dada en pattern.
     *
     * @param string $text cadena de entrada
     * @param array $patterns un array con los patrones de búsqueda, como cadena
     *
     * @return bool retorna verdadero si hay alguna coincidencia para el conjunto de expresiones regulares
     */
    private static function patternMatch($text, $patterns)
    {
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    public static function endsWith($string, $substring)
    {
        $length = Tools::strlen($substring);
        if ($length == 0) {
            return true;
        }

        return Tools::substr($string, -$length) === $substring;
    }

    public static function contains($string, $substring)
    {
        $length = Tools::strlen($substring);
        if ($length == 0) {
            return true;
        }

        return Tools::strpos($string, $substring) !== false;
    }
}
