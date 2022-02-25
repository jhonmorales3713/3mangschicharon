$(function(){
  var base_url = $("body").data('base_url');

  function forgot_pass(form, btn){
    showCover('Sending Link ...');
    let formData = new FormData(form);
    if(btn.dataset.hasOwnProperty('serviceurl') && btn.dataset.hasOwnProperty('serviceaccount')){
			console.log(btn);
			formData.append('serviceurl', btn.dataset.serviceurl);
			formData.append('serviceaccount', btn.dataset.serviceaccount);
		}

    $.ajax({
      url: form.action,
      type: 'post',
      data: formData,
      contentType: false,
      processData: false,
      cache: false,
      success: function(data){
        const json_data = JSON.parse(data);
        hideCover();
        if(json_data.success === true){
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
          $('#forgot_email').val('');
          // window.location = json_data.url;
        }else{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
          $('input[name='+json_data.csrf_name+']').val(json_data.csrf_hash);
        }
      },
      error: function(){
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again."
        });
        hideCover();
      }
    });
  }

  function reset_pass(form, btn){
    showCover('Updating Password ...');
    let formData = new FormData(form);
    if(btn.dataset.hasOwnProperty('serviceurl') && btn.dataset.hasOwnProperty('serviceaccount')){
			console.log(btn);
			formData.append('serviceurl', btn.dataset.serviceurl);
			formData.append('serviceaccount', btn.dataset.serviceaccount);
		}

    $.ajax({
      url: form.action,
      type: 'post',
      data: formData,
      contentType: false,
      processData: false,
      cache: false,
      success: function(data){
        const json_data = JSON.parse(data);
        hideCover();
        if(json_data.success === true){
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
          window.location = json_data.url;
        }else{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: json_data.message
          });
          $('input[name='+json_data.csrf_name+']').val(json_data.csrf_hash);
        }
      },
      error: function(){
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again."
        });
        hideCover();
      }
    });
  }

  function validateEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  // var token = $('#token').val();

  $(document).on('submit', '#forgot_form', function(e){
    e.preventDefault();
    forgot_pass($(this)[0], $(this).find('button[type=submit]')[0]);
  });

  $(document).on('click', '#btn_forgot_pass', function(){
    $('.login').hide();
    $('.forgot_pass').show();
  });

  $(document).on('click', '#btn_goback_from_forgotpass', function(){
    $('.forgot_pass').hide();
    $('.login').show();
  });

  $(document).on('keyup', '#forgot_email', function(){
    if($(this).val() != ''){
      (validateEmail($(this).val()))
      ? $('#btn_send_link').prop('disabled', false)
      : $('#btn_send_link').prop('disabled', true);
    }else{
      $('#btn_send_link').prop('disabled', true);
    }
  });

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

  $(document).on('submit', '#reset_form', function(e){
    e.preventDefault();
    reset_pass($(this)[0], $(this).find('button[type=submit]')[0]);
  });
});
