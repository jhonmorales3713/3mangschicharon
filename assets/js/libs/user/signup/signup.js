$(function(){



var base_url = $('body').data('base_url');

$('#btn_signup').click(function(){
    $.ajax({
        url: base_url + 'signup',
        type: 'POST',
        data: {
            email: $('#email').val(),
            full_name: $('#full_name').val(),
            mobile: $('#mobile').val(),
            password: $('#password').val(),
            password2: $('#password2').val(),
        },
        success: function(response){            
            if(response.success){
                sys_toast_success(response.message);
                window.location.assign(base_url+"login/");
            }
            else{
                clearFormErrors();
                show_errors(response,$('.signup-form'));                
            }
        },
        error: function(){

        }
    });
});

$('#login_link').click(function(e){
    e.preventDefault();
    $('#signup_page').hide();
    $('#login_page').slideDown(300);
});


})