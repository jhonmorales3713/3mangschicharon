<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Developer Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Email Settings</li>
        </ol>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Details</h3>
        </div>

        <div class="card-body">
           <input type="hidden" id="email_settings_id" value="<?php echo $get_email_settings[0]['id'] ?>">

            <div class="form-group">
                <label for="c_paypanda_link_live">toktok mall | New Product Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="new_product_email" id="new_product_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['new_product_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="new_product_name" id="new_product_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['new_product_name'] ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="c_paypanda_link_live">  toktok mall | Product Approval Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="new_approval_email" id="new_approval_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['approval_product_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="new_approval_name" id="new_approval_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['approval_product_name'] ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="c_paypanda_link_live">toktok mall | Product Verification Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="new_verification_email" id="new_verification_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['verification_product_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="new_verification_name" id="new_verification_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['verification_product_name'] ?>">
                    </div>
                </div>
            </div>

            
            <div class="form-group">
                <label for="c_paypanda_link_live"> toktokmall | Shop Mcr Approval Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="shop_mcr_approval_email" id="shop_mcr_approval_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_approval_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="shop_mcr_approval_name" id="shop_mcr_approval_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_approval_name'] ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="c_paypanda_link_live"> toktokmall | Shop MCR Verify Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="shop_mcr_verification_email" id="shop_mcr_verification_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_verify_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="shop_mcr_verification_name" id="shop_mcr_verification_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_verify_name'] ?>">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="c_paypanda_link_live"> toktokmall | Shop MCR Verified Notification:</label>
                <div class="row">
                    <div class="col-md-6">
                         <small id="helpId" class="form-text text-muted">email</small>
                        <input type="link" class="form-control" name="shop_mcr_verified_email" id="shop_mcr_verified_email" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_verifed_email'] ?>">
                        
                    </div>
                    <div class="col-md-6">
                        <small id="helpId" class="form-text text-muted">name</small>
                        <input type="link" class="form-control" name="shop_mcr_verified_name" id="shop_mcr_verified_name" aria-describedby="helpId" placeholder="" value="<?php echo $get_email_settings[0]['shop_mcr_verifed_name'] ?>">
                    </div>
                </div>
            </div>

            <?php if(isset($this->loginstate->get_access()['email_settings']['update']) && $this->loginstate->get_access()['email_settings']['update'] == 1) : ?>
                <div class="buttons text-right">
                    <a type="button" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</a>
                    <button  class="btn btn-success saveBtn" id="saveBtn">Save</button>
                </div>
            <?php endif ?>
        </div>
        </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/update_email_settings.js');?>"></script>