$(() => {
    var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
    var shop_url = $("body").data('shop_url');
	var token = $("body").data('token');
	var prev_val = {
		'powered_by' : '',
		'image': '',
		'shop_main_announcement' : '',
		'c_paypanda_link_live' : '',
		'c_paypanda_link_test' : '',
		'c_allowed_jcfulfillment_prefix' : '',
	};

    $('#logo_image_multip').on('change', function () {
    	countFiles = $(this)[0].files.length;
    	$.LoadingOverlay("show");
    	$(".imagepreview").empty();
    	imagesPreview(this, 'div.imagepreview');
    	$('#product-placeholder').hide();
    	$('.imagepreview').hide('slow');
    	$('.imagepreview').show('slow');
    	$('#file_label').text(countFiles + ' Attached Image(s)');
    	$('#upload_checker').val(0)
    	$.LoadingOverlay("hide");
	});
	
	$(document).ready(function () {
		if (!is_editable) {
			$('input.form-control').prop('disabled',true);
			$('textarea.form-control').prop('disabled',true);
			$('div.file-input').addClass('hidden');
			$('div.buttons').addClass('hidden');
		}

		prev_val.id = $('#cp_id').val();
		prev_val.powered_by = $('#powered_by').val();
		prev_val.image = $('.imagepreview').attr('src');
		prev_val.shop_main_announcement = $('#shop_main_announcement').val();
		prev_val.c_paypanda_link_live = $('#c_paypanda_link_live').val();
		prev_val.c_paypanda_link_test = $('#c_paypanda_link_test').val();
		prev_val.c_allowed_jcfulfillment_prefix = $('#c_allowed_jcfulfillment_prefix').val();

		$('#prev_val').val(JSON.stringify(prev_val));
	});

    var imagesPreview = function (input, placeToInsertImagePreview) {
    	if (input.files) {
    		var filesAmount = input.files.length;

    		for (i = 0; i < filesAmount; i++) {
    			var reader = new FileReader();

    			reader.onload = function (event) {
    				$($.parseHTML('<img id="product_preview">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
    				$('<font>&nbsp;</font>').appendTo(placeToInsertImagePreview)
    			}

    			reader.readAsDataURL(input.files[i]);
    		}
    	}
    };

    $('#form_shop_util').submit(function (e) {
    	e.preventDefault();
    	$.LoadingOverlay("show");

    	var form = $(this);
    	var form_data = new FormData(form[0]);

    	$.ajax({
    		type: form[0].method,
    		url: base_url + 'developer_settings/Dev_settings/update_shop_utility',
    		data: form_data,
    		contentType: false,
    		cache: false,
    		processData: false,
    		success: function (data) {
    			$.LoadingOverlay("hide");
    			var json_data = JSON.parse(data);

    			if (json_data.success) {
    				// $.toast({
    				// 	heading: 'Success',
    				// 	text: json_data.message,
    				// 	icon: 'success',
    				// 	loader: false,
    				// 	stack: false,
    				// 	position: 'top-center',
    				// 	bgColor: '#5cb85c',
    				// 	textColor: 'white',
    				// 	allowToastClose: false,
    				// 	hideAfter: 10000
    				// });
    				// location.reload();
    				showCpToast("success", "Success!", json_data.message);
        			setTimeout(function(){location.reload()}, 2000);
    			} else {
    				// $.toast({
    				// 	heading: 'Note',
    				// 	text: json_data.message,
    				// 	icon: 'info',
    				// 	loader: false,
    				// 	stack: false,
    				// 	position: 'top-center',
    				// 	bgColor: '#FFA500',
    				// 	textColor: 'white'
    				// });
    				showCpToast("info", "Note!", json_data.message);
    				$('#f_member_shop').prop('disabled', true);

    			}
    		},
    		error: function (error) {
    			// $.toast({
    			// 	heading: 'Error',
    			// 	text: json_data.message,
    			// 	icon: 'error',
    			// 	loader: false,
    			// 	stack: false,
    			// 	position: 'top-center',
    			// 	bgColor: '#FFA500',
    			// 	textColor: 'white'
    			// });
    			showCpToast("error", "Error!", json_data.message);
    		}
    	});

    });
})