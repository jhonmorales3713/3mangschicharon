<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/maploader.css');?>">
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Shops"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shops</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php if($this->session->userdata('sys_shop') == 0){ ?>
                <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Shops/comrate_approval/'.$token);?>">Shop MCR Approval</a></li>
                <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <?php }?>
            <li class="breadcrumb-item active"><?= $breadcrumbs_active ?></li>
        </ol>
    </div>
    <form id="entry-form">
    <div class="row">
    <div class="col-lg-7">
        <section class="tables">   
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">View Shop</h3>
                            </div>
                                <?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row hidden">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>ID</label>
                                                            <input type="text" name="entry-id" id="entry-id" class="form-control" value="<?= $idno ?>" >
                                                            <input type="text" name="logo_select" id="logo_select" class="form-control">
                                                            <input type="text" name="banner_select" id="banner_select" class="form-control">
                                                            <input type="text" name="advertisement_select" id="advertisement_select" class="form-control">
                                                            <input type="text" name="whatsnew_select" id="whatsnew_select" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <!-- <div class="col-12">
                                                        <div class="form-group" id="imgContainer">
                                                            <label>Shop Logo</label><br/>
                                                            <input type="file" class="hidden" name="shop_image" id="shop_image"up>
                                                            <div id="shop-placeholder">
                                                                <p class="small">Upload Photo</p>
                                                            </div>
                                                            <img src="" id="shop_preview" class="img-responsive">
                                                            <input type="text" class="hidden" name="current_shop_url" id="current_shop_url">
                                                            <button type="button" class="btn btn-primary btn-sm" id="change-shop-image">Change photo</button>
                                                        </div>
                                                    </div> -->
                                                    <div class="col-12">
                                                        <div class="row flex-md-row-reverse">
                                                            <div class="col-12 col-md-5 col-lg-4">
                                                                <div class="form-group">
                                                                    <label for="" class="d-none d-md-block">Logo Preview</label>
                                                                    <!-- <label class="control-label"><?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?> Main Logo <span class="red-asterisk">*</span></label> -->
                                                                    <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                                    <div class="img_preview_container square" style="display:none;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 col-md-7 col-lg-8">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="form-group">
                                                                            <label for="">Main Logo</label>
                                                                            <div class="input-group" style="width:100%;">
                                                                                <div class="custom-file">
                                                                                    <input type="file" class="custom-file-input required_fields" name="file_container" id="file_container">
                                                                                    <label class="custom-file-label" id="file_description">Choose file</label>
                                                                                    <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                                </div>
                                                                            </div>
                                                                            <div class="note ">
                                                                                Make sure to meet the minimum image size requirements for better image quality
                                                                                <br>
                                                                                <br>
                                                                                1. Dimension:  200x200px<br>
                                                                                2. File type: JPEG, PNG, JPG<br>
                                                                                3. File Size: maximum 3mb
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="entry-mobile" class="control-label">Contact Number <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control allownumericwithoutdecimal required_fields" name="entry-mobile" id="entry-mobile" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="entry-email" class="control-label">Email Address <span class="red-asterisk">*</span></label>
                                                            <input type="email" class="form-control required_fields" name="entry-email" id="entry-email" >
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">Address <span class="red-asterisk">*</span></label>
                                                            <input class="form-control form-control-sm form-state" type="text" name="entry-address">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div id="pac-container">
                                                            <label for="">Pin Address <span class="asterisk"></span></label>
                                                            <input id="pin_address" type="text" placeholder="Search" class = "form-control pr_field detail-input" name = "pin_address" style = "padding:10px;font-size:15px !important;" value="">
                                                            <input type="hidden" name = "loc_latitude" id = "loc_latitude" name = "loc_latitude" class = "pr_field" value = "">
                                                            <input type="hidden" name = "loc_longitude" id = "loc_longitude" name = "loc_longitude" class = "pr_field" value = "">
                                                        </div>
                                                        <div id="map" style = "height:200px;margin-top:30px;"></div>
                                                        <div id="infowindow-content">
                                                          <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                                          <span id="place-name"  class="title"></span><br>
                                                          <span id="place-address"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">Region <span class="red-asterisk">*</span></label>
                                                            <select class="select2 form-control form-control-sm required_fields form-state" name="entry-shop_region" data-reqselect2="yes">
                                                                <option value="">Select Region</option>
                                                                <?php select_option_obj($region, 'region') ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">City</label>
                                                            <select class="select2 form-control form-control-sm form-state" name="entry-shop_city" data-reqselect2="yes">
                                                                <option value="">Select City</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="col-12" style="padding:0px;">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    <!-- <?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?>  -->
                                                                    Shop Banner 
                                                                </label>
                                                                <div class="input-group" style="width:100%;">
                                                                    <div class="custom-file">
                                                                        <input type="file" class="custom-file-input" name="file_container_banner" id="file_container_banner">
                                                                        <label class="custom-file-label" id="file_description_banner">Choose file</label>
                                                                        <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                    </div>
                                                                </div>
                                                                <div class="note">
                                                                    Make sure to meet the minimum image size requirements for better image quality
                                                                    <br>
                                                                    <br>
                                                                    1. Dimension:  1500 x 400px<br>
                                                                    2. File type: JPEG, PNG, JPG<br>
                                                                    3. File Size: maximum 3mb
                                                                </div>
                                                                <div class="square mb-3 " id="imgthumbnail-banner"><img src="<?= base_url('assets/img/banner-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                                <div class="img_preview_container_banner square" style="display:none;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }else{ ?> <!-- UPDATING RECORD -->
                                        <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row hidden">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label>ID</label>
                                                            <input type="text" name="entry-id" id="entry-id" class="form-control" value="<?= $sys_shop_details->idno ?>" >
                                                            <input type="hidden" name="entry-old_logo" class="form-control" value="<?= $sys_shop_details->logo ?>" >
                                                            <input type="hidden" name="entry-old_banner" class="form-control" value="<?= $sys_shop_details->banner ?>" >
                                                            <input type="hidden" name="entry-old_advertisement" class="form-control" value="<?= $sys_shop_details->set_shop_advertisement ?>" >
                                                            <input type="hidden" name="entry-old_whatsnew" class="form-control" value="<?= $sys_shop_details->set_whatsnew_merchant_photo ?>" >
                                                            <input type="text" name="logo_select" id="logo_select" class="form-control">
                                                            <input type="text" name="banner_select" id="banner_select" class="form-control">
                                                            <input type="text" name="advertisement_select" id="advertisement_select" class="form-control">
                                                            <input type="text" name="whatsnew_select" id="whatsnew_select" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="row flex-md-row-reverse">
                                                            <div class="col-md-5 col-lg-4">
                                                                <label class="control-label">
                                                                    Logo Preview
                                                                </label>
                                                                <?php if(!empty($sys_shop_details->logo)){ ?>
                                                                    <div class="square" id="imgthumbnail-logo"><img src="<?= get_s3_imgpath_upload().'assets/img/shops/'.$sys_shop_details->logo; ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                                <?php }else{ ?>
                                                                    <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                                <?php } ?>
                                                                <div class="img_preview_container square" style="display:none;"></div>
                                                            </div>
                                                            <div class="col-md-7 col-lg-8" style="display: none;">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        <!-- <?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?> -->
                                                                        Main Logo <span class="red-asterisk">*</span>
                                                                    </label>
                                                                    <div class="input-group" style="width:100%;">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="file_container" id="file_container">
                                                                            <label class="custom-file-label" id="file_description">Choose file</label>
                                                                            <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                        </div>
                                                                    </div>
                                                                    <div class="note">
                                                                        Make sure to meet the minimum image size requirements for better image quality
                                                                        <br>
                                                                        <br>
                                                                        1. Dimension:  200x200px<br>
                                                                        2. File type: JPEG, PNG, JPG<br>
                                                                        3. File Size: maximum 3mb
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-12">
                                                        <div class="form-group" id="imgContainer">
                                                            <label>Shop Logo</label><br/>
                                                            <input type="file" class="hidden" name="shop_image" id="shop_image"up>
                                                            <div id="shop-placeholder">
                                                                <p class="small">Upload Photo</p>
                                                            </div>
                                                            <img src="" id="shop_preview" class="img-responsive">
                                                            <input type="text" class="hidden" name="current_shop_url" id="current_shop_url">
                                                            <button type="button" class="btn btn-primary btn-sm" id="change-shop-image">Change photo</button>
                                                        </div>
                                                    </div> -->
                                                    <?php if($this->session->userdata('sys_shop') != 0){ ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="entry-shopname" class="col-form-label-sm">Shop Name</label>
                                                            <input class="form-control form-control-sm disabled" type="text" value="<?= $sys_shop_details->shopname ?>" disabled>
                                                            <!-- <label class="control-label form-control-lg" id="entry-shopname" style="padding: 0px !important;"><?= $sys_shop_details->shopname ?></label> -->
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="entry-mobile" class="col-form-label-sm">Contact Number <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control allownumericwithoutdecimal required_fields" name="entry-mobile" id="entry-mobile" value="<?= $sys_shop_details->mobile ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="entry-email" class="col-form-label-sm">Email Address <span class="red-asterisk">*</span></label>
                                                            <input type="email" class="form-control required_fields" name="entry-email" id="entry-email" value="<?= $sys_shop_details->email ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">Address <span class="red-asterisk">*</span></label>
                                                            <input class="form-control form-control-sm required_fields form-state" type="text" name="entry-address" value="<?= $sys_shop_details->address ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">Referral Code</label>
                                                            <input class="form-control form-control-smform-state" type="text" name="" value="<?= $sys_shop_details->merch_referral_code ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div id="pac-container">
                                                            <label for="">Pin Address <span class="asterisk"></span></label>
                                                            <input id="pin_address" type="text" placeholder="Search" class = "form-control pr_field detail-input" name = "pin_address" style = "padding:10px;font-size:15px !important;" disabled>
                                                            <input type="hidden" name = "loc_latitude" id = "loc_latitude" name = "loc_latitude" class = "pr_field" value = "<?= $sys_shop_details->latitude ?>">
                                                            <input type="hidden" name = "loc_longitude" id = "loc_longitude" name = "loc_longitude" class = "pr_field" value = "<?= $sys_shop_details->longitude ?>">
                                                        </div>
                                                        <div id="map" style = "height:200px;margin-top:30px;"></div>
                                                        <div id="infowindow-content">
                                                          <!-- <img src="" width="16" height="16" id="place-icon"> -->
                                                          <span id="place-name"  class="title"></span><br>
                                                          <span id="place-address"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-control-label col-form-label-sm">Region <span class="red-asterisk">*</span></label>
                                                            <select class="select2 form-control form-control-sm required_fields form-state" name="entry-shop_region" data-reqselect2="yes" disabled>
                                                                <option value="">Select Region</option>
                                                                <?php foreach($region as $row){ ?>
                                                                        <?php if($sys_shop_details->shop_region == $row->regCode){ ?>
                                                                                <option value="<?= $row->regCode ?>" data-regcode="<?= $row->regCode ?>" selected><?= $row->regDesc ?></option>
                                                                        <?php }else{ ?>
                                                                                <option value="<?= $row->regCode ?>"  data-regcode="<?= $row->regCode ?>"><?= $row->regDesc ?></option>
                                                                        <?php } ?>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <input type="hidden" id="city_hidden" value="<?= $sys_shop_details->shop_city ?>">
                                                            <label class="form-control-label col-form-label-sm">City </label>
                                                            <select class="select2 form-control form-control-sm form-state" name="entry-shop_city" data-reqselect2="yes" disabled>
                                                                <option value="">Select City</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <!-- <?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?> -->
                                                                Shop Banner
                                                            </label>
                                                            <div class="input-group" style="width:100%;" style="display: none">
                                                                <div class="custom-file" style="display: none">
                                                                    <input type="file" class="custom-file-input" name="file_container_banner" id="file_container_banner">
                                                                    <label class="custom-file-label" id="file_description_banner">Choose file</label>
                                                                    <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                </div>
                                                            </div>
                                                            <div class="note" style="display: none">
                                                                Make sure to meet the minimum image size requirements for better image quality
                                                                <br>
                                                                <br>
                                                                1. Dimension:  1500 x 400px<br>
                                                                2. File type: JPEG, PNG, JPG<br>
                                                                3. File Size: maximum 3mb
                                                            </div>
                                                            <?php if(!empty($sys_shop_details->banner)){ ?>
                                                                <div class="square" id="imgthumbnail-banner"><img src="<?= get_s3_imgpath_upload().'assets/img/shops-banner/'.$sys_shop_details->banner ?>" style="max-width: 100%;max-height: 100%;"></div><br>
                                                            <?php }else{ ?>
                                                                <div class="square" id="imgthumbnail-banner"><img src="<?= base_url('assets/img/banner-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div><br>
                                                            <?php } ?>
                                                            <div class="img_preview_container_banner square" style="display:none;"></div><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>

                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php if($this->session->userdata('sys_shop') == 0){ ?>
                        <section class="tables" style="padding-bottom: 20px;">   
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12 col-lg-12"> 
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Bank Details</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-bankname" class="control-label">Bank Name <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" disabled >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctname" class="control-label">Account Name <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname"  disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctno" class="control-label">Account Number <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno"  disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="entry-desc" class="control-label">Account Type <span class="red-asterisk">*</span></label>
                                                            <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2" disabled></textarea>
                                                        </div>
                                                    </div>
                                                    <?php }else{ ?> <!-- UPDATING RECORD -->
                                                    <div class="col-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-bankname" class="control-label">Bank Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" value="<?= $sys_shop_details->bankname ?>" disabled> 
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-acctname" class="control-label">Account Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname" value="<?= $sys_shop_details->accountname ?>" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-acctno" class="control-label">Account Number <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno" value="<?= $sys_shop_details->accountno ?>" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-desc" class="control-label">Account Type<span class="red-asterisk">*</span></label>
                                                                <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2" disabled><?= $sys_shop_details->description ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php }?>
                </div>
                <?php if($this->session->userdata('sys_shop') != 0){ ?>
                    <div class="col-lg-5" style="padding: 0px !important;">
                        <section class="tables" style="padding-bottom: 20px;">   
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-12 col-lg-12"> 
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="card-title">Bank Details</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-bankname" class="control-label">Bank Name <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctname" class="control-label">Account Name <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname" >
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="entry-acctno" class="control-label">Account Number <span class="red-asterisk">*</span></label>
                                                            <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno" >
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="entry-desc" class="control-label">Account Type<span class="red-asterisk">*</span></label>
                                                            <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2"></textarea>
                                                        </div>
                                                    </div>
                                                    <?php }else{ ?> <!-- UPDATING RECORD -->
                                                    <div class="col-12">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-bankname" class="control-label">Bank Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-bankname" id="entry-bankname" value="<?= $sys_shop_details->bankname ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-acctname" class="control-label">Account Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-acctname" id="entry-acctname" value="<?= $sys_shop_details->accountname ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="entry-acctno" class="control-label">Account Number <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-acctno" id="entry-acctno" value="<?= $sys_shop_details->accountno ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-desc" class="control-label">Account Type <span class="red-asterisk">*</span></label>
                                                                <textarea class="form-control required_fields" name="entry-desc" id="entry-desc" row="2"><?= $sys_shop_details->description ?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                <?php }?>
                
                    <?php if($this->session->userdata('sys_shop') == 0){ ?> <!-- ADDING NEW RECORD -->
                        <div class="col-lg-5" style="padding: 0px !important;">
                            <section class="tables">   
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-12 col-md-12">  
                    <?php }else{ ?>
                        <div class="col-lg-5" style="padding: 0px !important; display:none;">
                            <section class="tables">   
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="col-12 col-md-12">  
                                                <div class="col-12 col-md-12 d-none">   
                    <?php } ?>
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Admin Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
                                                    <div class="form-group row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopcode" class="control-label">
                                                                    <!-- <?= infoicon_helper_msg('Uppercase letter and number only are allowed<br>Note: Max of 4 characters')?> -->
                                                                    Shop Code <span class="red-asterisk">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopcode" id="entry-shopcode" disabled>
                                                                <div class="note">
                                                                    Uppercase letter and number only are allowed<br>Note: Max of 4 characters
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopcode" class="control-label">
                                                                    <!-- <?= infoicon_helper_msg('Uppercase letter, numbers, space and underscore are allowed<br>Note: Space is converted to underscore after save')?> -->
                                                                    Shop URL<span class="red-asterisk">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopurl" id="entry-shopurl"  onkeydown="return shopurlKeytrapped(event);" disabled>
                                                                <div class="note">
                                                                    Uppercase letter, numbers, space and underscore are allowed<br>Note: Space is converted to underscore after save
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopname" class="control-label">Shop Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopname" id="entry-shopname" disabled >
                                                            </div>
                                                        </div>

                                                        <?php if(ini() == 'toktokmall'){   ?>

                                                            <div class="col-12 col-lg-12"> 
                                                                 <div class="form-group">
                                                                    <label for="entry-rate" class="control-label">Merchant Commission Rate <span class="red-asterisk">*</span></label>
                                                                       <div class="input-group-append">
                                                                            <input type="text" placeholder="0" class="form-control allownumericwithdecimal required_fields commcapping" name="entry-merchant-comrate" id="entry-merchant-comrate" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-12">
                                                                <div class="form-group">
                                                                      <label for="entry-rate" class="control-label">Account Type Commission Rate: </label>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                      <label for="f_rate" class="control-label">Startup</label> 
                                                                       <div class="input-group-append">
                                                                            <input type="text"  class="form-control allownumericwithdecimal required_fields" name="entry-f_startup" id="entry-f_startup" value="10" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                      <label for="f_jc" class="control-label">JC</label> 
                                                                       <div class="input-group-append">
                                                                           <input type="text" placeholder="1"  class="form-control allownumericwithdecimal required_fields" name="entry-f_jc" id="entry-f_jc" value="50" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                       <label for="f_mcjr" class="control-label">MCJR</label> 
                                                                       <div class="input-group-append">
                                                                       <input type="text" placeholder="1" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcjr" id="entry-f_mcjr"  value="60" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                      <label for="f_mc" class="control-label">MC</label> 
                                                                       <div class="input-group-append">
                                                                           <input type="text" placeholder="1" class="form-control allownumericwithdecimal required_fields" name="entry-f_mc" id="entry-f_mc"  value="70" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>


                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                      <label for="f_mcsuper" class="control-label">MCSUPER</label> 
                                                                       <div class="input-group-append">
                                                                            <input type="text"  placeholder="1"  class="form-control allownumericwithdecimal required_fields" name="entry-f_mcsuper" id="entry-f_mcsuper" value="80" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                     <label for="f_mcmega" class="control-label">MCMEGA</label> 
                                                                       <div class="input-group-append">
                                                                           <input type="text" placeholder="1" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcmega" id="entry-f_mcmega" value="100" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                 <div class="form-group">
                                                                     <label for="f_others" class="control-label">Others</label> 
                                                                       <div class="input-group-append">
                                                                           <input type="text" placeholder="1" class="form-control allownumericwithdecimal required_fields" name="entry-f_others" id="entry-f_others" value="50" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-md-12">
                                                                <div class="form-group">
                                                                    <div class="alert alert-warning" role="alert">
                                                                    Percentage of Account Type Commision Rate should not be more than  to 50% of Merchant Commission Rate.
                                                                    </div>
                                                                </div>
                                                            </div>


                                                        <?php    }else{    ?>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-ratetype" class="control-label">Rate Type <span class="red-asterisk">*</span></label>
                                                                        <select style="height:42px;" type="text" name="entry-ratetype" id="entry-ratetype" class="form-control required_fields">
                                                                            <option value="">-- Select Category --</option>
                                                                            <option value="p">Percentage</option>
                                                                            <option value="f">Fixed Amount</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-rate" class="control-label">Rate <span class="red-asterisk">*</span></label>
                                                                        <input type="number" placeholder="1.0" step="0.05" min="0" max="1" class="form-control required_fields" name="entry-rate" id="entry-rate" disabled>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-rate" class="control-label">Commission Rate </label>
                                                                        <input type="number" step="0.01" min="0" class="form-control" name="entry-commrate" id="entry-commrate" disabled >
                                                                    </div>
                                                                </div>
                                                            <?php    }    ?>
                                      
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">With Shipping Fee (Billing Type)</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-withshipping" name="entry-withshipping" value="" disabled>
                                                                        <input type="checkbox" id="checkbox-withshipping" class="success" disabled>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">Generate Billing Per Branch</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-generatebilling" name="entry-generatebilling" value="" disabled>
                                                                        <input type="checkbox" id="checkbox-generatebilling" class="success" disabled>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">With Pre-payment Wallet</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-prepayment" name="entry-prepayment" value="" disabled>
                                                                        <input type="checkbox" id="checkbox-prepayment" class="success" disabled>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">toktok Shipping</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-toktokdel" name="entry-toktokdel" value="" disabled>
                                                                        <input type="checkbox" id="checkbox-toktokdel" class="success" disabled>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-12 col-lg-6" id="threshold_amt_div" hidden>
                                                            <div class="form-group">
                                                                <label for="entry-rate" class="control-label">Threshold Amount <span class="red-asterisk">*</span></label>
                                                                <!-- <input type="text" class="form-control allownumericwithdecimal required_fields" name="entry-thresholdamt" id="entry-thresholdamt" value="0"> -->
                                                                <input type="number" placeholder="0.00" step="0.05" min="0" class="form-control required_fields" name="entry-thresholdamt" id="entry-rate" disabled >
                                                            </div>
                                                        </div>
                                                        <?php if(c_international() == 1){?>
                                                            <div class="col-12 col-lg-12">
                                                                <div class="form-group">
                                                                    <label for="entry-ratetype" class="control-label">Currency <span class="red-asterisk"></span></label>
                                                                    <select style="height:42px;" type="text" name="entry-currency" id="entry-currency" class="form-control" disabled>
                                                                        <!-- <option value="0" selected>PHP - Philippines</option> -->
                                                                        <?php foreach($get_currency as $row){?>
                                                                        <option value="<?=$row['id'];?>"><?=$row['currency'];?> - <?=$row['country_name'];?></option>
                                                                        <?php }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php } else{ ?>
                                                            <div class="col-12 col-lg-12" hidden>
                                                                <div class="form-group">
                                                                    <label for="entry-ratetype" class="control-label">Currency <span class="red-asterisk"></span></label>
                                                                    <select style="height:42px;" type="text" name="entry-currency" id="entry-currency" class="form-control" disabled>
                                                                        <!-- <option value="0" selected>PHP - Philippines</option> -->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php }?>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-treshold" class="control-label">Treshold Inventory</label>
                                                                <input type="text" class="form-control allownumericwithoutdecimal" name="entry-treshold" id="entry-treshold" disabled>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-allowed-unfulfilled" class="control-label">Allowed Unfulfilled</label>
                                                                <input type="text" class="form-control allownumericwithoutdecimal" name="entry-allowed-unfulfilled" id="entry-allowed-unfulfilled" disabled>
                                                            </div>
                                                        </div>
                                                    </div>

                                
                                                 
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <input type="hidden" name="set_allowpickup" value="0" disabled>
                                                            <input type="checkbox" class="form-control-input" id="set_allowpickup" name="set_allowpickup" value="1" disabled>
                                                            <label  class="form-control-label" for="set_allowpickup">Allow pickup</label>
                                                            <br>
                                                        </div>
                                                    </div>
                                                  
                                              
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <input type="hidden" name="set_advertisement" value="0" disabled>
                                                            <input type="checkbox" class="form-control-input" id="set_advertisement" name="set_advertisement" value="1" disabled>
                                                            <label  class="form-control-label" for="set_advertisement">Set Advertisement</label>
                                                            <br>
                                                        </div>
                                                    </div>


                                               

                                                    <hr><hr><br>
                                                
                                                    <div class="col-12 contsellingdiv" style="padding:0px;">
                                                        <div class="form-group">
                                                            <select class="select2 form-control form-control-sm  required_fields form-state" id="entry-feat-merchant-arrangement" name="entry-feat-merchant-arrangement" data-reqselect2="yes">
                                                            <option value="">Select Arrangement</option>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <!-- <?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?>  -->
                                                            Shop Advertisement

                                                            </label>
                                                            <div class="input-group" style="width:100%;">
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" name="file_container_advertisement" id="file_container_advertisement">
                                                                    <label class="custom-file-label" id="file_description_advertisement">Choose file</label>
                                                                    <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                </div>
                                                            </div>
                                                            <div class="note">
                                                                Make sure to meet the minimum image size requirements for better image quality
                                                            <br>
                                                                <br>
                                                                1. Dimension:  1500 x 400px<br>
                                                                2. File type: JPEG, PNG, JPG<br>
                                                                3. File Size: maximum 3mb 
                                                            </div>
                                                            <div class="square mb-3 " id="imgthumbnail-advertisement"><img src="<?= base_url('assets/img/banner-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                            <div class="img_preview_container_advertisement square" style="display:none;"></div>
                                                        </div>                                            
                                                     </div>

                                                   <hr><hr><br>

                                                   <div class="col-12">
                                                        <div class="form-group">
                                                            <input type="hidden" name="set_whatsnew_merchant" value="0">
                                                            <input type="checkbox" class="form-control-input" id="set_whatsnew_merchant" name="set_whatsnew_merchant" value="1">
                                                            <label  class="form-control-label" for="set_whatsnew_merchant">Set What`s New Merchant</label>
                                                            <br>
                                                        </div>
                                                    </div>

                                                     <div class="col-12 whatsnew_div" style="padding:0px;">
                                                        <div class="form-group">
                                                            <select class="select2 form-control form-control-sm  required_fields form-state" id="entry-feat-whatsnew-merchant-arrangement" name="entry-feat-whatsnew-merchant-arrangement" data-reqselect2="yes">
                                                               <option value="">Select Arrangement</option>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                                <option value="6">6</option>
                                                                <option value="7">7</option>
                                                                <option value="8">8</option>
                                                                <option value="9">9</option>
                                                                <option value="10">10</option>
                                                                <option value="11">11</option>
                                                                <option value="12">12</option>
                                                                <option value="13">13</option>
                                                                <option value="14">14</option>
                                                                <option value="15">15</option>
                                                                <option value="16">16</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                <!-- <?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?>  -->
                                                               What`s New Merchant   
                                                            </label>
                                                            <div class="input-group" style="width:100%;">
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" name="file_container_whatsnew" id="file_container_whatsnew">
                                                                    <label class="custom-file-label" id="file_description_whatsnew">Choose file</label>
                                                                    <!-- <input type="hidden" class="hidden" name="main_logo_checker" id="main_logo_checker" value="false"> -->
                                                                </div>
                                                            </div>
                                                            <div class="note">
                                                                Make sure to meet the minimum image size requirements for better image quality
                                                                <br>
                                                                <br>
                                                                1. Dimension:  520px x 520px<br>
                                                                2. File type: JPEG, PNG, JPG<br>
                                                                3. File Size: maximum 3mb 
                                                            </div>
                                                            <div class="square mb-3 " id="imgthumbnail-whatsnew"><img src="<?= base_url('assets/img/placeholder-500x500.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                            <div class="img_preview_container_whatsnew square" style="display:none;"></div>
                                                        </div>                                            
                                                     </div>

                                                <?php }else{ ?> <!-- UPDATING RECORD -->
                                                    <div class="form-group row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopcode" class="control-label">
                                                                    <!-- <?= infoicon_helper_msg('Uppercase letter and number only are allowed<br>Note: Max of 4 characters')?>  -->
                                                                    Shop Code <span class="red-asterisk">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopcode" id="entry-shopcode" value="<?= $sys_shop_details->shopcode ?>" readonly>
                                                                <div class="note">
                                                                    Uppercase letter and number only are allowed<br>Note: Max of 4 characters
                                                                </div>
                                                            </div>
                                                        </div>
                                                           <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopcode" class="control-label">
                                                                    <!-- <?= infoicon_helper_msg('Uppercase letter, numbers, space and underscore are allowed<br>Note: Space is converted to underscore after save')?> -->
                                                                    Shop URL <span class="red-asterisk">*</span>
                                                                </label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopurl" id="entry-shopurl" value="<?= $sys_shop_details->shopurl ?>" onkeydown="return shopurlKeytrapped(event);" disabled>
                                                                <div class="note">
                                                                    Uppercase letter, numbers, space and underscore are allowed<br>Note: Space is converted to underscore after save
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-shopname" class="control-label">Shop Name <span class="red-asterisk">*</span></label>
                                                                <input type="text" class="form-control required_fields" name="entry-shopname" id="entry-shopname" value="<?= $sys_shop_details->shopname ?>" disabled>
                                                            </div>
                                                        </div>
                                                   
                                                        
                                                        <?php if(ini() == 'toktokmall'){   ?>
                                                            <div class="col-12 col-lg-12"> 
                                                                <div class="form-group">
                                                                    <label for="entry-rate" class="control-label">Merchant Commission Rate <span class="red-asterisk">*</span></label>
                                                                    <div class="input-group-append">
                                                                          <input type="number" placeholder="" min="0" max="100" class="form-control required_fields commcapping" name="entry-merchant-comrate" id="entry-merchant-comrate" value="<?= $sys_shop_details->rateamount * 100?>" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 col-lg-12">
                                                                <div class="form-group">
                                                                      <label for="entry-rate" class="control-label">Account Type Commission Rate: </label>
                                                                </div>
                                                            </div>


                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_rate" class="control-label">Startup</label> 
                                                                    <div class="input-group-append">
                                                                            <!-- <?php if($sys_shop_details->startup == 0){ $startup = 10; }else{   $startup = $sys_shop_details->startup * 100; }  ?>   -->
                                                                            <input type="text"  class="form-control allownumericwithdecimal required_fields" name="entry-f_startup" id="entry-f_startup" value="<?=  $sys_shop_details->startup * 100;; ?>" disabled >
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_jc" class="control-label">JC</label> 
                                                                    <div class="input-group-append">
                                                                            <!-- <?php if($sys_shop_details->jc == 0){ $jc = 50; }else{   $jc = $sys_shop_details->jc * 100; }  ?>    -->
                                                                            <input type="text" placeholder=""  class="form-control allownumericwithdecimal required_fields" name="entry-f_jc" id="entry-f_jc" value="<?= $sys_shop_details->jc * 100;; ?>" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_mcjr" class="control-label">MCJR</label> 
                                                                    <div class="input-group-append">
                                                                             <!-- <?php if($sys_shop_details->mcjr == 0){ $mcjr = 60; }else{   $mcjr = $sys_shop_details->mcjr * 100; }  ?>  -->
                                                                             <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcjr" id="entry-f_mcjr" value="<?= $sys_shop_details->mcjr * 100;; ?>"disabled >
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_mc" class="control-label">MC</label> 
                                                                    <div class="input-group-append">
                                                                           <!-- <?php if($sys_shop_details->mc == 0){ $mc = 70; }else{   $mc = $sys_shop_details->mc * 100; }  ?>  -->
                                                                           <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mc" id="entry-f_mc" value="<?= $sys_shop_details->mc * 100;  ?>" disabled> 
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>


                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_mcsuper" class="control-label">MCSUPER</label> 
                                                                    <div class="input-group-append">
                                                                            <!-- <?php if($sys_shop_details->mcsuper == 0){ $mcsuper = 80; }else{   $mcsuper = $sys_shop_details->mcsuper * 100; }  ?>  -->
                                                                            <input type="text"  placeholder=""  class="form-control allownumericwithdecimal required_fields" name="entry-f_mcsuper" id="entry-f_mcsuper" value="<?= $sys_shop_details->mcsuper * 100; ?>" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_mcmega" class="control-label">MCMEGA</label> 
                                                                    <div class="input-group-append">
                                                                            <!-- <?php if($sys_shop_details->mcmega == 0){ $mcmega = 100; }else{   $mcmega = $sys_shop_details->mcmega * 100; }  ?>  -->
                                                                            <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcmega" id="entry-f_mcmega" value="<?= $sys_shop_details->mcmega * 100; ?>" disabled>
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-md-6">
                                                                <div class="form-group">
                                                                    <label for="f_others" class="control-label">Others</label> 
                                                                    <div class="input-group-append">
                                                                          <!-- <?php if($sys_shop_details->others == 0){ $others = 50; }else{   $others = $sys_shop_details->others * 100; }  ?>  -->
                                                                          <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_others" id="entry-f_others" value="<?= $sys_shop_details->others * 100; ?>" disabled >
                                                                            <span class="input-group-text">%</span>
                                                                        </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-12 col-md-12">
                                                                <div class="form-group">
                                                                    <div class="alert alert-warning" role="alert">
                                                                    Percentage of Account Type Commision Rate should not be more than  to 50% of Merchant Commission Rate.
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <?php    }else{    ?>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-ratetype" class="control-label">Rate Type <span class="red-asterisk">*</span></label>
                                                                        <select style="height:42px;" type="text" name="entry-ratetype" id="entry-ratetype" class="form-control required_fields" disabled>
                                                                            <?php if($sys_shop_details->ratetype == "p"){ ?>
                                                                                <option value="">-- Select Category --</option>
                                                                                <option value="p" selected>Percentage</option>
                                                                                <option value="f">Fixed Amount</option>
                                                                            <?php }else if($sys_shop_details->ratetype == "f"){ ?>
                                                                                <option value="">-- Select Category --</option>
                                                                                <option value="p">Percentage</option>
                                                                                <option value="f" selected>Fixed Amount</option>
                                                                            <?php }else{ ?>
                                                                                <option value="" selected>-- Select Category --</option>
                                                                                <option value="p">Percentage</option>
                                                                                <option value="f">Fixed Amount</option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-rate" class="control-label">Rate <span class="red-asterisk">*</span></label>
                                                                        <input type="number" placeholder="1.0" step="0.05" min="0" max="1" class="form-control required_fields" name="entry-rate" id="entry-rate" value="<?= $sys_shop_details->rateamount ?>" disabled>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12 col-lg-6">
                                                                    <div class="form-group">
                                                                        <label for="entry-rate" class="control-label">Commission Rate </label>
                                                                        <input type="number" step="0.01" min="0" class="form-control" name="entry-commrate" id="entry-commrate" value="<?= $sys_shop_details->commission_rate ?>" disabled>
                                                                    </div>
                                                                </div>
                                                            <?php    }    ?>
                                                        


                                                        <div class="col-12 col-lg-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">With Shipping Fee (Billing Type)</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-withshipping" name="entry-withshipping" value="<?= $sys_shop_details->billing_type ?>" disabled>
                                                                        <?php if($sys_shop_details->billing_type == 1){ ?>
                                                                            <input type="checkbox" id="checkbox-withshipping" class="success" checked disabled>
                                                                        <?php }else{ ?>
                                                                            <input type="checkbox" id="checkbox-withshipping" class="success" disabled>
                                                                        <?php } ?>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">Generate Billing Per Branch</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-generatebilling" name="entry-generatebilling" value="<?= $sys_shop_details->generatebilling ?>" disabled>
                                                                        <?php if($sys_shop_details->generatebilling == 1){ ?>
                                                                            <input type="checkbox" id="checkbox-generatebilling" class="success" checked>
                                                                        <?php }else{ ?>
                                                                            <input type="checkbox" id="checkbox-generatebilling" class="success" disabled> 
                                                                        <?php } ?>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">With Pre-payment Wallet</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-prepayment" name="entry-prepayment" value="<?= $sys_shop_details->prepayment ?>" disabled>
                                                                        <?php if($sys_shop_details->prepayment == 1){ ?>
                                                                            <input type="checkbox" id="checkbox-prepayment" class="success" checked disabled>
                                                                        <?php }else{ ?>
                                                                            <input type="checkbox" id="checkbox-prepayment" class="success" disabled>
                                                                        <?php } ?>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-control-label col-form-label-sm" style="margin: 0px !important;">toktok Shipping</label>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item" style="border-top: 0 none;">
                                                                    <label class="switch">
                                                                        <input type="hidden" id="entry-toktokdel" name="entry-toktokdel" value="<?= $sys_shop_details->toktok_shipping ?>" disabled>
                                                                        <?php if($sys_shop_details->toktok_shipping == 1){ ?>
                                                                            <input type="checkbox" id="checkbox-toktokdel" class="success" checked disabled>
                                                                        <?php }else{ ?>
                                                                            <input type="checkbox" id="checkbox-toktokdel" class="success" disabled>
                                                                        <?php } ?>
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        
                                                        <?php if($sys_shop_details->prepayment == 1){ ?>
                                                            <div class="col-12 col-lg-6" id="threshold_amt_div">
                                                        <?php }else{ ?>
                                                            <div class="col-12 col-lg-6" id="threshold_amt_div" hidden>
                                                        <?php } ?>
                                                            <div class="form-group">
                                                                <label for="entry-rate" class="control-label">Threshold Amount <span class="red-asterisk">*</span></label>
                                                                <input type="number" placeholder="0.00" step="0.05" min="0.00" class="form-control required_fields" name="entry-thresholdamt" id="entry-thresholdamt" value="<?= $sys_shop_details->threshold_amt ?>" disabled>
<!--                                                                 <input type="text" class="form-control allownumericwithdecimal required_fields" name="entry-thresholdamt" id="entry-thresholdamt" value="<?= $sys_shop_details->threshold_amt ?>"> -->
                                                            </div>
                                                        </div>
                                                        <?php if(c_international() == 1){?>
                                                            <div class="col-12 col-lg-12">
                                                                <div class="form-group">
                                                                    <label for="entry-ratetype" class="control-label">Currency <span class="red-asterisk"></span></label>
                                                                    <select style="height:42px;" type="text" name="entry-currency" id="entry-currency" class="form-control" disabled>
                                                                        <!-- <option value="0">PHP - Philippines</option> -->
                                                                        <?php foreach($get_currency as $row){?>
                                                                            <?php $selected = ($row['id'] == $sys_shop_details->app_currency_id) ? 'selected' : '';?>
                                                                            <option value="<?=$row['id'];?>" <?=$selected?>><?=$row['currency'];?> - <?=$row['country_name'];?></option>
                                                                        <?php }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php } else{?>
                                                            <div class="col-12 col-lg-12" hidden>
                                                                <div class="form-group">
                                                                    <label for="entry-ratetype" class="control-label">Currency <span class="red-asterisk"></span></label>
                                                                    <select style="height:42px;" type="text" name="entry-currency" id="entry-currency" class="form-control" disabled>
                                                                        <!-- <option value="0" selected>PHP - Philippines</option> -->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        <?php }?>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="entry-treshold" class="control-label">Treshold Inventory</label>
                                                            <input type="text" class="form-control allownumericwithoutdecimal" name="entry-treshold" id="entry-treshold" value="<?= $sys_shop_details->inv_threshold ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="entry-allowed-unfulfilled" class="control-label">Allowed Unfulfilled</label>
                                                                <input type="text" class="form-control allownumericwithoutdecimal" name="entry-allowed-unfulfilled" id="entry-allowed-unfulfilled" value="<?= $sys_shop_details->allowed_unfulfilled?>" disabled>
                                                            </div>
                                                        </div>
                                                    </div>

                                                 </div>

                                            
                                                  

                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        </div>
                <div class="col-lg-12">
                    <section class="tables">   
                        <div class="container-fluid">
                            <div class="row">
                                    <div class="col-md-12 col-12 text-right">
                                        <div class="card">
                                            <div class="card-body">     
                                                <?php if($shop_mcr_staus[0]['status']  == 3 &&  $validation['approve'] == 1){ ?>
                                                     <a type="button"  id="ApproveButton" data-toggle="modal" data-target="#approveModal"  style="float:right; color: #fff;" class="btn btn-primary">Approve</a>
                                                <?php } ?>
                                                <?php if($shop_mcr_staus[0]['status'] == 2  &&  $validation['verify'] == 1){ ?>
                                                     <a type="button"  id="VerifyButton" data-toggle="modal" data-target="#VerifyModal" style="float:right; margin-right: 10px; color: #fff;" class="btn btn-primary">Verify</a>
                                                <?php } ?>
                                                <?php if( $validation['edit'] == 1){ ?>
                                                    <a type="button"  id="EditBtn" style="float:right;  margin-right: 10px; color: #fff;" class="btn btn-primary">Edit</a>
                                                <?php } ?>
                                                <?php if( $validation['decline'] == 1){ ?>
                                                     <a type="button"  id="DeclineButton" data-toggle="modal" data-target="#declineModal"  style="float:right; margin-right: 10px; color: #fff;" class="btn btn-primary">Decline</a>
                                                <?php } ?>
                                                <a type="button"  id="backBtn" style="float:right; margin-right: 10px; color: #fff;" class="btn btn-primary" >Close</a>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </section>
                </div>
    </form>
        <!-- <div class="container-fluid">
            <div class="row">
                
            </div>
        </div>
    </section> -->
   

</div>
</div>

<!-- Modal -->


<!-- Approve Modal-->
<div id="approveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Approve</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop changes will be successfully Approved.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-prod_id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Decline Modal-->
<div id="declineModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Decline</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop  changes will be declined.</label>
                    </div>
                </div>
               
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" id="declineButton" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedDecBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>



<!-- Verify Modal-->
<div id="VerifyModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Verifiy</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Shop changes will be successfully Verified.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedBtnVerify" class="btn btn-success waves-effect waves-light" aria-label="Close" data-prod_id="">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>


<input type="hidden" id="shopid" value="<?= en_dec('dec', $idno) ?>">  
<input type="hidden" id="md5" value="<?= $idno?>">  
<input type="hidden" id="token" value="<?=$token?>">


<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->

<script type="text/javascript" src="<?=base_url('assets/js/shops/shop_cityofregion.js');?>"></script>
<script type="text/javascript" src="<?=base_url($core_js);?>"></script>
<?php if(!isset($sys_shop_details)){ ?> <!-- ADDING NEW RECORD -->
    <script type="text/javascript" src="<?=base_url('assets/js/shops/googlemap.js');?>"></script>
<?php }else{ ?> <!-- UPDATING RECORD -->
    <script type="text/javascript" src="<?=base_url('assets/js/shops/googlemap_edit.js');?>"></script>
<?php } ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>

<!-- end - load the footer here and some specific js