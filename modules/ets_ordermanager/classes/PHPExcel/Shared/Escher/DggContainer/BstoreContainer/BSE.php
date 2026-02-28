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

class PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE
{
    const BLIPTYPE_ERROR    = 0x00;
    const BLIPTYPE_UNKNOWN  = 0x01;
    const BLIPTYPE_EMF      = 0x02;
    const BLIPTYPE_WMF      = 0x03;
    const BLIPTYPE_PICT     = 0x04;
    const BLIPTYPE_JPEG     = 0x05;
    const BLIPTYPE_PNG      = 0x06;
    const BLIPTYPE_DIB      = 0x07;
    const BLIPTYPE_TIFF     = 0x11;
    const BLIPTYPE_CMYKJPEG = 0x12;

    /**
     * The parent BLIP Store Entry Container
     *
     * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer
     */
    private $parent;

    /**
     * The BLIP (Big Large Image or Picture)
     *
     * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
     */
    private $blip;

    /**
     * The BLIP type
     *
     * @var int
     */
    private $blipType;

    /**
     * Set parent BLIP Store Entry Container
     *
     * @param PHPExcel_Shared_Escher_DggContainer_BstoreContainer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the BLIP
     *
     * @return PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
     */
    public function getBlip()
    {
        return $this->blip;
    }

    /**
     * Set the BLIP
     *
     * @param PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip $blip
     */
    public function setBlip($blip)
    {
        $this->blip = $blip;
        $blip->setParent($this);
    }

    /**
     * Get the BLIP type
     *
     * @return int
     */
    public function getBlipType()
    {
        return $this->blipType;
    }

    /**
     * Set the BLIP type
     *
     * @param int
     */
    public function setBlipType($blipType)
    {
        $this->blipType = $blipType;
    }
}
