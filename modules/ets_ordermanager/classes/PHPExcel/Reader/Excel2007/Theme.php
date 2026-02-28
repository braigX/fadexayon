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

class PHPExcel_Reader_Excel2007_Theme
{
    /**
     * Theme Name
     *
     * @var string
     */
    private $themeName;

    /**
     * Colour Scheme Name
     *
     * @var string
     */
    private $colourSchemeName;

    /**
     * Colour Map indexed by position
     *
     * @var array of string
     */
    private $colourMapValues;


    /**
     * Colour Map
     *
     * @var array of string
     */
    private $colourMap;


    /**
     * Create a new PHPExcel_Theme
     *
     */
    public function __construct($themeName, $colourSchemeName, $colourMap)
    {
        // Initialise values
        $this->themeName        = $themeName;
        $this->colourSchemeName = $colourSchemeName;
        $this->colourMap        = $colourMap;
    }

    /**
     * Get Theme Name
     *
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * Get colour Scheme Name
     *
     * @return string
     */
    public function getColourSchemeName()
    {
        return $this->colourSchemeName;
    }

    /**
     * Get colour Map Value by Position
     *
     * @return string
     */
    public function getColourByIndex($index = 0)
    {
        if (isset($this->colourMap[$index])) {
            return $this->colourMap[$index];
        }
        return null;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != '_parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
