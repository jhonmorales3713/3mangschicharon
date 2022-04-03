$(function(){
    var target_img='';
    var source_id='';
    $('.custom-file-input').click(function(){
        source_id = "#"+$(this)[0].id;
        target_img = "#"+$(source_id).data('target');
        checker_img = "#"+$(source_id).data('checker');
        init();
    });
    function init(){
        $(source_id).change(function() {
            var fileInput = $(this);
            var input = this;
            var file = fileInput[0].files[0];
            try{
                // Ensure it's an image
                if(file.type.match(/image.*/)) {
                    $(checker_img).val($(source_id).val());
                    // Load the image
                    var reader = new FileReader();
                    reader.onload = function (readerEvent) {
                        $(target_img).attr('src', readerEvent.target.result);
                        $(target_img).show();
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
    }

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
    
    $("#btnSubmit").click(function(){
        var form = $("form[name=web_info]");
        var form_data = new FormData(form[0]);
        var form_data2 = new FormData(form[1]);
        var form_data3 = new FormData(form[2]);
        for (var pair of form_data2.entries()) {
            form_data.append(pair[0], pair[1]);
        }
        for (var pair of form_data3.entries()) {
            form_data.append(pair[0], pair[1]);
        }
        $.LoadingOverlay("show");
        $.ajax({
            type: 'post',
            url: base_url+'admin/settings/website_info/update',
            data: form_data,
            contentType: false,   
            cache: false,      
            processData:false,
            success:function(data){
                $.LoadingOverlay("hide");
                var json_data = JSON.parse(data);
                
                if(json_data.success) {
                    //sys_toast_success(json_data.message);
                    sys_toast_success(json_data.message);
                    //window.location.assign(base_url+"admin/Main_products/update_products/"+token+"/"+json_data.product_id);
                }else{
                    //sys_toast_warning(json_data.message);
                    sys_toast_warning(json_data.message[0]);
                }
            },
            error: function(error){
                sys_toast_warning(json_data.message);
                //showCpToast("warning", "Warning!", json_data.message);
            }
        });
    });
	$('#avatar-placeholder').click(function(){
		$('#main_logo').val('');
		$('#main_logo').click();
    });
    
    imageBlob = '';
	$(document).on("imageResized", function (event) {
	    if (event.blob && event.url) {
	        imageBlob = event.blob;
	    }
	});
    
});