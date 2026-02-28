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

class PHPExcel_Writer_PDF implements PHPExcel_Writer_IWriter
{

    /**
     * The wrapper for the requested PDF rendering engine
     *
     * @var PHPExcel_Writer_PDF_Core
     */
    private $renderer = null;

    /**
     *  Instantiate a new renderer of the configured type within this container class
     *
     *  @param  PHPExcel   $phpExcel         PHPExcel object
     *  @throws PHPExcel_Writer_Exception    when PDF library is not configured
     */
    public function __construct(PHPExcel $phpExcel)
    {
        $pdfLibraryName = PHPExcel_Settings::getPdfRendererName();
        if (is_null($pdfLibraryName)) {
            throw new PHPExcel_Writer_Exception("PDF Rendering library has not been defined.");
        }

        $pdfLibraryPath = PHPExcel_Settings::getPdfRendererPath();
        if (is_null($pdfLibraryName)) {
            throw new PHPExcel_Writer_Exception("PDF Rendering library path has not been defined.");
        }
        $includePath = str_replace('\\', '/', get_include_path());
        $rendererPath = str_replace('\\', '/', $pdfLibraryPath);
        if (strpos($rendererPath, $includePath) === false) {
            set_include_path(get_include_path() . PATH_SEPARATOR . $pdfLibraryPath);
        }

        $rendererName = 'PHPExcel_Writer_PDF_' . $pdfLibraryName;
        $this->renderer = new $rendererName($phpExcel);
    }


    /**
     *  Magic method to handle direct calls to the configured PDF renderer wrapper class.
     *
     *  @param   string   $name        Renderer library method name
     *  @param   mixed[]  $arguments   Array of arguments to pass to the renderer method
     *  @return  mixed    Returned data from the PDF renderer wrapper method
     */
    public function __call($name, $arguments)
    {
        if ($this->renderer === null) {
            throw new PHPExcel_Writer_Exception("PDF Rendering library has not been defined.");
        }

        return call_user_func_array(array($this->renderer, $name), $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function save($pFilename = null)
    {
        $this->renderer->save($pFilename);
    }
}
