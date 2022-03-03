$(function(){


var base_url = $('body').data('base_url');

$('#btn_login').click(function(){
    $.ajax({
        url: base_url + 'signin',
        type: 'POST',
        data: {
            login_email: $('#login_email').val(),            
            login_password: $('#login_password').val(),
        },
        success: function(response){            
            if(response.success){
                sys_toast_success(response.message);
                window.location.href = base_url;
            }
            else{
                clearFormErrors();
                show_errors(response,$('#login_form'));
            }
        },
        error: function(){

        }
    });
});

});