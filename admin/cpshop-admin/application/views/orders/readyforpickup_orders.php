<style>
    .datepicker {
        z-index:1001 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="content-inner" id="pageActive" data-num="<?=$main_nav_id;?>" data-namecollapse="" data-labelname="Products"> 
    <div class="bc-icons-2 card mb-4">
        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/orders_home/'.$token);?>">Orders</a></li>
            <li class="d-flex align-items-center"><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Ready for Pickup Order List</li>
        </ol>
    </div>
    <section class="tables">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Ready for Pickup Order List</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                          <!--  <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a></p> -->
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">
                    
                        <div class="search-filter col-lg-6 col-xs-12 col-md-7 col-sm-7 row m-0">
                            <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3" style="display:none;">
                            <select class="form-control" id="select_status">
                                <option value="">All Status</option>
                                <option value="0">Waiting for Payment</option>
                                <option value="1">Paid</option>
                                <option value="p">Ready for Processing</option>
                                <option value="po">Processing Order</option>
                                <option value="rp" selected>Ready for Pickup</option>
                                <option value="bc">Booking Confirmed</option>
                                <option value="f">Fulfilled</option>
                                <option value="s">Shipped</option>

                                <?php if(cs_clients_info()->c_allow_cod == 1){?>
                                    <option value="6">Pending(COD)<?=cs_clients_info()->c_allow_cod?></option>
                                    <option value="7">Paid(COD)</option>
                                <?php }?>
                                
                            </select>
                        </div>

                         <div class="col-md-3">
                            <select class="form-control" id="select_location">
                                <option value="all">All Locations</option>
                                <option value="address">Address</option>
                                <option value="region">Region</option>
                                <option value="province">Province</option>
                                <option value="citymun">City/Municipality</option>
                            </select>
                        </div>

                        <div class="col-lg-3 addressdiv" style="display:none;">
                            <input type="text" class="form-control" id="address" placeholder="Address">
                        </div>

                        <div class="col-lg-3 regiondiv" style="display:none;">
                            <select class="form-control select2" id="regCode">
                                <?php foreach($regions as $region){?>
                                    <option value="<?=$region['regCode']?>"><?=$region['regDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 provincediv" style="display:none;">
                            <select class="form-control select2" id="provCode">
                                <?php foreach($provinces as $province){?>
                                    <option value="<?=$province['provCode']?>"><?=$province['provDesc']?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-lg-3 citymundiv" style="display:none;">
                            <select class="form-control select2" id="citymunCode">
                                <?php foreach($citymuns as $citymun){?>
                                    <option value="<?=$citymun['citymunCode']?>"><?=$citymun['citymunName']?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <?php if($shopid == 0){?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="_shops" class="form-control material_josh form-control-sm search-input-text enter_search">
                                        <option value="">All Shops</option>
                                        <?php foreach ($shops as $shop): ?>
                                            <option value="<?=$shop['id'];?>"><?=$shop['shopname'];?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-auto table-search-container">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto px-1 mb-3">
                            <form action="<?php echo base_url('orders/main_orders/export_orders');?>" method="post" target="_blank">
                                <input type="hidden" name="_record_status_export" id="_record_status_export">
                                <input type="hidden" name="_name_export" id="_name_export">
                                <input type="hidden" name="status_export" id="status_export">
                                <input type="hidden" name="_shops_export" id="_shops_export">
                                <input type="hidden" name="date_from_export" id="date_from_export">
                                <input type="hidden" name="date_to_export" id="date_to_export">
                                <input type="hidden" name="location_export" id="location_export">
                                <input type="hidden" name="address_export" id="address_export">
                                <input type="hidden" name="regCode_export" id="regCode_export">
                                <input type="hidden" name="provCode_export" id="provCode_export">
                                <input type="hidden" name="citymunCode_export" id="citymunCode_export">
                                <input type="hidden" name="drno_export" id="drno_export">
                                <input type="hidden" name="request_filter" id="request_filter">
                                <button class="btn-mobile-w-100 btn btn-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                            </form>
                        </div>
                        <div class="col-12 col-md-auto px-1 mb-3">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3" style="display:none;">
                            <a class="btn-mobile-w-100 btn btn-outline-secondary" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                        </div>
                        <div class="col-6 col-md-auto px-1 mb-3">
                            <button class="btn-mobile-w-100 btn btn-primary btnSearch" type="button" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                    <!-- start - record status is a default for every table -->
                    <div class="col-md-3" hidden>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6" style="padding:0px;">
                                    <select name="_record_status" class="form-control material_josh form-control-sm enter_search" id="_record_status">
                                        <option value="">All Records</option>
                                        <option value="1" selected>Enabled</option>
                                        <option value="2">Disabled</option>
                                    </select> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end - record status is a default for every table -->
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid-order"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th width="150">Order</th>
                                <th>Customer</th>
                                <th>Contact No.</th>
                                <th>Amount</th>
                                <!-- <th>Voucher</th> -->
                                <!-- <th>Shipping</th> -->
                                <!-- <th>Total</th> -->
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Shop</th>
                                <th>Branch</th>
                                <!-- <th>City</th> -->
                                <th width="30">Action</th>
                            </tr>
                        </thead>
                    </table>
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

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header secondary-bg white-text d-flex align-items-center">
                <h3 class="modal-title" id="exampleModalLabel">Delete Confirmation</h3>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Message:</label>
                        <textarea class="form-control" id="message-text"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- hidden fields -->
<input type="hidden" id="token" value="<?=$token?>">
<input type="hidden" id="order_status_view" value="readyforpickup">
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/orders/orders.js');?>"></script>
<!-- end - load the footer here and some specific js -->

