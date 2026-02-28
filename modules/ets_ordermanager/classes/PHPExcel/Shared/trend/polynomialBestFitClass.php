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

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/JAMA/Matrix.php';
class PHPExcel_Polynomial_Best_Fit extends PHPExcel_Best_Fit
{
    /**
     * Algorithm type to use for best-fit
     * (Name of this trend class)
     *
     * @var    string
     **/
    protected $bestFitType = 'polynomial';

    /**
     * Polynomial order
     *
     * @protected
     * @var    int
     **/
    protected $order = 0;


    /**
     * Return the order of this polynomial
     *
     * @return     int
     **/
    public function getOrder()
    {
        return $this->order;
    }


    /**
     * Return the Y-Value for a specified value of X
     *
     * @param     float        $xValue            X-Value
     * @return     float                        Y-Value
     **/
    public function getValueOfYForX($xValue)
    {
        $retVal = $this->getIntersect();
        $slope = $this->getSlope();
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $retVal += $value * pow($xValue, $key + 1);
            }
        }
        return $retVal;
    }


    /**
     * Return the X-Value for a specified value of Y
     *
     * @param     float        $yValue            Y-Value
     * @return     float                        X-Value
     **/
    public function getValueOfXForY($yValue)
    {
        return ($yValue - $this->getIntersect()) / $this->getSlope();
    }


    /**
     * Return the Equation of the best-fit line
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     **/
    public function getEquation($dp = 0)
    {
        $slope = $this->getSlope($dp);
        $intersect = $this->getIntersect($dp);

        $equation = 'Y = ' . $intersect;
        foreach ($slope as $key => $value) {
            if ($value != 0.0) {
                $equation .= ' + ' . $value . ' * X';
                if ($key > 0) {
                    $equation .= '^' . ($key + 1);
                }
            }
        }
        return $equation;
    }


    /**
     * Return the Slope of the line
     *
     * @param     int        $dp        Number of places of decimal precision to display
     * @return     string
     **/
    public function getSlope($dp = 0)
    {
        if ($dp != 0) {
            $coefficients = array();
            foreach ($this->_slope as $coefficient) {
                $coefficients[] = round($coefficient, $dp);
            }
            return $coefficients;
        }
        return $this->_slope;
    }


    public function getCoefficients($dp = 0)
    {
        return array_merge(array($this->getIntersect($dp)), $this->getSlope($dp));
    }


    /**
     * Execute the regression and calculate the goodness of fit for a set of X and Y data values
     *
     * @param    int            $order        Order of Polynomial for this regression
     * @param    float[]        $yValues    The set of Y-values for this regression
     * @param    float[]        $xValues    The set of X-values for this regression
     * @param    boolean        $const
     */
    private function polynomialRegression($order, $yValues, $xValues, $const)
    {
        // calculate sums
        $x_sum = array_sum($xValues);
        $y_sum = array_sum($yValues);
        $xx_sum = $xy_sum = 0;
        for ($i = 0; $i < $this->valueCount; ++$i) {
            $xy_sum += $xValues[$i] * $yValues[$i];
            $xx_sum += $xValues[$i] * $xValues[$i];
            $yy_sum += $yValues[$i] * $yValues[$i];
        }
        /*
         *    This routine uses logic from the PHP port of polyfit version 0.1
         *    written by Michael Bommarito and Paul Meagher
         *
         *    The function fits a polynomial function of order $order through
         *    a series of x-y data points using least squares.
         *
         */
        for ($i = 0; $i < $this->valueCount; ++$i) {
            for ($j = 0; $j <= $order; ++$j) {
                $A[$i][$j] = pow($xValues[$i], $j);
            }
        }
        for ($i=0; $i < $this->valueCount; ++$i) {
            $B[$i] = array($yValues[$i]);
        }
        $matrixA = new Matrix($A);
        $matrixB = new Matrix($B);
        $C = $matrixA->solve($matrixB);

        $coefficients = array();
        for ($i = 0; $i < $C->m; ++$i) {
            $r = $C->get($i, 0);
            if (abs($r) <= pow(10, -9)) {
                $r = 0;
            }
            $coefficients[] = $r;
        }

        $this->intersect = array_shift($coefficients);
        $this->_slope = $coefficients;

        $this->calculateGoodnessOfFit($x_sum, $y_sum, $xx_sum, $yy_sum, $xy_sum);
        foreach ($this->xValues as $xKey => $xValue) {
            $this->yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);
        }
    }


    /**
     * Define the regression and calculate the goodness of fit for a set of X and Y data values
     *
     * @param    int            $order        Order of Polynomial for this regression
     * @param    float[]        $yValues    The set of Y-values for this regression
     * @param    float[]        $xValues    The set of X-values for this regression
     * @param    boolean        $const
     */
    public function __construct($order, $yValues, $xValues = array(), $const = true)
    {
        if (parent::__construct($yValues, $xValues) !== false) {
            if ($order < $this->valueCount) {
                $this->bestFitType .= '_'.$order;
                $this->order = $order;
                $this->polynomialRegression($order, $yValues, $xValues, $const);
                if (($this->getGoodnessOfFit() < 0.0) || ($this->getGoodnessOfFit() > 1.0)) {
                    $this->_error = true;
                }
            } else {
                $this->_error = true;
            }
        }
    }
}
