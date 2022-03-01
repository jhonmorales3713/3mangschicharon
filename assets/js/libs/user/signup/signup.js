var base_url = $('body').data('base_url');

$('#btn_signup').click(function(){
    $.ajax({
        url: base_url + 'signup',
        type: 'POST',
        data: {
            email: $('#email').val(),
            full_name: $('#full_name').val(),
            mobile: $('#mobile').val()
        },
        success: function(response){
            if(response.success){
                console.log(user_id);
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