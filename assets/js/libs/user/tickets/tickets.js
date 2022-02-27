$(function(){
    var base_url = $('body').data('base_url');

    $('#btn_check_ticket').click(function(){    
    
        $.ajax({
            url: base_url+'user/tickets/check_status',
            type: 'post',
            data: {
                ticket_num: $('#ticket_num').val(),
            },
            success: function(response){
                clearFormErrors();
                if(response.success){
                    if(response.data){
                        $('#ticket_num_text').text(response.data.ticket_num);
                        $('#full_name_text').text(response.data.full_name);
                        $('#event_date_text').text(response.data.event_date);
                        $('#total_amount_text').text(format_number(response.data.total_amount,2));
                        
                        if(response.data.status == 'Pending'){
                            $('#status_text').text(response.data.status);
                            $('#status_text').css('color','red');
                        }
                        else if(response.data.status == 'Approved'){
                            $('#status_text').text(response.data.status);
                            $('#status_text').css('color','green');
                            $('<small>Your booking has been approved. Please contact us immediately to discuss the contract</small><br>').insertAfter('#status_text');
                        }

                        
                        $('#ticket_exists').show();
                        $('#ticket_none').hide();
                    }
                    else{
                        $('#ticket_exists').hide();
                        $('#ticket_none').show();
                    }                    
                }
                else{                    
                    show_errors(response,$('#ticket_form'));                
                }
            },
            error: function(response){
    
            },
        });
    });
});
