<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

<style>
    .sorting:before{
        display : none;
    }
    input[type="checkbox"]:focus{
        outline:0;
    }
    input[type="checkbox"] {
        appearance: none;
        cursor: pointer;
        height: 18px;
        width: 20px;
        vertical-align: top;
        -webkit-appearance: none;
    }
    input[type="checkbox"]:after {
    }
    input[type="checkbox"]:checked {
        border-color: #259bf5;
        box-shadow: inset 0 0 0 13px #259bf5;
        padding-left:2px;
        margin-bottom:3px;
        content: '✔';
    }
    input[type="checkbox"]:checked:after {
        border-color: #259bf5;
        left: 16px;
        right: 0;
        padding-left:2px;
        margin-bottom:3px;
        content: "✔"
    }

::-webkit-scrollbar {
	width: 10px;
  }
  
  
  /* Track */
  
  ::-webkit-scrollbar-track {
	background: whitesmoke;
	border-radius: 2px;
  }
  
  
  /* Handle */
  
  ::-webkit-scrollbar-thumb {
	background: lightgrey;
	border-radius: 2px;
  }
  
  
  /* Handle on hover */
  
  ::-webkit-scrollbar-thumb:hover {
	background: grey;
  }

  .mainthead {
  display:block;
}
.maintbody {
  display:block;
  overflow-y:scroll;
  height:500px;
}
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/promotion_home/'.$token);?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_promotion/discount_promo/'.$token);?>">Discount Promo</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Update Discount</li>
        </ol>
    </div>
    <section class="tables" id="promoProductDiv">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header py-2">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            Product Promo
                        </div>
                        <div class="col d-flex justify-content-end align-items-center">
                            <p class="border-search_hideshow mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1" hidden>
                    <div class="row">
                        <div class="col-md-6 col-lg-3">
                            <?php if($shopid == 0){?> 
                                <div class="form-group">
                                    <select name="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <form id="form_promoprod" enctype="multipart/form-data" method="post">
                    <div class="card-body table-body">
                        <input type="hidden" id="get_ct_id" value="<?php echo $id; ?>">
                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                        <label class="control-label font-weight-bold">Start and End Date</label>
                             <div class="input-group">
                                <input type="date" style="text-align:center" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?php echo date('Y-m-d'); ?>" id="start_date" name="start_date" autocomplete="false">
                                <span class="input-group-addon" style="height:95%">&nbsp;to&nbsp;</span>
                                <input type="date" style="text-align:center" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?php echo date('Y-m-d'); ?>" id="end_date" name="end_date" autocomplete="false">                          
                            </div>
                        </div>
                        <br>
                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0" style="display:none;">
                            <?php if($shopid == 0){?> 
                                <select name="select_shop2" id="select_shop2" class="form-control material_josh form-control-sm search-input-text enter_search">
                                    <option value="">All Shops</option>
                                    <?php foreach ($shops as $shop): ?>
                                        <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                    <?php endforeach ?>
                                </select>
                            <?php } else{?>
                                <input type="hidden" class="select_shop2" id="select_shop2" name="select_shop2" value="<?=$shopid?>">
                            <?php } ?>
                        </div>
                        <div class="col-md-8 col-lg-auto table-search-container text-right">
                            <div class="row no-gutters">
                                <div class="col-12 col-md-auto px-1"> 
                                    <?php if($this->loginstate->get_access()['mystery_coupon']['create'] == 1){ ?>
                                            <a class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary mb-3 mb-md-0" id="addProductBtn" style="color:white;">Add Product</a>
                                    <?php } ?>
                                </div>
                                <div class="col-12 col-md-auto px-1">
                                    <?php if($this->loginstate->get_access()['mystery_coupon']['update'] == 1){ ?>
                                            <a class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-success mb-3 mb-md-0" id="saveBtn" style="color:white;">Save & Submit</a>
                                    <?php } ?>
                                </div>
                                <div class="col-12 col-md-auto px-1">
                                    <?php if($this->loginstate->get_access()['mystery_coupon']['update'] == 1){ ?>
                                            <a class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-danger mb-3 mb-md-0" id="discardBtn" style="color:white;">Discard</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row pl-3">
                                <input type="checkbox" class="form-control regular-checkbox" name="checkbox_time" id="checkbox_time"> <label class="control-label">&nbsp;Set Time</label>
                            </div>
                        </div>

                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0 timeDiv" style="display:none;">
                            <label class="control-label font-weight-bold">Start and End Time</label>
                            <div class="input-group">
                                <input type="text" autocomplete="off" class="input-sm form-control col-xs-10 mb-2 sm:mb-0 timepicker timepickervalidation" style="z-index: 2 !important;background-color:white;" id="start_time" name="start_time" placeholder="HH/MM">
                                <span class="input-group-addon" style="height:95%">&nbsp;to&nbsp;</span>
                                <input type="text" autocomplete="off" class="input-sm form-control col-xs-10 mb-2 sm:mb-0 timepicker timepickervalidation" style="z-index: 2 !important;background-color:white;" id="end_time" name="end_time" placeholder="HH/MM">                            
                            </div>
                            <!-- <input type="text" class="timepicker" name="timepicker" /> -->
                            <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                        </div>

                        <div class="col-md-8">
                            <br>
                        </div>

                        <div class="col-md-12">
                            <label class="control-label font-weight-bold">Batch Setting</label>
                        </div>
                        <div class="row pl-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Campaign Type</label>
                                    <select class="form-control" name="batch_promo_type" id="batch_promo_type">
                                        <?php foreach ($campaign as $row): ?>
                                            <option value="<?=$row['id'];?>"><?=$row['name'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Campaign Rate</label>
                                    <select class="form-control" name="batch_promo_rate" id="batch_promo_rate">
                                        <option value="1">Fixed</option>
                                        <option value="2">Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Campaign Price</label>
                                    <input type="text" class="form-control allownumericwithdecimal notallowzero" name="batch_promo_price" id="batch_promo_price" value="" style="background-color:white;">
                                </div>
                            </div>

                            <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Campaign Stock</label>
                                    <select class="form-control" name="batch_promo_stock" id="batch_promo_stock">
                                        <option value="0">No Limit</option>
                                        <option value="1">Limit</option>
                                    </select>
                                </div>
                            </div> -->

                            <div class="col-md-3 batchPromoQtyDiv">
                                <div class="form-group">
                                    <label class="control-label">Campaign Stock Quantity</label>
                                    <input type="text" class="form-control allownumericwithoutdecimal notallowzero" name="batch_promo_stock_qty" value="1" id="batch_promo_stock_qty">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Purchase Limit</label>
                                    <select class="form-control" name="batch_purch_limit_select" id="batch_purch_limit_select" readonly>
                                        <!-- <option value="0">No Limit</option> -->
                                        <option value="1">Limit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3 batchPurchLimitDiv">
                                <div class="form-group">
                                    <label class="control-label">Purchase Limit Quantity</label>
                                    <input type="number" class="form-control allownumericwithdecimal notallowzero" name="batch_purch_limit" id="batch_purch_limit" value="1">
                                </div>
                            </div>
                        </div>
                    
                        <div class="table-responsive">
                            <table class='table table-striped table-hover table-bordered table-grid display nowrap' id="promotable">
                                <thead class="mainthead">
                                    <tr>
                                        <th scope='col' width="5%"><input type="checkbox" class="form-control regular-checkbox" name="checkbox_all_prod" id="checkbox_all_prod">&nbsp;&nbsp;&nbsp;</th>
                                        <th scope='col' width="10.8%"><b>Item Name</b></th>
                                        <th scope='col' width="11%"><b>Campaign Type</b></th>
                                        <th scope='col' width="11%"><b>Campaign Rate</b></th>
                                        <th scope='col' width="9%"><b>Original Price</b></th>
                                        <th scope='col' width="12%"><b>Campaign Price</b></th>
                                        <th scope='col' width="8%"><b>Campaign Stock</b></th>
                                        <th scope='col' width="5%"><b>Current Stock</b></th>
                                        <th scope='col' width="9%"><b>Purchase Limit</b></th>
                                        <th scope='col' width="7%"><b>Status</b></th>
                                        <th scope='col' width="7%"><b>Action</b></th>
                                    </tr>
                                </thead>
                                <tbody id='tbody_prodpromo' class='tbody_prodpromo maintbody'>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                      <td colspan = "11"><span id="number_of_check">0</span> selected check box out of <span id="total_row">0</span> </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->

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
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-productpromo"  cellpadding="0" cellspacing="0" border="0">
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

<div class="modal fade" id="savePromoModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Save Promo</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to save this promotion?</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="savePromoConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteProdPromoModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Delete Piso Day</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this promotion? This action cannot be reversed.</p>
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

<div class="modal fade" id="discardModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Discard Changes</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to discard changes? This action cannot be reversed.</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="discardConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<input type="hidden" id="access_view" value="<?=$this->loginstate->get_access()['mystery_coupon']['view'] ?>">
<input type="hidden" id="access_create" value="<?=$this->loginstate->get_access()['mystery_coupon']['create']?>">
<input type="hidden" id="access_update" value="<?=$this->loginstate->get_access()['mystery_coupon']['update']?>">
<input type="hidden" id="access_delete" value="<?=$this->loginstate->get_access()['mystery_coupon']['delete']?>">
<input type="hidden" id="access_disable" value="<?=$this->loginstate->get_access()['mystery_coupon']['disable']?>">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<!-- <script type="text/javascript">
       $(document).ready(function() {
         $('.timepicker').timepicker({
                timeFormat: 'HH:mm',
                interval: 60,
                defaultTime: '10',
              });

       });


</script> -->
<script type="text/javascript">
       $(document).ready(function() {

       // alert("dsfdsf");

         $(function(){
            var dtToday = new Date();
            
            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if(month < 10)
                month = '0' + month.toString();
            if(day < 10)
                day = '0' + day.toString();
            
            var minDate= year + '-' + month + '-' + day;
            
            $('#start_date').attr('min', minDate);
            $('#end_date').attr('min', minDate);
        });

       });


</script>
<script type="text/javascript" src="<?=base_url('assets/js/promotion/mystery_coupon_id_100121.js');?>"></script>


<!-- end - load the footer here and some specific js -->

