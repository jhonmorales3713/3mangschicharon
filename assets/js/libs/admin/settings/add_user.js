var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
$(function () {
	$('#change-image').click(function(){
		$('#avatar-placeholder').removeClass('hidden');
		$('#avatar_preview').attr('src', '');
		$('#avatar_preview').hide();
		$('#avatar_image').val('');
		$('#avatar_image').click();
    });	
    
    $('#avatar_image').change(function() {
		var fileInput = $(this);
		var input = this;
		var file = fileInput[0].files[0];
		try{
	    	// Ensure it's an image
	    	if(file.type.match(/image.*/)) {
		        // Load the image
		        var reader = new FileReader();
		        reader.onload = function (readerEvent) {
		        	
		        	$('#avatar_preview').attr('src', readerEvent.target.result);
		        	$('#avatar_preview').show();
		        	var image = new Image();
		        	image.onload = function (imageEvent) {
		                // Resize the image
		                var canvas = document.createElement('canvas'),
		                max_size = 300,
		                width = image.width,
		                height = image.height;
		                if (width > height) {
		                	if (width > max_size) {
		                		height *= max_size / width;
		                		width = max_size;
		                	}
		                } else {
		                	if (height > max_size) {
		                		width *= max_size / height;
		                		height = max_size;
		                	}
		                }
		                canvas.width = width;
		                canvas.height = height;
		                canvas.getContext('2d').drawImage(image, 0, 0, width, height);
		                var dataUrl = canvas.toDataURL('image/jpeg');
		                var resizedImage = dataURLToBlob(dataUrl);
		                $.event.trigger({
		                	type: "imageResized",
		                	blob: resizedImage,
		                	url: dataUrl
		                });
		            }
		            image.src = readerEvent.target.result;
		        }
		        reader.readAsDataURL(file);
		    }
		    $('#avatar-placeholder').addClass('hidden');
		}
		catch(error){
			console.log(error.message);
		}
	});

	var dataURLToBlob = function(dataURL) {
	    var BASE64_MARKER = ';base64,';
	    if (dataURL.indexOf(BASE64_MARKER) == -1) {
	        var parts = dataURL.split(',');
	        var contentType = parts[0].split(':')[1];
	        var raw = parts[1];
	        return new Blob([raw], {type: contentType});
	    }
	    var parts = dataURL.split(BASE64_MARKER);
	    var contentType = parts[0].split(':')[1];
	    var raw = window.atob(parts[1]);
	    var rawLength = raw.length;
	    var uInt8Array = new Uint8Array(rawLength);
	    for (var i = 0; i < rawLength; ++i) {
	        uInt8Array[i] = raw.charCodeAt(i);
	    }
	    return new Blob([uInt8Array], {type: contentType});
	}
	$('#avatar-placeholder').click(function(){
		$('#avatar_image').val('');
		$('#avatar_image').click();
    });
    
    $(".btnClickAddRecord").click(function(){
		change_user_modal('add');
    });

    imageBlob = '';
	$(document).on("imageResized", function (event) {
	    if (event.blob && event.url) {
	        imageBlob = event.blob;
	    }
	});
	$('#record_form').submit(function(e){
		e.preventDefault();
		var form = $(this);
		if ($("input#f_id").val() != "") { //update
			add_record(form, 'update');
		}
		else { //add
			add_record(form);
		}
	});
    function add_record(form, mode = "add") {
		let form_url = form.attr("action");
		if (mode == "update") {
			form_url = base_url + 'settings/user_list/update_data';
		}
		postdata = new FormData(form[0]);
		postdata.append('item_image', imageBlob);
		$.ajax({				
			url: form_url,
	       	type: form.attr("method"),
			data: postdata,
			contentType: false,   
			cache: false,      
			processData:false,
			beforeSend:function() {
				$.LoadingOverlay("show"); 
			},
			success : function(data){
				json_data = JSON.parse(data);
				if(json_data.success == true) {
					//showToast('success', json_data.message);
					sys_toast_success(json_data.message);
					window.location.assign(base_url+"settings/user_list/view/"+token);
				} else {
					//showToast('note', json_data.message);
					sys_toast_info(json_data.message);
					$('input[name='+json_data.csrf_name+']').val(json_data.csrf_hash);
				}					
				$.LoadingOverlay("hide");
			},
			error: function(error){
                $.LoadingOverlay("hide");
                if (error.status == 403) {
                	sys_toast_info('Security token has been expired. This page is being reloaded.');
					setTimeout(function(){location.reload()}, 2000);
                	// showToast('note', 'Security token has been expired. This page is being reloaded.');
                 //    setTimeout(function(){
                 //        window.location.href = window.location.href;
                 //    }, 1000)
                } else if (error.status == 404) {
                	//showToast('note', 'Something went wrong. Please contact the system administrator.');
                	sys_toast_info('Something went wrong. Please contact the system administrator.');
                }
            }
		});
    }
});