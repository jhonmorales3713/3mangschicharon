<div class="container">
    <div class="row" id="upload_form">        
        <div class="col-lg-7 col-md-7 col-sm-12">                
            <div class="col-12 p10">
                <div class="alert alert-info" style="height: 100%;">
                    Upload valid Government IDs:
                </div>
            </div>      
            <div class="col-12 p10">
                <div class="form-group">                    
                    <select id="doc_type" class="form-control">
                        <option value="">Select ID type</option>
                        <option value="SSS">SSS</option>
                        <option value="UMID">UMID</option>
                        <option value="Drivers License">Drivers License</option>
                        <option value="others">Others</option>
                    </select>
                </div>
            </div>            
            <div class="col-12 p10">                                
                <div class="input-group" id="id_pic">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input file-input" id="image" accept="image/png, image/gif, image/jpeg">
                        <label class="custom-file-label" for="image">Upload image here</label>
                    </div>                
                </div>
            </div>
            <div class="col-12 p10 clearfix">
                <button type="button" class="btn btn-success float-right" id="upload_btn">SUBMIT</button>
            </div>
        </div>   
        <div class="col-lg-5 col-md-5 col-sm-12">
            <div class="col-12 p10">
                <div class="form-group">
                    <label class="form-control-label">Upload preview: </label><br>
                    <img src="" alt="" class="image-preview" width="90%">                                    
                </div>
            </div>   
        </div>     
    </div>
    <div class="row" id="verifying_note">
        <div class="col-12">
        <center>
            <strong class="text-success">Document has been submitted</strong><br>
            <p>Please give us <b>24-48 hrs</b> to verify your document</p>
            <p>Thank you.</p>
        </center>
        </div>
    </div>
    <input type="hidden" id="has_docs" value="<?= isset($has_docs) ? $has_docs : false; ?>">
</div>
<script>
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
</script>