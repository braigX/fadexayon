<?php
/**
 * TNT OFFICIAL MODULE FOR PRESTASHOP.
 *
 * @author    Inetum <inetum.com>
 * @copyright 2016-2024 Inetum, 2016-2024 TNT
 * @license   https://opensource.org/licenses/MIT MIT License
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

for ($i = 0; $i <= 255; $i++) {
    $tntofficiel_fpdf_charwidths['courier'][chr($i)] = 600;
}
$tntofficiel_fpdf_charwidths['courierB'] = $tntofficiel_fpdf_charwidths['courier'];
$tntofficiel_fpdf_charwidths['courierI'] = $tntofficiel_fpdf_charwidths['courier'];
$tntofficiel_fpdf_charwidths['courierBI'] = $tntofficiel_fpdf_charwidths['courier'];
