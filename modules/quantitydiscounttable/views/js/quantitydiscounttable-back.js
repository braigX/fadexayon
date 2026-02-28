/**
* Quantitydiscounttable
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
*  @category  FMM Modules
*  @package   Quantitydiscounttable
*  @author    FME Modules
*  @copyright 2023 FME Modules All right reserved
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

function qdtBackChecked(val) {
    var active_ele = document.getElementsByClassName('qdp-icon');
    for (var i = 0; i < active_ele.length; i++) {
        active_ele[i].style.outline = "none";
    }
    var ele = document.getElementById('qdt-back-checked-' + val);
    ele.style.outline = 'rgb(130, 180, 255) 3px solid';
}