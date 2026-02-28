/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$( document ).ready(function() {
    $( ".table.ec_seo_redirectimage tr td:nth-child(5)" ).each(function( index ) {
        if ($(this).html().trim()=='1') {
            $(this).html('<i class="icon-check" style="color:#72C279;"></i>');
        } else {
            $(this).html('<i class="icon-remove" style="color:#E08F95;"></i>');
            
        }
    });
    $( "input[name*='ec_seo_redirectimageFilter_default']" ).hide();
        all = 'All';
        yes = 'Yes';
        no = 'No';
        if (iso_user == 'fr') {
            all = 'Tous';
            yes = 'Oui';
            no = 'Non';
        }
        $( "input[name*='ec_seo_redirectimageFilter_default']" ).after('<select id="selectaction"><option value="">'+all+'</option><option value="1">'+yes+'</option> <option value="0">'+no+'</option></select>');
        $( "#selectaction").val($( "input[name*='ec_seo_redirectimageFilter_default']" ).val());
        $( "#selectaction").change(function(e) {
            $( "input[name*='ec_seo_redirectimageFilter_default']" ).val($(this).val());
        });
    
    function toggle()
    {
        if ($('#default_on').prop('checked')) {
            $('#img_redirect').parent().parent().parent().parent().hide(200);
            
        } else{
           $('#img_redirect').parent().parent().parent().parent().show(400);
        }
    }

    $('#default_on').click(function () {
        toggle();
    });
    $('#default_off').click(function () {
        toggle();
    });
    toggle();
});