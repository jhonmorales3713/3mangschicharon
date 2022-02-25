$(function(){
  var base_url = $("body").data('base_url');
  // var token = $('#token').val();
  function prevImage(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('#imagePrev').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]); // convert to base64 string
    }

    return input.files[0].size;
  }

  function submit_form(form, btn){
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
  			  window.location.reload(true);
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

  $(document).on('change', '#imageUpload', function(){
    let error = 0
    const size = prevImage(this);
  });

  $(document).on('click', '.gender', function(){
    $('.gender').prop('checked', false);
    $(this).prop('checked', true);
  });

  $(document).on('submit', '#profile_form', function(e){
    e.preventDefault();
    submit_form($(this)[0], $(this).find('button[type=submit]')[0]);
  });
});
