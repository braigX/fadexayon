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

class PHPExcel_Shared_ZipStreamWrapper
{
    /**
     * Internal ZipAcrhive
     *
     * @var ZipArchive
     */
    private $archive;

    /**
     * Filename in ZipAcrhive
     *
     * @var string
     */
    private $fileNameInArchive = '';

    /**
     * Position in file
     *
     * @var int
     */
    private $position = 0;

    /**
     * Data
     *
     * @var mixed
     */
    private $data = '';

    /**
     * Register wrapper
     */
    public static function register()
    {
        @stream_wrapper_unregister('zip');
        @stream_wrapper_register('zip', __CLASS__);
    }

    /**
     * Implements support for fopen().
     *
     * @param    string    $path            resource name including scheme, e.g.
     * @param    string    $mode            only "r" is supported
     * @param    int        $options        mask of STREAM_REPORT_ERRORS and STREAM_USE_PATH
     * @param    string  &$openedPath    absolute path of the opened stream (out parameter)
     * @return    bool    true on success
     */
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        // Check for mode
        if ($mode{0} != 'r') {
            throw new PHPExcel_Reader_Exception('Mode ' . $mode . ' is not supported. Only read mode is supported.');
        }

        $pos = strrpos($path, '#');
        $url['host'] = substr($path, 6, $pos - 6); // 6: strlen('zip://')
        $url['fragment'] = substr($path, $pos + 1);

        // Open archive
        $this->archive = new ZipArchive();
        $this->archive->open($url['host']);

        $this->fileNameInArchive = $url['fragment'];
        $this->position = 0;
        $this->data = $this->archive->getFromName($this->fileNameInArchive);

        return true;
    }

    /**
     * Implements support for fstat().
     *
     * @return  boolean
     */
    public function statName()
    {
        return $this->fileNameInArchive;
    }

    /**
     * Implements support for fstat().
     *
     * @return  boolean
     */
    public function url_stat()
    {
        return $this->statName($this->fileNameInArchive);
    }

    /**
     * Implements support for fstat().
     *
     * @return  boolean
     */
    public function stream_stat()
    {
        return $this->archive->statName($this->fileNameInArchive);
    }

    /**
     * Implements support for fread(), fgets() etc.
     *
     * @param   int        $count    maximum number of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        $ret = substr($this->data, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    /**
     * Returns the position of the file pointer, i.e. its offset into the file
     * stream. Implements support for ftell().
     *
     * @return  int
     */
    public function stream_tell()
    {
        return $this->position;
    }

    /**
     * EOF stream
     *
     * @return    bool
     */
    public function stream_eof()
    {
        return $this->position >= strlen($this->data);
    }

    /**
     * Seek stream
     *
     * @param    int        $offset    byte offset
     * @param    int        $whence    SEEK_SET, SEEK_CUR or SEEK_END
     * @return    bool
     */
    public function stream_seek($offset, $whence)
    {
        switch ($whence) {
            case SEEK_SET:
                if ($offset < strlen($this->data) && $offset >= 0) {
                     $this->position = $offset;
                     return true;
                } else {
                     return false;
                }
                break;
            case SEEK_CUR:
                if ($offset >= 0) {
                     $this->position += $offset;
                     return true;
                } else {
                     return false;
                }
                break;
            case SEEK_END:
                if (strlen($this->data) + $offset >= 0) {
                     $this->position = strlen($this->data) + $offset;
                     return true;
                } else {
                     return false;
                }
                break;
            default:
                return false;
        }
    }
}
