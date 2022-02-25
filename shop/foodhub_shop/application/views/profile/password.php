<link rel="stylesheet" href="<?=base_url("assets/css/profile/profile.css")?>">
<style>
    main {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .sidebar-item.sidebar-item-2 {
        color: #222;
        border-right: 4px solid var(--primary-color);
        font-weight: 700;
    }

    .sidebar-item.sidebar-item-2 i {
        color: var(--primary-color);
    }

    @media only screen and (max-width: 767px) {
            .sidebar-item.sidebar-item-2 {
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
                <div class="profile-page-header">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="profile-title">Change Password</h5>
                        </div>
                    </div>
                </div>
                <div class="profile-page-body">
                    <?php
                      $url = base_url('profile/Customer_profile/update_password');
                      echo form_open($url,array('id'=>'update_pass_form','method'=>'post'));
                    ?>
                      <div class="row justify-content-center">
                          <div class="col-12 col-md-8 col-lg-6">
                            <div class="form-group">
                              <label for="password">Current Password</label>
                              <div class="input-group">
                                  <input type="password" name="current_pass" id="current_pass" class="form-control password" autocomplete="off" required>
                                  <div class="input-group-append toggle-password-icon">
                                    <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
                                  </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="password">New Password</label>
                              <div class="input-group">
                                  <input type="password" name="new_pass" id="new_pass" class="form-control password" placeholder="Minimum of 8 characters" autocomplete="off" required>
                                  <div class="input-group-append toggle-password-icon">
                                    <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
                                  </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="password">Confirm New Password</label>
                              <div class="input-group">
                                  <input type="password" name="con_new_pass" id="con_new_pass" class="form-control password" placeholder="Minimum of 8 characters"  autocomplete="off" required>
                                  <div class="input-group-append toggle-password-icon">
                                    <span class="input-group-text bg-white toggle-password px-3"><i class="fa fa-eye-slash no-margin" data-target="#password"></i></span>
                                  </div>
                              </div>
                            </div>
                            <div class="form-group text-right">
                                <button type = "submit" class="btn portal-primary-btn">Save Password</button>
                            </div>
                          </div>
                      </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php  $this->load->view("includes/footer"); ?>
<script src="<?=base_url('assets/js/app/shop/shop-052620.js');?>"></script>
<script src="<?=base_url('assets\js\profile\password.js');?>"></script>
