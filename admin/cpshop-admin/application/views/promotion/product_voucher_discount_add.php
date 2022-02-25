<
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="" data-namecollapse="" data-labelname="Add Voucher"> 
    <div class="bc-icons-2 card mb-4">
       <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/promotion_home/'.$token);?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_promotion/voucher_discounts/'.$token);?>">Vouchers Discount</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Add Voucher Discount</li>
        </ol>
    </div>
  
    <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="h4">Details</h3>
                        </div>
                        <form id="save_voucher" enctype="multipart/form-data" method="post">
                            <div class="card-body center">

                               <h1 style="font-size: 16px;">Voucher Basic Information</h1>
                               <br>
                               <div class="row">
                                        <div class="col-md-10">
                                                <div class="form-group">
                                                    <div class="input-group" >
                                                    <label for="date_valid" class="control-label">Voucher Type:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
                                                      <input type="radio" id="product_voucher_type" name="voucher_type"  value="2">
                                                      <label for="html">Product Voucher</label><br>
                                                        &nbsp;&nbsp;
                                                      <input type="radio" id="shop_voucher_type" name="voucher_type"  value="1">
                                                       <label for="html">Shop Voucher</label><br>
                                                    </div>  
                                 
                                                </div>
                                        </div>
                                 </div>
                                <div class="row">
                                        <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="vrefnum" class="control-label">Voucher Name:</label>
                                                    <input type="text" class="form-control required_fields" name="voucher_name" id="voucher_name">
                                                </div>
                                        </div>
                                 </div>
                                 <div class="row">
                                        <div class="col-md-10">
                                                <div class="form-group">
                                                    <label for="vrefnum" class="control-label">Voucher Code:</label>
                                                    <input type="text" class="form-control required_fields" name="voucher_code" id="voucher_code">
                                                </div>
                                        </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-10">
                                            <label for="date_issue" class="control-label">Date Issue: </label>    
                                            <input type="datetime-local"  class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-10">
                                            <label for="date_valid" class="control-label">Valid Until:</label>    
                                            <input type="datetime-local" class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_to" name="date_to" placeholder="MM/DD/YYYY" >
                                    </div>
                                 </div>
                                 <br>
                                 <h1 style="font-size: 16px;">Voucher Conditions</h1>
                                 <br>
                                 <div class="row">
                                    <div class="col-md-10">
                                        <label for="date_valid" class="control-label">Discount Type| Amount:</label>    
                                        <div class="input-group" >
                                            <select name="disc_ammount_type" id="disc_ammount_type" class="form-control material_josh form-control-sm search-input-text enter_search">
                                                    <option value="1">Fixed Rate</option>
                                                    <option value="2">Percentage Rate</option>
                                            </select>
                                            <input type="number" class="form-control required_fields" name="disc_ammount" id="disc_ammount"  size="50">
                                            <input  style="display: none;" readonly class="form-control" name="disc_off" id="disc_off" placeholder="% OFF" size="2">
                                        </div>
                                    </div>
                                 </div>
                                    <br>
                                 <div class="row" style="display: none;" id="maximum_discount_price">
                                    <div class="col-md-10">
                                       

                                        <div class="form-group">
                                            <div class="input-group" >
                                            <label for="date_valid" class="control-label">Maximum Discount Price:</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
                                              <input type="radio" id="set_amount_limit" name="set_amount_limit"  value="1">
                                              <label for="html">Set Amount</label><br>
                                                &nbsp;&nbsp;
                                              <input type="radio" id="set_amount_limit" name="set_amount_limit" value="2">
                                               <label for="html"> No limit</label><br>
                                            </div>  

                                            <div class="col-md-6" style="display: none;" id="disc_ammountdiv"> 
                                                 <input  type="number" class="form-control required_fields" name="disc_ammount_limit" id="disc_ammount_limit"  size="10">
                                            </div>

                                        </div>
                                    </div>
                                 </div>
                                 

                                 <div class="row" >
                                    <div class="col-md-10" >
                                            <label for="date_valid" class="control-label">Minimum Basket Price:</label>    
                                            <input type="number" class="form-control required_fields" name="minimum_basket_price" id="minimum_basket_price">
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-10">
                                            <label for="date_valid" class="control-label">Usage Quantity:</label>    
                                            <input type="number" class="form-control required_fields" name="usage_quantity" id="usage_quantity">
                                    </div>
                                 </div>

                                 <br>
                                 <div class="row">
                                    <div class="col-md-10" style="display: none;" id="table-shop-div">
                                           <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap"  id="table-shop">
                                               <thead>
                                                    <tr>
                                                        <th>Shop Name</th>
                                                        <th style="text-align: center;">Action</th>
                                                    </tr>
                                               </thead>
                                               <tbody id='tbody_shops' class='tbody_shops maintbody'>
                                                
                                                </tbody>
                                           </table>
                                           <button type="button" class="btn btn-primary" id="addShopButton">Add Shop</button>
                                    </div>
                                 </div>

                                 <br>
                                 <div class="row">
                                    <div class="col-md-10" style="display: none;" id="table-product-div">
                                           <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap "  id="table-product">

                                               <thead>
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <th style="text-align: center;">Action</th>
                                                    </tr>
                                               </thead>
                                               <tbody id='tbody_prodpromo' class='tbody_prodpromo maintbody'>
                                                
                                               </tbody>
                                           </table>
                                           <button class="btn btn-primary" type="button"  id="addProductButton" >Add Product</button>
                                    </div>
                                 </div>

                                <div class="form-group row mt-10">       
                                        <div class="col-md-12">
                                            <button style="float:right" class="btn btn-primary saveChangeAvatarBtn">Save</button>
                                        </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>   
            </div>
        </div>
  
