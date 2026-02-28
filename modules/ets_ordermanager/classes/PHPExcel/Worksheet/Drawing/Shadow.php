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

class PHPExcel_Worksheet_Drawing_Shadow implements PHPExcel_IComparable
{
    /* Shadow alignment */
    const SHADOW_BOTTOM       = 'b';
    const SHADOW_BOTTOM_LEFT  = 'bl';
    const SHADOW_BOTTOM_RIGHT = 'br';
    const SHADOW_CENTER       = 'ctr';
    const SHADOW_LEFT         = 'l';
    const SHADOW_TOP          = 't';
    const SHADOW_TOP_LEFT     = 'tl';
    const SHADOW_TOP_RIGHT    = 'tr';

    /**
     * Visible
     *
     * @var boolean
     */
    private $visible;

    /**
     * Blur radius
     *
     * Defaults to 6
     *
     * @var int
     */
    private $blurRadius;

    /**
     * Shadow distance
     *
     * Defaults to 2
     *
     * @var int
     */
    private $distance;

    /**
     * Shadow direction (in degrees)
     *
     * @var int
     */
    private $direction;

    /**
     * Shadow alignment
     *
     * @var int
     */
    private $alignment;

    /**
     * Color
     *
     * @var PHPExcel_Style_Color
     */
    private $color;

    /**
     * Alpha
     *
     * @var int
     */
    private $alpha;

    /**
     * Create a new PHPExcel_Worksheet_Drawing_Shadow
     */
    public function __construct()
    {
        // Initialise values
        $this->visible     = false;
        $this->blurRadius  = 6;
        $this->distance    = 2;
        $this->direction   = 0;
        $this->alignment   = PHPExcel_Worksheet_Drawing_Shadow::SHADOW_BOTTOM_RIGHT;
        $this->color       = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK);
        $this->alpha       = 50;
    }

    /**
     * Get Visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set Visible
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setVisible($pValue = false)
    {
        $this->visible = $pValue;
        return $this;
    }

    /**
     * Get Blur radius
     *
     * @return int
     */
    public function getBlurRadius()
    {
        return $this->blurRadius;
    }

    /**
     * Set Blur radius
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setBlurRadius($pValue = 6)
    {
        $this->blurRadius = $pValue;
        return $this;
    }

    /**
     * Get Shadow distance
     *
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set Shadow distance
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setDistance($pValue = 2)
    {
        $this->distance = $pValue;
        return $this;
    }

    /**
     * Get Shadow direction (in degrees)
     *
     * @return int
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set Shadow direction (in degrees)
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setDirection($pValue = 0)
    {
        $this->direction = $pValue;
        return $this;
    }

   /**
     * Get Shadow alignment
     *
     * @return int
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Set Shadow alignment
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setAlignment($pValue = 0)
    {
        $this->alignment = $pValue;
        return $this;
    }

   /**
     * Get Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set Color
     *
     * @param     PHPExcel_Style_Color $pValue
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setColor(PHPExcel_Style_Color $pValue = null)
    {
           $this->color = $pValue;
           return $this;
    }

   /**
     * Get Alpha
     *
     * @return int
     */
    public function getAlpha()
    {
        return $this->alpha;
    }

    /**
     * Set Alpha
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Drawing_Shadow
     */
    public function setAlpha($pValue = 0)
    {
        $this->alpha = $pValue;
        return $this;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        return md5(
            ($this->visible ? 't' : 'f') .
            $this->blurRadius .
            $this->distance .
            $this->direction .
            $this->alignment .
            $this->color->getHashCode() .
            $this->alpha .
            __CLASS__
        );
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
