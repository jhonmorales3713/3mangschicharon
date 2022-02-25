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
          $('.password').val('');
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

  $(document).on('click', '.toggle-password-icon', function(){
    const pass = $(this).parent().children('.password');
    if(pass.attr('type') == "password"){
      pass.prop('type', 'text');
      $(this).children('span').children('i').removeClass('fa-eye-slash');
      $(this).children('span').children('i').addClass('fa-eye');
    }else{
      pass.prop('type', 'password');
      $(this).children('span').children('i').removeClass('fa-eye');
      $(this).children('span').children('i').addClass('fa-eye-slash');
    }
  });

  $(document).on('submit', '#update_pass_form', function(e){
    e.preventDefault();
    submit_form($(this)[0], $(this).find('button[type=submit]')[0]);
  });
});
