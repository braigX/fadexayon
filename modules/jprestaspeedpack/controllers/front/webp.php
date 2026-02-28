<?php
/**
 * Page Cache Ultimate, Page Cache standard and Speed pack are powered by Jpresta (jpresta . com)
 *
 * @author    Jpresta
 * @copyright Jpresta
 * @license   See the license of this module in file LICENSE.txt, thank you.
 */

// Don't use autoload-deps.php because _PS_VERSION_ is not defined
include_once dirname(__FILE__) . '/../../vendor/autoload.php';

use JPresta\SpeedPack\JprestaWebpModule;
use WebPConvert\Convert\Converters\Ewww;
use WebPConvert\WebPConvert;

if (!defined('_PS_VERSION_')) {
    require dirname(__FILE__) . '/../../../../config/defines.inc.php';
    if (file_exists(dirname(__FILE__) . '/../../webp.config.php')) {
        include_once dirname(__FILE__) . '/../../webp.config.php';
    }

    /**
     * Class Tools: subset of native Tools class
     */
    class Tools
    {
        public static function getValue($key)
        {
            $params = $_GET;

            return $params[$key];
        }

        public static function getIsset($key)
        {
            return array_key_exists($key, $_GET);
        }
    }

    // Skip Prestashop engine to consummes less resources and be faster
    $img_src = Tools::getValue('src');
    $report = Tools::getIsset('report');
    $reconvert = Tools::getIsset('quality');
    $reportOnFail = Tools::getIsset('quality');
    $returnOriginal = Tools::getIsset('original');

    $source = realpath(_PS_ROOT_DIR_ . '/' . $img_src);
    if ($source === false) {
        // Sometimes people (#4888) display WEBP files as original files, so we don't want to fail here. Normally the
        // htaccess should not come here but it happens... so...
        $possibleExtensions = ['png', 'JPG', 'PNG', 'jpeg', 'JPEG', 'webp'];
        foreach ($possibleExtensions as $possibleExtension) {
            $possibleSource = realpath(_PS_ROOT_DIR_ . '/' . str_replace('.jpg', '.' . $possibleExtension, $img_src));
            if ($possibleSource !== false) {
                $source = $possibleSource;
                break;
            }
        }
    }

    $extension = pathinfo($source, PATHINFO_EXTENSION);

    // Make sure it is an image
    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG', 'webp'])) {
        header('HTTP/1.1 404 Not Found');
        exit('The requested file could not be found: /' . htmlspecialchars($img_src));
    }
    $destination = preg_replace('/(.*)\.' . $extension . '$/i', '$1.webp', $source);

    // Make sure we have a value
    $jprestaWebpConverterOptions = $jprestaWebpConverterOptions ? $jprestaWebpConverterOptions : [];

    if (Tools::getIsset('quality')) {
        $quality = min(100, max(0, (int) Tools::getValue('quality')));
        $jprestaWebpConverterOptions['default-quality'] = $quality;
        $jprestaWebpConverterOptions['quality'] = $quality;
    }

    WebPConvert::serveConverted($source, $destination, [
        'fail' => ($reportOnFail ? 'report' : 'original'), // If failure, serve the original image (source). Other options include 'throw', '404' and 'report'
        'show-report' => $report, // Generates a report instead of serving an image
        'serve-original' => $returnOriginal, // if true, the original image will be served rather than the converted
        'reconvert' => $reconvert, // if true, existing (cached) image will be discarded
        'convert' => $jprestaWebpConverterOptions,
        'serve-image' => [
            'headers' => [
                'vary-accept' => true,
            ],
        ],
    ]);
} else {
    // For compatibility with previous version
    class jprestaspeedpackWebpModuleFrontController extends ModuleFrontController
    {
        public function initContent()
        {
            $img_src = Tools::getValue('src');
            $report = Tools::getIsset('report');
            $reconvert = Tools::getIsset('quality');
            $reportOnFail = Tools::getIsset('quality');
            $returnOriginal = Tools::getIsset('original');

            $source = realpath(_PS_ROOT_DIR_ . '/' . $img_src);
            $extension = pathinfo($source, PATHINFO_EXTENSION);

            // if baseDir isn't at the front 0==strpos, most likely hacking attempt
            if (strpos($source, _PS_ROOT_DIR_) !== 0 || strpos($source, _PS_ROOT_DIR_) === false || !in_array($extension, ['png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG'])) {
                header('HTTP/1.1 404 Not Found');
                exit('The requested file could not be found');
            }
            $destination = preg_replace('/(.*)\.' . $extension . '$/i', '$1.webp', $source);

            WebPConvert::serveConverted($source, $destination, [
                'fail' => ($reportOnFail ? 'report' : 'original'), // If failure, serve the original image (source). Other options include 'throw', '404' and 'report'
                'show-report' => $report, // Generates a report instead of serving an image
                'serve-original' => $returnOriginal, // if true, the original image will be served rather than the converted
                'reconvert' => $reconvert, // if true, existing (cached) image will be discarded
                'convert' => JprestaWebpModule::getConverterOptions(Tools::getValue('quality')),
                'serve-image' => [
                    'headers' => [
                        'vary-accept' => true,
                    ],
                ],
            ]);

            if (is_array(Ewww::$nonFunctionalApiKeysDiscoveredDuringConversion) && count(Ewww::$nonFunctionalApiKeysDiscoveredDuringConversion) > 0) {
                JprestaWebpModule::handleError(Ewww::$nonFunctionalApiKeysDiscoveredDuringConversion);
            }
            exit;
        }
    }
}
