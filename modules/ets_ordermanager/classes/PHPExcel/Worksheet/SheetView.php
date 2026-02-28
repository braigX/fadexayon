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

class PHPExcel_Worksheet_SheetView
{

    /* Sheet View types */
    const SHEETVIEW_NORMAL             = 'normal';
    const SHEETVIEW_PAGE_LAYOUT        = 'pageLayout';
    const SHEETVIEW_PAGE_BREAK_PREVIEW = 'pageBreakPreview';

    private static $sheetViewTypes = array(
        self::SHEETVIEW_NORMAL,
        self::SHEETVIEW_PAGE_LAYOUT,
        self::SHEETVIEW_PAGE_BREAK_PREVIEW,
    );

    /**
     * ZoomScale
     *
     * Valid values range from 10 to 400.
     *
     * @var int
     */
    private $zoomScale = 100;

    /**
     * ZoomScaleNormal
     *
     * Valid values range from 10 to 400.
     *
     * @var int
     */
    private $zoomScaleNormal = 100;

    /**
     * View
     *
     * Valid values range from 10 to 400.
     *
     * @var string
     */
    private $sheetviewType = self::SHEETVIEW_NORMAL;

    /**
     * Create a new PHPExcel_Worksheet_SheetView
     */
    public function __construct()
    {
    }

    /**
     * Get ZoomScale
     *
     * @return int
     */
    public function getZoomScale()
    {
        return $this->zoomScale;
    }

    /**
     * Set ZoomScale
     *
     * Valid values range from 10 to 400.
     *
     * @param     int     $pValue
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Worksheet_SheetView
     */
    public function setZoomScale($pValue = 100)
    {
        // Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
        // but it is apparently still able to handle any scale >= 1
        if (($pValue >= 1) || is_null($pValue)) {
            $this->zoomScale = $pValue;
        } else {
            throw new PHPExcel_Exception("Scale must be greater than or equal to 1.");
        }
        return $this;
    }

    /**
     * Get ZoomScaleNormal
     *
     * @return int
     */
    public function getZoomScaleNormal()
    {
        return $this->zoomScaleNormal;
    }

    /**
     * Set ZoomScale
     *
     * Valid values range from 10 to 400.
     *
     * @param     int     $pValue
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Worksheet_SheetView
     */
    public function setZoomScaleNormal($pValue = 100)
    {
        if (($pValue >= 1) || is_null($pValue)) {
            $this->zoomScaleNormal = $pValue;
        } else {
            throw new PHPExcel_Exception("Scale must be greater than or equal to 1.");
        }
        return $this;
    }

    /**
     * Get View
     *
     * @return string
     */
    public function getView()
    {
        return $this->sheetviewType;
    }

    /**
     * Set View
     *
     * Valid values are
     *        'normal'            self::SHEETVIEW_NORMAL
     *        'pageLayout'        self::SHEETVIEW_PAGE_LAYOUT
     *        'pageBreakPreview'  self::SHEETVIEW_PAGE_BREAK_PREVIEW
     *
     * @param     string     $pValue
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Worksheet_SheetView
     */
    public function setView($pValue = null)
    {
        // MS Excel 2007 allows setting the view to 'normal', 'pageLayout' or 'pageBreakPreview' via the user interface
        if ($pValue === null) {
            $pValue = self::SHEETVIEW_NORMAL;
        }
        if (in_array($pValue, self::$sheetViewTypes)) {
            $this->sheetviewType = $pValue;
        } else {
            throw new PHPExcel_Exception("Invalid sheetview layout type.");
        }

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
