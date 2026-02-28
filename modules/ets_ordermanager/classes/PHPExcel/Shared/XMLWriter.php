<?php
/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 * @author ETS-Soft <etssoft.jsc@gmail.com>
 * @copyright  2007-2023 ETS-Soft
 * @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */

if (!defined('DATE_W3C')) {
    define('DATE_W3C', 'Y-m-d\TH:i:sP');
}

if (!defined('DEBUGMODE_ENABLED')) {
    define('DEBUGMODE_ENABLED', false);
}
class PHPExcel_Shared_XMLWriter extends XMLWriter
{
    /** Temporary storage method */
    const STORAGE_MEMORY    = 1;
    const STORAGE_DISK      = 2;

    /**
     * Temporary filename
     *
     * @var string
     */
    private $tempFileName  = '';

    /**
     * Create a new PHPExcel_Shared_XMLWriter instance
     *
     * @param int      $pTemporaryStorage        Temporary storage location
     * @param string   $pTemporaryStorageFolder  Temporary storage folder
     */
    public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = null)
    {
        // Open temporary storage
        if ($pTemporaryStorage == self::STORAGE_MEMORY) {
            $this->openMemory();
        } else {
            // Create temporary filename
            if ($pTemporaryStorageFolder === null) {
                $pTemporaryStorageFolder = PHPExcel_Shared_File::sys_get_temp_dir();
            }
            $this->tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

            // Open storage
            if ($this->openUri($this->tempFileName) === false) {
                // Fallback to memory...
                $this->openMemory();
            }
        }

        // Set default values
        if (DEBUGMODE_ENABLED) {
            $this->setIndent(true);
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Unlink temporary files
        if ($this->tempFileName != '') {
            @unlink($this->tempFileName);
        }
    }

    /**
     * Get written data
     *
     * @return $data
     */
    public function getData()
    {
        if ($this->tempFileName == '') {
            return $this->outputMemory(true);
        } else {
            $this->flush();
            return Tools::file_get_contents($this->tempFileName);
        }
    }

    /**
     * Fallback method for writeRaw, introduced in PHP 5.2
     *
     * @param string $text
     * @return string
     */
    public function writeRawData($text)
    {
        if (is_array($text)) {
            $text = implode("\n", $text);
        }

        if (method_exists($this, 'writeRaw')) {
            return $this->writeRaw(htmlspecialchars($text));
        }

        return $this->text($text);
    }
}
