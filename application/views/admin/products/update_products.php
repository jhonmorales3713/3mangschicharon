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

.parentVariantDiv {
    display:none;
}
.parentVariantDiv2 {
    display:none;
}
.bootstrap-tagsinput input {
    width:100%;
}
.td-center{
    text-align: center;
}
</style>

<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products/'.$token);?>">Products List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold">Update Products</span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">
            
        <?=$itemname?>
            <!-- <?php if($prev_product != ''){?>
                <a href="<?=base_url('admin/Main_products/update_products/'.$token.'/'.$prev_product)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i><?=$prev_product?></a>
            <?php }else if($next_product != ''){?>
                <a href="<?=base_url('admin/Main_products/update_products/'.$token.'/'.$next_product)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i><?=$prev_product?></a>
            <?php } else {?> -->
            <!-- <?php } ?> -->
        </span>
        
    </div>
</div>

<div class="modal fade" id="inventory_modal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Product Inventory</h3>
            </div>
            <div class="modal-body">
                <form name="form_inventory" id="form_inventory">
                    
                    <!-- <hr>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="text" name="f_qty" id="f_qty" class="form-control" value="0" >
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label>Date Manufactured</label>
                                
                                <input type="text" name="f_date_manufactured"autocomplete="false" id="f_date_manufactured" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#datepicker">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label>Date Expiration</label>
                                <input type="text" name="f_date_expiration"autocomplete="false" id="f_date_expiration" class="form-control  datetimepicker-input" data-toggle="datetimepicker" data-target="#datepicker">
                            </div>
                        </div>
                    </div>-->
                    
                    <div class="row">
                        <div class="col-12 d-flex align-items-end flex-column">
                            <button class="btn btn-primary" type="button" id="btnAddInventory">Add to Inventory</button>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-12 table-responsive ">
                            <table class='table table-striped table-hover table-bordered table-grid display nowrap'>
                                <thead>
                                    <tr>
                                        <th scope='col'><b>#</b></th>
                                        <th scope='col'><b>Quantity</b></th>
                                        <th scope='col'><b>Date Manufactured</b></th>
                                        <th scope='col'><b>Date Expiration</b></th>
                                        <th scope='col'><b>Action</b></th>
                                    </tr>
                                </thead>
                                <tbody id='tbody_inventory' class='tbody_inventory'>
                                    <tr>
                                        <td colspan="5" class="td-center">No existing inventory.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-2">
                            <span class="bg-danger p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Expired Stocks
                        </div>
                        <div class="col-2">
                            <span class="bg-warning p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Expiring soon
                        </div>
                        <div class="col-2">
                            <span class="bg-secondary p-1 mr-1" style="width:100px !important;">&nbsp;&nbsp;&nbsp;&nbsp;</span>Out of Stocks
                        </div>
                    </div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="btnCloseInventory" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-id="<?=$Id?>" id="btnSaveinventory" >Save</button>
            </div>
        </div>
    </div>
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
                    <li class="list-group-item"><?=$products['set_product_arrangement'];?>.<?=$products['itemname'];?><?=$products['itemname'];?></li>
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

