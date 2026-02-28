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

require_once(PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php');
class PHPExcel_Linear_Best_Fit extends PHPExcel_Best_Fit
{
    /**
     * Algorithm type to use for best-fit
     * (Name of this trend class)
     *
     * @var    string
     **/
    protected $bestFitType        = 'linear';

    /**
     * Return the Y-Value for a specified value of X
     *
     * @param     float        $xValue            X-Value
     * @return     float                        Y-Value
     **/
    public function getValueOfYForX($xValue)
    {
        return $this->getIntersect() + $this->getSlope() * $xValue;
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

        return 'Y = ' . $intersect . ' + ' . $slope . ' * X';
    }

    /**
     * Execute the regression and calculate the goodness of fit for a set of X and Y data values
     *
     * @param     float[]    $yValues    The set of Y-values for this regression
     * @param     float[]    $xValues    The set of X-values for this regression
     * @param     boolean    $const
     */
    private function linearRegression($yValues, $xValues, $const)
    {
        $this->leastSquareFit($yValues, $xValues, $const);
    }

    /**
     * Define the regression and calculate the goodness of fit for a set of X and Y data values
     *
     * @param    float[]        $yValues    The set of Y-values for this regression
     * @param    float[]        $xValues    The set of X-values for this regression
     * @param    boolean        $const
     */
    public function __construct($yValues, $xValues = array(), $const = true)
    {
        if (parent::__construct($yValues, $xValues) !== false) {
            $this->linearRegression($yValues, $xValues, $const);
        }
    }
}