</div>



<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Product</h3>
            </div>
            <div class="modal-body">
                <div class="card-body table-body">
             
                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">

                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <?php if($shopid == 0){?> 
                                            <select name="select_shop" id="select_shop" class="form-control material_josh form-control-sm search-input-text enter_search">
                                                <option value="">All Shops</option>
                                                <?php foreach ($shops as $shop): ?>
                                                    <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                                <?php endforeach ?>
                                            </select>
                                        <?php } else{?>
                                            <input type="hidden" class="select_shop" id="select_shop" name="select_shop" value="<?=$shopid?>">
                                        <?php } ?>
                                    </div>
                                
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <select name="select_category" class="form-control material_josh form-control-sm enter_search" id="select_category">
                                            <option value="" selected>All Category</option>
                                            <option value="0">Variant</option>
                                            <?php foreach($product_categories as $row){?>
                                                <option value="<?=$row['id']?>"><?=$row['category_name']?></option>
                                            <?php } ?>
                                        </select> 
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <!-- end - record status is a default for every table -->
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%;" id="table-grid-productpromo"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th style="width:10%;">&nbsp;&nbsp;&nbsp;<input type="checkbox" class="form-control" name="checkbox_all" id="checkbox_all"></th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info clearTableFields" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnConfirm">Confirm</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="addShopModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Add Shop</h3>
            </div>
            <div class="modal-body">
                <div class="card-body table-body">
             
                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">

                    
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <!-- end - record status is a default for every table -->
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%;" id="table-grid-shop"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th style="width:10%;">&nbsp;&nbsp;&nbsp;<input type="checkbox" class="form-control" name="checkbox_all_shop" id="checkbox_all_shop"></th>
                                    <th>Shop Name</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info clearTableFields" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnConfirmShop">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteProdPromoModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Delete Product</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this product? This action cannot be reversed.</p>
                <input type="hidden" id="deleteProdPromoId">
                <input type="hidden" id="deleteProdPromoKey">
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="deleteProdPromoConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteShopModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Shop Product</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove this shop? This action cannot be reversed.</p>
                <input type="hidden" id="deleteShopId">
                <input type="hidden" id="deleteProdPromoKey">
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="deleteShopConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Select Shop</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <b class="mtext_record_status">Enable</b> this record?</p>
                <small>This record will be tagged as </small><small class="mtext_record_status">Enable</small><small>d.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="disable_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/promotion/vouchers_promotion_add.js');?>"></script>
<!-- end - load the footer here and some specific js -->

