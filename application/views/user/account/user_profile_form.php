<div class="container">
    <div class="row" id="profile_form">
        <div class="col-lg-12 p20">
            <center>
                <?php if(isset($customer_data['profile_img'])){ ?>                    
                    <img class="image-preview" src="<?= base_url('uploads/profile_img/'.$customer_data['profile_img']); ?>" alt="" width="300px">
                <?php } else { ?>                    
                    <img class="image-preview" src="<?= base_url('assets/img/profile_default.png');?>" alt="" width="300px">
                <?php }?>  
            </center><br>
        </div>        
        <div class="col-lg-3 col-md-3 col-sm-3"></div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="profile_img" accept="image/png, image/gif, image/jpeg">
                    <label class="custom-file-label" for="profile_img">Upload Profile Image</label>                    
                </div>                
            </div>
        </div>
        <br><br>
        <div class="col-lg-3 col-md-3 col-sm-3"></div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label>Full Name <span class="text-red">*</span></label>
                <input type="text" id="full_name" class="form-control" value="<?= $customer_data['full_name'] ?>">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label>Email Address <span class="text-red">*</span></label>
                <input type="text" id="email" class="form-control" value="<?= $customer_data['email'] ?>">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
                <label>Mobile Number <span class="text-red">*</span></label>
                <input type="text" id="mobile" class="form-control" value="<?= $customer_data['mobile'] ?>" placeholder="09XXXXXXXXX">
            </div>
        </div>
    </div>
</div>