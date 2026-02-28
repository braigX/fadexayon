function toggleAdvancedPriceSettingsVisibility() {
    if ($("input[name='show_advanced_price_settings']:checked").val() == 1) {
        $(".price-update-condition-rules").show();
    } else {
        $(".price-update-condition-rules").hide();
    }
}

function toggleAdvancedQuantitySettingsVisibility() {
    if ($("input[name='show_advanced_quantity_settings']:checked").val() == 1) {
        $(".quantity-update-condition-rules").show();
    } else {
        $(".quantity-update-condition-rules").hide();
    }
}


$(document).ready(function(){
  replaceUrlFile();

  $(document).on('keyup', 'input[name=name_file]', function(){
    replaceUrlFile();
  });
  
  $(document).on('change', 'input[name=format_file]', function(){
    replaceUrlFile();
  });
  
  $(document).on("click", "input[name='show_advanced_price_settings']", function() {
    toggleAdvancedPriceSettingsVisibility();
  });
  
  $(document).on("click", "input[name='show_advanced_quantity_settings']", function() {
    toggleAdvancedQuantitySettingsVisibility();
  });
  
  $(document).on("click", "#add_quantity_condition", function() {
      const new_condition = $("#quantity_condition_blueprint").clone();
      new_condition.addClass("quantity-condition").removeAttr("id");
      new_condition.appendTo("#quantity_conditions_block");
      new_condition.show();
  });
  
  $(document).on("click", ".remove-quantity-condition", function() {
      $(this).parents(".quantity-condition").remove();
  });
    
    $(document).on("click", "#add_price_condition", function() {
        const new_condition = $("#price_condition_blueprint").clone();
        new_condition.addClass("price-condition").removeAttr("id");
        new_condition.appendTo("#price_conditions_block");
        new_condition.show();
    });
    
    $(document).on("click", ".remove-price-condition", function() {
        $(this).parents(".price-condition").remove();
    });

  $(document).on('change', '.table_product_block .checkbox_product', function(){
    var x = document.getElementById("bulk_actions").children[0];
    x.setAttribute("selected", "selected");
  });

  $(document).on('change', '.pagination_left_block .bulk_actions', function(){
    var type = $(this).val();
    $(this).find('option').removeAttr('selected');

    if(type == 1){
      var checked = true;
    }

    if(type == 2){
      var checked = false;
    }

    $('.table_product_block tbody tr').each(function(){
      $(this).find('.checkbox_product').prop("checked", checked);
    });

  });

  $(document).on('click', '#live .update_live', function(e){
    liveUpdate();
  });
  
  $(document).on('click', '#live #submitResetButtonquantityupdate', function(e){
    e.preventDefault();
    filterQuantityupdate(1);
  });

  $(document).on('click', '#live #submitFilterButtonquantityupdate', function(e){
    e.preventDefault();
    $('input[name="pagination_page"]').val(1);
    filterQuantityupdate(0);
  });

  $(document).on('click', '#live .quantityupdate_pagination li a', function(e){
    e.preventDefault();
    var p = $(this).attr('href');
    $('input[name="pagination_page"]').val(p);
    filterQuantityupdate(0);

  })
  $(document).on('change','#live .display_pagination', function(e){
    e.preventDefault();
    $('input[name="pagination_page"]').val(1);
    var n = $('select[name="display_pagination"]').val();
    $('input[name="pagination_show"]').val(n);
    
    filterQuantityupdate(0);
  })
  $(document).on('change','#live input[name="categoryBox[]"]', function(e){
    e.preventDefault();
    $('input[name="pagination_page"]').val(1);
    filterQuantityupdate(0);
  })

  
  $(document).on('change', 'input[name=name_export_file]', function(){
    if($('input[name=name_export_file]:checked').val() == 1){
      $('.form_group_name_file').addClass('active_block');
      $('.auto_description').addClass('active_block');
    }
    else{
      $('.form_group_name_file').removeClass('active_block');
      $('.auto_description').removeClass('active_block');
    }
  });


  $(document).on('change', 'input[name=active_products]', function(){
    if($('#active_products_on:checked').val()){
      $('#inactive_products_off').prop('checked', true);
      $('#inactive_products_on').prop('checked', false);
    }
  });

  $(document).on('change', 'input[name=inactive_products]', function(){
    if($('#inactive_products_on:checked').val()){
      $('#active_products_off').prop('checked', true);
      $('#active_products_on').prop('checked', false);
    }
  });

  $(document).on('change', 'input[name=selection_type_price]', function(){
    $('.price .label_selection_type').removeClass('active');
    $(this).prev().addClass('active');
  });

  $(document).on('change', 'input[name=selection_type_quantity]', function(){
    $('.quantity .label_selection_type').removeClass('active');
    $(this).prev().addClass('active');
  });

  $(document).on('change', 'input[name=selection_price]', function(){
    $('.price_up .label_selection_type').removeClass('active');
    $(this).prev().addClass('active');
  });

  $(document).on('change', 'input[name=selection_quantity]', function(){
    $('.quantity_up .label_selection_type').removeClass('active');
    $(this).prev().addClass('active');
  });

  $(document).on('change', '.select_products', function(){
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'addProductToExport',
        id_shop: $("input[name=id_shop]").val(),
        id_product: $(this).val(),
      },
      dataType: 'json'
    });
  });

  $(document).on('change', '.select_manufacturers', function(){
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'addManufacturerToExport',
        id_shop: $("input[name=id_shop]").val(),
        id_manufacturer: $(this).val(),
      },
      dataType: 'json'
    });
  });

  $(document).on('change', '.select_suppliers', function(){
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'addSupplierToExport',
        id_shop: $("input[name=id_shop]").val(),
        id_supplier: $(this).val(),
      },
      dataType: 'json'
    });
  });

  $(document).on('change', '.selection_all', function(){
    $('#export .export_fields input').prop('checked', this.checked);
    $('#export .export_fields input[value=id_product]').prop('checked', true);
    $('#export .export_fields input[value=id_product_attribute]').prop('checked', true);
    $('#export .export_fields input[value=id_specific_price]').prop('checked', true);
  });

  $(document).on('change', '.selection_all_update', function(){
    $('#update .export_fields input').prop('checked', this.checked);
  });

  $(document).on('keyup', '#update #search_field', function(){
    var self = $(this);
    $('#update .export_fields label').each(function(){
      $(this).parent().css('opacity', '0.5');
      if( $(this).text().indexOf(self.val()) >= 0 ){
        $(this).parent().css('opacity', '1');
      }
    });
  });

  $(document).on('keyup', '#export #search_field', function(){
    var self = $(this);
    $('#export .export_fields label').each(function(){
      $(this).parent().css('opacity', '0.5');
      if( $(this).text().indexOf(self.val()) >= 0 ){
        $(this).parent().css('opacity', '1');
      }
    });
  });

  $(document).on('click', '.product_list #show_checked', function(e){
    e.preventDefault();
    $(".product_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showCheckedProducts',
        id_shop: $("input[name=id_shop]").val(),
        id_lang: $("input[name=id_lang]").val(),
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".product_list .col-lg-6 tbody").replaceWith(json['products']);
      }
    });
  });

  $(document).on('click', '.manufacturer_list #show_checked', function(e){
    e.preventDefault();
    $(".manufacturer_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showCheckedManufacturers',
      },
      dataType: 'json', 
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".manufacturer_list .col-lg-6 tbody").replaceWith(json['manufacturers']);
      }
    });
  });

  $(document).on('click', '.supplier_list #show_checked', function(e){
    e.preventDefault();
    $(".supplier_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showCheckedSuppliers',
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".supplier_list .col-lg-6 tbody").replaceWith(json['suppliers']);
      }
    });
  });

  $(document).on('click', '.product_list #show_all', function(e){
    e.preventDefault();
    $(".product_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showAllProducts',
        id_shop: $("input[name=id_shop]").val(),
        id_lang: $("input[name=id_lang]").val(),
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".product_list .col-lg-6 tbody").replaceWith(json['products']);
      }
    });
  });

  $(document).on('click', '.manufacturer_list #show_all', function(e){
    e.preventDefault();
    $(".manufacturer_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showAllManufacturers',
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".manufacturer_list .col-lg-6 tbody").replaceWith(json['manufacturers']);
      }
    });
  });

  $(document).on('click', '.supplier_list #show_all', function(e){
    e.preventDefault();
    $(".supplier_list .col-lg-6 .search_checkbox_table").val("");
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'showAllSuppliers',
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        $(".supplier_list .col-lg-6 tbody").replaceWith(json['suppliers']);
      }
    });
  });

  $(document).on('keyup', '.product_list .search_checkbox_table', function(e){
    var self = $(this);
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'searchProduct',
        id_shop: $("input[name=id_shop]").val(),
        id_lang: $("input[name=id_lang]").val(),
        search_product: self.val(),
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        if (json['products']) {
          self.parents('table').find('tbody').replaceWith(json['products']);
        }
      }
    });
  })

  $(document).on('keyup', '.manufacturer_list .search_checkbox_table', function(e){
    var self = $(this);
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'searchManufacturer',
        search_manufacturer: self.val(),
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        if (json['manufacturers']) {
          self.parents('table').find('tbody').replaceWith(json['manufacturers']);
        }
      }
    });
  })

  $(document).on('keyup', '.supplier_list .search_checkbox_table', function(e){
    var self = $(this);
    $.ajax({
      url: 'index.php',
      type: 'post',
      data: {
        ajax: true,
        token: $('input[name=token_quantityupdate]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'searchSupplier',
        search_supplier: self.val(),
      },
      dataType: 'json',
      success: function(json) {
        $('.alert, .alert-danger, .alert-success').remove();
        if (json['suppliers']) {
          self.parents('table').find('tbody').replaceWith(json['suppliers']);
        }
      }
    });
  })

  $(document).on('click', 'button.export', function(e){
    exportProducts(0);
  });

  $(document).on('click', 'button.update', function(e){
    updateProducts(0);
  });

  $(document).on('change', '.quantityupdate input[name=format_file_update_automatic]', function(){
    if( $(this).val() == 'csv' ){
      $('.quantityupdate .delimiter').fadeIn();
    }
    else{
      $('.quantityupdate .delimiter').fadeOut();
    }
  });

  $(document).on('change', '.quantityupdate select[name=feed_source]', function(){
    if( $(this).val() == 'file_url' ){
      $('.quantityupdate .form_file_url').show();
      $('.quantityupdate .file_import_ftp').hide();
      $('.quantityupdate .form_fields_mapping').removeClass('ftp');
    }
    else{
      $('.quantityupdate .file_import_ftp').show();
      $('.quantityupdate .form_file_url').hide();
      $('.quantityupdate .form_fields_mapping').addClass('ftp');
    }
  });

  $(document).on('click', '.quantityupdate .fields_mapping', function(){
    fieldsMapping();
  });
  
  $(document).on('click', '.quantityupdate button.save_settings', function(){
    $.ajax({
      type: "POST",
      url: "index.php",
      dataType: 'json',
      data: {
        ajax	: true,
        token: $('input[name=quantity_update_token]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'saveSettings',
        id_shop:$('input[name=id_shop]').val(),
        format:$('input[name=format_file_update_automatic]:checked').val(),
        delimiter:$('select[name=delimiter_val]').val(),
        file_url:$('input[name=file_url]').val(),
        feed_source:$('select[name=feed_source]').val(),
        ftp_server:$('input[name=ftp_server]').val(),
        ftp_user:$('input[name=ftp_user]').val(),
        ftp_password:$('input[name=ftp_password]').val(),
        ftp_file_path:$('input[name=ftp_file_path]').val(),
        product_identifier:$('select[name=product_identifier]').val(),
        file_product_identifier:$('select[name=file_product_identifier]').val(),
        product_price:$('select[name=product_price]').val(),
        file_product_price:$('select[name=file_product_price]').val(),
        file_quantity:$('select[name=file_quantity]').val(),
        quantity_update_method:$('select[name=quantity_update_method]').val(),
        quantity_source:$('select[name=quantity_source]').val(),
        price_source:$('select[name=price_source]').val(),
        settings:$('input[name=settings]').val(),
        emails:$('textarea[name=emails]').val(),
        in_store_not_in_file: $('select[name=in_store_not_in_file]').val(),
        in_store_and_in_file: $('select[name=in_store_and_in_file]').val(),
        zero_quantity_disable: $('input[name=zero_quantity_disable]:checked').val(),
        disable_hooks: $('input[name=disable_hooks]:checked').val(),
        show_advanced_price_settings: $('input[name=show_advanced_price_settings]:checked').val(),
        show_advanced_quantity_settings: $('input[name=show_advanced_quantity_settings]:checked').val(),
        quantity_update_conditions: getQuantityUpdateConditions(),
        price_update_conditions: getPriceUpdateConditions()
      },
      async : true,
      beforeSend : function(data)
      {
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
          $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $('.alert-danger, .alert-success').remove();
        $(".progres_bar_ex_quantityupdate").remove();
        if( !json ){
          $('.alert-danger, .alert-success').remove();
          $(".progres_bar_ex_quantityupdate").remove();
          $(document).scrollTop(0);
          $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please contact us!</div>');
        }

        if (json['error']) {
          $('html, body').animate({scrollTop:0}, 'slow');
          $('#content').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
        }
        else if( json['success'] ){
          $('#content').prepend('<div class="alert alert-success">' + json['success'] + '</div>');
          if( json['description'] ){
            $('.quantityupdate .automatic_block').html(json['description']);
          }
        }
      },
      error: function(){
        $('.alert-danger, .alert-success').remove();
        $(".progres_bar_ex_quantityupdate").remove();
        $(document).scrollTop(0);
        $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please contact us!</div>');
      }
    });
  });

  $(document).on('click', '.quantityupdate .nav li', function(){
    if( $(this).find('a').attr('href') == '#automatic' ){
      $('#form-settings_list').show();
    }
    else{
      $('#form-settings_list').hide();
    }
  })

  $(document).on('click', '#form-settings_list .delete', function(e){
    e.preventDefault();
    var self = $(this);
    var link = $(this).attr('href');
    var settings_name = getURLParameter('settings_name', link);

    $.ajax({
      type: "POST",
      url: "index.php",
      dataType: 'json',
      data: {
        ajax	: true,
        token: $('input[name=quantity_update_token]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'removeSettings',
        settings_name:settings_name,
      },
      async : true,
      beforeSend : function(data)
      {
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
          $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $(".progres_bar_ex_quantityupdate").remove();
        $('.alert-danger, .alert-success').remove();
        $('.quantityupdate .mapping_form').html('');
        if (json['error']) {
          $('html, body').animate({scrollTop:0}, 'slow');
          $('#content').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
        }
        else{
          self.parents('tr').remove();
        }
      }
    });

  })

  $(document).on('click', '#form-settings_list .btn.edit', function(e){
    e.preventDefault();
    var link = $(this).attr('href');
    var settings_name = getURLParameter('settings_name', link);

    $.ajax({
      type: "POST",
      url: "index.php",
      dataType: 'json',
      data: {
        ajax	: true,
        token: $('input[name=quantity_update_token]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'loadSettings',
        settings_name:settings_name,
      },
      async : true,
      beforeSend : function(data)
      {
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
          $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $(".progres_bar_ex_quantityupdate").remove();
        $('.alert-danger, .alert-success').remove();
        $('.quantityupdate .mapping_form').html('');
        if (json['error']) {
          $('html, body').animate({scrollTop:0}, 'slow');
          $('#content').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
        }
        else if( json['data'] ){
          $('input:radio[name="format_file_update_automatic"]').filter('[value="'+json['data']['format']+'"]').attr('checked', true);
          if( json['data']['format'] == 'xlsx' ){
            $('.quantityupdate .delimiter').hide();
          }
          else{
            $('.quantityupdate .delimiter').show();
          }
          $('select[name=delimiter_val]').val(json['data']['delimiter']);
          $('select[name=feed_source]').val(json['data']['feed_source']);
          $('select[name=feed_source]').change();
          if( json['data']['feed_source'] == 'file_url' ){
            $('input[name=file_url]').val(json['data']['file_url']);
          }
          else {
            $('input[name=ftp_server]').val(json['data']['ftp_server']);
            $('input[name=ftp_user]').val(json['data']['ftp_user']);
            $('input[name=ftp_password]').val(json['data']['ftp_password']);
            $('input[name=ftp_file_path]').val(json['data']['ftp_file_path']);
          }
          fieldsMapping(settings_name);
        }
        
        toggleAdvancedPriceSettingsVisibility();
        toggleAdvancedQuantitySettingsVisibility();
      }
    });
  });
  
  function getQuantityUpdateConditions() {
    var quantity_update_conditions = [];
    
    $("#quantity_conditions_block .quantity-condition").each(function() {
      var condition_value = $(this).find("#quantity_condition_value").val();
      var condition_formula = $(this).find("#quantity_condition_formula").val();
      
      if (!condition_formula) {
        return;
      }
      
      var quantity_condition = {
        condition: $(this).find("#quantity_condition").val(),
        value: condition_value,
        formula: condition_formula
      };
      
      quantity_update_conditions.push(quantity_condition);
    });
    
    return quantity_update_conditions;
  }
    
  function getPriceUpdateConditions() {
    var price_update_conditions = [];
    
    $("#price_conditions_block .price-condition").each(function() {
      var condition_value = $(this).find("#price_condition_value").val();
      var condition_formula = $(this).find("#price_condition_formula").val();
      
      if (!condition_formula) {
        return;
      }
      
      var price_condition = {
        condition: $(this).find("#price_condition").val(),
        value: condition_value,
        formula: condition_formula
      };

      price_update_conditions.push(price_condition);
    });
    
    return price_update_conditions;
  }

  function liveUpdate(){

    var data = $('form.quantityupdate').serializeArray();
    data.push({ name: 'ajax', value: true });
    data.push({ name: 'controller', value: 'AdminQuantityUpdate' });
    data.push({ name: 'action', value: 'liveUpdate' });
    data.push({ name: 'token', value: $('input[name=token_quantityupdate]').val() });

    $.ajax({
      url: 'indax.php',
      type: 'post',
      data: data,
      dataType: 'json',
      beforeSend: function(){
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
           $(".table_product_list").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $('.progres_bar_ex_quantityupdate').remove();
        $('.alert-danger, .alert-success').remove();
        if (json['error']) {
          showErrorMessage(json['error']);
        }
        if (json['success']) {
          showSuccessMessage(json['success'])
          if (json['table']) {
            $('.table_product_list').replaceWith(json['table'])
          }
        }
      },
    });
  }

  function filterQuantityupdate(reset){

    var data = $('form.quantityupdate').serializeArray();
    data.push({ name: 'ajax', value: true });
    data.push({ name: 'controller', value: 'AdminQuantityUpdate' });
    data.push({ name: 'action', value: 'filterQuantityUpdate' });
    data.push({ name: 'reset', value: reset });
    data.push({ name: 'token', value: $('input[name=token_quantityupdate]').val() });

    $.ajax({
      url: 'index.php',
      type: 'post',
      data: data,
      dataType: 'json',
      beforeSend: function(){
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
           $(".table_product_list").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $('.progres_bar_ex_quantityupdate').remove()
        if (json['error']) {
          showErrorMessage(json['error']);
        }
        if (json['success']) {
          $('.table_product_list').replaceWith(json['success'])
        }
      },
    });
  }


  function getURLParameter(sParam, link)
  {
    var sPageURL = link;
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
      var sParameterName = sURLVariables[i].split('=');
      if (sParameterName[0] == sParam)
      {
        return sParameterName[1];
      }
    }
  }

  function fieldsMapping( settings )
  {
    if( !settings ){
      settings = '';
    }
    $.ajax({
      type: "POST",
      url: "index.php",
      dataType: 'json',
      data: {
        ajax	: true,
        token: $('input[name=quantity_update_token]').val(),
        controller: 'AdminQuantityUpdate',
        action: 'fieldsMapping',
        id_shop:$('input[name=id_shop]').val(),
        format:$('input[name=format_file_update_automatic]:checked').val(),
        delimiter:$('select[name=delimiter_val]').val(),
        file_url:$('input[name=file_url]').val(),
        feed_source:$('select[name=feed_source]').val(),
        ftp_server:$('input[name=ftp_server]').val(),
        ftp_user:$('input[name=ftp_user]').val(),
        ftp_password:$('input[name=ftp_password]').val(),
        ftp_file_path:$('input[name=ftp_file_path]').val(),
        settings:settings
      },
      async : true,
      beforeSend : function(data)
      {
        if( $('.progres_bar_ex_quantityupdate').length < 1 ){
          $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
        }
      },
      success: function(json) {
        $(".progres_bar_ex_quantityupdate").remove();
        $('.alert-danger, .alert-success').remove();
        $('.quantityupdate .mapping_form').html('');
        if (json['error']) {
          $('html, body').animate({scrollTop:0}, 'slow');
          $('#content').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
        }
        else if( json['form'] ){
          $('.quantityupdate .mapping_form').html($(json['form']).find('.panel').html());
          $('.label-tooltip').tooltip();
        }
    
        toggleAdvancedPriceSettingsVisibility();
        toggleAdvancedQuantitySettingsVisibility();
      }
    });
  }

});

refreshIntervalId = false;
function exportProducts( pageLimit ) {
  if( pageLimit == 0 ){
    refreshIntervalId = setInterval(function(){ returnExportedProducts($("input[name=id_shop]").val()); }, 1000);
  }

  var data = $('form.quantityupdate').serializeArray();
  data.push({ name: 'ajax', value: true });
  data.push({ name: 'controller', value: 'AdminQuantityUpdate' });
  data.push({ name: 'action', value: 'exportProducts' });
  data.push({ name: 'token', value: $('input[name=token_quantityupdate]').val() });
  data.push({ name: 'page_limit', value: pageLimit });

  $.ajax({
    url: 'index.php',
    type: 'post',
    data: data,
    dataType: 'json',
    beforeSend: function(){
      if( $('.progres_bar_ex_quantityupdate').length < 1 ){
        $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
      }
    },
    success: function(json) {
      if( !json ){
        clearInterval(refreshIntervalId);
        $('.alert-danger, .alert-success').remove();
        $(".progres_bar_ex_quantityupdate").remove();
        $(document).scrollTop(0);
        $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please check <a href="../modules/quantityupdate/error.log" target="_blank">error.log</a> file or contact us!</div>');
      }
      if (json['error']) {
        $(".progres_bar_ex_quantityupdate").remove();
        $('.alert-danger, .alert-success').remove();
        clearInterval(refreshIntervalId);
        $(document).scrollTop(0);

        $('#bootstrap_products').before('<div class="alert alert-danger">' + json['error'] + '</div>');
      }
      else {
        if (json['success']) {
          $(".progres_bar_ex_quantityupdate").remove();
          $('.alert-danger, .alert-success').remove();
          clearInterval(refreshIntervalId);
          $('#bootstrap_products').before('<div class="alert alert-success">' + json['success'] + '</div>');

          if( json.file ){
            location.href = json.file;
          }
        }
        if( json['page_limit'] ){
          exportProducts(json['page_limit']);
        }
      }
    },
    error: function(){
      clearInterval(refreshIntervalId);
      $('.alert-danger, .alert-success').remove();
      $(".progres_bar_ex_quantityupdate").remove();
      $(document).scrollTop(0);
      $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please check <a href="../modules/quantityupdate/error.log" target="_blank">error.log</a> file or contact us!</div>');
    }
  });
}

function updateProducts(pageLimit) {
  if( pageLimit == 0 ){
    refreshIntervalId = setInterval(function(){ returnUpdatedProducts($("input[name=id_shop]").val()); }, 1000);
  }

  var xlsxData = new FormData();
  xlsxData.append('file', $('input[name=file]')[0].files[0]);
  xlsxData.append('id_shop', $("input[name=id_shop]").val());
  xlsxData.append('id_lang', $("select[name=id_lang_update]").val());
  xlsxData.append('field_for_update', $("select[name=field_for_update]").val());
  xlsxData.append('format_file', $("input[name=format_file_update]:checked").val());
  xlsxData.append('tax_price_update', $("input[name=tax_price_update]:checked").val());
  xlsxData.append('page_limit', pageLimit);
  xlsxData.append('action', 'updateProducts');
  xlsxData.append('token', $('input[name=token_quantityupdate]').val());
  xlsxData.append('ajax', true);
  xlsxData.append('controller', 'AdminQuantityUpdate');

  $.each( $('#update .export_fields input[type=checkbox]').serializeArray(), function( key, value ) {
    xlsxData.append(value.name, value.value );
  });


  $.ajax({
    url: 'index.php',
    type: 'post',
    data: xlsxData,
    dataType: 'json',
    processData: false,
    contentType: false,
    beforeSend: function(){
      if( $('.progres_bar_ex_quantityupdate').length < 1 ){
        $("body").append('<div class="progres_bar_ex_quantityupdate"><div class="loading_block"><div class="loading"></div><div class="exporting_notification"></div></div></div>');
      }
    },
    success: function(json) {
      $('.alert, .alert-danger, .alert-success').remove();

      if( !json ){
        clearInterval(refreshIntervalId);
        $('.alert-danger, .alert-success').remove();
        $(".progres_bar_ex_quantityupdate").remove();
        $(document).scrollTop(0);
        $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please check <a href="../modules/quantityupdate/error.log" target="_blank">error.log</a> file or contact us!</div>');
      }

      if( json.error ){
        clearInterval(refreshIntervalId);
        $('.alert-danger, .alert-success').remove();
        $(".progres_bar_ex_quantityupdate").remove();
        $(document).scrollTop(0);
        $('#content').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
      }
      else {
        if( json.success ){
          $('.alert-danger, .alert-success').remove();
          $(".progres_bar_ex_quantityupdate").remove();
          clearInterval(refreshIntervalId);
          var success = json['success'];
          var error_logs = success.error_logs;

          if(error_logs){
            var url = error_logs;
            url = ' <a class="error_logs_import" href="'+url+'">error_logs.csv<a>';
          }
          else{
            var url = '';
          }

          $('#content').prepend('<div class="alert alert-success">' + success.message + url + '</div>');
        }
        if( json['page_limit'] ){
          updateProducts(json['page_limit']);
        }
      }
      $(document).scrollTop(0);
    },
    error: function(){
      clearInterval(refreshIntervalId);
      $('.alert-danger, .alert-success').remove();
      $(".progres_bar_ex_quantityupdate").remove();
      $(document).scrollTop(0);
      $('#bootstrap_products').before('<div class="alert alert-danger">Some error occurred please check <a href="../modules/quantityupdate/error.log" target="_blank">error.log</a> file or contact us!</div>');
    }
  });
}

function returnExportedProducts(id_shop){
  $.ajax({
    url: 'index.php',
    type: 'post',
    data: {
      ajax: true,
      token: $('input[name=token_quantityupdate]').val(),
      controller: 'AdminQuantityUpdate',
      action: 'returnExportCount',
      id_shop: $("input[name=id_shop]").val(),
    },
    dataType: 'json',
    success: function(json) {
      if (json['export_notification']) {
        $('.exporting_notification').html(json['export_notification'])
      }
    }
  });
}

function returnUpdatedProducts(id_shop){
  $.ajax({
    url: 'index.php',
    type: 'post',
    data: {
      ajax: true,
      token: $('input[name=token_quantityupdate]').val(),
      controller: 'AdminQuantityUpdate',
      action: 'returnUpdateCount',
      id_shop: $("input[name=id_shop]").val(),
    },
    dataType: 'json',
    success: function(json) {
      if (json['update_notification']) {
        $('.exporting_notification').html(json['update_notification'])
      }
    }
  });
}

function replaceUrlFile(){
  var url = $('.href_export_file').attr('data-file-url');
  var name_file = $('input[name=name_file]').val();
  var type = $('input[name=format_file]:checked').val();
  if( type == 'xlsx' ){
    $('.form_image_cover').show();
  }
  else {
    $('.form_image_cover').hide();
  }

  var file_url = url+name_file+'.'+type;
  if(name_file){
    $('.href_export_file').attr('href', file_url);
    $('.href_export_file').html(file_url);
    $('.available_url').show();
  }
  else{
    $('.href_export_file').attr('href', '');
    $('.href_export_file').html('');
    $('.available_url').hide();
  }
}
function showSuccessMessage(msg) {
  $.growl.notice({ title: "", message:msg});
}

function showErrorMessage(msg) {
  $.growl.error({ title: "", message:msg});
}

function showNoticeMessage(msg) {
  $.growl.notice({ title: "", message:msg});
}

$(document).on("click", ".quantityupdate .nav-tabs a[href='#support']", function(e) {
  var url = $('.support_url').val();
  var win = window.open(url, '_blank');
  win.focus();
  $(".quantityupdate .nav-tabs a[href=#general]").click();
});

