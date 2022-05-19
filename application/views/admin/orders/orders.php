<style>
    .datepicker {
        z-index:1001 !important;
    }
</style>
<!-- change the data-num and data-subnum for numbering of navigation -->
<div class="col-12 alert_div">
    <div class="alert alert-secondary ml-4 color-dark" role="alert">
        <span class="font-weight-bold"><a class="text-dark" href="<?=base_url('admin/Main_orders/orders_home/Orders');?>"><?=$active_page?></a></span>
        
    </div>
</div>
<div class="col-12">
    <section class="tables  ml-4">   
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title">Order List</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow d-lg-none mb-0"><a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg text-white" aria-hidden="true"></i></a></p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                        <div class="row">
                        
                            <div class="search-filter col-lg-3 col-xs-12 col-md-6 col-sm-7 ">
                                Date From
                                <input type="text" placeholder="MM/DD/YYYY" autocomplete="false" class="form-control datetimepicker-input date_from" value="<?=today_date();?>"  id="date_from" data-toggle="datetimepicker" data-target="#datepicker"/>
                                
                                            <!--                                 
                                <div class="input-daterange input-group datetimepicker" data-date-end-date="0d">



                                    <input type="text" autocomplete="off" class="input-sm form-control search-input-select1 date_from col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_from" name="date_from" placeholder="MM/DD/YYYY" autocomplete="false">
                                    <span class="input-group-addon">&nbsp;to&nbsp;</span>
                                    <input type="text" autocomplete="off" class="input-sm form-control search-input-select2 date_to col-xs-10 mb-2 sm:mb-0" style="z-index: 2 !important;" value="<?=today_date();?>" id="date_to" name="date_to" placeholder="MM/DD/YYYY" autocomplete="false">
                                    <input type="hidden" id="todaydate" name="todaydate" value="<?=today_date();?>"/>
                                -->
                            </div>
                            <!-- <span class="input-group-addon">&nbsp;to&nbsp;</span> -->
                            
                            <div class="search-filter col-lg-3 col-xs-12 col-md-6 col-sm-7">
                                Date To
                                <input type="text" placeholder="MM/DD/YYYY" autocomplete="false" class="form-control datetimepicker-input date_to" value="<?=today_date();?>"  id="date_to" data-toggle="datetimepicker" data-target="#datepicker"/>
                            </div>

                            <div class="col-md-6 mb-3 col-lg-3">
                                Order By
                                <select class="form-control" id="select_date">
                                    <option value="date_created" selected >Date Ordered</option>
                                    <option value="date_accepted">Date Accepted</option>
                                    <option value="date_processed">Date Processed</option>
                                    <option value="date_shipped">Date Shipped</option>
                                    <option value="date_delivered">Date Delivered</option>
                                </select>
                            </div>
                            

                            <div class="col-lg-3 col-md-6 citymundiv">
                                City
                                <select class="form-control select2" id="citymunCode">
                                    <option value="">Select City</option>
                                    <?php foreach($cities as $citymun){?>
                                        <option value="<?=$citymun['city_name']?>"><?=$citymun['city_name']?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-12 col-lg-auto table-search-container text-right d-flex justify-content-end">
                        <div class="row no-gutters ">
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <input type="text" class="form-control" placeholder="Search..." aria-label="Recipient's username" aria-describedby="basic-addon2" name="_name" id="_name">
                            </div>
                            <div class="col-12 col-md-auto px-1 mb-3">
                                <form action="<?php echo base_url('admin/Main_orders/export_order_table');?>" method="post" target="_blank">
                                    <input type="hidden" name="_record_status_export" id="_record_status_export">
                                    <input type="hidden" name="_name_export" id="_name_export">
                                    <input type="hidden" name="status_export" id="status_export">
                                    <input type="hidden" name="date_export" id="date_export">
                                    <input type="hidden" name="_shops_export" id="_shops_export">
                                    <input type="hidden" name="date_from_export" id="date_from_export">
                                    <input type="hidden" name="date_to_export" id="date_to_export">
                                    <input type="hidden" name="address_export" id="address_export">
                                    <input type="hidden" name="regCode_export" id="regCode_export">
                                    <input type="hidden" name="provCode_export" id="provCode_export">
                                    <input type="hidden" name="citymunCode_export" id="citymunCode_export">
                                    <input type="hidden" name="drno_export" id="drno_export">
                                    <input type="hidden" name="forpickup_export" id="forpickup_export">
                                    <input type="hidden" name="request_filter" id="request_filter">
                                    <button class="btn-mobile-w-100 btn btn-primary btnExport mr-3" type="submit" id="btnExport" style="display:none">Export</button>
                                </form>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <a class="btn-mobile-w-100 btn btn-outline-secondary py-1 btn-block" type="button" id="search_clear_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-6 col-md-auto px-1 mb-3">
                                        <button class="btn-mobile-w-100 btn btn-primary btnSearch btn-block w-100" id="btnSearch"><i class="fa fa-search" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col btn btn-link bg-success text-white status-select nav-all" data-target="nav-all" data-status=10>
                            All &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light all-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-pending"data-target="nav-pending"data-status=1>
                            Pending &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light pending-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-processing"data-target="nav-processing"data-status=2>
                            Processing &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light processing-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-fulfilled"data-target="nav-fulfilled"data-status=3>
                            To Deliver &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light fulfilled-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-shipped"data-target="nav-shipped"data-status=4>
                            Shipped &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light shipped-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-failed"data-target="nav-failed"data-status=67>
                            Failed &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light failed-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-cancelled"data-target="nav-cancelled"data-status=89>
                            Cancelled &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light cancelled-status">0</span>
                        </div>
                        <div class="col btn btn-link status-select nav-delivered"data-target="nav-delivered"data-status=5>
                            Delivered &nbsp;<span class="check-count badge-success rounded text-xs rounded-lg px-1 bg-teal-500 text-light delivered-status">0</span>
                        </div>
                    </div>

                    <div id="tableDiv" >
                        <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%;" id="table-grid-order"  cellpadding="0" cellspacing="0" border="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th width="150">Order</th>
                                    <th>Customer</th>
                                    <!-- <th>Contact No.</th> -->
                                    <th>City</th>
                                    <th>Amount</th>
                                    <th>Discount</th>
                                    <th>Shipping</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th width="30">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
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
<input type="hidden" id="order_status_view" value="all">
<!-- start - load the footer here and some specific js -->
<script type="text/javascript" src="<?=base_url('assets/js/libs/admin/orders/orders.js');?>"></script>
<!-- end - load the footer here and some specific js -->

