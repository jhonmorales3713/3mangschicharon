var has_docs = $('#has_docs').val();
    if(has_docs || has_docs == 'true'){
        $('#upload_form').hide();
        $('#verifying_note').show();
    }
    else{
        $('#upload_form').show();
        $('#verifying_note').hide();
    }
    //file input image preview
    $('.file-input').on('change', function(){
        var className = $(this).attr('id') + '-preview';
        var reader = new FileReader();
        reader.onload = function(){            
            $('img.'+className).prop('src',reader.result);
        };
        reader.readAsDataURL($(this).prop('files')[0]);
    });
    
    function get_form_data(){        
        var form_data = new FormData();                
        form_data.append('doc_type',$('#doc_type').val());
        form_data.append('image',$('#image').prop('files')[0]);             
        return form_data;    
    }

    $('#upload_btn').click(function(e){        
        e.preventDefault();        
        $.ajax({
            url: base_url+'user/account/upload_document',
            type: 'post',
            data: get_form_data(),           
            success: function(response){
                clearFormErrors();
                if(response.success){
                    $('#upload_form').hide();
                    $('#verifying_note').show();
                }
                else{
                    show_errors(response,$('#upload_form'));                    
                }
            },
            error: function(response){

            },
            processData: false,
            contentType: false,
        });
    });