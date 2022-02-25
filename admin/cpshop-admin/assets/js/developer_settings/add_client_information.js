$(document).ready(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	var token    = $("body").data('token');
	imageBlob = '';

    $('#btn_open_modal_save').click(function(e){
        reset_form();
    })

    var reset_form = function(){
        $('#form_save')[0].reset();
        $('#modal_save').modal();
	}

	$('#form_save').submit(function(e){
		e.preventDefault();
        $.LoadingOverlay("show");
		var form = $(this);
		var form_data = new FormData(form[0]);
        //form_data.append([ajax_token_name],ajax_token);
        var endpoint = form.data('create_url');
        var close_modal = false;
		$.ajax({
	  		type: form[0].method,
	  		url: endpoint,
	  		data: form_data,
	  		contentType: false,   
			cache: false,      
			processData:false,
	  		success:function(data){
                $.LoadingOverlay("hide");
	  			var json_data = JSON.parse(data);
	  			update_token(json_data.csrf_hash);
	  			if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    showCpToast("success", "Success!", json_data.message);
	  				//window.location.assign(base_url+"Dev_settings_client_information/client_information/"+token);
	  			}else{
                    //sys_toast_warning(json_data.message);
                    showCpToast("warning", "Warning!", json_data.message);
	  			}
	  		},
	  		error: function(error){
	  			//sys_toast_error('Something went wrong. Please try again.');
                showCpToast("error", "Error!", 'Something went wrong. Please try again.');
	  		}
	  	});

	});
    
	$(document).delegate('.btn_tbl_update','click',function(e){		
		window.location.assign(base_url+"sys/client_information/update_client_information/"+e.currentTarget.id);
	});

	$('#main_logo').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".main_logo" ).empty();
        imagesPreview(this, 'div.main_logo');
        $('.main_logo').hide('slow');
        $('.main_logo').show('slow');
        $('#file_label_main_logo').text(countFiles+' Attached Image(s)');
        $('#main_logo_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#secondary_logo').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".secondary_logo" ).empty();
        imagesPreview(this, 'div.secondary_logo');
        $('.secondary_logo').hide('slow');
        $('.secondary_logo').show('slow');
        $('#file_label_secondary_logo').text(countFiles+' Attached Image(s)');
        $('#secondary_logo_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#placeholder_img').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".placeholder_img" ).empty();
        imagesPreview(this, 'div.placeholder_img');
        $('.placeholder_img').hide('slow');
        $('.placeholder_img').show('slow');
        $('#file_label_placeholder_img').text(countFiles+' Attached Image(s)');
        $('#placeholder_img_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#fb_image').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".fb_image" ).empty();
        imagesPreview(this, 'div.fb_image');
        $('.fb_image').hide('slow');
        $('.fb_image').show('slow');
        $('#file_label_fb_image').text(countFiles+' Attached Image(s)');
        $('#fb_image_checker').val('true')
        $.LoadingOverlay("hide");
    });

    $('#favicon').on('change', function() {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$( ".favicon" ).empty();
        imagesPreview(this, 'div.favicon');
        $('.favicon').hide('slow');
        $('.favicon').show('slow');
        $('#file_label_favicon').text(countFiles+' Attached Image(s)');
        $('#favicon_checker').val('true')
        $.LoadingOverlay("hide");
    });


	var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img id="product_preview">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                    $('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
                }

                reader.readAsDataURL(input.files[i]);

            }
        }

	};
	
	$('input[type=checkbox]').click(function(){
		if($(this).prop('checked') == true){
			$(this).parent().prev().val('1');
		}
		else{
			$(this).parent().prev().val('0');
		}
	});

	$('#backBtn').click(function(){
		var token = $("body").data('token');
		window.location.assign(base_url+"Dev_settings_client_information/client_information/"+token);
    });
    
    //no color option
    $('.no-color input[type=checkbox]').on('change',function(){
        if($(this).prop('checked') == true){
            $(this).parent().next().attr('readonly','true');
        }
        else{
            $(this).parent().next().removeAttr('readonly');
        }
    });

})