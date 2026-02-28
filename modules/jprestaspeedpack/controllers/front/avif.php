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

if (!defined('_PS_VERSION_')) {
    require dirname(__FILE__) . '/../../../../config/defines.inc.php';
    if (file_exists(dirname(__FILE__) . '/../../avif.config.php')) {
        include_once dirname(__FILE__) . '/../../avif.config.php';
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

    /**
     * Serve the original image with correct headers.
     */
    function serveOriginal($source)
    {
        $mime = mime_content_type($source);
        header("Content-Type: $mime");
        header('Content-Length: ' . filesize($source));
        readfile($source);
        exit;
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
        $possibleExtensions = ['png', 'JPG', 'PNG', 'jpeg', 'JPEG', 'webp', 'avif'];
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
    if (!in_array($extension, ['png', 'jpg', 'jpeg', 'PNG', 'JPG', 'JPEG', 'webp', 'avif'])) {
        header('HTTP/1.1 404 Not Found');
        exit('The requested file could not be found: /' . htmlspecialchars($img_src));
    }
    $destination = preg_replace('/(.*)\.' . $extension . '$/i', '$1.avif', $source);

    if (file_exists($destination . '_error') && !$reportOnFail) {
        // We already tried to convert the image but it failed so we directly serve the original image
        serveOriginal($source);
    }

    // Make sure we have a value
    $jprestaAvifConverterOptions = $jprestaAvifConverterOptions ? $jprestaAvifConverterOptions : [];

    if (Tools::getIsset('quality')) {
        $quality = min(100, max(0, (int) Tools::getValue('quality')));
        $jprestaAvifConverterOptions['quality'] = $quality;
    }

    /**
     * Display the converted image into the AVIF format. If an error occurs then display the original image.
     * Set the correct HTTP header 'Content-type' and 'Content-Length'.
     * Add the HTTP header 'Vary: Accept'.
     *
     * @param string $source File to convert into AVIF
     * @param string $destination Destination file where the AVIF image will be stored
     * @param int $quality Quality of the conversion
     * @param bool $reconvert If destination file exists then replace it with new converted file
     * @param bool $report List many informations to understand what is done. Do not convert the file, only display a report.
     * @param bool $reportOnFailure If an error occurs, display a report instead of the original file
     *
     * @return void
     */
    function serveConverted($source, $destination, $quality, $reconvert, $report, $reportOnFailure)
    {
        header('Vary: Accept');

        if (!file_exists($source)) {
            http_response_code(404);
            echo $reportOnFailure ? 'Error: source file not found.' : '';

            return;
        }

        $alreadyConverted = file_exists($destination);
        $shouldConvert = !$alreadyConverted || $reconvert;

        if ($report) {
            header('Content-type: text/plain');
            echo "=== Conversion Report ===\n";
            echo "Source       : $source\n";
            echo "Destination  : $destination\n";
            echo 'Already exists: ' . ($alreadyConverted ? 'yes' : 'no') . "\n";
            echo 'Reconvert    : ' . ($reconvert ? 'yes' : 'no') . "\n";
            echo "Quality      : $quality\n";
            echo 'PHP version  : ' . PHP_VERSION . "\n";
            echo 'AVIF support : ' . (function_exists('imageavif') ? 'yes' : 'no') . "\n";

            return;
        }

        if ($shouldConvert) {
            $info = getimagesize($source);
            $mime = isset($info['mime']) ? $info['mime'] : null;

            switch ($mime) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($source);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($source);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($source);
                    break;
                default:
                    if ($reportOnFailure) {
                        http_response_code(415);
                        echo "Error: unsupported image type '$mime'.";
                    } else {
                        serveOriginal($source);
                    }

                    return;
            }

            if (!function_exists('imageavif')) {
                if ($reportOnFailure) {
                    http_response_code(500);
                    echo 'Error: imageavif() not available in this PHP installation.';
                } else {
                    serveOriginal($source);
                }

                return;
            }

            $success = imageavif($image, $destination, $quality);
            imagedestroy($image);

            if (!$success || !file_exists($destination) || !filesize($destination)) {
                // Remember that this image cannot be converted
                touch($destination . '_error');
                if ($reportOnFailure) {
                    header('Content-Type: text/plain');
                    http_response_code(500);
                    echo "Error: failed to convert image to AVIF, check the logs of your PHP server for more informations\n";
                    $dirDest = dirname($destination);
                    if (!is_writable($dirDest)) {
                        echo "  * Destination directory is not writable : $dirDest\n";
                    }
                    else {
                        echo "  - Destination directory is writable : $dirDest\n";
                    }
                    if (!file_exists($dirDest)) {
                        echo "  * Destination directory does not exist : $dirDest\n";
                    }
                    else {
                        echo "  - Destination directory exists : $dirDest\n";
                    }
                    if (!file_exists($destination)) {
                        echo "  * Destination file does not exist : $destination\n";
                    }
                    else if (!filesize($destination)) {
                        echo "  * Destination file is empty : $destination\n";
                    }
                    if ($success) {
                        echo "  - imageavif() returned true ($success)\n";
                    }
                    else {
                        echo "  * imageavif() returned false ($success)\n";
                    }
                    // Delete generated file if any
                    @unlink($destination);

                } else {
                    // Delete generated file if any
                    @unlink($destination);

                    serveOriginal($source);
                }

                return;
            }
        }

        // Serve the AVIF image
        header('Content-Type: image/avif');
        header('Content-Length: ' . filesize($destination));
        readfile($destination);
        exit;
    }

    serveConverted($source, $destination, $jprestaAvifConverterOptions['quality'], $reconvert, $report, $reportOnFail);
}