<div class="modal fade" id="deleteVariantModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Delete Size</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this size? This action cannot be reversed.</p>
                <input type="hidden" id="deleteVariantId">
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="deleteVariantConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteVariantOptModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Delete Variant Option</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this variant option? This action cannot be reversed.</p>
                <input type="hidden" id="deleteVariantOptionId">
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="removeVariantOptBtn" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="col-12 " id="pageActive" data-num="3" data-namecollapse="" data-labelname="Products"> 
    <div class="container-fluid ml-3">
        <div class="row">
            <div class="col-md-12 text-right mb-3 d-md-none">
                <?php if($prev_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$prev_product)?>" type="button" class="prevBtn mx-3" id="prevBtn"><i class="fa fa-arrow-left"></i></a>
                <?php } ?>
                <?php if($next_product != '0'){?>
                    <a href="<?=base_url('Main_products/update_products/'.$token.'/'.$next_product)?>" type="button" class="nextBtn mx-3" id="nextBtn"><i class="fa fa-arrow-right"></i></a>
                <?php } ?>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-12 col-lg-7 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Details </h3>
                            </div>
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
                                                    <select style="height:42px;" type="text" name="f_category" id="f_category" class="form-control">
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

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="f_itemname" class="control-label">Product Name*</label>
                                                    <input type="text" class="form-control" name="f_itemname" id="f_itemname">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="f_summary" class="control-label">Product Summary</label>
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
                                            
                                            <!-- <div class="col-md-6 f_otherinfodiv">
                                                <div class="form-group">
                                                    <label for="f_otherinfo" class="control-label">Other Info (Packing/Variations)*</label>
                                                    <input type="text" class="form-control" name="f_otherinfo" id="f_otherinfo" placeholder="250g/pack, Small Size" >
                                                </div>
                                            </div> -->
                                            <div class="col-md-6 parentInvDiv">
                                                <div class="form-group">
                                                    <label for="f_price" class="control-label">Price*</label>
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_price" id="f_price" >
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="f_promo_price" class="control-label">Compared at Price</label>
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_compare_at_price" id="f_compare_at_price" value="0">
                                                </div>
                                            </div> -->
                                            <div class="col-md-6 ">
                                                <div class="form-group">
                                                    <label for="f_price" class="control-label">Product Tags</label><br/>
                                                    <!-- <small>List tags separated by comma (,)</small> -->
                                                    <input type="text" class="form-control tags" name="f_tags" id="f_tags" placeholder="(Optional)">
                                                </div>
                                            </div>

                                            <!-- <div class="col-md-12 hidden">
                                                <div class="form-group">
                                                    <input type="hidden" name="f_age_restriction_isset" value="0">
                                                    <input type="checkbox" class="form-control-input" id="f_age_restriction_isset" name="f_age_restriction_isset" value="1" checked>
                                                    <label class="form-control-label" for="max_qty_isset">With age restriction</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="f_arrangement" class="control-label">Arrangement</label>
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_arrangement" id="f_arrangement">
                                                </div>
                                            </div> -->
        
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="u_id" value="<?=$Id?>">
                        </div>
                    </div>                         
                    <div class="col-12 col-lg-5 mb-3 ">
                        
                        <!-- <div class="card row mb-3 parentInvDiv">
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
                                            <input type="text" class="form-control" name="f_uom" id="f_uom" value="0" placeholder="UOM ID">
                                        </div>
                                    </div> 

                                    <div class="col-md-12">
                                         <div class="form-group">
                                            <input type="hidden" name="f_max_qty_isset" value="0">
                                            <input type="checkbox" class="form-control-input" id="f_max_qty_isset" name="f_max_qty_isset" value="1" checked>
                                            <label class="form-control-label" for="max_qty_isset">Max quantity per checkout</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12 maxqtydiv">
                                        <div class="form-group">
                                            <label>Max quantity per checkout</label>
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
                                    
                                    <div class="col-md-6 nostocksdiv hidden" id="nostocksdiv">
                                        <div class="form-group">
                                            <label>Shop Branch:</label>
                                            <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                <option value="0" selected>Main</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 nostocksdiv" id="nostocksdiv2">
                                        <div class="form-group divnostock" id="div_no_of_stocks_0">
                                            <label>Available quantity</label>
                                            <input type="number" disabled class="form-control parentProductStock" name="f_no_of_stocks" id="f_no_of_stocks" placeholder="Number of stocks" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6 nostocksdiv" >
                                        <button class="btn btn-primary" type="button" id="btninventory" > Manage Inventory</button>
                                    </div>

                                </div>
                            </div>
                        </div> -->
                        
                        <div class="card row">
                            <div class="card-header">
                                <h3 class="card-title">Sizes</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-12"> -->
                                        <!-- <div class="form-group"> -->
                                            <!-- <input type="hidden" name="f_variants_isset" value="0">
                                            <input type="checkbox" class="form-control-input" id="f_variants_isset" name="f_variants_isset" value="1">
                                            <label class="form-control-label" for="f_variants_isset">This product has multiple sizes</label> -->
                                        <!-- </div> -->
                                    <!-- </div> -->

                                    <div class="col-12 parentVariantDiv2">
                                        <div class="border-bottom w-100"></div>
                                    </div>

                                    <div class="col-12 parentVariantDiv2">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <div class="col-md-12 parentVariantDiv2">
                                        <label class="form-control-label font-weight-bold">Variation</label>
                                    </div>

                                    <?php 
                                        $getVariantsOption[0]['variant_type'] = (!empty($getVariantsOption[0]['variant_type'])) ? $getVariantsOption[0]['variant_type'] : "";
                                        $getVariantsOption[0]['variant_list'] = (!empty($getVariantsOption[0]['variant_list'])) ? $getVariantsOption[0]['variant_list'] : "";
                                        $getVariantsOption[1]['variant_type'] = (!empty($getVariantsOption[1]['variant_type'])) ? $getVariantsOption[1]['variant_type'] : "";
                                        $getVariantsOption[1]['variant_list'] = (!empty($getVariantsOption[1]['variant_list'])) ? $getVariantsOption[1]['variant_list'] : "";
                                        $getVariantsOption[2]['variant_type'] = (!empty($getVariantsOption[2]['variant_type'])) ? $getVariantsOption[2]['variant_type'] : "";
                                        $getVariantsOption[2]['variant_list'] = (!empty($getVariantsOption[2]['variant_list'])) ? $getVariantsOption[2]['variant_list'] : "";
                                    ?>

                                    <div class="col-md-12 parentVariantDiv2">
                                        <label class="form-control-label font-weight-normal">Variation 1</label>
                                    </div>

                                    <div class="col-md-3 parentVariantDiv2">
                                        <input type="text" class="form-control" name="f_var_option_name[]" id="f_var_option_name" placeholder="Type" value="<?=$getVariantsOption[0]['variant_type']?>">
                                    </div>

                                    <div class="col-md-7 parentVariantDiv2">
                                        <input type="text" class="form-control taginputVariant f_var_option_list1" name="f_var_option_list[]" id="f_var_option_list" data-role="tagsinput" placeholder="<?=($getVariantsOption[0]['variant_list'] == '') ? "Press enter to separate":""?>" value="<?=$getVariantsOption[0]['variant_list']?>" ></input>
                                    </div>

                                    <div class="col-12 parentVariantDiv2">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <div class="col-md-12 parentVariantDiv2 varoptionDiv2">
                                        <label class="form-control-label font-weight-normal">Variation 2</label>
                                    </div>

                                    <div class="col-md-3 parentVariantDiv2 varoptionDiv2">
                                        <input type="text" class="form-control varoption2" name="f_var_option_name[]" id="f_var_option_name" placeholder="Type" value="<?=$getVariantsOption[1]['variant_type']?>">
                                    </div>

                                    <div class="col-md-7 parentVariantDiv2 varoptionDiv2">
                                        <input type="text" class="form-control taginputVariant varoption2 f_var_option_list2" name="f_var_option_list[]" id="f_var_option_list" data-role="tagsinput" placeholder="<?=($getVariantsOption[1]['variant_list'] == '') ? "Press enter to separate":""?>" value="<?=$getVariantsOption[1]['variant_list']?>" ></input>
                                    </div>

                                    <!-- <div class="col-md-2 parentVariantDiv varoptionDiv2">
                                        <u><a href="javascript:void(0)" class="removeVariantBtn" data-value="2">Remove</a></u>
                                    </div> -->

                                    <div class="col-12 parentVariantDiv2 varoptionDiv2">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <div class="col-md-12 parentVariantDiv2 varoptionDiv3">
                                        <label class="form-control-label font-weight-normal">Variation 3</label>
                                    </div>

                                    <div class="col-md-3 parentVariantDiv2 varoptionDiv3">
                                        <input type="text" class="form-control varoption3" name="f_var_option_name[]" id="f_var_option_name" placeholder="Type" value="<?=$getVariantsOption[2]['variant_type']?>">
                                    </div>

                                    <div class="col-md-7 parentVariantDiv2 varoptionDiv3">
                                        <input type="text" class="form-control taginputVariant varoption3 f_var_option_list3" name="f_var_option_list[]" id="f_var_option_list" data-role="tagsinput" placeholder="<?=($getVariantsOption[2]['variant_list'] == '') ? "Press enter to separate":""?>" value="<?=$getVariantsOption[2]['variant_list']?>"></input>
                                    </div>

                                    <!-- <div class="col-md-2 parentVariantDiv varoptionDiv3">
                                        <u><a href="javascript:void(0)" class="removeVariantBtn" data-value="3">Remove</a></u>
                                    </div> -->

                                    <!-- ////// -->
                                    <div class="col-12 parentVariantDiv2">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <div class="col-12 parentVariantDiv2">
                                        <div class="border-bottom w-100"></div>
                                    </div>

                                    <div class="col-12 parentVariantDiv2">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <?php if($this->loginstate->get_access()['products']['create'] == 1){ ?>
                                        <div class="col-3 parentVariantDiv">
                                            <a href="<?=base_url('admin/Main_products/add_variant/'.$token.'/'.$Id)?>" class="btn btn-primary">Add size</a>
                                        </div>
                                    <?php } ?>
                                
                                    

                                    <!-- <div class="col-9 parentVariantDiv">
                                        <button type="button" id="addOptionVariantBtn" class="btn btn-primary" style="color:white;<?=($getVariantsOption[2]['variant_type'] == "") ? null : "display:none;"?>">Add another option</button>
                                    </div>
                               -->

                                    

                                    <div class="col-12 parentVariantDiv">
                                        <label class="form-control-label">&nbsp;</label>
                                    </div>

                                    <div class="col-12 parentVariantDiv">
                                        <div class="alert alert-warning" role="alert">
                                            Any changes you made above will not affect or reflect on    the size table below.
                                        </div>
                                    </div>

                                    <div class="col-md-12 table-responsive parentVariantDiv">
                                        <table class='table table-striped table-hover table-bordered table-grid display nowrap'>
                                            <thead>
                                                <tr>
                                                    <th scope='col'><b>Size</b></th>
                                                    <th scope='col'><b>Price</b></th>
                                                    <th scope='col'><b>Status</b></th>
                                                    <th scope='col'></th>
                                                </tr>
                                            </thead>
                                            <tbody id='tbody_variants' class='tbody_variants'>
                                            <?php 
                                                if($branchid != 0){
                                                    $readonly = 'readonly';
                                                }
                                                else{
                                                    $readonly = '';
                                                }
                                            ?>

                                            <?php foreach($getVariants as $row){?>
                                                <tr class="variant_tr variant_tr_<?=$row['id']?>">
                                                    <td class="variant_tr_<?=$row['id']?>"><span class="variant_id<?=$row['id']?>"><?=$row['name']?></span><input type="hidden" name="variant_id[]" value="<?=$row['id']?>"><input type="hidden" class="variant_id<?=$row['id']?>" name="variant_name[]" value="<?=$row['name']?>" data-variant_id="<?=$row['id']?>"></td>
                                                    <td class="variant_tr_<?=$row['id']?>"><input type="text" class="form-control allownumericwithdecimal" name="variant_price[]" value="<?=$row['price']?>" <?=$readonly;?>></td>
                                                    <?php $variant_status = ($row['enabled'] == 1) ? 'Active' : 'Inactive';?>
                                                    <td class="variant_tr_<?=$row['id']?>"><span class="variant_status_label_<?=$row['id']?>"><?=$variant_status;?></span><input type="hidden" class="form-control variant_status_<?=$row['id']?>" name="variant_status[]" value="<?=$row['enabled']?>" <?=$readonly;?>></td>
                                                    <td style="width:25%;"class="td-center variant_tr_<?=$row['id']?>">
                                                        <?php if($this->loginstate->get_access()['products']['update'] == 1){ ?>
                                                            <a  href="<?=base_url('admin/Main_products/update_variants/'.$token.'/'.$row['id'].'/'.$Id);?>" class='btn btn-primary' data-value='<?=$row['id']?>'><i class='fa fa-pencil'></i></a>&nbsp;
                                                        <?php }?>
                                                        <?php if($this->loginstate->get_access()['products']['delete'] == 1){ ?>
                                                            <button type='button' id='removeVariantSpec' class='btn btn-danger' data-value='<?=$row['id']?>'><i class='fa fa-trash-o'></i></button>
                                                        <?php }?>
                                                        <?php if($this->loginstate->get_access()['products']['disable'] == 1){ ?>
                                                            <?php if($row['enabled'] == 1){ ?>
                                                                <button type='button' id='enableVariant' class='btn btn-success enableVariant<?=$row['id']?>' data-value='<?=$row['id']?>' style='display:none;'><i class='fa fa-check-circle'></i></button>
                                                                <button type='button' id='disableVariant' class='btn btn-warning disableVariant<?=$row['id']?>' data-value='<?=$row['id']?>'><i class='fa fa-times-circle'></i></button>

                                                            <?php } else{?>
                                                                <button type='button' id='disableVariant' class='btn btn-warning disableVariant<?=$row['id']?>' data-value='<?=$row['id']?>' style='display:none;'><i class='fa fa-times-circle'></i></button>
                                                                <button type='button' id='enableVariant' class='btn btn-success enableVariant<?=$row['id']?>' data-value='<?=$row['id']?>'><i class='fa fa-check-circle'></i></button>
                                                            <?php }?>
                                                        <?php }?>
                                                    </td>
                                                </tr>

                                           <?php }?>
                                            <?php if(empty($getVariants)){?>
                                                <tr>
                                                    <td colspan="5" class="td-center">No existing variant.</td>
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
            </div>
            
            <div class="col-lg-5">
                <div class="row">
                    <!-- 
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
                                                 <div class="note">Used to calculate shipping rates at checkout and label prices during fulfillment.</div> 
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
                                                <div class="note">Used to calculate shipping rates at checkout and label prices during fulfillment.</div> 
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
                        <input type="hidden" class="hidden" name="f_startup" id="f_startup">
                        <input type="hidden" class="hidden" name="f_jc" id="f_jc">
                        <input type="hidden" class="hidden" name="f_mcjr" id="f_mcjr">
                        <input type="hidden" class="hidden" name="f_mc" id="f_mc">
                        <input type="hidden" class="hidden" name="f_mcsuper" id="f_mcsuper">
                        <input type="hidden" class="hidden" name="f_mcmega" id="f_mcmega">
                        <input type="hidden" class="hidden" name="f_others" id="f_others">
                    <?php }?> -->
                </div>
            </div>


        </div>
        <div class="row">
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
</div>

<input type="hidden" name="branchid" id="branchid" value="<?=$branchid?>">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/products/update_products.js');?>"></script>

<script src="<?=base_url('assets/js/libs/bootstrap-tagsinput.min.js');?>"></script>s
<script src="<?=base_url('assets/js/libs/app.js');?>"></script>