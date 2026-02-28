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

if (!defined('PCLZIP_TEMPORARY_DIR')) {
    define('PCLZIP_TEMPORARY_DIR', PHPExcel_Shared_File::sys_get_temp_dir() . DIRECTORY_SEPARATOR);
}
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/PCLZip/pclzip.lib.php';
class PHPExcel_Shared_ZipArchive
{

    /**    constants */
    const OVERWRITE = 'OVERWRITE';
    const CREATE    = 'CREATE';


    /**
     * Temporary storage directory
     *
     * @var string
     */
    private $tempDir;

    /**
     * Zip Archive Stream Handle
     *
     * @var string
     */
    private $zip;


    /**
     * Open a new zip archive
     *
     * @param    string    $fileName    Filename for the zip archive
     * @return    boolean
     */
    public function open($fileName)
    {
        $this->tempDir = PHPExcel_Shared_File::sys_get_temp_dir();
        $this->zip = new PclZip($fileName);

        return true;
    }


    /**
     * Close this zip archive
     *
     */
    public function close()
    {
    }


    /**
     * Add a new file to the zip archive from a string of raw data.
     *
     * @param    string    $localname        Directory/Name of the file to add to the zip archive
     * @param    string    $contents        String of data to add to the zip archive
     */
    public function addFromString($localname, $contents)
    {
        $filenameParts = pathinfo($localname);

        $handle = fopen($this->tempDir.'/'.$filenameParts["basename"], "wb");
        fwrite($handle, $contents);
        fclose($handle);

        $res = $this->zip->add($this->tempDir.'/'.$filenameParts["basename"], PCLZIP_OPT_REMOVE_PATH, $this->tempDir, PCLZIP_OPT_ADD_PATH, $filenameParts["dirname"]);
        if ($res == 0) {
            throw new PHPExcel_Writer_Exception("Error zipping files : " . $this->zip->errorInfo(true));
        }

        unlink($this->tempDir.'/'.$filenameParts["basename"]);
    }

    /**
     * Find if given fileName exist in archive (Emulate ZipArchive locateName())
     *
     * @param        string        $fileName        Filename for the file in zip archive
     * @return        boolean
     */
    public function locateName($fileName)
    {
        $fileName = strtolower($fileName);

        $list = $this->zip->listContent();
        $listCount = count($list);
        $index = -1;
        for ($i = 0; $i < $listCount; ++$i) {
            if (strtolower($list[$i]["filename"]) == $fileName ||
                strtolower($list[$i]["stored_filename"]) == $fileName) {
                $index = $i;
                break;
            }
        }
        return ($index > -1) ? $index : false;
    }

    /**
     * Extract file from archive by given fileName (Emulate ZipArchive getFromName())
     *
     * @param        string        $fileName        Filename for the file in zip archive
     * @return        string  $contents        File string contents
     */
    public function getFromName($fileName)
    {
        $index = $this->locateName($fileName);

        if ($index !== false) {
            $extracted = $this->getFromIndex($index);
        } else {
            $fileName = substr($fileName, 1);
            $index = $this->locateName($fileName);
            if ($index === false) {
                return false;
            }
            $extracted = $this->zip->getFromIndex($index);
        }

        $contents = $extracted;
        if ((is_array($extracted)) && ($extracted != 0)) {
            $contents = $extracted[0]["content"];
        }

        return $contents;
    }
    
    public function getFromIndex($index) {
        $extracted = $this->zip->extractByIndex($index, PCLZIP_OPT_EXTRACT_AS_STRING);
        $contents = '';
        if ((is_array($extracted)) && ($extracted != 0)) {
            $contents = $extracted[0]["content"];
        }

        return $contents;
    }
}
