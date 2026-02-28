/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* @author    Innova Deluxe SL
* @copyright 2017 Innova Deluxe SL
* @license   INNOVADELUXE
*/

$(document).ready(function() {
    
    $('.option_img').fancybox();
    
    
    var inchange = false;
        
    $("#next").on("click",function(){
        if(inchange){
            return false;
        }
        inchange = true;
        index = parseInt($('#component_step_pointer').attr('data-index'));
        total = parseInt($('#component_step_pointer').attr('data-total'));
        $("#prev").show();
        if(index == total-1){
            $("#next").hide();
        }
        $('.left3d').addClass('rotate');
        $('.front3d').addClass('rotate');
        $('.right3d').addClass('rotate');
        $('#component_step_pointer').attr('data-index',(index+1));
        $('#component_step_actual').html((index+1));
        setTimeout(function () {
            //change the left to the darkness
            $('#component_step_'+(index-1)).removeClass('left3d');
            $('#component_step_'+(index-1)).removeClass('rotate');
            $('#component_step_'+(index-1)).addClass('other3d');
            
            //change the front to the left
            $('#component_step_'+index).removeClass('front3d');
            $('#component_step_'+index).removeClass('rotate');
            $('#component_step_'+index).addClass('left3d');
            
            //change the right to front
            $('#component_step_'+(index+1)).removeClass('right3d');
            $('#component_step_'+(index+1)).removeClass('rotate');
            $('#component_step_'+(index+1)).addClass('front3d');
            
            //Get the next from the darkness
            if (index < total-1){
                $('#component_step_'+(index+2)).removeClass('other3d');
                $('#component_step_'+(index+2)).addClass('right3d');
            }
            inchange = false;

        }, 2000);
        
     });
     
     $('#prev').on('click', function(){
         if(inchange){
            return false;
        }
        inchange = true;
        index = parseInt($('#component_step_pointer').attr('data-index'));
        total = parseInt($('#component_step_pointer').attr('data-total'));
        $("#next").show();
        if(index == 2){
            $("#prev").hide();
        }
        $('.left3d').addClass('unrotate');
        $('.front3d').addClass('unrotate');
        $('.right3d').addClass('unrotate');
        $('#component_step_pointer').attr('data-index',(index-1));
        $('#component_step_actual').html((index-1));
        setTimeout(function () {
            //change the rigth to the darkness               
            $('#component_step_'+(index+1)).removeClass('right3d');
            $('#component_step_'+(index+1)).removeClass('unrotate');
            $('#component_step_'+(index+1)).addClass('other3d');
            
            //change the front to the rigth
            $('#component_step_'+index).removeClass('front3d');
            $('#component_step_'+index).removeClass('unrotate');
            $('#component_step_'+index).addClass('right3d');
            
            //change the left to front            
            $('#component_step_'+(index-1)).removeClass('left3d');
            $('#component_step_'+(index-1)).removeClass('unrotate');
            $('#component_step_'+(index-1)).addClass('front3d');

            //Get the next from the darkness
            if(index > 2){
                $('#component_step_'+(index-2)).removeClass('other3d');
                $('#component_step_'+(index-2)).addClass('left3d');
            }
            inchange = false;
        }, 2000);
     });
});
