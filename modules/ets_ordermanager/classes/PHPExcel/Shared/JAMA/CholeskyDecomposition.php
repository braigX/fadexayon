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

class CholeskyDecomposition
{
    /**
     *    Decomposition storage
     *    @var array
     *    @access private
     */
    private $L = array();

    /**
     *    Matrix row and column dimension
     *    @var int
     *    @access private
     */
    private $m;

    /**
     *    Symmetric positive definite flag
     *    @var boolean
     *    @access private
     */
    private $isspd = true;

    /**
     *    CholeskyDecomposition
     *
     *    Class constructor - decomposes symmetric positive definite matrix
     *    @param mixed Matrix square symmetric positive definite matrix
     */
    public function __construct($A = null)
    {
        if ($A instanceof Matrix) {
            $this->L = $A->getArray();
            $this->m = $A->getRowDimension();

            for ($i = 0; $i < $this->m; ++$i) {
                for ($j = $i; $j < $this->m; ++$j) {
                    for ($sum = $this->L[$i][$j], $k = $i - 1; $k >= 0; --$k) {
                        $sum -= $this->L[$i][$k] * $this->L[$j][$k];
                    }
                    if ($i == $j) {
                        if ($sum >= 0) {
                            $this->L[$i][$i] = sqrt($sum);
                        } else {
                            $this->isspd = false;
                        }
                    } else {
                        if ($this->L[$i][$i] != 0) {
                            $this->L[$j][$i] = $sum / $this->L[$i][$i];
                        }
                    }
                }

                for ($k = $i+1; $k < $this->m; ++$k) {
                    $this->L[$i][$k] = 0.0;
                }
            }
        } else {
            throw new PHPExcel_Calculation_Exception(JAMAError(ARGUMENT_TYPE_EXCEPTION));
        }
    }    //    function __construct()

    /**
     *    Is the matrix symmetric and positive definite?
     *
     *    @return boolean
     */
    public function isSPD()
    {
        return $this->isspd;
    }    //    function isSPD()

    /**
     *    getL
     *
     *    Return triangular factor.
     *    @return Matrix Lower triangular matrix
     */
    public function getL()
    {
        return new Matrix($this->L);
    }    //    function getL()

    /**
     *    Solve A*X = B
     *
     *    @param $B Row-equal matrix
     *    @return Matrix L * L' * X = B
     */
    public function solve($B = null)
    {
        if ($B instanceof Matrix) {
            if ($B->getRowDimension() == $this->m) {
                if ($this->isspd) {
                    $X  = $B->getArrayCopy();
                    $nx = $B->getColumnDimension();

                    for ($k = 0; $k < $this->m; ++$k) {
                        for ($i = $k + 1; $i < $this->m; ++$i) {
                            for ($j = 0; $j < $nx; ++$j) {
                                $X[$i][$j] -= $X[$k][$j] * $this->L[$i][$k];
                            }
                        }
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$k][$j] /= $this->L[$k][$k];
                        }
                    }

                    for ($k = $this->m - 1; $k >= 0; --$k) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$k][$j] /= $this->L[$k][$k];
                        }
                        for ($i = 0; $i < $k; ++$i) {
                            for ($j = 0; $j < $nx; ++$j) {
                                $X[$i][$j] -= $X[$k][$j] * $this->L[$k][$i];
                            }
                        }
                    }

                    return new Matrix($X, $this->m, $nx);
                } else {
                    throw new PHPExcel_Calculation_Exception(JAMAError(MatrixSPDException));
                }
            } else {
                throw new PHPExcel_Calculation_Exception(JAMAError(MATRIX_DIMENSION_EXCEPTION));
            }
        } else {
            throw new PHPExcel_Calculation_Exception(JAMAError(ARGUMENT_TYPE_EXCEPTION));
        }
    }    //    function solve()
}
