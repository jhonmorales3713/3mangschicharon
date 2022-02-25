$(function(){
  var base_url = $("body").data('base_url');
  var email_error = 0;
  // var token = $('#token').val();

  function register(form, btn){
    showCover('Saving...');
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

  $(document).on('submit', '.reg_form', function(e){
    e.preventDefault();
  });

  $(document).on('keyup', '#email', function(){
    const email = $('#email').val();
    if(email != ""){
      $.ajax({
        url: base_url+'auth/authentication/validate_email',
        type: 'post',
        data:{email},
        cache: false,
        dataType: 'json',
        beforeSend: function(){
          // showCover('Email Checking ...');
        },
        success: function(data){
          // hideCover();
          if(data.success == true){
            $('.email-error-msg').html(`<span class="text-success">${data.message}</span>`);
            $('#btn-getemail-code').prop('disabled', false);
          }else{
            $('.email-error-msg').html(`<span class="text-danger">${data.message}</span>`);
            $('#btn-getemail-code').prop('disabled', true);
          }
        },
        error: function(){
          showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: 'Something went wrong. Please try again'
          });
          // hideCover();
        }
      });

    }else{
      $('.email-error-msg').html('');
    }
  });

  $(document).on('click', '#btn-getemail-code', function(){
    const email = $('#email').val();
    const fname = $('#fname').val();
    const lname = $('#lname').val();

    $(this).text('Sending Email Code ...');
    $.ajax({
      url: base_url+'auth/authentication/send_email_code',
      type: 'post',
      data:{email, fname, lname},
      dataType: 'json',
      beforeSend: function(){
        showCover('Sending Email Code ...');
      },
      success: function(data){
        hideCover();
        if(data.success == true){
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
          $('.email-error-msg').html('');
          $('.get-email-code-btn-wrapper').hide();
          $('.verification-box').show();
          let count = 60;
          let resend = setInterval(() => {
            $('.count_resend').text(`(${count})`);
            if(count > 0){
              count--;
            }else{
              $('#resend').addClass('text-info');
              $('#resend').data("disabled", "0");
              $('#resend').css('text-decoration', 'underline');
              $('.count_resend').html('');
              clearInterval(resend);
            }

          },1000);
        }else{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
        }
      },
      error: function(){
        showToast({
            type: "error",
            css: "toast-top-full-width mt-5",
            msg: "Something went wrong. Please try again"
        });
      }
    });
  });

  $(document).on('click', '#resend', function(){
    console.log($(this).data('disabled'));
    if($(this).data('disabled') == "1"){
      return;
    }
    const email = $('#email').val();
    const fname = $('#fname').val();
    const lname = $('#lname').val();
    $.ajax({
      url: base_url+'auth/authentication/resend_email_code',
      type: 'post',
      data:{email, fname, lname},
      dataType: 'json',
      cache: false,
      beforeSend: function(){
        showCover('Sending Email Code ...');
        $('#resend').data("disabled", "1");
      },
      success: function(data){
        hideCover();
        if(data.success === true){
          showToast({
              type: "success",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
          $('#resend').data("disabled", "1");
          $('#resend').removeClass('text-info');
          $('#resend').addClass('text-default');
          $('#resend').css('text-decoration', 'none');
          let count = 60;
          let resend = setInterval(() => {
            $('.count_resend').text(`(${count})`);
            if(count > 0){
              count--;
            }else{
              $('#resend').addClass('text-info');
              $('#resend').data('disabled', "0");
              $('#resend').css('text-decoration', 'underline');
              $('.count_resend').text('');
              clearInterval(resend);
            }

          },1000);
        }else{
          showToast({
              type: "warning",
              css: "toast-top-full-width mt-5",
              msg: data.message
          });
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
  });

  $(document).on('click', '.show-password', function(){
    const type = $('#password').attr('type');
    if(type == "password"){
      $('#password').prop('type', 'text');
      $(this).removeClass('fa-eye-slash');
      $(this).addClass('fa-eye');
    }else{
      $('#password').prop('type', 'password');
      $(this).removeClass('fa-eye');
      $(this).addClass('fa-eye-slash');
    }
  });

  $(document).on('submit', '#reg_form', function(e){
		e.preventDefault();
		register($(this)[0], $(this).find('button[type=submit]')[0]);
	});
});
