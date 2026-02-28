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

class PHPExcel_Worksheet_PageMargins
{
    /**
     * Left
     *
     * @var double
     */
    private $left        = 0.7;

    /**
     * Right
     *
     * @var double
     */
    private $right        = 0.7;

    /**
     * Top
     *
     * @var double
     */
    private $top        = 0.75;

    /**
     * Bottom
     *
     * @var double
     */
    private $bottom    = 0.75;

    /**
     * Header
     *
     * @var double
     */
    private $header     = 0.3;

    /**
     * Footer
     *
     * @var double
     */
    private $footer     = 0.3;

    /**
     * Create a new PHPExcel_Worksheet_PageMargins
     */
    public function __construct()
    {
    }

    /**
     * Get Left
     *
     * @return double
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set Left
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setLeft($pValue)
    {
        $this->left = $pValue;
        return $this;
    }

    /**
     * Get Right
     *
     * @return double
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set Right
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setRight($pValue)
    {
        $this->right = $pValue;
        return $this;
    }

    /**
     * Get Top
     *
     * @return double
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * Set Top
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setTop($pValue)
    {
        $this->top = $pValue;
        return $this;
    }

    /**
     * Get Bottom
     *
     * @return double
     */
    public function getBottom()
    {
        return $this->bottom;
    }

    /**
     * Set Bottom
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setBottom($pValue)
    {
        $this->bottom = $pValue;
        return $this;
    }

    /**
     * Get Header
     *
     * @return double
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set Header
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setHeader($pValue)
    {
        $this->header = $pValue;
        return $this;
    }

    /**
     * Get Footer
     *
     * @return double
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set Footer
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function setFooter($pValue)
    {
        $this->footer = $pValue;
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
