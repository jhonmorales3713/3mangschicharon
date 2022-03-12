
<link rel="stylesheet" href="<?=base_url('assets/css/libs/bootstrap-tagsinput.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/libs/app.css');?>">
<style>
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
</style>
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products_home/Products');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/products/'.$token);?>">Products List</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_products/update_products/'.$token.'/'.$parent_Id);?>"><?=$get_parentProduct['name']?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Add Size</span>
        
    </div>
</div>

<div class="col-12">
 <form id="form_save" enctype="multipart/form-data" method="post" class="ml-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sizes</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <?php if(!empty($getVariants)){?>
                                                <?php foreach($getVariants as $row){?>
                                                    <div class="alert alert-secondary" role="alert">
                                                        <div class="form-group">
                                                            <a style="width:100%;" href="<?=base_url('admin/Main_products/update_variants/'.$token.'/'.$row['id'].'/'.$parent_Id);?>">
                                                                <?=$row['name']?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                            <?php } else{?>
                                                <div class="text-center">
                                                    <label class="control-label">No existing size.</label>
                                                </div>
                                            <?php }?>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
            
                <div class="row">
                    <div class="col-12 mb-3">
                    <!-- <h1> Add Products</h1> -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Details
                                </h3>
                            </div>
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
                                            <div class="col-md-6" style="display:none;">
                                                <div class="form-group" id="category_field">
                                                    <label>Category*</label>
                                                    <select style="height:42px;" type="text" name="f_category" id="f_category" class="form-control">
                                                        <option value="0" selected>Select Category</option>
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

                                            <div class="col-md-12" style="display:none;">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="f_parent_product_id" id="f_parent_product_id" value="<?=$parent_Id?>">
                                                </div>
                                            </div>

                                            <!-- <?php foreach($getVariantsOption as $row){?>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="f_name" class="control-label"><?=$row['variant_type']?></label>
                                                        <input type="text" class="form-control" name="f_variant_name[]" id="f_variant_name">
                                                    </div>
                                                </div>
                                            <?php }?> -->

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="f_itemname" class="control-label">Size Name*</label>
                                                    <input type="text" class="form-control" name="f_itemname" id="f_itemname">
                                                </div>
                                            </div>

                                            <div class="col-md-12" hidden>
                                                <div class="form-group">
                                                    <label for="f_summary" class="control-label">Size Summary</label>
                                                    <textarea style="height: 106px;" class="form-control" name="f_summary" id="f_summary" rows="3"></textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mb-3">
                                                <div class="image-upload-card">
                                                    <div class="form-group">
                                                        <label>Product Image <small>(520x520 minimum width x height | JPG, PNG & JPEG | Total of 6 images)</small></label><br/>
                                                        <label><small><b>Primary Photo</b></small></label>
                                                        <img src="<?= base_url('assets/img/placeholder-500x500.jpg') ?>" id="primary_product" class="img-thumbnail" alt="Responsive image">
                                                        </br>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" name="product_image[]" id="product_image_multip" multiple>
                                                                <label class="custom-file-label" id="file_label">Choose file</label>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div id="product-placeholder">
                                                            
                                                        </div>
                
                                                        <div id="sortable" class="imagepreview" style="display:none;"></div><br>
                                                        <div class="oldimgurl" style="display:none;"></div>
                
                
                                                        <img src="" id="product_preview_multiple" class="img-responsive">
                                                        <input type="text" class="hidden" name="current_product_url" id="current_product_url">
                                                        <input type="text" class="hidden" name="upload_checker" id="upload_checker">
                                                        <button type="button" class="btn btn-primary btn-sm" id="change-product-image" hidden>Change photo</button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="f_otherinfo" class="control-label">Other Info (Packing/Variations)*</label>
                                                    <input type="text" class="form-control" name="f_otherinfo" id="f_otherinfo" placeholder="ex. 250g/pack, Small Size" value="none">
                                                </div>
                                            </div> -->
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="f_price" class="control-label">Price*</label>
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_price" id="f_price">
                                                </div>
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="f_promo_price" class="control-label">Compared at Price</label>
                                                    <input type="text" class="form-control allownumericwithdecimal" name="f_compare_at_price" id="f_compare_at_price" value="0" placeholder="Compared at Price">
                                                </div>
                                            </div> -->
                                            <div class="col-md-6" style="display:none;">
                                                <div class="form-group">
                                                    <label for="f_promo_price" class="control-label">Status</label>
                                                    <select class="form-control" id="f_status" name="f_status">
                                                        <option value="1" selected>Enabled</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- <div class="col-6">
                                                <div class="form-group">
                                                    <label for="f_price" class="control-label">Product Tags (Optional)</label><br/>
                                                    <small></small>
                                                    <input type="text" class="form-control" name="f_tags" id="f_tags" placeholder="List tags separated by comma (,)">
                                                </div>
                                            </div> -->

                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Inventory</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- <div class="col-md-6">
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
                                    </div> -->

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" name="f_max_qty_isset" value="0">
                                            <input type="checkbox" class="form-control-input" id="f_max_qty_isset" name="f_max_qty_isset" value="1" checked>
                                            <label class="form-control-label" for="max_qty_isset">Max quantity per checkout</label>
                                        </div>
                                        <div class="form-group">
                                            <!-- <label>Max quantity per checkout</label> -->
                                            <input type="number" class="form-control" name="f_max_qty" id="f_max_qty" placeholder="Max quantity" value="1">
                                        </div>
                                    </div>
        
                                    <!-- <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="hidden" name="f_tq_isset" value="0">
                                            <input type="checkbox" class="form-control-input" id="f_tq_isset" name="f_tq_isset" value="1" checked>
                                            <label class="form-control-label" for="f_tq_isset">Track quantity</label>
                                            <br>
                                            <input type="hidden" name="f_cont_selling_isset" value="0">
                                            <input type="checkbox" class="form-control-input contsellingdiv" id="f_cont_selling_isset" name="f_cont_selling_isset" value="1">
                                            <label class="form-control-label contsellingdiv" for="f_cont_selling_isset">Continue selling when out of stock</label>
                                            
                                        </div>
                                    </div> -->
                    <!--                                     
                                    <div class="col-md-6 nostocksdiv" id="nostocksdiv">
                                        <div class="form-group">
                                            <label>Shop Branch:</label>
                                            <select class="form-control" name="f_delivery_location" id="f_delivery_location">
                                                <option value="0" selected>Main</option>
                                            </select>
                                        </div>
                                    </div> -->


                                    <div class="col-md-6 nostocksdiv" id="nostocksdiv2">
                                        <div class="form-group divnostock mt-3" id="div_no_of_stocks_0">
                                            <label>Available quantity</label>
                                            <input type="number" class="form-control parentProductStock" name="f_no_of_stocks" id="f_no_of_stocks" placeholder="Number of stocks" >
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-3" style="display:none;">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sizes</h3>
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
            <div class="col-12 col-lg-5 text-right"></div>
            <div class="col-12 col-lg-7 text-right">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-outline-secondary cancelBtn" id="backBtn">Close</button>
                        <button type="submit" class="btn btn-success saveBtn">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <div class="footer">
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-12 text-right">
                
            </div>
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
                    <li class="list-group-item"><?=$products['set_product_arrangement'];?>.<?=$products['name'];?></li>
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




<input type="hidden" id="token" value="<?=$token?>">
<input type="hidden" id="branchid" value="<?=$branchid?>">
<input type="hidden" id="shopid" value="<?=$shopid?>">
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/products/add_variant.js');?>"></script>

<script src="<?=base_url('assets/js/libs/bootstrap-tagsinput.min.js');?>"></script>
<script src="<?=base_url('assets/js/libs/app.js');?>"></script>