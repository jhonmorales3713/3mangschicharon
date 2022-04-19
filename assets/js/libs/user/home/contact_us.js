$(function(){

    $('#send_message').click(function(){
        $.ajax({
            url: base_url + 'home/send_message',
            type: 'POST',
            data: {
                name: $('#name').val(),
                email: $('#email').val(),
                message: $('#message').val(),
            },
            success: function(response){
                clearFormErrors();
                if(response.success){
                    sys_toast_success(response.message);      
                    $('#message_form').hide();
                    $('#sent_limit').hide();              
                    $('#message_success').show();                        
                }
                else{                  
                    if(typeof response.limit !== 'undefined'){
                        $('#message_form').hide();
                        $('#message_success').hide();
                        $('#sent_limit').show();
                    }  
                    else{
                        show_errors(response,$('#contact_us_form'));
                    }                    
                }
            },
            error: function(){

            }
        })
    });


});