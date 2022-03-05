<link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap-tagsinput.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/libs/app.css');?>">
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
  display:none;

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
/* .parentVariantDiv {
    display:none;
} */
</style>

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products/'.$token);?>">Products List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold">View Product</span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular"><?=$itemname;?></span>
    </div>
</div>

<div class="col-12">
    <div class="container-fluid ml-4">
            <div class="row">
                <div class="col-md-12 text-right mb-3 d-md-none">
                    <?php if($prev_product != '0'){?>
                        <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$prev_product)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a>
                    <?php } ?>
                    <?php if($next_product != '0'){?>
                        <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$next_product)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a>
                    <?php } ?>
                </div>
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Details </h3>
                            </div>
                            <div class="card">
                                <form id="form_update" enctype="multipart/form-data" method="post">
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
                                                <div class="col-md-6">
                                                    <div class="form-group" id="category_field">
                                                        <label>Category*</label>
                                                        <select style="height:42px;" type="text" name="f_category" id="f_category" class="form-control" disabled>
                                                            <option value="">-- Select Category --</option>
                                                            <?php
                                                                foreach ($categories as $category) {
                                                                    ?>
                                                                        <option value="<?= $category['id']; ?>"><?= $category['category_name']; ?></option>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_itemname" class="control-label">Product Name*</label>
                                                        <input type="text" class="form-control" name="f_itemname" id="f_itemname" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_summary" class="control-label">Product Summary</label>
                                                        <textarea style="height: 106px;" class="form-control" name="f_summary" id="f_summary" rows="3" disabled></textarea>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12 mb-3">
                                                    <div class="image-upload-card">
                                                        <div class="form-group">
                                                            <label>Product Image <small>(520x520 minimum width x height | JPG, PNG & JPEG | Total of 6 images)</small></label></br>
                                                            <label><small><b>Primary Photo</b></small></label>
                                                            <img src="<?= base_url('assets/img/logo-imgplaceholder.jpg') ?>" id="primary_product" class="img-thumbnail" alt="Responsive image">
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
                                                        <input type="text" class="form-control" name="f_otherinfo" id="f_otherinfo" placeholder="250g/pack, Small Size" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Price*</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_price" id="f_price" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="f_promo_price" class="control-label">Compared at Price</label>
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_compare_at_price" id="f_compare_at_price" value="0" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_price" class="control-label">Product Tags</label><br/>
                                                        <!-- <small>List tags separated by comma (,)</small> -->
                                                        <input type="text" class="form-control" name="f_tags" id="f_tags" placeholder="(Optional)" disabled>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <input type="hidden" name="f_age_restriction_isset" value="0">
                                                        <input type="checkbox" class="form-control-input" id="f_age_restriction_isset" name="f_age_restriction_isset" value="1" checked disabled>
                                                        <label class="form-control-label" for="max_qty_isset">With age restriction</label>
                                                    </div>
                                                </div>
            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="u_id" value="<?=$Id?>">
                            
                            </div>
                        </div>

                
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
                                            <div class="form-group divnostock" id="div_no_of_stocks_0">
                                                        <label>Available quantity</label>
                                                        <input type="number" class="form-control" disabled name="f_no_of_stocks" id="f_no_of_stocks" placeholder="Number of stocks" value="<?=$get_productdetails['no_of_stocks']?>">
                                                        <input type="hidden" name="hidden_f_no_of_stocks_0" id="hidden_f_no_of_stocks_0" value="<?=$get_productdetails['no_of_stocks']?>">
                                                    </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Variants</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- <div class="form-group"> -->
                                                <input type="hidden" name="f_variants_isset" value="0">
                                                <!-- <input type="checkbox" class="form-control-input" id="f_variants_isset" name="f_variants_isset" value="1"> -->
                                                <!-- <label class="form-control-label" for="f_variants_isset">This product has multiple options, like different sizes or colors</label> -->
                                            <!-- </div> -->
                                        </div>
                            <!-- 
                                        <div class="col-12 parentVariantDiv">
                                            <div class="border-bottom w-100"></div>
                                        </div> -->

                                        <div class="col-12 parentVariantDiv">
                                            <label class="form-control-label">&nbsp;</label>
                                        </div>

                                        <div class="col-md-12 parentVariantDiv">
                                            <label class="form-control-label font-weight-bold">Options</label>
                                        </div>

                                        <?php 
                                            $getVariantsOption[0]['variant_type'] = (!empty($getVariantsOption[0]['variant_type'])) ? $getVariantsOption[0]['variant_type'] : "";
                                            $getVariantsOption[0]['variant_list'] = (!empty($getVariantsOption[0]['variant_list'])) ? $getVariantsOption[0]['variant_list'] : "";
                                            $getVariantsOption[1]['variant_type'] = (!empty($getVariantsOption[1]['variant_type'])) ? $getVariantsOption[1]['variant_type'] : "";
                                            $getVariantsOption[1]['variant_list'] = (!empty($getVariantsOption[1]['variant_list'])) ? $getVariantsOption[1]['variant_list'] : "";
                                            $getVariantsOption[2]['variant_type'] = (!empty($getVariantsOption[2]['variant_type'])) ? $getVariantsOption[2]['variant_type'] : "";
                                            $getVariantsOption[2]['variant_list'] = (!empty($getVariantsOption[2]['variant_list'])) ? $getVariantsOption[2]['variant_list'] : "";
                                        ?>


                                        <div class="col-md-12 table-responsive parentVariantDiv">
                                            <table class='table table-striped table-hover table-bordered table-grid display nowrap'>
                                                <thead>
                                                    <tr>
                                                        <th scope='col'><b>Variant</b></th>
                                                        <th scope='col'><b>Price</b></th>
                                                        <th scope='col'><b>SKU</b></th>
                                                        <th scope='col'><b>Barcode</b></th>
                                                        <!-- <th scope='col'></th> -->
                                                    </tr>
                                                </thead>
                                                <tbody id='tbody_variants' class='tbody_variants'>
                                            <?php foreach($getVariants as $row){?>
                                                    <tr class="variant_tr_<?=$row['Id']?>">
                                                        <td class="variant_tr_<?=$row['Id']?>"><span class="variant_id<?=$row['Id']?>"><?=$row['itemname']?></span><input type="hidden" name="variant_id[]" value="<?=$row['Id']?>"><input type="hidden" class="variant_id<?=$row['Id']?>" name="variant_name[]" value="<?=$row['itemname']?>" data-variant_id="<?=$row['Id']?>"></td>
                                                        <td class="variant_tr_<?=$row['Id']?>"><input type="text" class="form-control allownumericwithdecimal" name="variant_price[]" value="<?=$row['price']?>" disabled></td>
                                                        <td class="variant_tr_<?=$row['Id']?>"><input type="text" class="form-control" name="variant_sku[]" value="<?=$row['inv_sku']?>" disabled></td>
                                                        <td class="variant_tr_<?=$row['Id']?>"><input type="text" class="form-control" name="variant_barcode[]" value="<?=$row['inv_barcode']?>" disabled></td>
                                                        <!-- <td style="width:15%;"class="variant_tr_<?=$row['Id']?>"><a  href="<?=base_url('Main_products/update_variants/'.$token.'/'.$row['Id'].'/'.$Id);?>" class='btn btn-success' data-value='<?=$row['Id']?>'><i class='fa fa-pencil'></i></a>&nbsp;<button type='button' id='removeVariantSpec' class='btn btn-danger' data-value='<?=$row['Id']?>'><i class='fa fa-trash'></i></button></td> -->
                                                    </tr>

                                            <?php }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="row">
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

                                        <div class="col-4 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_length" id="f_length" placeholder="Length" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_width" id="f_width" placeholder="Width" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4 weightdiv">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control allownumericwithdecimal" name="f_height" id="f_height" placeholder="Height" value="0.0" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">inches</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 weightdiv">
                                            <div class="note">Please fill out the fields accurately. This is used to calculate the shipping rates at checkout and label prices during fulfilLment. Exceeding the maximum weight (20kg) and dimension (20"x20”x20") for the default toktok rates can still be catered but the shipping fee will depend on the amount you've provided in the Shipping and Delivery Settings Tab.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            
                        <?php if($shopid == 0){?>
                            <div class="col-lg-12">&nbsp;</div>
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Admin Settings</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

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
                                                            <input type="text" class="form-control allownumericwithdecimal" name="f_disc_rate" id="f_disc_rate" disabled>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }?>
        
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="f_itemid" class="control-label">Referral Commission ItemID</label>
                                                    <input type="text" class="form-control" name="f_itemid" id="f_itemid" disabled>
                                                </div>
                                            </div>

                                            <div class="col-4 col-md-6">
                                                <div class="form-group">
                                                    <label for="f_rate" class="control-label">Startup</label> 
                                                    <div class="input-group mb-3">
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_startup" id="f_startup" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_jc" id="f_jc" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_mcjr" id="f_mcjr" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_mc" id="f_mc" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_mcsuper" id="f_mcsuper" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_mcmega" id="f_mcmega" disabled>
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
                                                        <input type="text" class="form-control allownumericwithdecimal" name="f_others" id="f_others" disabled>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">%</span>
                                                        </div>
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
                        <?php }?>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-12 col-lg-7 text-right">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button>
                            <!-- <button type="submit" class="btn btn-success saveBtn">Save</button> -->
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
</div>

<input type="hidden" name="branchid" id="branchid" value="<?=$branchid?>">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/products/view_products.js');?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous"></script>
<script src="<?=base_url('assets/js/libs/bootstrap-tagsinput.min.js');?>"></script>
<script src="<?=base_url('assets/js/libs/app.js');?>"></script>