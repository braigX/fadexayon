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

class PHPExcel_Shared_Escher_DggContainer
{
    /**
     * Maximum shape index of all shapes in all drawings increased by one
     *
     * @var int
     */
    private $spIdMax;

    /**
     * Total number of drawings saved
     *
     * @var int
     */
    private $cDgSaved;

    /**
     * Total number of shapes saved (including group shapes)
     *
     * @var int
     */
    private $cSpSaved;

    /**
     * BLIP Store Container
     *
     * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer
     */
    private $bstoreContainer;

    /**
     * Array of options for the drawing group
     *
     * @var array
     */
    private $OPT = array();

    /**
     * Array of identifier clusters containg information about the maximum shape identifiers
     *
     * @var array
     */
    private $IDCLs = array();

    /**
     * Get maximum shape index of all shapes in all drawings (plus one)
     *
     * @return int
     */
    public function getSpIdMax()
    {
        return $this->spIdMax;
    }

    /**
     * Set maximum shape index of all shapes in all drawings (plus one)
     *
     * @param int
     */
    public function setSpIdMax($value)
    {
        $this->spIdMax = $value;
    }

    /**
     * Get total number of drawings saved
     *
     * @return int
     */
    public function getCDgSaved()
    {
        return $this->cDgSaved;
    }

    /**
     * Set total number of drawings saved
     *
     * @param int
     */
    public function setCDgSaved($value)
    {
        $this->cDgSaved = $value;
    }

    /**
     * Get total number of shapes saved (including group shapes)
     *
     * @return int
     */
    public function getCSpSaved()
    {
        return $this->cSpSaved;
    }

    /**
     * Set total number of shapes saved (including group shapes)
     *
     * @param int
     */
    public function setCSpSaved($value)
    {
        $this->cSpSaved = $value;
    }

    /**
     * Get BLIP Store Container
     *
     * @return PHPExcel_Shared_Escher_DggContainer_BstoreContainer
     */
    public function getBstoreContainer()
    {
        return $this->bstoreContainer;
    }

    /**
     * Set BLIP Store Container
     *
     * @param PHPExcel_Shared_Escher_DggContainer_BstoreContainer $bstoreContainer
     */
    public function setBstoreContainer($bstoreContainer)
    {
        $this->bstoreContainer = $bstoreContainer;
    }

    /**
     * Set an option for the drawing group
     *
     * @param int $property The number specifies the option
     * @param mixed $value
     */
    public function setOPT($property, $value)
    {
        $this->OPT[$property] = $value;
    }

    /**
     * Get an option for the drawing group
     *
     * @param int $property The number specifies the option
     * @return mixed
     */
    public function getOPT($property)
    {
        if (isset($this->OPT[$property])) {
            return $this->OPT[$property];
        }
        return null;
    }

    /**
     * Get identifier clusters
     *
     * @return array
     */
    public function getIDCLs()
    {
        return $this->IDCLs;
    }

    /**
     * Set identifier clusters. array(<drawingId> => <max shape id>, ...)
     *
     * @param array $pValue
     */
    public function setIDCLs($pValue)
    {
        $this->IDCLs = $pValue;
    }
}
