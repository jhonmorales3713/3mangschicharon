$(function(){
  var base_url = $("body").data('base_url');
  function submit_form(form, btn, modal = false){
		showCover("Saving...");
		var formData = new FormData(form);

		$.ajax({
  		type: 'post',
  		url: form.action,
  		data: formData,
  		contentType: false,
			cache: false,
			processData:false,
      dataType: 'json',
  		success:function(data){
  			hideCover();

  			if(data.success==false)
  			{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
  				$('input[name='+data.csrf_name+']').val(data.csrf_hash);
  			}
  			else
  			{
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
  				// window.location = data.url;
          if(modal){
          $('#'+modal).modal('hide');
          }
          setTimeout(() => {window.location.reload(true)},1500);
  			}
  		},
  		error: function(error){
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again."
        });
        hideCover();
  		}
  	});
	}

  $("#regCode").change(function() {
      showCover('Loading data...');
      $("#citymunCode option").remove();
      // provCode = $(this).val();
      regCode = $(this).val();

      $.ajax({
          type:'post',
          url:base_url+'sys/shipping_delivery/get_citymun',
          data:{
              'regCode': $(this).val()
          },
          success:function(data){
              hideCover();
              var json_data = JSON.parse(data);

              if(json_data.success){
                  $('#citymunCode')
                       .append($("<option></option>")
                          .attr("value", "")
                          .attr("readonly", "")
                          .text("SELECT CITY"));
                  if(zoneArray.filter(e => parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1).length > 0){
                  }else{
                      // $('#citymunCode')
                      //      .append($("<option></option>")
                      //         .attr("value", "0")
                      //         .text("ENTIRE PROVINCE"));
                  }


                  $.each(json_data.data, function(key, value) {
                      if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray.filter(e => parseFloat(e.citymunCode) === parseFloat(value.citymunCode) && e.status === 1).length > 0){
                      }
                      else if(zoneArray.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 &&zoneArray.filter(e => parseFloat(e.citymunCode) === parseFloat(0) && e.status === 1).length > 0){
                      }else{
                        //if NCR
                        if(regCode == 13) {
                          cityDesc = value.citymunDesc;
                        }
                        else {
                          cityDesc = value.provDesc+" ("+value.citymunDesc+")";
                        }

                        $('#citymunCode')
                            .append($("<option></option>")
                                .attr("value", value.citymunCode)
                                .text(cityDesc));

                      }

                  });
                  $('#citymunCode').prop('disabled', false);

              }else{
                  sys_toast_warning('No data found');
              }
          },
          error: function(error){
              hideCover();
              sys_toast_error('Error');
          }
      });

  });
  let zoneArray = [];

  $("#update_regCode").change(function() {
      showCover('Loading data...');
      const code = $('#city_code').val();
      // console.log('city',code);
      $("#update_citymunCode option").remove();
      // provCode = $(this).val();
      regCode = $(this).val();
      // console.log('regcode',regCode);

      $.ajax({
          type:'post',
          url:base_url+'sys/shipping_delivery/get_citymun',
          data:{
              'regCode': $(this).val()
          },
          success:function(data){
              hideCover();
              var json_data = JSON.parse(data);

              if(json_data.success){
                // console.log('mun',json_data);
                  $('#update_citymunCode')
                       .append($("<option></option>")
                          .attr("value", "")
                          .attr("readonly", "")
                          .text("SELECT CITY"));
                  if(zoneArray2.filter(e => parseFloat(e.citymunCode) != parseFloat(0) && e.status === 1).length > 0){
                  }else{
                      // $('#citymunCode')
                      //      .append($("<option></option>")
                      //         .attr("value", "0")
                      //         .text("ENTIRE PROVINCE"));
                  }


                  $.each(json_data.data, function(key, value) {
                      if(zoneArray2.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 && zoneArray2.filter(e => parseFloat(e.citymunCode) === parseFloat(value.citymunCode) && e.status === 1).length > 0){
                      }
                      else if(zoneArray2.filter(e => parseFloat(e.regCode) === parseFloat(value.regCode) && e.status === 1).length > 0 &&zoneArray2.filter(e => parseFloat(e.citymunCode) === parseFloat(0) && e.status === 1).length > 0){
                      }else{
                        //if NCR
                        if(regCode == 13) {
                          cityDesc = value.citymunDesc;
                        }
                        else {
                          cityDesc = value.provDesc+" ("+value.citymunDesc+")";
                        }

                        if(code == value.citymunCode) {
                          $('#update_citymunCode')
                            .append($("<option></option>")
                                .attr("value", value.citymunCode)
                                .attr("selected", "")
                                .text(cityDesc));
                        }
                        else {
                          $('#update_citymunCode')
                              .append($("<option></option>")
                                  .attr("value", value.citymunCode)
                                  .text(cityDesc));
                        }
                      }

                  });
                  // $('#update_citymunCode option[value = "'+code+'"]').prop('selected', true).trigger('change');
                  $('#update_citymunCode').prop('disabled', false);

              }else{
                  sys_toast_warning('No data found');
              }
          },
          error: function(error){
              hideCover();
              sys_toast_error('Error');
          }
      });

  });
  let zoneArray2 = [];

  $(document).on('submit', '#address_form', function(e){
    e.preventDefault();
    submit_form($(this)[0], $(this).find('button[type=submit]')[0],'addAddress');
  });

  $(document).on('click', '.btn_edit', function(){
    let uid = $(this).data('uid');
    let receiver_name = $(this).data('receiver_name');
    let receiver_contact = $(this).data('receiver_contact');
    let address = $(this).data('address');
    let regCode = $(this).data('regcode');
    let citymunCode = $(this).data('citymuncode');
    let postal_code = $(this).data('postal_code');
    let landmark = $(this).data('landmark');
    // console.log(typeof postal_code);
    $('#city_code').val(citymunCode);
    $('#uid').val(uid);
    $('#update_receiver_name').val(receiver_name);
    $('#update_receiver_no').val(receiver_contact);
    $('#update_receiver_address').val(address);
    if(postal_code == "" || postal_code == null || postal_code == 0){
      $('#update_postal_code').attr('placeholder','N/A');
    }else{
      $('#update_postal_code').val(postal_code);
    }
    $('#update_landmark').val(landmark);
    $('#update_regCode option[value = "'+regCode+'"]').prop("selected", true).trigger('change');
    // $('#update_citymunCode option[value = "'+citymunCode+'"]').prop("selected", true).trigger('change');

    $('#editAddress').modal();
  });

  $(document).on('submit', '#address_update_form', function(e){
    e.preventDefault();
    submit_form($(this)[0], $(this).find('button[type=submit]')[0],'editAddress');
  });

  $(document).on('click', '.btn_delete', function(){
    let delid = $(this).data('delid');
    $('#delid').val(delid);
    $('#deleteAddress').modal();
  });

  $(document).on('submit', '#address_delete_form', function(e){
    e.preventDefault();
    submit_form($(this)[0], $(this).find('button[type=submit]')[0],'deleteAddress');
  });

  $(document).on('click', '.btn_default', function(){
    let defaultid = $(this).data('defaultid');
    $.ajax({
      url: base_url+'profile/Customer_profile/set_default_address',
      type: 'post',
      data:{defaultid},
      dataType: 'json',
      cache: false,
      beforeSend: function(){
        showCover('Updating ...');
      },
      success: function(data){
        hideCover();
        if(data.success == true){
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
          setTimeout(() => {window.location.reload(true)},1500);
        }else{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
        }
      },
      error: function(){
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again"
        });
        hideCover();
      }
    });
  });
});
