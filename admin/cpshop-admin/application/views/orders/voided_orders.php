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
            <li class="breadcrumb-item active">Voided Order List</li>
        </ol>
    </div>
    <section class="tables">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <h3 class="card-title mb-0">Voided Order List</h3>
                        </div>
                        <div class="col d-flex align-items-center justify-content-end">
                            <p class="border-search_hideshow mb-0">
                                <a href="#" id="search_hideshow_btn"><i class="fa fa-chevron-circle-down fa-lg" aria-hidden="true"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 pt-3 pb-3 pb-md-0" id="card-header_search" data-show="1">
                    <form id="form_search">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" class="input-sm form-control search-input-select1 date_from datepicker" style="z-index: 2 !important;" id="date_from" value="<?=today_text();?>" name="start" readonly/>
                                <span class="input-group-addon" style="background-color:#fff; border:none;">&nbsp;to&nbsp;</span>
                                <input type="text" value="<?=today_text();?>" class="input-sm form-control search-input-select2 date_to datepicker" id="date_to" name="end" readonly/>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="_name" class="form-control material_josh form-control-sm search-input-text enter_search" placeholder="Reference No.">
                            </div>
                        </div>

                        <div class="col-md-3" hidden>
                            <select class="form-control" id="select_status">
                                <option value="">All</option>
                                <option value="Order List" selected>Order List</option>
                                <option value="Pre-Payment">Pre-Payment</option>
                            </select>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="card-body table-body">
                    <div class="col-md-8 col-lg-auto table-search-container text-right">
                        <div class="row no-gutters">
                            <div class="col-12 col-md-auto">
                                <div class="row no-gutters">                                    
                                    <div class="col-6 col-md-auto px-1">                                        
                                        <form action="<?=base_url('settings/void_record/Settings_void_record/export_void_records_tbl')?>" method="post" target="_blank">
                                            <input type="hidden" name="_search" id="_search">
                                            <input type="hidden" name="_filters" id="_filters">
                                            <button class="btn btn-primary btnExport" type="submit" id="btnExport" style="display:none">Export</button>&nbsp;
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
                    <table class="table wrap-btn-last-td table-striped table-hover table-bordered display nowrap" style="width:100%" id="table-grid"  cellpadding="0" cellspacing="0" border="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>ID</th>
                                <th width="150">Reference Number</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Username</th>
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
<!-- start - load the footer here and some specific js -->
<?php $this->load->view('includes/footer');?> <!-- includes your footer -->
<script type="text/javascript" src="<?=base_url('assets/js/orders/voided_orders.js');?>"></script>
<!-- end - load the footer here and some specific js -->
