<style>
/* .imageprevies{
    width: 100%;
    height: auto;
    margin: 0 auto 0 auto;
    background-color: #b5b5b5;
    overflow: hidden;
    position: relative;
} */
.divclose {
  position: relative;
}
.deleteimg {
  position: absolute;
  margin-bottom: 75px;
  margin-left: -28px;
  font-size: 18px;
  cursor:pointer;
  background-color:white;
  /* border-radius:50px; */
  opacity:0.9;
  padding:10px;
}
.img_preview{
    margin-top: 10px;
    margin-bottom: 10px;
    max-height: 200px;
    max-width: 250px;
}

/*toggle styles*/
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 54px;
  height: 27px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
    background-color: var(--primary-color) !important
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

div.toggle-switch{
    width: 100%;
    padding: 10px;
}

div.toggle-switch *{        
    vertical-align: middle;
    margin-top: auto;
    margin-bottom: auto;
    display: inline-block;
}

.no-color{
    display: inline-block;
    position: absolute;
    right: 15px;
    top: 0;       
}

@media screen and (max-width:1280px) {
    .no-color{
        position: relative;
        display: block;
        right: 0;
    }
} 
</style>
<input style="display:none;" id="c_id" value="<?=$c_id?>">;
<div class="content-inner" id="pageActive" data-num="9" data-namecollapse="" data-labelname="Client Information">
    <div class="bc-icons-2 card mb-4">
        <div class="row">
            <div class="col">
                <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
                    <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/dev_settings_home/'.$token);?>">Developer Settings</a></li>
                    <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                    <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Dev_settings_client_information/client_information/'.$token);?>">Client Information</a></li>
                    <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
                    <li class="breadcrumb-item active">Update Client Information</li>
                </ol>
            </div>
            <!--
            <div class="col-auto text-right d-none d-md-flex align-items-center">
                <?php if($prev_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$prev_product)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a>
                <?php } ?>
                <?php if($next_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$next_product)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a>
                <?php } ?>
            </div>
            -->
        </div>
    </div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add Client Information</h3>
                </div>
                <form id="form_save" enctype="multipart/form-data" method="post" action="" data-update_url="<?= base_url().'developer_settings/Dev_settings_client_information/update'; ?>" >
                    <div class="card-body">
                    <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_code" class="control-label">Name</label>
                                    <input type="text" class="form-control" name="f_name" id="f_name" >
                                    <input type="hidden" class="form-control" name="c_id" id="c_id" value="<?=$c_id;?>">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Initial</label>
                                    <input type="text" class="form-control" name="f_initial" id="f_initial" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">ID Key</label>
                                    <input type="text" class="form-control" name="f_id_key" id="f_id_key" readonly>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Tag Line</label>
                                    <textarea class="form-control" name="f_tag_line" id="f_tag_line"></textarea>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="main_announcement" class="control-label">Shop Main Announcement</label>
                                    <textarea class="form-control" name="c_shop_main_announcement" id="c_shop_main_announcement"></textarea>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="c_404page" class="control-label">404 Page</label>
                                    <input type="text" class="form-control" name="c_404page" id="c_404page" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="c_shop_faqs" class="control-label">FAQ Page</label>
                                    <input type="text" class="form-control" name="c_shop_faqs" id="c_shop_faqs" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Email</label>
                                    <input type="text" class="form-control" name="f_email" id="f_email" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Phone</label>
                                    <input type="text" class="form-control" name="f_phone" id="f_phone" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Auto Email Sender</label>
                                    <input type="text" class="form-control" name="f_auto_email_sender" id="f_auto_email_sender" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Social Media FB Link</label>
                                    <input type="text" class="form-control" name="f_social_media_fb_link" id="f_social_media_fb_link" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Social Media IG Link</label>
                                    <input type="text" class="form-control" name="f_social_media_ig_link" id="f_social_media_ig_link" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Social Media Youtube Link</label>
                                    <input type="text" class="form-control" name="f_social_media_youtube_link" id="f_social_media_youtube_link" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Shop Live</label>
                                    <input type="text" class="form-control" name="f_url_shop_live" id="f_url_shop_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Shop Test</label>
                                    <input type="text" class="form-control" name="f_url_shop_test" id="f_url_shop_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Shop Local</label>
                                    <input type="text" class="form-control" name="f_url_shop_local" id="f_url_shop_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Admin Live</label>
                                    <input type="text" class="form-control" name="f_url_admin_live" id="f_url_admin_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Admin Test</label>
                                    <input type="text" class="form-control" name="f_url_admin_test" id="f_url_admin_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Admin Local</label>
                                    <input type="text" class="form-control" name="f_url_admin_local" id="f_url_admin_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Live</label>
                                    <input type="text" class="form-control" name="f_url_root_live" id="f_url_root_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Test</label>
                                    <input type="text" class="form-control" name="f_url_root_test" id="f_url_root_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Local</label>
                                    <input type="text" class="form-control" name="f_url_root_local" id="f_url_root_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Segment Live</label>
                                    <input type="text" class="form-control" name="f_url_root_segment_live" id="f_url_root_segment_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Segment Test</label>
                                    <input type="text" class="form-control" name="f_url_root_segment_test" id="f_url_root_segment_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">URL Root Segment Local</label>
                                    <input type="text" class="form-control" name="f_url_root_segment_local" id="f_url_root_segment_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Google API Key</label>
                                    <input type="text" class="form-control" name="c_google_api_key" id="c_google_api_key" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Server Link Live</label>
                                    <input type="text" class="form-control" name="c_apiserver_link_live" id="c_apiserver_link_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Server Link Test</label>
                                    <input type="text" class="form-control" name="c_apiserver_link_test" id="c_apiserver_link_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Server Link Local</label>
                                    <input type="text" class="form-control" name="c_apiserver_link_local" id="c_apiserver_link_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">S3 Bucket Link Live</label>
                                    <input type="text" class="form-control" name="c_s3bucket_link_live" id="c_s3bucket_link_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">S3 Bucket Link Test</label>
                                    <input type="text" class="form-control" name="c_s3bucket_link_test" id="c_s3bucket_link_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">S3 Bucket Link Local</label>
                                    <input type="text" class="form-control" name="c_s3bucket_link_local" id="c_s3bucket_link_local" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Privacy Policy</label>
                                    <input type="text" class="form-control" name="f_privacy_policy" id="f_privacy_policy" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Terms and Condition</label>
                                    <input type="text" class="form-control" name="f_terms_and_condition" id="f_terms_and_condition" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Faqs</label>
                                    <input type="text" class="form-control" name="f_faqs_link" id="f_faqs_link" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Contact Us</label>
                                    <input type="text" class="form-control" name="f_contact_us" id="f_contact_us" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">FB Pixel ID Live</label>
                                    <input type="text" class="form-control" name="f_fb_pixel_id_live" id="f_fb_pixel_id_live" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">FB Pixel ID Test</label>
                                    <input type="text" class="form-control" name="f_fb_pixel_id_test" id="f_fb_pixel_id_test" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Seller Registration Form</label>
                                    <input type="text" class="form-control" name="f_get_seller_reg_form" id="f_get_seller_reg_form" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">CPShop API URL</label>
                                    <input type="text" class="form-control" name="c_cpshop_api_url" id="c_cpshop_api_url" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">JC Fulfillment Shop Id</label>
                                    <input type="text" class="form-control" name="c_jcfulfillment_shopidno" id="c_jcfulfillment_shopidno" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Google Site Verification</label>
                                    <input type="text" class="form-control" name="f_google_site_verification" id="f_google_site_verification" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Order Reference Prefix</label>
                                    <input type="text" class="form-control" name="c_order_ref_prefix" id="c_order_ref_prefix" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Order Order SO Reference Prefix</label>
                                    <input type="text" class="form-control" name="c_order_so_ref_prefix" id="c_order_so_ref_prefix" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">SEO Website Description</label>
                                    <input type="text" class="form-control" name="c_seo_website_desc" id="c_seo_website_desc" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Inventory Threshold</label>
                                    <input type="text" class="form-control" name="c_inv_threshold" id="c_inv_threshold" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Order Threshold</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="c_order_threshold" id="c_order_threshold" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Coming Soon Password Local</label>
                                    <input type="text" class="form-control" name="c_comingsoon_password_local" id="c_comingsoon_password_local" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Coming Soon Password Test</label>
                                    <input type="text" class="form-control" name="c_comingsoon_password_test" id="c_comingsoon_password_test" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Coming Soon Password Live</label>
                                    <input type="text" class="form-control" name="c_comingsoon_password_live" id="c_comingsoon_password_live" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">OFPS</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_ofps" id="f_ofps" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Startup</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_startup" id="f_startup" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">JC</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_jc" id="f_jc" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">MCJR</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcjr" id="f_mcjr" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">MC</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mc" id="f_mc" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">MCSUPER</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcsuper" id="f_mcsuper" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">MCMEGA</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcmega" id="f_mcmega" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">OTHERS</label>
                                    <input type="text" class="form-control allownumericwithdecimal" name="f_others" id="f_others" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">toktok Authorization Key</label>
                                    <input type="text" class="form-control" name="c_toktok_authorization_key" id="c_toktok_authorization_key" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Pusher App Key Local</label>
                                    <input type="text" class="form-control" name="c_pusher_app_key_local" id="c_pusher_app_key_local" >
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Pusher App Key Test</label>
                                    <input type="text" class="form-control" name="c_pusher_app_key_test" id="c_pusher_app_key_test" >
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Pusher App Key Live</label>
                                    <input type="text" class="form-control" name="c_pusher_app_key_live" id="c_pusher_app_key_live" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Authorization Key Local</label>
                                    <input type="text" class="form-control" name="c_api_auth_key_local" id="c_api_auth_key_local" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Authorization Key Test</label>
                                    <input type="text" class="form-control" name="c_api_auth_key_test" id="c_api_auth_key_test" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">API Authorization Key Live</label>
                                    <input type="text" class="form-control" name="c_api_auth_key_live" id="c_api_auth_key_live" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Toktok API Endpoint</label>
                                    <input type="text" class="form-control" name="c_toktok_api_endpoint" id="c_toktok_api_endpoint" >
                                </div>
                            </div> 

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Toktokwallet API Endpoint</label>
                                    <input type="text" class="form-control" name="c_toktokwallet_api_endpoint" id="c_toktokwallet_api_endpoint" >
                                </div>
                            </div> 

                             <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Toktokwallet Authorization Key</label>
                                    <input type="text" class="form-control" name="c_toktokwallet_authorization_key" id="c_toktokwallet_authorization_key" >
                                </div>
                            </div> 

                        </div>                            
                    </div>
                </div>
            </div><!-- end of card information -->
        </div><!-- end of container -->
        <br>
        <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Media</h3>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <div class="row">
                        <div class="col-4">
                                <div class="form-group">                                    
                                    <label for="f_name" class="control-label">Header Upper Background</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_upper_bg_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_upper_bg" id="f_header_upper_bg" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Header Upper Text Color</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_upper_txtcolor_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_upper_txtcolor" id="f_header_upper_txtcolor" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Header Middle Background</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_middle_bg_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_middle_bg" id="f_header_middle_bg" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">                                    
                                    <label for="f_name" class="control-label">Header Middle Text Color</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_middle_txtcolor_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_middle_txtcolor" id="f_header_middle_txtcolor" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Header Middle Icons</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_middle_icons_no_color"> No Color</div>                                   
                                    <input type="color" class="form-control" name="f_header_middle_icons" id="f_header_middle_icons" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Header Bottom Backgroud</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_bottom_bg_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_bottom_bg" id="f_header_bottom_bg" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Header Bottom Text Color</label>
                                    <div class="no-color"><input type="checkbox" name="f_header_bottom_textcolor_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_header_bottom_textcolor" id="f_header_bottom_textcolor" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">                                    
                                    <label for="f_name" class="control-label">Footer Background</label>
                                    <div class="no-color"><input type="checkbox" name="f_footer_bg_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_footer_bg" id="f_footer_bg" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Footer Text Color</label>
                                    <div class="no-color"><input type="checkbox" name="f_footer_textcolor_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_footer_textcolor" id="f_footer_textcolor" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Footer Title Color</label>
                                    <div class="no-color"><input type="checkbox" name="f_footer_titlecolor_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_footer_titlecolor" id="f_footer_titlecolor" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Primary Color Accent</label>
                                    <div class="no-color"><input type="checkbox" name="f_primaryColor_accent_no_color"> No Color</div>
                                    <input type="color" class="form-control" name="f_primaryColor_accent" id="f_primaryColor_accent" >
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="f_name" class="control-label">Font</label>
                                    <input type="text" class="form-control" name="f_fontChoice" id="f_fontChoice" >
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="control-label">Main Logo</label>
                                    <div class="main_logo" style="display:none;"></div><br>
                                     <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="main_logo" id="main_logo">
                                            <label class="custom-file-label" id="file_label_main_logo">Choose file</label>
                                            <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="control-label">Secondary Logo</label>
                                    <div class="secondary_logo" style="display:none;"></div><br>
                                     <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="secondary_logo" id="secondary_logo">
                                            <label class="custom-file-label" id="file_label_secondary_logo">Choose file</label>
                                            <input type="hidden" class="hidden" name="secondary_logo_checker" id="secondary_logo_checker" value="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="control-label">Placeholder Image</label>
                                    <div class="placeholder_img" style="display:none;"></div><br>
                                     <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="placeholder_img" id="placeholder_img">
                                            <label class="custom-file-label" id="file_label_placeholder_img">Choose file</label>
                                            <input type="hidden" class="hidden" name="placeholder_img_checker" id="placeholder_img_checker" value="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="control-label">FB Image</label>
                                    <div class="fb_image" style="display:none;"></div><br>
                                     <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="fb_image" id="fb_image">
                                            <label class="custom-file-label" id="file_label_fb_image">Choose file</label>
                                            <input type="hidden" class="hidden" name="fb_image_checker" id="fb_image_checker" value="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label class="control-label">Favicon</label>
                                    <div class="favicon" style="display:none;"></div><br>
                                     <div class="input-group" style="width:100%;">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="favicon" id="favicon">
                                            <label class="custom-file-label" id="file_label_favicon">Choose file</label>
                                            <input type="hidden" class="hidden" name="favicon_checker" id="favicon_checker" value="false">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
            <br>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Controls</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Login</strong><br>
                                <input type="text" style="display:none" name="f_allow_login" id="f_allow_login"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>               
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Shop Page</strong><br>
                                <input type="text" style="display:none;" name="f_allow_shop_page" id="f_allow_shop_page"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>                                           
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Facebook Login</strong><br>
                                <input type="text" style="display:none;" name="f_allow_facebook_login" id="f_allow_facebook_login"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>                                        
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Gmail Login</strong><br>
                                <input type="text" style="display:none;" name="f_allow_gmail_login" id="f_allow_gmail_login"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>           
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Connect as Online Reseller</strong><br>
                                <input type="text" style="display:none;" name="f_allow_connect_as_online_reseller" id="f_allow_connect_as_online_reseller"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>              
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Physical Login</strong><br>
                                <input type="text" style="display:none;" name="f_allow_physical_login" id="f_allow_physical_login"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>             
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Continue as Guest Button</strong><br>
                                <input type="text" style="display:none;" name="f_continue_as_guest_button" id="f_continue_as_guest_button"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>              
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Registration</strong><br>
                                <input type="text" style="display:none;" name="f_allow_registration" id="f_allow_registration"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Default Order</strong><br>
                                <input type="text" style="display:none;" name="f_default_order" id="f_default_order"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>SMS</strong><br>
                                <input type="text" style="display:none;" name="f_allow_sms" id="f_allow_sms"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>COD</strong><br>
                                <input type="text" style="display:none;" name="f_allow_cod" id="f_allow_cod"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Google Address</strong><br>
                                <input type="text" style="display:none;" name="c_allow_google_addr" id="c_allow_google_addr"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Pre-order</strong><br>
                                <input type="text" style="display:none;" name="c_allow_preorder" id="c_allow_preorder"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Voucher</strong><br>
                                <input type="text" style="display:none;" name="c_allow_voucher" id="c_allow_voucher"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Toktok Shipping</strong><br>
                                <input type="text" style="display:none;" name="c_allow_toktok_shipping" id="c_allow_toktok_shipping"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>International</strong><br>
                                <input type="text" style="display:none;" name="c_international" id="c_international"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Allow Pickup</strong><br>
                                <input type="text" style="display:none;" name="c_allow_pickup" id="c_allow_pickup"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Local</strong><br>
                                <input type="text" style="display:none;" name="c_with_comingsoon_cover_local" id="c_with_comingsoon_cover_local"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Test</strong><br>
                                <input type="text" style="display:none;" name="c_with_comingsoon_cover_test" id="c_with_comingsoon_cover_test"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Coming Soon Cover Live</strong><br>
                                <input type="text" style="display:none;" name="c_with_comingsoon_cover_live" id="c_with_comingsoon_cover_live"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Realtime Notification</strong><br>
                                <input type="text" style="display:none;" name="c_realtime_notif" id="c_realtime_notif"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>What's New</strong><br>
                                <input type="text" style="display:none;" name="c_allow_whats_new" id="c_allow_whats_new"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>
                        
                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Promotion</strong><br>
                                <input type="text" style="display:none;" name="c_allow_promotions" id="c_allow_promotions"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Following</strong><br>
                                <input type="text" style="display:none;" name="c_allow_following" id="c_allow_following"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Flash Sale</strong><br>
                                <input type="text" style="display:none;" name="c_allow_flash_sale" id="c_allow_flash_sale"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Mystery Coupon</strong><br>
                                <input type="text" style="display:none;" name="c_allow_mystery_coupon" id="c_allow_mystery_coupon"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Piso Deal</strong><br>
                                <input type="text" style="display:none;" name="c_allow_piso_deal" id="c_allow_piso_deal"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Categories Section</strong><br>
                                <input type="text" style="display:none;" name="c_allow_categories_section" id="c_allow_categories_section"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                        <div class="col-4">
                            <div class="toggle-switch">
                                <strong>Promo Featured Items</strong><br>
                                <input type="text" style="display:none;" name="c_allow_promo_featured_items" id="c_allow_promo_featured_items"> 
                                <label class="switch">                                    
                                    <input type="checkbox">
                                    <span class="slider round"></span>
                                </label>
                            </div>          
                        </div>

                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-12 col-lg-12 text-right">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button>
                            <button type="submit" class="btn btn-success saveBtn">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!-- end of row -->

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
        </div>
    </form>
</div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/update_client_information.js');?>"></script>