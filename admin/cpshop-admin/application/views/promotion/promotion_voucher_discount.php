<link rel="stylesheet" href="<?=base_url('assets/css/switch-checkbox.css');?>">
<link rel="stylesheet" href="<?=base_url('assets/css/select2required.css');?>">
<style>
    .required {
    color: red;
    }

    
/*toggle styles*/
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch {
  position: relative;
  display: inline-block;
  width: 54px;
  height: 27px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
    background-color: var(--primary-color) !important
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

div.toggle-switch{
    width: 100%;
    padding: 10px;
}

div.toggle-switch *{        
    vertical-align: middle;
    margin-top: auto;
    margin-bottom: auto;
    display: inline-block;
}

.no-color{
    display: inline-block;
    position: absolute;
    right: 15px;
    top: 0;       
}
</style>

<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Settings"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/promotion_home/'.$token);?>">Promotion</a></li>
            <li><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Vouchers Discount</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Vouchers Discount List</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                                <!-- <a href="#" id="search_clear_btn">Clear Search <i class="fa fa-times fa-lg" aria-hidden="true"></i></a> -->
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">      
                     <form id="form_search">
                        <div class="row">

                             <div class="col-md-6 col-lg-3">
                                <div class="form-group" id="shop_field">
                                        <select name="_voucher_type" id="_voucher_type" class="form-control material_josh form-control-sm search-input-text enter_search">
                                            <option value="">Select Voucher Type</option>
                                            <option value="1">Shop Voucher</option>
                                            <option value="2">Product Voucher</option>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shipping Code</label> -->
                                    <input type="text" name="_vcode"   id="_vcode" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Voucher Code">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <!-- <label class="form-control-label col-form-label-sm">Shipping Name</label> -->
                                    <input type="text" name="_vname" name="_vname" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Voucher Name">
                                </div>
                            </div>
                            <!-- start - record status is a default for every table -->

                            <div class="col-md-6 col-lg-3">
                                <div class="form-group">
                                    <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                        <option value="">All Records</option>
                                        <option value="1" selected>Enabled</option>
                                        <option value="2">Disabled</option>
                                    </select> 
                                </div>
                            </div>
            
                             
                            <div class="col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                                <div class="input-group" >
                                    <input type="datetime-local"  class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_from" name="date_from" placeholder="MM/DD/YYYY">
                                    <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                    <input type="datetime-local" class="form-control" style="z-index: 2 !important; text-align:center;" value="" id="date_to" name="date_to" placeholder="MM/DD/YYYY" >
                                </div>
                            </div>

                    

                        </div>
                    </form>
                </div>
        
                <br>
                <div class="card-body table-body">
                    <!-- <?php if ($this->loginstate->get_access()['voucher_discount']['view'] == 1){ ?>
                        <div class="col-lg col-12 text-right mb-3 position-absolute right-0">
                            <button data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#add_modal" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-primary" id="action_add">Add</button>
                        </div>
                    <?php } ?> -->
                    <div class="col-lg-auto table-search-container">
                        <div class="row no-gutters">
                           <?php if ($this->loginstate->get_access()['voucher_discount']['create'] == 1){ ?>
                                <a  href="<?=base_url('promotion/Main_promotion/add_voucher_discount/'.$token);?>" class="btn-mobile-w-100 mx-0 mx-sm-2 btn btn-outline-danger mb-3" id="action_add">Add</a>
                            <?php } ?>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-12 col-md-auto px-1 mb-3">
                                        <form action="<?=base_url('vouchers/List_vouchers/export_vouchers_list')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <input type="hidden" name="_filter" id="_filter">
                                            <!-- <input type="hidden" name="_data" id="_data"> -->
                                            <button class="btn btn-primary btnExport btn-mobile-w-100" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
                                        </form>  
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <a class="btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1">
                                        <button class="btn btn-primary btnSearch btn-block w-100" id="btnSearch">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end - record status is a default for every table -->
                    <!-- <div class="table-responsive"> -->
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" id="table-grid">
                            <thead>
                                <tr>
                                    <th>Date Issued</th>
                                    <th>Valid Until</th>
                                    <th>Voucher Type</th>
                                    <th>Voucher Name</th>
                                    <th>Voucher Code</th>
                                    <th>Discount Amount</th>
                                    <th>Usage Limit</th>
                                    <th>Usage Count</th>
                                    <th>Status</th>
                                    <th width="30">Actions</th> 
                                    <th ></th> 
                                </tr>
                            </thead>
                        </table>
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal-->
<div class="modal fade" id="disable_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Disable Confirmation</h3>
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

<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <small>This action cannot be undone.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="delete_modal_confirm_btn">Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/promotion/vouchers_promotion.js');?>"></script>
<!-- end - load the footer here and some specific js -->

<div class="modal fade" id="setFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Set to All</h3>
            </div>
            <div class="modal-body">
            <input  type="hidden" id="voucher_id" placeholder=""> 
            <p>Are you sure you want to set to all this voucher?</p>
             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveFeatureConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unsetFeadutedModal" tabindex="-1" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
            <h3 class="modal-title" id="exampleModalLabel"> Unset to all</h3>
            </div>
            <div class="modal-body">
               <input  type="hidden" id="voucher_ids" placeholder=""> 
                <p>Are you sure you want to unset to all this voucher?</p>
                <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="uncheck_rabutton" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="unsaveFeatureConfirm" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>
