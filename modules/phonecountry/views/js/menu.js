$(document).ready(function () {
    $('#content .defaultForm').addClass('col-lg-8'); 
    $('#content form.form-horizontal ').addClass('col-lg-10'); 
    $( "#menu a" ).each(function() {
        if ($(this).hasClass('active')) {
            showForm($(this).attr('id'));
            $(this).addClass('active');
        }
    });
    $('body').on('click','#menu a', function() {
        target = $(this).attr('id');
        showForm(target);
        $(this).addClass('active');
    });
});
function showForm(target) {
    target = target.replace('menu', '');
    $('#menu a').removeClass('active');
    
    $('#content form.defaultForm, #content .form-horizontal').hide();
    $('.ec_form').hide();
    $('#form-'+target.toLowerCase()).show();
    $('#'+target.toLowerCase()+'_form').show();
    /* if (target == 'Recipe') {
        $('#recipe_block_form').show();
    } else {
        $('#recipe_block_form').hide();
    } */
} 