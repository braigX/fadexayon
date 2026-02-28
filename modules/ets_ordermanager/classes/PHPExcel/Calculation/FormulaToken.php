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

class PHPExcel_Calculation_FormulaToken
{
    /* Token types */
    const TOKEN_TYPE_NOOP            = 'Noop';
    const TOKEN_TYPE_OPERAND         = 'Operand';
    const TOKEN_TYPE_FUNCTION        = 'Function';
    const TOKEN_TYPE_SUBEXPRESSION   = 'Subexpression';
    const TOKEN_TYPE_ARGUMENT        = 'Argument';
    const TOKEN_TYPE_OPERATORPREFIX  = 'OperatorPrefix';
    const TOKEN_TYPE_OPERATORINFIX   = 'OperatorInfix';
    const TOKEN_TYPE_OPERATORPOSTFIX = 'OperatorPostfix';
    const TOKEN_TYPE_WHITESPACE      = 'Whitespace';
    const TOKEN_TYPE_UNKNOWN         = 'Unknown';

    /* Token subtypes */
    const TOKEN_SUBTYPE_NOTHING       = 'Nothing';
    const TOKEN_SUBTYPE_START         = 'Start';
    const TOKEN_SUBTYPE_STOP          = 'Stop';
    const TOKEN_SUBTYPE_TEXT          = 'Text';
    const TOKEN_SUBTYPE_NUMBER        = 'Number';
    const TOKEN_SUBTYPE_LOGICAL       = 'Logical';
    const TOKEN_SUBTYPE_ERROR         = 'Error';
    const TOKEN_SUBTYPE_RANGE         = 'Range';
    const TOKEN_SUBTYPE_MATH          = 'Math';
    const TOKEN_SUBTYPE_CONCATENATION = 'Concatenation';
    const TOKEN_SUBTYPE_INTERSECTION  = 'Intersection';
    const TOKEN_SUBTYPE_UNION         = 'Union';

    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Token Type (represented by TOKEN_TYPE_*)
     *
     * @var string
     */
    private $tokenType;

    /**
     * Token SubType (represented by TOKEN_SUBTYPE_*)
     *
     * @var string
     */
    private $tokenSubType;

    /**
     * Create a new PHPExcel_Calculation_FormulaToken
     *
     * @param string    $pValue
     * @param string    $pTokenType     Token type (represented by TOKEN_TYPE_*)
     * @param string    $pTokenSubType     Token Subtype (represented by TOKEN_SUBTYPE_*)
     */
    public function __construct($pValue, $pTokenType = PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN, $pTokenSubType = PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING)
    {
        // Initialise values
        $this->value       = $pValue;
        $this->tokenType    = $pTokenType;
        $this->tokenSubType = $pTokenSubType;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value
     *
     * @param string    $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get Token Type (represented by TOKEN_TYPE_*)
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Set Token Type
     *
     * @param string    $value
     */
    public function setTokenType($value = PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN)
    {
        $this->tokenType = $value;
    }

    /**
     * Get Token SubType (represented by TOKEN_SUBTYPE_*)
     *
     * @return string
     */
    public function getTokenSubType()
    {
        return $this->tokenSubType;
    }

    /**
     * Set Token SubType
     *
     * @param string    $value
     */
    public function setTokenSubType($value = PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING)
    {
        $this->tokenSubType = $value;
    }
}
