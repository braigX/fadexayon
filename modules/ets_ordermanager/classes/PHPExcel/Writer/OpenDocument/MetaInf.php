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

class PHPExcel_Writer_OpenDocument_MetaInf extends PHPExcel_Writer_OpenDocument_WriterPart
{
    /**
     * Write META-INF/manifest.xml to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeManifest(PHPExcel $pPHPExcel = null)
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

        // Manifest
        $objWriter->startElement('manifest:manifest');
            $objWriter->writeAttribute('xmlns:manifest', 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0');
            $objWriter->writeAttribute('manifest:version', '1.2');

            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', '/');
                $objWriter->writeAttribute('manifest:version', '1.2');
                $objWriter->writeAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.spreadsheet');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'meta.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'settings.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'content.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'Thumbnails/thumbnail.png');
                $objWriter->writeAttribute('manifest:media-type', 'image/png');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'styles.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }
}
