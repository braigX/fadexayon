
jQuery(document).ready(function() {

	//show new item panel
	$('.button-new-item').on('click', function() {
		$('.new-item').find('.item-container').slideToggle();
	});
	// cancel new item
	$('.button-new-item-cancel').on('click',function() {
		$('.new-item').find('.item-container').slideToggle();
	});
	//show item content edit panel
	$('.button-edit').on('click', function(e) {
		e.preventDefault();
		var $item_container = $(this).closest('.item');
		$item_container.find('.item-container').slideToggle();
		$(this).find('.button-edit-edit').toggleClass('hide');
		$(this).find('.button-edit-close').toggleClass('hide');
	});
	//cancel item edit
	$('.button-item-edit-cancel').on('click',function(){
		$(this).closest('form').find('.button-edit .button-edit-edit').toggleClass('hide');
		$(this).closest('form').find('.button-edit .button-edit-close').toggleClass('hide');
		$(this).closest('form').find('.item-container').slideToggle();
	});

    tinySetup({
        editor_selector :"autoload_rte_custom",
        relative_urls : false,
        plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor fullpage",
        extended_valid_elements : "em[class|name|id],html,head",
        setup : function(ed){
            ed.on('init', function(ed){
                tinyMCE.triggerSave();
            });
            ed.on('keydown', function(ed, e){
                tinyMCE.triggerSave();
            });
        }
    });
});

$(function() {
	 $(".list-unstyled" ).sortable({
         cursor: 'move',
         start: function(e, ui){
             $(ui.item).find('textarea').each(function(){
                 tinymce.execCommand('mceRemoveEditor', false, $(this).attr('id'));
             });
         },
         stop: function(e, ui){
             $(ui.item).find('textarea').each(function(){
                 tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
             });
             $(this).sortable('refresh');
         },
         update: function(event, ui){
             $('#items li').not('.no-slides').each(function(index){
                 $(this).find('.sort-order').text(index + 1);
             });
         }
     }).bind('sortupdate', function() {
		var test = $(this).sortable('toArray');
		var h4_title = $(this).prev('h4').html();
		$.ajax({
			type: 'POST',
			url: theme_url + '&configure=roy_banners&ajax',
			headers: { "cache-control": "no-cache" },
			dataType: 'json',
			data: {
				action: 'updateposition',
				item: test,
				title: h4_title,
			},
			success: function(msg)
			{
				if (msg.error)
				{
					showErrorMessage(msg.error);
					return;
				}
				showSuccessMessage(msg.success);
			}
		});
	 });
 });
