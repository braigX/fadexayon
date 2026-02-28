/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
     
$(document).ready(function(){
    // Remove onclick event from the rows
    $('table#table-specific_price tbody tr td.pointer').attr('onclick', '');

    // Optionally, you can also remove the 'pointer' class if it's not needed anymore
    $('table#table-specific_price tbody tr td.pointer').removeClass('pointer');
    
    // Remove cursor pointer style to indicate rows are not clickable
    $('table.table tbody tr').css('cursor', 'default');
});