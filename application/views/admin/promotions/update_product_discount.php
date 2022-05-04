<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_promotions/');?>"><?=$active_page?></a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('Main_promotions/products_discount_list/'.$token);?>">Products Discount</a></span>
        &nbsp;<span class="fa fa-chevron-right"></span>&nbsp;
        <span class="font-weight-regular">Update Discount</span>
        
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
                                Update Product Discount
                            </h3>
                        </div>
                        <form id="form_discount"name="form_discount" enctype="multipart/form-data" method="post">
                            <div class="card-body center">
                                <div class="row">
                                    <div class="col">
                                        <h1 style="font-size: 16px;">Product Discount Basic Information</h1>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <!-- <div class="col">
                                        <label for="date_issue" class="control-label">Date Issue: </label> 
                                        <input type="datetime-local"  class="form-control" style="z-index: 2 !important; text-align:center;" value="<?=date('Y-m-d\TH:i:s',strtotime($discount_info["start_date"]))?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                                    </div>
                                    <div class="col">
                                        <label for="date_valid" class="control-label">Valid Until:</label>    
                                        <input type="datetime-local" class="form-control" style="z-index: 2 !important; text-align:center;" value="<?=date('Y-m-d\TH:i:s',strtotime($discount_info["end_date"]))?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" >
                                    </div> -->
                                    <div class="col">
                                        <label for="date_issue" class="control-label">Date Issue: </label> 
                                        <input type="date"  class="form-control" style="z-index: 2 !important; text-align:center;" value="<?=date('Y-m-d',strtotime($discount_info["start_date"]))?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                                    </div>
                                    <div class="col">
                                        <label for="date_valid" class="control-label">Valid Until:</label>    
                                        <input type="date" class="form-control" style="z-index: 2 !important; text-align:center;" value="<?=date('Y-m-d',strtotime($discount_info["end_date"]))?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" >
                                    </div>
                                </div>
                                <br>
                                <!-- <h1 style="font-size: 16px;">Discount Conditions</h1> -->
                                <div class="row">
                                    <div class="col-6">
                                        <label for="date_valid" class="control-label ">Discount Type:</label>    
                                        <div class="input-group" >
                                            <select name="disc_ammount_type" id="disc_ammount_type" class="form-control material_josh form-control-sm search-input-text enter_search">
                                                    <option value="1" <?=$discount_info['disc_amount_type'] == 1?"selected":''?>>Fixed Rate</option>
                                                    <option value="2" <?=$discount_info['disc_amount_type'] == 2?"selected":''?>>Percentage Rate</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="date_valid" class="control-label discount_type_label" id="disc_amount_label">Discount Amount:</label> 
                                            <input type="number" value="<?=$discount_info['disc_amount']?>" class="form-control required_fields allownumericwithdecimal" name="disc_ammount" id="disc_ammount"  size="50">
                                    </div>
                                </div>
                                    <div class="row" >
                                        <div class="col">
                                            <div class="input-group" >
                                                <label for="set_max_amount" class="control-label"><input  <?=$discount_info['max_discount_isset'] == 1?"checked":''?> type="checkbox" id="set_max_amount" name="set_max_amount"  value="1">Set Maximum Discount Price</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
                                                
                                            </div>
                                                    
                                        </div>
                                    </div>
                                    <div class="row" style="display: <?=$discount_info['max_discount_isset'] == 1?"":'none'?>;" id="maximum_discount_price">
                                        <div class="col">
                                            <div class="form-group">
                                                    <label for="disc_ammount_limit" class="control-label">Maximum Discount Price:</label><br>  
                                                    <input  type="number" class="form-control required_fields allownumericwithdecimal" name="disc_ammount_limit" value="<?=$discount_info['max_discount_price']?>" id="disc_ammount_limit"  size="10">
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div class="row" >
                                        <!-- <div class="col" >
                                                <label for="date_valid" class="control-label">Minimum Basket Price:</label>    
                                                <input type="number" class="form-control required_fields" name="minimum_basket_price" id="minimum_basket_price">
                                        </div> -->
                                        <!-- <div class="col">
                                                <label for="date_valid" class="control-label">Usage Quantity:</label>    
                                                <input type="number" class="form-control required_fields allownumericwithoutdecimal" value="<?=$discount_info['usage_quantity']?>" name="usage_quantity" id="usage_quantity">
                                        </div> -->
                                    </div>

                                    <div class="row">
                                        <div class="col" style="display: none;" id="table-shop-div">
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
                                        <div class="col"  id="table-product-div">
                                            <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap "  id="table-product">
                                                <thead>
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <th>Original Price</th>
                                                        <th>Discounted Price</th>
                                                        <th style="text-align: center;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id='tbody_prodpromo' class='tbody_prodpromo maintbody'>
                                                
                                                </tbody>
                                            </table>
                                            <button class="btn btn-primary addProductBtn" type="button"  id="addProductButton" >Add Product</button>
                                        </div>
                                    </div>

                                <div class="form-group row mt-10">       
                                        <div class="col-md-12">
                                            <button style="float:right" type="button" data-id="<?=$id?>" class="btn btn-primary saveBtn">Save</button>
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
                <div class="row"><div class="col-12" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row ">
                            <div class="col-md-6 col-lg-4">
                                Product Name
                                <div class="form-group">
                                    <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                Filter By
                                <div class="form-group">
                                    <select name="_categories" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Category</option>
                                        <option value="0">Variants</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?=$category['id'];?>"><?=$category['category_name'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <br>
                                <button class="btn btn-primary" type="button" id="btnSearch">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <!-- end - record status is a default for every table -->
                    <div class="table-responsive">
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-product"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th width="20"></th>
                                    <th width="150">Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>No of Stock</th>
                                    <th width="30">Stock Status</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info clearTableFields" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btnAddProducts">Add Products</button>
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
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/promotions/update_product_discount.js');?>"></script>

