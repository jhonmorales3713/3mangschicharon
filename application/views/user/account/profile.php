<div class="container">
    <div class="row">        
        <?php if($_SESSION['is_verified'] == 0){ ?>
            <?php if($has_docs){ ?>
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        <span>Document has been <b>submitted</b></span><br>
                        <span>An email will be sent to you once we are done with the verification process</span><br>
                    </div>
                </div>
            <?php } else {?>
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        <span>Your account is not <b>Verified</b></span><br>
                        <span>Please verify your account to avail of discount and other offers.</span><br>
                        <small>Click </small><a href="<?= base_url('account/verification/') ?>">here </a><small>to verify your account.</small>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <div class="col-lg-6 col-md-6 col-sm-12 clearfix">
            <h5>My Profile</h5>
            <span class="badge badge-pill badge-primary float-right" id="edit_profile_btn">EDIT PROFILE</span>                        
            <?php if($customer_data['profile_img'] != ''){ ?>
                <div class="profile-img" style="background-image: url(<?= base_url('uploads/profile_img/'.$customer_data['profile_img']); ?>);"></div>
            <?php } else { ?>
                <div class="profile-img" style="background-image: url(<?= base_url('assets/img/profile_default.png');?>"></div>
            <?php }?>            
            <b>Full Name: </b><span><?= $customer_data['full_name'] ?></span><br>
            <b>Email Address: </b><span><?= $customer_data['email'] ?></span><br>
            <b>Mobile Number: </b><span><?= $customer_data['mobile'] ?></span><br>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 clearfix"></div>        
        <!-- <div class="col-lg-6 col-md-6 col-sm-12 clearfix mt20">
            <h5>My Addresses</h5>
            <span class="badge badge-pill badge-primary float-right" id="edit_profile_btn">VIEW ADDRESSES</span>
        </div> -->
    </div>
</div>

<!-- Profile Info Modal -->
<div class="modal fade" id="profile_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Edit Profile</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">                                
            <?php $this->load->view('user/account/user_profile_form'); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="submit_profile_btn">Submit</button>
        </div>
        </div>
    </div>
</div>

<script>
    //file input image preview
    $('#profile_img').on('change', function(){        
        var reader = new FileReader();
        reader.onload = function(){            
            $('.image-preview').prop('src',reader.result);
        };
        reader.readAsDataURL($(this).prop('files')[0]);
    });


    $('#edit_profile_btn').click(function(){
        $('#profile_modal').modal('show');
    });    

    function get_form_data(){        
        var form_data = new FormData();                
        form_data.append('full_name',$('#full_name').val());
        form_data.append('email',$('#email').val());
        form_data.append('mobile',$('#mobile').val());
        form_data.append('profile_img',$('#profile_img').prop('files')[0]);             
        return form_data;    
    }

    $('#submit_profile_btn').click(function(e){        
        e.preventDefault();        
        $.ajax({
            url: base_url+'user/account/update_profile',
            type: 'post',
            data: get_form_data(),           
            success: function(response){
                clearFormErrors();
                if(response.success){
                    sys_toast_success(response.message);
                    $('#profile_modal').modal('hide');
                    setTimeout(function(){
                        window.location.reload();
                    },1000);                    
                }
                else{
                    show_errors(response,$('#profile_form'));                    
                }
            },
            error: function(response){

            },
            processData: false,
            contentType: false,
        });
    });
</script>
