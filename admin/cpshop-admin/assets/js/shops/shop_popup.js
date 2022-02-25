var ini     = $("body").data('ini');


// Pop up image

var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
var edit_loadstate = false;
    $('#entry-form').submit(function(e){
        e.preventDefault();

        var form = $(this);
        var form_data = new FormData(form[0]);
        form_data.append('file_container', get_img_file('file_container'));

        if(checkInputs("#entry-form") == 0){
            $.ajax({
                type:'post',
                url: base_url+'Shops/save_popup_image',
                data: form_data,
                processData: false,
                contentType: false,
                beforeSend:function(data){
                    $.LoadingOverlay("show");
                    $(".btn-save").prop('disabled', true); 
                    $(".btn-save").text("Please wait...");
                },
                success:function(data){
                    $.LoadingOverlay("hide");
                    $(".btn-save").prop('disabled', false); 
                    $(".btn-save").text("Save");
                    data = jQuery.parseJSON(data)

                    if (data.success == 1 || data.success == true || data.sucess == 'true') {
                        
                        // messageBox(data.message, 'Success', 'success');
                        showCpToast("success", "Success!", data.message);
                        setTimeout(function(){location.reload()}, 2000);

                    }else{
                        //messageBox(data.message, 'Warning', 'warning');
                        showCpToast("warning", "Warning!", data.message);
                    }
                }
            });
        }
    });


//////////////////////////////////////////////////
    var renderImages = (input, placeToInsertImagePreview) => {
        if (input.files) {  
            var filesAmount = input.files.length;
            if(input.files.length){
                //User choose a picture
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    var image_size = input.files[i].size;
                    var current_file = input.files[i];
                    
                                                //must be jpg/png type, and not greater than 2mb
                    if(!hasExtension(input.value,['.jpg', '.png','.JPG','.PNG','.jpeg','.JPEG'])){
                        //messageBox('Invalid file type, Only JPG/PNG are allowed', 'Warning', 'warning');
                        showCpToast("warning", "Warning!", 'Invalid file type, Only JPG/PNG are allowed');
                        $('#file_container').val("");
                        $('#file_description').text('Choose file');
                    }else{
                        if(image_size > 2000000){
                            //messageBox('Please enter with a valid size no larger than 3MB', 'Warning', 'warning');
                            showCpToast("warning", "Warning!", 'Please enter with a valid size no larger than 3MB');
                            uploadthumbnail('imgthumbnail-logo', 'show');
                            $('.img_preview_container').hide('slow');
                            $('#file_container').val("");
                            $('#file_description').text('Choose file');
                        }else{                        
                            reader.onload = function(event) {
                                $($.parseHTML('<img id="product_preview" style="max-width: 100%;max-height: 100%;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#file_description').text(filesAmount+' Attached Image(s)');
                            $('.img_preview_container').show('slow');
                        }
                    }
                }
            }else{
                //User clicked cancel
                uploadthumbnail('imgthumbnail-logo', 'show');
                $('.img_preview_container').hide('slow');
                $('#file_description').text('Choose file');
            }
        };
    }

    $('#file_container').on('change', function() {
        //Note: 2 kinds of container 1 the preview container (img_preview_container) 2 is the file input/file container (file_container)
        countFiles = $(this)[0].files.length;
        prep_files(countFiles, this);//param 1 file count, param 2 the file container/input
    });

    function prep_files(countFiles, file_container){
        $.LoadingOverlay("show");
        uploadthumbnail('imgthumbnail-logo', 'remove');
    	$( ".img_preview_container" ).empty();
        renderImages(file_container, 'div.img_preview_container');
        //$('#main_logo_checker').val('true');
        $.LoadingOverlay("hide");
    }

    function hasExtension(file, exts) {
        var fileName = file;
        return (new RegExp('(' + exts.join('|').replace(/\./g, '\\.') + ')$')).test(fileName);
    }

    function get_img_file(element_id){
        var fileInputs = $('#'+element_id);
        console.log(fileInputs[0].files);
        return fileInputs[0].files;
    }

    function uploadthumbnail(imagetoclear, action=''){
        if(action == 'show'){
            $("#"+imagetoclear).attr('hidden', false);
        }else{
            $("#"+imagetoclear).attr('hidden', true);
        }
    }

    $(document).delegate('.commcapping','input',function(e){
		var self = $(this);
		if (self.val() > 30 || self.val() < 0) 
		{
            self.val('');
		}
	});



   
