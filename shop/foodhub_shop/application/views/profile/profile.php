<link rel="stylesheet" href="<?=base_url("assets/css/profile/profile.css")?>">
<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .sidebar-item.sidebar-item-0 {
        color: #222;
        border-right: 4px solid var(--primary-color);
        font-weight: 700;
    }

    .sidebar-item.sidebar-item-0 i {
        color: var(--primary-color);
    }

    @media only screen and (max-width: 767px) {
            .sidebar-item.sidebar-item-0 {
            color: #fff;
            border-right: none;
        }
    }

</style>

<div class="shop-container profile-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <?php include 'sidebar.php'?>
            </div>
            <div class="col-12 col-md-9">
              <?php
                $url = base_url('profile/Customer_profile/update_profile');
  					    echo form_open($url,array('id'=>'profile_form','method'=>'post'));
              ?>
                <div class="row flex-md-row-reverse">
                    <!-- <div class="col-12 col-md-4 mb-5">
                        <div class="row justify-content-center">
                            <div class="col-8">
                                <img src="<?=base_url("assets/img/user-avatar.png")?>"  id = "imagePrev" alt="" class="profile-image">
                                <div class="form-group">
                                    <input type="file" class="form-control-file" name = "imageUpload" id="imageUpload" accept = "image/*">
                                </div>
                                <div>
                                    <small class="text-gray">Max file size: <b>1mb</b></small>
                                </div>
                                <div>
                                    <small class="text-gray">File extension: <b>.jpg, .png</b></small>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="col">
                        <div class="form-group row">
                          <div class="col-lg-6 mb-3">
                            <label for="First Name">First Name</label>
                            <input type="text" class="form-control" name="fname" id="fname" value="<?=$user->first_name?>">
                          </div>
                          <div class="col-lg-6">
                            <label for="Last Name">Last Name</label>
                            <input type="text" class="form-control" name="lname" id="lname" value="<?=$user->last_name?>">
                          </div>
                        </div>
                        <div class="form-group row">
                          <div class="col-md-6 mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" name = "email" id="email" value="<?=$user->email?>">
                          </div>
                          <div class="col-md-3 mb-3">
                            <label for="Contact Number">Contact Number</label>
                            <input type="number" class="form-control contactNumber" name = "conno" id="conno" value="<?=$user->conno?>">
                          </div>
                          <div class="col-md-3 mb-3">
                            <label for="Date of Birth">Date of Birth</label>
                            <input type="date" class="form-control" id="birthday" name = "birthday" value = "<?=$user->birthdate?>">
                          </div>
                          <div class="col-md-12 mb-3">
                            <div class="row">
                              <div class="col-12">
                                  <label for="Gender">Gender</label>
                              </div>
                              <div class="col-auto">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input gender" id="maleGender" name = "gender" <?=($user->gender == "M") ? "checked" : ""?> value = "M">
                                      <label class="form-check-label" for="maleGender">Male</label>
                                  </div>
                              </div>
                              <div class="col-auto">
                                  <div class="form-check">
                                      <input type="checkbox" class="form-check-input gender" id="femaleGender" name = "gender" <?=($user->gender == "F") ? "checked" : ""?> value = "F">
                                      <label class="form-check-label" for="femaleGender">Female</label>
                                  </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group text-right">
                            <button type = "submit" class="btn portal-primary-btn">Save</button>
                        </div>
                    </div>
                </div>
              </form>
            </div>
        </div>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
<script src="<?=base_url('assets\js\profile\jquery.alphanum.js');?>"></script>
<script src="<?=base_url('assets\js\profile\profile_helper.js');?>"></script>
<script src="<?=base_url('assets\js\profile\profile.js');?>"></script>
