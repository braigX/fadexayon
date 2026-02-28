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

class PHPExcel_Style_Conditional implements PHPExcel_IComparable
{
    /* Condition types */
    const CONDITION_NONE         = 'none';
    const CONDITION_CELLIS       = 'cellIs';
    const CONDITION_CONTAINSTEXT = 'containsText';
    const CONDITION_EXPRESSION   = 'expression';

    /* Operator types */
    const OPERATOR_NONE               = '';
    const OPERATOR_BEGINSWITH         = 'beginsWith';
    const OPERATOR_ENDSWITH           = 'endsWith';
    const OPERATOR_EQUAL              = 'equal';
    const OPERATOR_GREATERTHAN        = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN           = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL    = 'lessThanOrEqual';
    const OPERATOR_NOTEQUAL           = 'notEqual';
    const OPERATOR_CONTAINSTEXT       = 'containsText';
    const OPERATOR_NOTCONTAINS        = 'notContains';
    const OPERATOR_BETWEEN            = 'between';

    /**
     * Condition type
     *
     * @var int
     */
    private $conditionType;

    /**
     * Operator type
     *
     * @var int
     */
    private $operatorType;

    /**
     * Text
     *
     * @var string
     */
    private $text;

    /**
     * Condition
     *
     * @var string[]
     */
    private $condition = array();

    /**
     * Style
     *
     * @var PHPExcel_Style
     */
    private $style;

    /**
     * Create a new PHPExcel_Style_Conditional
     */
    public function __construct()
    {
        // Initialise values
        $this->conditionType = PHPExcel_Style_Conditional::CONDITION_NONE;
        $this->operatorType  = PHPExcel_Style_Conditional::OPERATOR_NONE;
        $this->text          = null;
        $this->condition     = array();
        $this->style         = new PHPExcel_Style(false, true);
    }

    /**
     * Get Condition type
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }

    /**
     * Set Condition type
     *
     * @param string $pValue    PHPExcel_Style_Conditional condition type
     * @return PHPExcel_Style_Conditional
     */
    public function setConditionType($pValue = PHPExcel_Style_Conditional::CONDITION_NONE)
    {
        $this->conditionType = $pValue;
        return $this;
    }

    /**
     * Get Operator type
     *
     * @return string
     */
    public function getOperatorType()
    {
        return $this->operatorType;
    }

    /**
     * Set Operator type
     *
     * @param string $pValue    PHPExcel_Style_Conditional operator type
     * @return PHPExcel_Style_Conditional
     */
    public function setOperatorType($pValue = PHPExcel_Style_Conditional::OPERATOR_NONE)
    {
        $this->operatorType = $pValue;
        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param string $value
     * @return PHPExcel_Style_Conditional
     */
    public function setText($value = null)
    {
        $this->text = $value;
        return $this;
    }

    /**
     * Get Condition
     *
     * @deprecated Deprecated, use getConditions instead
     * @return string
     */
    public function getCondition()
    {
        if (isset($this->condition[0])) {
            return $this->condition[0];
        }

        return '';
    }

    /**
     * Set Condition
     *
     * @deprecated Deprecated, use setConditions instead
     * @param string $pValue    Condition
     * @return PHPExcel_Style_Conditional
     */
    public function setCondition($pValue = '')
    {
        if (!is_array($pValue)) {
            $pValue = array($pValue);
        }

        return $this->setConditions($pValue);
    }

    /**
     * Get Conditions
     *
     * @return string[]
     */
    public function getConditions()
    {
        return $this->condition;
    }

    /**
     * Set Conditions
     *
     * @param string[] $pValue    Condition
     * @return PHPExcel_Style_Conditional
     */
    public function setConditions($pValue)
    {
        if (!is_array($pValue)) {
            $pValue = array($pValue);
        }
        $this->condition = $pValue;
        return $this;
    }

    /**
     * Add Condition
     *
     * @param string $pValue    Condition
     * @return PHPExcel_Style_Conditional
     */
    public function addCondition($pValue = '')
    {
        $this->condition[] = $pValue;
        return $this;
    }

    /**
     * Get Style
     *
     * @return PHPExcel_Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set Style
     *
     * @param     PHPExcel_Style $pValue
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Style_Conditional
     */
    public function setStyle(PHPExcel_Style $pValue = null)
    {
           $this->style = $pValue;
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
            $this->conditionType .
            $this->operatorType .
            implode(';', $this->condition) .
            $this->style->getHashCode() .
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
