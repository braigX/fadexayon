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

class PHPExcel_Worksheet_Row
{
    /**
     * PHPExcel_Worksheet
     *
     * @var PHPExcel_Worksheet
     */
    private $parent;

    /**
     * Row index
     *
     * @var int
     */
    private $rowIndex = 0;

    /**
     * Create a new row
     *
     * @param PHPExcel_Worksheet         $parent
     * @param int                        $rowIndex
     */
    public function __construct(PHPExcel_Worksheet $parent = null, $rowIndex = 1)
    {
        // Set parent and row index
        $this->parent   = $parent;
        $this->rowIndex = $rowIndex;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->parent);
    }

    /**
     * Get row index
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Get cell iterator
     *
     * @param    string                $startColumn    The column address at which to start iterating
     * @param    string                $endColumn        Optionally, the column address at which to stop iterating
     * @return PHPExcel_Worksheet_CellIterator
     */
    public function getCellIterator($startColumn = 'A', $endColumn = null)
    {
        return new PHPExcel_Worksheet_RowCellIterator($this->parent, $this->rowIndex, $startColumn, $endColumn);
    }
}
