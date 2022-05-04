<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_promotions/');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('Main_promotions/products_discount_list/'.$token);?>">Products Discount</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Add Discount</span>
        
    </div>
</div>
<div class="col-12 " id="pageActive" data-num="3" data-namecollapse="" data-labelname="Products"> 
    <div class="container-fluid col-lg-auto-ml-3">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title m-0">
                                Add Product Discount
                            </h3>
                        </div>
                        <form id="save_voucher" enctype="multipart/form-data" method="post">
                            <div class="card-body center">
                                <div class="row">
                                    <div class="col">
                                        <h1 style="font-size: 16px;">Product Discount Basic Information</h1>
                                    </div>
                                    <div class="col">
                                        <label>
                                            <input type="checkbox" checked id="require_voucher_code" name="require_voucher_code" class="checkbox-template m-r-xs mr-2">
                                            Require Voucher Code
                                        </label>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col">
                                            <div class="form-group">
                                                <label for="vrefnum" class="control-label">Voucher Name:</label>
                                                <input type="text" class="form-control required_fields" name="voucher_name" id="voucher_name">
                                            </div>
                                    </div>
                                    <div class="col">
                                            <div class="form-group">
                                                <label for="vrefnum" class="control-label">Voucher Code:</label>
                                                <input type="text" class="form-control required_fields" name="voucher_code" id="voucher_code">
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="date_issue" class="control-label">Date Issue: </label>    
                                        <input type="datetime-local"  class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                                    </div>
                                    <div class="col">
                                        <label for="date_valid" class="control-label">Valid Until:</label>    
                                        <input type="datetime-local" class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_to" name="date_to" placeholder="MM/DD/YYYY" >
                                    </div>
                                </div>
                                <br>
                                <h1 style="font-size: 16px;">Voucher Conditions</h1>
                                <div class="row">
                                    <div class="col-6">
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
                                        <div class="col">
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
                                        <div class="col" >
                                                <label for="date_valid" class="control-label">Minimum Basket Price:</label>    
                                                <input type="number" class="form-control required_fields" name="minimum_basket_price" id="minimum_basket_price">
                                        </div>
                                        <div class="col">
                                                <label for="date_valid" class="control-label">Usage Quantity:</label>    
                                                <input type="number" class="form-control required_fields" name="usage_quantity" id="usage_quantity">
                                        </div>
                                    </div>

                                    <br>
                                    <div class="row">
                                        <div class="col"  id="table-product-divs">
                                                <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap "  id="table-products">

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
                                        <select name="select_category" class="form-control material_josh form-control-sm enter_search" id="select_category">
                                            <option value="" selected>All Category</option>
                                            <option value="0">Variant</option>
                                            <?php foreach($categories as $row){?>
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


<script>
var token = "<?=$token;?>";
</script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/settings_user_list.js');?>"></script>
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/settings/user_access.js');?>"></script>

