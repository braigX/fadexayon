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

class PHPExcel_Writer_Excel5_Font
{
    /**
     * Color index
     *
     * @var int
     */
    private $colorIndex;

    /**
     * Font
     *
     * @var PHPExcel_Style_Font
     */
    private $font;

    /**
     * Constructor
     *
     * @param PHPExcel_Style_Font $font
     */
    public function __construct(PHPExcel_Style_Font $font = null)
    {
        $this->colorIndex = 0x7FFF;
        $this->font = $font;
    }

    /**
     * Set the color index
     *
     * @param int $colorIndex
     */
    public function setColorIndex($colorIndex)
    {
        $this->colorIndex = $colorIndex;
    }

    /**
     * Get font record data
     *
     * @return string
     */
    public function writeFont()
    {
        $font_outline = 0;
        $font_shadow = 0;

        $icv = $this->colorIndex; // Index to color palette
        if ($this->font->getSuperScript()) {
            $sss = 1;
        } elseif ($this->font->getSubScript()) {
            $sss = 2;
        } else {
            $sss = 0;
        }
        $bFamily = 0; // Font family
        $bCharSet = PHPExcel_Shared_Font::getCharsetFromFontName($this->font->getName()); // Character set

        $record = 0x31;        // Record identifier
        $reserved = 0x00;    // Reserved
        $grbit = 0x00;        // Font attributes
        if ($this->font->getItalic()) {
            $grbit |= 0x02;
        }
        if ($this->font->getStrikethrough()) {
            $grbit |= 0x08;
        }
        if ($font_outline) {
            $grbit |= 0x10;
        }
        if ($font_shadow) {
            $grbit |= 0x20;
        }

        $data = pack(
            "vvvvvCCCC",
            // Fontsize (in twips)
            $this->font->getSize() * 20,
            $grbit,
            // Colour
            $icv,
            // Font weight
            self::mapBold($this->font->getBold()),
            // Superscript/Subscript
            $sss,
            self::mapUnderline($this->font->getUnderline()),
            $bFamily,
            $bCharSet,
            $reserved
        );
        $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($this->font->getName());

        $length = strlen($data);
        $header = pack("vv", $record, $length);

        return($header . $data);
    }

    /**
     * Map to BIFF5-BIFF8 codes for bold
     *
     * @param boolean $bold
     * @return int
     */
    private static function mapBold($bold)
    {
        if ($bold) {
            return 0x2BC;  //  700 = Bold font weight
        }
        return 0x190;      //  400 = Normal font weight
    }

    /**
     * Map of BIFF2-BIFF8 codes for underline styles
     * @static    array of int
     *
     */
    private static $mapUnderline = array(
        PHPExcel_Style_Font::UNDERLINE_NONE              => 0x00,
        PHPExcel_Style_Font::UNDERLINE_SINGLE            => 0x01,
        PHPExcel_Style_Font::UNDERLINE_DOUBLE            => 0x02,
        PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING  => 0x21,
        PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING  => 0x22,
    );

    /**
     * Map underline
     *
     * @param string
     * @return int
     */
    private static function mapUnderline($underline)
    {
        if (isset(self::$mapUnderline[$underline])) {
            return self::$mapUnderline[$underline];
        }
        return 0x00;
    }
}
