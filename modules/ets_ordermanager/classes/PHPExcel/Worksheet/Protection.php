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

class PHPExcel_Worksheet_Protection
{
    /**
     * Sheet
     *
     * @var boolean
     */
    private $sheet                    = false;

    /**
     * Objects
     *
     * @var boolean
     */
    private $objects                = false;

    /**
     * Scenarios
     *
     * @var boolean
     */
    private $scenarios                = false;

    /**
     * Format cells
     *
     * @var boolean
     */
    private $formatCells            = false;

    /**
     * Format columns
     *
     * @var boolean
     */
    private $formatColumns            = false;

    /**
     * Format rows
     *
     * @var boolean
     */
    private $formatRows            = false;

    /**
     * Insert columns
     *
     * @var boolean
     */
    private $insertColumns            = false;

    /**
     * Insert rows
     *
     * @var boolean
     */
    private $insertRows            = false;

    /**
     * Insert hyperlinks
     *
     * @var boolean
     */
    private $insertHyperlinks        = false;

    /**
     * Delete columns
     *
     * @var boolean
     */
    private $deleteColumns            = false;

    /**
     * Delete rows
     *
     * @var boolean
     */
    private $deleteRows            = false;

    /**
     * Select locked cells
     *
     * @var boolean
     */
    private $selectLockedCells        = false;

    /**
     * Sort
     *
     * @var boolean
     */
    private $sort                    = false;

    /**
     * AutoFilter
     *
     * @var boolean
     */
    private $autoFilter            = false;

    /**
     * Pivot tables
     *
     * @var boolean
     */
    private $pivotTables            = false;

    /**
     * Select unlocked cells
     *
     * @var boolean
     */
    private $selectUnlockedCells    = false;

    /**
     * Password
     *
     * @var string
     */
    private $password                = '';

    /**
     * Create a new PHPExcel_Worksheet_Protection
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of protection enabled?
     *
     * @return boolean
     */
    public function isProtectionEnabled()
    {
        return $this->sheet ||
            $this->objects ||
            $this->scenarios ||
            $this->formatCells ||
            $this->formatColumns ||
            $this->formatRows ||
            $this->insertColumns ||
            $this->insertRows ||
            $this->insertHyperlinks ||
            $this->deleteColumns ||
            $this->deleteRows ||
            $this->selectLockedCells ||
            $this->sort ||
            $this->autoFilter ||
            $this->pivotTables ||
            $this->selectUnlockedCells;
    }

    /**
     * Get Sheet
     *
     * @return boolean
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * Set Sheet
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setSheet($pValue = false)
    {
        $this->sheet = $pValue;
        return $this;
    }

    /**
     * Get Objects
     *
     * @return boolean
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set Objects
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setObjects($pValue = false)
    {
        $this->objects = $pValue;
        return $this;
    }

    /**
     * Get Scenarios
     *
     * @return boolean
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }

    /**
     * Set Scenarios
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setScenarios($pValue = false)
    {
        $this->scenarios = $pValue;
        return $this;
    }

    /**
     * Get FormatCells
     *
     * @return boolean
     */
    public function getFormatCells()
    {
        return $this->formatCells;
    }

    /**
     * Set FormatCells
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setFormatCells($pValue = false)
    {
        $this->formatCells = $pValue;
        return $this;
    }

    /**
     * Get FormatColumns
     *
     * @return boolean
     */
    public function getFormatColumns()
    {
        return $this->formatColumns;
    }

    /**
     * Set FormatColumns
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setFormatColumns($pValue = false)
    {
        $this->formatColumns = $pValue;
        return $this;
    }

    /**
     * Get FormatRows
     *
     * @return boolean
     */
    public function getFormatRows()
    {
        return $this->formatRows;
    }

    /**
     * Set FormatRows
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setFormatRows($pValue = false)
    {
        $this->formatRows = $pValue;
        return $this;
    }

    /**
     * Get InsertColumns
     *
     * @return boolean
     */
    public function getInsertColumns()
    {
        return $this->insertColumns;
    }

    /**
     * Set InsertColumns
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setInsertColumns($pValue = false)
    {
        $this->insertColumns = $pValue;
        return $this;
    }

    /**
     * Get InsertRows
     *
     * @return boolean
     */
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    /**
     * Set InsertRows
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setInsertRows($pValue = false)
    {
        $this->insertRows = $pValue;
        return $this;
    }

    /**
     * Get InsertHyperlinks
     *
     * @return boolean
     */
    public function getInsertHyperlinks()
    {
        return $this->insertHyperlinks;
    }

    /**
     * Set InsertHyperlinks
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setInsertHyperlinks($pValue = false)
    {
        $this->insertHyperlinks = $pValue;
        return $this;
    }

    /**
     * Get DeleteColumns
     *
     * @return boolean
     */
    public function getDeleteColumns()
    {
        return $this->deleteColumns;
    }

    /**
     * Set DeleteColumns
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setDeleteColumns($pValue = false)
    {
        $this->deleteColumns = $pValue;
        return $this;
    }

    /**
     * Get DeleteRows
     *
     * @return boolean
     */
    public function getDeleteRows()
    {
        return $this->deleteRows;
    }

    /**
     * Set DeleteRows
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setDeleteRows($pValue = false)
    {
        $this->deleteRows = $pValue;
        return $this;
    }

    /**
     * Get SelectLockedCells
     *
     * @return boolean
     */
    public function getSelectLockedCells()
    {
        return $this->selectLockedCells;
    }

    /**
     * Set SelectLockedCells
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setSelectLockedCells($pValue = false)
    {
        $this->selectLockedCells = $pValue;
        return $this;
    }

    /**
     * Get Sort
     *
     * @return boolean
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set Sort
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setSort($pValue = false)
    {
        $this->sort = $pValue;
        return $this;
    }

    /**
     * Get AutoFilter
     *
     * @return boolean
     */
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    /**
     * Set AutoFilter
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setAutoFilter($pValue = false)
    {
        $this->autoFilter = $pValue;
        return $this;
    }

    /**
     * Get PivotTables
     *
     * @return boolean
     */
    public function getPivotTables()
    {
        return $this->pivotTables;
    }

    /**
     * Set PivotTables
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setPivotTables($pValue = false)
    {
        $this->pivotTables = $pValue;
        return $this;
    }

    /**
     * Get SelectUnlockedCells
     *
     * @return boolean
     */
    public function getSelectUnlockedCells()
    {
        return $this->selectUnlockedCells;
    }

    /**
     * Set SelectUnlockedCells
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Protection
     */
    public function setSelectUnlockedCells($pValue = false)
    {
        $this->selectUnlockedCells = $pValue;
        return $this;
    }

    /**
     * Get Password (hashed)
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set Password
     *
     * @param string     $pValue
     * @param boolean     $pAlreadyHashed If the password has already been hashed, set this to true
     * @return PHPExcel_Worksheet_Protection
     */
    public function setPassword($pValue = '', $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
        }
        $this->password = $pValue;
        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
