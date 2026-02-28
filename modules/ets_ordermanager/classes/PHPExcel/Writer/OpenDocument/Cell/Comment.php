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

class PHPExcel_Writer_OpenDocument_Cell_Comment
{
    public static function write(PHPExcel_Shared_XMLWriter $objWriter, PHPExcel_Cell $cell)
    {
        $comments = $cell->getWorksheet()->getComments();
        if (!isset($comments[$cell->getCoordinate()])) {
            return;
        }
        $comment = $comments[$cell->getCoordinate()];

        $objWriter->startElement('office:annotation');
            //$objWriter->writeAttribute('draw:style-name', 'gr1');
            //$objWriter->writeAttribute('draw:text-style-name', 'P1');
            $objWriter->writeAttribute('svg:width', $comment->getWidth());
            $objWriter->writeAttribute('svg:height', $comment->getHeight());
            $objWriter->writeAttribute('svg:x', $comment->getMarginLeft());
            $objWriter->writeAttribute('svg:y', $comment->getMarginTop());
            //$objWriter->writeAttribute('draw:caption-point-x', $comment->getMarginLeft());
            //$objWriter->writeAttribute('draw:caption-point-y', $comment->getMarginTop());
                $objWriter->writeElement('dc:creator', $comment->getAuthor());
                // TODO: Not realized in PHPExcel_Comment yet.
                //$objWriter->writeElement('dc:date', $comment->getDate());
                $objWriter->writeElement('text:p', $comment->getText()->getPlainText());
                    //$objWriter->writeAttribute('draw:text-style-name', 'P1');
        $objWriter->endElement();
    }
}
