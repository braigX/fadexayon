{*
 * Copyright ETS Software Technology Co., Ltd
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
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<script type="text/javascript">
var ctf7_link_ajax='{$ctf7_link_ajax nofilter}';
$(document).ready(function(){
    $.ajax({
        url: ctf7_link_ajax,
        data: 'action=getCountMessageContactForm7',
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            if(parseInt(json.count) >0)
            {
                if($('#subtab-AdminContactFormMessage span').length)
                    $('#subtab-AdminContactFormMessage span').append('<span class="count_messages ">'+json.count+'</span>'); 
                else
                    $('#subtab-AdminContactFormMessage a').append('<span class="count_messages ">'+json.count+'</span>');
            }
            else
            {
                if($('#subtab-AdminContactFormMessage span').length)
                    $('#subtab-AdminContactFormMessage span').append('<span class="count_messages hide">'+json.count+'</span>'); 
                else
                    $('#subtab-AdminContactFormMessage a').append('<span class="count_messages hide">'+json.count+'</span>');
            }
                                                              
        },
    });
});
</script>