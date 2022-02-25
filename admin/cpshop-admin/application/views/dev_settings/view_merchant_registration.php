<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/maploader.css');?>">

<style>
    .pac-container {
    z-index: 1051 !important;
    }
    .modal{
        z-index: 1050;   
    }
  
</style>

<?php 
    $split_business_permit = explode(",", $merchant_details['file_business_permt']); 
    $split_bir_cert = explode(",", $merchant_details['file_bir_cert']); 
    $split_brgy_permit = explode(",", $merchant_details['file_brgy_permit']); 
    $split_file_dtisec = explode(",", $merchant_details['file_dtisec']); 
    $split_company_profile = explode(",", $merchant_details['file_company_profile']); 
    $split_valid_id_signatory = explode(",", $merchant_details['file_valid_id_signatory']); 
    $split_trademarkIpoCert = explode(",", $merchant_details['file_trademark_ipo_cert']); 
    $split_retailAgreement = explode(",", $merchant_details['file_retail_agreement']); 
    $split_cdal = explode(",", $merchant_details['file_cdal']); 
    $split_fda = explode(",", $merchant_details['file_fda']); 
    $split_sml_fields = explode(",", $merchant_details['sml_fields']); 
    $view_access = ($merchant_details['application_status'] == 1) ? '' : 'disabled';
?>

