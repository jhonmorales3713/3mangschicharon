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
            <span class="badge badge-primary float-right" id="edit_profile_btn">EDIT PROFILE</span>                        
            <?php if($customer_data['profile_img'] != ''){ ?>
                <div class="profile-img" style="background-image: url(<?= base_url('uploads/profile_img/'.$customer_data['profile_img']); ?>);"></div>
            <?php } else { ?>
                <div class="profile-img" style="background-image: url(<?= base_url('assets/img/profile_default.png');?>"></div>
            <?php }?>            
            <b>Full Name: </b><span><?= $customer_data['full_name'] ?></span><br>
            <b>Email Address: </b><span><?= $customer_data['email'] ?></span><br>
            <b>Mobile Number: </b><span><?= $customer_data['mobile'] ?></span><br>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12"></div>        
        <div class="col-lg-6 col-md-6 col-sm-12 mt20 clearfix">
            <span class="badge badge-primary mt5 float-right" id="add_address_btn">ADD ADDRESS</span>
            <h5>My Addresses</h5>   
            <?php if($shipping_addresses){ ?>
                <div class="p10" style="overflow-y:scroll; width: 100%; max-height: 300px !important;"> 
                <?php foreach($shipping_addresses as $address){ ?>
                    <div class="mt10 clearfix p10 rounded border <?= $address['address_category_id'] > 1 ? 'border-warning' : 'border-success' ?>">
                        <span class="badge badge-primary float-right edit-address" data-en_id="<?= en_dec('en',$address['id']); ?>">Edit</span>
                        <span class="badge <?= $address['address_category_id'] > 1 ? 'badge-warning' : 'badge-success' ?>"><?= $address['address_type'] ?></span> 
                        <b><?= $address['contact_person']; ?></b><br>
                        <?= $address['address'].' '.$address['barangay'].' '.$address['city'].', '.$address['province'].' ('.$address['zip_code'].')'; ?>
                        <b><?= $address['contact_no']; ?></b>
                    </div>
                <?php } ?>
                </div>
            <?php } else {?>
                <div>
                    <center>
                        No saved addrress
                    </center>
                </div>
            <?php } ?>
        </div>
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
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="submit_profile_btn">Submit</button>
        </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="address_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="address_title">Address</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">            
            <input type="hidden" id="address_id">
            <?php $this->load->view('user/cart/address_form'); ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="submit_address_btn">Save</button>
            <button type="button" class="btn btn-success" id="update_address_btn">Update</button>
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

    $('#add_address_btn').click(function(){
        $('#submit_address_btn').show();
        $('#update_address_btn').hide();
        reset_address_form();
        $('#address_title').text('New Address');
        $('#address_modal').modal('show');
    });

    function get_shipping_details(){
        var data = {
            address_category_id: $('#address_category_id').val(),
            address_alias: $('#alias').val(),
            contact_person: $('#address_form #full_name').val(),
            contact_no: $('#contact_no').val(),            
            province: $('#province').val(),
            city: $('#city').val(),
            barangay: $('#barangay').val(),        
            zip_code: $('#zip_code').val(),
            address: $('#address').val(),    
            notes: $('#notes').val()
        }   
        return data; 
    }

    $('#submit_address_btn').click(function(e){        
        e.preventDefault();        
        $.ajax({
            url: base_url+'user/account/save_address',
            type: 'post',
            data: get_shipping_details(),
            success: function(response){
                clearFormErrors();
                if(response.success){
                    sys_toast_success(response.message);
                    $('#address_modal').modal('hide');
                    setTimeout(function(){
                        window.location.reload();
                    },1000);                    
                }
                else{
                    show_errors(response,$('#address_modal'));                    
                }
            },
            error: function(response){

            }           
        });
    });

    $(document).delegate('.edit-address','click',function(){
        $('#submit_address_btn').hide();
        $('#update_address_btn').show();
        $('#address_title').text('Edit Address');
        var address_id = $(this).data('en_id'); 
        $('#address_id').val(address_id);
        get_address_data(address_id);
    });

    $('#update_address_btn').click(function(e){        
        e.preventDefault();        
        shipping_data = get_shipping_details();
        shipping_data.address_id = $('#address_id').val();
        $.ajax({
            url: base_url+'user/account/update_address',
            type: 'post',
            data: shipping_data,
            success: function(response){
                clearFormErrors();
                if(response.success){
                    sys_toast_success(response.message);
                    $('#address_modal').modal('hide');
                    setTimeout(function(){
                        window.location.reload();
                    },1000);                    
                }
                else{
                    show_errors(response,$('#address_modal'));                    
                }
            },
            error: function(response){

            }           
        });
    });

    function get_address_data(address_id){
        $.ajax({
            url: base_url+'user/account/get_address',
            type: 'post',
            data: {
                address_id: address_id,
            },
            success: function(response){                
                if(response.success){                    
                    set_address_data(response.address_data);
                }                
            },
            error: function(response){

            }           
        });
    }

    function set_address_data(address_data){
        console.log(address_data);
        $('#address_modal #address_category_id').val(address_data.address_category_id);
        $('#address_modal #address').val(address_data.address);
        $('#address_modal #full_name').val(address_data.contact_person);
        $('#address_modal #contact_no').val(address_data.contact_no);
        $('#address_modal #address_alias').val(address_data.address_alias);
        $('#address_modal #province').val(address_data.province);
        $('#address_modal #city').val(address_data.city);
        $('#address_modal #barangay').val(address_data.barangay);
        $('#address_modal #zip_code').val(address_data.zip_code);
        $('#notes').text(address_data.notes);
        $('#address_modal').modal('show');
    }

    function reset_address_form(){
        $('#address_modal').find('').val('');
        $('#address_modal').find('textarea').text('');
        $('#address_modal').find('select').val('').trigger('change');
    }
</script>
