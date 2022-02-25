$(function(){
	var base_url = $("body").data('base_url'); //base_url came from built-in CI function base_url();
	
	avatar_file = ""
	cropper = ""
	image = ""
	
	$("#avatar_file").change(function(){
		readURL(this, $("#avatar_file_crop"))
		$.LoadingOverlay('show')

		setTimeout(function(){
			image = document.getElementById('avatar_file_crop')
			cropper = new Cropper(image, {
				viewMode: 1,
				aspectRatio: 1 / 1,
				cropBoxResizable: false,
				dragMode: 'move',
				preview: '.avatar_file_view'
			})
			
			cropper.setCropBoxData('{"left":283.875,"top":16.46875,"width":320,"height":320}')
			$.LoadingOverlay('hide')
		}, 1500)

		$('#cropModal').modal('show')
	})

	$('#btnCrop').on('click', function(){
		result = cropper.getCroppedCanvas({
			width: 320,
			height: 320,
			minWidth: 320,
			minHeight: 320,
			maxWidth: 4096,
			maxHeight: 4096,
			fillColor: '#fff',
			imageSmoothingEnabled: true,
			imageSmoothingQuality: 'high',
		})

		$('#crop_container').html(result)
		$('#cropModal').modal('hide')
	})

	//File Upload profile pic
    $("#saveChangeAvatarForm").submit(function(e){
		e.preventDefault();
		
		check_required_fields('#saveChangeAvatarForm .required')
		
		var avatar = $("#avatar_file").val();

		if(avatar){
			image = $('canvas')[0].toDataURL('image/png')
			base64ImageContent = image.replace(/^data:image\/(png|jpg);base64,/, "")
			blob = base64ToBlob(base64ImageContent, 'image/png')
		} else{
			blob = '';
		}
	
        $("#saveChangeAvatarForm").each(function(){
            if ($(this).find('.error').length > 0) {
                //toastMessage('Note', 'Please input appropriate values to all required fields.', 'error')
                showCpToast("error", "Note!", 'Please input appropriate values to all required fields.');
                checker = 0
            }else {
				formData = new FormData(this);

				formData.append('picture', blob)
				formData.append('first_name', $("#first_name").val());
				formData.append('middle_name', $("#middle_name").val());
				formData.append('last_name', $("#last_name").val());
				formData.append('mobile_no', $("#mobile_no").val());
				$.LoadingOverlay("show");
				$.ajax({
					type:'post',
					url:base_url+'Main_profile_settings/save_changeavatar',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					success:function(data){
						if (data.success == 1) {
							$.LoadingOverlay("hide");
							// toastMessage('Success', data.message, 'success')
							
							// setTimeout(function(){
							// 	location.reload()
							// }, 500)

							showCpToast("success", "Success!", data.message);
							setTimeout(function(){location.reload()}, 2000);
						}
						else {
							//toastMessage('Note', data.message, 'error');
							showCpToast("error", "Note!", data.message);
						}
					}
				});
            }
		});
    });
});