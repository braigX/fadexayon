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

class PHPExcel_Shared_Escher_DgContainer_SpgrContainer
{
    /**
     * Parent Shape Group Container
     *
     * @var PHPExcel_Shared_Escher_DgContainer_SpgrContainer
     */
    private $parent;

    /**
     * Shape Container collection
     *
     * @var array
     */
    private $children = array();

    /**
     * Set parent Shape Group Container
     *
     * @param PHPExcel_Shared_Escher_DgContainer_SpgrContainer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the parent Shape Group Container if any
     *
     * @return PHPExcel_Shared_Escher_DgContainer_SpgrContainer|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add a child. This will be either spgrContainer or spContainer
     *
     * @param mixed $child
     */
    public function addChild($child)
    {
        $this->children[] = $child;
        $child->setParent($this);
    }

    /**
     * Get collection of Shape Containers
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Recursively get all spContainers within this spgrContainer
     *
     * @return PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer[]
     */
    public function getAllSpContainers()
    {
        $allSpContainers = array();

        foreach ($this->children as $child) {
            if ($child instanceof PHPExcel_Shared_Escher_DgContainer_SpgrContainer) {
                $allSpContainers = array_merge($allSpContainers, $child->getAllSpContainers());
            } else {
                $allSpContainers[] = $child;
            }
        }

        return $allSpContainers;
    }
}
