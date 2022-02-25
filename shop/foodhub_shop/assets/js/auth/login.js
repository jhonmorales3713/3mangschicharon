var base_url = $("body").data("base_url");

$(document).ready(function(){

	//initiate login
	function login(form, btn)
	{
		showCover("Authenticating...");
		var formData = new FormData(form);
		if(btn.dataset.hasOwnProperty('serviceurl') && btn.dataset.hasOwnProperty('serviceaccount')){
			console.log(btn);
			formData.append('serviceurl', btn.dataset.serviceurl);
			formData.append('serviceaccount', btn.dataset.serviceaccount);
		}
		$.ajax({
  		type: 'post',
  		url: form.action,
  		data: formData,
  		contentType: false,
			cache: false,
			processData:false,
  		success:function(data){
  			var json_data = JSON.parse(data);
  			// sys_log(json_data.environment,json_data);
  			hideCover();

  			if(json_data.success==false)
  			{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
  				$('input[name='+json_data.csrf_name+']').val(json_data.csrf_hash);
  			}
  			else
  			{
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
  				window.location = json_data.url;
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

	$(".toggle-password-icon").click(function (e) {
		e.preventDefault();
		var i = $(this).find('.toggle-password *[data-target]');
		var target = i.data('target');
        if ($(target).attr('type') == "text") {
            $(i).removeClass('fa-eye')
            $(i).addClass('fa-eye-slash')
            $(target).attr('type', 'password')
        } else {
            $(i).removeClass('fa-eye-slash')
            $(i).addClass('fa-eye')
            $(target).attr('type', 'text')
        }
    });

	$("#jcBtn").click(function (e) {
		e.preventDefault();
    	$('#modal_jc').modal();
    });


	$("#guestBtn").click(function (e) {
		e.preventDefault();
		window.location = base_url;
    });

	$('#login_form').submit(function(e){
		e.preventDefault();
		login($(this)[0], $(this).find('button[type=submit]')[0]);
	});

	$('#login_form_jc').submit(function(e){
		e.preventDefault();
		login($(this)[0], $(this).find('button[type=submit]')[0]);
	});

	// social media login

	$(document).on('click', '#fbBtn', function(){
		fb_login();
	});

});
