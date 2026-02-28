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

class PHPExcel_Writer_OpenDocument_Meta extends PHPExcel_Writer_OpenDocument_WriterPart
{
    /**
     * Write meta.xml to XML format
     *
     * @param   PHPExcel                   $pPHPExcel
     * @return  string                     XML Output
     * @throws  PHPExcel_Writer_Exception
     */
    public function write(PHPExcel $pPHPExcel = null)
    {
        if (!$pPHPExcel) {
            $pPHPExcel = $this->getParentWriter()->getPHPExcel();
        }

        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Meta
        $objWriter->startElement('office:document-meta');

        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->startElement('office:meta');

        $objWriter->writeElement('meta:initial-creator', $pPHPExcel->getProperties()->getCreator());
        $objWriter->writeElement('dc:creator', $pPHPExcel->getProperties()->getCreator());
        $objWriter->writeElement('meta:creation-date', date(DATE_W3C, $pPHPExcel->getProperties()->getCreated()));
        $objWriter->writeElement('dc:date', date(DATE_W3C, $pPHPExcel->getProperties()->getCreated()));
        $objWriter->writeElement('dc:title', $pPHPExcel->getProperties()->getTitle());
        $objWriter->writeElement('dc:description', $pPHPExcel->getProperties()->getDescription());
        $objWriter->writeElement('dc:subject', $pPHPExcel->getProperties()->getSubject());
        $keywords = explode(' ', $pPHPExcel->getProperties()->getKeywords());
        foreach ($keywords as $keyword) {
            $objWriter->writeElement('meta:keyword', $keyword);
        }

        //<meta:document-statistic meta:table-count="XXX" meta:cell-count="XXX" meta:object-count="XXX"/>
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'Company');
        $objWriter->writeRaw($pPHPExcel->getProperties()->getCompany());
        $objWriter->endElement();
 
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'category');
        $objWriter->writeRaw($pPHPExcel->getProperties()->getCategory());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