<div class="content-inner" id="pageActive" data-num="4" data-namecollapse="" data-labelname="Order View"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/shops_home/'.$token);?>">Shop</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>

            <li class="breadcrumb-item"><a class="white-text backlink" href="<?=base_url('Shops/merchant_registration/'.$token);?>">Merchant Registration</a></li>

            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">View Merchant Registration</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row flex-md-row-reverse">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card customer-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <!-- <i class="fa fa-user no-margin"></i> -->
                                    Documents
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-12">
                                        <label class="">Business Permit:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_business_permit as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/business_permit/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">BIR 2303 Certification:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                    <?php foreach($split_bir_cert as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/bir/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">Barangay Permit:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_brgy_permit as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/brgy_permit/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">DTI/SEC:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_file_dtisec as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/dtisec/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">Company Profile:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_company_profile as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/company_profile/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">Valid ID of Signatory:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_valid_id_signatory as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/signatoryid/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">Trademark - IPO Certificate:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_trademarkIpoCert as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/trademarkicert/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class="">Retail Agreement:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_retailAgreement as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/retailagreement/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class=""> Certificate of Distribution / Authorization Letter:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_cdal as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/cdal/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                    <div class="col-12">
                                        <div class="border-bottom w-100"></div>
                                        <div class=" w-100">&nbsp;</div>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <label class=""> FDA Certificate of Product Registration:</label>
                                    </div>
                                    <div class="col-12 col-md-12">
                                        <?php foreach($split_fda as $value){
                                                $value = str_replace(' ', '', $value);    
                                        ?>
                                            <?php if($value != ''){?>
                                                <u><a href="<?=get_s3_imgpath_upload().'assets/docs/merchant_registration/fda/'.$value?>" target="_blank"id="tm_payment_ref_num" class="green-text font-weight-bold" download><?=$value?></a></u>
                                            <?php }?>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if($sys_shop == 0 && $merchant_details['application_status'] == 1){?>
                        <div class="col-12 mb-4">
                            <div class="card customer-card">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Admin Settings
                                    </h3>
                                </div>
                            <div class="card-body">

                                <?php if(ini() == 'toktokmall'){   ?>

                                    <div class="row no-gutters">
                                <div class="col-12 col-lg-12"> 
                                    <div class="form-group">
                                        <label for="entry-rate" class="control-label">Merchant Commission Rate <span class="red-asterisk">*</span></label>
                                        <div class="input-group-append">
                                                <input type="number" placeholder="0" min="1" max="100" class="form-control allownumericwithdecimal required_fields" name="entry-merchant-comrate" id="entry-merchant-comrate">
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
                                               
                                                <input type="text"  min="1" max="100" class="form-control  allownumericwithdecimal required_fields" name="entry-f_startup" id="entry-f_startup" value="10" >
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_jc" class="control-label">JC</label> 
                                        <div class="input-group-append">
                                          
                                                <input type="text" placeholder="" min="1" max="100"  class="form-control allownumericwithdecimal required_fields" name="entry-f_jc" id="entry-f_jc" value="50" >
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_mcjr" class="control-label">MCJR</label> 
                                        <div class="input-group-append">
                                                   
                                                    <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcjr" id="entry-f_mcjr" value="60" >
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_mc" class="control-label">MC</label> 
                                        <div class="input-group-append">
                                                
                                                <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mc" id="entry-f_mc" value="70">
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>


                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_mcsuper" class="control-label">MCSUPER</label> 
                                        <div class="input-group-append">
                                        
                                                <input type="text"  placeholder=""  class="form-control allownumericwithdecimal required_fields" name="entry-f_mcsuper" id="entry-f_mcsuper" value="80">
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_mcmega" class="control-label">MCMEGA</label> 
                                        <div class="input-group-append">
                           
                                                <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_mcmega" id="entry-f_mcmega" value="100">
                                                <span class="input-group-text">%</span>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6">
                                    <div class="form-group">
                                        <label for="f_others" class="control-label">Others</label> 
                                        <div class="input-group-append">
                                             
                                                <input type="text" placeholder="" class="form-control allownumericwithdecimal required_fields" name="entry-f_others" id="entry-f_others" value="50">
                                                <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                  </div>
                                </div>

                                <div class="col-12 col-md-12">
                                     <div class="form-group">
                                         <div class="alert alert-warning" role="alert">
                                            Percentage of Account Type Commision Rate should not be more than to 50% of Merchant Commission Rate.
                                         </div>
                                     </div>
                                </div>
                                <?php    }else{    ?>
                                    <div class="row no-gutters">
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="entry-ratetype" class="control-label">Rate Type <span class="red-asterisk">*</span></label>
                                                <select style="height:42px;" type="text" name="entry-ratetype" id="entry-ratetype" class="form-control required_fields">
                                                    <option value="">-- Select Category --</option>
                                                    <option value="p">Percentage</option>
                                                    <option value="f">Fixed Amount</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="entry-rate" class="control-label">Rate <span class="red-asterisk">*</span></label>
                                                <input type="number" placeholder="1.0" step="0.01" min="0" max="1" class="form-control required_fields" name="entry-rate" id="entry-rate" >
                                            </div>
                                        </div>
                                        
                                        <div class="col-12" style="display: none;">
                                            <div class="form-group">
                                                <label for="entry-treshold" class="control-label">Treshold Inventory</label>
                                                <input type="text" class="form-control allownumericwithoutdecimal" name="entry-treshold" id="entry-treshold">
                                            </div>
                                        </div>

                                        <div class="col-12" style="display: none;">
                                            <div class="form-group">
                                                <label for="entry-allowed-unfulfilled" class="control-label">Allowed Unfulfilled</label>
                                                <input type="text" class="form-control allownumericwithoutdecimal" name="entry-allowed-unfulfilled" id="entry-allowed-unfulfilled">
                                            </div>
                                        </div>

                                        <div class="col-12" style="display: none;">
                                            <div class="form-group">
                                                <input type="hidden" name="set_allowpickup" value="0">
                                                <input type="checkbox" class="form-control-input" id="set_allowpickup" name="set_allowpickup" value="1">
                                                <label  class="form-control-label" for="set_allowpickup">Allow pickup</label>
                                                <br>
                                            </div>
                                        </div>

                                    </div>
                                    <?php }?>
                                </div>
                            </div>
                         </div>
              <?php }?>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card detail-container">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Merchant Application Details</h3>
                            </div>
                            <div class="card-body px-lg-5">
                                <form id="merchant_form" enctype="multipart/form-data" method="post" action="" >
                                    <input type="hidden" name="entry-old_logo" class="form-control" value="<?= $merchant_details['shop_logo']; ?>" >
                                     <input type="hidden" name="entry-old_banner" class="form-control" value="<?= $merchant_details['shop_banner']; ?>" >
                                    <div class="">
            
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="">First Name:</label>
                                                <!-- <label id="tm_order_date" class="green-text font-weight-bold"><?=$merchant_details['cn_first_name'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_first_name" name="up_first_name" value="<?=$merchant_details['cn_first_name'];?>" <?=$view_access?>>
                                                <input type="hidden" class="form-control" id="up_app_id" name="up_app_id" value="<?=$id;?>">
                                            </div>
                                            <!-- <div class="col-12 col-md">
                                                
                                            </div> -->
                                            <div class="col-12 col-md-6">
                                                <label class="">Last Name:</label>
                                                <!-- <label id="tm_order_reference_num" class="green-text font-weight-bold"><?=$merchant_details['cn_last_name'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_last_name" name="up_last_name" value="<?=$merchant_details['cn_last_name'];?>" <?=$view_access?>>
                                            </div>
                                        
                                            <div class="col-12 col-md-6">
                                                <label class="">Email:</label>
                                                <!-- <label id="tm_voucher" class="font-weight-bold"><?=$merchant_details['ci_email'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_email" name="up_email" value="<?=$merchant_details['ci_email'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Contact Number:</label>
                                                <!-- <label id="tm_subtotal" class="font-weight-bold"><?=$merchant_details['ci_conno'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_conno" name="up_conno" value="<?=$merchant_details['ci_conno'];?>" <?=$view_access?>>
                                            </div> 

                                            <!-- <div class="col-12 col-md-6"> -->
                                       
                                                <!-- <label id="tm_vouchertotal" class="font-weight-bold"><?=$merchant_details['sml_facebook'];?></label> -->
                                               

                                            <!-- </div> -->

                                            <!-- <div class="col-12 col-md-6"> -->
                                                
                                                <!-- <label id="tm_shipping" class="font-weight-bold"><?=$merchant_details['sml_instagram'];?></label> -->
                                               
                                            <!-- </div> -->

                                            <div class="col-12 col-md-6">
                                                <label class="">Social Media Links:</label>
                                                <input type="text" placeholder="Enter social media link here."  class="form-control" id="up_instagram" name="up_instagram" value="<?=$merchant_details['sml_instagram'];?>" <?=$view_access?>>
                                                <br>
                                               <input type="text" placeholder="Enter social media link here." class="form-control" id="up_facebook" name="up_facebook" value="<?=$merchant_details['sml_facebook'];?>" <?=$view_access?>>
                                               <br> 
                                                <?php $socmed_counter = 1;?>
                                                
                                                    <?php foreach($split_sml_fields as $value){
                                                        $value = str_replace(' ', '', $value); 
                                                    ?>
                                                    
                                                    <?php if($value == '' && $socmed_counter == 1){?>
                                                        <?php 
                                                            if($socmed_counter == 1){
                                                                $button_class ="addBtn";
                                                                $button_color ="success";
                                                                $button_type  ="plus";
                                                            }
                                                            else{
                                                                $button_class ="removeBtn";
                                                                $button_color ="danger";
                                                                $button_type  ="minus";
                                                            }
                                                        ?>
                                                        <div class='input-group mb-3 up_socmed_<?=$socmed_counter;?>'>
                            
                                                            <input type="text" class='form-control mb-2 up_socmed_<?=$socmed_counter;?>' placeholder="Enter social media link here." id="up_socmed" name="up_socmed[]" value="<?=$value?>" <?=$view_access?>>
                                                            <div class="input-group-append">
                                                                <button class='btn up_socmed_<?=$socmed_counter;?> btn-<?=$button_color?> <?=$button_class?>' data-value="<?=$socmed_counter?>" type="button" <?=$view_access?>><i class='fa fa-<?=$button_type;?>'></i></button>
                                                            </div>
                                                        </div>
                                                        <?php $socmed_counter++;?>
                                                    <?php } else if($value != ''){?>
                                                        <!-- <label id="tm_shipping" class="font-weight-bold"><?=$value?></label> -->
                                                        <!-- <input type="text" class='form-control mb-2 up_socmed_"<?=$socmed_counter;?>"' id="up_socmed" name="up_socmed[]" value="<?=$value;?>"> -->
                                                            <?php 
                                                                if($socmed_counter == 1){
                                                                    $button_class ="addBtn";
                                                                    $button_color ="success";
                                                                    $button_type  ="plus";
                                                                }
                                                                else{
                                                                    $button_class ="removeBtn";
                                                                    $button_color ="danger";
                                                                    $button_type  ="minus";
                                                                }
                                                            ?>
                                                            <div class='input-group mb-3 up_socmed_<?=$socmed_counter;?>'>
                                                                <input type="text" class='form-control mb-2 up_socmed_<?=$socmed_counter;?>' placeholder="Enter social media link" id="up_socmed" name="up_socmed[]" value="<?=$value?>" <?=$view_access?>>
                                                                <div class="input-group-append">
                                                                    <button class='btn up_socmed_<?=$socmed_counter;?> btn-<?=$button_color?> <?=$button_class?>' data-value="<?=$socmed_counter?>" type="button" <?=$view_access?>><i class='fa fa-<?=$button_type;?>'></i></button>
                                                                </div>
                                                            </div>

                                                        <?php $socmed_counter++;?>
                                                        <?php }else if($socmed_counter == 1){?>
                                                                <?php if($socmed_counter == 1){?>
                                                                    <?php 
                                                                        if($socmed_counter == 1){
                                                                            $button_class ="addBtn";
                                                                            $button_color ="success";
                                                                            $button_type  ="plus";
                                                                        }
                                                                        else{
                                                                            $button_class ="removeBtn";
                                                                            $button_color ="danger";
                                                                            $button_type  ="minus";
                                                                        }
                                                                    ?>
                                                                    <div class='input-group mb-3 up_socmed_<?=$socmed_counter;?>'>
                                                                        <input type="text" class='form-control mb-2 up_socmed_<?=$socmed_counter;?>' placeholder="Enter social media link" id="up_socmed" name="up_socmed[]" value="<?=$value?>" <?=$view_access?>>
                                                                        <div class="input-group-append">
                                                                            <button class='btn up_socmed_<?=$socmed_counter;?> btn-<?=$button_color?> <?=$button_class?>' data-value="<?=$socmed_counter?>" type="button" <?=$view_access?>><i class='fa fa-<?=$button_type;?>'></i></button>
                                                                        </div>
                                                                    </div>
                                                            <?php }?>
                                                        <?php }?>
                                                    <?php }?>

                                                <input type="hidden" id="socmed_counter" value="<?=$socmed_counter?>">
                                                <div class="additionalSocmedFieldsDiv">
                                                
                                                </div>

                                            </div>
                                            
                                            <div class="col-6 col-md-6">
                                            <!-- <label>&nbsp;</label>
                                                <button type="button" class="btn-mobile-w-100 btn btn-primary waves-effect waves-light addSocmedBtn mb-2 mb-md-0" id="addSocmedBtn">Add</button> -->
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Registered Company Name:</label>
                                                <!-- <label id="tm_amount" class="green-text font-weight-bold"><?=$merchant_details['ci_registered_company_name'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_registered_company_name" name="up_registered_company_name" value="<?=$merchant_details['ci_registered_company_name'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Company Description:</label>
                                                <!-- <label id="tm_amount" class="green-text font-weight-bold"><?=$merchant_details['ci_company_description'];?></label> -->
                                                <textarea class="form-control required_fields" id="up_company_description" name="up_company_description" <?=$view_access?>><?=$merchant_details['ci_company_description'];?></textarea>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Shop Name:</label>
                                                <!-- <label id="mr_shopname" class="green-text font-weight"><?=$merchant_details['shop_name'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_shop_name" name="up_shop_name" value="<?=$merchant_details['shop_name'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Product Description:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['shop_description'];?></label> -->
                                                <textarea class="form-control required_fields" id="up_shop_description" name="up_shop_description" <?=$view_access?>><?=$merchant_details['shop_description'];?></textarea>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Unit/Floor/Bldg:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_unit_no'];?></label> -->
                                                <input type="text" class="form-control" id="up_unit_no" name="up_unit_no" value="<?=$merchant_details['a_unit_no'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Street:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_street'];?></label> -->
                                                <input type="text" class="form-control" id="up_street" name="up_street" value="<?=$merchant_details['a_street'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Barangay:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_brgy'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_brgy" name="up_brgy" value="<?=$merchant_details['a_brgy'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Region:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_regCode'];?></label> -->
                                                <!-- <input type="text" class="form-control" id="up_regCode" name="up_regCode" value="<?=$merchant_details['a_regCode'];?>"> -->
                                                <select class="select2 form-control form-control-sm form-state required_fields" id="up_regCode" name="up_regCode" data-reqselect2="yes" <?=$view_access?>>
                                                    <option value="">Select Region</option>
                                                    <?php foreach($region as $row){ ?>
                                                            <?php if($merchant_details['a_regCode'] == $row->regCode){ ?>
                                                                    <option value="<?= $row->regCode ?>" data-regcode="<?= $row->regCode ?>" selected><?= $row->regDesc ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->regCode ?>"  data-regcode="<?= $row->regCode ?>"><?= $row->regDesc ?></option>
                                                            <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Province:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_provCode'];?></label> -->
                                                <!-- <input type="text" class="form-control" id="up_provCode" name="up_provCode" value="<?=$merchant_details['a_provCode'];?>"> -->
                                                <select class="select2 form-control form-control-sm form-state required_fields" id="up_provCode" name="up_provCode" data-reqselect2="yes" <?=$view_access?>>
                                                    <option value="">Select Province</option>
                                                    <?php foreach($province as $row){ ?>
                                                            <?php if($merchant_details['a_provCode'] == $row->provCode){ ?>
                                                                    <option value="<?= $row->provCode ?>" data-provcode="<?= $row->provCode ?>" selected><?= $row->provDesc ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->provCode ?>"  data-provcode="<?= $row->provCode ?>"><?= $row->provDesc ?></option>
                                                            <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Municipality:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['citymunCode'];?></label> -->
                                                <!-- <input type="text" class="form-control" id="up_citymunCode" name="up_citymunCode" value="<?=$merchant_details['a_citymunCode'];?>"> -->
                                                <select class="select2 form-control form-control-sm form-state required_fields" id="up_citymunCode" name="up_citymunCode" data-reqselect2="yes" <?=$view_access?>>
                                                    <option value="">Select City</option>
                                                    <?php foreach($citymun as $row){ ?>
                                                            <?php if($merchant_details['a_citymunCode'] == $row->citymunCode){ ?>
                                                                    <option value="<?= $row->citymunCode ?>" data-citymuncode="<?= $row->citymunCode ?>" selected><?= $row->citymunDesc ?></option>
                                                            <?php }else{ ?>
                                                                    <option value="<?= $row->citymunCode ?>"  data-citymuncode="<?= $row->citymunCode ?>"><?= $row->citymunDesc ?></option>
                                                            <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Zipcode:</label>
                                                <!-- <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['a_brgy'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_zipcode" name="up_zipcode" value="<?=$merchant_details['a_zipcode'];?>" <?=$view_access?>>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Referral Code:</label>
                                                <!-- <label id="mr_shopname" class="green-text font-weight"><?=$merchant_details['referral_code'];?></label> -->
                                                <input type="text" class="form-control" id="up_referral_code"  name="up_referral_code" value="<?=$merchant_details['referral_code'];?>" <?=$view_access?> readonly>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="">Application Status:</label>
                                                <label id="tm_order_status" class="green-text font-weight"><?=get_application_merchant_status($merchant_details['application_status']);?></label>
                                            </div>

                                            <?php if($merchant_details['application_status'] == 0){?>
                                                <div class="col-12 col-md-6">
                                                    <label class="">Decline Notes:</label>
                                                    <label id="tm_order_status" class="green-text font-weight"><?=$merchant_details['reason'];?></label>
                                                </div>
                                            <?php }?>

                                            <div class="col-12 col-md-12">
                                                <div id="pac-container">
                                                    <label for="">Pin Shop Address:</label>
                                                    <input id="pin_address" type="text" placeholder="Search" class = "form-control pr_field detail-input required_fields" name = "pin_address" style = "padding:10px;font-size:15px !important;" value="" <?=$view_access?>>
                                                    <!-- <b><label id="pin_address" class="green-text font-weight"></label></b> -->
                                                    <input type="hidden" name = "loc_latitude" id = "loc_latitude" name = "loc_latitude" class = "pr_field" value = "<?=$merchant_details['pa_latitude']?>">
                                                    <input type="hidden" name = "loc_longitude" id = "loc_longitude" name = "loc_longitude" class = "pr_field" value = "<?=$merchant_details['pa_longitude']?>">
                                                </div>
                                                <div id="map" style = "height:300px;margin-top:30px;"></div>
                                                <div id="infowindow-content">
                                                    <span id="place-name"  class="title"></span><br>
                                                    <span id="place-address"></span>
                                                </div>
                                            </div>

                                 <?php if($merchant_details['application_status'] == 1) { ?>

                                            <div class="col-12-md ShopLogoBanner" style="display: block;">
                                                <div class="row flex-md-row-reverse">
                                                    <div class="col-12 col-md-5 col-lg-4">
                                                        <div class="form-group">
                                                            <label for="" class="d-none d-md-block">Shop Logo Preview</label>
                                                            <!-- <label class="control-label"><?= infoicon_helper_msg('Make sure to meet the minimum image size requirements for better image quality')?> Main Logo <span class="red-asterisk">*</span></label> -->
                                                            <!-- <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                            <div class="img_preview_container square" style="display:none;"></div> -->

                                                            <?php if(!empty($merchant_details['shop_logo'])){ ?>
                                                                <div class="square" id="imgthumbnail-logo"><img src="<?= get_s3_imgpath_upload().'assets/img/shops/'.$merchant_details['shop_logo']; ?>" style="max-width: 100%; height: 170px;"></div>
                                                            <?php }else{ ?>
                                                                <div class="square" id="imgthumbnail-logo"><img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" style="max-width: 100%; height: 170px;"></div>
                                                            <?php } ?>
                                                            <div class="img_preview_container square" style="display:none;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-7 col-lg-8">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label for="">Shop Logo</label>
                                                                    <div class="input-group" style="width:100%;">
                                                                        <div class="custom-file">
                                                                            <input type="file" class="custom-file-input" name="file_container" id="file_container">
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


                           

                                             <div class="col-12-md" >
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
                                                            <!-- <div class="square mb-3 " id="imgthumbnail-banner"><img src="<?= base_url('assets/img/banner-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div>
                                                            <div class="img_preview_container_banner square" style="display:none;"></div> -->

                                                            <?php if(!empty($merchant_details['shop_banner'])){ ?>
                                                                <div class="square" id="imgthumbnail-banner"><img src="<?= get_s3_imgpath_upload().'assets/img/shops-banner/'.$merchant_details['shop_banner']; ?>" style="max-width: 100%;max-height: 100%;"></div><br>
                                                            <?php }else{ ?>
                                                                <div class="square" id="imgthumbnail-banner"><img src="<?= base_url('assets/img/banner-imgplaceholder.jpg') ?>" style="max-width: 100%;max-height: 100%;"></div><br>
                                                            <?php } ?>
                                                            <div class="img_preview_container_banner square" style="display:none;"></div><br>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                <?php }?>

                                <?php if($merchant_details['application_status'] != 1) { ?>
                                         <div class="row">
                                            <div class="col-1-md">
                                                <?php if($merchant_details['shop_logo'] != ''){?>
                                                    <div class="col-md-6" style="display: ;">
                                                        <label class="">Shop Logo:</label>
                                                        <!-- <div class="square" id="imgthumbnail-logo"><img src="<?= get_s3_imgpath_upload().'assets/img/JCWW/products/0bc34b5de0614e1083c27878b707762e/'.$merchant_details['shop_logo']; ?>" style="max-width: 50%;max-height: 50%;"></div> -->
                                                        <div class="square" id="imgthumbnail-logo"><img src="<?= get_s3_imgpath_upload().'assets/img/shops/'.$merchant_details['shop_logo']; ?>" style="max-width: 50%;max-height: 50%;"></div>
                                                    </div>
                                                <?php }?>
                                                
                                                <?php if($merchant_details['shop_banner'] != ''){?>
                                                    <div class="col-md-12" style="display:;">
                                                        <label class="">Shop Banner:</label>
                                                        <!-- <div class="square" id="imgthumbnail-banner"><img src="<?= get_s3_imgpath_upload().'assets/img/all_banner/'.$merchant_details['shop_banner']; ?>" style="max-width: 100%;max-height: 100%;"></div><br> -->
                                                        <div class="square" id="imgthumbnail-banner"><img src="<?= get_s3_imgpath_upload().'assets/img/shops-banner/'.$merchant_details['shop_banner']; ?>" style="max-width: 100%;max-height: 100%;"></div><br>
                                                    </div>
                                                <?php }?>
                                            </div>
                                         </div>

                                <?php }?>   
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <label class="">Bank Name:<span class="red-asterisk">*</span></label>
                                                <!-- <label id="tm_drno" class="green-text font-weight-bold"><?=$merchant_details['bi_bank'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_bank" name="up_bank" value="<?=$merchant_details['bi_bank'];?>" <?=$view_access?>>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="">Bank Account Name:<span class="red-asterisk">*</span></label>
                                                <!-- <label id="tm_drno" class="green-text font-weight-bold"><?=$merchant_details['bi_bank_account_name'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_bank_account_name" name="up_bank_account_name" value="<?=$merchant_details['bi_bank_account_name'];?>" <?=$view_access?>>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="">Bank Account Number:<span class="red-asterisk">*</span></label>
                                                <!-- <label id="tm_drno" class="green-text font-weight-bold"><?=$merchant_details['bi_bank_account_number'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_bank_account_number" name="up_bank_account_number" value="<?=$merchant_details['bi_bank_account_number'];?>" <?=$view_access?>>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="">Bank Account Type:<span class="red-asterisk">*</span></label>
                                                <!-- <label id="tm_drno" class="green-text font-weight-bold"><?=$merchant_details['bi_bank_account_type'];?></label> -->
                                                <input type="text" class="form-control required_fields" id="up_bank_account_type" name="up_bank_account_type" value="<?=$merchant_details['bi_bank_account_type'];?>" <?=$view_access?>>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if($merchant_details['application_status'] == 1){?>
            <div class="row">
                <div class="col-lg-8 col-8 text-right col-md-auto mb-3">
                    <div class="card">
                        <div class="card-body">
                            <!-- <button type="button" class="btn-mobile-w-100 btn btn-success printBtn mb-2 mb-md-0" id="printBtn" data-reference_num="<?=$url_ref_num?>">Print</button> -->
                            <!-- <button type="button" class="btn-mobile-w-100 btn btn-outline-secondary backBtn mb-2 mb-md-0" id="backBtn">Close</button> -->

                            <?php if($this->loginstate->get_access()['merchant_registration']['edit'] == 1 && $merchant_details['application_status'] == 1){ ?>
                                <button type="button" class="btn-mobile-w-100 btn btn-info waves-effect waves-light saveChangesBtn mb-2 mb-md-0" id="saveChangesBtn" data-value="">Save</button>
                            <?php } ?>
                            <?php if($this->loginstate->get_access()['merchant_registration']['approve'] == 1 && $merchant_details['application_status'] == 1){ ?>
                                <button type="button" class="btn-mobile-w-100 btn btn-primary waves-effect waves-light processBtn mb-2 mb-md-0" id="approveBtn" data-value="">Approve</button>
                            <?php } ?>
                            <?php if($this->loginstate->get_access()['merchant_registration']['decline'] == 1 && $merchant_details['application_status'] == 1){ ?>
                                <button type="button" class="btn-mobile-w-100 btn btn-outline-secondary waves-effect waves-light declineBtn mb-2 mb-md-0" id="declineBtn" data-value="">Decline</button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
        
    

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
        </div>
    </form>
</div>



<!-- Approve Modal-->
<div id="approveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Approval</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">Applicant will be successfully added as a merchant.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-app_id="<?=$id;?>">Proceed</button>
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
                        <label class="">Application will be declined.</label>
                    </div>
                </div>
               
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <textarea type="text" class="form-control required_fields" name="dec_reason" id="dec_reason" placeholder="Notes..."></textarea>
                    </div>
                </div>
            </div>
    
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" id="declineButton" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="proceedDecBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-app_id="<?=$id;?>">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Save Modal-->
<div id="saveModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_approve" enctype="multipart/form-data" method="post" action="" >
            <div class="modal-header">
                <div class="col-md-12">
                    <h4 id="tm_header_ref" class="modal-title" style="color:black;">Save</h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="col-12">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <b>Are you sure?</b><br>
                        <label class="">All changes will be saved.</label>
                    </div>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-info cancelBtn waves-effect waves-light" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button type="button" id="saveBtn" class="btn btn-success waves-effect waves-light" aria-label="Close" data-app_id="<?=$id;?>">Proceed</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->

<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/view_merchant_registration.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/developer_settings/google_map.js');?>"></script>
<?php if(ENVIRONMENT == 'production'){?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>
<?php }else{?>
    <script src="http://maps.googleapis.com/maps/api/js?key=<?=get_google_api_key()?>&libraries=places&callback=initializeMaps"
async defer></script>
<?php }?>

