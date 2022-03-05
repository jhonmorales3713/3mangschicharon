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
  <?php if($branchid != 0){ ?>
    display:none;
  <?php } ?>
}

#sortable { 
    list-style-type: none; 
    margin: 0; padding: 0; 
    width: 100%; 
    }
#sortable li { 
    margin: 3px 3px 3px 0; 
    padding: 1px; float: left; 
    width: 100%; height: 100%; 
    font-size: 4em; 
    text-align: center; 
    }

.varoptionDiv2 {
    display:none;
}
.varoptionDiv3 {
    display:none;
}
.parentVariantDiv {
    display:none;
}
.alert {
  position: relative;
  display:block;
}
</style>

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products/'.$token);?>">Products List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/update_products/'.$token.'/'.$parent_Id);?>"><?=$get_parentProduct['itemname']?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Update Variant</span>
        
    </div>
</div>
<div class="col-12">
<form id="form_update" enctype="multipart/form-data" method="post">
<div class="container-fluid">
        <div class="row">
            
            <div class="col-lg-5">
                <div class="row">
                    <?php if(!empty($getVariants)){?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Variants</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <?php foreach($getVariants as $row){?>
                                                    <div class="alert alert-secondary" role="alert">
                                                        <a style="width:100%;" href="<?=base_url('Main_products/update_variants/'.$token.'/'.$row['Id'].'/'.$parent_Id);?>">
                                                            <?php if($Id == $row['Id']){?>
                                                                <b><?=$row['itemname']?></b>
                                                            <?php } else{?>
                                                                <?=$row['itemname']?>
                                                            <?php }?>
                                                        </a>
                                                    </div>
                                                <?php }?>
                                                <br>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
        
                    <?php if($shopid == 0){?>
                        <div class="col-lg-12">&nbsp;</div>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Admin Settings</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">

                                    <div class="col-12">
                                            <div class="form-group">
                                                <input type="hidden" name="featured_prod_isset" value="0">
                                                   <?php if($get_productdetails['featured_prod_isset'] == 0) {?>
                                                       <input type="checkbox" class="form-control-input" id="featured_prod_isset" name="featured_prod_isset" value="1">
                                                    <?php }else{?>
                                                       <input type="checkbox" class="form-control-input" id="featured_prod_isset" checked name="featured_prod_isset" value="1">
                                                    <?php }?>
                                                <label class="form-control-label" for="featured_prod_isset">Set as featured Products</label>
                                                <br>
                                            </div>
                                        </div>

                                        <div class="col-12 contsellingdiv">
                                                <div class="form-group">  
                                        <?php if ($get_productdetails['featured_prod_isset'] > 0) {?>  
                                                    <select class="select2 form-control required_fields" id="entry-feat-product-arrangement" name="entry-feat-product-arrangement" data-reqselect2="yes">     
                                            <?php }else{?>        
                                                    <select class="select2 form-control  required_fields " id="entry-feat-product-arrangement" name="entry-feat-product-arrangement" data-reqselect2="yes">     
                                        <?php }?>       
                                                       <option  selected value=" <?php echo $get_productdetails['set_product_arrangement'] ?>">  <?php echo $get_productdetails['set_product_arrangement'] ?></option>
                                                        <option value="">Select Arrangement</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                    </select>
                                                </div>
                                        </div>
                                          
                                                 
                                         
                                        <div class="col-12">
                                            <div class="form-group">
                                                <input type="hidden" name="f_admin_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_admin_isset" name="f_admin_isset" value="1">
                                                <label class="form-control-label" for="f_admin_isset">Enable Commission Rate</label>
                                                <br>
                                            </div>
                                        </div>
    
                                        <?php if(ini() != 'toktokmall'){?>
                                            <div class="col-12 adminsettings_div">
                                                <div class="form-group">
                                                    <label for="f_ratetype" class="control-label">Discount Rate Type</label>
                                                    <select style="height:42px;" type="text" name="f_disc_ratetype" id="f_disc_ratetype" class="form-control">
                                                        <option value="p">Percentage</option>
                                                        <option value="f">Fixed Amount</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 adminsettings_div">
                                                <div class="form-group">
                                                    <label for="f_rate" class="control-label">Discount Rate</label> 
                                                    <!-- <span class="badge badge-primary" data-toggle="tooltip" data-placement="top" title="Maximum rate is 1.0 if the rate type is percentage and any amount if the rate type is fixed amount.">?</span> -->
                                                    <input type="number" placeholder="1.0" step="0.01" min="0" max="1" class="form-control allownumericwithdecimal" name="f_disc_rate" id="f_disc_rate" >
                                                    <div class="note">
                                                        Maximum rate is 1.0 if the rate type is percentage and any amount if the rate type is fixed amount.
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else{?>
                                            <div class="col-12" style="display:none;">
                                                <div class="form-group">
                                                    <label for="f_ratetype" class="control-label">Discount Rate Type</label>
                                                    <select style="height:42px;" type="text" name="f_disc_ratetype" id="f_disc_ratetype" class="form-control">
                                                        <option value="p" selected>Percentage</option>
                                                        <!-- <option value="f">Fixed Amount</option> -->
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- <div class="col-12 adminsettings_div">
                                                <div class="form-group">
                                                    <label for="f_rate" class="control-label">Discount Rate</label> 
                                                    <span class="badge badge-primary" data-toggle="tooltip" data-placement="top" title="Maximum rate is 1.0 if the rate type is percentage and any amount if the rate type is fixed amount.">?</span>
                                                    <input type="number"class="form-control allownumericwithdecimal" name="f_disc_rate" id="f_disc_rate" >
                                                </div>
                                            </div> -->

                                            <div class="col-12 adminsettings_div">
                                                <div class="form-group">
                                                    <label for="f_rate" class="control-label">Merchant Commission Rate</label> 
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control allownumericwithdecimal commcapping" name="f_disc_rate" id="f_disc_rate" onkeypress="return isNumberKey(event)">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }?>
    
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label for="f_itemid" class="control-label">Referral Commission ItemID</label>
                                                <input type="text" class="form-control" name="f_itemid" id="f_itemid" >
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group">
                                            <label class="control-label">&nbsp; </label>
                                            <label class="control-label">&nbsp; </label>
                                                <button type="button" class="btn btn-primary" style="width:100%;" id="generateItemIDBtn" >Generate</button>
                                                <input type="hidden" class="form-control" name="f_company_initial" id="f_company_initial" value="<?=company_initial()?>">
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label"> Account Type Commission Rate</label> 
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">Startup</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_startup" id="f_startup" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">JC</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_jc" id="f_jc" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">MCJR</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcjr" id="f_mcjr" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">MC</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mc" id="f_mc" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">MCSUPER</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcsuper" id="f_mcsuper" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">MCMEGA</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_mcmega" id="f_mcmega" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 col-md-6">
                                            <div class="form-group">
                                                <label for="f_rate" class="control-label">Others</label> 
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_others" id="f_others" onkeypress="return isNumberKey(event)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-12">
                                            <div class="form-group">
                                                <div class="alert alert-warning" role="alert">
                                                    Percentage of Account Type Commission Rate should not be more than 50% of Merchant Commission Rate.
                                                </div>
                                            </div>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <input type="hidden" class="hidden" value="0" name="f_disc_ratetype" id="f_disc_ratetype">
                        <input type="hidden" class="hidden" value="0" name="f_disc_rate" id="f_disc_rate">
                        <input type="hidden" class="hidden" value="" name="f_itemid" id="f_itemid">
                        <input type="hidden" class="hidden" value="0" name="f_admin_isset" id="f_admin_isset">
                        <input type="hidden" class="hidden" value="0" name="f_startup" id="f_startup">
                        <input type="hidden" class="hidden" value="0" name="f_jc" id="f_jc">
                        <input type="hidden" class="hidden" value="0" name="f_mcjr" id="f_mcjr">
                        <input type="hidden" class="hidden" value="0" name="f_mc" id="f_mc">
                        <input type="hidden" class="hidden" value="0" name="f_mcsuper" id="f_mcsuper">
                        <input type="hidden" class="hidden" value="0" name="f_mcmega" id="f_mcmega">
                        <input type="hidden" class="hidden" value="0" name="f_others" id="f_others">
                    <?php }?>

                </div>
            </div>

            <div class="col-lg-7">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Details </h3>
                            </div>
                            <?php if($branchid == 0){?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row hidden">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>ID</label>
                                                        <input type="text" name="f_id" id="f_id" class="form-control" value="0" >
                                                        <input type="text" name="productimage_changes" id="productimage_changes" class="form-control" value="0" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <?php if($shopid == 0){?>
                                                    <div class="col-md-6">
                                                        <div class="form-group" id="shop_field">
                                                            <label>Shop Name*</label>
                                                            <select style="height:42px;" type="text" name="f_member_shop" id="f_member_shop" class="form-control" disabled>
                                                                <!-- <option value="">-- Select Shop --</option> -->
                                                                <?php
                                                                    foreach ($shops as $shop) {
                                                                        ?>
                                                                            <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php }else{ ?>
                                                    <div hidden class="col-md-6">
                                                        <div class="form-group" id="shop_field">
                                                            <label>Shop Name*</label>
                                                            <select style="height:42px;" type="text" name="f_member_shop" id="f_member_shop" class="form-control">
                                                                <option value="<?=$shopid; ?>" selected></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <div class="col-md-6" style="display:none;">
                                                    <div class="form-group" id="category_field">
                                                        <label>Category*</label>
                                                        <select style="height:42px;" type="text" name="f_category" id="f_category" class="form-control">
                                                            <option value="0" selected>-- Select Category --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" style="display:none;">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" name="f_parent_product_id" id="f_parent_product_id" value="<?=$parent_Id?>">
                                                    </div>
                                                </div>
                                                
                                                <?php 
                                                    $variant_counter = 0;
                                                    $variant_names   = explode("/", $get_productdetails['itemname'])
                                                ?>
                                                
                                                <!-- <?php foreach($getVariantsOption as $row){?>
                                                    <?php if($row['variant_type'] != ""){?>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="f_itemname" class="control-label"><?=$row['variant_type']?></label>
                                                                <input type="text" class="form-control" name="f_variant_name[]" id="f_variant_name" value="<?=(!empty($variant_names[$variant_counter])) ? $variant_names[$variant_counter] : "";?>">
                                                            </div>
                                                        </div>
                                                        <?php $variant_counter++ ?>
                                                    <?php }?>
                                                <?php }?> -->

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_itemname" class="control-label">Variant Name*</label>
                                                        <input type="text" class="form-control" name="f_itemname" id="f_itemname">
                                                    </div>
                                                </div>

                                                <div class="col-md-12" hidden>
                                                    <div class="form-group">
                                                        <label for="f_summary" class="control-label">Variant Summary</label>
                                                        <textarea style="height: 106px;" class="form-control" name="f_summary" id="f_summary" rows="3"></textarea>
                                                    </div>
                                                </div>

                                                <div class="col-12 mb-3">
                                                    <div class="image-upload-card">
                                                        <div class="form-group">
                                                            <label>Product Image <small>(520x520 minimum width x height | JPG, PNG & JPEG | Total of 6 images)</small></label></br>
                                                            <label><small><b>Primary Photo</b></small></label>
                                                            <img src="<?= base_url('assets/img/placeholder-500x500.jpg') ?>" id="primary_product" class="img-thumbnail" alt="Responsive image">
                                                            </br>
                                                            <div class="input-group">
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" name="product_image[]" id="product_image_multip" multiple>
                                                                    <label class="custom-file-label" id="file_label">Choose file</label>
                                                                </div>
                                                            </div>
                                                            </br>
                                                            <div id="product-placeholder">
                                                                
                                                            </div>
                
                                                            <div id="sortable" class="imagepreview2 d-flex flex-row bd-highlight mb-3 col-12" style="display:none;"></div>
                                                            <div class="imagepreview mb-3" style="display:none;"></div>
                
                                                            <div class="oldimgurl" style="display:none;"></div>
                
                                                            
                
                
                
                                                            <img src="" id="product_preview_multiple" class="img-responsive">
                                                            <input type="text" class="hidden" name="current_product_url" id="current_product_url">
                                                            <input type="text" class="hidden" name="upload_checker" id="upload_checker">
                                                            <button type="button" class="btn btn-primary btn-sm" id="change-product-image" hidden>Change photo</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_otherinfo" class="control-label">Other Info (Packing/Variations)*</label>
                                                        <input type="text" class="form-control" name="f_otherinfo" id="f_otherinfo" placeholder="250g/pack, Small Size" value="none">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Price*</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_price" id="f_price" >
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_promo_price" class="control-label">Compared at Price</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_compare_at_price" id="f_compare_at_price" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Product Tags</label><br/>
                                                        <!-- <small>List tags separated by comma (,)</small> -->
                                                        <input type="text" class="form-control" name="f_tags" id="f_tags" placeholder="(Optional)">
                                                    </div>
                                                </div>

                                                <?php if(ini() == 'jcww'){ ?>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="f_itemname" class="control-label">Delivery Areas*</label>
                                                            <select class="select2 form-control form-control-sm form-state taginput-field" name="f_delivery_areas[]" id="f_delivery_areas" multiple="multiple">
                                                                <?php foreach($get_province as $prov){ ?>
                                                                    <option value="<?=$prov['provCode']?>"><?=$prov['provDesc']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                <div class="col-md-12" style="display:none;">
                                                    <div class="form-group">
                                                        <input type="hidden" name="f_age_restriction_isset" value="0">
                                                        <input type="checkbox" class="form-control-input" id="f_age_restriction_isset" name="f_age_restriction_isset" value="1" checked>
                                                        <label class="form-control-label" for="f_age_restriction_isset">With age restriction</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6" style="display:none;">
                                                    <div class="form-group">
                                                        <label for="f_arrangement" class="control-label">Arrangement</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_arrangement" id="f_arrangement">
                                                    </div>
                                                </div>
            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <input type="hidden" id="u_id" value="<?=$Id?>">
                            <?php }else{?>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row hidden">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>ID</label>
                                                        <input type="text" name="f_id" id="f_id" class="form-control" value="0" >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <?php if($shopid == 0){?>
                                                    <div class="col-md-6" hidden>
                                                        <div class="form-group" id="shop_field">
                                                            <label>Shop Name*</label>
                                                            <select style="height:42px;" type="text" name="f_member_shop" id="f_member_shop" class="form-control" disabled>
                                                                <!-- <option value="">-- Select Shop --</option> -->
                                                                <?php
                                                                    foreach ($shops as $shop) {
                                                                        ?>
                                                                            <option value="<?= $shop['id']; ?>"><?= $shop['shopname']; ?></option>
                                                                        <?php
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php }else{ ?>
                                                    <div hidden class="col-md-6">
                                                        <div class="form-group" id="shop_field">
                                                            <label>Shop Name*</label>
                                                            <select style="height:42px;" type="text" name="f_member_shop" id="f_member_shop" class="form-control">
                                                                <option value="<?=$shopid; ?>" selected></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <div class="col-md-6" style="display:none;">
                                                    <div class="form-group" id="category_field">
                                                        <label>Category*</label>
                                                        <select style="height:42px;" type="text" name="f_category" id="f_category" class="form-control">
                                                            <option value="0" selected>-- Select Category --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <div class="image-upload-card">
                                                        <div class="form-group">
                                                            <label>Product Image <small>(520x520 minimum width x height | JPG, PNG & JPEG | Total of 6 images)</small></label>
                                                            <div class="input-group" hidden>
                                                                <div class="custom-file">
                                                                    <input type="file" class="custom-file-input" name="product_image[]" id="product_image_multip" multiple>
                                                                    <label class="custom-file-label" id="file_label">Choose file</label>
                                                                </div>
                                                            </div>
                                                            <div id="product-placeholder">
                                                                
                                                            </div>
                
                                                            <div class="imagepreview2 d-flex flex-row bd-highlight mb-3 col-12" style="display:none;"></div>
                                                            <div class="imagepreview mb-3" style="display:none;"></div>
                
                                                            <div class="oldimgurl" style="display:none;"></div>
                
                                                            
                
                
                
                                                            <img src="" id="product_preview_multiple" class="img-responsive">
                                                            <input type="text" class="hidden" name="current_product_url" id="current_product_url">
                                                            <input type="text" class="hidden" name="upload_checker" id="upload_checker">
                                                            <button type="button" class="btn btn-primary btn-sm" id="change-product-image" hidden>Change photo</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_itemname" class="control-label">Product Name*</label>
                                                        <input type="text" class="form-control" name="f_itemname" id="f_itemname" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_summary" class="control-label">Product Summary</label>
                                                        <textarea style="height: 106px;" class="form-control" name="f_summary" id="f_summary" rows="3" readonly></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" style="display:none;">
                                                    <div class="form-group">
                                                        <label for="f_otherinfo" class="control-label">Other Info (Packing/Variations)*</label>
                                                        <input type="text" class="form-control" name="f_otherinfo" id="f_otherinfo" placeholder="250g/pack, Small Size" value="none" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Price*</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_price" id="f_price" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_promo_price" class="control-label">Compared at Price</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_compare_at_price" id="f_compare_at_price" value="0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Product Tags</label><br/>
                                                        <!-- <small>List tags separated by comma (,)</small> -->
                                                        <input type="text" class="form-control" name="f_tags" id="f_tags" placeholder="(Optional)" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-12" style="display:none;">
                                                    <div class="form-group">
                                                        <input type="hidden" name="f_age_restriction_isset" value="0">
                                                        <input type="checkbox" class="form-control-input" id="f_age_restriction_isset" name="f_age_restriction_isset" value="1" checked>
                                                        <label class="form-control-label" for="f_age_restriction_isset">With age restriction</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_arrangement" class="control-label">Arrangement</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_arrangement" id="f_arrangement">
                                                    </div>
                                                </div>

                                                <?php if(ini() == 'jcww'){ ?>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="f_itemname" class="control-label">Delivery Areas</label>
                                                            <select class="select2 form-control form-control-sm form-state taginput-field" name="f_delivery_areas[]" id="f_delivery_areas" multiple="multiple">
                                                                <?php foreach($get_province as $prov){ ?>
                                                                    <option value="<?=$prov['provCode']?>"><?=$prov['provDesc']?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>
            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <input type="hidden" id="u_id" value="<?=$Id?>">
                            <?php }?>
                        </div>
                    </div>

                    <?php if($branchid == 0){?>                                    
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Inventory</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="f_inv_sku" class="control-label">SKU (Stock Keeping Unit)</label>
                                                <input type="text" class="form-control" name="f_inv_sku" id="f_inv_sku">
                                            </div>
                                        </div>
        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="f_inv_barcode" class="control-label">Barcode (ISBN, UPC, GTIN, etc.)</label>
                                                <input type="text" class="form-control" name="f_inv_barcode" id="f_inv_barcode">
                                            </div>
                                        </div>
        
                                        <div hidden class="col-md-6">
                                            <div class="form-group">
                                                <label for="f_uom" class="control-label">UOM ID</label>
                                                <input type="text" class="form-control" name="f_uom" id="f_uom" value="0">
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <!-- <div class="form-group"> -->
                                                <input type="hidden" name="f_max_qty_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_max_qty_isset" name="f_max_qty_isset" value="1" checked>
                                                <label class="form-control-label" for="max_qty_isset">Max quantity per checkout</label>
                                            <!-- </div> -->
                                        </div>

                                        <div class="col-md-6 maxqtydiv">
                                            <div class="form-group">
                                                <!-- <label>Max quantity per checkout</label> -->
                                                <input type="number" class="form-control" name="f_max_qty" id="f_max_qty" placeholder="Max quantity" value="1">
                                            </div>
                                        </div>
        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="hidden" name="f_tq_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_tq_isset" name="f_tq_isset" value="1" checked>
                                                <label class="form-control-label" for="f_tq_isset">Track quantity</label>
                                                <br>
                                                <input type="hidden" name="f_cont_selling_isset" value="0">
                                                <input type="checkbox" class="form-control-input contsellingdiv" id="f_cont_selling_isset" name="f_cont_selling_isset" value="1">
                                                <label class="form-control-label contsellingdiv" for="f_cont_selling_isset">Continue selling when out of stock</label>
                                                
                                            </div>
                                        </div>
        
                                        <!-- <div class="col-md-6 nostocksdiv">
                                        
                                            <?php if($branchid == 0){?>
                                                <div class="form-group">
                                                    <label>Shop Branch:</label>
                                                    <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                        <option value="0" selected>Main</option>
                                                        <?php foreach($get_branchdetails as $row){?>
                                                            <option value="<?=$row['id']?>"><?=$row['branchname']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            <?php }else{?>
                                                <div class="form-group">
                                                    <label>Shop Branch:</label>
                                                    <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                        <?php foreach($get_branchdetails as $row){?>
                                                            <option value="<?=$row['id']?>"><?=$row['branchname']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            <?php }?>

                                        </div> -->

                                        <div class="col-md-6 nostocksdiv" id="nostocksdiv2">

                                        <div class="form-group divnostock" id="div_no_of_stocks_0">
                                                    <label>Available quantity</label>
                                                    <input type="number" class="form-control" name="f_no_of_stocks" id="f_no_of_stocks" placeholder="Number of stocks" value="<?=$get_productdetails['no_of_stocks']?>">
                                                    <input type="hidden" name="hidden_f_no_of_stocks_0" id="hidden_f_no_of_stocks_0" value="<?=$get_productdetails['no_of_stocks']?>">
                                                </div>
                                        </div>
                                        <!-- <div class="col-md-6 nostocksdiv">
                                            <?php if($branchid == 0){?>
                                                <div class="form-group divnostock" id="div_no_of_stocks_0">
                                                    <label>Available quantity(Main Branch)</label>
                                                    <input type="number" class="form-control" name="f_no_of_stocks_0" id="f_no_of_stocks_0" placeholder="Number of stocks" value="<?=$get_productdetails['inv_qty']?>">
                                                    <input type="hidden" name="hidden_f_no_of_stocks_0" id="hidden_f_no_of_stocks_0" value="<?=$get_productdetails['inv_qty']?>">
                                                </div>

                                                <?php if($get_branchdetails != false){?>
                                                    <?php foreach($get_branchdetails as $row){?>
                                                        <?php $input_name = 'f_no_of_stocks_0'?>
                                                        <?php $hidden_input_name = 'hidden_f_no_of_stocks_0'?>
                                                        <?php $div_name = 'div_no_of_stocks_0'?>
                                                        <div class="form-group divnostock" id="<?=$div_name?>" style="display:none">
                                                            <label>Available quantity(<?=$row['branchname']?>)</label>
                                                            <input type="number" class="form-control" name="<?=$input_name?>" id="<?=$input_name?>" placeholder="Number of stocks" value="<?=$row['inv_qty']?>">
                                                            <input type="hidden" name="<?=$hidden_input_name?>" id="<?=$hidden_input_name?>" value="<?=$row['inv_qty']?>">
                                                        </div>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }else{?>
                                                <?php if($get_branchdetails != false){?>
                                                    <?php foreach($get_branchdetails as $row){?>
                                                        <?php $input_name = 'f_no_of_stocks_0'?>
                                                        <?php $div_name = 'div_no_of_stocks_0'?>
                                                        <div class="form-group divnostock" id="<?=$div_name?>">
                                                            <label>Available quantity(<?=$row['branchname']?>)</label>
                                                            <input type="number" class="form-control" name="<?=$input_name?>" id="<?=$input_name?>" placeholder="Number of stocks" value="<?=$row['inv_qty']?>">
                                                        </div>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }?>
                                        </div> -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }else{?>
                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Inventory</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6" hidden>
                                            <div class="form-group">
                                                <label for="f_inv_sku" class="control-label">SKU (Stock Keeping Unit)</label>
                                                <input type="text" class="form-control" name="f_inv_sku" id="f_inv_sku">
                                            </div>
                                        </div>
        
                                        <div class="col-md-6" hidden>
                                            <div class="form-group">
                                                <label for="f_inv_barcode" class="control-label">Barcode (ISBN, UPC, GTIN, etc.)</label>
                                                <input type="text" class="form-control" name="f_inv_barcode" id="f_inv_barcode">
                                            </div>
                                        </div>
        
                                        <div hidden class="col-md-6" hidden>
                                            <div class="form-group">
                                                <label for="f_uom" class="control-label">UOM ID</label>
                                                <input type="text" class="form-control" name="f_uom" id="f_uom" value="0">
                                            </div>
                                        </div>

                                        <div class="col-md-12" hidden>
                                            <!-- <div class="form-group"> -->
                                                <input type="hidden" name="f_max_qty_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_max_qty_isset" name="f_max_qty_isset" value="1" checked>
                                                <label class="form-control-label" for="max_qty_isset">Max quantity per checkout</label>
                                            <!-- </div> -->
                                        </div>

                                        <div class="col-md-6 maxqtydiv" hidden>
                                            <div class="form-group">
                                                <label>Max quantity per checkout</label>
                                                <input type="number" class="form-control" name="f_max_qty" id="f_max_qty" placeholder="Max quantity" value="1">
                                            </div>
                                        </div>
        
                                        <div class="col-md-12" hidden>
                                            <div class="form-group">
                                                <input type="hidden" name="f_tq_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_tq_isset" name="f_tq_isset" value="1" checked>
                                                <label class="form-control-label" for="f_tq_isset">Track quantity</label>
                                                <br>
                                                <input type="hidden" name="f_cont_selling_isset" value="0">
                                                <input type="checkbox" class="form-control-input contsellingdiv" id="f_cont_selling_isset" name="f_cont_selling_isset" value="1">
                                                <label class="form-control-label contsellingdiv" for="f_cont_selling_isset">Continue selling when out of stock</label>
                                                
                                            </div>
                                        </div>


        
                                        <div class="col-md-6 nostocksdiv">
                                        
                                            <?php if($branchid == 0){?>
                                                <div class="form-group">
                                                    <label>Shop Branch:</label>
                                                    <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                        <option value="0" selected>Main</option>
                                                        <?php foreach($get_branchdetails as $row){?>
                                                            <option value="<?=$row['id']?>"><?=$row['branchname']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            <?php }else{?>
                                                <div class="form-group">
                                                    <label>Shop Branch:</label>
                                                    <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                        <?php foreach($get_branchdetails as $row){?>
                                                            <option value="<?=$row['id']?>"><?=$row['branchname']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                            <?php }?>

                                        </div>

                                        <div class="col-md-6 nostocksdiv">
                                            <?php if($branchid == 0){?>
                                                <div class="form-group divnostock" id="div_no_of_stocks_0">
                                                    <label>Available quantity(Main Branch)</label>
                                                    <input type="number" class="form-control" name="f_no_of_stocks_0" id="f_no_of_stocks_0" placeholder="Number of stocks" value="<?=$get_productdetails['inv_qty']?>">
                                                </div>

                                                <?php if($get_branchdetails != false){?>
                                                    <?php foreach($get_branchdetails as $row){?>
                                                        <?php $input_name = 'f_no_of_stocks_0'?>
                                                        <?php $div_name = 'div_no_of_stocks_0'?>
                                                        <div class="form-group divnostock" id="<?=$div_name?>" style="display:none">
                                                            <label>Available quantity(<?=$row['branchname']?>)</label>
                                                            <input type="number" class="form-control" name="<?=$input_name?>" id="<?=$input_name?>" placeholder="Number of stocks" value="<?=$row['inv_qty']?>">
                                                        </div>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }else{?>
                                                <?php if($get_branchdetails != false){?>
                                                    <?php foreach($get_branchdetails as $row){?>
                                                        <?php $input_name = 'f_no_of_stocks_0'?>
                                                        <?php $div_name = 'div_no_of_stocks_0'?>
                                                        <div class="form-group divnostock" id="<?=$div_name?>">
                                                            <label>Available quantity(<?=$row['branchname']?>)</label>
                                                            <?php if(ini() == 'jcww'){?>
                                                                <input type="number" class="form-control" placeholder="Number of stocks" value="<?=$row['inv_qty']?>" readonly>
                                                                <input type="hidden" class="form-control" name="<?=$input_name?>" id="<?=$input_name?>" placeholder="Number of stocks" value="<?=$row['inv_qty']?>">
                                                            <?php }else{?>
                                                                <input type="number" class="form-control" name="<?=$input_name?>" id="<?=$input_name?>" placeholder="Number of stocks" value="<?=$row['inv_qty']?>">
                                                            <?php }?>
                                                        </div>
                                                    <?php }?>
                                                <?php }?>
                                            <?php }?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                    <?php if($branchid == 0){?> 
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Shipping</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <input type="hidden" name="f_shipping_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_shipping_isset" name="f_shipping_isset" value="1" checked>
                                                <label class="form-control-label" for="f_shipping_isset">This is a physical product</label>
                                                <br>
                                            </div>
                                        </div>
        
                                        <div class="col-12 weightdiv">
                                            <div class="form-group">
                                                <label for="f_itemid" class="control-label">Weight</label><br>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_weight" id="f_weight" placeholder="0.0" value="0.0">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">grams</span>
                                                    </div>
                                                </div>
                                                <!-- <div class="note">Used to calculate shipping rates at checkout and label prices during fulfillment.</div> -->
                                            </div>
                                        </div>

                                        <div class="col-12 weightdiv">
                                            <label for="f_length" class="control-label">Parcel Size</label>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_length" id="f_length" placeholder="Length" value="0.0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_width" id="f_width" placeholder="Width" value="0.0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_height" id="f_height" placeholder="Height" value="0.0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 weightdiv">
                                            <div class="note">Please fill out the fields accurately. This is used to calculate the shipping rates at checkout and label prices during fulfillment. Exceeding the maximum weight (20kg) and dimension (20"x20”x20") for the default toktok rates can still be catered but the shipping fee will depend on the amount you've provided in the Shipping and Delivery Settings Tab.</div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }else{?>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Shipping</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group" hidden>
                                                <input type="hidden" name="f_shipping_isset" value="0">
                                                <input type="checkbox" class="form-control-input" id="f_shipping_isset" name="f_shipping_isset" value="1" checked>
                                                <label class="form-control-label" for="f_shipping_isset">This is a physical product</label>
                                                <br>
                                            </div>
                                        </div>
        
                                        <div class="col-12 weightdiv">
                                            <div class="form-group">
                                                <label for="f_itemid" class="control-label">Weight</label><br>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_weight" id="f_weight" placeholder="0.0" value="0.0" readonly>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">grams</span>
                                                    </div>
                                                </div>
                                                <!-- <div class="note">Used to calculate shipping rates at checkout and label prices during fulfillment.</div> -->
                                            </div>
                                        </div>

                                        <div class="col-12 weightdiv">
                                            <label for="f_length" class="control-label">Parcel Size</label>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_length" id="f_length" placeholder="Length" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_width" id="f_width" placeholder="Width" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_height" id="f_height" placeholder="Height" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 weightdiv">
                                            <div class="note">Please fill out the fields accurately. This is used to calculate the shipping rates at checkout and label prices during fulfillment. Exceeding the maximum weight (20kg) and dimension (20"x20”x20") for the default toktok rates can still be catered but the shipping fee will depend on the amount you've provided in the Shipping and Delivery Settings Tab.</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>

                    <div class="col-12 mb-3" style="display:none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Variants</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- <div class="form-group"> -->
                                            <input type="hidden" name="f_variants_isset" value="0">
                                        <!-- </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>
        <div class="row">
        <div class="col-12 col-lg-12 text-right">&nbsp;</div>
        <div class="col-12 col-lg-5 text-right"></div>
            <div class="col-12 col-lg-7 text-right">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button>
                        <button type="submit" class="btn btn-success saveBtn">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
            
        </div>
    </form>
</div>

<!-- Modal -->

<div class="modal fade" id="show_feature_prod_modal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Set as featured product</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to set this as featured products?</p>
                <br>
                <p>List of Featured Products.</p>
                <ol class="list-group">
                <?php foreach ($featured_products as $products): ?>
                    <li class="list-group-item"><?=$products['set_product_arrangement'];?>.<?=$products['itemname'];?></li>
                <?php endforeach ?>
                </ol>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="check_rabutton" >Confirm</button>
            </div>
        </div>
    </div>
</div>


<input type="hidden" name="branchid" id="branchid" value="<?=$branchid?>">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/products/update_variants.js');?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>