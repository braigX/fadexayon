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

abstract class PHPExcel_Writer_Excel2007_WriterPart
{
    /**
     * Parent IWriter object
     *
     * @var PHPExcel_Writer_IWriter
     */
    private $parentWriter;

    /**
     * Set parent IWriter object
     *
     * @param PHPExcel_Writer_IWriter    $pWriter
     * @throws PHPExcel_Writer_Exception
     */
    public function setParentWriter(PHPExcel_Writer_IWriter $pWriter = null)
    {
        $this->parentWriter = $pWriter;
    }

    /**
     * Get parent IWriter object
     *
     * @return PHPExcel_Writer_IWriter
     * @throws PHPExcel_Writer_Exception
     */
    public function getParentWriter()
    {
        if (!is_null($this->parentWriter)) {
            return $this->parentWriter;
        } else {
            throw new PHPExcel_Writer_Exception("No parent PHPExcel_Writer_IWriter assigned.");
        }
    }

    /**
     * Set parent IWriter object
     *
     * @param PHPExcel_Writer_IWriter    $pWriter
     * @throws PHPExcel_Writer_Exception
     */
    public function __construct(PHPExcel_Writer_IWriter $pWriter = null)
    {
        if (!is_null($pWriter)) {
            $this->parentWriter = $pWriter;
        }
    }
}
