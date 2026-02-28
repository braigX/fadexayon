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

class PHPExcel_Worksheet_RowDimension extends PHPExcel_Worksheet_Dimension
{
    /**
     * Row index
     *
     * @var int
     */
    private $rowIndex;

    /**
     * Row height (in pt)
     *
     * When this is set to a negative value, the row height should be ignored by IWriter
     *
     * @var double
     */
    private $height = -1;

     /**
     * ZeroHeight for Row?
     *
     * @var bool
     */
    private $zeroHeight = false;

    /**
     * Create a new PHPExcel_Worksheet_RowDimension
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($pIndex = 0)
    {
        // Initialise values
        $this->rowIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(null);
    }

    /**
     * Get Row Index
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Set Row Index
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function setRowIndex($pValue)
    {
        $this->rowIndex = $pValue;
        return $this;
    }

    /**
     * Get Row Height
     *
     * @return double
     */
    public function getRowHeight()
    {
        return $this->height;
    }

    /**
     * Set Row Height
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function setRowHeight($pValue = -1)
    {
        $this->height = $pValue;
        return $this;
    }

    /**
     * Get ZeroHeight
     *
     * @return bool
     */
    public function getZeroHeight()
    {
        return $this->zeroHeight;
    }

    /**
     * Set ZeroHeight
     *
     * @param bool $pValue
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function setZeroHeight($pValue = false)
    {
        $this->zeroHeight = $pValue;
        return $this;
    }
}
