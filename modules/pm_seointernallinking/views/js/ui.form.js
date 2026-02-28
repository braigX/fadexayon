// JavaScript Document


$.widget("ui.form",{
		 _init:function(){
			 var object = this;
			 var form = this.element;
			 var inputs = form.find("input , select ,textarea");

			  form.find("fieldset").addClass("ui-widget-content");
			  form.find("legend").addClass("ui-widget-header ui-corner-all");
			  form.addClass("ui-widget");

			  jQuery.each(inputs,function(){
				jQuery(this).addClass('ui-state-default ui-corner-all');
				jQuery(this).wrap("<label />");

				if(jQuery(this).is(":reset ,:submit"))
				object.buttons(this);
				else if(jQuery(this).is(":checkbox"))
				object.checkboxes(this);
				else if(jQuery(this).is("input[type='text']")||jQuery(this).is("textarea")||jQuery(this).is("input[type='password']"))
				object.textelements(this);
				else if(jQuery(this).is(":radio"))
				object.radio(this);
				else if(jQuery(this).is("select"))
				object.selector(this);

				if(jQuery(this).hasClass("date"))
				{
					jQuery(this).datepicker();


				}


				});
			  var div = jQuery("<div />",{
							   css:{
							   width:20,height:20,
							   margin:10,textAlign:"center"
							   }

							   }).addClass("ui-state-default drag");
			  var no = Math.ceil(Math.random() * 4);
			  var holder = jQuery("<div />",{
								  id:'droppable',
								  text:"Drop the box with "+no+" here",
								 css:{
									 width:100,height:100,float:'right',fontWeight:'bold'}
								  }).addClass('ui-state-default');
			   jQuery(form).find("fieldset").append(holder);
			  for(var i=1;i<5;i++)
				{

					jQuery(form).find("fieldset").append(div.clone().html(i).attr("id",i));

				}


			   jQuery(".drag").draggable({containment: 'parent'});
			   jQuery("#droppable").droppable({
			accept:'#'+no,
			drop: function(event, ui) {
				jQuery(this).addClass('ui-state-highlight').html("Right One");

				form.find(":submit").removeClass('ui-state-disabled').unbind('click');
				}
		});
			 jQuery(".hover").hover(function(){
						  jQuery(this).addClass("ui-state-hover");
						   },function(){
						  jQuery(this).removeClass("ui-state-hover");
						   });

			 },
		 textelements:function(element){

			jQuery(element).bind({

 			  focusin: function() {
 			   jQuery(this).toggleClass('ui-state-focus');
 				 },
			   focusout: function() {
 			    jQuery(this).toggleClass('ui-state-focus');
 				 }
			  });

			 },
		 buttons:function(element)
		 {
			if(jQuery(element).is(":submit"))
			{
				jQuery(element).addClass("ui-priority-primary ui-corner-all ui-state-disabled hover");
			 jQuery(element).bind("click",function(event)
			   {
				   event.preventDefault();
			   });
			}
			else if(jQuery(element).is(":reset"))
			jQuery(element).addClass("ui-priority-secondary ui-corner-all hover");
			jQuery(element).bind('mousedown mouseup', function() {
 			   jQuery(this).toggleClass('ui-state-active');
 				 }

			  );
		 },

		 checkboxes:function(element){
		  jQuery(element).parent("label").after("<span />");
		  var parent =  jQuery(element).parent("label").next();
			 jQuery(element).addClass("ui-helper-hidden");
				parent.css({width:16,height:16,display:"block"});

				 parent.wrap("<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");

			 parent.parent().addClass('hover');


			 parent.parent("span").click(function(event){
						 jQuery(this).toggleClass("ui-state-active");
						 parent.toggleClass("ui-icon ui-icon-check");
						jQuery(element).click();

						});

			 },
		 radio:function(element){
			   jQuery(element).parent("label").after("<span />");
		  var parent =  jQuery(element).parent("label").next();
			 jQuery(element).addClass("ui-helper-hidden");
			 parent.addClass("ui-icon ui-icon-radio-off");
				 parent.wrap("<span class='ui-state-default ui-corner-all' style='display:inline-block;width:16px;height:16px;margin-right:5px;'/>");

			 parent.parent().addClass('hover');


			 parent.parent("span").click(function(event){
						 jQuery(this).toggleClass("ui-state-active");
						 parent.toggleClass("ui-icon-radio-off ui-icon-bullet");
						jQuery(element).click();

						});
			 },
			 selector:function(element){
				 var parent = jQuery(element).parent();
				 parent.css({"display":"block",width:140,height:21}).addClass("ui-state-default ui-corner-all");
				  jQuery(element).addClass("ui-helper-hidden");
				 parent.append("<span id='labeltext' style='float:left;'></span><span style='float:right;display:inline-block' class='ui-icon ui-icon-triangle-1-s' ></span>");
				 parent.after("<ul class=' ui-helper-reset ui-widget-content ui-helper-hidden' style='position:absolute;z-index:50;width:140px;' ></ul>");
				 jQuery.each(jQuery(element).find("option"),function(){

													  jQuery(parent).next("ul").append("<li class='hover'>"+jQuery(this).html()+"</li>");

													   });
				 jQuery(parent).next("ul").find("li").click(function(){ jQuery("#labeltext").html(jQuery(this).html());
																							jQuery(element).val(jQuery(this).html());

																									 });

				 jQuery(parent).click(function(event){ jQuery(this).next().slideToggle('fast');
												 event.preventDefault();
																		});

				}


		 });
